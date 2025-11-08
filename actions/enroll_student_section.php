<?php
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['section_id']) && !empty($_POST['student_ids'])) {
        
        // --- ADD THESE TWO DEBUG LINES ---
       
        // ------------------------------------

        $section_id = $_POST['section_id'];
        $student_ids = $_POST['student_ids']; // This is an array

        $sql = "INSERT INTO enrollments (section_id, student_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);

        // Loop through each selected student and insert them
        foreach ($student_ids as $student_id) {
            $stmt->execute([$section_id, $student_id]);
        }
    }
    // Redirect back to the section page
    header('Location: ../view_section.php?id=' . $section_id);
    exit();
}