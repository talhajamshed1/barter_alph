<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE=ClientFilePathName($_SERVER['PHP_SELF']);

if($_GET['mode']=='edit')
{
	$sqlPoint=mysqli_query($conn, "select * from ".TABLEPREFIX."usercredits where nUserId='".$_GET['nUserId']."'") or die(mysqli_error($conn));
	if(mysqli_num_rows($sqlPoint)>0)
	{
		$txtPoints=mysqli_result($sqlPoint,0,'nPoints');
		$shwAdditional='';
	}//end if
	else
	{
		$txtPoints='0';
		$shwAdditional='<input type="hidden" value="addNew" name="NewUser">';
	}//end else
	$mode='edit';
	$btnVal='Edit';
}//end if
else
{
	$btnVal='Add';
}//end else


if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]!='')
{
	if (function_exists('get_magic_quotes_gpc'))
	{
   			 $nUserId = stripslashes($_POST['nUserId'] );
  			 $txtPoints = stripslashes($_POST["txtPoints"]);
	}//end if
	else
	{
		    $nUserId = $_POST['nUserId'] ;
    		$txtPoints = $_POST["txtPoints"];
	}//end else
}//end if

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Edit User '.POINT_NAME)
{
	//checking new insertion
	if(isset($_POST['NewUser']) && $_POST['NewUser']=='addNew')
	{
		mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."usercredits (nPoints,nUserId) values ('".addslashes($txtPoints)."','".$nUserId."')") or die(mysqli_error($conn));
	}//end if
	else
	{
		mysqli_query($conn, "update ".TABLEPREFIX."usercredits set nPoints='".addslashes($txtPoints)."' WHERE nUserId='".$nUserId."'") or die(mysqli_error($conn));
	}//end else
	$_SESSION['sessionMsg']=POINT_NAME.' updated successfully.';
	header('location:users_points.php');
	exit();
}//end if

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<script language="javascript" type="text/javascript">
function  validateUserPoints()
{
	var s=document.frmUserPoints;
	if(s.txtPoints.value=='')
	{
		alert("Total <?php echo POINT_NAME;?> can't be blank");
		s.txtPoints.focus();
		return false;
	}//end if
	return true;
}//end function
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
                        <td width="100%" class="heading_admn boldtextblack" align="left"><?php echo $btnVal;?> User <?php echo POINT_NAME;?></td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2">
<form name="frmUserPoints" method ="POST" action = "" onsubmit="return validateUserPoints();">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr bgcolor="#FFFFFF">
							  <input type="hidden" name="nUserId" value="<?php echo $_GET['nUserId'];?>">
							  <?php echo $shwAdditional;?>
                                <td colspan="2" align="left" class="warning"> * indicates mandatory fields</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Username</td>
                                <td><?php echo base64_decode_fix($_GET['uName']);?> </td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="31%" align="left">Total <?php echo POINT_NAME;?> <span class="warning">*</span></td>
                                <td width="69%"><input type="number" class="textbox2" min=0  step="any" name="txtPoints" size="70" value="<?php echo $txtPoints;?>"/></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td><input type="submit" name="btnSubmit" value="<?php echo $btnVal;?> User <?php echo POINT_NAME;?>" class="submit"/></td>
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
<?php include_once('../includes/footer_admin.php');
function    base64_decode_fix( $data, $strict = false ) 
{ 
    if( $strict ) 
        if( preg_match( '![^a-zA-Z0-9/+=]!', $data ) ) 
            return( false ); 
    
    return( base64_decode( $data ) ); 
} 
?>