<?php
require_once 'config/db_config.php';

// --- GPA CONVERSION FUNCTION (Based on your table) ---
function calculateGpaEquivalent($final_grade) {
    if ($final_grade >= 99) {
        return ['gpa' => 1.0, 'letter' => 'A+', 'remarks' => 'Excellent', 'class' => 'remarks-excellent'];
    } elseif ($final_grade >= 96) { // 96-98
        return ['gpa' => 1.25, 'letter' => 'A', 'remarks' => 'Excellent', 'class' => 'remarks-excellent'];
    } elseif ($final_grade >= 93) { // 93-95
        return ['gpa' => 1.5, 'letter' => 'A-', 'remarks' => 'Excellent', 'class' => 'remarks-excellent'];
    } elseif ($final_grade >= 90) { // 90-92
        return ['gpa' => 1.75, 'letter' => 'B+', 'remarks' => 'Very Good', 'class' => 'remarks-good'];
    } elseif ($final_grade >= 87) { // 87-89
        return ['gpa' => 2.0, 'letter' => 'B', 'remarks' => 'Good', 'class' => 'remarks-good'];
    } elseif ($final_grade >= 84) { // 84-86
        return ['gpa' => 2.25, 'letter' => 'B-', 'remarks' => 'Good', 'class' => 'remarks-good'];
    } elseif ($final_grade >= 81) { // 81-83
        return ['gpa' => 2.5, 'letter' => 'C+', 'remarks' => 'Satisfactory', 'class' => 'remarks-pass'];
    } elseif ($final_grade >= 78) { // 78-80
        return ['gpa' => 2.75, 'letter' => 'C', 'remarks' => 'Satisfactory', 'class' => 'remarks-pass'];
    } elseif ($final_grade >= 75) { // 75-77.74
        return ['gpa' => 3.0, 'letter' => 'C-', 'remarks' => 'Pass', 'class' => 'remarks-pass'];
    } else { // Below 75 is considered Fail
        return ['gpa' => 5.0, 'letter' => 'F', 'remarks' => 'Fail', 'class' => 'remarks-fail'];
    }
}
// --- END OF GPA FUNCTION ---

// Validate the section ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: subjects.php');
    exit();
}
$section_id = $_GET['id'];

// --- DATA FETCHING ---
$section_stmt = $pdo->prepare("SELECT sec.*, sub.subject_name FROM sections sec JOIN subjects sub ON sec.subject_id = sub.subject_id WHERE sec.section_id = ?");
$section_stmt->execute([$section_id]);
$section = $section_stmt->fetch(PDO::FETCH_ASSOC);

if (!$section) { die("Section not found."); }
$subject_id = $section['subject_id'];

$components_stmt = $pdo->prepare("SELECT * FROM grading_components WHERE subject_id = ?");
$components_stmt->execute([$subject_id]);
$components = $components_stmt->fetchAll(PDO::FETCH_ASSOC);

$enrolled_stmt = $pdo->prepare("SELECT s.* FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = ? ORDER BY s.last_name, s.first_name");
$enrolled_stmt->execute([$section_id]);
$enrolled_students = $enrolled_stmt->fetchAll(PDO::FETCH_ASSOC);

