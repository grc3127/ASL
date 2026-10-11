<?php
require_once __DIR__ . '/../auth/guard.php';
requireRole('admin','administrator');
require_once __DIR__ . '/../config/database.php';
try {
 $logs=$pdo->query("SELECT al.log_id,al.action,al.description,al.ip_address,al.created_at,u.username,r.role_name,
 COALESCE(NULLIF(TRIM(CONCAT_WS(' ',st.first_name,st.middle_name,st.last_name)),''),NULLIF(TRIM(CONCAT_WS(' ',t.first_name,t.middle_name,t.last_name)),''),NULLIF(TRIM(CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name)),''),u.username,'System') AS full_name
 FROM activity_logs al LEFT JOIN users u ON u.user_id=al.user_id LEFT JOIN roles r ON r.role_id=u.role_id
 LEFT JOIN students st ON st.user_id=u.user_id LEFT JOIN teachers t ON t.user_id=u.user_id LEFT JOIN admins a ON a.user_id=u.user_id
 ORDER BY al.created_at DESC LIMIT 500")->fetchAll();
} catch (Throwable $e) { error_log('KIDSS activity logs: '.$e->getMessage()); $logs=[]; $loadError='Activity logs could not be loaded.'; }
$h=static fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');
?>
<div class="admin-topbar"><div><h1>Activity Logs</h1><p>Recent system actions recorded by the application (latest 500 records).</p></div></div>
<?php if(!empty($loadError)): ?><div class="alert alert-warning"><?= $h($loadError) ?></div><?php endif; ?>
<div class="admin-card"><input id="activitySearch" class="admin-input mb-3" type="search" placeholder="Search user, action, or description...">
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Date / Time</th><th>User</th><th>Role</th><th>Action</th><th>Description</th><th>IP address</th></tr></thead><tbody id="activityRows">
<?php if(!$logs): ?><tr><td colspan="6" class="text-center text-muted py-4">No activity logs have been recorded yet.</td></tr><?php else: foreach($logs as $log): ?><tr><td><?= $h(date('M j, Y g:i A',strtotime($log['created_at']))) ?></td><td><?= $h($log['full_name'] ?: 'System') ?><br><small><?= $h($log['username']??'') ?></small></td><td><?= $h($log['role_name']??'System') ?></td><td><strong><?= $h($log['action']) ?></strong></td><td><?= $h($log['description']??'—') ?></td><td><?= $h($log['ip_address']??'—') ?></td></tr><?php endforeach; endif; ?>
</tbody></table></div><p id="activityCount" class="text-muted small mt-3 mb-0"><?= count($logs) ?> log record(s) loaded.</p></div>
<script>(function(){const input=document.getElementById('activitySearch'),rows=document.querySelectorAll('#activityRows tr'),count=document.getElementById('activityCount');input.addEventListener('input',()=>{let n=0;rows.forEach(row=>{row.hidden=!row.textContent.toLowerCase().includes(input.value.toLowerCase());if(!row.hidden)n++});count.textContent=n+' log record(s) shown.'})})();</script>