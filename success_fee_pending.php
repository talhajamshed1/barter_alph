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
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include ("./includes/session_check.php");

include_once('./includes/gpc_map.php');

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

$qryopt = "";
if ($ddlSearchType == "amount") {
        $qryopt .= "  and nAmount like '" . addslashes($txtSearch) . "%'";
}
else if ($ddlSearchType == "date") {
        $date = $txtSearch;
      //  $arr = split("/", $date);
        $arr = explode("/", $date);
        if (strlen($arr[0]) < 2) {
            $month = "0" . $arr[0];
        }//end if
        else {
            $month = $arr[0];
        }//end else
        if (strlen($arr[1]) < 2) {
            $day = "0" . $arr[1];
        }//end if
        else {
            $day = $arr[1];
        }//end else
        $year = $arr[2];
        $newdate = $year . "-" . $month . "-" . $day;
        $qryopt .= "  and dDate='" . addslashes($newdate) . "'";
}//end if

$sqlSent = "SELECT nSId,nUserId,nAmount,nPoints,date_format(dDate,'%m/%d/%Y') as sentDate,vStatus,nPurchaseBy,vType,nProdId ";
$sqlSent .= " FROM " . TABLEPREFIX . "successfee";
$sqlSent .= " WHERE nUserId  = '" . $_SESSION["guserid"] . "' AND vStatus = 'P'";
$sqlSent .= $qryopt;
$sqlSent .= "  order by dDate DESC ";
$totalrows = mysqli_num_rows(mysqli_query($conn, $sqlSent));
$sqlSent=dopaging($sqlSent,'',PAGINATION_LIMIT);
//$sql = $sql . $navigate[0];
$rssale = mysqli_query($conn, $sqlSent) or die(mysqli_error($conn));
$numRecords = mysqli_num_rows($rssale);
  if($numRecords>0) {
$pagenumber     =   getCurrentPageNum();
$defaultUrl     =   $_SERVER['PHP_SELF'];
$querysting     =   "ddlSearchType=$ddlSearchType&txtSearch=$txtSearch&";
$paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
$pageString     =   getnavigation($totalrows);
include_once("lib/pager/pagination.php"); 
$pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}

/*$navigate = pageBrowser($totalrows, 5, 5, "&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
$sqlSent = $sqlSent . $navigate[0];

$rssale = mysqli_query($conn, $sqlSent) or die(mysqli_error($conn));*/

include_once('./includes/title.php');
?>
<script type="text/javascript" src="js/dhtmlwindow.js"></script>
<script language="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmSent.submit();
    }
