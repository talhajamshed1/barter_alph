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

$txtPaypalEmail = DisplayLookUp('paypalemail');
$txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
$txtPaypalSandbox = DisplayLookUp('paypalmode');

$var_message = "";

$txnid              =   $_REQUEST['tx'];
$purchase_amount    =   $_REQUEST['amt'];  
$item_number        =   $_REQUEST['item_number'];
$saleid             =   $item_number;
$custom             =   $_REQUEST['cm'];

if($txnid!='' && $purchase_amount!='' ) {
    $flag_to_continue = true;
}
      
if ($flag_to_continue==true){
    //extract($_REQUEST);
    //extract($_POST);
    
    //$userid = $option_selection1;
    /*$now = $option_selection2;
    $txnid = $txn_id;
    //$saleid = $item_number;*/
    $cost = $purchase_amount;
    //$custom = $custom;
    $customArray = @explode("@", $custom);
    $_SESSION['sess_PointSelected'] = $customArray[2];
    $userid     =   $_SESSION["guserid"];
    $now        =   $customArray[1];
   
    
    $sql = "Select *  from " . TABLEPREFIX . "saledetails where vTxnId='".addslashes($txnid)."'  AND vMethod='pp' AND nUserId='" . addslashes($userid)."' ";
     if (mysql_num_rows($result) <= 0) {
            if (DisplayLookUp('Enable Escrow') == 'Yes') {
                $SaleStatus = '2';
            }//end if
            else {
                $SaleStatus = '4';
            }//end esle
                //update the database when this is okay
        $sql = "Update " . TABLEPREFIX . "saledetails set vSaleStatus='".$SaleStatus."',vTxnId='$txnid',dTxnDate=now() where ";
        $sql .= " nSaleId='" . addslashes($saleid) . "' AND nUserId='" . addslashes($userid) . "' ORDER BY dDate DESC LIMIT 1 ";
        
        mysql_query($sql) or die(mysql_error());

				// quantity updating section
        $sql_qty = "Select dDate, nQuantity from " . TABLEPREFIX . "saledetails where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . addslashes($userid);
        $sql_qty .= "'  AND  vSaleStatus IN('2','3','4')";
        $sql_qty .= " ORDER BY dDate DESC LIMIT 1";
        
        $result = mysql_query($sql_qty) or die(mysql_error());
        if (mysql_num_rows($result) > 0) {
                $row_det = mysql_fetch_array($result);
                $quantityREQD   =     $row_det['nQuantity'];
               // $now            =     $row_det['dDate'];
                if($quantityREQD!='')
                {
                //reduce requested quantity from the master table
                        $sql = "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity - $quantityREQD where nSaleId ='" . addslashes($saleid) . "'";
                        mysql_query($sql) or die(mysql_error());
                }
        }
				// quantity updating section ends



    $sql = "Select nUserId from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($saleid) . "'";
    $result = mysql_query($sql) or die(mysql_error());
//    if (mysql_num_rows($result) > 0) {
//        if ($row = mysql_fetch_array($result)) {
//            $sql = "Update " . TABLEPREFIX . "users set nAccount = nAccount +  $cost  where  nUserId='" . $row["nUserId"] . "'";
//            mysql_query($sql) or die(mysql_error());
//        }//end if
//    }
                //end of update


$sql = "insert into " . TABLEPREFIX . "tempdata(nId,vValue,vData)  values('','" . addslashes($saleid) . "|" . addslashes($userid) . "|" .
        addslashes($now) . "','" . addslashes($txnid) . "');";
mysql_query($sql) or die(mysql_error());

 

            $sql = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
            $sql .= " on sd.nSaleId = s.nSaleId ";
            $sql .= " where  sd.nSaleId='" . addslashes($saleid) . "' AND sd.nUserId='" . $userid. "' AND sd.dDate='";
            $sql .= addslashes($now) . "' AND sd.vSaleStatus='" . $SaleStatus . "' AND sd.vRejected='0' ";
	 
            $result = mysql_query($sql) or die(mysql_error());
            if (mysql_num_rows($result) > 0) {
                if ($row = mysql_fetch_array($result)) {
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
                $langRs = mysql_query($lanSql) or die(mysql_error());
                $langRw = mysql_fetch_array($langRs);

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
                $mailRs  = mysql_query($mailSql) or die(mysql_error());
                $mailRw  = mysql_fetch_array($mailRs);

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
                $mailRs  = mysql_query($mailSql) or die(mysql_error());
                $mailRw  = mysql_fetch_array($mailRs);

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
                $mailRs  = mysql_query($mailSql) or die(mysql_error());
                $mailRw  = mysql_fetch_array($mailRs);


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
                
             

                    $var_message = stripslashes(MESSAGE_THANKYOU_FOR_PAYMENT_RECEIPT_EMAILED);
                    $_SESSION['sess_buyerid_escrow'] = '';
                }
            }
    
     }
     
   
if (DisplayLookUp('Enable Escrow') == 'Yes') {
    $SaleStatus = '2';
}
else {
    $SaleStatus = '4';
}

//	$sql_sel = "select dDate FROM ".TABLEPREFIX."saledetails WHERE vTxnId ='".$tx."'";
//	$res_sel = mysql_query($sql_sel) or die(mysql_error());
//    if (mysql_num_rows($res_sel) > 0) {
//        //populate data here
//        if ($row_sel = mysql_fetch_array($res_sel)) {
//			$now = $row_sel["dDate"];
//		}
        
    //check if txnid alredy there to prevent refresh
    $sql2 = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
    $sql2 .= " on sd.nSaleId = s.nSaleId ";
    $sql2 .= " where sd.nSaleId='" . addslashes($saleid) . "' AND sd.nUserId='" . $_SESSION["guserid"];
    $sql2 .= "' AND sd.vSaleStatus IN('" . $SaleStatus . "','3')";//sd.dDate='" . addslashes($now) . "' AND

   // echo $sql2;
    $result2 = mysql_query($sql2) or die(mysql_error());
    if (mysql_num_rows($result2) > 0) {
        //populate data here
        if ($row2 = mysql_fetch_array($result2)) {
            $var_title = $row2["vTitle"];
            $var_quantity = $row2["nQuantity"];
            $var_amount = number_format($row2["nAmount"], 2, '.', '');
            $var_date = $row2["dDate"];
            //date("F d, Y",  strtotime($row2["dDate"]));
            $flag = true;
            $var_message = stripslashes(MESSAGE_THANKYOU_FOR_PAYMENT_RECEIPT_EMAILED);
            $_SESSION['sess_buyerid_escrow'] = '';
        }//end if
        //end population
    }//end else
//}//end if
}
else {
        $var_message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
}//end else

include_once('./includes/purchase_information.php');
?>