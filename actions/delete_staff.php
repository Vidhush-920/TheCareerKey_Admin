<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$s_crud_staff = $_SESSION['crud_staff'] ?? 0;
$s_crud_admins = $_SESSION['crud_admins'] ?? 0;

header('Content-Type: application/json');

if ($s_crud_staff == 0) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to delete staff.']);
    exit;
}

require_once __DIR__ . '/../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$staff_id = $_POST['staff_id'] ?? '';

if (empty($staff_id)) {
    echo json_encode(['success' => false, 'message' => 'Staff ID is required.']);
    exit;
}

try {
    if ($s_crud_admins == 0) {
        $checkStmt = $pdo->prepare("SELECT role FROM ckey_staffs WHERE staff_id = :staff_id");
        $checkStmt->execute([':staff_id' => $staff_id]);
        $targetRole = $checkStmt->fetchColumn();

        if ($targetRole === 'admin' || $targetRole === 'superadmin') {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to delete admin or superadmin accounts.']);
            exit;
        }
    }

    //SQL Query to delete staff
    $stmt = $pdo->prepare("DELETE FROM ckey_staffs WHERE staff_id = :staff_id");
    $stmt->execute([':staff_id' => $staff_id]);

    // Fetch the updated staff records from the database
    // $fetch = $pdo->prepare("SELECT * FROM ckey_staffs LEFT JOIN ckey_roles ON ckey_staffs.role = ckey_roles.role_name");
    // $fetch->execute();
    // $new_staffs = $fetch->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'message' => 'Staff deleted successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

?>