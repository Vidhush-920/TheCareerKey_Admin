<?php
require_once __DIR__ . '/../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$rec_id = $_POST['rec_id'] ?? '';
$notes = $_POST['notes'] ?? '';

if (empty($rec_id)) {
    echo json_encode(['success' => false, 'message' => 'Record ID is required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE ckey_results SET notes = :notes WHERE rec_id = :rec_id");
    $stmt->execute([':notes' => $notes, ':rec_id' => $rec_id]);

    // Fetch the updated notes
    $fetch = $pdo->prepare("SELECT notes FROM ckey_results WHERE rec_id = :rec_id");
    $fetch->execute([':rec_id' => $rec_id]);
    $updated_notes = $fetch->fetchColumn();

    echo json_encode(['success' => true, 'message' => 'Notes updated successfully.','recid' => $rec_id, 'notes' => $updated_notes]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>