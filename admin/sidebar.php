<?php
$cur = basename($_SERVER['PHP_SELF']);
$A = fn($p) => $cur === $p ? 'on' : '';
?>
<div class="admin-side">
    <div class="brand">
        <span class="brand-mark"><i class="fa-solid fa-utensils"></i></span>
        Food Hub
    </div>
    <nav class="admin-nav">
        <a href="<?= url('admin/index.php') ?>" class="<?= $A('index.php') ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="<?= url('admin/add_item.php') ?>" class="<?= $A('add_item.php') ?>"><i class="fa-solid fa-circle-plus"></i> Add Item</a>
        <a href="<?= url('admin/edit_item.php') ?>" class="<?= $A('edit_item.php') ?><?= $A('update_item.php') ?>"><i class="fa-solid fa-pen-to-square"></i> Manage Menu</a>
        <a href="<?= url('admin/manage_orders.php') ?>" class="<?= $A('manage_orders.php') ?>"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        <a href="<?= url('admin/view_feedback.php') ?>" class="<?= $A('view_feedback.php') ?>"><i class="fa-solid fa-comments"></i> Feedback</a>
        <a href="<?= url('admin/reports.php') ?>" class="<?= $A('reports.php') ?>"><i class="fa-solid fa-chart-line"></i> Reports</a>
        <a href="<?= url('index.php') ?>"><i class="fa-solid fa-house"></i> Visit Site</a>
        <a href="<?= url('logout.php') ?>"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
</div>