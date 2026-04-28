<?php
/**
 * Students List
 * 
 * Displays all students in a searchable table.
 */

$pageTitle = 'Students';
require_once 'includes/header.php';
requireAuth();
require_once 'config/database.php';

$flash = getFlashMessage();
$search = trim($_GET['search'] ?? '');

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE 
        student_id LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR course LIKE ?
        ORDER BY created_at DESC");
    $term = "%{$search}%";
    $stmt->execute([$term, $term, $term, $term, $term]);
} else {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY created_at DESC");
}

$students = $stmt->fetchAll();
?>

<div class="page-wrapper">
    <div class="container">

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill' ?> me-2"></i>
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="page-title">Students</h1>
                <p class="page-subtitle"><?= count($students) ?> student<?= count($students) !== 1 ? 's' : '' ?> found</p>
            </div>
            <a href="add_student.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Student</a>
        </div>

        <div class="card">
            <div class="card-header">
                <form method="GET" action="" class="d-flex gap-2">
                    <div class="search-box flex-grow-1">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control" name="search" placeholder="Search by name, ID, email, or course..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="students.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i> Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="card-body p-0">
                <?php if (count($students) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Year</th>
                                    <th>Status</th>
                                    <th>Email</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $s): ?>
                                    <tr>
                                        <td><span class="fw-semibold text-primary"><?= htmlspecialchars($s['student_id']) ?></span></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                                        <td><?= htmlspecialchars($s['course']) ?></td>
                                        <td><?= $s['year_level'] ?></td>
                                        <td>
                                            <?php $sc = match($s['status']) { 'Active'=>'badge-active','Inactive'=>'badge-inactive','Graduated'=>'badge-graduated',default=>'badge-active' }; ?>
                                            <span class="badge-status <?= $sc ?>"><?= $s['status'] ?></span>
                                        </td>
                                        <td class="text-muted"><?= htmlspecialchars($s['email'] ?? '—') ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="view_student.php?id=<?= $s['id'] ?>" class="btn btn-icon btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                                <a href="edit_student.php?id=<?= $s['id'] ?>" class="btn btn-icon btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                                <button type="button" class="btn btn-icon btn-outline-danger" title="Delete"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-id="<?= $s['id'] ?>" data-name="<?= htmlspecialchars($s['first_name'].' '.$s['last_name']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <h5>No students found</h5>
                        <p><?= !empty($search) ? 'Try a different search keyword.' : 'Start by adding your first student.' ?></p>
                        <?php if (empty($search)): ?>
                            <a href="add_student.php" class="btn btn-primary btn-sm mt-2"><i class="bi bi-plus-lg"></i> Add Student</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteStudentName"></strong>?</p>
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="delete_student.php">
                    <input type="hidden" name="id" id="deleteStudentId">
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('deleteModal')?.addEventListener('show.bs.modal', function(e) {
    document.getElementById('deleteStudentId').value = e.relatedTarget.getAttribute('data-id');
    document.getElementById('deleteStudentName').textContent = e.relatedTarget.getAttribute('data-name');
});
</script>

<?php require_once 'includes/footer.php'; ?>
