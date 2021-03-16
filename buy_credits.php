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


$paymentList = false;
$pageTitle = str_replace('{point_name}', POINT_NAME, HEADING_BUY_POINTS);
$payMethod = ($_GET['payMethod'] != '') ? $_GET['payMethod'] : $_POST['payMethod'];

if (trim($payMethod) != '') {
    $pageTitle = HEADING_PAYMENT_PROCESS;
}//end if

$showOnLoad = '';
$showPayMethod = get_payment_name($payMethod);
$Sscope = "buycredits";

if ($payMethod == 'cc' || $payMethod == 'yp' || $payMethod == 'bp' || $payMethod == 'sp') {
    //store user profile
    $userProfile = userProfiles($_SESSION["guserid"]);
    if (trim($userProfile['vCountry'])=='') $userProfile['vCountry'] = 'United States';
    $showOnLoad = "document.getElementById('ddlCountry').value='" . $userProfile['vCountry'] . "';";
}//end if

if (isset($_POST['btnGo']) && $_POST['btnGo'] != '') {
    $paymentList = true;
    $pageTitle = HEADING_PAYMENT_FORM;

    //store selected point value
    $_SESSION['sess_PointSelected'] = $_POST['ddlPoints'];
    $_SESSION['sess_PointAmount'] = round(($_POST['ddlPoints'] / DisplayLookUp('PointValue2')) * DisplayLookUp('PointValue'), 2);
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
    $cost = $_SESSION['sess_PointAmount'];

    $Cust_ip = getClientIP();
    $Company = '-NA-';
    $Phone = $_SESSION["gphone"];
    $Cust_id = $_SESSION["guserid"];

	//checking payment mode
    if ($txtPayMethod == 'cc') {
        require("credit_inte_points.php");
    }//end if
    if ($txtPayMethod == 'bp') {
        require("Bluepay.php");
	}//end if
	if ($txtPayMethod == 'sp') {
        require("stripepay.php");
    }//end if
    if ($txtPayMethod == 'yp') {
        require("yourpay.php");
    }//end else

    if ($cc_flag == true) {

        if (DisplayLookUp('authmode') == "TEST" || $paymentMode == "TEST") {
            $cc_tran = "TEST-" . date("m/d/Y H:i:s");
        }//end if

        switch ($txtPayMethod) {
            case "cc":
            $showPayMethod = TEXT_AUTHORIZE_NET;
            break;

            case "yp":
            $showPayMethod = TEXT_YOUR_PAY;
            break;
            case "sp":
            $showPayMethod = TEXT_STRIPE;
            break;
        }//end switch

        $sqltxn = @mysqli_query($conn, "Select vTxnId from " . TABLEPREFIX . "creditpayments where vTxnId ='" . $cc_tran . "' AND vMethod='" . $txtPayMethod . "'") or die(mysqli_error($conn));

        if (@mysqli_num_rows($sqltxn) > 0) {
            //$cc_flag = false;
            //$cc_err = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
            $cc_flag = true;

            $message = str_replace('{amount}', CURRENCY_CODE . $_SESSION['sess_PointAmount'],str_replace('{point_name}', $_SESSION['sess_PointSelected']." ".POINT_NAME,MESSAGE_SUCCESS_PURCHASED_POINTS));

            //clear sessions
            $_SESSION['sess_PointSelected'] = "";
            $_SESSION['sess_PointAmount'] = "";
        }//end if
        else {
            $var_date = date('m/d/Y');

            //checking alredy exits
            $chkPoint = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');
            if (trim($chkPoint) != '') {
                //update points to user credit
                mysqli_query($conn, "UPDATE " . TABLEPREFIX . "usercredits set nPoints=nPoints+" . $_SESSION['sess_PointSelected'] . " WHERE
                    nUserId='" . $_SESSION["guserid"] . "'") or die(mysqli_error($conn));
            }//end if
            else {
                //add points to user credit
                mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "usercredits (nPoints,nUserId) VALUES ('" . $_SESSION['sess_PointSelected'] . "','" . $_SESSION["guserid"] . "')") or die(mysqli_error($conn));
            }//end else
            //added purchase date point and amount conversion status
            $vComments = CURRENCY_CODE . DisplayLookUp('PointValue') . '&nbsp;=&nbsp;' . DisplayLookUp('PointValue2') . '&nbsp;' . POINT_NAME;

            //add into user table
            mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "creditpayments (nUserId,nAmount,nPoints,vTxnId,vMethod,dDate,vCurrentTransaction,vStatus) VALUES
               ('" . $_SESSION["guserid"] . "','" . $_SESSION['sess_PointAmount'] . "','" . $_SESSION['sess_PointSelected'] . "','" . $cc_tran . "',
               '" . $txtPayMethod . "',now(),'" . addslashes($vComments) . "','A')") or die(mysqli_error($conn));



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
            AND C.content_name = 'pointsPurchasedMailToUser'
            AND C.content_type = 'email'
            AND L.lang_id = '".$_SESSION["lang_id"]."'";

            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{POINT_NAME}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,POINT_NAME,$showPayMethod,date('m/d/Y'),CURRENCY_CODE.$_SESSION["sess_PointAmount"],$_SESSION["gloginname"]);
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
            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');


            //mail sent to admin
            $var_admin_email = SITE_NAME;

            if (DisplayLookUp('4') != '') {
                $var_admin_email = DisplayLookUp('4');
            }//end if

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
            AND C.content_name = 'pointsPurchasedMailToAdmin'
            AND C.content_type = 'email'
            AND L.lang_id = '".$_SESSION["lang_id"]."'";

            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{POINT_NAME}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,POINT_NAME,$showPayMethod,date('m/d/Y'),CURRENCY_CODE.$_SESSION["sess_PointAmount"],$_SESSION["gloginname"]);
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

            $cc_flag = true;

            $message = str_replace('{amount}', CURRENCY_CODE . $_SESSION['sess_PointAmount'],str_replace('{point_name}',$_SESSION['sess_PointSelected']." ".POINT_NAME,MESSAGE_SUCCESS_PURCHASED_POINTS));

            //clear sessions
            $_SESSION['sess_PointSelected'] = "";
            $_SESSION['sess_PointAmount'] = "";
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

    $txtACurrency = PAYMENT_CURRENCY_CODE;
    $txtPayMethod = $_POST["txtPayMethod"];

    $Name = $_POST["txtName"];
    $Bank = $_POST["txtBank"];
    $Reference = $_POST["txtrefno"];
    $Date = $_POST["txtYY"] . "/" . $_POST["txtMM"] . "/" . $_POST["txtDD"];

    //if(isset($showMsg) && $showMsg==false)
    //{
    //added purchase date point and amount conversion status
    $vComments = CURRENCY_CODE . DisplayLookUp('PointValue') . '&nbsp;=&nbsp;' . DisplayLookUp('PointValue2') . '&nbsp;' . POINT_NAME;

    //add into user table
    mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "creditpayments (nUserId,nAmount,nPoints,vTxnId,vMethod,dDate,vCurrentTransaction,vStatus,
      vName,vBank,vReferenceNo,dReferenceDate) VALUES
      ('" . $_SESSION["guserid"] . "','" . $_SESSION['sess_PointAmount'] . "','" . $_SESSION['sess_PointSelected'] . "','" . $cc_tran . "',
      '" . $txtPayMethod . "',now(),'" . addslashes($vComments) . "','P','" . addslashes($Name) . "',
      '" . addslashes($Bank) . "','" . addslashes($Reference) . "','" . addslashes($Date) . "')") or die(mysqli_error($conn));
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
    AND C.content_name = 'pointsPurchasedMailToUser'
    AND C.content_type = 'email'
    AND L.lang_id = '".$_SESSION["lang_id"]."'";

    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
    $mailRw  = mysqli_fetch_array($mailRs);

    $mainTextShow   = $mailRw['content'];

    $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,$showPayMethod,date('m/d/Y'),CURRENCY_CODE.$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
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
    AND C.content_name = 'pointsPurchasedMailToAdmin'
    AND C.content_type = 'email'
    AND L.lang_id = '".$_SESSION["lang_id"]."'";

    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
    $mailRw  = mysqli_fetch_array($mailRs);

    $mainTextShow   = $mailRw['content'];

    $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{POINT_NAME}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,POINT_NAME,$showPayMethod,date('m/d/Y'),CURRENCY_CODE.$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
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

    $showMsg = true;

    //$message = "You have successfully purchased ".POINT_NAME." by paying an amount of ".CURRENCY_CODE.$_SESSION['sess_PointAmount'];
    $message = MESSAGE_THANKYOU_FOR_PAYMENT_WAITING_FOR_ADMIN;// MESSAGE_THANKYOU_PAYMENT_TRANSACTION_COMPLETED;
    //clear sessions
    $_SESSION['sess_PointSelected'] = "";
    $_SESSION['sess_PointAmount'] = "";
    //}//end if
}//end if
?>
<style type="text/css">
    #pageloaddiv {
        position: fixed;
        left: 0px;
        top: 50px;
        width: 100%;
        height: 100%;
        z-index: 1000;
        background: url('images/pageloader.gif') no-repeat center center;
    }
