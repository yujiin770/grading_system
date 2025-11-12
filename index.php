<?php
require_once 'config/db_config.php';
session_start();
$page_title = "Dashboard";

// --- FILTERING AND DATA FETCHING ---
$filter_sy = $_GET['school_year'] ?? '';
$filter_sem = $_GET['semester'] ?? '';

// Base query
$sql = "SELECT 
            sub.subject_id, sub.subject_name, sub.class_code,
            sec.section_id, sec.section_name, sec.school_year, sec.semester
        FROM subjects sub
        LEFT JOIN sections sec ON sub.subject_id = sec.subject_id";
        
$where_clauses = [];
$params = [];

// Add filters to the query if they are selected
if (!empty($filter_sy)) {
    $where_clauses[] = "sec.school_year = ?";
    $params[] = $filter_sy;
}
if (!empty($filter_sem)) {
    $where_clauses[] = "sec.semester = ?";
    $params[] = $filter_sem;
}

// If we are filtering, we also need to get the subjects that match
// This is a bit complex: get all sections that match, then find their parent subjects.
if (!empty($where_clauses)) {
    $sql = "SELECT 
                sub.subject_id, sub.subject_name, sub.class_code,
                sec.section_id, sec.section_name, sec.school_year, sec.semester
            FROM subjects sub
            JOIN sections sec ON sub.subject_id = sec.subject_id 
            WHERE " . implode(' AND ', $where_clauses);
}

$sql .= " ORDER BY sub.subject_name, sec.school_year, sec.semester, sec.section_name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group results by subject
$subjects = [];
foreach ($results as $row) {
    if (!isset($subjects[$row['subject_id']])) {
        $subjects[$row['subject_id']] = [
            'id' => $row['subject_id'], 'name' => $row['subject_name'], 'code' => $row['class_code'], 'sections' => []
        ];
    }
    if ($row['section_id']) {
        $subjects[$row['subject_id']]['sections'][] = [
            'id' => $row['section_id'], 'name' => $row['section_name'], 
            'sy' => $row['school_year'], 'sem' => $row['semester']
        ];
    }
}

// Fetch data for the filter dropdowns
$school_years_stmt = $pdo->query("SELECT DISTINCT school_year FROM sections ORDER BY school_year DESC");
$school_years = $school_years_stmt->fetchAll(PDO::FETCH_COLUMN);
$semesters = ['1st Sem', '2nd Sem'];
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Dashboard</h2>
        <p>Your subjects and sections are listed below. Use the filters to find specific classes.</p>
    </div>

    <!-- NEW FILTER BAR -->
    <div class="card">
        <form id="filter-form" method="GET" action="index.php">
            <div class="filter-bar">
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
                    <label for="sem-select">Filter by Semester</label>
                    <select id="sem-select" name="semester">
                        <option value="">All Semesters</option>
                        <?php foreach($semesters as $sem): ?>
                            <option value="<?php echo $sem; ?>" <?php if ($filter_sem == $sem) echo 'selected'; ?>><?php echo htmlspecialchars($sem); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
    
    <!-- NEW DASHBOARD GRID -->
    <div class="dashboard-grid">
        <?php if (empty($subjects)): ?>
            <p>No subjects found<?php if(!empty($filter_sy) || !empty($filter_sem)) echo ' matching your criteria. Go to <a href="subjects.php">Manage Subjects</a> to create sections for this term.'; else echo '. Go to <a href="subjects.php">Manage Subjects</a> to add your first one.'; ?></p>
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
                                <p>No sections found for the selected term.</p>
                            <?php else: ?>
                                <?php foreach ($subject['sections'] as $section): ?>
                                    <a href="view_section.php?id=<?php echo $section['id']; ?>" class="section-link-btn" title="<?php echo htmlspecialchars($section['sy'] . ' - ' . $section['sem']); ?>">
                                        <?php echo htmlspecialchars($section['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- JAVASCRIPT FOR AUTO-SUBMITTING FILTERS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filter-form');
        const selects = filterForm.querySelectorAll('select');

        selects.forEach(select => {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    });
</script>