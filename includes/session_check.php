<?php
// This script checks if the user is logged in.
// If not, it redirects them to the login page.
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}