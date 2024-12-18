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
      <button class="create-order">+ Create Order</button>
     </div>
     <table class="table">
      <thead>
       <tr>
        <th>Order ID</th>
        <th>Order Number</th>
        <th>Status</th>
        <th>Item</th>
        <th>Date</th>
        <th>Shipping Service</th>
        <th>Tracking Code</th>
       </tr>
      </thead>
      <tbody>
       <tr>
        <td>00001</td>
        <td>59217342</td>
        <td><span class="status on-the-way">On the Way</span></td>
        <td>1</td>
        <td>05/12/2024</td>
        <td>Hemat</td>
        <td>940010010936113003113</td>
        <td>
            <i class="fas fa-edit edit"></i>
        </td>
       </tr>
       <tr>
        <td>00002</td>
        <td>81736193</td>
        <td><span class="status on-process">On Process</span></td>
        <td>2</td>
        <td>07/12/2024</td>
        <td>Fast</td>
        <td>940010010936113003113</td>
        <td>
            <i class="fas fa-edit edit"></i>
        </td>
       </tr>
       <tr>
        <td>00003</td>
        <td>59217344</td>
        <td><span class="status delivered">Delivered</span></td>
        <td>12</td>
        <td>07/12/2024</td>
        <td>Regular</td>
        <td>940010010936113003113</td>
        <td>
            <i class="fas fa-edit edit"></i>
        </td>
       </tr>
       <tr>
        <td>00129</td>
        <td>59217345</td>
        <td><span class="status cancelled">Cancelled</span></td>
        <td>22</td>
        <td>28/11/2024</td>
        <td>Regular</td>
        <td>940010010936113003113</td>
        <td>
            <i class="fas fa-edit edit"></i>
        </td>
       </tr>
       <tr>
        <td>00194</td>
        <td>59217346</td>
        <td><span class="status returned">Returned</span></td>
        <td>32</td>
        <td>21/11/2024</td>
        <td>Regular</td>
        <td>940010010936113003113</td>
        <td>
            <i class="fas fa-edit edit"></i>
        </td>
       </tr>
       <tr>
        <td>00200</td>
        <td>59217346</td>
        <td><span class="status draft">Draft</span></td>
        <td>41</td>
        <td>17/11/2024</td>
        <td>Regular</td>
        <td>940010010936113003113</td>
        <td>
            <i class="fas fa-edit edit"></i>
        </td>
       </tr>
      </tbody>
     </table>
    </main>
   </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
 </body>
</html>

