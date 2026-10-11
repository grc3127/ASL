<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth/guard.php'; requireRole('student'); require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/achievement_engine.php';
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store');
try {
 $s=$pdo->prepare('SELECT student_id FROM students WHERE user_id=?');$s->execute([(int)$_SESSION['user_id']]);$studentId=(int)$s->fetchColumn();
 if(!$studentId){http_response_code(422);echo json_encode(['success'=>false,'message'=>'Student profile not found.']);exit;}
 refreshStudentAchievements($pdo,$studentId);
 $metrics=[];
 $q=$pdo->prepare("SELECT COUNT(*) FROM student_progress p JOIN lessons l ON l.lesson_id=p.lesson_id WHERE p.student_id=? AND p.status='completed' AND l.status='published'");$q->execute([$studentId]);$metrics['lessons_completed']=(int)$q->fetchColumn();
 $q=$pdo->prepare('SELECT COUNT(*) FROM quiz_attempts WHERE student_id=? AND completed_at IS NOT NULL');$q->execute([$studentId]);$metrics['quizzes_completed']=(int)$q->fetchColumn();
 $q=$pdo->prepare('SELECT COUNT(*) FROM quiz_attempts WHERE student_id=? AND completed_at IS NOT NULL AND percentage>=100');$q->execute([$studentId]);$metrics['perfect_quiz']=(int)$q->fetchColumn();
 $q=$pdo->prepare("SELECT COUNT(*) FROM (
  SELECT c.category_id,COUNT(l.lesson_id) total_lessons,SUM(CASE WHEN p.status='completed' THEN 1 ELSE 0 END) completed_lessons
  FROM lesson_categories c JOIN lessons l ON l.category_id=c.category_id AND l.status='published'
  LEFT JOIN student_progress p ON p.lesson_id=l.lesson_id AND p.student_id=?
  WHERE c.status='active' GROUP BY c.category_id
  HAVING COUNT(l.lesson_id)>0 AND SUM(CASE WHEN p.status='completed' THEN 1 ELSE 0 END)=COUNT(l.lesson_id)
 ) completed_categories");$q->execute([$studentId]);$metrics['category_completed']=(int)$q->fetchColumn();
 $defs=$pdo->query("SELECT achievement_id,name,description,icon,requirement_type,requirement_value FROM achievements WHERE status='active' ORDER BY achievement_id")->fetchAll();
 $earn=$pdo->prepare('INSERT IGNORE INTO student_achievements(student_id,achievement_id,earned_at) VALUES(?,?,NOW())');
 foreach($defs as $a){$type=(string)($a['requirement_type']??'');$target=max(1,(int)($a['requirement_value']??1));if(isset($metrics[$type])&&$metrics[$type]>=$target)$earn->execute([$studentId,(int)$a['achievement_id']]);}
 $q=$pdo->prepare('SELECT achievement_id,earned_at FROM student_achievements WHERE student_id=?');$q->execute([$studentId]);$earned=[];foreach($q->fetchAll() as $row)$earned[(int)$row['achievement_id']]=$row['earned_at'];
 $items=[];foreach($defs as $a){$type=(string)($a['requirement_type']??'');$target=max(1,(int)($a['requirement_value']??1));$progress=(int)($metrics[$type]??0);$a['progress']=min($progress,$target);$a['target']=$target;$a['unlocked']=isset($earned[(int)$a['achievement_id']]);$a['earned_at']=$earned[(int)$a['achievement_id']]??null;$items[]=$a;}
 echo json_encode(['success'=>true,'achievements'=>$items,'unlocked'=>count($earned),'total'=>count($items)],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
} catch(Throwable $e){error_log('KIDSS achievements error: '.$e->getMessage());http_response_code(500);echo json_encode(['success'=>false,'message'=>'Unable to load achievements.']);}
