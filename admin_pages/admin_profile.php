<?php require_once __DIR__ . '/../auth/guard.php'; requireRole('admin','administrator'); ?>
<div class="admin-topbar"><div><h1>Admin Profile</h1><p>Update the administrator account used for this session.</p></div></div>
<div id="adminProfileNotice" class="alert d-none" role="status"></div>
<div class="admin-card"><form id="adminProfileForm" class="form-grid-admin">
<div class="form-group-admin"><label>First name *</label><input class="admin-input" name="first_name" required></div>
<div class="form-group-admin"><label>Middle name</label><input class="admin-input" name="middle_name"></div>
<div class="form-group-admin"><label>Last name *</label><input class="admin-input" name="last_name" required></div>
<div class="form-group-admin"><label>Email *</label><input class="admin-input" name="email" type="email" required></div>
<div class="form-group-admin"><label>Username *</label><input class="admin-input" name="username" required></div>
<div class="form-group-admin"><label>New password</label><input class="admin-input" name="new_password" type="password" minlength="8" placeholder="Leave blank to keep current password"></div>
<div class="form-group-admin"><label>Confirm new password</label><input class="admin-input" id="adminProfileConfirm" type="password" minlength="8" placeholder="Re-enter new password"></div>
<div class="form-group-admin full"><p class="text-muted small">Leave both password fields blank if you do not want to change your password.</p><button class="admin-btn admin-btn-primary" type="submit"><i class="bi bi-check-lg"></i> Save Profile</button></div>
</form></div>
<script>
(function(){const ep='api/profile.php',form=document.getElementById('adminProfileForm'),notice=document.getElementById('adminProfileNotice'),confirmInput=document.getElementById('adminProfileConfirm');if(!form||form.dataset.bound)return;form.dataset.bound='1';function msg(t,ok){notice.className='alert '+(ok?'alert-success':'alert-danger');notice.textContent=t}
async function load(){try{const r=await fetch(ep,{credentials:'same-origin'}),d=await r.json();if(!r.ok||!d.success)throw Error(d.message);const p=d.profile;['first_name','middle_name','last_name','email','username'].forEach(k=>form.elements[k].value=p[k]||'')}catch(e){msg(e.message,false)}}
form.addEventListener('submit',async e=>{e.preventDefault();const password=form.elements.new_password.value;if(password!==confirmInput.value){msg('The new password and confirmation do not match.',false);return}const data=Object.fromEntries(new FormData(form));delete data.new_password;data.new_password=password;try{const r=await fetch(ep,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)}),d=await r.json();if(!r.ok||!d.success)throw Error(d.message);msg(d.message,true);form.elements.new_password.value='';confirmInput.value='';await load()}catch(e){msg(e.message,false)}});load()})();
</script>