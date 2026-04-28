<?php
/**
 * Add Student
 * 
 * Form to create a new student record.
 */

$pageTitle = 'Add Student';
require_once 'includes/header.php';
requireAuth();
require_once 'config/database.php';

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input
    $old = [
        'student_id'  => trim($_POST['student_id'] ?? ''),
        'first_name'  => trim($_POST['first_name'] ?? ''),
        'last_name'   => trim($_POST['last_name'] ?? ''),
        'email'       => trim($_POST['email'] ?? ''),
        'phone'       => trim($_POST['phone'] ?? ''),
        'gender'      => $_POST['gender'] ?? '',
        'birthdate'   => $_POST['birthdate'] ?? '',
        'course'      => trim($_POST['course'] ?? ''),
        'year_level'  => $_POST['year_level'] ?? '',
        'address'     => trim($_POST['address'] ?? ''),
        'status'      => $_POST['status'] ?? 'Active',
    ];

    // Validation
    if (empty($old['student_id']))  $errors[] = 'Student ID is required.';
    if (empty($old['first_name']))  $errors[] = 'First name is required.';
    if (empty($old['last_name']))   $errors[] = 'Last name is required.';
    if (empty($old['gender']))      $errors[] = 'Gender is required.';
    if (empty($old['course']))      $errors[] = 'Course is required.';
    if (empty($old['year_level']))  $errors[] = 'Year level is required.';

    if (!empty($old['email']) && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    // Check unique student_id
    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
        $check->execute([$old['student_id']]);
        if ($check->fetch()) {
            $errors[] = 'This Student ID already exists.';
        }
    }

    // Check unique email
    if (empty($errors) && !empty($old['email'])) {
        $check = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $check->execute([$old['email']]);
        if ($check->fetch()) {
            $errors[] = 'This email address is already in use.';
        }
    }

    // Insert
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO students 
            (student_id, first_name, last_name, email, phone, gender, birthdate, course, year_level, address, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $old['student_id'],
            $old['first_name'],
            $old['last_name'],
            $old['email'] ?: null,
            $old['phone'] ?: null,
            $old['gender'],
            $old['birthdate'] ?: null,
            $old['course'],
            (int) $old['year_level'],
            $old['address'] ?: null,
            $old['status'],
        ]);

        setFlashMessage('success', 'Student "' . $old['first_name'] . ' ' . $old['last_name'] . '" added successfully!');
        header('Location: students.php');
        exit;
    }
}
?>

<div class="page-wrapper">
    <div class="container">

        <!-- Page Header -->
        <div class="page-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="students.php">Students</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
            <h1 class="page-title">Add New Student</h1>
            <p class="page-subtitle">Fill in the details below to register a new student.</p>
        </div>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Student Form -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-plus-fill me-2"></i>Student Information
            </div>
            <div class="card-body">
                <form method="POST" action="" id="studentForm" novalidate>

                    <div class="row g-3">
                        <!-- Student ID -->
                        <div class="col-md-4">
                            <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="student_id" name="student_id"
                                   placeholder="e.g. STU-2024-006"
                                   value="<?= htmlspecialchars($old['student_id'] ?? '') ?>" required>
                        </div>

                        <!-- First Name -->
                        <div class="col-md-4">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   placeholder="Enter first name"
                                   value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-4">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   placeholder="Enter last name"
                                   value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="student@email.com"
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                   placeholder="09XX XXX XXXX"
                                   value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                        </div>

                        <!-- Gender -->
                        <div class="col-md-4">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select gender</option>
                                <option value="Male" <?= ($old['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($old['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($old['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>

                        <!-- Birthdate -->
                        <div class="col-md-4">
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate"
                                   value="<?= htmlspecialchars($old['birthdate'] ?? '') ?>">
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active" <?= ($old['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= ($old['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="Graduated" <?= ($old['status'] ?? '') === 'Graduated' ? 'selected' : '' ?>>Graduated</option>
                            </select>
                        </div>

                        <!-- Course -->
                        <div class="col-md-8">
                            <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="course" name="course"
                                   placeholder="e.g. BS Computer Science"
                                   value="<?= htmlspecialchars($old['course'] ?? '') ?>" required>
                        </div>

                        <!-- Year Level -->
                        <div class="col-md-4">
                            <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="year_level" name="year_level" required>
                                <option value="">Select year</option>
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <option value="<?= $i ?>" <?= ($old['year_level'] ?? '') == $i ? 'selected' : '' ?>>
                                        Year <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"
                                      placeholder="Enter complete address"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="students.php" class="btn btn-light">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Save Student
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
