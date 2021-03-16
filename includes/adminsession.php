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
if(strcasecmp(basename($_SERVER['SCRIPT_FILENAME']),"install.php")==0)
{
	;
}//end if
else
{
	//installation checking
	if (INSTALLED ===true) 
	{
		;
	}//end if
	else
	{

		header("Location:../install/install.php");
	}//end else
}//end else

session_start();
/*if (!isset($_SESSION["gadminid"]) || $_SESSION["gadminid"] == "") 
{
	if(function_exists('session_register'))
	{
		session_register("backurl");
	}//end if
}*/
?>