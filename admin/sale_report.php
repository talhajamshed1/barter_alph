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

$txtFromDate=($_GET['txtFromDate']!='')?$_GET['txtFromDate']:$_POST['txtFromDate'];
$txtToDate=($_GET['txtToDate']!='')?$_GET['txtToDate']:$_POST['txtToDate'];

switch ($txtFromDate)
{
	case "":
		$txtFromDate=date('m-d-Y',strtotime('-30 day'));
	break;
}//ends switch

switch ($txtToDate)
{
	case "":
		$txtToDate=date('m-d-Y');
	break;
}//ends switch

//from date
if($_POST["txtFromDate"] != "")
{
   $txtFromDate = $_POST["txtFromDate"];
}//end if
else if($_GET["txtFromDate"] != "")
{
   $txtFromDate = $_GET["txtFromDate"];
}//end else if

//to date
if($_POST["txtToDate"] != "")
{
  $txtToDate = $_POST["txtToDate"];
}//end if
else if($_GET["txtToDate"] != "")
{
        $txtToDate = $_GET["txtToDate"];
}//end else if





if(!isset($begin) || $begin =="")
{
        $begin = 0;
}//end if
		
$showReport=false;
$showCsv='&nbsp;';

$qryopt = "";

if(isset($txtFromDate) && trim($txtFromDate!=''))
{
	//convet date to mysql format
	$from_date 	= dateFormat($txtFromDate,"m-d-Y","Y-m-d");
	$to_date 	= dateFormat($txtToDate,"m-d-Y","Y-m-d");
	
	$qryopt .= "  AND sd.dTxnDate>='".addslashes($from_date)."' AND sd.dTxnDate<=DATE_ADD('".addslashes($to_date)."', INTERVAL 1 DAY) ";
	
	//show listing
	$showReport=true;
}//end if

$orderby = (trim($_REQUEST['orderby'])=='')? 'sd.dDate': trim($_REQUEST['orderby']);
$sort = (trim($_REQUEST['sort'])=='')? 'DESC': trim($_REQUEST['sort']);
	
$sqlSent = "SELECT s.vTitle, sd.nAmount,sd.nPoint,sd.nSaleId,sd.nUserId,sd.dDate as 'dDate2',sd.vDelivered,date_format(sd.dTxnDate,'%m/%d/%Y') as dTxnDate,  
				date_format(sd.dDate ,'%m/%d/%Y') as dDate, sd.vTxnId, sd.vMethod  , u.vLoginName,sd.nQuantity,s.nUserId as sellerId ";
$sqlSent .= " FROM ".TABLEPREFIX."sale s  INNER JOIN ".TABLEPREFIX."saledetails sd ON s.nSaleId = sd.nSaleId LEFT JOIN  ".TABLEPREFIX."users u ON u.nUserId  = s.nUserId ";
$sqlSent .= " WHERE ";
$sqlSent .= " (sd.vSaleStatus ='2'  OR sd.vSaleStatus ='3' ".$SaleStatus.") ";
$sqlSent .= $qryopt ;
$sqlSent .= "  order by ".$orderby." ".$sort;
$totalrows = mysqli_num_rows(mysqli_query($conn, $sqlSent));

if($showReport==true)
{
	$showCsv='<a href="export_csv.php?sType=sale"><b>Export as CSV</b></a>';
	$_SESSION['sess_query']=$sqlSent;
}//end if

