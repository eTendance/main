<?php

require_once('global.php');

$userID = "1";

if (isset($_REQUEST['user']) && isset($_REQUEST['pass']) ){
        $name = mysql_escape_string($_REQUEST['user']);
        $pass = mysql_escape_string($_REQUEST['pass']);
        $mysql = mysql_query("SELECT * FROM users WHERE username = '$name' AND password = '$pass'");

        if(mysql_num_rows($mysql) < 1){
                echo "0";
                exit;

        }else{

              	if($_REQUEST['activity'] == "login"){

                        echo "1";
                        exit;
                }
        $user = mysql_fetch_array($mysql);
        $userID = $user['id'];

        }

} else {
        echo "0";
        exit;
}


if($_REQUEST['activity'] == "view_classes"){
        $class_list = mysql_query("SELECT * FROM enrollment JOIN classes ON(enrollment.classid = classes.id)  WHERE enrollment.userid = '$userID'");

        $data_array = array();
        $counter = 0;

        while ($next = mysql_fetch_assoc($class_list)) {
              $data_array[$counter] = $next;
              $counter++;
        }



        echo json_encode($data_array);
        exit;

}
/*
  	login - on success echo 1, else 0
        view_classes - list of classes student is enrolled in, json encode


*/


?>

