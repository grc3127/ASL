<?php
/*
 * KIDSS Authentication Entry Point
 *
 * IMPORTANT:
 * This file is the only place that should need to change when the final
 * database/account schema is connected.
 *
 * Expected POST:
 *   username
 *   password
 *   role = student | teacher | admin
 *
 * Replace authenticateUser() with your actual prepared MySQL query.
 */

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$requestedRole = strtolower(trim($_POST['role'] ?? ''));

$allowedRoles = ['student', 'teacher', 'admin'];

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Please enter your username/email and password.';
    header('Location: ../login.php');
    exit;
}

if (!in_array($requestedRole, $allowedRoles, true)) {
    $_SESSION['login_error'] = 'Please select a valid account role.';
    header('Location: ../login.php');
    exit;
}

/*
 * -------------------------------------------------------------------------
 * DATABASE INTEGRATION POINT
 * -------------------------------------------------------------------------
 *
 * Replace the demo return below with a prepared statement against your
 * finalized account tables.
 *
 * Recommended return shape:
 *
 * [
 *   'user_id' => (int)$row['user_id'],
 *   'role'    => strtolower($row['role']),
 *   'name'    => $row['full_name'],
 *   'email'   => $row['email']
 * ]
 *
 * Passwords should be stored using password_hash() and checked using
 * password_verify().
 */
$user = authenticateUser($username, $password, $requestedRole);

if ($user === false) {
    $_SESSION['login_error'] = 'Invalid credentials or account role.';
    header('Location: ../login.php');
    exit;
}

session_regenerate_id(true);

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['role'] = $user['role'];
$_SESSION['name'] = $user['name'] ?? '';
$_SESSION['email'] = $user['email'] ?? '';
$_SESSION['logged_in'] = true;

header('Location: role_redirect.php');
exit;


function authenticateUser(string $username, string $password, string $requestedRole): array|false
{
    /*
     * TEMPORARY:
     * Database authentication is intentionally not guessed because the
     * repository currently does not contain a database connection/schema.
     *
     * Once your SQL is available, implement:
     *
     * require_once __DIR__ . '/../db.php';
     * $stmt = $conn->prepare('...');
     * ...
     * return [...]
     *
     * DO NOT put plaintext production passwords here.
     */

    return false;
}
