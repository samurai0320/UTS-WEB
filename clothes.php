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


$sql = "SELECT * FROM products WHERE category = 'clothes'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toys</title>
    <link rel="stylesheet" href="style.css">
    <style>

        .dropdown {
        position: relative;
        display: inline-block;
        }

        .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        }

        .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        }

        .dropdown-content a:hover {background-color: #f1f1f1}

        .dropdown:hover .dropdown-content {
            display: block;
        }
        .dropbtn {
            position: relative;
            display: inline-block;
            font-weight: bold;
            text-decoration: none;
            color: black;
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
    </nav>
    <div class="user-profile">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <div class="user-icon"><a href="profile.php">👤</a></div>
</header>

    <main>
        <section class="product-section">
            <h2>Clothes</h2>
            <div class="product-container">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="product-card">';
                        echo '<a href="OpenItem.php?productId=' . $row['id'] . '">';
                        echo '<img src="' . $row['image'] . '" alt="' . htmlspecialchars($row['name']) . '">';
                        echo '</a>';
                        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                        echo '<p>Harga satu set</p>';
                        echo '<p>Qty: ' . htmlspecialchars($row['quantity']) . '</p>';
                        echo '<p class="price">Rp' . htmlspecialchars($row['price']) . '</p>';
                        echo '<form action="" method="post">';
                        echo '<input type="hidden" name="productId" value="' . $row['id'] . '">';
                        echo '<button type="submit" class="add-to-cart" name="add-to-cart">Add to Cart</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>No products found.</p>";
                }
                ?>
            </div>
            <button class="view-all">See All Products</button>
        </section>
    </main>
</body>
</html>

<?php
$conn->close(); 
?>  