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
$PGTITLE='users';

//delete user
if(isset($_GET['mode']) && $_GET['mode']=='delete')
{
	mysqli_query($conn, "update ".TABLEPREFIX."users set vDelStatus='1' where nUserId='".$_GET['id']."'") or die(mysqli_error($conn));
        if($_GET['id']==$_SESSION['guserid'])
        {
           $_SESSION['user_status'] ="1";
        }
        
}//end if

$msg = $_REQUEST['msg'];

if($msg!='')
{
    $message    = "User added successfully";
}
//for changing status
if($_POST["postback"] == "CS")
{
        $userid = $_POST["userid"];
        if($_POST["changeto"] == "A")
		{
                $newstatus = "1";
        }//end if
		else if($_POST["changeto"] == "D")
		{
                $newstatus = "0";
        }//end else if
        if(($_POST["changeto"] == "D") || ($_POST["changeto"] == "A"))
		{
                if(canUserBeDeactivated($userid))
				{
                        $sqlcs = "UPDATE ".TABLEPREFIX."users   SET vStatus   = '$newstatus' where nUserId  ='". addslashes($_POST["userid"]) ."'";  
                        mysqli_query($conn, $sqlcs) or die(mysqli_error($conn));
                                               
                        
                    if($_POST["userid"]==$_SESSION['guserid'])
                    {
                       $_SESSION['user_status'] =$newstatus;
                    }    
                        
                }//end if
				else
				{
                        $message = "This user cannot be deactivated since s/he has got some pending transactions.";
                }//end else
        }//end fi
}//end first if

$qryopt="";
if($_POST["txtSearch"] != "")
{
        $txtSearch = $_POST["txtSearch"];
}//end if
else if($_GET["txtSearch"] != "")
{
        $txtSearch = $_GET["txtSearch"];
}//end else
if($_POST["ddlSearchType"] != "")
{
        $ddlSearchType = $_POST["ddlSearchType"];
}//end if
else if($_GET["ddlSearchType"] != "")
{
        $ddlSearchType = $_GET["ddlSearchType"];
}//end else if

//from edit user amount
if(isset($_GET['txtEditSearch']) && $_GET['txtEditSearch']!='')
{
	$qryopt .= " AND u.nUserId='".$_GET['txtEditSearch']."'";
}//end if

if($txtSearch != ""){

        if($ddlSearchType == "username"){
                $qryopt .= " AND u.vLoginName like '" . addslashes($txtSearch) . "%'";
        }else if($ddlSearchType == "affname"){
                $qryopt .= " AND a.vLoginName like '" . addslashes($txtSearch) . "%'";
        }else if($ddlSearchType == "city"){
                $qryopt .= " AND u.vCity  like '" . addslashes($txtSearch) . "%'";
        }else if($ddlSearchType == "date"){
                $date = $txtSearch;
                //$arr = split("/",$date);
                $arr = explode("/",$date);
                if(strlen($arr[0]) < 2){
                        $month = "0".$arr[0];
                }else{
                        $month = $arr[0];
                }
                if(strlen($arr[1]) < 2){
                        $day = "0".$arr[1];
                }else{
                        $day = $arr[1];
                }
                $year = $arr[2];
                $newdate = $year ."-". $month ."-". $day;
                $qryopt .= " AND u.dDateReg  like '" . addslashes($newdate) . "%'";
        }
}//end first if

if(!isset($begin) || $begin =="")
{
        $begin = 0;
}//end if

$sql    = "SELECT u.*,a.nAffiliateId , a.vLoginName as affname  FROM ".TABLEPREFIX."users u LEFT OUTER JOIN ".TABLEPREFIX."affiliate a ON u.nAffiliateId = a.nAffiliateId WHERE u.vDelStatus='0'" . $qryopt . "  order by u.dDateReg DESC ";

$sess_back="users.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $ddlSearchType . "&txtSearch=" . $txtSearch;

$_SESSION["backurl"] = $sess_back;
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,10,10,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

$sql = $sql.$navigate[0];

$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<script LANGUAGE="javascript" type="text/javascript">
function clickSearch()
{
        document.frmAdminMain.submit();
}
function ChangeStatus(id,status){
        var frm = document.frmAdminMain;
        if(status == "A"){
                changeto = "deactivate";
        }else{
                changeto = "activate";
        }
        if(confirm("Are you sure you want to "+ changeto +" this user?")){
                frm.changeto.value=status;
                frm.userid.value=id;
                frm.postback.value="CS";
                frm.submit();
        }
}
</script>

<div class="row admin_wrapper">
	<div class="admin_container">

 
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="19%" valign="top"> <!--  Admin menu comes here -->
		                                 <?php require("../includes/adminmenu.php"); ?>
									<!--   Admin menu  comes here ahead --></td>
                                    <td width="4%"></td>
                  <td width="81%" valign="top">
             
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                        
                      <tr>
                        <td width="84%" class="heading_admn boldtextblack" align="left">Registered Users</td>
                        <td align="right" class="heading_admn"><a href="adduser.php" class="AddLinks"><b>Add User</b></a></td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td align="left" valign="top">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2">
