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
//include common files
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
$header .= "POST https://".$paypalurl."/cgi-bin/webscr HTTP/1.1\r\n"; 
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Host: ".$paypalurl."\r\n";
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

if ($keyarray['payment_status'] == "Completed") {

    //reset flag intended mail is different
    if ($keyarray['receiver_email'] != $txtPaypalEmail && trim($txtPaypalAuthtoken)!='') {
        $cc_flag = false;
    }

    $saleid = $keyarray['item_number'];
    $saleitem = $keyarray['item_name'];
    $userid = $keyarray['option_selection1'];
    $txnid = $keyarray['txn_id'];
    $paymentgross = $keyarray['payment_gross'];
    $paytype = "pp";



    if ($cc_flag == true) {

        //check if updation is already done by pdt
        $sql = "Select * from " . TABLEPREFIX . "saleextra where nSaleextraId='$saleid'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) > 0) {

//==================================AFTER PDT CHECK==========================================
            //check if txnid alredy there to prevent refresh
            $sql = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid' AND vTxn_mode='pp'";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) == 0) {


                //get data from temp table
                $sql = "Select * from " . TABLEPREFIX . "saleextra where nSaleextraId='$saleid'";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                if (mysqli_num_rows($result) > 0) {

                    if ($row = mysqli_fetch_array($result)) {
                        $action = "";

                        if ($row["nFeatured"] > 0) {

                            $vfeatured = "Y";
                            $action.="F";
                        } else {

                            $vfeatured = "N";
                        }

                        if ($row["nCommission"] > 0) {

                            $action.="C";
                        }

                        $totalamt = $row["nCommission"] + $row["nFeatured"];


                        //insert data to sales table
                        $txtCategory = $row["nCategoryId"];
                        $txtTitle = $row["vTitle"];
                        $txtBrand = $row["vBrand"];
                        $ddlType = $row["vType"];
                        $txtYear = $row["vYear"];
                        $ddlCondition = $row["vCondition"];
                        $txtValue = $row["nValue"];
                        $txtPoint = $row["nPoint"];
                        $txtPostDate = $row["dPostDate"];


                        $sql = "INSERT INTO " . TABLEPREFIX . "sale (nSaleId, nCategoryId, nUserId,";
                        $sql .= "vTitle, vBrand, vType, vCondition, vYear, nValue,";
                        $sql .= "nShipping, vUrl, vDescription, dPostDate, nQuantity,vFeatured,vDelStatus,vSmlImg,vImgDes,nPoint)";
                        $sql .= "VALUES ('', '" . $row["nCategoryId"] . "', '";
                        $sql .= $row["nUserId"];
                        $sql .= "', '" . $row["vTitle"] . "',";
                        $sql .= "'" . $row["vBrand"] . "', '" . $row["vType"] . "', '" . $row["vCondition"] . "', '" . $row["vYear"] . "', '" . $row["nValue"] . "',";
                        $sql .= "'" . $row["nShipping"] . "', '" . $row["vUrl"] . "', '" . $row["vDescription"] . "','";
                        $sql .= $row["dPostDate"] . "'";
                        $sql .= ", '" . $row["nQuantity"] . "','$vfeatured','0','" . $row["vSmlImg"] . "','" . addslashes(stripslashes($row["vImgDes"])) . "','".$row["nPoint"]."')";
                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }
                }




                //get last posted sale id
                $sql = "Select nSaleId from " . TABLEPREFIX . "sale where dPostDate='$txtPostDate' AND nUserId = '" . $userid . "'";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if (mysqli_num_rows($result) > 0) {

                    if ($row2 = mysqli_fetch_array($result)) {
                        $saleid1 = $row2["nSaleId"];
                    }
                }


                //insert to payemnt table
                /* get the invoice number */
                $sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
                $result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
                $row1 = mysqli_fetch_array($result1);
                $Inv_id = $row1['maxinvid'];
                /*                 * *********************** */

                $sql = "INSERT INTO " . TABLEPREFIX . "payment (nTxn_no, vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId,vInvno) VALUES ('', '$action', '$txnid', ' $totalamt', '$paytype', '$txtPostDate', '', '$saleid1','$Inv_id')";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));




                //delete from temp table
                $sql = "delete from " . TABLEPREFIX . "saleextra where nSaleextraId='$saleid'";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $_SESSION["gsaleextraid"] = "";


                //update category listings
                $routesql = "Select vRoute from " . TABLEPREFIX . "category where nCategoryId ='$txtCategory'";
                //echo $routesql;
                $result = mysqli_query($conn, $routesql) or die(mysqli_error($conn));
                $row3 = mysqli_fetch_array($result);
                $route = $row3["vRoute"];
                $countsql = "UPDATE " . TABLEPREFIX . "category SET nCount=nCount+1 WHERE nCategoryId in($route)";
                $result = mysqli_query($conn, $countsql) or die(mysqli_error($conn));

