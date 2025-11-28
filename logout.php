<?php
session_start();

// Destroy every session variable
session_unset();

// Destroy the session completely
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
