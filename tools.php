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
        die(header("Location: homepage.php"));
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


$sql = "SELECT * FROM products WHERE category = 'tools'";
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
    <title>Toys</title>
    <script>
        function redirectToCategory(selectElement) {
            const selectedValue = selectElement.value;
            if (selectedValue) {
                window.location.href = selectedValue; 
            }
        }
    </script>
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
            <div class=dropdown-content>
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
                <option value="tools.php">Tools</option>
                <option value="homepage.php">All Categories</option>
                <option value="toy.php">Toys</option>
                <option value="clothes.php">Clothes</option>
                
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

    <main>
        <section class="product-section">
            <h2>Tools</h2>
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