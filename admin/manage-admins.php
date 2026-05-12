<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';

// this entire page is superadmin only — moderators get booted
if ($_SESSION['admin_role'] !== 'superadmin') {
    header("Location: index.php");
    exit();
}

// create a new moderator account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])) {
    $name = trim(htmlspecialchars($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'moderator'; // new accounts are always moderator — superadmin is created via SQL only

    try {
        $stmt = $pdo->prepare("INSERT INTO admin_users (name, email, password, role) 
                                VALUES (:name, :email, :password, :role)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role
        ]);
        $success = "Moderator account created.";
    } catch (PDOException $e) {
        $error = "Email already in use.";
    }
}

// delete a moderator account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_admin'])) {
    $aid = (int)$_POST['admin_id'];

    // prevent superadmin from deleting themselves
    if ($aid === $_SESSION['admin_id']) {
        $error = "You cannot delete your own account.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = :id AND role = 'moderator'");
        $stmt->execute([':id' => $aid]);
        $success = "Moderator deleted.";
    }
}

// fetch all admin accounts
$admins = $pdo->query("SELECT * FROM admin_users ORDER BY created_at DESC")->fetchAll();
?>

<div class="container mt-5">
    <h2 class="mb-4">Manage Admin Accounts</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- create moderator form -->
    <div class="card mb-5">
        <div class="card-body">
            <h5>Create Moderator Account</h5>
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" name="create_admin" class="btn btn-primary">
                    Create Moderator
                </button>
            </form>
        </div>
    </div>

    <!-- all admin accounts -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?= $admin['name'] ?></td>
                    <td><?= $admin['email'] ?></td>
                    <td>
                        <!-- badge colour differs by role -->
                        <span class="badge <?= $admin['role'] === 'superadmin' ? 'bg-danger' : 'bg-secondary' ?>">
                            <?= ucfirst($admin['role']) ?>
                        </span>
                    </td>
                    <td><?= date('d M Y', strtotime($admin['created_at'])) ?></td>
                    <td>
                        <!-- cannot delete superadmin accounts or yourself -->
                        <?php if ($admin['role'] === 'moderator' && $admin['id'] !== $_SESSION['admin_id']): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                <button type="submit" name="delete_admin" 
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this moderator?')">
                                    Delete
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">Protected</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>