<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$s = $conn->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=?");
$s->bind_param('i', $id); $s->execute();
$order = $s->get_result()->fetch_assoc();
if (!$order) die('Order not found.');

$items = $conn->prepare("SELECT oi.*, COALESCE(m.name, oi.item_name) AS item_name FROM order_items oi LEFT JOIN menu_items m ON oi.menu_item_id=m.id WHERE oi.order_id=?");
$items->bind_param('i', $id); $items->execute();
$res = $items->get_result();

$arr = []; $subtotal = 0;
while ($it = $res->fetch_assoc()) { $arr[] = $it; $subtotal += $it['price'] * $it['quantity']; }
$tax = $order['tax'] ?: ($subtotal * TAX_RATE);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Order #<?= $id ?> · Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head><body>
<div class="admin-wrap">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <h1>Order #<?= $order['id'] ?></h1>
        <p class="lead">Placed on <?= date('F d, Y - h:i A', strtotime($order['order_date'])) ?></p>

        <div class="order-detail-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
            <div class="admin-card">
                <h3><i class="fa-solid fa-user"></i> Customer</h3>
                <p><strong><?= e($order['username']) ?></strong></p>
                <p class="muted"><?= e($order['email']) ?></p>
                <p class="mt-1"><i class="fa-solid fa-phone"></i> <?= e($order['phone']) ?></p>
                <p class="mt-1"><i class="fa-solid fa-location-dot"></i> <?= e($order['delivery_address']) ?></p>
            </div>
            <div class="admin-card">
                <h3><i class="fa-solid fa-receipt"></i> Summary</h3>
                <div class="sum-row"><span>Subtotal</span><span><?= money($subtotal) ?></span></div>
                <div class="sum-row"><span>Delivery</span><span><?= money($order['delivery_fee']) ?></span></div>
                <div class="sum-row"><span>Tax</span><span><?= money($tax) ?></span></div>
                <div class="sum-total"><span>Total</span><span><?= money($order['total_amount']) ?></span></div>
                <p class="mt-2"><span class="pill pill-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></p>
            </div>
        </div>

        <div class="admin-card mt-3">
            <h3><i class="fa-solid fa-list"></i> Items</h3>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($arr as $it): ?>
                        <tr><td><?= e($it['item_name'] ?? 'Deleted') ?></td><td><?= $it['quantity'] ?></td><td><?= money($it['price']) ?></td><td><?= money($it['price'] * $it['quantity']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($order['notes']): ?>
        <div class="admin-card"><h3>Notes</h3><p><?= nl2br(e($order['notes'])) ?></p></div>
        <?php endif; ?>

        <a href="manage_orders.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
</div>
<style>@media(max-width:800px){.order-detail-grid{grid-template-columns:1fr!important}}</style>
</body></html>