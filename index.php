<?php
require_once 'includes/auth.php'; // session start — must come first
require_once 'includes/db.php';
require_once 'includes/header.php';

// fetch all active listings newest first
$stmt = $pdo->query("SELECT * FROM listings WHERE status = 'active' ORDER BY created_at DESC");
$listings = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h2 class="mb-4">Latest Listings</h2>
    <div class="row">

        <?php if (count($listings) > 0): ?>
            <?php foreach ($listings as $listing): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">

                        <!-- show image if uploaded, grey box if not -->
                        <?php if ($listing['image']): ?>
                            <img src="/c2c_platform/assets/uploads/<?= $listing['image'] ?>"
                                 class="card-img-top"
                                 style="height:200px; object-fit:cover;">
                        <?php else: ?>
                            <div class="bg-secondary text-white text-center py-5">No Image</div>
                        <?php endif; ?>

                        <div class="card-body">
                            <!-- listing title -->
                            <h5 class="card-title"><?= $listing['title'] ?></h5>

                            <!-- price formatted to 2 decimal places -->
                            <p class="card-text text-success fw-bold">
                                R <?= number_format($listing['price'], 2) ?>
                            </p>

                            <!-- view button passes listing id through URL -->
                            <a href="pages/listing.php?id=<?= $listing['id'] ?>"
                               class="btn btn-primary btn-sm">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <!-- nothing for sale yet -->
            <p class="text-muted">No listings yet. Be the first to sell something.</p>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>