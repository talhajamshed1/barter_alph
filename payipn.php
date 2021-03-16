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
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file


$approval_tag = "0";

if (DisplayLookUp('userapproval') != '') {
    $approval_tag = DisplayLookUp('userapproval');
}//end if

$txtPaypalEmail = DisplayLookUp('paypalemail');
$txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
$txtPaypalSandbox = DisplayLookUp('paypalmode');

if ($txtPaypalSandbox == "TEST") {
    $paypalurl = "ssl://www.sandbox.paypal.com";
}//end if
else {
    $paypalurl = "ssl://www.paypal.com";
}//end else

















$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ($paypalurl, 443, $errno, $errstr, 30);

$myFile = "testFile.txt";
	$fh = fopen($myFile, 'a') or die("can't open file");
 
	fclose($fh);

if (!$fp) {
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
					fwrite($fh, urldecode($val));

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

if (trim($keyarray['txn_id']) != '' && $keyarray['txn_type'] == "web_accept" || trim($keyarray['subscr_id']) != '') {
    $var_id = $keyarray['option_selection1'];
    $in_login_name = $keyarray['option_selection2'];
    if ($keyarray['txn_id'] != '') {
        $txnid = $keyarray['txn_id'];
    }//end if
    else {
        $txnid = $keyarray['subscr_id'];
    }//end else
    $var_txnid = $txnid;

    if ($cc_flag == true) {
        //TRANSACTION START
        $sql = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid' AND vTxn_mode='pp'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) <= 0) {
            $sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
            $sql .="vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
            $sql .="vDescription  ,dDateReg   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee,nRefId
		    from " . TABLEPREFIX . "users where nUserId='" . $var_id . "' and vDelStatus <> '0'";

            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

            if (mysqli_num_rows($result) > 0) {
                if ($row = mysqli_fetch_array($result)) {
                    //if you have data for the transaction
                    $var_login_name = $row["vLoginName"];
                    $var_password = $row["vPassword"];
                    $var_first_name = $row["vFirstName"];
                    $var_last_name = $row["vLastName"];
                    $var_email = $row["vEmail"];
                    $totalamt = $row["nAmount"];
                    $paytype = $row["vMethod"];
                    $var_date = date('m-d-Y');
                    $userUpdate = '';
                    $payTableField = '';
                    $payTableFieldValue = '';

                    //if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
                    if (DisplayLookUp('15') != '1' && DisplayLookUp('plan_system')=='yes') {
                        //calculate end date
                        switch ($CustomArray[0]) {
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

                        $userUpdate = ",dPlanExpDate='" . $nExpDate . "'";

                        //add one field in payment table
                        $payTableField = ',vPlanStatus';
                        $payTableFieldValue = ",'A'";
                        $totalamt = $CustomArray[1];
                    }//end if register mode and escrow checking

                    if ($approval_tag == "1") {
                        $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',vDelStatus='0' " . $userUpdate . "
										 WHERE nUserId='" . $row['nUserId'] . "'";
                    }//end if
                    if ($approval_tag == "E") {
                        $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',
										 vStatus='4',vDelStatus='0' " . $userUpdate . " WHERE nUserId='" . $row['nUserId'] . "'";
                    }//end if
                    else {
                        $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',
										 vStatus='0',vDelStatus='0' " . $userUpdate . " WHERE nUserId='" . $row['nUserId'] . "'";
                    }//end else

                    @mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    $var_new_id = @mysqli_insert_id($conn);


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
                                    $sql = "insert into " . TABLEPREFIX . "user_referral(nUserId,nRegCount,nRegAmount) values('"
                                            . $row_final["nUserId"] . "','1','$var_reg_amount')";
                                }//end else
                                mysqli_query($conn, $sql) or die(mysqli_error($conn));
                            }//end 3rd if
                        }//end 2nd if
                    }//end first if
                    //end of referrals

                    /*
                    * Fetch user language details
                    */

                    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
                    $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
                    $langRw = mysqli_fetch_array($langRs);

                    /*
                    * Fetch email contents from content table
                    */
                    if ($approval_tag == "E") {
                        $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'activationLinkOnRegister'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$_SESSION["lang_id"]."'";
                    }else{
                        $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'welcomeMailUser'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$_SESSION["lang_id"]."'";
                    }
                    $activate_link = '<a href="' . SITE_URL . '/activation.php?uid=' . $var_new_id . '&status=eactivate">Activate</a>';
                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                    $mailRw  = mysqli_fetch_array($mailRs);

                    $mainTextShow   = $mailRw['content'];

                    $mainTextShow = str_replace("{Password}", '******', $mainTextShow);
                    //$mainTextShow = str_replace("Password", '', $mainTextShow);

                    $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
                    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($row["vLoginName"]),'******',$activate_link );
                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                    $mailcontent1   = $mainTextShow;

                    $subject    = $mailRw['content_title'];
                    $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

                    $StyleContent   =  MailStyle($sitestyle,SITE_URL);
                    $EMail = $var_email;

                    //readf file n replace
                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                    $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
                    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');


                    /* get the invoice number */
                    $sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
                    $result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
                    $row1 = mysqli_fetch_array($result1);
                    $Inv_id = $row1['maxinvid'];
                    /*                     * *********************** */

                    $var_new_id = ($var_new_id != '') ? $var_new_id : $CustomArray[2];

                    $sqlPayment = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date,
								nUserId, nSaleId,vInvno " . $payTableField . ") VALUES ('R', '$txnid', ' $totalamt', '$paytype',now(),
								'" . $var_new_id . "', '','$Inv_id' " . $payTableFieldValue . ")";
                    $result = mysqli_query($conn, $sqlPayment) or die(mysqli_error($conn));
                    $CustomArray[3] = '';
                    $CustomArray[0] = '';
                    $CustomArray[1] = '';
                    //  $message="Your payment process is over.To view the details go to account summary.";

                    if (DisplayLookUp('4') != '') {
                        $var_admin_email = DisplayLookUp('4');
                    }//end if

                    /*
                    * Fetch email contents from content table
                    */
                    $mailRw = array();
                        $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'registrationNotificationAdmin'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$_SESSION["lang_id"]."'";

                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                    $mailRw  = mysqli_fetch_array($mailRs);

                    $mainTextShow   = $mailRw['content'];

                    $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
                    $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($var_login_name),htmlentities($var_first_name),$var_email );
                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                    $mailcontent1   = $mainTextShow;

                    $subject    = $mailRw['content_title'];
                    $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
                    $StyleContent=MailStyle($sitestyle,SITE_URL);

                    $EMail = $var_admin_email;


                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                    $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
                    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
                //end of the section
                }//end if
            }//end if
        }//end if
        //TRANSACTION END
    }//end if
}//end if
?>