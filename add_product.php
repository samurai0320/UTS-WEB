<?php
if (isset($_POST['submit'])) {
    include 'donnection.php'; 
    
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_FILES['image'];

    
    $target_dir = "uploads/"; 
    $target_file = $target_dir . basename($image["name"]); 
    $uploadOk = 1; 
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); 

    
    $check = getimagesize($image["tmp_name"]);
    if ($check !== false) {
        
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    
    if ($image["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
        $uploadOk = 0;
    }

    
    if ($uploadOk == 1) {
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            
            $sql = "INSERT INTO products (name,description, quantity, price, image,category) VALUES (?,?, ?, ?, ?, ?)";
            $add_product = $conn->prepare($sql);
            $add_product->bind_param("ssidss", $name, $description, $quantity, $price, $target_file,$category); 
            
            if ($add_product->execute()) {
                echo "The product has been added ";
                header("Location: halamankelolaproduk.php"); 
                exit();
            } else {
                
                echo "Error: " . $add_product->error; 
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

