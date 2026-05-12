<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';

// fetch platform stats for dashboard cards
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalListings = $pdo->query("SELECT COUNT(*) FROM listings")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingFlags = $pdo->query("SELECT COUNT(*) FROM flags WHERE status = 'pending'")->fetchColumn();
?>

<div class="container mt-5">
    <h2 class="mb-4">Admin Dashboard</h2>

    <!-- stat cards — quick overview of platform health -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2><?= $totalUsers ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Listings</h5>
                    <h2><?= $totalListings ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <h2><?= $totalOrders ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Pending Flags</h5>
                    <h2><?= $pendingFlags ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- navigation cards to each admin section -->
    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5>Users</h5>
                    <p class="text-muted">View and manage all registered users.</p>
                    <a href="users.php" class="btn btn-primary btn-sm">Manage Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5>Listings</h5>
                    <p class="text-muted">View and remove listings from the platform.</p>
                    <a href="listings.php" class="btn btn-primary btn-sm">Manage Listings</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5>Flags</h5>
                    <p class="text-muted">Review reported listings and escalate or dismiss.</p>
                    <a href="flags.php" class="btn btn-primary btn-sm">Review Flags</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5>Orders</h5>
                    <p class="text-muted">View all transactions across the platform.</p>
                    <a href="orders.php" class="btn btn-primary btn-sm">View Orders</a>
                </div>
            </div>
        </div>

        <!-- superadmin only card — moderators cannot see this -->
        <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-danger">
                <div class="card-body">
                    <h5>Manage Admins</h5>
                    <p class="text-muted">Create and remove moderator accounts.</p>
                    <a href="manage-admins.php" class="btn btn-danger btn-sm">Manage Admins</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>