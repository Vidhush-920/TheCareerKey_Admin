<?php
require_once __DIR__ . '/../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$role_id = $_POST['role_id'] ?? '';

if (empty($role_id)) {
    echo json_encode(['success' => false, 'message' => 'Role ID is required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE ckey_roles SET role_status = CASE WHEN role_status = 'active' THEN 'inactive' ELSE 'active' END WHERE role_id = :role_id");
    $stmt->execute([':role_id' => $role_id]);

    // Fetch the updated status so the frontend can sync without a page reload
    $fetch = $pdo->prepare("SELECT role_status FROM ckey_roles WHERE role_id = :role_id");
    $fetch->execute([':role_id' => $role_id]);
    $new_status = $fetch->fetchColumn();

    echo json_encode(['success' => true, 'message' => 'Status toggled successfully.', 'new_status' => $new_status]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>