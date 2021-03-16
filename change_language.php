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
$sql_lang = "select lang_id,folder_name,country_abbrev from " . TABLEPREFIX . "lang where lang_id= '" . addslashes(trim($_REQUEST['language_id'])) . "' and lang_status='y'";

$res_lang = mysqli_query($conn, $sql_lang) or die(mysqli_error($conn));
if ($obj_row = mysqli_fetch_object($res_lang)) {
    $_SESSION['lang_id'] = $obj_row->lang_id;
    $_SESSION['lang_folder'] = $obj_row->folder_name;
    if($_SESSION['guserid']){
    mysqli_query($conn, "Update ".TABLEPREFIX."users set preferred_language='".$_SESSION["lang_id"]."' where nUserId='".$_SESSION['guserid']."'") or
												die(mysqli_error($conn));
    }
}

//$arr = array ('lang_id'=>$_SESSION['lang_id'],'redirectUrl'=>urldecode($_REQUEST['redirect_url']));
//echo json_encode($arr);
header("Location:" . urldecode($_REQUEST['redirect_url']));
exit();
?>