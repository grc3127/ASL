<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth/guard.php'; requireLogin(); require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json; charset=utf-8');header('Cache-Control: no-store');
function feedbackOut(array $d,int $code=200):never{http_response_code($code);echo json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
try{
 $u=currentUser();$role=$u['role'];
 if($_SERVER['REQUEST_METHOD']==='GET'){
  if(!in_array($role,['admin','administrator'],true))feedbackOut(['success'=>false,'message'=>'Administrator access required.'],403);
  $q=$pdo->query("SELECT f.feedback_id,f.rating,f.comment,f.created_at,CONCAT_WS(' ',s.first_name,s.middle_name,s.last_name) student_name,l.title lesson_title,qu.title quiz_title
    FROM feedback f JOIN students s ON s.student_id=f.student_id LEFT JOIN lessons l ON l.lesson_id=f.lesson_id LEFT JOIN quizzes qu ON qu.quiz_id=f.quiz_id ORDER BY f.created_at DESC,f.feedback_id DESC");
  feedbackOut(['success'=>true,'feedback'=>$q->fetchAll()]);
 }
 if($_SERVER['REQUEST_METHOD']!=='POST')feedbackOut(['success'=>false,'message'=>'Method not allowed.'],405);
 if($role!=='student')feedbackOut(['success'=>false,'message'=>'Only students can submit feedback.'],403);
 $d=json_decode(file_get_contents('php://input')?:'',true);if(!is_array($d))$d=$_POST;
 $rating=filter_var($d['rating']??null,FILTER_VALIDATE_INT);$comment=trim((string)($d['comment']??''));$lessonId=(int)($d['lesson_id']??0);$quizId=(int)($d['quiz_id']??0);
 if(!$rating||$rating<1||$rating>5||strlen($comment)>5000)feedbackOut(['success'=>false,'message'=>'Choose a 1–5 star rating and keep comments under 5,000 characters.'],422);
 $q=$pdo->prepare('SELECT student_id FROM students WHERE user_id=?');$q->execute([$u['user_id']]);$studentId=(int)$q->fetchColumn();if(!$studentId)feedbackOut(['success'=>false,'message'=>'Student profile not found.'],422);
 if($lessonId){$q=$pdo->prepare("SELECT lesson_id FROM lessons WHERE lesson_id=? AND status='published'");$q->execute([$lessonId]);if(!$q->fetchColumn())feedbackOut(['success'=>false,'message'=>'Lesson is not available.'],404);}
 if($quizId){$q=$pdo->prepare("SELECT quiz_id FROM quizzes WHERE quiz_id=? AND status='published'");$q->execute([$quizId]);if(!$q->fetchColumn())feedbackOut(['success'=>false,'message'=>'Quiz is not available.'],404);}
 $q=$pdo->prepare('INSERT INTO feedback(student_id,lesson_id,quiz_id,rating,comment) VALUES(?,?,?,?,?)');$q->execute([$studentId,$lessonId?:null,$quizId?:null,$rating,$comment?:null]);
 $log=$pdo->prepare('INSERT INTO activity_logs(user_id,action,description,ip_address) VALUES(?,?,?,?)');$log->execute([$u['user_id'],'feedback_submitted','Submitted learning feedback ('.$rating.'/5)',substr((string)($_SERVER['REMOTE_ADDR']??''),0,45)]);
 feedbackOut(['success'=>true,'message'=>'Thank you. Your feedback has been saved.'],201);
}catch(Throwable $e){error_log('KIDSS feedback API: '.$e->getMessage());http_response_code(500);echo json_encode(['success'=>false,'message'=>'Unable to process feedback.']);}
