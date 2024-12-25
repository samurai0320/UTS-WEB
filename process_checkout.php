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
    $tax = 12000;

    while ($item = $result->fetch_assoc()) {
        $items[] = $item;
        $totalAmount += $item['cart_quantity'] * $item['price']+$tax;
    }

    return [$items, $totalAmount];
}

function insertOrder($conn, $userId, $totalAmount, $address, $orderNotes, $shipping) {
    $sql = "INSERT INTO orders (user_id, total_amount, order_date, status, address, information, shipping_service) 
            VALUES (?, ?, NOW(), 'Processing', ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idsss", $userId, $totalAmount, $address, $orderNotes, $shipping);
    $stmt->execute();
    return $stmt->insert_id;
}

function insertOrderItems($conn, $orderId, $items) {
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("iiid", $orderId, $productId, $quantity, $price);

    foreach ($items as $item) {
        $productId = $item['productid'];
        $quantity = $item['cart_quantity'];
        $price = $item['price'];

        $stmt->execute();

        updateProductStock($conn, $productId, $quantity);
    }

    $stmt->close();
}

function removeCartItems($conn, $cartId) {
    $sql = "DELETE FROM cart_item WHERE cartid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cartId);
    $stmt->execute();
}

function updateProductStock($conn, $productId, $quantitySold) {
    $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantitySold, $productId);
    $stmt->execute();
}

function getUserEmail($conn, $userId) {
    $sql = "SELECT email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['email'];
}

$userId = $_SESSION['user_id'];
$address = $_POST['address'] ?? '';
$orderNotes = $_POST['order_notes'] ?? '';
$shippingCost = $_POST['shipping_options'] ?? 0;
$shipping = '';

if (empty($address)) {
    die("Alamat harus diisi.");
}

$cartId = getCartId($conn, $userId);
list($items, $totalAmount) = getCartItems($conn, $cartId);
$totalAmountWithShipping = $totalAmount + $shippingCost+12000; 

if ($shippingCost == 50000) {
    $shipping = "Fast";
} elseif ($shippingCost == 27000) {
    $shipping = "Regular";
} elseif ($shippingCost == 10000) {
    $shipping = "Hemat";
} else {
    $shipping = "Unknown";
}

$conn->begin_transaction();
try {
    $orderId = insertOrder($conn, $userId, $totalAmountWithShipping, $address, $orderNotes, $shipping);
    insertOrderItems($conn, $orderId, $items);
    removeCartItems($conn, $cartId);
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    die("Terjadi kesalahan: " . $e->getMessage());
}

$email = getUserEmail($conn, $userId);


require_once 'midtrans-php-master/Midtrans.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-CV_8shkSMu_ueN7eEpLVsE61';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$totalAmountWithShipping+=12000;
$params = array(
    'transaction_details' => array(
        'order_id' => $orderId,
        'gross_amount' => max($totalAmountWithShipping, 0.01), 
    ),
    'customer_details' => array(
        'first_name' => $_SESSION['username'],
        'email' => $email,
    ),
    'item_details' => array_merge(
        array_map(function($item) {
            return array(
                'id' => $item['productid'],
                'price' => $item['price'],
                'quantity' => $item['cart_quantity'],
                'name' => 'Product ' . $item['productid']
            );
        }, $items),
        [
            array(
                'id' => 'shipping_cost',
                'price' => $shippingCost,
                'quantity' => 1,
                'name' => 'Shipping Cost (' . $shipping . ')'
            ),
            array(
                'id' => 'tax',
                'price' => 12000, 
                'quantity' => 1,
                'name' => 'Tax (12000)'
            )
            
        ]
        
    )
);


try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    echo json_encode(['snapToken' => $snapToken]);
} catch (Exception $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>

