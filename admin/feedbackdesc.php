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
$PGTITLE='feedback';

if($_POST["btnSubmit"] =="Back")
{
  echo "<script>history.back;</script>";
}//end if
else
{
  $nFBid=$_GET['nFBid'];
  
  $sql="SELECT   f.nFBId ,f.nUserId,f.vTitle ,f.vMatter,date_format(f.dPostDate,'%m/%d/%Y') as 'dPostDate' ,u.vLoginName,u.vFirstName,u.vLastName,f.vStatus FROM";
  $sql .=" ".TABLEPREFIX."userfeedback as f,".TABLEPREFIX."users as u where   nFBId=$nFBid and f.nUserFBId=u.nUserId";
  $excqry=mysqli_query($conn, $sql) or die("Could Not Exicute:".mysqli_error($conn));
  
  list($nFBid,$nPostedUid,$txtTitle,$txtDescription,$txtPostdate,$txtLoginName,$txtFirstName,$txtLastName,$txtStatus)=mysqli_fetch_row($excqry);
  
  $sql_against="SELECT vLoginName from ".TABLEPREFIX."users where nUserId=".$nPostedUid;
  $excqry=mysqli_query($conn, $sql_against) or die("Could Not Exicute:".mysqli_error($conn));
  
  list($txtAgainstLoginName)=mysqli_fetch_row($excqry);
  
//check status
	switch($txtStatus)
	{
		case "S":
			$showStatus='Satisfied';
		break;
										
		case "D":
			$showStatus='Dissatisfied';
		break;
										
		case "N":
			$showStatus='Neutral';
		break;
	}//end switch  
}
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
                        <td width="100%" class="heading_admn boldtextblack" align="left">Feedback Details</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                      <tr>
                        <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmFeedBack" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr bgcolor="#FFFFFF"><input type=hidden name="nFBid" value="<?php echo $nFBid?>">
                                <td width="20%" align="left">Posted By</td>
                                <td width="80%" align="left"><?php echo htmlentities(stripslashes($txtLoginName))?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">User</td>
                                <td align="left"><?php echo htmlentities(stripslashes($txtAgainstLoginName))?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Name</td>
                                <td align="left"><?php if($txtFirstName!='') { echo  htmlentities(stripslashes($txtFirstName))." ".htmlentities(stripslashes($txtLastName)); }else { echo "not provided";}?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Title</td>
                                <td align="left"><?php echo htmlentities(stripslashes($txtTitle))?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Posted Date</td>
                                <td align="left"><?php echo date('m/d/Y',strtotime($txtPostdate));?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Matter</td>
                                <td align="left"><?php echo htmlentities(stripslashes($txtDescription))?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Rating</td>
                                <td align="left"><?php echo htmlentities($showStatus);?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td align="left" class="maintext"><b>&lsaquo;&lsaquo;&nbsp;</b><a href="<?php echo $_SESSION["backurl_feed"]?>"><b>Back</b></a></td>
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
