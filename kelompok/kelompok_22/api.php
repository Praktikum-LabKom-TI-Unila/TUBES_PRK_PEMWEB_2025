<?php
/**
 * REST API untuk SiPaMaLi
 * Tugas Besar Praktikum Pemrograman Web 2025
 * Kelompok 22
 * Endpoints:
 * - GET    /api.php?action=getReports       - Get all reports
 * - GET    /api.php?action=getReport&id=xxx - Get single report
 * - GET    /api.php?action=getStats         - Get statistics
 * - GET    /api.php?action=getPetugas       - GET LIST PETUGAS
 * - POST   /api.php?action=createReport     - Create new report
 * - PUT    /api.php?action=updateStatus     - Update report status
 * - PUT    /api.php?action=assignReport     - ASSIGN PETUGAS (PRIMARY KEY ID)
 * - PUT    /api.php?action=validateReport   - VALIDASI (DITERIMA -> TUNTAS)
 * - PUT    /api.php?action=rejectValidation - VALIDASI (DITOLAK -> DIPROSES)
 * - DELETE /api.php?action=deleteReport&id=xxx - Delete report
 * * CATATAN: Memerlukan fungsi helper: getDBConnection(), jsonResponse(), sanitizeInput(), generateReportId(), validateUploadedFile(), UPLOAD_DIR
 */

require_once 'includes/config.php';

function getPutData() {
    $input = file_get_contents('php://input'); 
    $putData = [];
    
    parse_str($input, $putData); 

    if (empty($putData)) {
        $json = json_decode($input, true);
        if ($json !== null) {
            $putData = $json;
        }
    }
    return $putData;
}

$action = $_GET['action'] ?? '';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGet($action);
        break;
    
    case 'POST':
        handlePost($action);
        break;
    
    case 'PUT':
        handlePut($action);
        break;
    
    case 'DELETE':
        handleDelete($action);
        break;
    
    default:
        jsonResponse(false, null, 'Method not allowed', 405);
}

function handleGet($action) {
    $conn = getDBConnection();
    
    switch ($action) {
        case 'getReports':
            jsonResponse(false, null, 'Not implemented', 501);
            break;
        
        case 'getReport':
            jsonResponse(false, null, 'Not implemented', 501);
            break;
        
        case 'getStats':
            jsonResponse(false, null, 'Not implemented', 501);
            break;
            
        case 'getPetugas':
            $query = "SELECT id, full_name, email 
                      FROM users 
                      WHERE role = 'petugas' 
                      ORDER BY full_name ASC";
            
            $result = $conn->query($query);
            
            if ($result === FALSE) {
                jsonResponse(false, null, 'Failed to fetch petugas list: ' . $conn->error, 500);
                break;
            }
            
            $petugas_list = [];
            while ($row = $result->fetch_assoc()) {
                $row['id'] = (string)$row['id']; 
                $petugas_list[] = $row;
            }
            
            jsonResponse(true, $petugas_list, 'Petugas list retrieved successfully');
            break;
            
        default:
            jsonResponse(false, null, 'Invalid action', 400);
    }
}

function handlePost($action) {
    jsonResponse(false, null, 'Invalid action', 400);
}

