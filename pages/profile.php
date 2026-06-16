<?php
require_once '../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /pages/login.php");
    exit();
}

require_once '../includes/db.php';

// handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name = trim(htmlspecialchars($_POST['name']));
    $surname = trim(htmlspecialchars($_POST['surname']));

    $stmt = $pdo->prepare("UPDATE users SET name = :name, surname = :surname WHERE id = :id");
    $stmt->execute([
        ':name' => $name,
        ':surname' => $surname,
        ':id' => $_SESSION['user_id']
    ]);

    // update session name so navbar reflects change immediately
    $_SESSION['user_name'] = $name;
    $success = "Profile updated.";
}

// fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <h2 class="mb-4">My Profile</h2>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label>Name</label>
                    <!-- pre-fills with current value from database -->
                    <input type="text" name="name" class="form-control" 
                           value="<?= $user['name'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>Surname</label>
                    <input type="text" name="surname" class="form-control" 
                           value="<?= $user['surname'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <!-- email is not editable — it is the unique identifier -->
                    <input type="email" class="form-control" 
                           value="<?= $user['email'] ?>" disabled>
                </div>
                <button type="submit" name="update" class="btn btn-primary">
                    Update Profile
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>