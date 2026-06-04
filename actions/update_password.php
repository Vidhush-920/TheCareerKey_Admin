<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Suppress HTML warning output to ensure pure JSON responses
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
require_once __DIR__ . '/../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Determine staff ID (fallback to alternate session key)
$staff_id = $_SESSION['user_id'] ?? ($_SESSION['staff_id'] ?? '');
if (empty($staff_id)) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit;
}
// Ensure both session keys are set for future requests
$_SESSION['user_id'] = $staff_id;
$_SESSION['staff_id'] = $staff_id;

$current_password = $_POST['current-password'];
$new_password = $_POST['new-password'];
$confirm_new_password = $_POST['confirm-new-password'];
// Validate required fields
if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

// Validate new password length (minimum 6 characters)
if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
    exit;
}

// Validate new password and confirmation match
if ($new_password !== $confirm_new_password) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
    exit;
}

// Check if current password is correct
$currstmt = $pdo->prepare("SELECT password_hash FROM ckey_staffs WHERE staff_id=:staff_id");
$currstmt->execute([':staff_id'=> $staff_id]);
$currProfile = $currstmt->fetch(PDO::FETCH_ASSOC);
if (!password_verify($current_password, $currProfile['password_hash'])) {
    echo json_encode(['success' => false, 'message' => 'Current Password is not Correct.']);
    exit;
}

try {

    $stmt = $pdo->prepare("UPDATE ckey_staffs SET password_hash=:password WHERE staff_id=:staff_id");
    $stmt->execute([':password'=> password_hash($new_password, PASSWORD_DEFAULT), ':staff_id'=> $staff_id]);

    $refStmt = $pdo->prepare("SELECT s.*, r.role_id, r.role_name, r.role_status FROM ckey_staffs s LEFT JOIN ckey_roles r ON s.role = r.role_name WHERE s.staff_id = :staff_id");
    $refStmt->execute([':staff_id' => $staff_id]);
    $updatedProfile = $refStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully.',
        'profile' => $updatedProfile
    ]);
    exit;

} catch(Exception $ex) {
    echo json_encode(['success' => false, 'message' => $ex->getMessage()]);
    exit;
}