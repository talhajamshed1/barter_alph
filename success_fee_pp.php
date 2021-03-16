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
include ("./includes/session_check.php");

include_once('./includes/gpc_map.php');

$txtPaypalEmail = DisplayLookUp('paypalemail');
$txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
$txtPaypalSandbox = DisplayLookUp('paypalmode');
$paypalenabled = DisplayLookUp('paypalsupport');

if ($paypalenabled != "YES") {
    header('location:index.php');
    exit();
}
if ($txtPaypalSandbox == "TEST") {
    $paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    $paypalbuttonurl = "https://www.sandbox.paypal.com/en_US/i/btn/x-click-but23.gif";
}//end if
else {
    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
    $paypalbuttonurl = "https://www.paypal.com/en_US/i/btn/x-click-but23.gif";
}//end else
//select value from succes
$sqlSuccess = mysqli_query($conn, "select nProdId,nAmount,nPoints from " . TABLEPREFIX . "successfee where nSId='" . $_SESSION['sess_success_fee_id'] . "'") or die(mysqli_error($conn));
if (mysqli_num_rows($sqlSuccess) > 0) {
    $passProdId = mysqli_result($sqlSuccess, 0, 'nProdId');
    $passAmount = mysqli_result($sqlSuccess, 0, 'nAmount');
    $passPoints = mysqli_result($sqlSuccess, 0, 'nPoints');
}//end if
//passing custom field for paypal ipn updation
$customValues = $_SESSION["guserid"] . '-' . round(DisplayLookUp('SuccessFee'), 2) . '-' . $passProdId . '-' . $_SESSION["guserFName"] . '-' .
        $_SESSION["guseremail"] . '-' . $passAmount . '-' . $passPoints . '-' . $_SESSION['sess_success_fee_id'];

include_once('./includes/title.php');
?>
<body onLoad="javascript:document.frmPay.submit();">
<?php include_once('./includes/top_header.php'); ?>
<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
                <div class="col-lg-9">
					<div class="innersubheader">
                    	<h4><?php echo HEADING_PAYMENT_PROCESS; ?></h4>
                    </div>
					
					<div class="row">
						<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>
						<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
							<form name="frmPay"  action="<?php echo  $paypalurl ?>" method="post">
								<input type="hidden" name="cmd" value="_xclick">
								<input type="hidden" name="business" value="<?php echo $txtPaypalEmail; ?>">
								<input type="hidden" name="on0" value="TempId">
								<input type="hidden" name="os0" maxlength="200" value="<?php echo  $_SESSION["guserid"] ?>">
								<input type="hidden" name="item_name" value="<?php echo TEXT_SUCCESS_FEE; ?>">
								<input type="hidden" name="item_number" value="<?php echo  $_SESSION['sess_success_fee_id'] ?>">
								<input type="hidden" name="amount" value="<?php echo  round(DisplayLookUp('SuccessFee'), 2) ?>">
								<input type="hidden" name="no_shipping" value="1">
								<input type="hidden" name="rm" value="2">
								<input type="hidden" name="notify_url" value="<?php echo SECURE_SITE_URL; ?>/sucess_fee_ipn.php">
								<input type="hidden" name="return" value="<?php echo SECURE_SITE_URL; ?>/sucess_fee_success.php">
								<input type="hidden" name="cancel_return" value="<?php echo SECURE_SITE_URL; ?>/sucess_fee_failure.php">
								<input type="hidden" name="no_note" value="1">
								<input type="hidden" name="custom" value="<?php echo $customValues;?>" >
								<input type="hidden" name="currency_code" value="<?php echo PAYMENT_CURRENCY_CODE; ?>">
								<input type="hidden" name="bn" value="armiasystems_shoppingcart_wps_us">
								<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but23.gif" border="0" name="submit" alt="" height="0" width="0">
							</form>
							<script language="javascript1.1" type="text/javascript">
								document.frmPay.submit();
							</script>
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