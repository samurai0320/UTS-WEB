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
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Management</title>
  <!-- Logo -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Custom CSS -->
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
        <span>Welcome New User</span>
        <i class="fas fa-user-circle"></i>
    </div>
  </div>
  <div class="main">
    
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
        <th>address</th>
        <th>Status</th>
        <th>Item</th>
        <th>Date</th>
        <th>Shipping Service</th>
        <th>information</th>
        <th>action</th>
       </tr>
      </thead>
      <tbody>
      <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['order_id']}</td>";
                    echo "<td>{$row['order_number']}</td>";
                    echo "<td>{$row['status']}</td>";
                    echo "<td>{$row['item_count']}</td>";
                    echo "<td>{$row['order_date']}</td>";
                    echo "<td>{$row['shipping_service']}</td>";
                    echo "<td>{$row['tracking_code']}</td>";
                    echo "<td>
                            <a href='edit_order.php?id={$row['order_id']}' class='btn btn-primary btn-sm'>Edit</a>
                            <a href='delete_order.php?id={$row['order_id']}' class='btn btn-danger btn-sm'>Delete</a>
                            <a href='refund_order.php?id={$row['order_id']}' class='btn btn-warning btn-sm'>Refund</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No orders found.</td></tr>";
            }
            ?>
      </tbody>
     </table>
    </main>
   </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
 </body>
</html>

