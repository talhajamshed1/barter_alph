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
$PGTITLE='client_modules';

$btntxt = 'Save Details';
//updation
if(isset($_POST['savechange']) && $_POST['savechange']=='Save Details')
{ 
	$categoryIdArr = $_POST['modules'];
	if (is_array($categoryIdArr))
	{
		foreach ($categoryIdArr as $id)
		{
			$vActive='1';
			$parent_id=mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."client_module_category where nCategoryId='".$id."'") 
											or die(mysqli_error($conn));
					
			//checking parent is vActive or not
			if(mysqli_num_rows($parent_id)>0)
			{
				$parid=mysqli_result($parent_id,0,'nParentId');
				$nParentId=mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."client_module_category where nCategoryId='".$parid."'") 
													or die(mysqli_error($conn));
				if(mysqli_num_rows($nParentId)>0)
				{
					$vActiveornot=mysqli_result($nParentId,0,'vActive');
				}//end if
			}//end if
			if($vActiveornot=='1')
			{
				//update both parent n child status
				mysqli_query($conn, "UPDATE ".TABLEPREFIX."client_module_category SET vActive='".$vActive."' 
									where nCategoryId='".$id."'") or die(mysqli_error($conn));

				mysqli_query($conn, "UPDATE ".TABLEPREFIX."client_module_category SET vActive='".$vActive."' 
									where nParentId='".$id."'") or die(mysqli_error($conn));
			}//end if
		}//end foreach

		$sql=mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."client_module_category where nCategoryId NOT IN ('1','2','3','4','8')") or die(mysqli_error($conn));
		if(mysqli_num_rows($sql)>0)
		{
			for($i=0;$i<mysqli_num_rows($sql);$i++)
			{
				$catid=mysqli_result($sql,$i,'nCategoryId');
				$catname=mysqli_result($sql,$i,'vCategoryTitle');
				$catvActive=mysqli_result($sql,$i,'vActive');
				if(!in_array($catid,$categoryIdArr))
				{
					$vActive='0';
					mysqli_query($conn, "UPDATE ".TABLEPREFIX."client_module_category SET vActive='".$vActive."' where 
											nCategoryId='".$catid."' and nParentId!='0'") or die(mysqli_error($conn));
				}//end if
			}//end for loop
		}//end if
				
		mysqli_query($conn, "UPDATE ".TABLEPREFIX."client_module_category SET vActive='1' where nParentId NOT IN 
								('19','20','21','22') and nTmp_status='0'") or die(mysqli_error($conn));
	}//end array if

	$message='Links modified successfully.';
}//end if
?>

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
                        <td width="100%" class="heading_admn" align="left"><span class="boldtextblack">Active Modules</span></td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                      <tr>
                        <td align="left" valign="top" class="noborderbottm" ><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmSettings" method ="POST" action="<?php echo $_SERVER['PHP_SELF']?>">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>
 <tr bgcolor="#FFFFFF">
                                <td width="36%" align="left">&nbsp;</td>
                                <td width="64%" align="right"><input class="submit" type="submit" value="<?php echo $btntxt; ?>" name="savechange"></td>
                              </tr>
<tr bgcolor="#FFFFFF">
               <td width="36%" align="left" valign="top">Client Module Access</td>
                                <td width="64%" align="left" valign="top"><?php list_clinet_modules(TABLEPREFIX);?></td>
                              </tr>
							  <tr bgcolor="#FFFFFF">
                                <td width="36%" align="left">&nbsp;</td>
                                <td width="64%"><input class="submit" type="submit" value="<?php echo $btntxt; ?>" name="savechange"></td>
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