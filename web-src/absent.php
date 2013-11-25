<?php

require_once('global.php');

?>

<!DOCTYPE html>
<html>
<head>

     <link rel="stylesheet" type="text/css" href="css/eTendenceProfessor.css" />
    <title> Students absent</title>
</head>
<body>

    <div id="absent">

        <ul>
                <p id="bottom" >
                    Students Absent
                </p>
            
        
<?php 


	
	if($_POST['action'] == "getAbsent") {
		
		$array = $_POST['result'];
		for($x = 0; $x < count($array); $x=$x+1) {
			echo $array[$x];
		}
	}
	else if($_POST['action']=="getabsent" ){
    	$query =  'SELECT firstname, lastname from enrollment left JOIN  (SELECT userid,checkins.id from checkins left join checkincodes on checkins.checkincodeid = checkincodes.id where forclassday="' . mysql_real_escape_string($_REQUEST['date'] ) . ' " )AS inattendance on inattendance.userid = enrollment.userid JOIN users ON enrollment.userid = users.id WHERE inattendance.id IS NULL and classid = "' . mysql_real_escape_string($_REQUEST['id']) . '";' ;
    	$absent = mysql_query($query);
    	
    	while($row = mysql_fetch_assoc($absent)){
    		echo '<li>' . $row['firstname'] .' ' . $row['lastname'] . '</li>';
    	}
    

    }else{
        echo "No students were absent";
    }

    

?>


    
    </ul>

</body>

</html>


