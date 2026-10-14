<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin('order.php');
$pageTitle = 'My Orders';

$s = $conn->prepare("SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id=o.id) AS item_count FROM orders o WHERE o.user_id=? ORDER BY o.order_date DESC");
$s->bind_param('i', $_SESSION['user_id']);
$s->execute();
$orders = $s->get_result();
include 'includes/header.php';
?>
<div class="container" style="padding-top:36px">
    <div class="sec-title">
        <span class="eyebrow">History</span>
        <h2>My Orders</h2>
        <p>Track and review your past orders</p>
    </div>

    <?php if ($orders->num_rows): while ($o = $orders->fetch_assoc()): ?>
        <div class="order-card reveal">
            <div class="order-top">
                <div>
                    <h3>Order #<?= $o['id'] ?></h3>
                    <p class="meta"><i class="fa-regular fa-calendar"></i> <?= date('M d, Y · h:i A', strtotime($o['order_date'])) ?></p>
                </div>
                <span class="pill pill-<?= $o['status'] ?>"><i class="fa-solid <?= statusIcon($o['status']) ?>"></i> <?= ucfirst($o['status']) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap;align-items:center">
                <div>
                    <p class="muted" style="font-size:.86rem"><i class="fa-solid fa-bag-shopping"></i> <?= $o['item_count'] ?> items</p>
                    <p style="color:var(--ink-900);font-weight:800;font-size:1.05rem;margin-top:4px"><?= money($o['total_amount']) ?></p>
                </div>
                <a href="<?= url('order_details.php?id=' . $o['id']) ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-eye"></i> View Details</a>
            </div>
        </div>
    <?php endwhile; else: ?>
        <div class="panel empty">
            <i class="fa-solid fa-receipt"></i>
            <h3>No orders yet</h3>
            <p class="mb-3">Start ordering delicious food from our menu!</p>
            <a href="<?= url('menu.php') ?>" class="btn btn-primary"><i class="fa-solid fa-utensils"></i> Browse Menu</a>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>