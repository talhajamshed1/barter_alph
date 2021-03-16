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

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
	
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3">
				<?php include_once ("./includes/usermenu.php"); ?>
			</div>
			<div class="col-lg-9">
				<div class="row">
					<?php 
					//include_once("./includes/swapdetailed_new.php");
					//func_swap_detailed(1); 
					$_POST['cmbItemType'] = 'swap';
					$fid = $_SESSION["guserid"];
					include_once("./includes/productspercat.php");
					?>				
				</div>
			</div>
			<div class="col-lg-3"></div>
		</div>  
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>

