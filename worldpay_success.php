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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

//checking escrow status
if (DisplayLookUp('Enable Escrow') == 'Yes') {
    $SaleStatus = '2';
}//end if
else {
    $SaleStatus = '4';
}//end esle

$flag_to_continue = false;

$transstatus = $_REQUEST['transStatus'];

if (isset($_REQUEST['transStatus']) && $_REQUEST['transStatus'] == 'Y') {
    $flag_to_continue = true;
}//end if

if ($flag_to_continue == true) {

    if (DisplayLookUp('worldpaydemo') == "YES") {
        $txnid = 'TEST-' . time();
    }//end if
    else {
        $txnid = time();
    }//end if

    $saleid = $_SESSION['sess_saleid'];
    $now = date('Y-m-d');
    $cost = $_SESSION['sess_amount'];


    //check if txnid alredy there to prevent refresh
    $sql2 = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
    $sql2 .= " on sd.nSaleId = s.nSaleId ";
    $sql2 .= " where sd.nSaleId='" . addslashes($saleid) . "' AND sd.nUserId='" . $_SESSION["guserid"];
    $sql2 .= "' AND sd.dDate='" . addslashes($now) . "' AND  sd.vSaleStatus IN('" . $SaleStatus . "','3')";
    $result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
    if (mysqli_num_rows($result2) <= 0) {
        $sql = "Select vTxnId from " . TABLEPREFIX . "saledetails where vTxnId='" . addslashes($txnid) . "' AND vMethod='wp'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) <= 0) {
            $sql = "Update " . TABLEPREFIX . "saledetails set vSaleStatus='" . $SaleStatus . "',vTxnId='$txnid',dTxnDate=now() where ";
            $sql .= " nSaleId='" . addslashes($saleid) . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
            $sql .= addslashes($now) . "' ";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));

            $sql = "Select nUserId from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($saleid) . "'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));

            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                if ($row = mysqli_fetch_array($result)) {
                    if ($cost == '') {
                        $cost = '0';
                    }//end if

//                    $sql = "Update " . TABLEPREFIX . "users set nAccount = nAccount +  $cost  where  nUserId='" . $row["nUserId"] . "'";
//                    mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }//end if
            }//end if

            $sql = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
            $sql .= " on sd.nSaleId = s.nSaleId ";
            $sql .= " where  sd.nSaleId='" . addslashes($saleid) . "' AND sd.nUserId='" . $_SESSION["guserid"] . "' AND sd.dDate='";
            $sql .= addslashes($now) . "' AND sd.vSaleStatus='" . $SaleStatus . "' AND sd.vRejected='0' ";

            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                if ($row = mysqli_fetch_array($result)) {
                    $var_title = $row["vTitle"];
                    $var_quantity = $row["nQuantity"];
                    $var_amount = $row["nAmount"];
                    $var_date = $now;
                    $flag = true;

                    
                    //fetching seller information
                    $condition = "where nSaleId='" . $saleid . "'";
                    $sellerUserId = fetchSingleValue(select_rows(TABLEPREFIX . 'sale', 'nUserId', $condition), 'nUserId');

                    $condition = "where nUserId='" . $sellerUserId . "'";
                    $SellerName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vFirstName', $condition), 'vLoginName');
                    $EMail = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vEmail', $condition), 'vEmail');

                /*
                * Fetch user language details
                */

                $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
                $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
                $langRw = mysqli_fetch_array($langRs);

                /*
                * Fetch email contents from content table
                */
               $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'soldout'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$_SESSION["lang_id"]."'";
                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                $mailRw  = mysqli_fetch_array($mailRs);


                $mainTextShow   = $mailRw['content'];

                $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{var_quantity}","{txnid}","{guserFName}");
                $arrTReplace	= array(SITE_NAME,SITE_URL,$var_title,$var_amount,$var_quantity,$txnid,$_SESSION["guserFName"]);
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent1   = $mainTextShow;

                $subject        = $mailRw['content_title'];
                $subject        = str_replace("{SITE_NAME}",SITE_NAME,$subject);

                $StyleContent   = MailStyle($sitestyle,SITE_URL);
                
                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, addslashes($SellerName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
                //send mail to seller end
                //send mail to buyer
                $mailRw = array();
            /*
            * Fetch email contents from content table
            */
            $mailSql = "SELECT L.content,L.content_title
                      FROM ".TABLEPREFIX."content C
                      JOIN ".TABLEPREFIX."content_lang L
                        ON C.content_id = L.content_id
                       AND C.content_name = 'soldoutMailToBuyer'
                       AND C.content_type = 'email'
                       AND L.lang_id = '".$_SESSION["lang_id"]."'";
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);


            $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{var_quantity}","{txnid}","{guserFName}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,$var_title,$var_amount,$var_quantity,$txnid,$_SESSION["guserFName"]);
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent11  = $mainTextShow;

            $subject2       = $mailRw['content_title'];
            $subject2       = str_replace("{SITE_NAME}",SITE_NAME,$subject2);

            $StyleContent   = MailStyle($sitestyle,SITE_URL);

                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, addslashes($_SESSION["guserFName"]), $mailcontent11, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                send_mail($_SESSION["guseremail"], $subject2, $msgBody, SITE_EMAIL, 'Admin');


                $var_message = MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU;
                $_SESSION['sess_buyerid_escrow'] = '';
                $_SESSION['sess_saleid'] = '';
                $_SESSION['sess_amount'] = '';
            }//end if
        }//end if
        else {
                $flag = false;
                //a mail must be sent to the adminindicating the  rejecting of the offer while making the payment
                //end of mail section
                $var_message = ERROR_MISMATCH_DATA_REQUESTED."  ".str_replace('{site_email}',SITE_EMAIL,MESSAGE_CONTACT_EMAIL_FOR_DETAILS);
            }//end else
        }//end if
        else {
            $var_message = str_replace('{site_email}',SITE_EMAIL,MESSAGE_CHECK_PAYMENT_FURTHER_CONTACT_EMAIL);
        }//end else
    }//end if
    else {
        //populate data here
        if ($row2 = mysqli_fetch_array($result2)) {
            $var_title = $row2["vTitle"];
            $var_quantity = $row2["nQuantity"];
            $var_amount = $row2["nAmount"];
            $var_date = $now;
            $flag = true;
            $var_message = MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU;
            $_SESSION['sess_buyerid_escrow'] = '';
        }//end if
        //end population
    }//end else
}//end if
else {
    $var_message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
}//end else

include_once('./includes/purchase_information.php');
?>
