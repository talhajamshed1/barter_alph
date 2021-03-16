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
$PGTITLE='saleapproval';


$activateflag ="";
if(isset($_POST["postback"]) && $_POST["postback"] == "CS")
{ //for changing status

    $var_id = $_POST["saleinterid"];

    if($_POST["changeto"] == "A")
	{
		//start of approval of sale
		$sql = "Select nSaleInterId,nSaleId,nUserId,dDate,vMethod,vName,vBank,vReferenceNo,dReferenceDate,dEntryDate from ".TABLEPREFIX."saleinter ";
		$sql .= " where nSaleInterId='" . addslashes($var_id) . "' AND vDelStatus='0' ";
                
		$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($result) > 0) 
		{
			if($row = mysqli_fetch_array($result)) 
			{
				//Begin
				$flag_proceed = false;
				$sql = "Select nAmount,vSaleStatus,vRejected,nQuantity from ".TABLEPREFIX."saledetails where ";
				$sql .= " nSaleId='" . $row["nSaleId"] . "' AND nUserId='" . $row["nUserId"] . "' AND dDate='";
				$sql .= $row["dDate"] . "' ";
                                
                               
				$result_chk = mysqli_query($conn, $sql) or die(mysqli_error($conn));
				if(mysqli_num_rows($result_chk) > 0) 
				{
					$row_chk = mysqli_fetch_array($result_chk);
					$cost=$row_chk["nAmount"];
					$nQuantity=$row_chk["nQuantity"];
					if($row_chk["vSaleStatus"] == "1" && $row_chk["vRejected"] == "0")
					{
						$flag_proceed=true;
					}//end if
				}//end if
                               
				if($flag_proceed == true) 
				{
					//I - Store check details
					$sql = "Update ".TABLEPREFIX."saleinter set vDelStatus='1' where nSaleInterId='" . addslashes($var_id) . "'";
					mysqli_query($conn, $sql) or die(mysqli_error($conn));

					$sql = "Insert into ".TABLEPREFIX."paymentdetails(nPaymentId,vName,vReferenceNo,vBank,dReferenceDate,dEntryDate) ";
					$sql .= " Values('','" . addslashes($row["vName"]) . "','" . addslashes($row["vReferenceNo"]) . "','" . addslashes($row["vBank"]) . "','" . addslashes($row["dReferenceDate"]) . "',now())";
					mysqli_query($conn, $sql) or die(mysqli_error($conn));

					$var_txnid = mysqli_insert_id($conn);

					$sql = "Update ".TABLEPREFIX."saledetails set vMethod='" . $row["vMethod"] . "',vSaleStatus='2',vTxnId='$var_txnid',dTxnDate=now() where ";
					$sql .= " nSaleId='" . $row["nSaleId"] . "' AND nUserId='" . $row["nUserId"] . "' AND dDate='";
					$sql .= $row["dDate"] . "' ";
					mysqli_query($conn, $sql) or die(mysqli_error($conn));

					$sql = "Select nUserId from ".TABLEPREFIX."sale where nSaleId='" . addslashes($row["nSaleId"]) . "'";
					mysqli_query($conn, $sql) or die(mysqli_error($conn));

					$result_2 = mysqli_query($conn, $sql);
//					if(mysqli_num_rows($result_2) > 0)
//					{
//						if($row_2=mysqli_fetch_array($result_2))
//						{
//							$sql = "Update ".TABLEPREFIX."users set nAccount = nAccount +  $cost  where  nUserId='" . $row_2["nUserId"] . "'";
//							mysqli_query($conn, $sql) or die(mysqli_error($conn));
//						}//end if
//					}//end if
					
					
					
					if($nQuantity!='')
						{
						 $sql = "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity - ".$nQuantity." where nSaleId ='" . addslashes($row["nSaleId"]) . "'";
						
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
						}
						
					echo("<script>alert('Transaction accepted');</script>");

			}//end if
			else 
			{
				echo("<script>alert('This transaction cannot be accepted since the order status has been changed.');</script>");

			}//end else
					//End
		}//end if
	}//end if
	//end of approval of sale
  }//end if
}//end if

//reject sale
if(isset($_POST["postback"]) && $_POST["postback"]=="CSR")
{ //for changing status
	$var_id = $_POST["saleinterid"];
	$chid=mysqli_query($conn, "select nSaleId from ".TABLEPREFIX."saleinter where nSaleInterId='".$var_id."'") or die(mysqli_error($conn));
	if(mysqli_num_rows($chid)>0)
	{
                $sql = "select S.nUserId as buyer, S.nPoint as point, ss.nUserId as seller from ".TABLEPREFIX."saledetails S 
                                left join ".TABLEPREFIX."sale ss on S.nSaleId = ss.nSaleId 
                                    where S.nSaleId = ".mysqli_result($chid,0,'nSaleId');
                $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if ($row = mysqli_fetch_array($res)) {
                    $sql = "update " . TABLEPREFIX . "usercredits set nPoints = nPoints - ".$row['point']." where nUserId='".$row['seller']."'";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));//decrementing the points for seller

                    $sql = "update " . TABLEPREFIX . "usercredits set nPoints = nPoints + ".$row['point']." where nUserId='".$row['buyer']."'";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));//incrementing the points for buyer
                }
		//delete from sale details
		mysqli_query($conn, "delete from ".TABLEPREFIX."saledetails where nSaleId='".mysqli_result($chid,0,'nSaleId')."'") or die(mysqli_error($conn));
		//update sale quantity
		/*
		mysqli_query($conn, "update ".TABLEPREFIX."sale set nQuantity=nQuantity+1 where 
				nSaleId='".mysqli_result($chid,0,'nSaleId')."'") or die(mysqli_error($conn));
				*/
	}//end if
	//delete from saleinter table
	mysqli_query($conn, "delete from ".TABLEPREFIX."saleinter where nSaleInterId='".$var_id."'") or die(mysqli_error($conn));
	echo("<script>alert('Transaction rejected');</script>");
}//end if


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
}//end else
else if($_GET["ddlSearchType"] != "")
{
    $ddlSearchType = $_GET["ddlSearchType"];
}//end else

