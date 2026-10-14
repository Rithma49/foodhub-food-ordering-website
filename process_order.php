<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success'=>false, 'message'=>'Please sign in to place an order', 'require_login'=>true]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['cart'])) {
    echo json_encode(['success'=>false, 'message'=>'Cart is empty']);
    exit();
}

$cart = $data['cart'];
$address = trim($data['delivery_address'] ?? '');
$phone = trim($data['phone'] ?? '');
$notes = trim($data['notes'] ?? '');

if (!$address || !$phone) {
    echo json_encode(['success'=>false, 'message'=>'Address and phone are required']);
    exit();
}

$subtotal = 0;
foreach ($cart as $it) $subtotal += (float)$it['price'] * (int)$it['quantity'];
$fee = DELIVERY_FEE;
$tax = $subtotal * TAX_RATE;
$total = $subtotal + $fee + $tax;

$conn->begin_transaction();
try {
    $s = $conn->prepare("INSERT INTO orders (user_id, subtotal, delivery_fee, tax, total_amount, delivery_address, phone, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $uid = $_SESSION['user_id'];
    $s->bind_param('iddddsss', $uid, $subtotal, $fee, $tax, $total, $address, $phone, $notes);
    $s->execute();
    $oid = $conn->insert_id;

    $s2 = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
    foreach ($cart as $it) {
        $mid  = (int)$it['id'];
        $name = $it['name'];
        $qty  = (int)$it['quantity'];
        $prc  = (float)$it['price'];
        $s2->bind_param('iisid', $oid, $mid, $name, $qty, $prc);
        $s2->execute();
    }
    $conn->commit();
    echo json_encode(['success'=>true, 'order_id'=>$oid]);
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['success'=>false, 'message'=>'Error: ' . $e->getMessage()]);
}