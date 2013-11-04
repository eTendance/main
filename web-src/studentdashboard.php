<?php
/*
 * This is where students will be taken after loggin in
 */

require_once('global.php');

check_auth('s');

if (!empty($_REQUEST['action'])) {

    if ($_REQUEST['action'] == 'enroll' && !empty($_POST['enrollmentcode'])) {
        $query = 'SELECT * FROM classes WHERE enrollmentcode="' . mysql_real_escape_string($_POST['enrollmentcode']) . '"';
        $result = mysql_query($query);
        if (mysql_num_rows($result) == 1) {
            $row = mysql_fetch_assoc($result);
            if ($row['enrollmentopen'] == "true") {
                $query = 'SELECT * FROM enrollment WHERE classid ="' . mysql_real_escape_string($row['id']) . '" AND userid ="' . $_SESSION['userdata']['id'] . '"';
                $result = mysql_query($query);
                if (mysql_num_rows($result) == 0) {
                    $query = 'INSERT INTO enrollment (classid,userid) values("' . mysql_real_escape_string($row['id']) . '","' . $_SESSION['userdata']['id'] . '")';
                    mysql_query($query);
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
}
?>
<!--<h1>Student Dashboard</h1>
<h2>Your Classes</h2>
<ul>
<?php
$query = 'SELECT classes.*, classowners.professorid, users.firstname, users.lastname FROM etendance.classes 
    join enrollment on classes.id=enrollment.classid 
    join classowners on classes.id=classowners.classid 
    join users on classowners.professorid=users.id 
    WHERE enrollment.userid="' . $_SESSION['userdata']['id'] . '"';
$result = mysql_query($query);

while ($row = mysql_fetch_assoc($result)) {
    echo '<li>' . $row['name'] . ' - ' . $row['firstname'] . ' ' . $row['lastname'] . '</li>';
}
?>
</ul>

<br />

<h2>Enroll in a class</h2>
<div><?php echo isset($enrollmenterror) ? $enrollmenterror : ""; ?></div>
Enter the code provided by your professor.
<form action="" method="post">
    <input type="hidden" name="action" value="enroll" />
    <input type="text" name="enrollmentcode" placeholder="Class enrollment code"/><input type="submit" value="Enroll" />
</form>

<br /><br /><br />
<a href="login.php?logout=1">Logout</a>
-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="css/eTendenceStudent.css" />
        <meta charset=UTF-8>
        <title>eTendence - Student Dashboard</title>
    </head>
    <body>
        <nav>
            <ul>
                <img src="img//eTendance-Logo.png" alt="eTendence Logo" id="logoPlacement" height="50px"/>
                <li><form action="login.php" method="get"><input type="hidden" name="logout" value="1" /><input type="submit" value="Logout" class="submit"/></form></li>
                <li><span id="studName"><?php echo $_SESSION['userdata']['firstname'] . ' ' . $_SESSION['userdata']['lastname'] ?></span></li>
            </ul>
        </nav>
        <div id="add">
            <h2>Enroll in a Class</h2>
            <p>Enter the enrollment code provided by your professor to enroll in a course.</p>
            <form action="studentdashboard.php" method="POST">
                <input type="hidden" name="action" value="enroll" />
                <input type="text" name="enrollmentcode" placeholder="Class enrollment code"/><input type="submit" value="Enroll" />
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

while ($row = mysql_fetch_assoc($result)) {
    echo '<li>' . $row['name'] . ' - ' . $row['firstname'] . ' ' . $row['lastname'] . '</li>';
}
?>
            </ul>
        </div>
        <div id="checkIn">
            <h2>Check In to class (Incomplete feature)</h2>
            <form action="" method="">
                <label for="checkPin">Check In Pin:</label>
                <input type="text" name="checkPin" id="checkPin" value="" maxlength="30"/>
                <input type="submit" value="Submit" class="submit"/>
            </form>
        </div>
    </body>
</html>