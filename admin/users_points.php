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
$PGTITLE=ClientFilePathName($_SERVER['PHP_SELF']);

if(!isset($begin) || $begin =="")
{
        $begin = 0;
}//end if
											
$sql="SELECT u.vLoginName,u.nUserId,uc.nPoints FROM ".TABLEPREFIX."users u LEFT JOIN ".TABLEPREFIX."usercredits uc on u.nUserId=uc.nUserId 
											 WHERE u.vDelStatus='0' ORDER BY u.dDateReg DESC";

$sess_back="users_points.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $ddlSearchType . "&txtSearch=" . $txtSearch;

$_SESSION["backurl"] = $sess_back;
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,10,10,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

$sql = $sql.$navigate[0];

$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
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
         
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="100%" class="heading_admn boldtextblack" align="left">User <?php echo POINT_NAME;?></td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="4" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td width="8%" align="center">Sl No. </td>
                                <td width="49%" align="center">User Name   </td>
                                <td width="24%" align="center"><?php echo POINT_NAME;?></td>
                                <td width="19%" align="center">Action</td>
                      </tr>
					  <?php
					     if(mysqli_num_rows($rs)>0)
						 {
						  	switch($_GET['begin'])
							{
								case "":
									$cnt=1;
								break;
								
								default:
									$cnt=$_GET['begin']+1;
								break;
							}//end switch
							
							while ($arr = mysqli_fetch_array($rs))
						  	{
								switch($arr["nPoints"])
								{
									case "":
										$arr["nPoints"]=0;
									break;
									
									default:
										$arr["nPoints"]=$arr["nPoints"];
									break;
								}//end switch
					  ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="center"><?php echo $cnt;?></td>
                                <td align="center"><?php echo "<a href='edit_users_points.php?nUserId=".$arr["nUserId"]."&uName=".base64_encode($arr["vLoginName"])."&mode=edit' title='Click here to edit'>".restrict_string_size($arr["vLoginName"],15)."</a>";?></td>
                                <td align="center"><?php echo "<a href='edit_users_points.php?nUserId=".$arr["nUserId"]."&uName=".base64_encode($arr["vLoginName"])."&mode=edit' title='Click here to edit'>".htmlentities($arr["nPoints"])."</a>";?></td>
                                <td align="center"><a href="edit_users_points.php?nUserId=<?php echo $arr['nUserId'];?>&uName=<?php echo base64_encode($arr["vLoginName"]);?>&mode=edit">Edit</a></td>
                              </tr>
					<?php 
								$cnt++;
							}//end while
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="4" class="noborderbottm" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
  <tr>
    <td align="left"><?php echo($navigate[2]);?></td>
    <td align="right"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
  </tr>
</table></td>
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