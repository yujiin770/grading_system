<?php
require_once '../includes/session_check.php';
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $new_username = trim($_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];
    
    // Update username
    if ($new_username != $_SESSION['username']) {
        // Check if new username is already taken
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$new_username, $user_id]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "Username '{$new_username}' is already taken.";
            header('Location: ../account.php');
            exit();
        }
        
        $update_stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $update_stmt->execute([$new_username, $user_id]);
        $_SESSION['username'] = $new_username; // Update session
    }

    // Update password if fields are not empty
    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $_SESSION['error_message'] = "New passwords do not match.";
            header('Location: ../account.php');
            exit();
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->execute([$hashed_password, $user_id]);
    }

    $_SESSION['success_message'] = "Account updated successfully!";
}

header('Location: ../account.php');
exit();