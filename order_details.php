<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$order_id = (int)($_GET['id'] ?? 0);
$uid = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'] ?? 'customer';

$s = $conn->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=? AND (o.user_id=? OR ?='admin')");
$s->bind_param('iis', $order_id, $uid, $user_type);
$s->execute();
$order = $s->get_result()->fetch_assoc();
if (!$order) die('Order not found or access denied.');

$s2 = $conn->prepare("SELECT oi.*, COALESCE(m.name, oi.item_name) AS item_name FROM order_items oi LEFT JOIN menu_items m ON oi.menu_item_id=m.id WHERE oi.order_id=?");
$s2->bind_param('i', $order_id);
$s2->execute();
$items = $s2->get_result();

$arr = []; $subtotal = 0;
while ($it = $items->fetch_assoc()) { $arr[] = $it; $subtotal += $it['price'] * $it['quantity']; }
$fee = $order['delivery_fee'] ?: DELIVERY_FEE;
$tax = $order['tax'] ?: ($subtotal * TAX_RATE);
$total = $order['total_amount'];

$pageTitle = 'Order #' . $order_id;
include 'includes/header.php';

$steps = [
    ['label'=>'Placed','icon'=>'fa-receipt'],
    ['label'=>'Preparing','icon'=>'fa-utensils'],
    ['label'=>'Delivered','icon'=>'fa-circle-check'],
];
$idx = array_search($order['status'], ['pending','processing','completed']);
if ($idx === false) $idx = -1;
?>
<div class="container" style="padding-top:36px">
    <div class="sec-title">
        <span class="eyebrow">Order Details</span>
        <h2>Order #<?= $order['id'] ?></h2>
        <p>Placed on <?= date('F d, Y - h:i A', strtotime($order['order_date'])) ?></p>
    </div>

    <div class="panel mb-3">
        <div class="tracker">
            <?php foreach ($steps as $i => $st): ?>
                <div class="tr-step <?= $i < $idx ? 'done' : ($i === $idx ? 'active' : '') ?>">
                    <div class="tr-dot"><i class="fa-solid <?= $st['icon'] ?>"></i></div>
                    <div class="tr-label"><?= $st['label'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="order-detail-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:22px">
        <div class="panel">
            <div class="panel-title"><i class="fa-solid fa-user"></i> Customer Info</div>
            <p style="margin-bottom:8px"><strong>Name:</strong> <?= e($order['username']) ?></p>
            <p style="margin-bottom:8px"><strong>Email:</strong> <?= e($order['email']) ?></p>
            <p style="margin-bottom:8px"><strong>Phone:</strong> <?= e($order['phone']) ?></p>
            <p><strong>Address:</strong><br><?= nl2br(e($order['delivery_address'])) ?></p>
        </div>
        <div class="panel">
            <div class="panel-title"><i class="fa-solid fa-receipt"></i> Summary</div>
            <div class="sum-row"><span>Subtotal</span><span><?= money($subtotal) ?></span></div>
            <div class="sum-row"><span>Delivery</span><span><?= money($fee) ?></span></div>
            <div class="sum-row"><span>Tax (8%)</span><span><?= money($tax) ?></span></div>
            <div class="sum-total"><span>Total</span><span><?= money($total) ?></span></div>
        </div>
    </div>

    <div class="panel mt-3">
        <div class="panel-title"><i class="fa-solid fa-list"></i> Order Items</div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($arr as $it): ?>
                    <tr>
                        <td><?= e($it['item_name']) ?></td>
                        <td><?= $it['quantity'] ?></td>
                        <td><?= money($it['price']) ?></td>
                        <td><?= money($it['price'] * $it['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($order['notes'])): ?>
    <div class="panel mt-3">
        <div class="panel-title"><i class="fa-solid fa-note-sticky"></i> Notes</div>
        <p class="muted"><?= nl2br(e($order['notes'])) ?></p>
    </div>
    <?php endif; ?>

    <div class="center mt-4">
        <a href="<?= url('order.php') ?>" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to Orders</a>
    </div>
</div>
<style>@media(max-width:800px){.order-detail-grid{grid-template-columns:1fr!important}}</style>
<?php include 'includes/footer.php'; ?>