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
$PGTITLE='plans';

//delete 
if(isset($_GET['mode']) && $_GET['mode']=='delete')
{
	$nPlanId = $_GET['nPlanId'];
	//check if it in use
	$check_category=mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."users WHERE nPlanId='".$nPlanId."'") or die (mysqli_error($conn));
	if(mysqli_num_rows($check_category)>0)
	{
		header('location:plans.php?msg=dn');
		exit();
	}//end if
	else
	{
		$sqlDel = "DELETE FROM ".TABLEPREFIX."plan WHERE nPlanId='".$nPlanId."'";
		mysqli_query($conn, $sqlDel) or die(mysqli_error($conn));

                $sqlDel_1 = "DELETE FROM ".TABLEPREFIX."plan_lang WHERE plan_id='".$nPlanId."'";
		mysqli_query($conn, $sqlDel_1) or die(mysqli_error($conn));
                
		header('location:plans.php?msg=d');
		exit();
	}//end else
}//end if

//ordering content
if(isset($_GET['Action']) && $_GET['Action']=='ordering')
{
	$oldId=$_GET['id'];
	$oldPosition=$_GET['pos'];
	$table=TABLEPREFIX."plan";
	$PositionfieldName='nPosition'; //db field name
	$IdfieldName='nPlanId'; //db field name
	$returnPath='location:plans.php';

 /*
	if(isset($_GET['move']) && $_GET['move']=='up')
	{
		OrderUp($table,$oldId,$oldPosition,$PositionfieldName,$IdfieldName,$returnPath);
		listing();
	}//end if

	if(isset($_GET['move']) && $_GET['move']=='down')
	{
		OrderDown($table,$oldId,$oldPosition,$PositionfieldName,$IdfieldName,$returnPath);
		listing();
	}//end if
*/
        if(isset($_GET['move'])) {
            sortableDb("plan", $oldId, $PositionfieldName, $IdfieldName, $_GET['move']);
        }

}//end if
?>
<div class="row admin_wrapper">
	<div class="admin_container">
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td width="18%" valign="top"> <!--  Admin menu comes here -->
								 <?php require("../includes/adminmenu.php"); ?>
							<!--   Admin menu  comes here ahead --></td>
			<td width="4%" valign="top"></td>
		  <td width="78%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
			</tr>
		  </table>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="94%" height="32" class="headerbg">&nbsp;</td>
			  <td width="6%" align="right" valign="bottom" class="headerbg"><a href="adminmain.php"><img src="../images/home-icon1.gif" width="44" height="25" border="0"></a></td>
			</tr>
		  </table>
			<table width="100%"  border="0" cellspacing="0" cellpadding="10">
			  <tr>
				<td width="84%" class="heading_admn boldtextblack" align="left">Plan </td>
				<td width="16%" class="heading_admn">&nbsp;</td>
			  </tr>
			</table>
			<table width="100%"  border="0" cellspacing="0" cellpadding="10">
			  <tr>
				<td align="left" valign="top" style="padding:20px 0; ">
					<table width="100%"  border="0" cellpadding="5" cellspacing="1" class="AddLinks">
					<tr>
					<td align="left"><a href="add_plan.php<?php if($_POST['ddlCategory']!=''){echo '?ddlCategory='.$_POST['ddlCategory'];}?>" class="AddLinks">Add New Plan</a></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top">
			
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
				<?php
				$sql = "SELECT * 
						  FROM ".TABLEPREFIX."plan P
						  JOIN ".TABLEPREFIX."plan_lang L
							ON P.nPlanId = L.plan_id
						 WHERE L.lang_id = '".$_SESSION["lang_id"]."'
					  ORDER BY nPosition ASC";
	
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
	
				switch($_GET['msg'])
				{
						case "a":
								$message='Plan add successfully.';
						break;
	
						case "e":
								$message='Plan updated successfully.';
						break;
	
						case "d":
								$message='Plan deleted successfully.';
						break;
	
						case "dn":
								$message='This Plan cannot be deleted since it is in use!';
						break;
	
						default:
								$message='';
						break;
	
				}//end if
				?>
				<?php
				if(isset($message) && $message!='')
				{
				?>
		  <tr bgcolor="#FFFFFF">
			<td colspan="5" align="center" class="warning"><?php echo $message;?></td>
		  </tr>
			<?php  }//end if?>
		  <tr align="center" bgcolor="#FFFFFF" class="gray tbl-header-custom-2">
			<td width="10%">Sl No. </td>
			<td width="27%" style="text-align: left;">Title</td>
			<td width="21%">Set Order </td>
			<td width="21%">Active</td>
			<td width="21%">Action</td>
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
		
		$count=mysqli_num_rows($rs);
		while ($arr = mysqli_fetch_array($rs))
		{
	?>
		  <tr bgcolor="#FFFFFF" class="tbl-header-custom-3">
			<td align="center"><?php echo $cnt;?></td>
			<td class="maintext"><?php echo htmlentities($arr["vPlanName"]);?></td>
			<td align="center" class="maintext"><?php if($cnt!=1){?>
			<a href='plans.php?Action=ordering&move=up&id=<?php echo $arr["nPlanId"];?>&pos=<?php echo $arr["nPosition"];?>'>
			
			<img src="../images/up.gif" alt="Up" border=0></a>
			<?php } ?>
			<?php if($cnt!=$totalrows){?>
			<a href="plans.php?Action=ordering&move=down&id=<?php echo $arr["nPlanId"];?>&pos=<?php echo $arr["nPosition"];?>">
			
			<img src="../images/down.gif" alt="Down" border=0></a>
			<?php } ?>
			</td>
			<td align="center"><?php if($arr['vActive']=='1'){echo 'Yes';}else{echo 'No';}?></td>
			<td align="center"><?php echo "<a href='add_plan.php?nPlanId=".$arr["nPlanId"]."&mode=edit".$order_category."'>Edit</a>";?>&nbsp;|&nbsp;<?php echo "<a href='plans.php?nPlanId=".$arr["nPlanId"]."&mode=delete".$order_category."' onclick='return confirm(\"Are you sure to delete?\")'>Delete</a>";?></td>
		  </tr>
	<?php 
			$cnt++;
		}//end while
	}//end if
	?>
	  <tr bgcolor="#FFFFFF">
		<td colspan="5" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
	<tr>
	<td align="left" class="table-pagination"><?php echo($navigate[2]);?></td>
	<td align="right" class="table-pagination"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
	</tr>
	</table></td>
	</tr>
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