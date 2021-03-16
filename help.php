<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com ? 2005                |
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

include_once('./includes/title.php');

$sql = "SELECT h.*,hc.*, CL.vHctitle FROM " . TABLEPREFIX . "help h 
            left join " . TABLEPREFIX . "helpcategory hc on h.nHcId=hc.nHcId 
            LEFT JOIN " . TABLEPREFIX . "helpcategory_lang CL on hc.nHcId = CL.help_cat_id and CL.lang_id = '" . $_SESSION['lang_id'] . "' 
        where hc.vHtype='client' and h.vActive='1' 
        GROUP BY h.nHcId 
        ORDER BY hc.nHcposition ASC,h.nHposition ASC";

//LEFT JOIN " . TABLEPREFIX . "Help_lang L on h.nHId = L.help_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
//, L.vHtitle, L.vHdescription

$sess_back = $targetfile . "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;

//get the total amount of rows returned
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$sql=dopaging($sql,'',PAGINATION_LIMIT);

$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$numRecords = mysqli_num_rows($rs);

if($numRecords>0) {
$pagenumber     =   getCurrentPageNum();
$defaultUrl     =   $_SERVER['PHP_SELF'];
$querysting     =   "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&";
$paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
$pageString     =   getnavigation($totalrows);
include_once("lib/pager/pagination.php"); 
$pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}
/*
  Call the function:

  I've used the global $_GET array as an example for people
  running php with register_globals turned 'off' :)
 */

/*$navigate = pageBrowser($totalrows, 10, 10, "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql . $navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));*/
?>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
    
         <div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
                <div class="col-lg-9">
                    <div class="row">
                    	<div class="col-lg-12">
                        	
                              
                              <!-------------------------------plain HTML--------------------------->
                              
                              <div class="cms_content help-content">
                                <div class="innersubheader">
                                <h4><?php echo MENU_HELP; ?></h4>
                              </div>
                              <br>
                                  	<div class="row">
                                      <?php  if (mysqli_num_rows($rs) > 0) {
                                           for ($i = 0; $i < mysqli_num_rows($rs); $i++) {
                                        $contents = mysqli_query($conn, "SELECT * FROM " . TABLEPREFIX . "help H
                                                                    LEFT JOIN " . TABLEPREFIX . "help_lang L on H.nHId = L.help_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                                                    where H.vActive='1' and H.nHcId='" . mysqli_result($rs, $i, 'nHcId') . "' ORDER BY H.nHposition ASC") or die(mysqli_error($conn));
                                        if (mysqli_num_rows($contents) > 0) {
                                      ?>   
                                    	<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                        	<div class="search_cat_container">
                                            	<h3><?php echo ucfirst(utf8_encode(mysqli_result($rs, $i, 'vHctitle'))) ;?></h3>
                                                <ul>
                                                    <?php 
                                                    for ($j = 0; $j < mysqli_num_rows($contents); $j++) {
                                                     ?>
                                                	<li><a href="help_details.php?id=<?php echo mysqli_result($contents, $j, 'nHId'); ?>&name=<?php echo utf8_encode(mysqli_result($contents, $j, 'vHtitle')); ?>">
                                                    <span class="glyphicon helpicon"><img src="./help/<?php echo mysqli_result($contents, $j, 'vHimage'); ?>"></span>
                                                    <?php echo utf8_encode(mysqli_result($contents, $j, 'vHtitle')); ?></a></li>
                                                    
                                                    <?php } ?>
                                                  
                                                </ul>
                                            </div>
                                        </div>
                                            
                                      <?php
                                      } 
                                           }
                         ?>
                                            
                 <?php                           
                                      }   
                                      
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
                        <div class="clear"></div>
                    </div>  
                                    </div>
                              </div>
                              
 <!-------------------------------plain HTML( after four divs in a row should start a new row "<div class="row"></div>")--------------------------->
                                
                                <div class="clear"></div>
                        </div>
                    </div>					
                	<div class="subbanner">
					 <?php include('./includes/sub_banners.php'); ?>
                    </div>
                </div>
            </div>  
        </div>
 </div>
 
 
    
                <?php require_once("./includes/footer.php"); ?>