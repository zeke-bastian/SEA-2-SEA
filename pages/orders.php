<?php
require_once '../includes/auth.php';

// session check — members only
if (!isset($_SESSION['user_id'])) {
    header("Location: /pages/login.php");
    exit();
}

require_once '../includes/db.php';

// fetch all orders belonging to this buyer
// JOIN pulls listing title and seller name so the table makes sense
$stmt = $pdo->prepare("SELECT orders.*, listings.title, 
                        users.name AS seller_name, users.surname AS seller_surname
                        FROM orders
                        JOIN listings ON orders.listing_id = listings.id
                        JOIN users ON orders.seller_id = users.id
                        WHERE orders.buyer_id = :buyer_id
                        ORDER BY orders.created_at DESC");
$stmt->execute([':buyer_id' => $_SESSION['user_id']]);
$orders = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Order History</h2>

    <?php if (count($orders) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Item</th>
                    <th>Seller</th>
                    <th>Amount Paid</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['title'] ?></td>
                        <td><?= $order['seller_name'] . ' ' . $order['seller_surname'] ?></td>
                        <td>R <?= number_format($order['amount'], 2) ?></td>
                        <!-- ucfirst capitalises the first letter of status -->
                        <td><?= ucfirst($order['status']) ?></td>
                        <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- no orders yet — send them shopping -->
        <p class="text-muted">You have not made any purchases yet.</p>
        <a href="/index.php" class="btn btn-primary">Browse Listings</a>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>