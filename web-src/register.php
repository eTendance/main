<?php
/*
 * Allow users to register for an account
 */

require_once('global.php');

//process registration here
if (isset($_POST['firstname'])) {
    if (empty($_POST['firstname']))
        $error[] = 'First name was left empty';
    if (empty($_POST['lastname']))
        $error[] = 'last name was left empty';
    if (empty($_POST['email']))
        $error[] = 'email was left empty';
    if (empty($_POST['username'])) {
        $error[] = 'username cannot be empty';
    }
    if (empty($_POST['password'])) {
        $error[] = 'password cannot be empty';
    }

    if (!isset($error)) {
        $result = mysql_query("select id from users where username=\"" . mysql_real_escape_string($_POST['username']) . '"');
        if (mysql_num_rows($result) > 0) {
            $error[] = 'a user with that username already exists';
        }
    }

    if (!isset($error)) {
        mysql_query('INSERT INTO users (firstname,lastname,email,username,password,usertype) values("' . mysql_real_escape_string($_POST['firstname']) . '","' . mysql_real_escape_string($_POST['lastname']) . '","'
                        . mysql_real_escape_string($_POST['email']) . '","' . mysql_real_escape_string($_POST['username']) . '","' . mysql_real_escape_string($_POST['password']) . '","' . mysql_real_escape_string($_POST['type']) . '")') or die(mysql_error());
        header('Location: index.php');
    } else {
        echo $error[0];
    }
}

exit;
?>
<!--registration form here -->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        Login to the system
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            First Name: <input type="text" name="firstname" /><br />
            Last Name: <input type="text" name="lastname" /><br />
            Email: <input type="text" name="email" /><br />
            Username: <input type="text" name="username"/><br />
            Password: <input type="text" name="password"/><br />
            Account type: <select name="type">
                <option value="s">Student</option>
                <option value="p">Professor</option>
            </select><br />
            <input type="submit" value="Register" />
        </form>
    </body>
</html>
