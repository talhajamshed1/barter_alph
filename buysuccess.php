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
sleep(40);
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

$txtPaypalEmail = DisplayLookUp('paypalemail');
$txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
$txtPaypalSandbox = DisplayLookUp('paypalmode');

$flag_to_continue = true;
      
if ($flag_to_continue==true){
    
    $userid     =   $_SESSION["guserid"];
 $flag = true;
    $sql = "Select vSaleStatus from " . TABLEPREFIX . "saledetails where nSaleId='".addslashes($_SESSION['userBuyID'])."'  AND nUserId = '" . addslashes($userid)."' AND dDate='" . addslashes($_SESSION['userBuyNow']) . "'" ;
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        if($row['vSaleStatus'] == '2' || $row['vSaleStatus'] == '4') {
            $var_message = stripslashes(MESSAGE_THANKYOU_FOR_PAYMENT_RECEIPT_EMAILED);
            $_SESSION['sess_buyerid_escrow'] = '';
            $sql = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
            $sql .= " on sd.nSaleId = s.nSaleId ";
            $sql .= " where  sd.nSaleId='" . addslashes($_SESSION['userBuyID']) . "' AND sd.nUserId='" . $userid. "' AND sd.dDate='".addslashes($_SESSION['userBuyNow'])."' AND sd.vRejected='0'";
     
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                if ($row = mysqli_fetch_array($result)) {
                    $var_title = $row["vTitle"];
                    $var_quantity = $row["nQuantity"];
                    $var_amount = $row["nAmount"];
                    $var_date = $_SESSION['userBuyNow'];
                    $flag = true;
                }
            }
        } else {
             $error = false;
            $var_message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
        }
    }
}
include_once('./includes/purchase_information.php');
?>