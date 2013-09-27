<?php
require_once('global.php');

if(isset($_GET['logout'])){
    session_destroy();
}

if(isset($_SESSION['et_logged_in']) && $_SESSION['et_logged_in']==true){
    header('Location: professordashboard.php');
}

if(isset($_POST['username'])){
    
  $result = mysql_query('SELECT * FROM users WHERE username="'.mysql_real_escape_string($_POST['username']).'" AND password="'.mysql_real_escape_string($_POST['password']).'" AND usertype="'.  mysql_real_escape_string($_POST['type']).'"') or die(mysql_error());
  
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
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
Login to the system
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            Username: <input type="text" name="username"/>
            Password: <input type="text" name="password"/>
            <select name="type">
                <option value="s">Student</option>
                <option value="p">Professor</option>
            </select>
            <input type="submit" value="Login" />
        </form>
    </body>
</html>
