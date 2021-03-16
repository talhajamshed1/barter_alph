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

$txtPaypalEmail = DisplayLookUp('paypalemail');
$txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
$txtPaypalSandbox = DisplayLookUp('paypalmode');

if ($txtPaypalSandbox == "TEST") {
    $paypalurl = "www.sandbox.paypal.com";
}//end if
else {
    $paypalurl = "www.paypal.com";
}//end else
//ipn code sample
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}

$lines = explode("&", $req);
$keyarray = array();

$cc_flag = false;

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
//$fp = fsockopen($paypalurl, 80, $errno, $errstr, 30);
$fp = fsockopen ("ssl://".$paypalurl, 443, $errno, $errstr, 30);

if (trim($txtPaypalAuthtoken)==''){
    $cc_flag = true;
}
else if (!$fp) {
// HTTP ERROR
} else {
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
            }
        } else if (strcmp($res, "INVALID") == 0) {
// log for manual investigation
        }
    }
    fclose($fp);
}

if ($keyarray['receiver_email'] != $txtPaypalEmail && trim($txtPaypalAuthtoken)!='') {
    $cc_flag = false;
}

if ($keyarray['payment_status'] == "Completed") {

    $var_tmpid = $keyarray['item_number'];
    $var_swapid = $keyarray['option_selection1'];
    $txnid = $keyarray['txn_id'];
    $var_txnid = $txnid;

    if ($cc_flag == true) {

        $sql = "Select st.nTempId,st.nSwapId,st.nUserId,st.nAmount,st.vMethod,st.vMode,st.vPostType,st.dDate,s.vTitle  ";
        $sql .=" from " . TABLEPREFIX . "swaptemp st inner join  " . TABLEPREFIX . "swap s on st.nSwapId=s.nSwapId where st.nTempId='" . addslashes($var_tmpid) . "' ";
        $sql .="  AND s.vSwapStatus='1'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            if ($row = mysqli_fetch_array($result)) {
//data present in the temperory table

                $var_title = $row["vTitle"];
                $db_swapid = $row["nSwapId"];
                $db_userid = $row["nUserId"];
                $db_amount = $row["nAmount"];
                $db_method = $row["vMethod"];
                $db_mode = $row["vMode"];
                $db_post_type = $row["vPostType"];
                $var_swapmember = "";
                $var_incmember = "";

                if ($db_mode == "od") {
                    //if the payment is being made by the person who made the offer
                    //that means the present userid is the one that is present in the swaptxn table
                    //and this user is giving money to the person who made the swap table entry
                    //and the userid is fetched from the table swap
                    //swapmember --> the one in the temporary table
                    //incmember --> the one who receives the money(comes from the swap table)
                    $var_swapmember = $db_userid;

                    $sql = "Select nUserId from " . TABLEPREFIX . "swap where nSwapId='"
                            . $db_swapid . "' ";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result) > 0) {
                        if ($row = mysqli_fetch_array($result)) {
                            $var_incmember = $row["nUserId"];
                        }
                    }
                } elseif ($db_mode == "om") {
                    //if the payment is being made by the person who accepts the offer(ie. the one who
                    //made the main swap item),here the userid is the one in the swap table,hence
                    //he has to fetch the swapuserid from the swaptxn table,and give money to him
                    //swapmember --> the one in the swaptxn table
                    //incmember --> the one who receives the money(comes from the swaptxn table)

                    $db_amount = -1 * $db_amount;

                    $sql = "Select nUserId from " . TABLEPREFIX . "swaptxn where nSwapId='"
                            . $db_swapid . "' and vStatus='A'";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result) > 0) {
                        if ($row = mysqli_fetch_array($result)) {
                            $var_swapmember = $row["nUserId"];
                            $var_incmember = $row["nUserId"];
                        }
                    }
                } elseif ($db_mode == "wm") {
                    //if the payment is being made by the person who accepts the offer(ie. the one who
                    //made the main swap item),here the userid is the one in the swap table,hence
                    //he has to fetch the swapuserid from the swaptxn table,and give money to him
                    //swapmember --> the one in the swaptxn table
                    //incmember --> the one who receives the money(comes from the swaptxn table)

                    $db_amount = -1 * $db_amount;

                    $sql = "Select nUserId from " . TABLEPREFIX . "swaptxn where nSwapId='"
                            . $db_swapid . "' and vStatus='A'";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result) > 0) {
                        if ($row = mysqli_fetch_array($result)) {
                            $var_swapmember = $row["nUserId"];
                            $var_incmember = $row["nUserId"];
                        }
                    }
                }

                $db_swap_ids = get_swaps_ids($db_swapid);
                
                $db_amount = ($db_amount < 0) ? (-1 * $db_amount) : $db_amount;
                
                $sql = "Update " . TABLEPREFIX . "swap set 
                                                                         nSwapAmount='$db_amount',
                                                                         vEscrow='1',

                                                                         vMethod='$db_method',
                                                                         vTxnId='$var_txnid',
                                                                         vSwapStatus='2',dTxnDate=now() where
                                                                         nSwapId in (" . $db_swap_ids . ") ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//nSwapMember='$var_swapmember',

                

//                $sql = "Update " . TABLEPREFIX . "users set nAccount=nAccount + $db_amount
//                                                                          where nUserId='" . $var_incmember . "' ";
//                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                $sql = "delete from " . TABLEPREFIX . "swaptemp where nTempId='"
                        . addslashes($var_tmpid) . "' ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

//end of data updation
            }
        }
    }
}

$sql = "Insert into " . TABLEPREFIX . "tempdata(nId,vValue,vData)  values('','" . addslashes($var_tmpid) . "|$db_mode" . "','" . addslashes($txnid) . "');";
mysqli_query($conn, $sql);
?>