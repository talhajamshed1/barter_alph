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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/session_check.php");

if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else

if ($_GET["saleid"] != "") {
    $amnt = $_GET["amnt"];
    $saleid = $_GET["saleid"];
    //$userid=$_GET["userid"];
    $now = $_GET["dt"];
}//end if
else if ($_POST["saleid"] != "") {
    $saleid = $_POST["saleid"];
    $now = urldecode($_POST["dt"]);
    $cost = $_POST["amnt"];
    $amnt = $cost;
}//end else

$userid = $_SESSION["guserid"];
$cc_err = "";
$cc_flag = false;
$var_insert_flag = false;

$var_sale_flag = false;
$var_rej_flag = false;
$sql = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected,sd.vMethod from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
$sql .= " on sd.nSaleId = s.nSaleId ";
$sql .= " where  sd.nSaleId='" . addslashes($saleid) . "' AND sd.nUserId='" . $_SESSION["guserid"] . "' AND sd.dDate='";
$sql .= addslashes($now) . "' ";


$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    if ($row = mysqli_fetch_array($result)) {
        $cost = $row["nAmount"];
        $amnt = $cost;
        $var_title = $row["vTitle"];
        if (strlen($var_title) > 30) {
                $var_title = substr($var_title, 0, 30).'...';
        }
        $var_method = $row["vMethod"];
        $reqd = $row["nQuantity"];

        //checking escrow status
        if (DisplayLookUp('Enable Escrow') == 'Yes') {
            $SaleStatus = '1';
        }//end if
        else {
            $SaleStatus = "4";
        }//end esle

        if ($row["vSaleStatus"] == $SaleStatus) {
            $var_sale_flag = true;
        }//end if
        if ($row["vRejected"] == "0") {
            $var_rej_flag = true;
        }//end if
    }//end if
}//end if
else {
    $cc_err = ERROR_CHECK_YOUR_INPUT;
}//end else



$sql = "Select nSaleInterId,vName,vBank,vReferenceNo,date_format(dReferenceDate,'%m/%d/%Y') as 'dReferenceDate',date_format(dEntryDate,'%m/%d/%Y  %H:%i') as 'dEntryDate',vMethod from " . TABLEPREFIX . "saleinter where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
$sql .= addslashes($now) . "' AND vDelStatus='0' ";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
    $var_insert_flag = false;
    $disp_name = $row["vName"];
    $disp_bank = $row["vBank"];
    $disp_refno = $row["vReferenceNo"];
    $var_method = $row["vMethod"];
    $disp_refdate = $row["dReferenceDate"];
    $disp_entrydate = $row["dEntryDate"];
}//end if
else {
    $var_insert_flag = true;
}//end else

$disp_method = get_payment_name($var_method);

