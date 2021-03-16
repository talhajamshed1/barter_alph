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
$PGTITLE='user_reg_fields';

$arrF = array("vLoginName", "vPassword", "vConfirmPassword", "vEmail","vPhone");

if (isset($_POST['btnSubmit'])) 
{
	$ids	=	isset($_POST['IDS'])	?	$_POST['IDS']	:	array();
	if (is_array($ids)) 
	{
		$id		= implode("','", $ids);
		$fields	= implode("','", $arrF);
		$sql	= "UPDATE ".TABLEPREFIX."mandatory
				   SET vManStatus = 'N' 
				   WHERE vManFieldName NOT IN ('{$fields}')";
	   
		$res	= @mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if (!empty($ids)) 
		{
			$sql	= "UPDATE ".TABLEPREFIX."mandatory
					   SET vManStatus = 'A' 
					   WHERE nManFieldId IN ('{$id}')";
			$res	= @mysqli_query($conn, $sql) or die(mysqli_error($conn));
			
			$message='Updated successfully';
		} //end if  
	}//end 2nd if
}//end first if

$arrFields	= array();
$sql		= "SELECT * FROM ".TABLEPREFIX."mandatory 
		   	   WHERE vActiveStatus = 'A' ORDER BY nOrder";
$res		= @mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (@mysqli_num_rows($res) > 0) 
{
	while($row = @mysqli_fetch_array($res)) 
	{
		$arrFields[]	= $row;
	}//end while loop
}//end if
?>
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
                        <td width="84%" class="heading_admn boldtextblack" align="left">Mandatory Fields in User Registration Form</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="70%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmRegistration" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="3" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr align="center" bgcolor="#FFFFFF" class="gray">
                                <td width="11%">Sl No. </td>
                                <td width="10%">Mandatory</td>
                                <td width="79%">Field Name</td>
                      </tr>
<?php if(!empty($arrFields)) 
	{ 
		$cnt=1;
		foreach ($arrFields as $key => $value) 
		{ 
			if(!in_array($value['vManFieldName'], $arrF))
			{
		
?>					  
                              <tr bgcolor="#FFFFFF">
                                <td align="center"><?php echo $cnt?></td>
                                <td align="center"><input type="checkbox" name="IDS[]" value="<?php echo $value['nManFieldId']?>" <?php echo $value['vManStatus'] == "A" ? "checked" : ""?> <?php echo in_array($value['vManFieldName'], $arrF) ? "disabled" : ""?>></td>
                                <td><?php echo $value['vManLabelName']?></td>
                      </tr>
<?php  
				$cnt++;
			}//end if
		}//end foreach
   }//end if
?>					  
                              <tr align="center" bgcolor="#FFFFFF">
                                <td colspan="3"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
  <tr>
    <td width="24%">&nbsp;</td>
    <td width="76%" align="left"><input type="submit" name="btnSubmit" value="Save" class="submit"/></td>
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
<?php include_once('../includes/footer_admin.php');?>