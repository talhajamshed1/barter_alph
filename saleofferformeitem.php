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
include ("./includes/enable_module.php");

$message = "";
$var_swapid = "";
$var_description = "";
$var_mpay = 0;
$var_hpay = 0;

if ($_GET["saleid"] != "") {
    $var_saleid = $_GET["saleid"];
    $var_userid = $_GET["userid"];
    $var_date = $_GET["dt"];
}//end if
else if ($_POST["saleid"] != "") {
    $var_saleid = $_POST["saleid"];
    $var_userid = $_POST["userid"];
    $var_date = urldecode($_POST["dt"]);
}//end else if
$reject_flag = false;


if (isset($_POST["postback"]) && $_POST["postback"] == "Y") {
    $sql = "Select nSaleInterId from " . TABLEPREFIX . "saleinter where nSaleId='" . addslashes($var_saleid) . "' AND nUserId='" . addslashes($var_userid) . "' AND dDate='" . addslashes($var_date) . "' AND vDelStatus='0' ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) <= 0) {
        $sql = "Select sd.nSaleId,sd.nUserId,sd.dDate,sd.vRejected,sd.vSaleStatus,s.vDelStatus,sd.nQuantity,s.nQuantity as 'nQuantity2' from " . TABLEPREFIX . "saledetails sd  ";
        $sql .= " inner join " . TABLEPREFIX . "sale s on  sd.nSaleId = s.nSaleId  where sd.nSaleId='" . addslashes($var_saleid) . "' ";
        $sql .= " AND sd.nUserId='" . addslashes($var_userid) . "' AND sd.dDate='" . addslashes($var_date) . "' ";
        $sql .= " AND s.nUserId='" . $_SESSION["guserid"] . "' ";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) > 0) {
            if ($row = mysqli_fetch_array($result)) {
                if ($row["vRejected"] == "0" && $row["vSaleStatus"] == "1" && $row["vDelStatus"] == "0") {
                    $qty = $row["nQuantity"];
                    $sqlupdateqty = "UPDATE " . TABLEPREFIX . "sale SET nQuantity = nQuantity + $qty  WHERE nSaleId= '" . addslashes($var_saleid) . "' ";
                    mysqli_query($conn, $sqlupdateqty) or die(mysqli_error($conn));

                    $sql = "UPDATE " . TABLEPREFIX . "saledetails SET vRejected ='1' WHERE nSaleId= '" . addslashes($var_saleid) . "' AND  ";
                    $sql .= " nUserId='" . addslashes($var_userid) . "' AND dDate='" . addslashes($var_date) . "' ";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    //CZQ check for zero quantity,if zero, decrease the no from categories
                    if ($row["nQuantity2"] == "0") {
                        settype($qty, double);
                        if ($qty > 0) {
                            $sqlupdate = "SELECT C.vRoute FROM " . TABLEPREFIX . "sale S inner join " . TABLEPREFIX . "category
										  C on S.nCategoryId = C.nCategoryId where nSaleId='" . addslashes($var_saleid) . "'";
                            $resultupdate = mysqli_query($conn, $sqlupdate) or die(mysqli_error($conn));
                            if (mysqli_num_rows($resultupdate) > 0) {
                                $row = mysqli_fetch_array($resultupdate);

                                $sqlupdate = "Update " . TABLEPREFIX . "category set nCount=nCount + 1
												  where nCategoryId IN(" . $row["vRoute"] . ")";
                                mysqli_query($conn, $sqlupdate);
                            }//end if
                        }//end if
                    }//end if
                    //End of CZQ
                }//end if
            }//end if
        }//end if
    }//end if
    else {
        $reject_flag = true;
    }//end else
}//end if
//End of editing

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <script language="javascript" type="text/javascript">
        function clickPhoto(picName){
            var str="picture.php?url=" + picName;
            var left = Math.floor( (screen.width - 300) / 2);
            var top = Math.floor( (screen.height - 400) / 2);
            picture=window.open(str,"picturedisplay","top=" + top + ",left=" + left + ",toolbars=no,maximize=yes,resize=no,width=300,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
        }

        function clickReject(){
            document.frmSaleOffer.action="saleofferformeitem.php";
            document.frmSaleOffer.postback.value="Y";
            document.frmSaleOffer.submit();
        }
    </script>
    <?php include_once('./includes/top_header.php'); ?>
	
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">
				<div class="innersubheader">
					<h4><?php echo HEADING_ORDER_DETAILS; ?></h4>
				</div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<div class="row">
							<div class="full_width fldset">
							<h4><?php echo HEADING_SALE_ORDER_DETAILS; ?></h4>
								<div style="overflow:auto; " id="mainSpan" class="scroll row">
									<div class="full_width">
										<?php
										$var_salestatus = "";
										$var_rejected = "";
	
										$sql = "Select sd.nSaleId,sd.nUserId,sd.nAmount,sd.nPoint,sd.nQuantity,sd.vSaleStatus,sd.vRejected,";
										$sql .= "u.vLoginName,sd.vAddress1,sd.vAddress2,sd.vCity,sd.vState,sd.vCountry,sd.nZip,sd.vPhone,";
										$sql .= "s.vUrl,date_format(dPostDate,'%m/%d/%Y') as 'dPostDate', s.vTitle , s.vBrand ,s.vType,";
										$sql .= "s.vDescription,s.vCondition,s.vYear ,s.nValue,s.nShipping,s.vDelStatus,s.nPoint as point,";
										$sql .= "u.vFax,u.vEmail from " . TABLEPREFIX . "saledetails sd inner join " . TABLEPREFIX . "users u on ";
										$sql .= "sd.nUserId = u.nUserId inner join " . TABLEPREFIX . "sale s on sd.nSaleId = s.nSaleId  where ";
										$sql .= " sd.nSaleId='" . $var_saleid . "' AND sd.nUserId='" . $var_userid . "' AND sd.dDate='" . $var_date . "' AND s.nUserId='" . $_SESSION["guserid"] . "' ";                                                                                                   
										$result1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
										if (mysqli_num_rows($result1) > 0) {
											if ($row1 = mysqli_fetch_array($result1)) {
												$var_salestatus = $row1["vSaleStatus"];
												$var_rejected = $row1["vRejected"];
												?>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_USERNAME; ?></b></label>
											<label><?php echo  htmlentities($row1["vLoginName"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_ADDRESS_LINE1; ?></b></label>
											<label><?php echo  htmlentities($row1["vAddress1"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_ADDRESS_LINE2; ?></b></label>
											<label><?php echo  htmlentities($row1["vAddress2"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_CITY; ?></b></label>
											<label><?php echo  htmlentities($row1["vCity"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_STATE; ?></b></label>
											<label><?php echo  htmlentities($row1["vState"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_COUNTRY; ?></b></label>
											<label><?php echo  htmlentities($row1["vCountry"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_ZIP; ?></b></label>
											<label><?php echo  htmlentities($row1["nZip"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_PHONE; ?><b></label>
											<label><?php echo  htmlentities($row1["vPhone"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_EMAIL; ?></b></label>
											<label><?php echo  htmlentities($row1["vEmail"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_TOTAL_AMOUNT; ?></b></label>
											<label><?php echo CURRENCY_CODE; ?><?php echo  htmlentities($row1["nAmount"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo str_replace('{point_name}',POINT_NAME,TEXT_TOTAL_POINTS); ?></b></label>
											<label><?php echo  htmlentities($row1["nPoint"]) ?></label>
										</div>
										<div class="full_width saleofferformeitem_inner">
											<label><b><?php echo TEXT_QUANTITY_REQUIRED; ?></b></label>
											<label><?php echo  htmlentities($row1["nQuantity"]) ?></label>
										</div>
											<?php
											}//end if
										}//end if
										?>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="full_width fldset">
							<h4><?php echo HEADING_SALES_ITEM_DETAILS; ?>...</h4>
								<div class="full_width">
									<div class="row main_form_inner">
										<?php
										if ($row1["vUrl"] == "") {
											//echo "Picture n/a";
										}//end if
										else {
											echo "<a href=\"javascript:clickPhoto('" . $row1["vUrl"] . "');\" class='style1'><img src=\"" . $row1["vUrl"] . "\" width='100' height='75' border=1><br><font size='1' face='verdana'>" . LINK_VIEW_LARGE_IMAGE . "</font></a>";
										}//end else
										?>
									</div>
									<div class="full_width saleofferformeitem_inner">
										<label><b><?php echo TEXT_TITLE; ?></b></label>
										<label><?php echo  htmlentities($row1["vTitle"]) ?></label>
									</div>
									<div class="full_width saleofferformeitem_inner">
										<label><b><?php echo TEXT_ITEM_DESCRIPTION; ?></b></label>
										<label><?php echo  htmlentities($row1["vDescription"]) ?></label>
									</div>
									<div class="full_width saleofferformeitem_inner">
										<label><b><?php echo TEXT_BRAND; ?></b></label>
										<label><?php echo  htmlentities($row1["vBrand"]) ?></label>
									</div>
									<div class="full_width saleofferformeitem_inner">
										<label><b><?php echo TEXT_TYPE; ?></b></label>
										<label><?php echo  htmlentities($row1["vType"]) ?></label>
									</div>
									<div class="full_width saleofferformeitem_inner">
										<label><b><?php echo TEXT_CONDITION; ?></b></label>
										<label><?php echo  htmlentities($row1["vCondition"]) ?></label>
									</div>
									<div class="full_width saleofferformeitem_inner">
										<label><b><?php echo TEXT_PRICE; ?></b></label>
										<label><?php echo CURRENCY_CODE; ?><?php echo  $row1["nValue"] ?></label>
									</div>
									<div class="full_width saleofferformeitem_inner">
										<label><b><?php echo POINT_NAME; ?></b></label>
										<label><?php echo  $row1["point"] ?></label>
									</div>
									<div class="full_width saleofferformeitem_inner">
										<label><b><?php echo TEXT_SHIPPING; ?></b></label>
										<label><?php echo CURRENCY_CODE; ?><?php echo  $row1["nShipping"] ?></label>
									</div>
									<div class="full_width saleofferformeitem_inner">
										<label><b><?php echo TEXT_POSTED_ON; ?></b></label>
										<label><?php echo  htmlentities($row1["dPostDate"]) ?></label>
									</div>
								</div>
							</div>
						</div>
						<div class="full_width warning">
							<form name="frmSaleOffer" method="post" action="">
								<?php
								if ($row1["vRejected"] == "1") {
									echo ERROR_STATUS_OF_ITEM_REJECTED;
								}//end if
								else if ($row1["vSaleStatus"] != "1") {
									echo ERROR_CANNOT_REJECT_PAYMENT_DONE;
								}//end else
								else if ($row1["vDelStatus"] == "1") {
									echo ERROR_CANNOT_REJECT_STATUS_DELETED;
								}//end else if
								elseif ($reject_flag == true) {
									echo ERROR_CANNOT_REJECT_CHECK_SUBMITTED_CONTACT_ADMIN;
								}//end else if
								else {
									?>
									<input type="hidden" name="saleid" value="<?php echo  $var_saleid ?>">
									<input type="hidden" name="userid" value="<?php echo  $var_userid ?>">
									<input type="hidden" name="dt" value="<?php echo  urlencode($var_date) ?>">
									<input type="hidden" name="postback" value="">
									<input type = "button" name= "btnReject" value="<?php echo BUTTON_REJECT_ORDER; ?>" onClick="javascript:clickReject();" class="submit">
									<?php
								}//end if
								?>
							</form>
                                                    
                                                    <div class="row main_form_inner">
								<label>
									<input type="button" name="Update" VALUE="<?php echo LINK_BACK; ?>" onClick="javascript:window.location.href='salepaymentsforme.php';" class="subm_btt">

								</label>
								
							</div>
						</div>
					</div>		
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
				</div>				
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>
			</div>
		</div>  
	</div>
</div>
				
<script type="text/javascript">
	try
	{
		for(i=0;i < chk.length;i++)
		{
			eval(document.getElementById(chk[i]).checked=true);
		}//end for loop
	}//end try
	catch(e)
	{
		//    alert('Have a  nice day!');
	}//end catch
</script>

<?php require_once("./includes/footer.php"); ?>