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

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
    $sql = "SELECT * FROM products WHERE name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%' OR category LIKE '%$searchQuery%'"; ;
    $result = $conn->query($sql);
} 
if (isset($_POST['view-all'])) {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="logo.gif" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenItem</title>
    <script>
        function redirectToCategory(selectElement) {
            const selectedValue = selectElement.value;
            if (selectedValue) {
                window.location.href = selectedValue; 
            }
        }
    </script>
    <link rel="stylesheet" href="styless.css">
    <style>
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
.logo {
    display: flex;
    align-items: center;
    font-size: 24px;
    font-weight: bold;
    margin-right: 40px;
}

.logo img {
    height: 100px;
    margin-right: 10px;
}
.main-nav {
    display: flex;
    gap: 20px;
    margin-right: auto;
}
header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.main-nav a {
    text-decoration: none;
    color: #333;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 5px;
}
</style>
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



        <div class="user-profile">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <div class="user-icon"><a href="profile.php">👤</a></div>
        </div>
    </header>

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
