<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/header.php';

// fetch all active listings newest first
$stmt = $pdo->query("SELECT * FROM listings WHERE status = 'active' ORDER BY created_at DESC");
$listings = $stmt->fetchAll();

// fetch distinct categories for filter dropdown
$categories = $pdo->query("SELECT DISTINCT category FROM listings WHERE category != ''")->fetchAll();
?>

<div class="container mt-5">
    <h2 class="mb-4">Latest Listings</h2>

    <!-- search and filter bar — jQuery handles filtering client side -->
    <div class="search-bar">
        <input type="text" id="search-input" class="form-control mb-2" 
               placeholder="Search listings...">
        <select id="category-filter" class="form-select">
            <option value="all">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category'] ?>"><?= $cat['category'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row">
        <?php if (count($listings) > 0): ?>
            <?php foreach ($listings as $listing): ?>
                <div class="col-md-4 mb-4">

                    <!-- data attributes used by jQuery to filter without page reload -->
                    <div class="card h-100 listing-card"
                         data-title="<?= $listing['title'] ?>"
                         data-category="<?= $listing['category'] ?>">

                        <?php if ($listing['image']): ?>
                            <img src="/c2c_platform/assets/uploads/<?= $listing['image'] ?>"
                                 class="card-img-top">
                        <?php else: ?>
                            <div class="bg-secondary text-white text-center py-5">No Image</div>
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title"><?= $listing['title'] ?></h5>
                            <p class="text-muted small"><?= $listing['category'] ?></p>
                            <p class="card-text text-success fw-bold">
                                R <?= number_format($listing['price'], 2) ?>
                            </p>
                            <a href="pages/listing.php?id=<?= $listing['id'] ?>"
                               class="btn btn-primary btn-sm">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No listings yet. Be the first to sell something.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>