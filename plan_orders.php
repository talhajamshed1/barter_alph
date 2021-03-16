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
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
$message = "";
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

include_once('./includes/title.php');
$errsflag = 0;
$txtSearch = "";
$cmbSearchType = "";


$sql = "SELECT U.*,L.vPlanName,p.vTxn_mode,pl.nPlanId as PId,p.vTxn_id,p.vComments,p.vPlanStatus,p.nTxn_no
FROM " . TABLEPREFIX . "plan pl
LEFT JOIN " . TABLEPREFIX . "plan_lang L on pl.nPlanId = L.plan_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
LEFT JOIN " . TABLEPREFIX . "payment p ON pl.nPlanId=p.nPlanId
LEFT JOIN " . TABLEPREFIX . "users U ON pl.nPlanId=U.nPlanId AND U.nUserId='" . $_SESSION["guserid"] . "'  

WHERE p.nUserId='" . $_SESSION["guserid"] . "' ORDER BY p.nTxn_no DESC";

$sess_back = $targetfile . "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;
//get the total amount of rows returned
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$sql=dopaging($sql,'',PAGINATION_LIMIT);
/*
  Call the function:

  I've used the global $_GET array as an example for people
  running php with register_globals turned 'off' :)
 */
//$navigate = pageBrowser($totalrows, 10, 10, "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
//$sql = $sql . $navigate[0];
  $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

  $numRecords = mysqli_num_rows($rs);
  

  if($numRecords>0) {
  	$pagenumber     =   getCurrentPageNum();
  	$defaultUrl     =   $_SERVER['PHP_SELF'];
  	$querysting     =   "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&";
  	$paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
  	$pageString     =   getnavigation($totalrows);
  	include_once("lib/pager/pagination.php"); 
  	$pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
  }

  if (isset($_POST['btnSubmit']) && $_POST['btnSubmit'] != "") {
  	$cancelid = $_POST['nPId'];
  	$pyid = $_POST['nPyId'];

  	$sqlCancel = mysqli_query($conn, "SELECT L.vPlanName,p.vTxn_mode,pl.nPlanId as PId,p.vTxn_id,p.vComments,p.vPlanStatus
  		FROM " . TABLEPREFIX . "plan pl
  		LEFT JOIN " . TABLEPREFIX . "plan_lang L on pl.nPlanId = L.plan_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
  		LEFT JOIN " . TABLEPREFIX . "payment p ON pl.nPlanId=p.nPlanId
  		WHERE p.nUserId='" . $_SESSION["guserid"] . "' AND p.nTxn_no='" . $pyid . "' AND
  		p.nPlanId='" . $cancelid . "'") or die(mysqli_error($conn));

  	if (mysqli_num_rows($sqlCancel) > 0) {
  		$row = mysqli_fetch_array($sqlCancel);
  		$recurringorderid = $row["vTxn_id"];
  		$planname = $row["vPlanName"];
  		$paymentid = $row["nTxn_no"];

  		if ($row["vPlanStatus"] == "A") {
  			$message = ERROR_CANT_CANCEL_ACTIVE_PLAN;
  			$errsflag = 1;
        }//end if
        else if ($row["vTxn_mode"] == "cc" || $row["vTxn_mode"] == "sp") {
        	$today = date("Y-m-d");
        	$sqlupdate = "UPDATE " . TABLEPREFIX . "payment SET vPlanStatus = 'C',vComments='Canceled on $today ' WHERE nTxn_no= '" . $paymentid . "'";
        	mysqli_query($conn, $sqlupdate) or die(mysqli_error($conn));

            /*
            * Fetch user language details
            */

            $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
            $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
            $langRw = mysqli_fetch_array($langRs);

            /*
            * Fetch email contents from content table
            */
            $mailSql = "SELECT L.content,L.content_title
            FROM ".TABLEPREFIX."content C
            JOIN ".TABLEPREFIX."content_lang L
            ON C.content_id = L.content_id
            AND C.content_name = 'plansubcancel'
            AND C.content_type = 'email'
            AND L.lang_id = '".$_SESSION["lang_id"]."'";

            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{username}","{planname}","{ptype}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($_SESSION["guserFName"]),htmlentities($planname),'Credit Card');
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace("{SITE_NAME}", SITE_NAME, $subject);
            
            $StyleContent = MailStyle($sitestyle, SITE_URL);

            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, $_SESSION["guserFName"], $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody    = str_replace($arrSearch, $arrReplace, $msgBody);

            send_mail(ADMIN_EMAIL, $subject, $msgBody, SITE_EMAIL, 'Admin');
            $message = MESSAGE_SUBSCRIPTION_CANCELED;
            $errsflag = 1;
        }//end else if
        else if ($row["vTxn_mode"] == "pp") {
        	$loc = "cancelsubscriptionpp.php?nPId=" . $cancelid . "&nPyId=" . $pyid;
        	header("location:$loc");
        	exit();
        }//end else if
    }//end if >0 check
    else {
    	$message = ERROR_CANT_CANCEL_PLAN . ADMIN_EMAIL;
    	$errsflag = 1;
    }//end else
}//end first if

