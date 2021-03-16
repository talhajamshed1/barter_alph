
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>                                |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+

$sql = "SELECT * FROM " . TABLEPREFIX . "category C
LEFT JOIN " . TABLEPREFIX . "category_lang L ON C.nCategoryId = L.cat_id AND L.lang_id = '" . $_SESSION['lang_id'] . "' 
WHERE C.nParentId = '0' ORDER BY rand()";

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));


$category = array();
$i = 0;
if (mysqli_num_rows($result) != 0) {
   while ($row = mysqli_fetch_array($result)) {

      $cat_image = "banners/".$row['cat_image'];
      if(($row['cat_image'] == '')||(!file_exists($cat_image))){
         $cat_image = "banners/nocatimage.jpg";
      }

      $category[$i]['cat_id'] = $row['cat_id'];
      $category[$i]['vCategoryDesc'] = $row['vCategoryDesc'];
      $category[$i]['cat_image'] = $cat_image;
      if(sizeof($category) == 4){
         break;
      }
      
      $i++;
   } 
}
?>


<!-- ===category-homepage== -->
<div class="category-home-section">
   <div class="container">
      <div class="row">
         <div class="col-sm-6 col-xs-6">
            <?php if(isset($category[0]['cat_id'])) { ?>
            <div class="category-tiles height-100">
               <a href="<?php echo $rootserver ?>/categorydetail.php?catid=<?php echo $category[0]['cat_id'] ?>&categorydesc=<?php echo $category[0]['vCategoryDesc'] ?>">
                  <img src="<?php echo $category[0]['cat_image'] ?>" alt="">
                  <span><?php echo $category[0]['vCategoryDesc'] ?></span>
               </a>
            </div>
            <?php }
            if(isset($category[1]['cat_id'])) { ?>
            <div class="category-tiles height-50">
               <a href="<?php echo $rootserver ?>/categorydetail.php?catid=<?php echo $category[1]['cat_id'] ?>&categorydesc=<?php echo $category[1]['vCategoryDesc'] ?>">
                  <img src="<?php echo $category[1]['cat_image'] ?>" alt="">
                  <span><?php echo $category[1]['vCategoryDesc'] ?></span>
               </a>
            </div>
         </div>
         <?php } ?>
         <div class="col-sm-6 col-xs-6">
            <?php if(isset($category[2]['cat_id'])) { ?>
            <div class="category-tiles height-50">
               <a href="<?php echo $rootserver ?>/categorydetail.php?catid=<?php echo $category[2]['cat_id'] ?>&categorydesc=<?php echo $category[2]['vCategoryDesc'] ?>">
                  <img src="<?php echo $category[2]['cat_image'] ?>" alt="">
                  <span><?php echo $category[2]['vCategoryDesc'] ?></span>
               </a>
            </div>
            <?php }
            if(isset($category[3]['cat_id'])) { ?>
            <div class="category-tiles height-100">
               <a href="<?php echo $rootserver ?>/categorydetail.php?catid=<?php echo $category[3]['cat_id'] ?>&categorydesc=<?php echo $category[3]['vCategoryDesc'] ?>">
                  <img src="<?php echo $category[3]['cat_image'] ?>" alt="">
                  <span><?php echo $category[3]['vCategoryDesc'] ?></span>
               </a>
            </div>
            <?php } ?>
         </div>
      </div>
      <div class="text-center">
         <br>
      <a class="see-more-btn" title="Categories" href="<?php echo $rootserver ?>/categorydetail.php">See More</a>
   </div>
   </div>
</div>

<!-- END -->