<?php
session_start();

// 1. Clear all session variables
$_SESSION = array();

// 2. If you want to be extra thorough, kill the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// 3. Destroy the session
session_destroy();

// 4. Redirect to login
// Since logout.php is in the 'auth' folder, login.php is in the same place.
header("Location: login.php");
exit();
?>