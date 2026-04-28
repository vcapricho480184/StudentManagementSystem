<?php
/**
 * Dashboard
 * 
 * Shows statistics and recent students.
 */

$pageTitle = 'Dashboard';
require_once 'includes/header.php';
requireAuth();
require_once 'config/database.php';

// Fetch statistics
$totalStudents    = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$activeStudents   = $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'Active'")->fetchColumn();
$inactiveStudents = $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'Inactive'")->fetchColumn();
$graduatedStudents = $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'Graduated'")->fetchColumn();

// Fetch recent students (last 5)
$recentStmt = $pdo->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5");
$recentStudents = $recentStmt->fetchAll();

// Get flash message
$flash = getFlashMessage();
?>

<div class="page-wrapper">
    <div class="container">

        <!-- Flash Message -->
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill' ?> me-2"></i>
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Welcome back, <?= htmlspecialchars(getAdminName()) ?></p>
            </div>
            <a href="add_student.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Student
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-value"><?= $totalStudents ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div class="stat-value"><?= $activeStudents ?></div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="bi bi-person-dash-fill"></i>
                    </div>
                    <div class="stat-value"><?= $inactiveStudents ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="stat-value"><?= $graduatedStudents ?></div>
                    <div class="stat-label">Graduated</div>
                </div>
            </div>
        </div>

        <!-- Recent Students -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Recent Students</span>
                <a href="students.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (count($recentStudents) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Year</th>
                                    <th>Status</th>
                                    <th>Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentStudents as $student): ?>
                                    <tr>
                                        <td>
                                            <a href="view_student.php?id=<?= $student['id'] ?>" class="fw-semibold">
                                                <?= htmlspecialchars($student['student_id']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                        <td><?= htmlspecialchars($student['course']) ?></td>
                                        <td><?= $student['year_level'] ?></td>
                                        <td>
                                            <?php
                                                $statusClass = match($student['status']) {
                                                    'Active'    => 'badge-active',
                                                    'Inactive'  => 'badge-inactive',
                                                    'Graduated' => 'badge-graduated',
                                                    default     => 'badge-active'
                                                };
                                            ?>
                                            <span class="badge-status <?= $statusClass ?>"><?= $student['status'] ?></span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($student['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>No students yet</h5>
                        <p>Start by adding your first student record.</p>
                        <a href="add_student.php" class="btn btn-primary btn-sm mt-2">
                            <i class="bi bi-plus-lg"></i> Add Student
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
