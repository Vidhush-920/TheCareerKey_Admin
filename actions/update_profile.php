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

// ... existing code continues unchanged ...

$username = trim($_POST['profile-user-name'] ?? '');
$fname    = trim($_POST['profile-first-name']    ?? '');
$lname    = trim($_POST['profile-last-name']    ?? '');
$nic      = trim($_POST['profile-nic']      ?? '');
$email    = trim($_POST['profile-email']    ?? '');
$phone    = trim($_POST['profile-mobile']    ?? '');

if(empty($username) || empty($fname) || empty($lname) || empty($nic) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE ckey_staffs SET username=:username, fname=:fname, lname=:lname, nic=:nic, email=:email, phone=:phone WHERE staff_id=:staff_id");
    $stmt->execute([':username'=> $username, ':fname'=> $fname, ':lname'=> $lname, ':nic'=> $nic, ':email'=> $email, ':phone'=> $phone, ':staff_id'=> $staff_id]);

    // Return the full staff row (with role info) for DOM update
    $refStmt = $pdo->prepare("SELECT s.*, r.role_id, r.role_name, r.role_status FROM ckey_staffs s LEFT JOIN ckey_roles r ON s.role = r.role_name WHERE s.staff_id = :staff_id");
    $refStmt->execute([':staff_id' => $staff_id]);
    $updatedProfile = $refStmt->fetch(PDO::FETCH_ASSOC);

    // Update session variables
    $_SESSION['username'] = $updatedProfile['username'];
    $_SESSION['fname']    = $updatedProfile['fname'];
    $_SESSION['lname']    = $updatedProfile['lname'];
    $_SESSION['nic']      = $updatedProfile['nic'];
    $_SESSION['email']    = $updatedProfile['email'];
    $_SESSION['phone']    = $updatedProfile['phone'];

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully.',
        'profile' => $updatedProfile
    ]);
    exit;

} catch(Exception $ex) {
    echo json_encode(['success' => false, 'message' => $ex->getMessage()]);
    exit;
}

