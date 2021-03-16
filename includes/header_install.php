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
define("TEXT_POWEREDBY",'Powered by<strong> <a rel="nofollow" href="http://www.iscripts.com/eswap/" target="_blank">iScripts eSwap</a></strong>. A premium product from<strong> <a rel="nofollow" href="http://www.iscripts.com" target="_blank">iScripts.com</a></strong>');
if($_SERVER['SERVER_PORT']=="80")
{
   $imagefolder=$txtSiteURL;
}//end if
else
{
   $imagefolder=$txtSecureSiteURL;
}//end else
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="18%" class="logo"><img src="../images/<?php echo $logourl?>" border="0"></td>
          <td width="26%" nowrap>&nbsp;<h1>Swap Everything</h1></td>
          <td width="56%" align="right" class="headerright">&nbsp;</td>
        </tr>
      </table>