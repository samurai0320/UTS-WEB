<?php
include 'donnection.php'; 
session_start();
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = '$userId'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$username = $data['username'];
$email = $data['email'];
$full_name = $data['full_name'];
$address = $data['address'];
$city = $data['city'];
$phone_number = $data['phone_number'];
$profile_picture = $data['profile_picture'];

$query = "
    SELECT order_id, status, total_amount, order_date, shipping_service, information 
    FROM orders 
    WHERE user_id = '$userId'
    ORDER BY order_date ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching orders: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="styles.css">
  <style>
    
  </style>
</head>

<body>

<header>
        <div class="logo">
            <img src="logo.jpg" alt="QuillBox Logo">
        </div>
        <nav class="main-nav">
            <a href="homepage.php">Home</a>
            <a href="cart.php">Cart</a>
        </nav>
        <div class="user-profile">
            <span>Welcome, <?php echo $_SESSION['username']; ?></span>
            <div class="user-icon">👤</div>
        </div>
    </header>
    <div class="search-bar"></div>
    <main class="wishlist-page">
        <nav class="breadcrumb">
            <a href="homepage.php">Home</a> / <a>Orders</a>
        </nav>
        <section class="profile-container">
            <div class="sidebar">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-pic">
                <h2><?php echo $username; ?></h2>
                <p><?php echo $email; ?></p>
                <a class="menu-item" href="profile.php">Account info</a>
                <a class="menu-item" href="wishlist.php">wishlist</a>
                <a class="menu-item" href="OrderManagementCust.php">orders</a>
                <a class="menu-item" href="log-in.php">Logout</a>
            </div>
            <div class="account-info">
      <h2 class="fw-bold">My Orders</h2>
      <div class="filters">
     
      </div>
      <table class="table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Status</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Shipping Service</th>
            <th>Information</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($order = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?php echo $order['order_id']; ?></td>
              <td><?php echo $order['status']; ?></td>
              <td>Rp <?php echo number_format($order['total_amount'], 2); ?></td>
              <td><?php echo date('Y-m-d H:i:s', strtotime($order['order_date'])); ?></td>
              <td><?php echo $order['shipping_service']; ?></td>
              <td><?php echo $order['information']; ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  
</body>
</html>

