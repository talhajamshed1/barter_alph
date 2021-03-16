<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
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
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

//get data from post
$saleid = $_GET["saleid"];
$amt = $_GET["amt"];

//checking point enable in website
if (ENABLE_POINT != '0' && $_GET['ptype'] == 'rp') {
    $pointValue = round(($amt / DisplayLookUp('PointValue')) * DisplayLookUp('PointValue2'), 2);
    $showPrice = '&nbsp;&nbsp;(' . $pointValue . '&nbsp;' . POINT_NAME . ')';
}//end if

$sql = "Select * from " . TABLEPREFIX . "sale where nSaleId='$saleid'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    if ($row = mysqli_fetch_array($result)) {
        $Title = $row["vTitle"];
        $PostDate = $row["dPostDate"];
    }//end if
}//end if

switch ($_GET['ptype']){
    case 'rp': $payment_type = str_replace('{point_name}',POINT_NAME,TEXT_REDEEM_POINTS); break;
    case 'gc': $payment_type = TEXT_GOOGLE_CHECKOUT; break;
    default: $payment_type = TEXT_CREDIT_CARD; break;
}

    
include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function proceed(cc)
    {
        document.frmBuy.cctype.value=cc;
        //alert(document.frmBuy.cctype.value);
        frmBuy.submit();
    }//end funciton
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
<?php require_once("./includes/header.php"); ?>
<div class="homepage_contentsec">
<div class="container">
	<div class="row">
		<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
		<div class="col-lg-9">					
			<div class="innersubheader">
				
			</div>
			
			<div class="row">
				<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
				<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">					<h4><?php echo HEADING_PAYMENT_STATUS; ?></h4>
					<div class="row alert alert-success text-center">
						<?php echo MESSAGE_THANKYOU_FOR_PAYMENT; ?><br />
						<?php echo MESSAGE_PAYMENT_DETAILS_FOLLOWS; ?>
					</div>
					<!-- <h3 class="subheader row">HEAD</h3> -->
					<div class="row main_form_inner">
						<label><?php echo TEXT_TITLE; ?></label>
						<?php echo  $Title ?>
					</div>
					<div class="row main_form_inner">
						<label><?php echo TEXT_POSTED_ON.' '.TEXT_MM_DD_YYYY; ?></label>
						<?php echo  change_date_format($PostDate,'mysql-to-mmddyy') ?>
					</div>
					<div class="row main_form_inner">
						<label><?php echo TEXT_PAYMENT_MODE; ?></label>
						<?php echo $payment_type; ?>
					</div>
					
					<?php if ($amt!='' && $amt!=0) { ?>
						<div class="row main_form_inner">
							<label><?php echo TEXT_AMOUNT; ?></label>
							<?php echo CURRENCY_CODE; ?><?php echo  $amt ?><?php echo $showPrice; ?>
						</div>
					<?php } ?>
				</div>
				<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>	
				
				<div class="clear"></div>					
				
				<div class="col-lg-12 col-sm-12 col-md-12">
					<div class="subbanner">
						<?php include('./includes/sub_banners.php'); ?>
					</div>
				</div>
				
			</div>					
		</div>
	</div>  
</div>
</div>

<?php require_once("./includes/footer.php"); ?>