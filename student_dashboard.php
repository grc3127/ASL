<?php
require_once __DIR__ . '/auth/guard.php';
requireRole('student');

$studentName = htmlspecialchars($_SESSION['name'] ?? 'Student', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KIDSS: Student Dashboard</title>
<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="assets/css/main.css" rel="stylesheet">
</head>
<body>

<div class="main-layout">

    <div id="sidebar-content">
        <?php include __DIR__ . '/sidebar.php'; ?>
    </div>

    <main class="content-area">
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white">
            <div>
                <small class="text-muted">Student Portal</small>
                <h5 class="mb-0 fw-bold">Welcome, <?= $studentName ?>!</h5>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>

        <div id="page-content" class="main-content-wrapper"></div>
    </main>

</div>

<script src="js/scripts.js"></script>
</body>
</html>
