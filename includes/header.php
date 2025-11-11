<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Grading System</title>
     <link rel="stylesheet" href="/grading-system/public/css/style.css">
</head>
<body>
    <div class="page-wrapper">
        <?php include 'includes/sidebar.php'; ?>

           <!-- Mobile Header with Hamburger Menu -->
        <div class="mobile-header">
            <button id="sidebar-toggle" class="sidebar-toggle-btn">
                &#9776; <!-- This is the hamburger icon character -->
            </button>
            <h3><?php echo $page_title ?? 'Dashboard'; ?></h3>
        </div>

        <main class="main-content">
            <!-- ALL YOUR PAGE CONTENT (containers, cards, forms, tables) GOES HERE -->
            
          