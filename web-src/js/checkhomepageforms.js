(function($) {
    $.QueryString = (function(a) {
        if (a == "")
            return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p = a[i].split('=');
            if (p.length != 2)
                continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'))
})(jQuery);

$(document).ready(function() {

    $("#login-required").dialog({
        modal: true,
        autoOpen: false,
        buttons: {
            Ok: function() {
                $(this).dialog("close");
            }
        }
    });
    $("#ui-message").dialog({
        modal: true,
        autoOpen: false,
        buttons: {
            Ok: function() {
                $(this).dialog("close");
            }
        }
    });

    if ($.QueryString["action"] == "enroll") {
        $("#login-required").dialog('open');
    }

    $("#theform").submit(function(e) {

        var url = "register.php"; // the script where you handle the form input.

        if (validateRegForm()) {
            $.ajax({
                type: "POST",
                url: url,
                data: $("#theform").serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $("#ui-message").html(data); // show response from the php script.
                    $("#ui-message").dialog('open');
                    $("#theform")[0].reset();
                    document.getElementById("username").focus();
                }
            });
        }

        e.preventDefault();
    });

    $("#topLoginForm").submit(function(e) {

        var url = "login.php"; // the script where you handle the form input.

        $.ajax({
            type: "POST",
            url: url,
            data: $("#topLoginForm").serialize(), // serializes the form's elements.
            success: function(data)
            {
                if (data == "1") {
                    $("#ui-message").html("System is logging you in...");
                    window.location.replace("professordashboard.php" + (location.search));
                } else if (data == "0") {
                    $("#ui-message").html("Login failure. Please check your username and password.");
                } else {
                    $("#ui-message").html("The system encountered an error. Please contact the site administrator.");
                }
                $("#ui-message").dialog('open');
                console.log(data);
            }
        });

        e.preventDefault();
    });


});

/*window.onload = init;
 // The "onload" handler. Run after the page is fully loaded.
 function init() {
 radio = document.getElementById("student");
 radio.checked = true;
 
 // Attach "onsubmit" handler
 document.getElementById("topLoginForm").onsubmit = validateForm;
 // Set initial focus
 document.getElementById("username").focus();
 }*/

function validateRegForm() {
    return (isNotEmpty("firstname", "Please enter your first name!")
            && isNotEmpty("lastname", "Please enter your last name!")
            && isValidEmail("email", "Enter a valid email!")
            && isLengthMinMax("usernameS", "User name should have 8 to 16 chars!", 8, 16)
            && isLengthMinMax("passwordS", "Enter a valid password (8 to 16 chars)!", 8, 16)
            && passwordCheck("passwordS", "passwordConf", "The passwords entered do not match!"));
}

function validateForm() {
    return(isLengthMinMax("username", "User name should have 8 to 16 chars!", 8, 16)
            && isLengthMinMax("password", "Enter a valid password (8 to 16 chars)!", 8, 16)
            && isAlphanumeric("username", "User name should be alphanumeric Characters!")
            && isAlphanumeric("password", "Password should be alphanumeric Characters!"));
}

// Return true if the input value is not empty
function isNotEmpty(inputId, errorMsg) {
    var inputElement = document.getElementById(inputId);
    var errorElement = document.getElementById(inputId + "Error");
    var inputValue = inputElement.value.trim();
    var isValid = (inputValue.length !== 0);  // boolean
    showMessage(isValid, inputElement, errorMsg, errorElement);
    return isValid;
}

// Check password confirmation
function passwordCheck(inputId, confId, errorMsg) {
    var inputElement = document.getElementById(inputId);
    var confElement = document.getElementById(confId);
    var errorElement = document.getElementById(inputId + "Error");
    var inputValue = inputElement.value.trim();
    var confValue = confElement.value.trim();
    var isValid = (inputValue == confValue);  // boolean
    showMessage(isValid, inputElement, errorMsg, errorElement);
    return isValid;
}

/* If "isValid" is false, print the errorMsg; else, reset to normal display.
 * The errorMsg shall be displayed on errorElement if it exists;
 *   otherwise via an alert().
 */
function showMessage(isValid, inputElement, errorMsg, errorElement) {
    if (!isValid) {
        // Put up error message on errorElement or via alert()
        if (errorElement !== null) {
            errorElement.innerHTML = errorMsg;
        } else {
            alert(errorMsg);
        }
        // Change "class" of inputElement, so that CSS displays differently
        if (inputElement !== null) {
            inputElement.className = "error";
            inputElement.focus();
        }
    } else {
        // Reset to normal display
        if (errorElement !== null) {
            errorElement.innerHTML = "";
        }
        if (inputElement !== null) {
            inputElement.className = "";
        }
    }
}

// Return true if the input value contains only letters (at least one)
function isAlphabetic(inputId, errorMsg) {
    var inputElement = document.getElementById(inputId);
    var errorElement = document.getElementById(inputId + "Error");
    var inputValue = inputElement.value.trim();
    var isValid = inputValue.match(/^[a-zA-Z]+$/);
    showMessage(isValid, inputElement, errorMsg, errorElement);
    return isValid;
}

// Return true if the input value contains only digits and letters (at least one)
function isAlphanumeric(inputId, errorMsg) {
    var inputElement = document.getElementById(inputId);
    var errorElement = document.getElementById(inputId + "Error");
    var inputValue = inputElement.value.trim();
    var isValid = inputValue.match(/^[0-9a-zA-Z]+$/);
    showMessage(isValid, inputElement, errorMsg, errorElement);
    return isValid;
}

// Return true if the input length is between minLength and maxLength
function isLengthMinMax(inputId, errorMsg, minLength, maxLength) {
    var inputElement = document.getElementById(inputId);
    var errorElement = document.getElementById(inputId + "Error");
    var inputValue = inputElement.value.trim();
    var isValid = (inputValue.length >= minLength) && (inputValue.length <= maxLength);
    showMessage(isValid, inputElement, errorMsg, errorElement);
    return isValid;
}

// Return true if the input value is a valid email address
// (For illustration only. Should use regexe in production.)
function isValidEmail(inputId, errorMsg) {
    var inputElement = document.getElementById(inputId);
    var errorElement = document.getElementById(inputId + "Error");
    var inputValue = inputElement.value;
    var atPos = inputValue.indexOf("@");
    var dotPos = inputValue.lastIndexOf(".");
    var isValid = (atPos > 0) && (dotPos > atPos + 1) && (inputValue.length > dotPos + 2);
    showMessage(isValid, inputElement, errorMsg, errorElement);
    return isValid;
}

// Verify password. The password is kept in element with id "pwId".
// The verified password in id "verifiedPwId".
function verifyPassword(pwId, verifiedPwId, errorMsg) {
    var pwElement = document.getElementById(pwId);
    var verifiedPwElement = document.getElementById(verifiedPwId);
    var errorElement = document.getElementById(verifiedPwId + "Error");
    var isTheSame = (pwElement.value === verifiedPwElement.value);
    showMessage(isTheSame, verifiedPwElement, errorMsg, errorElement);
    return isTheSame;
}
