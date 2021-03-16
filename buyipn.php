<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>                          |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+

/*$fiename  = time().".txt";
$myfile = fopen("ipn/".$fiename, "w") or die("Unable to open file!");
$txt = "Paypal ipncheck";
fwrite($myfile, $txt);*/
        
include "includes/config.php";
include "includes/functions.php";

$txtPaypalEmail = DisplayLookUp('paypalemail');
$txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
$txtPaypalSandbox = DisplayLookUp('paypalmode');

if ($txtPaypalSandbox == "TEST") {
    $paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
}//end if
else {
    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
}//end else

    //fwrite($fh,$paypalurl);
$cc_flag = false;
define("DEBUG", 0);
// Set to 0 once you're ready to go live
define("LOG_FILE", "ipn.log");
// read the post from PayPal system and add 'cmd'
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
    $keyval = explode ('=', $keyval);
    if (count($keyval) == 2)
        $myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
    $get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
    if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
        $value = urlencode(stripslashes($value));
    } else {
        $value = urlencode($value);
    }
    $req .= "&$key=$value";
}
// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data
$ch = curl_init($paypalurl);
if ($ch == FALSE) {
    return FALSE;
}
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSLVERSION, 6);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
if(DEBUG == true) {
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
}
// CONFIG: Optional proxy configuration
//curl_setopt($ch, CURLOPT_PROXY, $proxy);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
// Set TCP timeout to 30 seconds
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
// of the certificate as shown below. Ensure the file is readable by the webserver.
// This is mandatory for some environments.
//$cert = __DIR__ . "./cacert.pem";
//curl_setopt($ch, CURLOPT_CAINFO, $cert);
$res = curl_exec($ch);
if (curl_errno($ch) != 0) // cURL error
    {
    if(DEBUG == true) { 
        error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
    }
    curl_close($ch);
    exit;
} else {
        // Log the entire HTTP response if debug is switched on.
        if(DEBUG == true) {
            error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
            error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
        }
        curl_close($ch);
}

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

$tokens = explode("\r\n\r\n", trim($res));
$res = trim(end($tokens));
if (strcmp ($res, "VERIFIED") == 0) {
    extract($_REQUEST);
    extract($_POST);
    $userid = $option_selection1;
    $now = $option_selection2;
    $txnid = $txn_id;
    $saleid = $item_number;
    $cost = $payment_gross;
    $custom = $custom;
    $customArray = explode("-", $custom);
    $_SESSION['sess_PointSelected'] = $customArray[2];

    $cc_flag = true;
         for ($i = 1; $i < count($lines); $i++) {
                list($key, $val) = explode("=", $lines[$i]);
                $keyarray[urldecode($key)] = urldecode($val);
                //echo("key : " . urldecode($key) . "   value : " . urldecode($val) . "<br>");
            }//end for loop
}
 else if (strcmp($res, "INVALID") == 0) {
    // log for manual investigation
}//end else if
    if ($cc_flag == true) {
        $sql = "Select vSaleStatus from " . TABLEPREFIX . "saledetails where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . addslashes($userid);
        $sql .= "' AND dDate='" . addslashes($now) . "' AND  vSaleStatus IN('2','3')";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) <= 0) {
            $sql = "Select vTxnId from " . TABLEPREFIX . "saledetails where vTxnId='" . addslashes($txnid) . "' AND vMethod='pp'";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) <= 0) {
                
            if (DisplayLookUp('Enable Escrow') == 'Yes') {
                $SaleStatus = '2';
            }//end if
            else {
                $SaleStatus = '3';
            }//end esle
                //update the database when this is okay
                $sql = "Update " . TABLEPREFIX . "saledetails set vSaleStatus='".$SaleStatus."',vTxnId='$txnid',dTxnDate=now() where ";
                $sql .= " nSaleId='" . addslashes($saleid) . "' AND nUserId='" . addslashes($userid) . "' AND dDate='";
                $sql .= addslashes($now) . "' ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                // quantity updating section
                $sql_qty = "Select nQuantity from " . TABLEPREFIX . "saledetails where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . addslashes($userid);
                $sql_qty .= "' AND dDate='" . addslashes($now) . "' AND  vSaleStatus IN('2','3')";
                $result = mysqli_query($conn, $sql_qty) or die(mysqli_error($conn));
                if (mysqli_num_rows($result) > 0) {
                    $row_det = mysqli_fetch_array($result);
                    
                    $quantityREQD = $row_det['nQuantity'];
                    if($quantityREQD!='')
                    {
                    //reduce requested quantity from the master table
                        $sql = "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity - $quantityREQD where nSaleId ='" . addslashes($saleid) . "'";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }
                }
                // quantity updating section ends



                $sql = "Select nUserId from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($saleid) . "'";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
//                if (mysqli_num_rows($result) > 0) {
//                    if ($row = mysqli_fetch_array($result)) {
//                        $sql = "Update " . TABLEPREFIX . "users set nAccount = nAccount +  $cost  where  nUserId='" . $row["nUserId"] . "'";
//                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
//                    }//end if
//                }
                //end of update
            }//end if