</script>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
	<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">
				 <div class="row">
						<div class="col-lg-12">
							<div class="innersubheader2">
								<h3><?php echo MENU_PENDING_ORDER_CONFIRMATIONS; ?></h3>
							</div>
						</div>
				   </div>
					<div class="space">&nbsp;</div>
				   <div class="full-width">
						<div class="col-lg-12">
							<?php include('./includes/account_menu.php'); ?>                        
					  <div class="tabContent tabcontent_wrapper">
						  <div class="table-responsive">
							<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="table table-bordered table-hover">
								<form name="frmSent" method="post" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
									<?php
									$message = ($message != '') ? $message : $_SESSION['sessionMsg'];
									unset($_SESSION['sessionMsg']);
			
									if (isset($message) && $message != '') {
										?>
										<tr >
											<td colspan="7" align="center" class="warning"><?php echo $message; ?></td>
										</tr>
									<?php }//end if ?>
									<tr >
										<td colspan="7" align="center">
										<table border="0" width="100%" class="search_table">
												<tr>
													<td valign="top" align="right">
														<?php echo TEXT_SEARCH; ?>
														&nbsp; <select name="ddlSearchType" class="comm_input width1" >
															<option value="date" <?php
																if ($ddlSearchType == "date" || $ddlSearchType == "") {
																	echo("selected");
																}
																?>><?php echo TEXT_PURCHASED_DATE; ?>(<?php echo TEXT_MM_DD_YYYY; ?>)</option>
															<option value="amount"  <?php if ($ddlSearchType == "amount") {
																echo("selected");
															} ?>><?php echo TEXT_AMOUNT; ?></option>
														</select>
														&nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="comm_input width1">
													</td>
													<td align="left" >
														<a href="javascript:clickSearch();" class="login_btn comm_btn_orng_tileeffect2"><?php echo BUTTON_GO; ?><!--<img src='./images/gobut.gif'  width="20" height="20" border='0' >--></a>
													</td>
												</tr>
											</table></td>
									</tr>
									<tr align="center"  class="gray">
										<th width="6%"><?php echo TEXT_SLNO; ?> </th>
										<th width="13%"><?php echo TEXT_PURCHASED_BY; ?> </th>
										<th width="14%"><?php echo TEXT_PURCHASED_DATE; ?> </th>
										<th width="13%"><?php echo TEXT_PRODUCT; ?></th>
										<th width="14%"><?php echo TEXT_AMOUNT; ?></th>
									   <!--  <th width="15%"><?php echo TEXT_STATUS; ?></th> -->
										<th width="14%"><?php echo TEXT_ACTION;?></th>
									</tr>
									<?php
									if (mysqli_num_rows($rssale) > 0) {
										$cnt = 1;
										while ($arr = mysqli_fetch_array($rssale)) {
											//checking status
											switch ($arr["vStatus"]) {
												case "P":
													$shwStatus = TEXT_PENDING_TO_UPDATE_ACCOUNT;
													$showPaylink = true;
													break;
			
												case "I":
													$shwStatus = TEXT_PENDING_ADMIN_VERIFICATION;
													$showPaylink = false;
													break;
			
												case "A":
													$shwStatus = TEXT_ADDED_TO_ACCOUNT;;
													$showPaylink = false;
													break;
											}//end switch
			
											$showUserName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', "WHERE nUserId='" . $arr["nPurchaseBy"] . "'"), 'vLoginName');
			
											//checking status
											switch ($arr["vType"]) {
												case "sa":
													$showProdName = fetchSingleValue(select_rows(TABLEPREFIX . 'sale', 'vTitle', "WHERE nSaleId='" . $arr["nProdId"] . "'"), 'vTitle');
													break;
			
												case "s":
													$showProdName = fetchSingleValue(select_rows(TABLEPREFIX . 'swap', 'vTitle', "WHERE nSwapId='" . $arr["nProdId"] . "'"), 'vTitle');
													break;
			
												case "w":
													$showProdName = fetchSingleValue(select_rows(TABLEPREFIX . 'swap', 'vTitle', "WHERE nSwapId='" . $arr["nProdId"] . "'"), 'vTitle');
													break;
											}//end switch
			
											//$showLink = "<a href=\"#\" onClick=\"divwin=dhtmlwindow.open('divbox" . $arr['nSId'] . "', 'div', 'somediv" . $arr['nSId'] . "', '".HEADING_PURCHASE_DETAILS."', 'width=550px,height=170px,left=550px,top=190px,resize=1,scrolling=1'); return false\">";
											//$closeLink = '</a>';
											?>
											<tr >
												<td align="center"><?php echo (($page*$limit)+$cnt-$limit);?></td>
												<td><?php echo $showLink . htmlentities($showUserName) . $closeLink; ?></td>
												<td><?php echo $showLink . date('m/d/Y', strtotime($arr["sentDate"])) . $closeLink; ?></td>
												<td><?php echo $showLink . htmlentities($showProdName) . $closeLink; ?></td>
												<td><?php echo $showLink . CURRENCY_CODE.htmlentities($arr["nAmount"]) . $closeLink; ?></td>
											   <!-- <td><?php echo $showLink . htmlentities($shwStatus) . $closeLink; ?>
													<div id="somediv<?php echo $arr['nSId']; ?>" style="display:none">
														<table width="100%"  border="0" cellspacing="0" cellpadding="0">
															<tr>
																<td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
																		<tr align="center" >
																			<td width="6%" align="left" class="gray"><?php echo TEXT_PURCHASED_DATE; ?></td>
																			<td width="6%" align="left"><?php echo date('m/d/Y', strtotime($arr["sentDate"])); ?></td>
																		</tr>
																		<tr >
																			<td align="left" class="gray"><?php echo TEXT_AMOUNT; ?></td>
																			<td align="left"><?php echo CURRENCY_CODE.htmlentities($arr["nAmount"]); ?></td>
																		</tr>
																		<tr >
																			<td align="left" class="gray"><?php echo TEXT_PURCHASED_BY; ?></td>
																			<td align="left"><?php echo htmlentities($showUserName); ?></td>
																		</tr>
																		<tr >
																			<td align="left" class="gray"><?php echo TEXT_PRODUCT; ?></td>
																			<td align="left"><?php echo htmlentities($showProdName); ?></td>
																		</tr>
																		<tr >
																			<td align="left" class="gray"><?php echo POINT_NAME; ?></td>
																			<td align="left"><?php echo htmlentities($arr["nPoints"]); ?></td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
													</div></td> -->
												<td><?php
											if ($showPaylink == true) {
												echo '<a href="pay_success_fee.php?nSId=' . $arr['nSId'] . '">'.TEXT_PAY_SUCCESS_FEE_EACH_TRANSACTION.'</a>';
											}//end if
											?></td>
											</tr>
											<?php
											$cnt++;
										}//end while
									
									?>
									<tr >
										<td colspan="7" align="left" class="navigation">
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
										</td>
									</tr>
                                                                        
                                                                        <?php } ?>       
								</form>
							</table>
						  </div>
					  </div>
					</div>
				<div class="clear"></div>
				</div>
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>
			</div>
		</div>
	</div>
</div>

                
<?php require_once("./includes/footer.php"); ?>