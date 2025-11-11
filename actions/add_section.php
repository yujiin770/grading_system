<?php
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['section_name'])) {
    $subject_id = $_POST['subject_id'];
    $section_name = trim($_POST['section_name']);

    if (!empty($section_name)) {
        $stmt = $pdo->prepare("INSERT INTO sections (subject_id, section_name) VALUES (?, ?)");
        $stmt->execute([$subject_id, $section_name]);
    }
}

header('Location: ../index.php');
exit();