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
$PGTITLE='pending_settlement';

if($_POST["postback"] == "CS")
{//for changing status
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
     }//end if
	 else
	 {
         $message = "This user cannot be deactivated since s/he has got some pending transactions.";
     }//end else
  }//end if
}//end first if


$qryopt="";
if($_POST["txtSearch"] != "")
{
   $txtSearch = $_POST["txtSearch"];
}//end if
else if($_GET["txtSearch"] != "")
{
   $txtSearch = $_GET["txtSearch"];
}//end else if
if($_POST["ddlSearchType"] != "")
{
  $ddlSearchType = $_POST["ddlSearchType"];
}//end else if
else if($_GET["ddlSearchType"] != "")
{
  $ddlSearchType = $_GET["ddlSearchType"];
}//end else if

if($txtSearch != "")
{
  if($ddlSearchType == "firstname")
  {
      $qryopt .= " and vFirstName  like '" . addslashes($txtSearch) . "%'";
  }//end if
  else if($ddlSearchType == "lastname")
  {
     $qryopt .= " and vLastName like '" . addslashes($txtSearch) . "%'";
  }//end else if
  else if($ddlSearchType == "username")
  {
      $qryopt .= " and vLoginName like '" . addslashes($txtSearch) . "%'";
  }//end else if
}//end if

if(!isset($begin) || $begin =="")
{
   $begin = 0;
}

$sql="SELECT * FROM ".TABLEPREFIX."users  WHERE 1 " . $qryopt . "    order by dDateReg DESC ";
$sess_back="usersettlements.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $ddlSearchType . "&txtSearch=" . $txtSearch;

$_SESSION["backurl"] = $sess_back;
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,10,10,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);
 $sql = $sql.$navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);

if(isset($_GET['msg']) && $_GET['msg']=='s')
{
	$message = "Amount settled successfully!";
}//end if
?>
<script language="javascript" type="text/javascript">
function clickSearch()
{
        document.frmAdminMain.submit();
}//end function
</script>

<div class="row admin_wrapper">
	<div class="admin_container">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="padding_T_B_td">
                <tr>
                  <td width="18%" valign="top"> <!--  Admin menu comes here -->
		                                 <?php require("../includes/adminmenu.php"); ?>
									<!--   Admin menu  comes here ahead --></td>
					<td width="4%"></td>
                  <td width="78%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="94%" height="32" class="headerbg">&nbsp;</td>
                      <td width="6%" align="right" valign="bottom" class="headerbg"><a href="adminmain.php"><img src="../images/home-icon1.gif" width="44" height="25" border="0"></a></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="84%" class="heading_admn boldtextblack" align="left">Pending Settlements (Users)</td>
                        <td width="16%" class="heading_admn">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmAdminMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" >
<?php
$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);

if(isset($message) && $message!='')
{
?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="5" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>			
<tr bgcolor="#FFFFFF"><input type="hidden" name="userid" value="">
<input type="hidden" name="changeto" value="">
<input type="hidden" name="postback" value="">
                                <td colspan="5" align="center"><table border="0" width="100%" class="maintext">
                                        <tr>
                                                <td align="right" valign="top">
                                                Search
                                         &nbsp; <select name="ddlSearchType" class="textbox2">
                                                                                        <option value="username" <?php if($ddlSearchType== "username" || $ddlSearchType == ""){ echo("selected"); } ?>>User Name</option>
                                                                                        <option value="firstname"  <?php if($ddlSearchType == "firstname" ){ echo("selected"); } ?>>First Name</option>
                                                                                        <option value="lastname"  <?php if($ddlSearchType == "lastname" ){ echo("selected"); } ?>>Last Name</option>
                                                                        </select>
               &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo htmlentities($txtSearch)?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                                <a href="javascript:clickSearch();"><img src='../images/gobut.gif'  width="38" height="32" border='0' style="margin:0 0 0 10px; " ></a>
                                                </td>
                                        </tr>
                                </table></td>
                      </tr>  
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td width="7%">Sl No. </td>
                                <td width="25%">User Name</td>          
                                <td width="25%">Name</td>                                                                                           
                                <td width="25%"><?php echo TEXT_AMOUNT_TO_SETTLE; ?>(<?php echo CURRENCY_CODE; ?>)</td>
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
						  			
					  ?>
                              <tr bgcolor="#FFFFFF">
								<td align="center"><?php echo $cnt;?></td>
							    <td align="center"><?php echo '<a href="edituseramount.php?userid='.$arr["nUserId"].'" title="Click Here to Settle Amount" class="link1">'.restrict_string_size($arr["vLoginName"],20).'</a>';?></td>
                                <td align="center"><?php echo '<a href="edituseramount.php?userid='.$arr["nUserId"].'" title="Click Here to Settle Amount" class="link1">'.restrict_string_size($arr["vFirstName"],20).' '.restrict_string_size($arr["vLastName"],20).'</a>';?></td>
                                                    
                                 <td align="center"><?php echo '<a href="edituseramount.php?userid='.$arr["nUserId"].'" title="Click Here to Settle Amount" class="link1">'.getTotalPendingSettleAmount($arr["nUserId"]).'</a>';?></td>
                              </tr>
					<?php 
								$cnt++;
							}//end while
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="5" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
  <tr>
    <td align="left"><?php echo($navigate[2]);?></td>
    <td align="right"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
  </tr>
</table>
</td>
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