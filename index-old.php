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
   include("./includes/config.php");
   include("./includes/session.php");
   include("./includes/functions.php");
   include("./includes/logincheck.php");
   
   if(isset($_SESSION['user_status']) && $_SESSION['user_status']!= "0") 
      {
          header('location:logout.php');
   	exit();
      }
   
   if (!isset($_SESSION["guseraffid"]) || $_SESSION["guseraffid"] == "") {
       /*if (function_exists('session_register')) {
           session_register("guseraffid");
       }
       */
       $_SESSION["guseraffid"] = $_GET["guseraffid"];
   }
   
   include_once('./includes/gpc_map.php');
   $message = "";
   include_once('./includes/title.php');
   
   if ($_SESSION["guserid"] != "") {
       $login_display = 'none';
   }
   
   ?>
<script type="text/javascript" src="./js/jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="./js/stepcarousel.js">
   /***********************************************
    * Step Carousel Viewer script- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
    * Visit http://www.dynamicDrive.com for hundreds of DHTML scripts
    * This notice must stay intact for legal use
    ***********************************************/
</script>
<body onLoad="timersOne();">
   <?php include_once('./includes/top_header.php'); ?>
   <link rel="stylesheet" href="styles/jquery.remodal.css">
   <div class="row home_box_sec">
      <div class="container">
         <div class="wish_swap_sell_wrap">
            <?php if (in_array('Wish', ModuleAcess($toplinks))) { ?>
            <div class=" col-lg-4">
               <div class="wish_div">
                  <?php $wish_arr = ContentLookUp('wish'); ?>
                  <div class="wish_icon">&nbsp;</div>
                  <h5><?php echo $wish_arr['content_title']; ?></h5>
                  <p><?php echo $wish_arr['content']; ?></p>
                  <div class="btn_containr">
                     <input type="button" onclick="javascript:window.location='./addsale.php?type=wish';" value="<?php echo MENU_ADD_WISH; ?>" class="swap_btns">
                  </div>
                  <div class="clear"></div>
               </div>
            </div>
            <?php
               }
               if (in_array('Swap', ModuleAcess($toplinks))) {
               	?>
            <?php $swap_arr = ContentLookUp('swap'); ?>
            <div class=" col-lg-4">
               <div class="swap_div">
                  <div class="swap_icon">&nbsp;</div>
                  <h5><?php echo $swap_arr['content_title']; ?></h5>
                  <p><?php echo $swap_arr['content']; ?></p>
                  <div class="btn_containr">
                     <input type="button" onclick="javascript:window.location='./addsale.php?type=swap';" value="<?php echo MENU_ADD_SWAP; ?>" class="swap_btns">
                     <div class="clear"></div>
                  </div>
                  <div class="clear"></div>
               </div>
            </div>
            <?php
               }
               //checking point enable in website
               if (ENABLE_POINT != '1') {
               	if (in_array('Sell', ModuleAcess($toplinks))) {
               		?>
            <?php $sell_arr = ContentLookUp('sell'); ?>
            <div class="col-lg-4">
               <div class="sell_div">
                  <div class="sell_icon">&nbsp;</div>
                  <h5><?php echo $sell_arr['content_title']; ?></h5>
                  <p><?php echo $sell_arr['content']; ?></p>
                  <div class="btn_containr">
                     <input type="button" onclick="javascript:window.location='./addsale.php?type=sale';" value="<?php echo MENU_ADD_SALE; ?>" class="swap_btns">
                     <div class="clear"></div>
                  </div>
                  <div class="clear"></div>
               </div>
            </div>
            <?php
               }
               }
               ?>
            <div class="clear"></div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="welcome_section_home">
         <div class="container">
            <div class="full-width">
               <div class="col-lg-12">
                  <?php
                     $hintro_arr = ContentLookUp('hintro');
                     $hintro_img = DisplayLookUp('welcomeImage');
                     $hintro_img1 = "images/".$hintro_img;
                     ?>
                  <?php /*?> 
                  <div class="welcome_picdiv">
                     <?php
                        if(is_file($hintro_img1)) {
                        ?>
                     <img src="<?php echo $hintro_img1; ?>" alt="Welcome Pic" border="0" width="196" height="150" />
                     <?php
                        }
                        ?>
                  </div>
                  <?php */?>
                  <div class="welcome_contentdiv">
                     <h4><?php echo $hintro_arr['content_title']; ?></h4>
                     <p><?php echo $hintro_arr['content']; ?></p>
                  </div>
                  <div class="clear"></div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="homepage_contentsec home_product_outer">
      <div class="container">
         <div class="row">
            <div class="col-lg-12">
               <?php
                  //checking point enable in website
                  if (ENABLE_POINT != '1') {
                  	if (in_array('Sell', ModuleAcess($toplinks))) {
                  		include_once('./includes/featured.php');
                  	}
                  }
                  ?>
               <?php require_once("./includes/home_slideshow.php"); ?>
               <?php
                  if (DisplayLookUp('BannerDisplay') == 'Yes') {
                  	require_once("./includes/banners.php");
                  }
                  ?>
            </div>
         </div>
      </div>
   </div>
   <?php // require_once("./includes/header.php"); ?>
   <?php //require_once("menu.php"); ?>
   <script src="js/jquery.remodal.js"></script>
   <!-- Events -->
   <script>
      // $(document).on("open", ".remodal", function () {
      //   console.log("open");
      // });
      
      // $(document).on("opened", ".remodal", function () {
      //   console.log("opened");
      // });
      
      // $(document).on("close", ".remodal", function (e) {
      //   console.log('close' + (e.reason ? ", reason: " + e.reason : ''));
      // });
      
      // $(document).on("closed", ".remodal", function (e) {
      //   console.log('closed' + (e.reason ? ', reason: ' + e.reason : ''));
      // });
      
      // $(document).on("confirm", ".remodal", function () {
      //   console.log("confirm");
      // });
      
      // $(document).on("cancel", ".remodal", function () {
      //   console.log("cancel");
      // });
      
      //  You can open or close it like this:
      //  $(function () {
      //    var inst = $.remodal.lookup[$("[data-remodal-id=modal]"").data("remodal")];
      //    inst.open();
      //    inst.close();
      //  });
      
      //  Or init in this way:
      // var inst = $("[data-remodal-id=modal2]").remodal();
      //  inst.open();
      
      
      // $('.dropdown-toggle').dropdown();
      
   </script>
   <footer>
      <?php
         require_once("./includes/footer.php");
         if ($_GET["paid"] == "no") {
             echo("<script> alert('".ERROR_TRANSACTION."'); </script>");
         }
         ?>
   </footer>