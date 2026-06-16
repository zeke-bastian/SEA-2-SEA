<?php
require_once '../includes/db.php'; // connects to database

// only runs if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    
    // sanitize inputs — removes spaces and prevents malicious code
    $name = trim(htmlspecialchars($_POST['name']));
    $surname = trim(htmlspecialchars($_POST['surname']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // check passwords match before doing anything else
    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // hash the password — never store plain text passwords
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            // prepared statement — placeholders prevent SQL injection
            $stmt = $pdo->prepare("INSERT INTO users (name, surname, email, password) 
                                   VALUES (:name, :surname, :email, :password)");
            $stmt->execute([
                ':name' => $name,
                ':surname' => $surname,
                ':email' => $email,
                ':password' => $hashed
            ]);
            // redirect to login on success
            header("Location: /pages/login.php");
            exit();
        } catch (PDOException $e) {
            // catches duplicate email since email column is UNIQUE in database
            $error = "Email already registered.";
        }
    }
}
require_once '../includes/header.php'; // navbar loaded after PHP logic
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <h2 class="mb-4">Create an Account</h2>
            
            <!-- shows error message if something went wrong -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Surname</label>
                    <input type="text" name="surname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="register" class="btn btn-primary w-100">
                    Register
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>