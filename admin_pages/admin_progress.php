<?php
require_once __DIR__ . '/../auth/guard.php';
requireRole('admin', 'administrator');
require_once __DIR__ . '/../config/database.php';
try {
  $students = $pdo->query("SELECT s.student_id, s.first_name, s.middle_name, s.last_name, s.student_number,
    GROUP_CONCAT(DISTINCT CONCAT(c.class_name, IF(c.section IS NULL OR c.section='', '', CONCAT(' - ',c.section))) ORDER BY c.class_name SEPARATOR ', ') AS class_names,
    (SELECT COUNT(*) FROM lessons l WHERE l.status='published') AS published_total,
    (SELECT COUNT(*) FROM student_progress p INNER JOIN lessons l2 ON l2.lesson_id=p.lesson_id WHERE p.student_id=s.student_id AND p.status='completed' AND l2.status='published') AS completed_total,
    (SELECT ROUND(AVG(qa.percentage),1) FROM quiz_attempts qa WHERE qa.student_id=s.student_id AND qa.completed_at IS NOT NULL) AS quiz_average,
    (SELECT COUNT(*) FROM student_achievements sa WHERE sa.student_id=s.student_id) AS achievement_count,
    (SELECT MAX(COALESCE(p2.last_accessed,p2.completed_at)) FROM student_progress p2 WHERE p2.student_id=s.student_id) AS last_active
    FROM students s
    LEFT JOIN class_students cs ON cs.student_id=s.student_id AND cs.status='active'
    LEFT JOIN classes c ON c.class_id=cs.class_id AND c.status='active'
    GROUP BY s.student_id,s.first_name,s.middle_name,s.last_name,s.student_number
    ORDER BY s.last_name,s.first_name")->fetchAll();
} catch (Throwable $e) {
  error_log('KIDSS admin progress query error: '.$e->getMessage());
  $students=[]; $loadError='Unable to load student progress. Check the database connection and schema.';
}
$h=static fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');
?>
<div class="admin-topbar"><div><h1>Student Progress</h1><p>Progress, quiz results, and achievements from saved student activity.</p></div></div>
<?php if(!empty($loadError)): ?><div class="alert alert-warning"><?= $h($loadError) ?></div><?php endif; ?>
<div class="admin-card">
 <div class="admin-toolbar"><input id="progressSearch" class="admin-input" type="search" placeholder="Search student or class..." style="flex:1;min-width:220px">
 <select id="progressFilter" class="admin-select"><option value="">All progress</option><option value="completed">Has completed lessons</option><option value="started">Started learning</option><option value="notstarted">Not started</option></select></div>
 <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Student</th><th>Class / Section</th><th>Lessons completed</th><th>Quiz average</th><th>Achievements</th><th>Last active</th></tr></thead><tbody id="studentProgressRows">
 <?php if(!$students): ?><tr><td colspan="6" class="text-center text-muted py-4">No student progress records found. Student accounts and completed lessons will appear here when activity is saved.</td></tr>
 <?php else: foreach($students as $student):
  $total=(int)$student['published_total'];$done=(int)$student['completed_total'];$percent=$total?min(100,(int)round($done/$total*100)):0;
  $name=trim($student['first_name'].' '.($student['middle_name']??'').' '.$student['last_name']);
  $activity=$student['last_active']?date('M j, Y g:i A',strtotime($student['last_active'])):'No activity yet';
  $state=$done>0?'completed':($student['last_active']?'started':'notstarted');
 ?>
 <tr data-state="<?= $state ?>"><td><strong><?= $h($name) ?></strong><?php if($student['student_number']): ?><br><small><?= $h($student['student_number']) ?></small><?php endif; ?></td>
 <td><?= $h($student['class_names'] ?: 'Not assigned') ?></td>
 <td><div class="d-flex align-items-center gap-2"><div class="progress-bar-admin"><span style="width:<?= $percent ?>%"></span></div><?= $done ?>/<?= $total ?> (<?= $percent ?>%)</div></td>
 <td><?= $student['quiz_average']===null?'—':$h($student['quiz_average']).'%' ?></td><td><?= (int)$student['achievement_count'] ?></td><td><?= $h($activity) ?></td></tr>
 <?php endforeach; endif; ?>
 </tbody></table></div>
 <p id="progressCount" class="text-muted small mt-3 mb-0"><?= count($students) ?> student record(s)</p>
</div>
<script>
(function(){const search=document.getElementById('progressSearch'),filter=document.getElementById('progressFilter'),tbody=document.getElementById('studentProgressRows'),count=document.getElementById('progressCount');if(!tbody)return;
function apply(){const term=search.value.trim().toLowerCase();let visible=0;tbody.querySelectorAll('tr[data-state]').forEach(row=>{const matchText=row.textContent.toLowerCase().includes(term);const matchState=!filter.value||row.dataset.state===filter.value;row.hidden=!(matchText&&matchState);if(!row.hidden)visible++;});count.textContent=visible+' student record(s)';}
search.addEventListener('input',apply);filter.addEventListener('change',apply);})();
</script>
