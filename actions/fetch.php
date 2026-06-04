<?php 
require_once __DIR__ . '/../db_connection.php';

// Get Records Data
$records = [];
$query_records = "SELECT * FROM ckey_results";
$result_records = $pdo->query($query_records);
$records = $result_records->fetchAll(PDO::FETCH_ASSOC);


function getPeople($records) {
    $people = [];
    foreach ($records as $record) {
        $nic = $record['nic'] ?? 'Unknown NIC';
        if (!isset($people[$nic])) {
            $people[$nic] = [
                'name' => $record['name'] ?? 'Unknown Name',
            'nic' => $nic,
            'records' => []
        ];
    }
        $people[$nic]['records'][] = $record;
    }
    return $people;
}
$people = getPeople($records);

//Get Latest 5 Records Data from $records
//order records by created_at descending
usort($records, function($a, $b) {
    if ($a['created_at'] == $b['created_at']) {
        return 0;
    }
    return ($a['created_at'] > $b['created_at']) ? -1 : 1;
});

// Get Staffs Data
$staffs = [];
$query_staffs = "SELECT * FROM ckey_staffs LEFT JOIN ckey_roles ON ckey_staffs.role = ckey_roles.role_name;";
$result_staffs = $pdo->query($query_staffs);
$staffs = $result_staffs->fetchAll(PDO::FETCH_ASSOC);

// Get Roles Data
$roles = [];
$query_roles = "SELECT * FROM ckey_roles;";
$result_roles = $pdo->query($query_roles);
$roles = $result_roles->fetchAll(PDO::FETCH_ASSOC);

//Get Active Staffs Data
$active_staffs = [];
$query_active_staffs = "SELECT * FROM ckey_staffs LEFT JOIN ckey_roles ON ckey_staffs.role = ckey_roles.role_name WHERE ckey_staffs.status = 'active' AND ckey_roles.role_status = 'active';";
$result_active_staffs = $pdo->query($query_active_staffs);
$active_staffs = $result_active_staffs->fetchAll(PDO::FETCH_ASSOC);


?>
