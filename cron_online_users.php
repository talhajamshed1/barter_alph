<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include_once('./includes/gpc_map.php');

/*$sqlUser = mysqli_query($conn, "SELECT u.nUserId,o.nLoggedOn FROM " . TABLEPREFIX . "users u
                                LEFT JOIN " . TABLEPREFIX . "Online o ON o.nUserId=u.nUserId
                                WHERE DATE_ADD(date_format(FROM_UNIXTIME(nActiveTill),'%Y-%c-%d'), INTERVAL 1 DAY) < CURDATE()") or die(mysqli_error($conn));*/
$sqlUser = mysqli_query($conn, "SELECT u.nUserId,o.nLoggedOn FROM ".TABLEPREFIX."users u 
                                LEFT JOIN ".TABLEPREFIX."online o ON o.nUserId=u.nUserId
                                WHERE DATE_ADD(date_format(FROM_UNIXTIME(nActiveTill),'%Y-%c-%d'), INTERVAL 30 minute) < CURDATE()") or die(mysqli_error($conn));
if (mysqli_num_rows($sqlUser) > 0) {
    while ($arrUser = mysqli_fetch_array($sqlUser)) {
        //update user last login status
        mysqli_query($conn, "UPDATE " . TABLEPREFIX . "users SET nLastLogin='" . $arrUser['nLoggedOn'] . "' WHERE nUserId='" . $arrUser['nUserId'] . "'")
                or die(mysqli_error($conn));

        //delete from online table
        mysqli_query($conn, "DELETE FROM " . TABLEPREFIX . "online WHERE nUserId='" . $arrUser['nUserId'] . "'") or die(mysqli_error($conn));
    }//end while loop
}//end if
?>
