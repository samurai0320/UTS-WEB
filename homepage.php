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
    <title>Home</title>
    <script>
        function redirectToCategory(selectElement) {
            const selectedValue = selectElement.value;
            if (selectedValue) {
                window.location.href = selectedValue; 
            }
        }

        function toggleWishlist(productId, element) {
            const formData = new FormData();
            formData.append('toggle-wishlist', true);
            formData.append('productId', productId);

            fetch('', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    element.classList.toggle('wishlist-active');
                }
            });
        }
    </script>
    <link rel="stylesheet" href="style.css">
    <style>
    .product-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .wishlist {
        font-size: 50px;
        cursor: pointer;
        color: #ccc;
        transition: color 0.3s ease;
        margin-bottom: 10px;
    }

    .wishlist.wishlist-active {
        color: #ff4081;
    }

    .add-to-cart {
        margin-top: 10px;
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

    <main>
        <section class="product-section">
            <h2></h2>
            <div class="product-container">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        
                        
                        $isInWishlist = $conn->query("SELECT * FROM wishlist_item WHERE wishlistid = '$wishlistId' AND productid = '{$row['id']}'")->num_rows > 0;
                        $wishlistClass = $isInWishlist ? 'wishlist wishlist-active' : 'wishlist';

                        echo '<div class="product-card">';
                        echo '<a href="OpenItem.php?productId=' . $row['id'] . '">';
                        echo '<img src="' . $row['image'] . '" alt="' . htmlspecialchars($row['name']) . '">';
                        echo '</a>';
                        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                        echo '<p>Harga satu set</p>';
                        echo '<p>Qty: ' . htmlspecialchars($row['quantity']) . '</p>';
                        echo '<p class="price">Rp' . htmlspecialchars($row['price']) . '</p>';
                        echo '<span class="' . $wishlistClass . '" onclick="toggleWishlist(' . $row['id'] . ', this)">&#9825;</span>';
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
            <form action="" method="post">
            <button class="view-all" name="view-all">See All Products</button></form>
        </section>
    </main>
</body>
</html>

<?php
$conn->close(); 
?>


