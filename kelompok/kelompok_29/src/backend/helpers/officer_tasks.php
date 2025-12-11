<?php
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/database.php';

function find_officer_task(PDO $pdo, int $taskId, int $officerId): ?array
{
    $stmt = $pdo->prepare(
    'SELECT ot.id AS task_id, ot.started_at, ot.finished_at,
        c.id AS complaint_id, c.title, c.description, c.category, c.status,
        c.photo_before,
        (
            SELECT sub.photo_after
            FROM completion_proofs sub
            WHERE sub.complaint_id = c.id AND sub.officer_id = ot.officer_id
            ORDER BY sub.created_at DESC
            LIMIT 1
        ) AS completion_photo_after,
        c.latitude, c.longitude, c.address,
                c.created_at AS complaint_created_at, c.updated_at AS complaint_updated_at,
                c.reporter_id,
                reporter.full_name AS reporter_name, reporter.email AS reporter_email,
                reporter.phone AS reporter_phone, reporter.address AS reporter_address,
                reporter.profile_photo AS reporter_photo
         FROM officer_tasks ot
         JOIN complaints c ON c.id = ot.complaint_id
         JOIN users reporter ON reporter.id = c.reporter_id
                 WHERE ot.officer_id = :officer_id
                     AND (ot.id = :task_identifier OR c.id = :task_identifier)
         LIMIT 1'
    );
    $stmt->execute([
                ':task_identifier' => $taskId,
                ':officer_id' => $officerId,
    ]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function get_officer_task_or_404(int $taskId, int $officerId): array
{
    $pdo = get_pdo();
    $row = find_officer_task($pdo, $taskId, $officerId);

    if (!$row) {
        response_error(404, 'Tugas tidak ditemukan.', [
            'reason' => 'task_not_found',
        ]);
    }

    return $row;
}

function fetch_task_timeline(PDO $pdo, int $complaintId): array
{
    $stmt = $pdo->prepare(
        'SELECT cp.id, cp.status, cp.note, cp.created_at,
                u.id AS user_id, u.full_name AS user_name
         FROM complaint_progress cp
         JOIN users u ON u.id = cp.created_by
         WHERE cp.complaint_id = :complaint_id
         ORDER BY cp.created_at ASC'
    );
    $stmt->execute([':complaint_id' => $complaintId]);

    $timeline = [];
    foreach ($stmt->fetchAll() as $row) {
        $timeline[] = [
            'id' => (int) $row['id'],
            'status' => $row['status'],
            'note' => $row['note'],
            'created_at' => $row['created_at'],
            'created_by' => [
                'id' => (int) $row['user_id'],
                'name' => $row['user_name'],
            ],
        ];
    }

    return $timeline;
}

function list_officer_tasks_with_status(PDO $pdo, int $officerId, array $statuses, int $page, int $limit): array
{
    $placeholders = implode(',', array_fill(0, count($statuses), '?'));
    $countSql = 'SELECT COUNT(*) FROM officer_tasks ot JOIN complaints c ON c.id = ot.complaint_id WHERE ot.officer_id = ? AND c.status IN (' . $placeholders . ')';
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute(array_merge([$officerId], $statuses));
    $totalData = (int) $countStmt->fetchColumn();

    $dataSql = 'SELECT ot.id AS task_id, c.id AS complaint_id, c.title, c.category, c.status, c.address,
                       c.created_at, c.updated_at,
                       ot.started_at, ot.finished_at
                FROM officer_tasks ot
                JOIN complaints c ON c.id = ot.complaint_id
                WHERE ot.officer_id = ? AND c.status IN (' . $placeholders . ')
                ORDER BY c.updated_at DESC
                LIMIT ? OFFSET ?';

    $offset = ($page - 1) * $limit;
    $stmt = $pdo->prepare($dataSql);

    $paramIndex = 1;
    $stmt->bindValue($paramIndex++, $officerId, PDO::PARAM_INT);
    foreach ($statuses as $status) {
        $stmt->bindValue($paramIndex++, $status, PDO::PARAM_STR);
    }
    $stmt->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

    $stmt->execute();

    $records = [];
    foreach ($stmt->fetchAll() as $row) {
        $records[] = [
            'task_id' => (int) $row['task_id'],
            'complaint_id' => (int) $row['complaint_id'],
            'title' => $row['title'],
            'category' => $row['category'],
            'status' => $row['status'],
            'address' => $row['address'],
            'started_at' => $row['started_at'],
            'finished_at' => $row['finished_at'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ];
    }

    return [
        'page' => $page,
        'limit' => $limit,
        'total_data' => $totalData,
        'total_page' => $totalData > 0 ? (int) ceil($totalData / $limit) : 0,
        'records' => $records,
    ];
}

function recalc_officer_status(PDO $pdo, int $officerId): void
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*)
         FROM officer_tasks ot
         JOIN complaints c ON c.id = ot.complaint_id
         WHERE ot.officer_id = :officer_id
           AND c.status IN (\'ditugaskan_ke_petugas\', \'dalam_proses\', \'menunggu_validasi_admin\')'
    );
    $stmt->execute([':officer_id' => $officerId]);
    $active = (int) $stmt->fetchColumn();

    $newStatus = $active > 0 ? 'sibuk' : 'tersedia';

    $update = $pdo->prepare('UPDATE officers SET officer_status = :status, updated_at = NOW() WHERE id = :id AND officer_status <> :status');
    $update->execute([
        ':status' => $newStatus,
        ':id' => $officerId,
    ]);
}
