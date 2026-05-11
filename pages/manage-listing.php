<?php
require_once '../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// fetch listing — boot anyone who does not own it
$stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :id AND seller_id = :seller_id");
$stmt->execute([':id' => $id, ':seller_id' => $_SESSION['user_id']]);
$listing = $stmt->fetch();

if (!$listing) {
    header("Location: my-listings.php");
    exit();
}

// accept a bid — creates order and marks listing sold
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_bid'])) {
    $bid_id = (int)$_POST['bid_id'];
    $bidder_id = (int)$_POST['bidder_id'];
    $amount = $_POST['amount'];

    $orderStmt = $pdo->prepare("INSERT INTO orders (listing_id, buyer_id, seller_id, amount) 
                                 VALUES (:listing_id, :buyer_id, :seller_id, :amount)");
    $orderStmt->execute([
        ':listing_id' => $id,
        ':buyer_id' => $bidder_id,
        ':seller_id' => $_SESSION['user_id'],
        ':amount' => $amount
    ]);

    // mark as sold — disappears from homepage automatically
    $soldStmt = $pdo->prepare("UPDATE listings SET status = 'sold' WHERE id = :id");
    $soldStmt->execute([':id' => $id]);

    $success = "Bid accepted. Order created successfully.";
}

// remove listing — sets status to removed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_listing'])) {
    $removeStmt = $pdo->prepare("UPDATE listings SET status = 'removed' WHERE id = :id");
    $removeStmt->execute([':id' => $id]);
    header("Location: my-listings.php");
    exit();
}

// fetch all bids highest first
$bidsStmt = $pdo->prepare("SELECT bids.*, users.name, users.surname 
                            FROM bids 
                            JOIN users ON bids.bidder_id = users.id 
                            WHERE bids.listing_id = :id 
                            ORDER BY bids.amount DESC");
$bidsStmt->execute([':id' => $id]);
$bids = $bidsStmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-2">Managing: <?= $listing['title'] ?></h2>
    <p class="text-muted">Status: <?= ucfirst($listing['status']) ?></p>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <h4 class="mt-4">Bids Received</h4>

    <?php if (count($bids) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Bidder</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bids as $bid): ?>
                    <tr>
                        <td><?= $bid['name'] . ' ' . $bid['surname'] ?></td>
                        <td>R <?= number_format($bid['amount'], 2) ?></td>
                        <td><?= date('d M Y', strtotime($bid['created_at'])) ?></td>
                        <td>
                            <?php if ($listing['status'] === 'active'): ?>
                                <form method="POST" action="">
                                    <!-- hidden fields pass bid data to PHP on submit -->
                                    <input type="hidden" name="bid_id" value="<?= $bid['id'] ?>">
                                    <input type="hidden" name="bidder_id" value="<?= $bid['bidder_id'] ?>">
                                    <input type="hidden" name="amount" value="<?= $bid['amount'] ?>">
                                    <button type="submit" name="accept_bid" 
                                            class="btn btn-success btn-sm">Accept</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Listing closed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">No bids yet.</p>
    <?php endif; ?>

    <!-- remove listing — only available while active -->
    <?php if ($listing['status'] === 'active'): ?>
        <form method="POST" action="" class="mt-4">
            <button type="submit" name="remove_listing" class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to remove this listing?')">
                Remove Listing
            </button>
        </form>
    <?php endif; ?>

</div>

<?php require_once '../includes/footer.php'; ?>