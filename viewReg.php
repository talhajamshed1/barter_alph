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
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file for user
include ("./includes/session_check.php");

$message = "";
$var_saleid = "";
$var_userid = "";
$var_uname = "";
$var_bname = "";
$var_date = "";
$var_quantity = 0;
$var_amount = 0;
$var_method = "";

include_once('./includes/gpc_map.php');

if ($_GET["refid"] != "") {
    $var_refid = $_GET["refid"];
}//end if

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">

<?php include_once('./includes/top_header.php'); ?>
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"></div>
			<div class="col-lg-6">
				<div class="innersubheader">
					<h4><?php echo HEADING_TRANSACTION_DETAILS; ?></h4>
				</div>
				<div class="row">
					<div class="col-lg-12 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<div class="row"><h4><?php echo TEXT_DETAILS; ?>...</h4></div>
						<?php
						$sql = "Select * from " . TABLEPREFIX . "referrals where nRefId='" . addslashes($var_refid) . "'";
						$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
						if (mysqli_num_rows($result) > 0) {
							if ($row = mysqli_fetch_array($result)) {
								?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_NAME; ?></label>
								<?php echo  $row["vName"] ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_ADDRESS; ?></label>
								<?php echo  $row["vAddress"] ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_PHONE; ?></label>
								<?php echo  $row["vPhone"] ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_FAX; ?></label>
								<?php echo  $row["vFax"] ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_EMAIL; ?></label>
								<?php echo  $row["vEmail"] ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_SURVEY_STATUS; ?></label>
								<?php
									$var_sur_date = TEXT_N_A;
									if ($row["vSurveyStatus"] == "0") {
										echo TEXT_PENDING;
									}//end if
									else {
										echo TEXT_COMPLETED;
										$var_sur_date = date("m-d-Y", $row["dSurveyDate"]);
									}//end else
								?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_SURVEY_COMPLETION_DATE; ?></label>
								<?php echo  $var_sur_date ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_REGISTRATION_STATUS; ?></label>
								<?php
									$var_reg_date = TEXT_N_A;
									if ($row["vRegStatus"] == "0") {
										echo TEXT_PENDING;
									}//end if
									else {
										echo TEXT_COMPLETED;
										$var_reg_date = date("m-d-Y", $row["dRegDate"]);
									}//end else
								?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_REGISTRATION_COMPLETION_DATE; ?></label>
								<?php echo  $var_reg_date ?>
							</div>
									<?php
								}//end if
							}//end if
							else {
							?>
							<div class="row warning"><?php echo MESSAGE_DETAILS_NOT_AVAILABLE; ?></div>
							<div class="row main_form_inner">
								<label><input type="button" name="btClose" value="<?php echo LINK_CLOSE; ?>" class="subm_btt" onClick="javascript:window.close();"></label>
							</div>
							<?php
							}//end else
							?>
					</div>
				</div>
				
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>
			</div>
			<div class="col-lg-3"></div>
		</div>  
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>