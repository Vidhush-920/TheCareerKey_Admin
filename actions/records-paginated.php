<?php
require_once __DIR__ . '/../db_connection.php';

header('Content-Type: application/json');

// ── Parameters ──────────────────────────────────────────────────────────────
$page        = max(1, (int)($_GET['page']        ?? 1));
$limit       = max(1, (int)($_GET['limit']       ?? 50));
$filter_type = trim($_GET['filter_type'] ?? 'all');
$start_date  = trim($_GET['start_date']  ?? '');
$end_date    = trim($_GET['end_date']    ?? '');

// ── Base query (newest first) ────────────────────────────────────────────────
$where  = [];
$params = [];

if ($filter_type === 'today') {
    $where[]  = 'DATE(created_at) = CURDATE()';
} elseif ($filter_type === 'this-week') {
    $where[]  = 'YEARWEEK(created_at, 0) = YEARWEEK(CURDATE(), 0)';
} elseif ($filter_type === 'this-month') {
    $where[]  = 'YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())';
} elseif ($filter_type === 'this-year') {
    $where[]  = 'YEAR(created_at) = YEAR(CURDATE())';
} elseif ($filter_type === 'custom') {
    if ($start_date !== '') {
        $where[]    = 'created_at >= :start_date';
        $params[':start_date'] = $start_date . ' 00:00:00';
    }
    if ($end_date !== '') {
        $where[]    = 'created_at <= :end_date';
        $params[':end_date'] = $end_date . ' 23:59:59';
    }
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ── Total count ──────────────────────────────────────────────────────────────
$countSQL  = "SELECT COUNT(*) FROM ckey_results $whereSQL";
$countStmt = $pdo->prepare($countSQL);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

// ── Paginated rows ───────────────────────────────────────────────────────────
$offset   = ($page - 1) * $limit;
$dataSQL  = "SELECT * FROM ckey_results $whereSQL ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$dataStmt = $pdo->prepare($dataSQL);
foreach ($params as $key => $val) {
    $dataStmt->bindValue($key, $val);
}
$dataStmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$dataStmt->execute();
$records = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'records'    => $records,
    'total'      => $total,
    'page'       => $page,
    'limit'      => $limit,
    'total_pages'=> (int)ceil($total / $limit),
]);
?>
