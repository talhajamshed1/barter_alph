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
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

$flag_to_continue = false;

$transstatus = $_REQUEST['transStatus'];

if (isset($_REQUEST['transStatus']) && $_REQUEST['transStatus'] == 'Y') {
    $flag_to_continue = true;
}//end if

if (DisplayLookUp('worldpaydemo') == "YES") {
    $txnid = 'TEST-' . time();
}//end if
else {
    $txnid = time();
}//end if

$saleid = $_SESSION['sess_saleid'];
$saleitem = $_SESSION['sess_name'];
$paymentgross = $_SESSION['sess_amount'];
$paytype = "wp";
$nUserId = $_SESSION["guserid"];

if ($flag_to_continue == true) {
    //get all postback variables
    //=================================== Check if already validated by ipn================

    $sql = "Select * from " . TABLEPREFIX . "saleextra where nSaleextraId='$saleid'";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    if (mysqli_num_rows($result) > 0) {
        //check if txnid alredy there to prevent refresh
        $sql = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid' AND vTxn_mode='wp'";
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
                    }//end if
                    else {
                        $vfeatured = "N";
                    }//end else
                    if ($row["nCommission"] > 0) {
                        $action.="C";
                    }//end if

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
                    $sql .= ", '" . $row["nQuantity"] . "','$vfeatured','0','" . $row["vSmlImg"] . "','" . $row["vImgDes"] . "','".$row["nPoint"]."')";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }//end if
            }//end if
            //inert multiple images
            $NewId = mysqli_insert_id($conn);

            //update table with new id
            mysqli_query($conn, "update " . TABLEPREFIX . "gallery  set nTempId='', nSaleId='" . $NewId . "'
													where nTempId='" . $_SESSION["guserid"] . "'") or die(mysqli_error($conn));

            //get last posted sale id
            $sql = "Select nSaleId from " . TABLEPREFIX . "sale where dPostDate='$txtPostDate' AND nUserId = '" . $_SESSION["guserid"] . "'";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                if ($row2 = mysqli_fetch_array($result)) {
                    $saleid1 = $row2["nSaleId"];
                }//end if
            }//end if
            //insert to payemnt table

            /* get the invoice number */
            $sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
            $result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
            $row1 = mysqli_fetch_array($result1);
            $Inv_id = $row1['maxinvid'];
            /*             * *********************** */

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


            $categorysql = "Select L.vCategoryDesc from " . TABLEPREFIX . "category C
                                LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                            where C.nCategoryId ='$txtCategory'";
            $resultcategory = mysqli_query($conn, $categorysql) or die(mysqli_error($conn));
            $row4 = mysqli_fetch_array($resultcategory);
            $txtCategoryname = $row4["vCategoryDesc"];
            
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

                $mainTextShow   = $mailRw['content'];


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


            $sql = "Select vFirstName,vEmail from " . TABLEPREFIX . "users where (vAlertStatus='Y' OR nUserId = '" . $_SESSION["guserid"] . "') and vDelStatus = '0' and vStatus = '0'";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $EMail = stripslashes($row["vEmail"]);

                    //readf file n replace
                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, htmlentities($row["vFirstName"]), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                    $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
                }//end while loop
            }//end if
        }//end if
        //clear sessions
        $_SESSION['sess_saleid'] = '';
        $_SESSION['sess_amount'] = '';
        $_SESSION['sess_name'] = '';

        $message = "<table width='100%'  border='0' cellspacing='1' cellpadding='4' class='maintext2'>
							<tr bgcolor='#FFFFFF'><td width=50% class=gray align=left>Title</td>
							<td width=50% class=maintext2  align=left>$txtTitle</td></tr>
							<tr bgcolor='#FFFFFF'><td width=50% class=gray align=left>Post Date</td>
							<td width=50% class=maintext2  align=left>$txtPostDate</td></tr>
							<tr bgcolor='#FFFFFF'><td width=50% class=gray align=left>Payment Mode</td>
							<td width=50% class=maintext2  align=left>WorldPay</td></tr>
							<tr bgcolor='#FFFFFF'><td width=50% class=gray align=left>Amount</td>
							<td width=50% class=maintext2  align=left>$totalamt</td></tr>
							<tr bgcolor='#FFFFFF'><td width=100% colspan=2 class=maintext2 align=center>Thank you for your payment. Your transaction has been completed, and a receipt for your SALE ITEM ADDITION has been emailed to you.</td></tr>
							<tr bgcolor='#FFFFFF'><td width=100% colspan=2 class=maintext2 align=center><br>Item added successfully</td></tr>
				</table>";
    }//end if
    else {
        $message = "<table width='100%'  border='0' cellspacing='1' cellpadding='4' class='maintext2'>
							<tr bgcolor='#FFFFFF'><td width=50% class=gray align=left>Title</td>
							<td width=50% class=maintext2  align=left>$saleitem</td></tr>
							<tr bgcolor='#FFFFFF'><td width=50% class=gray align=left>Post Date</td>
							<td width=50% class=maintext2  align=left>" . date("F d, Y, g i s") . "</td></tr>
							<tr bgcolor='#FFFFFF'><td width=50% class=gray align=left>Payment Mode</td>
							<td width=50% class=maintext2  align=left>WorldPay</td></tr>
							<tr bgcolor='#FFFFFF'><td width=50% class=gray align=left>Amount</td>
							<td width=50% class=maintext2  align=left>$paymentgross</td></tr>
							<tr bgcolor='#FFFFFF'><td width=100% colspan=2 class=maintext2 align=center>Thank you for your payment. Your transaction has been completed, and a receipt for your SALE ITEM ADDITION has been emailed to you.</td></tr>
							<tr bgcolor='#FFFFFF'><td width=100% colspan=2 class=maintext2 align=center><br>Item added successfully</td></tr>
							</table>";
    }//end else
}//end if
else {
    $message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
}//end else

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="headerbg"><?php require_once("./includes/header.php"); ?>
                <?php require_once("menu.php"); ?>
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="10%" height="688" valign="top"><?php include_once ("./includes/usermenu.php"); ?>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td id="leftcoloumnbtm"></td>
                                            </tr>
                                        </table></td>
                                    <td width="74%" valign="top">
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td class="link3">&nbsp;</td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="heading" align="left"><?php echo HEADING_PAYMENT_STATUS; ?></td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><?php echo $message; ?></td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                        </table>
										<?php include('./includes/sub_banners.php'); ?>
                                    </td>
                                </tr>
                            </table></td>
                    </tr>
                </table>
<?php require_once("./includes/footer.php"); ?>