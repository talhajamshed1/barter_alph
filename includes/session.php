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
if (strcasecmp(basename($_SERVER['SCRIPT_FILENAME']), "install.php") == 0) {
    ;
}//endif
else {
    if (INSTALLED === true) {
        ;
    }//end if
    else {
        header("location:./install/install.php");
    }//end else
}//end else
//error_reporting(0);

session_start();
date_default_timezone_set("America/Los_Angeles");
/*
if (!isset($_SESSION["guserid"]) || $_SESSION["guserid"] == "") {
    if (function_exists('session_register')) {
        session_register("guserid");
        session_register("guserpass");
        session_register("guseremail");
        session_register("catbar");
        session_register("gloginname");
        session_register("gtempid"); //used  when a temperory user registers and goes to the payment
        session_register("gdefid");
        session_register("gsaleextraid");
        session_register("gstempid"); // used at the time user goes to the paypal site for escrow payment after swap/wish
    }//end if
}//end if

if (!isset($_SESSION["gaffid"]) || $_SESSION["gaffid"] == "") {
    if (function_exists('session_register')) {
        session_register("gaffid");
        session_register("gaffname");
        session_register("gloginname");
    }//end if
}//end if
if (!isset($_SESSION["gadminid"]) || $_SESSION["gadminid"] == "") {
    if (function_exists('session_register')) {
        session_register("gadminid");
        session_register("backurl");
        session_register("gloginname");
    }//end if
}//end if
*/
?>