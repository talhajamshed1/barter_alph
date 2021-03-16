<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
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
include "./includes/config.php";
include "./includes/session.php";
include "./includes/functions.php";
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
include "./includes/logincheck.php";

include_once('./includes/gpc_map.php');
$message = "";
include_once('./includes/title.php');

//checking sale module status
$sqlSale = mysqli_query($conn, "SELECT vActive FROM " . TABLEPREFIX . "client_module_category where nCategoryId='2' and vActive='1'") or die(mysqli_error($conn));
switch (mysqli_num_rows($sqlSale)) {
  case "0":
  header('location:index.php');
  exit();
  break;
}//end switch

$message = ($_SESSION['succ_msg'] != '') ? $_SESSION['succ_msg'] : '';
$itemListId = ($_SESSION['succ_msg_msg'] != '') ? $_SESSION['succ_msg_msg'] : '';

?>
<body onLoad="timersOne();">
  <?php include_once('./includes/top_header.php'); ?>

  <div class="homepage_contentsec">
   <div class="container">
     <div class="row">
       <div class="col-md-4 col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
       <div class="col-md-8 col-lg-9">
					<!--<div class="error_msg_outer">
						<span class="glyphicon glyphicon-remove-circle"></span> Email already exist 
					</div>
					<div class="success_msg_outer">
						<span class="glyphicon glyphicon-ok"></span>Registration success
					</div>-->
          <?php
          if (isset($message) && $message != '' && !empty($_SESSION['succ_msg_msg'])) {
            ?>

            <div class="success_msg">
              <span class="glyphicon glyphicon-ok"></span>
              <p class="msg"><?php echo $message; ?></p>
              <div class="clear"></div>
            </div>


            <?php
            unset($_SESSION['succ_msg']);
					}//end if
					?>


					<div>
           <?php
           include_once("./includes/saledetailed.php");
						//echo $page_name;
           $_REQUEST['cmbItemType'] = 'sale';
           $fid = $_REQUEST['no'];
			     // un-comment-this
           include_once("./includes/productspercat.php");
			     // =====


						/*if ($_GET["rf"] == "sid") {
							func_sale_detailed(0, $_GET["no"]);
						}//end if
						else {
							func_sale_detailed();
						}//end else
						 */
						?>

           <div class="subbanner">
             <?php
						  // include('./includes/sub_banners.php'); 
             ?> 
           </div>
         </div>
       </div>
     </div>
   </div>

   <!-- send message modal -->
   <!-- Modal -->
   <div id="sendMessagemodal" class="modal sendMessagemodal" role="dialog">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-body">
         <h4>Send Message</h4>
         <form>
          <div class="frm-ctrl">
           <input type="text" placeholder="Subject" required="">
         </div>
         <div class="frm-ctrl">
           <textarea  placeholder="Message" required=""></textarea>
         </div>
         <div class="frm-ctrl-btn">
           <button class="sendMessagemodal-btn" type="submit">Send Message</button>
         </div>
       </form>

       
     </div>
     
   </div>

 </div>
</div>


</div>



<?php require_once("./includes/footer.php"); ?>