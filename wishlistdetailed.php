<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include "./includes/config.php";
include "./includes/session.php";
include "./includes/functions.php";
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
include "./includes/logincheck.php";

include_once('./includes/gpc_map.php');
$message = "";
include_once('./includes/title.php');

$message = ($_SESSION['succ_msg'] != '') ? $_SESSION['succ_msg'] : '';
$itemListId = ($_SESSION['succ_msg_msg'] != '') ? $_SESSION['succ_msg_msg'] : '';
?>
<body onLoad="timersOne();">
	<?php include_once('./includes/top_header.php'); ?>

	<div class="homepage_contentsec">
		<div class="container">
			<div class="row">
				<div class="col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
				<div class="col-lg-9">
					<?php
					if (isset($message) && $message != ''  && !empty($_SESSION['succ_msg_msg'])) {
						?>
						<div class="success_msg">
							<span class="glyphicon glyphicon-ok"></span>
							<p class="msg">
								<?php echo $message; ?></p>
								<div class="clear"></div>
							</div>
							<?php
							unset($_SESSION['succ_msg']);
						}//end if
						?>                      
						<div>
						<?php
						//include_once("./includes/wishdetailed.php");
						$_REQUEST['cmbItemType'] = 'wish';
						$fid = $_REQUEST['no'];
							include_once("./includes/productspercat.php");
						/*if ($_GET["rf"] == "sid") {
							func_wish_detailed(0, $_GET["no"]);
						}//end if
						else {
							func_wish_detailed();
						}//end else
						 */
						?>
					</div>
					
					<div class="subbanner">
						<?php include('./includes/sub_banners.php'); ?>
					</div>
					
				</div>
			</div>
		</div>
	</div>

	
	<?php require_once("./includes/footer.php"); ?>