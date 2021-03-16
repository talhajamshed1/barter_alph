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
if (DisplayLookUp('userapproval') != '') {
    $approval_tag = DisplayLookUp('userapproval');
}//end if

$flag_to_continue = false;

$transstatus = $_REQUEST['transStatus'];

if (isset($_REQUEST['transStatus']) && $_REQUEST['transStatus'] == 'Y') {
    $flag_to_continue = true;
}//end if

$var_id = "";
$var_new_id = "";
$message = "";
$flag = false;

if ($flag_to_continue == true) {
    if (DisplayLookUp('worldpaydemo') == "YES") {
        $txnid = 'TEST-' . time();
    }//end if
    else {
        $txnid = time();
    }//end if

    $var_id = $_SESSION["gtempid"];
    $var_amount = "";
    $var_txnid = "";
    $var_method = "";
    $var_login_name = "";
    $var_password = "";
    $var_first_name = "";
    $var_last_name = "";
    $var_date = "";
    $var_txnid = $txnid;

    $sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
    $sql .="vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
    $sql .="vDescription  ,dDateReg   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee,nRefId
					from " . TABLEPREFIX . "users where nUserId='" . $var_id . "'";

    $result = @mysqli_query($conn, $sql) or die(mysqli_error($conn));

    if (@mysqli_num_rows($result) > 0) {       //If data is there in the temp table
        if ($row = @mysqli_fetch_array($result)) {
            $sqltxn = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid' AND vTxn_mode='wp'";

            $resulttxn = @mysqli_query($conn, $sqltxn) or die(mysqli_error($conn));
            if (@mysqli_num_rows($resulttxn) <= 0) {  // the tran id not present in the database
                $var_login_name = $row["vLoginName"];
                $var_password = $row["vPassword"];
                $var_first_name = $row["vFirstName"];
                $var_last_name = $row["vLastName"];
                $var_email = $row["vEmail"];
                $totalamt = $row["nAmount"];
                $paytype = $row["vMethod"];
                $now = $var_date = date('m/d/Y');
                $userUpdate = '';
                $payTableField = '';
                $payTableFieldValue = '';

                if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
                    //calculate end date
                    switch ($_SESSION['sess_Plan_Mode']) {
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
                    $payTableField = ',vPlanStatus,nPlanId';
                    $payTableFieldValue = ",'A','" . $_SESSION['nPlanId'] . "'";
                    $totalamt = $_SESSION['sess_Plan_Amt'];
                }//end if register mode and escrow checking

                if ($approval_tag == "1") {
                    $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "' " . $userUpdate . "
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
//                $var_new_id = @mysqli_insert_id($conn);
                  $var_new_id = $row['nUserId'];

                //Addition for referrals
                $var_reg_amount = 0;

                if ($row["nRefId"] != "0") {
                    $sql = "Select nRefId,nUserId,nRegAmount from " . TABLEPREFIX . "referrals where vRegStatus='0' AND nRefId='" . $row["nRefId"] . "'";
                    $result_test = @mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    if (@mysqli_num_rows($result_test) > 0) {
                        if ($row_final = @mysqli_fetch_array($result_test)) {
                            $var_reg_amount = $row_final["nRegAmount"];

                            $sql = "Update " . TABLEPREFIX . "referrals set vRegStatus='1',";
                            $sql .= "nUserRegId='" . $var_new_id . "',dRegDate=now() where nRefId='" . $row_final["nRefId"] . "'";

                            @mysqli_query($conn, $sql) or die(mysqli_error($conn));

                            $sql = "Select nUserId from " . TABLEPREFIX . "user_referral where nUserId='" . $row_final["nUserId"] . "'";
                            $result_ur = @mysqli_query($conn, $sql) or die(mysqli_error($conn));
                            if (mysqli_num_rows($result_ur) > 0) {
                                $sql = "Update " . TABLEPREFIX . "user_referral set nRegCount = nRegCount + 1,nRegAmount=nRegAmount + $var_reg_amount where nUserId='" . $row_final["nUserId"] . "'";
                            }//end if
                            else {
                                $sql = "insert into " . TABLEPREFIX . "user_referral(nUserId,nRegCount,nRegAmount) values('"
                                        . $row_final["nUserId"] . "','1','$var_reg_amount')";
                            }//end else
                            @mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        }//end if
                    }//end if
                }//end if
                //end of referrals

                $_SESSION["gtempid"] = "";

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

                $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
                $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($row["vLoginName"]),$row["vPassword"],$activate_link );
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent1   = $mainTextShow;

                $subject    = $mailRw['content_title'];
                $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

                $StyleContent=MailStyle($sitestyle,SITE_URL);

                $EMail = $var_email;

                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
                send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

                $_SESSION["guserid"] = $var_new_id;
                /* get the invoice number */
                $sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
                $result1 = @mysqli_query($conn, $sql1) or die(mysqli_error($conn));
                $row1 = @mysqli_fetch_array($result1);
                $Inv_id = $row1['maxinvid'];

                $_SESSION["guserid"] = ($_SESSION["guserid"] != '') ? $_SESSION["guserid"] : $_SESSION["gtempid"];

                $sqlPayment = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId,
							nSaleId,vInvno " . $payTableField . ") VALUES ('R', '$txnid', ' $totalamt', '$paytype',now(), '" . $_SESSION["guserid"] . "',
							'','$Inv_id' " . $payTableFieldValue . ")";
                $result = @mysqli_query($conn, $sqlPayment) or die(mysqli_error($conn));

                $var_admin_email = SITE_NAME;

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

                //now send the mail containing the link to get the pin
                $_SESSION["guserid"] = "";
                $_SESSION['nPlanId'] = '';
                $_SESSION['sess_Plan_Mode'] = '';
                $_SESSION['sess_Plan_Amt'] = '';

                $flag = true;

                if ($approval_tag == "1") {
                    $message = str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT_AFTER_ADMIN_APPROVAL);
                }//end if
                if ($approval_tag == "E") {
                    $message = str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT_AFTER_EMAIL_VERIFICATION);
                }//end if
                if ($approval_tag == "0") {
                    $message = MESSAGE_ACCESS_ACCOUNT_NOW . "<br>&nbsp;<br><a href='login.php'>".LINK_CLICK_LOGIN."</a>";
                }//end if
            }//end if
        }//end if
    }//end if
    else {  //If the data is not present in the temperory table
        $var_id = $_SESSION["gtempid"];
        $uname = $keyarray['option_selection2'];
        $txnid = $keyarray['txn_id'];
        $var_txnid = $txnid;


        $sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
        $sql .="vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
        $sql .="vDescription  ,dDateReg   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee from " . TABLEPREFIX . "users where vLoginName='" . addslashes($uname) . "'";

        $result = @mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (@mysqli_num_rows($result) > 0) {
            if ($row = @mysqli_fetch_array($result)) {
                $var_login_name = $row["vLoginName"];
                $var_password = $row["vPassword"];
                $var_first_name = $row["vFirstName"];
                $var_last_name = $row["vLastName"];
                $var_email = $row["vEmail"];
                $totalamt = $row["nAmount"];
                $paytype = $row["vMethod"];
                $now = $var_date = date('m-d-Y');

                $_SESSION["gtempid"] = "";

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

                $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
                $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($row["vLoginName"]),$row["vPassword"],$activate_link );
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent1   = $mainTextShow;

                $subject    = $mailRw['content_title'];
                $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

                $StyleContent=MailStyle($sitestyle,SITE_URL);
                $EMail = $var_email;

                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

                $var_admin_email = ADMIN_EMAIL;

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


                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

                $flag = true;
                $message = MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU;
                $message .="<br>&nbsp;<br>"."<br>&nbsp;<br> ".str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT);
                if ($approval_tag == "1") {
                    $message .= "<br><br>".MESSAGE_LOGIN_ACCOUNT_AFTER_ADMIN_APPROVAL;
                }//end if
                if ($approval_tag == "E") {
                    $message .= "<br><br>".MESSAGE_LOGIN_ACCOUNT_AFTER_EMAIL_VERIFICATION;
                }//end if
            }//end if
        }//end if
        else {
            $message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
        }//end else
    }//end if
}//end if

include_once('./includes/registration_information.php');
?>