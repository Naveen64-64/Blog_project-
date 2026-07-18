<?php
session_start();
session_destroy();

// Redirect to login page in the same directory
header("Location: login.php");
exit();
?>