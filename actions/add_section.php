<?php
require_once '../config/db_config.php';
session_start();

// Default subject_id for safe redirection if something fails early
$subject_id = $_POST['subject_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check for all the new required fields from the form
    if (!empty($subject_id) && !empty($_POST['section_name']) && !empty($_POST['start_year']) && !empty($_POST['semester'])) {
        
        $section_name = trim($_POST['section_name']);
        $semester = trim($_POST['semester']);

        // --- NEW: Construct the school_year string from the start year ---
        $start_year = (int)$_POST['start_year'];
        $end_year = $start_year + 1;
        $school_year = $start_year . '-' . $end_year;
        
        try {
            // Check if a section with the same name already exists for this subject, sy, and sem
            $check_sql = "SELECT section_id FROM sections WHERE subject_id = ? AND section_name = ? AND school_year = ? AND semester = ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$subject_id, $section_name, $school_year, $semester]);

            if ($check_stmt->rowCount() > 0) {
                $_SESSION['error_message'] = "A section with the name '{$section_name}' already exists for {$school_year} - {$semester}.";
            } else {
                // If it doesn't exist, proceed with insertion
                $stmt = $pdo->prepare("INSERT INTO sections (subject_id, section_name, school_year, semester) VALUES (?, ?, ?, ?)");
                $stmt->execute([$subject_id, $section_name, $school_year, $semester]);
                $_SESSION['success_message'] = "Section '{$section_name}' created successfully for {$school_year} - {$semester}!";
            }
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Database error: Could not create the section.";
        }
    } else {
        $_SESSION['error_message'] = "All fields are required to create a section.";
    }
} else {
    $_SESSION['error_message'] = "Invalid request method.";
}

// Redirect back to the subject page
header('Location: ../view_subject.php?id=' . $subject_id);
exit();