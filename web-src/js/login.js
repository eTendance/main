window.onload = init;
 
// The "onload" handler. Run after the page is fully loaded.
function init() {
   // Attach "onsubmit" handler
   document.getElementById("topLoginForm").onsubmit = validateForm;
   // Set initial focus
   document.getElementById("username").focus();
}

function validateForm() {
        return( isLengthMinMax("username", "User name should have 8 to 16 chars!", 8, 16)
        && isLengthMinMax("password", "Enter a valid password (8 to 16 chars)!", 8, 16)
        && isAlphabetic("username", "User name should be Alphbetic Characters!")
        && isAlphabetic("password", "Password should be Alphbetic Characters!"));
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
