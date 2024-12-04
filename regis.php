<?php
    if (isset($_POST["register"])) {
        include "donnection.php";  
        $username=$_POST['name'];
        $email=$_POST['email'];
        $password=$_POST['password'];
        $user_type = $_POST['user_type'];
        $full_name = $_POST['full_name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $phone_number = $_POST['phone_number'];

        $sql =  "select * from users where username= '$username'";
        $result=mysqli_query($conn,$sql);
        $count_users = mysqli_num_rows($result);

        $sql =  "select * from users where email= '$email'";
        $result=mysqli_query($conn,$sql);
        $count_email = mysqli_num_rows($result);

        if ($count_users == 0 && $count_email == 0) {
            $sql = "insert into users (username, email, password,user_type, full_name, address, city, phone_number) values ('$username', '$email', '$password', '$user_type', '$full_name', '$address', '$city', '$phone_number' )";
            $result= mysqli_query($conn, $sql);
            header("Location: log-in.php");
        }
        else{
            header("Location: regis.php?error=Username or email already taken");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="logo.gif" type="image/x-icon">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Page</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body, html {
      height: 100%;
      font-family: 'Zen Kaku Gothic Antique', sans-serif;
    }
    .Template1LogIn {
      width: 100%;
      height: 100vh;
      position: relative;
      overflow: hidden;
      background: linear-gradient(46deg, rgba(33, 33, 33, 0.84) 0%, rgba(66, 66, 66, 0.24) 100%), 
                  url('ppw log.jpg') center/cover no-repeat;
      background-size: cover;
    }
    .google-button {
      background: #2782D1; 
      border: none; 
      color: white; 
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      height: 48px;
      font-size: 13px;
      cursor: pointer;
      border-radius: 8px;
      font-weight: bold;
    }
    .SocialButton {
      background: #FAFAFA;
      border: 1px solid #EEEEEE;
      color: #616161;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      height: 48px;
      margin-top: 15px;
      font-size: 13px;
      cursor: pointer;
    }

    .DivSection {
      width: 460px;
      background: #FAFAFA;
      border-radius: 24px;
      padding: 40px;
      position: absolute;
      top: 50%;
      right: 10%;
      transform: translateY(-50%);
      box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
    }
    .Header h1, .Header p {
      color: black;
      font-family: 'Zen Kaku Gothic Antique';
      text-align: center;
    }
    .Header h1 {
      font-size: 25px;
      margin-bottom: 5px;
    }
    .Header p {
      font-size: 13px;
    }
    .InputGroup label {
      font-size: 13px;
      color: #424242;
    }
    .InputGroup input {
      width: 100%;
      height: 56px;
      border: 1px solid #424242;
      border-radius: 8px;
      padding: 0 16px;
      font-size: 16px;
      margin-top: 5px;
    }
    button {
      width: 100%;
      height: 56px;
      border: none;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
    }
    .MainButton {
      background: #212121;
      color: white;
      margin: 20px 0;
    }
    .Divider {
      display: flex;
      align-items: center;
      gap: 16px;
      margin: 20px 0;
    }
    .Divider hr {
      flex-grow: 1;
      border: 0;
      height: 1px;
      background-color: #E0E0E0;
    }
    .Divider span {
      font-size: 13px;
      color: #212121;
    }
    .google-button {
      background: #FAFAFA;
      border: 1px solid #616161;
      color: #616161;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      height: 48px;
      font-size: 13px;
      cursor: pointer;
      border-radius: 8px;
      font-weight: bold;
    }
    .google-button i {
      color: black; 
      font-size: 18px;
    }
    .AlreadyHaveAnAccountLogin, .SignupLink {
      text-align: center;
      font-size: 13px;
      margin-top: 20px;
    }
    .AlreadyHaveAnAccountLogin a, .SignupLink a {
      font-weight: 700;
      text-decoration: underline;
      color: #212121;
    }
  </style>
</head>
<body>

<div class="Template1LogIn">
  
  <div class="DivSection" style="width: 30%; right: 10%; position: absolute; top: 50%; transform: translateY(-50%);">
    <div class="Header">
      <h1>LET'S GET YOU STARTED</h1>
      <p>Create an Account</p>
    </div>

    
    <form method="POST" action="regis.php" style="display: flex; flex-direction: column; gap: 20px;">
      
      <div class="InputGroup">
        <label for="name">Your Name</label>
        <input type="text" name="name" id="name" placeholder="Deric imut" required />
      </div>
      <div class="InputGroup">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="deric_imut@gmail.com" required />
      </div>
      <div class="InputGroup">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="*****" required />
      </div>
      <div class="InputGroup">
        <select name="user_type">
          <option value="user">user</option>
          <option value="admin">admin</option>
        </select>
      </div>
  </div>

  
  <div class="DivSection" style="width: 30%; left: 10%; position: absolute; top: 50%; transform: translateY(-50%);">
    <div class="Header">
      <h1>LET'S GET YOU STARTED</h1>
      <p>Additional Information</p>
    </div>

      
      <div class="InputGroup">
        <label for="full_name">Full Name</label>
        <input type="text" name="full_name" id="full_name" placeholder="Deric Navino" required />
      </div>
      <div class="InputGroup">
        <label for="address">Address</label>
        <input type="text" name="address" id="address" placeholder="JL Paseban Timur gg 10 no 10" required />
      </div>
      <div class="InputGroup">
        <label for="city">City</label>
        <input type="text" name="city" id="city" placeholder="Jakarta" required />
      </div>
      <div class="InputGroup">
        <label for="phone_number">Phone Number</label>
        <input type="text" name="phone_number" id="phone_number" placeholder="08981852460" required />
      </div>


      <button type="submit" name="register" class="MainButton">Submit</button>
      <div class="Divider" style="display: flex; align-items: center; gap: 16px; margin: 20px 0;">
        <hr style="flex-grow: 1; border: 0; height: 1px; background-color: #E0E0E0;" />
        <span style="font-size: 13px; color: #212121;">Or</span>
        <hr style="flex-grow: 1; border: 0; height: 1px; background-color: #E0E0E0;" />
      </div>
      <button type="button" class="google-button" style="background: #FAFAFA; border: 1px solid #616161; color: #616161; display: flex; align-items: center; justify-content: center; gap: 8px; height: 48px; font-size: 13px; cursor: pointer; border-radius: 8px; font-weight: bold;">
        <i class="fab fa-google"></i> Google
      </button>

      
      <div class="AlreadyHaveAnAccountLogin" style="text-align: center; font-size: 13px; margin-top: 20px;">
        <span>Already have an account? <a href="log-in.php">LOGIN HERE</a></span>
      </div>

    </form>
  </div>
</div>
