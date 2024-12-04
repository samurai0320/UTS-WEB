<?php
include 'donnection.php';
session_start();
$productId = $_GET['productId'];

$sql = "SELECT * FROM products WHERE id = '$productId'";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

if (isset($_POST['add-to-cart'])) {
    addToCart($_POST['productId'], $_POST['cartId']);
}

$sql = "SELECT cart_id FROM cart WHERE user_id = '".$_SESSION['user_id']."'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$cartId = $row['cart_id'];

function addToCart($productId) {
    include 'donnection.php';
    $sql = "SELECT cart_id FROM cart WHERE user_id = '".$_SESSION['user_id']."'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $cartId = $row['cart_id'];
    
    if (!isset($_SESSION['user_id'])) {
      
      
      header("location: login.php");
      exit;
    }
  
    
    $userId = $_SESSION['user_id'];
  
    
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="logo.gif" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenItem</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>

    <header>
        <div class="navbar">
            <div class="navbar-left">
                <img src="logo.jpg" alt="Logo">
            </div>
            <div class="main-nav">
                <a href="homepage.php">Homes </a>
                <a >Products </a>
                <a href="cart.php">Cart </a>
            </div>
            <div class="user-profile">
                <span>Welcome, New User</span>
                <div class="user-icon"><a href="profile.php">👤</a></div>
            </div>
        </div>
    </header>

    <header2>
        <div class="search-bar">
            <select>
                <option>All Categories</option>
            </select>
            <input type="text" placeholder="Search anything...">
            <button>
                <img src="search.png" alt="Search Icon">
            </button>
        </div>
    </header2>

    <main>
        <div class="container">
            <div class="breadcrumb">
                <a href = "homepage.php" style="color: #4B3EC4;">Home</a> / <a style="color: #4B3EC4;">pages</a> / Product Details
            </div>
        
            <div class="product-image">
                <?php echo '<img src="' . $product['image'] . '" alt="' . $product['name'] . '">';?>
            </div>
        
            <div class="product-details">
                <?php
                echo '<h1>' . $product['name'] . '</h1>';
                echo '<p>Qty: ' . htmlspecialchars($product['quantity']) . '</p>';
                echo '<p>Deskripsi: ' . htmlspecialchars($product['description']) . '</p>';
                echo '<p>Price: ' . htmlspecialchars($product['price']) . '</p>';
                echo '<form action="" method="post">';
                echo '<input type="hidden" name="productId" value="' . $product['id'] . '">';
                echo '<input type="hidden" name="cartId" value="' . $cartId . '">';
                echo '<button type="submit" class="add-to-cart" name="add-to-cart">Add to Cart</button>';
                echo '</form>';
                ?>
            </div>
        </div>        
    </main>

</body>
</html>
