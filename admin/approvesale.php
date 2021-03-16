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

if(isset($_GET["id"]) && $_GET["id"]!="" )
{
   $var_id = $_GET["id"];
}//end if
else if(isset($_POST["id"]) && $_POST["id"]!="" )
{
   $var_id = $_POST["id"];
}//end else if

$add_flag = false;
$flag_proceed=false;


$sql = "SELECT * FROM ".TABLEPREFIX."saleinter  WHERE  nSaleInterId  = '". addslashes($var_id)."' AND vDelStatus='0' ";
$result  = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if(mysqli_num_rows($result) > 0) 
{
	if($row = mysqli_fetch_array($result)) 
	{
		$add_flag = true;
		$var_saleid = $row["nSaleId"];
		$var_userid = $row["nUserId"];
		$var_date = $row["dDate"];
		$var_method = $row["vMethod"];
		$var_bank = $row["vBank"];
		$var_name = $row["vName"];
		$var_reference = $row["vReferenceNo"];
		$var_referencedate = $row["dReferenceDate"];
		$var_entry_date = $row["dEntryDate"];

		switch($var_method) 
		{
			case "bu" :
						$disp_method = "Business Check";
						break;
			case "ca" :
						$disp_method = "Cashiers Check";
						break;
			case "wt" :
						$disp_method = "Wire Transfer";
						break;
			case "mo" :
						$disp_method = "Money Order";
						break;
			case "pc" :
						$disp_method = "Personal Check";
						break;
		}//end switch

        $sql = "Select u.vLoginName,s.vTitle,sd.nAmount,sd.nPoint,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from ".TABLEPREFIX."saledetails  sd  ";
        $sql .= " inner join ".TABLEPREFIX."sale s on sd.nSaleId = s.nSaleId  inner join ".TABLEPREFIX."users u on sd.nUserId = u.nUserId ";
        $sql .= " where  sd.nSaleId='" . $var_saleid . "' AND sd.nUserId='" . $var_userid . "' AND sd.dDate='";
        $sql .= $var_date . "' ";

        $result_3=mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if(mysqli_num_rows($result_3) > 0)
		{
           $row_3=mysqli_fetch_array($result_3);
           $cost=$row_3["nAmount"];
           $nPoint=$row_3["nPoint"];
		   $var_user_name = $row_3["vLoginName"];
           $var_title=$row_3["vTitle"];
           $reqd=$row_3["nQuantity"];
           if($row_3["vSaleStatus"] == "1" && $row_3["vRejected"] == "0")
		   {
               $flag_proceed=true;
           }//end if
		   
		  
        }//end if
	}//end if
}//end if
else 
{
	echo "<script>alert('Please try again!'); window.location.href='saleapproval.php'</script>";
	exit();
}//end else


if($_POST["postback"] == "Y")
{
		if($flag_proceed == true) 
		{
			// Start of approve sale
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

				$sql = "Select * from ".TABLEPREFIX."sale where nSaleId='" . addslashes($row["nSaleId"]) . "'";
				mysqli_query($conn, $sql);

				$result_2 = mysqli_query($conn, $sql);
				if(mysqli_num_rows($result_2) > 0)
				{
//						if($row_2=mysqli_fetch_array($result_2))
//						{
//								$sql = "Update ".TABLEPREFIX."users set nAccount = nAccount +  $cost  where  nUserId='" . $row_2["nUserId"] . "'";
//								mysqli_query($conn, $sql) or die(mysqli_error($conn));
//						}
                                    //end if
						if($_POST['qtyReq']!='')
						{
						 $sql = "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity - ".$_POST['qtyReq']." where nSaleId ='" . addslashes($var_saleid) . "'";
						
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
						}
						
				}//end if
				echo("<script>alert('Transaction accepted');window.location.href='saleapproval.php'</script>");
				exit();
// End of approve sale
		}//end if
}//end if
else if($_POST["postback"] == "D")
{
	$sql = "Update ".TABLEPREFIX."saleinter set vDelStatus='1' where nSaleInterId='" . addslashes($var_id) . "'";
	mysqli_query($conn, $sql) or die(mysqli_error($conn));
	echo "<script>alert('Transaction entry deleted!'); window.location.href='saleapproval.php'</script>";
	exit();
}
?>
<script language="javascript" type="text/javascript">
function loadFields(){
        var frm = window.document.frmUserProfile;
        var country ="<?php echo $ddlCountry?>";
        var gender ="<?php echo $ddlGender?>";
        var education = "<?php echo $ddlEducation?>";
        if(gender == ""){
                gender = "M";
        }
        if(education == ""){
                education = "GP";
        }
        if(country == ""){
                country = "UnitedStates";
        }
        for(i=0;i<frm.ddlCountry.options.length;i++){
            if(frm.ddlCountry.options[i].text == country){
                        frm.ddlCountry.options[i].selected=true;
                        break;
            }
    }
        for(i=0;i<frm.ddlGender.options.length;i++){
            if(frm.ddlGender.options[i].value == gender){
                        frm.ddlGender.options[i].selected=true;
                        break;
            }
    }
        for(i=0;i<frm.ddlEducation.options.length;i++){
            if(frm.ddlEducation.options[i].value == education){
                        frm.ddlEducation.options[i].selected=true;
                        break;
            }
    }
}


