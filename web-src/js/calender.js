$(document).ready(function() {

	var month = document.getElementById("month").innerHTML;
	var year = document.getElementById("year").innerHTML;

	$days = $('.day-number');
	$dayButtons = $('.calendar-day');

	$days.each(function(i) {
	var dayCast = $dayButtons[i];

	dayCast.onclick = function() {
	    console.log("I am clicked");
	    var day = $days[i].innerHTML;
	    var date;

	    var checker = day.charAt(1);
	    if(checker === null)
	    {
		console.log("I am null");
	    }
	    if(checker === undefined) {
		console.log("I am undefined");
	    }
	    if(checker === 0) {
		console.log("i am 0");
	    }
	    if(checker !== null && checker !== undefined) {
		date = month +"-" + day + "-20" + year;
	    } else {
		date = month +"-0" + day + "-20" + year;
	    }
	    console.log(date);

            $popup = window.open("absent.php", 'mywindow', "width=400, height=800");
	    $popup.focus();
	};
    });  
});