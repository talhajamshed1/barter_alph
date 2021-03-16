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
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE='settelemnt';


$cashid = $_GET["cashid"];

$sqlcashdetails ="SELECT c.nCashTxnId,c.nUserId,u.vLoginName,c.nAmount,c.dDate,c.vMode,c.vModeNo FROM ".TABLEPREFIX."cashtxn c,".TABLEPREFIX."users u WHERE c.nUserId=u.nUserId and c.nCashTxnId='$cashid'";

$resultcashdetails  = mysqli_query($conn, $sqlcashdetails ) or die(mysqli_error($conn));
$rowcash = mysqli_fetch_array($resultcashdetails);

$Name = $rowcash["vLoginName"];
$Amount = $rowcash["nAmount"];
$Date = $rowcash["dDate"];
$Mode = $rowcash["vMode"];
$Reference = $rowcash["vModeNo"];
?>
<script type="text/javascript" language="javascript">
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
                                    <td width="4%"></td>
                  <td width="78%" valign="top">
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="100%" class="heading_admn boldtextblack" align="left">Settlement Details</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                      <tr>
                        <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr align="right" bgcolor="#FFFFFF">
                                <td colspan="2"><a href="javascript:history.back();"><b>Back</b></a>&nbsp;</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="20%" align="left">Name</td>
                                <td width="80%" align="left"><?php echo $Name?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Total Amount</td>
                                <td align="left"><?php echo $Amount?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Date</td>
                                <td align="left"><?php echo $Date?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Payment Mode</td>
                                <td align="left"><?php echo $Mode?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Reference</td>
                                <td align="left"><?php echo $Reference?></td>
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