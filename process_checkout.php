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

function insertOrder($conn, $userId, $totalAmount, $address, $orderNotes, $shipping) {
    $sql = "INSERT INTO orders (user_id, total_amount, order_date, status, address, information, shipping_service) 
            VALUES (?, ?, NOW(), 'Pending', ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idsss", $userId, $totalAmount, $address, $orderNotes, $shipping);
    $stmt->execute();
    return $stmt->insert_id;
}

function insertOrderItems($conn, $orderId, $items) {
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters once outside the loop
    $stmt->bind_param("iiid", $orderId, $productId, $quantity, $price);

    foreach ($items as $item) {
        $productId = $item['productid'];
        $quantity = $item['cart_quantity'];
        $price = $item['price'];

        // Execute the prepared statement for each item
        $stmt->execute();
    }

    $stmt->close();
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
$shippingCost = $_POST['shipping_options'] ?? 0; // Shipping options are sent from the form
$shipping = ''; // To be determined based on shipping cost

if (empty($address)) {
    die("Alamat harus diisi.");
}

$cartId = getCartId($conn, $userId);

// Ambil semua item dalam keranjang
list($items, $totalAmount) = getCartItems($conn, $cartId);
$totalAmount += $shippingCost; // Include shipping cost into the total amount

// Tentukan shipping service berdasarkan shipping cost
if ($shippingCost == 50000) {
    $shipping = "Fast";
} elseif ($shippingCost == 27000) {
    $shipping = "Regular";
} elseif ($shippingCost == 10000) {
    $shipping = "Hemat";
} else {
    $shipping = "Unknown"; // Jika tidak ada yang cocok
}

$conn->begin_transaction();
try {
    $orderId = insertOrder($conn, $userId, $totalAmount, $address, $orderNotes, $shipping);
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


