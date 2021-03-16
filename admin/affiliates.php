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
$PGTITLE='affiliates';


if($_POST["postback"] == "CS")
{//for changing status
   $affid = $_POST["affid"];
   if($_POST["changeto"] == "A")
   {
      $newstatus = "0";
   }//end if
   else if($_POST["changeto"] == "D")
   {
       $newstatus = "1";
   }//end else if
   
   if(($_POST["changeto"] == "D") || ($_POST["changeto"] == "A"))
   {
      if(canAffBeDeactivated($affid))
	  {
         $sqlcs = "UPDATE ".TABLEPREFIX."affiliate   SET vDelStatus   = '$newstatus' where nAffiliateId  ='".$affid."'";
         mysqli_query($conn, $sqlcs) or die(mysqli_error($conn));
      }//end if
	  else
	  {
          $message = "This affiliate cannot be deactivated since s/he has got some pending transactions.";
      }//end else
   }//end if
}//end if

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
            $qryopt .= "  WHERE vFirstName  like '" . addslashes($txtSearch) . "%'";
        }//end if
		else if($ddlSearchType == "username")
		{
           $qryopt .= "  WHERE vLoginName like '" . addslashes($txtSearch) . "%'";
        }//end else if
		else if($ddlSearchType == "firstname")
		{
           $qryopt .= "  WHERE vLastName like '" . addslashes($txtSearch) . "%'";
        }//end else if
		else if($ddlSearchType == "city")
		{
           $qryopt .= "  WHERE vCity  like '" . addslashes($txtSearch) . "%'";
        }//end else if
		else if($ddlSearchType == "state")
		{
           $qryopt .= "  WHERE vState  like '" . addslashes($txtSearch) . "%'";
        }//end else if
		else if($ddlSearchType == "country")
		{
           $qryopt .= "  WHERE vCountry  like '" . addslashes($txtSearch) . "%'";
        }//end else if
		else if($ddlSearchType == "date")
		{
            $date = $txtSearch;
           // $arr = split("/",$date);
            $arr = explode("/",$date);
            if(strlen($arr[0]) < 2)
			{
                 $month = "0".$arr[0];
            }//end if
			else
			{
                $month = $arr[0];
            }//end else
			
			if(strlen($arr[1]) < 2)
			{
               $day = "0".$arr[1];
            }//end if
			else
			{
              $day = $arr[1];
            }//end else
            
			$year = $arr[2];
            $newdate = $year ."-". $month ."-". $day;
            $qryopt .= "  WHERE dDateReg  like '" . addslashes($newdate) . "%'";
       }//end else
}//end if

$sql="SELECT * FROM ".TABLEPREFIX."affiliate " . $qryopt . "  order by nAffiliateId  DESC ";

if(!isset($begin) || $begin =="")
{
  $begin = 0;
}
$sess_back="affiliates.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&ddlSearchType=" . $ddlSearchType . "&txtSearch=" . $txtSearch;

$_SESSION["backurl"] = $sess_back;
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,5,5,"&ddlSearchType=$ddlSearchType&txtSearch=$txtSearch&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql.$navigate[0];

$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
?>
<script language="javascript" type="text/javascript">
function clickSearch()
{
        document.frmAdminMain.submit();
}
function ChangeStatus(id,status){
        var frm = document.frmAdminMain;
        if(status == "A"){
                changeto = "activate";
        }else{
                changeto = "deactivate";
        }
        if(confirm("Are you sure you want to "+ changeto +" this user?")){
                frm.changeto.value=status;
                frm.affid.value=id;
                frm.postback.value="CS";
                frm.submit();
        }
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
                        <td width="84%" class="heading_admn boldtextblack" align="left">Categories</td>
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
                                <td colspan="6" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>			
<tr bgcolor="#FFFFFF">
                                <td colspan="6" align="center"><table border="0" width="100%" class="maintext">
                                        <tr>
                                                <td valign="top" align="right">
                                                Search
                                         &nbsp; <select name="ddlSearchType" class="textbox2">
                                                                                        <option value="username" <?php if($ddlSearchType== "username" || $ddlSearchType == ""){ echo("selected"); } ?>>Login Name</option>
                                                                                        <option value="firstname"  <?php if($ddlSearchType == "firstname" ){ echo("selected"); } ?>>First Name</option>
                                                                                        <option value="lastname"  <?php if($ddlSearchType == "lastname" ){ echo("selected"); } ?>>Last Name</option>
                                                                                        <option value="city" <?php if($ddlSearchType == "city"){ echo("selected"); } ?>>City</option>
                                                                                        <option value="state" <?php if($ddlSearchType == "state"){ echo("selected"); } ?>>State</option>
                                                                                        <option value="country" <?php if($ddlSearchType== "country"){ echo("selected"); } ?>>Country</option>
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
                                <td width="16%">First Name</td>
                                <td width="19%">City</td>
                                <td width="19%">State</td>
                                <td width="19%">Country</td>
                                <td width="20%">Status</td>
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
                                <td class="maintext"><?php echo "<a href=editaffiliate.php?affid='".$arr["nAffiliateId"]."'>".htmlentities($arr["vFirstName"])."</a>";?></td>
								<td><?php echo htmlentities($arr["vCity"]);?></td>
                                <td><?php echo htmlentities($arr["vState"]);?></td>
                                <td><?php echo htmlentities($arr['vCountry']);?></td>
                                <td><?php 
												$status =$arr["vDelStatus"];
                                                if($status == "0")
												{
                                                    $statusimg = "tick.gif";
                                                    $changestatuslink = "ChangeStatus(".$arr["nAffiliateId"].",'D');";
                                                }//end if
												else if($status == "1")
												{
                                                    $statusimg = "cross.gif";
                                                    $changestatuslink = "ChangeStatus(".$arr["nAffiliateId"].",'A');";
                                                }//end else if
                                                $statustextorlink = "<a href=javascript:$changestatuslink><img src='../images/".$statusimg."' border=\"0\"></a>";
								
								echo $statustextorlink;?></td>
                              </tr>
					<?php 
								$cnt++;
							}//end while
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="6" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
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