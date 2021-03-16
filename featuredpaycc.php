<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
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

if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else
//store user profile
$userProfile = userProfiles($_SESSION["guserid"]);
$Sscope = "featuredpay";

//checking payment method
$txtPayMethod = ($_GET['paytype'] != '') ? $_GET['paytype'] : $_POST['txtPayMethod'];

$saleid = $_SESSION["gsaleextraid"];
$paytype = $txtPayMethod;

$featured = "0";
$commission = "0";
$sql = "Select nFeatured,nCommission from " . TABLEPREFIX . "saleextra where nSaleextraId='$saleid'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
while ($row = mysqli_fetch_array($result)) {
    $featured = $row["nFeatured"];
    $commission = $row["nCommission"];
}//end while
$amount = $featured + $commission;

//redeem point code start here
//checking payment mode
if ($txtPayMethod == 'rp') {
    //fetch logged user total points
    $showUserTotalPoints = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');

    if ($showUserTotalPoints > 0) {
        $showUserTotalPoints = $showUserTotalPoints;
        $redeemPoint = round(($amount / DisplayLookUp('PointValue')) * DisplayLookUp('PointValue2'), 2);

        //checking enter user points and avilable points
        if ($redeemPoint <= $showUserTotalPoints) {
            //redeem points from user
            mysqli_query($conn, "UPDATE " . TABLEPREFIX . "usercredits SET nPoints=nPoints-$redeemPoint WHERE
								nUserId='" . $_SESSION["guserid"] . "'") or die(mysqli_error($conn));

            $txnid = $cc_tran;
            $paytype = $txtPayMethod;
            //check if txnid alredy there to prevent refresh
            $sql = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid' AND vTxn_mode='" . $paytype . "'";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            //if(mysqli_num_rows($result) == 0){
            if (1 == 1) {
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
                        $txtTitle = ($row["vTitle"]);
                        $txtBrand = ($row["vBrand"]);
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

                $sql = "INSERT INTO " . TABLEPREFIX . "payment (nTxn_no, vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId,vInvno) VALUES ('', '$action', '$txnid', ' $totalamt', '$paytype', '$txtPostDate', '', '$saleid1','$Inv_id')";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                //delete from temp table
                $sql = "delete from " . TABLEPREFIX . "saleextra where nSaleextraId='$saleid'";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));


                //update category listings
                $routesql = "Select vRoute from " . TABLEPREFIX . "category where nCategoryId ='$txtCategory'";
                $result = mysqli_query($conn, $routesql) or die(mysqli_error($conn));
                $row1 = mysqli_fetch_array($result);
                $route = $row1["vRoute"];
                if ($route!=''){
                    $countsql = "UPDATE " . TABLEPREFIX . "category SET nCount=nCount+1 WHERE nCategoryId in($route)";
                    $result = mysqli_query($conn, $countsql) or die(mysqli_error($conn));
                }
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

                
                $sql = "Select vFirstName,vLoginName,vEmail from " . TABLEPREFIX . "users where (vAlertStatus='Y' OR nUserId = '" . $_SESSION["guserid"] . "') and vDelStatus = '0' and vStatus = '0'";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $EMail = stripslashes($row["vEmail"]);

                        //readf file n replace
                        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, htmlentities($row["vLoginName"]), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                        $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                        $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                        send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
                    }//end while
                }//end if

                $_SESSION["gsaleextraid"] = "";
                $location = "featuredcon.php?saleid=$saleid1&amt=$totalamt&ptype=rp&";
                header("location:$location");

                exit();
            }//end if
        }//end if
        else {
            $message = str_replace('{point}',$showUserTotalPoints,str_replace('{point_name}',POINT_NAME,ERROR_CANNOT_COMPLETE_POINT_LOW));
            $message .= '<br><a class="cont-btn" href="featuredpay.php">'.LINK_CONTINUE.'</a>';
        }//end else
    }//end if
    else {
        $showUserTotalPoints = '0';
        $message = str_replace('{point_name}',POINT_NAME,ERROR_CANNOT_COMPLETE_NO_POINT_AVAILABLE).' '.TEXT_CLICK_LINK_TO_CONTINUE.'<br><a class="cont-btn" href="featuredpay.php">'.LINK_CONTINUE.'</a>';
    }//end else
}//end if
//redeem point code end here

