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
<!DOCTYPE html>
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
</html>
