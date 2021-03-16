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

include_once('./includes/gpc_map.php');

include_once('./includes/title.php');

$toplinks_sitemap = toplinks(TABLEPREFIX);
if ($toplinks_sitemap != '0') {
    for ($i = 0; $i < mysqli_num_rows($toplinks_sitemap); $i++) {
        $MenuEnableArray[] = mysqli_result($toplinks_sitemap, $i, 'vCategoryTitle');
    }//end for loop
}//end if
//footer links
$footerlinks_sitemap = footerlinks(TABLEPREFIX);
if ($footerlinks_sitemap != '0') {
    for ($i = 0; $i < mysqli_num_rows($footerlinks_sitemap); $i++) {
        //check categories
        $ParentId = mysqli_result($footerlinks_sitemap, $i, 'nCategoryId');
        $sub_cat_sitemap = mysqli_query($conn, "select * from " . TABLEPREFIX . "client_module_category where 
												nParentId='" . $ParentId . "' and vActive='1'") or die(mysqli_error($conn));
        if (mysqli_num_rows($sub_cat_sitemap) > 0) {
            for ($j = 0; $j < mysqli_num_rows($sub_cat_sitemap); $j++) {
                $MenuEnableArray2[] = mysqli_result($sub_cat_sitemap, $j, 'vCategoryTitle');
            }//end for loop
        }//end if
    }//end first for loop
}//end if
//top header links
$topheaderlinks_sitemap = topheaderlinks(TABLEPREFIX);
if ($topheaderlinks_sitemap != '0') {
    for ($i = 0; $i < mysqli_num_rows($topheaderlinks_sitemap); $i++) {
        $MenuEnableArray3[] = mysqli_result($topheaderlinks_sitemap, $i, 'vCategoryTitle');
    }//end for loop
}//end if
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
        <div class="row">
          <div class="col-lg-12">
            
           
            <div class="clearfix cms_content sitemap-section">
              <div class="innersubheader">
              <h4><?php echo MENU_SITEMAP; ?></h4>
            </div>
            <br>
              <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="sitemap_listwrapper">
                  <div class="subheader_hlp">Categories</div>
                  <div class="sitemap_listing">
                    <ul>
                      <?php
                                                                                                                                                            $sql = "SELECT * FROM " . TABLEPREFIX . "category C
                                                                                                                                                                        LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                                                                                                                        where C.nParentId = '0' order by C.nPosition desc";
                                                                                                                                                            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                                                                                                                            if (mysqli_num_rows($result) != 0) {
                                                                                                                                                                while ($row = mysqli_fetch_array($result)) {
                                                                                                                                                                    //checking showing which page
                                                                                                                                                                    switch (substr($PG_TITLE, 0, -8)) {
                                                                                                                                                                        case "catwiseproducts.php":
                                                                                                                                                                            $showpath = 'catwiseproducts.php?catid=' . $row["nCategoryId"];
                                                                                                                                                                            break;

                                                                                                                                                                        default:
                                                                                                                                                                            $showpath = 'categorydetail.php?catid=' . $row["nCategoryId"] . '&categorydesc=' . urlencode(htmlentities($row["vCategoryDesc"]));
                                                                                                                                                                            break;
                                                                                                                                                                    }//end switch

                                                                                                                                                                    echo '<li><a href="' . $rootserver . '/categorydetail.php?catid=' . $row["nCategoryId"] . "&categorydesc=" . urlencode(htmlentities($row["vCategoryDesc"])) . '">' . Highligt($PG_TITLE, $showpath, htmlentities($row["vCategoryDesc"]), '#000000') . ' (' . toGetTotal($row["nCategoryId"], $toplinks) . ')</a></li>';
                                                                                                                                                                }//end while
                                                                                                                                                            }//end if
                                                                                                                                                            else {
                                                                                                                                                                echo '<li>&nbsp;&nbsp;&nbsp;&nbsp;'.MESSAGE_SORRY_NO_CATEGORY.'</li>';
                                                                                                                                                            }//end else
                                                                                                                                                            ?>
                    </ul>
                  </div>
                  <div class="clear"></div>
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4">
              	<div class="sitemap_listwrapper">
                                      <div class="subheader_hlp">Features</div>
                                      <div class="sitemap_listing">
                                        <ul>
                                          <?php
                                                                                                                                                        if (is_array($MenuEnableArray)) {
                                                                                                                                                            if (in_array('Sell', $MenuEnableArray)) {
                                                                                                                                                        ?>
                                          <li><a href="salelistdetailed.php?type=sell"><?php echo MENU_SELL ?></a></li>
                                          <?php
                                                                                                                                                            }//end if
                                                                                                                                                        }//end if
                                                                                                                                                        if (is_array($MenuEnableArray)) {
                                                                                                                                                            if (in_array('Swap', $MenuEnableArray)) {
                                                                                                                                                                ?>
                                          <li><a href="swaplistdetailed.php?type=swap"><?php echo MENU_SWAP; ?></a></li>
                                          <?php
                                                                                                                                                            }//end if
                                                                                                                                                        }//end if
                                                                                                                                                        if (is_array($MenuEnableArray)) {
                                                                                                                                                            if (in_array('Wish', $MenuEnableArray)) {
                                                                                                                                                                ?>
                                          <li><a href="wishlistdetailed.php?type=wish"><?php echo MENU_WISH; ?></a></li>
                                          <?php
                                                                                                                                                                }//end if
                                                                                                                                                            }//end if
                                                                                                                                                           if (is_array($MenuEnableArray)) {
                                                                                                                                                            if (in_array('Sell', $MenuEnableArray)) {
                                                                                                                                                        ?>
                                          <li><a href="addsale.php?type=sale"><?php echo MENU_ADD_SALE; ?></a></li>
                                          <?php
                                                                                                                                                            }//end if
                                                                                                                                                        }//end if
                                                                                                                                                         if (is_array($MenuEnableArray)) {
                                                                                                                                                            if (in_array('Swap', $MenuEnableArray)) {
                                                                                                                                                                ?>
                                          <li><a href="addsale.php?type=swap"><?php echo MENU_ADD_SWAP; ?></a></li>
                                          <?php
                                                                                                                                                            }//end if
                                                                                                                                                        }//end if
                                                                                                                                                        if (is_array($MenuEnableArray)) {
                                                                                                                                                            if (in_array('Wish', $MenuEnableArray)) {
                                                                                                                                                                ?>
                                          <li><a href="addsale.php?type=wish"><?php echo MENU_ADD_WISH; ?></a></li>
                                          <?php
                                                                                                                                                                }//end if
                                                                                                                                                            }//end if
                                                                                                                                                        ?>
                                          <li><a href="viewfeedbacks.php"><?php echo MENU_VIEW_FEEDBACKS; ?></a></li>
                                          <li><a href="trackshipment.php"><?php echo MENU_TRACK_SHIPMENT; ?></a></li>
                                          <li><a href="escrowpayments.php"><?php echo MENU_ESCROW_PAYMENTS; ?></a></li>
                                        </ul>
                                      </div>
                                      <div class="clear"></div>
                                    </div>
              
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4">
              	<div class="sitemap_listwrapper">
                                      <div class="subheader_hlp">Account Details</div>
                                      <div class="sitemap_listing">
                                        <ul>
                                          <li><a href="editprofile.php"><?php echo MENU_EDIT_PROFILE; ?></a></li>
                                          <li><a href="usermain.php"><?php echo MENU_MYBOOTH; ?></a></li>
                                          <li><a href="salepaymentsbyme.php"><?php echo MENU_ACC_SUMMARY; ?></a></li>
                                          <?php
                                                                                                                                                        if (is_array($MenuEnableArray)) {
                                                                                                                                                            if (in_array('Online Members', $MenuEnableArray)) {
                                                                                                                                                                ?>
                                          <li><a href="online_members.php"><?php echo MENU_ONLINE_MEMBERS; ?></a></li>
                                          <?php
                                                                                                                                                            }//end if
                                                                                                                                                        }//end if
                                                                                                                                                        ?>
                                        </ul>
                                      </div>
                                      <div class="clear"></div>
                                    </div>
                                    
                                    <div class="sitemap_listwrapper">
                                      <div class="subheader_hlp">About</div>
                                      <div class="sitemap_listing">
                                        <ul>
                                          <?php
                                                                                                                                            if (is_array($MenuEnableArray2)) {
                                                                                                                                                if (in_array('About Us', $MenuEnableArray2)) {
                                                                                                                                                    ?>
                                          <li><a href="aboutus.php"><?php echo MENU_ABOUT; ?></a></li>
                                          <?php
                                                                                                                                                }
                                                                                                                                            }


                                                                                                                                                if (is_array($MenuEnableArray3)) {
                                                                                                                                                    if (in_array('Contact Us', $MenuEnableArray3)) {
                                                                                                                                                        ?>
                                          <li><a href="contactus.php"><?php echo MENU_CONTACT; ?></a></li>
                                          <?php
                                                                                                                                                    }//end if
                                                                                                                                                 }//end if
                                                                                                                                              if (is_array($MenuEnableArray3)) {
                                                                                                                                                    if (in_array('FAQ', $MenuEnableArray3)) {
                                                                                                                                                        ?>
                                          <li><a href="contactus.php"><?php echo MENU_FAQ; ?></a></li>
                                          <?php
                                                                                                                                                    }//end if
                                                                                                                                                 }//end if
                                                                                                                                                  if (is_array($MenuEnableArray3)) {
                                                                                                                                                    if (in_array('Help', $MenuEnableArray3)) {
                                                                                                                                                        ?>
                                          <li><a href="contactus.php"><?php echo MENU_HELP; ?></a></li>
                                          <?php
                                                                                                                                                    }//end if
                                                                                                                                                 }//end if
                                                                                                                                                if (is_array($MenuEnableArray3)) {
                                                                                                                                                    if (in_array('Privacy Policy', $MenuEnableArray3)) {
                                                                                                                                                        ?>
                                          <li><a href="contactus.php"><?php echo MENU_PRIVACY; ?></a></li>
                                          <?php
                                                                                                                                                    }//end if
                                                                                                                                                 }//end if




                                                                                                                                            ?>
                                        </ul>
                                      </div>
                                      <div class="clear"></div>
                                    </div>
              
              </div>
            </div>
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