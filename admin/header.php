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
    <title>SEA-2-SEA Admin</title>
<!-- Brite theme from Bootswatch — local copy -->
<link rel="stylesheet" href="/c2c_platform/assets/css/bootstrap.min.css">
<!-- SEA-2-SEA custom styles on top -->
<link rel="stylesheet" href="/c2c_platform/assets/css/style.css">
</head>
<body>

<!-- admin navbar — darker navy to distinguish from customer site -->
<nav class="navbar navbar-expand-lg navbar-dark px-4"
     style="background: linear-gradient(135deg, #0a1e3d 0%, #071428 100%) !important;
            border-bottom: 3px solid #00d4aa;">

    <a class="navbar-brand" href="/c2c_platform/admin/index.php"
       style="color: #00d4aa !important; font-weight: 800;
              text-transform: uppercase; letter-spacing: 1px;">
        SEA-2-SEA Admin
    </a>

    <div class="ms-auto d-flex align-items-center gap-3">
        <span style="color: #e0f0ee; font-size: 0.9rem;">
            <?= $_SESSION['admin_name'] ?>
            <span class="badge ms-1"
                  style="background: linear-gradient(135deg, #0d7a6b, #0a4d8c);
                         border: 1px solid #00d4aa; font-size: 0.7rem;">
                <?= ucfirst($_SESSION['admin_role']) ?>
            </span>
        </span>
        <a href="/c2c_platform/admin/logout.php"
           class="btn btn-sm"
           style="border: 2px solid #c0392b; color: #c0392b;">
            Logout
        </a>
    </div>
</nav>