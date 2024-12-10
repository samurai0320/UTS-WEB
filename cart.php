<?php
include 'donnection.php'; 
session_start();

function updateCartQuantity($cartId, $productId, $quantity) {
    include 'donnection.php';
    $sql = "UPDATE cart_item SET cart_quantity = '$quantity' WHERE cartid = '$cartId' AND productid = '$productId'";
    $conn->query($sql);
}

if (isset($_POST['updateQuantity'])) {
    $cartId = $_POST['cartId'];
    $productId = $_POST['productId'];
    $quantity = $_POST['quantity'];

    if ($quantity <= 0) {
        $sql = "DELETE FROM cart_item WHERE cartid = '$cartId' AND productid = '$productId'";
        $conn->query($sql);
    } else {
        updateCartQuantity($cartId, $productId, $quantity);
    }

    $sql = "SELECT SUM(p.price * ci.cart_quantity) AS total_subtotal FROM cart_item ci INNER JOIN products p ON ci.productid = p.id WHERE ci.cartid = '$cartId'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $totalSubtotal = $row['total_subtotal'];
}

$sql = "SELECT cart_id FROM cart WHERE user_id = '".$_SESSION['user_id']."'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$cartId = $row['cart_id'];

$sql = "SELECT ci.*, p.* FROM cart_item ci INNER JOIN products p ON ci.productid = p.id WHERE ci.cartid = '$cartId'";
$result = $conn->query($sql);
$cartItems = array();
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}
?>
<html lang="en">
<head>
    <link rel="icon" href="logo.gif" type="image/x-icon">   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Page</title>
    <link rel="stylesheet" href="stylesss.css">
</head>

<header>
    <div class="navbar">
        <div class="navbar-left">
            <img src="logo.jpg" alt="Logo">
        </div>
        <div class="main-nav">
            <a href="homepage.php">Homes</a>
            <a href="cart.php">Cart</a>
        </div>
        <div class="user-profile">
            <span>Welcome, New User</span>
            <div class="user-icon"><a href="profile.php">👤</a></div>
        </div>
    </div>
</header>

<nav class="breadcrumb">
    <a href="homepage.php" style="color: #4B3EC4;">Home</a> / <a style="color: #4B3EC4;">Pages</a> / <span>Cart</span>
</nav>

<div class="container">
    <div class="cart-items">
        <?php $totalSubtotal = 0; ?>
        <?php foreach ($cartItems as $item) { ?>
            <div class="cart-item">
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image">
                <div class="item-details">
                    <h3><?php echo $item['name']; ?></h3>
                    <p>Rp<?php echo $item['price']; ?></p>
                    <div class="quantity-control">
                        <?php
                        $subtotal = $item['price'] * $item['cart_quantity'];
                        $totalSubtotal += $subtotal;
                        if ($item['cart_quantity'] > 1) {
                            echo '<form action="" method="post">';
                            echo '<input type="hidden" name="cartId" value="' . $cartId . '">';
                            echo '<input type="hidden" name="productId" value="' . $item['id'] . '">';
                            echo '<input type="hidden" name="quantity" value="' . ($item['cart_quantity'] - 1) . '">';
                            echo '<button type="submit" name="updateQuantity">-</button>';
                            echo '</form>';
                        } else {
                            echo '<form action="" method="post">';
                            echo '<input type="hidden" name="cartId" value="' . $cartId . '">';
                            echo '<input type="hidden" name="productId" value="' . $item['id'] . '">';
                            echo '<input type="hidden" name="quantity" value="0">';
                            echo '<button type="submit" name="updateQuantity">-</button>';
                            echo '</form>';
                        }
                        ?>
                        <input type="text" value="<?php echo $item['cart_quantity']; ?>">
                        <?php
                        echo '<form action="" method="post">';
                        echo '<input type="hidden" name="cartId" value="' . $cartId . '">';
                        echo '<input type="hidden" name="productId" value="' . $item['id'] . '">';
                        echo '<input type="hidden" name="quantity" value="' . ($item['cart_quantity'] + 1) . '">';
                        echo '<button type="submit" name="updateQuantity">+</button>';
                        echo '</form>';
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="order-summary">
        <h3>Order Summary</h3>
        <p>Sub Total: <span>Rp<?php echo $totalSubtotal; ?></span></p>
        <p>Shipping estimate: <span>Rp25.000</span></p>
        <p>Tax estimate: <span>Rp12.000</span></p>
        <p><strong>ORDER TOTAL: <span>Rp<?php echo $totalSubtotal + 25000 + 12000; ?></strong></span></p>
        <a href="checkout.php" style="background-color: #4B3EC4; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">CHECKOUT</a>
    </div>
</div>
