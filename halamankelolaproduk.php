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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="styles.css" rel="stylesheet"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #E1E3EB;
        }
        .header {
    background-color: #fff;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e0e0e0;
}

.header .logo {
    display: flex;
    align-items: center;
}

.header .logo img {
    height: 40px;
    margin-right: 10px;
}

.header .nav {
    display: flex;
    align-items: center;
}

.header .nav a {
    margin: 0 15px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
}

.header .user {
    display: flex;
    align-items: center;
}

.header .user span {
    margin-right: 10px;
    font-weight: 500;
}

.header .user i {
    font-size: 24px;
}
        .search-bar {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: #4B3EC4;
            height: 40px;
        }
        .content {
            padding: 20px;
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
            background-color: #4B3EC4;
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

<div class="header">
    <div class="logo">
        <img src="logo.jpg" alt="Logo" height="40" width="40"/>
        <span>QuillBox</span>
    </div>
    <div class="nav">
        <a href="dashboard-admin.php" class="nav-link">Home</a>
        <a href="halamankelolaproduk.php" class="nav-link">Products</a>
        <a href="order_managementAdmin.php" class="nav-link">Orders</a>
    </div>
    <div class="user">
        <span>Welcome admin</span>
        <i class="fas fa-user-circle"></i>
    </div>
  </div>

<div class="search-bar"></div>

<div class="content">
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Category</th>
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
                        <input type="text" name="description" placeholder="Description" required>
                    </td>
                    <td>
                        <input type="number" name="quantity" placeholder="Quantity" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="price" placeholder="Price" required>
                    </td>
                    <td>
                        <input type="text" name="category" placeholder="Category" required>
                    </td>
                    <td>
                        <button type="submit" name="submit" class="add-item-btn">Add Product</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <h2 class="fw-bold" style="margin-top: 20px;">Existing Products</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Category</th>
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
                echo "<tr><td colspan='6'>No products found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
