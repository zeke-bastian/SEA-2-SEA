<?php
require_once '../includes/auth.php'; // session start
require_once '../includes/db.php';

// cast to int to prevent any funny URL manipulation
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// fetch listing and seller name in one query using JOIN
$stmt = $pdo->prepare("SELECT listings.*, users.name AS seller_name, users.surname AS seller_surname 
                        FROM listings 
                        JOIN users ON listings.seller_id = users.id 
                        WHERE listings.id = :id");
$stmt->execute([':id' => $id]);
$listing = $stmt->fetch();

// no listing found — back to homepage, nothing to see here
if (!$listing) {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">

        <!-- left: listing image -->
        <div class="col-md-6">
            <?php if ($listing['image']): ?>
                <img src="/c2c_platform/assets/uploads/<?= $listing['image'] ?>"
                     class="img-fluid rounded">
            <?php else: ?>
                <div class="bg-secondary text-white text-center py-5 rounded">No Image</div>
            <?php endif; ?>
        </div>

        <!-- right: listing details -->
        <div class="col-md-6">
            <h2><?= $listing['title'] ?></h2>
            <h4 class="text-success">R <?= number_format($listing['price'], 2) ?></h4>
            <p class="text-muted">Category: <?= $listing['category'] ?></p>
            <p><?= $listing['description'] ?></p>
            <p class="text-muted">
                Sold by: <?= $listing['seller_name'] . ' ' . $listing['seller_surname'] ?>
            </p>

            <!-- bid button only shows to logged in users -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" class="btn btn-primary">Place Bid</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-primary">Login to Bid</a>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>