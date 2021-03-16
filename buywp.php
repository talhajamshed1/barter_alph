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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/session_check.php");

include_once('./includes/gpc_map.php');

//worldpay details
$txtWorldInstId = DisplayLookUp('worldpayid');
$txtWorldEmailId = DisplayLookUp('worldpayemail');
$txtWorldTransMode = DisplayLookUp('worldpaytransmode');
$txtWorldpaySandbox = DisplayLookUp('worldpaydemo');
$worldpayenabled = DisplayLookUp('enableworldpay');

if ($txtWorldpaySandbox == "YES") {
    $worldpayserver = "https://select-test.worldpay.com/wcc/purchase";
    $txtWorldpaySandbox2 = '100';
}//end if
else {
    $worldpayserver = "https://select.worldpay.com/wcc/purchase";
    $txtWorldpaySandbox2 = '0';
}//end if

if ($worldpayenabled != "Y") {
    header('location:index.php');
    exit();
}//end if

include_once('./includes/title.php');
?>
<body onLoad="javascript:document.BuyForm.submit();">
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
                                                <td class="heading" align="left"><?php echo HEADING_PAYMENT_PROCESS; ?></td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td width="100%" align="center">
                                                                            <?php
                                                                            if ($_GET["saleid"] != "") {
                                                                                $amnt = $_GET["amnt"];
                                                                                $saleid = $_GET["saleid"];
                                                                                //$userid=$_GET["userid"];
                                                                                $now = $_GET["dt"];
                                                                            }//end if
                                                                            else if ($_POST["saleid"] != "") {
                                                                                $saleid = $_POST["saleid"];
                                                                                $now = urldecode($_POST["dt"]);
                                                                                $cost = $_POST["amnt"];
                                                                                $amnt = $cost;
                                                                            }//end else if

                                                                            $_SESSION['sess_saleid'] = $saleid;
                                                                            $_SESSION['sess_amount'] = round($amnt, 2);

                                                                            $userid = $_SESSION["guserid"];
                                                                            $cc_err = "";
                                                                            $cc_flag = false;
                                                                            $var_insert_flag = false;

                                                                            $var_sale_flag = false;
                                                                            $var_rej_flag = false;
                                                                            $sql = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
                                                                            $sql .= " on sd.nSaleId = s.nSaleId ";
                                                                            $sql .= " where  sd.nSaleId='" . addslashes($saleid) . "' AND sd.nUserId='" . $_SESSION["guserid"] . "' AND sd.dDate='";
                                                                            $sql .= addslashes($now) . "' ";

                                                                            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                                            if (mysqli_num_rows($result) > 0) {
                                                                                if ($row = mysqli_fetch_array($result)) {
                                                                                    $cost = $row["nAmount"];
                                                                                    $amnt = $cost;
                                                                                    $var_title = $row["vTitle"];
                                                                                    $reqd = $row["nQuantity"];
                                                                                    if ($row["vSaleStatus"] == "1") {
                                                                                        $var_sale_flag = true;
                                                                                    }//end if
                                                                                    if ($row["vRejected"] == "0") {
                                                                                        $var_rej_flag = true;
                                                                                    }//end if
                                                                                }//end if
                                                                            }//end if
                                                                            else {
                                                                                $cc_err = '<span class="warning">'.ERROR_CHECK_YOUR_INPUT.'</span>';
                                                                            }//end else

                                                                            $sql = "Select nSaleInterId from " . TABLEPREFIX . "saleinter where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
                                                                            $sql .= addslashes($now) . "' AND vDelStatus='0' ";
                                                                            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                                            if (mysqli_num_rows($result) > 0) {

                                                                                header('buyothers.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=$amnt&');
                                                                                exit();
                                                                            }//end if
                                                                            else {
                                                                                $var_insert_flag = true;
                                                                            }//end else

                                                                            if ($var_sale_flag == true && $var_rej_flag == true) {
                                                                                ?>
                                                                                <form action="<?php echo $worldpayserver; ?>" name="BuyForm" method="POST">
                                                                                    <input type="hidden" name="instId"  value="<?php echo $txtWorldInstId; ?>">
                                                                                    <input type="hidden" name="cartId" value="<?php echo htmlentities($var_title); ?>">
                                                                                    <input type="hidden" name="currency" value="<?php echo PAYMENT_CURRENCY_CODE; ?>">
                                                                                    <input type="hidden" NAME="email" VALUE="<?php echo $txtWorldEmailId; ?>">
                                                                                    <input type="hidden" name="amount"  value="<?php echo round($amnt, 2); ?>">
                                                                                    <input type="hidden" name="testMode" value="<?php echo $txtWorldpaySandbox2; ?>">
                                                                                    <input type="hidden" NAME="MC_ORDERID" VALUE="<?php echo $saleid; ?>">
                                                                                    <input type='hidden' name='authMode' value="<?php echo $txtWorldTransMode; ?>">
                                                                                    <input type="hidden" name="MC_callback" value="<?php echo SITE_URL . "/worldpay_success.php"; ?>">
                                                                                    <input name="imageField" type="image" src="./images/cc.jpg" width="180" height="31" border="0"  title="" alt="">
                                                                                </form>
                                                                                <?
                                                                            } else {
                                                                                header("location:" . SITE_URL . "/index.php?paid=no");
                                                                                exit();
                                                                            }
                                                                            ?>
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