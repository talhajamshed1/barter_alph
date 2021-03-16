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
$PGTITLE='swapapproval';

if(isset($_GET["id"]) && $_GET["id"]!="" )
{
   $var_tmpid = $_GET["id"];
}//end if
else if(isset($_POST["id"]) && $_POST["id"]!="" )
{
  $var_tmpid = $_POST["id"];
}//end else if

$flag_proceed = false;

$sql = "Select s.vTitle,u.vLoginName,si.nSwapInterId,si.nSwapId,si.nUserId,si.nAmount,si.vMethod,si.vMode,si.vPostType,si.vName,si.vReferenceNo,";
$sql .= "si.vBank,si.dReferenceDate,si.dEntryDate  from ".TABLEPREFIX."swapinter si inner join ".TABLEPREFIX."swap s on ";
$sql .= " si.nSwapId = s.nSwapId  inner join ".TABLEPREFIX."users u on si.nUserId = u.nUserId where si.nSwapInterId='" . addslashes($var_tmpid) . "' AND ";
$sql .= " s.vSwapStatus='1'  AND si.vDelStatus='0' ";

$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));

if(mysqli_num_rows($result) > 0) 
{
		$row = mysqli_fetch_array($result);

		$flag_proceed = true;

		$var_title = $row["vTitle"];
		$var_cost = $row["nAmount"];
		$var_posttype =$row["vPostType"];
		$var_method = $row["vMethod"];

		$var_reference = $row["vReferenceNo"];
		$var_bank = $row["vBank"];
		$var_refdate = $row["dReferenceDate"];
		$var_entrydate = $row["dEntryDate"];
		$var_name = $row["vName"];
		$var_username = $row["vLoginName"];

		$disp_method="";
		switch($var_method) 
		{
				case "bu" : $disp_method="Business Check";
										break;
				case "ca" : $disp_method="Cashiers Check";
										break;

				case "mo" : $disp_method="Money Order";
										break;

				case "wt" : $disp_method="Wire Transfer";
										break;
				case "pc" : $disp_method="Personal Check";
										break;
		}//end switch
}//end if
else 
{
	echo("<script>alert('Transaction entry not found!');window.location.href=\"swapapproval.php\";</script>");
	exit();
}//end else


