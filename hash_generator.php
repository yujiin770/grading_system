<?php
// This script will generate a new, correct password hash for your system.

$passwordToHash = 'password';

// Generate the hash using PHP's standard, secure method
$hashedPassword = password_hash($passwordToHash, PASSWORD_DEFAULT);

echo "<h1>Your New Password Hash</h1>";
echo "<p>The password to hash was: '<strong>" . htmlspecialchars($passwordToHash) . "</strong>'</p>";
echo "<p>Copy the entire line below and use it in your SQL command:</p>";
echo "<hr>";
echo "<strong>" . htmlspecialchars($hashedPassword) . "</strong>";
echo "<hr>";
?>