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
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include ("./includes/session_check.php");

include_once('./includes/gpc_map.php');

$qryopt = "";
$userid = $_SESSION["guserid"];
$username = "";
$transid = "";

if ($_POST["userid"] != "") {
    $userid = $_POST["userid"];
}//end if
else if ($_GET["userid"] != "") {
    $userid = $_GET["userid"];
}//end else if
if ($_POST["username"] != "") {
    $username = $_POST["username"];
}//end if
else if ($_GET["username"] != "") {
    $username = $_GET["username"];
}//end else if
if ($_POST["transid"] != "") {
    $transid = $_POST["transid"];
}//end if
else if ($_GET["transid"] != "") {
    $transid = $_GET["transid"];
}//end else if

$sql = "SELECT * FROM " . TABLEPREFIX . "cashtxn  WHERE nCashTxnId  = '" . addslashes($transid) . "' ";
$sess_back = "accountsummary.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch;
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$arr = mysqli_fetch_array($rs);

$txtTransactionNumber = $arr["vModeNo"];

$trdate = $arr["dDate"];
$trdate = explode(" ", $trdate);
$dateonly = $trdate[0];
$_tmp = explode("-", $dateonly);
$year = $_tmp[0];
$month = $_tmp[1];
$day = $_tmp[2];
$txtTransactionDate = $arr["dDate"];
$txtAmount = $arr["nAmount"];
$txtCommission = $arr["nCommission"];
$txtMode = $arr["vMode"];

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
                                    <td width="10%" height="688" valign="top"><?php include_once ("./includes/usermenu.php"); ?>
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
                                                <td class="heading" align="left"><?php echo HEADING_TRANSACTION_DETAILS; ?> </td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                                    <?php
                                                                    if (isset($message) && $message != '') {
                                                                        ?>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td colspan="2" align="center" class="warning"><?php echo $message; ?></td>
                                                                        </tr>
                                                                    <?php }//end if?>
                                                                    <tr align="right" bgcolor="#FFFFFF">
                                                                        <td colspan="2"><a href='user_account_payment_details.php?userid=<?php echo  $userid ?>&username=<?php echo  urlencode($username) ?>'><b><?php echo LINK_BACK; ?></b></a></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td colspan="2" align="left"><strong><?php echo HEADING_DETAILS_OF_TRANSACTION; ?> '<?php echo  $transid ?>'</strong></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td width="20%" align="left"><?php echo TEXT_TRANSACTION_DATE; ?></td>
                                                                        <td width="80%" align="left"><?php echo  date('m/d/Y', strtotime($txtTransactionDate)) ?></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left"><?php echo TEXT_AMOUNT; ?></td>
                                                                        <td align="left"><?php echo  $txtAmount ?></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left"><?php echo TEXT_COMMISSION; ?></td>
                                                                        <td align="left"><?php echo  $txtCommission ?></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left"><?php echo TEXT_MODE; ?></td>
                                                                        <td align="left"><?php echo  $txtMode ?></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left"><?php echo TEXT_REFERENCE_NUMBER; ?></td>
                                                                        <td align="left"><?php echo  htmlentities($txtTransactionNumber) ?></td>
                                                                    </tr>
                                                                </table></td>
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