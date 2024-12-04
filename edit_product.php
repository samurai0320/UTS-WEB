<?php
include 'donnection.php'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['id'])) {
        $product_id = $_GET['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        
        
        $update_product = $conn->prepare("UPDATE products SET name = ?, description = ?, quantity = ?, price = ?, category = ? WHERE id = ?");
        $update_product->bind_param("ssdisi", $name,$description, $quantity, $price, $category, $product_id);

        if ($update_product->execute()) {
            echo "product updated";
        } else {
            echo "error" . $conn->error;
        }

        $update_product->close();
        header("Location: halamankelolaproduk.php"); 
        exit;
    }
}


if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    
    $fetch_query = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $fetch_query->bind_param("i", $product_id);
    $fetch_query->execute();
    $product_data = $fetch_query->get_result();

    if ($product_data->num_rows > 0) {
        $product = $product_data->fetch_assoc();
    } else {
        echo "Product not found";
        exit;
    }

    $fetch_query->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="logo.gif" type="image/x-icon">
    <title>Edit Product</title>
</head>
<body>

<h2>Edit Product</h2>
<form method="POST" action="">
    <label>Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br><br>
    <label>description:</label>
    <input type="text" name="description" value="<?php echo htmlspecialchars($product['description']); ?>" required><br><br>
    <label>Quantity:</label>
    <input type="number" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required><br><br>
    <label>Price:</label>
    <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required><br><br>
    <label>category:</label>
    <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required><br><br>
    <button type="submit">Update Product</button>
</form>

</body>
</html>

<?php $conn->close(); ?>
