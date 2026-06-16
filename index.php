<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM listings WHERE status = 'active' ORDER BY created_at DESC");
$listings = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM listings
                            WHERE category != '' AND category IS NOT NULL")->fetchAll();
?>

<!-- hero -->
<div class="sea-hero">
    <div class="container">
        <h1>SEA-2-SEA Marketplace</h1>
        <p>Buy and sell anything from coast to coast.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="/pages/register.php" class="btn btn-primary btn-lg me-2">
                Get Started
            </a>
            <a href="pages/login.php" class="btn btn-outline-primary btn-lg">
                Login
            </a>
        <?php else: ?>
            <a href="/pages/create-listing.php" class="btn btn-primary btn-lg">
                + List an Item
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="section-title mb-0">Latest Listings</h2>
        <span class="badge"
              style="background:rgba(0,212,170,0.12); color:var(--aqua);
                     border:1px solid rgba(0,212,170,0.3); font-size:0.8rem;">
            <?= count($listings) ?> active
        </span>
    </div>

    <!-- search and category filter -->
    <div class="search-bar">
        <input type="text" id="search-input" class="form-control mb-2"
               placeholder="Search listings...">
        <select id="category-filter" class="form-select">
            <option value="all">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['category']) ?>">
                    <?= htmlspecialchars($cat['category']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row" id="listings-row">
        <?php if (count($listings) > 0): ?>
            <?php foreach ($listings as $listing): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="card h-100 listing-card"
                         data-title="<?= htmlspecialchars($listing['title']) ?>"
                         data-category="<?= htmlspecialchars($listing['category']) ?>">

                        <?php if ($listing['image']): ?>
                            <img src="/assets/uploads/<?= $listing['image'] ?>"
                                 class="card-img-top">
                        <?php else: ?>
                            <div class="no-image-placeholder">🌊</div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <div class="mb-auto">
                                <span class="badge mb-2"
                                      style="background:rgba(0,212,170,0.1); color:var(--aqua);
                                             border:1px solid rgba(0,212,170,0.25);
                                             font-size:0.7rem; font-weight:600;">
                                    <?= htmlspecialchars($listing['category']) ?: 'General' ?>
                                </span>
                                <h5 class="card-title">
                                    <?= htmlspecialchars($listing['title']) ?>
                                </h5>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold fs-5" style="color:var(--aqua);">
                                    R <?= number_format($listing['price'], 2) ?>
                                </span>
                                <a href="/pages/listing.php?id=<?= $listing['id'] ?>"
                                   class="btn btn-primary btn-sm">
                                    View →
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div style="font-size:4rem; opacity:0.25;">🌊</div>
                <p class="text-muted mt-3 mb-4">
                    No listings yet. Be the first to sell something.
                </p>
                <a href="pages/create-listing.php" class="btn btn-primary">
                    Create First Listing
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>