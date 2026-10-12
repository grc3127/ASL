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
     <div class="col-md-6"><label for="lessonThumbnail" class="form-label">Thumbnail path (optional)</label><div class="input-group"><input id="lessonThumbnail" class="form-control" placeholder="assets/lesson-media/Basic Greetings/[IMG] Hello.jpg"><button type="button" class="btn btn-outline-secondary browse-media" data-target="lessonThumbnail" data-media-type="thumbnail">Browse files</button></div><small class="text-muted">Select an image already stored in the project; its relative path will be filled in.</small></div>
     <div class="col-md-6"><label for="lessonDisplayOrder" class="form-label">Display order</label><input id="lessonDisplayOrder" class="form-control" type="number" value="0" min="0"></div>
     <div class="col-md-4"><label for="lessonImage" class="form-label">Image path</label><div class="input-group"><input id="lessonImage" class="form-control" placeholder="assets/lesson-media/Basic Greetings/[IMG] Hello.jpg"><button type="button" class="btn btn-outline-secondary browse-media" data-target="lessonImage" data-media-type="image">Browse</button></div></div>
     <div class="col-md-4"><label for="lessonAudio" class="form-label">Audio path</label><div class="input-group"><input id="lessonAudio" class="form-control" placeholder="assets/lesson-media/audio.mp3"><button type="button" class="btn btn-outline-secondary browse-media" data-target="lessonAudio" data-media-type="audio">Browse</button></div></div>
     <div class="col-md-4"><label for="lessonVideo" class="form-label">Video path</label><div class="input-group"><input id="lessonVideo" class="form-control" placeholder="assets/lesson-media/Basic Greetings/[VID] Hello.mp4"><button type="button" class="btn btn-outline-secondary browse-media" data-target="lessonVideo" data-media-type="video">Browse</button></div></div>
     <div class="col-md-6"><label for="lessonStatus" class="form-label">Publication status</label><select id="lessonStatus" class="form-select"><option value="draft">Draft (hidden from students)</option><option value="published">Published (visible to students)</option><option value="archived">Archived (hidden from students)</option></select></div>
    </div>
    <p id="lessonUploadNotice" class="small mt-3 mb-0" role="status"></p>
    <p class="text-muted small mt-3 mb-0">Media paths are saved to the lesson_media table. Use paths relative to the project root. Publishing makes this lesson visible to students immediately.</p>
   </div>
   <div class="modal-footer"><button type="button" class="admin-btn admin-btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="admin-btn admin-btn-primary" id="saveLessonBtn">Save Lesson</button></div>
  </form>
 </div></div>
</div>
<div class="modal fade" id="mediaBrowserModal" tabindex="-1" aria-labelledby="mediaBrowserTitle" aria-hidden="true">
 <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title" id="mediaBrowserTitle">Choose an existing project asset</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
  <div class="modal-body">
   <p class="text-muted small">Select a file already present under assets/. This only detects and fills in its project-relative path; it does not upload or copy files from your computer.</p>
   <input id="mediaFileSearch" class="form-control mb-3" type="search" placeholder="Filter by file name or path">
   <select id="mediaFileSelect" class="form-select" size="10" aria-label="Available project assets"></select>
   <p id="mediaBrowserStatus" class="small text-muted mt-2 mb-0" role="status"></p>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="useMediaPathBtn">Use selected path</button></div>
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
 const mediaModalElement=document.getElementById('mediaBrowserModal');
 const mediaSelect=document.getElementById('mediaFileSelect');
 const mediaSearch=document.getElementById('mediaFileSearch');
 let editor=null,mediaBrowser=null,mediaTarget=null,mediaFiles=[],categories=[],lessons=[];
 const $=id=>document.getElementById(id);
 const esc=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 const media=(lesson,type)=>(lesson.media||[]).find(item=>item.media_type===type)?.file_path||'';
 function message(text,type='success'){notice.className='alert alert-'+type;notice.textContent=text;notice.classList.remove('d-none');}
 async function request(url,options){const response=await fetch(url,{credentials:'same-origin',headers:{'Accept':'application/json',...(options&&options.body?{'Content-Type':'application/json'}:{})},...options});const raw=await response.text();let data;try{data=JSON.parse(raw)}catch(parseError){const detail=raw.replace(/<[^>]*>/g,' ').replace(/\\s+/g,' ').trim().slice(0,220);throw new Error('The server returned invalid JSON. '+(detail||'Check PHP/server logs.'))}if(!response.ok||!data.success)throw new Error(data.message||'Request failed.');return data;}
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
   form.reset();$('lessonEditorId').value=lesson?lesson.lesson_id:'';$('lessonEditorTitle').textContent=lesson?'Edit Lesson':'Add Lesson';$('saveLessonBtn').textContent=lesson?'Save Changes':'Create Lesson';$('lessonUploadNotice').textContent='';
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
 function renderMediaOptions(){
   const term=mediaSearch.value.trim().toLowerCase();
   const matches=mediaFiles.filter(file=>(file.path+' '+file.name).toLowerCase().includes(term));
   mediaSelect.innerHTML=matches.map(file=>'<option value="'+esc(file.path)+'">'+esc(file.path)+'</option>').join('');
   $('mediaBrowserStatus').textContent=matches.length+' matching file(s).';
   if(matches.length)mediaSelect.selectedIndex=0;
 }
 async function openMediaBrowser(target,type){
   mediaTarget=target;mediaFiles=[];mediaSearch.value='';mediaSelect.innerHTML='<option>Loading project files…</option>';
   $('mediaBrowserStatus').textContent='Scanning existing assets…';
   if(window.bootstrap&&bootstrap.Modal){mediaBrowser=bootstrap.Modal.getOrCreateInstance(mediaModalElement);mediaBrowser.show();}
   else {message('Bootstrap modal is unavailable.','danger');return;}
   try{const result=await request(endpoint+'?action=list_files&media_type='+encodeURIComponent(type));mediaFiles=result.files||[];renderMediaOptions();if(!mediaFiles.length)$('mediaBrowserStatus').textContent='No matching files were found under assets/. Add the file to the project first.';}
   catch(error){mediaSelect.innerHTML='';$('mediaBrowserStatus').textContent=error.message;}
 }
 document.querySelectorAll('.browse-media').forEach(button=>button.addEventListener('click',()=>openMediaBrowser(button.dataset.target,button.dataset.mediaType)));
 mediaSearch.addEventListener('input',renderMediaOptions);
 $('useMediaPathBtn').addEventListener('click',()=>{
   const path=mediaSelect.value;
   if(!path||!mediaTarget){$('mediaBrowserStatus').textContent='Choose a file first.';return;}
   $(mediaTarget).value=path;
   $('lessonUploadNotice').className='small mt-3 text-success';
   $('lessonUploadNotice').textContent='Selected existing asset: '+path+'. Save the lesson to apply the path.';
   if(mediaBrowser)mediaBrowser.hide();
 });

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
