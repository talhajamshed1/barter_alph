<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                      |
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
$PGTITLE='help_admin';

//delete 
if(isset($_GET['mode']) && $_GET['mode']=='delete')
{
	$nHId = $_GET['nHId'];
	$ddlCategory = $_GET['ddlCategory'];
	//remove deleted Help file from the Help folder 
	@unlink("../help/".$_GET['hfile']);

        $sqlDel = "DELETE FROM ".TABLEPREFIX."help WHERE nHId='".$nHId."'";
	mysqli_query($conn, $sqlDel) or die(mysqli_error($conn));

        $sqlDel2 = "DELETE FROM ".TABLEPREFIX."help_lang WHERE help_id ='".$nHId."'";
	mysqli_query($conn, $sqlDel2) or die(mysqli_error($conn));
	header('location:help_admin.php?msg=d&ddlCategory='.$ddlCategory);
	exit();
}//end if

//ordering content
if(isset($_GET['Action']) && $_GET['Action']=='ordering')
{
	$oldId=$_GET['id'];
	$oldPosition=$_GET['pos'];
	$table=TABLEPREFIX."help";
	$PositionfieldName='nHposition'; //db field name
	$IdfieldName='nHId'; //db field name
	$returnPath='location:help_admin.php';
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
            $sqlO = "SELECT h.nHId
                   FROM ".TABLEPREFIX."help h
              LEFT JOIN ".TABLEPREFIX."helpcategory hc
                     ON h.nHcId=hc.nHcId
                   JOIN ".TABLEPREFIX."helpcategory_lang hct
                     ON hc.nHcId = hct.help_cat_id
                   JOIN ".TABLEPREFIX."help_lang hp
                     ON h.nHId = hp.help_id
                    AND hp.lang_id = '".$_SESSION["lang_id"]."'
                    AND hct.lang_id = '".$_SESSION["lang_id"]."'
                  WHERE hc.vHtype='admin'
               ORDER BY hc.nHcposition ASC,h.nHposition ASC";            
            sortableDb("Help", $oldId, $PositionfieldName, $IdfieldName, $_GET['move'], $sqlO);
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
                                    <td width="4%"></td>
                  <td width="78%" valign="top">
                   
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="74%" class="heading_admn boldtextblack" align="left">Admin Help </td>
                        <td width="26%" class="heading_admn" align="right">
                        <a href="add_help_admin.php<?php if($ddlCategory!=''){echo '?ddlCategory='.$ddlCategory;}?>" class="AddLinks"><b>Add New Help</b></a>
                        </td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="15%"  border="0" cellpadding="5" cellspacing="1" class="AddLinks">
  <tr>
    <td align="left"></td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">
<form name="frmHelp" id="frmHelp" ACTION="<?php echo $_SERVER['PHP_SELF']?>" method="post" onSubmit="return Validate();">
<?php
//selected language for filtering
$ddlCategory=($_POST['ddlCategory']!='')?$_POST['ddlCategory']:$_GET['ddlCategory'];
					
//add this link to ordering 
if(isset($ddlCategory) && $ddlCategory!='')
{
	$order_category='&ddlCategory='.$ddlCategory;
}//end if		
			
if($ddlCategory=='')
{
	$ddlCategory='All';
}//end if		

if(isset($ddlCategory))
{
	if($ddlCategory!='All')
	{
		//requested language category listing
		$sql = "SELECT h.*,hp.*,hct.vHctitle,hc.*,h.vActive as ha,h.nHposition as nPos
                          FROM ".TABLEPREFIX."help h
                     LEFT JOIN ".TABLEPREFIX."helpcategory hc
			    ON h.nHcId=hc.nHcId
                          JOIN ".TABLEPREFIX."helpcategory_lang hct
                            ON hc.nHcId = hct.help_cat_id
                          JOIN ".TABLEPREFIX."help_lang hp
                            ON h.nHId = hp.help_id
                           AND hp.lang_id = '".$_SESSION["lang_id"]."'
                           AND hct.lang_id = '".$_SESSION["lang_id"]."'
                         WHERE hc.vHtype='admin'
                           AND hc.nHcId='".$ddlCategory."'
                      ORDER BY hc.nHcposition ASC,h.nHposition ASC";
	}//end if
	else
	{
		//all category listing
	 $sql = "SELECT h.*,hp.*,hc.*,hct.vHctitle,h.vActive as ha,h.nHposition as nPos
                   FROM ".TABLEPREFIX."help h
              LEFT JOIN ".TABLEPREFIX."helpcategory hc
                     ON h.nHcId=hc.nHcId
                   JOIN ".TABLEPREFIX."helpcategory_lang hct
                     ON hc.nHcId = hct.help_cat_id
                   JOIN ".TABLEPREFIX."help_lang hp
                     ON h.nHId = hp.help_id
                    AND hp.lang_id = '".$_SESSION["lang_id"]."'
                    AND hct.lang_id = '".$_SESSION["lang_id"]."'
                  WHERE hc.vHtype='admin'
               ORDER BY hc.nHcposition ASC,h.nHposition ASC";
	}//end else
}//end if
else
{
	//all category listing
	$sql = "SELECT h.*,hp.*,hct.vHctitle,hc.*,h.vActive as ha,h.nHposition as nPos
                  FROM ".TABLEPREFIX."help h
             LEFT JOIN ".TABLEPREFIX."helpcategory hc
		    ON h.nHcId=hc.nHcId
                  JOIN ".TABLEPREFIX."helpcategory_lang hct
                    ON hc.nHcId = hct.help_cat_id
                  JOIN ".TABLEPREFIX."help_lang hp
                    ON h.nHId = hp.help_id
                   AND hp.lang_id = '".$_SESSION["lang_id"]."'
                   AND hct.lang_id = '".$_SESSION["lang_id"]."'
                 where hc.vHtype='admin' ORDER BY hc.nHcposition ASC,h.nHposition ASC";
}//end else

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
		$message='Help add successfully.';
	break;
	
	case "e":
		$message='Help updated successfully.';
	break;
	
	case "d":
		$message='Help deleted successfully.';
	break;
	
	default:
		$message='';
	break;
	
}//end if
?>

 <tr align="left" bgcolor="#FFFFFF">
                                <td colspan="6"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td align="right">Filter By Category : <select name="ddlCategory" class="textbox2" onChange="document.frmHelp.submit();">
								<option value="All">All</option>
								<?php
			//fectch language from Help category table
			$language=mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."helpcategory H
                                                        JOIN ".TABLEPREFIX."helpcategory_lang L
                                                          ON H.nHcId = L.help_cat_id
                                                         AND H.vActive='1' and H.vHtype='admin' order by H.nHcposition ASC") or die(mysqli_error($conn));
			if(mysqli_num_rows($language)>0){
				while($arr=mysqli_fetch_array($language))
				{?>
				<option value="<?php echo $arr['nHcId'];?>" <?php if($_POST['ddlCategory']==$arr['nHcId']){echo 'selected';}else if($_GET['ddlCategory']==$arr['nHcId']){echo 'selected';}?>><?php echo $arr['vHctitle'];?></option>
				<?php
					}//end while
				}//end if
				else{
					echo '<option value="Nil">NIL</option>';
				}//end if?>
				</select></td>
  </tr>
