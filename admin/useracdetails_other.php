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
$PGTITLE='account2';

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

$qryopt = "";
if($txtSearch != "")
{
        if($ddlSearchType == "transno")
		{
              $qryopt .= "  and sd.vTxnId like '" . addslashes($txtSearch) . "%'";
        }//end if
		else if($ddlSearchType == "amount")
		{
             $qryopt .= "  and sd.nAmount like '" . addslashes($txtSearch) . "%'";
        }//end else if
		else if($ddlSearchType == "transmode")
		{
			  $va_method="";
			  $txtSearchNew=strtolower($txtSearch);
			  switch($txtSearchNew) 
			  {
					case strtolower("PayPal") :
						$va_method="pp";
					break;
					
					case strtolower("Credit Card") :	$va_method="cc";
					break;
					
					case strtolower("Business Check") : $va_method="bu";
					break;
					
					case strtolower("Cashiers Check") : $va_method="ca";
					break;

					case strtolower("Money Order") : $va_method="mo";
					break;

					case strtolower("Wire Transfer") : $va_method="wt";
					break;
				
					case strtolower("Personal Check") : $va_method="pc";
					break;
			}//end switch
			$qryopt .= " and sd.vMethod='$va_method' ";
       }//end else if
}//end if

//checking escrow status
if(DisplayLookUp('Enable Escrow')=='Yes')
{
	$SaleStatus='';
}//end if
else
{
	$SaleStatus=" OR sd.vSaleStatus ='4'";
}//end esle

$sqlsale = "SELECT s.vTitle, sd.nAmount,sd.nSaleId,sd.nUserId,sd.dDate as 'dDate2',sd.vDelivered,date_format(sd.dTxnDate,'%m/%d/%Y') as dTxnDate,  date_format(sd.dDate ,'%m/%d/%Y') as dDate, sd.vTxnId, sd.vMethod  , u.vLoginName,sd.nQuantity ";
$sqlsale .= " FROM ".TABLEPREFIX."sale s  INNER JOIN ".TABLEPREFIX."saledetails sd ON s.nSaleId = sd.nSaleId LEFT JOIN  ".TABLEPREFIX."users u ON u.nUserId  = s.nUserId ";
$sqlsale .= " WHERE ";
$sqlsale .= " sd.nUserId  = '".$_GET['userid']."' AND (sd.vSaleStatus ='2'  OR sd.vSaleStatus ='3' ".$SaleStatus.") ";
$sqlsale .= $qryopt ;
$sqlsale .= "  order by sd.dDate DESC ";
$totalrows = mysqli_num_rows(mysqli_query($conn, $sqlsale));

$navigate = pageBrowser($totalrows,5,5,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&userid=".$_GET['userid']."&username=".$_GET['username']."&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);
//execute the new query with the appended SQL bit returned by the function
$sqlsale = $sqlsale.$navigate[0];
$rssale = mysqli_query($conn, $sqlsale) or die(mysqli_error($conn));

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);

?>
<script LANGUAGE="javascript" type="text/javascript">
function clickSearch()
{
		document.frmAffMain.action="useracdetails_other.php?userid=<?php echo $_GET['userid'];?>&username=<?php echo $_GET['username'];?>";
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
                            <td bgcolor="#FFFFFF" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmAffMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>">

<?php
$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);

