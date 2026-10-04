<?php
/*
 * Include this file at the top of any protected page.
 *
 * Example:
 * require_once __DIR__ . '/auth/guard.php';
 * requireRole('admin');
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function requireLogin(): void
{
    if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
        header('Location: login.php');
        exit;
    }
}

function requireRole(string ...$allowedRoles): void
{
    requireLogin();

    $currentRole = strtolower((string)$_SESSION['role']);
    $allowedRoles = array_map('strtolower', $allowedRoles);

    if (!in_array($currentRole, $allowedRoles, true)) {
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 - Access Denied</title>
            <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container py-5">
                <div class="alert alert-danger">
                    <h4 class="alert-heading">403 - Access Denied</h4>
                    <p>You do not have permission to access this page.</p>
                    <a href="role_redirect.php" class="btn btn-danger">Return to Dashboard</a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
