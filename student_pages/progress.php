<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$studentDisplayName = htmlspecialchars((string)($_SESSION['name'] ?? 'Student'), ENT_QUOTES, 'UTF-8');
?>
<style>
.student-progress-page{padding:8px 2px 28px;color:#24355f}.student-progress-page h1{font-size:1.8rem;font-weight:850;margin-bottom:5px}.student-progress-page .muted{color:#6b7280}.progress-summary-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin:22px 0}.progress-summary-card,.progress-detail-card{background:#fff;border:1px solid #e8ebf3;border-radius:18px;padding:20px;box-shadow:0 7px 20px rgba(40,55,100,.04)}.progress-summary-card small{display:block;color:#6b7280;font-weight:700}.progress-summary-card strong{display:block;font-size:1.8rem;margin:7px 0}.progress-detail-card h2{font-size:1.1rem;font-weight:800}.student-progress-page .progress{height:10px;background:#eceaf5}.student-progress-page .progress-bar{background:#7048e8}.demo-note{padding:12px 14px;border-radius:12px;background:#fff4cf;color:#795600;font-size:.9rem;margin-top:18px}@media(max-width:760px){.progress-summary-grid{grid-template-columns:1fr}}
</style>
<div class="student-progress-page">
  <h1>My Progress</h1>
  <p class="muted">Keep going, <?= $studentDisplayName ?>! Every practice session helps you grow.</p>
  <div class="progress-summary-grid">
    <div class="progress-summary-card"><small>Lessons completed</small><strong>6 <span class="muted" style="font-size:1rem">/ 30</span></strong><div class="progress"><div class="progress-bar" style="width:20%"></div></div></div>
    <div class="progress-summary-card"><small>Quiz average</small><strong>80%</strong><div class="progress"><div class="progress-bar" style="width:80%"></div></div></div>
    <div class="progress-summary-card"><small>Achievements earned</small><strong>4 <span class="muted" style="font-size:1rem">badges</span></strong><div class="progress"><div class="progress-bar" style="width:40%"></div></div></div>
  </div>
  <div class="progress-detail-card">
    <h2>Learning categories</h2>
    <div class="mb-3"><div class="d-flex justify-content-between mb-1"><strong>Alphabet</strong><span>6 / 26</span></div><div class="progress"><div class="progress-bar" style="width:23%"></div></div></div>
    <div class="mb-3"><div class="d-flex justify-content-between mb-1"><strong>Greetings</strong><span>0 / 4</span></div><div class="progress"><div class="progress-bar" style="width:0%"></div></div></div>
    <div><div class="d-flex justify-content-between mb-1"><strong>Vocabulary</strong><span>0 / 10</span></div><div class="progress"><div class="progress-bar" style="width:0%"></div></div></div>
  </div>
  <div class="demo-note"><strong>Preview data:</strong> Progress values are sample values for the current frontend. They will need to be connected to the database tables when backend progress tracking is implemented.</div>
</div>
