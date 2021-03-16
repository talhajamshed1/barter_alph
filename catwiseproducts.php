<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                      |
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
//include ("./includes/session_check.php");
include ("./includes/logincheck.php");

include_once('./includes/gpc_map.php');
$message = "";
include_once('./includes/title.php');

$message = ($_SESSION['succ_msg'] != '') ? $_SESSION['succ_msg'] : '';
$itemListId = ($_SESSION['succ_msg_msg'] != '') ? $_SESSION['succ_msg_msg'] : '';

 /*
  * Get Swap count
  */
            $swapsql = "SELECT S.nSwapId,L.vCategoryDesc,S.vTitle,S.nPoint, date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
            S.vFeatured,S.vUrl, S.nUserId,S.nValue,S.vDescription,S.vBrand,S.vType,S.vCondition,S.vYear,S.vSmlImg 
                         FROM " . TABLEPREFIX . "swap S 
                         LEFT OUTER JOIN " . TABLEPREFIX . "category C ON S.nCategoryId = C.nCategoryId
                         LEFT JOIN " . TABLEPREFIX . "category_lang L ON C.nCategoryId = L.cat_id AND L.lang_id = '" . $_SESSION['lang_id'] . "'
                         LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=S.nUserId
                         WHERE (S.nCategoryId  = '" . addslashes($_REQUEST['catid']) . "' or '" . addslashes($_REQUEST['catid']) . "' = '')
                         AND S.vDelStatus = '0' AND S.vPostType='swap' AND u.vStatus='0' AND u.vDelStatus = '0' AND S.vSwapStatus= '0' 
                         AND (u.nUserId <> '".$_SESSION['guserid']."' or '".$_SESSION['guserid']."' = '".$fid."') ";

            $swapCount = mysqli_num_rows(mysqli_query($conn, $swapsql));
 /*
  * Get wish count
  */
                               
            $wishSql = "SELECT S.nSwapId,L.vCategoryDesc,S.vTitle,S.nPoint, date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate', 
            S.vFeatured,S.vUrl, S.nUserId,S.nValue,S.vDescription,S.vBrand,S.vType,S.vCondition,S.vYear,S.vSmlImg 
                        FROM " . TABLEPREFIX . "swap S 
                        LEFT OUTER JOIN " . TABLEPREFIX . "category C ON S.nCategoryId = C.nCategoryId
                        LEFT JOIN " . TABLEPREFIX . "category_lang L ON C.nCategoryId = L.cat_id AND L.lang_id = '" . $_SESSION['lang_id'] . "'
                        LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=S.nUserId
                        WHERE (S.nCategoryId  = '" . addslashes($_REQUEST['catid']) . "' or '" . addslashes($_REQUEST['catid']) . "' = '')
                        AND S.vDelStatus = '0' AND S.vPostType='wish'  AND u.vStatus='0' AND u.vDelStatus = '0'  AND S.vSwapStatus= '0'
                        AND (u.nUserId <> '".$_SESSION['guserid']."' or '".$_SESSION['guserid']."' = '".$fid."') ";

            $wishCount = mysqli_num_rows(mysqli_query($conn, $wishSql));

  /*
  * Get sale count
  */
            $saleSql = "SELECT S.nSaleId as 'nSwapId',L.vCategoryDesc,S.vTitle,S.nPoint,
                 date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',S.vFeatured,S.vUrl,
                                         S.nUserId,S.nValue,S.vDescription,S.vBrand,S.vType,S.vCondition,S.vYear,S.vSmlImg FROM
                 " . TABLEPREFIX . "sale S Left outer join " . TABLEPREFIX . "category C on
                 S.nCategoryId = C.nCategoryId
                 LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
                                         LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=S.nUserId
                 where (S.nCategoryId  = '" . addslashes($_REQUEST['catid']) . "' or '" . addslashes($_REQUEST['catid']) . "' = '')
                 AND S.vDelStatus = '0'  AND u.vStatus='0' and u.vDelStatus = '0'  and   S.nQuantity > '0' and (u.nUserId <> '".$_SESSION['guserid']."' or '".$_SESSION['guserid']."' = '".$fid."') ";

            $saleCount = mysqli_num_rows(mysqli_query($conn, $saleSql));

?>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
    
    <div class="homepage_contentsec">
<div class="container">
  
            <div class="row">
            	<div class="col-md-4 col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
                <div class="col-md-8 col-lg-9">
                	<?php include_once('./includes/cookie_trail.php'); ?>
                  <!-- new-design -->

            <!--  -->
           <!--  <div class="tab-tiles">
              <a href="#" class="active">swap (3)</a>
              <a href="#">sale (3)</a>
              <a href="#">wish (10)</a>
            </div> -->








                  <!--  -->
                                        <?php
                                        if ($_SESSION["guserid"] == "") {
                                            include_once("./login_box.php");
                                        }
                                        ?>
                                       <!--<div class="full-width">
                                       		<div class="col-lg-12 innersubheader2">
                                                <h3><?php echo HEADING_ITEMS; ?></h3>
                                            </div>
                                       </div>-->
                                       
                                       <div class="full-width">
                                       		<div>
                                             <?php
                                        if (isset($message) && $message != '' && empty($_SESSION['succ_msg_msg'])) {
                                            ?>
                                                    <br/> <br/><br/>
					                             <div class="row success"><?php echo $message; ?></div>
											
                                            <?php
                                            //$message = '';
                                            unset($_SESSION['succ_msg']);
                                        }//end if
                                        ?><!-- Here the latest swap list comes -->
                                        <?php
										                    $showcat = true;
                              // un-comment-this
                                        	include_once("./includes/productspercat.php");
                              // ============
                                        ?>
                                            </div>
                                       	
                                       </div>
                                       <div class="subbanner">
										<?php include('./includes/sub_banners.php');?>
										</div>
                </div>
            </div>
              
               </div>
                </div>

<?php require_once("./includes/footer.php"); ?>