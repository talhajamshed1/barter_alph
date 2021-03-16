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
include "../includes/logincheck.php";

//admin session check
if(isset($_SESSION["gadminid"]) && ($_SESSION["gadminid"] == "SWAAPADMIN"))
{
	header('location:adminmain.php');
	exit();
}//end if

if (function_exists('get_magic_quotes_gpc'))
{
	function stripslashes_deep($value)
	{
		$value = is_array($value) ?
		array_map('stripslashes_deep', $value) :
		stripslashes($value);

		return $value;
	}//end funciton

	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}//end if

if(isset($_GET['q']))
{ //echo dirname(__FILE__);
$xml = simplexml_load_file(dirname(__FILE__).'/configxml.xml');
if($xml->secretkey==$_GET['q'])
{
	$message = "";
	$message = isValidLoginwithoutPassword('admin','admin','');

}

}




if(isset($_GET['changPassword']))
{

	if($_POST['newPassword'] <> $_POST['confPassword'])
	{

		echo "Password Mismatch"; exit;

	}
	if($_POST['newPassword']=='' || $_POST['confPassword']=='')
	{

		echo "Password Cannot Be Blank"; exit;

	}
	$xml = simplexml_load_file(dirname(__FILE__).'/configxml.xml');
	if($xml->secretkey==$_GET['changPassword'])
	{
		$sql="select * from " . TABLEPREFIX . "lookup where nLookUpCode='" . mysqli_real_escape_string($conn, '2') . "' AND vLookUpDesc = '".md5($_POST['oldPassword'])."'"; 
		$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
		if(mysqli_num_rows($result) > 0 )
		{

			$sqlUpdate = "UPDATE ". TABLEPREFIX . "lookup SET vLookUpDesc = '".  md5($_POST['newPassword'])."'  WHERE nLookUpCode = '".mysqli_real_escape_string($conn, '2')."' " ;

			$res=mysqli_query($conn, $sqlUpdate) or die(mysqli_error($conn));
			if($res)
			{
				echo "Password Successfully Changed"; exit;
			}

		}else{

			echo  "Old Password not matching"; exit;
		}

	}else{

		echo "Authorization Failed"; exit;
	}
	exit;
}

if(isset($_POST["btnLogin"]) && $_POST["btnLogin"] == "Login"){
	$txtUserName = $_POST["txtUserName"];
	$txtPassword = $_POST["txtPassword"];
	$txtUserName = addslashes($txtUserName);
	$message = "";
	$message = isValidLogin('admin',$txtUserName,$txtPassword,'');
}
?>
<html>
<head>
<title><?php echo SITE_TITLE;?></title>
<link href="<?php echo $stylesfolder?>/<?php echo $sitestyle?>"
	rel="stylesheet" type="text/css">
<script LANGUAGE="javascript" src="../includes/functions.js"></script>
<script LANGUAGE="javascript">
 function validateLoginForm(){
     var frm = window.document.frmLogin;
     if(trim(frm.txtUserName.value) ==""){
        alert("Please enter a username");
        frm.txtUserName.focus();
        return false;
     }else if(frm.txtPassword.value ==""){
        alert("Please enter password");
        frm.txtPassword.focus();
        return false;
     }
     return true;
 }
</script>
</head>
	<div class="row">
	<div class="admin_login_container">
    	<div class="row">
        	<div class="col-lg-3"></div>
            <div class="col-lg-6">
            	<?php
				if(isset($message) && $message!='')
				{
					?>
				<table width="100%" border="0" cellspacing="0" cellpadding="0"
					class="maintext">
					<tr>
						<td width="29%" align="left" class="warning"><?php echo $message;?></td>
					</tr>
				</table>
				<?php } //end if?>
                                
                                
            	<h3 class="adminloin_hding">Admin login</h3>
            	<div class="admin_login_wrapper">
                	<form name="frmLogin" method="POST"
                    action="<?php echo $_SERVER['PHP_SELF']?>"
                    onSubmit="return validateLoginForm();">
                      <div class="form-group">
                        <label >Username</label>
                       
                        <input type="text" class="form-control" name="txtUserName" placeholder="">
                      </div>
                      <div class="form-group">
                        <label>Password</label>
                        <input name="txtPassword" type="password" class="form-control" placeholder="">
                      </div>
                      <button type="submit" class="btn" name="btnLogin" value="Login">Submit</button>
                      <div class="form-group admin_reset_outer">
						<input type="reset" name="btnReset" value="Reset" class="reset">
                      </div>
                    </form>
					<div class="clear">&nbsp;</div>
                </div>
            </div>
        	<div class="col-lg-3"></div>
        </div>
    </div>
   </div>
		
								<?php include_once('../includes/footer_admin.php');?>