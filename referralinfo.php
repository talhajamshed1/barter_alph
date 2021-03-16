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

include_once('./includes/gpc_map.php');

$sur_amnt = 0;
$reg_amnt = 0;

if (DisplayLookUp('9') != '') {
    $sur_amnt = DisplayLookUp('9');
}//end if

if (DisplayLookUp('10') != '') {
    $reg_amnt = DisplayLookUp('10');
}//end if

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
	

<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
			<div class="col-lg-9">
				<div class="full-width">
					<?php
						if ($_SESSION["guserid"] == "") {
						include_once("./login_box.php");
						}
					?>
				</div>
				<div class="refer-division">
					 <?php
						$contents = file_get_contents("./languages/" . $_SESSION['lang_folder'] . "/referral_info.html");
						$contents = str_replace('{site_name}', SITE_NAME, $contents);
						$contents = str_replace('{currency_code}', CURRENCY_CODE, $contents);
						$contents = str_replace('{sur_amt}', $sur_amnt, $contents);
						$contents = str_replace('{reg_amnt}', $reg_amnt, $contents);
						$contents = str_replace('{tot_amt}', ($sur_amnt + $reg_amnt), $contents);
						echo $contents;
					?>
				</div>
				<div class="full-width subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>
			</div>
		</div>
	</div>
</div>

                
<?php require_once("./includes/footer.php"); ?>