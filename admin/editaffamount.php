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
$PGTITLE='editaffamount';

if(isset($_GET["affid"]) || $_GET["affid"]!="" )
{
   $affid = $_GET["affid"];
}//end if
else if(isset($_POST["affid"]) || $_POST["affid"]!="" )
{
   $affid = $_POST["affid"];
}//end else if

if($_POST["btnSubmit"] =="Settle Amount")
{
   $txtOldAmount = $_POST["txtOldAmount"];
   $txtAmount = $_POST["txtAmount"];
   if($txtAmount > $txtOldAmount)
   {
       $message = "Please enter an amount less than or equal to ".$txtOldAmount ;
   }//end if
   else
   {
       $txtOldAmount -= $txtAmount;
       $sql = "UPDATE ".TABLEPREFIX."affiliate SET ";
       $sql .= " nAmount='".$txtOldAmount."' WHERE nAffiliateId ='".$affid."' ";
       mysqli_query($conn, $sql) or die(mysqli_error($conn));
	   
      if($txtOldAmount == 0)
	  {
           header("location:affsettlements.php");
           exit();
      }//end if
      $message = "Amount Settled successfully!";
  }//end else
}//end if

$sqluserdetails = "SELECT vLoginName, vFirstName, vLastName, nAmount  FROM ".TABLEPREFIX."affiliate  WHERE  nAffiliateId  = '".$affid."'";
$resultuserdetails  = mysqli_query($conn, $sqluserdetails ) or die(mysqli_error($conn));
$rowuser = mysqli_fetch_array($resultuserdetails);
$txtUserName = $rowuser["vLoginName"];
$txtFirstName = $rowuser["vFirstName"];
$txtLastName = $rowuser["vLastName"];

if($txtLastName !="")
{
   $userfullname = $txtFirstName. " ". $txtLastName;
}//end if
else
{
   $userfullname = $txtFirstName;
}//end else 
$txtOldAmount = $rowuser["nAmount"];
?>
<script language="javascript" type="text/javascript">
function validateAmountForm()
{
        var frm = window.document.frmUserProfile;
                if(trim(frm.txtAmount.value) == ""){
                alert("Amount cannot be empty.");
                frm.txtAmount.focus();
                return false;
        }else if(isNaN(frm.txtAmount.value)){
                alert("Please enter a valid amount.");
                frm.txtAmount.focus();
                return false;
        }else if( parseFloat(frm.txtAmount.value) > parseFloat(frm.txtOldAmount.value)){
                        alert("Please enter an amount less than or equal to " + frm.txtOldAmount.value );
                        frm.txtAmount.focus();
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
                        <td width="84%" class="heading_admn boldtextblack" align="left">Edit Amount for '<?php echo $userfullname?>'</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmUserProfile" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateAmountForm();">
<input type="hidden"  name="affid" value="<?php echo $affid; ?>" />
<input type="hidden"  name="username" value="<?php echo $username; ?>" />
<input type="hidden"  name="txtOldAmount" value="<?php echo $txtOldAmount;?>" />
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr align="right" bgcolor="#FFFFFF">
                                <td colspan="2"><a href="<?php echo $_SESSION["backurl"]?>"><strong>Back</strong></a>&nbsp;</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="20%" align="left">First Name</td>
                                <td width="80%" align="left"><?php echo stripslashes($txtFirstName); ?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Last Name</td>
                                <td align="left"><?php echo stripslashes($txtLastName);?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Amount to be Settled</td>
                                <td align="left"><b><?php echo $txtOldAmount;?></b></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Amount</td>
                                <td align="left"><input type="text" name="txtAmount" size="7" maxlength="10"  class="textbox" />&nbsp;&nbsp;&nbsp;<input type="submit" name="btnSubmit" value="Settle Amount" class="submit"/></td>
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