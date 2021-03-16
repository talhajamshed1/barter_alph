<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com ? 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE = 'settings';

//checking admin enabled listing fee
if (DisplayLookUp('Enable Escrow') != 'yes' && DisplayLookUp('EscrowCommissionType') != 'range') {
    header('location:setconf.php');
    exit();
}//end if
//delete
if (isset($_GET['mode']) && $_GET['mode'] == 'delete') {
    $nLId = $_GET['nLId'];

    $sqlDel = "DELETE FROM " . TABLEPREFIX . "escrowrangefee WHERE nLId='" . $nLId . "'";
    mysqli_query($conn, $sqlDel) or die(mysqli_error($conn));
    header('location:manage_commission_range.php?msg=d');
    exit();
}//end if
//ordering content
if (isset($_GET['Action']) && $_GET['Action'] == 'ordering') {
    $oldId = $_GET['id'];
    $oldPosition = $_GET['pos'];
    $table = TABLEPREFIX . "escrowrangefee";
    $PositionfieldName = 'nLPosition'; //db field name
    $IdfieldName = 'nLId'; //db field name
    $returnPath = 'location:manage_commission_range.php';

    if (isset($_GET['move']) && $_GET['move'] == 'up') {
        OrderUp($table, $oldId, $oldPosition, $PositionfieldName, $IdfieldName, $returnPath);
        listing();
    }//end if

    if (isset($_GET['move']) && $_GET['move'] == 'down') {
        OrderDown($table, $oldId, $oldPosition, $PositionfieldName, $IdfieldName, $returnPath);
        listing();
    }//end if
}//end if

$abrs = select_rows(TABLEPREFIX . 'escrowrangefee', 'nLId', "WHERE vActive='1' and above <> ''");
?>
<link href="../styles/tabcontent.css" rel="stylesheet" type="text/css">

<div class="row admin_wrapper">
	<div class="admin_container">
	
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="18%" valign="top"> <!--  Admin menu comes here -->
<?php require("../includes/adminmenu.php"); ?>
            <!--   Admin menu  comes here ahead --></td>
		<td width="4%" valign="top"</td>
        <td width="78%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="94%" height="32" class="headerbg">&nbsp;</td>
                    <td width="6%" align="right" valign="bottom" class="headerbg"><a href="adminmain.php"><img src="../images/home-icon1.gif" width="44" height="25" border="0"></a></td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td width="84%" class="heading_admn boldtextblack" align="left">Swap Listing Fee Range </td>
                    <td width="16%" class="heading_admn">&nbsp;</td>
                </tr>
            </table>
<?php include_once('../includes/settings_menu.php'); ?>
            <div class="tabcontentstyle">
                <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="padding_T_B_td">
                    <tr>
                        <td align="left" valign="top" style="padding:10px 0 0px!important; border:0; ">
                            <table width="100%"  border="0" cellpadding="5" cellspacing="1" >
<?php if (!(mysqli_num_rows($abrs))) { ?>
                                    <tr>
                                        <td align="left" style="border:0; "><a href="add_commission_range.php" class="AddLinks">Add New Range</b></td>
                                    </tr>
    <?php
} else {
    echo "<tr><td align='center' class='warning'><br>You would be allowed to add a new escrow fee range only if the 'Above " . CURRENCY_CODE . "xxx' range entry is removed
                        </td></tr>";
}
?>
                            </table>
							</td>
						</tr>
					<tr>
						<td align="left" valign="top" style="border:0; ">
                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                            <form name="frmswapListingFee" id="frmswapListingFee" ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" onSubmit="return Validate();">
                                <?php
                                $sql = "SELECT * FROM " . TABLEPREFIX . "escrowrangefee ORDER BY nLPosition DESC";

                                $sess_back = $targetfile . "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;

