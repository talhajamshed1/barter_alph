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
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');
include_once('./includes/title.php');

$paymentList = true;
$Sscope = "successfee";
$payMethod = ($_GET['payMethod'] != '') ? $_GET['payMethod'] : $_POST['payMethod'];

if (trim($payMethod) != '') {
    $pageTitle = HEADING_PAYMENT_PROCESS;
}//end if

$showOnLoad = '';

if ($payMethod == 'cc' || $payMethod == 'yp' || $payMethod == 'bp') {
    //store user profile
    $userProfile = userProfiles($_SESSION["guserid"]);
    if (trim($userProfile['vCountry'])=='') $userProfile['vCountry']='United States';
    $showOnLoad = "document.getElementById('ddlCountry').value='" . $userProfile['vCountry'] . "';";
}//end if
//your pay or authorize.net calculation
if (isset($_POST['btnPay']) && $_POST['btnPay'] != '') {
    $FirstName = $_POST["txtFirstName"];
    $LastName = $_POST["txtLastName"];
    $Address = $_POST["txtAddress"];
    $City = $_POST["txtCity"];
    $State = $_POST["txtState"];
    $Zip = $_POST["txtZIP"];
    $CardNum = $_POST["txtCCNumber"];
    $Email = $_POST["txtEmail"];
    $CardCode = $_POST["txtCVV2"];
    $Country = $_POST["ddlCountry"];
    $Month = $_POST["txtMM"];
    $Year = $_POST["txtYY"];
    $txtACurrency = PAYMENT_CURRENCY_CODE;
    $txtPayMethod = $_POST["txtPayMethod"];
    $cc_tran = "";
    $cost = round(DisplayLookUp('SuccessFee'), 2);


    $Cust_ip = getClientIP();
    $Company = '-NA-';
    $Phone = $_SESSION["gphone"];
    $Cust_id = $_SESSION["guserid"];

    //checking payment mode
    if ($txtPayMethod == 'cc') {
        require("credit_inte_success.php");
    }//end if
    if ($txtPayMethod == 'bp') {
        require("Bluepay.php");
    }//end if
    if ($txtPayMethod == 'yp') {
        require("yourpay.php");
	}//end else
	
	if ($txtPayMethod == 'sp') {
		require("stripepay.php");
    }//end else

    if ($cc_flag == true) {

        if (DisplayLookUp('authmode') == "TEST") {
            $cc_tran = "TEST-" . date("Y-m-d-H-s");
        }//end if

        switch ($txtPayMethod) {
            case "cc":
                $showPayMethod = TEXT_AUTHORIZE_NET;
                break;

            case "yp":
                $showPayMethod = TEXT_YOUR_PAY;
                break;
        }//end switch

        $sqltxn = @mysqli_query($conn, "Select vTxnId from " . TABLEPREFIX . "successtransactionpayments where vTxnId ='" . $cc_tran . "' AND vMethod='" . $txtPayMethod . "'") or die(mysqli_error($conn));

        if (@mysqli_num_rows($sqltxn) > 0) {
            $cc_flag = false;
            $cc_err = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
        }//end if
        else {
            $var_date = date('m/d/Y');

            //select value from succes
            $sqlSuccess = mysqli_query($conn, "select nProdId,nAmount,nPoints from " . TABLEPREFIX . "successfee where nSId='" . $_SESSION['sess_success_fee_id'] . "'") or die(mysqli_error($conn));
            if (mysqli_num_rows($sqlSuccess) > 0) {
                $passProdId = mysqli_result($sqlSuccess, 0, 'nProdId');
                $passAmount = mysqli_result($sqlSuccess, 0, 'nAmount');
                $passPoints = mysqli_result($sqlSuccess, 0, 'nPoints');
            }//end if
            //update status in success fee table
            mysqli_query($conn, "UPDATE " . TABLEPREFIX . "successfee SET vStatus='A' WHERE nSId='" . $_SESSION['sess_success_fee_id'] . "'") or die(mysqli_error($conn));

//            $sql = "Update " . TABLEPREFIX . "users set nAccount = nAccount +  " . $passAmount . "  where  nUserId='" . $_SESSION["guserid"] . "'";
//            mysqli_query($conn, $sql) or die(mysqli_error($conn));

            //checking alredy exits
            $chkPoint = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');
            if (trim($chkPoint) != '') {
                //update points to user credit
                mysqli_query($conn, "UPDATE " . TABLEPREFIX . "usercredits set nPoints=nPoints+" . $passPoints . " WHERE
													nUserId='" . $_SESSION["guserid"] . "'") or die(mysqli_error($conn));
            }//end if
            else {
                //add points to user credit
                mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "usercredits (nPoints,nUserId) VALUES ('" . $passPoints . "','" . $_SESSION["guserid"] . "')") or die(mysqli_error($conn));
            }//end else
            //add into user table
            mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "successtransactionpayments (nUserId,nAmount,nProdId,vTxnId,vMethod,dDate,vStatus,nSId) VALUES
									('" . $_SESSION["guserid"] . "','" . round(DisplayLookUp('SuccessFee'), 2) . "','" . $passProdId . "','" . $txnid . "',
									'" . $txtPayMethod . "',now(),'A','" . $_SESSION['sess_success_fee_id'] . "')") or die(mysqli_error($conn));

            /*
            * Fetch user language details
            */

            $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
            $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
            $langRw = mysqli_fetch_array($langRs);

            /*
            * Fetch email contents from content table
            */
             $mailSql = "SELECT L.content,L.content_title
              FROM ".TABLEPREFIX."content C
              JOIN ".TABLEPREFIX."content_lang L
                ON C.content_id = L.content_id
               AND C.content_name = 'SuccessFeeMailToUser'
               AND C.content_type = 'email'
               AND L.lang_id = '".$_SESSION["lang_id"]."'";

            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

           $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}","{purchase_details}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,"Credit Card",date('m/d/Y'),$_SESSION["sess_PointAmount"],$_SESSION["guserFName"],$passPoints);
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

            $StyleContent = MailStyle($sitestyle, SITE_URL);
            $EMail = $_SESSION["guseremail"];

            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
            
             //echo "<br>".$msgBody;
            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');


            //mail sent to admin
            $var_admin_email = SITE_NAME;

            if (DisplayLookUp('4') != '') {
                $var_admin_email = DisplayLookUp('4');
            }//end if

            /*
            * Fetch email contents from content table
            */
            $mailSql = "SELECT L.content,L.content_title
              FROM ".TABLEPREFIX."content C
              JOIN ".TABLEPREFIX."content_lang L
                ON C.content_id = L.content_id
               AND C.content_name = 'SuccessFeeMailToAdmin'
               AND C.content_type = 'email'
               AND L.lang_id = '".$_SESSION["lang_id"]."'";

            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);
            
            

            $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,"Credit Card",date('m/d/Y'),$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

            $StyleContent = MailStyle($sitestyle, SITE_URL);
            $EMail = $var_admin_email;


            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);            
            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

            $cc_flag = true;

            $message = stripslashes(MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU);
            //$message .="<br>&nbsp;<br>You may visit the \"" . POINT_NAME . "\" to view details of this transaction.";
            //clear sessions
            $_SESSION['sess_success_fee_id'] = "";
        }//end else
    }//end if
    else {
        $cc_flag = false;
        if (strlen($cc_err) <= 0) {
            $cc_err.=$cc_err;
        }//end if
    }//end else
}//end if
//other payment calculation
if (isset($_POST['btnPay2']) && $_POST['btnPay2'] != '') {
    $Name = $_POST["txtName"];
    $Bank = $_POST["txtBank"];
    $Reference = $_POST["txtrefno"];
    $Date = $_POST["txtYY"] . "/" . $_POST["txtMM"] . "/" . $_POST["txtDD"];

    //if(isset($showMsg) && $showMsg==false)
    //{
    //select value from succes
    $sqlSuccess = mysqli_query($conn, "select nProdId,nAmount,nPoints from " . TABLEPREFIX . "successfee where nSId='" . $_SESSION['sess_success_fee_id'] . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($sqlSuccess) > 0) {
        $passProdId = mysqli_result($sqlSuccess, 0, 'nProdId');
        $passAmount = mysqli_result($sqlSuccess, 0, 'nAmount');
        $passPoints = mysqli_result($sqlSuccess, 0, 'nPoints');
    }//end if
    //update status in success fee table
    mysqli_query($conn, "UPDATE " . TABLEPREFIX . "successfee SET vStatus='I' WHERE nSId='" . $_SESSION['sess_success_fee_id'] . "'") or die(mysqli_error($conn));

    //add into user table
    mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "successtransactionpayments (nUserId,nAmount,nProdId,vTxnId,vMethod,dDate,vStatus,
									vName,vBank,vReferenceNo,dReferenceDate,nSId) VALUES
									('" . $_SESSION["guserid"] . "','" . round(DisplayLookUp('SuccessFee'), 2) . "','" . $passProdId . "','" . $txnid . "',
									'" . $txtPayMethod . "',now(),'P','" . addslashes($Name) . "',
									'" . addslashes($Bank) . "','" . addslashes($Reference) . "','" . addslashes($Date) . "','" . $_SESSION['sess_success_fee_id'] . "')") or die(mysqli_error($conn));
    $showPayMethod = get_payment_name($txtPayMethod);

    /*
    * Fetch user language details
    */

    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
    $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
    $langRw = mysqli_fetch_array($langRs);

    /*
    * Fetch email contents from content table
    */
    $mailSql = "SELECT L.content,L.content_title
      FROM ".TABLEPREFIX."content C
      JOIN ".TABLEPREFIX."content_lang L
        ON C.content_id = L.content_id
       AND C.content_name = 'SuccessFeeMailMailToUser'
       AND C.content_type = 'email'
       AND L.lang_id = '".$_SESSION["lang_id"]."'";

    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
    $mailRw  = mysqli_fetch_array($mailRs);

    $mainTextShow   = $mailRw['content'];

    $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}","{purchase_details}");
    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,"Credit Card",date('m/d/Y'),$_SESSION["sess_PointAmount"],$_SESSION["guserFName"],$passPoints);
    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

    $mailcontent1   = $mainTextShow;

    $subject    = $mailRw['content_title'];
    $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

    $StyleContent = MailStyle($sitestyle, SITE_URL);
    $EMail = $_SESSION["guseremail"];

    //readf file n replace
    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
    $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');


    //mail sent to admin
    $var_admin_email = SITE_NAME;

    if (DisplayLookUp('4') != '') {
        $var_admin_email = DisplayLookUp('4');
    }//end if

    /*
    * Fetch email contents from content table
    */
    $mailSql = "SELECT L.content,L.content_title
      FROM ".TABLEPREFIX."content C
      JOIN ".TABLEPREFIX."content_lang L
        ON C.content_id = L.content_id
       AND C.content_name = 'SuccessFeeMailMailToAdmin'
       AND C.content_type = 'email'
       AND L.lang_id = '".$_SESSION["lang_id"]."'";

    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
    $mailRw  = mysqli_fetch_array($mailRs);

    $mainTextShow   = $mailRw['content'];

    $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,"Credit Card",date('m/d/Y'),$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

    $mailcontent1   = $mainTextShow;

    $subject    = $mailRw['content_title'];
    $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

    $StyleContent = MailStyle($sitestyle, SITE_URL);
    $EMail = $var_admin_email;


    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
    $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
    send_mail($var_admin_email, $subject, $msgBody, SITE_EMAIL, 'Admin');

    $showMsg = true;

    $message = MESSAGE_THANKYOU_FOR_PAYMENT_WAITING_FOR_ADMIN;//MESSAGE_THANKYOU_PAYMENT_TRANSACTION_COMPLETED; 
    //clear sessions
    $_SESSION['sess_success_fee_id'] = "";
    //}//end if
}//end if
?>
<?php
if ($payMethod == 'sp') {
?>
<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<?php } ?>
<body onLoad="timersOne();<?php echo $showOnLoad; ?>">
    <script language="javascript" src="./js/validation.js"></script>
    <script language="javascript1.1" type="text/javascript">
        function validateForm()
        {
            var frm = document.frmBuy;
            if(frm.txtFirstName.value.length == 0)
            {
                alert("<?php echo ERROR_EMPTY_FIRST_NAME; ?>");
                frm.txtFirstName.focus();
                return false;
            }//end if
            else if(frm.txtLastName.value.length == 0)
            {
                alert("<?php echo ERROR_EMPTY_LAST_NAME; ?>");
                frm.txtLastName.focus();
                return false;
            }//end else if
            else if(frm.txtCCNumber.value.length == 0)
            {
                alert("<?php echo ERROR_EMPTY_CARD_NUMBER; ?>");
                frm.txtCCNumber.focus();
                return false;
            }//end else if
            else if(frm.txtCVV2.value.length == 0)
            {
                alert("<?php echo ERROR_EMPTY_CARD_VERIFICATION_NUMBER; ?>");
                frm.txtCVV2.focus();
                return false;
            }//end else if
            else if((frm.txtMM.value.length < 2) || (frm.txtYY.value.length < 2) )
            {
                alert("<?php echo ERROR_INVALID_EXPIRY_DATE; ?>");
                frm.txtMM.focus();
                return false;
            }//end else if
            else if(frm.txtAddress.value.length == 0)
            {
                alert("<?php echo ERROR_EMPTY_ADDRESS; ?>");
                frm.txtAddress.focus();
                return false;
            }//end else if
            else if(frm.txtCity.value.length == 0)
            {
                alert("<?php echo ERROR_EMPTY_CITY; ?>");
                frm.txtCity.focus();
                return false;
            }//end else if
            else if(frm.txtState.value.length == 0)
            {
                alert("<?php echo ERROR_EMPTY_STATE; ?>");
                frm.txtState.focus();
                return false;
            }//end else if
            else if(frm.txtZIP.value.length == 0)
            {
                alert("<?php echo ERROR_EMPTY_ZIP; ?>");
                frm.txtZIP.focus();
                return false;
            }//end else if
            else if(!validEmail(frm.txtEmail.value) )
            {
                alert('<?php echo ERROR_EMAIL_INVALID; ?>');
                frm.txtEmail.focus();
                return false;
            }//end else if
            else if(frm.txtPhone && frm.txtPhone.value.length == 0){
                alert("<?php echo ERROR_EMPTY_PHONE; ?>");
                frm.txtPhone.focus();
                return false;
            }//end else if
            return true;
        }

        function checkValue(t)
        {
            if(isNaN(t.value) || t.value.substring(0,1) == " " || t.value.length == 0 || parseFloat(t.value) < 0 )
            {
                if(t.name == "txtCCNumber")
                {
                    t.value="";
                }//end if
                else
                {
                    t.value="000";
                }//end else
            }//end if
        }//end fucniton

        function clickConfirm()
        {
            if(document.frmOthers.txtrefno.value.length <= 0)
            {
                alert('<?php echo ERROR_REFERENCE_EMPTY; ?>');
                document.frmOthers.txtrefno.focus();
                return false;
            }//end if
            return true;
        }//end function
    </script>
	<?php