if(isset($message) && $message!='')
{
?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="7" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>			
<tr align="right" bgcolor="#FFFFFF">
  <td colspan="7"><a href="useraccountsummary_other.php"><b>Back</b></a></td>
</tr>
<tr bgcolor="#FFFFFF"><input type="hidden" name="saleid" id="saleid" value="">
<input type="hidden" name="userid" id="userid" value="">
<input type="hidden" name="dt" id="dt" value="">
<input type="hidden" name="postback" id="postback" value="">
	<td colspan="7" align="center">
	<table border="0" width="100%" class="maintext">
    <tr>
	    <td valign="top" align="right">
		    Search&nbsp; 
		    <select name="ddlSearchType" class="textbox2">                                                                                   <!---<option value="date" <?php if($ddlSearchType== "date" || $ddlSearchType == ""){ echo("selected"); } ?>>Transaction Date(mm/dd/yyyy)</option>--->
		    <option value="amount"  <?php if($ddlSearchType == "amount" || $ddlSearchType == ""){ echo("selected"); } ?>>Amount</option>
		    <option value="transmode"  <?php if($ddlSearchType == "transmode" ){ echo("selected"); } ?>>Transaction Mode</option>
		    <option value="transno" <?php if($ddlSearchType== "transno"){ echo("selected"); } ?>>Transaction Number</option>
			</select>
			&nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
		</td>
		<td align="left" valign="baseline">
			<a href="javascript:clickSearch();" class="link_style2">Go</a>
		</td>
	</tr>
	</table></td>
	</tr>  
	<tr align="center" bgcolor="#FFFFFF" class="gray">
		<td width="7%" align="center" valign="middle">Sl No. </td>
		<td width="16%" align="center" valign="middle">Title</td>
		<td width="19%" align="center" valign="middle">User</td>
		<td width="19%" align="center" valign="middle">Transaction Date </td>
		<td width="19%" align="center" valign="middle">Transaction No </td>
		<td width="20%" align="center" valign="middle">Mode</td>
		<td width="20%" align="center" valign="middle">Amount(<?php echo CURRENCY_CODE;?>)</td>
	</tr>
	<?php
	if(mysqli_num_rows($rssale)>0)
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
		while ($arr = mysqli_fetch_array($rssale))
	  	{
			$paydate = $arr["dTxnDate"];
			$amount = $arr["nAmount"];
			$transid = $arr["vTxnId"];
			$trnansmode ="";

			switch($arr["vMethod"]) 
			{
				case "pp" : $trnansmode  = "PayPal";
				break;
				
				case "wp" : $trnansmode  = "WorldPay";
				break;
				
				case "bp" : $trnansmode  = "BluePay";
				break;
				
				case "cc" :	$trnansmode ="Credit Card";
				break;
				
				case "bu" : $trnansmode ="Business Check";
				break;
				
				case "ca" : $trnansmode ="Cashiers Check";
				break;

				case "mo" : $trnansmode ="Money Order";
				break;

				case "wt" : $trnansmode ="Wire Transfer";
				break;
											
				case "pc" : $trnansmode ="Personal Check";
				break;

				case "yp":
					$trnansmode = "Yourpay";
				break;

				case "gc":
					$trnansmode = "Google Checkout";
				case "sp":
					$trnansmode = "Stripe";
				break;
			}//end switch
											
			$title =  $arr["vTitle"];
			$username = $arr["vLoginName"];
	?>
	<tr bgcolor="#FFFFFF">
    	<td align="center" valign="middle"><?php echo $cnt;?></td>
    	<td align="center" valign="middle"><a href="transdetails_other.php?saleid=<?php echo $arr["nSaleId"];?>&tot=<?php echo $arr["nAmount"];?>&reqd=<?php echo $arr["nQuantity"];?>&dt=<?php echo urlencode($arr["dDate2"]);?>&username=<?php echo $_GET['username'];?>&userid=<?php echo $_GET['userid'];?>&"><?php echo htmlentities($title);?></a></td>
    	<td align="center" valign="middle"><a href="transdetails_other.php?saleid=<?php echo $arr["nSaleId"];?>&tot=<?php echo $arr["nAmount"];?>&reqd=<?php echo $arr["nQuantity"];?>&dt=<?php echo urlencode($arr["dDate2"]);?>&username=<?php echo $_GET['username'];?>&userid=<?php echo $_GET['userid'];?>&"><?php echo htmlentities($username);?></a></td>
        <td align="center" valign="middle"><a href="transdetails_other.php?saleid=<?php echo $arr["nSaleId"];?>&tot=<?php echo $arr["nAmount"];?>&reqd=<?php echo $arr["nQuantity"];?>&dt=<?php echo urlencode($arr["dDate2"]);?>&username=<?php echo $_GET['username'];?>&userid=<?php echo $_GET['userid'];?>&"><?php echo date('F d, Y',strtotime($paydate));?></a></td>
        <td align="center" valign="middle"><a href="transdetails_other.php?saleid=<?php echo $arr["nSaleId"];?>&tot=<?php echo $arr["nAmount"];?>&reqd=<?php echo $arr["nQuantity"];?>&dt=<?php echo urlencode($arr["dDate2"]);?>&username=<?php echo $_GET['username'];?>&userid=<?php echo $_GET['userid'];?>&"><?php echo htmlentities($transid);?></a></td>
        <td align="center" valign="middle"><a href="transdetails_other.php?saleid=<?php echo $arr["nSaleId"];?>&tot=<?php echo $arr["nAmount"];?>&reqd=<?php echo $arr["nQuantity"];?>&dt=<?php echo urlencode($arr["dDate2"]);?>&username=<?php echo $_GET['username'];?>&userid=<?php echo $_GET['userid'];?>&"><?php echo htmlentities($trnansmode);?></a></td>
        <td align="center" valign="middle"><a href="transdetails_other.php?saleid=<?php echo $arr["nSaleId"];?>&tot=<?php echo $arr["nAmount"];?>&reqd=<?php echo $arr["nQuantity"];?>&dt=<?php echo urlencode($arr["dDate2"]);?>&username=<?php echo $_GET['username'];?>&userid=<?php echo $_GET['userid'];?>&"><?php echo htmlentities($amount);?></a></td>
    </tr>
	<?php 
			$cnt++;
		}//end while
	}//end if
  	?>
	<tr bgcolor="#FFFFFF">
		<td colspan="7"  class="noborderbottm"align="left">
			<table width="100%"  border="0" cellspacing="1" cellpadding="5">
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