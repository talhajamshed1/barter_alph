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

$flag1 = false;
//if ($_GET["swapid"] != "") {
    //$var_swapid = $_GET["swapid"];
    if ($_GET["flag"] == "false") {
        if ($_GET["mode"] == "edit") {
            $var_message = ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_EDIT;
            $flag1 = false;
        }//end if
        else if ($_GET["mode"] == "delete") {
            $var_message = ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_DELETE;
            $flag1 = false;
        }//end else if
        else {
            $var_message = ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_MAKE;
            $flag1 = false;
        }//end else
    }//end if
    else if ($_GET["flag"] == "true") {
        if ($_GET["mode"] == "delete") {
            $var_message = ERROR_OFFER_REQUEST_DELETED;
            $flag1 = true;
        }//end if
        else if ($_GET["mode"] == "edit") {
            $var_message = MESSAGE_OFFER_EDITED_SUCCESSFULLY;
            $flag1 = true;
        }
        else {
            $var_message = MESSAGE_OFFER_REQUESTED_SUCCESSFULLY;
            $flag1 = true;
        }//end else
    }//end else if
//}//end if

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
				<div class="full-width row">
					<div class="col-lg-12">
						<div class="innersubheader2">
							<h3 style="padding-top: 0"><?php echo HEADING_OFFER_STATUS; ?></h3>
						</div>
					</div>
				</div>
				<div class="full-width row">
					<div class="col-lg-12">
						<?php if ($_SESSION["guserid"] == "") {
							include_once("./login_box.php");
						} ?>
						<div class="full_width">
							<form name="frmSettings" method ="POST" enctype="multipart/form-data" action = "<?php echo $_SERVER['PHP_SELF'] ?>" onsubmit="return validateSettingsForm();">
								<?php
								if (isset($message) && $message != '') {
									?>
								<div class="custom-msg alert-warning" align="center"><?php echo $message; ?></div>
								<?php }//end if?>
								<?php
								if (isset($var_message) && $var_message != '') {
                                                                 if($flag1 == false){   
									?>
									<div class=" alert-warning custom-msg"><?php echo $var_message; ?></div>
								<?php } else if($flag1 == true){?>
                                    <div class="custom-msg alert-success"><?php echo $var_message; ?></div>           
                                                                <?php }}?>
                                                                <br>
							</form>
						</div>
					</div>
				</div>	
				<div class="full-width subbanner">
					<div class="col-lg-12">
						<?php include('./includes/sub_banners.php'); ?>
					</div>
				</div>		
			</div>
		</div>
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>