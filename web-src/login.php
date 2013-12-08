<?php
require_once('global.php');

if(isset($_GET['logout'])){
    if(isset($_SESSION['gplusuer'])) {
      unset($_SESSION['access_token']);
      unset($_SESSION['gplusuer']);
      session_destroy();
      #header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
      header('Location: https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
    } else {
        $_SESSION=array();
        session_destroy();
        header('Location: index.php');
    }
}

if(isset($_SESSION['et_logged_in']) && $_SESSION['et_logged_in']==true){
    //echo "1";
}

if(isset($_POST['username'])){
    
  $result = mysql_query('SELECT * FROM users WHERE username="'.mysql_real_escape_string($_POST['username']).'" AND password="'.mysql_real_escape_string($_POST['password']).'"') or die(mysql_error());
  
  if(mysql_num_rows($result)>0){
      echo "1";
      
      $_SESSION['et_logged_in'] = true;
      $_SESSION['userdata'] = mysql_fetch_array($result);
      
  } else {
      
      $_SESSION['et_logged_in'] = false;
      
      echo "0";
  }
    
}
?>
