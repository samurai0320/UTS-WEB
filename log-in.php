<?php
$random = rand(9999, 1000);
    if (isset($_POST["submit"])) {
        include "donnection.php";
        session_start();
        $email =  $_POST['email'];
        $password=$_POST['password'];
        $captcha = $_POST['captcha'];
        $captcharandom = $_POST['captcha-random'];
        $sql="SELECT * FROM users WHERE email='$email' and password='$password'";
        $result=$conn->query($sql);

        if($captcha != $captcharandom){
          header("Location: log-in.php?error=Captcha does not match");
        }
        else{
          if ($result->num_rows > 0) {
          $row = $result->fetch_assoc();
          $_SESSION['user_id'] = $row['user_id'];
          if($row['user_type']=="admin"){
              header("location: halamankelolaproduk.php");
          }
          if ($row['user_type']=="user") {
              header("location: homepage.php"); 
          }
      }
        else{
            header("Location: log-in.php?error=Username or email does not match");
        }
        }
        
        // $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

        // if($row){
        //     if(password_verify($password, $row["password"])){
        //         header("location: halamankelolaproduk.php"); 
        //     }
        // }
        // else{
        //     header("Location: log-in.php?error=Username or email does not match");
        // }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
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
                  url('ppw log.jpg') center/cover no-repeat; /* Ganti dengan URL gambar yang valid */
      background-size: cover; /* Memastikan background memenuhi layar */
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
    .SocialButton img {
      width: 20px;
      height: 20px;
    }
    .SignupLink {
      text-align: center;
      font-size: 13px;
      margin-top: 20px;
    }
    .SignupLink a {
      font-weight: 700;
      text-decoration: underline;
      color: #212121;
    }
    .Captcha {
      width: 50%;
      background-color: yellow;
      text-align: center;
      font-size: 24px;
      font-weight: 700;
    }
  </style>
  <link rel="icon" href="logo.gif" type="image/x-icon">
</head>
<body>
<?php
require_once 'vendor/autoload.php';



$clientID = '313345001089-fg1dt61d1h5rn1dlnq3d001oevj9943j.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/UTS/log-in.php';


$client = new Google_Client();
$client->setPrompt("select_account");
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");


if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token['access_token']);

  
  $google_oauth = new Google_Service_Oauth2($client);
  $google_account_info = $google_oauth->userinfo_v2_me->get();
  $email =  $google_account_info->email;
  $username =  $google_account_info->name;
?>
<?php
        include "donnection.php";  
        $sql =  "SELECT * from users where username= '$username'";
        $result=mysqli_query($conn,$sql);
        $count_users = mysqli_num_rows($result);

        $sql =  "SELECT * from users where email= '$email'";
        $result=mysqli_query($conn,$sql);
        $count_email = mysqli_num_rows($result);

        if ($count_users == 0 && $count_email == 0) {
            $sql = "INSERT INTO users(username, email) values ('$username', '$email')";
            $result= mysqli_query($conn, $sql);
            header("location: homepage.php");
        }
        else{
            header("location: homepage.php");
        }
    
?>
<?php } else { ?>

<div class="Template1LogIn">
  <div class="DivSection">
    
    <div class="Header">
      <h1>Log In to your Account</h1>
      <p>WELCOME BACK</p>
    </div>

   
    <form method="POST" class="Form" style="display: flex; flex-direction: column; gap: 20px;">
      <div class="InputGroup">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Masukkan Email" required />
      </div>

      <div class="InputGroup">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Masukkan Password" required />
      </div>

      <div class="InputGroup">
        <label for="captcha">Captcha</label>
        <input type="text" name="captcha" id="captcha" placeholder="Captcha" required />
        <input type="hidden" name="captcha-random" id="captcha" value="<?php echo $random; ?>" required />
        <div class="Captcha"><?php echo $random; ?></div>
      </div>

      <div style="display: flex; justify-content: space-between;">
        <label style="display: flex; align-items: center;">
          <input type="checkbox" style="margin-right: 8px;" /> Remember me
        </label>
        <a style="text-decoration: none; color: #424242;">Forgot Password?</a>
      </div>

      <button type="submit" name="submit" class="MainButton">CONTINUE</button>
    </form>

    
    <div class="Divider">
      <hr />
      <span>Or</span>
      <hr />
    </div>

    
     
     <a href="<?php echo $client->createAuthUrl() ?>">
    <button type="button" class="google-button">
      <i class="fab fa-google"></i> Google
    </button> 
  </a>

    
    <div class="SignupLink">
      New User? <a href="regis.php">SIGN UP HERE</a>
    </div>
  </div>
</div>
<?php } ?>
</body>
</html>
