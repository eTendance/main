<?php
require_once('global.php');

check_auth('p');

if (!isset($_GET['id'])) {
    showdashboard();
}


//check to make sure class exists and this professor owns it
$result = mysql_query('SELECT classes.*,classowners.superowner FROM classes join classowners on classes.id=classowners.classid where professorid="' . mysql_real_escape_string($_SESSION['userdata']['id']) . '" and classes.id="' . mysql_real_escape_string($_GET['id']) . '"');
if (mysql_num_rows($result) < 1) {
    showdashboard();
}
$classdata = mysql_fetch_assoc($result);


if (!empty($_REQUEST['action'])) {

    //this section will take care of any operations that occur on this page

    if ($_REQUEST['action'] == 'openenrollment') {
        mysql_query('UPDATE classes SET enrollmentopen="true" WHERE id="' . $classdata['id'] . '"');
    } elseif ($_REQUEST['action'] == "closeenrollment") {
        mysql_query('UPDATE classes SET enrollmentopen="false" WHERE id="' . $classdata['id'] . '"');
    } elseif ($_REQUEST['action'] == 'newenrollmentcode') {
        mysql_query('UPDATE classes SET enrollmentcode="' . strtoupper(generateRandomString(10)) . '" WHERE id="' . $classdata['id'] . '"');
    }

    if ($_REQUEST['action'] == 'opencheckin') {
        mysql_query('UPDATE checkincodes SET checkinopen="true" WHERE classid="' . $classdata['id'] . '" and code="' . mysql_real_escape_string($_GET['code']) . '"');
    } elseif ($_REQUEST['action'] == "closecheckin") {
        mysql_query('UPDATE checkincodes SET checkinopen="false" WHERE classid="' . $classdata['id'] . '" and code="' . mysql_real_escape_string($_GET['code']) . '"');
    }

    if ($_REQUEST['action'] == 'generatecheckin') {
        $query = 'SELECT id FROM checkincodes where classid="' . $classdata['id'] . '" and forclassday="' . mysql_real_escape_string($_POST['codeday']) . '"';
        $result = mysql_query($query) or die(mysql_error());
        if (mysql_num_rows($result) > 0) {
            $checkingenerationerror = 'A checkin code already exists for that class day.';
            $NOREDIR = true;
        }

        if (!isset($checkingenerationerror)) {
            $checkincode = strtoupper(generateRandomString(10));
            $query = 'INSERT INTO checkincodes (`code`,`classid`,`forclassday`,`checkinopen`) values("' . $checkincode . '","' . $classdata['id'] . '","' . mysql_real_escape_string($_POST['codeday']) . '","' . mysql_real_escape_string($_POST['open']) . '")';
            mysql_query($query) or die(mysql_error());
        }
    }


    /* return the number of users checked in, parameters are date, and id (of class) */
    if ($_REQUEST['action'] == 'getdatecheckinsajax') {
        $query = 'SELECT count(checkins.userid) FROM checkins '
                . 'join checkincodes on checkincodes.id=checkins.checkincodeid '
                . 'where checkins.classid = "' . $classdata['id'] . '" and '
                . 'checkincodes.forclassday = "' . mysql_real_escape_string($_GET['date']) . '"';

        $result = mysql_query($query) or die(mysql_error());
        $row = mysql_fetch_row($result);
        echo $row[0];
        exit;
    }


    if ($_REQUEST['action'] == 'addmanager' && $classdata['superowner'] == 'true') {
        $query = 'SELECT * FROM users WHERE username="' . mysql_real_escape_string($_POST['username']) . '" and usertype="p"';
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            if ($row['usertype']) {
                $sql = 'INSERT INTO classowners (`professorid`,`classid`,`superowner`) values("' . mysql_real_escape_string($_POST['username']) . '","' . $classdata['id'] . '","false");';
                $result = mysql_query($sql) or $addmanagererror = "There was an error adding this user as a manager. Please make sure this user is not already a manager.";
            } else {
                $addmanagererror = "The username specified does not exist or is not a professor.";
            }
            $NOREDIR = true;
        }
    }
    if (!isset($NOREDIR))
        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id']);
}


