<?php
require_once '../config/db_config.php';

// Check if an ID was passed in the URL
if (isset($_GET['id'])) {
    $subject_id = $_GET['id'];

    // Note: Deleting a subject will also delete all associated sections, components,
    // enrollments, and grades because of the "ON DELETE CASCADE" rules we set up
    // in the database. This is powerful but use with caution.
    
    try {
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE subject_id = ?");
        $stmt->execute([$subject_id]);
    } catch (PDOException $e) {
        // Handle potential errors, e.g., by setting a session error message
        die("Error: Could not delete the subject. " . $e->getMessage());
    }
}

// Redirect back to the main subjects list
header('Location: ../subjects.php');
exit();