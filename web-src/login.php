<?php
require_once('global.php');

if(isset($_GET['logout'])){
    $_SESSION=array();
    session_destroy();
    header('Location: index.php');
}

if(isset($_SESSION['et_logged_in']) && $_SESSION['et_logged_in']==true){
    header('Location: professordashboard.php');
}

if(isset($_POST['username'])){
    
  $result = mysql_query('SELECT * FROM users WHERE username="'.mysql_real_escape_string($_POST['username']).'" AND password="'.mysql_real_escape_string($_POST['password']).'"') or die(mysql_error());
  
  if(mysql_num_rows($result)>0){
      echo "Login success";
      
      $_SESSION['et_logged_in'] = true;
      $_SESSION['userdata'] = mysql_fetch_array($result);
      
      header("Location: professordashboard.php");
      
  } else {
      
      $_SESSION['et_logged_in'] = false;
      
      echo "Login failure";
  }
    
}

?>

