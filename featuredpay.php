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

$message = "";
$act = $_GET["act"];
if (!isset($_SESSION["guserid"]) || ($_SESSION["guserid"] == "")) {
    $message = ERROR_LOGIN_FIRST_TO_START;
}//end if
//back from payment
$saleid = $_SESSION["gsaleextraid"];
$featured = "0";
$commission = "0";
$sql = "Select nFeatured,nCommission from " . TABLEPREFIX . "saleextra where nSaleextraId='$saleid'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
while ($row = mysqli_fetch_array($result)) {
    $featured = $row["nFeatured"];
    $commission = $row["nCommission"];
}//end while loop

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function proceed(cc)
    {
        document.frmBuy.cctype.value=cc;
        //alert(document.frmBuy.cctype.value);
        frmBuy.submit();
    }//end funciton
</script>
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
					
				</div>
				<div class="clearfix">
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 pay-form">
						<h4><?php echo HEADING_PAYMENT_FORM; ?></h4>
						<form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" >
							<?php
							if (isset($message) && $message != '') {
								?>
							<div class="row warning"><?php echo $message; ?></div>
							<?php }//end if ?>
							
							<div class="row main_form_inner">
								<label><?php echo TEXT_FEATURED_ITEM_ADDITION; ?> &nbsp; ( <?php echo CURRENCY_CODE; ?> )</label>
								<input type="text" name="feaAmount" class="form-control" id="Amount" size="3" maxlength="3" readonly value="<?php echo  $featured ?>">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_COMMISSION_FOR_ADDITION; ?>  &nbsp; ( <?php echo CURRENCY_CODE; ?> )</label>
								<input type="text" name="commAmount" class="form-control" id="Amount" size="3" maxlength="3" readonly value="<?php echo  round($commission, 2) ?>">
							</div>
							<?php
						if (DisplayLookUp('paypalsupport') == "YES") {
							?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_PAYPAL; ?></label>
								<a href="featuredpaypp.php"><img src="images/x-click-but20.gif" border="0" alt=""></a>
							</div>
							
							<?php
						}//end if
						//checking point enable in website
						if (ENABLE_POINT != '0') {
							?>
							<div class="row main_form_inner">
								<h5><?php echo str_replace('{point_name}',POINT_NAME,TEXT_USE_REEDEEM_POINTS); ?></h5>
							</div>
							<div class="row main_form_inner">
								<label><?php echo str_replace('{point_name}',POINT_NAME,TEXT_USE_POINTS); ?></label>
								<a href="featuredpaycc.php?paytype=rp"><img src="images/redeempoints.jpg" border="0" alt=""></a>
							</div>													
							<?php
							}//end checking point if
							?>
							<?php
							if (DisplayLookUp('Enable Escrow') == 'Yes') {
								if (PAYMENT_CURRENCY_CODE == 'USD') {
									if (DisplayLookUp('authsupport') == "YES") {
										?>

							<div class="row main_form_inner">
								<h5><?php echo TEXT_USE_CREDIT_CARDS; ?></h5>
							</div>
							
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_CREDIT_CARDS; ?></label>
								<a href="featuredpaycc.php?paytype=cc"><img src="images/cc.jpg" border="0" alt=""></a>
							</div>
									<?php
								}//end if
							}//end if
							if (DisplayLookUp('enablestripe') == "Y") {
									?>
									<div class="row main_form_inner">
								<label><?php echo TEXT_USE_STRIPE; ?></label>
								<a href="featuredpaycc.php?paytype=sp"><img src="images/cc.jpg" border="0" alt=""></a>
							</div>
								<?php
								}//end if
							if (DisplayLookUp('yourpaysupport') == "YES") {
								?>								
							<div class="row main_form_inner">
								<h5><?php echo TEXT_USE_CREDIT_CARDS; ?></h5>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_YOURPAY; ?></label>
								<a href="featuredpaycc.php?paytype=yp"><img src="images/cc.jpg" border="0" alt=""></a>
							</div>
								<?php
							}//end if
							if (DisplayLookUp('enablebluepay') == "Y") {
								?>
							<div class="row main_form_inner">
								<h5><?php echo TEXT_USE_CREDIT_CARDS; ?></h5>
							</div>
							
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_BLUEPAY; ?></label>
								<a href="featuredpaycc.php?paytype=bp"><img src="images/cc.jpg" border="0" alt=""></a>
							</div>
									<?php
								}//end if
								/*if (DisplayLookUp('googlesupport') == "YES") {
									?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_GOOGLE_CHECKOUT; ?></label>
								<a href="<?php echo $secureserver; ?>/featuredpaycc.php?paytype=gc"><img src="images/checkout.gif" border="0" alt=""></a>
							</div>
									<?php
								}//end if*/
								if (DisplayLookUp('enableworldpay') == "Y") {
									?>								
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_WORLDPAY; ?></label>
							<a href="featuredpaywp.php"><img src="images/cc.jpg" width="180" height="31" title="" border="0" alt=""></a>
						</div>
						<?php
							}//end if
							if (DisplayLookUp('otherpayment') == 'YES') {
						?>
						<div class="row main_form_inner">
							<h5><?php echo TEXT_OTHER_PAYMENTS; ?></h5>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_CASHIERS_CHECK; ?></label>
							<a href="featuredpayothers.php?paytype=ca"><img src="images/cashierscheque.gif" border="0"></a>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_BUSINESS_CHECK; ?></label>
							<a href="featuredpayothers.php?paytype=bu"><img src="images/businesscheque.gif" border="0"></a>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_PERSONAL_CHECK; ?></label>
							<a href="featuredpayothers.php?paytype=pc"><img src="images/personalcheck.gif" border="0"></a>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_MONEY_ORDER; ?></label>
							<a href="featuredpayothers.php?paytype=mo"><img src="images/moneyorder.gif" border="0"></a>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_WIRETRANSFER; ?></label>
							<a href="featuredpayothers.php?paytype=wt"><img src="images/wireftransfer.gif" border="0"></a>
						</div>
								<?php }//end if
						}//end if ?>
					</form>
					</div>	
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>			
				</div>
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>	
			</div>
		</div>  
	</div>
</div>


<?php require_once("./includes/footer.php"); ?>

