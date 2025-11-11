<?php
require_once '../config/db_config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check for all required fields from the form
    if (isset($_POST['section_id'], $_POST['component_id'], $_POST['scores'], $_POST['current_page'], $_POST['term'])) {
        $section_id = $_POST['section_id']; 
        $component_id = $_POST['component_id'];
        $scores = $_POST['scores'];
        $current_page = $_POST['current_page'];
        $term = $_POST['term']; // The term we were working on

        try {
            $sql = "INSERT INTO grades (student_id, component_id, score) 
                    VALUES (:student_id, :component_id, :score)
                    ON DUPLICATE KEY UPDATE score = VALUES(score)";
            
            $stmt = $pdo->prepare($sql);

            foreach ($scores as $student_id => $score) {
                if ($score !== '' && is_numeric($score)) {
                    $stmt->execute([
                        'student_id' => $student_id,
                        'component_id' => $component_id,
                        'score' => $score
                    ]);
                }
            }
            $_SESSION['success_message'] = "Grades on page {$current_page} for term '{$term}' have been saved!";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        }

        // --- THE DEFINITIVE FIX ---
        // Construct the URL with all the parameters to restore the state perfectly.
        $redirect_url = "../enter_grades.php";
        $redirect_url .= "?section_id=" . urlencode($section_id);
        $redirect_url .= "&term=" . urlencode($term);
        $redirect_url .= "&component_id=" . urlencode($component_id);
        $redirect_url .= "&page=" . urlencode($current_page);
        
        header("Location: " . $redirect_url);
        exit();
    }
}

// Fallback redirect
header('Location: ../index.php');
exit();