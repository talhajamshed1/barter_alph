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
$PGTITLE='user';

$userid = $_GET["userid"];


$sqluserdetails = "SELECT * FROM ".TABLEPREFIX."users  WHERE  nUserId  = '".$userid."'";
$resultuserdetails  = mysqli_query($conn, $sqluserdetails ) or die(mysqli_error($conn));
$rowuser = mysqli_fetch_array($resultuserdetails);
$txtUserName = $rowuser["vLoginName"];
$txtFirstName = $rowuser["vFirstName"];
$txtLastName = $rowuser["vLastName"];
$txtAddress1 = $rowuser["vAddress1"];
$txtAddress2 = $rowuser["vAddress2"];
$txtCity = $rowuser["vCity"];
$txtState = $rowuser["vState"];
$txtZIP = $rowuser["nZip"];
$txtPhone = $rowuser["vPhone"];
$txtFAX = $rowuser["vFax"];
$txtEmail = $rowuser["vEmail"];
$ddlCountry = $rowuser["vCountry"];
$ddlGender = $rowuser["vGender"];
$txtURL = $rowuser["vUrl"];
$ddlEducation = $rowuser["vEducation"];
$txtDescription = $rowuser["vDescription"];
$txtAdvSource = $rowuser["vAdvSource"];
$txtAdvEmployee = $rowuser["vAdvEmployee"];
?>
<script language="javascript" type="text/javascript">
function loadFields(){
}
function checkNewPasswordEntered(){
}
function isAnyPasswordEntered(){
}
function isNewPasswordsValid(){
}
function validateChangePasswordForm(){
}
function validateProfileForm()
{
}

function ChangeStatus(id,status){
}

</script>
<div class="row admin_wrapper">
	<div class="admin_container">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="18%" valign="top"> <!--  Admin menu comes here -->
		                                 <?php require("../includes/adminmenu.php"); ?>
									<!--   Admin menu  comes here ahead --></td>
				<td width="4%" valign="top"></td>
                  <td width="78%" valign="top">
				  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
                    </tr>
                  </table>
                    
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="100%" class="heading_admn boldtextblack" align="left"><font class="pageheading">Edit User Pofile</font></td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="admin_tble_2">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="right"><a href="javascript:history.back();"><strong>Back</strong></a></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><strong>General Details</strong></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="20%" align="left">User Name</td>
                                <td width="80%" align="left"><?php echo $txtUserName?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">First Name <span class="warning">*</span></td>
                                <td align="left"><?php echo $txtFirstName?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Last Name</td>
                                <td align="left"><?php echo $txtLastName?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Address Line 1 <span class="warning">*</span></td>
                                <td align="left"><?php echo $txtAddress1?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Address Line 2</td>
                                <td align="left"><?php echo $txtAddress2?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">City <span class="warning">*</span></td>
                                <td align="left"><?php echo $txtCity?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">State <span class="warning">*</span></td>
                                <td align="left"><?php echo $txtState?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Country</td>
                                <td align="left"><?php echo $ddlCountry?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">ZIP <span class="warning">*</span></td>
                                <td align="left"><?php echo $txtZIP ?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Phone <span class="warning">*</span></td>
                                <td align="left"><?php echo $txtPhone?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">FAX</td>
                                <td align="left"><?php echo $txtFAX?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Email <span class="warning">*</span></td>
                                <td align="left"><?php echo $txtEmail?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">URL</td>
                                <td align="left"><?php echo $txtURL?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><strong>Other Details</strong></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Gender</td>
                                <td align="left"><?php echo $ddlGender?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Education</td>
                                <td align="left"><?php echo $ddlEducation?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Heard From</td>
                                <td align="left"><?php echo $txtAdvSource?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Description</td>
                                <td align="left"><?php echo $txtDescription?></td>
                              </tr>
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