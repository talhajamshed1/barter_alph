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
$PGTITLE='account';

$qryopt="";
$userid="";
$username="";

if($_POST["username"] != "")
{
   $username = $_POST["username"];
}//end if
else if($_GET["username"] != "")
{
        $username = $_GET["username"];
}//end else if

if($_POST["userid"] != "")
{
     $userid = $_POST["userid"];
}//end if
else if($_GET["userid"] != "")
{
   $userid = $_GET["userid"];
}//end else if

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
        if($ddlSearchType == "transmode")
		{
             $qryopt .= "  and vMode like '" . addslashes($txtSearch) . "%'";
        }//end if
		else if($ddlSearchType == "transno")
		{
            $qryopt .= "  and nCashTxnId like '" . addslashes($txtSearch) . "%'";
        }//end else if
		else if($ddlSearchType == "amount")
		{
            $qryopt .= "  and nAmount like '" . addslashes($txtSearch) . "%'";
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
                $qryopt .= "  and dDate  like '" . addslashes($newdate) . "%'";
        }//end else if
}//end if

$sql="SELECT * FROM ".TABLEPREFIX."cashtxn  WHERE nUserId  = '" . addslashes($userid) . "' " . $qryopt . "  order by dDate DESC ";
$sess_back="accountsummary.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch;
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,5,5,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&userid=$userid&username=" . urlencode($username) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql.$navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);

?>
<script LANGUAGE="javascript" type="text/javascript">
function clickSearch()
{
        document.frmAffMain.submit();
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
                        <td width="100%" class="heading_admn boldtextblack" align="left">Transaction Summary for <?php echo htmlentities($username);?></td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                      <tr>
                        <td align="left" class="noborderbottm" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td class="noborderbottm" bgcolor="#ffffff"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmAffMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" >
<?php
if(isset($message) && $message!='')
{
?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="5" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>	
<tr align="right" bgcolor="#FFFFFF">
  <td colspan="5"><a href="useraccountsummary.php"><b>Back</b></a></td>
</tr>
<tr bgcolor="#FFFFFF"><input type="hidden" name="username" value="<? echo htmlentities($username);?>" />
<input type="hidden" name="userid" value="<? echo $userid;?>" />
                                <td colspan="5" align="center"><table border="0" width="100%" class="maintext">
                                        <tr>
                                                <td valign="top" align="right">
                                                Search
                                         &nbsp; <select name="ddlSearchType" class="textbox2">
                                                                                        <option value="date" <?php if($ddlSearchType== "date" || $ddlSearchType == ""){ echo("selected"); } ?>>Transaction Date(mm/dd/yyyy)</option>
                                                                                        <option value="amount"  <?php if($ddlSearchType == "amount" ){ echo("selected"); } ?>>Amount</option>
                                                                                        <option value="transmode"  <?php if($ddlSearchType == "transmode" ){ echo("selected"); } ?>>Transaction Mode</option>
                                                                                        <option value="transno" <?php if($ddlSearchType== "transno"){ echo("selected"); } ?>>Transaction Number</option>
                                                                        </select>
               &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo htmlentities($txtSearch)?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                                </td>
                                                <td align="left" valign="baseline">
                                                <a href="javascript:clickSearch();" class="link_style2">
                                                Go</a>
                                                </td>
                                        </tr>
                                </table></td>
                      </tr>  
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td width="7%" align="center" valign="middle">Sl No. </td>
                                <td width="16%" align="center" valign="middle">Transaction Number</td>
                                <td width="19%" align="center" valign="middle">Transaction Date</td>
                                <td width="19%" align="center" valign="middle">Transaction Mode</td>
                                <td width="19%" align="center" valign="middle">Amount</td>
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
                                <td align="center" valign="middle"><?php echo $cnt;?></td>
                                <td align="center" valign="middle" class="maintext"><?php echo "<a href='transdetails.php?transid=".$arr["nCashTxnId"]."&userid=".$userid."&username=".urlencode($username)."'>".$arr["nCashTxnId"]."</a>";?></td>
                                <td align="center" valign="middle"><?php echo "<a href='transdetails.php?transid=".$arr["nCashTxnId"]."&userid=".$userid."&username=".urlencode($username)."'>".date('F d, Y',strtotime($arr["dDate"]))."</a>";?></td>
                                <td align="center" valign="middle"><?php echo "<a href='transdetails.php?transid=".$arr["nCashTxnId"]."&userid=".$userid."&username=".urlencode($username)."'>".$arr["vMode"]."</a>";?></td>
                                <td align="center" valign="middle"><?php echo "<a href='transdetails.php?transid=".$arr["nCashTxnId"]."&userid=".$userid."&username=".urlencode($username)."'>".$arr["nAmount"]."</a>";?></td>
                              </tr>
					<?php 
								$cnt++;
							}//end while
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="5" class="noborderbottm" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
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