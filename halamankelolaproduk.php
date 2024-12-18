<?php
include 'donnection.php'; 


$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="logo.gif" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0e0e0;
            margin: 0;
        }
        .navbar {
            background-color: #87CEEB; 
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .navbar a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
        }
        .container {
            width: 100%;
            margin: 0;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .admin-table th,
        .admin-table td {
            border: 1px solid #000000; 
            padding: 10px;
        }
        .admin-table th {
            background-color: #87CEEB; 
            color: white;
            text-align: left;
        }
        .admin-table td input {
            width: 95%;
            border: 1px solid #ddd;
            padding: 6px;
            border-radius: 4px;
            font-size: 14px;
        }
        .admin-table img {
            width: 50px;
            height: auto;
            border-radius: 5px;
        }
        .action-buttons {
            display: flex;
            justify-content: space-around;
        }
        .action-buttons .edit-btn,
        .action-buttons .delete-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0 5px;
            color: #2782D1;
            font-weight: bold;
        }
        .action-buttons .delete-btn {
            color: #f44336;
        }
        .add-item-btn {
            background-color: #2782D1;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
            margin: 20px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <span>Admin Panel</span>
    <a href="order_managementAdmin.php">Order</a>
    <a href="log-in.php" style="color: white;">Logout</a>
</div>

<div class="container">
    
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="file" name="image" accept="image/*" required>
                    </td>
                    <td>
                        <input type="text" name="name" placeholder="Product Name" required>
                    </td>
                    <td>
                        <input type="text" name="description" placeholder="description" required>
                    </td>
                    <td>
                        <input type="number" name="quantity" placeholder="Quantity" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="price" placeholder="Price" required>
                    </td>
                    <td>
                        <input type="text" name="category" placeholder="category" required>
                    </td>
                    <td>
                        <button type="submit" name="submit" class="add-item-btn">Add Product</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    
    <h2 style="margin-top: 20px;">Existing Products</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Name</th>
                <th>description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>category</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img src='" . htmlspecialchars($row['image']) . "' alt='Product Image'></td>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                    echo "<td class='action-buttons'>
                            <form action='edit_product.php' method='GET' style='display:inline;'>
                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                <button class='edit-btn' type='submit'>Edit</button>
                            </form>
                            <form action='delete_product.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                <button class='delete-btn' type='submit'>Delete</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>no proudct found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close(); 
?>
