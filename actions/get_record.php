<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../db_connection.php';

    $data = [];

    // Ensure body is parsed if application/json
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
    }

    $rec_id = trim($data['rec_id'] ?? '');

    $response = ['success' => false, 'message' => 'Invalid record ID.'];

    if ($rec_id !== '') {
        $query = "SELECT * FROM ckey_results WHERE rec_id = :rec_id LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['rec_id' => $rec_id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
            $response = ['success' => true, 'record' => $record];
        } else {
            $response = ['success' => false, 'message' => 'Record not found.'];
        }
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
