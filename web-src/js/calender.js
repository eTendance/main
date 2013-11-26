function dataPass(dat, uri, classid) {

    $.ajax({
        type: "POST",
        url: "absent.php",
        data: {
            action: "getAbsent",
            id: classid,
            date: dat
        },
        success: function(data2) {
            var jsonData = JSON.parse(data2);
            var awe = $('#modAbscence');
            console.log(jsonData);
        }
    });
}

$(document).ready(function() {

    var month = document.getElementById("month").innerHTML;
    var year = document.getElementById("year").innerHTML;
    var id = document.getElementById("id").innerHTML;

    $days = $('.day-number');
    $dayButtons = $('.calendar-day');

    $days.each(function(i) {
        var dayCast = $dayButtons[i];

//make a function and pass a parameter with either the date or prepared list of abscences
//or make an ajax call.
//take a look at order.js

        dayCast.onclick = function() {
            console.log("I am clicked");
            var day = $days[i].innerHTML;
            var date;

            if (month < 10) {
                date = "0" + month;
            } else {
                date = month;
            }
            if (day < 10) {
                date = date + "-0" + day;
            } else {
                date = date + "-" + day;
            }
            if (year < 10) {
                date = date + "-200" + year;
            } else {
                date = date + "-20" + year;
            }
            console.log(date);

            dataPass(date, "absent.php", id);
            var popup = window.open("absent.php", 'mywindow', "width=400, height=800");
            popup.focus();

            /*$.post('absent.php', {"action": "getabsent";"date": date; "id": "test"}, function() {
             //$_REQUEST['id'] //is class ID, and not sure how to get this nor pass this yet... probably need to tie
             //calendar into the actual viewclass.php
             });*/
        };
    });
});