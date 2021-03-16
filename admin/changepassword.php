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
$PGTITLE='changepass';

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=="Change Password")
{
        $txtNewPassword = $_POST["txtNewPassword"];
        $txtOldPassword = $_POST["txtOldPassword"];
        if($txtOldPassword!="" AND $txtNewPassword!="")
		{
                if(DisplayLookUp('2')!='')
				{
                    
                        $password = DisplayLookUp('2');
                        if(md5($txtOldPassword) == $password)
						{
                                $sql = " UPDATE ".TABLEPREFIX."lookup  SET vLookUpDesc ='".md5($txtNewPassword)."' ";
                                $sql .=" WHERE nLookUpCode = '2'";
                                mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                $message = "Password changed successfully!";
                        }//end if
						else
						{
                                $message = "The old password you entered was incorrect! Password not updated!";
                        }//end else
                }//end if
        }//end if
}//end first if
?>
<script language="JavaScript" type="text/javascript">
function checkNewPasswordEntered()
{
        var frm = window.document.frmChangePassword;
        if(frm.txtNewPassword.value == ""  || frm.txtConfirmNewPassword.value == "")
		{
                return false;
        }//end if
        return true;
}//end fucntion

function isNewPasswordsValid()
{
        var frm = window.document.frmChangePassword;
        if(frm.txtNewPassword.value.length < 6)
		{
                alert("New Password should be atleast six characters long.");
                frm.txtNewPassword.focus();
                return false;
        }else if(frm.txtConfirmNewPassword.value != frm.txtNewPassword.value){
                alert("New Password and Confirm New Password should match.");
                frm.txtConfirmNewPassword.select();
                frm.txtConfirmNewPassword.focus();
                return false;
        }
        return true;
}//end function

function validateChangePasswordForm()
{
        var frm = window.document.frmChangePassword;
        if(frm.txtOldPassword.value == ""){
                alert("Please enter Old password");
                frm.txtOldPassword.focus();
                return false;
        }else if(! checkNewPasswordEntered()){
                alert("Please enter New Password and Confirm New Password.");
                frm.txtNewPassword.focus();
                return false;
        }else if(!isNewPasswordsValid()){
                return false;
        }
        return true;
}//end function
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
                        <td width="100%" class="heading_admn boldtextblack" align="left">Change Password</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                      <tr>
                        <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmChangePassword" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateChangePasswordForm();">
<?php if(isset($message) && $message!='')
	  {
?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr bgcolor="#FFFFFF">
                                <td width="24%" align="left">Old Password <span class="warning">*</span></td>
                                <td width="76%"><INPUT type="password" name="txtOldPassword" size="40" maxlength="100" value="" class="textbox2"/></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">New Password <span class="warning">*</span></td>
                                <td><INPUT type="password" name="txtNewPassword" size="40" maxlength="100" class="textbox2" /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Confirm New Password <span class="warning">*</span></td>
                                <td><INPUT type="password"  name="txtConfirmNewPassword" size="40" maxlength="100" class="textbox2"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td><input type="submit" name="btnSubmit" value="Change Password" class="submit"></td>
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