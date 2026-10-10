<?php
require_once __DIR__ . '/../auth/guard.php';
requireRole('student');
$studentName = htmlspecialchars((string)($_SESSION['name'] ?? 'Student'), ENT_QUOTES, 'UTF-8');
?>
<div class="lessons-page-body">
  <div class="row align-items-center mb-4 g-3">
    <div class="col-12 col-md-7">
      <div class="lesson-heading">
        <div class="lesson-title-art"><span class="lesson-title-letter">K</span><span class="title-sparkle ts-one">✦</span><span class="title-sparkle ts-two">✦</span></div>
        <div><h1>My Lessons</h1><p>Explore the sign-language lessons your teacher has published.</p></div>
      </div>
    </div>
    <div class="col-12 col-md-5 d-flex justify-content-md-end align-items-center">
      <div class="profile-pill d-flex align-items-center">
        <img src="https://api.dicebear.com/7.x/bottts/svg?seed=<?= rawurlencode((string)($_SESSION['name'] ?? 'Student')) ?>" alt="Student avatar" class="user-avatar">
        <div class="lh-1 me-2"><div class="fw-bold fs-6"><?= $studentName ?></div><small class="text-muted">Student</small></div>
      </div>
    </div>
  </div>
  <div id="studentLessonNotice" class="alert d-none" role="status"></div>
  <div id="studentLessonSummary" class="mb-3 text-muted small" aria-live="polite">Loading published lessons…</div>
  <div id="studentLessonCategories">
    <div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading</span></div><p class="mt-3 text-muted">Loading your lessons…</p></div>
  </div>
  <div id="studentLessonEmpty" class="admin-card text-center py-5 d-none">
    <i class="bi bi-journal-bookmark fs-1 text-muted"></i>
    <h3 class="mt-3">No published lessons yet</h3>
    <p class="text-muted mb-0">Your lessons will appear here after a teacher or administrator publishes them.</p>
  </div>
  <div id="studentLessonPlayer" class="modal fade" tabindex="-1" aria-labelledby="studentLessonPlayerTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
      <div class="modal-header"><div><h5 class="modal-title" id="studentLessonPlayerTitle">Lesson</h5><p id="studentLessonPlayerDescription" class="text-muted small mb-0"></p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body" id="studentLessonPlayerBody"></div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-success" id="studentLessonCompleteBtn">Mark as completed</button></div>
    </div></div>
  </div>
