<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$user = currentUser();
$role = $user['role'];
$isAdmin = in_array($role, ['admin','administrator'], true);
$isTeacher = $role === 'teacher';
if (!$isAdmin && !$isTeacher) {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Administrator or teacher access required.']);
    exit;
}
function out(array $data, int $code=200): never {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    exit;
}
function inputData(): array {
    $data=json_decode(file_get_contents('php://input') ?: '', true);
    return is_array($data) ? $data : $_POST;
}
function logAction(PDO $pdo, int $userId, string $action, string $description): void {
    $s=$pdo->prepare('INSERT INTO activity_logs(user_id,action,description,ip_address) VALUES(?,?,?,?)');
    $s->execute([$userId,$action,$description,substr((string)($_SERVER['REMOTE_ADDR']??''),0,45)]);
}
try {
    $teacherId = 0;
    if ($isTeacher) {
        $s=$pdo->prepare('SELECT teacher_id FROM teachers WHERE user_id=?');
        $s->execute([$user['user_id']]);
        $teacherId=(int)$s->fetchColumn();
        if (!$teacherId) {
            $account=$pdo->prepare('SELECT username,email FROM users WHERE user_id=?');$account->execute([$user['user_id']]);$account=$account->fetch();
            if(!$account)out(['success'=>false,'message'=>'Teacher account was not found.'],404);
            $local=trim((string)strstr((string)$account['email'],'@',true));if($local==='')$local=(string)$account['username'];
            $label=trim(preg_replace('/[._-]+/u',' ',$local)??$local);$parts=preg_split('/\\s+/u',$label,-1,PREG_SPLIT_NO_EMPTY)?:[];
            $first=ucfirst((string)array_shift($parts));if($first==='')$first='New';$last=$parts?ucwords(implode(' ',$parts)):'User';
            $create=$pdo->prepare('INSERT INTO teachers(user_id,employee_number,first_name,middle_name,last_name) VALUES(?,NULL,?,NULL,?)');$create->execute([$user['user_id'],$first,$last]);$teacherId=(int)$pdo->lastInsertId();
        }
    }
    if ($_SERVER['REQUEST_METHOD']==='GET') {
        $view=(string)($_GET['view']??'');
        if ($view==='accounts') {
            if (!$isAdmin) out(['success'=>false,'message'=>'Only administrators can manage accounts.'],403);
            $rows=$pdo->query("SELECT u.user_id,u.username,u.email,u.role_id,r.role_name,u.status_id,s.status_name,u.created_at,u.last_login,
                COALESCE(st.student_id,t.teacher_id,a.admin_id) profile_id,
                COALESCE(NULLIF(TRIM(CONCAT_WS(' ',st.first_name,st.middle_name,st.last_name)),''),NULLIF(TRIM(CONCAT_WS(' ',t.first_name,t.middle_name,t.last_name)),''),NULLIF(TRIM(CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name)),''),u.username) full_name,
                st.student_number,t.employee_number,
                CASE
                  WHEN st.student_id IS NOT NULL THEN (SELECT GROUP_CONCAT(DISTINCT CONCAT(c.class_name,IFNULL(CONCAT(' - ',NULLIF(c.section,'')),'')) ORDER BY c.school_year DESC SEPARATOR ', ')
                    FROM class_students cs JOIN classes c ON c.class_id=cs.class_id AND c.status='active'
                    WHERE cs.student_id=st.student_id AND cs.status='active')
                  WHEN t.teacher_id IS NOT NULL THEN (SELECT GROUP_CONCAT(DISTINCT CONCAT(c.class_name,IFNULL(CONCAT(' - ',NULLIF(c.section,'')),'')) ORDER BY c.school_year DESC SEPARATOR ', ')
                    FROM classes c WHERE c.teacher_id=t.teacher_id AND c.status='active')
                  ELSE NULL END class_names
                FROM users u JOIN roles r ON r.role_id=u.role_id JOIN account_status s ON s.status_id=u.status_id
                LEFT JOIN students st ON st.user_id=u.user_id LEFT JOIN teachers t ON t.user_id=u.user_id LEFT JOIN admins a ON a.user_id=u.user_id
                WHERE LOWER(r.role_name) IN ('student','teacher') ORDER BY r.role_name,full_name")->fetchAll();
            out(['success'=>true,'accounts'=>$rows]);
        }
        if ($view==='classes') {
            $sql="SELECT c.class_id,c.class_name,c.grade_level,c.section,c.school_year,c.status,c.teacher_id,
                CONCAT_WS(' ',t.first_name,t.middle_name,t.last_name) teacher_name,
                COUNT(DISTINCT CASE WHEN cs.status='active' THEN cs.student_id END) student_count
                FROM classes c JOIN teachers t ON t.teacher_id=c.teacher_id
                LEFT JOIN class_students cs ON cs.class_id=c.class_id";
            $params=[];
            if ($isTeacher) {$sql.=" WHERE c.teacher_id=?";$params[]=$teacherId;}
            $sql.=" GROUP BY c.class_id,c.class_name,c.grade_level,c.section,c.school_year,c.status,c.teacher_id,t.first_name,t.middle_name,t.last_name ORDER BY c.school_year DESC,c.class_name,c.section";
            $s=$pdo->prepare($sql);$s->execute($params);
            $classes=$s->fetchAll();
            $teachers=$isAdmin?$pdo->query("SELECT teacher_id,CONCAT_WS(' ',first_name,middle_name,last_name) full_name FROM teachers ORDER BY last_name,first_name")->fetchAll():[];
            $students=$pdo->query("SELECT s.student_id,s.student_number,CONCAT_WS(' ',s.first_name,s.middle_name,s.last_name) full_name
                FROM students s JOIN users u ON u.user_id=s.user_id JOIN account_status a ON a.status_id=u.status_id
                WHERE LOWER(a.status_name)='active' ORDER BY s.last_name,s.first_name")->fetchAll();
            $schoolYearRow=$pdo->query("SELECT setting_value FROM system_settings WHERE setting_key='school_year' LIMIT 1")->fetchColumn();
            out(['success'=>true,'classes'=>$classes,'teachers'=>$teachers,'students'=>$students,'school_year'=>$schoolYearRow?:'2026-2027']);
        }
        if ($view==='progress') {
            $sql="SELECT s.student_id,s.student_number,CONCAT_WS(' ',s.first_name,s.middle_name,s.last_name) student_name,
                GROUP_CONCAT(DISTINCT CONCAT(c.class_name,IF(c.section IS NULL OR c.section='', '',CONCAT(' - ',c.section))) SEPARATOR ', ') class_names,
                (SELECT COUNT(*) FROM lessons l WHERE l.status='published') total_lessons,
                (SELECT COUNT(*) FROM student_progress p JOIN lessons l ON l.lesson_id=p.lesson_id WHERE p.student_id=s.student_id AND p.status='completed' AND l.status='published') completed_lessons,
                (SELECT ROUND(AVG(qa.percentage),1) FROM quiz_attempts qa WHERE qa.student_id=s.student_id AND qa.completed_at IS NOT NULL) quiz_average,
                (SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.student_id=s.student_id AND qa.completed_at IS NOT NULL) quiz_attempts,
                (SELECT COUNT(*) FROM student_achievements sa WHERE sa.student_id=s.student_id) achievement_count,
                (SELECT MAX(COALESCE(p.last_accessed,p.completed_at)) FROM student_progress p WHERE p.student_id=s.student_id) last_active
                FROM students s LEFT JOIN class_students cs ON cs.student_id=s.student_id AND cs.status='active'
                LEFT JOIN classes c ON c.class_id=cs.class_id AND c.status='active'";
            $params=[];
            if ($isTeacher) {$sql.=" WHERE EXISTS (SELECT 1 FROM class_students x JOIN classes y ON y.class_id=x.class_id WHERE x.student_id=s.student_id AND x.status='active' AND y.status='active' AND y.teacher_id=?)";$params[]=$teacherId;}
            $sql.=" GROUP BY s.student_id,s.student_number,s.first_name,s.middle_name,s.last_name ORDER BY s.last_name,s.first_name";
            $s=$pdo->prepare($sql);$s->execute($params);out(['success'=>true,'students'=>$s->fetchAll()]);
        }
        if ($view==='quizzes') {
            if(!$isAdmin)out(['success'=>false,'message'=>'Only administrators can manage quizzes.'],403);
            $rows=$pdo->query("SELECT q.quiz_id,q.title,q.description,q.passing_score,q.status,q.created_at,l.title lesson_title,
                COALESCE(c.category_name,CASE WHEN q.lesson_id IS NULL AND q.title LIKE 'Practice: %' THEN SUBSTRING(q.title,11) ELSE 'General' END) category_name,
                (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id=q.quiz_id AND qq.status='active') question_count,
                (SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.quiz_id=q.quiz_id AND qa.completed_at IS NOT NULL) attempt_count,
                (SELECT ROUND(AVG(qa.percentage),1) FROM quiz_attempts qa WHERE qa.quiz_id=q.quiz_id AND qa.completed_at IS NOT NULL) average_score
                FROM quizzes q LEFT JOIN lessons l ON l.lesson_id=q.lesson_id LEFT JOIN lesson_categories c ON c.category_id=l.category_id
                ORDER BY q.created_at DESC,q.quiz_id DESC")->fetchAll();
            out(['success'=>true,'quizzes'=>$rows]);
        }
        if ($view==='settings') {
            if(!$isAdmin)out(['success'=>false,'message'=>'Only administrators can view system settings.'],403);
            $rows=$pdo->query('SELECT setting_key,setting_value,updated_at FROM system_settings ORDER BY setting_key')->fetchAll();
            $settings=[];foreach($rows as $row)$settings[$row['setting_key']]=$row['setting_value'];
            out(['success'=>true,'settings'=>$settings,'updated_at'=>$rows?max(array_column($rows,'updated_at')):null]);
        }
        if ($view==='student_detail') {
            $studentId=(int)($_GET['student_id']??0);
            if(!$studentId)out(['success'=>false,'message'=>'Student ID is required.'],422);
            $scope="SELECT s.student_id FROM students s WHERE s.student_id=?";
            $scopeParams=[$studentId];
            if($isTeacher){$scope.=" AND EXISTS (SELECT 1 FROM class_students cs JOIN classes cl ON cl.class_id=cs.class_id WHERE cs.student_id=s.student_id AND cs.status='active' AND cl.status='active' AND cl.teacher_id=?)";$scopeParams[]=$teacherId;}
            $check=$pdo->prepare($scope);$check->execute($scopeParams);if(!$check->fetchColumn())out(['success'=>false,'message'=>'Student is not in one of your classes or does not exist.'],404);
            $p=$pdo->prepare("SELECT l.title,c.category_name,p.status,p.progress_percent,p.last_accessed,p.completed_at FROM student_progress p JOIN lessons l ON l.lesson_id=p.lesson_id JOIN lesson_categories c ON c.category_id=l.category_id WHERE p.student_id=? ORDER BY COALESCE(p.last_accessed,p.completed_at) DESC,l.title");$p->execute([$studentId]);$lessonLogs=$p->fetchAll();
            $q=$pdo->prepare("SELECT q.title,qa.score,qa.total_items,qa.percentage,qa.passed,qa.started_at,qa.completed_at FROM quiz_attempts qa JOIN quizzes q ON q.quiz_id=qa.quiz_id WHERE qa.student_id=? ORDER BY qa.started_at DESC");$q->execute([$studentId]);$quizLogs=$q->fetchAll();
            out(['success'=>true,'lesson_logs'=>$lessonLogs,'quiz_logs'=>$quizLogs]);
        }
        if ($view==='achievements') {
            if (!$isAdmin) out(['success'=>false,'message'=>'Only administrators can manage achievement rules.'],403);
            $defs=$pdo->query('SELECT achievement_id,name,description,icon,requirement_type,requirement_value,status FROM achievements ORDER BY achievement_id')->fetchAll();
            $earned=$pdo->query("SELECT sa.student_id,sa.achievement_id,sa.earned_at,CONCAT_WS(' ',s.first_name,s.middle_name,s.last_name) student_name,a.name achievement_name
                FROM student_achievements sa JOIN students s ON s.student_id=sa.student_id JOIN achievements a ON a.achievement_id=sa.achievement_id ORDER BY sa.earned_at DESC")->fetchAll();
            out(['success'=>true,'achievements'=>$defs,'earned'=>$earned]);
        }
        out(['success'=>false,'message'=>'Unknown view.'],400);
    }
    if ($_SERVER['REQUEST_METHOD']!=='POST') out(['success'=>false,'message'=>'Method not allowed.'],405);
    $d=inputData();$action=(string)($d['action']??'');
    if (!$isAdmin && !in_array($action,['class_enroll','class_transfer'],true)) out(['success'=>false,'message'=>'Only administrators can perform this action.'],403);

    if ($action==='account_create') {
        $accountRole=strtolower(trim((string)($d['role']??'')));
        if (!in_array($accountRole,['student','teacher'],true)) out(['success'=>false,'message'=>'Choose student or teacher.'],422);
        $username=trim((string)($d['username']??''));$email=trim((string)($d['email']??''));$password=(string)($d['password']??'');
        $first=trim((string)($d['first_name']??''));$middle=trim((string)($d['middle_name']??''));$last=trim((string)($d['last_name']??''));
        if ($username===''||$email===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<8||$first===''||$last==='') out(['success'=>false,'message'=>'Enter first and last name, a valid email, username, and password of at least 8 characters.'],422);
        $r=$pdo->prepare('SELECT role_id FROM roles WHERE LOWER(role_name)=?');$r->execute([$accountRole]);$roleId=(int)$r->fetchColumn();
        $st=$pdo->prepare("SELECT status_id FROM account_status WHERE LOWER(status_name)='active'");$st->execute();$statusId=(int)$st->fetchColumn();
        if(!$roleId||!$statusId) out(['success'=>false,'message'=>'Required role or Active account status is missing from the database.'],500);
        $pdo->beginTransaction();
        try {
            $q=$pdo->prepare('INSERT INTO users(username,email,password_hash,role_id,status_id) VALUES(?,?,?,?,?)');
            $q->execute([$username,$email,password_hash($password,PASSWORD_DEFAULT),$roleId,$statusId]);$newUser=(int)$pdo->lastInsertId();
            if($accountRole==='student') {
                $number=trim((string)($d['student_number']??''))?:null;
                $q=$pdo->prepare('INSERT INTO students(user_id,student_number,first_name,middle_name,last_name) VALUES(?,?,?,?,?)');$q->execute([$newUser,$number,$first,$middle?:null,$last]);
            } else {
                $number=trim((string)($d['employee_number']??''))?:null;
                $q=$pdo->prepare('INSERT INTO teachers(user_id,employee_number,first_name,middle_name,last_name) VALUES(?,?,?,?,?)');$q->execute([$newUser,$number,$first,$middle?:null,$last]);
            }
            logAction($pdo,$user['user_id'],'account_created',"Created {$accountRole} account for {$first} {$last}");
            $pdo->commit();
        } catch(Throwable $e) {$pdo->rollBack();throw $e;}
        out(['success'=>true,'message'=>ucfirst($accountRole).' account created.','user_id'=>$newUser],201);
    }
    if ($action==='account_update') {
        $userId=(int)($d['user_id']??0);$username=trim((string)($d['username']??''));$email=trim((string)($d['email']??''));
        $first=trim((string)($d['first_name']??''));$middle=trim((string)($d['middle_name']??''));$last=trim((string)($d['last_name']??''));
        if(!$userId||$username===''||$first===''||$last===''||!filter_var($email,FILTER_VALIDATE_EMAIL))out(['success'=>false,'message'=>'Username, first name, last name, and a valid email are required.'],422);
        $q=$pdo->prepare("SELECT u.user_id,LOWER(r.role_name) role_name FROM users u JOIN roles r ON r.role_id=u.role_id WHERE u.user_id=? AND LOWER(r.role_name) IN ('student','teacher')");
        $q->execute([$userId]);$target=$q->fetch();if(!$target)out(['success'=>false,'message'=>'Student/teacher account not found.'],404);
        $pdo->beginTransaction();
        try {
            $q=$pdo->prepare('UPDATE users SET username=?,email=? WHERE user_id=?');$q->execute([$username,$email,$userId]);
            if($target['role_name']==='student'){$identifier=trim((string)($d['identifier']??''));$q=$pdo->prepare('INSERT INTO students(user_id,first_name,middle_name,last_name,student_number) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE first_name=VALUES(first_name),middle_name=VALUES(middle_name),last_name=VALUES(last_name),student_number=VALUES(student_number)');$q->execute([$userId,$first,$middle?:null,$last,$identifier?:null]);}
            else {$identifier=trim((string)($d['identifier']??''));$q=$pdo->prepare('INSERT INTO teachers(user_id,first_name,middle_name,last_name,employee_number) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE first_name=VALUES(first_name),middle_name=VALUES(middle_name),last_name=VALUES(last_name),employee_number=VALUES(employee_number)');$q->execute([$userId,$first,$middle?:null,$last,$identifier?:null]);}
            logAction($pdo,$user['user_id'],'account_updated',"Updated {$target['role_name']} account {$userId}");
            $pdo->commit();
        } catch(Throwable $e) {$pdo->rollBack();throw $e;}
        out(['success'=>true,'message'=>'Account details updated.']);
    }
    if ($action==='account_status') {
        $userId=(int)($d['user_id']??0);$statusName=strtolower((string)($d['status']??''));
        if(!$userId||!in_array($statusName,['active','inactive','suspended','pending'],true)) out(['success'=>false,'message'=>'Invalid account or status.'],422);
        $q=$pdo->prepare('SELECT status_id FROM account_status WHERE LOWER(status_name)=?');$q->execute([$statusName]);$statusId=(int)$q->fetchColumn();
        if(!$statusId) out(['success'=>false,'message'=>'That status is not configured in account_status.'],422);
        $q=$pdo->prepare("SELECT u.user_id FROM users u JOIN roles r ON r.role_id=u.role_id WHERE u.user_id=? AND LOWER(r.role_name) IN ('student','teacher')");$q->execute([$userId]);if(!$q->fetchColumn())out(['success'=>false,'message'=>'Student/teacher account not found.'],404);
        $q=$pdo->prepare('UPDATE users SET status_id=? WHERE user_id=?');$q->execute([$statusId,$userId]);
        logAction($pdo,$user['user_id'],'account_status_changed',"Changed user {$userId} status to {$statusName}");
        out(['success'=>true,'message'=>'Account status updated.']);
    }
    if ($action==='class_save') {
        $id=(int)($d['class_id']??0);$name=trim((string)($d['class_name']??''));$grade=trim((string)($d['grade_level']??''));$section=trim((string)($d['section']??''));$year=trim((string)($d['school_year']??''));$tid=(int)($d['teacher_id']??0);
        if($name===''||$year===''||!preg_match('/^\d{4}\s*[-–]\s*\d{4}$/u',$year)||!$tid) out(['success'=>false,'message'=>'Class name, school year (e.g. 2026-2027), and teacher are required.'],422);
        $q=$pdo->prepare('SELECT teacher_id FROM teachers WHERE teacher_id=?');$q->execute([$tid]);if(!$q->fetchColumn())out(['success'=>false,'message'=>'Selected teacher does not exist.'],422);
        if($id) {
            $exists=$pdo->prepare('SELECT class_id FROM classes WHERE class_id=?');$exists->execute([$id]);if(!$exists->fetchColumn())out(['success'=>false,'message'=>'Class not found.'],404);
            $q=$pdo->prepare('UPDATE classes SET teacher_id=?,class_name=?,grade_level=?,section=?,school_year=? WHERE class_id=?');$q->execute([$tid,$name,$grade?:null,$section?:null,$year,$id]);
        } else {
            $q=$pdo->prepare("INSERT INTO classes(teacher_id,class_name,grade_level,section,school_year,status) VALUES(?,?,?,?,?,'active')");$q->execute([$tid,$name,$grade?:null,$section?:null,$year]);
            $id=(int)$pdo->lastInsertId();
        }
        logAction($pdo,$user['user_id'],'class_saved',"Saved class {$name} - {$section} ({$year})");
        out(['success'=>true,'message'=>'Class saved.','class_id'=>$id]);
    }
    if ($action==='class_enroll'||$action==='class_transfer') {
        $classId=(int)($d['class_id']??0);$studentId=(int)($d['student_id']??0);
        if(!$classId||!$studentId)out(['success'=>false,'message'=>'Select a class and student.'],422);
        $q=$pdo->prepare('SELECT class_id,teacher_id FROM classes WHERE class_id=? AND status=\'active\'');$q->execute([$classId]);$class=$q->fetch();
        if(!$class)out(['success'=>false,'message'=>'Class not found.'],404);
        if($isTeacher&&(int)$class['teacher_id']!==$teacherId)out(['success'=>false,'message'=>'You can only manage your own classes.'],403);
        $q=$pdo->prepare('SELECT student_id FROM students WHERE student_id=?');$q->execute([$studentId]);if(!$q->fetchColumn())out(['success'=>false,'message'=>'Student not found.'],404);
        $pdo->beginTransaction();
        try {
            if($action==='class_transfer') {
                $q=$pdo->prepare("UPDATE class_students SET status='inactive' WHERE student_id=? AND status='active' AND class_id<>?");$q->execute([$studentId,$classId]);
            }
            $q=$pdo->prepare("INSERT INTO class_students(class_id,student_id,status) VALUES(?,?,'active') ON DUPLICATE KEY UPDATE status='active'");$q->execute([$classId,$studentId]);
            logAction($pdo,$user['user_id'],$action==="class_transfer"?'student_transferred':'student_enrolled',"Student {$studentId} assigned to class {$classId}");
            $pdo->commit();
        } catch(Throwable $e) {$pdo->rollBack();throw $e;}
        out(['success'=>true,'message'=>$action==='class_transfer'?'Student transferred successfully.':'Student added to class successfully.']);
    }
    if ($action==='settings_save') {
        $systemName=trim((string)($d['system_name']??''));$schoolYear=trim((string)($d['school_year']??''));
        if($systemName===''||mb_strlen($systemName)>100||!preg_match('/^\d{4}\s*[-–]\s*\d{4}$/u',$schoolYear))out(['success'=>false,'message'=>'Enter a system name and school year such as 2026-2027.'],422);
        $q=$pdo->prepare('INSERT INTO system_settings(setting_key,setting_value,updated_by) VALUES(?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),updated_by=VALUES(updated_by),updated_at=NOW()');
        $q->execute(['system_name',$systemName,$user['user_id']]);$q->execute(['school_year',$schoolYear,$user['user_id']]);
        logAction($pdo,$user['user_id'],'settings_updated',"Updated system name and school year");
        out(['success'=>true,'message'=>'System settings saved.']);
    }
    if ($action==='quiz_status') {
        $quizId=(int)($d['quiz_id']??0);$status=(string)($d['status']??'');
        if(!$quizId||!in_array($status,['draft','published','archived'],true))out(['success'=>false,'message'=>'Invalid quiz or status.'],422);
        $q=$pdo->prepare('UPDATE quizzes SET status=? WHERE quiz_id=?');$q->execute([$status,$quizId]);
        if(!$q->rowCount()){$q=$pdo->prepare('SELECT quiz_id FROM quizzes WHERE quiz_id=?');$q->execute([$quizId]);if(!$q->fetchColumn())out(['success'=>false,'message'=>'Quiz not found.'],404);}
        logAction($pdo,$user['user_id'],'quiz_status_changed',"Changed quiz {$quizId} status to {$status}");
        out(['success'=>true,'message'=>'Quiz status updated.']);
    }
    if ($action==='achievement_save') {
        $id=(int)($d['achievement_id']??0);$name=trim((string)($d['name']??''));$desc=trim((string)($d['description']??''));$type=(string)($d['requirement_type']??'lessons_completed');$value=max(1,(int)($d['requirement_value']??1));$status=(string)($d['status']??'active');
        if($name===''||!in_array($type,['lessons_completed','quizzes_completed','perfect_quiz','category_completed'],true)||!in_array($status,['active','inactive'],true))out(['success'=>false,'message'=>'Enter a name and valid achievement rule.'],422);
        if($id){$q=$pdo->prepare('UPDATE achievements SET name=?,description=?,requirement_type=?,requirement_value=?,status=? WHERE achievement_id=?');$q->execute([$name,$desc?:null,$type,$value,$status,$id]);}
        else {$q=$pdo->prepare('INSERT INTO achievements(name,description,requirement_type,requirement_value,status) VALUES(?,?,?,?,?)');$q->execute([$name,$desc?:null,$type,$value,$status]);$id=(int)$pdo->lastInsertId();}
        logAction($pdo,$user['user_id'],'achievement_saved',"Saved achievement rule {$name}");
        out(['success'=>true,'message'=>'Achievement rule saved.','achievement_id'=>$id]);
    }
    out(['success'=>false,'message'=>'Unknown action.'],400);
} catch(Throwable $e) {
    if($pdo->inTransaction())$pdo->rollBack();
    error_log('KIDSS system management API: '.$e->getMessage());
    $code=($e instanceof PDOException && (string)$e->getCode()==='23000')?409:500;
    out(['success'=>false,'message'=>$code===409?'A duplicate username, email, student number, employee number, or class assignment exists.':'The request could not be completed. Check the database schema and server log.'],$code);
}
