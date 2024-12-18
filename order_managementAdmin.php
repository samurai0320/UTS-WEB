<?php
include 'donnection.php'; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: log-in.php");
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT username FROM users WHERE user_id = '$userId'";
$result = $conn->query($sql);
$usernames = $result->fetch_assoc()['username'];
$_SESSION['username'] = $usernames;


$sql = "SELECT * FROM orders";
$orderResults = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Management</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="OrderManagement.css" rel="stylesheet"/>
</head>

<body>
  <div class="header">
    <div class="logo">
        <img src="logo.jpg" alt="Logo" height="40" width="40"/>
        <span>QuillBox</span>
    </div>
    <div class="nav">
        <a href="#" class="nav-link">Homes</a>
        <a href="halamankelolaproduk.php" class="nav-link">Products</a>
    </div>
    <div class="user">
        <span>Welcome admin</span>
        <i class="fas fa-user-circle"></i>
    </div>
  </div>

  <div class="content">
    <h2 class="fw-bold">Order management</h2>
    <div class="filters">
      <button class="active">All</button>
      <button>On Process</button>
      <button>Delivered</button>
      <button>Cancelled</button>
      <button>Returned</button>
    </div>

    <table class="table">
      <thead>
       <tr>
        <th>Order ID</th>
        <th>Address</th>
        <th>Status</th>
        <th>Items ID</th> 
        <th>Amount</th>
        <th>Date</th>
        <th>Shipping Service</th>
        <th>Information</th>
        <th>Action</th>
       </tr>
      </thead>
      <tbody>
      <?php
            if ($orderResults && $orderResults->num_rows > 0) {
                while ($order = $orderResults->fetch_assoc()) {
                    $orderId = $order['order_id'];
                    
                    $sqlItems = "SELECT product_id FROM order_items WHERE order_id = '$orderId'";
                    $itemsResults = $conn->query($sqlItems);

                    $productIds = [];
                    while ($item = $itemsResults->fetch_assoc()) {
                        $productIds[] = $item['product_id'];
                    }

                    
                    echo "<tr>";
                    echo "<td>{$order['order_id']}</td>";
                    echo "<td>{$order['address']}</td>";
                    echo "<td>{$order['status']}</td>";
                    echo "<td>" . implode(", ", $productIds) . "</td>"; 
                    echo "<td>{$order['total_amount']}</td>";
                    echo "<td>{$order['order_date']}</td>";
                    echo "<td>{$order['shipping_service']}</td>";
                    echo "<td>{$order['information']}</td>";
                    echo "<td>
                            <a href='edit_order.php?id={$order['order_id']}' class='btn btn-primary btn-sm'>Edit</a>
                            <a href='delete_order.php?id={$order['order_id']}' class='btn btn-danger btn-sm'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No orders found.</td></tr>";
            }
      ?>
      </tbody>
     </table>
   </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

