<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Store intended URL for redirect after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login/login.php");
    exit();
}

// Optional: Check session age for security
$max_session_age = 3600; // 1 hour
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $max_session_age)) {
    session_destroy();
    header("Location: ../login/login.php?expired=1");
    exit();
}
?>