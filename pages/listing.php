<?php
require_once '../includes/auth.php'; // session start
require_once '../includes/db.php';

// cast to int — prevents URL tampering
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// fetch listing with seller name in one query
$stmt = $pdo->prepare("SELECT listings.*, users.name AS seller_name, users.surname AS seller_surname 
                        FROM listings 
                        JOIN users ON listings.seller_id = users.id 
                        WHERE listings.id = :id");
$stmt->execute([':id' => $id]);
$listing = $stmt->fetch();

// listing not found — nothing to see here
if (!$listing) {
    header("Location: ../index.php");
    exit();
}

// handle bid submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid'])) {
    $amount = $_POST['amount'];

    // get the current highest bid on this listing
    $highStmt = $pdo->prepare("SELECT MAX(amount) AS highest FROM bids WHERE listing_id = :id");
    $highStmt->execute([':id' => $id]);
    $highest = $highStmt->fetch()['highest'];

    // bid must beat current highest and meet minimum listing price
    if ($amount <= $highest) {
        $bidError = "Your bid must be higher than the current highest bid of R " . number_format($highest, 2);
    } elseif ($amount < $listing['price']) {
        $bidError = "Your bid must be at least the listing price of R " . number_format($listing['price'], 2);
    } else {
        $bidStmt = $pdo->prepare("INSERT INTO bids (listing_id, bidder_id, amount) 
                                  VALUES (:listing_id, :bidder_id, :amount)");
        $bidStmt->execute([
            ':listing_id' => $id,
            ':bidder_id' => $_SESSION['user_id'],
            ':amount' => $amount
        ]);
        $bidSuccess = "Bid placed successfully.";
    }
}

// handle listing report
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flag'])) {
    $reason = trim(htmlspecialchars($_POST['reason']));

    // insert flag — admin will review this
    $flagStmt = $pdo->prepare("INSERT INTO flags (listing_id, reported_by, reason) 
                                VALUES (:listing_id, :reported_by, :reason)");
    $flagStmt->execute([
        ':listing_id' => $id,
        ':reported_by' => $_SESSION['user_id'],
        ':reason' => $reason
    ]);
    $flagSuccess = "Listing reported successfully.";
}

// fetch all bids for this listing ordered by highest amount
$bidsStmt = $pdo->prepare("SELECT bids.amount, users.name, bids.created_at 
                            FROM bids 
                            JOIN users ON bids.bidder_id = users.id 
                            WHERE bids.listing_id = :id 
                            ORDER BY bids.amount DESC");
$bidsStmt->execute([':id' => $id]);
$bids = $bidsStmt->fetchAll();

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

        <!-- right: listing details and bidding -->
        <div class="col-md-6">
            <h2><?= $listing['title'] ?></h2>
            <h4 class="text-success">R <?= number_format($listing['price'], 2) ?></h4>
            <p class="text-muted">Category: <?= $listing['category'] ?></p>
            <p><?= $listing['description'] ?></p>
            <p class="text-muted">
                Sold by: <?= $listing['seller_name'] . ' ' . $listing['seller_surname'] ?>
            </p>

            <!-- bid success or error feedback -->
            <?php if (isset($bidSuccess)): ?>
                <div class="alert alert-success"><?= $bidSuccess ?></div>
            <?php endif; ?>
            <?php if (isset($bidError)): ?>
                <div class="alert alert-danger"><?= $bidError ?></div>
            <?php endif; ?>
            <!-- buy now button — goes straight to checkout -->
<?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $listing['seller_id']): ?>
    <a href="checkout.php?listing_id=<?= $listing['id'] ?>" 
       class="btn btn-success mt-2 mb-3">Buy Now — R <?= number_format($listing['price'], 2) ?></a>
<?php endif; ?>

            <!-- bid form — hidden from seller, they cannot bid on their own listing -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $listing['seller_id']): ?>
                <form method="POST" action="" class="mt-3">
                    <div class="input-group">
                        <span class="input-group-text">R</span>
                        <input type="number" name="amount" step="0.01"
                               class="form-control" placeholder="Enter bid amount" required>
                        <button type="submit" name="bid" class="btn btn-primary">Place Bid</button>
                    </div>
                </form>
            <?php elseif (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn btn-outline-primary mt-3">Login to Bid</a>
            <?php endif; ?>

            <!-- bids table — highest first -->
            <?php if (count($bids) > 0): ?>
                <h5 class="mt-4">Current Bids</h5>
                <table class="table table-sm table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Bidder</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bids as $bid): ?>
                            <tr>
                                <td><?= $bid['name'] ?></td>
                                <td>R <?= number_format($bid['amount'], 2) ?></td>
                                <td><?= date('d M Y', strtotime($bid['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- report section — hidden from seller and logged out users -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $listing['seller_id']): ?>
                <div class="mt-4">
                    <h6 class="text-danger">Report this listing</h6>

                    <?php if (isset($flagSuccess)): ?>
                        <div class="alert alert-success"><?= $flagSuccess ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-2">
                            <textarea name="reason" class="form-control" rows="2"
                                      placeholder="Reason for reporting..." required></textarea>
                        </div>
                        <button type="submit" name="flag" class="btn btn-danger btn-sm">
                            Report Listing
                        </button>
                    </form>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>