function validateForm() {
	if(document.frmUserProfile.postback.value.length == 0) {
		return false;
	}
	else {
		return true;
	}
}

function clickButton(i) {
	if(i == 0) {
		document.frmUserProfile.postback.value = 'D';
	}
	else if(i == 1) {
		document.frmUserProfile.postback.value = 'Y';
	}
	document.frmUserProfile.submit();
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
                  <td width="78%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
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
                        <td width="84%" class="heading_admn boldtextblack" align="left">Approve Sale</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
   <form name="frmUserProfile" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateForm();">
                                              
												<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr align="right" bgcolor="#FFFFFF">  <input type="hidden"  name="id" value="<?php echo $var_id; ?>" />
                                                <input type="hidden"  name="postback"  id="postback" value="<?php echo $userid; ?>" />
                                <td colspan="2"><a href="<?php echo $_SESSION["backurl"]?>" class="style2"><b>Back</b></a></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="subheader"><strong>Purchase Details</strong></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="20%" align="left">Buyer</td>
                                <td width="80%" align="left"><?php echo $var_user_name?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Title</td>
                                <td align="left"><?php echo htmlentities($var_title); ?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Amount</td>
                                <td align="left"><?php echo CURRENCY_CODE;?> <?php echo $cost?></td>
                      </tr>
                      <tr bgcolor="#FFFFFF">
                                <td align="left"><?php echo POINT_NAME; ?></td>
                                <td align="left"> <?php echo $nPoint?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Quantity</td>
                                <td align="left"><?php echo $reqd?><input name="qtyReq" type="hidden" value="<?php echo $reqd?>" /></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="subheader"><strong>Transaction Details</strong></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Name</td>
                                <td align="left"><input name="txtName" type="text" class="textbox2" id="txtName" size="40" maxlength="100" value="<?php echo htmlentities($var_name)?>"></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Reference No.</td>
                                <td align="left"><INPUT name="txtReferenceNo" type="text" class="textbox2" value="<?php echo htmlentities($var_reference); ?>" size="40" maxlength="100" readonly /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Bank(If Applicable)</td>
                                <td align="left"><INPUT name="txtBank" type="text" class="textbox2" value="<?php echo htmlentities($var_bank); ?>" size="40" maxlength="100" readonly /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Reference Date (mm/dd/yyyy)</td>
                                <td align="left"><INPUT name="txtRefDate" type="text" class="textbox2" value="<?php echo change_date_format(htmlentities($var_referencedate),'mysql-to-mmddyy'); ?>" size="40" maxlength="100" readonly /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Payment Method</td>
                                <td align="left"><?php echo $disp_method?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td align="left"><input name="btnSubmit" type="button" class="submit" value="Approve"  style="width:95px;" onClick="javascript:clickButton(1);"/>
                                                &nbsp;&nbsp;<input name="btnDelete" type="button" class="submit_grey" value="Delete"  onClick="javascript:clickButton(0);" /></td>
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