<div class="admin-topbar">
    <div>
        <h1>Student Accounts</h1>
        <p>Administrator workspace · UI prepared for database integration</p>
    </div>
</div>

<div class="admin-card">
    <div class="admin-toolbar">
        <input class="admin-input" type="search" placeholder="Search student name or email" style="flex:1;min-width:220px">
        <select class="admin-select"><option>All Statuses</option><option>Active</option><option>Inactive</option></select>
        <button class="admin-btn admin-btn-primary" onclick="alert('Connect this button to your student INSERT handler later.')"><i class="bi bi-plus-lg"></i> Add Student</button>
    </div>
    <div class="admin-note"><i class="bi bi-info-circle me-1"></i> This page is intentionally database-free. Replace the sample rows with a prepared SQL query when your account schema is finalized.</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Student</th><th>Email / Username</th><th>Class</th><th>Status</th><th>Last Activity</th><th>Actions</th></tr></thead>
        <tbody>
            <tr><td><strong>Maria Santos</strong></td><td>maria@example.com</td><td>Kindergarten A</td><td><span class="badge-status badge-active">Active</span></td><td>Today</td><td><button class="admin-btn admin-btn-light">View</button></td></tr>
            <tr><td><strong>Juan Dela Cruz</strong></td><td>juan@example.com</td><td>Kindergarten A</td><td><span class="badge-status badge-active">Active</span></td><td>Yesterday</td><td><button class="admin-btn admin-btn-light">View</button></td></tr>
            <tr><td><strong>Anne Garcia</strong></td><td>anne@example.com</td><td>Kindergarten B</td><td><span class="badge-status badge-inactive">Inactive</span></td><td>Sep 29</td><td><button class="admin-btn admin-btn-light">View</button></td></tr>
        </tbody>
    </table></div>
</div>

