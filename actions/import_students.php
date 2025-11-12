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
        $error_rows = 0;

        $check_student_stmt = $pdo->prepare("SELECT student_id FROM students WHERE last_name = ? AND first_name = ?");
        $insert_student_stmt = $pdo->prepare("INSERT INTO students (last_name, first_name, middle_name) VALUES (?, ?, ?)");
        $check_enrollment_stmt = $pdo->prepare("SELECT id FROM enrollments WHERE section_id = ? AND student_id = ?");
        $enroll_stmt = $pdo->prepare("INSERT INTO enrollments (section_id, student_id) VALUES (?, ?)");

        // Loop through each ROW from the Excel file
        foreach ($rows as $row) {
            $name_found = false;

            // --- NEW LOGIC: Loop through each CELL in the current row ---
            foreach ($row as $cell) {
                $full_name = trim($cell);
                
                // We check if the cell content looks like a name (contains a comma)
                if (!empty($full_name) && strpos($full_name, ',') !== false) {
                    
                    // Found a potential name, now parse it
                    $parts = explode(',', $full_name, 2);
                    if (count($parts) < 2) continue; // Malformed, skip to next cell

                    $last_name = trim($parts[0]);
                    $rest_of_name = trim($parts[1]);
                    
                    $name_parts = explode(' ', $rest_of_name, 2);
                    $first_name = trim($name_parts[0]);
                    $middle_name = isset($name_parts[1]) ? trim($name_parts[1]) : '';

                    if (empty($last_name) || empty($first_name)) {
                        continue; // Invalid parse, skip to next cell
                    }

                    // --- Database Logic (remains the same) ---
                    $check_student_stmt->execute([$last_name, $first_name]);
                    $student = $check_student_stmt->fetch();
                    $student_id = null;

                    if ($student) {
                        $student_id = $student['student_id'];
                    } else {
                        $insert_student_stmt->execute([$last_name, $first_name, $middle_name]);
                        $student_id = $pdo->lastInsertId();
                    }

                    $check_enrollment_stmt->execute([$section_id, $student_id]);
                    if ($check_enrollment_stmt->fetch()) {
                        $skipped_count++;
                    } else {
                        $enroll_stmt->execute([$section_id, $student_id]);
                        $imported_count++;
                    }
                    
                    $name_found = true;
                    break; // IMPORTANT: Stop searching this row once we've found and processed a name
                }
            }
            if (!$name_found && !empty(array_filter($row))) {
                $error_rows++; // Count rows that had data but no valid name format
            }
        }

        $message = "Import complete! <strong>{$imported_count}</strong> new students enrolled. <strong>{$skipped_count}</strong> students were already in this section.";
        if ($error_rows > 0) {
            $message .= " <strong>{$error_rows}</strong> rows were skipped due to an invalid name format (must be 'Last Name, First Name ...').";
        }
        $_SESSION['success_message'] = $message;

    } else {
        $_SESSION['error_message'] = "There was an error uploading the file.";
    }
}

header('Location: ../view_section.php?id=' . ($section_id ?? ''));
exit();