$unassigned_stmt = $pdo->prepare("SELECT * FROM students WHERE student_id NOT IN (SELECT student_id FROM enrollments WHERE section_id = ?) ORDER BY last_name, first_name");
$unassigned_stmt->execute([$section_id]);
$unassigned_students = $unassigned_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Section: <?php echo htmlspecialchars($section['section_name']); ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="page-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <main class="main-content">
            <div class="container">
                <div class="page-header">
                    <h2><?php echo htmlspecialchars($section['section_name']); ?></h2>
                    <p>Subject: <?php echo htmlspecialchars($section['subject_name']); ?></p>
                    <a href="enter_grades.php?section_id=<?php echo $section_id; ?>" class="btn">Enter/Edit Student Grades</a>
                </div>

                <!-- Card for Final Grades with Term Breakdown AND GPA -->
                <div class="card">
                    <h3>Final Grade Summary</h3>
                    <?php
                    // Pre-group components by term for efficient processing
                    $components_by_term = [];
                    foreach ($components as $component) {
                        $components_by_term[$component['term']][] = $component;
                    }
                    
                    $student_grades_summary = [];
                    $terms_order = ['PRE-LIM', 'MIDTERM', 'PRE-FINALS', 'FINALS'];

                    foreach ($enrolled_students as $student) {
                        $student_id = $student['student_id'];
                        $student_grades_summary[$student_id] = [
                            'name' => htmlspecialchars($student['last_name'] . ', ' . $student['first_name']),
                            'terms' => []
                        ];

                        $term_grades = [];
                        
                        foreach ($terms_order as $term) {
                            $total_weighted_score_for_term = 0;
                            if (!empty($components_by_term[$term])) {
                                foreach ($components_by_term[$term] as $component) {
                                    $grade_stmt = $pdo->prepare("SELECT score FROM grades WHERE student_id = ? AND component_id = ?");
                                    $grade_stmt->execute([$student_id, $component['component_id']]);
                                    $grade = $grade_stmt->fetch(PDO::FETCH_ASSOC);
                                    if ($grade && is_numeric($grade['score']) && $component['max_score'] > 0) {
                                        $weighted_score = ($grade['score'] / $component['max_score']) * $component['weight'];
                                        $total_weighted_score_for_term += $weighted_score;
                                    }
                                }
                            }
                            $student_grades_summary[$student_id]['terms'][$term] = $total_weighted_score_for_term;
                            $term_grades[] = $total_weighted_score_for_term;
                        }

                        $final_grade = (count($term_grades) > 0) ? array_sum($term_grades) / count($term_grades) : 0;
                        $student_grades_summary[$student_id]['final_grade'] = $final_grade;
                        
                        $gpa_data = calculateGpaEquivalent($final_grade);
                        $student_grades_summary[$student_id]['gpa'] = $gpa_data['gpa'];
                        $student_grades_summary[$student_id]['letter'] = $gpa_data['letter'];
                        $student_grades_summary[$student_id]['remarks'] = $gpa_data['remarks'];
                        $student_grades_summary[$student_id]['remarks_class'] = $gpa_data['class'];
                    }
                    ?>

                    <table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>PRE-LIM</th>
                                <th>MIDTERM</th>
                                <th>PRE-FINALS</th>
                                <th>FINALS</th>
                                <th>Final Grade (Sem)</th>
                                <th>Grade Point</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($enrolled_students)): ?>
                                <tr><td colspan="8">No students have been enrolled in this section yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($student_grades_summary as $summary): ?>
                                <tr>
                                    <td><?php echo $summary['name']; ?></td>
                                    <td><?php echo number_format($summary['terms']['PRE-LIM'], 2); ?></td>
                                    <td><?php echo number_format($summary['terms']['MIDTERM'], 2); ?></td>
                                    <td><?php echo number_format($summary['terms']['PRE-FINALS'], 2); ?></td>
                                    <td><?php echo number_format($summary['terms']['FINALS'], 2); ?></td>
                                    <td><strong><?php echo number_format($summary['final_grade'], 2); ?>%</strong></td>
                                    <td><?php echo number_format($summary['gpa'], 2); ?></td>
                                    <td class="<?php echo $summary['remarks_class']; ?>"><?php echo $summary['remarks']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Card for Student Enrollment -->
                <div class="card">
                    <h3>Manage Enrollment</h3>
                    <form action="actions/enroll_student_section.php" method="POST">
                        <input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
                        <div class="form-group">
                            <label for="student_ids">Add Students from Master List</label>
                            <select name="student_ids[]" id="student_ids" multiple size="8">
                                <?php if (empty($unassigned_students)): ?>
                                    <option disabled>All available students are in this section.</option>
                                <?php else: ?>
                                    <?php foreach ($unassigned_students as $student): ?>
                                    <option value="<?php echo $student['student_id']; ?>">
                                        <?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <button type="submit">Enroll Selected Students</button>
                    </form>
                    <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">
                    <h4>Currently Enrolled Students</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($enrolled_students)): ?>
                                <tr><td colspan="2">No students enrolled.</td></tr>
                            <?php else: ?>
                                <?php foreach ($enrolled_students as $student): ?>
                                <tr>
                                    <td><img src="uploads/<?php echo htmlspecialchars($student['image_path']); ?>" class="student-img"></td>
                                    <td><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>