<?php
// You can add PHP logic here later if you want to display stats, like total students.
require_once 'config/db_config.php';

// Example: Count total students
$student_count_stmt = $pdo->query("SELECT COUNT(*) FROM students");
$student_count = $student_count_stmt->fetchColumn();

// Example: Count total subjects
$subject_count_stmt = $pdo->query("SELECT COUNT(*) FROM subjects");
$subject_count = $subject_count_stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Grading System</title>
    <link rel="stylesheet" href="public/css/style.css">
    <!-- Add some inline styles for the dashboard stats -->
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid var(--border-color);
        }
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        .stat-card .label {
            font-size: 1rem;
            color: #555;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <div class="container">
                <div class="page-header">
                    <h2>Dashboard</h2>
                </div>

                <!-- Welcome Card -->
                <div class="card">
                    <h3>Welcome to Your Grading System!</h3>
                    <p>This is your central dashboard. Use the sidebar on the left to navigate through the system.</p>
                    <ul>
                        <li><strong>Manage Subjects:</strong> Create and organize your subjects and their grading criteria.</li>
                        <li><strong>Manage Student List:</strong> Maintain a master list of all your students.</li>
                    </ul>
                </div>

                <!-- Statistics Card -->
                <div class="card">
                    <h3>System Overview</h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="number"><?php echo $student_count; ?></div>
                            <div class="label">Total Students</div>
                        </div>
                        <div class="stat-card">
                            <div class="number"><?php echo $subject_count; ?></div>
                            <div class="label">Total Subjects</div>
                        </div>
                        <!-- You can add more stats here later, like "Total Sections" -->
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>