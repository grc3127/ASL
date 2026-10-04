<?php
/**
 * KIDSS authentication and authorization guard.
 *
 * Include this file before protected page output.
 *
 * Examples:
 *   require_once __DIR__ . '/auth/guard.php';
 *   requireLogin();
 *
 *   require_once __DIR__ . '/auth/guard.php';
 *   requireRole('student');
 */

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['logged_in'])
        && $_SESSION['logged_in'] === true
        && !empty($_SESSION['user_id'])
        && !empty($_SESSION['role']);
}

function currentUser(): array
{
    return [
        'user_id' => isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0,
        'username' => (string) ($_SESSION['username'] ?? ''),
        'email' => (string) ($_SESSION['email'] ?? ''),
        'role' => strtolower((string) ($_SESSION['role'] ?? '')),
        'role_id' => isset($_SESSION['role_id']) ? (int) $_SESSION['role_id'] : 0,
        'status_id' => isset($_SESSION['status_id']) ? (int) $_SESSION['status_id'] : 0,
        'name' => (string) ($_SESSION['name'] ?? ''),
        'profile_id' => isset($_SESSION['profile_id']) ? (int) $_SESSION['profile_id'] : 0,
        'student_number' => (string) ($_SESSION['student_number'] ?? ''),
        'teacher_employee_number' => (string) ($_SESSION['teacher_employee_number'] ?? ''),
    ];
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireRole(string ...$allowedRoles): void
{
    requireLogin();

    $currentRole = strtolower((string) ($_SESSION['role'] ?? ''));
    $allowedRoles = array_map('strtolower', $allowedRoles);

    if (!in_array($currentRole, $allowedRoles, true)) {
        http_response_code(403);
        $dashboardLink = 'auth/role_redirect.php';
        $assetPrefix = '';

        // Protected pages in subdirectories need a different relative path.
        $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        if (strpos($scriptPath, '/admin_pages/') !== false || strpos($scriptPath, '/student_pages/') !== false) {
            $dashboardLink = '../auth/role_redirect.php';
            $assetPrefix = '../';
        }

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 - Access Denied</title>
            <link href="<?= htmlspecialchars($assetPrefix, ENT_QUOTES, 'UTF-8') ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container py-5">
                <div class="alert alert-danger shadow-sm">
                    <h4 class="alert-heading">403 - Access Denied</h4>
                    <p>You do not have permission to access this page.</p>
                    <a href="<?= htmlspecialchars($dashboardLink, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-danger">Return to Dashboard</a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
