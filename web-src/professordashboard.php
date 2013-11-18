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
    
    if($_GET['action']=='deleteclass' && !empty($_GET['classid'])){
        //delete the class, mysql will take care of deleting from the other tables because of foreign keys
        $query='DELETE classes FROM classes left join classowners on classes.id=classowners.classid WHERE classowners.classid="'.  mysql_real_escape_string($_GET['classid']).'" and classowners.professorid="'.$_SESSION['userdata']['id'].'"';
        mysql_query($query);
        //die($query);
        showdashboard();
    }
    
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
        <nav><img src="img/eTendance-Logo.png" alt="eTendence Logo" id="logoPlacement" height="50px"/>
            <ul>
                <li><span id="profName">Welcome, <?php echo $_SESSION['userdata']['firstname'] . ' ' . $_SESSION['userdata']['lastname'] ?><form action="login.php" method="get"><input type="hidden" name="logout" value="1" /><input type="submit" value="Logout" id="loginButton" class="submit"/></form></span></li>
            </ul>
            
            <div id="breadcrumbs">Professor Dashboard</div>
        </nav>
	<div id="container">
        <div id="add">
            <h2>Create a Class</h2>
            <div>
                <form action="professordashboard.php" method="POST">
                    <div>
                        <label for="cName">Class Name:</label>
                        <input id="cName" type="text" name="classname" value="" maxlength="30"/>
                    </div>
                    <div>
                        <input type="submit" name="createclass" value="Create" id="create-class" class="submit" />
                    </div>
                </form>
            </div>
        </div>
        <div id="classes">
            <h2>Classes</h2>
            <ul>
            <?php
//select courses that this professor owns
$result = mysql_query('SELECT classes.id,classes.name FROM classes join classowners on classes.id=classowners.classid WHERE professorid="'.  mysql_real_escape_string($_SESSION['userdata']['id']).'"') or die(mysql_error());
for($i=0; $row=mysql_fetch_assoc($result);$i++){
    	echo '<li>'.$row['name'] . ' <a href="viewclass.php?id='.$row['id'].'">View Class</a> <a href="professordashboard.php?action=deleteclass&amp;classid='.$row['id'].'">Delete class</a></li>';
}
?>
            </ul>
        </div>
	</div>
    </body>
</html>