if ($_POST["postback"] == "Y") {
    $cost = $amount;
    $userid = $_SESSION["guserid"];
    $FirstName = $_POST["txtFirstName"];
    $LastName = $_POST["txtLastName"];
    $Address = $_POST["txtAddress"];
    $City = $_POST["txtCity"];
    $State = $_POST["txtState"];
    $Zip = $_POST["txtPostal"];
    $CardNum = $_POST["txtccno"];
    $Email = $_POST["txtEmail"];
    $CardCode = $_POST["txtcvv2"];
    $Country = $_POST["cmbCountry"];
    $Month = $_POST["txtMM"];
    $Year = $_POST["txtYY"];
    $amnt = $cost;
    $txtACurrency = PAYMENT_CURRENCY_CODE;
    $cc_flag = false;
    $cc_err = "";
    $cc_tran = "";
    
        $userProfile['vAddress1'] = $Address;

        $userProfile['vCity'] = $City;

        $userProfile['vState'] = $State;

        $userProfile['nZip'] = $Zip;

        $userProfile['vEmail'] = $Email;


    /* get the invoice number */
    $sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
    $result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
    $row1 = mysqli_fetch_array($result1);
    $Inv_id = $row1['maxinvid'];
    /*     * *********************** */


    $Cust_ip = getClientIP();
    $Company = '-NA-';
    $Phone = $_SESSION["gphone"];
    $Cust_id = $_SESSION["guserid"];


    //checking payment mode
    if ($txtPayMethod == 'cc') {
        require("credit_inte_featured.php");
    }//end if
    if ($txtPayMethod == 'bp') {
        require("Bluepay.php");
    }//end if
    if ($txtPayMethod == 'yp') {
        require("yourpay.php");
	}//end else
	if ($txtPayMethod == 'sp') {
		require("stripepay.php");
    }


    if ($cc_flag == true) {
        $txnid = $cc_tran;
        $paytype = $txtPayMethod;
        //check if txnid alredy there to prevent refresh
        $sql = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid' AND vTxn_mode='" . $paytype . "'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        //if(mysqli_num_rows($result) == 0){
        if (1 == 1) {
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
                if ($row3 = mysqli_fetch_array($result)) {
                    $saleid1 = $row3["nSaleId"];
                }//end if
            }//end if
            //insert to payemnt table

            $sql = "INSERT INTO " . TABLEPREFIX . "payment (nTxn_no, vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId,vInvno) VALUES ('', '$action', '$txnid', ' $totalamt', '$paytype', '$txtPostDate', '', '$saleid1','$Inv_id')";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

            //delete from temp table
            $sql = "delete from " . TABLEPREFIX . "saleextra where nSaleextraId='$saleid'";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));


            //update category listings
            $routesql = "Select vRoute from " . TABLEPREFIX . "category where nCategoryId ='$txtCategory'";
            $result = mysqli_query($conn, $routesql) or die(mysqli_error($conn));
            $row1 = mysqli_fetch_array($result);
            $route = $row1["vRoute"];
            if ($route!=''){
                $countsql = "UPDATE " . TABLEPREFIX . "category SET nCount=nCount+1 WHERE nCategoryId in($route)";
                $result = mysqli_query($conn, $countsql) or die(mysqli_error($conn));
            }
            $categorysql = "Select L.vCategoryDesc from " . TABLEPREFIX . "category C
                                LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                            where C.nCategoryId ='$txtCategory'";
            $resultcategory = mysqli_query($conn, $categorysql) or die(mysqli_error($conn));
            $row2 = mysqli_fetch_array($resultcategory);
            $txtCategoryname = $row2["vCategoryDesc"];

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
                $mainTextShow   = $mailRw['content'];

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
                    $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
                }//end while
            }//end if

            $_SESSION["gsaleextraid"] = "";
            $location = "featuredcon.php?saleid=$saleid1&amt=$totalamt&";
            header("location:$location");

            exit();
        }//end if
    }//end if
}//end if
include_once('./includes/title.php');
?>
<?php
if ($txtPayMethod == 'sp') {
?>
<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<?php } ?>
<script language="javascript" type="text/javascript">
    function clickConfirm(submitForm = 1)
    {
         var $card_num = $jqr( "#id_card_num" );
	var card_num = $card_num.val();
        var card_code = $jqr( "#txtcvv2").val();
	//-- Credit card can only be numeric
	if (!$jqr.isNumeric(card_num)) {
		alert('<?php echo PAYMENT_INVALID_CARD_NUMEBR;?>');
		$card_num.focus();
		return false;
	}
        
        if (!$jqr.isNumeric(card_code)) {
		alert('<?php echo PAYMENT_INVALID_CARD_CODE;?>');
		$jqr("#txtcvv2").focus();
		return false;
	}

	var $exp_mon =  $jqr( "#id_exp_mon" );
	var $exp_year = $jqr( "#id_exp_year" );
	var exp_mon = $exp_mon.val();
	var exp_year = $exp_year.val();
	//-- Month can only be numeric
	if (!$jqr.isNumeric(exp_mon)) {
		alert('<?php echo PAYMENT_INVALID_EXP_MONTH;?>');
		$exp_mon.focus();
		return false;
	}
	//-- Year can only be numeric
	if (!$jqr.isNumeric(exp_year)) {
		alert('<?php echo PAYMENT_INVALID_EXP_YEAR;?>');
		$exp_year.focus();
		return false;
	}
	//-- Year must be in YYYY format
	if($jqr.trim(exp_year).length < 4) {
		alert('<?php echo PAYMENT_INVALID_YEAR_FORMAT;?>');
		$exp_year.focus();
		return false;
	}
        
        document.frmBuy.postback.value="Y";
        document.frmBuy.method="post";
        if(submitForm){
        		document.frmBuy.submit();
			}else {
				return true;
			}
    }

    function checkValue(t)
    {
        if(isNaN(t.value) || t.value.substring(0,1) == " " || t.value.length == 0 || parseFloat(t.value) < 0 )
        {
            if(t.name == "txtccno")
            {
                t.value="";
            }//end if
            else
            {
                t.value="000";
            }//end else
        }//end if
    }//end function
