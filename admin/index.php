<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$total_orders    = $conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'];
$total_revenue   = $conn->query("SELECT COALESCE(SUM(total_amount),0) t FROM orders WHERE status='completed'")->fetch_assoc()['t'];
$total_customers = $conn->query("SELECT COUNT(*) c FROM users WHERE user_type='customer'")->fetch_assoc()['c'];
$pending         = $conn->query("SELECT COUNT(*) c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard · Food Hub Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head><body>
<div class="admin-wrap">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <h1>Dashboard</h1>
        <p class="lead">Welcome back, <?= e($_SESSION['username']) ?>!</p>

        <div class="stats">
            <div class="stat"><span class="ic"><i class="fa-solid fa-receipt"></i></span><h3><?= $total_orders ?></h3><p>Total Orders</p></div>
            <div class="stat green"><span class="ic"><i class="fa-solid fa-dollar-sign"></i></span><h3><?= money($total_revenue) ?></h3><p>Revenue</p></div>
            <div class="stat blue"><span class="ic"><i class="fa-solid fa-users"></i></span><h3><?= $total_customers ?></h3><p>Customers</p></div>
            <div class="stat amber"><span class="ic"><i class="fa-solid fa-clock"></i></span><h3><?= $pending ?></h3><p>Pending</p></div>
        </div>

        <div class="admin-card">
            <h3><i class="fa-solid fa-bolt"></i> Quick Actions</h3>
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <a href="add_item.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Item</a>
                <a href="manage_orders.php" class="btn btn-outline"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
                <a href="view_feedback.php" class="btn btn-outline"><i class="fa-solid fa-comments"></i> Feedback</a>
                <a href="reports.php" class="btn btn-outline"><i class="fa-solid fa-chart-line"></i> Reports</a>
            </div>
        </div>

        <div class="admin-card">
            <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Orders</h3>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
                    <tbody>
                    <?php
                    $r = $conn->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.order_date DESC LIMIT 8");
                    while ($o = $r->fetch_assoc()):
                    ?>
                        <tr>
                            <td>#<?= $o['id'] ?></td>
                            <td><?= e($o['username']) ?></td>
                            <td><?= money($o['total_amount']) ?></td>
                            <td><span class="pill pill-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
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