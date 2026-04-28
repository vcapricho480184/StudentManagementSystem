<?php
/**
 * Edit Student
 * 
 * Form to update an existing student record.
 */

$pageTitle = 'Edit Student';
require_once 'includes/header.php';
requireAuth();
require_once 'config/database.php';

// Get student ID
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    setFlashMessage('danger', 'Invalid student ID.');
    header('Location: students.php');
    exit;
}

// Fetch student
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    setFlashMessage('danger', 'Student not found.');
    header('Location: students.php');
    exit;
}

$errors = [];
$old = $student; // Pre-fill with existing data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Check unique student_id (excluding current)
    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM students WHERE student_id = ? AND id != ?");
        $check->execute([$old['student_id'], $id]);
        if ($check->fetch()) $errors[] = 'This Student ID already exists.';
    }

    // Check unique email (excluding current)
    if (empty($errors) && !empty($old['email'])) {
        $check = $pdo->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
        $check->execute([$old['email'], $id]);
        if ($check->fetch()) $errors[] = 'This email address is already in use.';
    }

    // Update
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE students SET 
            student_id=?, first_name=?, last_name=?, email=?, phone=?, 
            gender=?, birthdate=?, course=?, year_level=?, address=?, status=?
            WHERE id=?");

        $stmt->execute([
            $old['student_id'], $old['first_name'], $old['last_name'],
            $old['email'] ?: null, $old['phone'] ?: null,
            $old['gender'], $old['birthdate'] ?: null,
            $old['course'], (int) $old['year_level'],
            $old['address'] ?: null, $old['status'], $id
        ]);

        setFlashMessage('success', 'Student "' . $old['first_name'] . ' ' . $old['last_name'] . '" updated successfully!');
        header('Location: students.php');
        exit;
    }
}
?>

<div class="page-wrapper">
    <div class="container">

        <div class="page-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="students.php">Students</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="page-title">Edit Student</h1>
            <p class="page-subtitle">Update the student information below.</p>
        </div>

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

        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2"></i>Student Information
            </div>
            <div class="card-body">
                <form method="POST" action="edit_student.php?id=<?= $id ?>" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="student_id" name="student_id" value="<?= htmlspecialchars($old['student_id']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($old['first_name']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($old['last_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select gender</option>
                                <option value="Male" <?= $old['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= $old['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= $old['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?= htmlspecialchars($old['birthdate'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active" <?= $old['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= $old['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="Graduated" <?= $old['status'] === 'Graduated' ? 'selected' : '' ?>>Graduated</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="course" name="course" value="<?= htmlspecialchars($old['course']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="year_level" name="year_level" required>
                                <option value="">Select year</option>
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <option value="<?= $i ?>" <?= (int)$old['year_level'] === $i ? 'selected' : '' ?>>Year <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="students.php" class="btn btn-light"><i class="bi bi-x-lg"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
