
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="css/etendenceFront.css" />
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script type="text/javascript" src='js/checkhomepageforms.js'></script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>eTendence</title>
    </head>
    <body>
        <header>
            <img src="img/eTendance-Logo.png" alt="eTendence Logo" id="logoPlacement" height="100em" />
            <img src="img/logo.PNG" alt="eTendence Logo 2" id="logoPlacement2" height="100em" />

            <form action="login.php" method="post" id="topLoginForm">
                <input placeholder="Username" type="text" name="username" id="username" value="" maxlength="30"/> 
                <input placeholder="Password" type="password" name="password" id="password" value="" maxlength="30"/>
                <input type="hidden" id="querystring" name="qs" value="<?php echo $_SERVER['QUERY_STRING']; ?>" />
                <input type="submit" id="loginButton" value="Login" class="submit"/>
                <div id="forgotten">
                    <a href="forgotten.php">Forgot your Username or Password?</a>
                </div>
            </form>

        </header>

        <div id="container">
            <div id="features">
                <h2>Welcome To eTendance</h2>
                <p>eTendance is an online attendance manager designed for professors and students.</p>
                <p>Professors can create classes and generate unique check in codes for each day.</p>
                <p>Students can quickly check into a class using their laptop or smartphone. (student <a href="">app</a>)</p>
                <p>Attendance is immediately available, so professors and students can easily keep track of absences.</p>
                <p>Save time and avoid the hassel of taking attendance manually!</p>
            </div> 
            <div id="signUp">
                <h2>Sign Up</h2>

                <div title="large"> 
                    <form id="theform" action="register.php" method="post">

                        <span>

                            <input  type="text" name="firstname" id="firstname" value="" maxlength="30" placeholder="First Name"/>
                        </span>

                        <span>

                            <input type="text" name="lastname" id="lastname" value="" maxlength="30" placeholder="Last Name" />
                        </span>				

                        <span>

                            <input type="text" name="email" id="email" value="" maxlength="30" placeholder="E-Mail" />
                        </span>

                        <span>

                            <input type="text" name="username" id="usernameS" value="" maxlength="30" placeholder="Username" />
                        </span>

                        <span>

                            <input type="password" name="password" id="passwordS" value="" maxlength="30" placeholder="Password" />
                        </span>
                        <span>

                            <input type="password" name="passwordConf" id="passwordConf" value="" maxlength="30" placeholder="Confirm Password" />
                        </span>

                        <span>

                            <input type="radio" id="student" name="type" value="s" checked="checked"><label for="student"> Student</label>
                            <input type="radio" id="professor" name="type" value="p"><label for="professor"> Professor</label>

                        </span>
                        <span>
                            <input id="RegButton" type="submit" value="Register" class="submit"/>
                        </span>
                    </form>
                </div>
            </div>
        </div>
        <div id="login-required" title="Login Required">
            Please login to enroll in a course. If you do not yet have an account, use the registration form to create one then login with your new credentials.
        </div>
        <div id="ui-message" title="Message"></div>
    </body>
</html>
