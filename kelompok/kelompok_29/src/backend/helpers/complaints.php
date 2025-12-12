<?php
require_once __DIR__ . '/database.php';

function complaint_categories(): array
{
    return [
        ['id' => 'Jalan_Raya', 'label' => 'Kerusakan Jalan Raya'],
        ['id' => 'Penerangan_Jalan', 'label' => 'Penerangan Jalan'],
        ['id' => 'Drainase', 'label' => 'Drainase'],
        ['id' => 'Trotoar', 'label' => 'Trotoar'],
        ['id' => 'Taman', 'label' => 'Taman Kota'],
        ['id' => 'Jembatan', 'label' => 'Jembatan'],
        ['id' => 'Rambu_Lalu_Lintas', 'label' => 'Rambu Lalu Lintas'],
        ['id' => 'Fasilitas_Umum', 'label' => 'Fasilitas Umum'],
        ['id' => 'Lainnya', 'label' => 'Lainnya'],
    ];
}

function complaint_statuses(): array
{
    return [
        'diajukan',
        'diverifikasi_admin',
        'ditugaskan_ke_petugas',
        'dalam_proses',
        'menunggu_validasi_admin',
        'selesai',
    ];
}

function fetch_complaint_timeline(PDO $pdo, int $complaintId): array
{
    $stmt = $pdo->prepare(
        'SELECT cp.id, cp.status, cp.note, cp.created_at, cp.created_by, u.full_name AS created_by_name
         FROM complaint_progress cp
         JOIN users u ON u.id = cp.created_by
         WHERE cp.complaint_id = :complaint_id
         ORDER BY cp.created_at ASC'
    );
    $stmt->execute([':complaint_id' => $complaintId]);

    $timeline = [];
    while ($row = $stmt->fetch()) {
        $timeline[] = [
            'id' => (int) $row['id'],
            'status' => $row['status'],
            'note' => $row['note'],
            'created_at' => $row['created_at'],
            'created_by' => [
                'id' => (int) $row['created_by'],
                'name' => $row['created_by_name'],
            ],
        ];
    }

    return $timeline;
}