function handlePut($action) {
    $conn = getDBConnection();
    
    parse_str(file_get_contents("php://input"), $putData);
    
    $reportPkeyId = (int)($putData['id'] ?? $putData['report_id'] ?? 0); 
    
    if ($reportPkeyId === 0 && in_array($action, ['assignReport', 'validateReport', 'rejectValidation'])) {
        jsonResponse(false, null, 'Report Primary Key ID is required.', 400);
        return; 
    }
    
    switch ($action) {
        case 'updateStatus':
            jsonResponse(false, null, 'Not implemented', 501);
            break;
            
        case 'assignReport':
            $putData = getPutData();
            $reportPkeyId = (int)($putData['id'] ?? 0); 
            $petugasId = sanitizeInput($putData['assigned_to'] ?? ''); 

            if ($reportPkeyId <= 0) {
                jsonResponse(false, null, 'Invalid Report ID.', 400);
                return;
            }

            if (!empty($petugasId)) {
                $statusToUpdate = 'Diproses';
                $updateQuery = "UPDATE reports SET assigned_to = ?, status = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $conn->prepare($updateQuery);
                
                if ($stmt === false) {
                    jsonResponse(false, null, 'SQL Prepare Error (Assignment): ' . $conn->error, 500);
                    return;
                }

                $petugasIdInt = (int)$petugasId; 
                
                $stmt->bind_param('isi', $petugasIdInt, $statusToUpdate, $reportPkeyId); 
                
            } else {
                $statusToUpdate = 'Menunggu';
                $updateQuery = "UPDATE reports SET assigned_to = NULL, status = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $conn->prepare($updateQuery);
                
                if ($stmt === false) {
                    jsonResponse(false, null, 'SQL Prepare Error (Unassignment): ' . $conn->error, 500);
                    return;
                }
                
                $stmt->bind_param('si', $statusToUpdate, $reportPkeyId);
            }

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                jsonResponse(true, null, 'Laporan berhasil ditugaskan atau status diubah.');
            } else {
                jsonResponse(false, null, 'Gagal menugaskan laporan. Pastikan ID laporan valid.', 500);
            }
    
    $stmt->close();
    break;
            
        case 'validateReport':
            $putData = getPutData();
            $reportPkeyId = (int)($putData['id'] ?? 0);
            $adminNotes = sanitizeInput($putData['admin_notes'] ?? '');

            if ($reportPkeyId <= 0) {
                jsonResponse(false, null, 'Invalid Report ID.', 400);
                return;
            }

            $finalStatus = 'Tuntas'; 
            
            $stmt = $conn->prepare("UPDATE reports SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ? AND (status = 'Selesai' OR status = 'Diproses')");

            if ($stmt === false) {
                jsonResponse(false, null, 'SQL Prepare Error (Validate): ' . $conn->error, 500);
                return;
            }

            $stmt->bind_param('ssi', $finalStatus, $adminNotes, $reportPkeyId);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                jsonResponse(true, null, "Laporan berhasil divalidasi dan status diubah menjadi 'Tuntas'.");
            } else {
                jsonResponse(false, null, "Gagal memvalidasi. Pastikan status laporan masih 'Selesai' atau 'Diproses'.", 500);
            }
            $stmt->close();
            break;

        case 'rejectValidation': 
            $putData = getPutData();
            $reportPkeyId = (int)($putData['id'] ?? 0);
            $adminNotes = sanitizeInput($putData['admin_notes'] ?? '');

            if ($reportPkeyId <= 0) {
                jsonResponse(false, null, 'Invalid Report ID.', 400);
                return;
            }

            if (empty($adminNotes)) {
                jsonResponse(false, null, "Catatan Admin wajib diisi untuk penolakan.", 400);
                return;
            }
            
            $rejectStatus = 'Ditolak'; 
            
            $stmt = $conn->prepare("UPDATE reports SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ? AND (status = 'Selesai' OR status = 'Diproses')");
            
            if ($stmt === false) {
                jsonResponse(false, null, 'SQL Prepare Error (Reject): ' . $conn->error, 500);
                return;
            }

            $stmt->bind_param('ssi', $rejectStatus, $adminNotes, $reportPkeyId);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                jsonResponse(true, null, "Validasi ditolak. Status diubah menjadi 'Ditolak'.");
            } else {
                jsonResponse(false, null, "Gagal menolak validasi. Pastikan status laporan masih 'Selesai' atau 'Diproses'.", 500);
            }
            $stmt->close();
            break;
            
        default:
            jsonResponse(false, null, 'Invalid action', 400);
    }
}

function handleDelete($action) {
    jsonResponse(false, null, 'Invalid action', 400);
}
?>