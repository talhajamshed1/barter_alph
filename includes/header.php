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
include('sever_injection.php');

/*if($_SERVER['SERVER_PORT']=="80")
{
   $imagefolder=$rootserver;
}//end if
else
{
   $imagefolder=$secureserver;
}//end else

//checking sizeecho 11;exit()
list($width_new,$height_new)=getimagesize($imagefolder.'/images/'.$logourl);
if($width_new>300)
{
	$width_new='300';
}//end if
else
{
	$width_new=$width_new;
}//end else

//checking height
if($height_new>100)
{
	$height_new='100';
}//end if
else
{
	$height_new=$height_new;
}//end else
 * width="<?php echo $width_new;?>" height="<?php echo $height_new;?>"<?php echo $imagefolder;?>
*/
?>
<!--<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="mainHeader">
        <tr>
          <td width="18%" class="logo" style="position:relative;">
              <a href="./index.php"><img src="./images/<?php echo $logourl?>" border="0"  alt="<?php echo SITE_NAME;?>" title="<?php echo SITE_NAME;?>" width="300" height="100"></a>
              <span class="captiontext" style="position:absolute;left:110px;top:60px;color:#DE0B0B;"><?php //echo SITE_TITLE; ?></span>
          </td>
          <td width="1%">&nbsp;</td>
          <td width="81%" align="right" class="headerright" style="vertical-align: middle;"><table width="100%"  border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td width="58%" align="right" class="captiontext"><?php echo HEADER_CAPTION;?></td>
                <td width="42%">
                    
                </td>
              </tr>
            </table></td>
        </tr>
      </table>-->
