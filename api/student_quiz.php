<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth/guard.php'; requireRole('student'); require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/achievement_engine.php'; require_once __DIR__ . '/../config/seed_basic_greetings.php';
ob_start();
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store');
function quizOut(array $d,int $code=200):never{while(ob_get_level()>0)ob_end_clean();http_response_code($code);header('Content-Type: application/json; charset=utf-8');echo json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
function quizInput():array{$d=json_decode(file_get_contents('php://input')?:'',true);return is_array($d)?$d:$_POST;}
try{
 $q=$pdo->prepare('SELECT student_id FROM students WHERE user_id=?');$q->execute([(int)$_SESSION['user_id']]);$studentId=(int)$q->fetchColumn();if(!$studentId)quizOut(['success'=>false,'message'=>'Student profile not found.'],422);
 if($_SERVER['REQUEST_METHOD']==='GET'){
  ensureBasicGreetings($pdo);
  $cats=$pdo->query("SELECT c.category_id,c.category_name,COUNT(l.lesson_id) lesson_count FROM lesson_categories c JOIN lessons l ON l.category_id=c.category_id AND l.status='published' WHERE c.status='active' GROUP BY c.category_id,c.category_name,c.display_order ORDER BY c.display_order,c.category_name")->fetchAll();
  $categoryId=(int)($_GET['category_id']??($cats[0]['category_id']??0));
  if(!$categoryId)quizOut(['success'=>true,'categories'=>$cats,'items'=>[],'state'=>null,'category_id'=>0]);
  $cat=$pdo->prepare("SELECT category_id,category_name FROM lesson_categories WHERE category_id=? AND status='active'");$cat->execute([$categoryId]);$category=$cat->fetch();if(!$category)quizOut(['success'=>false,'message'=>'Category is not available.'],404);
  $s=$pdo->prepare("SELECT l.lesson_id,l.title,l.description,COALESCE(NULLIF(l.thumbnail,''),NULLIF(img.file_path,'')) image_path,
      aud.file_path audio_path,vid.file_path video_path
    FROM lessons l
    JOIN lesson_categories c ON c.category_id=l.category_id AND c.status='active'
    LEFT JOIN lesson_media img ON img.lesson_id=l.lesson_id AND img.media_type='image' AND img.media_id=(SELECT MIN(m2.media_id) FROM lesson_media m2 WHERE m2.lesson_id=l.lesson_id AND m2.media_type='image')
    LEFT JOIN lesson_media aud ON aud.lesson_id=l.lesson_id AND aud.media_type='audio' AND aud.media_id=(SELECT MIN(m3.media_id) FROM lesson_media m3 WHERE m3.lesson_id=l.lesson_id AND m3.media_type='audio')
    LEFT JOIN lesson_media vid ON vid.lesson_id=l.lesson_id AND vid.media_type='video' AND vid.media_id=(SELECT MIN(m4.media_id) FROM lesson_media m4 WHERE m4.lesson_id=l.lesson_id AND m4.media_type='video')
    WHERE l.category_id=? AND l.status='published' ORDER BY l.display_order,l.title");
  $s->execute([$categoryId]);$items=$s->fetchAll();
  foreach($items as &$item){
   $title=(string)$item['title'];$letter=(preg_match('/^[A-Z]$/i',$title)===1)?strtoupper($title):'';
   if(!$item['image_path']&&$letter&&is_file(__DIR__.'/../assets/lesson-media/Image/'.$letter.'.png'))$item['image_path']='assets/lesson-media/Image/'.$letter.'.png';
   if(!$item['image_path']&&$letter&&is_file(__DIR__.'/../assets/quiz/images/'.$letter.'.png'))$item['image_path']='assets/quiz/images/'.$letter.'.png';
   if(!$item['audio_path']&&$letter&&is_file(__DIR__.'/../assets/quiz/audio/'.$letter.'.mp3'))$item['audio_path']='assets/quiz/audio/'.$letter.'.mp3';
   if(!$item['video_path']&&$letter&&is_file(__DIR__.'/../assets/quiz/videos/'.$letter.'.mp4'))$item['video_path']='assets/quiz/videos/'.$letter.'.mp4';
   $item['image']=$item['image_path'];$item['audio']=$item['audio_path'];$item['video']=$item['video_path'];
   unset($item['image_path'],$item['audio_path'],$item['video_path']);
  }unset($item);
  $items=array_values(array_filter($items,fn($i)=>!empty($i['image'])));
  $poolStmt=$pdo->query("SELECT DISTINCT l.title FROM lessons l JOIN lesson_categories c ON c.category_id=l.category_id AND c.status='active' WHERE l.status='published' ORDER BY l.title");
  $choicePool=array_values(array_map(static fn($row)=>(string)$row['title'],$poolStmt->fetchAll()));
  $title='Practice: '.$category['category_name'];
  $find=$pdo->prepare("SELECT quiz_id,status FROM quizzes WHERE lesson_id IS NULL AND title=? LIMIT 1");$find->execute([$title]);$quizRow=$find->fetch();$quizId=(int)($quizRow['quiz_id']??0);
  if($quizId && $quizRow['status']!=='published')quizOut(['success'=>false,'message'=>'This category quiz is not currently published by the administrator.'],403);
  if(!$quizId){$ins=$pdo->prepare("INSERT INTO quizzes(lesson_id,title,description,passing_score,status) VALUES(NULL,?,?,75,'published')");$ins->execute([$title,'Automatically generated practice quiz for the published lessons in '.$category['category_name'].'.']);$quizId=(int)$pdo->lastInsertId();}
  $st=$pdo->prepare('SELECT question_index,score,question_order_json,answers_json,completed,updated_at FROM student_quiz_state WHERE student_id=? AND category_id=?');$st->execute([$studentId,$categoryId]);$state=$st->fetch()?:null;
  if($state){$state['question_order']=json_decode($state['question_order_json'],true)?:[];$state['answers']=json_decode($state['answers_json'],true)?:[];$state['question_index']=(int)$state['question_index'];$state['score']=(int)$state['score'];$state['completed']=(bool)$state['completed'];unset($state['question_order_json'],$state['answers_json']);}
  quizOut(['success'=>true,'categories'=>$cats,'category'=>$category,'category_id'=>$categoryId,'quiz_id'=>$quizId,'items'=>$items,'choice_pool'=>$choicePool,'state'=>$state]);
 }
 if($_SERVER['REQUEST_METHOD']!=='POST')quizOut(['success'=>false,'message'=>'Method not allowed.'],405);
 $d=quizInput();$action=(string)($d['action']??'');$categoryId=(int)($d['category_id']??0);
 if(!$categoryId)quizOut(['success'=>false,'message'=>'Choose a lesson category.'],422);
 $s=$pdo->prepare("SELECT category_id,category_name FROM lesson_categories WHERE category_id=? AND status='active'");$s->execute([$categoryId]);$category=$s->fetch();if(!$category)quizOut(['success'=>false,'message'=>'Category not found.'],404);
 $title='Practice: '.$category['category_name'];$s=$pdo->prepare('SELECT quiz_id,status FROM quizzes WHERE lesson_id IS NULL AND title=? LIMIT 1');$s->execute([$title]);$quizRow=$s->fetch();$quizId=(int)($quizRow['quiz_id']??0);if($quizId && $quizRow['status']!=='published')quizOut(['success'=>false,'message'=>'This category quiz is not currently published by the administrator.'],403);if(!$quizId){$s=$pdo->prepare("INSERT INTO quizzes(lesson_id,title,description,passing_score,status) VALUES(NULL,?,?,75,'published')");$s->execute([$title,'Practice quiz from published lessons.']);$quizId=(int)$pdo->lastInsertId();}
 if($action==='save_state'){
  $index=max(0,(int)($d['question_index']??0));$order=$d['question_order']??[];$answers=$d['answers']??[];
  if(!is_array($order)||!is_array($answers))quizOut(['success'=>false,'message'=>'Invalid quiz state.'],422);
  $ids=array_values(array_unique(array_filter(array_map('intval',$order),fn($n)=>$n>0)));
  if(count($ids)!==count($order)||!$ids||$index>=count($ids))quizOut(['success'=>false,'message'=>'Invalid question order.'],422);
  $lessonQuery=$pdo->prepare("SELECT lesson_id,title FROM lessons WHERE category_id=? AND status='published'");$lessonQuery->execute([$categoryId]);$allowed=[];foreach($lessonQuery->fetchAll() as $lesson)$allowed[(int)$lesson['lesson_id']]=(string)$lesson['title'];
  foreach($ids as $lessonId)if(!array_key_exists($lessonId,$allowed))quizOut(['success'=>false,'message'=>'The quiz includes a lesson that is no longer published. Reload the quiz.'],409);
  $verifiedScore=0;foreach($answers as $answerId=>$answer){$lessonId=(int)$answerId;if(!in_array($lessonId,$ids,true))quizOut(['success'=>false,'message'=>'Invalid saved answer.'],422);$selected=is_array($answer)?trim((string)($answer['selected']??'')):trim((string)$answer);if($selected!==''&&hash_equals($allowed[$lessonId],$selected))$verifiedScore++;}
  $score=$verifiedScore;
  $a=json_encode($answers,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);$o=json_encode($ids);
  $q=$pdo->prepare("INSERT INTO student_quiz_state(student_id,category_id,quiz_id,question_index,score,question_order_json,answers_json,completed) VALUES(?,?,?,?,?,?,?,0)
    ON DUPLICATE KEY UPDATE quiz_id=VALUES(quiz_id),question_index=IF(completed=1,question_index,VALUES(question_index)),score=IF(completed=1,score,VALUES(score)),question_order_json=IF(completed=1,question_order_json,VALUES(question_order_json)),answers_json=IF(completed=1,answers_json,VALUES(answers_json)),updated_at=NOW()");
  $q->execute([$studentId,$categoryId,$quizId,$index,$score,$o,$a]);quizOut(['success'=>true,'message'=>'Quiz progress saved.']);
 }
 if($action==='complete'){
  $order=$d['question_order']??[];$answers=$d['answers']??[];
  if(!is_array($order)||!is_array($answers)||count($order)<1)quizOut(['success'=>false,'message'=>'Invalid quiz result.'],422);
  $order=array_values(array_map('intval',$order));
  if(count(array_unique($order))!==count($order)||in_array(0,$order,true))quizOut(['success'=>false,'message'=>'Invalid question order.'],422);
  $lessonQuery=$pdo->prepare("SELECT lesson_id,title FROM lessons WHERE category_id=? AND status='published'");
  $lessonQuery->execute([$categoryId]);$allowedLessons=[];foreach($lessonQuery->fetchAll() as $lesson)$allowedLessons[(int)$lesson['lesson_id']]=(string)$lesson['title'];
  $verifiedScore=0;
  foreach($order as $lessonId){
   if(!array_key_exists($lessonId,$allowedLessons))quizOut(['success'=>false,'message'=>'The quiz includes a lesson that is no longer published. Reload the quiz.'],409);
   $answer=$answers[(string)$lessonId]??$answers[$lessonId]??null;
   $selected=is_array($answer)?trim((string)($answer['selected']??'')):trim((string)$answer);
   if($selected==='')quizOut(['success'=>false,'message'=>'Every quiz question must be answered before completion.'],422);
   if(hash_equals($allowedLessons[$lessonId],$selected))$verifiedScore++;
  }
  $score=$verifiedScore;$total=count($order);
  $pdo->beginTransaction();
  try{
   $q=$pdo->prepare('SELECT completed FROM student_quiz_state WHERE student_id=? AND category_id=? FOR UPDATE');$q->execute([$studentId,$categoryId]);$old=$q->fetch();
   if(!$old||!(int)$old['completed']){
    $pct=round($score/$total*100,2);$q=$pdo->prepare('INSERT INTO quiz_attempts(quiz_id,student_id,score,total_items,percentage,passed,started_at,completed_at) VALUES(?,?,?,?,?,?,NOW(),NOW())');$q->execute([$quizId,$studentId,$score,$total,$pct,$pct>=75?1:0]);
    $q=$pdo->prepare("INSERT INTO student_quiz_state(student_id,category_id,quiz_id,question_index,score,question_order_json,answers_json,completed) VALUES(?,?,?,?,?,?,?,1)
      ON DUPLICATE KEY UPDATE quiz_id=VALUES(quiz_id),question_index=VALUES(question_index),score=VALUES(score),question_order_json=VALUES(question_order_json),answers_json=VALUES(answers_json),completed=1,updated_at=NOW()");
    $q->execute([$studentId,$categoryId,$quizId,$total,$score,json_encode(array_map('intval',$order)),json_encode($answers,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)]);
   }
   refreshStudentAchievements($pdo,$studentId);
   $pdo->commit();
  }catch(Throwable $e){$pdo->rollBack();throw $e;}
  quizOut(['success'=>true,'message'=>'Quiz result saved.']);
 }
 if($action==='restart'){
  $order=$d['question_order']??[];if(!is_array($order))quizOut(['success'=>false,'message'=>'Invalid question order.'],422);
  $q=$pdo->prepare("INSERT INTO student_quiz_state(student_id,category_id,quiz_id,question_index,score,question_order_json,answers_json,completed) VALUES(?,?,?,0,0,?,'{}',0)
   ON DUPLICATE KEY UPDATE quiz_id=VALUES(quiz_id),question_index=0,score=0,question_order_json=VALUES(question_order_json),answers_json='{}',completed=0,updated_at=NOW()");
  $q->execute([$studentId,$categoryId,$quizId,json_encode(array_values(array_unique(array_map('intval',$order))))]);quizOut(['success'=>true,'message'=>'A new attempt has started.']);
 }
 quizOut(['success'=>false,'message'=>'Unknown action.'],400);
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();error_log('KIDSS quiz persistence: '.$e->getMessage());http_response_code(500);echo json_encode(['success'=>false,'message'=>'Unable to save/load quiz progress. Import the system completion migration and verify the database.']);}
