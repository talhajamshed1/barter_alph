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
include_once('./includes/gpc_map.php');

if (isset($_GET["userid"]) || $_GET["userid"] != "") {
    $userid = $_GET["userid"];
}//end if
else if (isset($_POST["userid"]) || $_POST["userid"] != "") {
    $userid = $_POST["userid"];
}//end else if
$userid = $_SESSION["guserid"];

$sqluserdetails = "SELECT vLoginName, vFirstName,vLastName  FROM " . TABLEPREFIX . "users  WHERE  nUserId  = '" . $userid . "'";
$resultuserdetails = mysqli_query($conn, $sqluserdetails) or die(mysqli_error($conn));
$rowuser = mysqli_fetch_array($resultuserdetails);
$txtUserName = $rowuser["vLoginName"];
$txtFirstName = $rowuser["vFirstName"];
$txtLastName = $rowuser["vLastName"];

if ($txtLastName != "") {
    $userfullname = $txtFirstName . " " . $txtLastName;
}//end if
else {
    $userfullname = $txtFirstName;
}//end else



include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <script language="javascript" type="text/javascript">
        var total=0;
        var comm=0;
        var famnt=0;

        function viewTransaction(swapid,userid,uname,member){
            var str = 'viewtransaction.php?swapid=' + swapid + '&userid=' + userid + '&uname=' + escape(uname) + '&memberid=' + member + '&';
            var left = Math.floor( (screen.width - 700) / 2);
            var top = Math.floor( (screen.height - 400) / 2);

            var loginWindow=window.open(str,"approvalpage","top=" + top + ",left=" + left + ",toolbars=no,maximize=no,resize=no,width=700,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
        }

        function viewSale(saleid,userid,ddate){
            var str = 'viewsale.php?saleid=' + saleid + '&userid=' + userid + '&dDate=' + escape(ddate) + '&';
            var left = Math.floor( (screen.width - 700) / 2);
            var top = Math.floor( (screen.height - 400) / 2);

            var loginWindow=window.open(str,"approvalpage","top=" + top + ",left=" + left + ",toolbars=no,maximize=no,resize=no,width=700,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
        }

        function viewReg(refId) {
            var str = 'viewReg.php?refid=' + refId + '&';
            var left = Math.floor( (screen.width - 500) / 2);
            var top = Math.floor( (screen.height - 400) / 2);

            var loginWindow=window.open(str,"approvalpage","top=" + top + ",left=" + left + ",toolbars=no,maximize=no,resize=no,width=500,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
        }
    </script>
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">
				<div class="innersubheader">
					<h4><?php echo HEADING_TRANSACTION_DETAILS; ?></h4>
				</div>
				<div class="clear"></div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-10 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<input type="hidden"  name="userid" value="<?php echo $userid; ?>" />
						<input type="hidden"  name="username" value="<?php echo htmlentities($username); ?>" />
						<input type="hidden"  name="txtAccount" value="<?php echo $txtAccount; ?>" />
						<?php
							if (isset($message) && $message != '') {
							?>
								<div class="row warning"><?php echo $message; ?></div>
						<?php }//end if?>
						
						<div class="innersubheader">
							<h4><?php echo str_replace('{user_name}',htmlentities($userfullname),TEXT_PENDING_FROM_ESCROW_FOR_USER); ?></h4>
						</div>
																	
						<div class="row main_form_inner">
							<label><?php echo TEXT_FIRST_NAME; ?></label>
							<label><?php echo htmlentities($txtFirstName); ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_LAST_NAME; ?></label>
							<label><?php echo htmlentities($txtLastName); ?></label>
						</div>
					</div>	
					<div class="col-lg-2 col-sm-12 col-md-10 col-xs-12">	
				</div>
			</div>
			
			
			<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" style="background:#f0f0f0; padding-top:15px; padding-bottom:15px; ">
			<?php
				$amt_to_be_settled = 0;
				$sql = "SELECT s.nSwapId,s.nUserId,s.vTitle,s.nSwapMember,s.nSwapAmount,u.vLoginName,s.vOwnerDelivery,
										s.vPartnerDelivery,s.vPostType
					FROM " . TABLEPREFIX . "swap s inner join " . TABLEPREFIX . "users u on
					s.nSwapMember = u.nUserId where s.vSwapStatus= '2' AND s.nSwapAmount > 0 AND s.nSwapMember='" . addslashes($userid)."'";
				$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
				if (mysqli_num_rows($result) > 0) {
				$cnt = 1;
			?>
			<div class="full_width">
				<div class="table-responsive">
					<table width="200" border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
						 <tr class="gray">
							<th><?php echo TEXT_SLNO; ?></th>
							<!--<td width="16%">&nbsp;</td>-->
							<th><?php echo TEXT_TITLE; ?></th>
							<th><?php echo TEXT_DELIVERED; ?></th>
							<th><?php echo TEXT_AMOUNT; ?>(<?php echo CURRENCY_CODE; ?>)</th>
							<th><?php echo TEXT_ESCROW_FEES; ?>(<?php echo getGeneralPercentageText();?>)</th>
                                                        <th><?php echo TEXT_AMOUNT_TO_SETTLE; ?>(<?php echo CURRENCY_CODE; ?>)</th>
						</tr>
						<?php
						while ($row = mysqli_fetch_array($result)) {
							
							?>
								<tr>
									<td><?php echo $cnt; ?></td>
									<!--<td>
									<?php
									   // if ($row["vPostType"] == "swap") {
											//$str_status = ((($row["vOwnerDelivery"] == "Y" || $row["vOwnerDelivery"] == "A") && $row["vPartnerDelivery"] == "Y") ? "" : "disabled");
											$str_status = (($row["vPartnerDelivery"] == "Y") ? "" : "disabled");
									   // }//end if
									   // else {
									   //     $str_status = (($row["vOwnerDelivery"] == "Y" || $row["vOwnerDelivery"] == "A") ? "" : "disabled");
										//}//end esle
										?>
									</td>-->
									<td>
									<?php 
										$sql = "SELECT nSTId from " . TABLEPREFIX . "swaptxn where (nSwapReturnId like '%".$row["nSwapId"]."%' or nSwapId like '%".$row["nSwapId"]."%') and vStatus ='A' and (nUserId='" . $row["nUserId"] . "' or nUserReturnId='" . $row["nUserId"] . "') ";
										$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
										if ($srow = mysqli_fetch_array($res)){
											echo "<a href='makeoffer.php?nSTId=".$srow['nSTId']."' target='_blank'>".htmlentities($row["vTitle"])."</a>";
										}
									?>
									</td>
									<!--<td><a href="javascript:viewTransaction(<?php echo  $row["nSwapId"] ?>,<?php echo  $row["nUserId"] ?>,'<?php echo  urlencode($row["vLoginName"]) ?>',<?php echo  $row["nSwapMember"] ?>);"><?php echo  htmlentities($row["vTitle"]) ?></a></td>-->
									<td><?php echo ($str_status == "disabled") ? TEXT_PENDING : TEXT_COMPLETED; ?></td>
									<td><?php echo $namt = ($row["nSwapAmount"] < 0) ? (-1 * $row["nSwapAmount"]) : $row["nSwapAmount"]; ?></td>
									<td><?php echo $var_escrow = getEscrowSettleAmount($row["nSwapAmount"]);?> <?php echo "(". getEscrowPercentage($row["nSwapAmount"]).")";?></td>
                                                                        <td><?php echo ($namt-$var_escrow);?> </td>
								</tr>
								<?php
								$cnt++;
								$amt_to_be_settled += ($namt-$var_escrow);
							}//end while
							?>
					</table>	
				</div>
			</div>
			<?php
				}//end if
			
				$sql = "SELECT s.nSaleId,st.nQuantity,st.dDate,st.nAmount,st.vMethod,st.vDelivered,
									 s.vTitle,st.nUserId from " . TABLEPREFIX . "sale s inner join  " . TABLEPREFIX . "saledetails st
									 on s.nSaleId = st.nSaleId where st.vSaleStatus = '2' AND s.nUserId='"
						. addslashes($userid) . "'";
				$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
				if (mysqli_num_rows($result) > 0) {
					$cnt = 1;
				?>				
				<div class="full_width">
					<div class="table-responsive">
						<table width="200" border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
							<tr align="left"  class="gray">
								<th width="7%" valign="top"><?php echo TEXT_SLNO; ?></th>
								<th width="19%" valign="top"><?php echo TEXT_TITLE; ?>(<?php echo TEXT_SALE_ITEM; ?>)</th>
								<th width="19%" valign="top"><?php echo TEXT_DELIVERED; ?></th>
								<th width="19%" valign="top"><?php echo TEXT_AMOUNT; ?>(<?php echo CURRENCY_CODE; ?>)</th>
								<th width="20%" valign="top"><?php echo TEXT_ESCROW_FEES; ?>(<?php echo getGeneralPercentageText();?>)</th>
                                                                <th><?php echo TEXT_AMOUNT_TO_SETTLE; ?>(<?php echo CURRENCY_CODE; ?>)</th>
							</tr>
								<?php
								while ($row = mysqli_fetch_array($result)) {
									
								?>
								<tr >
									<td align="center"><?php echo $cnt; ?></td>
									<td><a href="javascript:viewSale(<?php echo  $row["nSaleId"] ?>,<?php echo  $row["nUserId"] ?>,'<?php echo  urlencode($row["dDate"]) ?>');"><?php echo  htmlentities($row["vTitle"]) ?></a></td>
									<td><?php echo  ($row["vDelivered"] == "Y") ? TEXT_YES : TEXT_NO; ?></td>
									<td><?php echo  $row["nAmount"] ?></td>
									<td><?php echo $var_escrow = getEscrowSettleAmount($row["nAmount"]); ?> <?php echo "(". getEscrowPercentage($row["nAmount"]).")";?></td>
                                                                        <td><?php echo ($row["nAmount"]-$var_escrow);?> </td>
                                                                </tr>
								<?php
								$cnt++;
								$amt_to_be_settled += ($row["nAmount"]-$var_escrow);
							}//end while
							?>
						</table>	
					</div>
				</div>
				<?php }//end if ?>
				
			<div class="full_width">
				<label><?php echo TEXT_AMOUNT_TOTAL_SETTLE; ?></label>
				<label><?php echo CURRENCY_CODE; ?> <input type="text" name="txtAmount" id="txtAmount" value="<?php echo $amt_to_be_settled;//echo $var_account; ?>" class="comm_input width2" readonly ></label>
			</div>
		</div>
		<div class="subbanner">
			<?php include('./includes/sub_banners.php'); ?>
		</div>
		</div>  
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>