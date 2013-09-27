<?php

/*
 * This is where professors will be taken after logging in
 */


require_once('global.php');

//verify that a professor is logged in to continue
check_auth('p');

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Professor Dashboard</title>
    </head>
    <body>
Your Class list
<br />
<?php
//select courses that this professor owns
$result = mysql_query('SELECT * FROM classes join classowners on classes.id=classowners.classid WHERE professorid="'.  mysql_real_escape_string($_SESSION['userdata']['id']).'"') or die(mysql_error());
for($i=0; $row=mysql_fetch_assoc($result);$i++){
    echo $row['name'] . ' <a href="viewclass.php?id='.$row['id'].'">View Class</a><br />';
}
?>

<br /><br />
Create a class<br />
<form action="<?php echo $_SERVER['PHP_SELF'] ?>?action=createclass" method="post">
Class name: <input type="text" name="classname" /><input type="submit" name="Create" />
</form>

<br /><br /><br />
<a href="login.php?logout=1">Logout</a>

    </body>
</html>
