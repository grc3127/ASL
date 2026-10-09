<style>
.student-settings-page{padding:8px 2px 28px;color:#24355f}.student-settings-page h1{font-size:1.8rem;font-weight:850;margin-bottom:5px}.student-settings-page .muted{color:#6b7280}.settings-panel{max-width:850px;background:#fff;border:1px solid #e8ebf3;border-radius:20px;padding:22px;margin-top:22px;box-shadow:0 7px 20px rgba(40,55,100,.04)}.setting-row{display:flex;justify-content:space-between;align-items:center;gap:20px;padding:16px 0;border-bottom:1px solid #edf0f6}.setting-row:last-child{border-bottom:0}.setting-row h2{font-size:1rem;font-weight:800;margin:0 0 4px}.setting-row p{font-size:.9rem;margin:0;color:#6b7280}.settings-message{display:none;margin-top:16px}.settings-large-text #page-content{font-size:1.12rem}.settings-high-contrast #page-content{filter:contrast(1.2)}@media(max-width:620px){.setting-row{align-items:flex-start;flex-direction:column}}
</style>
<div class="student-settings-page">
  <h1>Settings</h1><p class="muted">Adjust a few learning preferences for this browser.</p>
  <section class="settings-panel">
    <div class="setting-row"><div><h2>Larger text</h2><p>Make lesson and dashboard text easier to read.</p></div><div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="largeTextSetting"><label class="form-check-label" for="largeTextSetting">Enable</label></div></div>
    <div class="setting-row"><div><h2>Higher contrast</h2><p>Increase contrast for content in the student portal.</p></div><div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="contrastSetting"><label class="form-check-label" for="contrastSetting">Enable</label></div></div>
    <div class="settings-message alert alert-success" id="settingsMessage" role="status">Preferences updated for this page session.</div>
    <p class="muted small mt-3 mb-0">These preferences are currently browser-side only and are not saved to your account.</p>
  </section>
</div>
<script>
(function(){
  const root=document.body;
  const large=document.getElementById('largeTextSetting');
  const contrast=document.getElementById('contrastSetting');
  const message=document.getElementById('settingsMessage');
  if(!large||!contrast)return;
  large.checked=root.classList.contains('settings-large-text');
  contrast.checked=root.classList.contains('settings-high-contrast');
  function update(){
    root.classList.toggle('settings-large-text',large.checked);
    root.classList.toggle('settings-high-contrast',contrast.checked);
    message.style.display='block';
  }
  large.addEventListener('change',update);
  contrast.addEventListener('change',update);
})();
</script>
