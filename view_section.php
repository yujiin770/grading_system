<?php
require_once 'config/db_config.php';
require_once 'includes/session_check.php';

// --- GPA CONVERSION FUNCTION ---
function calculateGpaEquivalent($final_grade) {
    if ($final_grade >= 99) { return ['gpa' => 1.0, 'letter' => 'A+', 'remarks' => 'Excellent', 'class' => 'remarks-excellent']; } 
    elseif ($final_grade >= 96) { return ['gpa' => 1.25, 'letter' => 'A', 'remarks' => 'Excellent', 'class' => 'remarks-excellent']; } 
    elseif ($final_grade >= 93) { return ['gpa' => 1.5, 'letter' => 'A-', 'remarks' => 'Excellent', 'class' => 'remarks-excellent']; } 
    elseif ($final_grade >= 90) { return ['gpa' => 1.75, 'letter' => 'B+', 'remarks' => 'Very Good', 'class' => 'remarks-good']; } 
    elseif ($final_grade >= 87) { return ['gpa' => 2.0, 'letter' => 'B', 'remarks' => 'Good', 'class' => 'remarks-good']; } 
    elseif ($final_grade >= 84) { return ['gpa' => 2.25, 'letter' => 'B-', 'remarks' => 'Good', 'class' => 'remarks-good']; } 
    elseif ($final_grade >= 81) { return ['gpa' => 2.5, 'letter' => 'C+', 'remarks' => 'Satisfactory', 'class' => 'remarks-pass']; } 
    elseif ($final_grade >= 78) { return ['gpa' => 2.75, 'letter' => 'C', 'remarks' => 'Satisfactory', 'class' => 'remarks-pass']; } 
    elseif ($final_grade >= 75) { return ['gpa' => 3.0, 'letter' => 'C-', 'remarks' => 'Pass', 'class' => 'remarks-pass']; } 
    else { return ['gpa' => 5.0, 'letter' => 'F', 'remarks' => 'Fail', 'class' => 'remarks-fail']; }
}

// --- PAGINATION AND DATA FETCHING ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php'); exit();
}
$section_id = $_GET['id'];

$section_stmt = $pdo->prepare("SELECT sec.*, sub.subject_name, sub.prelim_weight, sub.midterm_weight, sub.prefinals_weight, sub.finals_weight FROM sections sec JOIN subjects sub ON sec.subject_id = sub.subject_id WHERE sec.section_id = ?");
$section_stmt->execute([$section_id]);
$section = $section_stmt->fetch(PDO::FETCH_ASSOC);

if (!$section) {
    $page_title = "Error"; include 'includes/header.php';
    echo '<div class="container"><div class="alert alert-danger">Section not found.</div></div>';
    include 'includes/footer.php'; die();
}
$page_title = "Manage Section: " . htmlspecialchars($section['section_name']);
$subject_id = $section['subject_id'];

// Pagination Setup
$students_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$total_students_stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE section_id = ?");
$total_students_stmt->execute([$section_id]);
$total_students = $total_students_stmt->fetchColumn();

$total_pages = $total_students > 0 ? ceil($total_students / $students_per_page) : 1;
if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;
$offset = ($current_page - 1) * $students_per_page;

// Fetch other necessary data
$components_stmt = $pdo->prepare("SELECT * FROM grading_components WHERE subject_id = ?");
$components_stmt->execute([$subject_id]);
$components = $components_stmt->fetchAll(PDO::FETCH_ASSOC);

$enrolled_stmt = $pdo->prepare("SELECT s.* FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = :section_id ORDER BY s.last_name, s.first_name LIMIT :limit OFFSET :offset");
$enrolled_stmt->bindValue(':section_id', $section_id, PDO::PARAM_INT);
$enrolled_stmt->bindValue(':limit', $students_per_page, PDO::PARAM_INT);
$enrolled_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$enrolled_stmt->execute();
$enrolled_students = $enrolled_stmt->fetchAll(PDO::FETCH_ASSOC);

