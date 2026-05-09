
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C2C Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/c2c_platform/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="/c2c_platform/index.php">C2C Marketplace</a>
    <div class="ms-auto">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="/c2c_platform/pages/dashboard.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
            <a href="/c2c_platform/pages/logout.php" class="btn btn-danger btn-sm">Logout</a>
        <?php else: ?>
            <a href="/c2c_platform/pages/login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
            <a href="/c2c_platform/pages/register.php" class="btn btn-primary btn-sm">Register</a>
        <?php endif; ?>
    </div>
</nav>