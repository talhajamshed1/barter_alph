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
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

$message = str_replace('{site_email}',SITE_EMAIL,ERROR_PAYMENT_PROCESS_CONTACT_EMAIL);

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
<div class="container">
	<div class="row">
		<div class="col-lg-3">
			<?php
			if (!isset($_SESSION["guserid"]) && ($_SESSION["guserid"] == "")) {
				include_once ("./includes/categorymain.php");
			}//end if
			else {
				include_once ("./includes/usermenu.php");
			}//end else
			?>
		</div>
		<div class="col-lg-9">					
			<div class="innersubheader">
				<h4><?php echo HEADING_PAYMENT_FAILURE; ?></h4>
			</div>
			
			<div class="row">
				<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
				<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<div class="row warning"><?php echo $message; ?></div>
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