<form  name="frmAdminMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" >
                           
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="8" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							
<tr bgcolor="#FFFFFF"><input type="hidden" name="userid" value="">
                           <input type="hidden" name="changeto" value="">
                           <input type="hidden" name="postback" value="">
                                <td colspan="8" align="center"><table border="0" width="100%" class="maintext2">
                                    <tr>
                                      <td width="82%" align="right" valign="top">Search
                                        &nbsp;<select name="ddlSearchType" class="textbox2">
                                        <option value="username" <?php if($ddlSearchType== "username" || $ddlSearchType == ""){ echo("selected"); } ?>>User Name</option>
                                        <option value="city" <?php if($ddlSearchType == "city"){ echo("selected"); } ?>>City</option>
                                        <option value="date" <?php if($ddlSearchType== "date"){ echo("selected"); } ?>>Date Registered(mm/dd/yyyy)</option>
                                        <!--<option value="affname" <?php if($ddlSearchType== "affname"){ echo("selected"); } ?>>Affiliate Name</option>-->
                                        </select>
                                        &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo htmlentities($txtSearch)?>" autocomplete="off"  onKeyPress="if(window.event.keyCode == '13'){ return false; }"  class="textbox2" >                                                </td>
                                        <td width="6%" align="left" valign="middle">
                                            <a href="javascript:clickSearch();" class="link_style2">GO</a>                                                
                                        </td>
                                    </tr>
                                </table>
                                </td>
                            </tr>  
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td width="7%" align="center">Sl No. </td>
                                <td width="28%" align="center">User Name   </td>
                                <td width="12%" align="center">City</td>
                                <td width="18%" align="center">Date of Registration</td>
                                <td width="13%" align="center">Reference</td>
                                <td width="13%" align="center">Plan Name </td>
                                <td width="14%" align="center">Active</td>
                                <td width="8%" align="center">Delete</td>
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
								$condReg="where plan_id='".$arr['nPlanId']."' and lang_id = '" . $_SESSION['lang_id'] . "'";
								$PlanName=fetchSingleValue(select_rows(TABLEPREFIX.'plan_lang','vPlanName',$condReg),'vPlanName');
                                                                
                                                                
								//checking status
								switch($arr["vStatus"])
								{
									case "1":
                                                                             $changestatuslink = "ChangeStatus(".$arr['nUserId'].",'D');";
                                                                             $statustextorlink = "<b>No</b><br>(&nbsp;<a href=javascript:$changestatuslink class='activate'>Activate Now</a>&nbsp;)";
                                                                             break;

                                                                        case "2":
                                                                             $changestatuslink = "ChangeStatus(".$arr['nUserId'].",'A');";
                                                                             $statustextorlink = "<b>Yes</b><br>(&nbsp;<a href=javascript:$changestatuslink class='activate'>Deactivate Now</a>&nbsp;)";
                                                                             break;

                                                                        case "0":
                                                                             $changestatuslink = "ChangeStatus(".$arr['nUserId'].",'A');";
									     $statustextorlink = "<b>Yes</b><br>(&nbsp;<a href=javascript:$changestatuslink class='activate'>Deactivate Now</a>&nbsp;)";
                                                                             break;
                                                                         default :
                                                                             $statustextorlink = "<font color='#CD0000'>User didn't activate the account via email link</font>";



								}//end switch
					  ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="center"><?php echo $cnt;?></td>
                                <td align="center"><?php echo "<a href='edituser.php?userid=".$arr["nUserId"]."' title='Click here to edit'>".restrict_string_size(($arr["vLoginName"]),10)."</a>";?></td>
                                <td align="center"><?php echo "<a href='edituser.php?userid=".$arr["nUserId"]."' title='Click here to edit'>".restrict_string_size($arr['vCity'],10)."</a>";?></td>
                                <td align="center"><?php echo "<a href='edituser.php?userid=".$arr["nUserId"]."' title='Click here to edit'>".date('F d, Y',strtotime($arr["dDateReg"]))."</a>";?></td>
                                <td align="center"><?php echo "<a href='edituser.php?userid=".$arr["nUserId"]."' title='Click here to edit'>".(($arr['vAdvEmployee'] != NULL)?restrict_string_size($arr['vAdvEmployee'],10):'')."</a>";?></td>
                                <td align="center"><?php echo $PlanName;?></td>
                                <td align="center" valign="middle"><?php echo $statustextorlink;?></td>
                                <td align="center"><a href="users.php?id=<?php echo $arr['nUserId'];?>&mode=delete" onClick="return confirm('Are you sure want to delete?');">Delete</a></td>
                              </tr>
					<?php 
								$cnt++;
							}//end while
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="8" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
  <tr>
    <td align="left"><?php echo($navigate[2]);?></td>
    <td align="right"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
  </tr>
</table></td>
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