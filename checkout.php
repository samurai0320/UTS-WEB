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


$sql = "SELECT cart_id FROM cart WHERE user_id = '$userId'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if (!$row) {
    
    $sql = "INSERT INTO cart (user_id) VALUES ('$userId')";
    if ($conn->query($sql) === TRUE) {
        $cartId = $conn->insert_id; 
    } else {
        echo "Error creating cart: " . $conn->error;
        exit();
    }
} else {
    
    $cartId = $row['cart_id'];
}


if (isset($_POST['add-to-cart'])) {
    addToCart($_POST['productId'], $cartId);
}

function addToCart($productId, $cartId) {
    include 'donnection.php';
    
    $sql = "SELECT * FROM cart_item WHERE cartid = '$cartId' AND productid = '$productId'";
    $result = $conn->query($sql);
  
    if ($result->num_rows > 0) {
        
        $sql = "UPDATE cart_item SET cart_quantity = cart_quantity + 1 WHERE cartid = '$cartId' AND productid = '$productId'";
        $conn->query($sql);
    } else {
        
        $sql = "INSERT INTO cart_item (cartid, productid, cart_quantity) VALUES ('$cartId', '$productId', 1)";
        $conn->query($sql);
    }
}


$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $sql = "SELECT * FROM products WHERE name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%' OR category LIKE '%$searchQuery%'"; ;
    $result = $conn->query($sql);
} 
if (isset($_GET['view-all'])) {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <style>
    body {
      background-color: #E1E3EB;
      font-family: Arial, sans-serif;
    }
    .card {
      border-radius: 10px;
      background-color: #fff;
      padding: 20px;
    }
    .btn-primary {
      background-color: #6A5ACD;
      border-color: #6A5ACD;
    }
    .btn-primary:hover {
      background-color: #5A4AC0;
    }
    .img-thumbnail {
      border-radius: 5px;
      width: 60px;
      height: 60px;
    }
    .form-select, .form-control {
      border-radius: 5px;
    }
    .order-summary {
      font-weight: bold;
    }
    .payment-methods .form-check {
      margin-bottom: 10px;
    }
    .card-info input {
      margin-bottom: 10px;
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .text-success {
      color: #28a745 !important;
    }
    .order-total {
      font-size: 1.5rem;
    }
    .dropdown {
        position: relative;
        display: inline-block;
    }


    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        min-width: 160px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        font-size: 14px;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .search-bar-container {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 20px 0;
        background-color: #E7EAF6; 
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .search-category, .search-input, .search-btn {
        padding: 10px;
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .search-category {
        width: 150px;
    }

    .search-input {
        flex: 1;
        max-width: 400px;
    }

    .search-btn {
        background-color: #ffffff; 
        color: black; 
        cursor: pointer;
        transition: background-color 0.3s ease;
        border: 1px solid #6b24b7;
    }

    .search-btn:hover {
        background-color: #4a1783; 
        color: #ffffff;
    }
    .user-profile {
    margin-left: 50px; 
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 20px;
}
  </style>
  <script>
    function updateShippingCost() {
      const shippingSelect = document.getElementById('shipping-options');
      const shippingCost = parseInt(shippingSelect.value);
      const subTotal = 575000; 
      const couponDiscount = 35000;
      const totalElement = document.getElementById('order-total');

      const newTotal = subTotal - couponDiscount + shippingCost;
      totalElement.textContent = Rp${newTotal.toLocaleString()};
    }
  </script>
</head>
<body>
<header>
        <div class="logo">
            <img src="logo.jpg" alt="QuillBox Logo">
        </div>
        <nav class="main-nav">
        <a href="homepage.php">Home</a>
        <div class="dropdown">
            <span><strong>Products</strong></span>
            <div class="dropdown-content">
                <a href="toy.php">Toys</a>
                <a href="clothes.php">Clothes</a>
                <a href="tools.php">Tools</a>
            </div>
        </div>
        <a href="cart.php">Cart</a>
        <a href="log-in.php">Logout</a>
    </nav>

    <div class="search-bar-container">
        <form method="GET" action="">
            <select name="category" class="search-category" onchange="redirectToCategory(this)">
                <option value="">All Categories</option>
                <option value="toy.php">Toys</option>
                <option value="clothes.php">Clothes</option>
                <option value="tools.php">Tools</option>
            </select>
            <input type="text" name="search" placeholder="Search anything..." class="search-input">
            <button type="submit" class="search-btn">Search</button>
        </form>
    </div>


        <div class="user-profile">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <div class="user-icon"><a href="profile.php">👤</a></div>
        </div>
    </header>
  <div class="container py-5">
    <div class="row">
      <!-- Left Section -->
      <div class="col-lg-7">
        <h2 class="mb-3">Checkout</h2>
        
        
        <!-- Item Details -->
        <div class="card mb-4">
          <h5>Item Detail</h5>
          <!-- Item 1 -->
          <div class="d-flex align-items-center justify-content-between mb-3">
            <img src="path/to/binder-image.jpg" alt="Binder" class="img-thumbnail">
            <div>
              <p class="mb-0">Binder Kulit A5 Custom Nama</p>
              <p class="text-muted mb-0">Rp130.000 x3</p>
            </div>
            <button class="btn btn-danger btn-sm">Remove</button>
          </div>
          <!-- Item 2 -->
          <div class="d-flex align-items-center justify-content-between">
            <img src="path/to/tape-image.jpg" alt="Washi Tape" class="img-thumbnail">
            <div>
              <p class="mb-0">Pack Set Washi Tape Masking</p>
              <p class="text-muted mb-0">Rp185.000 x1</p>
            </div>
            <button class="btn btn-danger btn-sm">Remove</button>
          </div>
        </div>

        <!-- Additional Info -->
        <div class="mb-4">
          <label for="order-notes" class="form-label">Additional Information</label>
          <textarea id="order-notes" class="form-control" rows="3" placeholder="Notes about your order..."></textarea>
        </div>

        <!-- Address -->
        <div class="mb-4">
          <label for="address" class="form-label">Your Address</label>
          <input type="text" id="address" class="form-control" placeholder="House number and street name">
        </div>
      </div>

      <!-- Right Section -->
      <div class="col-lg-5">
        <div class="card">
          <div class="text-center mb-3">
            
          </div>

          <!-- Order Summary -->
          <div class="order-summary mb-4">
            <div class="d-flex justify-content-between">
              <p>Sub Total</p>
              <p>Rp575.000</p>
            </div>
            <div class="d-flex justify-content-between">
              <p>Coupon</p>
              <p>-Rp35.000</p>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <p>Shipping</p>
              <select id="shipping-options" class="form-select" onchange="updateShippingCost()">
                <option value="50000">Fast (Rp50.000)</option>
                <option value="27000" selected>Regular (Rp27.000)</option>
                <option value="10000">Hemat (Rp10.000)</option>
              </select>
            </div>
            <hr>
            <div class="d-flex justify-content-between order-total">
              <p>Order Total</p>
              <p id="order-total" class="text-success">Rp620.000</p>
            </div>
          </div>

          <!-- Payment Methods -->
          <div class="payment-methods mb-4">
            <h5>Payment Options</h5>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="paymentOption" id="bankTransfer">
              <label class="form-check-label" for="bankTransfer">Direct Bank Transfer</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="paymentOption" id="cashOnDelivery">
              <label class="form-check-label" for="cashOnDelivery">Cash on Delivery</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="paymentOption" id="paypal">
              <label class="form-check-label" for="paypal">PayPal</label>
            </div>
          </div>

          <!-- Card Information -->
          <div class="card-info mb-4">
            <input type="text" placeholder="Card Number">
            <input type="text" placeholder="MM/YY">
            <input type="text" placeholder="CVC">
            <input type="text" placeholder="Province">
          </div>

          
          <form method="POST" action="process_checkout.php">
            <button type="submit" class="btn btn-primary w-100">Place Order</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>