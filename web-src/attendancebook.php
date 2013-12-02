<?php
require_once('global.php');

check_auth('p');

if (!isset($_GET['id'])) {
    showdashboard();
}


//check to make sure class exists and this professor owns it
$result = mysql_query('SELECT classes.*,classowners.superowner FROM classes join classowners on classes.id=classowners.classid where professorid="' . mysql_real_escape_string($_SESSION['userdata']['id']) . '" and classes.id="' . mysql_real_escape_string($_GET['id']) . '"');
if (mysql_num_rows($result) < 1) {
    showdashboard();
}
$classdata = mysql_fetch_assoc($result);


if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] == 'removecheckinajax' && isset($_REQUEST['checkincodeid']) && isset($_REQUEST['studentid'])) {
        $query = 'DELETE FROM checkins WHERE userid="' . mysql_real_escape_string($_REQUEST['studentid']) . '" and classid="' . $classdata['id'] . '" and checkincodeid="' . $_REQUEST['checkincodeid'] . '"';
        mysql_query($query) or die(mysql_error());
        exit;
    } elseif ($_REQUEST['action'] == 'addcheckinajax' && isset($_REQUEST['checkincodeid']) && isset($_REQUEST['studentid'])) {
        $query = 'INSERT INTO checkins (userid,classid,checkincodeid) values("' . mysql_real_escape_string($_REQUEST['studentid']) . '","' . $classdata['id'] . '","' . $_REQUEST['checkincodeid'] . '")';
        mysql_query($query) or die(mysql_error());
        ;
        exit;
    }
}


/* $query = 'SELECT users.* FROM users join enrollment on users.id=enrollment.userid WHERE users.id="' . mysql_real_escape_string($_GET['studentid']) . '" and enrollment.classid="' . $classdata['id'] . '"';
  $result = mysql_query($query);

  $studentdata = mysql_fetch_assoc($result); */

//query to find days the class met
$query = 'select 
forclassday
from
checkincodes
where
checkincodes.classid = "' . $classdata['id'] . '"';
$result = mysql_query($query);
$numberOfClassdays = mysql_num_rows($result);
$classdays = array();
while ($row = mysql_fetch_row($result)) {
    $classdays[] = $row[0];
}

//get all students currently in class
$result = mysql_query('SELECT users.* FROM users 
join enrollment on users.id=enrollment.userid
join classes on classes.id=enrollment.classid
where classes.id="' . mysql_real_escape_string($_GET['id']) . '"');
$classusers = array();
while ($row = mysql_fetch_assoc($result)) {
    $classusers[] = $row;
}
?>
<html>
    <head>
        <style type="text/css" title="currentStyle">
            @import "js/datatables/css/jquery.dataTables.css";
            body {
                margin:0;
            }
        </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script type="text/javascript" src="js/datatables/jquery.dataTables.min.js"></script> 
        <script type="text/javascript" src="js/datatables/FixedColumns.min.js"></script> 
        <script>

            $(document).ready(function()
            {
                var oTable = $("#attendance").dataTable({"sScrollX": "100%",
                    "bScrollCollapse": true,
                    "oSearch": {"sSearch": "<?php echo!empty($_GET['search']) ? $_GET['search'] : '' ?>"}
                });
                new FixedColumns(oTable, {
                    "iLeftColumns": 1,
                    "iLeftWidth": 120,
                });


                function checkincheckout(classid, code, studentid, action) {
                    var request = "attendancebook.php?id=" + classid + "&action=" + action + "&checkincodeid=" + code + "&studentid=" + studentid;
                    $.get(request, function(data) {
                        //console.log(data);
                        //console.log(request);
                    });
                }
                $(".checkinstatus").click(function() {
                    var date = $(this).attr('date');
                    var studentname = $(this).attr('studentname');
                    var studentid = $(this).attr('studentid');

                    var finalcount = $("#total-" + studentid);
                    if ($(this).find("span").html() == "0") {
                        //checkin user for this date 


                        var confirm = window.confirm("Are you sure you would like to manually check " + studentname + " in for " + date + "?");

                        if (confirm === true) {
                            checkincheckout(<?php echo $classdata['id'] ?>, $(this).attr('checkincodeid'), studentid, 'addcheckinajax');
                            $(this).find("span").html('1');
                            $(this).find("img").attr('src', 'img/present.png');
                            $("#total-" + studentid).html();
                            finalcount.html((finalcount.attr('absentdays') - 1) + '/' + finalcount.attr('totaldays'));
                            finalcount.attr('absentdays', finalcount.attr('absentdays') - 1);
                        }
                    } else {
                        var confirm = window.confirm("Are you sure you would l ike to manually remove the checkin of " + studentname + " on " + date + "?");

                        if (confirm === true) {
                            checkincheckout(<?php echo $classdata['id'] ?>, $(this).attr('checkincodeid'), studentid, 'removecheckinajax');
                            $(this).find("span").html('0');
                            $(this).find("img").attr('src', 'img/absent.png');
                            finalcount.html((finalcount.attr('absentdays')*1 + 1) + '/' + finalcount.attr('totaldays'));
                            finalcount.attr('absentdays', finalcount.attr('absentdays')*1 + 1);
                        } else {
                            return;
                        }

                    }


                });

            });


        </script>
    </head>
    <body>
        <table id="attendance">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <?php
                    foreach ($classdays as $classday) {
                        echo '<th>' . $classday . '</th>';
                    }
                    ?>
                    <th>Absences/Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($classusers as $classuser) {
                    $counts = array('absent' => 0, 'present' => 0, 'total' => 0);

//get attendance for each student
                    $query = 'select 
    checkincodes.id as daycodeid,checkincodeid,forclassday
from
    checkincodes
        left join
(select * from checkins where userid=' . $classuser['id'] . ' and classid="' . $classdata['id'] . '") as checkins_sub on checkincodes.id=checkins_sub.checkincodeid
where checkincodes.classid="' . $classdata['id'] . '"'
                            . 'order by forclassday';
                    echo '<tr>';
                    $result = mysql_query($query);
                    echo '<td>' . $classuser['lastname'] . ', ' . $classuser['firstname'] . '</td>';
                    while ($row = mysql_fetch_assoc($result)) {
                        echo '<td>';
                        if ($row['checkincodeid'] == '') {
                            echo '<div class="checkinstatus" checkincodeid="' . $row['daycodeid'] . '" date="' . $row['forclassday'] . '" studentname="' . htmlentities($classuser['lastname'] . ', ' . $classuser['firstname']) . '" studentid="' . $classuser['id'] . '"><span style="display:none">0</span><img src="img/absent.png" alt="Absent" height="20" width="20" /></div>';
                            $counts['absent'] ++;
                        } else {
                            echo '<div class="checkinstatus" checkincodeid="' . $row['daycodeid'] . '" date="' . $row['forclassday'] . '" studentname="' . htmlentities($classuser['lastname'] . ', ' . $classuser['firstname']) . '" studentid="' . $classuser['id'] . '"><span style="display:none">1</span><img src="img/present.png" alt="Present" height="20" width="20" /></div>';
                            $counts['present'] ++;
                        }
                        $counts['total'] ++;
                        echo '</td>';
                    }
                    echo '<td id="total-' . $classuser['id'] . '" totaldays="' . $counts['total'] . '" absentdays="' . $counts['absent'] . '">' . $counts['absent'] . '/' . $counts['total'] . '</td>';
                    echo '</tr>';
                }
                ?>   
            </tbody>
        </table>
    </body>
</html>