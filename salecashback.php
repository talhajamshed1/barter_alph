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
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');
include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="headerbg"><?php require_once("./includes/header.php"); ?>
                <?php require_once("menu.php"); ?>
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="10%" height="688" valign="top"><?php include_once ("./includes/categorymain.php"); ?>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td id="leftcoloumnbtm"></td>
                                            </tr>
                                        </table></td>
                                    <td width="74%" valign="top">
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td class="link3">&nbsp;</td>
                                            </tr>
                                        </table>
                                        <?php $content_arr = ContentLookUp('cashback'); ?>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="heading" align="left"><?php echo $content_arr['content_title']; ?></td>
                                            </tr>
                                        </table>
                                        <?php
                                        if ($_SESSION["guserid"] == "") {
                                            include_once("./login_box.php");
                                        }
                                        ?>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE">
                                                                <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td width="20%" align="left"><img src="./images/wallet.jpg" class="imageborder"></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left"><?php echo str_replace('{currency_code}',CURRENCY_CODE,str_replace('{site_name}',SITE_NAME,$content_arr['content'])); ?></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="right"><img src="./images/money.jpg"></td>
                                                                    </tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
										<?php include('./includes/sub_banners.php'); ?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<?php require_once("./includes/footer.php"); ?>