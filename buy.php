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
ob_start();
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

$message = "";
$var_flag = false;
$var_update_flag = false;
$var_error_message = "";
$var_show_flag = false;

if (!isset($_SESSION["guserid"]) || ($_SESSION["guserid"] == "")) {
    $message = ERROR_LOGIN_FIRST_TO_START;
}
else {
    $var_flag = true;
}

if(isset($_SESSION["guserid"]) && $_POST["postback"] != "Y") 

{
   $sql = "SELECT  vAddress1, vAddress2 , vCity, vState, vCountry, nZip, vPhone FROM " . TABLEPREFIX . "users where nUserId ='" .$_SESSION["guserid"]. "'";
   $res           = mysqli_query($conn, $sql);
   $userDetailRec = mysqli_num_rows($res);
   
   if($userDetailRec>0) 
   {
        $row = mysqli_fetch_array($res);
        $vAddress1 = $row["vAddress1"];
        $vAddress2 = $row["vAddress2"];
        $vCity     = $row["vCity"];
        $vState    = $row["vState"];
        $Country   = $row["vCountry"];
        $nZip      = $row["nZip"];
        $vPhone    = $row["vPhone"];
   }
}

if ($_GET["saleid"] != "") {
    $saleid = $_GET["saleid"];
}
else if ($_POST["saleid"] != "") {
    $saleid = $_POST["saleid"];
} 
if ($_GET["source"] != "") {
    $source = $_GET["source"];
}
else if ($_POST["source"] != "") {
    $source = $_POST["source"];
}

    $sql = "SELECT  nUserId,nQuantity,nShipping,nValue,nPoint FROM " . TABLEPREFIX . "sale where nSaleId ='" . addslashes($saleid) . "'";
    $result     = mysqli_query($conn, $sql);
    $numRows    = mysqli_num_rows($result); 
    if ($numRows>0) {
        $row = mysqli_fetch_array($result);
        $seller_id = $row["nUserId"];
        $nQuantity = $row["nQuantity"];
        
        if($nQuantity<=0)
        {
            
             echo "<script>alert('".str_replace('{quantity}',$nQuantity,ERROR_REQUESTED_ITEM_ALREADY_PURCHASED)."');</script>";
                header("location:swapitemdisplay.php?saleid=" . $saleid . "&source=" . $source . "&");
                exit();
        }
    }
        
