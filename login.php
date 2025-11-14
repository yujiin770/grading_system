<?php
session_start();

// Server-side headers to prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


// If the user is already logged in, redirect them to the dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Check if there's a login error message
$error_message = '';
if (isset($_SESSION['login_error'])) {
    $error_message = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Clear the message after displaying
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Grading System</title>
    <link rel="stylesheet" href="public/css/login.css">
    
</head>
<body>
     <div class="login-wrapper">
        <!-- Left Branding Panel -->
        <div class="login-branding">
            <div class="branding-content">
                <h1>Grading System</h1>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="login-form">
            <h2>Sign In</h2>

            <?php if ($error_message): ?>
                <div class="alert"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="actions/login_process.php" method="POST">
                <div class="form-group">
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
               <div class="login-action">
                <button type="submit" class="login-button">Login </button>
            </div>
            </form>
        </div>
    </div>

    <script>
        // This script checks if the user navigated to this page via the back button.
        (function () {
            window.onpageshow = function(event) {
                if (event.persisted) {
                    // If the page is loaded from the fast back/forward cache,
                    // it means the user clicked "Back". We clear the form and reload.
                    document.querySelector('form').reset();
                    window.location.reload();
                }
            };
        })();
    </script>
</body>
</html>