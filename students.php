<?php
require_once 'config/db_config.php';
session_start();
$page_title = "Manage Student List";

// --- HANDLE ADD/UPDATE FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['first_name'])) {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $student_id = $_POST['student_id'];
    $image_path = $_POST['existing_image'] ?? 'default.png';

    if (isset($_FILES['student_image']) && $_FILES['student_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $image_name = uniqid() . '_' . basename($_FILES["student_image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["student_image"]["tmp_name"], $target_file)) {
            $image_path = $image_name;
        }
    }
    
    if (empty($student_id)) {
        $sql = "INSERT INTO students (first_name, middle_name, last_name, image_path) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$first_name, $middle_name, $last_name, $image_path]);
        $_SESSION['success_message'] = "Student added successfully!";
    } else {
        $sql = "UPDATE students SET first_name=?, middle_name=?, last_name=?, image_path=? WHERE student_id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$first_name, $middle_name, $last_name, $image_path, $student_id]);
        $_SESSION['success_message'] = "Student updated successfully!";
    }
    header('Location: students.php');
    exit();
}

// --- ADVANCED SEARCH, FILTERING, AND PAGINATION ---
$search_term = $_GET['search'] ?? '';
$filter_section_id = $_GET['section_id'] ?? '';
$filter_sy = $_GET['school_year'] ?? '';
$filter_sem = $_GET['semester'] ?? '';

$students_per_page = 15;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$sql_base = "FROM students s 
             LEFT JOIN enrollments e ON s.student_id = e.student_id
             LEFT JOIN sections sec ON e.section_id = sec.section_id";
$where_clauses = [];
$params = [];

if (!empty($search_term)) {
    $where_clauses[] = "CONCAT(s.first_name, ' ', s.middle_name, ' ', s.last_name) LIKE ?";
    $params[] = '%' . $search_term . '%';
}
if (!empty($filter_section_id) && is_numeric($filter_section_id)) {
    $where_clauses[] = "e.section_id = ?";
    $params[] = $filter_section_id;
}
if (!empty($filter_sy)) {
    $where_clauses[] = "sec.school_year = ?";
    $params[] = $filter_sy;
}
if (!empty($filter_sem)) {
    $where_clauses[] = "sec.semester = ?";
    $params[] = $filter_sem;
}

$sql_where = '';
if (!empty($where_clauses)) {
    $sql_where = ' WHERE ' . implode(' AND ', $where_clauses);
}

// 1. Count total matching students
$sql_count = "SELECT COUNT(DISTINCT s.student_id) " . $sql_base . $sql_where;
$total_students_stmt = $pdo->prepare($sql_count);
$total_students_stmt->execute($params);
$total_students = $total_students_stmt->fetchColumn();

// 2. Calculate pagination variables
$total_pages = $total_students > 0 ? ceil($total_students / $students_per_page) : 1;
if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;
$offset = ($current_page - 1) * $students_per_page;

// 3. Fetch the students for the current page
$sql_fetch = "SELECT DISTINCT s.*, GROUP_CONCAT(CONCAT(sec.school_year, ' - ', sec.semester, ' - ', sec.section_name) SEPARATOR '; ') as sections
              " . $sql_base . $sql_where . " 
              GROUP BY s.student_id
              ORDER BY s.last_name, s.first_name 
              LIMIT " . (int)$students_per_page . " OFFSET " . (int)$offset;
$stmt_fetch = $pdo->prepare($sql_fetch);
$stmt_fetch->execute($params);
$students = $stmt_fetch->fetchAll(PDO::FETCH_ASSOC);

// Fetch data for filters
$all_sections_stmt = $pdo->query("SELECT section_id, section_name, school_year, semester FROM sections ORDER BY school_year DESC, semester ASC, section_name ASC");
$all_sections = $all_sections_stmt->fetchAll(PDO::FETCH_ASSOC);
$school_years_stmt = $pdo->query("SELECT DISTINCT school_year FROM sections ORDER BY school_year DESC");
$school_years = $school_years_stmt->fetchAll(PDO::FETCH_COLUMN);
$semesters = ['1st Sem', '2nd Sem'];

