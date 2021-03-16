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
function func_wish_detailed($uid = 0,$fid = 0)
{
  global $conn;
	$now=mktime(date('H')-24,date('i'),date('s'),date('m'),date('d'),date('Y'));
	$date=date('Y-m-d H:i:s',$now);
	$sql = "delete from ".TABLEPREFIX."swaptemp where dDate <= '" . $date . "'";
	mysqli_query($conn, $sql) or die(mysqli_error($conn));
?>
<table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmSwap" id="frmSwap" ACTION="<?php echo $_SERVER['PHP_SELF']?>" method="post">
<?php
$txtSearch="";
$cmbSearchType="";
$var_rf="";
$var_no="";
if($_GET["txtSearch"] != "")
{
   $txtSearch = $_GET["txtSearch"];
   $cmbSearchType =  $_GET["cmbSearchType"];
}//end if
else if($_POST["txtSearch"] != "")
{
  $txtSearch = $_POST["txtSearch"];
  $cmbSearchType =  $_POST["cmbSearchType"];
}//end else if
if($_REQUEST['num']!='') { 
$page   = $_REQUEST['num'];
}
else {
    $page   =   1 ;
}
$qryopt="";
if($txtSearch != "")
{
   if($cmbSearchType == "category")
   {
      $qryopt .= "  AND L.vCategoryDesc like '%" . addslashes($txtSearch) . "%'";
   }//end if
   else if($cmbSearchType == "title")
   {
     $qryopt .= " AND vtitle like '%" . addslashes($txtSearch) . "%'";
   }//end else if
   else if($cmbSearchType == "user")
   {
      $qryopt .= "  AND vLoginName like '%" . addslashes($txtSearch) . "%'";
   }//end else if
}//end if

$sql = "SELECT s.nSwapId,s.vTitle,date_format(s.dPostDate,'%m/%d/%Y') as 'dPostDate',
              L.vCategoryDesc,s.vSwapStatus,s.vDelStatus,u.vLoginName as 'UserName'
              FROM ".TABLEPREFIX."swap s
                  left join ".TABLEPREFIX."category c on s.nCategoryId = c.nCategoryId 
                  left join ".TABLEPREFIX."users u on s.nUserId=u.nUserId
                  LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
              where 
                s.vPostType='wish' AND s.vDelStatus='0' ";

$targetfile="";
$detailfile="";
if($uid === 0)
{
  $detailfile="swapitem.php";
}//end if
else if($uid > 0)
{
  $detailfile="swapitem.php";
}//end else if

$sql .= $qryopt . "  ORDER BY s.dPostDate DESC ";
$sess_back= $targetfile .  "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;

//get the total amount of rows returned
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

/*
Call the function:


I've used the global $_GET array as an example for people
running php with register_globals turned 'off' :)
*/

$navigate = pageBrowser($totalrows,10,10,"&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql.$navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);

if(isset($message) && $message!='')
{
?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="6" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>			
<tr bgcolor="#FFFFFF"><input name="postback" type="hidden" id="postback">
<input NAME="rf" TYPE="hidden" id="rf" VALUE="<?php echo $var_rf?>">
<input NAME="no" TYPE="hidden" id="no" VALUE="<?php echo $var_no?>">
<input name="uname" TYPE="hidden" id="uname" VALUE="<?php echo htmlentities($var_uname)?>">

                                <td colspan="6" align="center"><table border="0" width="100%" class="maintext">
                                        <tr>
                                                <td valign="top" align="right">
                                                Search
                                         &nbsp; <select name="cmbSearchType" class="textbox2">
                <option value="category"  <?php if($cmbSearchType == "category" || $cmbSearchType == ""){ echo("selected"); } ?>>Category</option>
                <option value="title" <?php if($cmbSearchType == "title"){ echo("selected"); } ?>>Title</option>
                <option value="user" <?php if($cmbSearchType == "user"){ echo("selected"); } ?>>User Name</option>
              </select>
               &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                                </td>
                                                <td align="left" valign="baseline">
                                                <a href="javascript:document.forms['frmSwap'].submit();" class="link_style2">Go</a>
                                                </td>
                                        </tr>
                                </table></td>
    </tr>  
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td width="7%" align="center" valign="middle">Sl No. </td>
                                <td width="16%" align="center" valign="middle">Category</td>
                                <td width="19%" align="center" valign="middle">Title</td>
                                <td width="19%" align="center" valign="middle">Date</td>
                                <td width="19%" align="center" valign="middle">User Name</td>
                                <td width="20%" align="center" valign="middle">Status</td>
                      </tr>
					  <?php
					     if(mysqli_num_rows($rs)>0)
						 {
						  	$cnt=1;
                                                         if ($page == 1) {
                                                            $cnt = 1;
                                                            } else {
                                                        $cnt = (($page - 1) * 10) + 1;
                                                        }
							while ($arr = mysqli_fetch_array($rs))
						  	{
						  		
						  		$username = htmlentities($arr['UserName']);
						  		if(strlen($arr['UserName'])>10)
						  		{
						  			$username = substr(htmlentities($arr['UserName']),0,10)."...";
						  		}
						  			
						  		$item_title = htmlentities($arr['vTitle']);
						  		if(strlen($arr['vTitle'])>20)
						  		{
						  			$item_title = substr(htmlentities($arr['vTitle']),0,20)."...";
						  		}
			
						  		
					  ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="center" valign="middle"><?php echo $cnt;?></td>
                                <td align="center" valign="middle"><?php echo '<a href="'.$detailfile.'?swapid='.$arr["nSwapId"].'&source=w" title="Click Here to Edit/Delete">'.htmlentities($arr["vCategoryDesc"]).'</a>';?></td>
                                <td align="center" valign="middle" class="maintext2"><?php echo '<a href="'.$detailfile.'?swapid='.$arr["nSwapId"].'&source=w" title="Click Here to Edit/Delete">'.$item_title.'</a>';?></td>
                                <td align="center" valign="middle"><?php echo '<a href="'.$detailfile.'?swapid='.$arr["nSwapId"].'&source=w" title="Click Here to Edit/Delete">'.date('F d, Y',strtotime($arr["dPostDate"])).'</a>';?></td>
                                <td align="center" valign="middle"><?php echo '<a href="'.$detailfile.'?swapid='.$arr["nSwapId"].'&source=w" title="Click Here to Edit/Delete">'.$username.'</a>';?></td>
                                <td align="center" valign="middle"><?php echo '<a href="'.$detailfile.'?swapid='.$arr["nSwapId"].'&source=w" title="Click Here to Edit/Delete">'.(($arr["vSwapStatus"] == "0" && $arr["vDelStatus"] == "0")?"Active":(($arr["vSwapStatus"] != "0")?"Processed":"Deleted")).'</a>';?></td>
                              </tr>
					<?php 
								$cnt++;
							}//end while
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="6" class="noborderbottm" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
  <tr>
    <td align="left"><?php echo($navigate[2]);?></td>
    <td align="right"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
  </tr>
</table>
</td>
                      </tr>
  </form>
</table>
<?php }//end function?>							