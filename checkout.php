<?php
include 'donnection.php'; 
session_start();

if (!empty($_POST['selectedItems'])) {
  $selectedItems = explode(',', $_POST['selectedItems']);
} elseif (!empty($_SESSION['selectedItems'])) {
  $selectedItems = $_SESSION['selectedItems'];
} else {
  $selectedItems = [];
}
$totalQuantity = 0;
$totalSubtotal = 0;

if (!isset($_SESSION['user_id'])) {
    header("location: log-in.php");
    exit;
}

$userId = $_SESSION['user_id'];
$sqcart = "SELECT cart_id FROM cart WHERE user_id = '$userId';";
$result = $conn->query($sqcart);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cartId = $row['cart_id'];
} else {
    echo "Cart ID not found for user ID: " . $userId;
}

$sql = "SELECT ci.*, p.* FROM cart_item ci INNER JOIN products p ON ci.productid = p.id WHERE ci.cartid = '$cartId'";
$result = $conn->query($sql);
$cartItems = array();
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}
$tax = 12000;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-cAQ82EqHwx_gi0LN"></script>
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
        const shippingCost = parseInt(shippingSelect.value, 10);
        const subTotal = parseInt(document.getElementById('subtotal').getAttribute('data-value'), 10);
        const totalElement = document.getElementById('order-total');
        const newTotal = subTotal + shippingCost;
        totalElement.textContent = `Rp${newTotal.toLocaleString()}`;
    }
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.remove-item').forEach(function (button) {
            button.addEventListener('click', function () {
                const itemId = this.getAttribute('data-item-id');
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'checkout.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        console.log('Item removed successfully');
                        button.closest('.d-flex').remove();
                    } else {
                        console.log('Error removing item: ' + xhr.status);
                    }
                };
                xhr.send('item_id=' + encodeURIComponent(itemId));
            });
        });
    });
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
        <h2 class="mb-3">Checkout</h2>
        <div class="col card mb-4">
            <h5>Item Detail</h5>
            <?php
            foreach ($cartItems as $item) {
                $subtotal = $item['price'] * $item['cart_quantity'];
                $totalQuantity += $item['cart_quantity'];
                $totalSubtotal += $subtotal;

                echo "<div class='d-flex align-items-center justify-content-between mb-3'>";
                echo "<img src='" . htmlspecialchars($item['image']) . "' alt='Product Image' class='img-thumbnail'>";
                echo "<div>";
                echo "<p class='mb-0'>" . htmlspecialchars($item['name']) . "</p>";
                echo "<p class='text-muted mb-0'>" . number_format($item['price'], 0, ',', '.') . " x " . $item['cart_quantity'] . "</p>";
                echo "</div>";
                echo '<form action="" method="post">';
                echo "<button type='button' class='remove-item btn btn-danger btn-sm' data-item-id='" . $item['productid'] . "'>Remove</button>";
                echo '</form>';
                echo "</div>";
            }
            ?>
        </div>
        <form id="payment-form" method="POST" action="process_checkout.php">
            <div class="mb-4">
                <label for="order_notes" class="form-label">Additional Information</label>
                <textarea id="order_notes" name="order_notes" class="form-control" rows="3" placeholder="Notes about your order..."></textarea>
            </div>
            <div class="mb-4">
                <label for="address" class="form-label">Your Address</label>
                <input type="text" id="address" name="address" class="form-control" placeholder="House number and street name">
            </div>
            <div class="col col-lg-5">
                <div class="card">
                    <div class="order-summary mb-4">
                        <div class="d-flex justify-content-between">
                            <p>Sub Total + Tax</p>
                            
                            <p id="subtotal" data-value="<?php echo $totalSubtotal+$tax; ?>">
                                Rp<?php echo number_format($totalSubtotal+$tax, 0, ',', '.'); ?>
                            </p>
                            
                        </div>
                        
                            
                            
                        
                        <input type="hidden" id="subtotal-hidden" name="subtotal" value="<?php echo $totalSubtotal; ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <p>Shipping</p>
                            <select id="shipping-options" name="shipping_options" class="form-select" onchange="updateShippingCost()">
                                <option value="50000">Fast (Rp50.000)</option>
                                <option value="27000" selected>Regular (Rp27.000)</option>
                                <option value="10000">Hemat (Rp10.000)</option>
                            </select>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between order-total">
                            <p>Order Total</p>
                            <p id="order-total" class="text-success">
                                Rp<?php echo number_format($totalSubtotal+$tax + 27000, 0, ',', '.'); ?>
                            </p>
                            <input type="hidden" id="order-total-hidden" name="order_total" value="<?php echo $totalSubtotal+$tax + 27000; ?>">
                        </div>
                    </div>
                    <button id="pay-button" type="button" class="btn btn-primary w-100">Place Order</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    document.getElementById('pay-button').addEventListener('click', function (event) {
        event.preventDefault();
        var form = document.getElementById('payment-form');
        var formData = new FormData(form);

        fetch('process_checkout.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            window.snap.pay(data.snapToken, {
                onSuccess: function(result){
                    console.log(result);
                    window.location.href = 'homepage.php';
                },
                onPending: function(result){
                    console.log(result);
                    window.location.href = 'homepage.php'; 
                },
                onError: function(result){
                    console.log(result);
                    window.location.href = 'homepage.php'; 
                },
                onClose: function() {
            
                
                window.location.href = 'homepage.php'; 
                }
            });
        })
        .catch(error => console.error('Error:', error));
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>