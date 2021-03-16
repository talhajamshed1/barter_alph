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


//assing new plan id to old session
$_SESSION['sess_PlanId'] = $_SESSION['ChangePlanId'];

//cleaning sessions
$_SESSION['sess_Plan_Amt'] = '';
$_SESSION['ChangePlanId'] = '';
$_SESSION['sess_Plan_Mode'] = '';
$_SESSION['sess_upgradeplan'] = '';
$_SESSION['sess_upgradeplan_message'] = '';

include_once('./includes/title.php');
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
					<h4><?php echo HEADING_PLAN_UPGRADATION_COMPLETED; ?></h4>
				</div>
				
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						
						<div class="row main_form_inner">
							<br><br><br>
							<?php echo TEXT_PLAN_UPGRADATION_SUCCESSFULL_TO_CANCEL; ?>
							<a href='plan_orders.php'><?php echo MENU_PLAN_ORDERS; ?></a>
							<br><br><br>
						</div>
									
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

