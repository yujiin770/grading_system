<?php
// Start the session at the very top to handle alert messages
session_start();

require_once 'config/db_config.php';

// Validate the subject ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php'); // Redirect to dashboard if no ID
    exit();
}
$subject_id = $_GET['id'];

// --- DATA FETCHING ---

// Fetch subject details, now including the new term weight columns
$subject_stmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_id = ?");
$subject_stmt->execute([$subject_id]);
$subject = $subject_stmt->fetch(PDO::FETCH_ASSOC);

// Handle case where subject is not found
if (!$subject) {
    $page_title = "Error";
    include 'includes/header.php';
    echo '<div class="container"><div class="alert alert-danger">Subject not found.</div></div>';
    include 'includes/footer.php';
    die();
}

$page_title = "Manage Subject: " . htmlspecialchars($subject['subject_name']);

// Fetch sections for this subject
$sections_stmt = $pdo->prepare("SELECT * FROM sections WHERE subject_id = ? ORDER BY section_name");
$sections_stmt->execute([$subject_id]);
$sections = $sections_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all grading components for this subject
$components_stmt = $pdo->prepare("SELECT * FROM grading_components WHERE subject_id = ?");
$components_stmt->execute([$subject_id]);
$components = $components_stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Manage Subject: <?php echo htmlspecialchars($subject['subject_name']); ?></h2>
        <p>Use the cards below to manage sections, term weights, and grading criteria for this subject.</p>
    </div>

    <!-- Display any success or error messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <!-- Card for Managing Sections -->
    <div class="card">
        <h3>Manage Sections</h3>
        <form action="actions/add_section.php" method="POST">
            <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
            <div class="form-group">
                <label for="section_name">New Section Name</label>
                <input type="text" name="section_name" placeholder="e.g., BSIT 2A" required>
            </div>
            <button type="submit">Create Section</button>
        </form>
        <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">
        <h4>Existing Sections</h4>
        <?php if (empty($sections)): ?>
            <p>No sections have been created for this subject yet.</p>
        <?php else: ?>
            <ul>
                <?php foreach($sections as $section): ?>
                    <li style="margin-bottom: 10px;">
                        <?php echo htmlspecialchars($section['section_name']); ?>
                        <a href="view_section.php?id=<?php echo $section['section_id']; ?>" class="btn" style="margin-left: 15px; padding: 5px 10px; font-size: 0.9rem;">View & Grade Section</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    
    <!-- NEW CARD for Term Weighting -->
    <div class="card">
        <h3>Term Weighting</h3>
        <p>Define how much each term contributes to the final semester grade. The total must be exactly 100%.</p>
        <form action="actions/save_term_weights.php" method="POST">
            <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
            <div class="form-group">
                <label>PRE-LIM Weight (%)</label>
                <input type="number" step="0.01" min="0" name="prelim_weight" value="<?php echo htmlspecialchars($subject['prelim_weight']); ?>" required>
            </div>
            <div class="form-group">
                <label>MIDTERM Weight (%)</label>
                <input type="number" step="0.01" min="0" name="midterm_weight" value="<?php echo htmlspecialchars($subject['midterm_weight']); ?>" required>
            </div>
            <div class="form-group">
                <label>PRE-FINALS Weight (%)</label>
                <input type="number" step="0.01" min="0" name="prefinals_weight" value="<?php echo htmlspecialchars($subject['prefinals_weight']); ?>" required>
            </div>
            <div class="form-group">
                <label>FINALS Weight (%)</label>
                <input type="number" step="0.01" min="0" name="finals_weight" value="<?php echo htmlspecialchars($subject['finals_weight']); ?>" required>
            </div>
            <button type="submit">Save Term Weights</button>
        </form>
    </div>

    <!-- Card for Grading Criteria -->
    <div class="card">
        <h3>Grading Criteria (for all sections)</h3>
        <form action="actions/add_component.php" method="POST">
            <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
            <div class="form-group">
                <label for="term">Term</label>
                <select name="term" id="term" required>
                    <option value="">-- Select a Term --</option>
                    <option value="PRE-LIM">PRE-LIM</option>
                    <option value="MIDTERM">MIDTERM</option>
                    <option value="PRE-FINALS">PRE-FINALS</option>
                    <option value="FINALS">FINALS</option>
                </select>
            </div>
            <div class="form-group">
                <label for="component_name">Component Name</label>
                <input type="text" name="component_name" required placeholder="e.g., Chapter Quiz">
            </div>
            <div class="form-group">
                <label for="max_score">Max Possible Score</label>
                <input type="number" name="max_score" required value="100" min="1">
            </div>
            <div class="form-group">
                <label for="weight">Weight (within this term)</label>
                <input type="number" name="weight" required step="0.01" min="0" max="100" placeholder="e.g., 30 for 30%">
            </div>
            <button type="submit">Add Component</button>
        </form>
        <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Term</th><th>Component</th><th>Weight</th><th>Max Score</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grouped_components = [];
                    foreach ($components as $component) { $grouped_components[$component['term']][] = $component; }
                    $terms_order = ['PRE-LIM', 'MIDTERM', 'PRE-FINALS', 'FINALS'];
                    ?>
                    <?php foreach ($terms_order as $term): ?>
                        <?php if (!empty($grouped_components[$term])): ?>
                            <?php foreach ($grouped_components[$term] as $component): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($component['term']); ?></td>
                                <td><?php echo htmlspecialchars($component['component_name']); ?></td>
                                <td><?php echo htmlspecialchars($component['weight']); ?>%</td>
                                <td><?php echo htmlspecialchars($component['max_score']); ?></td>
                                <td class="action-links">
                                    <a href="actions/delete_component.php?id=<?php echo $component['component_id']; ?>&subject_id=<?php echo $subject_id; ?>" onclick="return confirm('Are you sure?');" class="btn-delete">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>