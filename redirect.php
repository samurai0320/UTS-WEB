<?php
require_once 'vendor/autoload.php';

// init configuration
$clientID = '313345001089-fg1dt61d1h5rn1dlnq3d001oevj9943j.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/UTS/log-in.php';

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token['access_token']);

  // get profile info
  $google_oauth = new Google_Service_Oauth2($client);
  $google_account_info = $google_oauth->userinfo->get();
  $email =  $google_account_info->email;
  $name =  $google_account_info->name;

  // now you can use this profile info to create account in your website and make user logged in.
} else {
  echo "<a href='".$client->createAuthUrl()."'>Google Login</a>";
}
?>