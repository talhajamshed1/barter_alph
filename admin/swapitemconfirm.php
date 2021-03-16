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
$PGTITLE='swapitemconfirm';


if($_GET["mode"] == "delete")
{
   if($_GET["flag"] == "true")
   {
      $var_message="The Item has been deleted.";
   }//end if
   else if($_GET["flag"] == "false")
   {
      $var_message="The Item cannot be deleted.";
   }//end else if
}//end if

else if($_GET["mode"] == "edit")
{
   if($_GET["flag"] == "true")
   {
      $var_message="The Item has been edited.";
   }//end if
   else if($_GET["flag"] == "false")
   {
      $var_message="The Item cannot be edited.";
   }//end else if
}//end if
?>
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
                        <td width="84%" class="heading_admn boldtextblack" align="left">Swap Item Confirm</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmSettings" method ="POST" enctype="multipart/form-data" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateSettingsForm();">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr bgcolor="#FFFFFF">
                                <td align="center"><?php echo $var_message?></td>
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
<?php include_once('../includes/footer_admin.php');?>