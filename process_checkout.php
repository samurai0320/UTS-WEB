<?php
include 'donnection.php'; // Pastikan nama file sudah benar
session_start();

// Pastikan pengguna login
if (!isset($_SESSION['user_id'])) {
    header("location: log-in.php");
    exit();
}

// Ambil user ID dan data dari form
$userId = $_SESSION['user_id'];
$address = $_POST['address'] ?? '';
$orderNotes = $_POST['order_notes'] ?? '';
$paymentOption = $_POST['paymentOption'] ?? '';
$shippingCost = $_POST['shipping_options'] ?? 0;

// Validasi input (pastikan tidak ada nilai kosong)
if (empty($address) ) {
    die("Alamat  harus diisi.");
}

// Dapatkan cart_id pengguna
$sql = "SELECT cart_id FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if (!$row) {
    die("Keranjang tidak ditemukan.");
}
$cartId = $row['cart_id'];

// Ambil data item dari keranjang
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

// Tambahkan biaya pengiriman ke total
$totalAmount += $shippingCost;

// Mulai transaksi
$conn->begin_transaction();
try {
    // Insert ke tabel orders
    $sql = "INSERT INTO orders (user_id, total_amount, order_date, status, address, information) 
            VALUES (?, ?, NOW(), 'Pending', ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idss", $userId, $totalAmount, $address, $orderNotes);
    $stmt->execute();
    
    $orderId = $stmt->insert_id; // Dapatkan order_id terakhir

    // Insert ke tabel order_items
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    foreach ($items as $item) {
        $stmt->bind_param("iiid", $orderId, $item['productid'], $item['cart_quantity'], $item['price']);
        $stmt->execute();
    }

    // Commit transaksi
    $conn->commit();

    echo "Pesanan berhasil dibuat dengan ID: $orderId";

} catch (Exception $e) {
    // Rollback jika terjadi kesalahan
    $conn->rollback();
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>