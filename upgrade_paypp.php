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
    header('./index.php');
    exit();
}//end if
//checking escrow status
//if (DisplayLookUp('Enable Escrow') == 'Yes') {
if (DisplayLookUp('plan_system')!='yes') {
    if ($txtPaypalSandbox == "TEST") {
        $paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        $paypalbuttonurl = "https://www.sandbox.paypal.com/en_US/i/btn/x-click-but23.gif";
    }//endi f
    else {
        $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
        $paypalbuttonurl = "https://www.paypal.com/en_US/i/btn/x-click-but23.gif";
    }//end else
}//end if
else {
    if ($txtPaypalSandbox == "TEST") {
        $paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        $paypalbuttonurl = "https://www.sandbox.paypal.com/en_US/i/btn/x-click-but20.gif";
    }//endi f
    else {
        $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
        $paypalbuttonurl = "https://www.paypal.com/en_US/i/btn/x-click-but20.gif";
    }//end else
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
				<!-- <h4><?php //echo HEADING_PAYMENT_PROCESS; ?></h4> -->
			</div>
			
			<div class="row">
				<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
				<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
					<h4><?php echo HEADING_PAYMENT_PROCESS; ?></h4>
						<div class="clear">&nbsp;</div>
						<div class="row main_form_inner">
							<div class="col-lg-6 col-sm-6 col-xs-12">
								<?php
									$showPlanOrReg = '';
									//... setting selected plan value
									$var_amount = $_SESSION['sess_Plan_Amt'];
									//if regmode is paid and escrow is disabled
									//if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
									if(DisplayLookUp('15')!='1' && DisplayLookUp('plan_system')=='yes'){
										switch ($_SESSION['sess_Plan_Mode']) {
											case "M":
												$year_show = TEXT_PER_MONTH;
												break;
	
											case "Y":
												$year_show = TEXT_PER_YEAR;
												break;
	
											case "F":
												$year_show = TEXT_FREE;
												break;
										}//end switch
										
	
										$uname = $_SESSION['sess_uName'];
										$var_fname = $_SESSION["guserFName"];
									  
										$condReg = "where plan_id='" . $_SESSION['ChangePlanId'] . "' and lang_id = '" . $_SESSION['lang_id'] . "'";
										$PlanName = fetchSingleValue(select_rows(TABLEPREFIX . 'plan_lang', 'vPlanName', $condReg), 'vPlanName');
										$showPlanOrReg = '<div class="col-lg-6 col-sm-6 col-xs-12">'.TEXT_PLAN_NAME.'</div><div class="col-lg-6 col-sm-6 col-xs-12"> : <b>' . $PlanName . ' ( ' . $year_show . ' )</b></div>';
	
										//passing custom field for paypal ipn updation
										$customValues = $_SESSION['ChangePlanId'] . '-' . $_SESSION['guserid'] . '-' . $_SESSION['sess_PlanId'] . '-' . $_SESSION['sess_Plan_Amt'] . '-' .
												$_SESSION['sess_Plan_Mode'];
									}//end if
									?>
									
									<?php echo TEXT_MAKE_PAYMENTS_PAYPAL; ?>
							</div>
							<div class="col-lg-6 col-sm-6 col-xs-12"><img src="./images/paypal_small.jpg" title="" border="0" ></div>
						</div>
						
						<div class="clear">&nbsp;</div>
						<div class="row main_form_inner">
							<?php echo $showPlanOrReg; ?>
							
						</div>
						<div class="clear">&nbsp;</div>
						<div class="row main_form_inner">
							<div class="col-lg-6 col-sm-6 col-xs-12"><?php echo TEXT_AMOUNT; ?> - <?php echo CURRENCY_CODE; ?></div>
							<div class="col-lg-6 col-sm-6 col-xs-12">:<b><?php echo $var_amount; ?></b></div>
						</div>
						<div class="clear">&nbsp;</div>
						<div class="row main_form_inner">
							<div class="col-lg-12 col-sm-12 col-xs-12" align="center">
								 <?php
									//checking escrow status
									//if (DisplayLookUp('Enable Escrow') != 'Yes') {
									if (DisplayLookUp('plan_system')=='yes') {
										?>
										<form action="<?php echo $paypalurl ?>" method="post" name="frmPayment">
											<input type="hidden" name="cmd" value="_xclick-subscriptions">
											<input type="hidden" name="bn" value="PP-SubscriptionsBF">
											<input type="hidden" name="src" value="1">
											<input type="hidden" name="sra" value="1">
											<input type="hidden" name="business" value="<?php echo $txtPaypalEmail ?>">
											<input type="hidden" name="item_name" value="<?php echo SITE_NAME; ?> <?php echo TEXT_PLAN_NAME; ?>">
											<input type="hidden" name="item_number" value="SR1">
											<input type="hidden" name="no_shipping" value="1">
											<input type="hidden" name="rm" value="2">
											<input type="hidden" name="notify_url" value="<?php echo SECURE_SITE_URL; ?>/upgrade_payipn.php">
											<input type="hidden" name="custom" value="<?php echo $customValues; ?>">
											<input type="hidden" name="return" value="<?php echo SITE_URL; ?>/upgrade_success.php">
											<input type="hidden" name="cancel_return" value="<?php echo SITE_URL; ?>/upgrade_failure.php">
											<input type="hidden" name="no_note" value="1">
											<input type="hidden" name="currency_code" value="<?php echo PAYMENT_CURRENCY_CODE; ?>">
											<input type="hidden" name="on0" value="TempId">
											<input type="hidden" name="os0" maxlength="200" value="<?php echo $var_tmpid ?>">
											<input type="hidden" name="on1" value="UserName">
											<input type="hidden" name="os1" maxlength="200" value="<?php echo htmlentities($uname) ?>">
											<!--<input type="hidden" name="custom" value="<?php echo $var_fname; ?>">-->
											<input type="hidden" name="a3" value="<?php echo  round($var_amount, 2); ?>">
											<input type="hidden" name="p3" value="1">
											<input type="hidden" name="t3" value="M">
											<input type="hidden" name="bn" value="armiasystems_shoppingcart_wps_us">
											<input type="image" src="<?php echo $paypalbuttonurl; ?>" border="0" name="submit" alt="">
										</form>
											<?php
										}//end else
										?>
							</div>
						</div>
						<div class="clear">&nbsp;</div>
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