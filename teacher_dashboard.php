<?php
require_once __DIR__ . '/auth/guard.php';
requireRole('teacher');
$teacherName = htmlspecialchars((string)($_SESSION['name'] ?? 'Teacher'), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KIDSS: Teacher Portal</title>
<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="assets/css/main.css" rel="stylesheet"><link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/teacher_sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-topbar"><div><h1>Welcome, <?= $teacherName ?></h1><p>Create, update, and publish lessons for students. Published lessons appear automatically in the student Lessons page.</p></div></div>
    <div id="admin-page-content"></div>
  </main>
</div>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/admin_scripts.js"></script>
</body></html>
