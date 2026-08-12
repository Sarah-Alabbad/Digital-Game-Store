<!-- Log-out - Developed by Zahra AL-mari -->

<?php
// Start the session so PHP can access the current logged-in user's session
session_start();

// Destroy the session
// This removes the saved login information like user_id, username, and role
session_destroy();

// After logging out, send the user back to the login page
header("Location: login.php");

// Stop the script after redirecting
exit();
?>