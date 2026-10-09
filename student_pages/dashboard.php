<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$studentDisplayName = htmlspecialchars((string)($_SESSION['name'] ?? 'Student'), ENT_QUOTES, 'UTF-8');
?>

<!-- Row 1: Header / Welcome & User Bar -->
<div class="row align-items-center mb-4 g-3">
    <div class="col-12 col-md-6">
        <h2 class="fw-extrabold mb-0 fs-3">Hi, <?= $studentDisplayName ?>! 👋</h2>
        <p class="text-muted fw-semibold mb-0">Ready to learn and have fun today?</p>
    </div>
    <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center gap-3">
        <!-- Notification Icon -->
        <!-- <div class="header-icon-pill">
            <i class="bi bi-bell-fill text-warning fs-5"></i>
            <span class="badge-count">3</span>
        </div> -->
        <!-- Stars Icon -->
        <!-- <div class="header-icon-pill">
            <i class="bi bi-star-fill text-warning fs-5"></i>
            <span class="badge-count" style="background: #f59e0b;">12</span>
        </div> -->
        <!-- Profile Card -->
        <div class="profile-pill d-flex align-items-center">
            <img src="https://api.dicebear.com/7.x/bottts/svg?seed=<?= rawurlencode((string)($_SESSION['name'] ?? 'Student')) ?>" alt="Student avatar" class="user-avatar">
            <div class="lh-1 me-2">
                <div class="fw-bold fs-6"><?= $studentDisplayName ?></div>
                <small class="text-muted" style="font-size: 0.75rem;"></small>
            </div>
            <i ></i>
        </div>
    </div>
</div>

<!-- Row 2: Hero Banner (Continue Learning & Today's Goal) -->
<div class="hero-banner mb-4"style="background-image:url('assets/img/bg2.png');background-repeat: no-repeat;background-size: 100% 100%;">
    <div class="row align-items-center g-4"  >
        <div class="col-12 col-lg-7">
            <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold mb-2">Continue Learning</span>
            <h1 class="display-5 fw-extrabold text-dark mb-2">Letter A</h1>
            <p class="text-secondary fw-semibold mb-4 fs-6" style="max-width: 400px;">
                You're doing great! Let's continue where you left off.
            </p>
            <button type="button" class="btn btn-resume d-inline-flex align-items-center gap-2" onclick="loadPage('lessons')">
                <i class="bi bi-play-fill fs-5"></i> Resume Lesson
            </button>
        </div>
        <div class="col-12 col-lg-5">
            <div class="goal-card shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-bullseye text-primary fs-5"></i>
                    <h6 class="fw-bold mb-0">Today's Goal</h6>
                </div>
                <div class="mb-2">
                    <span class="fs-4 fw-bolder">2 / 3</span> 
                    <span class="text-muted fw-bold">Lessons</span>
                </div>
                <div class="progress mb-3" style="height: 10px; border-radius: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 66%; border-radius: 10px;"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted fw-bold">Keep it up! You're almost there!</small>
                    <i class="bi bi-star-fill text-warning fs-5"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Learning Categories -->
<!-- <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Learning Categories</h5>
    <a href="#" class="btn btn-view-all" data-page="lessons" onclick="event.preventDefault(); loadPage('lessons')">View All</a>
</div> -->



<!-- Row 4: Bottom 3 Cards (Progress, Recent Lessons, Achievements) -->
<div class="row g-4">
    <!-- My Progress Card -->
    <div class="col-12 col-lg-4">
        <div class="card-custom h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">My Progress</h6>
                    <a href="#" class="btn btn-view-all" onclick="event.preventDefault(); loadPage('progress')">View Details</a>
                </div>
                <div class="row align-items-center mb-3">
                    <div class="col-5">
                        <div class="donut-chart">
                            <div class="donut-chart-center">
                                <span class="fw-extrabold fs-4 lh-1">72%</span>
                                <small class="text-muted text-center" style="font-size: 0.6rem;">Overall Progress</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="mb-2">
                            <small class="text-muted fw-bold d-block" style="font-size: 0.72rem;">Lessons Completed</small>
                            <span class="fw-extrabold fs-5 text-dark">18 <span class="text-muted fs-6">/ 25</span></span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted fw-bold d-block" style="font-size: 0.72rem;">Quizzes Taken</small>
                            <span class="fw-extrabold fs-5 text-primary">12</span>
                        </div>
                        <div>
                            <small class="text-muted fw-bold d-block" style="font-size: 0.72rem;">Games Played</small>
                            <span class="fw-extrabold fs-5 text-primary">15</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-2 rounded-3 text-center" style="background-color: #f0f9ff;">
                <small class="fw-bold text-primary" style="font-size: 0.78rem;">
                    <i class="bi bi-star-fill text-warning me-1"></i> Great job! You're on the right track! 🎉
                </small>
            </div>
        </div>
    </div>

    <!-- Recent Lessons Card -->
    <div class="col-12 col-lg-4">
        <div class="card-custom h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Recent Lessons</h6>
                    <a href="#" class="btn btn-view-all" onclick="event.preventDefault(); loadPage('lessons')">View All</a>
                </div>
                <!-- Lesson 1 -->
                <div class="lesson-row">
                    <div class="lesson-icon bg-success">A</div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0 fs-6">Letter A</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Learn the sign for A</small>
                    </div>
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                </div>
                <!-- Lesson 2 -->
                <div class="lesson-row">
                    <div class="lesson-icon" style="background-color: #f43f5e;">B</div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0 fs-6">Letter B</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Learn the sign for B</small>
                    </div>
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                </div>
                <!-- Lesson 3 -->
                <div class="lesson-row">
                    <div class="lesson-icon" style="background-color: #f59e0b;">1</div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0 fs-6">Number 1</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Learn the sign for 1</small>
                    </div>
                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill fw-bold" style="font-size: 0.7rem;">60%</span>
                </div>
            </div>
            <button class="btn btn-light w-100 fw-bold text-primary py-2 rounded-pill mt-2" style="background-color: #f1f5f9;">
                Continue Learning
            </button>
        </div>
    </div>

    <!-- Achievements Card -->
    <div class="col-12 col-lg-4">
        <div class="card-custom h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Achievements</h6>
                    <a href="#" class="btn btn-view-all" onclick="event.preventDefault(); loadPage('achievements')">View All</a>
                </div>
                <!-- Award Banner -->
                <div class="text-center my-2">
                    <i class="bi bi-shield-fill-check text-warning display-5"></i>
                    <h6 class="fw-bold mb-1 mt-1">Beginner Signer</h6>
                    <p class="text-muted fw-semibold mb-0" style="font-size: 0.75rem;">
                        You've earned this badge for completing 5 lessons!
                    </p>
                </div>
                <!-- Mini Badge Row -->
                <div class="d-flex justify-content-center gap-2 my-3">
                    <div class="badge-circle bg-info-subtle text-info"><i class="bi bi-hand-index-thumb-fill"></i></div>
                    <div class="badge-circle bg-primary-subtle text-primary fw-bold fs-6">10</div>
                    <div class="badge-circle bg-warning-subtle text-warning"><i class="bi bi-trophy-fill"></i></div>
                    <div class="badge-circle bg-secondary-subtle text-secondary"><i class="bi bi-lock-fill"></i></div>
                </div>
            </div>
            <!-- Progress Bar -->
            <div>
                <small class="fw-bold text-dark d-block mb-1" style="font-size: 0.75rem;">
                    <i class="bi bi-star-fill text-warning me-1"></i> 12 / 20 Badges Earned
                </small>
                <div class="progress" style="height: 6px; border-radius: 10px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 60%; border-radius: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
