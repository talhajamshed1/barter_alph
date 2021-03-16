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
//include ("./includes/config.php");
//include ("./includes/session.php");
//include ("./includes/functions.php");
//include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
//include ("./includes/session_check.php");

//include_once('./includes/gpc_map.php');

if ($_POST["btnSettings"] != "") {
    $alertstatus = $_POST["chkAlerts"];
    $nlstatus = $_POST["chkNewsletters"];
    $imstatus = $_POST["chkImage"];
    if ($alertstatus) {
        $alertstatus = "Y";
    }//end if
    else {
        $alertstatus = "N";
    }//end else
    if ($nlstatus) {
        $nlstatus = "Y";
    }//end if
    else {
        $nlstatus = "N";
    }//end else
    if ($imstatus) {
        $imstatus = "Y";
    }//end if
    else {
        $imstatus = "N";
    }//end else

    $sql = "UPDATE " . TABLEPREFIX . "users SET ";
    $sql .= " vAlertStatus='" . $alertstatus . "', vNLStatus= '" . $nlstatus . "', vIMStatus= '" . $imstatus . "' ";
    $sql .=" WHERE nUserId ='" . $_SESSION["guserid"] . "' ";

    mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $message = MESSAGE_SETTINGS_CHANGED;
}//end if

$sqluserdetails = "SELECT  vAlertStatus , vNLStatus, vIMStatus FROM " . TABLEPREFIX . "users  WHERE  nUserId  = '" . $_SESSION["guserid"] . "'";
$resultuserdetails = mysqli_query($conn, $sqluserdetails) or die(mysqli_error($conn));
$rowuser = mysqli_fetch_array($resultuserdetails);

$alertstatus = $rowuser["vAlertStatus"];
$nlstatus = $rowuser["vNLStatus"];
$imstatus = $rowuser["vIMStatus"];

//include_once('./includes/title.php');
?>
<!--<body onLoad="timersOne();">
<?php //include_once('./includes/top_header.php'); ?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="headerbg"><?php //require_once("./includes/header.php"); ?>
    <?php //require_once("menu.php"); ?>
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="10%" height="688" valign="top"><?php //include_once ("./includes/usermenu.php"); ?>
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
                                                <td class="heading" align="left"><?php //echo HEADING_MY_SETTINGS; ?></td>
                                            </tr>
                                        </table>
                                        <table width="50%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="6" class="maintext2">-->
                                                                    <form name="frmSettings" method ="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" onsubmit="return validateSettingsForm();">
                                                                        <tr >
                                                                            <td colspan="3" align="left" class="hor_bar"><a href="#" onclick="javascript:open_container('table_settings');return false;" class="my_settings"><?php echo HEADING_MY_SETTINGS; ?></a></td>
                                                                        </tr>
                                                                            <?php
                                                                            //if (isset($message) && $message != '') {
                                                                                ?>
                                                                            <!--<tr >
                                                                                <td colspan="2" align="center" class="warning"><?php //echo $message; ?></td>
                                                                            </tr>-->
                                                                        <?php //}//end if?>
                                                                        <tr>
																		<td colspan="3" >
                                                                        <table width="100%"  border="0" cellpadding="5" cellspacing="1" class="sub_box" id="table_settings" style="display:<?php echo ($_POST['btnSettings']!='')?'block':'none'; ?>;">
                                                                        <tr >
                                                                            <td width="20%" align="right"><input type = "checkbox" name="chkImage" <?php if ($imstatus == "Y") {
                                                                            echo "CHECKED";
                                                                        } ?>></td>
                                                                            <td width="80%" align="left"><?php echo TEXT_SHOW_IMAGE_EVERYONE; ?></td>
                                                                        </tr>
                                                                        <tr >
                                                                            <td width="20%" align="right"><input type = "checkbox" name="chkNewsletters" <?php if ($nlstatus == "Y") {
                                                                            echo "CHECKED";
                                                                        } ?>></td>
                                                                            <td width="80%" align="left"><?php echo TEXT_SUBSCRIBE_NEWSLETTER; ?></td>
                                                                        </tr>
                                                                        <?php
                                                                        //point not enabled
                                                                        if (DisplayLookUp('EnablePoint') != '1') {
                                                                            ?>
                                                                            <tr >
                                                                                <td align="right"><input type = "checkbox" name="chkAlerts" <?php if ($alertstatus == "Y") {
                                                                            echo "CHECKED";
                                                                        } ?> ></td>
                                                                                <td align="left"><?php echo TEXT_RECEIVE_ALERT_NEW_SALE_ADDITION; ?></td>
                                                                            </tr>
                                                                            <?php
                                                                        }//end if
                                                                        ?>

                                                                        <tr >
                                                                            <td align="left">&nbsp;</td>
                                                                            <td align="left"><input type="submit" name="btnSettings" class="submit"  value="<?php echo BUTTON_SAVE; ?>"/></td>
                                                                        </tr>
                                                                        </table></td></tr>
                                                                    </form>
                                                                <!--</table>
                                                            </td>
                                                        </tr>
                                                    </table>
													<?php //include('./includes/sub_banners.php'); ?>
													</td>
                                            </tr>
                                        </table></td>
                                </tr>
                            </table></td>
                    </tr>
                </table>-->
<?php //require_once("./includes/footer.php"); ?>