<?php
include 'donnection.php';


if (!isset($_POST['id'])) {
    header("Location: halamankelolaproduk.php");
    exit;
}


function deleteProduct($conn, $product_id) {
    $delete_query = $conn->prepare("DELETE FROM products WHERE id = ?");
    $delete_query->bind_param("i", $product_id);

    
    if ($delete_query->execute()) {
        echo "Product deleted";
    } else {
        echo "error " . $conn->error;
    }

    $delete_query->close();
}


$product_id = $_POST['id'];


deleteProduct($conn, $product_id);


header("Location: halamankelolaproduk.php");
exit;


$conn->close();
?>

