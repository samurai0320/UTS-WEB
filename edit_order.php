<?php
include 'donnection.php'; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: log-in.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = $conn->real_escape_string($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);

    $validStatuses = [ 'Processing', "Paid"];
    if (!in_array($status, $validStatuses)) {
        echo "Invalid status.";
        exit;
    }

    $sql = "UPDATE orders SET status = '$status' WHERE order_id = '$orderId'";
    if ($conn->query($sql) === TRUE) {
        header("location: order_managementAdmin.php");
    } else {
        echo "Error updating order status: " . $conn->error;
    }
}
?>