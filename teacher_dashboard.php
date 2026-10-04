<?php
require_once __DIR__ . '/auth/guard.php';
requireRole('teacher');

$teacherName = htmlspecialchars($_SESSION['name'] ?? 'Teacher', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KIDSS: Teacher Dashboard</title>
<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <small class="text-muted">Teacher Portal</small>
            <h2 class="fw-bold mb-0">Welcome, <?= $teacherName ?>!</h2>
        </div>
        <a href="logout.php" class="btn btn-outline-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100">
                <i class="bi bi-people-fill fs-1 text-primary"></i>
                <h5 class="fw-bold mt-3">My Students</h5>
                <p class="text-muted">Manage and monitor students assigned to your class.</p>
                <button class="btn btn-primary" disabled>Coming Next</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100">
                <i class="bi bi-bar-chart-fill fs-1 text-success"></i>
                <h5 class="fw-bold mt-3">Student Progress</h5>
                <p class="text-muted">Review lesson completion, quiz scores, and achievements.</p>
                <button class="btn btn-success" disabled>Coming Next</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100">
                <i class="bi bi-book-fill fs-1 text-warning"></i>
                <h5 class="fw-bold mt-3">Lessons</h5>
                <p class="text-muted">Access and later manage classroom learning materials.</p>
                <button class="btn btn-warning" disabled>Coming Next</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
