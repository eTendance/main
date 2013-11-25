            function updatecheckincount() {
                $.get("viewclass.php?id=<?php echo $_GET['id'] ?>&date=<?php echo date("Y-m-d"); ?>&action=getdatecheckinsajax", function(data) {
                    $("#livecheckincount").html(data);
                });
            }
            $(function() {
                $("#codeday_text").datepicker({
                    showOn: "button",
                    buttonImage: "img/calendar.gif",
                    buttonImageOnly: true,
                    dateFormat: "mm-dd-yy",
                    altField: "#codeday",
                    altFormat: "yy-mm-dd"
                });
                $("#studentsabsenton_text").datepicker({
                    showOn: "button",
                    buttonImage: "img/calendar.gif",
                    buttonImageOnly: true,
                    dateFormat: "mm-dd-yy",
                    altField: "#studentsabsenton",
                    altFormat: "yy-mm-dd"
                });
            });
            $(document).ready(function() {
                updatecheckincount();
                setInterval(function() {
                    updatecheckincount();
                }, 10000);

                $("#attendancedialog").dialog({
                    autoOpen: false,
                    modal: true,
                    height: 800,
                    width: 1000,
                    title: "Attendance Book",
                    open: function(ev, ui) {
                        $('#attendanceBookIframe').attr('src', 'attendancebook.php?id=<?php echo $classdata['id'] ?>');
                    }
                });

                $('#bookBtn').click(function() {
                    $('#attendancedialog').dialog('open');
                });
                $('#tabs').tabs({
                    beforeActivate: function(event, ui) {
                        window.location.hash = ui.newPanel.selector;
                    }
                });

            });