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

include_once('./includes/title.php');

$txtSearch = "";
$cmbSearchType = "";

$sql = "SELECT *  FROM " . TABLEPREFIX . "messages WHERE nFromUserId='" . $_SESSION["guserid"] . "' and vFromDel='N' order by nDate Desc";

$sess_back = $targetfile . "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;
//get the total amount of rows returned
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));
/*
  Call the function:

  I've used the global $_GET array as an example for people
  running php with register_globals turned 'off' :)
 */
//$navigate = pageBrowser($totalrows, 10, 10, "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
//$sql = $sql . $navigate[0];

$sql=dopaging($sql,'',PAGINATION_LIMIT);

$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$numRecords = mysqli_num_rows($rs);

 if($numRecords>0) {
$pagenumber     =   getCurrentPageNum();
$defaultUrl     =   $_SERVER['PHP_SELF'];
$querysting     =   "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&";
$paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
$pageString     =   getnavigation($totalrows);
include_once("lib/pager/pagination.php"); 
$pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}


$message = ($_SESSION['succ_msg'] != '') ? $_SESSION['succ_msg'] : $message;
?>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
    
    <div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
                <div class="col-lg-9">
                    <div class="full-width">
                        <div class="innersubheader">
                            <h4><?php echo HEADING_SENT_MESSAGES; ?></h4>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-lg-12">
                        	<div class="table-responsive">
                            	<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
                                                                    <?php
                                                                    if (isset($message) && $message != '') {
                                                                        ?>
                                                                        <tr align="center" >
                                                                            <td colspan="5" class="success"><b><?php echo $message; ?></b></td>
                                                                        </tr> 
                                                                        <?php
                                                                        unset($_SESSION['succ_msg']);
                                                                    }
                                                                    ?>
                                                                    <tr align="center"  class="gray">
                                                                        <th width="7%"><?php echo TEXT_SLNO; ?></th>
                                                                        <!--<td width="10%"><?php echo TEXT_STATUS; ?></td>-->
                                                                        <th width="21%"><?php echo TEXT_TO; ?></th>
                                                                        <th width="39%"><?php echo TEXT_SUBJECT; ?></th>
                                                                        <th width="23%" align="center" style="text-align:center;"><?php echo TEXT_DATE; ?></th>
                                                                    </tr>
                                                                    <?php
                                                                    if (mysqli_num_rows($rs) > 0) {
                                                                        $cnt = '1';
                                                                        while ($arr = mysqli_fetch_array($rs)) {
                                                                            /*switch ($arr['vStatus']) {
                                                                                case "N":
                                                                                    $status = '<img src="./images/mail_new.gif" width="14" height="11" alt="' . $arr['vTitle'] . '" title="' . $arr['vTitle'] . '" border="0">';
                                                                                    break;

                                                                                case "Y":
                                                                                    $status = '<img src="./images/mail_old.gif" width="14" height="11" alt="' . $arr['vTitle'] . '" title="' . $arr['vTitle'] . '" border="0">';
                                                                                    break;
                                                                            }//end switch
                                                                             */
                                                                            //fetching from user name
                                                                            $condition = "where nUserId='" . $arr['nToUserId'] . "'";
                                                                            $toUser = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
                                                                            ?>     
                                                                            <tr >
                                                                                <td align="center"><?php echo $cnt; ?></td>
                                                                                <!--<td align="center"><a href="message_read.php?mid=<?php //echo $arr['nMsgId']; ?>&from=sent"><?php //echo $status; ?></a></td>-->
                                                                                <td><?php echo $toUser; ?></td>
                                                                                <td><a href="message_read.php?mid=<?php echo $arr['nMsgId']; ?>&from=sent"><?php echo $arr['vTitle']; ?></a></td>
                                                                                <td align="center"><?php echo date('m/d/Y', strtotime($arr['nDate'])); ?></td>
                                                                            </tr>
                                                                            <?php
                                                                            $cnt++;
                                                                        }//end while
                                                                   
                                                                    ?>
 <div class="pagination_wrapper">  
                        <div class="left">
                            <?php echo str_replace('{total_rows}',$totalrows,str_replace('{current_rows}',$pageString,TEXT_LISTING_RESULTS)); ?>
                        </div>
                        <div class="right">
                     <?php
                            //Pagination code
                             echo $pg->process();
                     ?>
                        </div>
                        
 </div> 
        <?php } ?>                                                        </table>                            
                            </div>
                                                                
                                                             
                            
                                                                    
					</div>
				</div>
	
                	<div class="full-width subbanner">
						<?php include('./includes/sub_banners.php'); ?>
                    </div>
    </div>
    </div>
    </div>
                <?php require_once("./includes/footer.php"); ?>