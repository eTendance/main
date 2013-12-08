<?php
    require_once('global.php');
    if(isset($_POST['submit'])) {
	$decide = $_POST['action'];
	if($decide == 0) {
		$username = $_POST['username'];
		$email = $_POST['email'];

		$result = mysql_query('SELECT password FROM users WHERE email="'.mysql_real_escape_string($email).'" AND username="'.mysql_real_escape_string($username).'"') or die(mysql_error());
                $row = mysql_fetch_assoc($result);
                
		if(mysql_num_rows($result) >0) {
                        $tempPass = generateRandomString(10);
			$message = "Your temporary password is ".$tempPass;
			mysql_query('UPDATE users SET password="'.mysql_real_escape_string($tempPass).'" WHERE email="'.mysql_real_escape_string($email).'" AND username="'.mysql_real_escape_string($username).'"') or die(mysql_error());
			$subject = "Your eTendence password";
			mail($email, $subject, $message);
		}
	}
	else if($decide == 1) {
		$email = $_POST['email'];
		$result = mysql_query('SELECT username FROM users WHERE email="'.mysql_real_escape_string($email).'"') or die(mysql_error());
                $row = mysql_fetch_assoc($result);
                
		if(mysql_num_rows($result) >0) {
			$message = "Your username is ".$row['username'];
			$subject = "Your eTendence username";
			mail($email, $subject, $message);
		}
	}
    }
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="css/forgotten.css" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>eTendence</title>
	</head>
	<body>
		<nav>
			<ul>
				<img src="img/eTendance-Logo.png" alt="eTendence Logo" id="logoPlacement" height="100em" />
				<a href="index.php">Go Back</a>
			</ul>
		</nav>
		<div id="forgotName">
			<h2>Forgot your Username?</h2>
			<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" id="forgetName">
				<ul><input type="hidden" name="action" value="1"/>
					<li><input type="email" placeholder="Enter Email Address" name="email"/></li>
					<li><input type="submit" name="submit" value="submit" class="submit"/></li>
				</ul>
			</form>
		</div>
		<div id="forgotPass">
			<h2>Forgot your Password?</h2>
			<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" id="forgetPass">
				<ul><input type="hidden" name="action" value="0"/>
					<li><input type="text" placeholder="Enter your Username" name="username"/></li>
					<li><input type="email" placeholder="Enter Email Address" name="email"/></li>
					<li><input type="submit" name="submit" value="submit" class="submit"/></li>
				</ul>
			</form>
		</div>
		<p>Please note that users who sign in with Google cannot use this feature.</p>
	</body>
</html>
