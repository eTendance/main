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

	    if(month < 10) {
		date = "0" + month;
	    } else {
		date = month;
	    }
	    if(day < 10) {
		date = date +"-0" + day;
	    } else {
		date = date + "-" + day;
	    }
	    if(year < 10) {
		date = date + "-200" + year;
	    } else {
		date = date + "-20" + year;
	    }
	    console.log(date);

	    /*$.post('absent.php', {"action": "getabsent";"date": date; "id": "test"}, function() {
		//$_REQUEST['id'] //is class ID, and not sure how to get this nor pass this yet... probably need to tie
		//calendar into the actual viewclass.php
		$popup = window.open("absent.php", 'mywindow', "width=400, height=800");
		$popup.focus();
	    });*/
	};
    });  
});