<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ./login.php');
    exit();
}

$s_user_id = $_SESSION['user_id'] ?? '';
$s_username = $_SESSION['username'] ?? '';
$s_firstname = $_SESSION['firstname'] ?? '';
$s_lastname = $_SESSION['lastname'] ?? '';
$s_nic = $_SESSION['nic'] ?? '';
$s_email = $_SESSION['email'] ?? '';
$s_phone = $_SESSION['phone'] ?? '';
$s_status = $_SESSION['status'] ?? '';
$s_role = $_SESSION['role'] ?? '';

$s_role_id = $_SESSION['role_id'] ?? '';
$s_role_desc = $_SESSION['role_desc'] ?? '';
$s_role_status = $_SESSION['role_status'] ?? '';

$s_crud_staff = $_SESSION['crud_staff'] ?? 0;
$s_crud_admins = $_SESSION['crud_admins'] ?? 0;
$s_update_role = $_SESSION['update_role'] ?? 0;
$s_view_results = $_SESSION['view_results'] ?? 0;
$s_crud_results = $_SESSION['crud_results'] ?? 0;