if ($_POST["postback"] == "Y") {
    $Name = $_POST["txtName"];
    $Bank = $_POST["txtBank"];
    $Reference = $_POST["txtrefno"];
    $Date = $_POST["txtYY"] . "/" . $_POST["txtMM"] . "/" . $_POST["txtDD"];

    if ($var_sale_flag == true && $var_rej_flag == true) {
        if ($var_insert_flag == true) {
            $sql = "insert into " . TABLEPREFIX . "saleinter(nSaleInterId,nSaleId,nUserId,dDate,vMethod,vName,vBank,vReferenceNo,dReferenceDate,dEntryDate)";
            $sql .= " values('','" . addslashes($saleid) . "','" . $_SESSION["guserid"] . "',";
            $sql .= "'" . addslashes($now) . "','" . addslashes($var_method) . "','" . addslashes($Name) . "',";
            $sql .= "'" . addslashes($Bank) . "','" . addslashes($Reference) . "',";
            $sql .="'" . addslashes($Date) . "',now())";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
			
			
			
			// quantity updating section
			$sql_qty = "Select nQuantity from " . TABLEPREFIX . "saledetails where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . addslashes($_SESSION["guserid"]);
			$sql_qty .= "' AND dDate='" . addslashes($now) . "' AND  vSaleStatus IN('2','4')";

			$result = mysqli_query($conn, $sql_qty) or die(mysqli_error($conn));
			if (mysqli_num_rows($result) > 0) {
				$row_det = mysqli_fetch_array($result);
				$quantityREQD = $row_det['nQuantity'];
				if($quantityREQD!='') {
					$sql = "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity - $quantityREQD where nSaleId ='" . addslashes($saleid) . "'";
					mysqli_query($conn, $sql) or die(mysqli_error($conn));
				}
			}
			// quantity updating section ends			
			
			
			
			
			
            header('location:buyotcon.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=' . $cost . '&flag=true&');
            exit();
        }//end if
        else {
            header('location:buyotcon.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=' . $cost . '&flag=false&');
            exit();
        }//end else
    }//end if
    else {
        $cc_flag = false;
        if (strlen($cc_err) <= 0) {
            if ($var_sale_flag == false) {
                $cc_err = MESSAGE_PAYMENT_MADE_FOR_ITEM;
            }//end if
            if ($var_rej_flag == false) {
                $cc_err = MESSAGE_SALE_REJECTED_BY_OWNER;
            }//end if
        }//end if
    }//end else
}//end if

