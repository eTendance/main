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


if (!empty($_REQUEST['action'])) {

    //this section will take care of any operations that occur on this page

    if ($_GET['action'] == 'openenrollment') {
        mysql_query('UPDATE classes SET enrollmentopen="true" WHERE id="' . $classdata['id'] . '"');
    } elseif ($_GET['action'] == "closeenrollment") {
        mysql_query('UPDATE classes SET enrollmentopen="false" WHERE id="' . $classdata['id'] . '"');
    } elseif ($_GET['action'] == 'newenrollmentcode') {
        mysql_query('UPDATE classes SET enrollmentcode="' . strtoupper(generateRandomString(10)) . '" WHERE id="' . $classdata['id'] . '"');
    }

    if ($_REQUEST['action'] == 'generatecheckin') {
        $checkincode = strtoupper(generateRandomString(10));
        $query = 'INSERT INTO checkincodes (`code`,`classid`,`forclassday`,`checkinopen`) values("' . $checkincode . '","' . $classdata['id'] . '","' . mysql_real_escape_string($_POST['codeday']) . '","' . mysql_real_escape_string($_POST['open']) . '")';
        mysql_query($query) or die(mysql_error());
    }


    header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id']);
}


//get all students currently in class
$result = mysql_query('SELECT * FROM users 
join enrollment on users.id=enrollment.userid
join classes on classes.id=enrollment.classid
where classes.id="' . mysql_real_escape_string($_GET['id']) . '"');
$classusers = array();
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
        foreach ($classusers as $user) {
            echo $user['firstname'] . ' ' . $user['lastname'];
            echo '<br />';
        }
        ?>

        <h2>Enrollment Code</h2>
        Enrollment code for this course is <b><?php echo $classdata['enrollmentcode'] ?></b> [<?php
        if ($classdata['enrollmentopen'] == 'true') {
            echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=closeenrollment&id=' . $_GET['id'] . '">Open</a>';
        } else {
            echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=openenrollment&id=' . $_GET['id'] . '">Closed</a>';
        }
        ?>] <br />
        <form action="" method="get"><input type="hidden" name="id" value="<?php echo $classdata['id'] ?>" /><input type="hidden" name="action" value="newenrollmentcode" /><input type="submit" value="Generate New Enrollment Code" /></form>


        <h2>Checkin Codes</h2>
        Generate checkin codes for class days below.
        <form action="" method="post">
            <input type="hidden" name="action" value="generatecheckin" />
            Class Day: <input type="text" id="codeday" name="codeday" value="<?php echo date("Y-m-d"); ?>" />
            Status: <input type="radio" name="open" value="true"/>Open <input type="radio" name="open" value="false" /> Closed
            <input type="submit" name="submit" value="Generate Code" />
        </form>

        Last 5 codes generated:<ul>
            <?php
//select codes for this course
            $result = mysql_query('SELECT * from checkincodes WHERE classid="' . mysql_real_escape_string($classdata['id']) . '" ORDER BY creationtime DESC LIMIT 5') or die(mysql_error());
            if(mysql_num_rows($result)>0){
            for ($i = 0; $row = mysql_fetch_assoc($result); $i++) {
                echo '<li>' . $row['code'] . ' for ' . $row['forclassday'];
                if ($row['checkinopen'] == 'true') {
                    echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=closecheckin&id=' . $classdata['id'] . '&code=">Open</a>';
                } else {
                    echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=openecheckin&id=' . $classdata['id'] . '&code=">Closed</a>';
                }
                echo '</li>';
            }
            } else {
                echo '<li>No checkin codes have been generated yet.</li>';
            }
            ?></ul>
    </form>
</body>
</html>
