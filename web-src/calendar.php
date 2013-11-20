<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/calendar.css">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<script type="text/javascript" src="js/calender.js"></script>
	<meta charset=UTF-8>
</head>
<body>
<?php

require_once('global.php');

check_auth('p');

/* draws a calendar */
function draw_calendar($month,$year){

	
	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
	/* table headings */
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">Month: <span id="month">'.$month.'</span> Year: <span id="year">'.$year.'</span></td></tr>';
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np"></td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		$calendar.= '<td class="calendar-day">';
			/* add in the day number */
			$calendar.= '<div class="day-number">'.$list_day.'</div>';

			/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
			$calendar.= str_repeat('<p> </p>',2);
			
			$query = 'SELECT count(enrollment.userid) FROM checkincodes JOIN
					enrollment LEFT JOIN checkins ON enrollment.userid =
					checkins.userid AND enrollment.classid = checkins.classid
					WHERE checkins.checkintime IS NULL AND checkincodes.
					forclassday = "'. mysql_real_escape_string($year).
					'-'.mysql_real_escape_string($month).
					'-'.mysql_real_escape_string($list_day).'"';
			$query_result = mysql_query($query) or die(mysql_error());
			$result = mysql_fetch_array($query_result);
			
		    $calendar.= $result['count(enrollment.userid)'];
		    $calendar.= '</td>';
		    
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np"></td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';
	
	/* all done, return result */
	return $calendar;
}echo draw_calendar(date("m"), date("y"));

?>
</body>
</html>