<?php
session_start();
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subject_id'])) {
    $subject_id = $_POST['subject_id'];
    
    // Get the weights from the form, casting them to numbers
    $prelim = (float)($_POST['prelim_weight'] ?? 0);
    $midterm = (float)($_POST['midterm_weight'] ?? 0);
    $prefinals = (float)($_POST['prefinals_weight'] ?? 0);
    $finals = (float)($_POST['finals_weight'] ?? 0);

    // --- CRITICAL VALIDATION: Ensure the total is exactly 100% ---
    $total_weight = $prelim + $midterm + $prefinals + $finals;

    if ($total_weight != 100) {
        $_SESSION['error_message'] = "Validation failed! The sum of all term weights must be exactly 100%. Your total was {$total_weight}%.";
    } else {
        // Validation passed, update the database
        try {
            $sql = "UPDATE subjects SET 
                        prelim_weight = ?, 
                        midterm_weight = ?, 
                        prefinals_weight = ?, 
                        finals_weight = ? 
                    WHERE subject_id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$prelim, $midterm, $prefinals, $finals, $subject_id]);

            $_SESSION['success_message'] = "Term weights have been updated successfully!";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Database error: Could not update weights.";
        }
    }
}

// Redirect back to the subject management page
header('Location: ../view_subject.php?id=' . $subject_id);
exit();