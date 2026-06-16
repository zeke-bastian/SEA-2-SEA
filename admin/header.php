<?php
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEA-2-SEA Admin</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark"
     style="background: linear-gradient(135deg, #071428 0%, #0d2137 100%) !important;
            border-bottom: 2px solid var(--aqua);">
    <div class="container-fluid">

        <a class="navbar-brand" href="/admin/index.php">
            <img src="/assets/images/logo.png" alt="Logo" height="32"
                 onerror="this.style.display='none'">
            Admin Panel
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNav">

            <!-- nav links to each admin section -->
            <ul class="navbar-nav me-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link" href="/admin/index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/listings.php">Listings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/flags.php">Flags</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/orders.php">Orders</a>
                </li>
                <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="/admin/manage-admins.php">
                           Manage Admins
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <span style="color:#e0f0ee; font-size:0.85rem;">
                    <?= htmlspecialchars($_SESSION['admin_name']) ?>
                    <span class="badge ms-1"
                          style="background:rgba(0,212,170,0.12); color:var(--aqua);
                                 border:1px solid rgba(0,212,170,0.35); font-size:0.68rem;">
                        <?= ucfirst($_SESSION['admin_role']) ?>
                    </span>
                </span>
                <a href="/admin/logout.php"
                   class="btn btn-sm btn-danger">Logout</a>
            </div>

        </div>
    </div>
</nav>