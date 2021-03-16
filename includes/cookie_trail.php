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
$var_catid = ($_GET["catid"] != "") ? $_GET["catid"] : (($_POST["catid"] != "") ? $_POST["catid"] : "");
//include('./includes/sub_banners.php');
?>

            <div class="col-lg-12 row">
                                        <div class="breadcrumb">

                                                <?php echo getBreadCrumbs("categorydetail.php", addslashes($var_catid)); ?>
                                            
                                        </div>
            </div>
<?php //echo getCategoryLink("categorydetail.php", addslashes($var_catid)); ?>
