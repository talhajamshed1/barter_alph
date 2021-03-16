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
$approval_tag = DisplayLookUp('userapproval');

$var_id = "";
$var_amount = "50";
$var_real_amount = "50";
$var_cctype = "";
$var_mainflag = true;
$show_cc = false;


if ($_GET["id"] != "") {
    $var_id = $_GET["id"];
}//end if
else if ($_POST["id"] != "") {
    $var_id = $_POST["id"];
    $var_cctype = $_POST["cctype"];
    $var_amount = $_POST["amount"];
}//end else  if

//$var_id = $_SESSION["gtempid"];
$sql = "";

if (DisplayLookUp('3') != '') {
    $var_real_amount = DisplayLookUp('3');
    $var_amount = $var_real_amount;
}//end if

//if regmode is paid and plan system is enabled

if (DisplayLookUp('15') != '1' && DisplayLookUp('plan_system')=='yes') {
    $var_real_amount = $_SESSION['sess_Plan_Amt'];
    $var_amount = $_SESSION['sess_Plan_Amt'];
}//end if

if ($_POST["postback"] == "Y" && $var_mainflag == true) {
    $sql = "Update " . TABLEPREFIX . "users set vMethod='" . addslashes($var_cctype) . "',nAmount='" . addslashes($var_real_amount) . "' where nUserId='" . addslashes($var_id) . "'";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));



    if ($var_cctype == "cc") {
        header("location:paycc.php?paytype=cc&id=" . $var_id);
        exit();
    }//end if
    else if ($var_cctype == "yp") {
        header("location:paycc.php?paytype=yp&id=" . $var_id);
        exit();
    }//end if
    else if ($var_cctype == "bp") {
        header("location:paycc.php?paytype=bp&id=" . $var_id);
        exit();
	}//end if
	else if ($var_cctype == "sp") {
        header("location:paycc.php?paytype=sp&id=" . $var_id);
        exit();
    }//end if
    else if ($var_cctype == "gc") {
        header("location:paycc.php?paytype=gc&id=" . $var_id);
        exit();
    }//end if
    else if ($var_cctype == "pp") {
        header("location:paypp.php?id=" . $var_id);
        exit();
    }//end else if
    else if ($var_cctype == "wp") {
        header("location:paywp.php?id=" . $var_id);
        exit();
    }//end else if
    else {
        header("location:payothers.php?id=" . $var_id);
        exit();
    }//end else
}//end if

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function confirmPay(i)
    {
        if(i==0)
        {
            document.frmPay.cctype.value='pp';
        }//end if
        else if(i==1)
        {
            document.frmPay.cctype.value='cc';
        }//end else if
        else if(i==2)
        {
            document.frmPay.cctype.value='ca';
        }//end else if
        else if(i==3)
        {
            document.frmPay.cctype.value='bu';
        }//end else if
        else if(i==4)
        {
            document.frmPay.cctype.value='mo';
        }//end else if
        else if(i==5)
        {
            document.frmPay.cctype.value='wt';
        }//end else if
        else if(i==6)
        {
            document.frmPay.cctype.value='pc';
        }//end else if
        else if(i==11)
        {
            document.frmPay.cctype.value='yp';
        }//end else if
        else if(i==12)
        {
            document.frmPay.cctype.value='gc';
        }//end else if
        else if(i==13)
        {
            document.frmPay.cctype.value='wp';
        }//end else if
        else if(i==14)
        {
            document.frmPay.cctype.value='bp';
        }
		else if(i==54)
		{
            document.frmPay.cctype.value='sp';
        }//end else if
        document.frmPay.postback.value='Y';
        document.frmPay.submit();
    }//end function
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3">
				<?php include_once ("./includes/categorymain.php"); ?>
			</div>
			<div class="col-lg-9">			
				<div class="innersubheader">
					
				</div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 ">
						<div class="pay-form">
						<h4><?php echo HEADING_PAYMENT_FORM; ?></h4>
						<form NAME="frmPay" ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" METHOD="post">
							<?php
								if (isset($var_mainflag) && $var_mainflag == false) {
									?>
							<div class=" warning"><?php echo ERROR_AMOUNT_MISMATCHING_TRY_AGAIN; ?></div>
							<?php }//end if ?>
							<div class=" main_form_inner">
								<p><?php $ins=(DisplayLookUp('Enable Escrow') == 'Yes') ? TEXT_PAYMENT_INSTRUCTION : TEXT_PAYMENT_INSTRUCTION_NON_ESCROW;   echo str_replace("{site_url}",SITE_URL,$ins); ?></p>
							</div>
							
							<div class=" main_form_inner">
								<label>
									<?php echo TEXT_REGISTRATION." ".TEXT_AMOUNT; ?>
								</label>
									<?php
									if($_SESSION['nPlanId'])
									echo CURRENCY_CODE.$det = planAmount($_SESSION['nPlanId']);
									else
									echo CURRENCY_CODE.DisplayLookUp('3'); ?>
							</div>
							<?php
							if (DisplayLookUp('paypalsupport') == "YES") {
								?>
								<input type="hidden" NAME="cctype" id="cctype" VALUE="">
								<input type="hidden" NAME="id" id="id" VALUE="<?php echo  $var_id ?>">
								<input type="hidden" NAME="mode" id="mode" VALUE="<?php echo  $var_mode ?>">
								<input TYPE="hidden" NAME="amount" id="amount" value="<?php echo  $var_amount ?>">
								<input TYPE="hidden" NAME="postback" id="postback" value="">
								<div class=" main_form_inner">
									<label><?php echo TEXT_USE_PAYPAL; ?></label>
									<a href="javascript:confirmPay(0);"><img src="images/x-click-but20.gif" border="0" alt=""></a>
								</div>
							<?php
							}
							if (PAYMENT_CURRENCY_CODE == 'USD') {
								if (DisplayLookUp('authsupport') == "YES") {
									?>
									<div class=" main_form_inner">
										<label><?php echo TEXT_USE_CREDIT_CARDS; ?></label>
										<a href="javascript:confirmPay(1);"><img src="images/cc.jpg" border="0" alt=""></a>
									</div>
								<?php
								}//end if
								
							}//end if
							if (DisplayLookUp('enablestripe') == "Y") {
									?>
									<div class=" main_form_inner">
										<label><?php echo TEXT_USE_STRIPE; ?></label>
										<a href="javascript:confirmPay(54);"><img src="images/cc.jpg" border="0" alt=""></a>
									</div>
								<?php
								}//end if
							if (DisplayLookUp('Enable Escrow') == 'Yes') {
								if (DisplayLookUp('yourpaysupport') == "YES") {
									?>
									<div class=" main_form_inner">
										<label><?php echo TEXT_USE_YOURPAY; ?></label>
										<a href="javascript:confirmPay(11);"><img src="images/cc.jpg" border="0" alt=""></a>
									</div>
								<?php
								}//end if
								/*if (DisplayLookUp('googlesupport') == "YES") {
									?>
									<div class="row main_form_inner">
										<label><?php echo TEXT_USE_GOOGLE_CHECKOUT; ?></label>
										<a href="javascript:confirmPay(12);"><img src="images/checkout.gif" border="0" alt=""></a>
									</div>
									<?php
								}//end if*/
								if (DisplayLookUp('enableworldpay') == "Y") {
									?>
									<div class=" main_form_inner">
										<label><?php echo TEXT_USE_WORLDPAY; ?></label>
										<a href="javascript:confirmPay(13);"><img src="images/cc.jpg" width="180" title="" border="0" alt=""></a>
									</div>
									<?php
								}//end if
								if (DisplayLookUp('enablebluepay') == "Y") {
									?>
									<div class=" main_form_inner">
										<label><?php echo TEXT_USE_BLUEPAY; ?></label>
										<a href="javascript:confirmPay(14);"><img src="images/cc.jpg" width="180" title="" border="0" alt=""></a>
									</div>
								<?php
								}//end if
								if (DisplayLookUp('otherpayment') == 'YES') {
									?>
									<div class=" main_form_inner">
										<label><?php echo TEXT_USE_CASHIERS_CHECK; ?></label>
										<a href="javascript:confirmPay(2);"><img src="images/cashierscheque.gif" border="0"></a>
									</div>
									<div class=" main_form_inner">
										<label><?php echo TEXT_USE_BUSINESS_CHECK; ?></label>
										<a href="javascript:confirmPay(3);"><img src="images/businesscheque.gif" border="0"></a>
									</div>
									<div class=" main_form_inner">
										<label><?php echo TEXT_USE_PERSONAL_CHECK; ?></label>
										<a href="javascript:confirmPay(6);"><img src="images/personalcheck.gif" border="0"></a>
									</div>
									<div class=" main_form_inner">
										<label><?php echo TEXT_USE_MONEY_ORDER; ?></label>
										<a href="javascript:confirmPay(4);"><img src="images/moneyorder.gif" border="0"></a>
									</div>
									<div class=" main_form_inner">
										<label><?php echo TEXT_USE_WIRETRANSFER; ?></label>
										<a href="javascript:confirmPay(5);"><img src="images/wireftransfer.gif" border="0"></a>
									</div>
										<?php
									}//end if
								}//end if
								if (1!=1){
								if (DisplayLookUp('paypalsupport') == "YES") {
									?>
								<div class=" main_form_inner">
									<label>Using Paypal</label>
									your account will be activated immediately after the payment is made.
									<?php
									if ($approval_tag == "1") {
										echo "<br><span class='warning'>(Note: some delay may occur, since it needs approval from Administrator of the site).</span>";
									}//end if
									if ($approval_tag == "E") {
										echo "<br><span class='warning'>Note: A mail with activation link has been sent to your email. Please click
																								the activation link to activate your membership.</span>";
									}//end else
									?>
								</div>
							<?php
							} //end if
							if (PAYMENT_CURRENCY_CODE == 'USD') {
								if (DisplayLookUp('authsupport') == "YES") {
									?>
									<div class=" main_form_inner">
										<label>Using Credit Card</label>
										your account will be activated immediately after the payment is made.
											<?php
											if ($approval_tag == "1") {
												echo "<br><span class='warning'>(Note: some delay may occur, since it needs approval from Administrator of the site).</span>";
											}
											?>
									</div>
										<?php
										}//end if
									}//end if
									if (DisplayLookUp('Enable Escrow') == 'Yes') {
										if (DisplayLookUp('yourpaysupport') == "YES") {
											?>
									<div class=" main_form_inner">
										<label>Using Yourpay</label>
										your account will be activated immediately after the payment is made.
										<?php
										if ($approval_tag == "1") {
											echo "<br><span class='warning'>(Note: some delay may occur, since it needs approval from Administrator of the site).</span>";
										}
										?>
									</div>
											<?php
										}//end if
										if (DisplayLookUp('enableworldpay') == "Y") {
											?>
									<div class=" main_form_inner">
										<label>Using WorldPay</label>
										your account will be activated immediately after the payment is made.
										<?php
										if ($approval_tag == "1") {
											echo "<br><span class='warning'>(Note: some delay may occur, since it needs approval from Administrator of the site).</span>";
										}
										?>
									</div>
									<?php
									}//end if
									if (DisplayLookUp('otherpayment') == 'YES') {
										?>
									<div class=" main_form_inner">
										<label>Cashiers Cheque</label>
										takes about 7-10 business days or less for clearance.
									</div>
									<div class=" main_form_inner">
										<label>Business Cheque</label>
										takes about 7-10 business days or less for clearance.
									</div>
									<div class=" main_form_inner">
										<label>Personal Cheque</label>
										takes about 7-10 business days or less for clearance.
									</div>
									<div class=" main_form_inner">
										<label>Money Order</label>
										takes about 7 business days or less to clear.
									</div>
									<div class=" main_form_inner">
										<label>Bank Wire Transfer</label>
										Bank wire transfer usually take about 24 hrs for clearance.
									</div>
								<?php }//end if
							}//end if 
						  } ?>
						</form>
					</div>	
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