// Fetch data for the student being edited
$student_to_edit = null;
if (isset($_GET['edit_id'])) {
    $edit_stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
    $edit_stmt->execute([$_GET['edit_id']]);
    $student_to_edit = $edit_stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Manage Student List</h2>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h3><?php echo $student_to_edit ? 'Edit Student Details' : 'Add New Student'; ?></h3>
        <form action="students.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="student_id" value="<?php echo $student_to_edit['student_id'] ?? ''; ?>">
            <input type="hidden" name="existing_image" value="<?php echo $student_to_edit['image_path'] ?? 'default.png'; ?>">
            <div class="form-group"><label>First Name</label><input type="text" name="first_name" value="<?php echo htmlspecialchars($student_to_edit['first_name'] ?? ''); ?>" required></div>
            <div class="form-group"><label>Middle Name (Optional)</label><input type="text" name="middle_name" value="<?php echo htmlspecialchars($student_to_edit['middle_name'] ?? ''); ?>"></div>
            <div class="form-group"><label>Last Name</label><input type="text" name="last_name" value="<?php echo htmlspecialchars($student_to_edit['last_name'] ?? ''); ?>" required></div>
            <div class="form-group">
                <label>Student Image</label><input type="file" name="student_image" accept="image/*">
                <?php if ($student_to_edit && $student_to_edit['image_path'] && $student_to_edit['image_path'] != 'default.png'): ?>
                    <p style="margin-top: 10px;">Current Image: <img src="uploads/<?php echo htmlspecialchars($student_to_edit['image_path']); ?>" alt="Student Image" width="40" style="vertical-align: middle;"></p>
                <?php endif; ?>
            </div>
            <button type="submit"><?php echo $student_to_edit ? 'Update Student' : 'Add Student'; ?></button>
            <?php if ($student_to_edit): ?><a href="students.php" class="btn" style="background-color: #7f8c8d; border-color: #7f8c8d;">Cancel Edit</a><?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3>Current Student List</h3>
        <form id="filter-form" method="GET" action="students.php">
            <div class="filter-bar" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <div class="filter-group">
                    <label for="search-input">Search by Name</label>
                    <input type="text" id="search-input" name="search" placeholder="Start typing..." value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
                <div class="filter-group">
                    <label for="sy-select">Filter by School Year</label>
                    <select id="sy-select" name="school_year">
                        <option value="">All Years</option>
                        <?php foreach($school_years as $year): ?>
                            <option value="<?php echo $year; ?>" <?php if ($filter_sy == $year) echo 'selected'; ?>><?php echo htmlspecialchars($year); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="section-select">Filter by Section</label>
                    <select id="section-select" name="section_id">
                        <option value="">All Sections</option>
                        <?php foreach($all_sections as $section): ?>
                            <!-- THIS IS THE FIX for the dropdown text -->
                            <option value="<?php echo $section['section_id']; ?>" <?php if ($filter_section_id == $section['section_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($section['section_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table>
                <thead><tr><th>Image</th><th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Enrolled Sections</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr><td colspan="6">No students found matching your criteria.</td></tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><img src="uploads/<?php echo htmlspecialchars($student['image_path']); ?>" alt="Student" class="student-img"></td>
                            <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['middle_name']); ?></td>
                            <td><?php  echo !empty($student['sections']) ? nl2br(htmlspecialchars(str_replace(';', "\n", $student['sections']))) : 'Not Enrolled'; ?> </td>                    
                            <td class="action-links">
                                <a href="students.php?edit_id=<?php echo $student['student_id']; ?>" class="btn-edit">Edit</a>
                                <a href="actions/delete_student.php?id=<?php echo $student['student_id']; ?>" onclick="return confirm('Are you sure?');" class="btn-delete">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav><ul class="pagination">
            <?php $query_params = "search=" . urlencode($search_term) . "&school_year=" . urlencode($filter_sy) . "&semester=" . urlencode($filter_sem) . "&section_id=" . urlencode($filter_section_id); ?>
            <li class="<?php if($current_page <= 1){ echo 'disabled'; } ?>"><a href="?<?php echo $query_params; ?>&page=<?php echo $current_page - 1; ?>">Prev</a></li>
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <li class="<?php if($current_page == $i) {echo 'active'; } ?>"><a href="?<?php echo $query_params; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <li class="<?php if($current_page >= $total_pages){ echo 'disabled'; } ?>"><a href="?<?php echo $query_params; ?>&page=<?php echo $current_page + 1; ?>">Next</a></li>
        </ul></nav>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- JAVASCRIPT FOR AUTOMATIC FILTERS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filter-form');
        const inputs = filterForm.querySelectorAll('input, select');
        let searchTimeout;

        function submitForm() {
            filterForm.submit();
        }

        inputs.forEach(input => {
            if (input.type === 'text') {
                input.addEventListener('keyup', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(submitForm, 500); // Wait 500ms after typing
                });
            } else {
                input.addEventListener('change', submitForm); // Submit immediately for dropdowns
            }
        });
    });
</script>