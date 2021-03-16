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
$message = "";
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');


$approval_tag = DisplayLookUp('userapproval');

$var_id = "";
$var_cctype = "";
$var_mainflag = true;
$show_cc = false;

    //...Selected plan
$_SESSION['ChangePlanId'] = $_POST['ddlPlan'];
$condReg = "where nPlanId='" . $_SESSION['ChangePlanId'] . "'";
$PlanMode = fetchSingleValue(select_rows(TABLEPREFIX . 'plan', 'vPeriods', $condReg), 'vPeriods');
$_SESSION['sess_Plan_Amt'] = fetchSingleValue(select_rows(TABLEPREFIX . 'plan', 'nPrice', $condReg), 'nPrice');
$_SESSION['sess_Plan_Mode'] = $PlanMode;    

//if regmode is paid and escrow is disabled
//if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
if (DisplayLookUp('15') != '1' && DisplayLookUp('plan_system')=='yes') {

	$var_real_amount = $_SESSION['sess_Plan_Amt'];
	$var_amount = $_SESSION['sess_Plan_Amt'];

    //checking payment mode
	if ($PlanMode == 'F') {
		$today = date("Y-m-d");
        //update member tbl in new plan
		mysqli_query($conn, "update " . TABLEPREFIX . "users set nPlanId='" . $_SESSION['ChangePlanId'] . "',vStatus='0',dPlanExpDate='0000-00-00'
			where nUserId='" . $_SESSION['guserid'] . "'	and
			nPlanId='" . $_SESSION['sess_PlanId'] . "'") or die(mysqli_error($conn));

        //update old plan status in payment table
		mysqli_query($conn, "update " . TABLEPREFIX . "payment set vPlanStatus='I',vComments='Inactive on $today ' where
			nUserId='" . $_SESSION['guserid'] . "'	and vPlanStatus='A' and
			nPlanId='" . $_SESSION['sess_PlanId'] . "'") or die(mysqli_error($conn));
        //insert new entry
		$sqlPayment = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId,
		nSaleId,vInvno,vPlanStatus,nPlanId) VALUES ('R', '$txnid', '" . $_SESSION['sess_Plan_Amt'] . "',
		'free',now(), '" . $_SESSION["guserid"] . "',
		'','$Inv_id','A','" . $_SESSION['ChangePlanId'] . "')";
		$result = @mysqli_query($conn, $sqlPayment) or die(mysqli_error($conn));

		header("location:plan_upgrade_success.php");
		exit();
    }//end if
}//end if

if ($_POST["postback"] == "Y" && $var_mainflag == true) {
	if ($_POST['cctype'] == "cc") {
		header("location:upgrade_paycc.php?paytype=cc");
		exit();
    }//end if
    else if ($_POST['cctype'] == "sp") {
		header("location:upgrade_paycc.php?paytype=sp");
		exit();
    }//end if
    else {
    	header("location:upgrade_paypp.php");
    	exit();
    }//end else if
}//end if

include_once('./includes/title.php');
$message = ($_SESSION['succ_msg'] != '') ? $_SESSION['succ_msg'] : $message;
?>
<script language="javascript" type="text/javascript">
	function confirmPay(i)
	{
		if(i==0)
		{
			document.frmPayUpgrade.cctype.value='pp';
        }//end if
        else if(i==1)
        {
        	document.frmPayUpgrade.cctype.value='cc';
        }//end else if
        else if(i==2)
        {
        	document.frmPayUpgrade.cctype.value='sp';
        }//end else if
        document.frmPayUpgrade.postback.value='Y';
        document.frmPayUpgrade.submit();
    }//end function

</script>
<body onLoad="timersOne();">
	<?php include_once('./includes/top_header.php'); ?>

	<div class="homepage_contentsec">
		<div class="container">
			<div class="row">
				<div class="col-lg-3">
					<?php include_once("./includes/usermenu.php"); ?>
				</div>
				<div class="col-lg-9">				
					<div class="innersubheader">
						<h4><?php echo HEADING_PAYMENT_FORM; ?></h4>
					</div>
					<div class="row">
						<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
						<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
							<form name="frmPayUpgrade" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
								<input type="hidden" name="ddlPlan" value="<?php echo $_POST['ddlPlan']; ?>">
								<?php
								if (isset($var_mainflag) && $var_mainflag == false) {
									?>
									<div class="row warning"><?php echo ERROR_AMOUNT_MISMATCHING_TRY_AGAIN; ?></div>
								<?php }//end if?>
								<div class="row main_form_inner">
									<label><?php echo TEXT_SELECT_PAYMENT_OPTION_FOR_MEMBERSHIP.' '.SITE_URL; ?>. <?php echo TEXT_PAMENT_VERIFICATION_RESTRICTIONS; ?></label>
								</div>

								<?php
								if (DisplayLookUp('paypalsupport') == "YES") {
									?>
									<input type="hidden" NAME="cctype" id="cctype" VALUE="">
									<input type="hidden" NAME="id" id="id" VALUE="<?php echo  $var_id ?>">
									<input type="hidden" NAME="mode" id="mode" VALUE="<?php echo  $var_mode ?>">
									<input TYPE="hidden" NAME="amount" id="amount" value="<?php echo  $var_amount ?>">
									<input TYPE="hidden" NAME="postback" id="postback" value="">
									<div class="row main_form_inner ">
										<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
											<label><?php echo TEXT_USE_PAYPAL; ?></label>
										</div>
										<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
											<a href="javascript:confirmPay(0);"><img src="images/x-click-but20.gif" border="0" alt=""></a>
										</div>
									</div>


									<?php
								}
								if(DisplayLookUp("Enable Escrow")=="Yes"){
									if (PAYMENT_CURRENCY_CODE == 'USD') {
										if (DisplayLookUp('authsupport') == "YES") {
											?>
											<div  class="clearfix">
												<h5><?php echo TEXT_USE_CREDIT_CARDS; ?></h5>
											</div>
											<div class="row main_form_inner ">
												<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
													<label><?php echo TEXT_USE_AUTHORIZE; ?></label>
												</div>
												<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
													<a href="javascript:confirmPay(1);"><img src="images/cc.jpg" border="0" alt=""></a>
												</div>
											</div>
											<?php
										}
									}

									if (DisplayLookUp('enablestripe') == "Y") {
										?>		
										<div class="clearfix">
											<h5><?php echo TEXT_USE_CREDIT_CARDS; ?></h5>
										</div>					
										<div class="row main_form_inner ">
											<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
												<label><?php echo TEXT_USE_STRIPE; ?></label>
											</div>
											<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
												<a href="javascript:confirmPay(2);"><img src="images/cc.jpg" width="180" height="31" title="" border="0" alt=""></a>
											</div>
										</div>
										<?php
									}
								}
								if (1!=1){
									if (DisplayLookUp('paypalsupport') == "YES") {
										?>


										<div class="col-lg-6 col-sm-12 col-md-12 col-xs-12 main_form_inner row">
											<label>Using Paypal</label>
											your account will be activated immediately after the payment is made.
											<?php
											if ($approval_tag == "1") {
												echo "Note: some delay may occur, since it needs approval from Administrator of the site.";
								}//end if
								if ($approval_tag == "E") {
									echo "Note: A mail with activation link has been sent to your email. Please click the activation link to activate your membership.";
								}//end else
								?>
							</div>
							
							
							<?php
							} //end if
							if (PAYMENT_CURRENCY_CODE == 'USD') {
								if (DisplayLookUp('authsupport') == "YES") {
									?>
									<div class="col-lg-6 col-sm-12 col-md-12 col-xs-12 main_form_inner row">
										<label>Using Credit Card</label>
										your account will be activated immediately after the payment is made.
										<?php
										if ($approval_tag == "1") {
											echo "(Note: some delay may occur, since it needs approval from Administrator of the site).";
										}
										?>


									</div>
									<?php
							}//end if
						}//end if
					}
					?>

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
