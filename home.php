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
if(!isset($_GET['display']))
	include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

if (!isset($_SESSION["guseraffid"]) || $_SESSION["guseraffid"] == "") {
    /*if (function_exists('session_register')) {
        session_register("guseraffid");
    }//end if
    */
    $_SESSION["guseraffid"] = $_GET["guseraffid"];
}//end if
$message = "";
include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
<div class="homepage_contentsec">
  <div class="container">
    <div class="row">
      <div class="col-lg-3">
        <?php include_once ("./includes/categorymain.php"); ?>
      </div>
      <div class="col-lg-9">
        <div class="full-width">
          <?php if ($_SESSION["guserid"] == "") {
					include_once("./login_box.php");
				} ?>
          <?php
				if ($_GET["uid"] == "") {
					if (DisplayLookUp('15') != '') {
						$dates = DisplayLookUp('16');
					   // $dates = split("-", $dates);
						$dates = explode("-", $dates);
						$dates = "$dates[1]-$dates[2]-$dates[0]";
					}//end if
					?>
          <div class="full_width">
            <div class="col-lg-12">
			<div class="innersubheader2">
	              <h3><?php echo SITE_NAME; ?> - <?php echo HEADING_ULTIMATE_SWAPPING_EXPERIENCE; ?></h3>
			  </div>
            </div>
          </div>
          <div class="full_width">
            <div class="col-lg-12">
              <table width="100%"  border="0" cellspacing="1" cellpadding="0" class="innersubheader2">
                <tr bgcolor="#FFFFFF">
                  <td width="20%" align="left"><h3><?php echo DisplayLookUp('hintro'); ?></h3></td>
                </tr>
              </table>
              <?php
                                        }//end if
                                        else {
                                            echo '<table width="100%"  border="0" cellspacing="0" cellpadding="2" class="innersubheader2">
                  <tr>
                    <td class="subheader" align="left"><h3>' . HEADING_POSTINGS_OF . ' ' . $_GET["uname"] . '</h3></td>';
                                            if ($_SESSION["guserid"] != $_GET["uid"]) {
                                                //echo '<td align="right"><a href="./userfeedback.php?uid='.$_GET["uid"].'"><b>Post a feed Back About The User</b></a>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                                                echo '<td align="right">&nbsp;</td>';
                                            }//end if
                                            echo '</tr>';

                                            echo '</table>';
                                        }//end else
                                        
					?>
              <div class="table-responsive">
                <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="table table-bordered detail-user-table">
                  <tr>
                    <td align="left" valign="top" colspan="6" class="grey"><?php
									if($_GET["uid"] != "" && $_GET["uname"] != ""){
									 ?>
                      <div>
                        <h4>Swap Items</h4>
                      </div>
                      <!-- Here the latest swap list comes -->
                      <?php
									}
									include_once("./includes/swaplist.php");
									if ($_GET["uid"] != "") {
										func_swap_list(0, $_GET["uid"]);
									}//end if
									else {
										func_swap_list(0);
									}//end else
									?>
                      <?php
									//checking point enable in website
									if (ENABLE_POINT != '1') {
										?>
                      <!-- /Here the latest swap list comes -->
                      <!-- Here the latest sales list comes -->
                      <?php
										include_once("./includes/saleslist.php");
										if ($_GET["uid"] != "") {
											func_sale_list(0, $_GET["uid"]);
										}//end if
										else {
											func_sale_list(0);
										}//end else
										?>
                      <!-- /Here the latest sales list comes -->
                      <!-- Here the latest wish list comes -->
                      <?php
									}//end if
									?>
                      <?php
									include_once("./includes/wishlist.php");
									if ($_GET["uid"] != "") {
										func_wish_list(0, $_GET["uid"]);
									}//end if
									else {
										func_wish_list(0);
									}//end else
									?>
                      <!-- /Here the latest wish list comes -->
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>		
        <div class="full-width subbanner">
          <?php include('./includes/sub_banners.php'); ?>
        </div>
      </div>
      <?php
                if ($_GET["paid"] == "no") {
                    echo("<script> alert('" . ERROR_TRANSACTION . "'); </script>");
                }//end if
?>
    </div>
  </div>
</div>
<?php
                require_once("./includes/footer.php");
                ?>
