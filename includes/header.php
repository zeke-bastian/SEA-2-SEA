<?php
// only start session if one is not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEA-2-SEA</title>
<!-- Brite theme from Bootswatch — local copy -->
<link rel="stylesheet" href="/c2c_platform/assets/css/bootstrap.min.css">
<!-- SEA-2-SEA custom styles on top -->
<link rel="stylesheet" href="/c2c_platform/assets/css/style.css">
</head>
<body>

<!-- inline style forces ocean gradient over Brite theme overrides -->
<nav class="navbar navbar-expand-lg navbar-dark px-4"
     style="background: linear-gradient(135deg, #0d7a6b 0%, #0a1e3d 100%) !important;
            border-bottom: 3px solid #00d4aa;">

    <a class="navbar-brand" href="/c2c_platform/index.php"
       style="color: #00d4aa !important; font-weight: 800;
              text-transform: uppercase; letter-spacing: 1px;">
         SEA-2-SEA
    </a>

    <!-- mobile toggle button -->
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarNav"
            style="border-color: #00d4aa;">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <div class="ms-auto d-flex align-items-center gap-2">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/c2c_platform/index.php"
                   class="btn btn-sm"
                   style="border: 2px solid #00d4aa; color: #00d4aa;">Browse</a>
                <a href="/c2c_platform/pages/profile.php"
                   class="btn btn-sm"
                   style="border: 2px solid #00d4aa; color: #00d4aa;">Profile</a>
                <a href="/c2c_platform/pages/dashboard.php"
                   class="btn btn-sm"
                   style="border: 2px solid #00d4aa; color: #00d4aa;">Dashboard</a>
                <a href="/c2c_platform/pages/logout.php"
                   class="btn btn-sm"
                   style="background:#c0392b; border-color:#c0392b; color:#fff;">Logout</a>
            <?php else: ?>
                <a href="/c2c_platform/pages/login.php"
                   class="btn btn-sm"
                   style="border: 2px solid #00d4aa; color: #00d4aa;">Login</a>
                <a href="/c2c_platform/pages/register.php"
                   class="btn btn-sm"
                   style="background: linear-gradient(135deg, #0d7a6b, #0a4d8c);
                          border: 2px solid #00d4aa; color: #fff; font-weight: 700;">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>