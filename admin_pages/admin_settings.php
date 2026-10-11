<?php require_once __DIR__ . '/../auth/guard.php'; requireRole('admin','administrator'); ?>
<div class="admin-topbar"><div><h1>System Settings</h1><p>Update the system display name and active school year.</p></div></div>
<div id="systemSettingsNotice" class="alert d-none" role="status"></div>
<div class="admin-card"><form id="systemSettingsForm" class="form-grid-admin">
<div class="form-group-admin"><label for="systemName">System name</label><input class="admin-input" id="systemName" name="system_name" maxlength="100" required></div>
<div class="form-group-admin"><label for="schoolYear">School year</label><input class="admin-input" id="schoolYear" name="school_year" placeholder="2026-2027" pattern="\d{4}\s*[-–]\s*\d{4}" required></div>
<div class="form-group-admin full"><p class="text-muted small">These settings are stored in the system_settings table. Account registration and notification controls are not shown because the application does not use those features.</p><button class="admin-btn admin-btn-primary" type="submit"><i class="bi bi-save"></i> Save Settings</button></div>
</form><p id="settingsUpdatedAt" class="text-muted small mt-3 mb-0"></p></div>
<script>
(function(){const ep='api/system_management.php',form=document.getElementById('systemSettingsForm'),notice=document.getElementById('systemSettingsNotice'),updated=document.getElementById('settingsUpdatedAt');if(!form||form.dataset.bound)return;form.dataset.bound='1';function msg(t,ok){notice.className='alert '+(ok?'alert-success':'alert-danger');notice.textContent=t}
async function load(){try{const r=await fetch(ep+'?view=settings',{credentials:'same-origin'}),d=await r.json();if(!r.ok||!d.success)throw Error(d.message);form.elements.system_name.value=d.settings.system_name||'KIDSS';form.elements.school_year.value=d.settings.school_year||'2026-2027';updated.textContent=d.updated_at?'Last updated: '+d.updated_at:''}catch(e){msg(e.message,false)}}
form.addEventListener('submit',async e=>{e.preventDefault();const data=Object.fromEntries(new FormData(form));data.action='settings_save';try{const r=await fetch(ep,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)}),d=await r.json();if(!r.ok||!d.success)throw Error(d.message);msg(d.message,true);await load()}catch(e){msg(e.message,false)}});load()})();
</script>