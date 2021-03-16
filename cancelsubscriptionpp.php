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
$errsflag = 0;
$message = "";
$paymentMethod = "PP";
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

include_once('./includes/title.php');

$cancelid = $_GET['nPId'];
if ($cancelid == "") {
    $cancelid = $_POST['nPId'];
}//end if

$pyid = $_GET['nPyId'];
if ($pyid == "") {
    $pyid = $_POST['nPyId'];
}//end if

$sqlCancel = mysqli_query($conn, "SELECT L.vPlanName,p.vTxn_mode,pl.nPlanId as PId,p.vTxn_id,p.vComments,p.vPlanStatus,p.nTxn_no
				FROM " . TABLEPREFIX . "plan pl
                                    LEFT JOIN " . TABLEPREFIX . "plan_lang L on pl.nPlanId = L.plan_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
                                    LEFT JOIN " . TABLEPREFIX . "payment p ON pl.nPlanId=p.nPlanId
				WHERE p.nUserId='" . $_SESSION["guserid"] . "'  AND p.nTxn_no='" . $pyid . "' AND
				p.nPlanId='" . $cancelid . "'") or die(mysqli_error($conn));

if (mysqli_num_rows($sqlCancel) > 0) {
    $row = mysqli_fetch_array($sqlCancel);
    $planName = $row["vPlanName"];
    $subscriptionid = $row["vTxn_id"];
    $paymentid = $row["nTxn_no"];
}//end if
else {
    $message = ERROR_CANNOT_CANCEL_SUBSCRIPTION . ADMIN_EMAIL;
    $errsflag = 1;
    echo "<script>alert(\"$message\")</script>";
    echo "<script>window.location.href='plan_orders.php';</script>";
    exit();
}//end else


$txtPaypalEmail = DisplayLookUp('paypalemail');
$txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
$txtPaypalSandbox = DisplayLookUp('paypalmode');
$paypalenabled = DisplayLookUp('paypalsupport');

if ($txtPaypalSandbox == "TEST") {
    $paypalurl = "https://www.sandbox.paypal.com";
}//endi f
else {
    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
    $paypalbuttonurl = "https://www.paypal.com/en_US/i/btn/x-click-but20.gif";
}//end else


if (isset($_POST['cancelSubcription']) && $_POST['cancelSubcription'] != '') {
    $useremail = $_POST['txtPpUsername'];
    $userpass = $_POST['txtPpPasswd'];

    if ($txtPaypalSandbox == "TEST") {
        if (cancelpaypalOnTestMode("", "", $useremail, $userpass, $subscriptionid)) {
            $today = date("Y-m-d");
            $sqlupdate = "UPDATE " . TABLEPREFIX . "payment SET vPlanStatus = 'C',vComments='Canceled on $today ' WHERE nTxn_no= '" . $paymentid . "'";
            mysqli_query($conn, $sqlupdate) or die(mysqli_error($conn));

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
                   AND C.content_name = 'plansubcancel'
                   AND C.content_type = 'email'
                   AND L.lang_id = '".$_SESSION["lang_id"]."'";
            }else{
                $mailSql = "SELECT L.content,L.content_title
                  FROM ".TABLEPREFIX."content C
                  JOIN ".TABLEPREFIX."content_lang L
                    ON C.content_id = L.content_id
                   AND C.content_name = 'plansubcancel'
                   AND C.content_type = 'email'
                   AND L.lang_id = '".$_SESSION["lang_id"]."'";
            }
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{username}","{planname}","{ptype}");
            $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($_SESSION["guserFName"]),htmlentities($planname),'PayPal');
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];

       
            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, $_SESSION["guserFName"], $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

            send_mail(ADMIN_EMAIL, $subject, $msgBody, SITE_EMAIL, 'Admin');
            $message = MESSAGE_SUBSCRIPTION_CANCELLED;
            $errsflag = 1;
        }//end if
        else {
            $message = ERROR_CANNOT_CANCEL_SUBSCRIPTION . ADMIN_EMAIL;
            $errsflag = 1;
        }//end else
    }//end if
    else {
        if (ppsubscrcancel($paypalurl, $useremail, $userpass, $subscriptionid)) {
            $today = date("Y-m-d");
            $sqlupdate = "UPDATE " . TABLEPREFIX . "payment SET vPlanStatus = 'C',vComments='Canceled on $today ' WHERE nTxn_no= '" . $paymentid . "'";
            mysqli_query($conn, $sqlupdate) or die(mysqli_error($conn));

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
               AND C.content_name = 'plansubcancel'
               AND C.content_type = 'email'
               AND L.lang_id = '".$_SESSION["lang_id"]."'";

            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{username}","{planname}","{ptype}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($_SESSION["guserFName"]),htmlentities($planname),'PayPal');
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $StyleContent = MailStyle($sitestyle, SITE_URL);

            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

            send_mail(ADMIN_EMAIL, $subject, $msgBody, SITE_EMAIL, 'Admin');
            $message = MESSAGE_SUBSCRIPTION_CANCELLED;
            $errsflag = 0;
        }//end if
        else {
            $message = ERROR_CANNOT_CANCEL_SUBSCRIPTION . ADMIN_EMAIL;
            $errsflag = 1;
        }//end else
    }//end else
}//end first if

$message = ($_SESSION['succ_msg'] != '') ? $_SESSION['succ_msg'] : $message;
?>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
	
	
	<div class="homepage_contentsec">
		<div class="container">
			<div class="row">
				<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
				<div class="col-lg-9">					
					<div class="innersubheader">
						<h4><?php echo HEADING_CANCEL_SUBSCRIPTION; ?></h4>
					</div>
					
					<div class="row">
						<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
						<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
							<?php
								if ($paymentMethod == 'PP') {
									?>
							 <form action="" method="post" name="frmPayment">
								<input type="hidden" name="nPId">
								<input type="hidden" name="nPyId">
								<?php
								if (isset($message) && $message != '') {
									?>
									<div class="roe <?php if($errsflag == 1){ ?>warning<?php }else{?>success<?php } ?>"><b><?php echo $message; ?></b></div>
									<?php
									unset($_SESSION['succ_msg']);
								}
								?>
								<div class="row main_form_inner" align="center">
									<label><img src="images/paypal_small.jpg" title="" border="0"></label>
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_PLAN_NAME; ?></label>
									<?php echo $planName; ?>
								</div>
								<div class="row main_form_inner">
									<label>&nbsp;</label>
								</div>

								<div class="row main_form_inner">
									<label><?php echo TEXT_PAYPAL; ?> <?php echo TEXT_USERNAME; ?></label>
									<input type="text" name="txtPpUsername" class="textbox2">
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_PAYPAL; ?> <?php echo TEXT_PASSWORD; ?></label>
									<input type="password" name="txtPpPasswd" class="textbox2">
								</div>
								<div class="row main_form_inner">
									<label><input type="submit" name="cancelSubcription" value="<?php echo HEADING_CANCEL_SUBSCRIPTION; ?>" class="subm_btt"></label>
								</div>
							</form>
							 <?php
								}//end if
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

