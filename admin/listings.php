<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';

// moderators can escalate removal, superadmin can outright remove
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_listing'])) {
    $lid = (int)$_POST['listing_id'];

    if ($_SESSION['admin_role'] === 'superadmin') {
        // superadmin removes it outright
        $stmt = $pdo->prepare("UPDATE listings SET status = 'removed' WHERE id = :id");
        $stmt->execute([':id' => $lid]);
        $success = "Listing removed.";
    } else {
        $error = "You do not have permission to remove listings directly.";
    }
}

// fetch all listings with seller name
$listings = $pdo->query("SELECT listings.*, users.name AS seller_name, users.surname AS seller_surname
                          FROM listings
                          JOIN users ON listings.seller_id = users.id
                          ORDER BY listings.created_at DESC")->fetchAll();
?>

<div class="container mt-5">
    <h2 class="mb-4">All Listings</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Seller</th>
                <th>Price</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listings as $listing): ?>
                <tr>
                    <td><?= $listing['title'] ?></td>
                    <td><?= $listing['seller_name'] . ' ' . $listing['seller_surname'] ?></td>
                    <td>R <?= number_format($listing['price'], 2) ?></td>
                    <td><?= ucfirst($listing['status']) ?></td>
                    <td><?= date('d M Y', strtotime($listing['created_at'])) ?></td>
                    <td>
                        <?php if ($listing['status'] === 'active'): ?>
                            <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                                    <button type="submit" name="remove_listing" 
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Remove this listing?')">
                                        Remove
                                    </button>
                                </form>
                            <?php else: ?>
                                <!-- moderators see this but cannot remove directly -->
                                <span class="text-muted">No direct action</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted"><?= ucfirst($listing['status']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>