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

    // Handle file upload
    if (isset($_FILES['student_image']) && $_FILES['student_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $image_name = uniqid() . '_' . basename($_FILES["student_image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["student_image"]["tmp_name"], $target_file)) {
            $image_path = $image_name;
        }
    }
    
    // Logic for INSERT (new student) vs UPDATE (existing student)
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

// --- SEARCH AND PAGINATION LOGIC ---
$search_term = $_GET['search'] ?? '';
$students_per_page = 10; // You can change this number
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

// Base query parts
$sql_count = "SELECT COUNT(*) FROM students";
$sql_fetch = "SELECT * FROM students";
$params = [];

// Add WHERE clause if a search term is provided
if (!empty($search_term)) {
    $where_clause = " WHERE CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?";
    $sql_count .= $where_clause;
    $sql_fetch .= $where_clause;
    $params[] = '%' . $search_term . '%';
}

// 1. Count total matching students
$total_students_stmt = $pdo->prepare($sql_count);
$total_students_stmt->execute($params);
$total_students = $total_students_stmt->fetchColumn();

// 2. Calculate pagination variables
$total_pages = $total_students > 0 ? ceil($total_students / $students_per_page) : 1;
if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;
$offset = ($current_page - 1) * $students_per_page;

// 3. Fetch the students for the current page
$sql_fetch .= " ORDER BY last_name, first_name LIMIT " . (int)$students_per_page . " OFFSET " . (int)$offset;
$stmt_fetch = $pdo->prepare($sql_fetch);
$stmt_fetch->execute($params);
$students = $stmt_fetch->fetchAll(PDO::FETCH_ASSOC);

// Fetch data for the student being edited (if any)
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
        <p>A master list of all students. Add, edit, or search for students below.</p>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    
    <!-- Card for Adding or Editing a Student -->
    <div class="card">
        <h3><?php echo $student_to_edit ? 'Edit Student Details' : 'Add New Student'; ?></h3>
        <form action="students.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="student_id" value="<?php echo $student_to_edit['student_id'] ?? ''; ?>">
            <input type="hidden" name="existing_image" value="<?php echo $student_to_edit['image_path'] ?? 'default.png'; ?>">

            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student_to_edit['first_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Middle Name (Optional)</label>
                <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($student_to_edit['middle_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student_to_edit['last_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="student_image">Student Image</label>
                <input type="file" id="student_image" name="student_image" accept="image/*">
                <?php if ($student_to_edit && $student_to_edit['image_path'] && $student_to_edit['image_path'] != 'default.png'): ?>
                    <p style="margin-top: 10px;">Current Image: <img src="uploads/<?php echo htmlspecialchars($student_to_edit['image_path']); ?>" alt="Student Image" width="40" style="vertical-align: middle;"></p>
                <?php endif; ?>
            </div>
            <button type="submit"><?php echo $student_to_edit ? 'Update Student' : 'Add Student'; ?></button>
            <?php if ($student_to_edit): ?>
                <a href="students.php" class="btn" style="background-color: #7f8c8d; border-color: #7f8c8d;">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Card for Listing Existing Students -->
    <div class="card">
        <h3>Current Student List</h3>

        <!-- SEARCH FORM -->
        <form method="GET" action="students.php" class="search-form">
            <div class="form-group">
                <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Search</button>
            </div>
        </form>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Image</th><th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr><td colspan="5">No students found<?php if(!empty($search_term)) echo ' matching your search'; ?>.</td></tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><img src="uploads/<?php echo htmlspecialchars($student['image_path']); ?>" alt="Student" class="student-img"></td>
                            <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['middle_name']); ?></td>
                            <td class="action-links">
                                <a href="students.php?edit_id=<?php echo $student['student_id']; ?>" class="btn-edit">Edit</a>
                                <a href="actions/delete_student.php?id=<?php echo $student['student_id']; ?>" onclick="return confirm('Are you sure you want to permanently delete this student?');" class="btn-delete">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION LINKS -->
        <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination">
                <li class="<?php if($current_page <= 1){ echo 'disabled'; } ?>">
                    <a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $current_page - 1; ?>">Prev</a>
                </li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="<?php if($current_page == $i) {echo 'active'; } ?>">
                    <a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="<?php if($current_page >= $total_pages){ echo 'disabled'; } ?>">
                    <a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $current_page + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>