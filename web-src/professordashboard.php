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
            mysql_query('INSERT INTO classowners (`professorid`, `classid`) values("'.$_SESSION['userdata']['id'].'","'.mysql_insert_id().'")');
            
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
<!--<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Professor Dashboard</title>
    </head>
    <body>
        <h1>Professor Dashboard</h1>
        <h2>Your classes</h2>
<?php
//select courses that this professor owns
$result = mysql_query('SELECT classes.id,classes.name FROM classes join classowners on classes.id=classowners.classid WHERE professorid="'.  mysql_real_escape_string($_SESSION['userdata']['id']).'"') or die(mysql_error());
for($i=0; $row=mysql_fetch_assoc($result);$i++){
    echo $row['name'] . ' <a href="viewclass.php?id='.$row['id'].'">View Class</a> <a href="professordashboard.php?action=deleteclass&amp;classid='.$row['id'].'">Delete class</a><br />';
}
?>

<br /><br />
<h2>Create a class</h2>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<input type="text" name="classname" placeholder="Class name" /><input type="submit" name="createclass" value="Create" />
</form>

<br /><br /><br />
<a href="login.php?logout=1">Logout</a>

    </body>
</html>-->



<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="css/eTendenceProfessor.css" />
        <meta charset=UTF-8>
        <title>eTendence - Professor Dashboard</title>
    </head>
    <body>
        <nav>
            <ul>
                <img src="img/eTendance-Logo.png" alt="eTendence Logo" id="logoPlacement" height="50px"/>
                <li><form action="login.php" method="get"><input type="hidden" name="logout" value="1" /><input type="submit" value="Logout" class="submit"/></form></li>
                <li><span id="profName"><?php echo $_SESSION['userdata']['firstname'] . ' ' . $_SESSION['userdata']['lastname'] ?></span></li>
            </ul>
        </nav>
        <div id="add">
            <h2>Create a Class</h2>
            <div>
                <form action="professordashboard.php" method="POST">
                    <div>
                        <label for="cName">Class Name:</label>
                        <input id="cName" type="text" name="classname" value="" maxlength="30"/>
                    </div>
                    <!--<div>
                        <span>Meeting times</span>
                        <span class="meetTime">
                            <label for="sTime">Start Time:</label>
                            <span><input id="sTime" type="text" name="sTime" value="" maxlength="8"/></span>
                        </span>

                        <span class="meetTime">
                            <label for="eTime">End Time:</label> 
                            <span><input id="eTime" type="text" name="eTime" value="" maxlength="8"/></span>
                        </span>

                        <span class="meetTime" id="end">
                            Days of the Week:
                            <input type="checkbox" name="days" value="Mo"/>Mo
                            <input type="checkbox" name="days" value="Tu"/>Tu 
                            <input type="checkbox" name="days" value="We"/>We 
                            <input type="checkbox" name="days" value="Th"/>Th 
                            <input type="checkbox" name="days" value="Fr"/>Fr 
                            <input type="checkbox" name="days" value="Sa"/>Sa 
                            <input type="checkbox" name="days" value="Su"/>Su
                        </span>
                    </div>
                    <div>
                        <label for="descr">Brief Class description:</label><textarea cols="50" rows="4" name="description" id="descr" value=""></textarea>
                    </div>-->
                    <div>
                        <input type="submit" name="createclass" value="Create" class="submit" />
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
    </body>
</html>