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
include_once('./includes/gpc_map.php');

if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else

if ($_GET["tmpid"] != "") {
    $var_tmpid = $_GET["tmpid"];
}//end if
else if ($_POST["tmpid"] != "") {
    $var_tmpid = $_POST["tmpid"];
}//end else if

$userid = $_SESSION["guserid"];

$cc_err = "";
$cc_flag = false;
$dispflag = false;

$sql = "Select s.vTitle,st.nSwapId,st.nUserId,st.vMethod,st.vMode,st.dDate,st.nAmount,st.vPostType from " . TABLEPREFIX . "swaptemp st inner join ";
$sql .= " " . TABLEPREFIX . "swap s on st.nSwapId = s.nSwapId where st.nTempId='" . addslashes($var_tmpid) . "' ";

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    if ($row = mysqli_fetch_array($result)) {
        $var_title = $row["vTitle"];
        $cost = $row["nAmount"];
        $var_posttype = $row["vPostType"];
        $var_swapid = $row["nSwapId"];
        $var_userid = $row["nUserId"];
        $var_method = $row["vMethod"];
        $var_mode = $row["vMode"];
        $var_date = $row["dDate"];
        $disp_method = "";
        $disp_method = get_payment_name($var_method);

        $cc_flag = true;
    }//end if
}//end if
else {
    $cc_err = ERROR_CHECK_YOUR_INPUT;
}//end else


if (isset($_POST["postback"]) && $_POST["postback"] == "Y") {
    if ($cc_flag == true) {  //begin - IF (I)
        $Name = $_POST["txtName"];
        $Bank = $_POST["txtBank"];
        $Reference = $_POST["txtrefno"];
        $Date = $_POST["txtYY"] . "/" . $_POST["txtMM"] . "/" . $_POST["txtDD"];

        $sql = "Delete from " . TABLEPREFIX . "swaptemp where nTempId = '" . addslashes($var_tmpid) . "'";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        $sql = "Insert into " . TABLEPREFIX . "swapinter(nSwapInterId,nSwapId,nUserId,nAmount,vMethod,vMode,vPostType,dDate,nEntryUser,";
        $sql .= "dEntryDate,vName,vBank,vReferenceNo,dReferenceDate) Values('','$var_swapid','$var_userid',";
        $sql .= "'$cost','$var_method','$var_mode','$var_posttype','$var_date','$userid',now(),'";
        $sql .= addslashes($Name) . "','" . addslashes($Bank) . "','" . addslashes($Reference) . "','" . addslashes($Date) . "')";

        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        $dispflag = true;
    } //End IF (I)
}//end if

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
        if(isNaN(t.value) || t.value.substring(0,1)==" " || t.value.length==0 || parseInt(t.value) <= 0 )
        {
            if(t.name=='txtYY')
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
					
					<div class="row">
						<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
						<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="postback" id="postback" value="">
							<input type="hidden" name="tmpid" id="tmpid" value="<?php echo  $var_tmpid ?>">
							<?php
							if ($cc_flag == false && $cc_err != '') {
								?>
								<div class="row warning"><?php echo $cc_err; ?></div>
								<?php
							}//end if
							if (isset($var_method) && $var_method == 'wt') {
								?>
								<div class="row warning">
									<?php echo str_replace('{email_link}',"<a href=\"mailto:" . SITE_EMAIL . "\">" . SITE_EMAIL . "</a>",str_replace('{site_name}',SITE_NAME,CONTACT_ADMIN_GET_ACCOUNT_NUMBER)); ?>
								</div>
							<?php }//end if ?>
								
								<h3 class="subheader row"><?php echo TEXT_DETAILS; ?></h3>
								
								<div class="row main_form_inner">
									<label><?php echo TEXT_TITLE; ?></label>
									<?php echo  htmlentities($var_title) ?>
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_AMOUNT; ?></label>
									<?php echo CURRENCY_CODE; ?> <?php echo  $cost ?>
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_TYPE; ?></label>
									<?php echo  $var_posttype ?>
								</div>
								<?php
								if ($dispflag == true) {
									echo '
									
									<div class="row main_form_inner">
										<div class="success">'.MESSAGE_PAYMENT_SUBMITTED_FOR_CLEARANCE.'</div>
									</div>';
								}//end if
								else {
									?>
									
								<h3 class="subheader row"><?php echo TEXT_PAYMENT_DETAILS; ?></h3>

								<div class="row main_form_inner">
									<label><?php echo TEXT_NAME; ?> <span class="warning">*</span></label>
									<input type="text" name="txtName" id="txtName" value="" size="24" maxlength="40" class="form-control">
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_BANK; ?> (<?php echo TEXT_IF_APPLICABLE; ?>)</label>
									<input type="text" name="txtBank" id="txtBank" value="" size="24" maxlength="40" class="form-control">
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_REFERENCE_NUMBER; ?> <span class="warning">*</span></label>
									<input type="text" name="txtrefno" class="form-control" id="txtrefno" size="24" maxlength="16">
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_PAYMENT_MODE; ?></label>
									<input type="text" name="txtMode" class="form-control" id="txtMode" size=16 maxlength="40" value="<?php echo  $disp_method ?>" readonly>
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>) <span class="warning">*</span></label>
									<div class="row">
										<div class="col-xs-12 col-sm-4"><input type="text" name="txtMM" class="form-control" id="txtMM" size="3" maxlength="2" onBlur="javascript:checkValue(this);" value="1" placeholder="MM"></div>
										<div class="col-xs-12 col-sm-4"><input type="text" name="txtDD" class="form-control" id="txtDD" size="3" maxlength="2" onBlur="javascript:checkValue(this);" value="1" placeholder="DD"></div>
										<div class="col-xs-12 col-sm-4"><input type="text" name="txtYY" class="form-control" id="txtYY" size="4" maxlength="4" onBlur="javascript:checkValue(this);" value="2011" placeholder="YYYY"></div>
									</div>
								</div>
								<div class="row main_form_inner">
									<label>
										<input type="button" name="btPay" id="btPay" class="subm_btt"  value="<?php echo BUTTON_PAY_NOW; ?>" onClick="javascript:clickConfirm();">
									</label>
								</div>								
							<?php }//end else ?>
							</form>			
						</div>
						<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>	
						
						<div class="clear"></div>					
						
						<div class="col-lg-12 col-sm-12 col-md-12">
							<div class="subbanner"><?php include('./includes/sub_banners.php'); ?></div>
						</div>
						
					</div>					
				</div>
			</div>  
		</div>
	</div>
	
	<?php require_once("./includes/footer.php"); ?>