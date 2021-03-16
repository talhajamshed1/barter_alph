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

    $nFBid = $_GET['nFBid'];
    $sql = "SELECT   f.nFBId ,f.vTitle ,f.vMatter,date_format(f.dPostDate,'%m/%d/%Y') as 'dPostDate' ,u.vLoginName,u.vFirstName,u.vLastName,f.vStatus FROM";
    $sql .=" " . TABLEPREFIX . "userfeedback as f," . TABLEPREFIX . "users as u where   nFBId=$nFBid and f.nUserFBId=u.nUserId";
    $excqry = mysqli_query($conn, $sql) or die("Could Not Exicute:" . mysqli_error($conn));
    list($nFBid, $txtTitle, $txtDescription, $txtPostdate, $txtLoginName, $txtFirstName, $txtLastName, $txtStatus) = mysqli_fetch_row($excqry);

    //check status
    switch ($txtStatus) {
        case "S":
            $showStatus = TEXT_SATISFIED;
            break;

        case "D":
            $showStatus = TEXT_DISSATISFIED;
            break;

        case "N":
            $showStatus = TEXT_NEUTRAL;
            break;
    }//end switch

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
                <div class="col-lg-9">
                	<div class="full-width">
                        <div class="col-lg-12 ">
                        <div class="innersubheader2">
                            <h3><?php echo HEADING_FEEDBACK_DETAILS; ?></h3>
                        </div>
                        </div>
                      
                    </div>
                		
   					<div class="full-width">
                    	
                    	<div class="col-lg-12">
                        <div class="space">&nbsp;</div>
                        <div class="table-responsive">
                        	<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
                                                                   
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td width="20%" align="left"><b><?php echo TEXT_POSTED_BY; ?></b></td>
                                                                        <td width="80%" align="left"><?php echo  htmlentities(stripslashes($txtLoginName)) ?></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left" valign="top"><b><?php echo TEXT_NAME; ?></b></td>
                                                                        <td align="left" valign="top"><?php echo  htmlentities(stripslashes($txtFirstName)) ?>&nbsp;<?php echo  htmlentities(stripslashes($txtLastName)) ?></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left" class="boldtext"><?php echo TEXT_TITLE ?></td>
                                                                        <td align="left"><?php echo  htmlentities(stripslashes($txtTitle)) ?></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left" class="boldtext"><?php echo TEXT_POSTED_ON; ?></td>
                                                                        <td align="left"><?php echo  htmlentities(stripslashes($txtPostdate)) ?></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left" class="boldtext"><?php echo TEXT_COMMENT ?></td>
                                                                        <td align="left">
																		<?php echo  nl2br(htmlentities(stripslashes($txtDescription))) ?></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left" class="boldtext"><?php echo TEXT_RATING; ?></td>
                                                                        <td align="left"><?php echo htmlentities($showStatus); ?></td>
                                                                    </tr>
                                                                </table>
                        </div>
                        
                                                                 <a class="backbtn right" href="<?php echo  $_SESSION["backurl_feed_usr"] ?>">
						<span class=" glyphicon glyphicon-circle-arrow-left"></span>
						<?php echo LINK_BACK; ?></a>
                        </div>
                    <div class="clear"></div>
                    </div>      
				</div>
                	<div class="full-width subbanner">
						<?php include('./includes/sub_banners.php'); ?>
                    </div>
				</div>
			</div>
		</div>
<?php require_once("./includes/footer.php"); ?>