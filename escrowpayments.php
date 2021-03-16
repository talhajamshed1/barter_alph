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
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include ("./includes/session_check.php");

include_once('./includes/gpc_map.php');

$qryopt = "";
if ($_POST["txtSearch"] != "") {
    $txtSearch = $_POST["txtSearch"];
}//end if
else if ($_GET["txtSearch"] != "") {
    $txtSearch = $_GET["txtSearch"];
}//end else if
if ($_POST["ddlSearchType"] != "") {
    $ddlSearchType = $_POST["ddlSearchType"];
}//end if
else if ($_GET["ddlSearchType"] != "") {
    $ddlSearchType = $_GET["ddlSearchType"];
}//end else if

if ($txtSearch != "") {
    if ($ddlSearchType == "transmode") {
        $qryopt .= "  and vMode like '" . addslashes($txtSearch) . "%'";
    }//end if
    else if ($ddlSearchType == "transno") {
        $qryopt .= "  and vModeNo like '" . addslashes($txtSearch) . "%'";
    }//end else if
    else if ($ddlSearchType == "amount") {
        $qryopt .= "  and nAmount like '" . addslashes($txtSearch) . "%'";
    }//end else if
    else if ($ddlSearchType == "date") {
        $date = $txtSearch;
      //  $arr = split("/", $date);
         $arr = explode("/", $date);
        if (strlen($arr[0]) < 2) {
            $month = "0" . $arr[0];
        }//end if
        else {
            $month = $arr[0];
        }//end else if
        if (strlen($arr[1]) < 2) {
            $day = "0" . $arr[1];
        }//end if
        else {
            $day = $arr[1];
        }//end else if

        $year = $arr[2];
        $newdate = $year . "-" . $month . "-" . $day;
        $qryopt .= "  and dDate  like '" . addslashes($newdate) . "%'";
    }//end else if
}//end if

$sql = "SELECT * FROM " . TABLEPREFIX . "cashtxn  WHERE nUserId  = '" . $_SESSION["guserid"] . "' " . $qryopt . "  order by dDate DESC ";

$sess_back = "account.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch;

$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows, 10, 10, "&ddlSearchType=$ddlSearchType&txtSearch=$txtSearch&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql=dopaging($sql,'',PAGINATION_LIMIT);
//$sql = $sql . $navigate[0];

$rs = mysqli_query($conn, $sql);

