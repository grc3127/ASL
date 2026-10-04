<?php
/*
 * Administrator shell.
 * Backend authentication should be added here once the account/session schema is finalized.
 * Example future guard:
 * if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { ... }
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIDSS - Administrator</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="admin-layout">
    <?php include 'admin_sidebar.php'; ?>

    <main class="admin-main">
        <div id="admin-page-content"></div>
    </main>
</div>
<script src="js/admin_scripts.js"></script>
</body>
</html>
