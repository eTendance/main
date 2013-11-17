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


/* $query = 'SELECT users.* FROM users join enrollment on users.id=enrollment.userid WHERE users.id="' . mysql_real_escape_string($_GET['studentid']) . '" and enrollment.classid="' . $classdata['id'] . '"';
  $result = mysql_query($query);

  $studentdata = mysql_fetch_assoc($result); */

//query to find days the class met
$query = 'select 
    forclassday
from
    checkincodes
        left join
    checkins ON checkincodes.id = checkins.checkincodeid
where
    checkincodes.classid = "' . $classdata['id'] . '"';
$result = mysql_query($query);
$numberOfClassdays = mysql_num_rows($result);
$classdays=array();
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
    checkincodeid
from
    checkincodes
        left join
(select * from checkins where userid=' . $classuser['id'] . ') as checkins_sub on checkincodes.id=checkins_sub.checkincodeid
where checkincodes.classid="' . $classdata['id'] . '"';
                        echo '<tr>';
                        $result = mysql_query($query);
                        echo '<td>' . $classuser['lastname'] . ', ' . $classuser['firstname'] . '</td>';
                        while ($row = mysql_fetch_assoc($result)) {
                            echo '<td>';
                            if ($row['checkincodeid'] == '') {
                                echo '<span style="display:none">0</span><img src="img/absent.png" alt="Absent" height="20" width="20" />';
                                $counts['absent'] ++;
                            } else {
                                echo '<span style="display:none">1</span><img src="img/present.png" alt="Present" height="20" width="20" />';
                                $counts['present'] ++;
                            }
                            $counts['total'] ++;
                            echo '</td>';
                        }
                        echo '<td>' . $counts['absent'] . '/' . $counts['total'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
    </body>
</html>