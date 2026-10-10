<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');
$role = strtolower((string)($_SESSION['role'] ?? ''));
$isManager = in_array($role, ['admin', 'administrator', 'teacher'], true);
if (!$isManager && $role !== 'student') {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Access denied.']);
    exit;
}
function respond(array $data, int $code = 200): never {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
function bodyData(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '', true);
    return is_array($data) ? $data : $_POST;
}
try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $categories = $pdo->query("SELECT category_id, category_name, description, display_order FROM lesson_categories WHERE status='active' ORDER BY display_order, category_name")->fetchAll();
        $sql = "SELECT l.lesson_id, l.category_id, c.category_name, l.title, l.description, l.thumbnail, l.display_order, l.status, l.created_at, l.updated_at
                FROM lessons l INNER JOIN lesson_categories c ON c.category_id=l.category_id";
        $params = [];
        if (!$isManager) {
            $sql .= " WHERE l.status='published' AND c.status='active'";
        }
        $sql .= " ORDER BY c.display_order, c.category_name, l.display_order, l.title";
        $lessons = $pdo->query($sql)->fetchAll();
        $mediaStmt = $pdo->query("SELECT media_id, lesson_id, media_type, file_path, title, description, display_order FROM lesson_media ORDER BY display_order, media_id");
        $mediaByLesson = [];
        foreach ($mediaStmt->fetchAll() as $media) $mediaByLesson[(int)$media['lesson_id']][] = $media;
        foreach ($lessons as &$lesson) $lesson['media'] = $mediaByLesson[(int)$lesson['lesson_id']] ?? [];
        unset($lesson);
        respond(['success'=>true, 'categories'=>$categories, 'lessons'=>$lessons]);
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(['success'=>false,'message'=>'Method not allowed.'], 405);
    $data = bodyData();
    $action = (string)($data['action'] ?? '');
    if ($action === 'progress') {
        if ($role !== 'student') respond(['success'=>false,'message'=>'Student access required.'],403);
        $studentStmt = $pdo->prepare('SELECT student_id FROM students WHERE user_id=?');
        $studentStmt->execute([(int)($_SESSION['user_id'] ?? 0)]);
        $studentId = (int)$studentStmt->fetchColumn();
        if (!$studentId) respond(['success'=>false,'message'=>'Student profile was not found.'],422);
        $lessonId = filter_var($data['lesson_id'] ?? null, FILTER_VALIDATE_INT);
        $status = (string)($data['status'] ?? 'in_progress');
        if (!$lessonId || !in_array($status, ['in_progress','completed'], true)) respond(['success'=>false,'message'=>'Invalid progress data.'],422);
        $check = $pdo->prepare("SELECT lesson_id FROM lessons WHERE lesson_id=? AND status='published'");
        $check->execute([$lessonId]);
        if (!$check->fetchColumn()) respond(['success'=>false,'message'=>'This lesson is not available.'],404);
        $percent = $status === 'completed' ? 100 : 1;
        $upsert = $pdo->prepare("INSERT INTO student_progress (student_id, lesson_id, status, progress_percent, last_accessed, completed_at)
            VALUES (?, ?, ?, ?, NOW(), " . ($status === 'completed' ? 'NOW()' : 'NULL') . ")
            ON DUPLICATE KEY UPDATE status=IF(status='completed','completed',VALUES(status)), progress_percent=GREATEST(progress_percent, VALUES(progress_percent)), last_accessed=NOW(), completed_at=IF(VALUES(status)='completed', COALESCE(completed_at,NOW()), completed_at)");
        $upsert->execute([$studentId, $lessonId, $status, $percent]);
        respond(['success'=>true,'message'=>'Progress saved.']);
    }
    if (!$isManager) respond(['success'=>false,'message'=>'Only teachers and administrators can manage lessons.'],403);

    if ($action === 'create_category') {
        $name = trim((string)($data['category_name'] ?? ''));
        if ($name === '' || mb_strlen($name) > 100) respond(['success'=>false,'message'=>'Enter a category name (maximum 100 characters).'],422);
        $stmt = $pdo->prepare("INSERT INTO lesson_categories (category_name, description, display_order, status) VALUES (?, ?, ?, 'active')");
        $stmt->execute([$name, trim((string)($data['description'] ?? '')) ?: null, (int)($data['display_order'] ?? 0)]);
        respond(['success'=>true,'message'=>'Category created.','category_id'=>(int)$pdo->lastInsertId()]);
    }
    if ($action === 'create' || $action === 'update') {
        $id = (int)($data['lesson_id'] ?? 0);
        $categoryId = (int)($data['category_id'] ?? 0);
        $title = trim((string)($data['title'] ?? ''));
        $description = trim((string)($data['description'] ?? ''));
        $thumbnail = trim((string)($data['thumbnail'] ?? ''));
        $status = (string)($data['status'] ?? 'draft');
        if ($title === '' || mb_strlen($title) > 150) respond(['success'=>false,'message'=>'Lesson title is required (maximum 150 characters).'],422);
        if (!$categoryId || !in_array($status, ['draft','published','archived'], true)) respond(['success'=>false,'message'=>'Choose a valid category and status.'],422);
        $cat = $pdo->prepare("SELECT category_id FROM lesson_categories WHERE category_id=? AND status='active'");
        $cat->execute([$categoryId]);
        if (!$cat->fetchColumn()) respond(['success'=>false,'message'=>'Selected category is not active.'],422);
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO lessons (category_id,title,description,thumbnail,display_order,status) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$categoryId,$title,$description ?: null,$thumbnail ?: null,(int)($data['display_order'] ?? 0),$status]);
            $id = (int)$pdo->lastInsertId();
        } else {
            if ($id < 1) respond(['success'=>false,'message'=>'Invalid lesson ID.'],422);
            $stmt = $pdo->prepare("UPDATE lessons SET category_id=?,title=?,description=?,thumbnail=?,display_order=?,status=? WHERE lesson_id=?");
            $stmt->execute([$categoryId,$title,$description ?: null,$thumbnail ?: null,(int)($data['display_order'] ?? 0),$status,$id]);
            if (!$stmt->rowCount()) {
                $exists=$pdo->prepare('SELECT lesson_id FROM lessons WHERE lesson_id=?'); $exists->execute([$id]);
                if (!$exists->fetchColumn()) respond(['success'=>false,'message'=>'Lesson not found.'],404);
            }
        }
        // Media paths are stored in lesson_media and are optional. Blank fields remove that media type.
        $mediaTypes = ['image','audio','video'];
        foreach ($mediaTypes as $type) {
            if (!array_key_exists($type, $data)) continue;
            $path = trim((string)$data[$type]);
            $del=$pdo->prepare('DELETE FROM lesson_media WHERE lesson_id=? AND media_type=?');
            $del->execute([$id,$type]);
            if ($path !== '') {
                $ins=$pdo->prepare('INSERT INTO lesson_media (lesson_id,media_type,file_path,title,display_order) VALUES (?,?,?,?,?)');
                $ins->execute([$id,$type,$path,$title, array_search($type,$mediaTypes,true)]);
            }
        }
        respond(['success'=>true,'message'=>$action==='create'?'Lesson created.':'Lesson updated.','lesson_id'=>$id]);
    }
    if ($action === 'status') {
        $id=(int)($data['lesson_id'] ?? 0); $status=(string)($data['status'] ?? '');
        if ($id<1 || !in_array($status,['draft','published','archived'],true)) respond(['success'=>false,'message'=>'Invalid lesson status.'],422);
        $stmt=$pdo->prepare('UPDATE lessons SET status=? WHERE lesson_id=?'); $stmt->execute([$status,$id]);
        respond(['success'=>true,'message'=>'Lesson status updated.']);
    }
    respond(['success'=>false,'message'=>'Unknown action.'],400);
} catch (PDOException $e) {
    error_log('KIDSS lesson API error: '.$e->getMessage());
    $code = $e->getCode() === '23000' ? 409 : 500;
    respond(['success'=>false,'message'=>$code===409?'That category already exists or the data conflicts with an existing record.':'A database error occurred while processing lessons.'], $code);
} catch (Throwable $e) {
    error_log('KIDSS lesson API error: '.$e->getMessage());
    respond(['success'=>false,'message'=>'Unable to process the request. Check the server log for details.'],500);
}
