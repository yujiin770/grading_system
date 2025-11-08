<?php
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // We now receive section_id instead of subject_id for the redirect
    $section_id = $_POST['section_id']; 
    $component_id = $_POST['component_id'];
    $scores = $_POST['scores']; // Expecting an array like: $_POST['scores'][student_id] = score

    foreach ($scores as $student_id => $score) {
        // Only process if a score was actually entered to avoid blank entries
        if ($score !== '' && is_numeric($score)) {
            try {
                // "UPSERT" logic: UPDATE if exists, INSERT if not.
                $sql = "INSERT INTO grades (student_id, component_id, score) VALUES (?, ?, ?)
                        ON DUPLICATE KEY UPDATE score = VALUES(score)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$student_id, $component_id, $score]);

            } catch (PDOException $e) {
                die("Database error while saving grades: " . $e->getMessage());
            }
        }
    }
}

// Redirect back to the grade entry page, preserving the section and component
header('Location: ../enter_grades.php?section_id=' . $section_id . '&component_id=' . $component_id);
exit();
?>