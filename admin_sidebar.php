<aside id="admin-sidebar">
    <div class="admin-sidebar-header">
        <button class="admin-toggle" type="button" onclick="toggleAdminSidebar()" aria-label="Toggle sidebar">☰</button>
        <div class="admin-brand">
            <strong>K I D S S</strong>
            <small>Administrator</small>
        </div>
    </div>

    <nav class="admin-nav" aria-label="Administrator navigation">
        <a href="#" class="admin-link active" data-page="admin_dashboard"><i class="bi bi-grid-fill"></i><span>Dashboard</span></a>

        <div class="admin-section">USER MANAGEMENT</div>
        <a href="#" class="admin-link" data-page="admin_students"><i class="bi bi-mortarboard-fill"></i><span>Student Accounts</span></a>
        <a href="#" class="admin-link" data-page="admin_teachers"><i class="bi bi-person-workspace"></i><span>Teacher Accounts</span></a>
        <a href="#" class="admin-link" data-page="admin_classes"><i class="bi bi-people-fill"></i><span>Classes & Sections</span></a>

        <div class="admin-section">LEARNING MANAGEMENT</div>
        <a href="#" class="admin-link" data-page="admin_lessons"><i class="bi bi-book-fill"></i><span>Lessons</span></a>
        <a href="#" class="admin-link" data-page="admin_quizzes"><i class="bi bi-ui-checks-grid"></i><span>Quiz Management</span></a>
        <a href="#" class="admin-link" data-page="admin_achievements"><i class="bi bi-trophy-fill"></i><span>Achievements</span></a>

        <div class="admin-section">MONITORING</div>
        <a href="#" class="admin-link" data-page="admin_progress"><i class="bi bi-bar-chart-fill"></i><span>Student Progress</span></a>
        <a href="#" class="admin-link" data-page="admin_activity_logs"><i class="bi bi-clock-history"></i><span>Activity Logs</span></a>

        <div class="admin-section">REPORTS</div>
        <a href="#" class="admin-link" data-page="admin_reports"><i class="bi bi-file-earmark-bar-graph-fill"></i><span>Reports</span></a>

        <div class="admin-section">COMMUNICATION</div>
        <a href="#" class="admin-link" data-page="admin_feedback"><i class="bi bi-chat-square-text-fill"></i><span>Feedback</span></a>
        <a href="#" class="admin-link" data-page="admin_notifications"><i class="bi bi-bell-fill"></i><span>Notifications</span></a>

        <div class="admin-section">SETTINGS</div>
        <a href="#" class="admin-link" data-page="admin_profile"><i class="bi bi-person-circle"></i><span>Admin Profile</span></a>
        <a href="#" class="admin-link" data-page="admin_settings"><i class="bi bi-gear-fill"></i><span>System Settings</span></a>

        <div style="margin-top:20px;padding:0 5px">
            <a href="logout.php" class="admin-link" style="color:#ef5b68">
                <i class="bi bi-box-arrow-right"></i><span>Logout</span>
            </a>
        </div>
    </nav>
</aside>
