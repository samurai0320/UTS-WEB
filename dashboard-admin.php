<?php 
include 'donnection.php'; 
session_start();
$userId = $_SESSION['user_id']; 

$sql = "
    SELECT
        (SELECT COUNT(*) FROM users) AS total_users,
        (SELECT COUNT(*) FROM orders) AS total_orders,
        (SELECT SUM(total_amount) FROM orders) AS total_revenue,
        (SELECT COUNT(*) FROM orders WHERE status = 'Processing') AS total_processing_orders;
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

$total_users = $row['total_users'];
$total_orders = $row['total_orders'];
$total_revenue = $row['total_revenue'];
$total_processing_orders = $row['total_processing_orders'];

?>
<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Dashboard Admin</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  
  <style>
   body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #E1E3EB;
        }
        .header {
    background-color: #fff;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e0e0e0;
}

.header .logo {
    display: flex;
    align-items: center;
}

.header .logo img {
    height: 40px;
    margin-right: 10px;
}

.header .nav {
    display: flex;
    align-items: center;
}

.header .nav a {
    margin: 0 15px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
}

.header .user {
    display: flex;
    align-items: center;
}

.header .user span {
    margin-right: 10px;
    font-weight: 500;
}

.header .user i {
    font-size: 24px;
}
        .search-bar {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: #4B3EC4;
            height: 20px;
        }
        .search-bar input [type="text"] {
            flex: 1;
        }
        .search-bar button {
            background-color: #ffffff;
            border: none;
            cursor: pointer;
        }
        .search-bar button {
            height: 20px;
        }
        .main-content {
            padding: 20px;
        }
        .breadcrumb {
            font-size: 14px;
            color: #888;
            margin-bottom: 20px;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .stat-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1;
            margin: 0 10px;
            text-align: center;
        }
        .stat-card h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .stat-card p {
            margin: 5px 0;
            font-size: 14px;
            color: #888;
        }
        .stat-card .icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .stat-card .up {
            color: #4caf50;
        }
        .stat-card .down {
            color: #f44336;
        }
        .latest-orders {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .latest-orders h3 {
            margin: 0 0 20px;
            font-size: 20px;
            font-weight: 700;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        .orders-table th, .orders-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .orders-table th {
            background-color: #f9f9f9;
            font-weight: 500;
        }
        .orders-table td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 10px;
        }
        .orders-table .status {
            font-weight: 500;
        }
        .orders-table .status.pending {
            color: #ff9800;
        }
        .orders-table .status.completed {
            color: #4caf50;
        }
        .orders-table .status.shipping {
            color: #2196f3;
        }
        .orders-table .status.refund {
            color: #f44336;
        }
        .sidebar {
            width: 25%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-right: 20px;
        }
        .sidebar h3 {
            margin: 0 0 20px;
            font-size: 18px;
            font-weight: 700;
        }
        .sidebar .product {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .sidebar .product img {
            width: 50px;
            height: 50px;
            border-radius: 4px;
            margin-right: 10px;
        }
        .sidebar .product .info {
            flex: 1;
        }
        .sidebar .product .info p {
            margin: 0;
            font-size: 14px;
        }
        .sidebar .product .info .price {
            color: #47178E;
        }
        .cart {
            text-align: center;
            margin-top: 30px;
        }
        .cart .progress {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(#4B3EC4 0% 38%, #e0e0e0 38% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        .cart .progress span {
            font-size: 24px;
            font-weight: 700;
        }
        .cart p {
            margin: 10px 0;
            font-size: 14px;
            color: #888;
        }
        .content {
            display: flex;
        }
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
            .sidebar {
                margin-bottom: 20px;
            }
            .stats {
                flex-direction: column;
            }
            .stat-card {
                margin: 10px 0;
            }
        }
  </style>
 </head>

 <body>
 <div class="header">
    <div class="logo">
        <img src="logo.jpg" alt="Logo" height="40" width="40"/>
        <span>QuillBox</span>
    </div>
    <div class="nav">
        <a href="dashboard-admin.php" class="nav-link">Home</a>
        <a href="halamankelolaproduk.php" class="nav-link">Products</a>
        <a href="order_managementAdmin.php" class="nav-link">Orders</a>
    </div>
    <div class="user">
        <span>Welcome admin</span>
        <i class="fas fa-user-circle"></i>
    </div>
  </div>


  <div class="search-bar">
    
    
</div>

  <div class="main-content">
   <div class="breadcrumb">
   </div>

   <div class="content">
    <aside class="sidebar">
     <h3>Best Selling Products</h3>


<?php 
     
     $query = "
    SELECT 
        p.id, 
        p.name, 
        p.image, 
        p.price, 
        SUM(o.quantity) AS total_quantity
    FROM 
        products p
    JOIN 
        order_items o 
    ON 
        p.id = o.product_id
    GROUP BY 
        p.id, p.name, p.image, p.price
    ORDER BY 
        total_quantity DESC
    LIMIT 4;
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching top products: " . mysqli_error($conn));
}

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

foreach ($products as $product): ?>
        <div class="product">
            <img alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 height="50" 
                 src="<?php echo htmlspecialchars($product['image']); ?>" 
                 width="50"/>
            <div class="info">
                <p><?php echo htmlspecialchars($product['name']); ?></p>
                <p class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
    </aside>

    <div class="main">
     <div class="stats">
      <div class="stat-card">
       <div class="icon">
        <i class="fas fa-users"></i>
       </div>
       <h3><?php echo $total_users; ?></h3>
       <p>Total User</p>
       <p class="up">8.5% Up from yesterday</p>
      </div>

      <div class="stat-card">
       <div class="icon">
        <i class="fas fa-box"></i>
       </div>
       <h3><?php echo $total_orders; ?></h3>
       <p>Total Order</p>
       <p class="up">1.3% Up from past week</p>
      </div>

      <div class="stat-card">
       <div class="icon">
        <i class="fas fa-dollar-sign"></i>
       </div>
       <h3>Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></h3>
       <p>Total Revenue</p>
       <p class="up">10% up from yesterday</p>
      </div>

      <div class="stat-card">
       <div class="icon">
        <i class="fas fa-clock"></i>
       </div>
       <h3><?php echo $total_processing_orders; ?></h3>
       <p>Total Processing</p>
       <p class="up">1.8% Up from yesterday</p>
      </div>
     </div>

     <div class="latest-orders">
      <h3>Latest Orders</h3>

      <!-- <?php
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
                        <form method='POST' action='edit_order.php' class='d-inline'>
                            <input type='hidden' name='order_id' value='{$order['order_id']}'>
                            <select name='status' class='form-select form-select-sm'>
                                
                                <option value='Processing'>Processing</option>
                                <option value='Paid'>Paid</option>
                            </select>
                            <button type='submit' class='btn btn-success btn-sm'>Update</button>
                        </form>
                        <form method='POST' action='delete_order.php' class='d-inline'>
                            <input type='hidden' name='order_id' value='{$order['order_id']}'>
                            <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No orders found.</td></tr>";
        }
      ?> -->

      <table class="orders-table">
       <thead>
        <tr>
         <th>Order Id</th>
         <th>Address</th>
         <th>Date</th>
         <th>Total Amount</th>
         <th>Shipping Service</th>
         <th>Status</th>
         <th>Information</th>
        </tr>
       </thead>

       <tbody>
        <?php 
        $sql = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 7";

        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            die("Error executing query: " . mysqli_error($conn));
        }

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['order_id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['address']) . '</td>';
            echo '<td>' . date('M j, Y', strtotime($row['order_date'])) . '</td>';
            echo '<td>Rp ' . number_format($row['total_amount'], 0, ',', '.') . '</td>';
            echo '<td>' . htmlspecialchars($row['shipping_service']) . '</td>';
            echo '<td class="status ' . strtolower($row['status']) . '">' . htmlspecialchars($row['status']) . '</td>';
            echo '<td>'
                . htmlspecialchars($row['information']) . 
                  '</td>';
            echo '</tr>';
        }
        ?>
       </tbody>
      </table>
     </div>
    </div>
   </div>
  </div>
 </body>
</html>