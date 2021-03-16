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
$paypalenabled = DisplayLookUp('paypalsupport');

if ($paypalenabled != "YES") {
    header('./index.php');
    exit();
}//end if

if ($txtPaypalSandbox == "TEST") {
    $paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    $paypalbuttonurl = "https://www.sandbox.paypal.com/en_US/i/btn/x-click-but23.gif";
}//end if
else {
    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
    $paypalbuttonurl = "https://www.paypal.com/en_US/i/btn/x-click-but23.gif";
}//end else

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
				<h4><?php echo HEADING_PAYMENT_FORM; ?></h4>
			</div>
			
			<div class="row">
				<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
				<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<div class="row main_form_inner">
                            <!-- <p>Please wait..Redirecting to PayPal website.</p> -->
							<label>
								<?php
								if ($_GET["tmpid"] != "") {
									$var_id = $_GET["tmpid"];
								}//end if
								else if ($_POST["tmpid"] != "") {
									$var_id = $_POST["tmpid"];
								}//end else if
								$var_id = $_SESSION["gstempid"];

								$cc_err = "";
								$cc_flag = false;

								$sql = "Select ST.nTempId,ST.nSwapId,ST.nUserId,ST.nAmount,ST.vMethod,ST.vMode,ST.vPostType,ST.dDate,S.vTitle from ";
								$sql .= " " . TABLEPREFIX . "swaptemp ST inner join " . TABLEPREFIX . "swap S  on ST.nSwapId = S.nSwapId where nTempId ='" . $var_id . "'";

								$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

								if (mysqli_num_rows($result) > 0) {
									$row = mysqli_fetch_assoc($result);
									$var_title = $row["vTitle"];
									$var_amount = $row["nAmount"];
									$var_posttype = $row["vPostType"];
									$var_swapid = $row["nSwapId"];
									$cc_flag = true;
								}//end if

								if ($cc_flag == true) {
									?>

									<h3 class="subheader row"><?php echo HEADING_PAYMENT_DETAILS; ?></h3>
								
									<div class="row main_form_inner">
										<label><?php echo TEXT_TITLE; ?></label>
										<?php echo  htmlentities($var_title) ?>
									</div>								
									<div class="row main_form_inner">
										<label><?php echo TEXT_AMOUNT; ?></label>
										<?php echo CURRENCY_CODE; ?><?php echo  $var_amount ?>
									</div>	
									<div class="row main_form_inner">
										<label><?php echo TEXT_TYPE; ?></label>
										<?php echo  $var_posttype ?>
									</div>
									<form id="frmPay" name="frmPay"  action="<?php echo  $paypalurl ?>" method="post">
										<input type="hidden" name="cmd" value="_xclick">
										<input type="hidden" name="business" value="<?php echo  $txtPaypalEmail ?>">
										<input type="hidden" name="item_name" value="<?php echo  htmlentities($var_title) ?>">
										<input type="hidden" name="item_number" value="<?php echo  $var_id ?>">
										<input type="hidden" name="amount" value="<?php echo  round($var_amount, 2) ?>">
										<input type="hidden" name="no_shipping" value="1">
										<input type="hidden" name="rm" value="2">
										<input type="hidden" name="notify_url" value="<?php echo SECURE_SITE_URL; ?>/swapipn.php">
										<input type="hidden" name="return" value="<?php echo SECURE_SITE_URL; ?>/swapsuccess.php">
										<input type="hidden" name="cancel_return" value="<?php echo SECURE_SITE_URL; ?>/failure.php">
										<input type="hidden" name="no_note" value="1">
										<input type="hidden" name="currency_code" value="<?php echo PAYMENT_CURRENCY_CODE; ?>">
										<input type="hidden" name="on0" value="SwapId">
										<input type="hidden" name="os0" maxlength="200" value="<?php echo  $var_swapid ?>">
										<input type="hidden" name="on1" value="posttype">
										<input type="hidden" name="os1" maxlength="200" value="<?php echo  $var_posttype ?>">
										<input type="hidden" name="bn" value="armiasystems_shoppingcart_wps_us">
										<!-- <input type="image" src="<?php echo $paypalbuttonurl; ?>" border="0" name="submit" alt="Submit" height="0" width="0">
										<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but23.gif" border="0" name="submit" alt="Submit" height="0" width="0"> -->
										<input id="redirect-to-paypal" type="image" src="<?php echo $paypalbuttonurl; ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
									    <img  alt="Submit" border="0" src="<?php echo $paypalbuttonurl; ?>" width="1" height="1">

										
									</form>
									<?php
								}//end if
								else {
									header("location:" . SITE_URL . "/index.php?paid=no");
									exit();
								}//end else
								?>
							</label>
					</div>
				</div>
				<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>	
				
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