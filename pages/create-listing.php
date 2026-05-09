<?php
require_once '../includes/auth.php'; // session start

// boot them out if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    // sanitize all text inputs
    $title = trim(htmlspecialchars($_POST['title']));
    $description = trim(htmlspecialchars($_POST['description']));
    $price = $_POST['price'];
    $category = trim(htmlspecialchars($_POST['category']));
    $image = null;

    // if an image was uploaded, give it a unique name and move it to uploads
    if (!empty($_FILES['image']['name'])) {
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadPath = '../assets/uploads/' . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
        $image = $imageName;
    }

    // insert listing — seller_id ties it to the logged in user
    $stmt = $pdo->prepare("INSERT INTO listings (seller_id, title, description, price, category, image) 
                           VALUES (:seller_id, :title, :description, :price, :category, :image)");
    $stmt->execute([
        ':seller_id' => $_SESSION['user_id'],
        ':title' => $title,
        ':description' => $description,
        ':price' => $price,
        ':category' => $category,
        ':image' => $image
    ]);

    // back to dashboard after successful listing
    header("Location: dashboard.php");
    exit();
}

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2 class="mb-4">Create a Listing</h2>
            
            <!-- enctype is required for file uploads — skip it and images vanish into the void -->
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Price (R)</label>
                    <input type="number" name="price" step="0.01" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <button type="submit" name="create" class="btn btn-primary w-100">
                    Post Listing
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>