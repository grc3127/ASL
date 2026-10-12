<?php
declare(strict_types=1);

/**
 * Registers the bundled Basic Greetings media as normal lessons.
 * Safe to call repeatedly: existing lesson records and media paths are reused.
 */
function ensureBasicGreetings(PDO $pdo): void
{
    $relativeDir = 'assets/lesson-media/Basic Greetings';
    $absoluteDir = dirname(__DIR__) . '/' . $relativeDir;
    if (!is_dir($absoluteDir)) return;

    $greetings = [
        'Good Morning' => ['Good Morning', '[IMG] Good Morning.jpg', '[VID] Good Morning.mp4'],
        'Good Night'   => ['Good Night', '[IMG] Good Night.jpg', '[VID] Good Night.mp4'],
        'Hello'        => ['Hello', '[IMG] Hello.jpg', '[VID] Hello.mp4'],
        'Thank You'    => ['Thank You', '[IMG] Thank You.jpg', '[VID] Thank You.mp4'],
        'Welcome'      => ['Welcome', '[IMG] Welcome.jpg', '[VID] You_re Welcome.mp4'],
    ];

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT category_id FROM lesson_categories WHERE category_name=? LIMIT 1');
        $stmt->execute(['Basic Greetings']);
        $categoryId = (int)$stmt->fetchColumn();
        if (!$categoryId) {
            $insert = $pdo->prepare("INSERT INTO lesson_categories(category_name,description,display_order,status) VALUES(?,?,2,'active')");
            $insert->execute(['Basic Greetings', 'Everyday greetings and polite expressions in sign language.']);
            $categoryId = (int)$pdo->lastInsertId();
        } else {
            $activate = $pdo->prepare("UPDATE lesson_categories SET status='active' WHERE category_id=? AND status='inactive'");
            $activate->execute([$categoryId]);
        }

        foreach ($greetings as $position => [$title, $imageName, $videoName]) {
            $imagePath = $relativeDir . '/' . $imageName;
            $videoPath = $relativeDir . '/' . $videoName;
            if (!is_file(dirname(__DIR__) . '/' . $imagePath)) continue;

            $find = $pdo->prepare('SELECT lesson_id,category_id,thumbnail FROM lessons WHERE LOWER(title)=LOWER(?) ORDER BY (category_id=?) DESC, lesson_id ASC LIMIT 1');
            $find->execute([$title, $categoryId]);
            $lesson = $find->fetch(PDO::FETCH_ASSOC);

            if ($lesson) {
                $lessonId = (int)$lesson['lesson_id'];
                $thumbnail = trim((string)($lesson['thumbnail'] ?? '')) !== '' ? $lesson['thumbnail'] : $imagePath;
                $update = $pdo->prepare("UPDATE lessons SET category_id=?,thumbnail=?,description=COALESCE(NULLIF(description,''),?),status=IF(status='archived','published',status) WHERE lesson_id=?");
                $update->execute([$categoryId, $thumbnail, 'Learn the sign for ' . $title . '.', $lessonId]);
            } else {
                $insert = $pdo->prepare("INSERT INTO lessons(category_id,title,description,thumbnail,display_order,status) VALUES(?,?,?,?,?,'published')");
                $insert->execute([$categoryId, $title, 'Learn the sign for ' . $title . '.', $imagePath, $position + 1]);
                $lessonId = (int)$pdo->lastInsertId();
            }

            $media = [
                ['image', $imagePath, 0],
                ['video', $videoPath, 1],
            ];
            foreach ($media as [$type, $path, $order]) {
                if ($type === 'video' && !is_file(dirname(__DIR__) . '/' . $path)) continue;
                $exists = $pdo->prepare('SELECT media_id FROM lesson_media WHERE lesson_id=? AND media_type=? AND file_path=? LIMIT 1');
                $exists->execute([$lessonId, $type, $path]);
                if (!$exists->fetchColumn()) {
                    $insertMedia = $pdo->prepare('INSERT INTO lesson_media(lesson_id,media_type,file_path,title,display_order) VALUES(?,?,?,?,?)');
                    $insertMedia->execute([$lessonId, $type, $path, $title, $order]);
                }
            }
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('KIDSS Basic Greetings seed: ' . $e->getMessage());
        // Do not block normal lessons if this optional bundled content cannot be seeded.
    }
}
