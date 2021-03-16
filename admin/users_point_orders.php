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
        if($ddlSearchType == "date")
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
              $qryopt .= "  and c.dDate like '" . addslashes($newdate) . "%'";
        }//end if
		else if($ddlSearchType == "number")
		{
             $qryopt .= "  and c.vTxnId like '" . addslashes($txtSearch) . "%'";
        }//end else if
		else if($ddlSearchType == "uname")
		{
             $qryopt .= "  and u.vLoginName like '" . addslashes($txtSearch) . "%'";
        }//end else if
}//end if

$sqlSent = "SELECT c.nId,c.nUserId,c.nAmount,c.nPoints,date_format(c.dDate,'%m/%d/%Y') as sentDate,c.vTxnId,c.vMethod,c.vStatus,c.vReferenceNo ";
$sqlSent .= " FROM ".TABLEPREFIX."creditpayments c LEFT JOIN  ".TABLEPREFIX."users u ON u.nUserId=c.nUserId";
$sqlSent .= " WHERE 1=1 ";
$sqlSent .= $qryopt ;
$sqlSent .= "  order by dDate DESC ";
$totalrows = mysqli_num_rows(mysqli_query($conn, $sqlSent));

$navigate = pageBrowser($totalrows,5,5,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);
//execute the new query with the appended SQL bit returned by the function
$sqlSent = $sqlSent.$navigate[0];
$rssale = mysqli_query($conn, $sqlSent) or die(mysqli_error($conn));

