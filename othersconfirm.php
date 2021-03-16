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
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include_once('./includes/gpc_map.php');

$approval_tag = "0";

if ($_SESSION['guserid']!=''){
    header("Location:usermain.php");
    exit();
}

if (DisplayLookUp('userapproval') != '') {
    $approval_tag = DisplayLookUp('userapproval');
}//end if

$flag = false;
if ($_SESSION["gtempid"] != "") {
    //populate data
    $sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
    $sql .="vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
    $sql .="vDescription  ,date_format(dDateReg,'%m/%d/%Y') as 'dDateReg'   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee
			from " . TABLEPREFIX . "users where nUserId='" . $_SESSION["gtempid"] . "'";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        if ($row = mysqli_fetch_array($result)) {
            $flag = true;

            $var_login_name = $row["vLoginName"];
            $var_first_name = $row["vFirstName"];
            $var_last_name = $row["vLastName"];
            $var_email = $row["vEmail"];
            $totalamt = $row["nAmount"];
            $paytype = $row["vMethod"];
            $now = date('m/d/Y', strtotime($row["dDateReg"]));
            $message = MESSAGE_THANKYOU_PAYMENT_TRANSACTION_COMPLETED."<br>&nbsp;<br> ".MESSAGE_LOGIN_ACCOUNT_AFTER_ADMIN_APPROVAL;
        }//end if
    }//end if
    else {
        $message = str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT);
    }//end else
    //end of population
    $_SESSION["gtempid"] = "";
}//end else
else {
    $message = MESSAGE_THANKYOU_PAYMENT_TRANSACTION_COMPLETED."<br>&nbsp;<br> ".MESSAGE_LOGIN_ACCOUNT_AFTER_ADMIN_APPROVAL;
    $message .= "<br>&nbsp;<br> ".str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT);
}//end else

include_once('./includes/registration_information.php');
?>
