<?php
include 'donnection.php'; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: log-in.php");
    exit();
}

function getCartId($conn, $userId) {
    $sql = "SELECT cart_id FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        die("Keranjang tidak ditemukan.");
    }
    return $row['cart_id'];
}

function getCartItems($conn, $cartId) {
    $sql = "SELECT ci.productid, ci.cart_quantity, p.price 
            FROM cart_item ci 
            JOIN products p ON ci.productid = p.id 
            WHERE ci.cartid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cartId);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    $totalAmount = 0;

    while ($item = $result->fetch_assoc()) {
        $items[] = $item;
        $totalAmount += $item['cart_quantity'] * $item['price'];
    }

    return [$items, $totalAmount];
}

function insertOrder($conn, $userId, $totalAmount, $address, $orderNotes) {
    $sql = "INSERT INTO orders (user_id, total_amount, order_date, status, address, information) 
            VALUES (?, ?, NOW(), 'Pending', ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idss", $userId, $totalAmount, $address, $orderNotes);
    $stmt->execute();
    return $stmt->insert_id;
}

function insertOrderItems($conn, $orderId, $items) {
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    foreach ($items as $item) {
        $stmt->bind_param("iiid", $orderId, $item['productid'], $item['cart_quantity'], $item['price']);
        $stmt->execute();
    }
}

function removeCartItems($conn, $cartId) {
    $sql = "DELETE FROM cart_item WHERE cartid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cartId);
    $stmt->execute();
}

$userId = $_SESSION['user_id'];
$address = $_POST['address'] ?? '';
$orderNotes = $_POST['order_notes'] ?? '';
$shippingCost = $_POST['shipping_options'] ?? 0;

if (empty($address)) {
    die("Alamat harus diisi.");
}

$cartId = getCartId($conn, $userId);

// Ambil semua item dalam keranjang
list($items, $totalAmount) = getCartItems($conn, $cartId);
$totalAmount += $shippingCost;

$conn->begin_transaction();
try {
    $orderId = insertOrder($conn, $userId, $totalAmount, $address, $orderNotes);
    insertOrderItems($conn, $orderId, $items);
    removeCartItems($conn, $cartId);

    $conn->commit();

    header("location: homepage.php");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>

