<?php
require_once '../includes/auth.php';

// destroy session and send back to admin login
session_destroy();
header("Location: login.php");
exit();
?>