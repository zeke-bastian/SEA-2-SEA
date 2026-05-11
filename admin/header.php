<?php
// check admin session — every admin page must include this at the top
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>C2C Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger px-4">
    <a class="navbar-brand" href="/c2c_platform/admin/index.php">C2C Admin</a>
    <div class="ms-auto">
        <!-- show admin name and role in navbar -->
        <span class="text-white me-3">
            <?= $_SESSION['admin_name'] ?> 
            <span class="badge bg-dark"><?= $_SESSION['admin_role'] ?></span>
        </span>
        <a href="/c2c_platform/admin/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>