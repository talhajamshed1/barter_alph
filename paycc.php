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
include_once('./includes/gpc_map.php');

$approval_tag = "0";
$approval_tag = DisplayLookUp('userapproval');
$Sscope = "pay";
if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else
//store user profile
$userProfile = userProfiles($_SESSION["guserid"]);

//checking payment method
$txtPayMethod = ($_GET['paytype'] != '') ? $_GET['paytype'] : $_POST['txtPayMethod'];

// get get variables
If ($_GET["id"] != "") {
    $id = $_GET["id"];
}//end if
else if ($_POST["id"] != "") {
    $id = $_POST["id"];
}//end else
$id = $_SESSION["gtempid"];

$sql = "Select nAmount,vLoginName,vPhone from " . TABLEPREFIX . "users where nUserId='$id'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    if ($row = mysqli_fetch_array($result)) {
        $amount = $row["nAmount"];
        $var_credit_desc = $row["vLoginName"];
        $var_phone = $row["vPhone"];
    }//end if
}//end if
//if regmode is paid and escrow is disabled
//if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
if (DisplayLookUp('15') != '1' && DisplayLookUp('plan_system')=='yes') {
    $amount = $_SESSION['sess_Plan_Amt'];
}//end if

if (isset($_POST["postback"]) && $_POST["postback"] == "Y") {
    $FirstName = $_POST["txtFirstName"];
    $LastName = $_POST["txtLastName"];
    $Address = $_POST["txtAddress"];
    $City = $_POST["txtCity"];
    $State = $_POST["txtState"];
    $Zip = $_POST["txtPostal"];
    $CardNum = $_POST["txtccno"];
    $txtEmail = $Email = $_POST["txtEmail"];
    $CardCode = $_POST["txtcvv2"];
    $Country = $_POST["cmbCountry"];
    $Month = $_POST["txtMM"];
    $Year = $_POST["txtYY"];
    // $cost = $_POST["amnt"];
    $saleid = $_POST["saleid"];
    $userid = $_POST["userid"];
    $now = urldecode($_POST["dt"]);
    $cost = $amount;
    $txtACurrency = PAYMENT_CURRENCY_CODE;
    $cc_flag = false;
    $cc_err = "";
    $cc_tran = "";

    /* get the invoice number */
    $sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
    $result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
    $row1 = mysqli_fetch_array($result1);
    $Inv_id = $row1['maxinvid'];
    /*     * *********************** */

    $Cust_ip = getClientIP();
    $Company = '-NA-';
    $Phone = $var_phone;
    $Cust_id = $id;


    //checking payment mode
    if ($txtPayMethod == 'cc') {
        require("credit_inte_reg.php");
    }//end if
    if ($txtPayMethod == 'bp') {
        require("Bluepay.php");
    }//end if
    if ($txtPayMethod == 'yp') {
        require("yourpay.php");
	}//end else
	if ($txtPayMethod == 'sp') {
		require("stripepay.php");
    }


    if ($cc_flag == true) {
        // Start of the transaction  for adding a user since payment is successfull
        $txnid = $cc_tran;
        // ////////////////////////////////////////////////////////////////////////////////////
        // check if txnid alredy there to prevent refresh
        $sql = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid'  AND vTxn_mode='" . $txtPayMethod . "'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        // if(mysqli_num_rows($result)==0){
        if (1 == 1) {
            $var_id = $_SESSION["gtempid"];
            $var_amount = "";
            $var_txnid = "";
            $var_method = "";
            $var_login_name = "";
            $var_password = "";
            $var_first_name = "";
            $var_last_name = "";
            // here the transaction id has to be set that comes from the payment gateway
            $var_txnid = "$txnid";

            $sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
            $sql .= "vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
            $sql .= "vDescription  ,dDateReg   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee,nRefId
							from " . TABLEPREFIX . "users where nUserId='" . $var_id . "'";

            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

            if (mysqli_num_rows($result) > 0) {
                if ($row = mysqli_fetch_array($result)) {
                    // if you have data for the transaction
                    $var_login_name = $row["vLoginName"];
                    $var_password = $row["vPassword"];
                    $var_first_name = $row["vFirstName"];
                    $var_last_name = $row["vLastName"];
                    $var_email = $row["vEmail"];
                    $totalamt = $row["nAmount"];
                    $paytype = $row["vMethod"];
                    $userUpdate = '';
                    $payTableField = '';
                    $payTableFieldValue = '';

                    //if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
                    if (DisplayLookUp('15') != '1' && DisplayLookUp('plan_system')=='yes') {
                        //calculate end date
                        switch ($_SESSION['sess_Plan_Mode']) {
                            case "M":
                                $addInterval = 'Month';
                                break;

                            case "Y":
                                $addInterval = 'Year';
                                break;
                        }//end switch

                        $expDate = mysqli_query($conn, "SELECT DATE_ADD(now(),INTERVAL 1 " . $addInterval . ") as expPlanDate") or die(mysqli_error($conn));
                        if (mysqli_num_rows($expDate) > 0) {
                            $nExpDate = mysqli_result($expDate, 0, 'expPlanDate');
                        }//end if

                        $userUpdate = ",dPlanExpDate='" . $nExpDate . "'";

                        //add one field in payment table
                        $payTableField = ',vPlanStatus,nPlanId';
                        $payTableFieldValue = ",'A','" . $_SESSION['nPlanId'] . "'";
                        $totalamt = $_SESSION['sess_Plan_Amt'];
                    }//end if register mode and escrow checking

                    if ($approval_tag == "1") {
                        $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',vDelStatus='0' " . $userUpdate . "
											WHERE nUserId='" . $row['nUserId'] . "'";
                    }//end if
                    else if ($approval_tag == "E") {
                        $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',
											vStatus='4',vDelStatus='0' " . $userUpdate . " WHERE nUserId='" . $row['nUserId'] . "'";
                    }//end if
                    else {
                        $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',
											vStatus='0',vDelStatus='0' " . $userUpdate . " WHERE nUserId='" . $row['nUserId'] . "'";
                    }//end else
                    @mysqli_query($conn, $sql) or die(mysqli_error($conn));

//                    $var_new_id = @mysqli_insert_id($conn);
                    $var_new_id= $row['nUserId'];
                    // Addition for referrals
                    $var_reg_amount = 0;
                    

                    if ($row["nRefId"] != "0") {
                         $sql = "Select nRefId,nUserId,nRegAmount from " . TABLEPREFIX . "referrals where vRegStatus='0' AND nRefId='" . $row["nRefId"] . "'";
                        $result_test = @mysqli_query($conn, $sql) or die(mysqli_error($conn));

                        if (@mysqli_num_rows($result_test) > 0) {
                            if ($row_final = @mysqli_fetch_array($result_test)) {
                                $var_reg_amount = $row_final["nRegAmount"];

                                $sql = "Update " . TABLEPREFIX . "referrals set vRegStatus='1',";
                                $sql .= "nUserRegId='" . $var_new_id . "',dRegDate=now() where nRefId='" . $row_final["nRefId"] . "'";

                                @mysqli_query($conn, $sql) or die(mysqli_error($conn));

                                $sql = "Select nUserId from " . TABLEPREFIX . "user_referral where nUserId='" . $row_final["nUserId"] . "'";
                                $result_ur = @mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                if (@mysqli_num_rows($result_ur) > 0) {
                                    $sql = "Update " . TABLEPREFIX . "user_referral set nRegCount = nRegCount + 1,nRegAmount=nRegAmount + $var_reg_amount where nUserId='" . $row_final["nUserId"] . "'";
                                }//end if
                                else {
                                    $sql = "insert into " . TABLEPREFIX . "user_referral(nUserId,nRegCount,nRegAmount) values('"
                                            . $row_final["nUserId"] . "','1','$var_reg_amount')";
                                }//end else
                                @mysqli_query($conn, $sql) or die(mysqli_error($conn));
                            }//end if
                        }//end if
                    }//end if
                    // end of referrals
                    $_SESSION["gtempid"] = "";

                    /*
                    * Fetch user language details
                    */

                    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
                    $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
                    $langRw = mysqli_fetch_array($langRs);

                    /*
                    * Fetch email contents from content table
                    */
                    if ($approval_tag == "E") {
                        $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'activationLinkOnRegister'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$_SESSION["lang_id"]."'";
                    }else{
                        $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'welcomeMailUser'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$_SESSION["lang_id"]."'";
                    }
                    $activate_link = '<a href="' . SITE_URL . '/activation.php?uid=' . $var_new_id . '&status=eactivate">Activate</a>';
                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                    $mailRw  = mysqli_fetch_array($mailRs);

                    $mainTextShow   = $mailRw['content'];

                    if(!$_SESSION["tmp_pd"] || $_SESSION["tmp_pd"]==''){
                        $mainTextShow = str_replace("Password", "", $mainTextShow);
                        $mainTextShow = str_replace("{Password}", "", $mainTextShow);
                    }

                    $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
                    $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($row["vLoginName"]),$_SESSION["tmp_pd"],$activate_link );
                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                    $mailcontent1   = $mainTextShow;

                    $subject    = $mailRw['content_title'];
                    $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

                    $StyleContent=MailStyle($sitestyle,SITE_URL);

                    $EMail = $txtEmail;

                    //readf file n replace
                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                    $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

                    $_SESSION["guserid"] = $var_new_id;

                    $sqlPayment = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId,
										vInvno " . $payTableField . ") VALUES ('R', '$txnid', ' $totalamt', '$paytype',now(), '" . $_SESSION["guserid"] . "',
										'','$Inv_id' " . $payTableFieldValue . ")";
                    $result = @mysqli_query($conn, $sqlPayment) or die(mysqli_error($conn));

                    $var_admin_email = ADMIN_EMAIL;

                    if (DisplayLookUp('4') != '') {
                        $var_admin_email = DisplayLookUp('4');
                    }//end if

                    /*
                    * Fetch email contents from content table
                    */
                    $mailRw = array();
                        $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'registrationNotificationAdmin'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$_SESSION["lang_id"]."'";

                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                    $mailRw  = mysqli_fetch_array($mailRs);

                    $mainTextShow   = $mailRw['content'];

                    $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
                    $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($var_login_name),htmlentities($var_first_name),$var_email );
                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                    $mailcontent1   = $mainTextShow;

                    $subject    = $mailRw['content_title'];
                    $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
                    $StyleContent=MailStyle($sitestyle,SITE_URL);

                    $EMail = $var_admin_email;

                    //readf file n replace
                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                    $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

                    $_SESSION["gtempid"] = $_SESSION["guserid"];
                    $_SESSION["guserid"] = "";
                    $_SESSION['nPlanId'] = '';
                    $_SESSION['sess_Plan_Mode'] = '';
                    $_SESSION['sess_Plan_Amt'] = ''; 
                    header("location:".SITE_URL."/regconfirm.php");
                    exit();
                }//end if
               
            }//end if
            else {
                header("location:".SITE_URL."/index.php?paid=yes");
                exit();
            }//end else
        }//end if
        else {
            header("location:".SITE_URL."/index.php?paid=no");
            exit();
            // $message="Please view 'My Garage' for details.";
        }//end else
        // End of the transaction  for adding a user since payment is successfull
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
    }//end function


    function proceed(cc)
    {
        if(parseInt(document.frmBuy.quantityREQD.value) > parseInt(document.frmBuy.quantityAVL.value))
        {
            alert("<?php echo ERROR_QUANTITY_INVALID; ?>");
        }//end if
        else
        {
            document.frmBuy.cctype.value=cc;
            frmBuy.submit();
        }//end else
    }//end function

    function clickConfirm(submitForm = 1)
    {
        var frm = document.frmBuy;
        var flag = false;
        var integers = /^\d+$/;

       


        if (frm.txtccno.value.length==0 || frm.txtMM.value.length==0 || frm.txtYY.value.length==0)
        {
            alert("<?php echo ERROR_CREDIT_CARD_DETAILS_INVALID; ?>");
        }//end if
        else if(!integers.test(frm.txtPostal.value)) 
        {
            
            alert("<?php echo ERROR_ZIP; ?>");
        }
        else
        {
            flag = true;
        }//end else

        if (flag==true)
        {
            document.frmBuy.postback.value='Y';
            document.frmBuy.method='post';
			if(submitForm){
        		document.frmBuy.submit();
			}else {
				return true;
			}
            //document.frmBuy.submit();
        }//end if
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
		<div class="col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
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
							<input type="hidden" name="amnt" id="amnt" value="<?php echo  $amount ?>">
							<input type="hidden" name="id" id="id" value="<?php echo  $id ?>">
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
							<h3 class="subheader row"><?php echo HEADING_PURCHASE_DETAILS; ?></h3>
							
							<div class="col-lg-6 col-sm-12 col-md-6 col-xs-12 no_padding">
								<div class="row main_form_inner">
									<label><b><?php echo TEXT_ITEM; ?></b></label>
									<?php echo TEXT_USER_REGISTRATION; ?>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12 col-md-6 col-xs-12 no_padding">
								<div class="row main_form_inner">
									<label><b><?php echo TEXT_AMOUNT; ?></b></label>
									<?php echo CURRENCY_CODE; ?><?php echo  $amount ?>
								</div>
							</div>
							
							<h3 class="subheader row"><?php echo TEXT_CREDIT_CARD_DETAILS; ?></h3>
							
							<div class="row main_form_inner">
								<label><?php echo TEXT_FIRST_NAME ?><span class="warning">*</span></label>
								<input type="text" name="txtFirstName" id="txtFirstName" value="<?php echo($userProfile['vFirstName']); ?>" size="24" maxlength="40" class="form-control">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_LAST_NAME; ?><span class="warning">*</span></label>
								<input type="text" name="txtLastName" id="txtLastName" value="<?php echo($userProfile['vLastName']); ?>" size="24" maxlength="40" class="form-control">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_CARD_NUMBER; ?><span class="warning">*</span></label>
								<input type="text" name="txtccno" class="form-control visa_amex_img" id="txtccno" size="24" maxlength="16" onBlur="javascript:checkValue(this);">
								<!--<img src="<?php echo $imagefolder ?>/images/visa_amex.gif">-->
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_CARD_VALIDATION_CODE; ?><span class="warning">*</span> &nbsp; <a href="http://www.cvvnumber.com/cvv.html" target="_blank" style="font-size:11px">What is my CVV code?</a></label>
								<input type="password" name="txtcvv2" class="form-control" id="txtcvv2" size=10 maxlength="4" onBlur="javascript:checkValue(this);">
							</div>							
							<div class="row main_form_inner">
								<label><?php echo TEXT_EXPIRATION_DATE; ?><span class="warning">*</span></label>
								<div style="padding-right: 10px;" class="col-lg-6 col-sm-6 col-md-6 col-xs-6 no_padding">
									<input type="text" name="txtMM" class="form-control" id="txtMM" size=3 maxlength="2">
								</div>
								<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6 no_padding">
									<input type="text" name="txtYY" class="form-control" id="txtYY" size=4 maxlength="4">
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
								<input type="text" name="txtEmail" class="form-control" id="txtEmail" size=24 maxlength=50  value="<?php echo($userProfile['vEmail']); ?>">
							</div>
							<div class="row main_form_inner">
								<input type="button" name="btPay" id="btPay" class="subm_btt"  value="<?php echo BUTTON_PAY_NOW; ?>"  onClick="javascript:<?php echo ($txtPayMethod != 'sp')?'clickConfirm();':'stripePay()'?>">
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
								$_SESSION['sess_gc_amount'] = $amount;
								$_SESSION['sess_gc_txtACurrency'] = PAYMENT_CURRENCY_CODE;
								$_SESSION['sess_gc_userid'] = $userid;
								$_SESSION['sess_gc_id'] = $id;

								$gc_status = ($_GET['gc_status'] != '') ? $_GET['gc_status'] : failure;

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
									$userid = $_SESSION['sess_gc_userid'];
									$id = $_SESSION['sess_gc_id'];

									if ($google_demo == "TEST")
										$server_type = "sandbox";
									else
										$server_type = "checkout";


									// Create a new shopping cart object
									$cart = new GoogleCart($google_id, $google_key, $server_type, $currency);

									// Add items to the cart
									$item_1 = new GoogleItem(SITE_NAME, // Item name
													TEXT_USER_REGISTRATION, // Item description
													1, // Quantity
													$cost); // Unit price

									$cart->AddItem($item_1);

									// continue link page
									$cart->SetContinueShoppingUrl(SECURE_SITE_URL . "/paycc.php?paytype=gc&id=" . $id . "&gc_status=success");

									$cart->AddRoundingPolicy("HALF_UP", "PER_LINE");

									$cart->SetMerchantPrivateData('paycc-' . $id . '-' . $saleid . '-' . $amount . '-' . $txtACurrency);

									// Display Google Checkout button
									echo $cart->CheckoutButtonCode("LARGE");
								}

							//end google usecase

								$_SESSION['sess_page_name'] = 'paycc.php';
								$_SESSION['sess_page_return_url_suc'] = SITE_URL . "/paycc.php?paytype=gc&id=" . $id . "&gc_status=success";
								$_SESSION['sess_page_return_url_fail'] = SECURE_SITE_URL . "/paycc.php?paytype=gc&id=" . $id . "&gc_status=failure";

								//calculation starts here
								if (isset($gc_status) && $gc_status == 'success') {
									$saleid = $saleid;
									$userid = $userid;
									$now = urldecode($dt);
									$cost = $amount;
									$txtACurrency = $txtACurrency;
									$gc_flag = true;
									$gc_err = "";
									$gc_tran = "";

									/* get the invoice number */
									$sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
									$result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
									$row1 = mysqli_fetch_array($result1);
									$Inv_id = $row1['maxinvid'];
									/*         * *********************** */

									$Cust_ip = getClientIP();
									$Company = '-NA-';
									$Phone = $var_phone;
									$Cust_id = $id;


									if ($gc_flag == true) {
										// Start of the transaction  for adding a user since payment is successfull
										$txnid = $gc_tran;
										// ////////////////////////////////////////////////////////////////////////////////////
										// check if txnid alredy there to prevent refresh
										$sql = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid'  AND vTxn_mode='" . $txtPayMethod . "'";
										$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
										// if(mysqli_num_rows($result)==0){
										if (1 == 1) {
											$var_id = $_SESSION["gtempid"];
											$var_amount = "";
											$var_txnid = "";
											$var_method = "";
											$var_login_name = "";
											$var_password = "";
											$var_first_name = "";
											$var_last_name = "";
											// here the transaction id has to be set that comes from the payment gateway
											$var_txnid = "$txnid";

											$sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
											$sql .= "vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
											$sql .= "vDescription  ,dDateReg   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee,nRefId
																					from " . TABLEPREFIX . "users where nUserId='" . $var_id . "'";

											$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

											if (mysqli_num_rows($result) > 0) {
												if ($row = mysqli_fetch_array($result)) {
													// if you have data for the transaction
													$var_login_name = $row["vLoginName"];
													$var_password = $row["vPassword"];
													$var_first_name = $row["vFirstName"];
													$var_last_name = $row["vLastName"];
													$var_email = $row["vEmail"];
													$totalamt = $row["nAmount"];
													$paytype = $row["vMethod"];

													if ($approval_tag == "1") {
														$sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',vDelStatus='0'
																													WHERE nUserId='" . $row['nUserId'] . "'";
													}//end if
													else if ($approval_tag == "E") {
														$sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',vDelStatus='0'
																													WHERE nUserId='" . $row['nUserId'] . "'";
													}//end if
													else {
														$sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',
																													vStatus='0',vDelStatus='0' WHERE nUserId='" . $row['nUserId'] . "'";
													}//end else
													@mysqli_query($conn, $sql) or die(mysqli_error($conn));

//                                                                                            $var_new_id = @mysqli_insert_id($conn);
													$var_new_id = $row['nUserId'];

													// Addition for referrals
													$var_reg_amount = 0;

													if ($row["nRefId"] != "0") {
														$sql = "Select nRefId,nUserId,nRegAmount from " . TABLEPREFIX . "referrals where vRegStatus='0' AND nRefId='" . $row["nRefId"] . "'";
														$result_test = @mysqli_query($conn, $sql) or die(mysqli_error($conn));

														if (@mysqli_num_rows($result_test) > 0) {
															if ($row_final = @mysqli_fetch_array($result_test)) {
																$var_reg_amount = $row_final["nRegAmount"];

																$sql = "Update " . TABLEPREFIX . "referrals set vRegStatus='1',";
																$sql .= "nUserRegId='" . $var_new_id . "',dRegDate=now() where nRefId='" . $row_final["nRefId"] . "'";

																@mysqli_query($conn, $sql) or die(mysqli_error($conn));

																$sql = "Select nUserId from " . TABLEPREFIX . "user_referral where nUserId='" . $row_final["nUserId"] . "'";
																$result_ur = @mysqli_query($conn, $sql) or die(mysqli_error($conn));
																if (@mysqli_num_rows($result_ur) > 0) {
																	$sql = "Update " . TABLEPREFIX . "user_referral set nRegCount = nRegCount + 1,nRegAmount=nRegAmount + $var_reg_amount where nUserId='" . $row_final["nUserId"] . "'";
																}//end if
																else {
																	$sql = "insert into " . TABLEPREFIX . "user_referral(nUserId,nRegCount,nRegAmount) values('"
																			. $row_final["nUserId"] . "','1','$var_reg_amount')";
																}//end else
																@mysqli_query($conn, $sql) or die(mysqli_error($conn));
															}//end if
														}//end if
													}//end if
													// end of referrals
													$_SESSION["gtempid"] = "";

														 /*
														* Fetch user language details
														*/

														$lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
														$langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
														$langRw = mysqli_fetch_array($langRs);

														/*
														* Fetch email contents from content table
														*/
														if ($approval_tag == "E") {
															$mailSql = "SELECT L.content,L.content_title
															  FROM ".TABLEPREFIX."content C
															  JOIN ".TABLEPREFIX."content_lang L
																ON C.content_id = L.content_id
															   AND C.content_name = 'activationLinkOnRegister'
															   AND C.content_type = 'email'
															   AND L.lang_id = '".$_SESSION["lang_id"]."'";
														}else{
															$mailSql = "SELECT L.content,L.content_title
															  FROM ".TABLEPREFIX."content C
															  JOIN ".TABLEPREFIX."content_lang L
																ON C.content_id = L.content_id
															   AND C.content_name = 'welcomeMailUser'
															   AND C.content_type = 'email'
															   AND L.lang_id = '".$_SESSION["lang_id"]."'";
														}
														$activate_link = '<a href="' . SITE_URL . '/activation.php?uid=' . $var_new_id . '&status=eactivate">Activate</a>';
														$mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
														$mailRw  = mysqli_fetch_array($mailRs);

														$mainTextShow   = $mailRw['content'];

														$arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
														$arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($var_login_name),$_SESSION["tmp_pd"],$activate_link );
														$mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

														$mailcontent1   = $mainTextShow;

														$subject    = $mailRw['content_title'];
														$subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

														$StyleContent   =  MailStyle($sitestyle,SITE_URL);

													$EMail = $var_email;
												   

													//readf file n replace
													$arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
													$arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
													$msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
													$msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

													send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

													$_SESSION["guserid"] = $var_new_id;
													$_SESSION["guserid"] = ($_SESSION["guserid"] != '') ? $_SESSION["guserid"] : $_SESSION["gtempid"];

													$sql = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId,vInvno)
																									VALUES ('R', '$txnid', ' $totalamt', '$paytype',now(), '" . $_SESSION["guserid"] . "', '','$Inv_id')";
													$result = @mysqli_query($conn, $sql) or die(mysqli_error($conn));

													$var_admin_email = ADMIN_EMAIL;

													if (DisplayLookUp('4') != '') {
														$var_admin_email = DisplayLookUp('4');
													}//end if

													/*
													* Fetch email contents from content table
													*/
													$mailRw = array();
														$mailSql = "SELECT L.content,L.content_title
														  FROM ".TABLEPREFIX."content C
														  JOIN ".TABLEPREFIX."content_lang L
															ON C.content_id = L.content_id
														   AND C.content_name = 'registrationNotificationAdmin'
														   AND C.content_type = 'email'
														   AND L.lang_id = '".$_SESSION["lang_id"]."'";

													$mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
													$mailRw  = mysqli_fetch_array($mailRs);

													$mainTextShow   = $mailRw['content'];

													$arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
													$arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($var_login_name),htmlentities($var_first_name),$var_email);
													$mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

													$mailcontent1   = $mainTextShow;

													$subject3    = $mailRw['content_title'];
													$subject3    = str_replace('{SITE_NAME}',SITE_NAME,$subject3);
													$StyleContent=MailStyle($sitestyle,SITE_URL);
													$EMail = $var_admin_email;

													//readf file n replace
													$arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
													$arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject3);
													$msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
													$msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

													send_mail($EMail, $subject3, $msgBody, SITE_EMAIL, 'Admin');

													$_SESSION["gtempid"] = $_SESSION["guserid"];
													$_SESSION["guserid"] = "";
													$_SESSION["gsaleextraid"] = "";
													$_SESSION['txtGoogleId'] = "";
													$_SESSION['txtGoogleKey'] = "";
													$_SESSION['chkGoogleSandbox'] = "";
													$_SESSION['sess_gc_saleid'] = "";
													$_SESSION['sess_gc_paytype'] = "";
													$_SESSION['sess_gc_txtPayMethod'] = "";
													$_SESSION['sess_gc_amount'] = "";
													$_SESSION['sess_gc_txtACurrency'] = "";
													$_SESSION['sess_gc_userid'] = "";
													$_SESSION['sess_gc_id'] = "";
													$_SESSION['sess_page_name'] = '';
													$_SESSION['sess_page_return_url_suc'] = '';
													$_SESSION['sess_page_return_url_fail'] = '';
													$_SESSION['sess_flag_failure'] = '';

													header("location:".SITE_URL."/regconfirm.php");
													exit();
												}//end if
											}//end if
											else {
												header("location:".SITE_URL."/index.php?paid=yes");
												exit();
											}//end else
										}//end if
										else {
											header("location:".SITE_URL."/index.php?paid=no");
											exit();
											// $message="Please view 'My Garage' for details.";
										}//end else
										// End of the transaction  for adding a user since payment is successfull
									}//end if
								}//end if
								if (isset($_SESSION['sess_flag_failure']) && $_SESSION['sess_flag_failure'] == false) {
									$gc_flag = false;
									$gc_err = ERROR_PAYMENT_PROCESS_FAILED;
								}//end else
							//calculation ends here
								?>

							<div class="full_width">
							<?php
							if ($gc_flag == false && $gc_err != '') {
								?>
									<div class="row warning"><?php echo $gc_err; ?></div>
							<?php
							}//end if
							if (isset($gc_status) && $gc_status != 'success') {
								?>
									<h3 class="subheader row"><?php echo HEADING_PAYMENT_DETAILS; ?> (<?php echo TEXT_GOOGLE_CHECKOUT; ?>)</h3>
									<div class="row main_form_inner">
										<label><?php echo TEXT_ITEM; ?></label>
										<?php echo TEXT_USER_REGISTRATION; ?>
									</div>	
									<div class="row main_form_inner">
										<label><?php echo TEXT_AMOUNT; ?></label>
										<?php echo CURRENCY_CODE; ?><?php echo  $amount ?>
									</div>	
									<div class="row main_form_inner">
										<label>
										<?php echo MESSAGE_GOOGLE_CHECKOUT_INSTRUCTION; ?> <br>
										<br>
										<b><?php echo MESSAGE_WAITING_FOR_SECURE_PAYMENT_INTERFACE; ?> ....</b><br>
										<br><br>
										<?php UseCase1(); ?>
										</label>
									</div>									
								<?php
							}//end if
							?>
							</div>
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