<?php
// actions/add_subject.php

// Include database connection
require_once '../config/db_config.php';

$response = ['status' => 'error', 'message' => 'An error occurred.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    if (!empty($_POST['subject_name'])) {
        $subject_name = trim($_POST['subject_name']);
        $class_code = isset($_POST['class_code']) ? trim($_POST['class_code']) : null;

        try {
            $sql = "INSERT INTO subjects (subject_name, class_code) VALUES (:subject_name, :class_code)";
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':subject_name', $subject_name, PDO::PARAM_STR);
            $stmt->bindParam(':class_code', $class_code, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Subject added successfully!';
            } else {
                $response['message'] = 'Failed to add subject.';
            }
        } catch(PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Subject name is required.';
    }
}

// Redirect back to the subjects page or send a JSON response for AJAX calls
header('Location: ../subjects.php'); // Assuming you have a subjects.php frontend
exit();
?>