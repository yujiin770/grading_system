<?php
require_once 'config/db_config.php';

// We must receive a section_id to know which class we are grading
if (!isset($_GET['section_id'])) {
    header('Location: subjects.php');
    exit();
}
$section_id = $_GET['section_id'];
$selected_term = $_GET['term'] ?? null;
$selected_component_id = $_GET['component_id'] ?? null;

// --- DATA FETCHING ---
$stmt = $pdo->prepare("
    SELECT sec.section_id, sec.section_name, sub.subject_id, sub.subject_name 
    FROM sections sec 
    JOIN subjects sub ON sec.subject_id = sub.subject_id 
    WHERE sec.section_id = ?
");
$stmt->execute([$section_id]);
$section = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$section) { die("Section not found."); }
$subject_id = $section['subject_id'];

// Fetch ALL components and group them by term in a PHP array
$components_stmt = $pdo->prepare("SELECT * FROM grading_components WHERE subject_id = ? ORDER BY term, component_name");
$components_stmt->execute([$subject_id]);
$all_components = $components_stmt->fetchAll(PDO::FETCH_ASSOC);

$grouped_components = [];
foreach ($all_components as $component) {
    $grouped_components[$component['term']][] = $component;
}
$terms_order = ['PRE-LIM', 'MIDTERM', 'PRE-FINALS', 'FINALS'];

// If a component has been selected, fetch students, grades, and component details
$enrolled_students = [];
$current_grades = [];
$selected_component_details = null; // To store details like max_score

if ($selected_component_id) {
    // Find the details of the selected component from our earlier fetch
    foreach ($all_components as $component) {
        if ($component['component_id'] == $selected_component_id) {
            $selected_component_details = $component;
            break;
        }
    }

    // Get the list of students in this section
    $enrolled_stmt = $pdo->prepare("
        SELECT s.student_id, s.first_name, s.last_name FROM students s
        JOIN enrollments e ON s.student_id = e.student_id
        WHERE e.section_id = ? ORDER BY s.last_name, s.first_name
    ");
    $enrolled_stmt->execute([$section_id]);
    $enrolled_students = $enrolled_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get their existing scores for the selected component
    $grades_stmt = $pdo->prepare("SELECT student_id, score FROM grades WHERE component_id = ?");
    $grades_stmt->execute([$selected_component_id]);
    foreach ($grades_stmt->fetchAll(PDO::FETCH_ASSOC) as $grade) {
        $current_grades[$grade['student_id']] = $grade['score'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Grades</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="page-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <main class="main-content">
            <div class="container">
                <div class="page-header">
                    <h2>Enter Grades for <?php echo htmlspecialchars($section['section_name']); ?></h2>
                    <p>Subject: <?php echo htmlspecialchars($section['subject_name']); ?></p>
                </div>

                <div class="card">
                    <h3>Step 1: Select Term & Component</h3>
                    <form id="grade-selection-form" method="GET" action="enter_grades.php">
                        <input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
                        
                        <!-- Dropdown 1: Select the Term -->
                        <div class="form-group">
                            <label for="term">Select Term:</label>
                            <select name="term" id="term-select" required>
                                <option value="">-- Choose a Term --</option>
                                <?php foreach ($terms_order as $term): ?>
                                    <?php if (!empty($grouped_components[$term])): ?>
                                        <option value="<?php echo $term; ?>" <?php echo ($selected_term == $term) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($term); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dropdown 2: Select the Component (Initially hidden) -->
                        <div class="form-group" id="component-group" style="display: none;">
                            <label for="component_id">Select Component:</label>
                            <select name="component_id" id="component-select" required>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>
                    </form>
                </div>

                <?php if ($selected_component_id && !empty($enrolled_students)): ?>
                <div class="card">
                    <h3>Step 2: Enter Scores</h3>
                    <form action="actions/save_grades.php" method="POST">
                        <input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
                        <input type="hidden" name="component_id" value="<?php echo $selected_component_id; ?>">
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Score (out of <?php echo htmlspecialchars($selected_component_details['max_score'] ?? '100'); ?>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrolled_students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></td>
                                    <td>
                                        <input type="number" 
                                               name="scores[<?php echo $student['student_id']; ?>]" 
                                               step="0.01" 
                                               min="0" 
                                               max="<?php echo htmlspecialchars($selected_component_details['max_score'] ?? '100'); ?>"
                                               value="<?php echo $current_grades[$student['student_id']] ?? ''; ?>"
                                               placeholder="Enter score...">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="submit">Save All Grades</button>
                    </form>
                <?php elseif ($selected_component_id): ?>
                    <div class="card">
                        <p>No students are currently enrolled in this section. Please add students from the "Manage Enrollment" section on the previous page.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- JAVASCRIPT TO POWER THE DYNAMIC DROPDOWNS -->
    <script>
        const componentsByTerm = <?php echo json_encode($grouped_components); ?>;
        
        const termSelect = document.getElementById('term-select');
        const componentGroup = document.getElementById('component-group');
        const componentSelect = document.getElementById('component-select');
        const form = document.getElementById('grade-selection-form');

        function updateComponentDropdown() {
            const selectedTerm = termSelect.value;
            componentSelect.innerHTML = '<option value="">-- Choose a Component --</option>';

            if (selectedTerm && componentsByTerm[selectedTerm]) {
                componentGroup.style.display = 'block';
                componentsByTerm[selectedTerm].forEach(component => {
                    const option = document.createElement('option');
                    option.value = component.component_id;
                    option.textContent = `${component.component_name} (${component.weight}%)`;
                    
                    if (component.component_id == '<?php echo $selected_component_id; ?>') {
                        option.selected = true;
                    }
                    
                    componentSelect.appendChild(option);
                });
            } else {
                componentGroup.style.display = 'none';
            }
        }

        termSelect.addEventListener('change', updateComponentDropdown);

        componentSelect.addEventListener('change', () => {
            if (componentSelect.value) {
                form.submit();
            }
        });

        document.addEventListener('DOMContentLoaded', updateComponentDropdown);
    </script>
</body>
</html>