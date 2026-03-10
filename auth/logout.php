<?php
/**
 * Logout Handler
 * DailyBrew - Clears session and redirects to login
 */

session_start();
session_unset();
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
?>

<!-- Fallback if PHP redirect doesn't work -->
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="0;url=login.php">
</head>
<body>
    <p>Logging out... <a href="login.php">Click here</a> if not redirected.</p>
</body>
</html>

<script>
    // Clear localStorage as fallback
    localStorage.removeItem('dailybrew_current_user');
    window.location.href = 'login.php';
</script>

