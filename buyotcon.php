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

if ($_GET["saleid"] != "") {
    $var_saleid = $_GET["saleid"];
    $var_userid = $_SESSION["guserid"];
    $var_date = $_GET["dt"];
    $var_flag = $_GET["flag"];
}//end if
$flag = false;


if ($var_flag == "true") {
    //checking escrow status
    if (DisplayLookUp('Enable Escrow') == 'Yes') {
        $SaleStatus = '1';
    }//end if
    else {
        $SaleStatus = "4";
    }//end esle

    $sql = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
    $sql .= " on sd.nSaleId = s.nSaleId ";
    $sql .= " where  sd.nSaleId='" . addslashes($var_saleid) . "' AND sd.nUserId='" . $_SESSION["guserid"] . "' AND sd.dDate='";
    $sql .= addslashes($var_date) . "' AND sd.vSaleStatus='" . $SaleStatus . "' AND sd.vRejected='0' ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        if ($row = mysqli_fetch_array($result)) {
            $var_title = $row["vTitle"];
            $var_quantity = $row["nQuantity"];
            $var_amount = $row["nAmount"];
            $var_message = MESSAGE_THANKYOU_FOR_PAYMENT_WAITING_FOR_ADMIN;
            $flag = true;
            //send mail to seller
            $subject = "One of your products listed at " . SITE_NAME . " has been sold.";
            //fetching seller information
            $condition = "where nSaleId='" . $var_saleid . "'";
            $sellerUserId = fetchSingleValue(select_rows(TABLEPREFIX . 'sale', 'nUserId', $condition), 'nUserId');
        
            $sellerSql = "select vLoginName,vEmail from ".TABLEPREFIX ."users where nUserId='" . $sellerUserId . "'";
            $sellerRs = mysqli_query($conn, $sellerSql);
            $sellRow = mysqli_fetch_array($sellerRs);
            $SellerName = $sellRow['vLoginName'];
            $EMail = $sellRow['vEmail'];
            
            if (DisplayLookUp('4') != '') {
            $var_admin_email = DisplayLookUp('4');
            }//end if

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
                $mainTextShow   = $mailRw['content'];
                if(!$txnid || $txnid==''){
                   $mainTextShow = str_replace("{txnid}", "", $mainTextShow);
                   $mainTextShow = str_replace("Transaction Id", "", $mainTextShow);
                }

                $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{var_quantity}","{txnid}","{guserFName}","{Account Summary}");
                $arrTReplace	= array(SITE_NAME,SITE_URL,$var_title,CURRENCY_CODE.$var_amount,$var_quantity,$txnid,$_SESSION["gloginname"],"'Account Summary'");
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

                $mainTextShow   = $mailRw['content'];
                if(!$txnid || $txnid==''){
                   $mainTextShow = str_replace("{txnid}", "", $mainTextShow);
                   $mainTextShow = str_replace("Transaction Id", "", $mainTextShow);
                }

                $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{var_quantity}","{txnid}","{guserFName}","{Account Summary}");
                $arrTReplace	= array(SITE_NAME,SITE_URL,$var_title,CURRENCY_CODE.$var_amount,$var_quantity,$txnid,$_SESSION["gloginname"],"'Account Summary'");
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent11   = $mainTextShow;

                $subject2        = $mailRw['content_title'];
                $subject2        = str_replace("{SITE_NAME}",SITE_NAME,$subject2);

                $StyleContent   = MailStyle($sitestyle,SITE_URL);
            

            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, addslashes($_SESSION["gloginname"]), $mailcontent11, $logourl, date('m/d/Y'), SITE_NAME, $subject2);
            $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

            send_mail($_SESSION["guseremail"], $subject2, $msgBody, SITE_EMAIL, 'Admin');
             //send mail to buyer end
                //
                //send mail to admin
                $mailRw = array();
                /*
                * Fetch email contents from content table
                */
               $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'soldoutMailToAdmin'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$_SESSION["lang_id"]."'";
                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                $mailRw  = mysqli_fetch_array($mailRs);


                $mainTextShow   = $mailRw['content'];

                $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{sellerName}","{buyerName}");
                $arrTReplace	= array(SITE_NAME,SITE_URL,$var_title,CURRENCY_CODE.$var_amount,$SellerName,$_SESSION["gloginname"]);
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent12   = $mainTextShow;

                $subject3        = $mailRw['content_title'];
                $subject3        = str_replace("{SITE_NAME}",SITE_NAME,$subject2);

                $StyleContent   = MailStyle($sitestyle,SITE_URL);

                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Administrator', $mailcontent12, $logourl, date('m/d/Y'), SITE_NAME, $subject3);
                $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');

                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                send_mail($var_admin_email, $subject3, $msgBody, SITE_EMAIL, 'Admin');
        }//end if
    }//end if
    else {
        $var_message =  ERROR_MISMATCH_DATA_CHECK_STATUS;
    }//end lese
}//end if
else {
    $var_message = MESSAGE_ENTRY_FOR_PURCHASE_CONTACT_ADMIN;
}//end else

include_once('./includes/purchase_information.php');
?>
