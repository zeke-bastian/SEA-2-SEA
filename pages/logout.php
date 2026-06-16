<?php
require_once '../includes/auth.php'; // start session so I can destroy it

// destroys all session data
session_destroy();

// redirect to homepage after logout
header("Location: /index.php");
exit();
?>