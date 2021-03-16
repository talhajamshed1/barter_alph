<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
   // +----------------------------------------------------------------------+
   // | PHP version 4/5                                                      |
   // +----------------------------------------------------------------------+
   // | This source file is a part of iScripts eSwap                         |
   // +----------------------------------------------------------------------+
   // | Authors: Programmer<simi@armia.com>                                 |
   // +----------------------------------------------------------------------+
   // | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
   // | All rights reserved                                                  |
   // +----------------------------------------------------------------------+
   // | This script may not be distributed, sold, given away for free to     |
   // | third party, or used as a part of any internet services such as      |
   // | webdesign etc.                                                       |
   // +----------------------------------------------------------------------+

if (isset($_REQUEST['txtHomeSearch']) && trim($_REQUEST['txtHomeSearch']) != '') {
  $txtHomeSearch = $_REQUEST['txtHomeSearch'];
}
else {
  $txtHomeSearch = TEXT_SEARCH_PRODUCT_NAME;
}

$PG_TITLE = ClientFilePathName($_SERVER['PHP_SELF']);

//fetching active top links from db
$topheaderlinks = topheaderlinks(TABLEPREFIX);
//fetching active top links from db
if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != ""){    
  $toplinks = session_toplinks(TABLEPREFIX);

  /************ Validation for user existence or user deactivation **********/
  if(validate_if_user_exists($_SESSION["guserid"],TABLEPREFIX)){
    if(!validate_if_user_active($_SESSION["guserid"],TABLEPREFIX)){
     header("location:logout.php?from=dea");
     exit();
   }
 }else{
   session_destroy();        
   header("location:login.php?msg=".ERROR_ACCOUNT_DELETED);
   exit();
 }
 /************ Validation for user existence or user deactivation **********/
}
else {

  $toplinks = toplinks(TABLEPREFIX);
}
   //setting active link colors
$filename = "themes/" . $sitestyle;
$css_file = file_get_contents("$filename");
$color_line = explode("\n", strstr($css_file, '.toplinks_current'));
$selctdColor = explode(":", $color_line[2]);
$selctdColor = trim(str_replace(";", "", $selctdColor[1]));

if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
  if(isset($_SESSION['sess_activeTill']))
  {
    $present_time = time() + (60 * 5);
    if($present_time > $_SESSION['sess_activeTill'])
    {
      $session_active_time = ini_get("session.gc_maxlifetime");
      $activeTill = $_SESSION['sess_activeTill'] + $session_active_time;
      $display = 'Y';
        //mysqli_query($conn, "UPDATE ".TABLEPREFIX."online SET nActiveTill='".$activeTill."' WHERE nUserId='".$_SESSION["guserid"]."'") or die(mysqli_error($conn));
      mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."online (nUserId,nLoggedOn,nActiveTill,nIdle,vVisible) VALUES ('".$_SESSION["guserid"]."','".$present_time."','".$activeTill."','0','".$display."') ON DUPLICATE KEY UPDATE nActiveTill='".$activeTill."'") or die(mysqli_error($conn));
    }
  }
}
?>




<script type="text/javascript" src="<?php echo SITE_URL;?>/js/jquery.min.1.11.1.js"></script>
<!--  <script src="<?php echo SITE_URL;?>/js/main.js" language="javascript" type="text/javascript"></script> -->


<?php if(basename($_SERVER['PHP_SELF'])=='usermain.php' )  { ?>

<script src="<?php echo SITE_URL;?>/js/bootstrap-select.min.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo SITE_URL;?>/js/bootstrap.min.js" language="javascript" type="text/javascript"></script>

<?php }?>

