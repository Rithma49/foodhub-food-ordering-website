<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html><head><title>Food Hub Diagnostic</title>
<style>
body{font-family:system-ui;max-width:800px;margin:40px auto;padding:20px;background:#f5f5f5}
h1{color:#E85D04}.box{background:#fff;padding:20px;border-radius:12px;margin-bottom:16px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.ok{color:#10B981;font-weight:700}.bad{color:#EF4444;font-weight:700}
ul{list-style:none;padding:0}li{padding:6px 0;border-bottom:1px solid #eee}
</style></head><body>
<h1>Food Hub Diagnostic</h1>
<div class="box"><h3>Database</h3>
<p><?= $conn->connect_error ? '<span class="bad">FAILED</span>' : '<span class="ok">Connected</span>' ?></p>
<p><strong>Base URL:</strong> <code><?= e(BASE_URL) ?></code></p>
</div>
<div class="box"><h3>Tables</h3><ul>
<?php foreach (['users','categories','menu_items','orders','order_items','feedback'] as $t) {
    try {
        $r = $conn->query("SELECT COUNT(*) c FROM $t");
        echo "<li><span class='ok'>OK</span> $t — {$r->fetch_assoc()['c']} rows</li>";
    } catch (Throwable $e) { echo "<li><span class='bad'>MISSING</span> $t</li>"; }
}
?></ul></div>
<div class="box"><h3>Files</h3><ul>
<?php
$files = ['includes/config.php','includes/functions.php','includes/auth.php','includes/header.php','includes/footer.php',
'api/auth.php','assets/css/style.css','assets/js/app.js','index.php','menu.php','cart.php','order.php',
'order_details.php','process_order.php','logout.php',
'admin/sidebar.php','admin/index.php','admin/add_item.php','admin/edit_item.php','admin/update_item.php',
'admin/manage_orders.php','admin/view_order.php','admin/view_feedback.php','admin/reports.php'];
foreach ($files as $f) {
    echo file_exists(__DIR__.'/'.$f)
        ? "<li><span class='ok'>OK</span> $f</li>"
        : "<li><span class='bad'>MISSING</span> $f</li>";
}
?></ul></div>
<div class="box"><h3>PHP</h3><p>Version: <strong><?= phpversion() ?></strong></p></div>
</body></html>