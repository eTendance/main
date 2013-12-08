<?php

/*
 * This is where professors will be taken after logging in
 */


require_once('global.php');

//verify that a professor is logged in to continue
check_auth('p');

if(isset($_POST['createclass'])){
        if(!empty($_POST['classname'])){
            
            //insert the class and create the association with this professor
            mysql_query('INSERT INTO classes (`name`,`enrollmentcode`) values ("'.mysql_real_escape_string($_POST['classname']).'","'.strtoupper(generateRandomString(10)).'")');
            mysql_query('INSERT INTO classowners (`professorid`, `classid`,`superowner`) values("'.$_SESSION['userdata']['id'].'","'.mysql_insert_id().'","true")');
            
        }    
}
if(isset($_GET['action'])){
    
    
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="css/eTendenceProfessor.css" />
        <meta charset=UTF-8>
        <title>eTendence - Professor Dashboard</title>
    </head>
    <body>
        <header><img src="img/eTendance-Logo.png" alt="eTendence Logo" id="logoPlacement" height="50px"/>
            <ul>
                <li><span id="profName">Welcome, <?php echo $_SESSION['userdata']['firstname'] . ' ' . $_SESSION['userdata']['lastname'] ?><form action="login.php" method="get"><input type="hidden" name="logout" value="1" /><input type="submit" value="Logout" id="loginButton" class="submit"/></form></span></li>
            </ul>
            
            <div id="breadcrumbs">eTendance Professor Dashboard</div>
        </header>
	<div id="container">
        <div id="add" class="dashboardbox">
            <h2>Create a Class</h2>
            <div>
                <form action="professordashboard.php" method="POST">
                    <div>
                        <input id="cName" type="text" name="classname" value="" maxlength="30" placeholder="Class Name"/>
                    </div>
                    <div>
                        <input type="submit" name="createclass" value="Create" id="create-class" class="submit" />
                    </div>
                </form>
            </div>
        </div>
        <div id="classes" class="dashboardbox">
            <h2>Your Classes</h2>
            <ul>
            <?php
//select courses that this professor owns
$result = mysql_query('SELECT classes.id,classes.name FROM classes join classowners on classes.id=classowners.classid WHERE professorid="'.  mysql_real_escape_string($_SESSION['userdata']['id']).'"') or die(mysql_error());
for($i=0; $row=mysql_fetch_assoc($result);$i++){
    	echo '<li>'.$row['name'] . ' <a href="viewclass.php?id='.$row['id'].'">View Class</a> <a href="viewclass.php?action=deleteclass&amp;id='.$row['id'].'" onclick="return confirm(\'Are you sure you wish to delete '.$row['name'].'? This action cannot be undone. All enrollment and attendance data will be permanently deleted.\')">Delete class</a></li>';
}
?>
            </ul>
        </div>
	</div>
    </body>
</html>
