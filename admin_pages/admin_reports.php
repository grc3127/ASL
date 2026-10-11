<?php
require_once __DIR__ . '/../auth/guard.php'; requireRole('admin','administrator'); require_once __DIR__ . '/../config/database.php';
$reportRows=[];$classes=[];$summary=['students'=>0,'completion'=>0,'quiz_average'=>null,'feedback_average'=>null,'active'=>0];
try{
 $summary['students']=(int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
 $summary['active']=(int)$pdo->query("SELECT COUNT(*) FROM users u JOIN roles r ON r.role_id=u.role_id JOIN account_status s ON s.status_id=u.status_id WHERE LOWER(r.role_name)='student' AND LOWER(s.status_name)='active'")->fetchColumn();
 $summary['completion']=(float)$pdo->query("SELECT COALESCE(ROUND(100*(SELECT COUNT(*) FROM student_progress p JOIN lessons l ON l.lesson_id=p.lesson_id WHERE p.status='completed' AND l.status='published')/NULLIF((SELECT COUNT(*) FROM students)*(SELECT COUNT(*) FROM lessons WHERE status='published'),0),1),0)")->fetchColumn();
 $summary['quiz_average']=$pdo->query('SELECT ROUND(AVG(percentage),1) FROM quiz_attempts WHERE completed_at IS NOT NULL')->fetchColumn();
 $summary['feedback_average']=$pdo->query('SELECT ROUND(AVG(rating),1) FROM feedback')->fetchColumn();
 $classes=$pdo->query("SELECT class_id,class_name,section,school_year FROM classes WHERE status='active' ORDER BY school_year DESC,class_name,section")->fetchAll();
 $reportRows=$pdo->query("SELECT s.student_id,s.student_number,CONCAT_WS(' ',s.first_name,s.middle_name,s.last_name) student_name,
 GROUP_CONCAT(DISTINCT CONCAT(c.class_name,IF(c.section IS NULL OR c.section='', '',CONCAT(' - ',c.section))) SEPARATOR ', ') class_names,
 (SELECT COUNT(*) FROM lessons l WHERE l.status='published') total_lessons,
 (SELECT COUNT(*) FROM student_progress p JOIN lessons l ON l.lesson_id=p.lesson_id WHERE p.student_id=s.student_id AND p.status='completed' AND l.status='published') completed_lessons,
 (SELECT ROUND(AVG(qa.percentage),1) FROM quiz_attempts qa WHERE qa.student_id=s.student_id AND qa.completed_at IS NOT NULL) quiz_average,
 (SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.student_id=s.student_id AND qa.completed_at IS NOT NULL) quiz_attempts,
 (SELECT COUNT(*) FROM student_achievements sa WHERE sa.student_id=s.student_id) achievements,
 (SELECT MAX(COALESCE(p.last_accessed,p.completed_at)) FROM student_progress p WHERE p.student_id=s.student_id) last_active
 FROM students s LEFT JOIN class_students cs ON cs.student_id=s.student_id AND cs.status='active' LEFT JOIN classes c ON c.class_id=cs.class_id AND c.status='active'
 GROUP BY s.student_id,s.student_number,s.first_name,s.middle_name,s.last_name ORDER BY s.last_name,s.first_name")->fetchAll();
}catch(Throwable $e){error_log('KIDSS reports: '.$e->getMessage());$loadError='Report data could not be loaded. Check the database connection.';}
$h=static fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');
?>
<div class="admin-topbar"><div><h1>Reports</h1><p>Live reports generated from student progress, quiz attempts, achievements, and feedback.</p></div></div>
<?php if(!empty($loadError)): ?><div class="alert alert-warning"><?= $h($loadError) ?></div><?php endif; ?>
<div class="admin-grid">
<div class="admin-stat"><div class="label">STUDENT ACCOUNTS</div><div class="value"><?= (int)$summary['students'] ?></div><div class="meta"><?= (int)$summary['active'] ?> active</div></div>
<div class="admin-stat"><div class="label">LESSON COMPLETION</div><div class="value"><?= $h($summary['completion']) ?>%</div><div class="meta">Completed student-lesson records</div></div>
<div class="admin-stat"><div class="label">AVERAGE QUIZ SCORE</div><div class="value"><?= $summary['quiz_average']===null?'—':$h($summary['quiz_average']).'%' ?></div><div class="meta">Completed quiz attempts</div></div>
<div class="admin-stat"><div class="label">FEEDBACK RATING</div><div class="value"><?= $summary['feedback_average']===null?'—':$h($summary['feedback_average']).'/5' ?></div><div class="meta">Average student rating</div></div>
</div>
<div class="admin-card"><div class="admin-toolbar"><input id="reportSearch" class="admin-input" type="search" placeholder="Search student…" style="flex:1;min-width:180px"><select id="reportClass" class="admin-select"><option value="">All classes</option><?php foreach($classes as $class): $label=$class['class_name'].($class['section']?' - '.$class['section']:'').' ('.$class['school_year'].')'; ?><option value="<?= $h($class['class_name'].($class['section']?' - '.$class['section']:'')) ?>"><?= $h($label) ?></option><?php endforeach; ?></select><button id="exportReport" class="admin-btn admin-btn-primary" type="button"><i class="bi bi-download"></i> Export CSV</button></div>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Student</th><th>Class / Section</th><th>Lessons</th><th>Quiz average</th><th>Quiz attempts</th><th>Achievements</th><th>Last active</th></tr></thead><tbody id="reportRows">
<?php if(!$reportRows): ?><tr><td colspan="7" class="text-center text-muted py-4">No student records found.</td></tr><?php else: foreach($reportRows as $row): $total=(int)$row['total_lessons'];$done=(int)$row['completed_lessons'];$classNames=$row['class_names']?:'Not assigned'; ?><tr data-class="<?= $h($classNames) ?>"><td><?= $h($row['student_name']) ?><br><small><?= $h($row['student_number']??'') ?></small></td><td><?= $h($classNames) ?></td><td><?= $done ?>/<?= $total ?> (<?= $total?round($done/$total*100):0 ?>%)</td><td><?= $row['quiz_average']===null?'—':$h($row['quiz_average']).'%' ?></td><td><?= (int)$row['quiz_attempts'] ?></td><td><?= (int)$row['achievements'] ?></td><td><?= $row['last_active']?$h($row['last_active']):'No activity yet' ?></td></tr><?php endforeach; endif; ?>
<tr id="reportNoMatches" hidden><td colspan="7" class="text-center text-muted py-4">No student records match your search.</td></tr></tbody></table></div><p id="reportCount" class="text-muted small mt-3 mb-0"><?= count($reportRows) ?> student records</p></div>
<script>
(function(){
 const search=document.getElementById('reportSearch'),cls=document.getElementById('reportClass'),tbody=document.getElementById('reportRows'),count=document.getElementById('reportCount'),empty=document.getElementById('reportNoMatches');
 function filtered(){
  const term=search.value.trim().toLocaleLowerCase(),selectedClass=cls.value;
  return Array.from(tbody.querySelectorAll('tr[data-class]')).filter(row=>{
   const matchesText=row.textContent.toLocaleLowerCase().includes(term);
   const classes=row.dataset.class.split(', ').map(value=>value.trim());
   return matchesText&&(!selectedClass||classes.includes(selectedClass));
  });
 }
 function apply(){
  const all=Array.from(tbody.querySelectorAll('tr[data-class]')),shown=new Set(filtered());
  all.forEach(row=>{row.style.display=shown.has(row)?'table-row':'none';});
  if(empty)empty.hidden=shown.size!==0||all.length===0;
  count.textContent=shown.size+' student record(s) shown.';
 }
 search.addEventListener('input',apply);cls.addEventListener('change',apply);
 document.getElementById('exportReport').addEventListener('click',()=>{
  const rows=filtered(),header=['Student','Class / Section','Lessons completed','Quiz average','Quiz attempts','Achievements','Last active'];
  const data=[header,...rows.map(row=>Array.from(row.cells).map(cell=>cell.innerText.replace(/\\s+/g,' ').trim()))];
  const csv=data.map(row=>row.map(value=>'"'+String(value).replace(/"/g,'""')+'"').join(',')).join('\\r\\n');
  const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'}),url=URL.createObjectURL(blob),a=document.createElement('a');
  a.href=url;a.download='kidss-student-progress-report.csv';document.body.appendChild(a);a.click();a.remove();URL.revokeObjectURL(url);
 });
 apply();
})();
</script>