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
$PGTITLE='affiliates';

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
}//end if
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
}//end if

$sql="SELECT nAffiliateId,vLoginName,vFirstName,vLastName,nAmount  FROM ".TABLEPREFIX."affiliate  WHERE nAmount <> 0 " . $qryopt . "    order by nAffiliateId DESC ";

$sess_back="affsettlements.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $ddlSearchType . "&txtSearch=" . $txtSearch;
//$sess_back="users.php?begin=" . $_GET[begin] . "&num=" . $_GET[num] . "&numBegin=" . $_GET[numBegin] . "&ddlSearchType=" . $ddlSearchType . "&txtSearch=" . $txtSearch;

$_SESSION["backurl"] = $sess_back;
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,10,10,"&ddlSearchType=$ddlSearchType&txtSearch=$txtSearch&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql.$navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
?>
<script language="javascript" type="text/javascript">
function clickSearch()
{
        document.frmAdminMain.submit();
}

</script>

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
                        <td width="84%" class="heading_admn boldtextblack" align="left">Pending Settlements (Affiliates)</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmAdminMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" >
<input type="hidden" name="affid" value="">
<input type="hidden" name="changeto" value="">
<input type="hidden" name="postback" value="">
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
<tr bgcolor="#FFFFFF">
                                <td colspan="5" align="center"><table border="0" width="100%" class="maintext">
                                        <tr>
                                                <td valign="top" align="right">
                                                Search
                                         &nbsp; <select name="ddlSearchType" class="textbox2">
                                                                                        <option value="username" <?php if($ddlSearchType== "username" || $ddlSearchType == ""){ echo("selected"); } ?>>Affiliate Name</option>
                                                                                        <option value="firstname"  <?php if($ddlSearchType == "firstname" ){ echo("selected"); } ?>>First Name</option>
                                                                                        <option value="lastname"  <?php if($ddlSearchType == "lastname" ){ echo("selected"); } ?>>Last Name</option>
                                                                                </select>
               &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                                </td>
                                                <td align="left" valign="baseline">
                                                <a href="javascript:clickSearch();"><img src='../images/gobut.gif'  width="20" height="20" border='0' ></a>
                                                </td>
                                        </tr>
                                </table></td>
                      </tr>  
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td width="7%">Sl No. </td>
                                <td width="16%">Affiliate Name </td>
                                <td width="19%">First Name </td>
                                <td width="19%">Last Name </td>
                                <td width="19%">Amount</td>
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
								<td class="maintext"><?php echo "<a href='editaffamount.php?affid=".$arr["nAffiliateId"]."'>".htmlentities($arr["vLoginName"])."</a>";?></td>
                                <td><?php echo htmlentities($arr["vFirstName"]);?></td>
                                <td><?php echo htmlentities($arr["vLastName"]);?></td>
                                <td><?php echo htmlentities($arr["nAmount"]);?></td>
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
<?php include_once('../includes/footer_admin.php');?>