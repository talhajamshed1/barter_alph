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
session_start();
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
include_once('./includes/gpc_map.php');

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="headerbg">
             
			 <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="74%" height="688" valign="top">
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td class="link3">&nbsp;</td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="heading" align="left"><?php echo HEADING_ITEM_DETAILS; ?></td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="10" cellpadding="4" class="maintext2 itemdetails_padding">
                                                                    <?php
                                                                    $sql = "select nSwapId,S.nCategoryId,
                                                                             L.vCategoryDesc,S.nUserId,S.vPostType,
                                                                             CONCAT(CONCAT(U.vFirstName,'  '),U.vLastName) as UserName,
                                                                             S.vTitle,S.vBrand,S.vType,S.vCondition,
                                                                             S.vYear,S.nValue,S.nPoint,S.nShipping,S.vUrl,S.vSmlImg,S.vDescription,date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate'
                                                                             from
                                                                             " . TABLEPREFIX . "swap S
                                                                                 left join " . TABLEPREFIX . "users U on S.nUserId = U.nUserId
                                                                                 left join " . TABLEPREFIX . "category C on S.nCategoryId = C.nCategoryId
                                                                                 LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                                                             where  
                                                                                nSwapId = '" . addslashes($_GET["swapid"]) . "'";


                                                                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                                                                    if (mysqli_num_rows($result) > 0) {
                                                                        if ($row = mysqli_fetch_array($result)) {
                                                                            ?>
                                                                            <tr bgcolor="#FFFFFF">
                                                                                <td width="20%" align="left"><?php echo TEXT_PICTURE; ?></td>
                                                                                <td width="80%" align="left"><?php
                                                                    if ($row["vSmlImg"] == "" || !file_exists($row["vSmlImg"])) {
                                                                        echo TEXT_N_A;
                                                                    }//end if
                                                                    else {

                                                                        @list($wid, $heht) = @getimagesize($row["vSmlImg"]);
                                                                        if ($wid > 200) {
                                                                            $wid = '450';
                                                                        }//end if
                                                                        else {
                                                                            $wid = $wid;
                                                                        }//end if

                                                                        if ($heht > 200) {
                                                                            $heht = '450';
                                                                        }//end if
                                                                        else {
                                                                            $heht = $heht;
                                                                        }//end if

                                                                        echo '<img src="' . $row["vSmlImg"] . '" width="' . $wid . '" height="' . $heht . '">';
                                                                    }//end else
                                                                            ?></td>
                                                                            </tr>
                                                                            <tr bgcolor="#f0f0f0">
                                                                                <td align="left"><?php echo TEXT_POSTED_ON; ?></td>
                                                                                <td align="left"><?php echo  $row["dPostDate"] ?></td>
                                                                            </tr>
                                                                            <tr bgcolor="#FFFFFF">
                                                                                <td align="left"><?php echo TEXT_CATEGORY; ?></td>
                                                                                <td align="left"><?php echo  utf8_encode($row["vCategoryDesc"]) ?></td>
                                                                            </tr>
                                                                            <tr bgcolor="#f0f0f0">
                                                                                <td align="left"><?php echo TEXT_TITLE; ?></td>
                                                                                <td align="left"><?php echo  $row["vTitle"] ?></td>
                                                                            </tr>
                                                                            <tr bgcolor="#FFFFFF">
                                                                                <td align="left"><?php echo TEXT_BRAND; ?></td>
                                                                                <td align="left"><?php echo  $row["vBrand"] ?></td>
                                                                            </tr>
                                                                            <tr bgcolor="#f0f0f0">
                                                                                <td align="left"><?php echo TEXT_TYPE; ?></td>
                                                                                <td align="left"><?php echo  $row["vType"] ?></td>
                                                                            </tr>
                                                                            <tr bgcolor="#FFFFFF">
                                                                                <td align="left"><?php echo TEXT_CONDITION; ?></td>
                                                                                <td align="left"><?php echo  $row["vCondition"] ?></td>
                                                                            </tr>
                                                                            <tr bgcolor="#f0f0f0">
                                                                                <td align="left"><?php echo TEXT_YEAR; ?></td>
                                                                                <td align="left"><?php echo  $row["vYear"] ?></td>
                                                                            </tr>
                                                                            <?php if (ENABLE_POINT!='1'){ ?>
                                                                            <tr bgcolor="#FFFFFF">
                                                                                <td align="left"><?php echo TEXT_PRICE; ?></td>
                                                                                <td align="left"><?php echo  CURRENCY_CODE.$row["nValue"] ?></td>
                                                                            </tr>
                                                                            <?php } ?>
                                                                            <?php if (ENABLE_POINT!='0'){ ?>
                                                                            <tr bgcolor="#f0f0f0">
                                                                                <td align="left"><?php echo POINT_NAME; ?></td>
                                                                                <td align="left"><?php echo  $row["nPoint"] ?></td>
                                                                            </tr>
                                                                            <?php } ?>
                                                                            <?php if (ENABLE_POINT!='1'){ ?>
                                                                            <tr bgcolor="#FFFFFF">
                                                                                <td align="left"><?php echo TEXT_SHIPPING_CHARGE; ?></td>
                                                                                <td align="left"><?php echo  CURRENCY_CODE.$row["nShipping"] ?></td>
                                                                            </tr>
                                                                            <?php } ?>
                                                                            <tr bgcolor="#f0f0f0">
                                                                                <td align="left"><?php echo TEXT_ITEM_DESCRIPTION; ?></td>
                                                                                <td align="left"><?php echo  $row["vDescription"] ?></td>
                                                                            </tr>
                                                                                <?php
                                                                            }//end if
                                                                        }//end if
                                                                        else {
                                                                            ?>
                                                                        <tr align="center" bgcolor="#FFFFFF">
                                                                            <td colspan="2" class="warning"><?php echo ERROR_ITEM_DETAILS_UNAVAILABLE; ?></td>
                                                                        </tr>
                                                                        <?php
                                                                    }//end else
                                                                    ?>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                        </table></td>
                                </tr>
                            </table></td>
                    </tr>
                </table>
			 
			 </td>
		 </tr>
	 </table>