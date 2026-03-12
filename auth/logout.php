<?php
/**
 * Logout — DailyBrew
 * Clears PHP session and bounces to login; JS clears localStorage as fallback
 */
session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
?>
<!DOCTYPE html>
<html>
<head><meta http-equiv="refresh" content="0;url=login.php"></head>
<body>
  <script>
    localStorage.removeItem('dailybrew_current_user');
    window.location.href = 'login.php';
  </script>
  <p>Signing you out… <a href="login.php">click here</a> if not redirected.</p>
</body>
</html>
