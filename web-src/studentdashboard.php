<?php
/*
 * This is where students will be taken after loggin in
 */

require_once('global.php');

check_auth('s');

if (!empty($_REQUEST['action'])) {

    if ($_REQUEST['action'] == 'enroll' && !empty($_REQUEST['enrollmentcode'])) {
        $query = 'SELECT * FROM classes WHERE enrollmentcode="' . mysql_real_escape_string($_REQUEST['enrollmentcode']) . '"';
        $result = mysql_query($query);
        if (mysql_num_rows($result) == 1) {
            $row = mysql_fetch_assoc($result);
            if ($row['enrollmentopen'] == "true") {
                $query = 'SELECT * FROM enrollment WHERE classid ="' . mysql_real_escape_string($row['id']) . '" AND userid ="' . $_SESSION['userdata']['id'] . '"';
                $result = mysql_query($query);
                if (mysql_num_rows($result) == 0) {
                    $query = 'INSERT INTO enrollment (classid,userid) values("' . mysql_real_escape_string($row['id']) . '","' . $_SESSION['userdata']['id'] . '")';
                    mysql_query($query);
                    $enrollmenterror = 'You have been enrolled successfully!';
                } else {
                    $enrollmenterror = 'You are already enrolled in this class.';
                }
            } else {
                $enrollmenterror = 'Enrollment for that class is currently closed. Please contact your professor.';
            }
        } else if (mysql_num_rows($result) == 0) {
            $enrollmenterror = 'Invalid enrollment code';
        }
    }


    if ($_REQUEST['action'] == 'checkin' && !empty($_REQUEST['checkincode'])) {
        $query = 'SELECT * FROM checkincodes WHERE code="' . mysql_real_escape_string($_POST['checkincode']) . '"';
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            $row_checkincode = mysql_fetch_assoc($result);

            $query = 'SELECT id from enrollment WHERE classid="' . $row_checkincode['classid'] . '" and userid="' . $_SESSION['userdata']['id'] . '"';
            $result = mysql_query($query);
            if (mysql_num_rows($result) < 1) {
                $checkinerror[] = 'You are not enrolled in that class. Please enroll before checking in or contact your professor to obtain an enrollment code.';
            }
            if ($row_checkincode['checkinopen'] == 'false') {
                $checkinerror[] = "The check-in for the selected class and day is currently closed. Please contact your professor.";
            }
        } else {
            $checkinerror[] = "The checkin code entered was invalid.";
        }

        if (!isset($checkinerror)) {
            $query = 'SELECT * FROM checkins WHERE checkincodeid="' . $row_checkincode['id'] . '" and userid="' . $_SESSION['userdata']['id'] . '"';
            $result = mysql_query($query);
            if (mysql_num_rows($result) > 0) {
                $checkinerror[] = "You have already checked in using that code.";
            }
        }


        if (!isset($checkinerror)) {
            $query = 'INSERT INTO checkins (`userid`,`classid`,`checkincodeid`) values("' . $_SESSION['userdata']['id'] . '","' . $row_checkincode['classid'] . '","' . $row_checkincode['id'] . '")';
            mysql_query($query) or die(mysql_error());
            $checkinerror[0]="Checked in sucessfully!";
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="css/eTendenceStudent.css" />
        <meta charset=UTF-8>
        <title>eTendence - Student Dashboard</title>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $(".openabsenceswindow").click(function(ev) {
                    ev.preventDefault();
                    $("#absencesdialog-" + $(this).attr('classid')).dialog('open');
                });
                $('.absencesdialog').each(function() {

                    var dialog = $(this).dialog({
                        height: 'auto',
                        width: '400',
                        modal: true,
                        autoOpen: false
                    });

                });
                if ($("#enrollmenterror").html() != "") {
                    $("#enrollmenterror").dialog({
                        modal: true,
                        autoOpen: true,
                        buttons: {
                            Ok: function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
                if ($("#checkinerror").html() != "") {
                    $("#checkinerror").dialog({
                        modal: true,
                        autoOpen: true,
                        buttons: {
                            Ok: function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });
        </script>
    </head>
    <body>
        <header>
            <img src="img/eTendance-Logo.png" alt="eTendence Logo" id="logoPlacement" height="50px"/>
            <ul>
                <li><span id="studName">Welcome, <?php echo $_SESSION['userdata']['firstname'] . ' ' . $_SESSION['userdata']['lastname'] ?><form action="login.php" method="get"><input type="hidden" name="logout" value="1" /><input type="submit" id="loginButton" value="Logout" class="submit"/></form></span></li>
            </ul>
            <div id="breadcrumbs">
                Student Dashboard
            </div>
        </header>
        <div id="container">
            <div id="add">
                <h2>Enroll in a Class</h2>
                <div id="enrollmenterror" title="Enrollment"><?php echo isset($enrollmenterror) ? $enrollmenterror : ""; ?></div>
                <p>Enter the enrollment code provided by your professor to enroll in a course.</p>
                <form action="studentdashboard.php" method="POST">
                    <input type="hidden" name="action" value="enroll" />
                    <input type="text" name="enrollmentcode" placeholder="Class enrollment code"/><input type="submit" id="enroll" class="submit" value="Enroll" />
                </form>
            </div>
            <div id="classes">
                <h2>Your Classes</h2>
                <ul>
                    <?php
                    $query = 'SELECT classes.*, classowners.professorid, users.firstname, users.lastname FROM etendance.classes 
    join enrollment on classes.id=enrollment.classid 
    join classowners on classes.id=classowners.classid 
    join users on classowners.professorid=users.id 
    WHERE enrollment.userid="' . $_SESSION['userdata']['id'] . '"';
                    $result = mysql_query($query);


                    if (mysql_num_rows($result) > 0) {
                        while ($row = mysql_fetch_assoc($result)) {
                            $dates_absent = mysql_query("SELECT forclassday from (SELECT * from checkins where checkins.userid = '" . $_SESSION['userdata']['id'] . "')  AS attendance RIGHT JOIN checkincodes ON( attendance.checkincodeid = checkincodes.id) WHERE checkincodes.classid='" . $row['id'] . "' AND checkincodeid IS NULL ");
                            $num_absences = mysql_num_rows($dates_absent);
                            echo '<li>' . $row['name'] . ' - ' . $row['firstname'] . ' ' . $row['lastname'];
                            if ($num_absences > 0) {
                                echo ' (<a href="#" class="openabsenceswindow" classid="' . $row['id'] . '">' . $num_absences . " absences</a>)";
                            } else {
                                echo ' (0 absences)';
                            }
                            echo '</li>';

                            if ($num_absences > 0) {
                                echo '<div title="Absences for ' . $row['name'] . '" class="absencesdialog" id="absencesdialog-' . $row['id'] . '"><h2>Days Absent:</h2><ul>';
                                while ($absent_row = mysql_fetch_assoc($dates_absent)) {
                                    echo '<li>' . $absent_row['forclassday'] . '</li>';
                                }
                                echo "</ul></div>";
                            }
                        }
                    } else {
                        echo '<div>You are current not signed up for any classes.</div>';
                    }
                    ?>
                </ul>
            </div>
            <div id="checkIn">
                <h2>Check In to class</h2>
                <div id="checkinerror" title="Class Checkin"><?php echo isset($checkinerror) ? $checkinerror[0] : ""; ?></div>
                <form action="" method="post">
                    <input type="hidden" name="action" value="checkin" />
                    <input type="text" name="checkincode" id="checkincode" value="" maxlength="30" placeholder="Checkin Pin"/>
                    <br><input type="submit" value="Checkin" id="check-In" class="submit"/>
                </form>
            </div>
        </div>

    </body>
</html>