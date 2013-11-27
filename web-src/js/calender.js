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
            console.log(response);
        });
  
}

$(document).ready(function() {

    $('.calendar-day.highlight-class-day').click(function() {
        console.log("I am clicked");

        openWindow($(this).attr('date'), $(this).attr('classid'));

        /*$.post('absent.php', {"action": "getabsent";"date": date; "id": "test"}, function() {
         //$_REQUEST['id'] //is class ID, and not sure how to get this nor pass this yet... probably need to tie
         //calendar into the actual viewclass.php
         });*/
    });
});
