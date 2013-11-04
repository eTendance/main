<!--<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        Welcome to eTendance 1.0 Pre-Alpha!
        <br /><br />
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </body>
</html>-->

<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="css/etendenceFront.css" />
		<script type="text/javascript" src='js/acctCreation.js'></script>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>eTendence</title>
	</head>
	<body>
		<nav>
			<ul>
				<img src="img/eTendance-Logo.png" alt="eTendence Logo" id="logoPlacement" height="50px" />
				<form action="login.php" method="post" id="topLoginForm">
					<label for="username">Username: <input type="text" name="username" id="username" value="" maxlength="30"/> </label>
					<label for="password">Password: <input type="password" name="password" id="password" value="" maxlength="30"/></label>
					<input type="submit" value="Login" class="submit"/>
				
				
				</form>
			</ul>
		</nav>
		<div id="features">
			<h2>Features</h2>
			<h3>Student Accounts</h3>
			<ul>
				<li>Register for a class via pin number</li>
				<li>Remove a class</li>
				<li>Enter a pin number to "sign the attendance sheet"</li>
				<li>Check average attendance of self for class as well as the average attendance of all students for the class</li>
			</ul>
			<h3>Professor Accounts</h3>
			<ul>
				<li>Create a class (which will generate a pin number for sign up)</li>
				<li>Delete a class</li>
				<li>Manually alter the attendance sheet, if necessary</li>
				<li>Generate a pin number for students to sign in for the day.</li>
				<li>Check average attendance per student within a class, and by class as a whole.</li>
			</ul>
		</div>
		<div id="signUp">
			<h2>Sign Up</h2>
			<form id="theform" action="register.php" method="post">
			<div>
				<span>
					<label for="firstname">First Name: </label>
					<input type="text" name="firstname" id="firstname" value="" maxlength="30"/>
				</span>

				<span>
					<label for="lastname">Last Name: </label>
					<input type="text" name="lastname" id="lastname" value="" maxlength="30"/>
				</span>				

				<span>
					<label for="email">e-mail: </label>
					<input type="text" name="email" id="email" value="" maxlength="30"/>
				</span>

				<span>
					<label for="usernameS">Username: </label>
					<input type="text" name="username" id="usernameS" value="" maxlength="30"/>
				</span>

				<span>
					<label for="passwordS">Password: </label>
					<input type="password" name="password" id="passwordS" value="" maxlength="30"/>
				</span>

				<span>
					Account type
					<select name="type"> <option id="s" value="s">Student</option> <option id="p" value="p">Professor</option> </select>
				</span>
				<span>
					<input type="submit" value="Register" class="submit"/>
				</span>
				
			</div>
			</form>
		</div>
	</body>
</html>