</div>
<script>
(function () {
  const endpoint = 'api/lesson_management.php';
  const root = document.getElementById('studentLessonCategories');
  if (!root || root.dataset.bound === 'true') return;
  root.dataset.bound = 'true';
  const summary = document.getElementById('studentLessonSummary');
  const empty = document.getElementById('studentLessonEmpty');
  const notice = document.getElementById('studentLessonNotice');
  let activeLesson = null;
  let player = null;
  const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  function showNotice(message, type='info') {
    notice.className = 'alert alert-' + type;
    notice.textContent = message;
    notice.classList.remove('d-none');
  }
  function mediaFor(lesson, type) { return (lesson.media || []).find(media => media.media_type === type)?.file_path || ''; }
  function lessonCard(lesson, index) {
    const image = lesson.thumbnail || mediaFor(lesson, 'image');
    const completed = lesson.progress_status === 'completed';
    const artClasses = ['art-red','art-blue','art-yellow','art-green'];
    const visual = image
      ? '<img src="' + escapeHtml(image) + '" alt="' + escapeHtml(lesson.title) + '" style="width:100%;height:100%;object-fit:contain;padding:12px">'
      : '<span class="lesson-letter">' + escapeHtml(lesson.title.length <= 2 ? lesson.title : '✋') + '</span>';
    return '<article class="lesson-card ' + (completed ? 'completed' : 'not-started') + '">' +
      '<div class="lesson-card-art ' + artClasses[index % artClasses.length] + '">' + visual +
      '<button type="button" class="lesson-play" data-open-lesson="' + Number(lesson.lesson_id) + '" aria-label="Open ' + escapeHtml(lesson.title) + '"><i class="bi bi-play-fill"></i></button></div>' +
      '<div class="lesson-card-body"><div class="lesson-main-copy"><div class="lesson-name-row"><h2>' + escapeHtml(lesson.title) + '</h2><i class="bi bi-star-fill lesson-star ' + (completed ? 'earned' : '') + '"></i></div>' +
      '<p>' + escapeHtml(lesson.description || 'Practice this sign.') + '</p><div class="lesson-status ' + (completed ? 'completed-status' : 'locked-status') + '"><i class="bi ' + (completed ? 'bi-check-circle-fill' : 'bi-bookmark') + '"></i> ' + (completed ? 'Completed' : 'Available') + '</div></div>' +
      '<button type="button" class="lesson-next" data-open-lesson="' + Number(lesson.lesson_id) + '" aria-label="Open ' + escapeHtml(lesson.title) + '"><i class="bi bi-chevron-right"></i></button></div></article>';
  }
  function render(data) {
    const categories = data.categories || [];
    const lessons = data.lessons || [];
    const byCategory = new Map();
    lessons.forEach(lesson => {
      const key = String(lesson.category_id);
      if (!byCategory.has(key)) byCategory.set(key, []);
      byCategory.get(key).push(lesson);
    });
    const groups = categories.map(category => ({category, lessons: byCategory.get(String(category.category_id)) || []})).filter(group => group.lessons.length);
    summary.textContent = lessons.length + ' published lesson' + (lessons.length === 1 ? '' : 's') + ' in ' + groups.length + ' categor' + (groups.length === 1 ? 'y' : 'ies');
    empty.classList.toggle('d-none', lessons.length > 0);
    if (!lessons.length) { root.innerHTML = ''; return; }
    root.innerHTML = groups.map((group, groupIndex) => {
      const id = 'publishedLessonCategory' + group.category.category_id;
      const completedCount = group.lessons.filter(lesson => lesson.progress_status === 'completed').length;
      const pct = Math.round(completedCount / group.lessons.length * 100);
      return '<section class="lesson-category collapsible-category mb-4"><button class="lesson-category-bar category-toggle" type="button" data-target="' + id + '" aria-expanded="' + (groupIndex === 0 ? 'true' : 'false') + '" aria-controls="' + id + '">' +
        '<div class="lesson-category-info"><div class="lesson-book-icon"><i class="bi bi-book-half"></i></div><div><h3>' + escapeHtml(group.category.category_name) + '</h3><span>' + group.lessons.length + ' published lesson' + (group.lessons.length === 1 ? '' : 's') + '</span></div></div>' +
        '<div class="lesson-progress-wrap"><div class="lesson-progress-label"><strong>' + completedCount + ' / ' + group.lessons.length + '</strong> Completed</div><div class="lesson-progress"><div class="lesson-progress-fill" style="width:' + pct + '%"></div></div></div>' +
        '<span class="category-chevron" aria-hidden="true"><i class="bi ' + (groupIndex === 0 ? 'bi-chevron-up' : 'bi-chevron-down') + '"></i></span></button>' +
        '<div id="' + id + '" class="category-content ' + (groupIndex === 0 ? 'is-open' : '') + '" ' + (groupIndex === 0 ? '' : 'hidden') + '><div class="lesson-grid">' +
        group.lessons.map((lesson,index) => lessonCard(lesson,index)).join('') + '</div></div></section>';
    }).join('');
  }
  async function loadLessons() {
    try {
      const response = await fetch(endpoint, {credentials:'same-origin', headers:{'Accept':'application/json'}});
      const data = await response.json();
      if (!response.ok || !data.success) throw new Error(data.message || 'Could not load lessons.');
      const progressResponse = await fetch('api/lesson_progress.php', {credentials:'same-origin', headers:{'Accept':'application/json'}});
      if (progressResponse.ok) {
        const progress = await progressResponse.json();
        if (progress.success) {
          const lookup = new Map((progress.progress || []).map(item => [String(item.lesson_id), item]));
          data.lessons.forEach(lesson => { const item = lookup.get(String(lesson.lesson_id)); lesson.progress_status = item?.status || 'not_started'; });
        }
      }
      render(data);
    } catch (error) {
      root.innerHTML = '';
      showNotice(error.message || 'Unable to load published lessons. Check your database connection.', 'danger');
      summary.textContent = 'Lessons could not be loaded.';
    }
  }
  async function saveProgress(status) {
    if (!activeLesson) return;
    const response = await fetch(endpoint, {method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({action:'progress',lesson_id:Number(activeLesson.lesson_id),status})});
    const data = await response.json();
    if (!response.ok || !data.success) throw new Error(data.message || 'Unable to save progress.');
  }
  async function openLesson(id) {
    try {
      const response = await fetch(endpoint, {credentials:'same-origin',headers:{'Accept':'application/json'}});
      const data = await response.json();
      if (!response.ok || !data.success) throw new Error(data.message || 'Unable to open lesson.');
      activeLesson = data.lessons.find(item => Number(item.lesson_id) === Number(id));
      if (!activeLesson) throw new Error('This lesson is no longer published.');
      await saveProgress('in_progress');
      document.getElementById('studentLessonPlayerTitle').textContent = activeLesson.title;
      document.getElementById('studentLessonPlayerDescription').textContent = activeLesson.description || '';
      const body = document.getElementById('studentLessonPlayerBody');
      const image = activeLesson.thumbnail || mediaFor(activeLesson,'image');
      const audio = mediaFor(activeLesson,'audio');
      const video = mediaFor(activeLesson,'video');
      body.innerHTML = (image ? '<div class="text-center mb-3"><img src="' + escapeHtml(image) + '" alt="' + escapeHtml(activeLesson.title) + '" style="max-width:100%;max-height:360px;object-fit:contain"></div>' : '') +
        (video ? '<div class="mb-3"><video controls playsinline class="w-100" style="max-height:420px" src="' + escapeHtml(video) + '"></video></div>' : '') +
        (audio ? '<div class="mb-3"><label class="form-label fw-bold">Listen to the lesson audio</label><audio controls class="w-100" src="' + escapeHtml(audio) + '"></audio></div>' : '') +
        (!image && !audio && !video ? '<div class="alert alert-light">Your teacher has published this lesson, but has not added media yet.</div>' : '') +
        '<p class="mb-0">' + escapeHtml(activeLesson.description || 'Practice the sign, then mark the lesson as completed when you are ready.') + '</p>';
      if (window.bootstrap && bootstrap.Modal) {
        player = bootstrap.Modal.getOrCreateInstance(document.getElementById('studentLessonPlayer'));
        player.show();
      } else {
        body.insertAdjacentHTML('afterbegin','<div class="alert alert-warning">Modal library unavailable. Lesson content is displayed below.</div>');
      }
    } catch(error) { showNotice(error.message || 'Unable to open this lesson.', 'danger'); }
  }
  root.addEventListener('click', event => {
    const button = event.target.closest('[data-open-lesson]');
    if (button) { event.preventDefault(); openLesson(button.dataset.openLesson); }
  });
  document.getElementById('studentLessonCompleteBtn').addEventListener('click', async () => {
    try {
      await saveProgress('completed');
      showNotice('Lesson completed! Your progress has been saved.', 'success');
      if (player) player.hide();
      await loadLessons();
    } catch(error) { showNotice(error.message || 'Unable to save your progress.', 'danger'); }
  });
  loadLessons();
})();
</script>
