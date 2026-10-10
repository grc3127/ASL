<?php
require_once __DIR__ . '/../auth/guard.php';
requireRole('admin', 'administrator', 'teacher');
?>
<div class="admin-topbar">
  <div><h1>Lesson Management</h1><p>Create and publish lessons shared with the student learning area.</p></div>
</div>
<div id="lessonManagerNotice" class="alert d-none" role="status"></div>
<div class="admin-card">
  <div class="admin-toolbar">
    <input id="lessonSearch" class="admin-input" type="search" placeholder="Search lessons..." style="flex:1;min-width:200px">
    <select id="lessonCategoryFilter" class="admin-select"><option value="">All Categories</option></select>
    <button type="button" id="addLessonBtn" class="admin-btn admin-btn-primary"><i class="bi bi-plus-lg"></i> Add Lesson</button>
    <button type="button" id="addLessonCategoryBtn" class="admin-btn admin-btn-light"><i class="bi bi-folder-plus"></i> Add Category</button>
  </div>
  <div class="admin-table-wrap"><table class="admin-table">
    <thead><tr><th>Lesson</th><th>Category</th><th>Media</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody id="lessonManagementRows"><tr><td colspan="5">Loading lessons from database…</td></tr></tbody>
  </table></div>
  <p class="text-muted small mt-3 mb-0" id="lessonManagementCount"></p>
</div>
<div class="modal fade" id="lessonEditorModal" tabindex="-1" aria-labelledby="lessonEditorTitle" aria-hidden="true">
 <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
  <form id="lessonEditorForm">
   <div class="modal-header"><h5 class="modal-title" id="lessonEditorTitle">Add Lesson</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
   <div class="modal-body">
    <input type="hidden" id="lessonEditorId">
    <div class="row g-3">
     <div class="col-md-8"><label for="lessonTitle" class="form-label">Lesson title *</label><input id="lessonTitle" class="form-control" maxlength="150" required></div>
     <div class="col-md-4"><label for="lessonCategory" class="form-label">Category *</label><select id="lessonCategory" class="form-select" required></select></div>
     <div class="col-12"><label for="lessonDescription" class="form-label">Description / learning instructions</label><textarea id="lessonDescription" class="form-control" rows="3"></textarea></div>
     <div class="col-md-6"><label for="lessonThumbnail" class="form-label">Thumbnail path (optional)</label><input id="lessonThumbnail" class="form-control" placeholder="assets/lessons/A.png"></div>
     <div class="col-md-6"><label for="lessonDisplayOrder" class="form-label">Display order</label><input id="lessonDisplayOrder" class="form-control" type="number" value="0" min="0"></div>
     <div class="col-md-4"><label for="lessonImage" class="form-label">Image path</label><input id="lessonImage" class="form-control" placeholder="assets/lessons/A.png"></div>
     <div class="col-md-4"><label for="lessonAudio" class="form-label">Audio path</label><input id="lessonAudio" class="form-control" placeholder="assets/lessons/A.mp3"></div>
     <div class="col-md-4"><label for="lessonVideo" class="form-label">Video path</label><input id="lessonVideo" class="form-control" placeholder="assets/lessons/A.mp4"></div>
     <div class="col-md-6"><label for="lessonStatus" class="form-label">Publication status</label><select id="lessonStatus" class="form-select"><option value="draft">Draft (hidden from students)</option><option value="published">Published (visible to students)</option><option value="archived">Archived (hidden from students)</option></select></div>
    </div>
    <p class="text-muted small mt-3 mb-0">Media paths are saved to the lesson_media table. Use paths relative to the project root. Publishing makes this lesson visible to students immediately.</p>
   </div>
   <div class="modal-footer"><button type="button" class="admin-btn admin-btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="admin-btn admin-btn-primary" id="saveLessonBtn">Save Lesson</button></div>
  </form>
 </div></div>
