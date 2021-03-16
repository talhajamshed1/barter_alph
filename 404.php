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
include "./includes/config.php";
include "./includes/session.php";
include "./includes/functions.php";
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include "./includes/logincheck.php";

include_once('./includes/gpc_map.php');

include_once('./includes/title.php');

//end if
?>

<body>
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-12 col-sm-12 col-md-3 col-xs-12">
                    <img src="<?php echo SITE_URL?>/images/404.jpg" style="margin: 0 auto; display: block;">
                </div>
            </div>  
        </div>
 </div>     
                
    
<?php require_once("./includes/footer.php"); ?>