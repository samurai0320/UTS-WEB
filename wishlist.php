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
function updateAccountCredentials($username, $email, $password, $userId,$full_name, $address, $city, $phone_number) {
    include 'donnection.php';

  $sql = "UPDATE users SET username = '$username', full_name = '$full_name', address = '$address', city = '$city', phone_number = '$phone_number', email = '$email', password = '$password' WHERE user_id = '$userId'";
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
    updateAccountCredentials($username, $email,$full_name, $address, $city, $phone_number, $password, $userId);
    $pass = "UPDATE users SET username = '$username', 
                full_name = '$full_name', 
                email = '$email', 
                address = '$address', 
                city = '$city', 
                phone_number = '$phone_number', 
                password = '$password' 
            WHERE user_id = '$userId'";
    $result= mysqli_query($conn, $pass);
    $_SESSION['username'] = $username;
  }

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
    $sql = "SELECT * FROM products WHERE name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%'";
    $result = $conn->query($sql);
} 
if (isset($_POST['view-all'])) {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
}
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
// 
$sql = "SELECT wishlist_id FROM wishlist WHERE user_id = '$userId'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if (!$row) {
    
    $sql = "INSERT INTO wishlist (wishlist_id) VALUES ('$userId')";
    if ($conn->query($sql) === TRUE) {
        $wishlistId = $conn->insert_id; 
    } else {
        echo "Error creating wishlist: " . $conn->error;
        exit();
    }
} else {
    
    $wishlistId = $row['wishlist_id'];
}


if (isset($_POST['add-to-wishlist'])) {
    addToCart($_POST['productId'], $wishlistId);
}

function addToWishlist($productId, $wishlistId) {
    include 'donnection.php';
    
    $sql = "SELECT * FROM wishlist_item WHERE wishlistid = '$wishlistId' AND productid = '$productId'";
    $result = $conn->query($sql);
  
    if ($result->num_rows > 0) {
        
        $sql = "UPDATE wishlist_item SET wishlist_quantity = wishlist_quantity + 1 WHERE wishlistid = '$wishlistId' AND productid = '$productId'";
        $conn->query($sql);
    } else {
        
        $sql = "INSERT INTO wishlist_item (wishlistid, productid, wishlist_quantity) VALUES ('$wishlistId', '$productId', 1)";
        $conn->query($sql);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    <script>
        function redirectToCategory(selectElement) {
            const selectedValue = selectElement.value;
            if (selectedValue) {
                window.location.href = selectedValue; 
            }
        }
    </script>

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
    <div class="search-bar">
        
        
    </div>

    <main class="profile-page">
        <nav class="breadcrumb">
            <a href="homepage.php">Home</a> / <a>pages</a> / <span>wishlist</span>
        </nav>

        <section class="profile-container">
            <div class="sidebar">
                <img src="ayaka.jpg" alt="User Profile Picture" class="profile-pic">
                <h2><?php echo $username; ?></h2>
                <p><?php echo $email; ?></p>
                <a class="menu-item" href="profile.php">Account info</a>
                <a class="menu-item" href="wishlist.php">Wishlist</a>
                <a class="menu-item" href="orders.php">Orders</a>
                <a class="menu-item" href="log-in.php">Logout</a>
            </div>

            <div class="wishlist-info">
                <h2>My Wishlist</h2>
                <div class="wishlist-items">
                    <?php 
                    $query = "SELECT * FROM wishlist WHERE user_id = '$userId'";
                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="wishlist-item">';
                        echo '<img src="' . $row['product_image'] . '" alt="' . $row['product_name'] . '" class="product-img">';
                        echo '<div class="item-details">';
                        echo '<h3>' . $row['product_name'] . '</h3>';
                        echo '<p>Added on: ' . $row['added_date'] . '</p>';
                        echo '<form method="POST" action="remove_wishlist.php">';
                        echo '<input type="hidden" name="wishlist_id" value="' . $row['id'] . '">';
                        echo '<button type="submit" class="remove-btn">Remove</button>';
                        echo '</form>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

