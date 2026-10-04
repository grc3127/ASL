<?php
session_start();

/*
 * Application entry point.
 * Logged-in users are routed according to their role.
 * Everyone else is sent to the KIDSS login page.
 */
if (!empty($_SESSION['logged_in']) && !empty($_SESSION['role'])) {
    header('Location: auth/role_redirect.php');
    exit;
}

header('Location: login.php');
exit;
