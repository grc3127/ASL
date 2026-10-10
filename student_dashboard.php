<?php
require_once __DIR__ . '/auth/guard.php';
requireRole('student');
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>KIDSS: Student Dashboard</title>
<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link href="assets/css/main.css" rel="stylesheet">
</head><body><div class="main-layout"><div id="sidebar-content"><?php include __DIR__ . '/sidebar.php'; ?></div><main class="content-area"><div id="page-content" class="main-content-wrapper"></div></main></div>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script><script src="js/lesson_script.js"></script><script src="js/scripts.js"></script></body></html>
