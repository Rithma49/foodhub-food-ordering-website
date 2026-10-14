<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $chk = $conn->prepare("SELECT image_url FROM menu_items WHERE id=?");
    $chk->bind_param('i', $id); $chk->execute();
    $row = $chk->get_result()->fetch_assoc();
    if ($row) {
        if (!empty($row['image_url']) && file_exists(__DIR__ . '/../images/' . $row['image_url'])) @unlink(__DIR__ . '/../images/' . $row['image_url']);
        $del = $conn->prepare("DELETE FROM menu_items WHERE id=?");
        $del->bind_param('i', $id);
        if ($del->execute()) $message = 'Item deleted.';
    }
}
$items = $conn->query("SELECT m.*, c.name AS cat FROM menu_items m LEFT JOIN categories c ON m.category_id=c.id ORDER BY m.category_id, m.name");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Menu · Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head><body>
<div class="admin-wrap">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <h1>Manage Menu</h1>
        <p class="lead">Edit or delete existing menu items</p>
        <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

        <div class="admin-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                <h3 style="margin:0"><i class="fa-solid fa-list"></i> All Items</h3>
                <a href="add_item.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Add</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php while ($it = $items->fetch_assoc()):
                        $img = !empty($it['image_url']) ? '../images/' . $it['image_url'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=200&q=80';
                    ?>
                        <tr>
                            <td><?= $it['id'] ?></td>
                            <td><img src="<?= e($img) ?>" style="width:52px;height:52px;object-fit:cover;border-radius:8px" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=200&q=80'"></td>
                            <td><strong><?= e($it['name']) ?></strong></td>
                            <td><?= e($it['cat']) ?></td>
                            <td><?= money($it['price']) ?></td>
                            <td>
                                <?php if ($it['is_available']): ?>
                                    <span class="pill pill-completed">Available</span>
                                <?php else: ?>
                                    <span class="pill pill-cancelled">Off</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px">
                                    <a href="update_item.php?id=<?= $it['id'] ?>" class="btn btn-outline btn-sm"><i class="fa-solid fa-pen"></i></a>
                                    <a href="?delete_id=<?= $it['id'] ?>" onclick="return confirm('Delete?')" class="btn btn-outline btn-sm" style="color:var(--danger)"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body></html>