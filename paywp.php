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
                                                                        <td width="20%" align="center">
                                                                        <?php
                                                                        $var_tmpid = $_SESSION["gtempid"];
                                                                        $sql = "Select vLoginName,nAmount,vFirstName from " . TABLEPREFIX . "users where nUserId='" . $var_tmpid . "'";
                                                                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                                        if (mysqli_num_rows($result) > 0) {
                                                                            $row = mysqli_fetch_array($result);
                                                                            $uname = $row["vLoginName"];
                                                                            $var_amount = $row["nAmount"];
                                                                            $var_fname = $row["vFirstName"];
                                                                        }//end if
                                                                        ?>
                                                                            <b><?php echo TEXT_MAKE_PAYMENT_WORLDPAY; ?>  <img src="./images/worldpay.jpg" title="" border="0" ></b></td>
                                                                    </tr>
                                                                            <?php echo $showPlanOrReg; ?>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="center"><?php echo TEXT_AMOUNT; ?> : <?php echo CURRENCY_CODE; ?> <b><?php echo $var_amount; ?></b></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td width="20%" align="center">
                                                                            <?php
                                                                            //checking escrow status
                                                                            //if (DisplayLookUp('Enable Escrow') == 'Yes') {
                                                                            if (DisplayLookUp('plan_system')!='yes') {
                                                                                ?>
                                                                                <form action="<?php echo $worldpayserver; ?>" name="BuyForm" method="POST">
                                                                                    <input type="hidden" name="instId"  value="<?php echo $txtWorldInstId; ?>">
                                                                                    <input type="hidden" name="cartId" value="<?php echo SITE_NAME; ?> <?php echo TEXT_REGISTRATION; ?>">
                                                                                    <input type="hidden" name="currency" value="<?php echo PAYMENT_CURRENCY_CODE; ?>">
                                                                                    <input type="hidden" NAME="email" VALUE="<?php echo $txtWorldEmailId; ?>">
                                                                                    <input type="hidden" name="amount"  value="<?php echo round($var_amount, 2); ?>">
                                                                                    <input type="hidden" name="testMode" value="<?php echo $txtWorldpaySandbox2; ?>">
                                                                                    <input type="hidden" NAME="MC_ORDERID" VALUE="SR1">
                                                                                    <input type='hidden' name='authMode' value="<?php echo $txtWorldTransMode; ?>">
                                                                                    <input type="hidden" name="MC_callback" value="<?php echo SITE_URL . "/success_wp.php"; ?>">
                                                                                    <input name="imageField" type="image" src="./images/cc.jpg" width="180" height="31" border="0"  title="" alt="">
                                                                                </form>
                                                                                <?php
                                                                            }//end if
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