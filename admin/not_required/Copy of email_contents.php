<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE='email_contents';

if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) 
{
    $ddlType = stripslashes($_POST['ddlType']);
	$txtDes = stripslashes($_POST['txtDes']);
}//end if
else 
{
    $ddlType = $_POST['ddlType'];
	$txtDes = $_POST['txtDes'];
}//end else 

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=="Update")
{
		//updation for site email contents
		mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtDes)."' 
								where nLookUpCode='".addslashes($ddlType)."'") or die(mysqli_error($conn));

		$message="Settings updated";
}//end if
?>
<script language="javascript" type="text/javascript" src="../js/template.js"></script>
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
                        <td width="84%" class="heading_admn" align="left"><span class="boldtextblack">Edit Email Contents</span></td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><?php echo '<form name="frmSettings" method ="POST" action="">';?>
<table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>
                              <tr bgcolor="#FFFFFF">
                                <td width="15%" align="left" valign="top">Type</td>
                                <td width="85%" align="left" valign="top"><?php echo ShowEmailContent($ddlType);?></td>
                              </tr>
				</table>
<span id="txtHint"></span>
<?php echo '</form>';?>
</td>
                          </tr>
                        </table></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
<?php include_once('../includes/footer_admin.php');?>