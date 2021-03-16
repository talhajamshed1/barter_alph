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
$Sscope = "paypage";
if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else

if ($_GET["tmpid"] != "") {
    $var_tmpid = $_GET["tmpid"];
}//end if
else if ($_POST["tmpid"] != "") {
    $var_tmpid = $_POST["tmpid"];
}//end else if
//store user profile
$userProfile = userProfiles($_SESSION["guserid"]);

//checking payment method
$txtPayMethod = ($_GET['paytype'] != '') ? $_GET['paytype'] : $_POST['txtPayMethod'];

$userid = $_SESSION["guserid"];

$cc_err = "";
$cc_flag = false;

$sql = "Select s.vTitle,st.nAmount,st.vPostType from " . TABLEPREFIX . "swaptemp st inner join ";
$sql .= " " . TABLEPREFIX . "swap s on st.nSwapId = s.nSwapId where st.nTempId='" . addslashes($var_tmpid) . "' ";

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    if ($row = mysqli_fetch_array($result)) {
        $cost = $row["nAmount"];
        $amnt = $cost;
        $var_title = $row["vTitle"];
        $var_posttype = $row["vPostType"];
    }//end if
}//end if
else {
    $cc_err = ERROR_CHECK_YOUR_INPUT;
}//end else


if (isset($_POST["postback"]) && $_POST["postback"] == "Y") {
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
    $txtACurrency = PAYMENT_CURRENCY_CODE;

    $cc_tran = "";
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
        require("credit_inte_paypage.php");
    }//end if
    if ($txtPayMethod == 'bp') {
        require("Bluepay.php");
	}//end if
	if ($txtPayMethod == 'sp') {
		require("stripepay.php");
    } //end if
    if ($txtPayMethod == 'yp') {
        require("yourpay.php");
	}//end else
	


    if ($cc_flag == true) {
        //Start of the process of performing the transaction entry
        $db_swapid = "";
        $db_userid = "";
        $db_amount = 0;
        $db_method = "";
        $db_mode = "";
        $db_post_type = "";

        //here the transaction id has to be set that comes from the payment gateway
        $var_txnid = $cc_tran;

        $sql = "Select nTempId,nSwapId,nUserId,nAmount,vMethod,vMode,vPostType,dDate
                                        from " . TABLEPREFIX . "swaptemp where nTempId='" . addslashes($var_tmpid) . "' ";

        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) > 0) {
            if ($row = mysqli_fetch_array($result)) {
                //if you have data for the transaction
                $db_swapid = $row["nSwapId"];
                $db_userid = $row["nUserId"];
                $db_amount = $row["nAmount"];
                $db_method = $row["vMethod"];
                $db_mode = $row["vMode"];
                $db_post_type = $row["vPostType"];
                $var_swapmember = "";
                $var_incmember = "";

                if ($db_mode == "od") {
                    //if the payment is being made by the person who made the offer
                    //that means the present userid is the one that is present in the swaptxn table
                    //and this user is giving money to the person who made the swap table entry
                    //and the userid is fetched from the table swap
                    //swapmember --> the one in the temporary table
                    //incmember --> the one who receives the money(comes from the swap table)
                    $var_swapmember = $db_userid;

                    $sql = "Select nUserId from " . TABLEPREFIX . "swap where nSwapId='"
                            . $db_swapid . "' ";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result) > 0) {
                        if ($row = mysqli_fetch_array($result)) {
                            $var_incmember = $row["nUserId"];
                        }//end if
                    }//end if
                }//end if
                else if ($db_mode == "om") {
                    //if the payment is being made by the person who accepts the offer(ie. the one who
                    //made the main swap item),here the userid is the one in the swap table,hence
                    //he has to fetch the swapuserid from the swaptxn table,and give money to him
                    //swapmember --> the one in the swaptxn table
                    //incmember --> the one who receives the money(comes from the swaptxn table)

                    $db_amount = -1 * $db_amount;

                    $sql = "Select nUserId from " . TABLEPREFIX . "swaptxn where nSwapId='"
                            . $db_swapid . "' and vStatus='A'";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result) > 0) {
                        if ($row = mysqli_fetch_array($result)) {
                            $var_swapmember = $row["nUserId"];
                            $var_incmember = $row["nUserId"];
                        }//end if
                    }//end if
                }//end else
                else if ($db_mode == "wm") {
                    //if the payment is being made by the person who accepts the offer(ie. the one who
                    //made the main swap item),here the userid is the one in the swap table,hence
                    //he has to fetch the swapuserid from the swaptxn table,and give money to him
                    //swapmember --> the one in the swaptxn table
                    //incmember --> the one who receives the money(comes from the swaptxn table)

                    $db_amount = -1 * $db_amount;

                    $sql = "Select nUserId from " . TABLEPREFIX . "swaptxn where nSwapId='"
                            . $db_swapid . "' and vStatus='A'";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result) > 0) {
                        if ($row = mysqli_fetch_array($result)) {
                            $var_swapmember = $row["nUserId"];
                            $var_incmember = $row["nUserId"];
                        }//end if
                    }//end if
                }//end else if


                $db_swap_ids = get_swaps_ids($db_swapid);
                
                $db_amount = ($db_amount < 0) ? (-1 * $db_amount) : $db_amount;
                
                $sql = "Update " . TABLEPREFIX . "swap set 
                                              nSwapAmount='$db_amount',
                                               vEscrow='1',
                                               vMethod='$db_method',
                                               vTxnId='$var_txnid',
                                               vSwapStatus='2',dTxnDate=now() where
                                               nSwapId in (" . $db_swap_ids . ") ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//nSwapMember='$var_swapmember',

                

