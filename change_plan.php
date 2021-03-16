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

$message = ($_SESSION['succ_msg'] != '') ? $_SESSION['succ_msg'] : $message;
?>

<script language="javascript" type="text/javascript">
function validate()
{
    ddlPlan = document.getElementById('ddlPlan').value;
    
    if(ddlPlan=="")
    {
       alert("Please select a Plan!"); 
       return false;
    }
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
				<div class="innersubheader">
					<h4><?php echo HEADING_CHANGE_PLAN; ?></h4>
				</div>
				<div class="clear">&nbsp;</div>
				<div class="row">
					<div class="col-lg-12 col-sm-12 col-md-10 col-xs-12">
						<div class="table-responsive">
						<table width="100%"  border="0" cellspacing="1" cellpadding="4" class="table table-bordered">
							<form name="frmMethod" method="post" action="change_plan_payment_method.php" onsubmit="return validate()">
								<?php
								if (isset($message) && $message != '') {
									?>
									<tr align="center" >
										<td colspan="2" class="success"><b><?php echo $message; ?></b></td>
									</tr>
									<?php
									unset($_SESSION['succ_msg']);
								}
								?>
								<?php
								if (isset($_SESSION['sess_upgradeplan_message']) && $_SESSION['sess_upgradeplan_message'] != '') {
									?>
									<tr align="center" >
										<td colspan="2" class="warning"><b><?php echo $_SESSION['sess_upgradeplan_message']; ?></b></td>
									</tr>
									<?php
								}
								?>
								<tr align="center"  class="gray">
									<td width="31%" align="right"><?php echo TEXT_PLAN; ?></td>
									<td width="69%" align="left"><?php
								$plan = mysqli_query($conn, "select * from " . TABLEPREFIX . "plan P
															LEFT JOIN " . TABLEPREFIX . "plan_lang L on P.nPlanId = L.plan_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
														where P.vActive='1' and P.nPlanId!='" . $_SESSION['sess_PlanId'] . "' ORDER BY P.nPosition ASC") or
										die(mysqli_error($conn));
								if (mysqli_num_rows($plan) > 0) {
									$showButtonSub = true;
									echo '<select id="ddlPlan" name="ddlPlan" class="comm_input width1"><option value="">' . TEXT_SELECT_ONE . '</option>';
									while ($parr = mysqli_fetch_array($plan)) {
										switch ($parr['vPeriods']) {
											case "P":
												$year = TEXT_PAID;
												break;
	
											case "F":
												$year = TEXT_FREE;
												break;
										}//end switch
										?>
										<option value="<?php echo $parr['nPlanId']; ?>" <?php
										if ($parr['nPlanId'] == $_POST['ddlPlan']) {
											echo 'selected';
										}
										?>><?php echo $parr['vPlanName']; ?> ( $<?php echo $parr['nPrice']; ?>)</option>
												<?php
											}//end while loop
											echo '</select>';
										}//end if
										else {
											echo '<b>' . ERROR_NO_PLANS_TO_UPGRADE . '</b>';
											$showButtonSub = false;
										}//end else
										?></td>
								</tr>
								<?php
								if ($showButtonSub == true) {
									?>
									<tr >
										<td align="right">&nbsp;</td>
										<td align="left"><input name="btnGo" type="submit" class="submit" value="<?php echo BUTTON_CONTINUE; ?>"></td>
									</tr>
									<?php
								}//end if
								?>
							</form>
						</table>
					</div>
					</div>	
				</div>				
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>				
			</div>
		</div>  
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>