<?php
include 'donnection.php'; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: log-in.php");
    exit;
}

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

function updateAccountCredentials($username, $email, $password, $userId, $full_name, $address, $city, $phone_number) {
    include 'donnection.php';
    $sql = "UPDATE users SET username = '$username', full_name = '$full_name', address = '$address', city = '$city', 
            phone_number = '$phone_number', email = '$email', password = '$password' WHERE user_id = '$userId'";
    $conn->query($sql);
}

if (isset($_POST["submit-btn"])) {
    include 'donnection.php';
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $userId = $_SESSION['user_id'];

    updateAccountCredentials($username, $email, $full_name, $address, $city, $phone_number, $password, $userId);

    $pass = "UPDATE users SET username = '$username', full_name = '$full_name', email = '$email', 
             address = '$address', city = '$city', phone_number = '$phone_number', password = '$password' 
             WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $pass);
    $_SESSION['username'] = $username;
}

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

function deleteFromWishlist($productId, $wishlistId) {
    include 'donnection.php';
    $sql = "DELETE FROM wishlist_item WHERE wishlistid = '$wishlistId' AND productid = '$productId'";
    return $conn->query($sql);
}


if (isset($_POST['delete-wishlist'])) {
    $productId = $_POST['productId'];

    if (deleteFromWishlist($productId, $wishlistId)) {
        
    } else {
        
    }
}
function addToCart($productId, $cartId) {
    include 'donnection.php';

    
    $sql = "SELECT quantity FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    $availableQuantity = $product['quantity'];

    
    $sql = "SELECT cart_quantity FROM cart_item WHERE cartid = ? AND productid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cartId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItem = $result->fetch_assoc();
    $stmt->close();

    $currentCartQuantity = $cartItem ? $cartItem['cart_quantity'] : 0;

    
    if ($currentCartQuantity >= $availableQuantity) {
        die(header("Location: wishlist.php"));
    }

    
    if ($cartItem) {
        $sql = "UPDATE cart_item SET cart_quantity = cart_quantity + 1 WHERE cartid = ? AND productid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $cartId, $productId);
        $stmt->execute();
        $stmt->close();
    } else {
        $sql = "INSERT INTO cart_item (cartid, productid, cart_quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $cartId, $productId);
        $stmt->execute();
        $stmt->close();
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

    $sql = "SELECT SUM(p.price * ci.wishlist_quantity) AS total_subtotal FROM wishlist_item ci 
            INNER JOIN products p ON ci.productid = p.id WHERE ci.wishlistid = '$wishlistId'";
    $results = $conn->query($sql);
    $row = $results->fetch_assoc();
    $totalSubtotal = $row['total_subtotal'];
}

$sql = "SELECT wishlist_id FROM wishlist WHERE user_id = '".$_SESSION['user_id']."'";
$results = $conn->query($sql);
$row = $results->fetch_assoc();
$cartId = $row['wishlist_id'];

$sql = "SELECT ci.*, p.* FROM wishlist_item ci 
        INNER JOIN products p ON ci.productid = p.id WHERE ci.wishlistid = '$wishlistId'";
$results = $conn->query($sql);
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
    <style>
        .add-to-cart {
    width: 100%;
    padding: 10px;
    background-color: #8A2BE2;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    margin-top: 10px;
    font-size: 14px;
}

.add-to-cart:hover {
    background-color: #6b24b7;
}
.delete-btn {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
}

.delete-btn:hover {
    background-color: #c0392b;
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
            <a href="homepage.php">Home</a> / <a>Wishlist</a>
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
                <h2>Wishlist</h2>
                <?php 
                while ($row = mysqli_fetch_assoc($results)) { 
                ?>
                    <div class="wishlist-item">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p>Rp<?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                            <div class="quantity-control">
                                <form action="" method="post">
                                    <input type="hidden" name="productId" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="add-to-cart" name="add-to-cart">Add to Cart</button>
                                </form>
                                <form action="" method="post" style="display: inline-block;">
                                    <input type="hidden" name="productId" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete-wishlist" class="delete-btn">Remove</button>
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




