<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$message = '';
if (isset($_POST['update_status'])) {
    $oid = (int)$_POST['order_id'];
    $status = $_POST['status'];
    if (in_array($status, ['pending','processing','completed','cancelled'])) {
        $u = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $u->bind_param('si', $status, $oid);
        if ($u->execute()) $message = 'Status updated.';
    }
}

$orders = $conn->query("SELECT o.*, u.username, u.email, (SELECT COUNT(*) FROM order_items WHERE order_id=o.id) AS item_count FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.order_date DESC");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Orders · Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head><body>
<div class="admin-wrap">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <h1>Manage Orders</h1>
        <p class="lead">Update status and view order details</p>
        <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

        <div class="admin-card">
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>Customer</th><th>Items</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
                    <tbody>
                    <?php while ($o = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $o['id'] ?></td>
                            <td><strong><?= e($o['username']) ?></strong><br><small class="muted"><?= e($o['email']) ?></small></td>
                            <td><?= $o['item_count'] ?></td>
                            <td><?= money($o['total_amount']) ?></td>
                            <td>
                                <form method="POST" style="display:flex;gap:6px;align-items:center">
                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                    <select name="status" class="input" style="padding:6px 10px;font-size:.8rem;width:auto" onchange="this.form.submit()">
                                        <?php foreach (['pending','processing','completed','cancelled'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" style="display:none"></button>
                                </form>
                            </td>
                            <td><?= date('M d, Y', strtotime($o['order_date'])) ?></td>
                            <td><a href="view_order.php?id=<?= $o['id'] ?>" class="btn btn-outline btn-sm"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body></html>