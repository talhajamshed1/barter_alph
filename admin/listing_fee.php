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
$PGTITLE='settings';

function isNotNull($value)
{
    if (is_array($value)) 
	{
        if (sizeof($value) > 0) 
		{
            return true;
        }//end if
		else 
		{
            return false;
        }//end else
    }//end if
	else 
	{
        if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) 
		{
            return true;
        }//end if
		else 
		{
            return false;
        }//end else
    }//end else
}//end function

$flag = false;
$var_param="";
$var_value="";

if (function_exists('get_magic_quotes_gpc')) 
{
	$txtCommission = stripslashes($_POST['txtCommission']);
	$txtCommissionFree = stripslashes($_POST['txtCommissionFree']);
	$radType = stripslashes($_POST['radType']);
}//end if
else 
{
	$txtCommission = $_POST['txtCommission'];
	$txtCommissionFree = $_POST['txtCommissionFree'];
	$radType = $_POST['radType'];
}//end else 

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=="Change Settings")
{
		//updation for Commission
		mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtCommission)."' where nLookUpCode='7'") or die(mysqli_error($conn));
		
		//updation for Commission free limit
		mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtCommissionFree)."' where nLookUpCode='8'") or die(mysqli_error($conn));
	
		//updation for listing fee type
		mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($radType)."' where nLookUpCode='Listing Type'") or die(mysqli_error($conn));
	
		$flag = true;
        $message="Settings updated";
}//end if
else
{
	$txtCommission=DisplayLookUp('7');
	$txtCommissionFree=DisplayLookUp('8');
	$radType=DisplayLookUp('Listing Type');
}//end if            
?>
<link href="../styles/tabcontent.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function validateSettingsForm()
{
     var frm = window.document.frmSettings;

	 if(isNaN(frm.txtCommission.value) || frm.txtCommission.value.length == 0 || frm.txtCommission.value.substring(0,1) == " ")
	 {
          frm.txtCommission.value=0;
          frm.txtCommission.focus();
          alert('Please enter a positive number');
          return false;
     }//end if
	 if(isNaN(frm.txtCommissionFree.value) || frm.txtCommissionFree.value.length == 0 || frm.txtCommissionFree.value.substring(0,1) == " ")
	 {
          frm.txtCommissionFree.value=0;
          frm.txtCommissionFree.focus();
          alert('Please enter a positive number');
          return false;
     }//end if
    return true;
}

//function for disable/enable
function showSettings(nVar)
{
	if(nVar=='s')
	{
		document.getElementById('ShowSingle').style.display='';
		document.getElementById('ShowSingle2').style.display='';
		document.getElementById('ShowSingle3').style.display='none';
	}//end if
	else
	{
		document.getElementById('ShowSingle').style.display='none';
		document.getElementById('ShowSingle2').style.display='none';
		document.getElementById('ShowSingle3').style.display='';
	}//end else
}//end function

</script>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="19%" valign="top"> <!--  Admin menu comes here -->
		                                 <?php require("../includes/adminmenu.php"); ?>
									<!--   Admin menu  comes here ahead --></td>
                  <td width="81%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="94%" height="32" class="headerbg">&nbsp;</td>
                      <td width="6%" align="right" valign="top" class="headerbg"><a href="adminmain.php"><img src="../images/home-icon1.gif" width="44" height="25" border="0"></a></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="84%" class="heading_admn" align="left"><span class="boldtextblack">Listing Fee Details</span></td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
					<?php include_once('../includes/settings_menu.php');?>
					<div class="tabcontentstyle">
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmSettings" method="post" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateSettingsForm();">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="3" align="center" class="success"><?php echo $message;?></td>
                              </tr>
<?php  }//end if							
?>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td colspan="2"><input type="submit" name="btnSubmit" value="Change Settings" class="submit"></td>
                      </tr>
					          <tr bgcolor="#FFFFFF">
					            <td align="left">Combination</td>
					            <td colspan="2"><input type="radio" name="radType" value="s" <?php if($radType=='s'){echo 'checked';}if($radType==''){echo 'checked';}?> onClick="showSettings('s');">Single Amount
								<input type="radio" name="radType" value="m" <?php if($radType=='m'){echo 'checked';}?> onClick="showSettings('m');">Amount Range</td>
			            </tr>
			            <tr bgcolor="#FFFFFF" id="ShowSingle" style="<?php if($radType!='s'){echo 'display:none;';}?>">
                                <td width="40%" align="left">Listing Price (%)</td>
                                <td colspan="2"><input name="txtCommission" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtCommission);?>"></td>
                      </tr>
							  <tr bgcolor="#FFFFFF" id="ShowSingle2" style="<?php if($radType!='s'){echo 'display:none;';}?>">
                                <td width="40%" align="left">Listing price below which no commission is charged</td>
                                <td colspan="2"><input name="txtCommissionFree" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtCommissionFree);?>"></td>
                              </tr>
							   <tr bgcolor="#FFFFFF" id="ShowSingle3" style="<?php if($radType!='m'){echo 'display:none;';}?>">
                                <td width="40%" align="left">&nbsp;</td>
                                <td colspan="2"><a href="listing_combinations.php"><b>Click Here to Manage Range</b></a></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td colspan="2"><input type="submit" name="btnSubmit" value="Change Settings" class="submit"></td>
                      </tr>
							  </form>
                            </table>
</td>
                          </tr>
                        </table></td>
                      </tr>
                    </table></div>
                  </td>
                </tr>
</table>
<?php include_once('../includes/footer_admin.php');?>