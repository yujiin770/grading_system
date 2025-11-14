<?php
session_start();
require_once 'config/db_config.php';


if (!isset($_GET['section_id'])) {
    header('Location: subjects.php');
    exit();
}
$section_id = $_GET['section_id'];
$selected_term = $_GET['term'] ?? null;
$selected_component_id = $_GET['component_id'] ?? null;

// --- DATA FETCHING (with Pagination) ---
$stmt = $pdo->prepare("SELECT sec.section_id, sec.section_name, sub.subject_id, sub.subject_name FROM sections sec JOIN subjects sub ON sec.subject_id = sub.subject_id WHERE sec.section_id = ?");
$stmt->execute([$section_id]);
$section = $stmt->fetch(PDO::FETCH_ASSOC);

$page_title = $section ? "Enter Grades: " . htmlspecialchars($section['section_name']) : "Error";

if (!$section) {
    include 'includes/header.php';
    echo '<div class="container"><div class="alert alert-danger">Section not found.</div></div>';
    include 'includes/footer.php';
    die();
}
$subject_id = $section['subject_id'];

$components_stmt = $pdo->prepare("SELECT * FROM grading_components WHERE subject_id = ? ORDER BY term, component_name");
$components_stmt->execute([$subject_id]);
$all_components = $components_stmt->fetchAll(PDO::FETCH_ASSOC);

$grouped_components = [];
foreach ($all_components as $component) {
    $grouped_components[$component['term']][] = $component;
}
$terms_order = ['PRE-LIM', 'MIDTERM', 'PRE-FINALS', 'FINALS'];

$enrolled_students = []; $current_grades = []; $selected_component_details = null;
$total_pages = 1; $current_page = 1;

if ($selected_component_id) {
    $students_per_page = 10;
    $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE section_id = ?");
    $total_stmt->execute([$section_id]);
    $total_students = $total_stmt->fetchColumn();
    $total_pages = $total_students > 0 ? ceil($total_students / $students_per_page) : 1;
    if ($current_page < 1) $current_page = 1;
    if ($current_page > $total_pages) $current_page = $total_pages;
    $offset = ($current_page - 1) * $students_per_page;
    foreach ($all_components as $component) {
        if ($component['component_id'] == $selected_component_id) { $selected_component_details = $component; break; }
    }
    $enrolled_stmt = $pdo->prepare("SELECT s.student_id, s.first_name, s.last_name FROM students s JOIN enrollments e ON s.student_id = e.student_id WHERE e.section_id = :section_id ORDER BY s.last_name, s.first_name LIMIT :limit OFFSET :offset");
    $enrolled_stmt->bindValue(':section_id', $section_id, PDO::PARAM_INT);
    $enrolled_stmt->bindValue(':limit', $students_per_page, PDO::PARAM_INT);
    $enrolled_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $enrolled_stmt->execute();
    $enrolled_students = $enrolled_stmt->fetchAll(PDO::FETCH_ASSOC);
    $grades_stmt = $pdo->prepare("SELECT student_id, score FROM grades WHERE component_id = ?");
    $grades_stmt->execute([$selected_component_id]);
    foreach ($grades_stmt->fetchAll(PDO::FETCH_ASSOC) as $grade) {
        $current_grades[$grade['student_id']] = $grade['score'];
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <!-- All header content now lives inside this div -->
        <div class="header-content">
            <h2>Enter Grades for <?php echo htmlspecialchars($section['section_name']); ?></h2>
            <p>Subject: <?php echo htmlspecialchars($section['subject_name']); ?></p>
        </div>
        <div class="header-actions">
            <a href="view_section.php?id=<?php echo $section_id; ?>" class="btn btn-secondary">
                &larr; Back to Grade Summary
            </a>
        </div>
    </div>


    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <div class="card step-card active">
        <h3><span class="step-number">1</span> Select Term & Component</h3>
        <form id="grade-selection-form" method="GET" action="enter_grades.php">
            <input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
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
            <div class="form-group" id="component-group" style="display: none;">
                <label for="component_id">Select Component:</label>
                <select name="component_id" id="component-select" required>
                    <!-- Options populated by JavaScript -->
                </select>
            </div>
        </form>
    </div>

    <div class="card step-card <?php if ($selected_component_id) echo 'active'; ?>">
        <h3><span class="step-number">2</span> Enter Scores <?php if ($total_pages > 1) echo "(Page {$current_page} of {$total_pages})"; ?></h3>
        
        <?php if ($selected_component_id && !empty($enrolled_students)): ?>
        <form action="actions/save_grades.php" method="POST">
            <input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
            <input type="hidden" name="component_id" value="<?php echo $selected_component_id; ?>">
            <input type="hidden" name="current_page" value="<?php echo $current_page; ?>">
            <input type="hidden" name="term" value="<?php echo $selected_term; ?>">

            <div class="table-responsive">
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
                            <input type="number" name="scores[<?php echo $student['student_id']; ?>]" step="0.01" min="0" max="<?php echo htmlspecialchars($selected_component_details['max_score'] ?? '100'); ?>" value="<?php echo $current_grades[$student['student_id']] ?? ''; ?>" placeholder="Enter score...">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            
            <button type="submit">Save Grades on This Page</button>

            <?php if ($total_pages > 1): ?>
            <nav>
                <ul class="pagination">
                    <li class="<?php if($current_page <= 1){ echo 'disabled'; } ?>"><a href="?section_id=<?php echo $section_id; ?>&term=<?php echo $selected_term; ?>&component_id=<?php echo $selected_component_id; ?>&page=<?php echo $current_page - 1; ?>">Prev</a></li>
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="<?php if($current_page == $i) {echo 'active'; } ?>"><a href="?section_id=<?php echo $section_id; ?>&term=<?php echo $selected_term; ?>&component_id=<?php echo $selected_component_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                    <li class="<?php if($current_page >= $total_pages){ echo 'disabled'; } ?>"><a href="?section_id=<?php echo $section_id; ?>&term=<?php echo $selected_term; ?>&component_id=<?php echo $selected_component_id; ?>&page=<?php echo $current_page + 1; ?>">Next</a></li>
                </ul>
            </nav>
            <?php endif; ?>
        </form>
        <?php elseif ($selected_component_id): ?>
            <p>No students are currently enrolled in this section.</p>
        <?php else: ?>
            <p>Please select a term and component above to begin entering grades.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

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
                
                // This part is crucial for restoring state after a refresh
                if (component.component_id == '<?php echo $selected_component_id; ?>') {
                    option.selected = true;
                }
                
                componentSelect.appendChild(option);
            });
        } else {
            componentGroup.style.display = 'none';
        }
    }

    // When the user changes the TERM dropdown
    termSelect.addEventListener('change', () => {
        // We only want to submit the form if the user manually selects a component next.
        // So, we just update the component dropdown.
        updateComponentDropdown();
    });

    // When the user changes the COMPONENT dropdown
    componentSelect.addEventListener('change', () => {
        if (componentSelect.value) {
            form.submit(); // Submit the form to reload the page with the selected component
        }
    });

    // THIS IS THE KEY: Run this function when the page first loads.
    // It will check the value of the Term dropdown (which PHP has already set to 'PRE-LIM', etc.)
    // and correctly populate and show the Component dropdown.
    document.addEventListener('DOMContentLoaded', updateComponentDropdown);
</script>