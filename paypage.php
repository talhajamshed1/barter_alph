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
include_once('./includes/httprefer_check.php');

$var_swapid = "";
$var_mode = "";
$var_userid = "";
$var_amount = 0;
$var_cctype = "";
$var_post_type = "";
$var_date = "";
$var_mainflag = false;
$err_flag = false;

if ($_GET["swapid"] != "") {
    $var_swapid = $_GET["swapid"];
    $var_mode = $_GET["mode"];
    $var_amount = $_GET["amount"];
}//end if
else if ($_POST["swapid"] != "") {
    $var_swapid = $_POST["swapid"];
    $var_mode = $_POST["mode"];
    $var_amount = $_POST["amount"];
    $var_cctype = $_POST["cctype"];
    $var_post_type = ($var_mode == "od" || $var_mode == "om") ? "swap" : "wish";
    $var_date = date('Y-m-d H:i:s');
}//end else if
$var_userid = $_SESSION["guserid"];

$sql = "Select vSwapStatus from " . TABLEPREFIX . "swap where nSwapId='" . $var_swapid . "'  AND vSwapStatus = '1' ";
if (mysqli_num_rows(mysqli_query($conn, $sql)) > 0) {
    // If the item is swapped then check if any check has been submitted for this,if yes show the details.
    $sql = "Select * from " . TABLEPREFIX . "swapinter where  nSwapId='"
            . addslashes($var_swapid) . "' AND nUserId='"
            . addslashes($var_userid) . "' AND vDelStatus='0' ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $err_method = $row["vMethod"];
        $err_name = $row["vName"];
        $err_bank = $row["vBank"];
        $err_refno = $row["vReferenceNo"];
        $err_refdate = $row["dReferenceDate"];
        $err_entrydate = $row["dEntryDate"];
        $disp_method = "";
        $disp_method = get_payment_name($err_method);
        
        $err_flag = true;
    }//end if
    else {
        $var_mainflag = true;
    }//end else
}//end if


