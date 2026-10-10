<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth/guard.php';
requireRole('student');
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json; charset=utf-8');
try {
    $stmt=$pdo->prepare("SELECT p.lesson_id,p.status,p.progress_percent,p.last_accessed,p.completed_at
      FROM student_progress p INNER JOIN students s ON s.student_id=p.student_id
      WHERE s.user_id=?");
    $stmt->execute([(int)($_SESSION['user_id'] ?? 0)]);
    echo json_encode(['success'=>true,'progress'=>$stmt->fetchAll()], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('KIDSS student lesson progress error: '.$e->getMessage());
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Unable to load lesson progress.']);
}
