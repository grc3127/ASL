<div class="admin-topbar">
    <div>
        <h1>Teacher Accounts</h1>
        <p>Administrator workspace · UI prepared for database integration</p>
    </div>
</div>

<div class="admin-card">
    <div class="admin-toolbar">
        <input class="admin-input" type="search" placeholder="Search teacher name or email" style="flex:1;min-width:220px">
        <select class="admin-select"><option>All Statuses</option><option>Active</option><option>Inactive</option><option>Pending</option></select>
        <button class="admin-btn admin-btn-primary"><i class="bi bi-plus-lg"></i> Add Teacher</button>
    </div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Teacher</th><th>Email</th><th>Assigned Classes</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            <tr><td><strong>Ms. Santos</strong></td><td>santos@example.com</td><td>Kindergarten A</td><td><span class="badge-status badge-active">Active</span></td><td><button class="admin-btn admin-btn-light">Manage</button></td></tr>
            <tr><td><strong>Ms. Reyes</strong></td><td>reyes@example.com</td><td>Kindergarten B, C</td><td><span class="badge-status badge-active">Active</span></td><td><button class="admin-btn admin-btn-light">Manage</button></td></tr>
            <tr><td><strong>Mr. Cruz</strong></td><td>cruz@example.com</td><td>—</td><td><span class="badge-status badge-pending">Pending</span></td><td><button class="admin-btn admin-btn-warning">Review</button></td></tr>
        </tbody>
    </table></div>
</div>