</style>
<?php
if ($payMethod == 'sp') {
    ?>
    <script type="text/javascript" src="https://js.stripe.com/v1/"></script>
    <?php } ?>
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
            //else if(frm.txtPhone.value.length == 0){
               // alert("<?php echo ERROR_EMPTY_PHONE; ?>");
              //  frm.txtPhone.focus();
               // return false;
           // }//end else if

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
    <?php include_once('./includes/top_header.php'); ?>
    <script src="js/responsive-tabs-2.3.2.js"></script>
    <script type="text/javascript">
     // var $jqr1=jQuery.noConflict();
     $jqr(document).ready(function() {
        $jqr('#s1').addClass("active");
    });
</script>
<script language="Javascript">
    $jqr(document).ready(function(){
        //$jqr("#pageloaddiv").fadeOut(2000);
        $jqr("form#ppform").submit();
         //setTimeout($jqr("form#ppform").submit(),1000);
         
         $jqr('form#ppform').submit(function() {
            var pass = true;
                //some validations

                if(pass == false){
                    return false;
                }
               // $jqr("form#ppform").submit();
               $jqr("#pageloaddiv").fadeOut(2000);

               return true;
           });
     });
    //onLoad="javascript:document.frmPay.submit();"
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

<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">
               <div class="row">
                  <div class="col-lg-12">
                     <div class="innersubheader2">
                        <h3><?php echo $pageTitle; ?></h3>
                    </div>
                </div>
            </div>
            <div class="space">&nbsp;</div>
            <div class="row">
              <div class="col-lg-12">
                 <?php include('./includes/points_menu.php'); ?>                     
                 <div class="tabContent tabcontent_wrapper">
                    <div class="table-responsive">
                     <table width="100%"  border="0" cellspacing="0" cellpadding="4" class="table table-bordered table-hover">
                        <?php
                        if (isset($msg) && $msg != '') {
                           echo '<tr align="center"  class="warning"><td colspan="3"><b>' . $msg . '</b></td></tr>';
								}//end if
								if ($paymentList != true && trim($payMethod) == '') {
									?>
                                    <form action="" method="post" name="frmSend" onSubmit="return Validate();">
                                       <tr align="right" >
                                          <td width="32%" align="left"><?php echo str_replace('{point_name}',POINT_NAME,TEXT_SELECT_POINT); ?></td>
                                          <td width="78%" colspan="2" align="left"><select name="ddlPoints" class="comm_input width2"><?php
													//create points list
                                           for ($i = DisplayLookUp('PointValue2'); $i <= 100; $i+=DisplayLookUp('PointValue2')) {
                                              echo '<option value="' . $i . '">' . $i . '</option>';
													}//end for loop
													?>
                                             </select></td>
                                         </tr>
                                         <tr align="right" >
                                          <td align="left">&nbsp;</td>
                                          <td colspan="2" align="left"><input type="submit" name="btnGo" value="<?php echo BUTTON_BUY; ?>" class="submit"></td>
                                      </tr>
                                  </form>
                                  <?php
								}//end if
								if ($paymentList == true) {
									?>
                                    <tr >
                                       <td width="32%" align="left"><?php echo TEXT_AMOUNT;//echo $amountToPay; ?></td>
                                       <td width="80%" align="left"><?php echo CURRENCY_CODE; ?><?php echo round($_SESSION['sess_PointAmount'], 2); ?></td>
                                   </tr>
                                   <?php
                                   if (DisplayLookUp('paypalsupport') == "YES") {
                                      ?>
                                      <tr >
                                       <td align="left"><?php echo TEXT_USE_PAYPAL; ?></td>
                                       <td align="left"><a href="buy_credits.php?payMethod=pp"><img src="images/x-click-but20.gif" border="0" alt=""></a></td>
                                   </tr>
                                   <?php
                               }
                               if (DisplayLookUp('Enable Escrow') == 'Yes') {
                                  if (PAYMENT_CURRENCY_CODE == 'USD') {
                                     if (DisplayLookUp('authsupport') == "YES") {
                                        ?>
                                        <tr >
                                           <td height="40" align="left"><?php echo TEXT_USE_CREDIT_CARDS; ?></td>
                                           <td align="left"><a href="buy_credits.php?payMethod=cc"><img src="images/cc.jpg" border="0" alt=""></a></td>
                                       </tr>
                                       <?php
											}//end if
										}//end if
										if (DisplayLookUp('enablestripe') == "Y") {
											?>
                                         <tr >
                                            <td height="40" align="left"><?php echo TEXT_USE_STRIPE; ?></td>
                                            <td align="left"><a href="buy_credits.php?payMethod=sp"><img src="images/cc.jpg" border="0" alt=""></a></td>
                                        </tr>
                                        <?php
                                    }
                                    if (DisplayLookUp('yourpaysupport') == "YES") {
                                     ?>
                                     <tr >
                                       <td align="left"><?php echo TEXT_USE_YOURPAY; ?></td>
                                       <td align="left"><a href="buy_credits.php?payMethod=yp"><img src="images/cc.jpg" border="0" alt=""></a></td>
                                   </tr>
                                   <?php
										}//end if
										if (DisplayLookUp('enablebluepay') == "Y") {
											?>
                                            <tr >
                                               <td align="left"><?php echo TEXT_USE_BLUEPAY; ?></td>
                                               <td align="left"><a href="buy_credits.php?payMethod=bp"><img src="images/cc.jpg" border="0" alt=""></a></td>
                                           </tr>
                                           <?php
										}//end if
										/*if (DisplayLookUp('googlesupport') == "YES") {
											?>
								<tr >
									<td align="left"><?php echo TEXT_USE_GOOGLE_CHECKOUT; ?></td>
									<td align="left"><a href="<?php echo $secureserver; ?>/buy_credits.php?payMethod=gc"><img src="images/checkout.gif" border="0" alt=""></a></td>
								</tr>
											<?php
										}//end if*/
										if (DisplayLookUp('enableworldpay') == "Y") {
											?>
                                            <tr >
                                               <td align="left"><?php echo TEXT_USE_WORLDPAY; ?></td>
                                               <td align="left"><a href="buy_credits.php?payMethod=wp"><img src="images/cc.jpg" width="180" height="31" title="" border="0" alt=""></a></td>
                                           </tr>
                                           <?php
										}//end if
										if (DisplayLookUp('otherpayment') == 'YES') {
											?>
                                            <tr >
                                               <td colspan="2" align="left" class="subheader"><?php echo TEXT_OTHER_PAYMENTS; ?></td>
                                           </tr>
                                           <tr >
                                               <td align="left"><?php echo TEXT_USE_CASHIERS_CHECK; ?></td>
                                               <td align="left"><a href="buy_credits.php?payMethod=ca"><img src="images/cashierscheque.gif" border="0"></a></td>
                                           </tr>
                                           <tr >
                                               <td align="left"><?php echo TEXT_USE_BUSINESS_CHECK; ?></td>
                                               <td align="left"><a href="buy_credits.php?payMethod=bu"><img src="images/businesscheque.gif" border="0"></a></td>
                                           </tr>
                                           <tr >
                                               <td align="left"><?php echo TEXT_USE_PERSONAL_CHECK; ?></td>
                                               <td align="left"><a href="buy_credits.php?payMethod=pc"><img src="images/personalcheck.gif" border="0"></a></td>
                                           </tr>
                                           <tr >
                                               <td align="left"><?php echo TEXT_USE_MONEY_ORDER; ?></td>
                                               <td align="left"><a href="buy_credits.php?payMethod=mo"><img src="images/moneyorder.gif" border="0"></a></td>
                                           </tr>
                                           <tr >
                                               <td align="left"><?php echo TEXT_USE_MONEY_ORDER; ?></td>
                                               <td align="left"><a href="buy_credits.php?payMethod=wt"><img src="images/wireftransfer.gif" border="0"></a></td>
                                           </tr>
											<?php }//end if
                                       }//end if ?>

                                       <?php
								}//end else
								//for payapal payment
								if ($payMethod == 'pp') {
									$txtPaypalEmail = DisplayLookUp('paypalemail');
									$txtPaypalSandbox = DisplayLookUp('paypalmode');
									$paypalenabled = DisplayLookUp('paypalsupport');

									if ($paypalenabled != "YES") {
										header('location:'.SITE_URL.'/buy_credits.php');
										exit();
									}
									if ($txtPaypalSandbox == "TEST") {
										$paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
										$paypalbuttonurl = "https://www.sandbox.paypal.com/en_US/i/btn/x-click-but23.gif";
									}//end if
									else {
										$paypalurl = "https://www.paypal.com/cgi-bin/webscr";
										$paypalbuttonurl = "https://www.paypal.com/en_US/i/btn/x-click-but23.gif";
									}//end else
									//generate randId
									$randId = rand(1000, time());


									?>
                                    <tr align="center" >
                                       <td colspan="2">
                                        <div id="pageloaddiv"></div>
                                        <form name="frmPay"  action="<?php echo  $paypalurl ?>" method="post" id="ppform">
                                         <input type="hidden" name="cmd" value="_xclick">
                                         <input type="hidden" name="business" value="<?php echo $txtPaypalEmail; ?>">
                                         <input type="hidden" name="os0" maxlength="200" value="<?php echo  $_SESSION["guserid"] ?>">
                                         <input type="hidden" name="item_name" value="<?php echo  htmlentities(POINT_NAME) ?>">
                                         <input type="hidden" name="item_number" value="<?php echo $randId; ?>">
                                         <input type="hidden" name="amount" value="<?php echo round($_SESSION['sess_PointAmount'], 2); ?>">
                                         <input type="hidden" name="no_shipping" value="1">
                                         <input type="hidden" name="custom" value="<?php echo $customValues; ?>">
                                         <input type="hidden" name="notify_url" value="<?php echo SECURE_SITE_URL; ?>/credits_ipn.php">
                                         <input type="hidden" name="return" value="<?php echo SECURE_SITE_URL; ?>/credits_success.php">

                                         <input type="hidden" name="cancel_return" value="<?php echo SECURE_SITE_URL; ?>/credits_failure.php">
                                         <input type="hidden" name="no_note" value="1">
                                         <input type="hidden" name="currency_code" value="<?php echo PAYMENT_CURRENCY_CODE; ?>">
                                         <input type="hidden" name="bn" value="armiasystems_shoppingcart_wps_us">
                                         <input type="image" src="<?php echo $paypalbuttonurl; ?>" border="0" name="submit" alt="" height="0" width="0">
                                     </form>

                                     <script language="javascript1.1" type="text/javascript">
                                         document.frmPay.submit();
                                     </script></td>
                                 </tr>
                                 <?php
								}//end if
								//for worldpay payment
								if ($payMethod == 'wp') {
									//worldpay details
									$txtWorldInstId = DisplayLookUp('worldpayid');
									$txtWorldEmailId = DisplayLookUp('worldpayemail');
									$txtWorldTransMode = DisplayLookUp('worldpaytransmode');
									$txtWorldpaySandbox = DisplayLookUp('worldpaydemo');
									$worldpayenabled = DisplayLookUp('enableworldpay');

									if ($txtWorldpaySandbox == "YES") {
										$worldpayserver = "https://select-test.worldpay.com/wcc/purchase";
										$txtWorldpaySandbox2 = '100';
									}//end if
									else {
										$worldpayserver = "https://select.worldpay.com/wcc/purchase";
										$txtWorldpaySandbox2 = '0';
									}//end if

									if ($worldpayenabled != "Y") {
										header('location:'.SITE_URL.'/buy_credits.php');
										exit();
									}//end if
									//generate randId
									$randId = rand(1000, time());
									?>
                                    <tr align="center" >
                                       <td colspan="2">
                                          <form action="<?php echo $worldpayserver; ?>" name="BuyForm" method="POST">
                                             <input type="hidden" name="instId"  value="<?php echo $txtWorldInstId; ?>">
                                             <input type="hidden" name="cartId" value="<?php echo htmlentities(POINT_NAME); ?>">
                                             <input type="hidden" name="currency" value="<?php echo PAYMENT_CURRENCY_CODE; ?>">
                                             <input type="hidden" NAME="email" VALUE="<?php echo $txtWorldEmailId; ?>">
                                             <input type="hidden" name="amount"  value="<?php echo round($_SESSION['sess_PointAmount'], 2); ?>">
                                             <input type="hidden" name="testMode" value="<?php echo $txtWorldpaySandbox2; ?>">
                                             <input type="hidden" NAME="MC_ORDERID" VALUE="<?php echo $randId; ?>">
                                             <input type='hidden' name='authMode' value="<?php echo $txtWorldTransMode; ?>">
                                             <input type="hidden" name="MC_callback" value="<?php echo SITE_URL . "/credits_success_wp.php"; ?>">
                                             <input name="imageField" type="image" src="./images/cc.jpg" width="180" height="31" border="0"  title="" alt="">
                                         </form>
                                         <script language="javascript1.1" type="text/javascript">
                                             document.BuyForm.submit();
                                         </script></td>
                                     </tr>
                                     <?php
								}//end worldpay
								//for yourpay,authorize,bluepay
								if ($payMethod == 'cc' || $payMethod == 'yp' || $payMethod == 'bp' || $payMethod == 'sp') {
									?>
                                    <form name="frmBuy" method="post" action="" id="<?php echo ($payMethod == 'sp')? 'frmStripePayment':''; ?>">
                                       <input type="hidden" name="txtPayMethod" value="<?php echo $payMethod; ?>">
                                       <?php
                                       if (isset($message) && trim($message) != '') {
                                         ?>
                                         <tr >
                                         <td colspan="2" align="center" class="success"><?php echo $message; ?></td>
                                      </tr>
                                      <?php
										}//end if
										if (isset($cc_flag) && $cc_flag == false) {
											?>
                                           <tr >
                                              <td colspan="2" align="center" class="warning"><?php echo $cc_err; ?></td>
                                          </tr>
                                          <?php
										}//end if
										if ($cc_flag != true) {
											?>

                                           <tr >
                                              <td colspan="2" align="left" class="subheader"><?php echo HEADING_PAYMENT_DETAILS; ?></td>
                                          </tr>
                                          <tr >
                                              <td align="left"><?php echo TEXT_AMOUNT; ?></td>
                                              <td align="left"><?php echo CURRENCY_CODE; ?><?php echo round($_SESSION['sess_PointAmount'], 2); ?></td>
                                          </tr>
                                          <tr >
                                              <td colspan="2" align="left" class="subheader"><?php echo TEXT_CREDIT_CARD_DETAILS; ?></td>
                                          </tr>
                                          <tr >
                                              <td align="left"><?php echo TEXT_FIRST_NAME; ?> <span class="warning">*</span></span></td>
                                              <td><input type="text" name="txtFirstName" id="txtFirstName" value="<?php echo($userProfile['vFirstName']); ?>" size="24" maxlength="40" class="textbox2"></td>
                                          </tr>
                                          <tr >
                                              <td align="left"><?php echo TEXT_LAST_NAME; ?> <span class="warning">*</span></span></td>
                                              <td><input type="text" name="txtLastName" id="txtLastName" value="<?php echo($userProfile['vLastName']); ?>" size="24" maxlength="40" class="textbox2"></td>
                                          </tr>
                                          <tr >
                                              <td align="left"><?php echo TEXT_CARD_NUMBER; ?> <span class="warning">*</span></span></td>
                                              <td><input type=text name="txtCCNumber" class="textbox2" id="txtCCNumber" size="24" maxlength="16" onBlur="javascript:checkValue(this);"> <img src="<?php echo $imagefolder ?>/images/visa_amex.gif"></td>
                                          </tr>
                                          <tr >
                                              <td align="left"><?php echo TEXT_CARD_VALIDATION_CODE; ?> <span class="warning">*</span></span></td>
                                              <td>
                                                 <input type=password name="txtCVV2" class="textbox2" id="txtCVV2" size=10 maxlength="4" onBlur="javascript:checkValue(this);">
                                                 <a href="http://www.cvvnumber.com/cvv.html" target="_blank" style="font-size:11px">What is my CVV code?</a>

                                             </td>
                                         </tr>
                                         <tr >
                                          <td align="left"><?php echo TEXT_EXPIRATION_DATE; ?> <span class="warning">*</span></span></td>
                                          <td><input type=text name="txtMM" class="textbox2" id="txtMM" size=3 maxlength="2"> /
                                             <input type=text name="txtYY" class="textbox2" id="txtYY" size=4 maxlength="4"></td>
                                         </tr>
                                         <tr >
                                          <td colspan="2" align="left" class="subheader"><?php echo TEXT_BILLING_ADDRESS_DETAILS; ?></td>
                                      </tr>
                                      <tr >
                                          <td align="left"><?php echo TEXT_ADDRESS; ?> <span class="warning">*</span></span></td>
                                          <td align="left"><input type="text" name="txtAddress" class="textbox2" id="txtAddress" size="24" maxlength="30" value="<?php echo($userProfile['vAddress1']); ?>"></td>
                                      </tr>
                                      <tr >
                                          <td align="left"><?php echo TEXT_CITY; ?> <span class="warning">*</span></span></td>
                                          <td align="left"><input type="text" name="txtCity" class="textbox2" id="txtCity" size="24" maxlength="30"  value="<?php echo($userProfile['vCity']); ?>"></td>
                                      </tr>
                                      <tr >
                                          <td align="left"><?php echo TEXT_STATE; ?> <span class="warning">*</span></span></td>
                                          <td align="left"><input type="text" name="txtState" class="textbox2" id="txtState" size="24" maxlength=30 value="<?php echo($userProfile['vState']); ?>"></td>
                                      </tr>
                                      <tr >
                                          <td align="left"><?php echo TEXT_ZIP; ?> <span class="warning">*</span></span></td>
                                          <td align="left"><input type="text" name="txtZIP" class="textbox2" id="txtZIP" size="24" maxlength="10" value="<?php echo($userProfile['nZip']); ?>"></td>
                                      </tr>
                                      <tr >
                                          <td align="left"><?php echo TEXT_COUNTRY; ?> <span class="warning">*</span></span></td>
                                          <td align="left">
                                             <select name="ddlCountry" class="textbox22" id="ddlCountry">
                                              <?php include("includes/country_select.php"); ?>
                                          </select>
                                      </td>
                                  </tr>
                                  <tr >
                                      <td align="left"><?php echo TEXT_EMAIL; ?> <span class="warning">*</span></span></td>
                                      <td align="left"><input type=text name="txtEmail" class="textbox2" id="txtEmail" size="24" maxlength="50" value="<?php echo($userProfile['vEmail']); ?>"></td>
                                  </tr>
                                  <tr >
                                      <td align="left">&nbsp;</td>
                                      <?php
                                      if($payMethod == 'sp'){
                                          ?>
                                          <input type="hidden" name="btnPay" value='<?php echo BUTTON_PAY_NOW; ?>'/>
                                          <td align="left"><input type="button" name="btnPay" id="SbtnPay" class="submit"  value="<?php echo BUTTON_PAY_NOW; ?>"  onClick="javascript:stripePay();"></td>

                                          <?php
                                      }else {
                                          ?>
                                          <td align="left"><input type="submit" name="btnPay" id="btnPay" class="submit"  value="<?php echo BUTTON_PAY_NOW; ?>" onClick="javascript:return validateForm();"></td>
                                          <?php
                                      } 
                                      ?>
                                  </tr>
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
                                         <tr >
                                          <td colspan="2" align="center" class="success"><b><?php echo $message; ?></b></td>
                                      </tr>
                                      <?php
										}//end if
										else {
											?>

                                           <tr >
                                              <td align="left"><?php echo TEXT_AMOUNT; ?></td>
                                              <td align="left"><?php echo CURRENCY_CODE; ?><?php echo round($_SESSION['sess_PointAmount'], 2); ?></td>
                                          </tr>
                                          <tr >
                                              <td colspan="2" align="left" class="subheader"><?php echo HEADING_PAYMENT_DETAILS; ?></td>
                                          </tr>
                                          <tr >
                                              <td align="left"><?php echo TEXT_NAME; ?></td>
                                              <td align="left"><input type="text" name="txtName" id="txtName" value="" size="24" maxlength="40" class="textbox2"></td>
                                          </tr>
                                          <tr >
                                              <td align="left"><?php echo TEXT_BANK; ?> (<?php echo TEXT_IF_APPLICABLE; ?>)</td>
                                              <td align="left"><input type="text" name="txtBank" id="txtBank" value="" size="24" maxlength="40" class="textbox2"></td>
                                          </tr>
                                          <tr >
                                              <td align="left"><?php echo TEXT_REFERENCE_NUMBER; ?> <span class="warning">*</span></td>
                                              <td align="left"><input type=text name="txtrefno" class="textbox2" id="txtrefno" size="24" maxlength="16"></td>
                                          </tr>
                                          <tr >
                                              <td align="left"><?php echo TEXT_PAYMENT_MODE; ?></td>
                                              <td align="left"><input type=text name="txtMode" class="textbox2" id="txtMode" size=16 maxlength="40" value="<?php echo  $disp_method ?>" readonly></td>
                                          </tr>
                                          <tr >
                                              <td align="left"><?php echo TEXT_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>)</td>
                                              <td align="left"><input type=text name="txtMM" class="textbox2" id="txtMM" size="3" maxlength="2" onBlur="javascript:checkValue(this);" value="<?php echo date('m'); ?>"> /
                                                 <input type=text name="txtDD" class="textbox2" id="txtDD" size="3" maxlength="2" onBlur="javascript:checkValue(this);" value="<?php echo date('d'); ?>"> /
                                                 <input type=text name="txtYY" class="textbox2" id="txtYY" size="4" maxlength="4" onBlur="javascript:checkValue(this);" value="<?php echo date('Y'); ?>"></td>
                                             </tr>
                                             <tr >
                                              <td align="left">&nbsp;</td>
                                              <td align="left"><input type="submit" name="btnPay2" id="btnPay2" class="submit"  value="<?php echo BUTTON_PAY_NOW; ?>" onClick="javascript:return clickConfirm();"></td>
                                          </tr>
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
									$_SESSION['sess_gc_amount'] = round($_SESSION['sess_PointAmount'], 2);
									$_SESSION['sess_gc_txtACurrency'] = PAYMENT_CURRENCY_CODE;
									$_SESSION["sess_gc_var_title"] = POINT_NAME;
									$_SESSION["sess_gc_userid"] = $_SESSION["guserid"];

									$_SESSION['sess_sess_PointSelected'] = $_SESSION['sess_PointSelected'];
									$_SESSION["sess_guserFName"] = $_SESSION["guserFName"];
									$_SESSION["sess_guseremail"] = $_SESSION["guseremail"];

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

										$_SESSION['sess_PointSelected'] = $_SESSION['sess_sess_PointSelected'];
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
										$cart->SetContinueShoppingUrl(SECURE_SITE_URL . "/buy_credits.php?payMethod=gc&userid=" . $userid . "&amnt=" . $amount . "&paymethod=gc&gc_status=success&");

										$cart->AddRoundingPolicy("HALF_UP", "PER_LINE");
										// Display XML data
										// echo "<pre>";
										// echo htmlentities($cart->GetXML());
										// echo "</pre>";

										$cart->SetMerchantPrivateData('points-' . $userid . '-' . $_SESSION['sess_PointSelected'] . '-' . $amount . '-' . $txtACurrency . '-' . $_SESSION["guserFName"] . '-' . $_SESSION["guseremail"]);

										// Display Google Checkout button
										echo $cart->CheckoutButtonCode("LARGE");
									}

									//end google usecase

									$_SESSION['sess_page_name'] = 'buy_credits.php?payMethod=gc';
									$_SESSION['sess_page_return_url_suc'] = SITE_URL . "/buy_credits.php?payMethod=gc&userid=" . $userid . "&amnt=" . $amount . "&paymethod=gc&gc_status=success&";
									$_SESSION['sess_page_return_url_fail'] = SECURE_SITE_URL . "/buy_credits.php?payMethod=gc&userid=" . $userid . "&amnt=" . $amount . "&paymethod=gc&gc_status=failure&";

									//calculation starts here
									if (isset($gc_status) && $gc_status == 'success') {
										$txtACurrency = $txtACurrency;
										$gc_tran = "";
										$gc_flag = true;

										if ($gc_flag == true) {
											$message = str_replace('{amount}', CURRENCY_CODE . $_SESSION['sess_PointAmount'],str_replace('{point_name}',$_SESSION['sess_PointSelected']." ".POINT_NAME,MESSAGE_SUCCESS_PURCHASED_POINTS));
											//clear sessions
                                            $_SESSION['sess_PointSelected'] = "";
                                            $_SESSION['sess_PointAmount'] = "";
											/*
										   
											$sqltxn = @mysqli_query($conn, "Select vTxnId from " . TABLEPREFIX . "creditpayments where vTxnId ='" . $gc_tran . "' AND vMethod='gc'") or die(mysqli_error($conn));
	
											if (@mysqli_num_rows($sqltxn) > 0) {
												//$gc_flag = false;
												//$gc_err = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
												$gc_flag = true;
	
												$message = str_replace('{amount}', CURRENCY_CODE . $_SESSION['sess_PointAmount'],str_replace('{point_name}',POINT_NAME,MESSAGE_SUCCESS_PURCHASED_POINTS));
	
												//clear sessions
												$_SESSION['sess_PointSelected'] = "";
												$_SESSION['sess_PointAmount'] = "";
											}//end if
											else {
												$var_date = date('m/d/Y');
	
												//checking alredy exits
												$chkPoint = fetchSingleValue(select_rows(TABLEPREFIX . 'UserCredits', 'nPoints', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');
												if (trim($chkPoint) != '') {
													//update points to user credit
													mysqli_query($conn, "UPDATE " . TABLEPREFIX . "UserCredits set nPoints=nPoints+" . $_SESSION['sess_PointSelected'] . " WHERE
																																						nUserId='" . $_SESSION["guserid"] . "'") or die(mysqli_error($conn));
												}//end if
												else {
													//add points to user credit
													mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "UserCredits (nPoints,nUserId) VALUES ('" . $_SESSION['sess_PointSelected'] . "','" . $_SESSION["guserid"] . "')") or die(mysqli_error($conn));
												}//end else
												//added purchase date point and amount conversion status
												$vComments = CURRENCY_CODE . DisplayLookUp('PointValue') . '&nbsp;=&nbsp;' . DisplayLookUp('PointValue2') . '&nbsp;' . POINT_NAME;
	
												//add into user table
												mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "creditpayments (nUserId,nAmount,nPoints,vTxnId,vMethod,dDate,vCurrentTransaction,vStatus) VALUES
																																('" . $_SESSION["guserid"] . "','" . $_SESSION['sess_PointAmount'] . "','" . $_SESSION['sess_PointSelected'] . "','" . $gc_tran . "',
																																'gc',now(),'" . addslashes($vComments) . "','A')") or die(mysqli_error($conn));
	
	
	
	//                                                                                    /*
	//                                                                                    * Fetch user language details
	//                                                                                    */
	//
	//                                                                                    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
	//                                                                                    $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
	//
	//
	//                                                                                    /*
	//                                                                                    * Fetch email contents from content table
	//                                                                                    */
	//                                                                                    $mailSql = "SELECT L.content,L.content_title
	//                                                                                      FROM ".TABLEPREFIX."content C
	//                                                                                      JOIN ".TABLEPREFIX."content_lang L
	//                                                                                        ON C.content_id = L.content_id
	//                                                                                       AND C.content_name = 'pointsPurchasedMailToUser'
	//                                                                                       AND C.content_type = 'email'
	//                                                                                       AND L.lang_id = '".$_SESSION["lang_id"]."'";
	//
	//                                                                                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
	//                                                                                    $mailRw  = mysqli_fetch_array($mailRs);
	//
	//                                                                                    $mainTextShow   = $mailRw['content'];
	//
	//                                                                                    $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{POINT_NAME}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
	//                                                                                    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,POINT_NAME,$payMethod,date('m/d/Y'),CURRENCY_CODE.$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
	//                                                                                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);
	//
	//                                                                                    $mailcontent1   = $mainTextShow;
	//
	//                                                                                    $subject    = $mailRw['content_title'];
	//                                                                                    $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);
	//
	//                                                                                    $StyleContent = MailStyle($sitestyle, SITE_URL);
	//
	//                                                                                    $subject = POINT_NAME . " purchased details";
	//                                                                                    $EMail = $_SESSION["guseremail"];
	//
	//                                                                                    //readf file n replace
	//                                                                                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
	//                                                                                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
	//                                                                                    $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
	//                                                                                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
	//
	//                                                                                    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
	//
	//
	//                                                                                    //mail sent to admin
	//                                                                                    $var_admin_email = SITE_NAME;
	//
	//                                                                                    if (DisplayLookUp('4') != '') {
	//                                                                                        $var_admin_email = DisplayLookUp('4');
	//                                                                                    }//end if
	//
	//                                                                                    /*
	//                                                                                    * Fetch email contents from content table
	//                                                                                    */
	//                                                                                    $mailSql = "SELECT L.content,L.content_title
	//                                                                                      FROM ".TABLEPREFIX."content C
	//                                                                                      JOIN ".TABLEPREFIX."content_lang L
	//                                                                                        ON C.content_id = L.content_id
	//                                                                                       AND C.content_name = 'pointsPurchasedMailToAdmin'
	//                                                                                       AND C.content_type = 'email'
	//                                                                                       AND L.lang_id = '".$_SESSION["lang_id"]."'";
	//
	//                                                                                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
	//                                                                                    $mailRw  = mysqli_fetch_array($mailRs);
	//
	//                                                                                    $mainTextShow   = $mailRw['content'];
	//                                                                                    
	//                                                                                    
	//
	//                                                                                    $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{POINT_NAME}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
	//                                                                                    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,POINT_NAME,$payMethod,date('m/d/Y'),CURRENCY_CODE.$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
	//                                                                                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);
	//
	//                                                                                    $mailcontent1   = $mainTextShow;
	//
	//                                                                                    $subject    = $mailRw['content_title'];
	//                                                                                    $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);
	//
	//                                                                                    $StyleContent = MailStyle($sitestyle, SITE_URL);
	//                                                                                    $EMail = $var_admin_email;
	//
	//
	//                                                                                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
	//                                                                                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
	//                                                                                    $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
	//                                                                                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
	//                                                                                    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
	//                                                                                   
	//                                                                                    $gc_flag = true;
	//
	//                                                                                    $message = str_replace('{amount}', CURRENCY_CODE . $_SESSION['sess_PointAmount'],str_replace('{point_name}',POINT_NAME,MESSAGE_SUCCESS_PURCHASED_POINTS));
	//
	//                                                                                    //clear sessions
	//                                                                                    $_SESSION['sess_PointSelected'] = "";
	//                                                                                    $_SESSION['sess_PointAmount'] = "";
	//                                                                                }//end else */
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
                                      <tr >
                                       <td colspan="2" align="center" class="success"><b><?php echo $message; ?></b></td>
                                   </tr>
                                   <?php
									}//end if
									if ($gc_flag == false && $gc_err != '') {
										?>
                                        <tr >
                                           <td colspan="2" align="center" class="warning"><?php echo $gc_err; ?></td>
                                       </tr>
                                       <?php
									}//end if
									if (isset($gc_status) && $gc_status != 'success') {
										?>
                                        <tr >
                                           <td colspan="2" align="left" class="subheader"><?php echo HEADING_PAYMENT_DETAILS; ?> </td>
                                       </tr>
                                       <tr >
                                           <td align="left" width="30%"><?php echo TEXT_AMOUNT; ?></td>
                                           <td align="left"><?php echo CURRENCY_CODE; ?><?php echo round($_SESSION['sess_PointAmount'], 2); ?></td>
                                       </tr>
                                       <tr align="center" >
                                           <td colspan="2"><br>
                                              <br>
                                              <?php echo MESSAGE_GOOGLE_CHECKOUT_INSTRUCTION; ?>
                                              <br>
                                              <br>
                                              <b><?php echo MESSAGE_WAITING_FOR_SECURE_PAYMENT_INTERFACE; ?>....</b><br>
                                              <br><br>
                                              <?php UseCase1(); ?></td>
                                          </tr>
                                          <?php
									}//end if
									?>
									<?php
								}//end google chekout if
								?>
                            </form>
                        </table>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>				
        <div class="subbanner">
           <?php include('./includes/sub_banners.php'); ?>
       </div>
   </div>
</div>
</div>
</div>

<?php require_once("./includes/footer.php"); ?>