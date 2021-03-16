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

if($_GET["saleid"]!="")
{
        $saleid=$_GET["saleid"];
        $quantityREQD = $_GET["reqd"];
        $amount = $_GET["amnt"];
        $total = $_GET["tot"];
        $source=$_GET["source"];
		$now=$_GET["dt"];
		$amount=($amount == "")?($total/$quantityREQD):$amount;
}//end if


$sql ="SELECT s.nSaleId,s.nUserId,s.vTitle,s.nQuantity,s.nShipping,s.nValue,u.vLoginName,u.vAddress1,u.vAddress2,u.vCity,
			u.vState,u.vCountry,";
$sql .= "u.nZip,u.vFax,u.vEmail from ".TABLEPREFIX."sale s inner join ".TABLEPREFIX."users u ";
$sql .= " on s.nUserId = u.nUserId where s.nSaleId  = '$saleid' ";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if(mysqli_num_rows($result)>0)
{
    while($row = mysqli_fetch_array($result))
	{
        $User=$row["nUserId"];
        $Title=$row["vTitle"];
        $QuantityAVL=$row["nQuantity"];
        $ShipingPrice=$row["nShipping"];
        $Price=$row["nValue"];
		$var_login=$row["vLoginName"];
		$var_address1=$row["vAddress1"];
		$var_address2=$row["vAddress2"];
		$var_city=$row["vCity"];
		$var_state=$row["vState"];
		$var_country=$row["vCountry"];
		$var_zip=$row["nZip"];
		$var_fax=$row["vFax"];
		$var_email=$row["vEmail"];
    }//end while
}//end if
?>

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
 							<tr align="right" bgcolor="#FFFFFF">
                                <td colspan="2"><a href='useracdetails_other.php?userid=<?php echo $_GET['userid'];?>&username=<?php echo $_GET['username'];?>'><b>Back</b></a></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="subheader">Contact Information</td>
                                </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="23%" align="left">Name</td>
                                <td width="77%" align="left"><?php echo htmlentities($var_login)?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Address1</td>
                                <td align="left"><?php echo htmlentities($var_address1)?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Address2</td>
                                <td align="left"><?php echo htmlentities($var_address2)?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">City</td>
                                <td align="left"><?php echo htmlentities($var_city)?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">State</td>
                                <td align="left"><?php echo htmlentities($var_state)?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Country</td>
                                <td align="left"><?php echo htmlentities(country)?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Zip</td>
                                <td align="left"><?php echo $var_zip?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Fax</td>
                                <td align="left"><?php echo htmlentities($var_fax)?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Email</td>
                                <td align="left"><?php echo htmlentities($var_email)?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="subheader">Sale Details</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Title</td>
                                <td align="left"><?php echo htmlentities($Title)?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Quantity</td>
                                <td align="left"><?php echo $quantityREQD?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Amount [Including Shipping]</td>
                                <td align="left"> <?php echo CURRENCY_CODE;?> <?php echo $amount?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Amount Total</td>
                                <td align="left"><?php echo CURRENCY_CODE;?> <?php echo $total?></td>
                              </tr>
                            </table></td>
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