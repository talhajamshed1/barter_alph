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
	
	$qryopt .= "  AND dDate>='".addslashes($from_date)."' AND dDate<=DATE_ADD('".addslashes($to_date)."', INTERVAL 1 DAY) ";
	
	//show listing
	$showReport=true;
}//end if

$orderby = (trim($_REQUEST['orderby'])=='')? 'dDate': trim($_REQUEST['orderby']);
$sort = (trim($_REQUEST['sort'])=='')? 'DESC': trim($_REQUEST['sort']);

$sqlSent = "SELECT nId,nUserId,nAmount,nPoints,date_format(dDate,'%m/%d/%Y') as sentDate,vTxnId,vMethod,vStatus,vReferenceNo ";
$sqlSent .= " FROM ".TABLEPREFIX."creditpayments";
$sqlSent .= " WHERE 1=1 ";
$sqlSent .= $qryopt ;
$sqlSent .= "  order by ".$orderby." ".$sort;
$totalrows = mysqli_num_rows(mysqli_query($conn, $sqlSent));

if($showReport==true)
{
	$showCsv='<a href="export_csv.php?sType=point"><b>Export as CSV</b></a>';
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
}

//for date picker
$(document).ready(
 function()
{
	$("#jqFromDate").datepick({ dateFormat: 'mm-dd-yyyy' });
	$("#jqToDate").datepick({ dateFormat: 'mm-dd-yyyy' });
});//end if
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
                        <td width="84%" class="heading_admn boldtextblack" align="left"><?php echo POINT_NAME;?> Purchase Report </td>
                        <td width="16%" class="maintext2"><?php echo $showCsv;?></td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
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
                                <td colspan="7" align="center"><table border="0" width="100%" class="maintext">
                                        <tr>
                                                <td valign="top" align="right"><b>From Date</b>  &nbsp;<input type="text" class="textbox" name="txtFromDate" id="jqFromDate" size="20" value="<?php echo htmlentities($txtFromDate);?>"> &nbsp; <b>To Date</b>  &nbsp;<input type="text" class="textbox" name="txtToDate" id="jqToDate" size="20" value="<?php echo htmlentities($txtToDate);?>">
&nbsp;
                                          </td>
                                                <td align="left" valign="baseline">
                                                <a href="javascript:clickSearch();"><img src='../images/gobut.gif'  width="20" height="20" border='0' ></a>
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
                                <td width="8%">Sl No. </td>
                                <td width="19%">UserName&nbsp;&nbsp;<a href='point_report.php?orderby=nUserId&sort=ASC&txtFromDate=<?php echo $txtFromDate;?>&txtToDate=<?php echo $txtToDate;?>'><img src='../images/up.gif' border=0 alt='Sort Ascending'></a>&nbsp;<a href="point_report.php?orderby=nUserId&sort=DESC"><img src='../images/down.gif' border=0 alt='Sort Descending'></a></td>
                                <td width="18%">Transaction Date</td>
                                <td width="17%">Transaction Number</td>
                                <td width="12%">Mode</td>
								<td width="14%"><?php echo POINT_NAME;?></td>
								<td width="12%">Amount</td>
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
								
								$userName=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',"WHERE nUserId='".$arr['nUserId']."'"),'vLoginName');
						  ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="center"><?php echo $cnt;?></td>
                                <td><?php echo htmlentities($userName);?></td>
                                <td><?php echo date('m/d/Y',strtotime($arr["sentDate"]));?></td>
                                <td><?php echo htmlentities($arr["vTxnId"]);?></td>
                                <td><?php echo htmlentities($trnansmode);?></td>
                                <td><?php echo htmlentities($arr["nPoints"]);?></td>
								<td><?php echo CURRENCY_CODE.htmlentities($arr["nAmount"]);?></td>
                              </tr>
					<?php 
								$cnt++;
								$amountTotal+=$arr["nAmount"];
								$pointsTotal+=$arr["nPoints"];
							}//end while
							echo ' <tr bgcolor="#FFFFFF">
                                <td colspan="5" align="right"><b>Total</b></td>
                                <td><b>'.$pointsTotal.'</b></td>
                                <td><b>'.CURRENCY_CODE.$amountTotal.'</b></td>
                              </tr>';
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="7" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
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
<?php include_once('../includes/footer_admin.php');?>