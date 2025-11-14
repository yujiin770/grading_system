<?php
require_once 'config/db_config.php';
require_once 'includes/session_check.php';


// --- HANDLE FORM SUBMISSIONS (FOR BOTH ADDING AND UPDATING) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subject_name'])) {
    $subject_name = trim($_POST['subject_name']);
    $class_code = trim($_POST['class_code']);
    $subject_id = $_POST['subject_id']; // This will be hidden in the form

    if (empty($subject_id)) {
        // If no ID, this is a NEW subject
        $sql = "INSERT INTO subjects (subject_name, class_code) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$subject_name, $class_code]);
    } else {
        // If there is an ID, UPDATE the existing subject
        $sql = "UPDATE subjects SET subject_name = ?, class_code = ? WHERE subject_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$subject_name, $class_code, $subject_id]);
    }

    header('Location: subjects.php'); // Redirect to clear the form and prevent resubmission
    exit();
}

// --- DATA FETCHING FOR THE PAGE ---

// Check if we are in "edit mode" (an edit_id is in the URL)
$subject_to_edit = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $subject_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all subjects to display in the list
$subjects_stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_name");
$subjects = $subjects_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - Grading System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="page-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <div class="container">
                <div class="page-header">
                    <h2>Manage Subjects</h2>
                </div>

                <!-- Card for Adding or Editing a Subject -->
                <div class="card">
                    <h3><?php echo $subject_to_edit ? 'Edit Subject' : 'Add New Subject'; ?></h3>
                    <form action="subjects.php" method="POST">
                        <!-- Hidden input to store the ID when editing -->
                        <input type="hidden" name="subject_id" value="<?php echo $subject_to_edit['subject_id'] ?? ''; ?>">
                        
                        <div class="form-group">
                            <label for="subject_name">Subject Name</label>
                            <input type="text" id="subject_name" name="subject_name" placeholder="e.g., Introduction to Programming" value="<?php echo htmlspecialchars($subject_to_edit['subject_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="class_code">Class Code (Optional)</label>
                            <input type="text" id="class_code" name="class_code" placeholder="e.g., CS101" value="<?php echo htmlspecialchars($subject_to_edit['class_code'] ?? ''); ?>">
                        </div>
                        <button type="submit"><?php echo $subject_to_edit ? 'Update Subject' : 'Add Subject'; ?></button>
                    </form>
                </div>

                <!-- Card for Listing Existing Subjects -->
                <div class="card">
                    <h3>My Subjects</h3>
                    <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Subject Name</th>
                                <th>Class Code</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($subjects)): ?>
                                <tr><td colspan="3">No subjects found. Add one using the form above</td></tr>
                            <?php else: ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                        <td><?php echo htmlspecialchars($subject['class_code']); ?></td>
                                        <td class="action-links">
                                            <a href="subjects.php?edit_id=<?php echo $subject['subject_id']; ?>" class="btn-edit">Edit</a>
                                            <a href="view_subject.php?id=<?php echo $subject['subject_id']; ?>" class="btn">View Details</a>
                                            <a href="actions/delete_subject.php?id=<?php echo $subject['subject_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this subject? This will delete all its sections, grades, and components permanently.');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>