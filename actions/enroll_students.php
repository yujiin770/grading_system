<?php
// actions/enroll_students.php

require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic validation
    if (isset($_POST['subject_id']) && !empty($_POST['student_ids'])) {
        $subject_id = $_POST['subject_id'];
        $student_ids = $_POST['student_ids']; // This will be an array from the form

        // Prepare the statement for inserting
        $sql = "INSERT INTO student_subjects (subject_id, student_id) VALUES (:subject_id, :student_id)";
        $stmt = $pdo->prepare($sql);

        // Prepare a statement to check for existing enrollment to avoid duplicates
        $check_sql = "SELECT id FROM student_subjects WHERE subject_id = :subject_id AND student_id = :student_id";
        $check_stmt = $pdo->prepare($check_sql);

        foreach ($student_ids as $student_id) {
            // Check if this student is already enrolled
            $check_stmt->execute(['subject_id' => $subject_id, 'student_id' => $student_id]);
            
            if ($check_stmt->rowCount() == 0) {
                // Not enrolled, so insert the new record
                $stmt->execute(['subject_id' => $subject_id, 'student_id' => $student_id]);
            }
        }
    }
}

// Redirect back to the subject detail page
header('Location: ../view_subject.php?id=' . $subject_id);
exit();
?>