//get the total amount of rows returned
                                $totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

                                /*
                                  Call the function:

                                  I've used the global $_GET array as an example for people
                                  running php with register_globals turned 'off' :)
                                 */

                                $navigate = pageBrowser($totalrows, 10, 10, "&ddlCategory=" . $ddlCategory . "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);

//execute the new query with the appended SQL bit returned by the function
                                $sql = $sql . $navigate[0];
                                $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                                switch ($_GET['msg']) {
                                    case "a":
                                        $message = 'Range added successfully.';
                                        break;

                                    case "e":
                                        $message = 'Range updated successfully.';
                                        break;

                                    case "d":
                                        $message = 'Range deleted successfully.';
                                        break;

                                    default:
                                        $message = '';
                                        break;
                                }//end if
                                ?>
                                                <?php
                                                if (isset($message) && $message != '') {
                                                    ?>
                                                    <tr bgcolor="#FFFFFF">
                                                        <td colspan="6" align="center" class="warning"><?php echo $message; ?></td>
                                                    </tr>
                                                <?php }//end if?>
                                                <tr align="center" bgcolor="#FFFFFF" class="gray">
                                                    <td width="8%">Sl No. </td>
                                                    <td width="15%">Range</td>
                                                    <td width="15%">Listing Price (%)</td>
                                                    <td width="15%">Set Order </td>
                                                    <td width="15%">Active</td>
                                                    <td width="15%">Action</td>
                                                </tr>
                                                <?php
                                                if (mysqli_num_rows($rs) > 0) {
                                                    switch ($_GET['begin']) {
                                                        case "":
                                                            $cnt = 1;
                                                            break;

                                                        default:
                                                            $cnt = $_GET['begin'] + 1;
                                                            break;
                                                    }//end switch

                                                    $count = mysqli_num_rows($rs);
                                                    $oldId = 0;
                                                    while ($arr = mysqli_fetch_array($rs)) {
                                                        ?>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td align="center"><?php echo $cnt; ?></td>
                                                            <td class="maintext">
                                                        <?php
                                                        if ($arr["above"] == "" || $arr["above"] == 0)
                                                            echo CURRENCY_CODE . htmlentities($arr["nFrom"]) . '&nbsp;-&nbsp;' . CURRENCY_CODE . htmlentities($arr["nTo"]);
                                                        else
                                                            echo "Above " . CURRENCY_CODE . $arr["above"];
                                                        ?>
                                                            </td>
                                                            <td class="maintext"><?php echo htmlentities($arr["nPrice"]); ?></td>
                                                            <td align="center" class="maintext"><?php if ($cnt != 1) { ?>
                                                                    <a href='manage_commission_range.php?Action=ordering&move=up&id=<?php echo $arr["nLId"]; ?>&pos=<?php echo $arr["nLPosition"]; ?><?php echo $order_category; ?>'>
        <?php } ?>
                                                                    <img src="../images/up.gif" alt="Up" border=0></a>

                                                                <?php if ($cnt != $count) { ?>
                                                                    <a href="manage_commission_range.php?Action=ordering&move=down&id=<?php echo $arr["nLId"]; ?>&pos=<?php echo $arr["nLPosition"]; ?><?php echo $order_category; ?>">
                                                                <?php } ?>
                                                                    <img src="../images/down.gif" alt="Down" border=0></a></td>
                                                            <td align="center"><?php if ($arr['vActive'] == '1') {
                                                            echo 'Yes';
                                                        } else {
                                                            echo 'No';
                                                        } ?></td>
                                                            <td align="center"><?php echo "<a href='add_commission_range.php?nLId=" . $arr["nLId"] . "&mode=edit" . $order_category . "&oldId=" . $oldId . "'>Edit</a>"; ?>&nbsp;|&nbsp;<?php echo "<a href='manage_commission_range.php?nLId=" . $arr["nLId"] . "&mode=delete" . $order_category . "' onClick='javascript:return confirm(\"Are you sure you want to delete this?\");'>Delete</a>"; ?></td>
                                                        </tr>
        <?php
        $oldId = $arr["nLId"];
        $cnt++;
    }//end while
}//end if
?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td colspan="6" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
                                                            <tr>
                                                                <td align="left"><?php echo($navigate[2]); ?></td>
                                                                <td align="right"><?php echo("Listing $navigate[1] of $totalrows results."); ?></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </form>
                                        </table>
                                    </td>
                                </tr>
                            </table></td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
</div>
</div>
<?php include_once('../includes/footer_admin.php'); ?>