</div>
<script>
(function(){
 const endpoint='api/lesson_management.php';
 const rows=document.getElementById('lessonManagementRows');
 const search=document.getElementById('lessonSearch');
 const filter=document.getElementById('lessonCategoryFilter');
 const categorySelect=document.getElementById('lessonCategory');
 const notice=document.getElementById('lessonManagerNotice');
 const form=document.getElementById('lessonEditorForm');
 const modalElement=document.getElementById('lessonEditorModal');
 let editor=null,categories=[],lessons=[];
 const $=id=>document.getElementById(id);
 const esc=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 const media=(lesson,type)=>(lesson.media||[]).find(item=>item.media_type===type)?.file_path||'';
 function message(text,type='success'){notice.className='alert alert-'+type;notice.textContent=text;notice.classList.remove('d-none');}
 async function request(url,options){const response=await fetch(url,{credentials:'same-origin',headers:{'Accept':'application/json',...(options&&options.body?{'Content-Type':'application/json'}:{})},...options});const data=await response.json();if(!response.ok||!data.success)throw new Error(data.message||'Request failed.');return data;}
 function fillCategories(){
   filter.innerHTML='<option value="">All Categories</option>'+categories.map(c=>'<option value="'+Number(c.category_id)+'">'+esc(c.category_name)+'</option>').join('');
   categorySelect.innerHTML=categories.map(c=>'<option value="'+Number(c.category_id)+'">'+esc(c.category_name)+'</option>').join('');
 }
 function renderRows(){
   const term=search.value.trim().toLowerCase(),cat=filter.value;
   const shown=lessons.filter(l=>(!cat||String(l.category_id)===cat)&&(!term||(l.title+' '+(l.description||'')+' '+l.category_name).toLowerCase().includes(term)));
   $('lessonManagementCount').textContent=shown.length+' of '+lessons.length+' lesson record(s)';
   if(!shown.length){rows.innerHTML='<tr><td colspan="5" class="text-center py-4">No lesson records match. Add a lesson to get started.</td></tr>';return;}
   rows.innerHTML=shown.map(l=>{
     const mediaIcons=['image','audio','video'].map(type=>media(l,type)?'<i class="bi '+({image:'bi-image',audio:'bi-volume-up',video:'bi-play-btn'}[type])+' me-2" title="'+type+' available"></i>':'').join('')||'<span class="text-muted small">No media</span>';
     const badge=l.status==='published'?'badge-active':(l.status==='draft'?'badge-draft':'badge-secondary');
     return '<tr><td><strong>'+esc(l.title)+'</strong><br><small>'+esc(l.description||'No description')+'</small></td><td>'+esc(l.category_name)+'</td><td>'+mediaIcons+'</td><td><span class="badge-status '+badge+'">'+esc(l.status)+'</span></td><td><button type="button" class="admin-btn admin-btn-light me-1" data-edit="'+Number(l.lesson_id)+'">Edit</button><button type="button" class="admin-btn admin-btn-light" data-status="'+Number(l.lesson_id)+'" data-next="'+(l.status==='published'?'draft':'published')+'">'+(l.status==='published'?'Unpublish':'Publish')+'</button></td></tr>';
   }).join('');
 }
 async function reload(){const data=await request(endpoint);categories=data.categories||[];lessons=data.lessons||[];fillCategories();renderRows();}
 function openEditor(lesson){
   form.reset();$('lessonEditorId').value=lesson?lesson.lesson_id:'';$('lessonEditorTitle').textContent=lesson?'Edit Lesson':'Add Lesson';
   if(lesson){$('lessonTitle').value=lesson.title||'';$('lessonCategory').value=lesson.category_id;$('lessonDescription').value=lesson.description||'';$('lessonThumbnail').value=lesson.thumbnail||'';$('lessonDisplayOrder').value=lesson.display_order||0;$('lessonStatus').value=lesson.status||'draft';$('lessonImage').value=media(lesson,'image');$('lessonAudio').value=media(lesson,'audio');$('lessonVideo').value=media(lesson,'video');}
   if(window.bootstrap&&bootstrap.Modal){editor=bootstrap.Modal.getOrCreateInstance(modalElement);editor.show();}
   else message('Bootstrap modal is unavailable. Please check assets/bootstrap/js/bootstrap.bundle.min.js on the dashboard.','danger');
 }
 $('addLessonBtn').addEventListener('click',()=>openEditor(null));
 $('addLessonCategoryBtn').addEventListener('click',async()=>{
   const name=window.prompt('Enter a new lesson category name:');if(name===null)return;
   const trimmed=name.trim();if(!trimmed)return;
   try{await request(endpoint,{method:'POST',body:JSON.stringify({action:'create_category',category_name:trimmed})});await reload();message('Category created.');}
   catch(error){message(error.message,'danger');}
 });
 search.addEventListener('input',renderRows);filter.addEventListener('change',renderRows);
 rows.addEventListener('click',async event=>{
   const edit=event.target.closest('[data-edit]');
   if(edit){const lesson=lessons.find(l=>Number(l.lesson_id)===Number(edit.dataset.edit));if(lesson)openEditor(lesson);return;}
   const status=event.target.closest('[data-status]');if(!status)return;
   try{await request(endpoint,{method:'POST',body:JSON.stringify({action:'status',lesson_id:Number(status.dataset.status),status:status.dataset.next})});await reload();message('Lesson status updated.');}
   catch(error){message(error.message,'danger');}
 });
 form.addEventListener('submit',async event=>{
   event.preventDefault();const id=$('lessonEditorId').value;
   const data={action:id?'update':'create',lesson_id:id?Number(id):undefined,category_id:Number($('lessonCategory').value),title:$('lessonTitle').value.trim(),description:$('lessonDescription').value.trim(),thumbnail:$('lessonThumbnail').value.trim(),display_order:Number($('lessonDisplayOrder').value||0),status:$('lessonStatus').value,image:$('lessonImage').value.trim(),audio:$('lessonAudio').value.trim(),video:$('lessonVideo').value.trim()};
   $('saveLessonBtn').disabled=true;
   try{await request(endpoint,{method:'POST',body:JSON.stringify(data)});if(editor)editor.hide();await reload();message(id?'Lesson updated.':'Lesson created.');}
   catch(error){message(error.message,'danger');}
   finally{$('saveLessonBtn').disabled=false;}
 });
 reload().catch(error=>{rows.innerHTML='<tr><td colspan="5">Unable to load lessons: '+esc(error.message)+'</td></tr>';message(error.message,'danger');});
})();
</script>
