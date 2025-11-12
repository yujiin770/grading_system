<?php
// Start session if needed, though not strictly required for this script
session_start();

// Include necessary files
require_once '../config/db_config.php';
require_once '../vendor/autoload.php'; // The Composer autoloader for PhpSpreadsheet

// Use the PhpSpreadsheet classes
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// --- GPA CONVERSION FUNCTION ---
// This must be the same function as in your view_section.php
function calculateGpaEquivalent($final_grade) {
    if ($final_grade >= 99) { return ['gpa' => 1.0, 'letter' => 'A+', 'remarks' => 'Excellent']; } 
    elseif ($final_grade >= 96) { return ['gpa' => 1.25, 'letter' => 'A', 'remarks' => 'Excellent']; } 
    elseif ($final_grade >= 93) { return ['gpa' => 1.5, 'letter' => 'A-', 'remarks' => 'Excellent']; } 
    elseif ($final_grade >= 90) { return ['gpa' => 1.75, 'letter' => 'B+', 'remarks' => 'Very Good']; } 
    elseif ($final_grade >= 87) { return ['gpa' => 2.0, 'letter' => 'B', 'remarks' => 'Good']; } 
    elseif ($final_grade >= 84) { return ['gpa' => 2.25, 'letter' => 'B-', 'remarks' => 'Good']; } 
    elseif ($final_grade >= 81) { return ['gpa' => 2.5, 'letter' => 'C+', 'remarks' => 'Satisfactory']; } 
    elseif ($final_grade >= 78) { return ['gpa' => 2.75, 'letter' => 'C', 'remarks' => 'Satisfactory']; } 
    elseif ($final_grade >= 75) { return ['gpa' => 3.0, 'letter' => 'C-', 'remarks' => 'Pass']; } 
    else { return ['gpa' => 5.0, 'letter' => 'F', 'remarks' => 'Fail']; }
}

// --- DATA FETCHING (for ALL students in the section) ---
if (!isset($_GET['section_id']) || empty($_GET['section_id'])) {
    die("Error: No section ID provided.");
}
$section_id = $_GET['section_id'];

// Fetch section and subject details
$section_stmt = $pdo->prepare("SELECT sec.*, sub.subject_name, sub.prelim_weight, sub.midterm_weight, sub.prefinals_weight, sub.finals_weight FROM sections sec JOIN subjects sub ON sec.subject_id = sub.subject_id WHERE sec.section_id = ?");
$section_stmt->execute([$section_id]);
$section = $section_stmt->fetch(PDO::FETCH_ASSOC);

if (!$section) { die("Section not found."); }
$subject_id = $section['subject_id'];

// Fetch components
$components_stmt = $pdo->prepare("SELECT * FROM grading_components WHERE subject_id = ?");
$components_stmt->execute([$subject_id]);
$components = $components_stmt->fetchAll(PDO::FETCH_ASSOC);

// IMPORTANT: Fetch ALL enrolled students, not a paginated list
$enrolled_stmt = $pdo->prepare("SELECT s.* FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ? ORDER BY s.last_name, s.first_name");
$enrolled_stmt->execute([$section_id]);
$all_enrolled_students = $enrolled_stmt->fetchAll(PDO::FETCH_ASSOC);

// --- GRADE CALCULATION (for ALL students) ---
$components_by_term = [];
foreach ($components as $component) { $components_by_term[$component['term']][] = $component; }
$student_grades_summary = [];
$terms_order = ['PRE-LIM', 'MIDTERM', 'PRE-FINALS', 'FINALS'];

foreach ($all_enrolled_students as $student) {
    $student_id = $student['student_id'];
    $middle_initial = !empty($student['middle_name']) ? ' ' . mb_substr($student['middle_name'], 0, 1) . '.' : '';
    $display_name = $student['last_name'] . ', ' . $student['first_name'] . $middle_initial;
    $student_grades_summary[$student_id] = [ 'name' => $display_name, 'terms' => [] ];
    
    foreach ($terms_order as $term) {
        $total_weighted_score = 0;
        if (!empty($components_by_term[$term])) {
            foreach ($components_by_term[$term] as $component) {
                $grade_stmt = $pdo->prepare("SELECT score FROM grades WHERE student_id = ? AND component_id = ?");
                $grade_stmt->execute([$student_id, $component['component_id']]);
                $grade = $grade_stmt->fetch(PDO::FETCH_ASSOC);
                if ($grade && is_numeric($grade['score']) && $component['max_score'] > 0) {
                    $weighted_score = ($grade['score'] / $component['max_score']) * $component['weight'];
                    $total_weighted_score += $weighted_score;
                }
            }
        }
        $student_grades_summary[$student_id]['terms'][$term] = $total_weighted_score;
    }

    $final_grade = 0;
    $final_grade += $student_grades_summary[$student_id]['terms']['PRE-LIM'] * ($section['prelim_weight'] / 100);
    $final_grade += $student_grades_summary[$student_id]['terms']['MIDTERM'] * ($section['midterm_weight'] / 100);
    $final_grade += $student_grades_summary[$student_id]['terms']['PRE-FINALS'] * ($section['prefinals_weight'] / 100);
    $final_grade += $student_grades_summary[$student_id]['terms']['FINALS'] * ($section['finals_weight'] / 100);
    
    $student_grades_summary[$student_id]['final_grade'] = $final_grade;
    $student_grades_summary[$student_id] += calculateGpaEquivalent($final_grade);
}

// --- SPREADSHEET CREATION ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle(substr(preg_replace('/[^a-zA-Z0-9\s]/', '', $section['section_name']), 0, 31)); // Clean title for Excel

// Set Header Row and Style it
$headers = ['Student Name', 'PRE-LIM', 'MIDTERM', 'PRE-FINALS', 'FINALS', 'Final Grade (Sem)', 'Grade Point', 'Remarks'];
$sheet->fromArray($headers, NULL, 'A1');
$header_style = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
];
$sheet->getStyle('A1:H1')->applyFromArray($header_style);

// Prepare data array for export
$data_to_export = [];
foreach ($student_grades_summary as $summary) {
    $data_to_export[] = [
        $summary['name'],
        number_format($summary['terms']['PRE-LIM'], 2),
        number_format($summary['terms']['MIDTERM'], 2),
        number_format($summary['terms']['PRE-FINALS'], 2),
        number_format($summary['terms']['FINALS'], 2),
        number_format($summary['final_grade'], 2),
        number_format($summary['gpa'], 2),
        $summary['remarks'],
    ];
}

// Add data to the sheet starting from cell A2
if (!empty($data_to_export)) {
    $sheet->fromArray($data_to_export, NULL, 'A2');
}

// Auto-size columns for better readability
foreach (range('A', 'H') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// --- FILE DOWNLOAD ---
// Sanitize the filename to remove invalid characters
$safe_section_name = preg_replace('/[^a-zA-Z0-9\s]/', '', $section['section_name']);
$filename = "Grade_Summary_" . $safe_section_name . "_" . date('Y-m-d') . ".xlsx";

// Set headers to force download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Create the writer and save the file to the browser's output stream
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();