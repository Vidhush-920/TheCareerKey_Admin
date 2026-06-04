<?php 

require_once __DIR__ . '/../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$nic = $_POST['nic'] ?? '';

if (empty($nic)) {
    echo json_encode(['success' => false, 'message' => "Person's NIC is required."]);
    exit;
}

try {
    //SQL Query to delete person records
    $stmt = $pdo->prepare("DELETE FROM ckey_results WHERE nic = :nic");
    $stmt->execute([':nic' => $nic]);

    echo json_encode(['success' => true, 'message' => "All Records of this Person deleted successfully."]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

?>