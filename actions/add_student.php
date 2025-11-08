<?php
// actions/add_student.php

require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['first_name']) && !empty($_POST['last_name'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);

        try {
            $sql = "INSERT INTO students (first_name, last_name) VALUES (:first_name, :last_name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->execute();
        } catch(PDOException $e) {
            // Handle error, maybe log it or display a user-friendly message
            die("Error adding student: " . $e->getMessage());
        }
    }
}

header('Location: ../students.php'); // Assuming you have a students.php frontend
exit();
?>