<script language="javascript" type="text/javascript">

 function Clear_text(obj,act)
 {
   if(obj.value=='<?php echo TEXT_SEARCH_PRODUCT_NAME; ?>' && act=='in')
   {
     obj.value='';
   }
   else if(obj.value=='' && act=='out')
   {
     obj.value='<?php echo TEXT_SEARCH_PRODUCT_NAME; ?>';
   }
   }//end function
   
   function ValidSearch()
   {
     if(document.frmHSearch.txtHomeSearch.value=='<?php echo TEXT_SEARCH_PRODUCT_NAME; ?>' || document.frmHSearch.txtHomeSearch.value=='')
     {
       alert("<?php echo ERROR_SEARCH_TEXT_EMPTY; ?>");
          // document.frmHSearch.txtHomeSearch.value='';
          document.frmHSearch.txtHomeSearch.focus();
          return false;
       }//end if
       /*if(document.frmHSearch.txtHomeSearch.value=='')
       {
           alert("Search can't be blank");
           document.frmHSearch.txtHomeSearch.focus();
           return false;
       }//end if
       */
   }//end function
 </script>

 <script type="text/javascript">
   var $jqr=jQuery.noConflict();
   $jqr(document).ready(function () {


    $jqr('.signin').click(function () {           
     if ($jqr('#signin_menu').css('display') == 'block') {
       $jqr('#signin_menu').hide()
       $jqr('.signin').removeClass('menu-open');
     } else {
       $jqr('#signin_menu').show()
       $jqr('.maintext_small').html('');
       $jqr('.signin').addClass('menu-open');
     }        
     return false;
   });
    $jqr('#signin_menu').click(function(e) {

     e.stopPropagation();

   });
    $jqr(document).click(function() {
     $jqr('#signin_menu').hide();
     $jqr('.signin').removeClass('menu-open');
   });



    <?php if(basename($_SERVER['PHP_SELF'])=='usermain.php' || basename($_SERVER['PHP_SELF'])=='addsale.php' )  { ?>


      $jqr("#changeStatus").change(function(){
       var statusVal=$jqr(this).val();
       var redirectUrl = $jqr("#redirectUrl").val(); 
       var loggedUser = $jqr("#logged-user").val();
       var goLink =  'change_visibility_status.php?redirect_url='+redirectUrl+'&visibility_status='+statusVal; 
       window.location = goLink;


     });    

      $jqr("#selectBox").change(function(){
        var langVal=$jqr(this).val();
        var redirectUrl = $jqr("#redirectUrl").val(); 
        var goLink =  'change_language.php?language_id='+langVal+'&redirect_url='+redirectUrl; 
        window.location = goLink;


      });    

      <?php }?>



    });   
  </script>


  <input type="hidden" name="redirect-url" value="<?php echo urlencode($_SERVER['REQUEST_URI']);?>" id="redirectUrl">
  <div id="Layout" >
    <?php if ($_SESSION["guserid"] == "") { ?>
    <?php require("./includes/login_drop.php"); ?>
    <?php } ?>
    <header>







  <!--  <div class="header_top_panel">
      <div class="container">
         <div class="row ">
            <div class="col-sm-6 col-md-6 col-lg-6">
               <div class="header_top_panel_left_col">
                  <p class="laguage_change">
                     <select class="selectpicker" id="selectBox" name="cmbLanguage">
                        <option value=""> <?php echo TEXT_CHANGE_LANGUAGE;?></option>
                        <?php
                           $sql_flag = "select * from ".TABLEPREFIX."lang where lang_status = 'y' ";
                           $res_flag = mysqli_query($conn, $sql_flag) or die(mysqli_error($conn));
                           while($query_data = mysqli_fetch_array($res_flag))
                           {
                              $languageDir    =   "languages/".$query_data['folder_name'];
                           
                              //if (is_dir($languageDir)) {
                           ?>
                        <option value="<?php echo $query_data["lang_id"]; ?>" <?php if($_SESSION['lang_id']==$query_data["lang_id"]) {  echo "selected"; } ?>><?php echo $query_data["lang_name"]; ?></option>
                        <?php }
                           // }
                            ?>
                     </select>
                  </p>
                 
                  <div class="clear"></div>
               </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 ">
             
               <div class="sign_insection header_top_panel_right_col">
                  <div class="sign_indiv" id="sign_indiv" >
                     <?php if ($_SESSION["guserid"] == "") { ?>
                     <a href="login.php" class="signin">
                     <?php echo TEXT_SIGNIN;?>
                     </a>
                     <?php
                        } else {
                            ?>
                     <a href="logout.php" class="logout"><span><?php echo MENU_LOGOUT;?></span></a>
                     <?php
                        }
                        ?>
                  </div>
                  <?php if ($_SESSION["guserid"] == "") { ?>
                  <div id="signin_menu" style="display: none;">
                     <?php //require("./includes/login_drop.php"); ?>
                  </div>
                  <?php } ?>
                  <?php if ($_SESSION["guserid"] == "") { ?>
                  <div class="signup_div" > <a href="register.php">
                     <?php echo TEXT_SIGNUP;?>
                     </a> 
                  </div>
                  <?php } ?>
               </div>
               <div class="Wlcome_user_area">
                  <?php
                     if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
                         ?>
                  <span class="glyphicon glyphicon-user"></span>
                  <?php echo TEXT_WELCOME; ?> <span><?php echo ucfirst($_SESSION["guserFName"]); ?> </span>
                  <select id="changeStatus" name="cmbStatus">
                     <?php
                        $sql_flag = "select vVisible from ".TABLEPREFIX."online where nUserId = '".$_SESSION["guserid"]."' ";
                        $res_flag = mysqli_query($conn, $sql_flag) or die(mysqli_error($conn));
                        $result = mysqli_fetch_array($res_flag);
                        $status = $result['vVisible'];
                        ?>
                     <option value='Y' <?php echo ($status == 'Y') ? "selected" : '' ;?>>Visible</option>
                     <option value='N' <?php echo ($status == 'N') ? "selected" : '' ;?>>Invisible</option>
                  </select>
                  <?php } ?>
                  <div class="clear"></div>
               </div>
               <div class="clear"></div>
            </div>
         </div>
      </div>
    </div> -->
    <!-- top-header -->
    <div class="top-header">
      <div class="search-pop">
        <i class="flaticon-cancel" id="search-pop-close"></i>
        <form name="frmHSearch" class="home_search" method="get" action="search.php" onSubmit="return ValidSearch();">
          <input type="text" name="txtHomeSearch" placeholder="Searc here..">
          <button type="submit"> <i class="flaticon-loupe"></i></button>
        </form>
      </div>
      <div class="container">
        <div class="top-header-inner">
          <div class="top-header-left">
            <div class="language-switch-btn">
              <select class="selectpicker" id="selectBox" name="cmbLanguage">
                <option value=""> <?php echo TEXT_CHANGE_LANGUAGE;?></option>
                <?php
                $sql_flag = "select * from ".TABLEPREFIX."lang where lang_status = 'y' ";
                $res_flag = mysqli_query($conn, $sql_flag) or die(mysqli_error($conn));
                while($query_data = mysqli_fetch_array($res_flag))
                {
                  // $languageDir    =   "languages/".$query_data['folder_name'];

                        //if (is_dir($languageDir)) {
                  ?>
                  <option value="<?php echo $query_data["lang_id"]; ?>" <?php if($_SESSION['lang_id']==$query_data["lang_id"]) {  echo "selected"; } ?>><?php echo $query_data["lang_name"]; ?></option>
                  <?php 
                }
                     // }
                ?>
              </select>
            </div>
            <?php
            if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
             ?>
             <div class="user-welcome-sec">

               <span class="welcom-usr">
                <?php echo TEXT_WELCOME; ?> 
                <span>
                <?php 
                  function limit_text($text, $limit) {
                      if (str_word_count($text, 0) > $limit) {
                          $words = str_word_count($text, 2);
                          $pos   = array_keys($words);
                          $text  = substr($text, 0, $pos[$limit]) . '...';
                      }
                      return $text;
                  }
                  echo limit_text(ucfirst($_SESSION["guserFName"]),3);
                ?>
               <span>
               
               <!--  <span><?php echo ucfirst($_SESSION["guserFName"]); ?> </span> -->
              </span>
               <select class="selectpicker visible-switcher" id="changeStatus" name="cmbStatus">
                <?php
                $sql_flag = "select vVisible from ".TABLEPREFIX."online where nUserId = '".$_SESSION["guserid"]."' ";
                $res_flag = mysqli_query($conn, $sql_flag) or die(mysqli_error($conn));
                $result = mysqli_fetch_array($res_flag);
                $status = $result['vVisible'];
                ?>
                <option value='Y' <?php echo ($status == 'Y') ? "selected" : '' ;?>>Visible</option>
                <option value='N' <?php echo ($status == 'N') ? "selected" : '' ;?>>Invisible</option>
              </select>

            </select>

          </div>
          <?php }else{
            echo '<div class="user-welcome-sec"><span class="welcom-usr">Welcome To<span>Eswap</span></span></div>';
          } ?>


        </div>
        <div class="top-header-center">
         <a href="<?php echo SITE_URL?>/index.php" title="<?php echo SITE_NAME;?>">
          <img class="img-responsive" src="<?php echo SITE_URL?>/images/<?php echo $logourl?>"  alt="<?php echo SITE_NAME;?>"  >
        </a>
      </div>
      <div class="top-header-right">
        <a class="search-btn-top" href="#"  title="Search Now">
          <i class="flaticon-loupe"></i>
        </a>
       <!--  <a class="wish-btn-top" title="Wish items" href="<?php echo SITE_URL?>/wishlistdetailed.php">
          <i class="flaticon-heart"></i>
        </a> -->

        <?php if ($_SESSION["guserid"] == "") { ?>
        <a class="wish-btn-top" href="#" data-toggle="modal" data-target="#login-modal">
         <i class="flaticon-user-1"></i>
       </a>
       <!-- <?php echo TEXT_SIGNIN;?> -->

       <?php
     } else {
      ?>
      <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
          <i class="flaticon-user-1"></i>
        </button>
        <ul class="dropdown-menu">
          <li><a href="<?php echo $rootserver?>/user_profile.php?uid=<?php echo $_SESSION["guserid"]; ?>">My Account</a></li>
          <li><a href="logout.php"><?php echo MENU_LOGOUT;?></a></li>
        </ul>
      </div>

      <?php
    }
    ?>




    <!--  -->
  </div>