</script>
<?php
if ($txtPayMethod == 'sp') {
if(DisplayLookUp('stripedemo')=="YES"){	
?>
<script>
Stripe.setPublishableKey("<?php echo DisplayLookUp('stripepublic'); ?>");
</script>
<?php
}
if(DisplayLookUp('stripedemo')=="NO"){	
	?>
	<script>
	Stripe.setPublishableKey("<?php echo DisplayLookUp('stripepubliclive'); ?>");
	</script>
	<?php
	}
	?>
<script>
//callback to handle the response from stripe
function stripeResponseHandler(status, response) {
    if (response.error) {
        //enable the submit button
        //$("#submit-btn").show();
        //$( "#loader" ).css("display", "none");
        //display the errors on the form
        $jqr("#error-message").html(response.error.message).show();
    } else {
        //get token id
        var token = response['id'];
        //insert the token into the form
        $jqr("#frmStripePayment").append("<input type='hidden' name='token' value='" + token + "' />");

        //submit form to the server
        $jqr("#frmStripePayment").submit();
    }
}
function stripePay() {
    //e.preventDefault();
    var valid = clickConfirm(0);
	var frm = document.frmBuy;

    if(valid == true) {
        //$("#submit-btn").hide();
        //$( "#loader" ).css("display", "inline-block");
		Stripe.createToken({
            number: $jqr( "#id_card_num" ).val(),
            cvc: $jqr( "#txtcvv2").val(),
            exp_month: $jqr( "#id_exp_mon" ).val(),
            exp_year: $jqr( "#id_exp_year" ).val(),
        }, stripeResponseHandler);

        //submit from callback
        return false;
    }
}
</script>
<?php 
}
?>
<?php if (trim($userProfile['vCountry'])=='') $userProfile['vCountry']='United States'; ?>
<body onLoad="javascript:document.getElementById('ddlCountry').value='<?php echo $userProfile['vCountry']; ?>';"><!--document.frmPay.submit();-->
<?php include_once('./includes/top_header.php'); ?>