</table>
</td>
                      </tr>
<?php							  
if(isset($message) && $message!='')
{
?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="6" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>			
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td width="8%" align="center">Sl No. </td>
                                <td width="15%" align="center">Help</td>
                                <td width="15%" align="center">Category</td>
                                <td width="15%" align="center">Set Order </td>
                                <td width="15%" align="center">Active</td>
                                <td width="15%" align="center">Action</td>
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
                              <tr bgcolor="#FFFFFF">
                                <td align="center"><?php echo $cnt;?></td>
                                <td align="center" class="maintext"><?php echo htmlentities($arr["vHtitle"]);?></td>
                                <td align="center" class="maintext"><?php echo htmlentities($arr["vHctitle"]);?></td>
                                <td align="center" class="maintext"><?php if($cnt!=1){?>
	<a href='help_admin.php?Action=ordering&move=up&id=<?php echo $arr["nHId"];?>&pos=<?php echo $arr["nPos"];?><?php echo $order_category;?>'>

<img src="../images/up.gif" alt="Up" border=0></a><?php } ?>

<?php if($cnt!=$totalrows){?>
<a href="help_admin.php?Action=ordering&move=down&id=<?php echo $arr["nHId"];?>&pos=<?php echo $arr["nPos"];?><?php echo $order_category;?>">

<img src="../images/down.gif" alt="Down" border=0></a><?php } ?></td>
                                <td align="center"><?php if($arr['ha']=='1'){echo 'Yes';}else{echo 'No';}?></td>
                                <td align="center"><?php echo "<a href='add_help_admin.php?nHId=".$arr["nHId"]."&mode=edit".$order_category."'>Edit</a>";?>&nbsp;|&nbsp;<?php echo "<a href='help_admin.php?nHId=".$arr["nHId"]."&mode=delete".$order_category."' onClick='javascript:return confirm(\"Are you sure you want to delete this?\");'>Delete</a>";?></td>
                              </tr>
					<?php 
								$cnt++;
							}//end while
						}//end if
				  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="6" align="center" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
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