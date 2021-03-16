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

$message = "";
if (!isset($_SESSION["guserid"]) || ($_SESSION["guserid"] == "")) {
    $message = ERROR_LOGIN_FIRST_TO_START;
}//end if
//store success fee pending id in session
$_SESSION['sess_success_fee_id'] = $_GET['nSId'];

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
				<div class="full-width">
					<div class="col-lg-12">
					<div class="innersubheader2">
							<h3><?php echo HEADING_PAYMENT_FORM; ?></h3>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
					
				<div class="full-width">
					<div class="col-lg-12">
						<div class="full_width">
							<form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" >
							  <?php
								if (isset($message) && $message != '') {
									?>
							<div class="row warning"><?php echo $message; ?></div>
								<?php }//end if ?>
							
							
							<div class="row main_form_inner">
								<label><?php echo TEXT_SUCCESS_FEE_FOR_EACH_TRANSACTION; ?> </label>
								<?php echo CURRENCY_CODE; ?><?php echo DisplayLookUp('SuccessFee'); ?>
							</div>
							<?php
							if (DisplayLookUp('paypalsupport') == "YES") {
								?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_PAYPAL; ?></label>
								<a href="success_fee_pp.php"><img src="images/x-click-but20.gif" border="0" alt=""></a>
							</div>
								<?php
							}//end if
							?>
                                                                        
							<?php
							if (DisplayLookUp('Enable Escrow') == 'Yes') {                                                                     
								if (PAYMENT_CURRENCY_CODE == 'USD') {
									if (DisplayLookUp('authsupport') == "YES") {
										?>
							<div class="row main_form_inner">
								<h4><?php echo TEXT_USE_CREDIT_CARDS; ?></h4>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_CREDIT_CARDS; ?></label>
								<a href="success_fee_cc.php?payMethod=cc"><img src="images/cc.jpg" border="0" alt=""></a>
							</div>
										<?php
									}//end if
								}//end if
								if (DisplayLookUp('enablestripe') == "Y") {
									?>
									<div class="row main_form_inner">
								<label><?php echo TEXT_USE_STRIPE; ?></label>
								<a href="success_fee_cc.php?payMethod=sp"><img src="images/cc.jpg" border="0" alt=""></a>
							</div>
								<?php
								}//end if
								if (DisplayLookUp('yourpaysupport') == "YES") {
									?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_YOURPAY; ?></label>
								<a href="success_fee_cc.php?payMethod=yp"><img src="images/cc.jpg" border="0" alt=""></a>
							</div>
									<?php
								}//end if
								/*if (DisplayLookUp('googlesupport') == "YES") {
									?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_GOOGLE_CHECKOUT; ?></label>
								<a href="<?php echo $secureserver; ?>/success_fee_cc.php?payMethod=gc"><img src="images/checkout.gif" border="0" alt=""></a>
							</div>
									<?php
								}//end if*/
								if (DisplayLookUp('enableworldpay') == "Y") {
									?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_WORLDPAY; ?></label>
								<a href="success_fee_wp.php"><img src="images/cc.jpg" width="180" height="31" title="" border="0" alt=""></a>
							</div>
									<?php
								}//end if
								if (DisplayLookUp('enablebluepay') == "Y") {
									?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_BLUEPAY; ?></label>
								<a href="success_fee_cc.php?payMethod=bp"><img src="images/cc.jpg" width="180" height="31" title="" border="0" alt=""></a>
							</div>
									<?php
								}//end if
								if (DisplayLookUp('otherpayment') == 'YES') {
									?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_CASHIERS_CHECK; ?></label>
								<a href="success_fee_cc.php?payMethod=ca"><img src="images/cashierscheque.gif" border="0"></a>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_BUSINESS_CHECK; ?></label>
								<a href="success_fee_cc.php?payMethod=bu"><img src="images/businesscheque.gif" border="0"></a>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_PERSONAL_CHECK; ?></label>
								<a href="success_fee_cc.php?payMethod=pc"><img src="images/personalcheck.gif" border="0"></a>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_MONEY_ORDER; ?></label>
								<a href="success_fee_cc.php?payMethod=mo"><img src="images/moneyorder.gif" border="0"></a>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_WIRETRANSFER; ?></label>
								<a href="success_fee_cc.php?payMethod=wt"><img src="images/wireftransfer.gif" border="0"></a>
							</div>
									<?php }//end if
							}//end if ?>
							
							</form>
						</div>
					</div>
				</div>			
				
				</div>
				</div>
				<div class="full-width subbanner">
					<div class="col-lg-12">
						<?php include('./includes/sub_banners.php'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>
