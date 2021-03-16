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

//if a post back is requested
if ($_GET["type"] == "sale") {
    //get posted values from form
    $txn = $_GET["txn"];
    $nUserId = $_SESSION["guserid"];

    //make sure the quanity asked is not purchased by another user
    $sql = "SELECT  * FROM " . TABLEPREFIX . "saletemp where nSaleTempId ='$txn'";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $nSaleId = $row["nSaleId"];
        $nUserId = $row["nUserId"];
        $vMethod = $row["vMethod"];
        $nAmount = $row["nAmount"];
        $dDate = $row["dDate"];
        $nQuantity = $row["nQuantity"];
    }//end while

    $sql = "INSERT INTO " . TABLEPREFIX . "saledetails (nSaleId, nUserId, vMethod, vTxnId,";
    $sql .="nAmount, dDate, nQuantity) VALUES ('$nSaleId', '$nUserId', '$vMethod', 'dd', '$nAmount', '$dDate', '$nQuantity')";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $sql = "DELETE FROM " . TABLEPREFIX . "saletemp where nSaleId='$nSaleId' and nUserId='$nUserId' and nSaleTempId ='$txn'";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $sql = "SELECT  nUserId FROM " . TABLEPREFIX . "sale where nSaleId ='$nSaleId'";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    while ($row = mysqli_fetch_array($result)) {
        $nUserId1 = $row["nUserId"];
    }//end while

//    $sql = "UPDATE " . TABLEPREFIX . "users SET nAccount =nAccount  + $nAmount where nUserId='$nUserId1' ";
//
//    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
}//end if
//display confirm message
//get item equested details
$sql = "SELECT * from " . TABLEPREFIX . "sale where nSaleId  = '$nSaleId' ";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) != 0) {
    while ($row = mysqli_fetch_array($result)) {
        $Title = $row["vTitle"];
    }//end while
}//end if
//get total price

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function varify()
    {

    }//end funciton


    function proceed(cc)
    {
    }//end funciton
</script>

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
                                                <td class="heading" align="left"><?php echo HEADING_PAYMENT_DETAILS; ?></td>
                                            </tr>
                                        </table>
                                        <table width="70%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                                    <form name="frmUserMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td width="28%" align="left"><?php echo TEXT_QUANTITY; ?></td>
                                                                            <td width="72%" align="left"><input type="text" name="quantityREQD"  class="textbox2" id="quantityREQD" size="3" maxlength="3"  readonly value="<?php echo  $nQuantity ?>"></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td align="left"><?php echo TEXT_AMOUNT; ?></td>
                                                                            <td align="left"><?php echo CURRENCY_CODE; ?> <input type="text" name="total"  id="total" size="5" maxlength="10" class="textbox2"  value="<?php echo  $nAmount ?>" readonly></td>
                                                                        </tr>
                                                                        <tr align="center" bgcolor="#FFFFFF">
                                                                            <td colspan="2"><strong><?php echo MESSAGE_THANKYOU_FOR_PAYMENT; ?></strong></td>
                                                                        </tr>
                                                                    </form>
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