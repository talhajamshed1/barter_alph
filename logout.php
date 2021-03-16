<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include "./includes/config.php";
include "./includes/functions.php";
include "./includes/session.php";
$language_id = $_SESSION['lang_id'];
$language_folder = $_SESSION['lang_folder'];
//update online table
$condition = "where nUserId='" . $_SESSION["guserid"] . "'";
$row = fetchSingleValue(select_rows(TABLEPREFIX . 'online', 'nLoggedOn', $condition), 'nLoggedOn');


//update user last login status
mysqli_query($conn, "UPDATE " . TABLEPREFIX . "users SET nLastLogin='" . $row . "' WHERE nUserId='" . $_SESSION["guserid"] . "'")
        or die(mysqli_error($conn));


//checking any sale details page without payment
$sqlSaleCheck = mysqli_query($conn, "select nSaleId,nQuantity from " . TABLEPREFIX . "saledetails where vMethod is null and dTxnDate is null") or die(mysqli_error($conn));
if (mysqli_num_rows($sqlSaleCheck) > 0) {
    while ($arr = mysqli_fetch_array($sqlSaleCheck)) {
        //back to added quantity to each row
        mysqli_query($conn, "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity + " . $arr['nQuantity'] . " where nSaleId ='" . addslashes($arr['nSaleId']) . "'") or die(mysqli_error($conn));
    }//end while loop
    //delete records
    mysqli_query($conn, "delete from " . TABLEPREFIX . "saledetails where vMethod is null and dTxnDate is null") or die(mysqli_error($conn));
}//end if

$url = "thanks.php?guseraffid=";
$url.=$_SESSION["guseraffid"];

if (isset($_SESSION["gaffid"])) {
    //delete from online table
    mysqli_query($conn, "DELETE FROM " . TABLEPREFIX . "online WHERE nUserId='" . $_SESSION["gaffid"] . "'") or die(mysqli_error($conn));

    $_SESSION["gaffid"] = "";
   /* if (function_exists('session_unregister')) {
        session_unregister("gaffid");
    }//end if*/
     unset ($_SESSION["gaffid"]);
     unset($_SESSION['user_status']);
    //session_unset();
}
if (isset($_SESSION["guserid"])) {
    //delete from online table
    mysqli_query($conn, "DELETE FROM " . TABLEPREFIX . "online WHERE nUserId='" . $_SESSION["guserid"] . "'") or die(mysqli_error($conn));

    $_SESSION["guserid"] = "";
  /*  if (function_exists('session_unregister')) {
        session_unregister("guserid");
    }*///end if

    unset ($_SESSION["guserid"]);
    unset($_SESSION['user_status']);
    unset($_SESSION['sess_upgradeplan']);
    
}

$_SESSION['lang_id'] = $language_id;
$_SESSION['lang_folder'] = $language_folder;
if (file_exists("./languages/".$_SESSION['lang_folder']."/common.php"))
    include("./languages/".$_SESSION['lang_folder']."/common.php");//common language file

if($_GET["from"]=='mfee'){
    $_SESSION["rurl"] = "paymonthlyfee.php?uid=".$_GET['uid'];
    header("location:login.php?msg=".ERROR_LOGIN_TO_PAY);
    exit();
}else if($_GET["from"]=='dea'){     
    header("location:login.php?msg=".ERROR_ACCOUNT_DEACTIVATED);
    exit();
}else{    
    header("location:$url");
    exit();
}
?>