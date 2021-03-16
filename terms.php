<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		          |
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

include_once('./includes/title.php');
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
                        	
                               <div class="cms_content privacy-policy-section">
                                <div class="innersubheader">
                                 <?php $content_arr = ContentLookUp('terms'); ?>
                                <h4><?php echo $content_arr['content_title']; ?></h4>
                              </div>
                                <p><?php echo $content_arr['content']; ?></p>
                                </div>
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