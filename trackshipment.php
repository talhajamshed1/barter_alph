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
				<h4><?php echo HEADING_TRACK_SHIPMENT; ?></h4>
			</div>
			
			<div class="full_width">
				<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
					<div class="full_width">
						<?php echo str_replace('{site_name}',SITE_NAME,CONTENT_SHIPMENT); ?>
					</div>
					<div class="full_width">
						<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 trackshipment_inner">
							<b><?php echo TEXT_USPS; ?></b><br>
							<a href="http://www.usps.com/shipping/trackandconfirm.htm?from=home&page=0035trackandconfirm" target="_blank"><img border="0" src="images/usps.jpg"></a>
						</div>
						<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 trackshipment_inner">
							<b><?php echo TEXT_DHL; ?></b><br>
							<a href="http://www.dhl-usa.com/home/home.asp" target="_blank"><img border="0" src="images/dhl.gif"></a>
						</div>
						<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 trackshipment_inner">
							<b><?php echo TEXT_FEDEX; ?></b><br>
							<a href="http://www.fedex.com/us/" target="_blank"><img border="0" src="images/fedex.gif"></a>
						</div>
						<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 trackshipment_inner">
							<b><?php echo TEXT_UPS; ?></b><br>
							<a href="http://www.ups.com/content/us/en/index.jsx" target="_blank"><img border="0" src="images/ups.jpg"></a>
						</div>
					</div>
				</div>
				
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
				
				