$sql = "insert into " . TABLEPREFIX . "tempdata(nId,vValue,vData)  values('','" . addslashes($saleid) . "|" . addslashes($userid) . "|" .
        addslashes($now) . "','" . addslashes($txnid) . "');";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

 

  $sql = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
            $sql .= " on sd.nSaleId = s.nSaleId ";
            $sql .= " where  sd.nSaleId='" . addslashes($saleid) . "' AND sd.nUserId='" . $userid. "' AND sd.dDate='";
            $sql .= addslashes($now) . "' AND sd.vSaleStatus='" . $SaleStatus . "' AND sd.vRejected='0' ";
     
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                if ($row = mysqli_fetch_array($result)) {
                    $var_title = $row["vTitle"];
                    $var_quantity = $row["nQuantity"];
                    $var_amount = $row["nAmount"];
                    $var_date = $now;
                    $flag = true;


                    //send mail to seller
                    $subject = "One of your products listed at " . SITE_NAME . " has been sold.";
                    //fetching seller information
                    $condition = "where nSaleId='" . $saleid . "'";
                    $sellerUserId = fetchSingleValue(select_rows(TABLEPREFIX . 'sale', 'nUserId', $condition), 'nUserId');

                    $condition = "where nUserId='" . $sellerUserId . "'";
                    $SellerName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
                    $EMail = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vEmail', $condition), 'vEmail');

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

                if(!$txnid || $txnid==''){
                   $mainTextShow = str_replace("{txnid}", "", $mainTextShow);
                   $mainTextShow = str_replace("Transaction Id", "", $mainTextShow);
                }

                $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{var_quantity}","{txnid}","{guserFName}","{Account Summary}");
                $arrTReplace    = array(SITE_NAME,SITE_URL,$var_title,CURRENCY_CODE.$var_amount,$var_quantity,$txnid,$_SESSION["gloginname"],"'Account Summary'");
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
                $mainTextShow   = $mailRw['content'];
                if(!$txnid || $txnid==''){
                   $mainTextShow = str_replace("{txnid}", "", $mainTextShow);
                   $mainTextShow = str_replace("Transaction Id", "", $mainTextShow);
                }

                $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{var_quantity}","{txnid}","{guserFName}","{Account Summary}");
                $arrTReplace    = array(SITE_NAME,SITE_URL,$var_title,CURRENCY_CODE.$var_amount,$var_quantity,$txnid,$_SESSION["gloginname"],"'Account Summary'");
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

                $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{sellerName}","{buyerName}");
                $arrTReplace    = array(SITE_NAME,SITE_URL,$var_title,CURRENCY_CODE.$var_amount,$SellerName,$_SESSION["gloginname"]);
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
                
             

                    $var_message = stripslashes(MESSAGE_THANKYOU_FOR_PAYMENT_RECEIPT_EMAILED);
                    $_SESSION['sess_buyerid_escrow'] = '';
                }//end if
            }//end if

}

    }

 ?>