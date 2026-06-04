<?php
require_once __DIR__ . '/../db_connection.php';

header('Content-Type: application/json');

// ── Parameters ──────────────────────────────────────────────────────────────
$filter_type = trim($_GET['filter_type'] ?? 'all');
$start_date  = trim($_GET['start_date']  ?? '');
$end_date    = trim($_GET['end_date']    ?? '');
$page        = max(1, (int)($_GET['page'] ?? 1));
$limit       = max(1, (int)($_GET['limit'] ?? 50));

// ── Build WHERE clause (same logic as records-paginated.php) ─────────────────
$where  = [];
$params = [];

if ($filter_type === 'today') {
    $where[] = 'DATE(created_at) = CURDATE()';
} elseif ($filter_type === 'this-week') {
    $where[] = 'YEARWEEK(created_at, 0) = YEARWEEK(CURDATE(), 0)';
} elseif ($filter_type === 'this-month') {
    $where[] = 'YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())';
} elseif ($filter_type === 'this-year') {
    $where[] = 'YEAR(created_at) = YEAR(CURDATE())';
} elseif ($filter_type === 'custom') {
    if ($start_date !== '') {
        $where[]               = 'created_at >= :start_date';
        $params[':start_date'] = $start_date . ' 00:00:00';
    }
    if ($end_date !== '') {
        $where[]             = 'created_at <= :end_date';
        $params[':end_date'] = $end_date . ' 23:59:59';
    }
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ── Fetch matching records, newest first ─────────────────────────────────────
$sql  = "SELECT * FROM ckey_results $whereSQL ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Group by NIC ─────────────────────────────────────────────────────────────
$people = [];
foreach ($records as $i => $record) {
    $nic = $record['nic'] ?? 'unknown';
    if (!isset($people[$nic])) {
        $people[$nic] = [
            'nic'     => $nic,
            'name'    => $record['name'] ?? '',
            'records' => [],
        ];
    }
    $people[$nic]['records'][] = $record;
}

$people_values = array_values($people);
$total = count($people_values);
$total_pages = ceil($total / $limit) ?: 1;
$offset = ($page - 1) * $limit;
$sliced_people = array_slice($people_values, $offset, $limit);

echo json_encode([
    'people'      => $sliced_people,
    'total'       => $total,
    'page'        => $page,
    'total_pages' => $total_pages,
]);
?>
