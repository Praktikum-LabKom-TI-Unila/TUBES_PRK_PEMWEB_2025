<?php
// api/admin/ambil_laporan.php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../fungsi_helper.php';

cek_role('admin');

header('Content-Type: application/json');

try {
    // Ambil parameter
    $status = $_GET['status'] ?? '';
    $kategori = $_GET['kategori'] ?? '';
    $search = $_GET['search'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(100, max(10, (int)($_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;
    
    // Build query
    $where = [];
    $params = [];
    
    if ($status) {
        $where[] = "l.status = :status";
        $params[':status'] = $status;
    }
    
    if ($kategori) {
        $where[] = "l.kategori = :kategori";
        $params[':kategori'] = $kategori;
    }
    
    if ($search) {
        $where[] = "(l.judul LIKE :search OR l.deskripsi LIKE :search OR l.alamat LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Hitung total
    $count_sql = "SELECT COUNT(*) as total FROM laporan l $where_clause";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Ambil data
    $sql = "
        SELECT 
            l.*,
            p.nama as nama_pelapor,
            p.telepon as telepon_pelapor,
            (SELECT COUNT(*) FROM foto_laporan WHERE laporan_id = l.id) as jumlah_foto,
            (SELECT COUNT(*) FROM penugasan WHERE laporan_id = l.id) as jumlah_petugas
        FROM laporan l
        JOIN pengguna p ON l.pengguna_id = p.id
        $where_clause
        ORDER BY l.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    json_response([
        'success' => true,
        'data' => $laporan,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'total_pages' => ceil($total / $limit)
        ]
    ]);
    
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ], 500);
}
