<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Manage Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Section -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <p class="card-text">Rp. 10,680,000</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <p class="card-text">4</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Customers</h5>
                        <p class="card-text">9</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <p class="card-text">13</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders and Top Selling -->
        <div class="row mt-4">
            <div class="col-md-6">
                <h5>Recent Orders</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>#24</td><td>John Doe</td><td>Rp. 3,819,000</td><td>Processed</td></tr>
                        <tr><td>#23</td><td>Ryan Tsany</td><td>Rp. 7,068,000</td><td>Processed</td></tr>
                        <tr><td>#22</td><td>Ryan Tsany</td><td>Rp. 3,612,000</td><td>Completed</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Top Selling Products</h5>
                <ul class="list-group">
                    <li class="list-group-item">NOVA V4 Mouse - Rp. 339,000 (6 sold)</li>
                    <li class="list-group-item">PNY GEFORCE RTX 4060 - Rp. 4,855,000 (4 sold)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Manage Products Section -->
    <div class="container mt-4">
        <h3>Manage Products</h3>
        <form class="row g-3">
            <div class="col-md-2">
                <input type="file" class="form-control" />
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" placeholder="Product Name" />
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" placeholder="Description" />
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" placeholder="Quantity" />
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" placeholder="Price" />
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
        <table class="table mt-3">
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
                <!-- Sample Row -->
                <tr>
                    <td><img src="image_url" alt="Product" width="50"></td>
                    <td>1</td>
                    <td>Product Name</td>
                    <td>Short Description</td>
                    <td>100</td>
                    <td>Rp. 50,000</td>
                    <td>Category</td>
                    <td><button class="btn btn-warning">Edit</button> <button class="btn btn-danger">Delete</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