//get all students currently in class
$result = mysql_query('SELECT users.* FROM users 
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
        <link rel="stylesheet" type="text/css" href="css/viewClass.css" />
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script>
            function updatecheckincount() {
                $.get("viewclass.php?id=<?php echo $_GET['id'] ?>&date=<?php echo date("Y-m-d"); ?>&action=getdatecheckinsajax", function(data) {
                    $("#livecheckincount").html(data);
                });
            }
            $(function() {
                $("#codeday").datepicker({
                    showOn: "button",
                    buttonImage: "img/calendar.gif",
                    buttonImageOnly: true,
                    dateFormat: "yy-mm-dd"
                });
            });
            $(document).ready(function() {
                updatecheckincount();
                setInterval(function() {
                    updatecheckincount();
                }, 10000);

                $("#attendancedialog").dialog({
                    autoOpen: false,
                    modal: true,
                    height: 800,
                    width:1000,
                    title:"Attendance Book",
                    open: function(ev, ui) {
                        $('#attendanceBookIframe').attr('src', 'attendancebook.php?id=<?php echo $classdata['id'] ?>');
                    }
                });

                $('#bookBtn').click(function() {
                    $('#attendancedialog').dialog('open');
                });


            });
        </script>
    </head>
    <body>
	<header>
		<img src="img/eTendance-Logo.png" alt="eTendence Logo" id="logoPlacement" height="100em" />
		<ul>
			<li><form action="login.php" method="get"><input type="hidden" name="logout" value="1" /><input type="submit" value="Logout" id="loginButton" class="submit"/></form></li>
			<li><span id="profName"><?php echo $_SESSION['userdata']['firstname'] . ' ' . $_SESSION['userdata']['lastname']?></span></li>
		</ul>
	</header>
        <h1><?php echo $classdata['name'] ?></h1><br />

        <h2>Students</h2>
        <?php
        foreach ($classusers as $user) {
            echo $user['firstname'] . ' ' . $user['lastname'];
            echo '<br />';
        }
        ?>

        <div id="attendancedialog" style="overflow-x:hidden">
            <iframe id="attendanceBookIframe" src="" style="width:100%;height:95%;border:none"></iframe>
        </div>
        <button id="bookBtn">Open Attendance Book</button>

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
        <div class="errormsg"><?php if (isset($checkingenerationerror)) echo $checkingenerationerror; ?></div>
        <form action="" method="post">
            <input type="hidden" name="action" value="generatecheckin" />
            Class Day: <input type="text" id="codeday" name="codeday" value="<?php echo date("Y-m-d"); ?>" /><br />
            Status: <input type="radio" name="open" value="true" checked="checked"/>Open <input type="radio" name="open" value="false" /> Closed
            <br /><input type="submit" name="submit" value="Generate Code" />
        </form>

        <br />

        <div>
            Last 5 codes generated:<ul>
                <?php
//select and output the last 5 checkin codes created
                $result = mysql_query('SELECT * from checkincodes WHERE classid="' . mysql_real_escape_string($classdata['id']) . '" ORDER BY creationtime DESC LIMIT 5') or die(mysql_error());
                if (mysql_num_rows($result) > 0) {
                    for ($i = 0; $row = mysql_fetch_assoc($result); $i++) {
                        echo '<li>' . $row['code'] . ' for ' . $row['forclassday'] . ' ';
                        if ($row['checkinopen'] == 'true') {
                            echo '[<a href="' . $_SERVER['PHP_SELF'] . '?action=closecheckin&id=' . $classdata['id'] . '&code=' . $row['code'] . '">Open</a>]<br /><img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . $row['code'] . '&choe=UTF-8" />';
                        } else {
                            echo '[<a href="' . $_SERVER['PHP_SELF'] . '?action=opencheckin&id=' . $classdata['id'] . '&code=' . $row['code'] . '">Closed</a>]';
                        }
                        echo '</li>';
                    }
                } else {
                    echo '<li>No checkin codes have been generated yet.</li>';
                }
                ?></ul>
        </div>

        <h2>Live Checkin Count</h2>
        <div>
            So far, <b><span id="livecheckincount"></span></b> class members have checked in today.
        </div>

        <?php if ($classdata['superowner'] == 'true'): ?>
            <h2>Class Managers</h2>
            <div>
                Enter the username of another manager of the class. This person will be able to manage this class as you do, but will not have the ability to add or remove class managers.
                <br />
                <div><?php
                    if (isset($addmanagererror)) {
                        echo $addmanagererror;
                    }
                    ?></div>
                <form action="" method="post">
                    <input type="text" name="username" placeholder="Username" />
                    <input type="hidden" name="action" value="addmanager" />
                    <input type="submit" value="Add Manager"/>
                </form>
            </div>
        <?php endif; ?>
	<div><input type="button" value="Back" id="backButton"/>
	</div>
    </body>
</html>
