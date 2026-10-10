<?php
require_once __DIR__ . '/../auth/guard.php';
requireRole('student');
require_once __DIR__ . '/../config/database.php';
$studentName = htmlspecialchars((string)($_SESSION['name'] ?? 'Student'), ENT_QUOTES, 'UTF-8');
$publishedTotal=0;$completedTotal=0;$quizAttempts=0;$quizAverage=null;$achievementTotal=0;$categories=[];$errorMessage='';
try {
 $studentStmt=$pdo->prepare('SELECT student_id FROM students WHERE user_id=?');$studentStmt->execute([(int)($_SESSION['user_id']??0)]);$studentId=(int)$studentStmt->fetchColumn();
 $publishedTotal=(int)$pdo->query("SELECT COUNT(*) FROM lessons WHERE status='published'")->fetchColumn();
 if($studentId){
  $stmt=$pdo->prepare("SELECT COUNT(*) FROM student_progress p INNER JOIN lessons l ON l.lesson_id=p.lesson_id WHERE p.student_id=? AND p.status='completed' AND l.status='published'");$stmt->execute([$studentId]);$completedTotal=(int)$stmt->fetchColumn();
  $stmt=$pdo->prepare("SELECT COUNT(*) AS attempts, AVG(percentage) AS avg_percentage FROM quiz_attempts WHERE student_id=? AND completed_at IS NOT NULL");$stmt->execute([$studentId]);$quizRow=$stmt->fetch();$quizAttempts=(int)$quizRow['attempts'];$quizAverage=$quizRow['avg_percentage']===null?null:(int)round((float)$quizRow['avg_percentage']);
  $stmt=$pdo->prepare('SELECT COUNT(*) FROM student_achievements WHERE student_id=?');$stmt->execute([$studentId]);$achievementTotal=(int)$stmt->fetchColumn();
  $stmt=$pdo->prepare("SELECT c.category_id,c.category_name,COUNT(l.lesson_id) AS total_lessons,SUM(CASE WHEN p.status='completed' THEN 1 ELSE 0 END) AS completed_lessons
    FROM lesson_categories c LEFT JOIN lessons l ON l.category_id=c.category_id AND l.status='published'
    LEFT JOIN student_progress p ON p.lesson_id=l.lesson_id AND p.student_id=?
    WHERE c.status='active' GROUP BY c.category_id,c.category_name,c.display_order ORDER BY c.display_order,c.category_name");$stmt->execute([$studentId]);$categories=$stmt->fetchAll();
 } else {
  $categories=$pdo->query("SELECT c.category_id,c.category_name,COUNT(l.lesson_id) AS total_lessons,0 AS completed_lessons FROM lesson_categories c LEFT JOIN lessons l ON l.category_id=c.category_id AND l.status='published' WHERE c.status='active' GROUP BY c.category_id,c.category_name,c.display_order ORDER BY c.display_order,c.category_name")->fetchAll();
 }
} catch(Throwable $e){error_log('KIDSS student progress query error: '.$e->getMessage());$errorMessage='Progress could not be loaded. Please check the database connection.';}
$percent=$publishedTotal?min(100,(int)round($completedTotal/$publishedTotal*100)):0;
$h=static fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');
?>
<style>
.student-progress-page{padding:8px 2px 28px;color:#24355f}.student-progress-page h1{font-size:1.8rem;font-weight:850;margin-bottom:5px}.student-progress-page .muted{color:#6b7280}.progress-summary-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin:22px 0}.progress-summary-card,.progress-detail-card{background:#fff;border:1px solid #e8ebf3;border-radius:18px;padding:20px;box-shadow:0 7px 20px rgba(40,55,100,.04)}.progress-summary-card small{display:block;color:#6b7280;font-weight:700}.progress-summary-card strong{display:block;font-size:1.8rem;margin:7px 0}.progress-detail-card h2{font-size:1.1rem;font-weight:800}.student-progress-page .progress{height:10px;background:#eceaf5}.student-progress-page .progress-bar{background:#7048e8}@media(max-width:760px){.progress-summary-grid{grid-template-columns:1fr}}
</style>
<div class="student-progress-page">
 <h1>My Progress</h1><p class="muted">Keep going, <?= $studentName ?>! Your progress updates as you complete published lessons.</p>
 <?php if($errorMessage): ?><div class="alert alert-warning"><?= $h($errorMessage) ?></div><?php endif; ?>
 <div class="progress-summary-grid">
  <div class="progress-summary-card"><small>Lessons completed</small><strong><?= $completedTotal ?> <span class="muted" style="font-size:1rem">/ <?= $publishedTotal ?></span></strong><div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $percent ?>%"></div></div><small class="mt-2"><?= $percent ?>% completed</small></div>
  <div class="progress-summary-card"><small>Quiz average</small><strong><?= $quizAverage===null?'—':$quizAverage.'%' ?></strong><div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="<?= $quizAverage??0 ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $quizAverage??0 ?>%"></div></div><small class="mt-2"><?= $quizAttempts ?> completed quiz attempt(s)</small></div>
  <div class="progress-summary-card"><small>Achievements earned</small><strong><?= $achievementTotal ?> <span class="muted" style="font-size:1rem">badges</span></strong><small class="mt-2">Achievements recorded for your account</small></div>
 </div>
 <div class="progress-detail-card"><h2>Learning categories</h2>
 <?php if(!$categories): ?><p class="muted mb-0">No active lesson categories yet. They will appear after an administrator or teacher creates them.</p>
 <?php else: foreach($categories as $category): $total=(int)$category['total_lessons'];$done=(int)$category['completed_lessons'];$categoryPercent=$total?min(100,(int)round($done/$total*100)):0; ?>
 <div class="mb-3"><div class="d-flex justify-content-between mb-1"><strong><?= $h($category['category_name']) ?></strong><span><?= $done ?> / <?= $total ?></span></div><div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="<?= $categoryPercent ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $categoryPercent ?>%"></div></div></div>
 <?php endforeach; endif; ?>
 </div>
</div>
