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
$PGTITLE='settings';

//checking admin enabled listing fee
if(DisplayLookUp('Listing Type')!='1')
{
	header('Location:setconf.php');
	exit();					
}//end if

//activate / deactivate
if(isset($_GET['mode']) && $_GET['mode']=='status')
{
	$nLId = $_GET['nLId'];
        $active = $_GET['active'];
	$sqlDel = "UPDATE ".TABLEPREFIX."listingfee SET vActive='".$active."' WHERE nLId='".$nLId."'";
	mysqli_query($conn, $sqlDel) or die(mysqli_error($conn));
        $successMsg = ($active==1)? 'sa' : 'sd';
	header('location:listing_combinations.php?msg='.$successMsg);
	exit();
}//end if

//delete 
if(isset($_GET['mode']) && $_GET['mode']=='delete')
{
	$nLId = $_GET['nLId'];
	
	$sqlDel = "DELETE FROM ".TABLEPREFIX."listingfee WHERE nLId='".$nLId."'";
	mysqli_query($conn, $sqlDel) or die(mysqli_error($conn));
	header('location:listing_combinations.php?msg=d');
	exit();
}//end if

//ordering content
if(isset($_GET['Action']) && $_GET['Action']=='ordering')
{
	$oldId=$_GET['id'];
	$oldPosition=$_GET['pos'];
	$table=TABLEPREFIX."listingfee";
	$PositionfieldName='nLPosition'; //db field name
	$IdfieldName='nLId'; //db field name
	$returnPath='location:listing_combinations.php';
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
            sortableDb("listingfee", $oldId, $PositionfieldName, $IdfieldName, $_GET['move']);
        }
}//end if

$abrs = select_rows(TABLEPREFIX.'listingfee','nLId',"WHERE vActive='1' and above <> ''");
?>
<link href="../styles/tabcontent.css" rel="stylesheet" type="text/css">
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
     
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="84%" class="heading_admn boldtextblack" align="left">Listing Fee Range </td>
                        <td width="16%" class="heading_admn boldtextblack">&nbsp;</td>
                      </tr>
                    </table>
					<?php include_once('../includes/settings_menu.php');?>
					<div class="tabcontentstyle">
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2"> 
                      <tr>
                        <td align="left" class="noborderbottm" valign="top"><table width="100%"  border="0" cellpadding="0" cellspacing="0" >

                        <?php if(!(mysqli_num_rows($abrs))){?>
                       <tr>
                        <td align="left"><a href="add_listing_combinations.php" class="AddLinks"><b>Add New Range</b></a></td>
                        </tr>
                     <?php
                      }else{
                         echo "<tr><td align='left' class='warning'><br>Note:- You would be allowed to add a new listing fee range only if the 'Above ".CURRENCY_CODE."xxx' range entry is removed
                        </td></tr>";
                      }?>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#ffffff" class="noborderbottm">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="maintext2">
<form name="frmListingFee" id="frmListingFee" ACTION="<?php echo $_SERVER['PHP_SELF']?>" method="post" onSubmit="return Validate();">
<?php
$sql = "SELECT * FROM ".TABLEPREFIX."listingfee ORDER BY nLPosition ASC";

$sess_back= $targetfile .  "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;

//get the total amount of rows returned
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

/*
Call the function:

I've used the global $_GET array as an example for people
running php with register_globals turned 'off' :)
*/

$navigate = pageBrowser($totalrows,10,10,"&ddlCategory=".$ddlCategory."&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql.$navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

switch($_GET['msg'])
{
	case "a":
		$message='Range added successfully.';
	break;
	
	case "e":
		$message='Range updated successfully.';
	break;
	
	case "d":
		$message='Range deleted successfully.';
	break;
        case "sa":
		$message='Range Activated successfully.';
	break;
        case "sd":
		$message='Range Deactivated successfully.';
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
                                <td colspan="6" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>			
                              <tr  class="gray">
                                <td width="8%" align="left">Sl No. </td>
                                <td width="15%" align="left">Range</td>
                                <td width="15%" align="left">Listing Price (%)</td>
                                <td width="15%" align="left">Set Order </td>
                                <!--<td width="15%">Active</td>-->
                                <td width="15%" align="left">Action</td>
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
							$oldId=0;
							while ($arr = mysqli_fetch_array($rs))
						  	{
					  ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="left"><?php echo $cnt;?></td>
                                <td class="maintext" align="left">
                                <?php
                                if($arr["above"]=="" || $arr["above"]==0)
                                        echo CURRENCY_CODE.htmlentities($arr["nFrom"]).'&nbsp;-&nbsp;'.CURRENCY_CODE.htmlentities($arr["nTo"]);
                                    else
                                        echo "Above ".CURRENCY_CODE.$arr["above"];
                                ?>
                                </td>
                                <td class="maintext" align="left"><?php echo htmlentities($arr["nPrice"]);?></td>
                                <td align="left" class="maintext" align="left"><?php if($cnt!=1){?>
	<a href='listing_combinations.php?Action=ordering&move=up&id=<?php echo $arr["nLId"];?>&pos=<?php echo $arr["nLPosition"];?><?php echo $order_category;?>'>

<img src="../images/up.gif" alt="Up" border=0></a>
<?php } ?>
<?php if($cnt!=$totalrows){ ?>
<a href="listing_combinations.php?Action=ordering&move=down&id=<?php echo $arr["nLId"];?>&pos=<?php echo $arr["nLPosition"];?><?php echo $order_category;?>">

<img src="../images/down.gif" alt="Down" border=0></a>
                                    <?php } ?>
                                </td>
                                <!--<td align="center">
                                    
                                    <?php

                                        $statusContent1 = ($arr['vActive']=='1') ? 'Yes' : 'No';
                                        $statusContent = ($arr['vActive']=='0')? 'activate' : 'deactivate';
                                        $statusChange = ($arr['vActive']=='1') ? '0' : '1';
                                        //echo $statusContent1;
                                        //echo "<a href='listing_combinations.php?nLId=".$arr["nLId"]."&mode=status&active=".$statusChange.$order_category."' onClick='javascript:return confirm(\"Are you sure you want to ".$statusContent." this?\");'>".$statusContent1."</a>";

                                    ?>
                                </td>-->
                                <td align="left"><?php echo "<a href='add_listing_combinations.php?nLId=".$arr["nLId"]."&mode=edit".$order_category."&oldId=".$oldId."'>Edit</a>";?>&nbsp;|&nbsp;<?php echo "<a href='listing_combinations.php?nLId=".$arr["nLId"]."&mode=delete".$order_category."' onClick='javascript:return confirm(\"Are you sure you want to delete this?\");'>Delete</a>";?></td>
                              </tr>
					<?php 
								$oldId=$arr["nLId"];
								$cnt++;
							}//end while
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td class="noborderbottm" colspan="6" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
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
					</div>
                  </td>
                </tr>
              </table>
              </div>
              </div>
<?php include_once('../includes/footer_admin.php');?>