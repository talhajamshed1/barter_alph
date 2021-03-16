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
if($_SERVER['SERVER_PORT']=="80")
{
 	$imagefolder=$rootserver;
}//end if
else
{
   $imagefolder=$secureserver;
}//end else
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td class="leftcoloumn"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td id="leftcoloumntop"></td>
                        </tr>
                      </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td><div class="body1">
                                <div id="menu">
                                  <ul>
								  	 <li><a href="<?php echo $rootserver?>/registeredusers.php"><?php echo Highligt($PG_TITLE,'registeredusers.php','Registered Users','#000000');?></a></li>
									 <li><a href="<?php echo $rootserver?>/accountsummary.php"><?php echo Highligt($PG_TITLE,'accountsummary.php','Account Summary','#000000');?></a></li>
									 <li><a href="<?php echo $rootserver?>/editaffprofile.php"><?php echo Highligt($PG_TITLE,'editaffprofile.php','Edit Profile','#000000');?></a></li>
									 <li><a href="<?php echo $rootserver?>/changeaffpassword.php"><?php echo Highligt($PG_TITLE,'changeaffpassword.php','Change Password','#000000');?></a></li>
									 <li><a href="<?php echo $rootserver?>/../logout.php"><?php echo Highligt($PG_TITLE,'../logout.php','Logout','#000000');?></a></li>
                                  </ul>
                                </div>
                              </div>
                                </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>