if ($_POST["postback"] == "Y") {

    //get posted values from form
    $quantityREQD = $_POST["quantityREQD"];
    $quantityREQD = (abs($quantityREQD) > 0) ? abs($quantityREQD) : 1;
    $amount       = $_POST["amount"];
    $total        = $_POST["total"];
    $points       = $_POST["points"];
    $total_points = $_POST["total_points"];
    $nSaleId      = $saleid;
    $nUserId      = $_SESSION["guserid"];
    $vAddress1    = $_POST["vAddress1"];
    $vAddress2    = $_POST["vAddress2"];
    $vCity        = $_POST["vCity"];
    $vState       = $_POST["vState"];
    $Country      = $_POST["vCountry"];
    $nZip         = $_POST["nZip"];
    $vPhone       = $_POST["vPhone"];
    
    //make sure the quanity asked is not purchased by another user
    $sql = "SELECT  nUserId,nQuantity,nShipping,nValue,nPoint FROM " . TABLEPREFIX . "sale where nSaleId ='" . addslashes($nSaleId) . "'";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_array($result)) {
        $seller_id = $row["nUserId"];
        $nQuantity = $row["nQuantity"];
        $db_shipping = $row["nShipping"];
        $db_price = $row["nValue"];
        $db_point = $row["nPoint"];
        $db_total = $db_shipping + $db_price;
    }
    $flag = true;
    
       
    if ($EnablePoint != '0') {//if not price only
        $sql = "select nPoints from " . TABLEPREFIX . "usercredits where nUserId='".$nUserId."'";
        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if ($row = mysqli_fetch_array($res)){//user has purchased points
            if ($row['nPoints']<$total_points ){//if sufficient point is not available
                $flag = false;
                $message = str_replace('{point_name}', POINT_NAME, ERROR_AVAILABLE_POINT_IS_LESS);  
                //echo "<script>alert('".str_replace('{point_name}',POINT_NAME,ERROR_AVAILABLE_POINT_IS_LESS)."');< /script>";
            }
            else //sufficient point is available
                $flag = true;
        }
        else if ($total_points==0){//points not required for the purchase
            $flag = true;
        }
        else{//user has not purchased a point yet
            $flag = false;
            $message = str_replace('{point_name}', POINT_NAME, ERROR_CANNOT_COMPLETE_NO_POINT_AVAILABLE);            
        }
    }
    
    if(($vAddress1=="" || $vCity=="" || $vState=="" || $Country=="" || $nZip=="" || $vPhone=="") && $flag == true && $message=='')
    {
        $flag = false;
        $message = MANDATORY_FIELDS_COMPULSORY;
    } 
    
    if ($flag == true && $message==''){
    //if enough quanity of item is avalable
    if ($nQuantity >= $quantityREQD) {

        $total = $db_total * $quantityREQD;  //edited on Jan 13, 2005
        $total = round($total, 2);
        $total_points = $db_point * $quantityREQD;
        $total_points = round($total_points, 2);
		
		/*
		Commented on 09 Dec 2011
		Reduce the required qunatity when payment success
        //reduce requested quantity from the master table
        $sql = "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity - $quantityREQD where nSaleId ='" . addslashes($nSaleId) . "'";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        */
		
        $success_fee = DisplayLookUp('SuccessFee');//success fee for the transaction
        $free_trans_level = DisplayLookUp('freeTransactionsPerMonth');//no. of free trans per month
        $paid_trans = 'N';
        $this_user = $seller_id;
        $succ_trans_sql = "select s.nUserId from " . TABLEPREFIX . "saledetails sd left join " . TABLEPREFIX . "sale s on s.nSaleId = sd.nSaleId where sd.vSaleStatus>=2 and s.nUserId = '".$this_user."' and dDate > '".date('Y-m-').'01 00:00:00'."'
                                    union 
                               select st.nUserId from " . TABLEPREFIX . "swaptxn st where st.vStatus = 'A' and st.dDate > '".date('Y-m-').'01 00:00:00'."' and st.nUserId = '".$this_user."'
                                   union 
                               select st2.nUserReturnId from " . TABLEPREFIX . "swaptxn st2 where st2.vStatus = 'A' and st2.dDate > '".date('Y-m-').'01 00:00:00'."' and st2.nUserReturnId = '".$this_user."'";
        $succ_trans_res = mysqli_query($conn, $succ_trans_sql) or die(mysqli_error($conn));//to count the no. of trans
        if (mysqli_num_rows($succ_trans_res) >= $free_trans_level) $paid_trans = 'Y';

        if ($success_fee > 0 && $paid_trans == 'Y'){//if transaction fee needs to be paid make the entries
            mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "successfee (nUserId,nPurchaseBy,nProdId,nAmount,
                                nPoints,dDate,vType) VALUES ('" . $this_user . "','" . $_SESSION["guserid"] . "',
                                '" . $nSaleId . "','" . $success_fee . "','0',now(),'sa')") or die(mysqli_error($conn));
        }
        
        //transfer the requested quantity to the temp table
        $now = date('Y-m-d H:i:s');

        //checking escrow status
        if (DisplayLookUp('Enable Escrow') == 'Yes') {
            $SaleStatus = '1';
        }
        else {
            $SaleStatus = "4";
        }

        $sql = "insert into " . TABLEPREFIX . "saledetails(nSaleId,nUserId,nAmount,nPoint,dDate,nQuantity,vSaleStatus,vRejected,vAddress1,vAddress2,vCity,vState,vCountry,nZip,vPhone) values(";
        $sql .= "'" . addslashes($nSaleId) . "',";
        $sql .= "'" . $nUserId . "',";
        $sql .= "'" . $total . "',";
        $sql .= "'" . $total_points . "',";
        $sql .= "'" . $now . "',";
        $sql .= "'" . $quantityREQD . "',";
        $sql .= "'" . $SaleStatus . "',";
        $sql .= "'0',";
        $sql .= "'" . $vAddress1 . "',";
        $sql .= "'" . $vAddress2 . "',";
        $sql .= "'" . $vCity . "',";
        $sql .= "'" . $vState . "',";
        $sql .= "'" . $Country . "',";
        $sql .= "'" . $nZip . "',";
        $sql .= "'" . $vPhone."')";
        
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        
        if ($EnablePoint != '0') {
            $sql = "update " . TABLEPREFIX . "usercredits set nPoints = nPoints - ".$total_points." where nUserId='".$nUserId."'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));//decrementing the points for buyer

            $sql = "update " . TABLEPREFIX . "usercredits set nPoints = nPoints + ".$total_points." where nUserId='".$seller_id."'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));//incrementing the points for seller
        }
        //redirect to payment page
        //CZQ check for zero quantity,if zero, decrease the no from categories
        if (($nQuantity - $quantityREQD) == 0) { 
            $sql = "SELECT C.vRoute FROM " . TABLEPREFIX . "sale S inner join " . TABLEPREFIX . "category
									  C on S.nCategoryId = C.nCategoryId where nSaleId='" . addslashes($nSaleId) . "'";

            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                if ($row = mysqli_fetch_array($result)) {

                    $sql = "Update " . TABLEPREFIX . "category set nCount=nCount - 1 where nCategoryId IN(" . $row["vRoute"] . ")";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }
            }
        }

        header("location:buynext.php?saleid=" . $nSaleId . "&source=" . $source . "&amnt=" . $amount . "&tot=" . $total . "&points=" . $points . "&total_points=" . $total_points . "&reqd=" . $quantityREQD . "&dt=" . urlencode($now) . "&");
        exit();
    }
    else {
        //if enough quanity of item is not avalable
          echo "<script>alert('".str_replace('{quantity}',$nQuantity,ERROR_REQUESTED_ITEM_ALREADY_PURCHASED)."');</script>";
          header("location:swapitemdisplay.php?saleid=" . $nSaleId . "&source=" . $source . "&");
          exit();
        
    }
    }
}


