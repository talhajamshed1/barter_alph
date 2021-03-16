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
include ("./includes/session_check.php");
include_once('./includes/httprefer_check.php');
include_once('./includes/gpc_map.php');

if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else

$id = $_SESSION["gsaleextraid"];
$var_method = ($_GET["paytype"] != "") ? addslashes($_GET["paytype"]) : addslashes($_POST["paytype"]);

//get amounts
$sql = "Select vTitle,nFeatured,nCommission  from " . TABLEPREFIX . "saleextra where nSaleextraId='$id'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$total = 0;
if (mysqli_num_rows($result) > 0) {
    if ($row = mysqli_fetch_array($result)) {
        $nFeatured = $row["nFeatured"];
        $nCommission = $row["nCommission"];
        $vTitle = $row["vTitle"];
        $total = ($nFeatured + $nCommission);
    }//end if
}//end if
//get method
$disp_method = get_payment_name($var_method);

//if post back
if ($_POST["postback"] == "Y") {
    $Name = $_POST["txtName"];
    $Bank = $_POST["txtBank"];
    $RefNo = $_POST["txtrefno"];

    $Month = $_POST["txtMM"];
    $Year = $_POST["txtYY"];
    $Date = $_POST["txtMM"] . "/" . $_POST["txtDD"] . "/" . $_POST["txtYY"];
    $mysql_date = $_POST["txtYY"] . '-' . $_POST["txtMM"] . '-' . $_POST["txtDD"];

//Start of the transaction  for adding a user since payment is successfull
//check if saleid alredy there to prevent refresh
    if ($total != 0) {
        $sql = "UPDATE " . TABLEPREFIX . "saleextra SET vReferenceNo = '" . addslashes($RefNo) . "', `vName` = '" . addslashes($Name) . "', `vBank` = '" . addslashes($Bank) . "', `dReferenceDate` = '" . addslashes($mysql_date) . "',vMode ='$var_method' WHERE `nSaleextraId` = '$id'";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
    }//end if

    $_SESSION["gsaleextraid"] = "";
    $postback = "true";
}//end if

include_once('./includes/title.php');
?>

