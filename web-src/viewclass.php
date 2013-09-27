<?php
require_once('global.php');

check_auth('p');

if (!isset($_GET['id'])) {
    header('Location: professordashboard.php');
    exit;
}


//check to make sure class exists and this professor owns it
$result = mysql_query('SELECT * FROM classes join classowners on classes.id=classowners.classid where professorid="' . mysql_real_escape_string($_SESSION['userdata']['id']) . '" and classes.id="' . mysql_real_escape_string($_GET['id']) . '"');
if (mysql_num_rows($result) < 1) {
    header('Location: professordashboard.php');
    exit;
}
$classdata = mysql_fetch_assoc($result);


//get all students currently in class
$result = mysql_query('SELECT * FROM users 
join enrollment on users.id=enrollment.userid
join classes on classes.id=enrollment.classid
where classes.id="' . mysql_real_escape_string($_GET['id']) . '"');
$classusers=array();
while ($row = mysql_fetch_assoc($result)) {
    $classusers[] = $row;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        Viewing class id <?php echo $_GET['id'] ?><br /><br />
        <?php
        foreach($classusers as $user){
            print_r($user);
        }
        ?>

        Enrollment code for this course is <b><?php echo $classdata['enrollmentcode'] ?></b>
    </body>
</html>
