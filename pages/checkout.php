<?php
require_once '../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';

$listing_id = isset($_GET['listing_id']) ? (int)$_GET['listing_id'] : 0;

// fetch the listing being purchased
$stmt = $pdo->prepare("SELECT listings.*, users.name AS seller_name 
                        FROM listings 
                        JOIN users ON listings.seller_id = users.id 
                        WHERE listings.id = :id AND listings.status = 'active'");
$stmt->execute([':id' => $listing_id]);
$listing = $stmt->fetch();

if (!$listing) {
    header("Location: /index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $card_number = $_POST['card_number'] ?? '';
    $method = $_POST['payment_method'] ?? '';

    // simulated validation — just checks something was entered
    if (empty($method)) {
        $error = "Please select a payment method.";
    } elseif ($method !== 'paypal' && strlen(preg_replace('/\s+/', '', $card_number)) < 16) {
        $error = "Please enter a valid 16 digit card number.";
    } else {
        // record the order as completed
        $orderStmt = $pdo->prepare("INSERT INTO orders (listing_id, buyer_id, seller_id, amount, status) 
                                     VALUES (:listing_id, :buyer_id, :seller_id, :amount, 'completed')");
        $orderStmt->execute([
            ':listing_id' => $listing_id,
            ':buyer_id' => $_SESSION['user_id'],
            ':seller_id' => $listing['seller_id'],
            ':amount' => $listing['price']
        ]);

        // mark listing as sold
        $soldStmt = $pdo->prepare("UPDATE listings SET status = 'sold' WHERE id = :id");
        $soldStmt->execute([':id' => $listing_id]);

        $success = true;
    }
}

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">

            <?php if (isset($success) && $success): ?>
                <!-- order confirmed -->
                <div class="text-center py-5">
                    <h2 class="text-success">Payment Confirmed</h2>
                    <p class="lead">Your order for <strong><?= $listing['title'] ?></strong> 
                       has been placed.</p>
                    <div class="alert alert-warning mt-3">
                         This is a simulated payment. No real transaction occurred.
                    </div>
                    <a href="/pages/orders.php" class="btn btn-primary mt-3">View My Orders</a>
                </div>

            <?php else: ?>

                <h2 class="mb-1">Checkout</h2>

                <!-- simulated payment notice -->
                <div class="alert alert-warning mb-4">
                    This is a prototype. Payment is simulated — no real charges occur.
                </div>

                <!-- order summary card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-1"><?= $listing['title'] ?></h5>
                        <p class="text-muted mb-1">Seller: <?= $listing['seller_name'] ?></p>
                        <h4 class="text-success mb-0">
                            R <?= number_format($listing['price'], 2) ?>
                        </h4>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="">

                    <!-- payment method selector with icons -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Payment Method</label>
                        <div class="d-flex gap-3 flex-wrap">

                            <label class="border rounded p-3 text-center" 
                                   style="cursor:pointer; min-width:100px;">
                                <input type="radio" name="payment_method" 
                                       value="visa" class="d-none payment-radio">
                                <div class="fw-bold text-primary fs-4">VISA</div>
                                <div class="small text-muted">Credit/Debit</div>
                            </label>

                            <label class="border rounded p-3 text-center" 
                                   style="cursor:pointer; min-width:100px;">
                                <input type="radio" name="payment_method" 
                                       value="mastercard" class="d-none payment-radio">
                                <div class="fw-bold text-danger fs-4">MC</div>
                                <div class="small text-muted">Mastercard</div>
                            </label>

                            <label class="border rounded p-3 text-center" 
                                   style="cursor:pointer; min-width:100px;">
                                <input type="radio" name="payment_method" 
                                       value="amex" class="d-none payment-radio">
                                <div class="fw-bold text-success fs-4">AMEX</div>
                                <div class="small text-muted">American Express</div>
                            </label>

                            <label class="border rounded p-3 text-center" 
                                   style="cursor:pointer; min-width:100px;">
                                <input type="radio" name="payment_method" 
                                       value="paypal" class="d-none payment-radio" 
                                       id="paypal-radio">
                                <div class="fw-bold fs-4" 
                                     style="color:#003087;">Pay<span style="color:#009cde;">Pal</span></div>
                                <div class="small text-muted">PayPal</div>
                            </label>

                        </div>
                    </div>

                    <!-- card fields — hidden when PayPal selected -->
                    <div id="card-fields">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" 
                                       placeholder="John">
                            </div>
                            <div class="col-md-6">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" 
                                       placeholder="Smith">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Card Number</label>
                            <input type="text" name="card_number" class="form-control" 
                                   maxlength="19" placeholder="1234 5678 1234 5678">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Expiry</label>
                                <div class="d-flex gap-2">
                                    <select name="expiry_month" class="form-select">
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>">
                                                <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <select name="expiry_year" class="form-select">
                                        <?php for ($y = date('Y'); $y <= date('Y') + 8; $y++): ?>
                                            <option value="<?= $y ?>"><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>CVV</label>
                                <input type="text" name="cvv" class="form-control" 
                                       maxlength="4" placeholder="123">
                            </div>
                        </div>
                    </div>

                    <!-- paypal placeholder — shown when PayPal selected -->
                    <div id="paypal-fields" class="d-none">
                        <div class="alert alert-info">
                            You will be redirected to PayPal to complete payment. 
                            (Simulated — no redirect occurs in prototype.)
                        </div>
                    </div>

                    <button type="submit" name="pay" class="btn btn-success w-100 mt-2">
                        Pay R <?= number_format($listing['price'], 2) ?>
                    </button>
                </form>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>