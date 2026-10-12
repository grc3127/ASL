<?php
/**
 * KIDSS – Kindergarten Interactive Digital Signing System
 * Public landing page. Authenticated users continue to their role dashboard.
 */
session_start();
if (!empty($_SESSION['logged_in']) && !empty($_SESSION['role'])) {
    header('Location: auth/role_redirect.php');
    exit;
}

$siteName = 'KIDSS';
$siteTag = ['Kindergarten Interactive', 'Digital Signing System'];
$activePage = basename($_SERVER['PHP_SELF'] ?? 'index.php', '.php');
if ($activePage === 'index') { $activePage = 'home'; }

$icons = [
    'home' => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 9.5V20h4.5v-5.5h4V20h4.5V9.5"/>',
    'lessons' => '<path d="M2.5 5.5c3-1.3 6.2-1.2 9.5.8v14c-3.3-2-6.5-2.1-9.5-.8z" fill="currentColor"/><path d="M21.5 5.5c-3-1.3-6.2-1.2-9.5.8v14c3.3-2 6.5-2.1 9.5-.8z"/>',
    'practice' => '<path d="M8 13V5.5a1.4 1.4 0 0 1 2.8 0V11"/><path d="M10.8 11V4a1.4 1.4 0 0 1 2.8 0v7"/><path d="M13.6 11V5a1.4 1.4 0 0 1 2.8 0v7"/><path d="M16.4 12V8a1.4 1.4 0 0 1 2.8 0v5.5c0 4-2.8 7-6.6 7-2.8 0-4.4-1.4-5.8-3.6L4.6 13.4a1.5 1.5 0 0 1 2.5-1.6L8 13"/>',
    'games' => '<path d="M7 6h10a5 5 0 0 1 4.9 6l-1 4.6a2.6 2.6 0 0 1-4.5 1.2L14.3 15H9.7l-2.1 2.8a2.6 2.6 0 0 1-4.5-1.2l-1-4.6A5 5 0 0 1 7 6z" fill="currentColor"/><path d="M7.5 9v4M5.5 11h4" stroke="#fff"/><circle cx="16" cy="10" r="1" fill="#fff" stroke="none"/><circle cx="18" cy="12.2" r="1" fill="#fff" stroke="none"/>',
    'progress' => '<rect x="3.5" y="13" width="4" height="7" rx="1" fill="currentColor"/><rect x="10" y="9" width="4" height="11" rx="1" fill="currentColor"/><rect x="16.5" y="4" width="4" height="16" rx="1" fill="currentColor"/>',
    'about' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6"/><circle cx="12" cy="7.6" r="1.1" fill="currentColor" stroke="none"/>',
    'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c.6-4.3 3.8-6.5 8-6.5s7.4 2.2 8 6.5"/>',
    'play' => '<path d="M7 4.5v15a1 1 0 0 0 1.5.9l12-7.5a1 1 0 0 0 0-1.7l-12-7.5A1 1 0 0 0 7 4.5z" fill="currentColor" stroke="none"/>',
    'teachers' => '<circle cx="9" cy="7.5" r="3.6" fill="currentColor" stroke="none"/><path d="M2 20c.4-4.4 3.2-7 7-7s6.6 2.6 7 7z" fill="currentColor" stroke="none"/><circle cx="17" cy="8.5" r="2.8" fill="currentColor" stroke="none"/><path d="M17.5 13c2.6.3 4.3 2.6 4.5 6h-4.4c-.2-2.4-1-4.4-2.4-5.6.7-.3 1.5-.4 2.3-.4z" fill="currentColor" stroke="none"/>',
    'chevron' => '<path d="m9 5.5 6.5 6.5L9 18.5"/>',
];

function icon(string $name, int $size = 22, float $stroke = 2): string {
    global $icons;
    return '<svg class="ico" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' . $stroke . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($icons[$name] ?? '') . '</svg>';
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$nav = [
    ['key'=>'home','label'=>'Home','href'=>'index.php'],
    ['key'=>'lessons','label'=>'Lessons','href'=>'student_dashboard.php'],
    ['key'=>'practice','label'=>'Practice','href'=>'student_pages/quiz.php'],
    ['key'=>'games','label'=>'Games','href'=>'student_pages/quiz.php'],
    ['key'=>'progress','label'=>'Progress','href'=>'student_pages/achievement.php'],
    ['key'=>'about','label'=>'About','href'=>'#about'],
];
$features = [
    ['title'=>'Video Lessons','icon'=>'assets/img/icon1.png','theme'=>'purple','href'=>'login.php','text'=>'Watch and learn signs through fun and engaging lessons.'],
    ['title'=>'Practice Signs','icon'=>'assets/img/icon2.png','theme'=>'green','href'=>'login.php','text'=>'Practice ASL letters and words with interactive exercises.'],
    ['title'=>'Fun Games','icon'=>'assets/img/icon3.png','theme'=>'yellow','href'=>'login.php','text'=>'Play exciting activities that help you learn and remember signs!'],
    ['title'=>'My Progress','icon'=>'assets/img/icon4.png','theme'=>'blue','href'=>'login.php','text'=>'Track your learning progress and achievements.'],
    ['title'=>'For Teachers','icon'=>'assets/img/icon5.png','theme'=>'pink','href'=>'login.php','text'=>'Access teaching tools, resources, and student progress.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($siteName) ?> – <?= e(implode(' ', $siteTag)) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
  <div class="wrap header-inner">
    <a class="brand" href="index.php">
      <img src="assets/img/logo.png" alt="KIDSS logo" class="brand-logo">
      <span class="brand-text">
        <span class="brand-name" aria-label="<?= e($siteName) ?>"><span class="c1">K</span><span class="c2">I</span><span class="c3">D</span><span class="c4">S</span><span class="c5">S</span></span>
        <span class="brand-tag"><?= e($siteTag[0]) ?><br><?= e($siteTag[1]) ?></span>
      </span>
    </a>
    <nav class="main-nav" aria-label="Main">
      
    </nav>
    <a href="login.php" class="btn-login"><?= icon('user', 22, 1.8) ?><span>Login</span></a>
  </div>
</header>
<section class="hero">
  <div class="wrap hero-inner">
    <div class="hero-copy">
      <h1><span class="t-blue">Learn.</span> <span class="t-pink">Sign.</span><br><span class="t-green">Grow</span> <span class="t-navy">Together!</span></h1>
      <p>An interactive and fun way for kindergartners<br>to learn American Sign Language (ASL)<br>through digital lessons, videos, and games.</p>
      <div class="hero-actions">
        <a href="login.php" class="btn btn-primary"><?= icon('play', 26) ?><span>Start Learning</span></a>
        <a href="login.php" class="btn btn-white"><?= icon('teachers', 30) ?><span>For Teachers</span></a>
      </div>
    </div>
  </div>
</section>
<main class="features-panel">
  <div class="features">
    <?php foreach ($features as $f): ?>
      <article class="card card-<?= e($f['theme']) ?>">
        <div class="card-inner">
          <img src="<?= e($f['icon']) ?>" alt="" class="card-icon">
          <h2><?= e($f['title']) ?></h2><p><?= e($f['text']) ?></p>
          <a href="<?= e($f['href']) ?>" class="btn-explore">Explore <?= icon('chevron', 18, 2.6) ?></a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
  <div class="strip">
    <img src="assets/img/shield.png" alt="" class="strip-shield">
    <strong>Safe • Fun • Educational</strong>
    <span>Designed especially for young learners to build confidence and communication skills.</span>
    <img src="assets/hearts.png" alt="" class="strip-hearts">
  </div>
</main>
</body>
</html>
