function openWindow(date, classid) {

    $.ajax({
        type: "GET",
        url: "viewclass.php",
        data: {
            action: "getabsentjson",
            id: classid,
            date: date
        }}).done( function(response) {
            var jsonData = JSON.parse(response);
            var string = "";
            for(var i = 0; i < jsonData.length; i++) {
                var first = jsonData[i]["firstname"];
                var last = jsonData[i]["lastname"];
                string = string + first + " " + last + "<br>";
            }
            if(jsonData.length === 0) {
                string = "No students absent";
            }
            var dayI = document.getElementById("DayInfo");
            if(dayI.className === "hidden") {
                dayI.className = "";
            }
            var ab = document.getElementById("modAbscence");
            ab.innerHTML = "<h2>Students Absent for "+date+"</h2><br>" + string;
            
            $('#DayInfo').dialog();
        });
  
}

$(document).ready(function() {

    $('.calendar-day.highlight-class-day').click(function() {

        openWindow($(this).attr('date'), $(this).attr('classid'));

        /*$.post('absent.php', {"action": "getabsent";"date": date; "id": "test"}, function() {
         //$_REQUEST['id'] //is class ID, and not sure how to get this nor pass this yet... probably need to tie
         //calendar into the actual viewclass.php
         });*/
    });
});
