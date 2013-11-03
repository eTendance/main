<?php

require_once('global.php');

if (isset($_POST('user')) && isset($_POST('pass')) ){
	$name = mysql_escape_string($_POST('user'));
	$pass = mysql_escape_string($_POST('pass'));

	$mysql = mysql_query($user = "SELECT * FROM users WHERE username = '$name' AND password = '$pass'");

	if(mysql_num_rows($mysql) < 1){
		echo 0;
		exit;

	}else{

		if($_POST('activity') == "login"){

			echo 1;
			exit;
		}

	}

} else {
	echo 0;
	exit
}

$userID = mysql_escape_string($user['id']);

if($_POST('activity') == "view_classes"){
	$class_list = mysql_query('SELECT * FROM enrollment WHERE userid = '$userID'');

	echo json_encode($class_list);
	exit

}

/*
	login - on success echo 1, else 0
	view_classes - list of classes student is enrolled in, json encode


*/

	
?>