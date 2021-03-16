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
ob_start();
include "./includes/config.php";
include "./includes/functions.php";
include_once('./includes/gpc_map.php');

if (isset($_GET['status']) && $_GET['status'] == 'eactivate') {
    //for changing status
    $userid = $_GET['uid'];

    $sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , vTxnId, ";
    $sql .="vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
    $sql .="vDescription  ,dDateReg,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee,nRefId,vReferenceNo,vName,vBank,
			dReferenceDate, nPlanId  from " . TABLEPREFIX . "users where nUserId='" . addslashes($userid) . "'";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        if ($row = mysqli_fetch_array($result)) {
            //start the activation
            $var_txnid = $row["vTxnId"];
            $var_first_name = $row["vFirstName"];
            $totalamt = $row["nAmount"];
            $paytype = $row["vMethod"];
            $var_email = $row["vEmail"];

            if ($paytype <> "cc" AND $paytype <> "pp" AND $paytype <> "free") {
                //II - Store check details
                $sql = "insert into " . TABLEPREFIX . "paymentdetails(nPaymentId,vName,vReferenceNo,vBank,dReferenceDate,dEntryDate) ";
                $sql .= " Values('','" . addslashes($row["vName"]) . "','" . addslashes($row["vReferenceNo"]) . "','" . addslashes($row["vBank"]) . "','" . addslashes($row["dReferenceDate"]) . "',now())";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                $var_txnid = mysqli_insert_id($conn);
            }//end if
            //III - add the user table entry
            $var_first_name = $row["vFirstName"];
            $totalamt = $row["nAmount"];
            $paytype = $row["vMethod"];
            $var_email = $row["vEmail"];

            $sql = "UPDATE " . TABLEPREFIX . "users SET vStatus='0',vDelStatus='0' WHERE nUserId='" . $row['nUserId'] . "'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));

            $var_new_id = mysqli_insert_id($conn);
            if(is_null($var_new_id) || $var_new_id == 0)
                $var_new_id = $row['nUserId'];

            //if ($paytype <> "cc" AND $paytype <> "pp" AND $paytype <> "free") {
                //IV - check and add referral table
                //Addition for referrals
                $var_reg_amount = 0;

                if ($row["nRefId"] != "0") {
                    $sql = "Select nRefId,nUserId,nRegAmount from " . TABLEPREFIX . "referrals where vRegStatus='0' AND nRefId='" . $row["nRefId"] . "'";
                    $result_test = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    if (mysqli_num_rows($result_test) > 0) {
                        if ($row_final = mysqli_fetch_array($result_test)) {
                            $var_reg_amount = $row_final["nRegAmount"];

                            $sql = "Update " . TABLEPREFIX . "referrals set vRegStatus='1',";
                            $sql .= "nUserRegId='" . $var_new_id . "',dRegDate=now() where nRefId='" . $row_final["nRefId"] . "'";

                            mysqli_query($conn, $sql) or die(mysqli_error($conn));

                            $sql = "Select nUserId from " . TABLEPREFIX . "user_referral where nUserId='" . $row_final["nUserId"] . "'";
                            $result_ur = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                            if (mysqli_num_rows($result_ur) > 0) {
                                $sql = "Update " . TABLEPREFIX . "user_referral set nRegCount = nRegCount + 1,nRegAmount=nRegAmount + $var_reg_amount where nUserId='" . $row_final["nUserId"] . "'";
                            }//end if
                            else {
                                $sql = "Insert into " . TABLEPREFIX . "user_referral(nUserId,nRegCount,nRegAmount) values('"
                                        . $row_final["nUserId"] . "','1','$var_reg_amount')";
                            }//end else
                            mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        }//end if
                    }//end if
                }//end if of referrals
                //V - Update transaction table

                $sql = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date,
						nUserId, nSaleId, nPlanId) VALUES ('R', '$var_txnid', ' $totalamt', '$paytype',now(), '" . $var_new_id . "', '', ".$row['nPlanId'].")";
                //echo $sql;
                //$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            /* }else {
                if($paytype <> 'free') {
                    $sql = "UPDATE " . TABLEPREFIX . "payment
                             SET nUserId = $userid where vTxn_id = '" . $row['vTxnId'] . "'";
                    $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }

                if(!is_null($var_new_id) && $var_new_id) {
                    $sql = "UPDATE " . TABLEPREFIX . "payment
    			  		 SET nUserId = '{$var_new_id}'
    			  		 WHERE nUserId = '" . addslashes($userid) . "'";
                    $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }
            } */
        }//end if
    }//end if
    header('location:login.php?status=activate');
    exit();
}//end if
?>