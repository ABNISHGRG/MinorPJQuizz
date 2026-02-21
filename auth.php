<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    // Member Logic
    $user_name = $_SESSION['user_name'];
    $user_role = $_SESSION['user_role']; 
    $is_guest = false;
} else {
    // Guest Logic
    if (!isset($_SESSION['guest_id'])) {
        $_SESSION['guest_id'] = "Guest" . rand(100, 999);
    }
    $user_name = $_SESSION['guest_id'];
    $user_role = 'guest'; 
    $is_guest = true;
}
?>