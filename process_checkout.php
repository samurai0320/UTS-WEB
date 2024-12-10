<?php
include 'donnection.php';
session_start();

// Pastikan pengguna login
if (!isset($_SESSION['user_id'])) {
    header("location: log-in.php");
    exit();
}

// Ambil user ID dan data dari form
$userId = $_SESSION['user_id'];
$address = $_POST['address'];
$orderNotes = $_POST['order_notes'];
$paymentOption = $_POST['paymentOption'];
$shippingCost = $_POST['shipping_options'];

// Dapatkan cart_id pengguna
$sql = "SELECT cart_id FROM cart WHERE user_id = '$userId'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
if (!$row) {
    die("Cart tidak ditemukan.");
}
$cartId = $row['cart_id'];

// Ambil data item dari keranjang
$sql = "SELECT * FROM cart_item WHERE cartid = '$cartId'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Keranjang kosong.");
}

// Hitung total order
$subTotal = 0;
$orderItems = [];
while ($item = $result->fetch_assoc()) {
    $productId = $item['productid'];
    $quantity = $item['cart_quantity'];

    // Ambil harga produk
    $sql = "SELECT price FROM products WHERE product_id = '$productId'";
    $productResult = $conn->query($sql);
    $product = $productResult->fetch_assoc();
    $price = $product['price'];

    $subTotal += $price * $quantity;
    $orderItems[] = [
        'product_id' => $productId,
        'quantity' => $quantity,
        'price' => $price
    ];
}

// Hitung total keseluruhan
$couponDiscount = 35000; // Sesuai data checkout Anda
$total = $subTotal - $couponDiscount + $shippingCost;

// Simpan ke tabel `order`
$sql = "INSERT INTO `order` (user_id, address, notes, payment_method, subtotal, discount, shipping_cost, total)
        VALUES ('$userId', '$address', '$orderNotes', '$paymentOption', '$subTotal', '$couponDiscount', '$shippingCost', '$total')";
if ($conn->query($sql) === TRUE) {
    $orderId = $conn->insert_id;

    // Simpan ke tabel `order_items`
    foreach ($orderItems as $item) {
        $productId = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];

        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES ('$orderId', '$productId', '$quantity', '$price')";
        $conn->query($sql);
    }

    // Kosongkan keranjang pengguna
    $sql = "DELETE FROM cart_item WHERE cartid = '$cartId'";
    $conn->query($sql);

    echo "Pesanan berhasil dibuat!";
    header("location: order_success.php");
} else {
    echo "Error: " . $conn->error;
}
?>
