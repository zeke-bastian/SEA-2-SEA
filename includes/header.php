<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEA-2-SEA Marketplace</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">

        <a class="navbar-brand" href="/index.php">
            <!-- logo — onerror hides it if file not found yet -->
            <img src="/assets/images/logo.png" alt="SEA-2-SEA"
                 onerror="this.style.display='none'">
             SEA-2-SEA
        </a>

        <!-- mobile hamburger -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <div class="ms-auto d-flex flex-wrap align-items-center gap-2 py-2 py-lg-0">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="/index.php"
                       class="btn btn-sm btn-outline-primary">Browse</a>
                    <a href="/pages/create-listing.php"
                       class="btn btn-sm btn-success">+ Sell</a>
                    <a href="/pages/profile.php"
                       class="btn btn-sm btn-outline-primary">Profile</a>
                    <a href="/pages/dashboard.php"
                       class="btn btn-sm btn-outline-primary">Dashboard</a>
                    <a href="/pages/logout.php"
                       class="btn btn-sm btn-danger">Logout</a>
                <?php else: ?>
                    <a href="/pages/login.php"
                       class="btn btn-sm btn-outline-primary">Login</a>
                    <a href="/pages/register.php"
                       class="btn btn-sm btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</nav>