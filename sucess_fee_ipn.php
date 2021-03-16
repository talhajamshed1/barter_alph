<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
//include common files
include "./includes/config.php";
include "./includes/functions.php";
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
//include ("./includes/session_check.php");

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
$header .= "POST https://".$paypalurl."/cgi-bin/webscr HTTP/1.1\r\n"; 
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Host: ".$paypalurl."\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ("ssl://".$paypalurl, 443, $errno, $errstr, 30);

if (!$fp) {
    // HTTP ERROR
}else {
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
            $cc_flag = false;
            // log for manual investigation
        }//end else if
    }//end while loop
    fclose($fp);
}//end else
//filter custom value
//$CustomArray = @split('-', $keyarray['custom']);
$CustomArray = @explode('-', $keyarray['custom']);

if ($cc_flag == true) {
//    $txnid = $keyarray['txn_id'];
    $txnid = $_POST['txn_id'];
    $var_txnid = $txnid;

    $sqltxn = @mysqli_query($conn, "Select vTxnId from " . TABLEPREFIX . "successtransactionpayments where vTxnId ='$txnid' AND vMethod='pp'") or die(mysqli_error($conn));

    if (@mysqli_num_rows($sqltxn) > 0) {
        $message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
    }//end if
    else {
        $var_date = date('m/d/Y');

        //update status in success fee table
        mysqli_query($conn, "UPDATE " . TABLEPREFIX . "successfee SET vStatus='A' WHERE nSId='" . $CustomArray[7] . "'") or die(mysqli_error($conn));


//        $sql = "Update " . TABLEPREFIX . "users set nAccount = nAccount +  " . $CustomArray[5] . "  where  nUserId='" . $CustomArray[0] . "'";
//        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        //checking alredy exits
        $chkPoint = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "WHERE nUserId='" . $CustomArray[0] . "'"), 'nPoints');
        if (trim($chkPoint) != '') {
            //update points to user credit
            mysqli_query($conn, "UPDATE " . TABLEPREFIX . "usercredits set nPoints=nPoints+" . $CustomArray[6] . " WHERE
												nUserId='" . $CustomArray[0] . "'") or die(mysqli_error($conn));
        }//end if
        else {
            //add points to user credit
            mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "usercredits (nPoints,nUserId) VALUES ('" . $CustomArray[6] . "','" . $CustomArray[0] . "')") or die(mysqli_error($conn));
        }//end else
        //add into user table
        mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "successtransactionpayments (nUserId,nAmount,nProdId,vTxnId,vMethod,dDate,vStatus,nSId) VALUES
								('" . $CustomArray[0] . "','" . $CustomArray[1] . "','" . $CustomArray[2] . "','" . $txnid . "',
								'pp',now(),'A','" . $CustomArray[7] . "')") or die(mysqli_error($conn));



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
               AND C.content_name = 'SuccessFeeMailToUser'
               AND C.content_type = 'email'
               AND L.lang_id = '".$_SESSION["lang_id"]."'";

            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];
            
            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}","{purchase_details}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,"Pay Pal",date('m/d/Y'),$_SESSION["sess_PointAmount"],$_SESSION["guserFName"],$passPoints);
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

            $StyleContent = MailStyle($sitestyle, SITE_URL);
        $EMail = $CustomArray[4];

        //readf file n replace
        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
        $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
        $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
        send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');


        //mail sent to admin
        $var_admin_email = SITE_NAME;

        if (DisplayLookUp('4') != '') {
            $var_admin_email = DisplayLookUp('4');
        }//end if

        /*
        * Fetch email contents from content table
        */
        $mailSql = "SELECT L.content,L.content_title
          FROM ".TABLEPREFIX."content C
          JOIN ".TABLEPREFIX."content_lang L
            ON C.content_id = L.content_id
           AND C.content_name = 'SuccessFeeMailToAdmin'
           AND C.content_type = 'email'
           AND L.lang_id = '".$_SESSION["lang_id"]."'";

        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $mainTextShow   = $mailRw['content'];

        $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
        $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,"Pay Pal",date('m/d/Y'),$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

        $mailcontent1   = $mainTextShow;

        $subject    = $mailRw['content_title'];
        $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

        $StyleContent = MailStyle($sitestyle, SITE_URL);
        $EMail = $var_admin_email;


        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
        $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
        $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
        send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

        $flag = true;

        $message = MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU;
        //$message .="<br>&nbsp;<br>You may visit the \"".POINT_NAME."\" to view details of this transaction.";
        //clear sessions
        $CustomArray[2] = "";
        $CustomArray[1] = "";
        $_SESSION['sess_success_fee_id'] = '';
    }//end else
}//end if
?>