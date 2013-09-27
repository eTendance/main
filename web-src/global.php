<?php
/*
 * 
 * This file handles system sessions along with the global connection to the database.
 */
ini_set('display_errors', 'On');
require_once('settings.php');
mysql_connect($settings['mysql_host'],$settings['mysql_username'],$settings['mysql_password']);
mysql_select_db($settings['mysql_database']);

session_start();




//globally defined functions
function check_auth($reqtype){
    
    if(isset($_SESSION['et_logged_in'])){
        if($_SESSION['userdata']['usertype']!=$reqtype){
            if($_SESSION['userdata']['usertype']=='p'){ header("Location: professordashboard.php"); exit; }
            if($_SESSION['userdata']['usertype']=='s'){ header("Location: studentdashboard.php"); exit; }
        }
    } else {
        header("Location: login.php");
    }
   
}

?>
