<?php
require_once '../config/db_config.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Optional: Delete the student's image file from the server
    $stmt = $pdo->prepare("SELECT image_path FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
    if ($student && $student['image_path'] != 'default.png' && file_exists("../uploads/" . $student['image_path'])) {
        unlink("../uploads/" . $student['image_path']);
    }

    // Delete the student from the database
    $delete_stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
    $delete_stmt->execute([$student_id]);
}

header('Location: ../students.php');
exit();