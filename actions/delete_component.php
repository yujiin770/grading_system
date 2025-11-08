<?php
require_once '../config/db_config.php';

if (isset($_GET['id']) && isset($_GET['subject_id'])) {
    $component_id = $_GET['id'];
    $subject_id = $_GET['subject_id']; // For redirecting back

    $stmt = $pdo->prepare("DELETE FROM grading_components WHERE component_id = ?");
    $stmt->execute([$component_id]);
}

// Redirect back to the subject view page
header('Location: ../view_subject.php?id=' . $subject_id);
exit();