//update into user account
if(isset($_GET['mode']) && $_GET['mode']=='update')
{
	//checking alredy exits
	$chkPoint=fetchSingleValue(select_rows(TABLEPREFIX.'usercredits','nPoints',"WHERE nUserId='".$_GET['nUserId']."'"),'nPoints');
	if(trim($chkPoint)!='')
	{
		//update points to user credit
		mysqli_query($conn, "UPDATE ".TABLEPREFIX."usercredits set nPoints=nPoints+".base64_decode($_GET['nPt'])." WHERE 
												nUserId='".$_GET['nUserId']."'") or die(mysqli_error($conn));
	}//end if
	else
	{
		//add points to user credit
		mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."usercredits (nPoints,nUserId) VALUES ('".base64_decode($_GET['nPt'])."','".$_GET['nUserId']."')") or die(mysqli_error($conn));
	}//end else
	
	//update payment status
	mysqli_query($conn, "UPDATE ".TABLEPREFIX."creditpayments SET vStatus='A' WHERE nUserId='".$_GET['nUserId']."' and nId='".$_GET['nId']."'
						AND vStatus='P'") or die(mysqli_error($conn));
	
	$_SESSION['sessionMsg']='Successfully updated.';
	echo '<script>location.href="users_point_orders.php";</script>';
}//end if
?>
<script type="text/javascript" src="../js/admin_dhtmlwindow.js"></script>
<script language="javascript" type="text/javascript">
function clickSearch()
{
		document.frmSent.submit();
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
                        <td width="100%" class="heading_admn boldtextblack" align="left"><?php echo POINT_NAME;?> Purchase History</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="admin_tble_2">
<form name="frmSent" method="post" action = "<?php echo $_SERVER['PHP_SELF']?>">

<?php
$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);

if(isset($message) && $message!='')
{
?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="9" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>			
<tr bgcolor="#FFFFFF">
                                <td colspan="9" align="center"><table border="0" width="100%" class="maintext">
                                        <tr>
                                                <td valign="top" align="right">
                                                Search
                                         &nbsp; <select name="ddlSearchType" class="textbox2">
                                                                                        <option value="date" <?php if($ddlSearchType== "date" || $ddlSearchType == ""){ echo("selected"); } ?>>Transaction Date(mm/dd/yyyy)</option>
                                                                                        <option value="number"  <?php if($ddlSearchType == "number" || $ddlSearchType == ""){ echo("selected"); } ?>>Transaction Number</option>
																						<option value="uname"  <?php if($ddlSearchType == "uname" || $ddlSearchType == ""){ echo("selected"); } ?>>UserName</option>
                                                                                </select>
               &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                                </td>
                                                <td align="left" valign="baseline">
                                                <a href="javascript:clickSearch();" class="link_style2">
                                                GO</a>
                                                </td>
                                        </tr>
                                </table></td>
                      </tr>  
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td width="5%" align="center" valign="middle">Sl No. </td>
                                <td width="13%" align="center" valign="middle">UserName</td>
                                <td width="14%" align="center" valign="middle">Transaction Date</td>
                                <td width="13%" align="center" valign="middle">Transaction Number</td>
                                <td width="8%" align="center" valign="middle">Mode</td>
                                <td width="14%" align="center" valign="middle"><?php echo POINT_NAME;?></td>
                                <td width="8%" align="center" valign="middle">Amount</td>
                                <td width="10%" align="center" valign="middle">Status</td>
                                <td width="15%" align="center" valign="middle">Action</td>
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
								
								//checking status
								switch($arr["vStatus"])
								{
									case "P":
										$shwStatus='Pending to update into user account';
										$shwStatusLink='<a href="users_point_orders.php?nUserId='.$arr['nUserId'].'&nPt='.base64_encode($arr['nPoints']).'&nId='.$arr['nId'].'&mode=update"><b>Update</b></a>';
									break;
									
									case "A":
										$shwStatus='Added to user account';
										$shwStatusLink='Confirmed';
									break;
								}//end switch
								
								$showLink="<a href=\"#\" onClick=\"divwin=dhtmlwindow.open('divbox".$arr['nId']."', 'div', 'somediv". $arr['nId']."', 'Transaction Details', 'width=550px,height=170px,left=550px,top=190px,resize=1,scrolling=1'); return false\">";			
								$closeLink='</a>';
								
								$userName=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',"WHERE nUserId='".$arr['nUserId']."'"),'vLoginName');
						  ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="center" valign="middle"><?php echo $cnt;?></td>
                                <td align="center" valign="middle"><?php echo $showLink.htmlentities($userName).$closeLink;?></td>
                                <td align="center" valign="middle"><?php echo $showLink.date('F d, Y',strtotime($arr["sentDate"])).$closeLink;?></td>
                                <td align="center" valign="middle"><?php echo $showLink.htmlentities($arr["vTxnId"]).$closeLink;?></td>
                                <td align="center" valign="middle"><?php echo $showLink.htmlentities($trnansmode).$closeLink;?></td>
                                <td align="center" valign="middle"><?php echo $showLink.htmlentities($arr["nPoints"]).$closeLink;?></td>
                                <td align="center" valign="middle"><?php echo $showLink.CURRENCY_CODE.htmlentities($arr["nAmount"]).$closeLink;?></td>
                                <td align="center" valign="middle"><?php echo $showLink.htmlentities($shwStatus).$closeLink;?>
								<div id="somediv<?php echo $arr['nId'];?>" style="display:none">
											<table width="100%"  border="0" cellspacing="0" cellpadding="0">
									  <tr>
										<td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
										  <tr align="center" bgcolor="#FFFFFF">
											<td width="6%" align="left" class="gray">Transaction Date</td>
											<td width="6%" align="left"><?php echo date('F d, Y',strtotime($arr["sentDate"]));?></td>
										  </tr>
										  <tr bgcolor="#FFFFFF">
											<td align="left" class="gray">Amount</td>
											<td align="left"><?php echo CURRENCY_CODE.htmlentities($arr["nAmount"]);?></td>
										  </tr>
										  <tr bgcolor="#FFFFFF">
											<td align="left" class="gray">Mode</td>
											<td align="left"><?php echo htmlentities($trnansmode);?></td>
										  </tr>
										  <!--<tr bgcolor="#FFFFFF">
											<td align="left" class="gray">Reference Number</td>
											<td align="left"><?php echo htmlentities($arr["vReferenceNo"]);?></td>
										  </tr>-->
                                                                                  <tr bgcolor="#FFFFFF">
											<td align="left" class="gray">Transaction Number</td>
											<td align="left"><?php echo htmlentities($arr["vTxnId"]);?></td>
										  </tr>
										  <tr bgcolor="#FFFFFF">
											<td align="left" class="gray"a>Points</td>
											<td align="left"><?php echo htmlentities($arr["nPoints"]);?></td>
										  </tr>
										</table>
			</td>
									  </tr>
									</table>
							</div></td>
                                <td align="center" valign="middle"><?php echo $shwStatusLink;?></td>
                              </tr>
					<?php 
								$cnt++;
							}//end while
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="9" class="noborderbottm" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
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