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
ob_start();
include_once('../includes/config.php');
include_once('../includes/adminsession.php');
include_once('../includes/functions.php');
if(ClientFilePathName($_SERVER['PHP_SELF'])=='meta_tags.php')
{
	$load="ShowTitle('".$ShowContent."');";
}//end if
else
{
	$load='';
}//end else

if($_SERVER['SERVER_PORT']=="80")
{
   $imagefolder=$rootserver;
}//end if
else
{
   $imagefolder=$secureserver;
}//end else
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo SITE_TITLE;?></title>
<link href="<?php echo $stylesfolder?>/<?php echo $sitestyle?>" rel="stylesheet" type="text/css">
<script LANGUAGE="javascript" src="../includes/functions.js"></script>
<script LANGUAGE="javascript">
 function validateLoginForm(){
     var frm = window.document.frmLogin;
     if(trim(frm.txtUserName.value) ==""){
        alert("Please enter a user name");
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
<body <?php if(isset($notregistered) and $notregistered== "1"){ echo "onload='loadFields();'";}?> onLoad="<?php echo $load;?>">
<table width="100%" height="24"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="80%" height="24" valign="middle" class="topcolor">&nbsp;</td>
    <td width="20%" align="right" valign="middle" class="topcolor">&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="middle" class="logoadmin">