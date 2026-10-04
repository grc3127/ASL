<?php
/**
 * KIDSS login processor.
 *
 * Expected POST fields:
 *   username - username or email
 *   password - account password
 *   role     - student, teacher, or admin
 */

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$usernameOrEmail = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$requestedRole = strtolower(trim((string) ($_POST['role'] ?? '')));

$roleMap = [
    'admin'  => 'Administrator',
    'teacher' => 'Teacher',
    'student' => 'Student',
];

if ($usernameOrEmail === '' || $password === '') {
    setLoginError('Please enter your username/email and password.');
    redirectToLogin();
}

if (!isset($roleMap[$requestedRole])) {
    setLoginError('Please select a valid account role.');
    redirectToLogin();
}

try {
    $pdo = getDatabaseConnection();

    /*
     * The role is checked in SQL so a user cannot authenticate through a
     * different role button from the role assigned to the account.
     * Profile information is loaded from the corresponding role table.
     */
    $sql = <<<'SQL'
SELECT
    u.user_id,
    u.username,
    u.email,
    u.password_hash,
    u.role_id,
    u.status_id,
    u.last_login,
    r.role_name,
    s.status_name,
    COALESCE(
        NULLIF(TRIM(CONCAT_WS(' ', st.first_name, st.middle_name, st.last_name)), ''),
        NULLIF(TRIM(CONCAT_WS(' ', t.first_name, t.middle_name, t.last_name)), ''),
        NULLIF(TRIM(CONCAT_WS(' ', a.first_name, a.middle_name, a.last_name)), ''),
        u.username
    ) AS full_name,
    st.student_id,
    st.student_number,
    t.teacher_id,
    t.employee_number,
    a.admin_id
FROM users u
INNER JOIN roles r ON r.role_id = u.role_id
INNER JOIN account_status s ON s.status_id = u.status_id
LEFT JOIN students st ON st.user_id = u.user_id
LEFT JOIN teachers t ON t.user_id = u.user_id
LEFT JOIN admins a ON a.user_id = u.user_id
WHERE (u.username = :username OR u.email = :email)
  AND r.role_name = :role_name
LIMIT 1
SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $usernameOrEmail,
        ':email' => $usernameOrEmail,
        ':role_name' => $roleMap[$requestedRole],
    ]);

    $user = $stmt->fetch();

    if (!$user || !password_verify($password, (string) $user['password_hash'])) {
        setLoginError('Invalid username/email, password, or account role.');
        redirectToLogin();
    }

    // Only Active accounts may sign in.
    if ((int) $user['status_id'] !== 1 || strcasecmp((string) $user['status_name'], 'Active') !== 0) {
        $statusMessage = match (strtolower((string) $user['status_name'])) {
            'pending' => 'Your account is still pending approval.',
            'inactive' => 'Your account is inactive. Please contact your administrator.',
            'suspended' => 'Your account has been suspended. Please contact your administrator.',
            default => 'Your account is not currently allowed to log in.',
        };

        setLoginError($statusMessage);
        redirectToLogin();
    }

    session_regenerate_id(true);

    $role = strtolower((string) $user['role_name']);

    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = (int) $user['user_id'];
    $_SESSION['username'] = (string) $user['username'];
    $_SESSION['email'] = (string) $user['email'];
    $_SESSION['role_id'] = (int) $user['role_id'];
    $_SESSION['role'] = $role === 'administrator' ? 'admin' : $role;
    $_SESSION['role_name'] = (string) $user['role_name'];
    $_SESSION['status_id'] = (int) $user['status_id'];
    $_SESSION['status_name'] = (string) $user['status_name'];
    $_SESSION['name'] = (string) $user['full_name'];

    // Keep role-specific identifiers available for future pages.
    if ($role === 'student') {
        $_SESSION['profile_id'] = (int) $user['student_id'];
        $_SESSION['student_id'] = (int) $user['student_id'];
        $_SESSION['student_number'] = (string) ($user['student_number'] ?? '');
    } elseif ($role === 'teacher') {
        $_SESSION['profile_id'] = (int) $user['teacher_id'];
        $_SESSION['teacher_id'] = (int) $user['teacher_id'];
        $_SESSION['teacher_employee_number'] = (string) ($user['employee_number'] ?? '');
    } else {
        $_SESSION['profile_id'] = (int) $user['admin_id'];
        $_SESSION['admin_id'] = (int) $user['admin_id'];
    }

    // Update the user's last successful login.
    $update = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE user_id = :user_id');
    $update->execute([':user_id' => (int) $user['user_id']]);

    header('Location: role_redirect.php');
    exit;
} catch (Throwable $e) {
    error_log('KIDSS login error: ' . $e->getMessage());
    setLoginError('Unable to process the login right now. Please try again.');
    redirectToLogin();
}

function setLoginError(string $message): void
{
    $_SESSION['login_error'] = $message;
}

function redirectToLogin(): void
{
    header('Location: ../login.php');
    exit;
}
