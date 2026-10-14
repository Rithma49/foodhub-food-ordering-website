<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$start = $_GET['start_date'] ?? date('Y-m-01');
$end   = $_GET['end_date'] ?? date('Y-m-t');

$s = $conn->prepare("SELECT COUNT(*) total_orders, COALESCE(SUM(total_amount),0) revenue, COALESCE(AVG(total_amount),0) avg_val, SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) completed FROM orders WHERE DATE(order_date) BETWEEN ? AND ?");
$s->bind_param('ss', $start, $end); $s->execute();
$stats = $s->get_result()->fetch_assoc();

$top = $conn->prepare("SELECT m.name, SUM(oi.quantity) qty, SUM(oi.quantity * oi.price) revenue FROM order_items oi JOIN menu_items m ON oi.menu_item_id=m.id JOIN orders o ON oi.order_id=o.id WHERE o.status='completed' AND DATE(o.order_date) BETWEEN ? AND ? GROUP BY m.id ORDER BY qty DESC LIMIT 10");
$top->bind_param('ss', $start, $end); $top->execute();
$top_res = $top->get_result();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reports · Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head><body>
<div class="admin-wrap">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <h1>Sales Reports</h1>
        <p class="lead">Analyze performance over time</p>

        <div class="admin-card">
            <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
                <div class="field" style="margin:0"><label><i class="fa-solid fa-calendar"></i> From</label><input type="date" name="start_date" class="input" value="<?= e($start) ?>"></div>
                <div class="field" style="margin:0"><label><i class="fa-solid fa-calendar"></i> To</label><input type="date" name="end_date" class="input" value="<?= e($end) ?>"></div>
                <button class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
                <a href="reports.php" class="btn btn-outline">Reset</a>
            </form>
        </div>

        <div class="stats">
            <div class="stat"><span class="ic"><i class="fa-solid fa-receipt"></i></span><h3><?= $stats['total_orders'] ?></h3><p>Orders</p></div>
            <div class="stat green"><span class="ic"><i class="fa-solid fa-dollar-sign"></i></span><h3><?= money($stats['revenue']) ?></h3><p>Revenue</p></div>
            <div class="stat blue"><span class="ic"><i class="fa-solid fa-chart-simple"></i></span><h3><?= money($stats['avg_val']) ?></h3><p>Avg Order</p></div>
            <div class="stat amber"><span class="ic"><i class="fa-solid fa-circle-check"></i></span><h3><?= $stats['completed'] ?></h3><p>Completed</p></div>
        </div>

        <div class="admin-card">
            <h3><i class="fa-solid fa-trophy"></i> Top Selling Items</h3>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>#</th><th>Item</th><th>Qty</th><th>Revenue</th></tr></thead>
                    <tbody>
                    <?php $i=1; while ($r = $top_res->fetch_assoc()): ?>
                        <tr><td><?= $i++ ?></td><td><?= e($r['name']) ?></td><td><?= $r['qty'] ?></td><td><?= money($r['revenue']) ?></td></tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body></html>