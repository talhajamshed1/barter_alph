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
//include_once('../includes/headeradmin.php');
ob_start();
include_once('../includes/config.php');
include_once('../includes/adminsession.php');
include_once('../includes/functions.php');
if($_SERVER['SERVER_PORT']=="80")
{
   $imagefolder=$rootserver;
}//end if
else
{
   $imagefolder=$secureserver;
}//end else
$PGTITLE='invalidlicense';
$message ="";
if ($_POST["btnGo"] == "Submit") {
	$txtAdminPass  = trim($_POST['txtAdminPass']);
	$txtLicenseKey  = trim($_POST['txtLicenseKey']);
	if ($txtLicenseKey != "" && $txtAdminPass != "") {
		$sqlSelect	= "SELECT * FROM ".TABLEPREFIX . "lookup WHERE vLookUpDesc='".md5($txtAdminPass)."' AND nLookUpCode ='2'";
		$res =	mysqli_query($conn, $sqlSelect);
		if(mysqli_num_rows($res) > 0){	
			if (strlen($txtLicenseKey) == '30') {
				$sql = "UPDATE ".TABLEPREFIX . "lookup SET vLookUpDesc='" . addslashes($txtLicenseKey) . "' where nLookUpCode = 'vLicenceKey'";
				mysqli_query($conn, $sql,$conn);
				header("Location:index.php");
				exit;
			}else
				$message = "Invalid key. Please enter a valid key";
		}else
			$message = "Invalid admin password. Please enter a valid admin password";
	}else
		$message = "Please enter new key";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo SITE_TITLE;?></title>
<link href="<?php echo $stylesfolder?>/<?php echo $sitestyle?>" rel="stylesheet" type="text/css">
<script language="javascript1.1" type="text/javascript">
// Finction to show license key entry textbox...
function enterNewKey(){
	document.getElementById('adminpass').style.display = '';
	document.getElementById('licensekey').style.display = '';
}
 --> 
</script>
<script language="javascript1.1" type="text/javascript">
function emptyCheck()
{
	if(document.frmLicense.txtAdminPass.value == ""){
		alert('Please enter administrator password');
		document.frmLicense.txtAdminPass.focus();
		return false;	
	}else if(document.frmLicense.txtLicenseKey.value == ""){
		alert('Please enter valid license key');
		document.frmLicense.txtLicenseKey.focus();
		return false;	
	}	
}
</script>
</head>
<body <?php if(isset($notregistered) and $notregistered== "1"){ echo "onload='loadFields();'";}?> onLoad="<?php echo $load;?><?php echo $showPageVar;?>">
<table width="100%" height="24"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="80%" height="24" valign="middle" class="topcolor">&nbsp;</td>
    <td width="20%" align="right" valign="middle" class="topcolor">&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="middle" class="logoadmin"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="18%" height="61"><img src="<?php echo $imagefolder;?>/images/<?php echo $logourl?>" width="300" height=100 border="0"></td>
                <td width="82%" align="right" class="welcome"><img src="../images/administrator.jpg" width="183" height="75"></td>
              </tr>
            </table>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="81%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="94%" height="32" class="headerbg">&nbsp;</td>
                      <td width="6%" align="right" valign="top" class="headerbg">&nbsp;</td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="84%" class="heading_admn boldtextblack" align="left">Invalid License</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top">
							<form name=frmLicense method=post action="<?php echo $_SERVER["PHP_SELF"];?>" onSubmit="return emptyCheck();">
								<table width="100%"  border="0" cellspacing="0" cellpadding="0">
									<tr><td bgcolor="#EEEEEE" colspan="3">&nbsp;</td></tr>
									<tr><td bgcolor="#EEEEEE" align="center" colspan="3" class="warning"><?php echo $message;?></td></tr>
									<tr>
										<td bgcolor="#EEEEEE" align="center" class="maintext" colspan="3">
											<p align=center>
												<b>Invalid License Key (<?php echo getLicense();?>)</b>.<br>Please contact <b>support@iscripts.com</b>
											</p>
										</td>
									</tr>
									<tr>
										<td bgcolor="#EEEEEE" align="center" class="maintext" colspan="3">Click <a href="javascript: enterNewKey();">here</a> to enter new key
										</td>
									</tr>
									<tr id="adminpass" style="display:none;">
										<td width="44%" align="right" bgcolor="#EEEEEE" class="maintext">Admin password</td>
										<td bgcolor="#EEEEEE" width="20%" align="left">
											<input name="txtAdminPass"  id="txtAdminPass" type="text" class="textbox" size="25" maxlength="40" value="<?php echo htmlentities($txtAdminPass);?>">
										</td>
										<td bgcolor="#EEEEEE" width="20%" align="left">&nbsp;</td>
									</tr>
									<tr id="licensekey" style="display:none;">
										<td width="44%" align="right" bgcolor="#EEEEEE" class="maintext">Enter new license key </td>
										<td bgcolor="#EEEEEE" width="20%" align="left">
											<input name="txtLicenseKey"  id="txtLicenseKey" type="text" class="textbox" size="40" maxlength="40" value="<?php echo htmlentities($txtLicenseKey);?>">
										</td>
										<td width="31%" align="left" bgcolor="#EEEEEE" class="maintext">
											<input type="submit" name="btnGo" value="Submit" class="submit">
										</td>
									</tr>
									<tr><td colspan="3">&nbsp;</td></tr>
									<tr><td colspan="3">&nbsp;</td></tr>
									<tr><td colspan="3">&nbsp;</td></tr>
								</table>
							</form>
						</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
<?php include_once('../includes/footer_admin.php');?>