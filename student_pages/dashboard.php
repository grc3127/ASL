<?php
require_once __DIR__ . '/../auth/guard.php';
requireRole('student');
require_once __DIR__ . '/../config/database.php';
$name = (string)($_SESSION['name'] ?? 'Student');
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$publishedTotal = 0; $completedTotal = 0; $quizAttempts = 0; $achievementTotal = 0; $recentLessons = []; $resumeLesson = null; $loadError = false;
try {
  $studentStmt=$pdo->prepare('SELECT student_id FROM students WHERE user_id=?');
  $studentStmt->execute([(int)($_SESSION['user_id'] ?? 0)]);
  $studentId=(int)$studentStmt->fetchColumn();
  $publishedTotal=(int)$pdo->query("SELECT COUNT(*) FROM lessons WHERE status='published'")->fetchColumn();
  if ($studentId) {
    $stmt=$pdo->prepare("SELECT COUNT(*) FROM student_progress p INNER JOIN lessons l ON l.lesson_id=p.lesson_id WHERE p.student_id=? AND p.status='completed' AND l.status='published'");
    $stmt->execute([$studentId]);$completedTotal=(int)$stmt->fetchColumn();
    $stmt=$pdo->prepare('SELECT COUNT(*) FROM quiz_attempts WHERE student_id=? AND completed_at IS NOT NULL');$stmt->execute([$studentId]);$quizAttempts=(int)$stmt->fetchColumn();
    $stmt=$pdo->prepare('SELECT COUNT(*) FROM student_achievements WHERE student_id=?');$stmt->execute([$studentId]);$achievementTotal=(int)$stmt->fetchColumn();
    $stmt=$pdo->prepare("SELECT l.lesson_id,l.title,l.description,c.category_name,p.status,p.progress_percent,p.last_accessed
      FROM student_progress p INNER JOIN lessons l ON l.lesson_id=p.lesson_id INNER JOIN lesson_categories c ON c.category_id=l.category_id
      WHERE p.student_id=? AND l.status='published' ORDER BY COALESCE(p.last_accessed,p.completed_at) DESC LIMIT 3");
    $stmt->execute([$studentId]);$recentLessons=$stmt->fetchAll();
    $stmt=$pdo->prepare("SELECT l.lesson_id,l.title,l.description FROM lessons l LEFT JOIN student_progress p ON p.lesson_id=l.lesson_id AND p.student_id=? WHERE l.status='published' ORDER BY CASE WHEN p.status='in_progress' THEN 0 WHEN p.status='not_started' OR p.status IS NULL THEN 1 ELSE 2 END,l.display_order,l.title LIMIT 1");
    $stmt->execute([$studentId]);$resumeLesson=$stmt->fetch() ?: null;
  } else {
    $resumeLesson=$pdo->query("SELECT lesson_id,title,description FROM lessons WHERE status='published' ORDER BY display_order,title LIMIT 1")->fetch() ?: null;
  }
} catch (Throwable $e) {
  error_log('KIDSS student dashboard query error: '.$e->getMessage());
  $loadError=true;
}
$percent=$publishedTotal?min(100,(int)round($completedTotal/$publishedTotal*100)):0;
?>
<div class="row align-items-center mb-4 g-3">
 <div class="col-12 col-md-7"><h2 class="fw-extrabold mb-0 fs-3">Hi, <?= $h($name) ?>! 👋</h2><p class="text-muted fw-semibold mb-0">Ready to learn and have fun today?</p></div>
 <div class="col-12 col-md-5 d-flex justify-content-md-end"><div class="profile-pill d-flex align-items-center"><img src="https://api.dicebear.com/7.x/bottts/svg?seed=<?= rawurlencode($name) ?>" alt="Student avatar" class="user-avatar"><div class="lh-1 me-2"><div class="fw-bold fs-6"><?= $h($name) ?></div><small class="text-muted">Student</small></div></div></div>
</div>
<?php if($loadError): ?><div class="alert alert-warning">Some dashboard information could not be loaded. Check your database connection.</div><?php endif; ?>
<div class="hero-banner mb-4" style="background-image:url('assets/img/bg2.png');background-repeat:no-repeat;background-size:100% 100%">
 <div class="row align-items-center g-4"><div class="col-12 col-lg-7"><span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold mb-2">Continue Learning</span>
 <?php if($resumeLesson): ?><h1 class="display-5 fw-extrabold text-dark mb-2"><?= $h($resumeLesson['title']) ?></h1><p class="text-secondary fw-semibold mb-4 fs-6" style="max-width:400px"><?= $h($resumeLesson['description'] ?: 'Open this lesson and practice the sign.') ?></p><button type="button" class="btn btn-resume d-inline-flex align-items-center gap-2" onclick="loadPage('lessons')"><i class="bi bi-play-fill fs-5"></i> Open Lessons</button>
 <?php else: ?><h1 class="display-5 fw-extrabold text-dark mb-2">Your learning journey starts here!</h1><p class="text-secondary fw-semibold mb-4 fs-6">There are no published lessons yet. Check back after your teacher publishes a lesson.</p><?php endif; ?>
 </div><div class="col-12 col-lg-5"><div class="goal-card shadow-sm"><div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-bullseye text-primary fs-5"></i><h6 class="fw-bold mb-0">Lesson Progress</h6></div><div class="mb-2"><span class="fs-4 fw-bolder"><?= $completedTotal ?> / <?= $publishedTotal ?></span> <span class="text-muted fw-bold">Lessons completed</span></div><div class="progress mb-3" style="height:10px;border-radius:10px"><div class="progress-bar bg-success" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $percent ?>%;border-radius:10px"></div></div><small class="text-muted fw-bold"><?= $percent ?>% of published lessons completed</small></div></div></div>
</div>
<div class="row g-4">
 <div class="col-12 col-lg-4"><div class="card-custom h-100"><h6 class="fw-bold mb-3">My Progress</h6><div class="d-flex align-items-center gap-3 mb-3"><div class="donut-chart"><div class="donut-chart-center"><span class="fw-extrabold fs-4 lh-1"><?= $percent ?>%</span><small class="text-muted text-center" style="font-size:.6rem">Overall</small></div></div><div><div class="mb-2"><small class="text-muted fw-bold d-block">Lessons Completed</small><span class="fw-extrabold fs-5"><?= $completedTotal ?> / <?= $publishedTotal ?></span></div><div><small class="text-muted fw-bold d-block">Quizzes Completed</small><span class="fw-extrabold fs-5 text-primary"><?= $quizAttempts ?></span></div></div></div><a href="#" class="btn btn-view-all" onclick="event.preventDefault();loadPage('progress')">View Details</a></div></div>
 <div class="col-12 col-lg-4"><div class="card-custom h-100"><div class="d-flex justify-content-between align-items-center mb-3"><h6 class="fw-bold mb-0">Recent Lessons</h6><a href="#" class="btn btn-view-all" onclick="event.preventDefault();loadPage('lessons')">View All</a></div>
 <?php if(!$recentLessons): ?><p class="text-muted small">Your recent lessons will appear here after you open a published lesson.</p><?php else: foreach($recentLessons as $lesson): ?><div class="lesson-row"><div class="lesson-icon bg-success"><i class="bi bi-book"></i></div><div class="flex-grow-1"><h6 class="fw-bold mb-0 fs-6"><?= $h($lesson['title']) ?></h6><small class="text-muted"><?= $h($lesson['category_name']) ?></small></div><?php if($lesson['status']==='completed'): ?><i class="bi bi-check-circle-fill text-success fs-5"></i><?php else: ?><span class="badge bg-primary-subtle text-primary rounded-pill"><?= (int)$lesson['progress_percent'] ?>%</span><?php endif; ?></div><?php endforeach; endif; ?>
 <button type="button" class="btn btn-light w-100 fw-bold text-primary py-2 rounded-pill mt-2" onclick="loadPage('lessons')">Continue Learning</button></div></div>
 <div class="col-12 col-lg-4"><div class="card-custom h-100"><div class="d-flex justify-content-between align-items-center mb-3"><h6 class="fw-bold mb-0">Achievements</h6><a href="#" class="btn btn-view-all" onclick="event.preventDefault();loadPage('achievements')">View All</a></div><div class="text-center my-4"><i class="bi bi-trophy-fill text-warning display-5"></i><h6 class="fw-bold mt-2 mb-1"><?= $achievementTotal ?> earned</h6><p class="text-muted small mb-0">Achievements earned from recorded activity.</p></div></div></div>
</div>
