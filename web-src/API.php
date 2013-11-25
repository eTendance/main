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
if($_REQUEST['activity'] == 'get_absences_for_student'){

    $classID = mysql_escape_string($_REQUEST['classid']);


    $dates_absent = mysql_query("SELECT forclassday from (SELECT * from checkins where checkins.userid = '$userID')  AS attendance RIGHT JOIN checkincodes ON( attendance.checkincodeid = checkincodes.id) WHERE checkincodes.classid='$classID' AND checkincodeid IS NULL ");

    $data_array = array();
    $counter = 0;

    while( $next = mysql_fetch_assoc($dates_absent)){
         $data_array[$counter] =  $next;
         $counter++;
      }

    echo json_encode($data_array);
    exit;
    
}


if($_REQUEST['activity']== "get_checkin_PIN"){


    $datePull = getdate();

  

    $date = $datePull['year'] . '-' . $datePull['mon'] . '-' . $datePull['mday'];

    

    $classID = mysql_escape_string($_REQUEST['classid']);

    $query = "SELECT code FROM  checkincodes WHERE checkinopen='true' AND classid = '$classID' AND  forclassday= '$date' ";

    $data_array = array();

    $codes = mysql_query($query);

    $data_array[0] = mysql_fetch_assoc($codes);

    echo json_encode(  $data_array ) ;
    exit;

}

if($_REQUEST['activity'] ==  "checkin_with_PIN"){

    $code = $_REQUEST['checkincode'];

    $query1 = " SELECT * from checkincodes WHERE code='$code'" ;

    $result = mysql_query($query1);

    $holder =  mysql_fetch_assoc($result);

    if($holder['checkinopen'] == 'true'){
        
        $classID = $holder['classid'];
        $codeId = $holder['id'];


        $checkError = mysql_query("INSERT INTO checkins(userid, classid, checkincodeid) VALUE('$userID', '$classID', '$codeId' ) " )  ;

        echo $checkError;

        if($checkError == NULL){
        echo "already_checked_in"; # code for already exists
        }

        if($checkError == 1){
            echo "checkin_success"; #self explanatory
        }


    } else {
    
        echo "checkin_closed";
        
       


    }



}




/*
    login - on success echo 1, else 0
        view_classes - list of classes student is enrolled in, json encode


*/

if($_REQUEST['action'] == "getAbsent") {
	$classID = $_REQUEST['classID'];
	$date = $_REQUEST['date'];
	$url = $_REQUEST['urk'];
	$query = 'SELECT firstname, lastname from enrollment left JOIN  (SELECT userid,checkins.id from checkins left join checkincodes on checkins.checkincodeid = checkincodes.id where forclassday="' . mysql_real_escape_string($date) . ' " )AS inattendance on inattendance.userid = enrollment.userid JOIN users ON enrollment.userid = users.id WHERE inattendance.id IS NULL and classid = "' . mysql_real_escape_string($classID) . '";' ;
	$absent = mysql_query($query);
	$data_array = array();
	$counter = 0;
	while($row = mysql_fetch_assoc($absent)){
		$data_array[$counter]= '<li>' . $row['firstname'] .' ' . $row['lastname'] . '</li>';
		$counter++;
	}

	echo json_encode($data_array);
	exit;
	
}


?>

