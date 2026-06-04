<?php 

require_once __DIR__ . '/../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$rec_id = $_POST['rec_id'] ?? '';

if (empty($rec_id)) {
    echo json_encode(['success' => false, 'message' => 'Record ID is required.']);
    exit;
}

try {
    //SQL Query to delete record
    $stmt = $pdo->prepare("DELETE FROM ckey_results WHERE rec_id = :rec_id");
    $stmt->execute([':rec_id' => $rec_id]);

    echo json_encode(['success' => true, 'message' => 'Record deleted successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

?>