///////////////
                $categorysql = "Select L.vCategoryDesc from " . TABLEPREFIX . "category C
                                    LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                where C.nCategoryId ='$txtCategory'";
                $resultcategory = mysqli_query($conn, $categorysql) or die(mysqli_error($conn));
                $row4 = mysqli_fetch_array($resultcategory);
                $txtCategoryname = $row4["vCategoryDesc"];
///////////////

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
                           AND C.content_name = 'featured'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$_SESSION["lang_id"]."'";
                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                $mailRw  = mysqli_fetch_array($mailRs);


                $enbPnt = DisplayLookUp("EnablePoint");

                if($enbPnt=="2"){
                    if(!$row["nValue"] && !$row["nPoint"]){
                        $mainTextShow   = str_replace("{pricename}", "", $mainTextShow);
                        $mainTextShow   = str_replace("{txtValue}", "", $mainTextShow);
                    }
                    $mainTextShow   = str_replace("{pricename}",POINT_NAME.'/'.TEXT_PRICE, $mainTextShow);
                    $itemValue  =   $txtPoint."/".CURRENCY_CODE.$txtValue;
                }else if($enbPnt=="1"){
                    if(!$row["nPoint"]){
                        $mainTextShow   = str_replace("{pricename}", "", $mainTextShow);
                        $mainTextShow   = str_replace("{txtValue}", "", $mainTextShow);
                    }
                    $mainTextShow   = str_replace("{pricename}",POINT_NAME, $mainTextShow);
                    $itemValue  =   $row["nPoint"];

                }else{
                    if(!$row["nValue"]){
                        $mainTextShow   = str_replace("{pricename}", "", $mainTextShow);
                        $mainTextShow   = str_replace("{txtValue}", "", $mainTextShow);
                    }
                    $mainTextShow   = str_replace("{pricename}",TEXT_PRICE, $mainTextShow);
                    $itemValue  =   $row["nValue"];
                    if($itemValue)
                       $itemValue  =   CURRENCY_CODE.$itemValue;
                }

                $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{TYPE}","{txtCategoryname}","{txtTitle}","{txtBrand}","{ddlType}","{ddlCondition}","{txtYear}","{txtValue}");
                $arrTReplace	= array(SITE_NAME,SITE_URL,"sale",$txtCategoryname,$txtTitle,$txtBrand,$ddlType,$ddlCondition,$txtYear,$itemValue);
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent1   = $mainTextShow;

                $subject        = $mailRw['content_title'];

                $StyleContent   = MailStyle($sitestyle,SITE_URL);

                $sql = "Select vFirstName,vLoginName,vEmail from " . TABLEPREFIX . "users where (vAlertStatus='Y' OR nUserId = '" . $userid . "') and vDelStatus = '0' and vStatus = '0'";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $EMail = stripslashes($row["vEmail"]);

                        //readf file n replace
                        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, htmlentities($row["vLoginName"]), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                        $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                        $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                        send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
                    }//end while
                }//end if
            }

            $message = "<table width=80% border=0>
<tr><td width=50% class=gray align=left>Title</td>
<td width=50% class=maintext  align=left>$saleitem</td></tr>
<tr><td width=50% class=gray align=left>Post Date</td>
<td width=50% class=maintext align=left>" . date("m-d-Y, Y, g i s") . "</td></tr>
<tr><td width=50% class=gray align=left>Payment Mode</td>
<td width=50% class=maintext  align=left>PayPal</td></tr>
<tr><td width=50% class=gray align=left>Amount</td>
<td width=50% class=maintext  align=left>$paymentgross</td></tr>

<tr><td width=100% colspan=2 class=maintext align=center>Thank you for your payment. Your transaction has been completed, and a receipt for your SALE ITEM ADDITION has been emailed to you.</td></tr>
<tr><td width=100% colspan=2 class=maintext align=center><br>Item added successfully</td></tr>
</table>";


//=================================== /AFRET PDT CHECK========================================
        }
    }
}

$sql = "Insert into " . TABLEPREFIX . "tempdata(nId,vValue,vData)  values('','" . addslashes($saleid) . "|$userid|featured" . "','" . addslashes($txnid) . "');";
mysqli_query($conn, $sql);
?>