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

$activateflag ="";
if($_POST["postback"] == "CS")
{ //for changing status

   $var_tmpid = $_POST["swapinterid"];

   if($_POST["changeto"] == "A")
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



      //$sql= "Select * from ".TABLEPREFIX."swapinter where nSwapInterId='" . addslashes($var_tmpid) . "' AND vDelStatus='0' ";
	  $sql = "Select si.nSwapInterId,si.nSwapId,si.nUserId,si.nAmount,si.vMethod,si.vMode,si.vPostType,si.vName,si.vReferenceNo,";
	  $sql .= "si.vBank,si.dReferenceDate  from ".TABLEPREFIX."swapinter si inner join ".TABLEPREFIX."swap s on ";
	  $sql .= " si.nSwapId = s.nSwapId where si.nSwapInterId='" . addslashes($var_tmpid) . "' AND ";
	  $sql .= " s.vSwapStatus='1'  AND si.vDelStatus='0' ";

      $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));

      if(mysqli_num_rows($result) > 0)
	  {
         if($row=mysqli_fetch_array($result))
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
          }//end else if
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

           

//           $sql = "Update ".TABLEPREFIX."users set nAccount=nAccount + $db_amount
//                                                                          where nUserId='" . $var_incmember . "' ";
//           mysqli_query($conn, $sql) or die(mysqli_error($conn));

           $sql = "delete from ".TABLEPREFIX."swapinter where nSwapInterId='"
                               . addslashes($var_tmpid) . "' ";
                              mysqli_query($conn, $sql) or die(mysqli_error($conn));
		   echo("<script>alert('Transaction approved!');</script>");
         }//end if
       }//end if
       else
	   {
           echo("<script>alert('Transaction cannot be approved since there is a change in status!');</script>");
       }//end else

//End of the process of performing the transaction entry

/******** END ******************/
//end of approval of swap/wish
   }//end if
}//end if

$qryopt="";
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

if($txtSearch != "")
{
        if($ddlSearchType == "username")
		{
             $qryopt .= " AND u.vLoginName like '" . addslashes($txtSearch) . "%'";
        }//end if
		else if($ddlSearchType == "bank")
		{
             $qryopt .= " AND si.vBank  like '" . addslashes($txtSearch) . "%'";
        }//end if
		else if($ddlSearchType == "referenceno") 
		{
             $qryopt .= " AND si.vReferenceNo  like '" . addslashes($txtSearch) . "%'";
		}//end else if
		/*elseif($ddlSearchType == "date"){
                $date = $txtSearch;
                $arr = split("/",$date);
                if(strlen($arr[0]) < 2){
                        $month = "0".$arr[0];
                }else{
                        $month = $arr[0];
                }
                if(strlen($arr[1]) < 2){
                        $day = "0".$arr[1];
                }else{
                        $day = $arr[1];
                }
                $year = $arr[2];
                $newdate = $year ."-". $month ."-". $day;
                $qryopt .= " WHERE dDateReg  like '" . addslashes($newdate) . "%'";
        }*/
}//end if
if(!isset($begin) || $begin =="")
{
     $begin = 0;
}//end if

$sql = "Select s.vTitle,u.vLoginName,si.nSwapInterId,si.nSwapId,si.nUserId,si.dDate,si.vName,si.vBank,si.vReferenceNo,si.dReferenceDate,date_format(si.dEntryDate,'%m/%d/%Y') as 'dEntryDate'  from ".TABLEPREFIX."swapinter si ";
$sql .= " inner join ".TABLEPREFIX."swap s on si.nSwapId = s.nSwapId  inner join ".TABLEPREFIX."users u on si.nUserId = u.nUserId ";
$sql .= " Where si.vDelStatus='0' ";

$sess_back="swapapproval.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $ddlSearchType . "&txtSearch=" . urlencode($txtSearch) . "&";
//$sess_back="users.php?begin=" . $_GET[begin] . "&num=" . $_GET[num] . "&numBegin=" . $_GET[numBegin] . "&ddlSearchType=" . $ddlSearchType . "&txtSearch=" . $txtSearch;

$_SESSION["backurl"] = $sess_back;

$sql .= $qryopt . " Order By si.dEntryDate Desc ";
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,10,10,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql.$navigate[0];

$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
?>
<script language="javascript" type="text/javascript">
function clickSearch()
{
        document.frmAdminMain.submit();
}
function changeStatus(id) {
                var frm = document.frmAdminMain;
        if(confirm("Are you sure you want to approve this transaction?")){
                frm.changeto.value="A";
                frm.swapinterid.value=id;
                frm.postback.value="CS";
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
                        <td width="100%" class="heading_admn boldtextblack" align="left">Swap To be Approved</td>
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
<tr bgcolor="#FFFFFF"><input type="hidden" name="swapinterid" value="">
<input type="hidden" name="changeto" value="">
<input type="hidden" name="postback" value="">
                                <td colspan="6" align="center"><table border="0" width="100%" class="maintext">
                                        <tr>
                                                <td valign="top" align="right">
                                                Search
&nbsp;<select name="ddlSearchType" class="textbox2">
                                        <option value="username" <?php if($ddlSearchType== "username" || $ddlSearchType == ""){ echo("selected"); } ?>>User Name</option>
                                        <option value="bank" <?php if($ddlSearchType == "bank"){ echo("selected"); } ?>>Bank</option>
                                        <option value="referenceno" <?php if($ddlSearchType== "referenceno"){ echo("selected"); } ?>>Reference No</option>
                                    </select>               &nbsp;
               <input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                          </td>
                                                <td align="left" valign="baseline">
                                                <a href="javascript:clickSearch();" class="link_style2">
                                                GO</a>
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
                                <td align="center" width="20%">Approve</td>
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
					  ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="center"><?php echo $cnt;?></td>
                                <td align="center" class="maintext"><?php echo '<a href="approveswap.php?id='.$arr["nSwapInterId"].'">'.restrict_string_size($arr["vTitle"],20).'</a>';?></td>
                                <td align="center"><?php echo '<a href="approveswap.php?id='.$arr["nSwapInterId"].'">'.restrict_string_size($arr["vLoginName"],20).'</a>';?></td>
                                <td align="center"><?php echo '<a href="approveswap.php?id='.$arr["nSwapInterId"].'">'.date('F d, Y',strtotime($arr["dEntryDate"])).'</a>';?></td>
                                <td align="center"><?php echo '<a href="approveswap.php?id='.$arr["nSwapInterId"].'">'.htmlentities($arr['vReferenceNo']).'</a>';?></td>
                                <td align="center"><?php  $statustextorlink = "<a href=\"javascript:changeStatus('".$arr["nSwapInterId"]."');\">Activate</a>";
											echo $statustextorlink;?></td>
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