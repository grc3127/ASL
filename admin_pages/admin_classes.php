<?php require_once __DIR__ . '/../auth/guard.php'; requireRole('admin','administrator'); ?>
<div class="admin-topbar"><div><h1>Classes &amp; Sections</h1><p>Create sections, assign teachers, and enroll or transfer students using database records.</p></div></div>
<div id="classNotice" class="alert d-none" role="status"></div>
<div class="admin-card mb-4"><h2 class="h5 fw-bold">Create / Update Class</h2>
<form id="classForm" class="row g-2">
<input type="hidden" name="class_id" id="classId">
<div class="col-md-3"><label class="form-label">Class name *</label><input class="admin-input w-100" name="class_name" required placeholder="Kindergarten"></div>
<div class="col-md-2"><label class="form-label">Grade level</label><input class="admin-input w-100" name="grade_level" placeholder="Kinder"></div>
<div class="col-md-2"><label class="form-label">Section</label><input class="admin-input w-100" name="section" placeholder="A"></div>
<div class="col-md-2"><label class="form-label">School year *</label><input class="admin-input w-100" name="school_year" required value="2026-2027" pattern="\d{4}\s*[-–]\s*\d{4}"></div>
<div class="col-md-3"><label class="form-label">Teacher *</label><select class="admin-select w-100" name="teacher_id" id="classTeacher" required><option value="">Loading teachers…</option></select></div>
<div class="col-12"><button class="admin-btn admin-btn-primary" type="submit">Save class</button> <button class="admin-btn admin-btn-light" type="button" id="classFormReset">Clear</button></div>
</form></div>
<div class="admin-card mb-4"><h2 class="h5 fw-bold">Add / Transfer Student</h2><form id="enrollForm" class="row g-2">
<div class="col-md-5"><label class="form-label">Student *</label><select class="admin-select w-100" name="student_id" id="enrollStudent" required><option value="">Loading students…</option></select></div>
<div class="col-md-5"><label class="form-label">Destination class *</label><select class="admin-select w-100" name="class_id" id="enrollClass" required><option value="">Select class</option></select></div>
<div class="col-md-2 d-flex align-items-end"><button class="admin-btn admin-btn-primary w-100" type="submit">Add student</button></div>
<div class="col-12"><label><input type="checkbox" id="transferMode"> Transfer student from previous active class (deactivate old enrollment)</label></div>
</form></div>
<div class="admin-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Class / Section</th><th>Teacher</th><th>Grade</th><th>School year</th><th>Students</th><th>Status</th><th>Action</th></tr></thead><tbody id="classRows"><tr><td colspan="7">Loading classes…</td></tr></tbody></table></div></div>
<script>
(function(){const ep='api/system_management.php',form=document.getElementById('classForm'),enroll=document.getElementById('enrollForm'),rows=document.getElementById('classRows'),notice=document.getElementById('classNotice'),teacher=document.getElementById('classTeacher'),student=document.getElementById('enrollStudent'),destination=document.getElementById('enrollClass');if(!form||form.dataset.bound)return;form.dataset.bound='1';let classes=[];
const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
function msg(t,ok){notice.className='alert '+(ok?'alert-success':'alert-danger');notice.textContent=t}
async function request(body){const r=await fetch(ep,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)}),d=await r.json();if(!r.ok||!d.success)throw Error(d.message||'Request failed');return d}
function render(){rows.innerHTML=classes.length?classes.map(c=>'<tr><td><strong>'+esc(c.class_name)+(c.section?' - '+esc(c.section):'')+'</strong></td><td>'+esc(c.teacher_name)+'</td><td>'+esc(c.grade_level||'—')+'</td><td>'+esc(c.school_year)+'</td><td>'+Number(c.student_count)+'</td><td>'+esc(c.status)+'</td><td><button class="admin-btn admin-btn-light" data-edit="'+c.class_id+'">Edit</button></td></tr>').join(''):'<tr><td colspan="7" class="text-center text-muted py-4">No classes in database. Create your first class above.</td></tr>';destination.innerHTML='<option value="">Select class</option>'+classes.filter(c=>c.status==='active').map(c=>'<option value="'+c.class_id+'">'+esc(c.class_name+(c.section?' - '+c.section:'')+' ('+c.school_year+')')+'</option>').join('')}
async function load(){try{const r=await fetch(ep+'?view=classes',{credentials:'same-origin'}),d=await r.json();if(!r.ok||!d.success)throw Error(d.message);classes=d.classes||[];if(!document.getElementById('classId').value&&d.school_year)form.elements.school_year.value=d.school_year;teacher.innerHTML='<option value="">Select teacher</option>'+(d.teachers||[]).map(t=>'<option value="'+t.teacher_id+'">'+esc(t.full_name)+'</option>').join('');student.innerHTML='<option value="">Select student</option>'+(d.students||[]).map(s=>'<option value="'+s.student_id+'">'+esc(s.full_name+(s.student_number?' · '+s.student_number:''))+'</option>').join('');render()}catch(e){msg(e.message,false);rows.innerHTML='<tr><td colspan="7">'+esc(e.message)+'</td></tr>'}}
form.addEventListener('submit',async e=>{e.preventDefault();const d=Object.fromEntries(new FormData(form));d.action='class_save';try{const r=await request(d);msg(r.message,true);form.reset();document.getElementById('classId').value='';await load()}catch(e){msg(e.message,false)}});
document.getElementById('classFormReset').addEventListener('click',()=>{form.reset();document.getElementById('classId').value=''});
rows.addEventListener('click',e=>{const b=e.target.closest('[data-edit]');if(!b)return;const c=classes.find(x=>Number(x.class_id)===Number(b.dataset.edit));if(!c)return;document.getElementById('classId').value=c.class_id;form.elements.class_name.value=c.class_name;form.elements.grade_level.value=c.grade_level||'';form.elements.section.value=c.section||'';form.elements.school_year.value=c.school_year;teacher.value=c.teacher_id;form.scrollIntoView({behavior:'smooth',block:'start'})});
enroll.addEventListener('submit',async e=>{e.preventDefault();const d=Object.fromEntries(new FormData(enroll));d.action=document.getElementById('transferMode').checked?'class_transfer':'class_enroll';try{const r=await request(d);msg(r.message,true);enroll.reset();await load()}catch(e){msg(e.message,false)}});
load()})();
</script>