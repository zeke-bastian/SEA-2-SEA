<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';

// superadmin can delete users, moderator can only view
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    if ($_SESSION['admin_role'] === 'superadmin') {
        $uid = (int)$_POST['user_id'];

        // delete in order of dependency — child records first
        // bids placed by this user
        $pdo->prepare("DELETE FROM bids WHERE bidder_id = :id")
            ->execute([':id' => $uid]);

        // flags submitted by this user
        $pdo->prepare("DELETE FROM flags WHERE reported_by = :id")
            ->execute([':id' => $uid]);

        // orders where this user is buyer or seller
        $pdo->prepare("DELETE FROM orders WHERE buyer_id = :id OR seller_id = :id")
            ->execute([':id' => $uid]);

        // bids on this user's listings before deleting the listings
        $pdo->prepare("DELETE FROM bids WHERE listing_id IN 
                       (SELECT id FROM listings WHERE seller_id = :id)")
            ->execute([':id' => $uid]);

        // listings belonging to this user
        $pdo->prepare("DELETE FROM listings WHERE seller_id = :id")
            ->execute([':id' => $uid]);

        // finally safe to delete the user
        $pdo->prepare("DELETE FROM users WHERE id = :id")
            ->execute([':id' => $uid]);

        $success = "User and all associated data deleted.";
    } else {
        $error = "You do not have permission to delete users.";
    }
}

// fetch all users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="container mt-5">
    <h2 class="mb-4">Manage Users</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registered</th>
                <!-- delete column only shown to superadmin -->
                <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['name'] . ' ' . $user['surname'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                    <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this user?')">Delete</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>