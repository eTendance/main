<?php
    require_once 'google-api-php-client/src/Google_Client.php';
    require_once 'google-api-php-client/src/contrib/Google_PlusService.php';
    require_once 'google-api-php-client/src/contrib/Google_Oauth2Service.php';
    session_start();
?>

<?php
    $client = new Google_Client();
    
    $client->setApplicationName("eTendance Sign In with GooglePlus");
    $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.me')); 
    
    $client->setClientId('1034546594741.apps.googleusercontent.com');
    $client->setClientSecret('8mzqJxJCKezSa4SlT1cMzOpY'); 
    $client->setRedirectUri('http://etendance.kleq.net/oauth2callback');
    $client->setDeveloperKey('AIzaSyBZMc5F_dqjIr-K9xYXD_Cad6o14AxMOFY');
    
    $plus       = new Google_PlusService($client);
    $oauth2     = new Google_Oauth2Service($client);
    
if(isset($_GET['code'])) {
    $client->authenticate(); // Authenticate
    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
    }
 
    if(isset($_SESSION['access_token'])) {
        $client->setAccessToken($_SESSION['access_token']);
    }
     
    if ($client->getAccessToken()) {
      $user         = $oauth2->userinfo->get();
      $me           = $plus->people->get('me');
      $optParams    = array('maxResults' => 100);
      $activities   = $plus->activities->listActivities('me', 'public',$optParams);
      
      $_SESSION['access_token']         = $client->getAccessToken();
      $email                            = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
    } else {
        $authUrl = $client->createAuthUrl();
    }
     
    if(isset($me)){ 
        $_SESSION['gplusuer'] = $me; // start the session
        $_SESSION['et_logged_in'] = true;
        
        $result = mysql_query('SELECT * FROM users WHERE username="'.mysql_real_escape_string($me['id']).'"') or die (mysql_error());
        
        if(mysql_num_rows($result) > 0) { //returning google user
            $_SESSION['userdata'] = mysql_fetch_array($result);
        } else { //first time google user
            $first_name = $me['displayName'];
            $last_name = "";
            $user_email = $user['email'];
            $username = $me['id'];
            $password = "googleLogin".$first_name;
            $type = "s"
            
            mysql_query('INSERT INTO users (firstname,lastname,email,username,password,usertype) values("' . mysql_real_escape_string($first_name) . '","' . mysql_real_escape_string($last_name) . '","'
                        . mysql_real_escape_string($user_email) . '","' . mysql_real_escape_string($username) . '","' . mysql_real_escape_string($password) . '","' . mysql_real_escape_string($type) . '")') or die(mysql_error());
                        
           $result = mysql_query('SELECT * FROM users WHERE username="'.mysql_real_escape_string($me['id']).'"') or die (mysql_error());
           $_SESSION['userdata'] = mysql_fetch_array($result);
        }
        
        header('Location: studentdashboard.php');
    }
    
?>




