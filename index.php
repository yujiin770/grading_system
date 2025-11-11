<?php
require_once 'config/db_config.php';
session_start();

$page_title = "Dashboard";

// Fetch all subjects and their associated sections
$sql = "SELECT sub.subject_id, sub.subject_name, sub.class_code, sec.section_id, sec.section_name 
        FROM subjects sub
        LEFT JOIN sections sec ON sub.subject_id = sec.subject_id
        ORDER BY sub.subject_name, sec.section_name";
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group the results by subject
$subjects = [];
foreach ($results as $row) {
    if (!isset($subjects[$row['subject_id']])) {
        $subjects[$row['subject_id']] = [
            'id' => $row['subject_id'], 'name' => $row['subject_name'], 'code' => $row['class_code'], 'sections' => []
        ];
    }
    if ($row['section_id']) {
        $subjects[$row['subject_id']]['sections'][] = ['id' => $row['section_id'], 'name' => $row['section_name']];
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Dashboard</h2>
        <p>All your subjects are listed below. Click a section name to view its gradebook.</p>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <!-- Loop through each subject and display it as a modern card -->
    <?php if (empty($subjects)): ?>
        <div class="card">
            <p>No subjects found. Go to <a href="subjects.php">Manage Subjects</a> to add your first one.</p>
        </div>
    <?php else: ?>
        <?php foreach ($subjects as $subject): ?>
            <div class="subject-card">
                <div class="subject-card-header">
                    <div class="subject-info">
                        <h3><?php echo htmlspecialchars($subject['name']); ?></h3>
                        <p><?php echo htmlspecialchars($subject['code']); ?></p>
                    </div>
                    <div class="subject-actions">
                        <a href="subjects.php?edit_id=<?php echo $subject['id']; ?>" class="btn-edit">Edit</a>
                    </div>
                </div>

                <div class="subject-card-body">
                    <h4>Sections</h4>
                    <div class="sections-list">
                        <?php if (empty($subject['sections'])): ?>
                            <p>No sections have been created for this subject yet.</p>
                        <?php else: ?>
                            <?php foreach ($subject['sections'] as $section): ?>
                                <a href="view_section.php?id=<?php echo $section['id']; ?>" class="section-link-btn">
                                    <?php echo htmlspecialchars($section['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <form action="actions/add_section.php" method="POST" class="add-section-form">
                        <input type="hidden" name="subject_id" value="<?php echo $subject['id']; ?>">
                        <input type="text" name="section_name" placeholder="Add New Section..." required>
                        <button type="submit" title="Add Section">+</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>