<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$s = $conn->prepare("SELECT * FROM menu_items WHERE id=?");
$s->bind_param('i', $id); $s->execute();
$item = $s->get_result()->fetch_assoc();
if (!$item) { header('Location: edit_item.php'); exit(); }

$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $image_url = $item['image_url'];

    if (!$name || !$description || $price <= 0 || !$category_id) $error = 'Please fill in all fields.';
    else {
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $dir = __DIR__ . '/../images/';
                $fn = uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $fn)) {
                    if ($image_url && file_exists($dir . $image_url)) @unlink($dir . $image_url);
                    $image_url = $fn;
                }
            } else $error = 'Invalid file.';
        }
        if (!$error) {
            $u = $conn->prepare("UPDATE menu_items SET name=?, description=?, price=?, category_id=?, image_url=?, is_available=?, is_featured=? WHERE id=?");
            $u->bind_param('ssdisiii', $name, $description, $price, $category_id, $image_url, $is_available, $is_featured, $id);
            if ($u->execute()) {
                $success = 'Item updated!';
                $item = array_merge($item, compact('name','description','price','category_id','image_url','is_available','is_featured'));
            } else $error = 'Error: ' . $conn->error;
        }
    }
}
$cats = $conn->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Update Item · Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head><body>
<div class="admin-wrap">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <h1>Update Menu Item</h1>
        <p class="lead">Editing: <?= e($item['name']) ?></p>
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

        <div class="admin-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="field"><label>Name</label><input type="text" name="name" class="input" value="<?= e($item['name']) ?>" required></div>
                <div class="field"><label>Description</label><textarea name="description" rows="4" class="input" required><?= e($item['description']) ?></textarea></div>
                <div class="field"><label>Price</label><input type="number" step="0.01" name="price" class="input" value="<?= e($item['price']) ?>" required></div>
                <div class="field">
                    <label>Category</label>
                    <select name="category_id" class="input" required>
                        <?php while($c = $cats->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $item['category_id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Current Image</label>
                    <?php if ($item['image_url']): ?>
                        <img src="../images/<?= e($item['image_url']) ?>" style="width:100px;border-radius:8px;margin-bottom:8px">
                    <?php endif; ?>
                    <input type="file" name="image" class="input" accept="image/*">
                </div>
                <div class="field" style="display:flex;gap:20px;flex-wrap:wrap">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="is_available" value="1" <?= $item['is_available'] ? 'checked' : '' ?>> Available</label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="is_featured" value="1" <?= $item['is_featured'] ? 'checked' : '' ?>> Featured</label>
                </div>
                <div style="display:flex;gap:12px">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Update</button>
                    <a href="edit_item.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body></html>