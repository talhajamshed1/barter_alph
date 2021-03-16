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

$qryopt="";

if($_POST["affid"] != "")
{
   $affid = $_POST["affid"];
}//end if
else if($_GET["affid"] != "")
{
   $affid = $_GET["affid"];
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

if($_POST[btnSaveChanges] == "Save Changes")
{
   $sql = "UPDATE ".TABLEPREFIX."cashtxn SET vModeNo= '$_POST[txtTransactionNumber]', nAmount='$_POST[txtAmount]', nCommission= '$_POST[txtCommission]' ,vMode='$_POST[txtMode]' WHERE  nCashTxnId = '$transid' ";
   $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
}//end if


$sql="SELECT * FROM ".TABLEPREFIX."cashtxn  WHERE nCashTxnId  = '$transid' ";
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
$txtTransactionDate = $month."/".$day."/".$year;
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
                        <td width="84%" class="heading_admn boldtextblack" align="left"> Edit Details for Transaction '<?php echo $txtTransactionNumber;?>'</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
 <form name="frmAffMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onSubmit="return validateForm();">
           <input type="hidden" name="username" value="<?echo $username;?>" />
           <input type="hidden" name="affid" value="<?echo $affid;?>" />
           <input type="hidden" name="transid" value="<?echo $transid;?>" />
		   <?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr align="right" bgcolor="#FFFFFF">
                                <td colspan="2"><a href='useracdetails.php?affid=<?php echo $affid?>&username=<?php echo $username?>'><strong>Back</strong></a>&nbsp;</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="20%" align="left">Transaction Number</td>
                                <td width="80%" align="left"><input type="text" name= "txtTransactionNumber" value="<?php echo $txtTransactionNumber?>" class="textbox"></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Transaction Date</td>
                                <td align="left"><?php echo $txtTransactionDate?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Amount</td>
                                <td align="left"><input type="text" name= "txtAmount" value="<?php echo $txtAmount?>"></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Commission</td>
                                <td align="left"><input type="text" name= "txtCommission" value="<?php echo $txtCommission?>"></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Mode</td>
                                <td align="left"><input type="text" name= "txtMode" value="<?php echo $txtMode?>"></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td align="left"><input type="submit" name= "btnSaveChanges" value="Save Changes" class="submit"></td>
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