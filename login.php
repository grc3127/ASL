<?php
session_start();

if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    header('Location: auth/role_redirect.php');
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KIDSS Login</title>
<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-page">
<div class="login-card">

    <div class="left-panel">
        <div class="logo mb-4 d-flex align-items-center gap-3">
            <img src="assets/img/logo.png" width="80" alt="KIDSS Logo">
            <div>
                <h2 class="mb-0 fw-bold">
                    <span class="text-primary">K</span>
                    <span class="text-purple">I</span>
                    <span class="text-warning">D</span>
                    <span class="text-success">S</span>
                    <span class="text-info">S</span>
                </h2>
                <small class="text-muted lh-sm d-block">
                    Kindergarten Interactive<br>
                    Digital Signing System
                </small>
            </div>
        </div>

        <div class="hero-title">
            <h1 class="display-3 fw-bold mb-3">
                <span class="hero-title-1">Learn. Sign.</span><br>
                <span class="hero-title-2">Grow Together!</span>
            </h1>
        </div>

        <p class="hero-text">
            A fun and interactive way for kindergarten kids
            to learn American Sign Language (ASL)
            through videos, games and exciting activities.
        </p>

        <div class="text-center my-4">
            <img src="assets/img/girlboy.png" class="img-fluid hero-image" alt="Children learning">
        </div>

        <div class="security mt-4 d-flex align-items-center gap-2">
            <i class="bi bi-shield-check-fill text-success fs-3"></i>
            <div>
                <strong class="text-success">Safe • Secure • Child-Friendly</strong>
                <br>
                <small class="text-muted">We protect your learning and personal information.</small>
            </div>
        </div>
    </div>

    <div class="floating-card-wrapper">
    <div id="floating-card" class="right-panel">

        <h1 class="fw-bold">Welcome Back!</h1>
        <p class="text-secondary">Login to continue your learning journey.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="auth/login_process.php" method="POST" autocomplete="on">

            <div class="row mb-4 g-2" id="role-buttons">
                <div class="col-4">
                    <button type="button" class="btn btn-role active-role w-100 role-button" data-role="student">
                        <i class="bi bi-person-fill"></i><br>
                        <small>Student</small>
                    </button>
                </div>
                <div class="col-4">
                    <button type="button" class="btn btn-role w-100 role-button" data-role="teacher">
                        <i class="bi bi-person-fill-gear"></i><br>
                        <small>Teacher</small>
                    </button>
                </div>
                <div class="col-4">
                    <button type="button" class="btn btn-role w-100 role-button" data-role="admin">
                        <i class="bi bi-shield-lock-fill"></i><br>
                        <small>Admin</small>
                    </button>
                </div>
            </div>

            <input type="hidden" name="role" id="selectedRole" value="student">

            <h5 class="text-center mb-3">Login as <span id="roleLabel">Student</span></h5>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control"
                           placeholder="Username or Email" required autocomplete="username">
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control"
                           placeholder="Password" required autocomplete="current-password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <div>
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Remember me</label>
                </div>
                <a href="#">Forgot Password?</a>
            </div>

            <div class="text-center mb-3 text-muted small">
                By signing in to KIDSS you agree to our Terms and Privacy Policy
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-lg">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>

        </form>

        <p class="text-center mt-4 mb-0">
            Don't have an account? <a href="#">Sign up here</a>
        </p>

        <div class="text-center mb-3 mt-4 text-muted small">
            <p class="mb-0">&copy; <span id="year"></span> KIDSS - Kindergarten Interactive Digital Signing System. All rights reserved.</p>
        </div>

    </div>
    </div>
</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("year").textContent = new Date().getFullYear();

    const roleButtons = document.querySelectorAll(".role-button");
    const selectedRole = document.getElementById("selectedRole");
    const roleLabel = document.getElementById("roleLabel");

    roleButtons.forEach(button => {
        button.addEventListener("click", () => {
            roleButtons.forEach(item => item.classList.remove("active-role"));
            button.classList.add("active-role");

            selectedRole.value = button.dataset.role;
            roleLabel.textContent =
                button.dataset.role.charAt(0).toUpperCase() + button.dataset.role.slice(1);
        });
    });

    const togglePasswordBtn = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("passwordInput");
    const togglePasswordIcon = document.getElementById("togglePasswordIcon");

    togglePasswordBtn.addEventListener("click", () => {
        const isPassword = passwordInput.getAttribute("type") === "password";
        passwordInput.setAttribute("type", isPassword ? "text" : "password");
        
        togglePasswordIcon.classList.toggle("bi-eye");
        togglePasswordIcon.classList.toggle("bi-eye-slash");
    });
});
</script>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>