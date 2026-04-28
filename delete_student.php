<?php
/**
 * Delete Student Handler
 * 
 * Handles POST-only delete requests.
 */

require_once 'includes/auth.php';
requireAuth();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: students.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    setFlashMessage('danger', 'Invalid student ID.');
    header('Location: students.php');
    exit;
}

// Fetch student name for the message
$stmt = $pdo->prepare("SELECT first_name, last_name FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    setFlashMessage('danger', 'Student not found.');
    header('Location: students.php');
    exit;
}

// Delete the student
$deleteStmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
$deleteStmt->execute([$id]);

setFlashMessage('success', 'Student "' . $student['first_name'] . ' ' . $student['last_name'] . '" has been deleted.');
header('Location: students.php');
exit;
