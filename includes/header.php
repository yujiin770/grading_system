<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Grading System</title>
    <link rel="stylesheet" href="/grading-system/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
 <script>
        (function(window, location) {
            history.replaceState(null, document.title, location.pathname+"#!/stealingyourhistory");
            history.pushState(null, document.title, location.pathname);
            window.addEventListener("popstate", function() {
              if(location.hash === "#!/stealingyourhistory") {
                history.replaceState(null, document.title, location.pathname);
                setTimeout(function(){
                  location.replace("index.php");
                },0);
              }
            }, false);
        }(window, location));
    </script>

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
            
          