$message = ($_SESSION['succ_msg'] != '') ? $_SESSION['succ_msg'] : $message;
?>
<body onLoad="timersOne();">
	<script language="javascript1.1" type="text/javascript">
		function clickCalcel(chk,pid)
		{
			if(confirm('<?php echo ERROR_SURE_TO_CANCEL_SUBSCRIPTION; ?>'))
			{
				document.frmCacnelSubscription.btnSubmit.value="Go";
				document.frmCacnelSubscription.nPId.value=chk;
				document.frmCacnelSubscription.nPyId.value=pid;
				document.frmCacnelSubscription.submit();
            }//end if
        }//end function
    </script>
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
    						<div class="innersubheader">
    							<h3><?php echo TEXT_PLANS; ?></h3>
    						</div>
    					</div>
    				</div>
    				<div class="full-width">
    					<div class="col-lg-12">
    						<div class="table-responsive">
    							<table width="100%"  border="0" cellspacing="0" cellpadding="10" class="table table-bordered">
    								<tr>
    									<td align="left" valign="top">
    										<table width="100%"  border="0" cellspacing="0" cellpadding="0">
    											<tr>
    												<td ><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="table table-bordered">
    													<form action="" method="post" name="frmCacnelSubscription">
    														<input type="hidden" name="btnSubmit">
    														<input type="hidden" name="nPId">
    														<input type="hidden" name="nPyId">
    														<?php
    														if (isset($message) && $message != '') {
    															?>
    															<tr align="center" >
    																<td colspan="5" class="<?php if($errsflag == 0){ ?>success<?php }else{?>warning<?php } ?>"><b><?php echo $message; ?></b></td>
    															</tr>
    															<?php
    															unset($_SESSION['succ_msg']);
    														}
    														?>
    														<tr align="left"  class="gray">
    															<th width="6%" align="center"><?php echo TEXT_SLNO; ?></th>
    															<th width="33%"><?php echo TEXT_PLAN_NAME; ?></th>
    															<th width="19%"><?php echo TEXT_PAYMENT_METHOD; ?></th>
    															<th width="21%"><?php echo TEXT_TRANSACTION_ID; ?></th>
    															<td width="21%"><?php echo TEXT_ACTION; ?></td>
    														</tr>
    														<?php

    														$userSql = "select * from ".TABLEPREFIX."users U
    														JOIN ".TABLEPREFIX."plan  P
    														ON P.nPlanId = U.nPlanId
    														JOIN ".TABLEPREFIX."plan_lang PL
    														ON P.nPlanId = PL.plan_id
    														AND PL.lang_id = '".$_SESSION['lang_id']."'
    														AND U.nUserId='" . $_SESSION["guserid"] . "'";
    														$userRs  = mysqli_query($conn, $userSql) or die(mysqli_error($conn));
    														$cnt = '1';
    														$firstcnt = mysqli_num_rows($userRs);
    														if(mysqli_num_rows($userRs)>0){
    															$userRw  = mysqli_fetch_array($userRs);
    															if ($userRw["nPlanId"] == $_SESSION["sess_PlanId"]) {
    																$activePlan = "<font color='green'> [ " . TEXT_ACTIVE_PLAN . " ] </font>";
														}//end if
														switch ($userRw['vMethod']) {
															case "sp":
															$paymntMethod = TEXT_STRIPE;
															break;

															case "pp":
															$paymntMethod = TEXT_PAYPAL;
															break;

															case "wp":
															$paymntMethod = TEXT_WORLDPAY;
															break;

															case "cc":
															$paymntMethod = TEXT_AUTHORIZE_NET;
															break;

															case "free":
															$paymntMethod = TEXT_FREE;
															break;
														}//end switch
														?>
												<!--<tr >
													<td align="center">
														<?php echo $cnt;?>
													</td>
													<td>
														<?php echo $userRw['vPlanName'].$activePlan;?>
													</td>
													<td>
														<?php echo $paymntMethod;?>
													</td>
													<td>
														<?php echo $userRw['vTxnId'];?>
													</td>
													<!-- <td>
		
																<?php
																if ($userRw['vPlanStatus'] != "C" && $userRw['vTxn_mode'] != 'free') {
																	?>
																	&nbsp;&nbsp;<a href="javascript:clickCalcel('<?php echo $userRw["nPlanId"]; ?>','<?php echo $userRw['vTxnId']; ?>');"  class="linktext" style="text-decoration:none;"><?php echo LINK_CANCEL; ?></a>
																	<?php
																}//end if
																//else
																//{
																$cancemessage = "";
																if (trim($userRw["vComments"]) != "") {
																	$cancemessage = $userRw["vComments"];
																	echo ' | <span class="warning">' . $cancemessage . '</span>';
																}//end if
																//}//end else
																?>
													</td> 
												</tr> -->
												<?php
												$cnt++;
												$paymntMethod = '';
											}
											$i=1;
											if (mysqli_num_rows($rs) > 0 ) {

												while ($arr = mysqli_fetch_array($rs)) {

													switch ($arr['vTxn_mode']) {
														case "sp":
														$paymntMethod = TEXT_STRIPE;
														break;

														case "pp":
														$paymntMethod = TEXT_PAYPAL;
														break;

														case "wp":
														$paymntMethod = TEXT_WORLDPAY;
														break;

														case "cc":
														$paymntMethod = TEXT_AUTHORIZE_NET;
														break;

														case "free":
														$paymntMethod = TEXT_FREE;
														break;
														}//end switch

														$activePlan = '';

														if ($arr["PId"] == $_SESSION["sess_PlanId"] && $arr['vPlanStatus'] == 'A') {
															$activePlan = "<font color='green'> [ " . TEXT_ACTIVE_PLAN . " ] </font>";
														}//end if
														?>
														<tr >
															<td align="center"><?php echo $i; ?></td>
															<td align="left"><?php echo $arr['vPlanName'] . $activePlan; ?></td>
															<td align="left"><?php echo $paymntMethod; ?></td>
															<td align="left"><?php echo $arr['vTxn_id']; ?></td>
															<td align="left">
																<?php $flag=0;
																if ($arr['vPlanStatus'] != "C" && $arr['vTxn_mode'] != 'free') {
																	$flag=1;																	?>
																	&nbsp;&nbsp;<a href="javascript:clickCalcel('<?php echo $arr["PId"]; ?>','<?php echo $arr['nTxn_no']; ?>');"  class="linktext" style="text-decoration:none;"><?php echo LINK_CANCEL; ?></a>
																	<?php
																}

																$cancemessage = "";
																if (trim($arr["vComments"]) != "") {
																	$flag=1;															$cancemessage = $arr["vComments"];
																	echo ' | <span class="warning">' . $cancemessage . '</span>';
																}//end if
																if($flag == "0")
																	echo "-"; 																
																?>
															</td> 
														</tr>
														<?php
														$cnt++;
														$i++;
													}
													?>

													<tr ><td colspan="5">
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

													</td></tr>

													<?php                
												}else{ ?>

													<tr bgcolor="#FFFFFF" align="center">
														<td style="border : 0px;" colspan="7"><strong>Sorry No records found.</strong></td>
													</tr>															

												<?php } ?>
											</form>  </table>
										</td>
									</tr>
								</table>

								<?php if(1!=1){?>
									<table width="100%"  border="0" cellspacing="1" cellpadding="5" class="maintext2">
										<tr >
											<td width="23%" align="center" class="link3"><?php if($navigate['0']== "0" && $firstcnt == 0) echo($navigate[2]); ?></td>
											<td width="77%" align="right"><?php echo str_replace('{total_rows}', $totalrows, str_replace('{current_rows}', $navigate[1], TEXT_LISTING_RESULTS)); ?></td>
										</tr>
									</table>
									<?php }?></td>
								</tr>
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