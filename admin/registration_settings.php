<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE='registration_settings';

//-------------------------------------------------------------------------------------
//------------------------------------------------FREE TRIAL SECTION-------------------
//-------------------------------------------------------------------------------------
$act=$_GET["act"];
$ddlFree=$_POST["ddlFree"];
$txtDate=$_POST["txtDate"];

if(isset($act) && $act=="post")
{
	$sql="Update ".TABLEPREFIX."lookup  set vLookUpDesc = '$ddlFree' where  nLookUpCode = '15'";
	mysqli_query($conn, $sql) or die(mysqli_error($conn));

	if($ddlFree!="0")
	{
		$sql="Update ".TABLEPREFIX."lookup  set vLookUpDesc = DATE_ADD(CURDATE(), INTERVAL ".$txtDate." DAY) where  nLookUpCode = '16'";

		mysqli_query($conn, $sql);
         }//end if
         else
         {
			  //enable one of the payment gateway
         	if(DisplayLookUp('paypalsupport')=='YES' || DisplayLookUp('authsupport')=='YES' || DisplayLookUp('yourpaysupport')=='YES' 
         		|| DisplayLookUp('googlesupport')=='YES')
         	{
         		$sql="Update ".TABLEPREFIX."lookup  set vLookUpDesc = '0000-00-00' where  nLookUpCode = '16'";
			  }//end if 
			  else
			  {
			  	$message2='Please enable atleast one payment gateway option (using payments menu) to complete this action';
			  }//end else
		 }//end else
		 mysqli_query($conn, $sql) or die(mysqli_error($conn));
		 if($message2!='')
		 {
		 	$message2=$message2;
		 }//end if
		 else
		 {
		 	$message='Settings Updated.';
		 }//end else
}//end if
else
{
	$ddlFree=DisplayLookUp('15');
	$txt_Date=DisplayLookUp('16');
	if($txt_Date != '0000-00-00'){
		$date = date_create($txt_Date);
		$today = date_create(date("Y-m-d"));
		$diff = date_diff($date,$today);
		$txtDate = $diff->format("%a");
	} else{
		$txtDate = "";
	}
	
	
}//end else

$enableFree="0";
$enableFreeDay="0";
$sql = "Select vLookUpDesc from ".TABLEPREFIX."lookup where nLookUpCode = '15' and vLookUpDesc='1'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$currentsetting="Free registration is disabled.";
if(mysqli_num_rows($result) > 0)
{
	if(DisplayLookUp('16')!='')
	{
		$enableFreeDay=DisplayLookUp('16');
	}//end if

	if($enableFreeDay!='0000-00-00')
	{
		$currentsetting="Free registration is enabled till ".date('F d, Y',strtotime($enableFreeDay));
	}//end if
	else
	{
		$currentsetting="Free registration is enabled till ".date('F d, Y');
	}//end else
}//end if

//FREE TRIAL SECTION

$flag = false;
$var_param="";
$var_value="";
?>


<script language="javascript" type="text/javascript">
	function numericCheck()
	{
		if(isNaN(document.frmFree.txtDate.value) || document.frmFree.txtDate.value.length == 0 || document.frmFree.txtDate.value.substring(0,1) == " " || parseInt(document.frmFree.txtDate.value) < 1)
		{
			if(document.getElementById('showVal').style.display!='none')
			{
				document.frmFree.txtDate.focus();
				return false;
		}//end if
  }//end if
  else
  {
  	return true;
  }//end else
}//end function

function validateFree()
{

	xxx=numericCheck();

	if(document.frmFree.ddlFree.value=="1" && (document.frmFree.txtDate.value=="" || document.frmFree.txtDate.value=="0" ||xxx==false))
	{
		alert("Please enter a valid free trial day");
      }//end if
      else
      {
      	document.frmFree.submit();
      }//end else
}//end funciton

function checkVal(chV)
{
	if(chV=='0')
	{
		document.getElementById('showVal').style.display='none';
	}//end if
	else
	{
		document.getElementById('showVal').style.display='';
	}//end else
}//end fucniton
</script>
<div class="row admin_wrapper">
	<div class="admin_container">


		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="18%" valign="top"> <!--  Admin menu comes here -->
					<?php require("../includes/adminmenu.php"); ?>
					<!--   Admin menu  comes here ahead --></td>
					<td width="4%"></td>
					<td width="78%" valign="top">

						<table width="100%"  border="0" cellspacing="0" cellpadding="10">
							<tr>
								<td width="100%" class="heading_admn" align="left"><span class="boldtextblack">Registration Configuration Details</span></td>

							</tr>
						</table>
						<table width="100%"  border="0" cellspacing="0" cellpadding="10">
							<tr>
								<td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2">
									<tr>
										<td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
											<form name="frmFree" method="post" action="registration_settings.php?act=post">
												<?php if(isset($message) && $message!='')
												{
													?>
													<tr bgcolor="#FFFFFF">
														<td colspan="2" align="center" class="success"><b><?php echo $message;?></b></td>
													</tr>
													<?php  } else if(isset($message2) && $message2!='') {

														?>
														<tr bgcolor="#FFFFFF">
															<td colspan="2" align="center" class="warning"><b><?php echo $message2;?></b></td>
														</tr>
														<?php


													} //end if	?>		
													<tr align="left" bgcolor="#FFFFFF">
														<td colspan="2" class="subheader">Free Registration</td>
													</tr>
													<tr align="left" bgcolor="#FFFFFF">
														<td colspan="2">Current Setting : <?php echo $currentsetting;?></td>
													</tr>
													<?php if(isset($message1) && $message1!='')
													{
														?>
														<tr align="center" bgcolor="#FFFFFF">
															<td colspan="2"><?php echo $message1;?></td>
														</tr>
														<?php  }//end if?>
														<tr align="left" bgcolor="#FFFFFF">
															<td colspan="2" class="subheader">Change Settings</td>
														</tr>

														<tr align="center" bgcolor="#FFFFFF">
															<td width="33%" align="left">Enable Free Registration?</td>
															<td width="67%" align="left"><select name="ddlFree" class="textbox2" onChange="checkVal(this.value)">
																<option value="1" <?php if($ddlFree=='1'){echo 'selected';}if($ddlFree==''){echo 'selected';}?>>Yes</option>
																<option value="0" <?php if($ddlFree=='0'){echo 'selected';}?>>No</option>
															</select></td>
														</tr>
														<tr align="center" bgcolor="#FFFFFF" id="showVal" style="<?php if($ddlFree=='0'){echo 'display:none;';}?>">
															<td align="left">Number of Days</td>
															<td align="left"><input type="text" maxlength="3" size="3" class="textbox2" name="txtDate" value="<?php echo $txtDate;?>"></td>
														</tr>
														<tr align="center" bgcolor="#FFFFFF">
															<td align="left">&nbsp;</td>
															<td align="left"><input type="button" class="submit" value="Update" onClick="validateFree();"></td>
														</tr>
													</form>
												</table>
											</td>
										</tr>
									</table></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<?php include_once('../includes/footer_admin.php');?>