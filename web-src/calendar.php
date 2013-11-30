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



/* draws a calendar */

function draw_calendar($month, $year) {
    global $classdata, $classusers;
    $query = 'SELECT forclassday, code, checkinopen FROM checkincodes WHERE classid="' . $classdata['id'] . '"';
    $result = mysql_query($query) or die(mysql_error());
    $classdays = array();
$checkincodes=array();
    while ($row = mysql_fetch_row($result)) {
        $classdays[] = $row[0];
        $checkincodes[] = array($row[1],$row[2]);
    }

    $id = $_GET['id'];
    /* draw table */
    $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
    /* table headings */
    $calendar.= '<tr class="calendar-row"><td class="calendar-day-head">Month: <span id="month">' . $month . '</span> Year: <span id="year">' . $year . '</span></td></tr>';
    $headings = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    $calendar.= '<tr class="calendar-row"><td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';

    /* days and weeks vars now ... */
    $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
    $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
    $days_in_this_week = 1;
    $day_counter = 0;
    $dates_array = array();

    $nextdate = new DateTime("$year-$month-01");
    $nextdate->modify('first day of next month');
    $prevdate = new DateTime("$year-$month-01");
    $prevdate->modify('first day of last month');


    $calendar .= '<div id="calendarselectmonth">';
    $calendar.='<a href="viewclass.php?id=' . $_GET['id'] . '&showmonth=' . $prevdate->format('Y-m') . '#CalendarTab">&lt;&lt; Previous Month</a> | ';
    $calendar.='<a href="viewclass.php?id=' . $_GET['id'] . '&showmonth=' . $nextdate->format('Y-m') . '#CalendarTab">Next Month &gt;&gt;</a></div>';

    /* row for week one */
    $calendar.= '<tr class="calendar-row">';

    /* print "blank" days until the first of the current week */
    for ($x = 0; $x < $running_day; $x++):
        $calendar.= '<td class="calendar-day-np"></td>';
        $days_in_this_week++;
    endfor;

    /* keep going with days.... */
    for ($list_day = 1; $list_day <= $days_in_month; $list_day++):
        $date = DateTime::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $list_day);
        if (($result=array_search($date->format('Y-m-d'), $classdays)) !== false) {
            $class = "calendar-day highlight-class-day";
            $isclassday = true;
            $code = $checkincodes[$result][0];
            $checkinopen = $checkincodes[$result][1];
        } else {
            $class = "calendar-day";
            $isclassday = false;
            $code="";
            $checkinopen = '';
        }
        if($checkinopen=='true') $status='open'; else $status='closed';
        $calendar.= '<td class="' . $class . '" date="' . $date->format('Y-m-d') . '" classid="' . $classdata['id'] . '" code="'.$code.'" status="'.$status.'">';
        /* add in the day number */
        $calendar.= '<div class="day-number">' . $list_day . '</div>';

        /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! * */
        $calendar.= str_repeat('<p> </p>', 2);
        $query = 'select count(checkins.userid), checkincodes.id, checkincodes.code from enrollment
left join checkins on enrollment.userid=checkins.userid and enrollment.classid=checkins.classid
left join checkincodes on checkincodes.id=checkins.checkincodeid
left join users on users.id=enrollment.userid
where enrollment.classid="' . mysql_real_escape_string($_REQUEST['id']) . '"
and (checkincodes.forclassday="' . mysql_real_escape_string($year) . '-' . mysql_real_escape_string($month) .
                '-' . mysql_real_escape_string($list_day) . '" or checkincodes.forclassday IS NULL)
    and checkins.checkintime IS NOT NULL';
        /* $query = 'SELECT count(enrollment.userid) FROM checkincodes left JOIN
          enrollment LEFT JOIN checkins ON enrollment.userid =
          checkins.userid AND enrollment.classid = checkins.classid
          WHERE enrollment.classid = "'.mysql_real_escape_string($_GET['id']).
          '" and checkins.checkintime IS NOT NULL AND checkincodes.forclassday = "'.
          mysql_real_escape_string($year).'-'.mysql_real_escape_string($month).
          '-'.mysql_real_escape_string($list_day).'" and checkins.classid="'.  mysql_real_escape_string($_GET['id']).'"'; */
        $query_result = mysql_query($query) or die(mysql_error());
        $result = mysql_fetch_row($query_result);



        if ($isclassday) {
            $calendar.= '<span class="absences-number">' . (count($classusers) - $result[0]) . '</span>';
        }
        $calendar.= '</td>';

        if ($running_day == 6):
            $calendar.= '</tr>';
            if (($day_counter + 1) != $days_in_month):
                $calendar.= '<tr class="calendar-row">';
            endif;
            $running_day = -1;
            $days_in_this_week = 0;
        endif;
        $days_in_this_week++;
        $running_day++;
        $day_counter++;
    endfor;

    /* finish the rest of the days in the week */
    if ($days_in_this_week < 8):
        for ($x = 1; $x <= (8 - $days_in_this_week); $x++):
            $calendar.= '<td class="calendar-day-np"></td>';
        endfor;
    endif;

    /* final row */
    $calendar.= '</tr>';

    /* end the table */
    $calendar.= '</table>';

    /* all done, return result */
    return $calendar;
}

if (isset($_GET['showmonth'])) {
    $date = DateTime::createFromFormat('Y-m', $_GET['showmonth']);
    echo draw_calendar($date->format('m'), $date->format('Y'));
} else {
    echo draw_calendar(date("m"), date("Y"));
}
?>