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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

$txtPaypalEmail = DisplayLookUp('paypalemail');
$txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
$txtPaypalSandbox = DisplayLookUp('paypalmode');

if ($txtPaypalSandbox == "TEST") {
    $paypalurl = "www.sandbox.paypal.com";
}//rnf ig
else {
    $paypalurl = "www.paypal.com";
}//end else

$flag_to_continue = false;

if($_GET['tx']!='' || $_GET['st']!='') { 
//Data check for PDT and proceeded further
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-synch';

$tx_token = $_GET['tx'];
//$auth_token = "Yl95T6hgNiiI3TsU4VYxHabtIiy-AZzCP5ks92AzLz3We2Jknt5lhx-wQtK";
$auth_token = $txtPaypalAuthtoken;
$req .= "&tx=$tx_token&at=$auth_token";

// post back to PayPal system to validate
$header .= "POST https://".$paypalurl."/cgi-bin/webscr HTTP/1.1\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Host: ".$paypalurl."\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n";
$header .= "Connection: close\r\n\r\n";
$fp = fsockopen ("ssl://".$paypalurl, 443, $errno, $errstr, 30);

// If possible, securely post back to paypal using HTTPS
// Your PHP server will need to be SSL enabled
// $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

if (trim($txtPaypalAuthtoken)==''){
    $flag_to_continue = true;
}
else if (!$fp) {
    // HTTP ERROR
}//end if
else {
    fputs($fp, $header . $req);
    // read the body data
    $res = '';
    $headerdone = false;
    while (!feof($fp)) {
        $line = fgets($fp, 1024);
        if (strcmp($line, "\r\n") == 0) {
            // read the header
            $headerdone = true;
        }//end if
        else if ($headerdone) {
            // header has been read. now read the contents
            $res .= $line;
        }//end else if
    }//end while
    // parse the data
    $lines = explode("\n", $res);
    $keyarray = array();
    //echo($lines[0] . " here");
    if (strcmp($lines[0], "SUCCESS") == 0 || strcmp($lines[1], "SUCCESS") == 0) {
        for ($i = 1; $i < count($lines); $i++) {
            list($key, $val) = explode("=", $lines[$i]);
            $keyarray[urldecode($key)] = urldecode($val);
        }//end for loop
        // check the payment_status is Completed
        // check that txn_id has not been previously processed
        // check that receiver_email is your Primary PayPal email
        // check that payment_amount/payment_currency are correct
        // process payment
        $flag_to_continue = true;
    }//end if
    else if (strcmp($lines[0], "FAIL") == 0 || strcmp($lines[1], "FAIL") == 0) {
        // log for manual investigation
        //                echo("Fail in result");
        $flag_to_continue = false;
    }//end else if
}//end if

fclose($fp);

$var_id = "";
$var_new_id = "";
$message = "";


$txnid = $keyarray['txn_id'];
$saleid = $keyarray['item_number'];
$saleitem = $keyarray['item_name'];
$paymentgross = $keyarray['payment_gross'];
$paytype = "pp";
$nUserId = $_SESSION["guserid"];

if ($keyarray['receiver_email'] != $txtPaypalEmail && trim($txtPaypalAuthtoken)!='') {
    $flag_to_continue = false;
}//end if

if ($flag_to_continue == true) {
    //get all postback variables
    //=================================== Check if already validated by ipn================

    $sql = "Select * from " . TABLEPREFIX . "saleextra where nSaleextraId='$saleid'";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    if (mysqli_num_rows($result) > 0) {
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
                    $sql .= "', '" . addslashes(stripslashes($row["vTitle"])) . "',";
                    $sql .= "'" . addslashes(stripslashes($row["vBrand"])) . "', '" . $row["vType"] . "', '" . $row["vCondition"] . "', '" . $row["vYear"] . "', '" . $row["nValue"] . "',";
                    $sql .= "'" . $row["nShipping"] . "', '" . $row["vUrl"] . "', '" . addslashes(stripslashes($row["vDescription"])) . "','";
                    $sql .= $row["dPostDate"] . "'";
                    $sql .= ", '" . $row["nQuantity"] . "','$vfeatured','0','" . $row["vSmlImg"] . "','" . addslashes(stripslashes($row["vImgDes"])) . "','".$row["nPoint"]."')";
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
                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, "Member", $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                    $msgBody = file_get_contents('./languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
                }//end while loop
            }//end if
        }//end if
        $ddt = explode(' ',$txtPostDate);
        $ddt_arr = explode('-', $ddt[0]);
        $txtPostDate = $ddt_arr[1].'/'.$ddt_arr[2].'/'.$ddt_arr[0];
        
        $message = "<div class='row'>
					<div class='col-lg-2 col-sm-12 col-md-1 col-xs-2'></div>
					<div class='col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer'>
							<div class='row main_form_inner'>
								<label>".TEXT_TITLE."</label>
								<label>$txtTitle</label>
							</div>
							<div class='row main_form_inner'>
								<label>".TEXT_POSTED_ON." (".TEXT_MM_DD_YYYY.")</label>
								<label>".$txtPostDate."</label>
							</div>
							<div class='row main_form_inner'>
								<label>".TEXT_PAYMENT_MODE."</label>
								<label>".TEXT_PAYPAL."</label>
							</div>
							<div class='row main_form_inner'>
								<label>".TEXT_AMOUNT."</label>
								<label>".CURRENCY_CODE.$totalamt."</label>
							</div>
							<div class='row main_form_inner'>
								<label>".MESSAGE_THANKYOU_PAYMENT_TRANSACTION_COMPLETED."</label>
							</div>
							<div class='row main_form_inner'>
								<label>".MESSAGE_ITEM_SUCCESSFULLY_LISTED."</label>
								<label></label>
							</div>
					</div>	
					<div class='col-lg-2 col-sm-12 col-md-1 col-xs-2'></div>			
				</div>";
    }//end if
    else {
	$message = "<div class='row'>
					<div class='col-lg-2 col-sm-12 col-md-1 col-xs-2'></div>
					<div class='col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer'>";
	if (trim($txtPaypalAuthtoken)!='') {
        $message .= 		"	<div class='row main_form_inner'>
									<label>".TEXT_TITLE."</label>
									<label>$saleitem</label>
								</div>
								<div class='row main_form_inner'>
									<label>".TEXT_POSTED_ON." (".TEXT_MM_DD_YYYY.")</label>
									<label>" . date("m/d/Y") . "</label>
								</div>
								<div class='row main_form_inner'>
									<label>".TEXT_PAYMENT_MODE."</label>
									<label>".TEXT_PAYPAL."</label>
								</div>
								<div class='row main_form_inner'>
									<label>".TEXT_AMOUNT."</label>
									<label>".CURRENCY_CODE.$paymentgross."</label>
								</div>";
									}
								$message .=				"
								
								<div class='row main_form_inner'>
									<label>".MESSAGE_THANKYOU_PAYMENT_TRANSACTION_COMPLETED."</label>
								</div>
								<div class='row main_form_inner'>
									<label>".MESSAGE_ITEM_SUCCESSFULLY_LISTED."</label>
								</div>	
							</div>	
						<div class='col-lg-2 col-sm-12 col-md-1 col-xs-2'></div>			
					</div>";
    }//end else
    
    unset($_GET['tx']);
    unset($_GET['st']);
    
}//end if
else {
        $message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
    }//end else
}
else {
    $message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
}//end else

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
	
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3">
				<?php include_once ("./includes/usermenu.php"); ?>
			</div>
			<div class="col-lg-9">
				<div class="innersubheader">
					<h4><?php echo HEADING_PAYMENT_STATUS; ?></h4>
				</div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<div class="row main_form_inner">
							<label><?php echo $message; ?></label>
						</div>
					</div>	
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>			
				</div>
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>
			</div>
		</div>  
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>