<?php
require_once '../includes/auth.php';

// session check — no sneaking in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';

// fetch only this user's listings using session id
$stmt = $pdo->prepare("SELECT * FROM listings WHERE seller_id = :seller_id ORDER BY created_at DESC");
$stmt->execute([':seller_id' => $_SESSION['user_id']]);
$listings = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">My Listings</h2>

    <?php if (count($listings) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- loop through every listing this user owns -->
                <?php foreach ($listings as $listing): ?>
                    <tr>
                        <td><?= $listing['title'] ?></td>
                        <td>R <?= number_format($listing['price'], 2) ?></td>
                        <td><?= $listing['category'] ?></td>
                        <td><?= $listing['status'] ?></td>
                        <!-- format date from MySQL timestamp to readable format -->
                        <td><?= date('d M Y', strtotime($listing['created_at'])) ?></td>
                        <td>
                            <a href="listing.php?id=<?= $listing['id'] ?>" 
                               class="btn btn-sm btn-primary">View</a>
                               <a href="manage-listing.php?id=<?= $listing['id'] ?>" 
   class="btn btn-sm btn-warning">Manage</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- no listings yet — nudge them to create one -->
        <p class="text-muted">You have not listed anything yet.</p>
        <a href="create-listing.php" class="btn btn-primary">Create Listing</a>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>