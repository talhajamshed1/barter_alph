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

$var_tmpid = "";
$message = "";
$flag = false;
if ($_GET["tmpid"] != "") {
    $var_tmpid = $_GET["tmpid"];

    $sql = "Select nSwapAmount,vTitle,vPostType from " . TABLEPREFIX . "swap where nSwapId='" . addslashes($var_tmpid) . "' AND nSwapAmount <> '0'";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        if ($row = mysqli_fetch_array($result)) {
            $var_title = $row["vTitle"];
            $var_amount = $row["nSwapAmount"];
            settype($var_amount, double);
            $var_posttype = $row["vPostType"];
            $var_amount = ($var_amount < 0) ? (-1 * $var_amount) : $var_amount;
            $flag = true;
            $message = MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU;
        }//end if
    }//end if
    else {
        $message = ERROR_MISMATCH_DATA_REQUESTED;
    }//end else
}//end if

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
<div class="container">
	<div class="row">
		<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
		<div class="col-lg-9">					
			<div class="innersubheader">
				<h4><?php echo TEXT_PAYMENT_DONE; ?></h4>
			</div>
			
			<div class="row">
				<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
				<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
					<form name="frmSettings" method ="POST" enctype="multipart/form-data" action = "<?php echo $_SERVER['PHP_SELF'] ?>" onSubmit="return validateSettingsForm();">					
						<?php
						if (isset($flag) && $flag == false) {
							?>
							<div class="row warning"><?php echo $message; ?></div>
						<?php
						}//end if
						else {
							?>
						<h3 class="subheader row"><?php echo HEADING_TRANSACTION_DETAILS; ?></h3>
						<div class="row main_form_inner">
							<label><?php echo TEXT_TITLE; ?></label>
							<?php echo  $var_title ?>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_AMOUNT; ?></label>
							<?php echo CURRENCY_CODE; ?><?php echo  $var_amount ?>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_TYPE; ?></label>
							<?php echo  ($var_posttype == "swap") ? TEXT_SWAP_TRANSACTION : TEXT_WISH_TRANSACTION; ?>
						</div>
						<div class="row main_form_inner">
							<label><b><?php echo  $message ?></b></label>
						</div>
							<?php }//end if ?>
						</form>				
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


