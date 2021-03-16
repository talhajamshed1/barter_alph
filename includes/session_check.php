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
if (!isset($_SESSION["guserid"]) || ($_SESSION["guserid"]== "")) {
        unset($_SESSION["guserid"]);
    $redirecturi = base64_encode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']);
    $redirectpath = $rootserver . '/login.php?redirecturi=' . $redirecturi;
   
    header("location:" . $redirectpath);
    exit();
}//end if
else {
    $var_flag = true;
}//end else

if (!isset($_SESSION["gdefid"]) || $_SESSION["gdefid"] == "") {
    /*if (function_exists('session_register')) {
        session_register("gdefid");
    }//end if
  */
    $_SESSION["gdefid"] = "1";
}//end if

?>