$unassigned_stmt = $pdo->prepare("SELECT * FROM students WHERE student_id NOT IN (SELECT student_id FROM enrollments WHERE section_id = ?) ORDER BY last_name, first_name");
$unassigned_stmt->execute([$section_id]);
$unassigned_students = $unassigned_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2><?php echo htmlspecialchars($section['section_name']); ?></h2>
        <p>Subject: <?php echo htmlspecialchars($section['subject_name']); ?></p>
        <a href="enter_grades.php?section_id=<?php echo $section_id; ?>" class="btn">Enter/Edit Student Grades</a>
    </div>


    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <!-- TAB NAVIGATION BUTTONS -->
    <div class="tab-navigation">
        <button class="tab-nav-button" onclick="openTab(event, 'summary')" data-tab="summary">Grade Summary</button>
        <button class="tab-nav-button" onclick="openTab(event, 'enrollment')" data-tab="enrollment">Manage Enrollment</button>
        <button class="tab-nav-button" onclick="openTab(event, 'list')" data-tab="list">Student List</button>
    </div>

    <!-- TAB CONTENT PANELS -->

    <!-- Grade Summary Tab -->
    <div id="summary" class="tab-content">
        <div class="card">
           <!-- NEW CARD HEADER STRUCTURE -->
            <div class="card-header">
                <h3>Final Grade Summary</h3>
                <a href="actions/export_grades.php?section_id=<?php echo $section_id; ?>" class="btn">Export</a>
            </div>
            <?php
            $components_by_term = [];
            foreach ($components as $component) { $components_by_term[$component['term']][] = $component; }
            $student_grades_summary = [];
            $terms_order = ['PRE-LIM', 'MIDTERM', 'PRE-FINALS', 'FINALS'];
            
            foreach ($enrolled_students as $student) {
                $student_id = $student['student_id'];
                $middle_initial = !empty($student['middle_name']) ? ' ' . mb_substr($student['middle_name'], 0, 1) . '.' : '';
                $display_name = htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . $middle_initial);
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
            ?>
            <div class="table-responsive">
            <table>
                <thead><tr><th>Student Name</th><th>PRE-LIM</th><th>MIDTERM</th><th>PRE-FINALS</th><th>FINALS</th><th>Final Grade (Sem)</th><th>Grade Point</th><th>Remarks</th></tr></thead>
                <tbody>
                    <?php if (empty($enrolled_students)): ?>
                        <tr><td colspan="8">No students enrolled on this page.</td></tr>
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
                            <td class="<?php echo $summary['class']; ?>"><?php echo $summary['remarks']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
            <?php if ($total_pages > 1): ?>
            <nav><ul class="pagination">
                <li class="<?php if($current_page <= 1){ echo 'disabled'; } ?>"><a href="?id=<?php echo $section_id; ?>&page=<?php echo $current_page - 1; ?>&tab=summary">Prev</a></li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="<?php if($current_page == $i) {echo 'active'; } ?>"><a href="?id=<?php echo $section_id; ?>&page=<?php echo $i; ?>&tab=summary"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <li class="<?php if($current_page >= $total_pages){ echo 'disabled'; } ?>"><a href="?id=<?php echo $section_id; ?>&page=<?php echo $current_page + 1; ?>&tab=summary">Next</a></li>
            </ul></nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Manage Enrollment Tab -->
    <div id="enrollment" class="tab-content">
        <div class="card">
            <h3>Manage Enrollment</h3>
            <div class="import-form">
                <h4>Import Students from Excel</h4>
                <p>Upload an .xls or .xlsx file. Format: One student per row in Column A. Name must be <strong>Last Name, First Name Middle Name</strong>.</p>
                <form action="actions/import_students.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
                    <div class="form-group">
                        <input type="file" name="student_file" accept=".xls,.xlsx" required>
                    </div>
                    <button type="submit">Import and Enroll</button>
                </form>
            </div>
            <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">
            <h4>Manually Add Students</h4>
            <form action="actions/enroll_student_section.php" method="POST">
                <input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
                <div class="form-group">
                    <label for="student-list-select" style="display:inline-block;">Add Students from Master List</label>
                    <button type="button" id="select-all-btn" class="btn" style="margin-left: 15px; font-size: 0.8rem; padding: 5px 10px; background-color: #555;">Select All</button>
                    <select name="student_ids[]" id="student-list-select" multiple size="8">
                        <?php if (empty($unassigned_students)): ?>
                            <option disabled>All available students are already in this section.</option>
                        <?php else: ?>
                            <?php foreach ($unassigned_students as $student): ?>
                            <option value="<?php echo $student['student_id']; ?>"><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit">Enroll Selected</button>
            </form>
        </div>
    </div>

    <!-- Student List Tab -->
    <div id="list" class="tab-content">
        <div class="card">
            <h4>Currently Enrolled Students (Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>)</h4>
            <div class="table-responsive">
            <table>
                <thead><tr><th>Image</th><th>Name</th></tr></thead>
                <tbody>
                    <?php if (empty($enrolled_students)): ?>
                        <tr><td colspan="2">No students enrolled on this page.</td></tr>
                    <?php else: ?>
                        <?php foreach ($enrolled_students as $student): ?>
                        <tr>
                            <td><img src="uploads/<?php echo htmlspecialchars($student['image_path']); ?>" class="student-img"></td>
                            <td>
                                <?php 
                                    $middle_initial = !empty($student['middle_name']) ? ' ' . mb_substr($student['middle_name'], 0, 1) . '.' : '';
                                    echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . $middle_initial);
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
            <?php if ($total_pages > 1): ?>
            <nav><ul class="pagination">
                <li class="<?php if($current_page <= 1){ echo 'disabled'; } ?>"><a href="?id=<?php echo $section_id; ?>&page=<?php echo $current_page - 1; ?>&tab=list">Prev</a></li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="<?php if($current_page == $i) {echo 'active'; } ?>"><a href="?id=<?php echo $section_id; ?>&page=<?php echo $i; ?>&tab=list"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <li class="<?php if($current_page >= $total_pages){ echo 'disabled'; } ?>"><a href="?id=<?php echo $section_id; ?>&page=<?php echo $current_page + 1; ?>&tab=list">Next</a></li>
            </ul></nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- JAVASCRIPT FOR TABS and SELECT ALL -->
<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tab-nav-button");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        if(evt.currentTarget) {
            evt.currentTarget.className += " active";
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // This is the new logic to open the correct tab on page load
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'summary'; // Default to 'summary' if no tab is in the URL

        const targetButton = document.querySelector(`.tab-nav-button[data-tab="${activeTab}"]`);
        
        if (targetButton) {
            targetButton.click();
        } else {
            // Fallback to clicking the very first tab if something goes wrong
            document.querySelector('.tab-nav-button').click();
        }
        
        // Logic for the 'Select All' button
        const selectAllBtn = document.getElementById('select-all-btn');
        const studentListSelect = document.getElementById('student-list-select');
        if (selectAllBtn && studentListSelect) {
            selectAllBtn.addEventListener('click', function() {
                for (let i = 0; i < studentListSelect.options.length; i++) {
                    const option = studentListSelect.options[i];
                    if (!option.disabled) {
                        option.selected = true;
                    }
                }
            });
        }
    });
</script>