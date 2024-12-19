<?php
include 'donnection.php'; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: log-in.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $orderId = $conn->real_escape_string($_POST['order_id']);

    $sqlDeleteItems = "DELETE FROM order_items WHERE order_id = '$orderId'";
    if ($conn->query($sqlDeleteItems) === TRUE) {
        $sqlDeleteOrder = "DELETE FROM orders WHERE order_id = '$orderId'";
        if ($conn->query($sqlDeleteOrder) === TRUE) {
            header("location: order_managementAdmin.php");
        } else {
            echo "Error deleting order: " . $conn->error;
        }
    } else {
        echo "Error deleting order items: " . $conn->error;
    }
}
?>
