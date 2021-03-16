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

$sql = "SELECT s.vTitle, s.nSaleId, s.vFeatured, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName , s.vBrand, cl.vCategoryDesc, cl.lang_id
FROM  " . TABLEPREFIX . "sale s 
LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId AND cl.lang_id = '" . $_SESSION['lang_id'] . "'
WHERE s.vFeatured = 'Y' AND s.nQuantity >'0' AND s.vDelStatus = '0' AND u.vStatus = '0'
AND u.nUserId <> '".$_SESSION['guserid']."' ORDER BY s.dPostDate DESC";

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$result2 = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$displayTooltip = DisplayLookUp('ThumbToolTip');
if($displayTooltip == 'Yes')
{
	$toolTip = true;
}
else 
{
	$toolTip = false;
}
?>
<div class="item-slider-section">
  <div class="container">

    <h2 class="item-slider-section-heading"><?php echo HEADING_FEATURED_ITEMS;?></h2>
    <div class="featured_wrps">
      <?php  
      if (mysqli_num_rows($result) != 0) {
        ?>

        <div class="itemSlider">
          <?php  $i=0;
          while ($arr = mysqli_fetch_array($result)) {

            $v_url = getDisplayImage($arr["nSaleId"], "sa", $arr["vUrl"]);
            $vurl = trim($v_url);
            if ($vurl != ''){

              $vurl_img = str_replace('medium_', '', $vurl);

              if(@file_exists($vurl_img)) {
                $var_burl = $vurl_img;
              } elseif (@file_exists($vurl)) {
                $var_burl = $vurl;
              } else{
                $var_burl = "images/nophoto.gif";
              }
            } else{
              $var_burl = "images/nophoto.gif";
            }

            $item_price='';
            $item_name='';

            if (($EnablePoint == '0' || $EnablePoint == '2') && $arr['nValue']>0) {
              $item_price .= CURRENCY_CODE . $arr['nValue'];
              $item_name.=TEXT_PRICE.'&nbsp;';

            }
            if ($EnablePoint == '2' && $arr['nValue'] > 0 && $arr['nPoint'] > 0) $item_price .= ' & '; 
            if (($EnablePoint == '1' || $EnablePoint == '2') && $arr['nPoint']>0) {
              $item_price .=  $arr['nPoint'] .' '.POINT_NAME. '';

            }


            if($i < mysqli_num_rows($result2)){
              ?>
              <div class="item">
                <div class="item-slider-section-tile" style="padding: 0">
                  <div class="item-slider-section-tile-img-sec">
                    <?php
                    if($arr["vCondition"]=="new" || $arr["vCondition"]=="New"){
                      echo'<span class="condition-new-label">New</span>';
                    }else if($arr["vCondition"]=="used" || $arr["vCondition"]=="Used"){
                      echo'<span class="condition-used-label">New</span>';
                    }

                    ?>

                    <img src="<?php echo $var_burl;?>" alt="Product">
                    <div class="action-tiles">
                      <a href="<?php echo $rootserver ?>/swapitemdisplay.php?saleid=<?php echo $arr["nSaleId"]?>&source=sa" title="View Details"><i class="flaticon-shopping-cart"></i></a>

                      <a href="#" data-toggle="modal" data-target="#detailproductModalfea<?php echo $i; ?>"><i class="flaticon-zoom-in"></i></a>

                    </div>
                  </div>
                  <div class="item-slider-section-tile-bottom-sec">
                    <a href="<?php echo $rootserver ?>/swapitemdisplay.php?saleid=<?php echo $arr["nSaleId"]?>&source=sa" title="View Details">
                      <h4><?php echo (strlen(trim($arr["vTitle"]))>28)?substr(htmlentities($arr["vTitle"]),0,28)."...":htmlentities($arr["vTitle"])?></h4>
                      <h3><?php echo $item_price ?></h3>
                    </a>
                    <span>Posted By <i><a ><?php echo $arr['vLoginName']; ?></a></i></span>
                  </div>

                </div>
              </div>

              <?php 
            } 
            ?>

            <?php $i++; 
          } ?>

        </div>


        <!-- MODAL START -->

        <?php 

        $j=0;
        while ($arr = mysqli_fetch_array($result2)) {

          $item_price2='';
          $item_name2='';

          if (($EnablePoint == '0' || $EnablePoint == '2') && $arr['nValue']>0) {
            $item_price2 .= CURRENCY_CODE . $arr['nValue'];
            $item_name2.=TEXT_PRICE.'&nbsp;';

          }
          if ($EnablePoint == '2' && $arr['nValue'] > 0 && $arr['nPoint'] > 0) $item_price2 .= ' & '; 
          if (($EnablePoint == '1' || $EnablePoint == '2') && $arr['nPoint']>0) {
            $item_price2 .=  $arr['nPoint'] .' '.POINT_NAME. '';

          }


          $img_list = array();
          $vurl = trim($arr["vUrl"]);
          if ($vurl != ''){

            $gallery_list = getMoreImages($arr["nSaleId"], "sa");
            $vurl_img = str_replace('medium_', '', $vurl);

            if(@file_exists($vurl_img)) {
              $first_image = $vurl_img;
            } elseif (@file_exists($vurl)) {
              $first_image = $vurl;
            } elseif (!empty($gallery_list)) {
              $first_image = array_shift($gallery_list);
            } else{
              $first_image = "images/nophoto.gif";
            }

                // get more images
            if (!empty($gallery_list)) {
              foreach ($gallery_list as $img) {
                $img_list[] = $img;
              }
            }
          } else{

            $gallery_list = getMoreImages($arr["nSaleId"], "sa");
            if(!empty($gallery_list)){

              $first_image = array_shift($gallery_list);
              foreach ($gallery_list as $img) {
                $img_list[] = $img;
              }
            } else{
              $first_image = "images/nophoto.gif"; 
            }
          }


          if($j < mysqli_num_rows($result2)){
            ?>
            <div id="detailproductModalfea<?php echo  $j; ?>" class="modal productdetailpopup" role="dialog">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <div class="modal-dialog">
                <div class="modal-content">

                  <div class="modal-body">

                   <div class="row">
                     <div class="col-md-5">
                      <div class="productdetailpopup-left">
                        <div class="Product-img-container">
                          <img id="ProductImg<?php echo  $j; ?>" src="<?php echo $first_image;?>" data-zoom-image="<?php echo $first_image;?>"/>
                        </div>

                        <div class="product_item_gallery" id="product_item_gallery<?php echo $j ?>">
                          <a class="product_gallery_item active" data-image="<?php echo $first_image;?>" data-zoom-image="<?php echo $first_image;?>" tabindex="0">
                            <img src="<?php echo $first_image;?>" alt="product_img" data-targetid ="<?php echo  $j; ?>">
                          </a>

                          <?php 
                          foreach($img_list as $key=>$image){?>
                          <a class="product_gallery_item active" data-image="<?php echo $image;?>" data-zoom-image="<?php echo $image;?>" tabindex="0">
                            <img src="<?php echo $image;?>" alt="product_img_<?php echo $key; ?>" data-targetid ="<?php echo  $j; ?>">
                          </a>
                          <?php }  ?>

                        </div> 

                      </div>
                    </div>
                    <div class="col-md-7">
                      <div class="productdetailpopup-right">
                       <h3><?php echo (strlen(trim($arr["vTitle"]))>21)?strtoupper(substr(htmlentities($arr["vTitle"]),0,21))."...":strtoupper(htmlentities($arr["vTitle"]));?></h3>
                       <div class="bottom-price">
                        <h4><?php echo ($arr['nValue'] > 0) ? 'Price ' : '' ?><?php echo $item_price2 ?></h4>

                        <span class="post-dte">Posted On <i><?php echo date('m/d/Y', strtotime($arr["dPostDate"])); ?></i></span>
                      </div>
                      <div class="bottom-usr">
                        <span><?php echo TEXT_POSTED_BY ?> <i><a href="user_profile.php?uid=<?php echo $userData['nUserId'] . "&uname=" . urlencode($userData['vLoginName']); ?>" tabindex="-1"><?php echo $arr['vLoginName']; ?></a></i></span>
<!--                        <div class="rating-usr">
                         <i class="flaticon-star"></i>
                         <i class="flaticon-star"></i>
                         <i class="flaticon-star"></i>
                         <i class="flaticon-star-1"></i>
                         <i class="flaticon-star-1"></i>
                       </div>-->
                     </div>
                     <p><?php echo substr(htmlentities($arr['vDescription']),0,200);?></p>
                     <table>
                      <tr>
                        <td>Category</td>
                        <td><?php echo  $arr["vCategoryDesc"] ? $arr["vCategoryDesc"] : '--'; ?></td>
                      </tr>
                      <tr>
                        <td>Brand</td>
                        <td><?php echo  $arr["vBrand"] ? $arr["vBrand"] : '--'; ?></td>
                      </tr>
                      <tr>
                        <td>Type</td>
                        <td><?php echo  $arr["vType"] ? $arr["vType"] : '--'; ?></td>
                      </tr>
                      <tr>
                        <td>Condition</td>
                        <td><?php echo  $arr["vCondition"] ? $arr["vCondition"] : '--'; ?></td>
                      </tr>
                      <tr>
                        <td>Year</td>
                        <td><?php echo  $arr["vYear"] ? $arr["vYear"] : '--'; ?></td>
                      </tr>
                    </table>
                    <a class="make-offer-btn" href="<?php echo $rootserver ?>/swapitemdisplay.php?saleid=<?php echo $arr["nSaleId"]?>&source=sa">Buy Now</a>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>

      <?php $j++;} }?>

      <!-- MODAL END -->

      <?php } else {?>  
      <div class="col-lg-12">
        <div class="box1_mdfd">
         Sorry No featured products available.
       </div>
     </div>    
     <?php }?>
   </div>
   <div class="clear"></div>
 </div>
</div>