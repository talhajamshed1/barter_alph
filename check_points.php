<?php

session_start();
include ("./includes/config.php");
include ("./includes/functions.php");

$sndAmount = $_GET['q'];
//fectching point value
$showUserTotalPoints = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "Where nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');
if ($showUserTotalPoints > 0) {
    $showUserTotalPoints = $showUserTotalPoints;
}//end if
else {
    $showUserTotalPoints = '0';
}//end else

if ($showUserTotalPoints < $sndAmount) {
    echo '<b>' . str_replace('{point_name}', POINT_NAME, ERROR_ENTERED_POINT_LESS_THAN_AVAILABLE) . '</b>';
}//end if
?>