<?php
require_once 'config/db_config.php';

// --- HANDLE FORM SUBMISSION FOR ADDING OR EDITING A STUDENT ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['first_name'])) {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $student_id = $_POST['student_id']; // Will be empty for new students
    $image_path = $_POST['existing_image'] ?? 'default.png';

    // --- Handle Image Upload ---
    // Check if a new image was uploaded and there are no errors
    if (isset($_FILES['student_image']) && $_FILES['student_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        // Create a unique name for the image to prevent overwriting
        $image_name = uniqid() . '_' . basename($_FILES["student_image"]["name"]);
        $target_file = $target_dir . $image_name;
        
        // Move the uploaded file to the 'uploads' directory
        if (move_uploaded_file($_FILES["student_image"]["tmp_name"], $target_file)) {
            $image_path = $image_name;
        }
    }
    
    // --- Logic for INSERT (new) vs UPDATE (existing) ---
    if (empty($student_id)) {
        // ID is empty, so INSERT a new student
        $sql = "INSERT INTO students (first_name, middle_name, last_name, image_path) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$first_name, $middle_name, $last_name, $image_path]);
    } else {
        // ID exists, so UPDATE the existing student
        $sql = "UPDATE students SET first_name=?, middle_name=?, last_name=?, image_path=? WHERE student_id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$first_name, $middle_name, $last_name, $image_path, $student_id]);
    }

    header('Location: students.php'); // Redirect to clear the form and prevent resubmission
    exit();
}

// --- DATA FETCHING FOR THE PAGE ---

// Fetch a specific student's data if an 'edit_id' is in the URL
$student_to_edit = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $student_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch the complete list of all students to display in the table
$students_stmt = $pdo->query("SELECT * FROM students ORDER BY last_name, first_name");
$students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student List - Grading System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="page-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <div class="container">
                <div class="page-header">
                    <h2>Manage Student List</h2>
                </div>

                <!-- Card for Adding or Editing a Student -->
                <div class="card">
                    <h3><?php echo $student_to_edit ? 'Edit Student Details' : 'Add New Student to Master List'; ?></h3>
                    <form action="students.php" method="POST" enctype="multipart/form-data">
                        <!-- Hidden fields to manage state (editing vs adding) -->
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
                            <?php if ($student_to_edit && $student_to_edit['image_path']): ?>
                                <p style="margin-top: 10px;">Current Image: <img src="uploads/<?php echo htmlspecialchars($student_to_edit['image_path']); ?>" alt="Student Image" width="40" style="vertical-align: middle;"></p>
                            <?php endif; ?>
                        </div>
                        <button type="submit"><?php echo $student_to_edit ? 'Update Student' : 'Add Student'; ?></button>
                    </form>
                </div>

                <!-- Card for Displaying the Student List -->
                <div class="card">
                    <h3>Current Student List</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                                <tr>
                                    <td colspan="5">No students found. Add one using the form above.</td>
                                </tr>
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
            </div>
        </main>
    </div>
</body>
</html>