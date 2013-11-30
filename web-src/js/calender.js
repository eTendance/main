function openWindow(date, classid, code, checkinopen) {

    $.ajax({
        type: "GET",
        url: "viewclass.php",
        data: {
            action: "getabsentjson",
            id: classid,
            date: date
        }}).done(function(response) {
        var jsonData = JSON.parse(response);
        var string = "";
        for (var i = 0; i < jsonData.length; i++) {
            var first = jsonData[i]["firstname"];
            var last = jsonData[i]["lastname"];
            string = string + first + " " + last + "<br>";
        }
        if (jsonData.length === 0) {
            string = "No students absent";
        }
        var dayI = document.getElementById("DayInfo");
        if (dayI.className === "hidden") {
            dayI.className = "";
        }
        var ab = document.getElementById("modAbscence");
        ab.innerHTML = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" + code + "&choe=UTF-8\" />";
        if (checkinopen == 'open')
            ab.innerHTML += "<br />Checkin Code: " + code + '[<a href="#" class="openclosecheckin" status="open" classid="' + classid + '" code="' + code + '">Open</a>]';
        else
            ab.innerHTML += "<br />Checkin Code: " + code + '[<a href="#" class="openclosecheckin" status="closed" classid="' + classid + '" code="' + code + '">Closed</a>]';
        ab.innerHTML += "<h2>Absences:</h2>" + string;

setOpenCloseCheckinHandler();
        $('#DayInfo').dialog({width: 'auto', title: date});
        console.log(response);
    });

}



$(document).ready(function() {

    $('.calendar-day.highlight-class-day').click(function() {

        openWindow($(this).attr('date'), $(this).attr('classid'), $(this).attr('code'), $(this).attr('status'));

        /*$.post('absent.php', {"action": "getabsent";"date": date; "id": "test"}, function() {
         //$_REQUEST['id'] //is class ID, and not sure how to get this nor pass this yet... probably need to tie
         //calendar into the actual viewclass.php
         });*/
    });

});
