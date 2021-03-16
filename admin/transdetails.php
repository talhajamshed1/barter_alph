<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
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
$transid="";

if($_POST["userid"] != "")
{
   $userid = $_POST["userid"];
}//end if
else if($_GET["userid"] != "")
{
   $userid = $_GET["userid"];
}//end else if
if($_POST["username"] != "")
{
   $username = $_POST["username"];
}//end if
else if($_GET["username"] != "")
{
   $username = $_GET["username"];
}//end else if
if($_POST["transid"] != "")
{
   $transid = $_POST["transid"];
}//end if
else if($_GET["transid"] != "")
{
   $transid = $_GET["transid"];
}//end else if

/*if($_POST[btnSaveChanges] == "Save Changes"){
        $sql = "UPDATE ".TABLEPREFIX."cashtxn SET vModeNo= '$_POST[txtTransactionNumber]', nAmount='$_POST[txtAmount]', nCommission= '$_POST[txtCommission]' ,vMode='$_POST[txtMode]' WHERE  nCashTxnId = '$transid' ";
        $result = mysqli_query($conn, $sql);
}
*/

$sql="SELECT * FROM ".TABLEPREFIX."cashtxn  WHERE nCashTxnId  = '" . addslashes($transid) . "' ";
$sess_back="accountsummary.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch;
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$arr = mysqli_fetch_array($rs);

$txtTransactionNumber = $arr["vModeNo"];

$trdate = $arr["dDate"];
$trdate = explode(" ",$trdate);
$dateonly = $trdate[0];
$_tmp = explode("-",$dateonly);
$year = $_tmp[0];
$month = $_tmp[1];
$day = $_tmp[2];
$txtTransactionDate = $arr["dDate"];
$txtAmount = $arr["nAmount"];
$txtCommission = $arr["nCommission"];
$txtMode = $arr["vMode"];
?>
<script language="javascript" type="text/javascript">
function validateForm()
{
        var frm = document.frmAffMain;
                if(trim(frm.txtTransactionNumber.value) == ""){
                alert("Transaction Number cannot be empty.");
                frm.txtTransactionNumber.focus();
                return false;
        }else if(trim(frm.txtAmount.value) == "" || trim(frm.txtAmount.value) == "0"){
                alert("Amount cannot be empty or zero.");
                frm.txtAmount.focus();
                return false;
        }else if(isNaN(frm.txtAmount.value)){
                alert("Please enter a valid amount");
                frm.txtAmount.focus();
                return false;
        }else if(isNaN(frm.txtCommission.value)){
                alert("Please enter a valid commission");
                frm.txtCommission.focus();
                return false;
        }else if(trim(frm.txtMode.value) == ""){
                alert("Transaction cannot be empty.");
                frm.txtMode.focus();
                return false;
        }else{
                        return true;
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
                        <td width="100%" class="heading_admn boldtextblack" align="left">Transaction Details</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                      <tr>
                        <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
 <form name="frmAffMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onSubmit="return validateForm();">
           <input type="hidden" name="username" value="<? echo htmlentities($username); ?>" />
           <input type="hidden" name="userid" value="<? echo $userid; ?>" />
           <input type="hidden" name="transid" value="<? echo $transid; ?>" />
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr align="right" bgcolor="#FFFFFF">
                                <td colspan="2"><a href='useracdetails.php?userid=<?php echo $userid?>&username=<?php echo urlencode($username)?>'><b>Back</b></a></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><strong>Details for Transaction '<?php echo $transid?>'</strong></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="20%" align="left">Transaction Date</td>
                                <td width="80%" align="left"><?php echo date('F d, Y',strtotime($txtTransactionDate))?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Amount</td>
                                <td align="left"><?php echo CURRENCY_CODE;?><?php echo $txtAmount?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Commission</td>
                                <td align="left"><?php echo $txtCommission?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Mode</td>
                                <td align="left"><?php echo $txtMode?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Reference Number</td>
                                <td align="left"><?php echo htmlentities($txtTransactionNumber)?></td>
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