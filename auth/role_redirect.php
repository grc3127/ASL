<?php
/**
 * Redirect an authenticated user to the dashboard for their role.
 */

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    empty($_SESSION['logged_in'])
    || $_SESSION['logged_in'] !== true
    || empty($_SESSION['user_id'])
    || empty($_SESSION['role'])
) {
    header('Location: ../login.php');
    exit;
}

$role = strtolower((string) $_SESSION['role']);

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
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        session_start();
        $_SESSION['login_error'] = 'Your account has an invalid role.';
        header('Location: ../login.php');
        break;
}

exit;
