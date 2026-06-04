<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    header('Content-Type: application/json');

    try {
        require_once __DIR__ . '/../db_connection.php';

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $response = ['success' => false, 'message' => 'Invalid username or password.'];
        
        if ($username !== '' && $password !== '') {
            $query = "SELECT * FROM ckey_staffs LEFT JOIN ckey_roles ON ckey_staffs.role = ckey_roles.role_name WHERE ckey_staffs.username = :username LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            //if username is not matching, try matching with email
            if (!$user) {
                $query = "SELECT * FROM ckey_staffs LEFT JOIN ckey_roles ON ckey_staffs.role = ckey_roles.role_name WHERE ckey_staffs.email = :username LIMIT 1";
                $stmt = $pdo->prepare($query);
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if ($user && password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['staff_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['firstname'] = $user['fname'];
                $_SESSION['lastname'] = $user['lname'];
                $_SESSION['nic'] = $user['nic'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['phone'] = $user['phone'];
                $_SESSION['status'] = $user['status'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['role'] = $user['role_name'];
                $_SESSION['role_desc'] = $user['role_description'];
                $_SESSION['crud_staff'] = $user['crud_staff'];
                $_SESSION['crud_admins'] = $user['crud_admins'];
                $_SESSION['update_role'] = $user['update_role'];
                $_SESSION['view_results'] = $user['view_results'];
                $_SESSION['crud_results'] = $user['crud_results'];
                $_SESSION['role_status'] = $user['role_status'];

                if ($user['role_status'] != 'active') {
                    $response = ['success' => false, 'message' => 'Your Role is not Active. Please contact the Administrator.'];
                } else if ($user['status'] != 'active') {
                    $response = ['success' => false, 'message' => 'Your Account is not Active. Please contact the Administrator.'];
                } else {
                    $response = ['success' => true, 'message' => 'Login successful. Redirecting...'];
                }
            }
        }

        echo json_encode($response);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
    }
?>