</div>
</div>
</div>
<!-- //top-header -->


<!--plain HTML-->
<div class="main-navigation">
 <div class="container main-navigation-inner">
  <nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
     <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
       <span class="sr-only"><?php echo TEXT_TOGGLE;?></span>
       <span class="icon-bar"></span>
       <span class="icon-bar"></span>
       <span class="icon-bar"></span>
     </button>
   </div>
   <!-- Collect the nav links, forms, and other content for toggling -->
   <div class="collapse navbar-collapse navbar-ex1-collapse">
     <ul class="nav navbar-nav">
      <?php
      $current_page =  basename($_SERVER['REQUEST_URI']); 
      if ($toplinks != '0'){
       while($rowSet = mysqli_fetch_array($toplinks)){      
         $escapeFlag=0;              
         switch(strtoupper($rowSet['vCategoryTitle'])){
           case 'HOME': $menu_item = MENU_HOME; $escapeFlag=1; break;
           case 'WISH': $menu_item = MENU_WISH; break;
           case 'SWAP': $menu_item = MENU_SWAP; break;
           case 'SELL': $menu_item = MENU_SELL; break;
           case 'REGISTER': $menu_item = MENU_REGISTER; break;
           case 'LOGIN': $menu_item = MENU_LOGIN; $escapeFlag=1; break;
           case 'ONLINE MEMBERS': $menu_item = MENU_ONLINE_MEMBERS; break;
           case 'CATEGORY DISPLAY': $menu_item = MENU_CATEGORY_DISPLAY; break;
           case 'REFERRAL': $menu_item = MENU_REFERRAL; break;
           case 'MY BOOTH': $menu_item = MENU_MYBOOTH;  break;
           case 'LOGOUT': $menu_item = MENU_LOGOUT; break;
           default: $menu_item = 'NULL';$escapeFlag=1; $menu_class = ''; break;
         }          

         if($menu_item == MENU_HOME && $rowSet['vCategoryFile']=="index.php"){
           $menu_class = "active";
         }
         if(($current_page == "eswap" && $rowSet['vCategoryFile'] == "index.php")||($rowSet['vCategoryFile'] == $current_page)){ 
           $menu_class = "active";
         }
         else{
           $menu_class = "";
         }
         echo '<li><a class="'.$menu_class.'" href="' .SITE_URL.'/'.$rowSet['vCategoryFile'] . '" >' . Highligt($PG_TITLE, $rowSet['vCategoryFile'], $menu_item, $selctdMenuColor) . '</a></li>';      
       }
     }
     $sql = "select vLookUpDesc from " . TABLEPREFIX . "lookup where nLookupcode=15 and vLookUpDesc='0'";
     $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
     if (mysqli_num_rows($result) > 0){
       if($current_page == "referralinfo.php")
        $menu_class = "active";
      else
        $menu_class = "";
      echo '<li><a class = "'.$menu_class.'" href="' . $rootserver . '/referralinfo.php" >' . Highligt($PG_TITLE, 'referralinfo.php', MENU_REFERRAL, $selctdMenuColor) . '</a></li>';
                           }//end if
                           
                                            // Logged Links Display 

                              /*  if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != "") {
                               $loggedlinks = logged_toplinks(TABLEPREFIX);
                               if ($loggedlinks != '0') {
                                   for ($i = 0; $i < mysqli_num_rows($loggedlinks); $i++) {
                              
                                       switch (strtoupper(mysqli_result($loggedlinks, $i, 'vCategoryTitle'))){
                                           
                                           case 'ONLINE MEMBERS': $menu_item = MENU_ONLINE_MEMBERS; break;
                                           case 'CATEGORY DISPLAY': $menu_item = MENU_CATEGORY_DISPLAY; break;
                                           case 'MY BOOTH': $menu_item = MENU_MYBOOTH; break;
                                           case 'LOGOUT': $menu_item = MENU_LOGOUT; 
                                                          
                                                           break;
                                           default: $menu_item = 'NULL'; break;
                                       }
                                
                                       echo '<li><a href="' . mysqli_result($loggedlinks, $i, 'vCategoryFile') . '">' . Highligt($PG_TITLE, mysqli_result($loggedlinks, $i, 'vCategoryFile'), $menu_item, $selctdMenuColor) . '</a></li>';
                                $escapeFlag=0;
                                
                                   }//end for loop
                               }//end if
                             }//end if*/

                             ?>
                        <!-- <li class="more_main_btt">         
                           <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                              More...
                              <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
                                                                                 
                              <li><a href="#">Menu 1</a></li>
                              <li><a href="#">Menu 2</a></li>
                              <li><a href="#">Menu 3</a></li>
                              <li><a href="#">Menu 4</a></li>
                            </ul>
                           </div>  
                         </li>  -->
                       </ul>
                     </div>
                     <!-- /.navbar-collapse -->
                     <!-- /.container -->
                     <div class="clear"></div>
                   </nav>
                 </div>
               </div>
             </header>
             <?php
             if(basename($_SERVER['PHP_SELF']) == "index.php" )
             {
              $hintro_img = DisplayLookUp('welcomeImage');
          /*$hintro_img1 = "images/".$hintro_img;
          if(@file_exists($hintro_img1))
          {
              $welcomeImage    =  SITE_URL."/".$hintro_img1;
          }
          else{
               $welcomeImage    =  SITE_URL."/images/slider/sliderimg1.jpg";
             }*/
             $welcomeImage    =  SITE_URL."/images/slider/sliderimg1.jpg";

             $home_text = ContentLookUp('homebannertexts');
             $home_banner_heading = $home_text['content_title'];
             $home_banner_text = $home_text['content'];

             ?>
             <?php
             $sql = "select vImg,vlocUrl from ".TABLEPREFIX."sliders where vActive = '1' ";
             $result1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
             $result2 = mysqli_query($conn, $sql) or die(mysqli_error($conn));

             ?>
             <!-- Homepage-slider -->
 <div class="home-slider-section">
  <div class="home-slider" id="homeSlider">
   
       <?php $i=0;
            if(mysqli_num_rows($result2)>0){
              while($query_data2 = mysqli_fetch_array($result2))
              { 
                $url = '#';
                if($query_data2['vlocUrl']!="")
                {
                  $url = $query_data2['vlocUrl'];
                }
            ?>
         <div class="item ">
            <img src="sliders/<?php echo $query_data2['vImg'];?>" alt="...">
            <div class="container">
               <div class="slider-caption">
                  <h2><?php echo $home_banner_heading;?></h2>
                  <h1><?php echo $home_banner_text;?></h1>
                  <a href="<?php echo $url;?>" class="shopnow-btn">shop now</a> 
               </div>
            </div>
         </div>
         <?php $i++; }}else{ ?> 
         <div class="item active">
            <img src="<?php echo $welcomeImage;?>" alt="...">
            <div class="container">
               <div class="slider-caption">
                  <h2><?php echo $home_banner_heading;?></h2>
                  <h1><?php echo  strip_tags($home_banner_text);?></h1>
               </div>
            </div>
         </div>
         <?php } ?>

  </div>
</div>  



<!-- <div class="home-slider-section">
  <div class="home-slider" id="homeSlider">

        <div class="item ">
          <img src="temp-images/slider-img.jpg" alt="...">
          <div class="container">
           <div class="slider-caption">
            <h2>All In One Best Place for</h2>
            <h1>Smart Products</h1>
            <a href="salelistdetailed.php" class="shopnow-btn">shop now</a> 
          </div>
        </div>
      </div>
      <div class="item ">
        <img src="temp-images/slider-img.jpg" alt="...">
        <div class="container">
         <div class="slider-caption">
          <h2>All In One Best Place for</h2>
          <h1>Smart Products</h1>
          <a href="<?php echo $url;?>/salelistdetailed.php" class="shopnow-btn">shop now</a> 
        </div>
      </div>
    </div>


  </div>
</div> -->


<?php }  // Display BAnner on Home page only?> 






<!-- inner page header -->
<?php  if(basename($_SERVER['PHP_SELF']) != "index.php" ) { ?>
<!-- <div class="row inner_page_banner">
   <div class="container">
      <div class="row">
         <div class="col-lg-12">
            <div class="search_container_inner">
               <form name="frmHSearch" class="home_search" method="get" action="search.php" onSubmit="return ValidSearch();">
                  <div class="form-group">
                     <input name="txtHomeSearch" type="text" class="form-control" value="<?php echo stripslashes(htmlentities($txtHomeSearch)); ?>" onFocus="javascript:Clear_text(this,'in');" onBlur="javascript:Clear_text(this,'out');">
                  </div>
                  <button type="submit" value="<?php echo BUTTON_SEARCH; ?>"  height="21" class="innersearch btn btn-default btn-new">
                  <?php echo SEARCH_TEXT;?>
                  </button>
               </form>
            </div>
         </div>
      </div>
   </div>
   <div class="clear"></div>
 </div> -->
 <div style="height: 20px;" class="height-40"></div>
 <?php } ?>
 <!--inner page header-->
