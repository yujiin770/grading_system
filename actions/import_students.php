<?php
session_start();
require_once '../config/db_config.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['student_file']) && isset($_POST['section_id'])) {
    $section_id = $_POST['section_id'];

    if ($_FILES['student_file']['error'] == UPLOAD_ERR_OK) {
        $file_name = $_FILES['student_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['student_file']['name'], PATHINFO_EXTENSION));

        if ($file_ext == 'xlsx') {
            $reader = new Xlsx();
        } elseif ($file_ext == 'xls') {
            $reader = new Xls();
        } else {
            $_SESSION['error_message'] = "Invalid file type. Please upload an .xlsx or .xls file.";
            header('Location: ../view_section.php?id=' . $section_id);
            exit();
        }

        $spreadsheet = $reader->load($file_name);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        $imported_count = 0;
        $skipped_count = 0;

        $check_student_stmt = $pdo->prepare("SELECT student_id FROM students WHERE last_name = ? AND first_name = ?");
        $insert_student_stmt = $pdo->prepare("INSERT INTO students (last_name, first_name, middle_name) VALUES (?, ?, ?)");
        $check_enrollment_stmt = $pdo->prepare("SELECT id FROM enrollments WHERE section_id = ? AND student_id = ?");
        $enroll_stmt = $pdo->prepare("INSERT INTO enrollments (section_id, student_id) VALUES (?, ?)");

        foreach ($rows as $row) {
            // --- NEW SINGLE-CELL PARSING LOGIC ---
            $full_name = isset($row[0]) ? trim($row[0]) : '';
            if (empty($full_name)) {
                continue; // Skip empty rows
            }

            // Split by the first comma to separate Last Name from the rest
            $parts = explode(',', $full_name, 2);
            if (count($parts) < 2) {
                continue; // Skip rows that don't have a comma
            }

            $last_name = trim($parts[0]);
            $rest_of_name = trim($parts[1]);

            // Split the rest by the first space to separate First Name from Middle Name
            $name_parts = explode(' ', $rest_of_name, 2);
            $first_name = trim($name_parts[0]);
            $middle_name = isset($name_parts[1]) ? trim($name_parts[1]) : '';
            // --- END OF NEW LOGIC ---

            if (empty($last_name) || empty($first_name)) {
                continue;
            }

            // Check if student exists
            $check_student_stmt->execute([$last_name, $first_name]);
            $student = $check_student_stmt->fetch();
            $student_id = null;

            if ($student) {
                $student_id = $student['student_id'];
            } else {
                $insert_student_stmt->execute([$last_name, $first_name, $middle_name]);
                $student_id = $pdo->lastInsertId();
            }

            // Check if enrolled
            $check_enrollment_stmt->execute([$section_id, $student_id]);
            if ($check_enrollment_stmt->fetch()) {
                $skipped_count++;
            } else {
                $enroll_stmt->execute([$section_id, $student_id]);
                $imported_count++;
            }
        }

        $_SESSION['success_message'] = "Import complete! {$imported_count} new students enrolled. {$skipped_count} students were already enrolled.";

    } else {
        $_SESSION['error_message'] = "There was an error uploading the file.";
    }
}

header('Location: ../view_section.php?id=' . ($section_id ?? ''));
exit();