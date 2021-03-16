<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<sreejith.t@armia.com>        		                  |
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

$visibility_status = trim($_REQUEST['visibility_status']);

if($_SESSION['guserid']){
    mysqli_query($conn, "Update ".TABLEPREFIX."online set vVisible='".$visibility_status."' where nUserId='".$_SESSION['guserid']."'") or
												die(mysqli_error($conn));
}

//$arr = array ('lang_id'=>$_SESSION['lang_id'],'redirectUrl'=>urldecode($_REQUEST['redirect_url']));
//echo json_encode($arr);
header("Location:" . urldecode($_REQUEST['redirect_url']));
exit();
?>