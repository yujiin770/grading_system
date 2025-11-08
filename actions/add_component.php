<?php
// Start the session to be able to store messages for the user
session_start();

require_once '../config/db_config.php';

// Default subject_id for safe redirection in case of early failure
$subject_id = $_POST['subject_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check that all required fields are present
    if (!empty($subject_id) && !empty($_POST['term']) && !empty($_POST['component_name']) && isset($_POST['weight']) && !empty($_POST['max_score'])) {
        
        $term = $_POST['term'];
        $component_name = trim($_POST['component_name']);
        $new_weight = (float)$_POST['weight'];
        $max_score = (int)$_POST['max_score'];

        // --- WEIGHT VALIDATION LOGIC ---

        // 1. Get the sum of all existing weights for the selected subject and term
        $sql_check = "SELECT SUM(weight) as total_weight FROM grading_components WHERE subject_id = ? AND term = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$subject_id, $term]);
        $current_weight = (float)$stmt_check->fetchColumn();

        // 2. Check if adding the new weight would exceed 100
        if (($current_weight + $new_weight) > 100) {
            // If it exceeds 100, create a detailed error message and stop.
            $new_total = $current_weight + $new_weight;
            $_SESSION['error_message'] = "Cannot add component. The current weight for the '{$term}' term is {$current_weight}%. Adding {$new_weight}% would bring the total to {$new_total}%, which is over 100%.";
        
        } else {
            // --- VALIDATION PASSED ---
            // 3. Proceed with inserting the new component into the database
            try {
                $sql = "INSERT INTO grading_components (subject_id, term, component_name, weight, max_score) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$subject_id, $term, $component_name, $new_weight, $max_score]);

                // Set a success message to confirm the action
                $_SESSION['success_message'] = "Component '{$component_name}' added successfully to the {$term} term.";
                
            } catch(PDOException $e) {
                // Catch any other potential database errors
                $_SESSION['error_message'] = "Database Error: Could not add the component.";
            }
        }
    } else {
        // If any of the form fields were missing
        $_SESSION['error_message'] = "All fields are required to add a component.";
    }
} else {
    // If the page was accessed without posting a form
    $_SESSION['error_message'] = "Invalid request.";
}

// Redirect back to the subject page so the user can see the result
header('Location: ../view_subject.php?id=' . $subject_id);
exit();