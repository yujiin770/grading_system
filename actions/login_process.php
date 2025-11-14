<?php
session_start();
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
   


    // The original login logic (will not be reached while debugging)
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, start the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        header('Location: ../index.php'); // Redirect to dashboard
        exit();
    } else {
        // Invalid credentials
        $_SESSION['login_error'] = "Invalid username or password.";
        header('Location: ../login.php');
        exit();
    }
}