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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
include_once('./includes/gpc_map.php');
include_once('./includes/title.php');
?>
<script language="javascript1.1" type="text/javascript" src="js/categories.js"></script>
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
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="heading" align="left"> <?php echo TEXT_SELECT_CATEGORY; ?> </td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left"><select name="ddlCateory" class="textbox2" onChange="javascript:if (this.value!='') showCategory(this.value,0);" multiple>
                                                                                <?php
                                                                                //display root category starts here
                                                                                $sqlCategory = mysqli_query($conn, "SELECT * FROM " . TABLEPREFIX . "category C
                                                                                    LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                                                                    where nParentId = '0' order by C.nPosition desc") or die(mysqli_error($conn));
                                                                                if (mysqli_num_rows($sqlCategory) > 0) {
                                                                                    while ($arrCategory = mysqli_fetch_array($sqlCategory)) {
                                                                                        echo '<option value="' . $arrCategory["nCategoryId"] . '">' . htmlentities($arrCategory["vCategoryDesc"]) . '</option>';
                                                                                    }//end while
                                                                                }//end if
                                                                                else {
                                                                                    echo '<option value="">'.MESSAGE_SORRY_NO_CATEGORY.'</option>';
                                                                                }//end else
                                                                                //display root category stops here
                                                                                ?>
                                                                            </select>
                                                                            <span id="txtDisplayCategory0"></span>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                        </table>
										<?php include('./includes/sub_banners.php'); ?>
										</td>
                                </tr>
                            </table></td>
                    </tr>
                </table>
<?php require_once("./includes/footer.php"); ?>