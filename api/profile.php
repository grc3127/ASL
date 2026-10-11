<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth/guard.php'; requireLogin(); require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store');
function profileOut(array $d,int $status=200):never{http_response_code($status);echo json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
function ensureRoleProfile(PDO $pdo,string $role,int $userId):void{
 $map=['student'=>['students','student_id'],'teacher'=>['teachers','teacher_id'],'admin'=>['admins','admin_id'],'administrator'=>['admins','admin_id']];
 if(!isset($map[$role]))throw new RuntimeException('Unsupported account role.');
 [$table,$idColumn]=$map[$role];
 $check=$pdo->prepare("SELECT {$idColumn} FROM {$table} WHERE user_id=?");$check->execute([$userId]);if($check->fetchColumn())return;
 $u=$pdo->prepare('SELECT username,email FROM users WHERE user_id=?');$u->execute([$userId]);$account=$u->fetch();
 if(!$account)throw new RuntimeException('Account not found.');
 $local=trim((string)strstr((string)$account['email'],'@',true));if($local==='')$local=(string)$account['username'];
 $label=trim(preg_replace('/[._-]+/u',' ',$local)??$local);$parts=preg_split('/\\s+/u',$label,-1,PREG_SPLIT_NO_EMPTY)?:[];
 $first=ucfirst((string)array_shift($parts));if($first==='')$first='New';
 $last=$parts?ucwords(implode(' ',$parts)):'User';
 if($role==='student')$insert=$pdo->prepare('INSERT INTO students(user_id,student_number,first_name,middle_name,last_name) VALUES(?,NULL,?,NULL,?)');
 elseif($role==='teacher')$insert=$pdo->prepare('INSERT INTO teachers(user_id,employee_number,first_name,middle_name,last_name) VALUES(?,NULL,?,NULL,?)');
 else $insert=$pdo->prepare('INSERT INTO admins(user_id,first_name,middle_name,last_name) VALUES(?,?,NULL,?)');
 $insert->execute([$userId,$first,$last]);
}
try{
 $u=currentUser();$userId=$u['user_id'];$role=$u['role'];
 $table=$role==='student'?'students':($role==='teacher'?'teachers':($role==='admin'||$role==='administrator'?'admins':''));
 if(!$table)profileOut(['success'=>false,'message'=>'Unsupported role.'],403);
 ensureRoleProfile($pdo,$role,(int)$userId);
 $idColumn=$role==='student'?'student_id':($role==='teacher'?'teacher_id':'admin_id');
 if($_SERVER['REQUEST_METHOD']==='GET'){
  $extra=$role==='student'?',p.birth_date,p.gender,p.profile_image':',NULL AS birth_date,NULL AS gender,NULL AS profile_image';
  $q=$pdo->prepare("SELECT u.username,u.email,u.created_at,u.last_login, p.{$idColumn} profile_id,p.first_name,p.middle_name,p.last_name,".($role==='student'?'p.student_number':($role==='teacher'?'p.employee_number':'NULL'))." AS identifier {$extra} FROM users u JOIN {$table} p ON p.user_id=u.user_id WHERE u.user_id=?");
  $q->execute([$userId]);$p=$q->fetch();if(!$p)profileOut(['success'=>false,'message'=>'Profile record not found.'],404);profileOut(['success'=>true,'profile'=>$p]);
 }
 if($_SERVER['REQUEST_METHOD']!=='POST')profileOut(['success'=>false,'message'=>'Method not allowed.'],405);
 $d=json_decode(file_get_contents('php://input')?:'',true);if(!is_array($d))$d=$_POST;
 $first=trim((string)($d['first_name']??''));$middle=trim((string)($d['middle_name']??''));$last=trim((string)($d['last_name']??''));$email=trim((string)($d['email']??''));$username=trim((string)($d['username']??$u['username']));$newPassword=(string)($d['new_password']??'');
 if($first===''||$last===''||$username===''||!filter_var($email,FILTER_VALIDATE_EMAIL))profileOut(['success'=>false,'message'=>'First name, last name, username, and a valid email are required.'],422);
 if($newPassword!==''&&strlen($newPassword)<8)profileOut(['success'=>false,'message'=>'A new password must contain at least 8 characters.'],422);
 $pdo->beginTransaction();
 try{
  $q=$pdo->prepare("UPDATE {$table} SET first_name=?,middle_name=?,last_name=? WHERE user_id=?");$q->execute([$first,$middle?:null,$last,$userId]);
  $q=$pdo->prepare('UPDATE users SET username=?,email=? WHERE user_id=?');$q->execute([$username,$email,$userId]);
  if($newPassword!==''){$q=$pdo->prepare('UPDATE users SET password_hash=? WHERE user_id=?');$q->execute([password_hash($newPassword,PASSWORD_DEFAULT),$userId]);}
  if($role==='student'){
   $num=trim((string)($d['identifier']??''));$birthDate=trim((string)($d['birth_date']??''));$gender=trim((string)($d['gender']??''));
   if($birthDate!==''&&!preg_match('/^\d{4}-\d{2}-\d{2}$/',$birthDate))throw new InvalidArgumentException('Enter a valid date of birth.');
   if($gender!==''&&mb_strlen($gender)>20)throw new InvalidArgumentException('Gender field is too long.');
   $q=$pdo->prepare('UPDATE students SET student_number=?,birth_date=?,gender=? WHERE user_id=?');$q->execute([$num?:null,$birthDate?:null,$gender?:null,$userId]);
  }
  if($role==='teacher'){$num=trim((string)($d['identifier']??''));$q=$pdo->prepare('UPDATE teachers SET employee_number=? WHERE user_id=?');$q->execute([$num?:null,$userId]);}
  $pdo->commit();
 }catch(Throwable $e){$pdo->rollBack();throw $e;}
 $_SESSION['name']=trim($first.' '.($middle?:'').' '.$last);$_SESSION['email']=$email;$_SESSION['username']=$username;
 if($role==='student')$_SESSION['student_number']=trim((string)($d['identifier']??''));
 if($role==='teacher')$_SESSION['teacher_employee_number']=trim((string)($d['identifier']??''));
 $log=$pdo->prepare('INSERT INTO activity_logs(user_id,action,description,ip_address) VALUES(?,?,?,?)');$log->execute([$userId,'profile_updated','Updated account profile',substr((string)($_SERVER['REMOTE_ADDR']??''),0,45)]);
 profileOut(['success'=>true,'message'=>'Profile updated successfully.']);
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();error_log('KIDSS profile API: '.$e->getMessage());$code=$e instanceof InvalidArgumentException?422:(($e instanceof PDOException&&(string)$e->getCode()==='23000')?409:500);$message=$code===422?$e->getMessage():($code===409?'That username, email, or identifier is already in use.':'Unable to save profile. Check database/server logs.');profileOut(['success'=>false,'message'=>$message],$code);}
