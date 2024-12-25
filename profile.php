<?php 
include 'donnection.php'; 
session_start();
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = '$userId'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$username = $data['username'];
$email = $data['email'];
$full_name = $data['full_name'];
$address = $data['address'];
$city = $data['city'];
$phone_number = $data['phone_number'];
function updateAccountCredentials($username, $email, $password, $userId,$full_name, $address, $city, $phone_number) {
    include 'donnection.php';

  $sql = "UPDATE users SET username = '$username', full_name = '$full_name', address = '$address', city = '$city', phone_number = '$phone_number', email = '$email', password = '$password' WHERE user_id = '$userId'";
  $conn->query($sql);

  }
  if (isset($_POST["submit-btn"])) {
    include 'donnection.php';
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $userId = $_SESSION['user_id'];
    updateAccountCredentials($username, $email,$full_name, $address, $city, $phone_number, $password, $userId);
    $pass = "UPDATE users SET username = '$username', 
                full_name = '$full_name', 
                email = '$email', 
                address = '$address', 
                city = '$city', 
                phone_number = '$phone_number', 
                password = '$password' 
            WHERE user_id = '$userId'";
    $result= mysqli_query($conn, $pass);
    $_SESSION['username'] = $username;
  }

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
    $sql = "SELECT * FROM products WHERE name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%'";
    $result = $conn->query($sql);
} 
if (isset($_POST['view-all'])) {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script>
        function redirectToCategory(selectElement) {
            const selectedValue = selectElement.value;
            if (selectedValue) {
                window.location.href = selectedValue; 
            }
        }
    </script>

    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
    <header>
        <div class="logo">
            <img src="logo.jpg" alt="QuillBox Logo">
        </div>
        <nav class="main-nav">
            <a href="homepage.php">Home</a>
            <a href="cart.php">cart</a>
        </nav>
        <div class="user-profile">
            <span>Welcome, <?php echo $_SESSION['username']; ?></span>
            <div class="user-icon">👤</div>
        </div>
    </header>

    <div class="search-bar">
        
        
    </div>

    <main class="profile-page">
        <nav class="breadcrumb">
            <a href="homepage.php">Home</a> / <span>profile</span>
        </nav>

        <section class="profile-container">
            <div class="sidebar">
                <img src="ayaka.jpg" alt="User Profile Picture" class="profile-pic">
                <h2><?php echo $username; ?></h2>
                <p><?php echo $email; ?></p>
                <a class="menu-item" href="profile.php">Account info</a>
                <a class="menu-item" href="wishlist.php">wishlist</a>
                <a class="menu-item" href="orders.php">orders</a>
                <a class="menu-item" href="log-in.php">Logout</a>
            </div>

            <div class="account-info">
                <h2>Account Info</h2>
                <form action="#" method="POST">
                    <label for="username">Username <span class="required">*</span></label>
                    <input type="text" id="username" name="username" value="<?php echo $username; ?>" placeholder="Deric"required>

                    <label for="full_name">Full-Name <span class="required">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo $full_name; ?>" placeholder="Deric Imut" required>

                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" placeholder="Dericimutbanget@gmail.com" required>

                    <label for="city">City <span class="required">*</span></label>
                    <input type="text" id="city" name="city" value="<?php echo $city; ?>" placeholder="Jayakarta" required>

                    <label for="address">Address <span class="required">*</span></label>
                    <input type="text" id="address" name="address" value="<?php echo $address; ?>" placeholder="Jl jalan ke kota malang" required>

                    <label for="phone_number">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="phone_number" name="phone_number" value="<?php echo $phone_number; ?>" placeholder="08122314567" required>

                    <label for="password">Change Password (input old password if don't want to change)</label>
                    <input type="password" id="password" name="password" placeholder="********" required>

                    <button type="submit" name="submit-btn" class="submit-btn">Submit</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
