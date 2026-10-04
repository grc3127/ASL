<?php
/*
 * KIDSS Student Dashboard
 *
 * The existing project currently uses a client-side page loader:
 * student_dashboard.php -> sidebar.php -> js/scripts.js -> student_pages/*.php
 *
 * Keep this shell database-free for now. Authentication/session validation can be
 * added here once the final account schema is available.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KIDSS: Kindergarten Interactive Digital Signing System</title>
<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="assets/css/main.css" rel="stylesheet">
</head>

<body>
<div class="main-layout">
    <div id="sidebar-content">
        <?php include 'sidebar.php'; ?>
    </div>

    <main class="content-area">
        <div id="page-content" class="main-content-wrapper"></div>
    </main>
</div>

<script src="js/scripts.js"></script>
</body>
</html>
