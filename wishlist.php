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


$sql = "SELECT wishlist_id FROM wishlist WHERE user_id = '$userId'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if (!$row) {
    
    $sql = "INSERT INTO wishlist (user_id) VALUES ('$userId')";
    if ($conn->query($sql) === TRUE) {
        
        $wishlistId = $conn->insert_id; 
    } else {
        echo "Error creating wishlist: " . $conn->error;
        exit();
    }
} else {
    
    $wishlistId = $row['wishlist_id'];
}


if (isset($_POST['add-to-cart'])) {
    addToCart($_POST['productId'], $cartId);
}


if (isset($_POST['toggle-wishlist'])) {
    toggleWishlist($_POST['productId'], $wishlistId);
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


function toggleWishlist($productId, $wishlistId) {
    include 'donnection.php';
    
    
    $sql = "SELECT * FROM wishlist_item WHERE wishlistid = '$wishlistId' AND productid = '$productId'";
    $result = $conn->query($sql);

    
    if ($result->num_rows > 0) {
        $sql = "DELETE FROM wishlist_item WHERE wishlistid = '$wishlistId' AND productid = '$productId'";  
        $conn->query($sql);
    } else {
        
        $sql = "INSERT INTO wishlist_item (wishlistid, productid) VALUES ('$wishlistId', '$productId')";  
        $conn->query($sql);
    }
}

function updatewishlistQuantity($wishlistId, $productId, $quantity) {
    include 'donnection.php';
    $sql = "UPDATE wishlist_item SET wishlist_quantity = '$quantity' WHERE wishlistid = '$wishlistId' AND productid = '$productId'";
    $conn->query($sql);
}

if (isset($_POST['updateQuantity'])) {
    $cartId = $_POST['wishlistId'];
    $productId = $_POST['productId'];
    $quantity = $_POST['quantity'];

    if ($quantity <= 0) {
        $sql = "DELETE FROM wishlist_item WHERE wishlistid = '$wishlistId' AND productid = '$productId'";
        $conn->query($sql);
    } else {
        updateCartQuantity($wishlistId, $productId, $quantity);
    }

    $sql = "SELECT SUM(p.price * ci.wishlist_quantity) AS total_subtotal FROM wishlist_item ci INNER JOIN products p ON ci.productid = p.id WHERE ci.wishlistid = '$wishlistId'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $totalSubtotal = $row['total_subtotal'];
}

$sql = "SELECT wishlist_id FROM wishlist WHERE user_id = '".$_SESSION['user_id']."'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$cartId = $row['wishlist_id'];

$sql = "SELECT ci.*, p.* FROM wishlist_item ci INNER JOIN products p ON ci.productid = p.id WHERE ci.wisshlistid = '$wishlistId'";
$result = $conn->query($sql);
$wishlistItems = array();
while ($row = $result->fetch_assoc()) {
    $wishlistItems[] = $row;
}


$sql = "SELECT * FROM products";
$result = $conn->query($sql);


if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $sql = "SELECT * FROM products WHERE name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%' OR category LIKE '%$searchQuery%'"; 
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
    <title>Wishlist</title>
    <link rel="stylesheet" href="styles.css">
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
    
    <main class="wishlist-page">
        <nav class="breadcrumb">
            <a href="homepage.php">Home</a> / <a>Wishlist</a>
        </nav>

        <section class="wishlist-container">
            <div class="wishlist-items">
                <?php 
                
                while ($row = mysqli_fetch_assoc($result)) { 
                ?>
                    <div class="wishlist-item">
                        <img src="<?php echo htmlspecialchars($row['product_image']); ?>" alt="Product Image">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                            <p>Rp<?php echo number_format($row['product_price'], 0, ',', '.'); ?></p>
                            <div class="quantity-control">
                                <form action="remove_wishlist.php" method="post">
                                    <input type="hidden" name="wishlistItemId" value="<?php echo $row['wishlist_item_id']; ?>">
                                    <button type="submit" name="removeFromWishlist">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php 
                }
                ?>
            </div>
        </section>
    </main>
</body>
</html>



