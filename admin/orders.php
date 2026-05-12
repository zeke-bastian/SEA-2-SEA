<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';

// fetch all orders with buyer, seller and listing details
$orders = $pdo->query("SELECT orders.*, listings.title,
                        buyers.name AS buyer_name, buyers.surname AS buyer_surname,
                        sellers.name AS seller_name, sellers.surname AS seller_surname
                        FROM orders
                        JOIN listings ON orders.listing_id = listings.id
                        JOIN users AS buyers ON orders.buyer_id = buyers.id
                        JOIN users AS sellers ON orders.seller_id = sellers.id
                        ORDER BY orders.created_at DESC")->fetchAll();
?>

<div class="container mt-5">
    <h2 class="mb-4">All Orders</h2>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Item</th>
                <th>Buyer</th>
                <th>Seller</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= $order['title'] ?></td>
                    <td><?= $order['buyer_name'] . ' ' . $order['buyer_surname'] ?></td>
                    <td><?= $order['seller_name'] . ' ' . $order['seller_surname'] ?></td>
                    <td>R <?= number_format($order['amount'], 2) ?></td>
                    <td><?= ucfirst($order['status']) ?></td>
                    <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>