if ($_POST["btnLogin"] != "") {
    $txtUserName = $_POST["txtUserName"];
    $txtPassword = $_POST["txtPassword"];

    $txtUserName = addslashes($txtUserName);
    $sqluserdetails = "SELECT nUserId, vEmail,vStatus  FROM " . TABLEPREFIX . "users WHERE vLoginName = '$txtUserName' AND vPassword = '" . md5($txtPassword) . "' ";
    $resultuserdetails = mysqli_query($conn, $sqluserdetails) or die(mysqli_error($conn));
    if (mysqli_num_rows($resultuserdetails) != 0) {
        $row = mysqli_fetch_array($resultuserdetails);
        if ($row["vStatus"] == "0") {
            $_SESSION["guserid"] = $row["nUserId"];
            $_SESSION["guseremail"] = $row["vEmail"];
            $_SESSION["gloginname"] = stripslashes($txtUserName);
            $var_flag = true;
        }
        else {
            $message = ERROR_ACCESS_DENIED_CONTACT_EMAIL."<a href=\"mailto:" . SITE_EMAIL . "\">" . SITE_EMAIL . "</a>";
            $var_flag = false;
        }
    }
    else {
        $message = ERROR_INVALID_USERNAME_PASSWORD;
        $var_flag = false;
    }
}
//if already set clean session
$_SESSION['sess_buyerid_escrow'] = '';