<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
	
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">					
				
				<div class="clearfix row">
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<h4><?php echo HEADING_PAYMENT_FORM; ?></h4>
						<form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
						
							<input type="hidden" name="postback" id="postback" value="">
							<input type="hidden" name="amnt" id="amnt" value="<?php echo  $total ?>">
							<input type="hidden" name="saleid" id="saleid" value="<?php echo  $id ?>">
							<input type="hidden" name="paytype" id="paytype" value="<?php echo  $var_method ?>">
							<?php
							if ($cc_flag == false && $cc_err != '') {
								?>
								<div class="row warning"><?php echo $cc_err; ?></div>
								<?php
							}//end if
							?>
							
							<h3 class="subheader row"><?php echo HEADING_ITEM_DETAILS; ?></h3>
							
							<div class="col-lg-6 col-sm-12 col-md-6 col-xs-12 no_padding">
								<div class="row main_form_inner">
									<label><b><?php echo TEXT_DESCRIPTION; ?></b></label>
									<?php echo TEXT_SALE_ITEM_ADDITION; ?>
								</div>
							</div>
							
							<div class="col-lg-6 col-sm-12 col-md-6 col-xs-12 no_padding">
								<div class="row main_form_inner">
									<label><b><?php echo TEXT_TITLE; ?></b></label>
									<?php echo  $vTitle ?>
								</div>
							</div>
						
							<div class="row main_form_inner">
								<label><?php echo TEXT_FEATURED; ?></label>
								<?php echo CURRENCY_CODE; ?><?php echo  $nFeatured ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_COMMISSION; ?></label>
								<?php echo CURRENCY_CODE; ?><?php echo  $nCommission ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_TOTAL_AMOUNT; ?></label>
								<?php echo CURRENCY_CODE; ?><?php echo  $total ?>
							</div>
							
							<?php
							if ($postback != "true") {
							?>
							<?php
							if (isset($var_method) && $var_method == 'wt') {
								?>
							
							<div class="row warning"><?php echo "<br>".str_replace('{site_name}',SITE_NAME,str_replace('{email_link}',SITE_EMAIL,CONTACT_ADMIN_GET_ACCOUNT_NUMBER))."<br>"; ?></div>
							<?php }//end if?>

							<h3 class="subheader row"><?php echo TEXT_DETAILS; ?></h3>
								
							<div class="row main_form_inner">
								<label><?php echo TEXT_NAME; ?> <span class="warning">*</span></label>
								<input type="text" name="txtName" id="txtName" value="" size="24" maxlength="40" class="form-control">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_BANK; ?>(<?php echo TEXT_IF_APPLICABLE; ?>)</label>
								<input type="text" name="txtBank" id="txtBank" value="" size="24" maxlength="40" class="form-control">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_REFERENCE_NUMBER; ?> <span class="warning">*</span></label>
								<input type="text" name="txtrefno" class="form-control" id="txtrefno" size="24" maxlength="16">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_PAYMENT_MODE; ?></label>
								<input type="text" name="txtpaymode" class="form-control" id="txtpaymode" size="15" maxlength="20" value="<?php echo  $disp_method ?>" readonly>
							</div>
							
							
							
							
							<div class="row main_form_inner">
								<label><?php echo TEXT_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>) <span class="warning">*</span></label>
								<div style="padding-right: 10px;" class="col-lg-4 col-sm-4 col-md-4 col-xs-4 no_padding">
									<input type="text" name="txtMM" class="form-control" id="txtMM" size="3" maxlength="2">
								</div>
								<div style="padding-right: 10px;" class="col-lg-4 col-sm-4 col-md-4 col-xs-4 no_padding">
									<input type="text" name="txtDD" class="form-control" id="txtDD" size="3" maxlength="2">
								</div>
								<div class="col-lg-4 col-sm-4 col-md-4 col-xs-4 no_padding">
									<input type="text" name="txtYY" class="form-control" id="txtYY" size="4" maxlength="4">
								</div>
							</div>
							
							<div class="row main_form_inner">
								<label>&nbsp;</label>
								<input type="button" name="btConfirm" id="btConfirm" class="subm_btt"  value="<?php echo BUTTON_CONFIRM; ?>" onClick="javascript:clickConfirm();">
							</div>
							
							
							<?php
							}//endif
							else {
							?>
							
							<div class="row success"><?php echo MESSAGE_THANKYOU_FOR_PAYMENT; ?><br><?php echo MESSAGE_PAYMENT_DETAILS_FOLLOWS; ?></div>
								
							<div class="row main_form_inner">
								<label><?php echo TEXT_NAME; ?></label>
								<?php echo  $Name ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_BANK; ?>(<?php echo TEXT_IF_APPLICABLE; ?>)</label>
								<?php echo  $Bank ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_REFERENCE_NUMBER; ?></label>
								<?php echo  $RefNo ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_PAYMENT_MODE; ?></label>
								<?php echo  $disp_method ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>)</label>
								<?php echo  $Date ?>
							</div>
							<div class="row warning"><?php echo MESSAGE_ITEM_AVAILABLE_AFTER_ADMIN_VERIFIES_PAYMENT; ?></div>
							<?php }//end if ?>
							
							
						</form>					
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
<script language="javascript" type="text/javascript">
    function clickConfirm()
    {
        if(document.frmBuy.txtName.value.length <= 0 ||  document.frmBuy.txtrefno.value.length <= 0 || document.frmBuy.txtMM.value.length <= 0 || parseInt(document.frmBuy.txtMM.value) > 12 || document.frmBuy.txtDD.value.length <= 0 || parseInt(document.frmBuy.txtDD.value) > 31 || document.frmBuy.txtYY.value.length <= 0)
        {
            alert("<?php echo ERROR_GIVEN_INFO_EMPTY_INVALID; ?>");
        }else if(document.frmBuy.txtName.value.length <= 0){
            
        }//end if
        else
        {
            document.frmBuy.postback.value='Y';
            document.frmBuy.method='post';
            document.frmBuy.submit();
        }//end else
    }//end funciton

    function checkValue(t)
    {
        if(t.value.length == 0)
        {
            if(t.name == "txtccno")
            {
                t.value="";
            }//end if
            else
            {
                t.value="000";
            }//end else
        }//end if
    }//end function
</script>