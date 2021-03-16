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
include_once("./includes/logincheck.php");

$hintro_arr = ContentLookUp('hintro');
$hintro_img = DisplayLookUp('welcomeImage');
$hintro_img1 = "images/".$hintro_img;

?>
<table width="100%"  style="display:none;" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="1%" valign="top" class="homeheader">
            <img src="images/cor1.gif" width="10" height="10">
        </td>
        <td width="70%" class="homeheader">
            <img src="images/spacer.gif" width="1" height="12">
        </td>
        <td width="27%" align="right" valign="top" class="homeheaderright">
            <img src="images/spacer.gif" width="1" height="12">
        </td>
        <td width="2%" align="right" valign="top" class="homeheaderright">
            <img src="images/cor2.gif" width="10" height="10">
        </td>
    </tr>
    <tr>
        <td class="homeheader">&nbsp;</td>
        <td valign="top" class="homeheader"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr><?php if (file_exists($hintro_img1) && trim($hintro_img)!=''){ ?>
                    <td width="39%" align="left" valign="center" >
                        <img src="images/spacer.gif" width="1" height="125">
                        <img src="images/<?php echo $hintro_img;?>" width="150" height="150" />
                    </td>
                    <?php
                }else{
                ?>
                    <td width="39%" align="left" valign="bottom" class="homeheaderpic">
                        <img src="images/spacer.gif" width="1" height="125">
                    </td>
                <?php
                }
                ?>
                    <td width="61%" align="left" class="welcomebg">
                        <span class="maintext">
                            <span class="welcome"><?php echo $hintro_arr['content_title']; ?></span><br>
                            <?php echo $hintro_arr['content']; ?>
                        </span>
                    </td>
                </tr>
            </table></td>
        <td class="homeheaderright">
            <?php
            if ($_SESSION["guserid"] == "") {
                require("./includes/login.php");
            }//end if
            ?>
        </td>
        <td class="homeheaderright"><img src="images/spacer.gif" width="11" height="8"></td>
    </tr>
    <tr>
        <td valign="bottom" class="homeheader"><img src="images/cor3.gif" width="10" height="10"></td>
        <td class="homeheader"><img src="images/spacer.gif" width="1" height="12"></td>
        <td align="right" valign="bottom" class="homeheaderright"><img src="images/spacer.gif" width="1" height="12"></td>
        <td align="right" valign="bottom" class="homeheaderright"><img src="images/cor4.gif" width="10" height="10"></td>
    </tr>
</table>