if ($payMethod == 'sp') {
?>
<script>
Stripe.setPublishableKey("<?php echo DisplayLookUp('stripepublic'); ?>");

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
    var valid = validateForm();
	
    if(valid == true) {
        //$("#submit-btn").hide();
        //$( "#loader" ).css("display", "inline-block");
        Stripe.createToken({
            number: $jqr( "#txtCCNumber" ).val(),
            cvc: $jqr( "#txtCVV2").val(),
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
							<h3><?php echo $pageTitle; ?></h3>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
					<?php
						if (isset($msg) && $msg != '') {
							echo '<div class="row warning">' . $msg . '</div>';
						}//end if
						?>
						<?php
						//for yourpay,authorize                                                              
						if ($payMethod == 'cc' || $payMethod == 'yp' || $payMethod == 'bp' || $payMethod == 'sp') {                                                                   
							?>
						<form name="frmBuy" method="post" action="" id="<?php echo ($payMethod == 'sp')? 'frmStripePayment':''; ?>">
						<input type="hidden" name="txtPayMethod" value="<?php echo $payMethod; ?>">
						<?php
						if (isset($message) && trim($message) != '') {
							?>
								<div class="row success"><?php echo $message; ?></div>
						<?php
						}//end if
						if (isset($cc_flag) && $cc_flag == false) {
							?>
								<div class="row warning"><?php echo $cc_err; ?></div>
							<?php
							}//end if
							if ($cc_flag != true) {
								?>
							<div class="row main_form_inner">
								<h4><?php echo HEADING_PAYMENT_DETAILS; ?></h4>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_AMOUNT; ?></label>
								<?php echo CURRENCY_CODE; ?><?php echo round(DisplayLookUp('SuccessFee'), 2); ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_FIRST_NAME; ?> <span class="warning">*</span></span></label>
								<input type="text" name="txtFirstName" id="txtFirstName" value="<?php echo($userProfile['vFirstName']); ?>" size="24" maxlength="40" class="form-control">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_LAST_NAME; ?> <span class="warning">*</span></span></label>
								<input type="text" name="txtLastName" id="txtLastName" value="<?php echo($userProfile['vLastName']); ?>" size="24" maxlength="40" class="form-control">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_CARD_NUMBER; ?> <span class="warning">*</span></span></label>
								<input type=text name="txtCCNumber" class="form-control" id="txtCCNumber" size="24" maxlength="16" onBlur="javascript:checkValue(this);">
								<img src="<?php echo $imagefolder ?>/images/visa_amex.gif">
							</div>
							<div class="row main_form_inner">
								<?php echo TEXT_CARD_VALIDATION_CODE; ?> <span class="warning">*</span></span>
							</div>
							<div class="row main_form_inner">
								<label>
									<input type=password name="txtCVV2" class="form-control" id="txtCVV2" size=10 maxlength="4" onBlur="javascript:checkValue(this);">
									<a href="http://www.cvvnumber.com/cvv.html" target="_blank" style="font-size:11px">What is my CVV code?</a>
								</label>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_EXPIRATION_DATE; ?> <span class="warning">*</span></span></label>
								<div class="full_width">
									<div class="col-lg-6 col-sm-12 col-md-12 col-xs-12 row"><input type=text name="txtMM" class="form-control" id="txtMM" size=3 maxlength="2"></div>
									<div class="col-lg-6 col-sm-12 col-md-12 col-xs-12"><input type=text name="txtYY" class="form-control" id="txtYY" size=4 maxlength="4"></div>
								</div>
							</div>
							<div class="row main_form_inner">
								<h4><?php echo TEXT_BILLING_ADDRESS_DETAILS; ?></h4>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_ADDRESS; ?> <span class="warning">*</span></span></label>
								<input type="text" name="txtAddress" class="form-control" id="txtAddress" size="24" maxlength="30" value="<?php echo($userProfile['vAddress1']); ?>">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_CITY; ?> <span class="warning">*</span></span></label>
								<input type="text" name="txtCity" class="form-control" id="txtCity" size="24" maxlength="30"  value="<?php echo($userProfile['vCity']); ?>">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_STATE; ?> <span class="warning">*</span></span></label>
								<input type="text" name="txtState" class="form-control" id="txtState" size="24" maxlength=30 value="<?php echo($userProfile['vState']); ?>">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_ZIP; ?> <span class="warning">*</span></span></label>
								<input type="text" name="txtZIP" class="form-control" id="txtZIP" size="24" maxlength="10" value="<?php echo($userProfile['nZip']); ?>">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_COUNTRY; ?> <span class="warning">*</span></span></label>
								<select name="ddlCountry" class="form-control" id="ddlCountry"><?php include("includes/country_select.php"); ?></select>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_EMAIL; ?> <span class="warning">*</span></span></label>
								<input type=text name="txtEmail" class="form-control" id="txtEmail" size="24" maxlength="50" value="<?php echo($userProfile['vEmail']); ?>">
							</div>
							<div class="row main_form_inner">
							<?php
										if($payMethod == 'sp'){
										?>
										<input type="hidden" name="btnPay" value='<?php echo BUTTON_PAY_NOW; ?>'/>
										<label><input type="button" name="btnPay" id="btnPay" class="subm_btt"  value="<?php echo BUTTON_PAY_NOW; ?>"  onClick="javascript:stripePay();"></label>
										
										<?php
										}else { ?>
								<label><input type="submit" name="btnPay" id="btnPay" class="subm_btt"  value="<?php echo BUTTON_PAY_NOW; ?>" onClick="javascript:return validateForm();"></label>
								<?php
										} 
										?>
							</div>
								<?php
							}//end else
							?>
							</form>
							<?php
						}//end if
						//for other payments
						if ($payMethod == 'ca' || $payMethod == 'bu' || $payMethod == 'pc' || $payMethod == 'mo' || $payMethod == 'wt') {
							$disp_method = "";
							$showMsg = false;
							$disp_method = get_payment_name($payMethod);
							?>
							
							
							<form name="frmOthers" method="post" action="">
								<input type="hidden" name="txtPayMethod" value="<?php echo $payMethod; ?>">
								<?php
								if (isset($message) && trim($message) != '') {
									?>
							<div class="row success"><?php echo $message; ?></div>
							<?php
							}//end if
							else {
								?>

							<div class="row main_form_inner">
								<label><?php echo TEXT_AMOUNT; ?></label>
								<?php echo CURRENCY_CODE; ?><?php echo round(DisplayLookUp('SuccessFee'), 2); ?>
							</div>
							<div class="row main_form_inner">
								<h4><?php echo HEADING_PAYMENT_DETAILS; ?></h4>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_NAME; ?></label>
								<input type="text" name="txtName" id="txtName" value="" size="24" maxlength="40" class="form-control">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_BANK; ?> (<?php echo TEXT_IF_APPLICABLE; ?>)</label>
								<input type="text" name="txtBank" id="txtBank" value="" size="24" maxlength="40" class="form-control">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_REFERENCE_NUMBER; ?> <span class="warning">*</span></label>
								<input type=text name="txtrefno" class="form-control" id="txtrefno" size="24" maxlength="16">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_PAYMENT_MODE; ?></label>
								<input type=text name="txtMode" class="textbox2" id="txtMode" size=16 maxlength="40" value="<?php echo  $disp_method ?>" readonly>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>)</label>
								<input type=text name="txtMM" class="form-control" id="txtMM" size="3" maxlength="2" onBlur="javascript:checkValue(this);" value="<?php echo date('m'); ?>"> /
								<input type=text name="txtDD" class="form-control" id="txtDD" size="3" maxlength="2" onBlur="javascript:checkValue(this);" value="<?php echo date('d'); ?>"> /
								<input type=text name="txtYY" class="form-control" id="txtYY" size="4" maxlength="4" onBlur="javascript:checkValue(this);" value="<?php echo date('Y'); ?>">
							</div>
							<div class="row main_form_inner">
								<label><input type="submit" name="btnPay2" id="btnPay2" class="subm_btt"  value="<?php echo BUTTON_PAY_NOW; ?>" onClick="javascript:return clickConfirm();"></label>
							</div>
								<?php
							}//end if
							?>
						</form>

			<?php
		}//end other payment if
		//for google checkou
		if ($payMethod == 'gc') {
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

			$_SESSION['sess_gc_txtPayMethod'] = $payMethod;
			$_SESSION['sess_gc_amount'] = round(DisplayLookUp('SuccessFee'), 2);
			$_SESSION['sess_gc_txtACurrency'] = PAYMENT_CURRENCY_CODE;
			$_SESSION["sess_gc_var_title"] = 'Success Fee';
			$_SESSION["sess_gc_userid"] = $_SESSION["guserid"];

			$_SESSION['sess_success_fee_id'] = $_SESSION['sess_success_fee_id'];

			$gc_status = ($_GET['gc_status'] != '') ? $_GET['gc_status'] : 'failure';

			function UseCase1() {
				$google_id = $_SESSION['txtGoogleId']; // Merchant ID
				$google_key = $_SESSION['txtGoogleKey']; // Merchant Key
				$google_demo = $_SESSION['chkGoogleSandbox']; // "YES" if in test mode, "NO" if in live mode
				$cost = $_SESSION['sess_gc_amount']; // price
				$currency = $_SESSION['sess_gc_txtACurrency'];

				$txtPayMethod = $_SESSION['sess_gc_txtPayMethod'];
				$amount = $_SESSION['sess_gc_amount'];
				$txtACurrency = $_SESSION['sess_gc_txtACurrency'];
				$var_title = $_SESSION["sess_gc_var_title"];
				$userid = $_SESSION["sess_gc_userid"];

				$_SESSION['sess_success_fee_id'] = $_SESSION['sess_success_fee_id'];
				$_SESSION["guserFName"] = $_SESSION["sess_guserFName"];
				$_SESSION["guseremail"] = $_SESSION["sess_guseremail"];

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
								$amount); // Unit price

				$cart->AddItem($item_1);

				// continue link page
				$cart->SetContinueShoppingUrl(SECURE_SITE_URL . "/success_fee_cc.php?payMethod=gc&userid=" . $userid . "&amnt=" . $amount . "&paymethod=gc&gc_status=success&");

				$cart->AddRoundingPolicy("HALF_UP", "PER_LINE");
				// Display XML data
				// echo "<pre>";
				// echo htmlentities($cart->GetXML());
				// echo "</pre>";
				//select value from succes
				$sqlSuccess = mysqli_query($conn, "select nProdId,nAmount,nPoints from " . TABLEPREFIX . "successfee where nSId='" . $_SESSION['sess_success_fee_id'] . "'") or die(mysqli_error($conn));
				if (mysqli_num_rows($sqlSuccess) > 0) {
					$passProdId = mysqli_result($sqlSuccess, 0, 'nProdId');
					$passAmount = mysqli_result($sqlSuccess, 0, 'nAmount');
					$passPoints = mysqli_result($sqlSuccess, 0, 'nPoints');
				}//end if

				$cart->SetMerchantPrivateData('successTrans-' . $userid . '-' . '-' . round(DisplayLookUp('SuccessFee'), 2) . '-' . $txtACurrency . '-' . $_SESSION["guserFName"] . '-' . $_SESSION["guseremail"]);

				// Display Google Checkout button
				echo $cart->CheckoutButtonCode("LARGE");
			}

		//end google usecase

			$_SESSION['sess_page_name'] = 'success_fee_cc.php?payMethod=gc';
			$_SESSION['sess_page_return_url_suc'] = SITE_URL . "/success_fee_cc.php?payMethod=gc&userid=" . $userid . "&amnt=" . $amount . "&paymethod=gc&gc_status=success&";
			$_SESSION['sess_page_return_url_fail'] = SECURE_SITE_URL . "/success_fee_cc.php?payMethod=gc&userid=" . $userid . "&amnt=" . $amount . "&paymethod=gc&gc_status=failure&";

			//calculation starts here
			if (isset($gc_status) && $gc_status == 'success') {
				$txtACurrency = $txtACurrency;
				$gc_tran = "";
				$gc_flag = true;

				if ($gc_flag == true) {
					$sqltxn = @mysqli_query($conn, "Select vTxnId from " . TABLEPREFIX . "successtransactionpayments where vTxnId ='" . $gc_tran . "' AND vMethod='gc'") or die(mysqli_error($conn));

					if (@mysqli_num_rows($sqltxn) > 0) {
						$gc_flag = false;
						$gc_err = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
					}//end if
					else {
						$var_date = date('m/d/Y');

						//select value from succes
						$sqlSuccess = mysqli_query($conn, "select nProdId,nAmount,nPoints from " . TABLEPREFIX . "successfee where nSId='" . $_SESSION['sess_success_fee_id'] . "'") or die(mysqli_error($conn));
						if (mysqli_num_rows($sqlSuccess) > 0) {
							$passProdId = mysqli_result($sqlSuccess, 0, 'nProdId');
							$passAmount = mysqli_result($sqlSuccess, 0, 'nAmount');
							$passPoints = mysqli_result($sqlSuccess, 0, 'nPoints');
						}//end if
						//update status in success fee table
						mysqli_query($conn, "UPDATE " . TABLEPREFIX . "successfee SET vStatus='A' WHERE nSId='" . $_SESSION['sess_success_fee_id'] . "'") or die(mysqli_error($conn));

//						$sql = "Update " . TABLEPREFIX . "users set nAccount = nAccount +  " . $passAmount . "  where  nUserId='" . $_SESSION["guserid"] . "'";
//						mysqli_query($conn, $sql) or die(mysqli_error($conn));

						//checking alredy exits
						$chkPoint = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');
						if (trim($chkPoint) != '') {
							//update points to user credit
							mysqli_query($conn, "UPDATE " . TABLEPREFIX . "usercredits set nPoints=nPoints+" . $passPoints . " WHERE
																																		nUserId='" . $_SESSION["guserid"] . "'") or die(mysqli_error($conn));
						}//end if
						else {
							//add points to user credit
							mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "usercredits (nPoints,nUserId) VALUES ('" . $passPoints . "','" . $_SESSION["guserid"] . "')") or die(mysqli_error($conn));
						}//end else
						//add into user table
						mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "successtransactionpayments (nUserId,nAmount,nProdId,vTxnId,vMethod,dDate,vStatus,nSId) VALUES
																										('" . $_SESSION["guserid"] . "','" . round(DisplayLookUp('SuccessFee'), 2) . "','" . $passProdId . "','" . $gc_tran . "',
																										'gc',now(),'A','" . $_SESSION['sess_success_fee_id'] . "')") or die(mysqli_error($conn));

						/*
						* Fetch user language details
						*/

						$lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
						$langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
						$langRw = mysqli_fetch_array($langRs);

						/*
						* Fetch email contents from content table
						*/
						$mailSql = "SELECT L.content,L.content_title
						  FROM ".TABLEPREFIX."content C
						  JOIN ".TABLEPREFIX."content_lang L
							ON C.content_id = L.content_id
						   AND C.content_name = 'SuccessFeeMailMailToUser'
						   AND C.content_type = 'email'
						   AND L.lang_id = '".$_SESSION["lang_id"]."'";

						$mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
						$mailRw  = mysqli_fetch_array($mailRs);

						$mainTextShow   = $mailRw['content'];

						$arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}","{purchase_details}");
						$arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,"Credit Card",date('m/d/Y'),$_SESSION["sess_PointAmount"],$_SESSION["guserFName"],$passPoints);
						$mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

						$mailcontent1   = $mainTextShow;

						$subject    = $mailRw['content_title'];
						$subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

						$StyleContent = MailStyle($sitestyle, SITE_URL);
						$EMail = $_SESSION["guseremail"];

						//readf file n replace
						$arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
						$arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
						$msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
						$msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
						send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');


						//mail sent to admin
						$var_admin_email = SITE_NAME;

						if (DisplayLookUp('4') != '') {
							$var_admin_email = DisplayLookUp('4');
						}//end if

						/*
						* Fetch email contents from content table
						*/
						$mailSql = "SELECT L.content,L.content_title
						  FROM ".TABLEPREFIX."content C
						  JOIN ".TABLEPREFIX."content_lang L
							ON C.content_id = L.content_id
						   AND C.content_name = 'SuccessFeeMailMailToAdmin'
						   AND C.content_type = 'email'
						   AND L.lang_id = '".$_SESSION["lang_id"]."'";


						$mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
						$mailRw  = mysqli_fetch_array($mailRs);

						$mainTextShow   = $mailRw['content'];

						$arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
						$arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,"Credit Card",date('m/d/Y'),$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
						$mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

						$mailcontent1   = $mainTextShow;

						$subject    = $mailRw['content_title'];
						$subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

						$StyleContent = MailStyle($sitestyle, SITE_URL);
						$EMail = $var_admin_email;


						$arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
						$arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
						$msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
						$msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
						send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

						$gc_flag = true;

						$message = MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU;
						//$message .="<br>&nbsp;<br>You may visit the \"" . POINT_NAME . "\" to view details of this transaction.";

						//clear sessions
						$_SESSION['sess_success_fee_id'] = "";
					}//end else
				}//end if
				else {
					$gc_flag = false;
					if (strlen($gc_err) <= 0) {
						$gc_err.=$gc_err;
					}//end if
				}//end else
			}//end if
			//calculation ends here
			?>
						<?php
						if (isset($message) && trim($message) != '') {
							?>
							<div class="row success"><?php echo $message; ?></div>
						<?php
						}//end if
						if ($gc_flag == false && $gc_err != '') {
							?>
							<div class="row success"><?php echo $gc_err; ?></div>
						<?php
						}//end if
						if (isset($gc_status) && $gc_status != 'success') {
							?>
							
							<div class="row main_form_inner">
								<h4><?php echo HEADING_PAYMENT_DETAILS; ?></h4>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_AMOUNT; ?></label>
								<?php echo CURRENCY_CODE; ?><?php echo round(DisplayLookUp('SuccessFee'), 2); ?>
							</div>

							<div class="row main_form_inner">
								<label>
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
						}//end google chekout if
						?>
					</div>	
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>			
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