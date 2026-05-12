<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';

// moderator escalates a flag
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['escalate'])) {
    $fid = (int)$_POST['flag_id'];
    $stmt = $pdo->prepare("UPDATE flags SET status = 'escalated' WHERE id = :id");
    $stmt->execute([':id' => $fid]);
    $success = "Flag escalated to Super Admin.";
}

// moderator dismisses a flag
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dismiss'])) {
    $fid = (int)$_POST['flag_id'];
    $stmt = $pdo->prepare("UPDATE flags SET status = 'dismissed' WHERE id = :id");
    $stmt->execute([':id' => $fid]);
    $success = "Flag dismissed.";
}

// superadmin confirms removal of flagged listing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_remove'])) {
    if ($_SESSION['admin_role'] === 'superadmin') {
        $fid = (int)$_POST['flag_id'];
        $lid = (int)$_POST['listing_id'];

        // remove the listing
        $stmt = $pdo->prepare("UPDATE listings SET status = 'removed' WHERE id = :id");
        $stmt->execute([':id' => $lid]);

        // mark flag as dismissed after acting on it
        $stmt2 = $pdo->prepare("UPDATE flags SET status = 'dismissed' WHERE id = :id");
        $stmt2->execute([':id' => $fid]);

        $success = "Listing removed successfully.";
    }
}

// fetch all flags with listing title and reporter name
$flags = $pdo->query("SELECT flags.*, listings.title AS listing_title,
                       users.name AS reporter_name, users.surname AS reporter_surname
                       FROM flags
                       JOIN listings ON flags.listing_id = listings.id
                       JOIN users ON flags.reported_by = users.id
                       ORDER BY flags.created_at DESC")->fetchAll();
?>

<div class="container mt-5">
    <h2 class="mb-4">Reported Listings</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Listing</th>
                <th>Reported By</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($flags as $flag): ?>
                <tr>
                    <td><?= $flag['listing_title'] ?></td>
                    <td><?= $flag['reporter_name'] . ' ' . $flag['reporter_surname'] ?></td>
                    <td><?= $flag['reason'] ?></td>
                    <td><?= ucfirst($flag['status']) ?></td>
                    <td><?= date('d M Y', strtotime($flag['created_at'])) ?></td>
                    <td>
                        <?php if ($flag['status'] === 'pending'): ?>

                            <!-- moderator and superadmin can escalate or dismiss -->
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="flag_id" value="<?= $flag['id'] ?>">
                                <button type="submit" name="escalate" 
                                        class="btn btn-warning btn-sm">Escalate</button>
                            </form>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="flag_id" value="<?= $flag['id'] ?>">
                                <button type="submit" name="dismiss"
                                        class="btn btn-secondary btn-sm">Dismiss</button>
                            </form>

                        <?php elseif ($flag['status'] === 'escalated' && $_SESSION['admin_role'] === 'superadmin'): ?>

                            <!-- only superadmin sees confirm removal on escalated flags -->
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="flag_id" value="<?= $flag['id'] ?>">
                                <input type="hidden" name="listing_id" value="<?= $flag['listing_id'] ?>">
                                <button type="submit" name="confirm_remove"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Remove this listing?')">
                                    Confirm Remove
                                </button>
                            </form>

                        <?php else: ?>
                            <span class="text-muted"><?= ucfirst($flag['status']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>