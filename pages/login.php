<?php
require_once '../includes/db.php'; // database connection

// only runs if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    // sanitize inputs
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];

    // fetch user row from database matching the email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    // password_verify checks the plain input against the stored hash
    if ($user && password_verify($password, $user['password'])) {
        
        // start session and store user details in it
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        // redirect to dashboard on success
        header("Location: /index.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <h2 class="mb-4">Login</h2>

            <!-- shows error if credentials are wrong -->
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
            
            <!-- link to register if user has no account -->
            <p class="mt-3 text-center">
                No account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>