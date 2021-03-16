<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>                          |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
//fetching active footer links from db
$footerlinks=  footerlinks(TABLEPREFIX);
?>
<div class="full_width footer">
  <div class="container">

    <div class="full-width ftr_second_panel">

      <?php                   if ($footerlinks != '0') { $i=1; 
        while($rowSet = mysqli_fetch_array($footerlinks))
        {

          $escapeFlag=0;

          switch (strtoupper($rowSet['vCategoryTitle'])){
            case 'ABOUT US': $menu_item = TEXT_ABOUT_US;$escapeFlag=1;break;
            case 'CONTACT US': $menu_item = TEXT_CONTACT_US; break;
            case 'FAQ': $menu_item = TEXT_FAQ; break;
            case 'HELP': $menu_item = TEXT_HELP; break;
            case 'PRIVACY POLICY': $menu_item = TEXT_PRIVACY_POLICY; break;
            case 'SITE MAP': $menu_item = TEXT_SITEMAP;$escapeFlag=1;break;
            case 'TELL A FRIEND': $menu_item = TEXT_TELL_FRIEND; break;
            case 'TERMS': $menu_item = TEXT_TERMS; break;
            case 'CASHBACK': $menu_item = TEXT_CASHBACK; break;
            default: $menu_item = 'NULL';$escapeFlag=1; break;
          }
      //echo "<br>".$menu_item;

           // echo '<li>' . $menu_item . '</li>';

          if($i=="1")
            echo '<ul>'; 

          echo '<li><a href="' .$rowSet['vCategoryFile'] . '" >' .$menu_item. '</a></li>';



          $i++;
    }//end first for loop
  }?>

</div>

</div>
</div>

<div class="footer-bottom">
  <div class="container">
    <a href="<?php echo SITE_URL?>/index.php" title="<?php echo SITE_NAME;?>">
      <img class="img-responsive" src="<?php echo SITE_URL?>/images/<?php echo $logourl?>"  alt="<?php echo SITE_NAME;?>"  >
    </a>
    <span><?php echo TEXT_POWEREDBY; ?></span>
  </div>
</div>





  
  

<?php  if(basename($_SERVER['PHP_SELF'])!='usermain.php'  ) {?>

 <script type="text/javascript" src="<?php echo SITE_URL;?>/js/jquery.min.1.11.1.js"></script>  
    <script src="<?php echo SITE_URL;?>/js/bootstrap.min.js" language="javascript" type="text/javascript"></script>
    <script src="<?php echo SITE_URL;?>/js/bootstrap-select.min.js" language="javascript" type="text/javascript"></script>

<?php }?>

<?php  if(basename($_SERVER['PHP_SELF'])=='usermain.php'  ) {?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/jquery.min.1.11.1.js"></script> 
<?php }?>

    <script src="<?php echo SITE_URL;?>/js/custom.min.js"  type="text/javascript"></script>
    <script src="<?php echo SITE_URL;?>/js/main.js" language="javascript" type="text/javascript"></script>
    <script type="text/javascript">


      window.onload = (event) => {

       $('#homeSlider').slick({
        dots:true,
        arrows:false,
        fade:true,
        autoplay:true,
        slidesToShow: 1,
        slidesToScroll: 1

      });

     };
<?php if(basename($_SERVER['PHP_SELF'])!='usermain.php' && basename($_SERVER['PHP_SELF'])!='addsale.php'  && basename($_SERVER['PHP_SELF'])!='swapitem.php' )  { ?>


    $("#changeStatus").change(function(){
     var statusVal=$jqr(this).val();
     var redirectUrl = $jqr("#redirectUrl").val(); 
     var loggedUser = $jqr("#logged-user").val();
     var goLink =  'change_visibility_status.php?redirect_url='+redirectUrl+'&visibility_status='+statusVal; 
     window.location = goLink;


   });    

    $("#selectBox").change(function(){
      var langVal=$jqr(this).val();
      var redirectUrl = $jqr("#redirectUrl").val(); 
      var goLink =  'change_language.php?language_id='+langVal+'&redirect_url='+redirectUrl; 
      window.location = goLink;


    });    

<?php }?>
   </script>
   
   
   <?php  if(basename($_SERVER['PHP_SELF'])=='addsale.php') {?>
       <script src="js/jquery-1.8.3.min.js"></script>
<script src="js/jquery.Jcrop.min.js"></script>
<script src="js/jquery.uploadfile.min.js"></script>
<script src="js/product_image_upload.js"></script>
<script src="js/product_more_image_upload.js"></script>
       
   <?php }
   if(basename($_SERVER['PHP_SELF'])=='swapitem.php'){
   ?>


<link href="styles/upload_image.css" rel="stylesheet" type="text/css">
<link href="styles/jquery.Jcrop.css" rel="stylesheet" type="text/css">
<script src="languages/<?php echo $_SESSION['lang_folder']?>/message.js"></script>
       <script src="js/jquery-1.8.3.min.js"></script>
<script src="js/jquery.Jcrop.min.js"></script>
<script src="js/jquery.uploadfile.min.js"></script>
<script src="js/product_image_upload.js"></script>
<script src="js/product_more_image_upload.js"></script>

   <?php } ?>


</body>
</html>
