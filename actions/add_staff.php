<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$s_user_id = $_SESSION['user_id'] ?? '';
$s_crud_staff = $_SESSION['crud_staff'] ?? 0;
$s_crud_admins = $_SESSION['crud_admins'] ?? 0;

header('Content-Type: application/json');

if ($s_crud_staff == 0) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to manage staff.']);
    exit;
}

require_once __DIR__ . '/../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$staff_id = trim($_POST['staff_id'] ?? '');
$role_id  = trim($_POST['role_id']  ?? '');
$username = trim($_POST['username'] ?? '');
$fname    = trim($_POST['fname']    ?? '');
$lname    = trim($_POST['lname']    ?? '');
$nic      = trim($_POST['nic']      ?? '');
$email    = trim($_POST['email']    ?? '');
$phone    = trim($_POST['phone']    ?? '');
$password = $_POST['password'] ?? '';

$is_update = !empty($staff_id);

// Validate required fields
if (empty($role_id) || empty($username) || empty($fname) || empty($nic) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!$is_update && empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required for new staff.']);
    exit;
}

try {
    // Resolve role name from role_id
    $roleStmt = $pdo->prepare("SELECT role_name, role_status FROM ckey_roles WHERE role_id = :role_id");
    $roleStmt->execute([':role_id' => $role_id]);
    $roleRow = $roleStmt->fetch(PDO::FETCH_ASSOC);

    if (!$roleRow) {
        echo json_encode(['success' => false, 'message' => 'Invalid role selected.']);
        exit;
    }
    $role_name = $roleRow['role_name'];

    // Check permission restrictions for s_crud_admins == 0
    if ($s_crud_admins == 0) {
        if ($is_update) {
            // Fetch current role of the staff being edited to ensure it is not admin/superadmin,
            // and ensure that the role is not changed.
            $existStmt = $pdo->prepare("SELECT role FROM ckey_staffs WHERE staff_id = :staff_id");
            $existStmt->execute([':staff_id' => $staff_id]);
            $existingStaff = $existStmt->fetch(PDO::FETCH_ASSOC);
            if (!$existingStaff) {
                echo json_encode(['success' => false, 'message' => 'Staff member not found.']);
                exit;
            }
            if (($existingStaff['role'] === 'admin' || $existingStaff['role'] === 'superadmin') && $staff_id != $s_user_id) {
                echo json_encode(['success' => false, 'message' => 'You do not have permission to modify admin or superadmin accounts.']);
                exit;
            }
            if ($role_name !== $existingStaff['role']) {
                echo json_encode(['success' => false, 'message' => 'You do not have permission to change roles.']);
                exit;
            }
        } else {
            // New staff role cannot be admin or superadmin
            if ($role_name === 'admin' || $role_name === 'superadmin') {
                echo json_encode(['success' => false, 'message' => 'You do not have permission to create admin or superadmin accounts.']);
                exit;
            }
        }
    }

    // Check duplicate username
    $sql = "SELECT staff_id FROM ckey_staffs WHERE username = :username" . ($is_update ? " AND staff_id != :staff_id" : "");
    $dupStmt = $pdo->prepare($sql);
    $params = [':username' => $username];
    if ($is_update) $params[':staff_id'] = $staff_id;
    $dupStmt->execute($params);
    if ($dupStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username already exists.']);
        exit;
    }

    // Check duplicate NIC
    $sql = "SELECT staff_id FROM ckey_staffs WHERE nic = :nic" . ($is_update ? " AND staff_id != :staff_id" : "");
    $nicStmt = $pdo->prepare($sql);
    $params = [':nic' => $nic];
    if ($is_update) $params[':staff_id'] = $staff_id;
    $nicStmt->execute($params);
    if ($nicStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'NIC number already exists.']);
        exit;
    }

    //Check Duplicate Email
    $sql = "SELECT staff_id FROM ckey_staffs WHERE email = :email" . ($is_update ? " AND staff_id != :staff_id" : "");
    $emailStmt = $pdo->prepare($sql);
    $params = [':email' => $email];
    if ($is_update) $params[':staff_id'] = $staff_id;
    $emailStmt->execute($params);
    if ($emailStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
        exit;
    }

    if ($is_update) {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE ckey_staffs SET role=:role, username=:username, fname=:fname, lname=:lname, nic=:nic, email=:email, phone=:phone, password_hash=:password WHERE staff_id=:staff_id");
            $stmt->execute([':role'=>$role_name,':username'=>$username,':fname'=>$fname,':lname'=>$lname,':nic'=>$nic,':email'=>$email,':phone'=>$phone,':password'=>$hashed,':staff_id'=>$staff_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE ckey_staffs SET role=:role, username=:username, fname=:fname, lname=:lname, nic=:nic, email=:email, phone=:phone WHERE staff_id=:staff_id");
            $stmt->execute([':role'=>$role_name,':username'=>$username,':fname'=>$fname,':lname'=>$lname,':nic'=>$nic,':email'=>$email,':phone'=>$phone,':staff_id'=>$staff_id]);
        }
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO ckey_staffs (role, username, password_hash, fname, lname, nic, email, phone, status) VALUES (:role,:username,:password,:fname,:lname,:nic,:email,:phone,'active')");
        $stmt->execute([':role'=>$role_name,':username'=>$username,':password'=>$hashed,':fname'=>$fname,':lname'=>$lname,':nic'=>$nic,':email'=>$email,':phone'=>$phone]);
        $staff_id = $pdo->lastInsertId();
    }

    // Return the full staff row (with role info) for DOM update
    $fetchStmt = $pdo->prepare("SELECT s.*, r.role_id, r.role_name, r.role_status FROM ckey_staffs s LEFT JOIN ckey_roles r ON s.role = r.role_name WHERE s.staff_id = :staff_id");
    $fetchStmt->execute([':staff_id' => $staff_id]);
    $staff = $fetchStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'message' => $is_update ? 'Staff updated successfully.' : 'Staff added successfully.', 'staff' => $staff, 'is_update' => $is_update]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

?>