include_once('./includes/gpc_map.php');

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function clickConfirm()
    {
        if(document.frmBuy.txtName.value.length <= 0 ||  document.frmBuy.txtrefno.value.length <= 0 || document.frmBuy.txtMM.value.length <= 0 || parseInt(document.frmBuy.txtMM.value) > 12 || document.frmBuy.txtDD.value.length <= 0 || parseInt(document.frmBuy.txtDD.value) > 31 || document.frmBuy.txtYY.value.length <= 0)
        {
            alert('<?php echo ERROR_GIVEN_INFO_EMPTY_INVALID; ?>');
        }//end if
        else
        {
            document.frmBuy.postback.value='Y';
            document.frmBuy.method='post';
            document.frmBuy.submit();
        }//end else
    }//end function

    function checkValue(t)
    {
        if(isNaN(t.value) || t.value.substring(0,1) == " " || t.value.length == 0 || parseInt(t.value) <= 0 )
        {
            if(t.name == 'txtYY')
            {
                t.value = '2004';
            }//end if
            else
            {
                t.value = '1';
            }//end else
        }//end if
    }//end function
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
<div class="container">
	<div class="row">
		<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
		<div class="col-lg-9">					
			<div class="innersubheader">
				<h4><?php echo HEADING_PAYMENT_FORM; ?></h4>
			</div>
			
			<div class="clearfix">
				
					<?php if ($_SESSION["guserid"] == "") {
							include_once("./login_box.php");
						} ?>
				<div class="col-lg-12 profile-section-bottom">
				
										
					<form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
					<?php
					if (isset($cc_flag) && $cc_flag == false) {
						
						?>
						<?php if($cc_err!=''){ ?>
							<div class="alert alert-danger"><?php echo $cc_err; ?></div>
						<?php } ?>
						<?php
						}//end if
						if (isset($var_method) && $var_method == "wt") {
							?>
							<div class="alert alert-danger"><?php echo 'Please contact administrator at <a href="mailto:' . SITE_EMAIL . '">' . SITE_EMAIL . '</a> to get the ' . SITE_NAME . ' account number for bank wire transfer'; ?></div>
						<?php }//end if?>

						<h4 ><?php echo HEADING_PURCHASE_DETAILS; ?></h4>
						<div class="col-lg-6 col-sm-12 col-md-6 col-xs-12 no_padding">
							<div class=" custom-text-div">
								<label><?php echo TEXT_TITLE; ?></label>
								<div><?php echo  htmlentities($var_title) ?></div>
							</div>
						</div>
						<div class="col-lg-6 col-sm-12 col-md-6 col-xs-12 no_padding">
							<div class=" custom-text-div">
								<label><?php echo TEXT_AMOUNT; ?></label>
								<div><?php echo CURRENCY_CODE; ?><?php echo  $cost ?></div>
							</div>
						</div>
						<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 no_padding">
							<div class="custom-text-div">
								<label><?php echo TEXT_QUANTITY; ?></label>
								<div><?php echo  $reqd ?></div>
							</div>
						</div>
					
						<?php
						if ($var_insert_flag == true) {
							?>
							<input type="hidden" name="postback" id="postback" value="">
							<input type="hidden" name="amnt" id="amnt" value="<?php echo  $amnt ?>">
							<input type="hidden" name="saleid" id="saleid" value="<?php echo  $saleid ?>">
							<input type="hidden" name="userid" id="userid" value="<?php echo  $userid ?>">
							<input type="hidden" name="dt" id="dt" value="<?php echo  urlencode($now) ?>">
							<div class="clearfix main_form_inner"><br>

								<h4><?php echo HEADING_PAYMENT_DETAILS; ?></h4>
							</div>
							<div class=" main_form_inner">
								<label><?php echo TEXT_NAME; ?> <span class="warning">*</span></label>
								<input type="text" name="txtName" id="txtName" value="" size="24" maxlength="40" class="form-control">
							</div>
							<div class=" main_form_inner">
								<label><?php echo TEXT_BANK; ?> (<?php echo TEXT_IF_APPLICABLE; ?>)</label>
								<input type="text" name="txtBank" id="txtBank" value="" size="24" maxlength="40" class="form-control">
							</div>
							<div class=" main_form_inner">
								<label><?php echo TEXT_REFERENCE_NUMBER; ?> <span class="warning">*</span></label>
								<input type=text name="txtrefno" class="form-control" id="txtrefno" size="24" maxlength="16">
							</div>
							<div class=" main_form_inner">
								<label><?php echo TEXT_PAYMENT_MODE; ?></label>
								<input type=text name="txtMode" class="form-control" id="txtMode" size=16 maxlength="40" value="<?php echo  $disp_method ?>" readonly>
							</div>							
							<div class=" main_form_inner">
								<label><?php echo TEXT_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>) <span class="warning">*</span></label>
								<div class="col-lg-4 col-sm-4 col-md-4 col-xs-4 no_padding" style="padding-right: 10px;">
									<input type=text name="txtMM" class="form-control" id="txtMM" size="3" maxlength="2" onBlur="javascript:checkValue(this);" value="1">
								</div>
								<div class="col-lg-4 col-sm-4 col-md-4 col-xs-4 no_padding" style="padding-right: 10px;">
									<input type=text name="txtDD" class="form-control" id="txtDD" size="3" maxlength="2" onBlur="javascript:checkValue(this);" value="1">
								</div>
								<div class="col-lg-4 col-sm-4 col-md-4 col-xs-4 no_padding">
									<input type=text name="txtYY" class="form-control" id="txtYY" size="4" maxlength="4" onBlur="javascript:checkValue(this);" value="2009">
								</div>
							</div>
							
							
							
							<div class="clearfix main_form_inner">
								<br>
								<input type="button" name="btPay" id="btPay" class="subm_btt"  value="<?php echo BUTTON_PAY_NOW; ?>" onClick="javascript:clickConfirm();">
							</div>
								<?php
							}//end if
							else {
								?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_NAME; ?></label>
								<?php echo  $disp_name ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_BANK; ?> (<?php echo TEXT_IF_APPLICABLE; ?>)</label>
								<?php echo  $disp_bank ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_REFERENCE_NUMBER; ?></label>
								<?php echo  $disp_refno ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_PAYMENT_MODE; ?></label>
								<?php echo  $disp_method ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_REFERENCE_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>)</label>
								<?php echo  $disp_refdate ?>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_ENTRY_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>)</label>
								<?php echo  $disp_entrydate ?>
							</div>
						<?php }//end else ?>
					</form>					
				</div>
				
			
				
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