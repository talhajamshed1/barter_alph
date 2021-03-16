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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
$message = "";
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

include_once('./includes/title.php');

$txtSearch = "";
$cmbSearchType = "";

// $present_time = time();
// mysqli_query($conn, "DELETE FROM " . TABLEPREFIX . "online WHERE nActiveTill < '" . $present_time . "'") ;

$sql = "SELECT u.nUserId, u.vEmail,u.vStatus,u.vGender,u.profile_image,u.vLoginName,n.nLoggedOn  FROM " . TABLEPREFIX . "users u 
LEFT JOIN " . TABLEPREFIX . "online n ON (u.nUserId = n.nUserId) WHERE
n.vVisible='Y' and u.nUserId!='" . $_SESSION["guserid"] . "'";

$sess_back = $targetfile . "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;
//get the total amount of rows returned
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));
/*
  Call the function:

  I've used the global $_GET array as an example for people
  running php with register_globals turned 'off' :)
 */
  $navigate = pageBrowser($totalrows, 10, 10, "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function

  $sql=dopaging($sql,'',12);
//$result=mysqli_query($conn, $sql_qr) or die(mysqli_error($conn));
//echo $sql = $sql . $navigate[0];
  $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
  $numRecords = mysqli_num_rows($rs);

//Pagination with Bootstrap Pagination Component
  if($numRecords>0) {
    
    $pagenumber     =   getCurrentPageNum();
    $defaultUrl     =   $_SERVER['PHP_SELF'];
    $querysting     =   "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&";
    $paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]";
    $pageString     =   getnavigation($totalrows,12);
    include_once("lib/pager/pagination.php"); 
    $pg = new bootPagination($pagenumber,12,$totalrows,$defaultUrl,$paginationUrl);

  }
  ?>
  <body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
    <div class="homepage_contentsec">
    	<div class="container">
       <div class="row">
         <div class="col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
         <div class="col-lg-9">
          <div class="row " >
           <div class="innersubheader">
             <div class="col-lg-12 ">
               <h3><?php echo HEADING_ONLINE_MEMBERS; ?></h3>
             </div>
           </div>
         </div>
         <!-- ------------------------plain HTML-------------------->
         <?php if($numRecords>0) 
         { ?>    
         <div class="row" style="min-height: 300px;">
          
           <?php
           while ($arr = mysqli_fetch_array($rs)) {
             
             $profile_image = $arr["profile_image"];
             if ($profile_image=='' || !file_exists('./pics/profile/'.$profile_image)) {
              $assignedname = '';
              if ($arr["vGender"]=='F')
                $profile_image = './images/nophoto_available_women.gif';
              else
                $profile_image = './images/nophoto_available_men.gif';
            }
            else {
              $assignedname = $profile_image;
              $profile_image = './pics/profile/'.$profile_image;
            }
            
            ?>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
             <div class="online_member_list">
               <div class="member_img">
                <img src="<?php echo $profile_image; ?>" alt="" style="width: inherit;height: inherit;border-radius: inherit;">
              </div>
              <div class="member_details">
               <h2><?php echo ucfirst($arr['vLoginName']); ?> </h2>
               <a href="javascript:WindowPop('<?php echo $arr['nUserId']; ?>');"><?php echo LINK_CHAT_WITH; ?> <?php echo ucfirst($arr['vLoginName']); ?> </a>
             </div>
             <div class="clear"></div>
           </div>
         </div>              
         
         
         <?php 
         
       } ?>
     </div>
     <div class="pagination_wrapper">  
      <div class="left">
       <?php echo str_replace('{total_rows}',$totalrows,str_replace('{current_rows}',$pageString,TEXT_LISTING_RESULTS)); ?>
     </div>
     <div class="right">
      <?php
							//Pagination code
      echo $pg->process();
      ?>
    </div>
    <div class="clear"></div>
  </div>   
  <?php      
} else { ?>

<div class="no-product-items">
  <div class="msg">
    <img src="http://localhost/eswap/images/no-product-img.gif">
    <span>-  <?php echo NO_ONLINE_MEMBERS_FOUND;?> -</span>
  </div>
</div>

<?php } ?>

<div class="subbanner">
  <?php include('./includes/sub_banners.php'); ?>
</div>					                      
</div>
</div>
</div>  
</div>   
<?php require_once("./includes/footer.php"); ?>