$navigate = pageBrowser($totalrows,5,10,"&txtFromDate=".urlencode($txtFromDate)."&txtToDate=".urlencode($txtToDate)."&orderby=".urlencode($orderby)."&sort=".urlencode($sort)."&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);
//execute the new query with the appended SQL bit returned by the function
$sqlSent = $sqlSent.$navigate[0];
$rssale = mysqli_query($conn, $sqlSent) or die(mysqli_error($conn));
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.datepick.js"></script>
<link href="../styles/jquery.datepick.css" type="text/css" rel="stylesheet">
<script language="javascript" type="text/javascript">
function clickSearch()
{
		document.frmSent.submit();
		var frmdate = $("#jqFromDate").val();
		var todate = $("#jqToDate").val();
		if(frmdate>todate)
		{
			alert("From date should not be greater than To date !!");
			return false;
		}		
}

//for date picker
$(document).ready(
 function()
{
	$("#jqFromDate").datepick({ dateFormat: 'mm-dd-yyyy' });
	$("#jqToDate").datepick({ dateFormat: 'mm-dd-yyyy' });
});//end if
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
                        <td width="75%" class="heading_admn boldtextblack" align="left">Sales Report </td>
                        <td width="25%" align="right" class="heading_admn"><?php echo $showCsv;?></td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">
<form name="frmSent" method="post" action = "<?php echo $_SERVER['PHP_SELF']?>">

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
<tr bgcolor="#FFFFFF">
                                <td colspan="8" align="center"><table border="0" width="100%" class="maintext">
                                        <tr>
                                                <td valign="top" align="right">From Date &nbsp;<input type="text" class="textbox2" name="txtFromDate" id="jqFromDate" size="20" value="<?php echo htmlentities($txtFromDate);?>"> &nbsp;To Date &nbsp;<input type="text" class="textbox2" name="txtToDate" id="jqToDate" size="20" value="<?php echo htmlentities($txtToDate);?>">
&nbsp;
                                          </td>
                                                <td align="left" valign="baseline">
                                                <a class="link_style2" href="javascript:clickSearch();">
                                                GO</a>
                                                </td>
                                        </tr>
                                </table></td>
                      </tr>  
					  <?php
					  	 //show select report
						 if($showReport==true)
						 {
					 ?>
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td width="5%" align="center">Sl No. </td>
                                <td width="13%" align="center">Seller&nbsp;&nbsp;<a href='sale_report.php?orderby=s.nUserId&sort=ASC&txtFromDate=<?php echo $txtFromDate;?>&txtToDate=<?php echo $txtToDate;?>'><img src='../images/up.gif' border=0 alt='Sort Ascending'></a>&nbsp;<a href="sale_report.php?orderby=s.nUserId&sort=DESC&txtFromDate=<?php echo $txtFromDate;?>&txtToDate=<?php echo $txtToDate;?>"><img src='../images/down.gif' border=0 alt='Sort Descending'></a></td>
                                <td width="14%" align="center">Transaction Date</td>
                                <td width="13%" align="center">Transaction Number</td>
                                <td width="8%" align="center">Mode</td>
                                <td width="8%" align="center">Amount</td>
                                <td width="8%" align="center"><?php echo POINT_NAME; ?></td>
								<?php
									//checking point enable in website
									if($EnablePoint!='0')
									{
								?>
								<?php
									}//end if
								?>
                                <td width="15%" align="center">Buyer&nbsp;&nbsp;<a href='sale_report.php?orderby=s.nUserId&sort=ASC&txtFromDate=<?php echo $txtFromDate;?>&txtToDate=<?php echo $txtToDate;?>'><img src='../images/up.gif' border=0 alt='Sort Ascending'></a>&nbsp;<a href="sale_report.php?orderby=s.nUserId&sort=DESC&txtFromDate=<?php echo $txtFromDate;?>&txtToDate=<?php echo $txtToDate;?>"><img src='../images/down.gif' border=0 alt='Sort Descending'></a></td>
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
										break;
										
										case "rp":
											$trnansmode = POINT_NAME;
										break;
								}//end switch			
								
								//fetch seller name
								$SellerName=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',"WHERE nUserId='".$arr['sellerId']."'"),'vLoginName');
								
								//fetch buyer name
								$userName=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',"WHERE nUserId='".$arr['nUserId']."'"),'vLoginName');
						  ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="center"><?php echo $cnt;?></td>
                                <td align="center"><?php echo restrict_string_size($SellerName,20);?></td>
                                <td align="center"><?php echo date('F d, Y',strtotime($arr["dTxnDate"]));?></td>
                                <td align="center"><?php echo htmlentities($arr["vTxnId"]);?></td>
                                <td align="center"><?php echo htmlentities($trnansmode);?></td>
                                <td align="center"><?php echo CURRENCY_CODE.htmlentities($arr["nAmount"]);?></td>
                                <td align="center"><?php echo $arr["nPoint"];?></td>
								<?php
									//checking point enable in website
									if(DisplayLookUp('EnablePoint')!='0')
									{
										$pointValue=round(($arr["nAmount"]/DisplayLookUp('PointValue'))*DisplayLookUp('PointValue2'),2);
								?>
                               	<?php
									}//end if
								?>
                                <td align="center"><?php echo htmlentities($userName);?></td>
                              </tr>
					<?php 
								$cnt++;
								$amountTotal+=$arr["nAmount"];
								$pointsTotal+=$pointValue;
							}//end while
							echo ' <tr bgcolor="#FFFFFF">
                                <td colspan="5" align="right"><b>Total</b></td>
                                <td colspan="3"><b>'.CURRENCY_CODE.$amountTotal.'</b></td>
                              </tr>';
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="8" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
  <tr>
    <td align="left"><?php echo($navigate[2]);?></td>
    <td align="right"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
  </tr>
  <?php
  		}//end show report if
 ?>
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