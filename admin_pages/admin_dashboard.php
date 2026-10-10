<?php
require_once __DIR__ . '/../auth/guard.php';
requireRole('admin', 'administrator');
require_once __DIR__ . '/../config/database.php';

$stats = ['students'=>0,'teachers'=>0,'lessons'=>0,'published'=>0,'quiz_attempts'=>0];
try {
    $stats['students'] = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
    $stats['teachers'] = (int)$pdo->query('SELECT COUNT(*) FROM teachers')->fetchColumn();
    $stats['lessons'] = (int)$pdo->query('SELECT COUNT(*) FROM lessons')->fetchColumn();
    $stats['published'] = (int)$pdo->query("SELECT COUNT(*) FROM lessons WHERE status='published'")->fetchColumn();
    $stats['quiz_attempts'] = (int)$pdo->query('SELECT COUNT(*) FROM quiz_attempts')->fetchColumn();
    $pendingUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status_id=(SELECT status_id FROM account_status WHERE status_name='Pending' LIMIT 1)")->fetchColumn();
    $feedbackCount = (int)$pdo->query('SELECT COUNT(*) FROM feedback')->fetchColumn();
    $week = $pdo->query("SELECT DATE_FORMAT(day_date,'%a') AS day_label, COALESCE(activity_count,0) AS activity_count
      FROM (
        SELECT CURDATE() - INTERVAL seq DAY AS day_date FROM (
          SELECT 0 AS seq UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6
        ) days
      ) dates
      LEFT JOIN (
        SELECT DATE(COALESCE(completed_at,last_accessed)) AS activity_date, COUNT(*) AS activity_count
        FROM student_progress WHERE COALESCE(completed_at,last_accessed) >= CURDATE() - INTERVAL 6 DAY
        GROUP BY DATE(COALESCE(completed_at,last_accessed))
      ) activity ON activity.activity_date=dates.day_date
      ORDER BY dates.day_date")->fetchAll();
    $recent = $pdo->query("SELECT al.action, al.description, al.created_at, u.username
      FROM activity_logs al LEFT JOIN users u ON u.user_id=al.user_id
      ORDER BY al.created_at DESC LIMIT 8")->fetchAll();
} catch (Throwable $e) {
    error_log('KIDSS admin dashboard query error: '.$e->getMessage());
    $pendingUsers = 0; $feedbackCount = 0; $week = []; $recent = [];
    $dashboardError = 'Some dashboard data could not be loaded. Check the database connection and imported schema.';
}
$maxActivity = max(1, ...array_map(static fn($day) => (int)$day['activity_count'], $week ?: [['activity_count'=>0]]));
$h = static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
?>
<div class="admin-topbar"><div><h1>Administrator Dashboard</h1><p>Live summary of the KIDSS database.</p></div></div>
<?php if (!empty($dashboardError)): ?><div class="alert alert-warning"><?= $h($dashboardError) ?></div><?php endif; ?>
<div class="admin-grid">
 <div class="admin-stat"><div class="label">TOTAL STUDENTS</div><div class="value"><?= $stats['students'] ?></div><div class="meta">Student profiles in database</div></div>
 <div class="admin-stat"><div class="label">TEACHERS</div><div class="value"><?= $stats['teachers'] ?></div><div class="meta">Teacher profiles in database</div></div>
 <div class="admin-stat"><div class="label">LESSONS</div><div class="value"><?= $stats['lessons'] ?></div><div class="meta"><?= $stats['published'] ?> published</div></div>
 <div class="admin-stat"><div class="label">QUIZ ATTEMPTS</div><div class="value"><?= $stats['quiz_attempts'] ?></div><div class="meta">Recorded attempts</div></div>
</div>
<div class="row g-3">
 <div class="col-lg-8"><div class="admin-card"><div class="admin-card-header"><h2>Lesson Activity</h2><span class="badge-status badge-active">Last 7 days</span></div>
 <div class="admin-chart">
 <?php if (!$week): ?><p class="text-muted">No lesson activity has been recorded yet.</p><?php else: foreach ($week as $day): $height = max(4, (int)round(((int)$day['activity_count'] / $maxActivity) * 100)); ?>
 <div class="bar" style="height:<?= $height ?>%"><span><?= $h($day['day_label']) ?> · <?= (int)$day['activity_count'] ?></span></div>
 <?php endforeach; endif; ?>
 </div></div></div>
 <div class="col-lg-4"><div class="admin-card"><div class="admin-card-header"><h2>Pending Actions</h2></div>
 <div class="lesson-row"><i class="bi bi-person-plus-fill text-primary me-2"></i><strong><?= $pendingUsers ?></strong><span class="ms-2">pending accounts</span></div>
 <div class="lesson-row"><i class="bi bi-chat-square-text-fill text-success me-2"></i><strong><?= $feedbackCount ?></strong><span class="ms-2">feedback records</span></div>
 <div class="lesson-row"><i class="bi bi-book-fill text-warning me-2"></i><strong><?= $stats['lessons'] - $stats['published'] ?></strong><span class="ms-2">draft or archived lessons</span></div>
 </div></div>
</div>
<div class="admin-card"><div class="admin-card-header"><h2>Recent Activity</h2></div><div class="admin-table-wrap"><table class="admin-table">
<thead><tr><th>User</th><th>Activity</th><th>Time</th></tr></thead><tbody>
<?php if (!$recent): ?><tr><td colspan="3" class="text-muted text-center py-4">No activity logs have been recorded yet.</td></tr>
<?php else: foreach ($recent as $item): ?><tr><td><?= $h($item['username'] ?: 'System') ?></td><td><?= $h($item['description'] ?: $item['action']) ?></td><td><?= $h(date('M j, Y g:i A', strtotime($item['created_at']))) ?></td></tr><?php endforeach; endif; ?>
</tbody></table></div></div>