//get item equested details
$sql = "SELECT s.nSaleId,s.nUserId,s.vTitle,s.nQuantity,s.nShipping,s.nValue,s.nPoint,u.vLoginName,u.vAddress1,u.vAddress2,u.vCity,u.vState,u.vCountry,";
$sql .= "u.nZip,u.vFax,u.vEmail from " . TABLEPREFIX . "sale s inner join " . TABLEPREFIX . "users u ";
$sql .= " on s.nUserId = u.nUserId where s.nSaleId  = '$saleid' ";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) != 0) {
    while ($row = mysqli_fetch_array($result)) {
        $User = $row["nUserId"];

        //if escrow is disable
        if (DisplayLookUp('Enable Escrow') == 'No') {
            $_SESSION['sess_buyerid_escrow'] = $User;
        }

        $Title = $row["vTitle"];
        $QuantityAVL = $row["nQuantity"];
        $ShipingPrice = $row["nShipping"];
        $Price = $row["nValue"];
        $point = $row["nPoint"];
        $var_login = $row["vLoginName"];
        $var_address1 = $row["vAddress1"];
        $var_address2 = $row["vAddress2"];
        $var_city = $row["vCity"];
        $var_state = $row["vState"];
        $var_country = $row["vCountry"];
        $var_zip = $row["nZip"];
        $var_fax = $row["vFax"];
        $var_email = $row["vEmail"];
    }
}
//get total price
$total = $ShipingPrice + $Price;
if(strlen($Title)>60)
{
	$Title = substr($Title ,0 ,60)."...";
}

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function varify()
    {
        reqd= document.frmBuy.quantityREQD.value;
        avail = document.frmBuy.quantityAVL.value;
        if(isNaN(parseInt(reqd)) || isNaN(reqd) || reqd.substring(0,1) == " " || reqd.length <= 0 || parseInt(reqd) > parseInt(avail) || parseInt(reqd) < 1)
        {
            alert("<?php echo ERROR_QUANTITY_INVALID; ?>");
            document.frmBuy.quantityREQD.value="1";
        }
        else
        {
            document.frmBuy.quantityREQD.value=parseInt(reqd);
        }

        //store actual value
        var OrginalValue=parseFloat(document.frmBuy.amount.value)*parseFloat(document.frmBuy.quantityREQD.value);
        //rounded value for 2 decimal
        var RoundedValue = OrginalValue * Math.pow(10, 2);
        RoundedValue = Math.round(RoundedValue);
        RoundedValue = RoundedValue / Math.pow(10, 2);
        //store final value
        document.frmBuy.total.value=RoundedValue;
        
        <?php if ($EnablePoint != '0') { ?>
        var OrginalPoints = parseFloat(document.frmBuy.points.value)*parseFloat(document.frmBuy.quantityREQD.value);
        var RoundedPoints = Math.round(OrginalPoints * Math.pow(10, 2));
        RoundedPoints = RoundedPoints / Math.pow(10, 2);
        document.frmBuy.total_points.value=RoundedPoints;
        <?php } ?>
        
    }

    function proceed(cc){
        if(parseInt(document.frmBuy.quantityREQD.value) > parseInt(document.frmBuy.quantityAVL.value)){
            alert("<?php echo ERROR_QUANTITY_INVALID; ?>");

        }else{
            document.frmBuy.cctype.value=cc;
            frmBuy.submit();
        }
    }

    function clickConfirm(){
        
        var vAddress1   = document.getElementsByName("vAddress1")['0'].value;
        var vCountry    = document.getElementsByName("vCountry")['0'].value;
        var vState      = document.getElementsByName("vState")['0'].value;
        var vCity       = document.getElementsByName("vCity")['0'].value;
        var nZip        = document.getElementsByName("nZip")['0'].value;
        var vPhone      = document.getElementsByName("vPhone")['0'].value;
        
        if(vAddress1 == "" || vCountry == "" || vState == "" || vCity == "" || nZip == "" || vPhone == "")
        {
           alert("Please fill all mandatory fields!"); 
           return false;
        }
        document.frmBuy.postback.value='Y';
        document.frmBuy.method='post';
        document.frmBuy.submit();
    }
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
                <div class="col-lg-9">
					
					
					<!--<table width="100%"  border="0" cellspacing="0" cellpadding="2">
						<tr>
							<td class="link3">&nbsp;</td>
						</tr>
					</table>
					<table width="100%"  border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td class="heading" align="left"><?php //echo HEADING_PAYMENT_FORM; ?></td>
						</tr>
					</table>-->
					<?php if ($_SESSION["guserid"] == "") {
						include_once("./login_box.php");
					} ?>
					
					<!-- <div class="innersubheader">
                    	<h4><?php echo HEADING_SALES_DETAILS; ?></h4>
                    </div>  -->
					
					<div class="">
						
						<!-- <div class="col-lg-12 main_form_outer"> -->
                            <div class="col-lg-12 buy-form-inner">
                                <h2><?php echo HEADING_SALES_DETAILS; ?></h2>
							<form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
								<input type="hidden" name="cctype" id="cctype"  value="">
								<input type="hidden" name="source" value="<?php echo  $source ?>">
								<input type="hidden" name="saleid" value="<?php echo  $saleid ?>">
								<input type="hidden" name="postback" id="postback" value="">
								<?php
								if (isset($message) && $message != '') {
                                                                 if($flag == false || $var_flag == false){   
									?>
									<div class="row  alert alert-danger"><?php echo $message; ?></div>
								<?php } else if($flag == true || $var_flag == true){?>
                                                                <div class="row success"><?php echo $message; ?></div>           
                                                                <?php }}?>
                                                                <br>
								<?php
								if ($var_flag == true) {
									if ($_SESSION["guserid"] != $User) {
										//checking point enable in website
										if ($EnablePoint == '1' || $EnablePoint == '2') {
											$pointValue = round(($total / DisplayLookUp('PointValue')) * DisplayLookUp('PointValue2'), 2);
											//$showPrice = '&nbsp;&nbsp;(' . $pointValue . '&nbsp;' . POINT_NAME . ')';
										}
										?>
                                        <div class="clearfix">
								<div class="row main_form_inner title-class" style="width: 100%">
									<label><?php echo TEXT_TITLE; ?></label>
									<h4><?php echo  htmlentities($Title) ?></h4>
								</div>
                            </div>
                              <div class="clearfix custom-text-panel">  
								<div class="row main_form_inner">
									<label><?php echo TEXT_QUANTITY_AVAILABLE; ?></label>
									<input type="text" name="quantityAVL" class="form-control" id="quantityAVL" size="5" maxlength="3" readonly value="<?php echo  $QuantityAVL ?>">
								</div>
								<div class="row main_form_inner custom-text-panel">
									<label><?php echo TEXT_QUANTITY_REQUIRED; ?></label>
									<input type="text" name="quantityREQD" class="form-control" id="quantityREQD" size="5" maxlength="3"  value="1" onBlur="varify();">
								</div>
                            </div>
                              <div class="clearfix custom-text-panel">  
								<div class="row main_form_inner">
									<label><?php echo TEXT_AMOUNT; ?> [<?php echo TEXT_INCLUDING_SHIPPING; ?>]</label>
                                    <div class="grid-sec">
									<i><?php echo CURRENCY_CODE; ?> </i>
									<input type="text" name="amount" class="form-control" id="amount" size="5" maxlength="10"  value="<?php echo  $total ?>" readonly>
                                    <?php echo $showPrice; ?>
                                </div>
								</div>
								
								<?php if ($EnablePoint != '0') { ?>
								<div class="row main_form_inner">
									<label><?php echo POINT_NAME; ?></label>
									<input type="text" name="points" class="form-control" id="points" size="5" maxlength="10"  value="<?php echo  $point ?>" readonly>
									<?php echo $showPrice; ?>
								</div>
								<?php } ?>
								</div>
                                 <div class="clearfix custom-text-panel">  
								<div class="row main_form_inner">
									<label><?php echo TEXT_TOTAL_AMOUNT; ?></label>
                                    <div class="grid-sec">
									<i><?php echo CURRENCY_CODE; ?> </i>
                                    <input type="text" name="total" class="form-control" id="total" size="5" maxlength="10"  value="<?php echo  $total ?>" readonly>
                                </div>
								</div>
								
								<?php if ($EnablePoint != '0') { ?>
								<div class="row main_form_inner">
									<label><?php echo str_replace('{point_name}',POINT_NAME,TEXT_TOTAL_POINTS); ?></label>
									<input type="text" name="total_points" class="form-control" id="total_points" size="5" maxlength="10"  value="<?php echo  $point ?>" readonly>
								</div>
								<?php } ?>
                            </div>
								
                                    <div class="clearfix">
                                         <div class="row main_form_inner addr-head">
                                            <h4><?php echo SHIPPING_ADDRESS; ?></h4>
                                        </div>
                                    </div> 
                                <div class="clearfix">                     
                                    <div class="row main_form_inner">
    									<label><?php echo TEXT_ADDRESS_LINE1; ?> <span class='warning'>*</span></label>
    									<input type="text" class="comm_input form-control" name="vAddress1" size="40" maxlength="100" value="<?php echo $vAddress1; ?>" />
    								</div>
                           
                                	<div class="row main_form_inner">
										<label><?php echo TEXT_ADDRESS_LINE2; ?></label>
										<input type="text" class="comm_input form-control" name="vAddress2" size="40" maxlength="100" value="<?php echo $vAddress2; ?>" />
									</div>
                                </div>
                                <div class="clearfix">
									<div class="row main_form_inner">
										<label><?php echo TEXT_COUNTRY; ?><span class='warning'>*</span></label>
										<select name="vCountry" class="comm_input form-control"  onchange="checkCountry('parent',this,0)">
											<?php include('./includes/country_select.php'); ?>
										</select>
									</div>
                               
									<div class="row main_form_inner">
										<label><?php echo TEXT_STATE; ?><span class='warning'>*</span></label>
										<input type="text" class="comm_input form-control" name="vState" size="40" maxlength="100" value="<?php echo $vState; ?>" />
									</div>
                                </div>
                                <div class="clearfix">
									<div class="row main_form_inner">
										<label><?php echo TEXT_CITY; ?><span class='warning'>*</span></label>
										<input type="text" class="comm_input form-control" name="vCity" size="40" maxlength="100" value="<?php echo $vCity; ?>" />
									</div>
                                
									<div class="row main_form_inner">
										<label><?php echo TEXT_ZIP; ?><span class='warning'>*</span></label>
										<input type="text" class="comm_input form-control jQNumericOnly" name="nZip" size="40" maxlength="100" value="<?php echo $nZip; ?>" />
									</div>
                                </div>
                                <div class="clearfix">
									<div class="row main_form_inner">
										<label><?php echo TEXT_PHONE; ?><span class='warning'>*</span></label>
										<input type="text" class="comm_input form-control jQNumericOnly" name="vPhone" size="40" maxlength="50" value="<?php echo $vPhone; ?>" />
									</div>
                                 </div>
                                 <div class="clearfix">                              
								<div class="button-bottom text-center">
							
										<input type="button" name="btConfirm" id="btConfirm" class="subm_btt" value="<?php echo BUTTON_CONFIRM; ?>" onClick="clickConfirm();">
									
								</div>
                                </div>
                                <div class="clearfix">
								<div class="row main_form_inner">
									<?php
										}
										else {
											echo '<tr align="center"  class="warning"><td colspan="3"><b>'.ERROR_ITEMP_POSTED_BY_YOU_CANNOT_PAY.'</b></td></tr>';
										}
									}
									else {
										echo '<tr align="center"  class="warning"><td colspan="3"><b>' . $var_error_message . '</b></td></tr>';
									}
									?>
								</div>
                            </div>
                            </div>
                            </div>
							</form>
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

 <script>
    var jqr = jQuery.noConflict();
    jqr(document).ready(function() {
        jqr('.jQNumericOnly').keypress(function(e) { 
          var key_codes = [45, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 0, 8];
            if (!(jqr.inArray(e.which, key_codes) >= 0)) {
                e.preventDefault();
            }
          });
    });
</script>

<?php require_once("./includes/footer.php"); ?>