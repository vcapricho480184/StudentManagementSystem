<?php
/**
 * Shared Header
 * 
 * Includes HTML head, Bootstrap/icons CDN, custom CSS, and the navbar.
 * Set $pageTitle before including this file.
 */

require_once __DIR__ . '/auth.php';

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Student Management System — Manage student records efficiently.">
    <title><?= htmlspecialchars($pageTitle ?? 'Student Management System') ?> — ISCP</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php if (isLoggedIn()): ?>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-mortarboard-fill"></i>
            ISCP Student MS
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'students.php' ? 'active' : '' ?>" href="students.php">
                        <i class="bi bi-people-fill"></i> Students
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'add_student.php' ? 'active' : '' ?>" href="add_student.php">
                        <i class="bi bi-person-plus-fill"></i> Add Student
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="nav-admin-badge">
                    <i class="bi bi-person-circle"></i>
                    <?= htmlspecialchars(getAdminName()) ?>
                </span>
                <a href="logout.php" class="btn btn-logout btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>