<?php require_once("./includes/header.php"); ?>
                <?php //require_once("menu.php"); ?>
				
				
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">					
		
				
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 ">
                        <div class="main_form_outer">
                            <h4><?php echo HEADING_PAYMENT_FORM; ?></h4>
					
					<?php
						if ($txtPayMethod == 'rp') {
							if (isset($message) && $message != '') {
								?>
									<div class="row warning"><?php echo $message; ?></div>
								<?php
							}//end if
						}//end if
						if ($txtPayMethod != 'rp' && $txtPayMethod != 'gc') {
							?>
						<form name="frmBuy" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>" id="<?php echo ($txtPayMethod == 'sp')? 'frmStripePayment':''; ?>">
							<input type="hidden" name="postback" id="postback" value="">
							<input type="hidden" name="saleid" id="saleid" value="<?php echo  $saleid ?>">
							<input type="hidden" name="paytype" id="paytype" value="<?php echo  $paytype ?>">
							<input type="hidden" name="txtPayMethod" value="<?php echo $txtPayMethod; ?>">
							<?php
							if ($cc_flag == false && $cc_err != '') {
								?>
							<div class="row warning"><?php echo $cc_err; ?></div>
							<?php }//end if ?>
							<h3 class="subheader row"><?php echo HEADING_PAYMENT_DETAILS; ?></h3>
							<div class="col-lg-6 col-sm-12 col-md-6 col-xs-12 no_padding">
								<div class="row main_form_inner">
									<label><b><?php echo TEXT_ITEM; ?></b></label>
									<?php echo TEXT_SALE_ITEM_ADDITION; ?>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 col-md-6 col-xs-12 no_padding">
								<div class="row main_form_inner">
									<label><b><?php echo TEXT_AMOUNT; ?></b></label>
									<?php echo CURRENCY_CODE; ?><?php echo $amount ?>
								</div>
							</div>
							
							<h3 class="subheader row"><?php echo TEXT_CREDIT_CARD_DETAILS; ?></h3>
							
							<div class="row main_form_inner">
								<label><?php echo TEXT_FIRST_NAME; ?><span class="warning">*</span></label>
								<input type="text" name="txtFirstName" id="txtFirstName" value="<?php if($_POST["postback"] == "Y"){echo trim($FirstName);}else{echo($userProfile['vFirstName']);} ?>" size="24" maxlength="40" class="comm_input form-control">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_LAST_NAME; ?><span class="warning">*</span></label>
								<input type="text" name="txtLastName" id="txtLastName" value="<?php if($_POST["postback"] == "Y"){echo trim($LastName);}else{echo($userProfile['vLastName']);} ?>" size="24" maxlength="40" class="comm_input form-control">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_CARD_NUMBER; ?><span class="warning">*</span></label>
								<input type=text name="txtccno" class="comm_input form-control visa_amex_img" id="id_card_num" size="24" maxlength="16" onBlur="javascript:checkValue(this);" value="<?php echo $CardNum;?>">
								<!--<img src="<?php echo $imagefolder ?>/images/visa_amex.gif">-->
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_CARD_VALIDATION_CODE; ?><span class="warning">*</span> &nbsp; &nbsp; <a href="http://www.cvvnumber.com/cvv.html" target="_blank" style="font-size:11px">What is my CVV code?</a></label>
								<input type=password name="txtcvv2" class="comm_input form-control" id="txtcvv2" size=10 maxlength="4" value="<?php echo $CardCode;?>" onBlur="javascript:checkValue(this);">
								
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_EXPIRATION_DATE; ?><span class="warning">*</span></label>
								<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6 no_padding" style="padding-right: 10px;">
									<input type="text" name="txtMM" class="comm_input form-control" value="<?php echo $Month;?>" id="id_exp_mon" size=3 maxlength="2" >
								</div>
								<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6 no_padding">
									<input type="text" name="txtYY" class="comm_input form-control" id="id_exp_year" size=4 maxlength="4" value="<?php echo $Year;?>">
								</div>
							</div>
							
							<h3 class="subheader row"><?php echo TEXT_BILLING_ADDRESS_DETAILS; ?></h3>
							
							<div class="row main_form_inner">
								<label><?php echo TEXT_ADDRESS; ?><span class="warning">*</span></label>
								<input type="text" name="txtAddress" class="comm_input form-control" id="txtAddress" size="24" maxlength="30" value="<?php if($_POST["postback"] == "Y"){echo trim($Address);}else{echo($userProfile['vAddress1']);} ?>">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_CITY; ?><span class="warning">*</span></label>
								<input type="text" name="txtCity" class="comm_input form-control" id="txtCity" size="24" maxlength="30"  value="<?php if($_POST["postback"] == "Y"){echo trim($City);}else{echo($userProfile['vCity']);} ?>">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_STATE; ?><span class="warning">*</span></label>
								<input type="text" name="txtState" class="comm_input form-control" id="txtState" size="24" maxlength=30 value="<?php if($_POST["postback"] == "Y"){echo trim($State);}else{echo($userProfile['vState']);} ?>">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_ZIP; ?><span class="warning">*</span></label>
								<input type="text" name="txtPostal" class="comm_input form-control" id="txtPostal" size="24" maxlength="10" value="<?php if($_POST["postback"] == "Y"){echo trim($Zip);}else{echo($userProfile['nZip']);} ?>">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_COUNTRY; ?><span class="warning">*</span></label>
								<select name="cmbCountry" class="comm_input form-control" id="ddlCountry"><?php include("includes/country_select.php"); ?></select>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_EMAIL; ?><span class="warning">*</span></label>
								<input type="text" name="txtEmail" class="comm_input form-control" id="txtEmail" size="24" maxlength="50" value="<?php if($_POST["postback"] == "Y"){echo trim($Email);}else{echo($userProfile['vEmail']);} ?>">
							</div>
							<div class="row main_form_inner">
								<input type="button" name="btPay" id="btPay" class="subm_btt"  value="<?php echo BUTTON_PAY_NOW; ?>" onClick="javascript:<?php echo ($txtPayMethod != 'sp')?'clickConfirm();':'stripePay()'?>">
							</div>								
						</form>
						
						
						<?php
							}//end if
							if ($txtPayMethod == 'gc') {

								require_once('gc/library/googlecart.php');
								require_once('gc/library/googleitem.php');
								require_once('gc/library/googleshipping.php');
								require_once('gc/library/googletax.php');
								require_once('gc/library/googleresponse.php');
								require_once('gc/library/googlemerchantcalculations.php');
								require_once('gc/library/googleresult.php');

								$_SESSION['txtGoogleId'] = DisplayLookUp('googleid');
								$_SESSION['txtGoogleKey'] = DisplayLookUp('googlekey');
								$_SESSION['chkGoogleSandbox'] = DisplayLookUp('googlemode');
								$_SESSION['sess_gc_saleid'] = $saleid;
								$_SESSION['sess_gc_paytype'] = $paytype;
								$_SESSION['sess_gc_txtPayMethod'] = $txtPayMethod;
								$_SESSION['sess_gc_amount'] = $amount;
								$_SESSION['sess_gc_txtACurrency'] = PAYMENT_CURRENCY_CODE;

								$gc_status = ($_GET['gc_status'] != '') ? $_GET['gc_status'] : 'failure';

								function UseCase1() {
									$google_id = $_SESSION['txtGoogleId']; // Merchant ID
									$google_key = $_SESSION['txtGoogleKey']; // Merchant Key
									$google_demo = $_SESSION['chkGoogleSandbox']; // "YES" if in test mode, "NO" if in live mode
									$cost = $_SESSION['sess_gc_amount']; // price
									$currency = $_SESSION['sess_gc_txtACurrency'];

									$saleid = $_SESSION['sess_gc_saleid'];
									$paytype = $_SESSION['sess_gc_paytype'];
									$txtPayMethod = $_SESSION['sess_gc_txtPayMethod'];
									$amount = $_SESSION['sess_gc_amount'];
									$txtACurrency = $_SESSION['sess_gc_txtACurrency'];


									if ($google_demo == "TEST")
										$server_type = "sandbox";
									else
										$server_type = "checkout";


									// Create a new shopping cart object
									$cart = new GoogleCart($google_id, $google_key, $server_type, $currency);

									// Add items to the cart
									$item_1 = new GoogleItem(SITE_NAME, // Item name
													TEXT_SALE_ITEM_ADDITION, // Item description
													1, // Quantity
													$cost); // Unit price

									$cart->AddItem($item_1);

									// continue link page
									$cart->SetContinueShoppingUrl(SECURE_SITE_URL . "/featuredpaycc.php?paytype=gc&gc_status=success");

									$cart->AddRoundingPolicy("HALF_UP", "PER_LINE");

									$cart->SetMerchantPrivateData('featuredpaycc-' . $_SESSION["guserid"] . '-' . $saleid . '-' . $_SESSION["gphone"] . '-' . $amount . '-' . $txtACurrency. '-' . $email.'-'.$_SESSION["points"]);

									// Display Google Checkout button
									echo $cart->CheckoutButtonCode("LARGE");
								}

							//end google usecase

								$_SESSION['sess_page_name'] = 'featuredpaycc.php';
								$_SESSION['sess_page_return_url_suc'] = SITE_URL . "/featuredpaycc.php?paytype=gc&gc_status=success";
								$_SESSION['sess_page_return_url_fail'] = SECURE_SITE_URL . "/featuredpaycc.php?paytype=gc&gc_status=failure";


								//calculation starts here
								if (isset($gc_status) && $gc_status == 'success') {
									$cost = $amount;
									$userid = $_SESSION["guserid"];
									$amnt = $cost;
									$txtACurrency = $txtACurrency;
									$gc_flag = true;
									$gc_err = "";
									$gc_tran = "";


									/* get the invoice number */
									$sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
									$result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
									$row1 = mysqli_fetch_array($result1);
									$Inv_id = $row1['maxinvid'];
									/*         * *********************** */


									$Cust_ip = getClientIP();
									$Company = '-NA-';
									$Phone = $_SESSION["gphone"];
									$Cust_id = $_SESSION["guserid"];

									if ($gc_flag == true) {
										$txnid = $gc_tran;
										$paytype = $txtPayMethod;
										//check if txnid alredy there to prevent refresh
										$sql = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid' AND vTxn_mode='" . $paytype . "'";
										$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
										//if(mysqli_num_rows($result) == 0){
										if (1 == 1) {
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
											
											//inert multiple images
											$NewId = mysqli_insert_id($conn);

											//update table with new id
											mysqli_query($conn, "update " . TABLEPREFIX . "gallery  set nTempId='', nSaleId='" . $NewId . "'
																																	where nTempId='" . $_SESSION["guserid"] . "'") or die(mysqli_error($conn));

											//get last posted sale id
											$sql = "Select nSaleId from " . TABLEPREFIX . "sale where dPostDate='$txtPostDate' AND nUserId = '" . $_SESSION["guserid"] . "'";
											$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
											if (mysqli_num_rows($result) > 0) {
												if ($row1 = mysqli_fetch_array($result)) {
													$saleid1 = $row1["nSaleId"];
												}//end if
											}//end if
											//insert to payemnt table

											$sql = "INSERT INTO " . TABLEPREFIX . "payment (nTxn_no, vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId,vInvno) VALUES ('', '$action', '$txnid', ' $totalamt', '$paytype', '$txtPostDate', '', '$saleid1','$Inv_id')";
											$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

											//delete from temp table
											$sql = "delete from " . TABLEPREFIX . "saleextra where nSaleextraId='$saleid'";
											$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));


											//update category listings
											$routesql = "Select vRoute from " . TABLEPREFIX . "category where nCategoryId ='$txtCategory'";
											$result = mysqli_query($conn, $routesql) or die(mysqli_error($conn));
											$row2 = mysqli_fetch_array($result);
											$route = $row2["vRoute"];
											if ($route!=''){
												$countsql = "UPDATE " . TABLEPREFIX . "category SET nCount=nCount+1 WHERE nCategoryId in($route)";
												$result = mysqli_query($conn, $countsql) or die(mysqli_error($conn));
											}
											$categorysql = "Select L.vCategoryDesc from " . TABLEPREFIX . "category C
																LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
															where C.nCategoryId ='$txtCategory'";
											$resultcategory = mysqli_query($conn, $categorysql) or die(mysqli_error($conn));
											$row3 = mysqli_fetch_array($resultcategory);
											$txtCategoryname = $row3["vCategoryDesc"];
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
											$mainTextShow   = $mailRw['content'];

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
													$msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
													$msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

													send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
												}//end while
											}//end if
											}//end if
											else {//If it is processed in the response.php itself
												 //get last posted sale id
												$sql = "Select nSaleId from " . TABLEPREFIX . "sale where nUserId = '" . $_SESSION["guserid"] . "' order by nSaleId desc limit 1";
												$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
												if (mysqli_num_rows($result) > 0) {
													if ($row = mysqli_fetch_array($result)) {
														$saleid1 = $row["nSaleId"];
													}//end if
												}//end if
											}
											$_SESSION["gsaleextraid"] = "";
											$_SESSION['txtGoogleId'] = "";
											$_SESSION['txtGoogleKey'] = "";
											$_SESSION['chkGoogleSandbox'] = "";
											$_SESSION['sess_gc_saleid'] = "";
											$_SESSION['sess_gc_paytype'] = "";
											$_SESSION['sess_gc_txtPayMethod'] = "";
											$_SESSION['sess_gc_amount'] = "";
											$_SESSION['sess_gc_txtACurrency'] = "";
											$_SESSION['sess_page_name'] = '';
											$_SESSION['sess_page_return_url_suc'] = '';
											$_SESSION['sess_page_return_url_fail'] = '';
											$_SESSION['sess_flag_failure'] = '';

											$location = "featuredcon.php?saleid=$saleid1&amt=$totalamt&ptype=gc&";
											header("location:$location");

											exit();
										}//end if
									}//end if
								}//end if
								if (isset($_SESSION['sess_flag_failure']) && $_SESSION['sess_flag_failure'] == false) {
									$gc_flag = false;
									$gc_err = ERROR_PAYMENT_PROCESS_FAILED;
								}//end else
							//calculation ends here
								?>

								<div class="full_width">
								<?php
								if ($gc_flag == false && $gc_err != '') {
									?>
										<div class="row main_form_inner">
											<div class="row warning"><?php echo $gc_err; ?></div>
										</div>
								<?php
								}//end if
								if (isset($gc_status) && $gc_status != 'success') {
									?>	
										<div class="row main_form_inner">
											<h4 class="subheader row"><?php echo HEADING_PAYMENT_DETAILS; ?></h4>
										</div>
										<div class="row main_form_inner">
											<label><?php echo TEXT_ITEM; ?></label>
											<?php echo TEXT_SALE_ITEM_ADDITION; ?>
										</div>
										<div class="row main_form_inner">
											<label><?php echo TEXT_AMOUNT; ?></label>
											<?php echo CURRENCY_CODE; ?><?php echo  $amount ?>
										</div>
										<div class="row main_form_inner">
											<label style="text-align:center; ">
												<?php echo MESSAGE_GOOGLE_CHECKOUT_INSTRUCTION; ?><br><br>
												<b><?php echo MESSAGE_WAITING_FOR_SECURE_PAYMENT_INTERFACE; ?>....</b><br>
												<br><br>
												<?php UseCase1(); ?>
											</label>
										</div>
										
									<?php
								}//end if
								?>
								</div>
								<?php
							}//end else
							?>	
                            </div>						
					</div>
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>	
					<div class="clear"></div>					
					
					<div class="col-lg-12 col-sm-12 col-md-12">
						<div class="subbanner">
							<?php include('./includes/sub_banners.php'); ?>
						</div>
					</div>
				</div>					
			</div>
		</div>  
	</div>
</div>
                
<?php require_once("./includes/footer.php"); ?>
