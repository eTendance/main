<?php
require_once('global.php');

check_auth('p');

if (!isset($_GET['id'])) {
    header('Location: professordashboard.php');
    exit;
}


//check to make sure class exists and this professor owns it
$result = mysql_query('SELECT classes.* FROM classes join classowners on classes.id=classowners.classid where professorid="' . mysql_real_escape_string($_SESSION['userdata']['id']) . '" and classes.id="' . mysql_real_escape_string($_GET['id']) . '"');
if (mysql_num_rows($result) < 1) {
    showdashboard();
}
$classdata = mysql_fetch_assoc($result);


if(!empty($_GET['action'])){
    
    //this section will take care of any operations that occur on this page
    
    if($_GET['action']=='openenrollment'){
        mysql_query('UPDATE classes SET enrollmentopen="true" WHERE id="'.$classdata['id'].'"');
    } elseif($_GET['action']=="closeenrollment"){
        mysql_query('UPDATE classes SET enrollmentopen="false" WHERE id="'.$classdata['id'].'"');
    } elseif($_GET['action']=='newenrollmentcode'){
        mysql_query('UPDATE classes SET enrollmentcode="'.strtoupper(generateRandomString(10)).'" WHERE id="'.$classdata['id'].'"');
    }
    header('Location: '.$_SERVER['PHP_SELF'].'?id='.$_GET['id']);
    
}


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
        <h1><?php echo $classdata['name'] ?></h1><br />
        
        <h2>Students</h2>
        <?php
        foreach($classusers as $user){
            echo $user['firstname'] . ' ' . $user['lastname'];
            echo '<br />';
        }
        ?>

        <h2>Enrollment Code</h2>
        Enrollment code for this course is <b><?php echo $classdata['enrollmentcode'] ?></b> [<?php
        if($classdata['enrollmentopen']=='true'){
           echo '<a href="'.$_SERVER['PHP_SELF'].'?action=closeenrollment&id='.$_GET['id'].'">Open</a>';
        } else {
            echo '<a href="'.$_SERVER['PHP_SELF'].'?action=openenrollment&id='.$_GET['id'].'">Closed</a>';
        }
        ?>] <br />
        <form action="" method="get"><input type="hidden" name="id" value="<?php echo $classdata['id'] ?>" /><input type="hidden" name="action" value="newenrollmentcode" /><input type="submit" value="Generate New Enrollment Code" /></form>
    </body>
</html>
