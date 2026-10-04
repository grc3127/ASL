<?php
session_start();

if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header('Location: ../login.php');
    exit;
}

$role = strtolower((string)$_SESSION['role']);

switch ($role) {
    case 'admin':
    case 'administrator':
        header('Location: ../admin_dashboard.php');
        break;

    case 'teacher':
        header('Location: ../teacher_dashboard.php');
        break;

    case 'student':
        header('Location: ../student_dashboard.php');
        break;

    default:
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['login_error'] = 'Your account has an invalid role.';
        header('Location: ../login.php');
        break;
}

exit;
