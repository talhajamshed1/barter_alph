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
session_start();
include "./includes/functions.php";

$approval_tag = "0";

if (DisplayLookUp('userapproval') != '') {
    $approval_tag = DisplayLookUp('userapproval');
}//end if

$txtPaypalEmail = DisplayLookUp('paypalemail');
$txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
$txtPaypalSandbox = DisplayLookUp('paypalmode');

if ($txtPaypalSandbox == "TEST") {
    $paypalurl = "www.sandbox.paypal.com";
}//end if
else {
    $paypalurl = "www.paypal.com";
}//end else

$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}//end foreach

$lines = explode("&", $req);
$keyarray = array();

$cc_flag = false;

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen('ssl://'.$paypalurl, 443, $errno, $errstr, 30);

if (trim($txtPaypalAuthtoken)==''){
    $cc_flag = true;
}
else if (!$fp) {
    // HTTP ERROR
}//end if
else {
    fputs($fp, $header . $req);
    while (!feof($fp)) {
        $res = fgets($fp, 1024);
        if (strcmp($res, "VERIFIED") == 0) {
            // check the payment_status is Completed
            // check that txn_id has not been previously processed
            // check that receiver_email is your Primary PayPal email
            // check that payment_amount/payment_currency are correct
            // process payment
            $cc_flag = true;

            for ($i = 1; $i < count($lines); $i++) {
                list($key, $val) = explode("=", $lines[$i]);
                $keyarray[urldecode($key)] = urldecode($val);
                //echo("key : " . urldecode($key) . "   value : " . urldecode($val) . "<br>");
            }//end for loop
        }//end if
        else if (strcmp($res, "INVALID") == 0) {
            // log for manual investigation
        }//end else if
    }//end while loop
    fclose($fp);
}//end else
//filter custom value
//$CustomArray = @split('-', $keyarray['custom']);
$CustomArray = @explode('-', $keyarray['custom']);

if ($keyarray['receiver_email'] != $txtPaypalEmail && trim($txtPaypalAuthtoken)!='') {
    $cc_flag = false;
}//end if

if (trim($keyarray['subscr_id']) != '') {
    $var_id = $keyarray['option_selection1'];
    $in_login_name = $keyarray['option_selection2'];
    $txnid = $keyarray['subscr_id'];
    $var_txnid = $txnid;

    if ($cc_flag == true) {
        //TRANSACTION START
        $sql = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid' AND vTxn_mode='pp'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) <= 0) {
            $var_date = date('m/d/Y');

            //calculate end date
            switch ($CustomArray[4]) {
                case "M":
                    $addInterval = 'MONTH';
                    break;

                case "Y":
                    $addInterval = 'YEAR';
                    break;
            }//end switch

            $expDate = mysqli_query($conn, "SELECT DATE_ADD(now(),INTERVAL 1 " . $addInterval . ") as expPlanDate") or die(mysqli_error($conn));
            if (mysqli_num_rows($expDate) > 0) {
                $nExpDate = mysqli_result($expDate, 0, 'expPlanDate');
            }//end if

            $userUpdate = "";

            //update member tbl in new plan
            mysqli_query($conn, "update " . TABLEPREFIX . "users set nPlanId='" . $CustomArray[0] . "',vStatus='0',dPlanExpDate='" . $nExpDate . "', vMethod='pp', vTxnId='$txnid'
										where nUserId='" . $CustomArray[1] . "'	and
										nPlanId='" . $CustomArray[2] . "'") or die(mysqli_error($conn));

            $sqlPayment = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId,
								nSaleId,vInvno,vPlanStatus,nPlanId) VALUES ('R', '$txnid', '" . $CustomArray[3] . "',
								'pp',now(), '" . $CustomArray[1] . "',
								'','$Inv_id','A','" . $CustomArray[0] . "')";
            $result = @mysqli_query($conn, $sqlPayment) or die(mysqli_error($conn));

            $flag = true;
            $CustomArray[0] = '';
            $CustomArray[3] = '';
            $CustomArray[4] = '';
            $_SESSION['sess_PlanId'] = $CustomArray[0];
        }//end if
        //TRANSACTION END
    }//end if
}//end if
?>