if ($var_mainflag == true && $_POST["postback"] == "Y") {
    $sql = "delete from " . TABLEPREFIX . "swaptemp where  nSwapId='"
            . addslashes($var_swapid) . "' AND nUserId='"
            . addslashes($var_userid) . "' ";

    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $sql = "Insert into " . TABLEPREFIX . "swaptemp(nTempId,nSwapId,nUserId,nAmount,vMethod,
                   vMode,vPostType,dDate) values('',
                   '" . addslashes($var_swapid) . "',
                   '" . $var_userid . "',
                   '" . addslashes($var_amount) . "',
                   '" . addslashes($var_cctype) . "',
                   '" . addslashes($var_mode) . "',
                   '" . $var_post_type . "',
                   '" . $var_date . "')";

    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $sql = "Select nTempId from " . TABLEPREFIX . "swaptemp where nSwapId='"
            . addslashes($var_swapid) . "' AND nUserId='"
            . addslashes($var_userid) . "' AND dDate='"
            . $var_date . "' ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    if (mysqli_num_rows($result) > 0) {
        if ($row = mysqli_fetch_array($result)) {
            //Here the redirection to the payment gateway should come
            //Presently the redirection is done to the confirmation page
            //and steps taken to perform action
            if ($var_cctype == "cc") {
                header("location:paypagecc.php?paytype=cc&tmpid=" . $row["nTempId"]);
                exit();
            }//end if
            else if ($var_cctype == "yp") {
                header("location:paypagecc.php?paytype=yp&tmpid=" . $row["nTempId"]);
                exit();
            }//end if
            else if ($var_cctype == "bp") {
                header("location:paypagecc.php?paytype=bp&tmpid=" . $row["nTempId"]);
                exit();
			}//end if
			else if ($var_cctype == "sp") {
                header("location:paypagecc.php?paytype=sp&tmpid=" . $row["nTempId"]);
                exit();
            }//end if
            else if ($var_cctype == "gc") {
                header("location:paypagecc.php?paytype=gc&tmpid=" . $row["nTempId"]);
                exit();
            }//end if
            else if ($var_cctype == "pp") {
                $_SESSION["gstempid"] = $row["nTempId"];
                header("location:paypagepp.php?tmpid=" . $row["nTempId"]);
                exit();
            }//end else if
            else if ($var_cctype == "wp") {
                $_SESSION["gstempid"] = $row["nTempId"];
                header("location:paypagewp.php?tmpid=" . $row["nTempId"]);
                exit();
            }//end else if
            else {
                header("location:paypageothers.php?tmpid=" . $row["nTempId"]);
                exit();
            }//end else
        }//end if
    }//end if
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
				<?php include_once ("./includes/usermenu.php"); ?>
			</div>
			<div class="col-lg-9">			
				<div class="innersubheader">
					<h4><?php echo HEADING_PAYMENT_FORM; ?></h4>
				</div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<form name="frmPay" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
							<?php
							if (isset($message) && $message != '') {
								?>
							<div class="row warning"><?php echo $message; ?></div>
							<?php
							}//end if
							if ($var_mainflag == false) {
								?>
							<div class="row warning"><?php echo MESSAGE_CHECK_PAYMENT_PROCESS; ?>...</div>
							<?php
							if ($err_flag == true) {
								?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_NAME; ?></label>
								<?php echo  $err_name ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_BANK; ?>(<?php echo TEXT_IF_APPLICABLE; ?>)</label>
								<?php echo  $err_bank ?>
							</div>     
							<div class="row main_form_inner">
								<label><?php echo TEXT_REFERENCE_NUMBER; ?></label>
								<?php echo  $err_refno ?>
							</div>     
							<div class="row main_form_inner">
								<label><?php echo TEXT_REFERENCE_DATE; ?></label>
								<?php echo  date("F d, Y", strtotime($err_refdate)) ?>
							</div>     
							<div class="row main_form_inner">
								<label><?php echo TEXT_ENTRY_DATE; ?></label>
								<?php echo  date("F d, Y H:i", strtotime($err_entrydate)) ?>
							</div>     
							<div class="row main_form_inner">
								<label><?php echo TEXT_METHOD; ?></label>
								<?php echo  $disp_method ?>
							</div>
							    <?php
								}//end 2nd if
							}//end if
							else {
								?>
								<?php
								if (DisplayLookUp('paypalsupport') == "YES") {
									?>
                                                                               
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_PAYPAL; ?></label>
								<input type="hidden" NAME="cctype" id="cctype" VALUE="">
								<input type="hidden" NAME="swapid" id="swapid" VALUE="<?php echo  $var_swapid ?>">
								<input type="hidden" NAME="mode" id="mode" VALUE="<?php echo  $var_mode ?>">
								<input TYPE="hidden" NAME="amount" id="amount" value="<?php echo  $var_amount ?>">
								<input TYPE="hidden" NAME="postback" id="postback" value="">
								<a href="javascript:confirmPay(0);"><img src="images/x-click-but20.gif" border="0" alt=""></a>
							</div>
							<?php
							}
							if (DisplayLookUp('Enable Escrow') == 'Yes') {
								if (PAYMENT_CURRENCY_CODE == 'USD') {
									if (DisplayLookUp('authsupport') == "YES") {
										?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_CREDIT_CARDS; ?></label>
								<a href="javascript:confirmPay(1);"><img src="images/cc.jpg" border="0" alt=""></a>
							</div>
									<?php
									}//end if
									
								}//end if
								if (DisplayLookUp('enablestripe') == "Y") {
									?>
									<div class="row main_form_inner">
								<label><?php echo TEXT_USE_STRIPE; ?></label>
								<a href="javascript:confirmPay(54);"><img src="images/cc.jpg" border="0" alt=""></a>
							</div>
								<?php
								}//end if
								if (DisplayLookUp('yourpaysupport') == "YES") {
									?>
									
									
							<div class="row main_form_inner">
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
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_WORLDPAY; ?></label>
								<a href="javascript:confirmPay(13);"><img src="images/cc.jpg" width="180" height="31" title="" border="0" alt=""></a>
							</div>
							<?php
						}//end if
						if (DisplayLookUp('enablebluepay') == "Y") {
							?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_USE_BLUEPAY; ?></label>
								<a href="javascript:confirmPay(14);"><img src="images/cc.jpg" width="180" height="31" title="" border="0" alt=""></a>
							</div>
						<?php
						}//end if
						if (DisplayLookUp('otherpayment') == 'YES') {
							?>
							
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_CASHIERS_CHECK; ?></label>
							<a href="javascript:confirmPay(2);"><img src="images/cashierscheque.gif" border="0"></a>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_BUSINESS_CHECK; ?></label>
							<a href="javascript:confirmPay(3);"><img src="images/businesscheque.gif" border="0"></a>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_PERSONAL_CHECK; ?></label>
							<a href="javascript:confirmPay(6);"><img src="images/personalcheck.gif" border="0"></a>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_MONEY_ORDER; ?></label>
							<a href="javascript:confirmPay(4);"><img src="images/moneyorder.gif" border="0"></a>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_USE_WIRETRANSFER; ?></label>
							<a href="javascript:confirmPay(5);"><img src="images/wireftransfer.gif" border="0"></a>
						</div>
						<?php
					}//end if
				}//end if
				if (1!=1){
				if (DisplayLookUp('paypalsupport') == "YES") {
					?>
					<div class="row main_form_inner">
						<label>Using Paypal</label>
						your sale will be recorded immediately after the payment is made.
					</div>
					<?php
					}//end if
					if (DisplayLookUp('Enable Escrow') == 'Yes') {
						if (DisplayLookUp('otherpayment') == 'YES') {
							?>
						<div class="row main_form_inner">
							<label>Cashiers Cheque</label>
							takes about 7-10 business days or less for clearance.
						</div>
						<div class="row main_form_inner">
							<label>Business Cheque</label>
							takes about 7-10 business days or less for clearance.
						</div>
						<div class="row main_form_inner">
							<label>Personal Cheque</label>
							takes about 7-10 business days or less for clearance.
						</div>
						<div class="row main_form_inner">
							<label>Money Order</label>
							takes about 7 business days or less to clear.
						</div>
						<div class="row main_form_inner">
							<label>Bank Wire Transfer</label>
							Bank wire transfer usually take about 24 hrs for clearance.
						</div>
						<?php
					}//end if
				}//end if
				}
			}//end else
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