if($txtSearch != "")
{
        if($ddlSearchType == "username")
		{
            $qryopt .= " AND u.vLoginName like '" . addslashes($txtSearch) . "%'";
        }//end if 
		else if($ddlSearchType == "bank")
		{
            $qryopt .= " AND si.vBank  like '" . addslashes($txtSearch) . "%'";
        }//end else if
		else if($ddlSearchType == "referenceno") 
		{
            $qryopt .= " AND si.vReferenceNo  like '" . addslashes($txtSearch) . "%'";
		}//end else if
}//end if

if(!isset($begin) || $begin =="")
{
    $begin = 0;
}//end if


$sql = "Select s.vTitle,u.vLoginName,si.nSaleInterId,si.nSaleId,si.nUserId,si.dDate,si.vName,si.vBank,si.vReferenceNo,si.dReferenceDate,date_format(si.dEntryDate,'%m/%d/%Y') as 'dEntryDate'  from ".TABLEPREFIX."saleinter si ";
$sql .= " inner join ".TABLEPREFIX."sale s on si.nSaleId = s.nSaleId  inner join ".TABLEPREFIX."users u on si.nUserId = u.nUserId ";
$sql .= " Where si.vDelStatus='0' ";

$sess_back="saleapproval.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $ddlSearchType . "&txtSearch=" . urlencode($txtSearch) . "&";

$_SESSION["backurl"] = $sess_back;

$sql .= $qryopt . " Order By si.dEntryDate Desc ";
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,10,10,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql.$navigate[0];

$rs = mysqli_query($conn, $sql);
?>
<script type="text/javascript" language="javascript">
function clickSearch()
{
        document.frmAdminMain.submit();
}
function changeStatus(id) {
                var frm = document.frmAdminMain;
        if(confirm("Are you sure you want to approve this transaction?")){
                frm.changeto.value="A";
                frm.saleinterid.value=id;
                frm.postback.value="CS";
                frm.submit();
        }
}
function changeStatus2(id) {
                var frm = document.frmAdminMain;
        if(confirm("Are you sure you want to reject this transaction?")){
                frm.saleinterid.value=id;
                frm.postback.value="CSR";
                frm.submit();
        }
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
                        <td width="100%" class="heading_admn boldtextblack" align="left">Sales To Be Approved</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">
<form  name="frmAdminMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" >

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
<tr bgcolor="#FFFFFF"><input type="hidden" name="saleinterid" value="">
<input type="hidden" name="changeto" value="">
<input type="hidden" name="postback" value="">
                                <td colspan="6" align="center"><table border="0" width="100%" class="maintext">
                                        <tr>
                                                <td valign="top" align="right">
                                                Search
                                         &nbsp; <select name="ddlSearchType" class="textbox2">
                                        <option value="username" <?php if($ddlSearchType== "username" || $ddlSearchType == ""){ echo("selected"); } ?>>User Name</option>
                                        <option value="bank" <?php if($ddlSearchType == "bank"){ echo("selected"); } ?>>Bank</option>
                                        <option value="referenceno" <?php if($ddlSearchType== "referenceno"){ echo("selected"); } ?>>Reference No</option>
                                    </select>
               &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                                </td>
                                                <td align="left" valign="baseline">
                                                <a href="javascript:clickSearch();" class="link_style2">
                                                GO
                                                </a>
                                                </td>
                                        </tr>
                                </table></td>
                      </tr>  
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td align="center" width="7%">Sl No. </td>
                                <td align="center" width="16%">Title</td>
                                <td align="center" width="19%">User Name </td>
                                <td align="center" width="19%">Date</td>
                                <td align="center" width="19%">Reference No </td>
                                <td align="center" width="20%">Approve/Reject</td>
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
								$statustextorlink = "<a href=\"javascript:changeStatus('".$arr["nSaleInterId"]."','".$arr["nSaleInterId"]."');\">Activate</a>";
								$statustextorlink2 = "<a href=\"javascript:changeStatus2('".$arr["nSaleInterId"]."');\">Reject</a>";
					  ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="center"><?php echo $cnt;?></td>
                                <td align="center" class="maintext"><?php echo "<a href=approvesale.php?id=".$arr["nSaleInterId"].">".restrict_string_size($arr["vTitle"],20)."</a>";?></td>
                                <td align="center" ><?php echo "<a href=approvesale.php?id=".$arr["nSaleInterId"].">".htmlentities($arr["vLoginName"])."</a>";?></td>
								<td align="center"><?php echo "<a href=approvesale.php?id=".$arr["nSaleInterId"].">".date('F d, Y',strtotime($arr["dEntryDate"]))."</a>";?></td>
                                <td align="center"><?php echo "<a href=approvesale.php?id=".$arr["nSaleInterId"].">".htmlentities($arr["vReferenceNo"])."</a>";?></td>
                                <td align="center"><?php echo $statustextorlink;?>&nbsp;|&nbsp;<?php echo $statustextorlink2;?></td>
                              </tr>
					<?php 
								$cnt++;
							}//end while
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="6" align="left" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
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