//                $sql = "Update " . TABLEPREFIX . "users set nAccount=nAccount + $db_amount
//                                                                          where nUserId='" . $var_incmember . "' ";
//                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                $sql = "delete from " . TABLEPREFIX . "swaptemp where nTempId='"
                        . addslashes($var_tmpid) . "' ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));
            }//end else if
        }//end if
        else {
            header('location:payconfirm.php?tmpid=' . $db_swapid . '&flag=1&');
            exit();
        }//end else
        //End of the process of performing the transaction entry
        header('location:payconfirm.php?tmpid=' . $db_swapid . '&flag=0&');
        exit();
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
    function varify()
    {
        reqd= document.frmBuy.quantityREQD.value;
        avail = document.frmBuy.quantityAVL.value;
        if(isNaN(reqd) || reqd.substring(0,1)==" " || reqd.length <= 0 || parseInt(reqd) > parseInt(avail) || parseInt(reqd) < 1)
        {
            alert("<?php echo ERROR_QUANTITY_INVALID; ?>");
            document.frmBuy.quantityREQD.value="1";
        }//end if
        else
        {
            document.frmBuy.quantityREQD.value=parseInt(reqd);
        }//end else
        document.frmBuy.total.value=parseInt(document.frmBuy.amount.value)*parseInt(document.frmBuy.quantityREQD.value);
    }//end funciton


    function proceed(cc)
    {
        if(parseInt(document.frmBuy.quantityREQD.value) > parseInt(document.frmBuy.quantityAVL.value))
        {
            alert("<?php echo ERROR_QUANTITY_INVALID; ?>");
        }//endi f
        else
        {
            document.frmBuy.cctype.value=cc;
            document.frmBuy.submit();
        }//end else
    }//end funciton

    function clickConfirm(submitForm = 1)
    {
        document.frmBuy.postback.value='Y';
        document.frmBuy.method="post";
		if(submitForm){
        		document.frmBuy.submit();
			}else {
				return true;
			}
    }//end function

    function checkValue(t)
    {
        if(isNaN(t.value) || t.value.substring(0,1)==" " || t.value.length==0 || parseFloat(t.value) < 0 )
        {
            if(t.name=="txtccno")
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
            number: $jqr( "#txtccno" ).val(),
            cvc: $jqr( "#txtcvv2").val(),
            exp_month: $jqr( "#txtMM" ).val(),
            exp_year: $jqr( "#txtYY" ).val(),
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
<body onLoad="timersOne();document.getElementById('ddlCountry').value='<?php echo $userProfile['vCountry']; ?>';">
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
							<?php
								if ($txtPayMethod != 'gc') {
									?>
							<form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" id="<?php echo ($txtPayMethod == 'sp')? 'frmStripePayment':''; ?>">
								<input type="hidden" name="postback" id="postback" value="">
								<input type="hidden" name="tmpid" id="tmpid" value="<?php echo  $var_tmpid ?>">
								<input type="hidden" name="txtPayMethod" value="<?php echo $txtPayMethod; ?>">
								<?php
								if ($message == false && $message != '') {
									?>
									<div class="row warning"><?php echo $message; ?></div>
									<?php
								}//end if
								if ($cc_flag == false && $cc_err != '') {
									?>
									<div class="row warning"><?php echo $cc_err; ?></div>
								<?php }//end if ?>
								
								<h3 class="subheader row"><?php echo HEADING_PAYMENT_DETAILS; ?></h3>
								
								<div class="row main_form_inner">
									<label><?php echo TEXT_TITLE; ?></label>
									<?php echo  htmlentities($var_title) ?>
								</div>								
								<div class="row main_form_inner">
									<label><?php echo TEXT_AMOUNT; ?></label>
									<?php echo CURRENCY_CODE; ?><?php echo  $cost ?>
								</div>	
								<div class="row main_form_inner">
									<label><?php echo TEXT_TYPE; ?></label>
									<?php echo  $var_posttype ?>
								</div>
								
								<h3 class="subheader row"><?php echo TEXT_CREDIT_CARD_DETAILS; ?></h3>
								
								<div class="row main_form_inner">
									<label><?php echo TEXT_FIRST_NAME; ?><span class="warning">*</span></label>
									<input type="text" name="txtFirstName" id="txtFirstName" value="<?php echo($userProfile['vFirstName']); ?>" size="24" maxlength="40" class="form-control">
								</div>	
								<div class="row main_form_inner">
									<label><?php echo TEXT_LAST_NAME; ?><span class="warning">*</span></label>
									<input type="text" name="txtLastName" id="txtLastName" value="<?php echo($userProfile['vLastName']); ?>" size="24" maxlength="40" class="form-control">
								</div>	
								<div class="row main_form_inner form_card_img">
									<label><?php echo TEXT_CARD_NUMBER; ?><span class="warning">*</span></label>
									<input type="text" name="txtccno" class="form-control" id="txtccno" size="24" maxlength="16" onBlur="javascript:checkValue(this);">
									<img src="<?php echo $imagefolder ?>/images/visa_amex.gif">
								</div>	
								<div class="row main_form_inner">
									<label><?php echo TEXT_CARD_VALIDATION_CODE; ?><span class="warning">*</span></label>
									<input type="password" name="txtcvv2" class="form-control" id="txtcvv2" size=10 maxlength="4" onBlur="javascript:checkValue(this);">
									<a href="http://www.cvvnumber.com/cvv.html" target="_blank" style="font-size:11px">What is my CVV code?</a>
								</div>	
								<div class="row main_form_inner">
									<label><?php echo TEXT_EXPIRATION_DATE; ?><span class="warning">*</span></label>
									<div class="row">
										<div class="col-xs-12 col-sm-6">
											<input type="text" name="txtMM" class="form-control" id="txtMM" size="3" maxlength="2">
										</div>
										<div class="col-xs-12 col-sm-6">
											<input type="text" name="txtYY" class="form-control" id="txtYY" size="4" maxlength="4">
										</div>
									</div>
								</div>
								
								<h3 class="subheader row"><?php echo TEXT_BILLING_ADDRESS_DETAILS; ?></h3>
								
								
								<div class="row main_form_inner">
									<label><?php echo TEXT_ADDRESS; ?><span class="warning">*</span></label>
									<input type="text" name="txtAddress" class="form-control" id="txtAddress" size="24" maxlength="30" value="<?php echo($userProfile['vAddress1']); ?>">
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_CITY; ?><span class="warning">*</span></label>
									<input type="text" name="txtCity" class="form-control" id="txtCity" size="24" maxlength="30"  value="<?php echo($userProfile['vCity']); ?>">
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_STATE; ?><span class="warning">*</span></label>
									<input type="text" name="txtState" class="form-control" id="txtState" size="24" maxlength=30 value="<?php echo($userProfile['vState']); ?>">
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_ZIP; ?><span class="warning">*</span></label>
									<input type="text" name="txtPostal" class="form-control" id="txtPostal" size="24" maxlength="10" value="<?php echo($userProfile['nZip']); ?>">
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_COUNTRY; ?><span class="warning">*</span></label>
									<select name="cmbCountry" class="form-control" id="ddlCountry"><?php include("includes/country_select.php"); ?></select>
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_EMAIL; ?><span class="warning">*</span></label>
									<input type="text" name="txtEmail" class="form-control" id="txtEmail" size=24 maxlength=50 value="<?php echo($userProfile['vEmail']); ?>">
								</div>
								<div class="row main_form_inner">
									<label>
										<input type="button" name="btPay" id="btPay" class="subm_btt"  value="<?php echo BUTTON_PAY_NOW; ?>" onClick="javascript:<?php echo ($txtPayMethod != 'sp')?'clickConfirm();':'stripePay()'?>">
									</label>
								</div>
							</form>
							 <?php
								}//end if
								else {

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
									$_SESSION['sess_gc_amount'] = $cost;
									$_SESSION['sess_gc_txtACurrency'] = PAYMENT_CURRENCY_CODE;
									$_SESSION["sess_gc_tmpid"] = $var_tmpid;
									$_SESSION["sess_gc_var_title"] = $var_title;

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
										$var_tmpid = $_SESSION["sess_gc_tmpid"];
										$var_title = $_SESSION["sess_gc_var_title"];


										if ($google_demo == "TEST")
											$server_type = "sandbox";
										else
											$server_type = "checkout";


										// Create a new shopping cart object
										$cart = new GoogleCart($google_id, $google_key, $server_type, $currency);

										// Add items to the cart
										$item_1 = new GoogleItem(SITE_NAME, // Item name
														$var_title, // Item description
														1, // Quantity
														$cost); // Unit price

										$cart->AddItem($item_1);

										// continue link page
										$cart->SetContinueShoppingUrl(SECURE_SITE_URL . "/paypagecc.php?paytype=gc&tmpid=" . $var_tmpid . "&gc_status=success");


										$cart->AddRoundingPolicy("HALF_UP", "PER_LINE");

										$cart->SetMerchantPrivateData('paypagecc-'.$var_tmpid.'-'.$_SESSION["guserid".'-'.$_SESSION["gphone"].'-'.$txtACurrency]);

										// Display Google Checkout button
										echo $cart->CheckoutButtonCode("LARGE");
									}

//end google usecase

									$_SESSION['sess_page_name'] = 'paypagecc.php';
									$_SESSION['sess_page_return_url_suc'] = SITE_URL . "/paypagecc.php?paytype=gc&tmpid=" . $var_tmpid . "&gc_status=success";
									$_SESSION['sess_page_return_url_fail'] = SECURE_SITE_URL . "/paypagecc.php?paytype=gc&tmpid=" . $var_tmpid . "&gc_status=failure";

									//calculation starts here
									if (isset($gc_status) && $gc_status == 'success') {
										$txtACurrency = $txtACurrency;

										$gc_tran = "";
										$gc_flag = true;
										/* get the invoice number */
										$sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
										$result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
										$row1 = mysqli_fetch_array($result1);
										$Inv_id = $row1['maxinvid'];
										/*                                                                         * *********************** */

										$Cust_ip = getClientIP();
										$Company = '-NA-';
										$Phone = $_SESSION["gphone"];
										$Cust_id = $_SESSION["guserid"];


										if ($gc_flag == true) {
											//Start of the process of performing the transaction entry
											$db_swapid = "";
											$db_userid = "";
											$db_amount = 0;
											$db_method = "";
											$db_mode = "";
											$db_post_type = "";

											//here the transaction id has to be set that comes from the payment gateway
											$var_txnid = $gc_tran;

											$sql = "Select nTempId,nSwapId,nUserId,nAmount,vMethod,vMode,vPostType,dDate
																from " . TABLEPREFIX . "swaptemp where nTempId='" . addslashes($var_tmpid) . "' ";

											$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
											if (mysqli_num_rows($result) > 0) {
												if ($row = mysqli_fetch_array($result)) {
													//if you have data for the transaction
													$db_swapid = $row["nSwapId"];
													$db_userid = $row["nUserId"];
													$db_amount = $row["nAmount"];
													$db_method = $row["vMethod"];
													$db_mode = $row["vMode"];
													$db_post_type = $row["vPostType"];
													$var_swapmember = "";
													$var_incmember = "";

													if ($db_mode == "od") {
														//if the payment is being made by the person who made the offer
														//that means the present userid is the one that is present in the swaptxn table
														//and this user is giving money to the person who made the swap table entry
														//and the userid is fetched from the table swap
														//swapmember --> the one in the temporary table
														//incmember --> the one who receives the money(comes from the swap table)
														$var_swapmember = $db_userid;

														$sql = "Select nUserId from " . TABLEPREFIX . "swap where nSwapId='"
																. $db_swapid . "' ";
														$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
														if (mysqli_num_rows($result) > 0) {
															if ($row = mysqli_fetch_array($result)) {
																$var_incmember = $row["nUserId"];
															}//end if
														}//end if
													}//end if
													else if ($db_mode == "om") {
														//if the payment is being made by the person who accepts the offer(ie. the one who
														//made the main swap item),here the userid is the one in the swap table,hence
														//he has to fetch the swapuserid from the swaptxn table,and give money to him
														//swapmember --> the one in the swaptxn table
														//incmember --> the one who receives the money(comes from the swaptxn table)

														$db_amount = -1 * $db_amount;

														$sql = "Select nUserId from " . TABLEPREFIX . "swaptxn where nSwapId='"
																. $db_swapid . "' and vStatus='A'";
														$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
														if (mysqli_num_rows($result) > 0) {
															if ($row = mysqli_fetch_array($result)) {
																$var_swapmember = $row["nUserId"];
																$var_incmember = $row["nUserId"];
															}//end if
														}//end if
													}//end else
													else if ($db_mode == "wm") {
														//if the payment is being made by the person who accepts the offer(ie. the one who
														//made the main swap item),here the userid is the one in the swap table,hence
														//he has to fetch the swapuserid from the swaptxn table,and give money to him
														//swapmember --> the one in the swaptxn table
														//incmember --> the one who receives the money(comes from the swaptxn table)

														$db_amount = -1 * $db_amount;

														$sql = "Select nUserId from " . TABLEPREFIX . "swaptxn where nSwapId='"
																. $db_swapid . "' and vStatus='A'";
														$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
														if (mysqli_num_rows($result) > 0) {
															if ($row = mysqli_fetch_array($result)) {
																$var_swapmember = $row["nUserId"];
																$var_incmember = $row["nUserId"];
															}//end if
														}//end if
													}//end else if

													$db_swap_ids = get_swaps_ids($db_swapid);

													$db_amount = ($db_amount < 0) ? (-1 * $db_amount) : $db_amount;
													
													$sql = "Update " . TABLEPREFIX . "swap set 
															  nSwapAmount='$db_amount',
															   vEscrow='1',
															   vMethod='$db_method',
															   vTxnId='$var_txnid',
															   vSwapStatus='2',dTxnDate=now() where
															   nSwapId in (" . $db_swap_ids . ") ";
													mysqli_query($conn, $sql) or die(mysqli_error($conn));//nSwapMember='$var_swapmember',

													

//													$sql = "Update " . TABLEPREFIX . "users set nAccount=nAccount + $db_amount
//																	where nUserId='" . $var_incmember . "' ";
//													mysqli_query($conn, $sql) or die(mysqli_error($conn));

													$sql = "delete from " . TABLEPREFIX . "swaptemp where nTempId='"
															. addslashes($var_tmpid) . "' ";
													mysqli_query($conn, $sql) or die(mysqli_error($conn));
												}//end else if
											}//end if
											else {
												$_SESSION["gsaleextraid"] = "";
												$_SESSION['txtGoogleId'] = "";
												$_SESSION['txtGoogleKey'] = "";
												$_SESSION['chkGoogleSandbox'] = "";
												$_SESSION['sess_gc_saleid'] = "";
												$_SESSION['sess_gc_paytype'] = "";
												$_SESSION['sess_gc_txtPayMethod'] = "";
												$_SESSION['sess_gc_amount'] = "";
												$_SESSION['sess_gc_txtACurrency'] = "";
												$_SESSION["sess_gc_tmpid"] = "";
												$_SESSION["sess_gc_var_title"] = "";
												$_SESSION['sess_page_name'] = '';
												$_SESSION['sess_page_return_url_suc'] = '';
												$_SESSION['sess_page_return_url_fail'] = '';
												$_SESSION['sess_flag_failure'] = '';

												header('location:payconfirm.php?tmpid=' . $db_swapid . '&flag=1&');
												exit();
											}//end else
											//End of the process of performing the transaction entry
											header('location:payconfirm.php?tmpid=' . $db_swapid . '&flag=0&');
											exit();
										}//end if
									}//end if
									if (isset($_SESSION['sess_flag_failure']) && $_SESSION['sess_flag_failure'] == false) {
										$gc_flag = false;
										$gc_err = ERROR_PAYMENT_PROCESS_FAILED;
									}//end else
									//calculation ends here
									?>			
									
							<div class="clear"></div>
							
							<?php
							if ($gc_flag == false && $gc_err != '') {
							?>
							
							<div class="row warning"><?php echo $gc_err; ?></div>
							
							<?php
							}//end if
							if (isset($gc_status) && $gc_status != 'success') {
							?>
							
							<h3 class="subheader row"><?php echo TEXT_PAYMENT_DETAILS; ?></h3>
						
							<div class="row main_form_inner">
								<label><?php echo TEXT_TITLE; ?></label>
								<?php echo  htmlentities($var_title) ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_AMOUNT; ?></label>
								<?php echo CURRENCY_CODE; ?><?php echo  $cost ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_TYPE; ?></label>
								<?php echo  $var_posttype ?>
							</div>
							<div class="row main_form_inner">
								<label>
									<br>
									<br>
									<?php echo MESSAGE_GOOGLE_CHECKOUT_INSTRUCTION; ?> <br>
									<br>
									<b><?php echo MESSAGE_WAITING_FOR_SECURE_PAYMENT_INTERFACE; ?>....</b><br>
									<br><br>
									<?php UseCase1(); ?>
								</label>
							</div>
							
							
							
						<?php
						}//end if
						?>
						 
						<?php
						}//end else
						?>
									
									
						</div>
						<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>	
						
						<div class="clear"></div>					
						
						<div class="col-lg-12 col-sm-12 col-md-12">
							<div class="subbanner"><?php include('./includes/sub_banners.php'); ?></div>
						</div>
						
					</div>					
				</div>
			</div>  
		</div>
	</div>

	<?php require_once("./includes/footer.php"); ?>
	