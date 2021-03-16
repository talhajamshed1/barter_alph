<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
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

//fetch logged user total points
$showUserTotalPoints = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "Where nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');

if ($showUserTotalPoints > 0) {
    $showUserTotalPoints = $showUserTotalPoints;
}//end if
else {
    $showUserTotalPoints = '0';
}//end else

?>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
                <div class="col-lg-9">
                    <div class="full-width">
                        <div class="innersubheader2 row">
                            <div class="col-lg-12">
                            	<h3><?php echo POINT_NAME; ?> <?php echo TEXT_HISTORY; ?></h3>
                            </div>
                        </div>
                        <div class="space"></div>
                    </div>
					<div class="row">
                    	<div class="col-lg-12">
                        	<table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><?php include('./includes/points_menu.php'); ?>
                                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td class="tabContent tabcontent_wrapper">
															
															<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
                                                                    <tr align="right" >
                                                                        <th colspan="2" align="left" ><?php echo TEXT_DASHBOARD; ?></th>
                                                                    </tr>
                                                                    <tr align="right" >
                                                                        <td width="19%" align="left"><?php echo POINT_NAME; ?></td>
                                                                        <td width="81%" align="left"><?php echo CURRENCY_CODE . DisplayLookUp('PointValue') . '&nbsp;=&nbsp;' . DisplayLookUp('PointValue2'); ?> <?php echo POINT_NAME; ?></td>
                                                                    </tr>
                                                                </table>
																
																<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
                                                                    <tr align="right" >
                                                                        <th align="left" class="gray"><?php echo TEXT_AVAILABLE; ?> <?php echo POINT_NAME; ?></th>
                                                                    </tr>
                                                                    <tr align="right" >
                                                                        <td width="19%" align="left"><?php echo $showUserTotalPoints; ?></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    </td>
                                            </tr>
                                        </table>
                        </div>
                    </div>
					
                	<div class="full-width subbanner">
						<?php include('./includes/sub_banners.php'); ?>
                    </div>
                </div>
		</div>
	</div>
</div>
    
<?php require_once("./includes/footer.php"); ?>