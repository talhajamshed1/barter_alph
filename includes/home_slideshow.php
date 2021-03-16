<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>                              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+


if (ENABLE_POINT != '1') {
  if (in_array('Sell', ModuleAcess($toplinks))) {
        //sale n feature item display
        /*$sql = "SELECT s.vTitle,s.nSaleId,s.vSmlImg,s.vUrl FROM  " . TABLEPREFIX . "sale s
          LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=s.nUserId
          where s.nQuantity >'0' and s.vDelStatus='0' AND u.vStatus='0' order by s.nSaleId DESC  LIMIT 0,4 ";
        $sql = "(SELECT sw.vTitle,sw.nSwapId nSwapId,sw.vSmlImg,sw.vUrl,sw.dPostDate, 1 AS types,sw.vDescription,sw.nValue,sw.vCondition,
        sw.vType, sw.vBrand,sw.vYear, u.vLoginName FROM  " . TABLEPREFIX . "swap sw
      LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=sw.nUserId
      where sw.vDelStatus='0' AND u.vStatus='0') UNION 
                        (SELECT sa.vTitle,sa.nSaleId nSwapId,sa.vSmlImg,sa.vUrl,sa.dPostDate, 2 AS types,sa.vDescription,sa.nValue,sa.vCondition,
        sa.vType, sa.vBrand,sa.vYear, u.vLoginName FROM  " . TABLEPREFIX . "sale sa
      LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=sa.nUserId
      where sa.nQuantity >'0' and sa.vDelStatus='0' AND u.vStatus='0') ORDER BY dPostDate DESC LIMIT 0,4"; */

      $sql = "(SELECT sw.vTitle,sw.nSwapId nSwapId,sw.vSmlImg,sw.vUrl,sw.dPostDate, 1 AS types,sw.vDescription,sw.nValue,
      sw.vCondition, sw.vType, sw.vBrand,sw.vYear, u.vLoginName, sw.vPostType as itemType, cl.vCategoryDesc, cl.lang_id 
      FROM  " . TABLEPREFIX . "swap sw
      LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=sw.nUserId 
      LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = sw.nCategoryId 
      WHERE sw.vDelStatus='0' AND u.vStatus='0' AND sw.vSwapStatus= '0' AND u.nUserId <> '".$_SESSION['guserid']."' AND cl.lang_id = '" . $_SESSION['lang_id']."') 
      UNION
      (SELECT sa.vTitle,sa.nSaleId nSwapId,sa.vSmlImg,sa.vUrl,sa.dPostDate, 2 AS types,sa.vDescription,sa.nValue,
      sa.vCondition, sa.vType, sa.vBrand,sa.vYear, u.vLoginName,'sale' as itemType, cl.vCategoryDesc, cl.lang_id  
      FROM  " . TABLEPREFIX . "sale sa
      LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=sa.nUserId
      LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = sa.nCategoryId
      WHERE sa.nQuantity >'0' AND sa.vDelStatus='0' AND u.vStatus='0' 
      AND u.nUserId <> '".$_SESSION['guserid']."' AND cl.lang_id = '" . $_SESSION['lang_id']."') ORDER BY dPostDate DESC";

    }
  }
  else{
 /*   $sql = "SELECT s.vTitle,s.nSwapId,s.vSmlImg,s.vUrl,s.vDescription,s.nQuantity,s.dPostDate,s.nValue,s.vCondition,
        s.vType, s.vBrand,s.vYear, u.vLoginName FROM  " . TABLEPREFIX . "swap s
      LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=s.nUserId
      where s.vDelStatus='0' AND u.vStatus='0' AND  s.vPostType='swap'  order by s.nSwapId DESC  LIMIT 0,4";  */

      $sql = "SELECT s.vTitle, s.nSwapId, s.vSmlImg, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, s.nValue, s.vCondition,
      s.vType, s.vBrand, s.vYear, u.vLoginName, s.vPostType as itemType, cl.vCategoryDesc, cl.lang_id  
      FROM  " . TABLEPREFIX . "swap s
      LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
      LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId
      WHERE s.vDelStatus='0' AND u.vStatus = '0' AND u.nUserId <> '".$_SESSION['guserid']."' AND cl.lang_id = '" . $_SESSION['lang_id']."' AND s.vSwapStatus= '0'
      AND s.vPostType = 'swap' ORDER BY s.nSwapId DESC";
    }


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
    <div class="">

      <h2 class="item-slider-section-heading"><?php echo HEADING_NEW_ADDITION; ?></h2>
      <div class="featured_wrps">
        <?php  
        if (mysqli_num_rows($result) != 0) { ?>

        <div class="itemSlider">
          <?php $i=0;
          while ($arr = mysqli_fetch_array($result)) {

            if (ENABLE_POINT != '1') {

              $sale_id       = $arr["nSwapId"];
              $var_source    =  "sa";

              if($arr["types"]=="1")
                $surl = "swapid";
              else if($arr["types"]=="2")
               $surl = "saleid"; 

           } else{

            $sale_id       = $arr["nSwapId"];
            $var_source    =  "s";

            $surl = "swapid";
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


          $type = "";
          if($arr["itemType"]=="sale"){

            $v_url = getDisplayImage($arr["nSwapId"], "sa", $arr["vUrl"]);
            $type ="sa";

          } elseif($arr["itemType"]=="swap"){

            $v_url = getDisplayImage($arr["nSwapId"], "s", $arr["vUrl"]);
            $type ="s";

          } elseif($arr["itemType"]=="wish"){

            $type ="w";
            $v_url =$arr["vUrl"];
          }

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


          if($i < mysqli_num_rows($result2)){ ?>
          <!-- newdesign -->
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

                <img src="<?php echo $var_burl;?>" />
                <div class="action-tiles">
                  <a href="<?php echo $rootserver ?>/swapitemdisplay.php?<?php echo $surl ?>=<?php echo $sale_id?>&source=<?php echo $type;?>" title="View Details"><i class="flaticon-shopping-cart"></i></a>
                  <a href="#" data-toggle="modal" title="Quick View" data-target="#detailproductModaladd<?php echo $i; ?>"><i class="flaticon-zoom-in"></i></a>
                </div>
              </div>
              <div class="item-slider-section-tile-bottom-sec">
                <a href="<?php echo $rootserver ?>/swapitemdisplay.php?<?php echo $surl ?>=<?php echo $sale_id?>&source=<?php echo $type;?>" title="View Details">
                  <?php 
                  $title = $arr['vTitle'];
                  if(strlen($arr['vTitle'])>20)
                    $title = substr($arr['vTitle'], 0,20)."...";
                  ?>
                  <h4><?php echo $title; ?></h4>
                  <h3><?php echo $item_price ?></h3>
                </a>
                <span>Posted By <i><a ><?php echo $arr['vLoginName']; ?></a></i></span>
              </div>
            </div>
          </div>

          <?php } 
          ?>

          <?php $i++; } ?>

        </div>

        <!-- MODAL START -->

        <?php 

        $j=0;
        while ($arr = mysqli_fetch_array($result2)) {

          if (ENABLE_POINT != '1') {

            $sale_id2       = $arr["nSwapId"];
            $var_source2    =  "sa";

            if($arr["types"]=="1")
              $surl2 = "swapid";
            else if($arr["types"]=="2")
              $surl2 = "saleid";

          } else{

            $sale_id2       = $arr["nSwapId"];
            $var_source2    =  "s";

            $surl2 = "swapid";
          }
          

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

          
          $type2 = "";
          if($arr["itemType"]=="sale"){

            $var_burl2 = getDisplayImage($arr["nSwapId"], "sa", $arr["vUrl"]);
            $type2 ="sa";

          } elseif($arr["itemType"]=="swap"){

            $var_burl2 = getDisplayImage($arr["nSwapId"], "s", $arr["vUrl"]);
            $type2 ="s";

          } elseif($arr["itemType"]=="wish"){

            $type2 ="w";
            $var_burl2 = $arr["vUrl"];
          }



          $img_list = array();
          if($arr["itemType"] != "wish") {

            $vurl = trim($arr["vUrl"]);
            if ($vurl != ''){

              $gallery_list = getMoreImages($arr["nSwapId"], $type2);
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

              $gallery_list = getMoreImages($arr["nSwapId"], $type2);
              if(!empty($gallery_list)){

                $first_image = array_shift($gallery_list);
                foreach ($gallery_list as $img) {
                  $img_list[] = $img;
                }
              } else{
                $first_image = "images/nophoto.gif"; 
              }
            }
          } else {

            $vurl = trim($arr["vUrl"]);
            if ($vurl != ''){

              $vurl_img = str_replace('medium_', '', $vurl);
              if(@file_exists($vurl_img)) {
                $first_image = $vurl_img;
              } elseif (@file_exists($vurl)) {
                $first_image = $vurl;
              } else{
                $first_image = "images/nophoto.gif"; 
              }
            }
          }


          if($j < mysqli_num_rows($result2)){ ?>

          <!-- QUICK VIEW -->
          <div id="detailproductModaladd<?php echo  $j; ?>" class="modal productdetailpopup" role="dialog">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-dialog">
              <div class="modal-content">

                <div class="modal-body">

                 <div class="row">
                   <div class="col-md-5">
                    <div class="productdetailpopup-left">
                      <div class="Product-img-container">
                        <img id="ProductImg0<?php echo  $j; ?>" src="<?php echo $first_image;?>" data-zoom-image="<?php echo $first_image;?>"/>
                      </div>
                      <div class="product_item_gallery" id="product_item_gallery0<?php echo $j ?>">
                        <a class="product_gallery_item active" data-image="<?php echo $first_image;?>" data-zoom-image="<?php echo $first_image;?>" tabindex="0">
                          <img src="<?php echo $first_image;?>" alt="product_img" data-targetid ="<?php echo  $j; ?>">
                        </a>

                        <?php 
                        if(!empty($img_list)){
                          foreach($img_list as $key=>$image){ ?>
                          <a class="product_gallery_item active" data-image="<?php echo $image;?>" data-zoom-image="<?php echo $image;?>" tabindex="0">
                            <img src="<?php echo $image;?>" alt="product_img_<?php echo $key; ?>" data-targetid ="<?php echo  $j; ?>">
                          </a>

                          <?php } }?>
                        </div> 

                      </div>
                    </div>
                    <div class="col-md-7">
                      <div class="productdetailpopup-right">
                       <h3><?php echo (strlen(trim($arr["vTitle"]))>21)?strtoupper(substr(htmlentities($arr["vTitle"]),0,21))."...":strtoupper(htmlentities($arr["vTitle"]));?></h3>
                       <div class="bottom-price">
                        <h4><?php echo ($arr['nValue'] > 0) ? 'Price ' : '' ?><?php echo $item_price ?></h4>

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
                    <a class="make-offer-btn" href="<?php echo $rootserver ?>/swapitemdisplay.php?<?php echo $surl2 ?>=<?php echo $sale_id2?>&source=<?php echo $type2;?>">Buy Now</a>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>


      <?php $j++; } } ?>

      <!-- End QUICK VIEW  -->

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
