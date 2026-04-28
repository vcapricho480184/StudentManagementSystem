<?php
/**
 * View Student
 * 
 * Displays full student details in a read-only card layout.
 */

$pageTitle = 'View Student';
require_once 'includes/header.php';
requireAuth();
require_once 'config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    setFlashMessage('danger', 'Invalid student ID.');
    header('Location: students.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    setFlashMessage('danger', 'Student not found.');
    header('Location: students.php');
    exit;
}

$statusClass = match($student['status']) {
    'Active'    => 'badge-active',
    'Inactive'  => 'badge-inactive',
    'Graduated' => 'badge-graduated',
    default     => 'badge-active'
};
?>

<div class="page-wrapper">
    <div class="container">

        <div class="page-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="students.php">Students</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="page-title"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h1>
                    <p class="page-subtitle"><?= htmlspecialchars($student['student_id']) ?></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="students.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Personal Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-person-fill me-2"></i>Personal Information
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="detail-group">
                                    <div class="detail-label">First Name</div>
                                    <div class="detail-value"><?= htmlspecialchars($student['first_name']) ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-group">
                                    <div class="detail-label">Last Name</div>
                                    <div class="detail-value"><?= htmlspecialchars($student['last_name']) ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-group">
                                    <div class="detail-label">Gender</div>
                                    <div class="detail-value"><?= htmlspecialchars($student['gender']) ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-group">
                                    <div class="detail-label">Birthdate</div>
                                    <div class="detail-value">
                                        <?= $student['birthdate'] ? date('F d, Y', strtotime($student['birthdate'])) : '—' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-group">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value"><?= htmlspecialchars($student['email'] ?? '—') ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-group">
                                    <div class="detail-label">Phone</div>
                                    <div class="detail-value"><?= htmlspecialchars($student['phone'] ?? '—') ?></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-group mb-0">
                                    <div class="detail-label">Address</div>
                                    <div class="detail-value"><?= htmlspecialchars($student['address'] ?? '—') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-mortarboard-fill me-2"></i>Academic Info
                    </div>
                    <div class="card-body">
                        <div class="detail-group">
                            <div class="detail-label">Student ID</div>
                            <div class="detail-value fw-bold text-primary"><?= htmlspecialchars($student['student_id']) ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Course</div>
                            <div class="detail-value"><?= htmlspecialchars($student['course']) ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Year Level</div>
                            <div class="detail-value">Year <?= $student['year_level'] ?></div>
                        </div>
                        <div class="detail-group mb-0">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="badge-status <?= $statusClass ?>"><?= $student['status'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clock me-2"></i>Record Info
                    </div>
                    <div class="card-body">
                        <div class="detail-group">
                            <div class="detail-label">Date Added</div>
                            <div class="detail-value"><?= date('F d, Y g:i A', strtotime($student['created_at'])) ?></div>
                        </div>
                        <div class="detail-group mb-0">
                            <div class="detail-label">Last Updated</div>
                            <div class="detail-value"><?= date('F d, Y g:i A', strtotime($student['updated_at'])) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