if($_POST["postback"] == "Y") 
{
	//start of approval of swap/wish
	/***********  BEGIN ****************/
	//Start of the process of performing the transaction entry

   $db_swapid="";
   $db_userid="";
   $db_amount=0;
   $db_method="";
   $db_mode="";
   $db_post_type="";

   //here the transaction id has to be set that comes from the payment gateway
   //$var_txnid=$cc_tran;

    if($flag_proceed == true)
	{
		//I - Store check details
		$sql = "Insert into ".TABLEPREFIX."paymentdetails(nPaymentId,vName,vReferenceNo,vBank,dReferenceDate,dEntryDate) ";
		$sql .= " Values('','" . addslashes($row["vName"]) . "','" . addslashes($row["vReferenceNo"]) . "','" . addslashes($row["vBank"]) . "','" . addslashes($row["dReferenceDate"]) . "',now())";
		mysqli_query($conn, $sql) or die(mysqli_error($conn));

		$var_txnid = mysqli_insert_id($conn);
        //if you have data for the transaction
        $db_swapid=$row["nSwapId"];
        $db_userid=$row["nUserId"];
        $db_amount=$row["nAmount"];
        $db_method=$row["vMethod"];
        $db_mode=$row["vMode"];
        $db_post_type=$row["vPostType"];
        $var_swapmember="";
        $var_incmember="";

        if($db_mode == "od")
		{
                //if the payment is being made by the person who made the offer
                //that means the present userid is the one that is present in the swaptxn table
                //and this user is giving money to the person who made the swap table entry
                //and the userid is fetched from the table swap
                //swapmember --> the one in the temporary table
                //incmember --> the one who receives the money(comes from the swap table)
                $var_swapmember=$db_userid;

                $sql = "Select nUserId from ".TABLEPREFIX."swap where nSwapId='"
                                    . $db_swapid  . "' ";
               $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
               if(mysqli_num_rows($result) > 0)
			   {
                   if($row=mysqli_fetch_array($result))
				   {
                       $var_incmember=$row["nUserId"];
                   }//end if
               }//end if
       }//end if
       else if($db_mode == "om")
	   {
                //if the payment is being made by the person who accepts the offer(ie. the one who
                //made the main swap item),here the userid is the one in the swap table,hence
                //he has to fetch the swapuserid from the swaptxn table,and give money to him
                //swapmember --> the one in the swaptxn table
                //incmember --> the one who receives the money(comes from the swaptxn table)

                $db_amount = -1 * $db_amount;

                $sql = "Select nUserId from ".TABLEPREFIX."swaptxn where nSwapId='"
                                              . $db_swapid  . "' and vStatus='A'";
                $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if(mysqli_num_rows($result) > 0)
				{
                    if($row=mysqli_fetch_array($result))
					{
                        $var_swapmember=$row["nUserId"];
                        $var_incmember=$row["nUserId"];
                    }//end if
                }//end if
          }//end if
          else if($db_mode == "wm")
		  {
                //if the payment is being made by the person who accepts the offer(ie. the one who
                //made the main swap item),here the userid is the one in the swap table,hence
                //he has to fetch the swapuserid from the swaptxn table,and give money to him
                //swapmember --> the one in the swaptxn table
                //incmember --> the one who receives the money(comes from the swaptxn table)

                $db_amount = -1 * $db_amount;

                $sql = "Select nUserId from ".TABLEPREFIX."swaptxn where nSwapId='"
                                         . $db_swapid  . "' and vStatus='A'";
                $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if(mysqli_num_rows($result) > 0)
				{
                     if($row=mysqli_fetch_array($result))
					 {
                         $var_swapmember=$row["nUserId"];
                         $var_incmember=$row["nUserId"];
                     }//end if
                }//end if
		}//end else if

        $db_swap_ids = get_swaps_ids($db_swapid);

        $db_amount = ($db_amount < 0)?(-1 * $db_amount):$db_amount;
        
        $sql = "Update ".TABLEPREFIX."swap set 
                                     nSwapAmount='$db_amount',
                                     vEscrow='1',
                                     vMethod='$db_method',
                                     vTxnId='$var_txnid',
                                     vSwapStatus='2',dTxnDate=now() where
                                     nSwapId in (" . $db_swap_ids . ") ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//nSwapMember='$var_swapmember',

       

//       $sql = "Update ".TABLEPREFIX."users set nAccount=nAccount + $db_amount
//                                   where nUserId='" . $var_incmember . "' ";
//       mysqli_query($conn, $sql) or die(mysqli_error($conn));

       $sql = "delete from ".TABLEPREFIX."swapinter where nSwapInterId='"
                            . addslashes($var_tmpid) . "' ";
       mysqli_query($conn, $sql) or die(mysqli_error($conn));
	   echo("<script>alert('Transaction approved!');window.location.href=\"swapapproval.php\";</script>");
	   exit();
   }//end if
   else
   {
        echo("<script>alert('Transaction cannot be approved since there is a change in status!');window.location.href=\"swapapproval.php\";</script>");
        exit();
   }//end else
//End of the process of performing the transaction entry
/******** END ******************/
//end of approval of swap/wish
}//end if
else if($_POST["postback"] == "D") 
{
	$sql = "Update ".TABLEPREFIX."swapinter set vDelStatus='1' where nSwapInterId='" . addslashes($var_tmpid) . "'";
	mysqli_query($conn, $sql) or die(mysqli_error($conn));
    echo("<script>alert('Transaction deleted!');window.location.href=\"swapapproval.php\";</script>");
	exit();
}//end if
?>
<script language="javascript" type="text/javascript">
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
                  <td width="78%" valign="top">
                   
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="100%" class="heading_admn boldtextblack" align="left">Approve Swap</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2">
 <form name="frmUserProfile" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateForm();">
                  <?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr align="right" bgcolor="#FFFFFF"><input type="hidden"  name="id" value="<?php echo $var_tmpid?>" />
                                                <input type="hidden"  name="postback"  id="postback" value="" />
                                <td colspan="2"><a href="<?php echo $_SESSION["backurl"]?>" class="style2"><strong>Back</strong></a></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><strong> Details</strong></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="20%" align="left">User</td>
                                <td width="80%" align="left"><?php echo htmlentities($var_username)?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Title</td>
                                <td align="left"><?php echo htmlentities($var_title)?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Amount</td>
                                <td align="left"><?php echo CURRENCY_CODE;?> <?php echo $var_cost?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Post Type</td>
                                <td align="left"><?php echo $var_posttype?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><strong>Transaction Details</strong></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Name</td>
                                <td align="left"><input name="txtName" type="text" class="textbox" id="txtName" size="40" maxlength="100" value="<?php echo htmlentities($var_name)?>" readonly></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Reference No.</td>
                                <td align="left"><input name="txtReferenceNo" type="text" class="textbox" value="<?php echo htmlentities($var_reference); ?>" size="40" maxlength="100" readonly /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Bank(If Applicable)</td>
                                <td align="left"><input name="txtBank" type="text" class="textbox" value="<?php echo htmlentities($var_bank); ?>" size="40" maxlength="100" readonly /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Reference Date</td>
                                <td align="left"><input name="txtRefDate" type="text" class="textbox" value="<?php echo date("m-d-Y",strtotime($var_refdate)); ?>" size="40" maxlength="100" readonly /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Entry Date (mm/dd/yyyy)</td>
                                <td align="left"><?php echo change_date_format($var_entrydate,'mysql-to-mmddyy')?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Payment Method</td>
                                <td align="left"><?php echo $disp_method?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td align="left"><input name="btnSubmit" type="button" class="submit" value="Approve Swap"  style="width:95px;" onClick="javascript:clickButton(1);"/>
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