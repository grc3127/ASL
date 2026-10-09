<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$profileName = (string)($_SESSION['name'] ?? 'Student');
$profileUsername = (string)($_SESSION['username'] ?? '');
$profileEmail = (string)($_SESSION['email'] ?? '');
$studentNumber = (string)($_SESSION['student_number'] ?? 'Not assigned');
function profileEscape(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<style>
.student-profile-page{padding:8px 2px 28px;color:#24355f}.student-profile-page h1{font-size:1.8rem;font-weight:850;margin-bottom:5px}.student-profile-page .muted{color:#6b7280}.profile-panel{max-width:850px;background:#fff;border:1px solid #e8ebf3;border-radius:20px;padding:24px;margin-top:22px;box-shadow:0 7px 20px rgba(40,55,100,.04)}.profile-hero{display:flex;align-items:center;gap:16px;padding-bottom:20px;border-bottom:1px solid #edf0f6}.profile-avatar{width:76px;height:76px;border-radius:50%;background:#f1eaff;display:grid;place-items:center;color:#7048e8;font-size:2rem}.profile-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-top:22px}.profile-field{padding:13px 15px;border-radius:12px;background:#f8f9fc;min-width:0}.profile-field small{display:block;color:#6b7280;margin-bottom:5px}.profile-field strong{overflow-wrap:anywhere}@media(max-width:620px){.profile-fields{grid-template-columns:1fr}.profile-panel{padding:18px}}
</style>
<div class="student-profile-page">
  <h1>My Profile</h1><p class="muted">View the account details associated with your student login.</p>
  <section class="profile-panel">
    <div class="profile-hero"><div class="profile-avatar"><i class="bi bi-person-fill" aria-hidden="true"></i></div><div><h2 class="h4 fw-bold mb-1"><?= profileEscape($profileName) ?></h2><span class="badge text-bg-primary">Student</span></div></div>
    <div class="profile-fields">
      <div class="profile-field"><small>Full name</small><strong><?= profileEscape($profileName) ?></strong></div>
      <div class="profile-field"><small>Username</small><strong><?= profileEscape($profileUsername ?: 'Not available') ?></strong></div>
      <div class="profile-field"><small>Email address</small><strong><?= profileEscape($profileEmail ?: 'Not available') ?></strong></div>
      <div class="profile-field"><small>Student number</small><strong><?= profileEscape($studentNumber) ?></strong></div>
    </div>
    <p class="muted small mt-3 mb-0">Profile editing is not connected yet. These details are read from the current login session.</p>
  </section>
</div>
