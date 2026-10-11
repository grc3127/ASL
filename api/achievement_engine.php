<?php
declare(strict_types=1);
/** Award active achievement rules from persisted student activity. */
function refreshStudentAchievements(PDO $pdo, int $studentId): void {
    if ($studentId < 1) return;
    $metrics=[];
    $q=$pdo->prepare("SELECT COUNT(*) FROM student_progress p JOIN lessons l ON l.lesson_id=p.lesson_id WHERE p.student_id=? AND p.status='completed' AND l.status='published'");
    $q->execute([$studentId]);$metrics['lessons_completed']=(int)$q->fetchColumn();
    $q=$pdo->prepare('SELECT COUNT(*) FROM quiz_attempts WHERE student_id=? AND completed_at IS NOT NULL');
    $q->execute([$studentId]);$metrics['quizzes_completed']=(int)$q->fetchColumn();
    $q=$pdo->prepare('SELECT COUNT(*) FROM quiz_attempts WHERE student_id=? AND completed_at IS NOT NULL AND percentage>=100');
    $q->execute([$studentId]);$metrics['perfect_quiz']=(int)$q->fetchColumn();
    $q=$pdo->prepare("SELECT COUNT(*) FROM (
        SELECT c.category_id,COUNT(l.lesson_id) total_lessons,
          SUM(CASE WHEN p.status='completed' THEN 1 ELSE 0 END) completed_lessons
        FROM lesson_categories c JOIN lessons l ON l.category_id=c.category_id AND l.status='published'
        LEFT JOIN student_progress p ON p.lesson_id=l.lesson_id AND p.student_id=?
        WHERE c.status='active' GROUP BY c.category_id
        HAVING COUNT(l.lesson_id)>0 AND SUM(CASE WHEN p.status='completed' THEN 1 ELSE 0 END)=COUNT(l.lesson_id)
      ) done_categories");
    $q->execute([$studentId]);$metrics['category_completed']=(int)$q->fetchColumn();
    $defs=$pdo->query("SELECT achievement_id,requirement_type,requirement_value FROM achievements WHERE status='active'")->fetchAll();
    $insert=$pdo->prepare('INSERT IGNORE INTO student_achievements(student_id,achievement_id,earned_at) VALUES(?,?,NOW())');
    foreach($defs as $def){$type=(string)($def['requirement_type']??'');$target=max(1,(int)($def['requirement_value']??1));if(isset($metrics[$type])&&$metrics[$type]>=$target)$insert->execute([$studentId,(int)$def['achievement_id']]);}
}
