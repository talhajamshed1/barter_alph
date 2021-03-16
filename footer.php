<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
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
                	<!-- <ul class="footer_links">
                    	<li><a href="aboutus.php" ><?php //echo TEXT_ABOUT_US?></a></li>
                        <li><a href="contactus.php"><?php //echo TEXT_CONTACT_US;?></a></li>
                        <li><a href="privacy.php"><?php //echo TEXT_PRIVACY_POLICY;?> </a></li>
                        <li><a href="terms.php"><?php //echo TEXT_TERMS;?> </a></li>
                    </ul>
                    <ul class="footer_links">
                    	<li><a href="faq.php"><?php //echo TEXT_FAQ;?></a></li>
                        <li><a href="help.php"><?php //echo TEXT_HELP;?></a></li>
                        <li><a href="tell_friend.php"><?php //echo TEXT_TELL_FRIEND;?></a></li>
                        <li><a href="sitemap.php"><?php //echo TEXT_SITEMAP;?></a></li>
                    </ul> -->
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
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/jquery.min.1.11.1.js"></script>
<script src="<?php echo SITE_URL;?>/js/bootstrap.min.js" language="javascript" type="text/javascript"></script>

<script src="<?php echo SITE_URL;?>/js/custom.min.js"  type="text/javascript"></script>
<script src="<?php echo SITE_URL;?>/js/main.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
  window.onload = (event) => {
    
 $('#homeSlider').slick({
      dots:false,
      arrows:true,
      fade:true,
      autoplay:true,
      slidesToShow: 1,
      slidesToScroll: 1,
      prevArrow: '<div class="slick-arrow slick-prev"><i class="flaticon-left-arrow"></i></div>',
            nextArrow: '<div class="slick-arrow slick-next"><i class="flaticon-next"></i></div>',

    });
};
</script>
</body>
</html>
