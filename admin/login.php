<?php
require_once '../includes/auth.php';

// already logged in — straight to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];

    // query admin_users — not the customer users table
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        // role stored in session — used for RBAC checks on every admin page
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $admin['role'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
<!-- Brite theme from Bootswatch — local copy -->
<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
<!-- SEA-2-SEA custom styles on top -->
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4 text-center">Admin Login</h4>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>