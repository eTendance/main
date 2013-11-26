<?php

require_once('global.php');


$query = 'SELECT firstname, lastname from enrollment left JOIN  (SELECT userid,checkins.id from checkins left join checkincodes on checkins.checkincodeid = checkincodes.id where forclassday="' . mysql_real_escape_string($_REQUEST['date']) . ' " )AS inattendance on inattendance.userid = enrollment.userid JOIN users ON enrollment.userid = users.id WHERE inattendance.id IS NULL and classid = "' . mysql_real_escape_string($_REQUEST['id']) . '";';
$absent = mysql_query($query);

while ($row = mysql_fetch_assoc($absent)) {
    $absentpeople[] = $row;
}

echo json_encode($absentpeople);
?>

