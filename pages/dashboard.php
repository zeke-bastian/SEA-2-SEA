<?php
require_once '../includes/auth.php'; // handles session start

// not logged in — back to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';
require_once '../includes/header.php';
?>

<div class="container mt-5">

    <!-- greet the user by name pulled from session -->
    <h2>Welcome, <?= $_SESSION['user_name'] ?>!</h2>
    <p class="text-muted">What would you like to do today?</p>

    <div class="row mt-4">

        <!-- sell an item -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Sell an Item</h5>
                    <p class="card-text">List something for sale or auction.</p>
                    <a href="create-listing.php" class="btn btn-primary btn-sm">Create Listing</a>
                </div>
            </div>
        </div>

        <!-- view their own listings -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">My Listings</h5>
                    <p class="card-text">View and manage items you are selling.</p>
                    <a href="my-listings.php" class="btn btn-primary btn-sm">View Listings</a>
                </div>
            </div>
        </div>

        <!-- order history -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Order History</h5>
                    <p class="card-text">See everything you have bought.</p>
                    <a href="orders.php" class="btn btn-primary btn-sm">View Orders</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>