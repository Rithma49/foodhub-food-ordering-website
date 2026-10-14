<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $is_featured  = isset($_POST['is_featured']) ? 1 : 0;
    $image_url = '';

    if (!$name || !$description || $price <= 0 || !$category_id) $error = 'Please fill in all required fields.';
    else {
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $dir = __DIR__ . '/../images/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $fn = uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $fn)) $image_url = $fn;
            } else $error = 'Invalid file type.';
        }
        if (!$error) {
            $s = $conn->prepare("INSERT INTO menu_items (name, description, price, category_id, image_url, is_available, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $s->bind_param('ssdisii', $name, $description, $price, $category_id, $image_url, $is_available, $is_featured);
            if ($s->execute()) { $success = 'Item added successfully!'; $_POST = []; }
            else $error = 'Error: ' . $conn->error;
        }
    }
}
$cats = $conn->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Add Item · Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head><body>
<div class="admin-wrap">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <h1>Add Menu Item</h1>
        <p class="lead">Create a new dish for your menu</p>
        <?php if ($error): ?><div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= e($success) ?></div><?php endif; ?>

        <div class="admin-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="field"><label><i class="fa-solid fa-tag"></i> Item Name</label><input type="text" name="name" class="input" required></div>
                <div class="field"><label><i class="fa-solid fa-align-left"></i> Description</label><textarea name="description" rows="4" class="input" required></textarea></div>
                <div class="field"><label><i class="fa-solid fa-dollar-sign"></i> Price (Rs.)</label><input type="number" step="0.01" min="0" name="price" class="input" required></div>
                <div class="field">
                    <label><i class="fa-solid fa-layer-group"></i> Category</label>
                    <select name="category_id" class="input" required>
                        <option value="">Select Category</option>
                        <?php while($c = $cats->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="field"><label><i class="fa-solid fa-image"></i> Item Image</label><input type="file" name="image" class="input" accept="image/*"></div>
                <div class="field" style="display:flex;gap:20px;flex-wrap:wrap">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="is_available" value="1" checked> Available</label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="is_featured" value="1"> Featured</label>
                </div>
                <div style="display:flex;gap:12px">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Item</button>
                    <a href="edit_item.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body></html>