$numRecords = mysqli_num_rows($rs);
if($numRecords>0) {
    
$pagenumber     =   getCurrentPageNum();
$defaultUrl     =   $_SERVER['PHP_SELF'];
$querysting     =   "&ddlSearchType=$ddlSearchType&txtSearch=$txtSearch&";
$paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
$pageString     =   getnavigation($totalrows);
include_once("lib/pager/pagination.php"); 
$pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}

    
//$sql1 = "SELECT nAccount FROM " . TABLEPREFIX . "users  WHERE nUserId = '" . $_SESSION["guserid"] . "'";
//$result = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
//$row = mysqli_fetch_array($result);
//if ($row["nAccount"] != "0") {
//    $amount = $row["nAccount"];
//} else {
//    $amount = "0";
//}

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmAffMain.submit();
    }

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
    function viewSurvey(refId) {
        var str = 'viewReg.php?refid=' + refId  + '&';
        var left = Math.floor( (screen.width - 500) / 2);
        var top = Math.floor( (screen.height - 400) / 2);

        var loginWindow=window.open(str,"approvalpage","top=" + top + ",left=" + left + ",toolbars=no,maximize=no,resize=no,width=500,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
    }
    function viewReg(refId) {
        var str = 'viewReg.php?refid=' + refId + '&';
        var left = Math.floor( (screen.width - 500) / 2);
        var top = Math.floor( (screen.height - 400) / 2);

        var loginWindow=window.open(str,"approvalpage","top=" + top + ",left=" + left + ",toolbars=no,maximize=no,resize=no,width=500,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
    }
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3">
				<?php include_once ("./includes/usermenu.php"); ?>
			</div>
			<div class="col-lg-9">
				<div class="full-width">
					<div class="col-lg-12">
						<div class="innersubheader2">
							<h3><?php echo HEADING_ESCROW_PAYMENTS; ?></h3>
						</div>
					</div>
				</div>
				<div class="clear">&nbsp;</div>
				<div class="full-width">
					<div class="col-lg-12">
						<div class="table-responsive">
						
						<table width="100%"  border="0" cellspacing="1" cellpadding="4" class="table table-bordered">
							<form name="frmAffMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
								<?php
								$message = ($message != '') ? $message : $_SESSION['sessionMsg'];
								unset($_SESSION['sessionMsg']);
	
								if (isset($message) && $message != '') {
									?>
									<tr >
										<td colspan="8" align="center" class="warning"><?php echo $message; ?></td>
									</tr>
								<?php }//end if ?>
								<tr align="center" >
									<td colspan="8" style="text-align: right;font-size: 13px;
    padding: 13px;">
										<a class="escrowpayments-clickbtn" style="" href="viewalltran.php"><?php echo TEXT_YOUR_PENDING_SETTLEMENT_IS; ?></a> <!--<span class="warning"><?php //echo CURRENCY_CODE; ?><? //echo $amount; ?> <a href="viewalltran.php"><?php //echo LINK_DETAILS; ?></a></span>-->
									</td>
								</tr>
								<tr >
									<td colspan="8" align="center">
									
									<table border="0" width="100%" class="search_table">
											<tr>
												<td valign="top" align="right">
													<?php echo TEXT_SEARCH; ?>
													&nbsp; <select name="ddlSearchType" class="comm_input width1">
														<option value="date" <?php if ($ddlSearchType == "date" || $ddlSearchType == "") {
																echo("selected");
															} ?>><?php echo TEXT_TRANSACTION_DATE; ?>(<?php echo TEXT_MM_DD_YYYY; ?>)</option>
														<option value="amount"  <?php if ($ddlSearchType == "amount") {
																echo("selected");
															} ?>><?php echo TEXT_AMOUNT; ?></option>
														<option value="transmode"  <?php if ($ddlSearchType == "transmode") {
																echo("selected");
															} ?>><?php echo TEXT_TRANSACTION_MODE; ?></option>
														<option value="transno" <?php if ($ddlSearchType == "transno") {
																echo("selected");
															} ?>><?php echo TEXT_TRANSACTION_NUMBER; ?></option>
													</select>
													&nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode=='13'){ return false; }" class="comm_input width1">
												</td>
												<td align="left" >
													<a href="javascript:clickSearch();" class="login_btn comm_btn_orng_tileeffect2"><?php echo BUTTON_GO; ?><!--<img src='./images/gobut.gif'  width="20" height="20" border='0' >--></a>
												</td>
											</tr>
										</table></td>
								</tr>
								<tr align="center"  class="gray">
									<th width="7%" valign="top"><?php echo TEXT_SLNO; ?></th>
									<th width="15%"><?php echo TEXT_DATE; ?></th>
									<th width="16%"><?php echo TEXT_TRANSACTION_NUMBER; ?></th>
									<th width="13%"><?php echo TEXT_MODE; ?></th>
									<th width="12%"><?php echo TEXT_AMOUNT; ?></th>
									<th width="14%"><?php echo TEXT_ESCROW_FEES; ?></th>
									<th width="10%"><?php echo TEXT_TYPE; ?></th>
									<th width="13%"><?php echo TEXT_DETAILS; ?></th>
								</tr>
								<?php
								if (mysqli_num_rows($rs) > 0) {
									$cnt = 1;
									$sep = chr(236);
									while ($arr = mysqli_fetch_array($rs)) {
										$regdate = $arr["dDate"];
										$regdate = explode(" ", $regdate);
										$dateonly = $regdate[0];
										?>
										<tr >
											<td align="center"><?php echo $cnt; ?></td>
											<td>
											<?php if ($dateonly != '0000-00-00') {
												echo date('m/d/Y', strtotime($dateonly));
											} ?>
											</td>
											<td><?php echo htmlentities($arr["vModeNo"]); ?></td>
											<td><?php echo constant('TEXT_'.strtoupper(str_replace(' ','_',$arr["vMode"]))); ?></td>
											<td><?php echo CURRENCY_CODE.htmlentities($arr["nAmount"]); ?></td>
											<td><?php echo htmlentities($arr["nCommission"]); ?></td>
											<td align="center">
											<?php
												if ($arr["vReason"] == "" . TABLEPREFIX . "swap") {
													$key = explode("$sep", $arr["vKey"]);
													echo TEXT_SWAP_ITEM."/".TEXT_WISH_ITEM;
												}//end if
												else if ($arr["vReason"] == "" . TABLEPREFIX . "saledetails") {
													$key = explode("|", $arr["vKey"]);
													echo TEXT_SALE_ITEM;
												}//end else if
												else if ($arr["vReason"] == "survey") {
													$key = $arr["vKey"];
													echo TEXT_SURVEY;
												}//end else if
												else if ($arr["vReason"] == "registration") {
													$key = $arr["vKey"];
													echo TEXT_REGISTRATION;
												}//end else if
												else {
													echo TEXT_N_A;
												}//end else
											 ?>
											</td>
											<td align="center">
												<?php
										//details
										if ($arr["vReason"] == "" . TABLEPREFIX . "swap") {
											$key = explode("$sep", $arr["vKey"]);
											$sql = "SELECT nSTId from " . TABLEPREFIX . "swaptxn where (nSwapReturnId like '%".$key[0]."%' or nSwapId like '%".$key[0]."%') and vStatus ='A' and (nUserId='" . $key[1] . "' or nUserReturnId='" . $key[1] . "') ";
											$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
											if ($srow = mysqli_fetch_array($res)){
												echo "<a href='makeoffer.php?nSTId=".$srow['nSTId']."' target='_blank'>".LINK_VIEW."</a>";
											}
											//echo "<a href=\"javascript:viewTransaction('$key[0]','$key[1]','$key[3]','$key[2]');\" title=''>".LINK_VIEW."</a>";
										}//end if
										else if ($arr["vReason"] == "" . TABLEPREFIX . "saledetails") {
											$key = explode("|", $arr["vKey"]);
											echo "<a href=\"javascript:viewSale('$key[0]','$key[1]','$key[2]');\" title=''>".LINK_VIEW."</a>";
										}//end else if
										else if ($arr["vReason"] == "survey") {
											$key = $arr["vKey"];
											echo "<a href=\"javascript:viewSurvey('$key');\" title=''>".LINK_VIEW."</a>";
										}//end else if
										else if ($arr["vReason"] == "registration") {
											$key = $arr["vKey"];
											echo "<a href=\"javascript:viewReg('$key');\" title=''>".LINK_VIEW."</a>";
										}//end else if
										else {
											echo TEXT_N_A;
										}//end else
										 ?></td>
										</tr>
												<?php
												$cnt++;
											}//end while
									   
										?>
								<tr >
									<td colspan="8" align="left" class="navigation">
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
	
					   </div>  
									</td>
								</tr>
								
								<?php } ?>      
							</form>
						</table>
						</div>
					</div>
				</div>			
				
				<div class="full-width subbanner">
					<div class="col-lg-12">
						<?php include('./includes/sub_banners.php'); ?>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>