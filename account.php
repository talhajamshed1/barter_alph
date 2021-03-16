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
if ($_POST["txtSearch"] != "") {
    $txtSearch = $_POST["txtSearch"];
}//end if
else if ($_GET["txtSearch"] != "") {
    $txtSearch = $_GET["txtSearch"];
}//end else if

if ($_POST["ddlSearchType"] != "") {
    $ddlSearchType = $_POST["ddlSearchType"];
}//end if
else if ($_GET["ddlSearchType"] != "") {
    $ddlSearchType = $_GET["ddlSearchType"];
}//end else

if ($txtSearch != "") {
    if ($ddlSearchType == "transmode") {
        $qryopt .= "  and vMode like '" . addslashes($txtSearch) . "%'";
    }//end if
    else if ($ddlSearchType == "transno") {
        $qryopt .= "  and vModeNo like '" . addslashes($txtSearch) . "%'";
    }//end if
    else if ($ddlSearchType == "amount") {
        $qryopt .= "  and nAmount like '" . addslashes($txtSearch) . "%'";
    }//end if
    else if ($ddlSearchType == "date") {
        $date = $txtSearch;
       // $arr = split("/", $date);
         $arr = explode("/", $date);
        if (strlen($arr[0]) < 2) {
            $month = "0" . $arr[0];
        }//end if
        else {
            $month = $arr[0];
        }//end else

        if (strlen($arr[1]) < 2) {
            $day = "0" . $arr[1];
        }//end if
        else {
            $day = $arr[1];
        }//end else

        $year = $arr[2];
        $newdate = $year . "-" . $month . "-" . $day;
        $qryopt .= "  and dDate  like '" . addslashes($newdate) . "%'";
    }//end else
}//end if

$sql = "SELECT * FROM " . TABLEPREFIX . "cashtxn  WHERE nUserId  = '" . $_SESSION["guserid"] . "' " . $qryopt . "  order by dDate DESC ";
$sess_back = "account.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch;
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));
$navigate = pageBrowser($totalrows, 5, 5, "&ddlSearchType=$ddlSearchType&txtSearch=$txtSearch&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql . $navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$sql1 = "SELECT nAccount FROM " . TABLEPREFIX . "users  WHERE nUserId = '" . $_SESSION["guserid"] . "'";
$result = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);
if ($row["nAccount"] != "0") {
    $amount = $row["nAccount"];
}//end if
else {
    $amount = "0";
}//end else

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmAffMain.submit();
    }//end function
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
                                                <td class="heading" align="left"><?php echo TEXT_ACCOUNT_SUMMARY; ?></td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                                    <form name="frmAffMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
                                                                    <?php
                                                                    $message = ($message != '') ? $message : $_SESSION['sessionMsg'];
                                                                    unset($_SESSION['sessionMsg']);

                                                                    if (isset($message) && $message != '') {
                                                                        ?>
                                                                            <tr bgcolor="#FFFFFF">
                                                                                <td colspan="5" align="center" class="warning"><?php echo $message; ?></td>
                                                                            </tr>
                                                                    <?php }//end if ?>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td colspan="5" align="center"><b><?php echo TEXT_YOUR_PENDING_SETTLEMENT_IS; ?> <span class="warning"><?php echo CURRENCY_CODE; ?><?php echo  $amount ?></span></b></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td colspan="5" align="center"><table border="0" width="100%" class="maintext">
                                                                                    <tr>
                                                                                        <td valign="top" align="right">
                                                                                            <?php echo TEXT_SEARCH; ?>
                                                                                            &nbsp;               &nbsp;<select name="ddlSearchType" class="textbox2">
                                                                                                <option value="date" <?php if ($ddlSearchType == "date" || $ddlSearchType == "") {
                                                                                                        echo("selected");
                                                                                                    } ?>><?php echo TEXT_TRANSACTION_DATE; ?>(<?php echo TEXT_MM_DD_YYYY; ?>)</option>
                                                                                                <option value="amount"  <?php if ($ddlSearchType == "amount") {
                                                                                                        echo("selected");
                                                                                                    } ?>><?php echo TEXT_AMOUNT; ?></option>
                                                                                                <option value="transmode"  <?php if ($ddlSearchType == "transmode") {
                                                                                                        echo("selected");
                                                                                                    } ?>><?php echo TEXT_TRANSACTION_MODE; ?></option>
                                                                                                <option value="transno" <?php if ($ddlSearchType == "transno") {
                                                                                                        echo("selected");
                                                                                                    } ?>><?php echo TEXT_TRANSACTION_NUMBER; ?></option>
                                                                                            </select>
                                                                                            <input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                                                                        </td>
                                                                                        <td align="left" valign="baseline" style="padding-top:5px;">
                                                                                            <a href="javascript:clickSearch();" class="login_btn go_button2"><?php echo BUTTON_GO; ?><!--<img src='./images/gobut.gif'  width="20" height="20" border='0' >--></a>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table></td>
                                                                        </tr>
                                                                        <tr align="center" bgcolor="#FFFFFF" class="gray">
                                                                            <td width="7%"><?php echo TEXT_SLNO; ?></td>
                                                                            <td width="16%"><?php echo TEXT_TRANSACTION_DATE; ?></td>
                                                                            <td width="19%"><?php echo TEXT_TRANSACTION_NUMBER; ?></td>
                                                                            <td width="19%"><?php echo TEXT_TRANSACTION_MODE; ?></td>
                                                                            <td width="19%"><?php echo TEXT_AMOUNT; ?></td>
                                                                        </tr>
                                                                        <?php
                                                                        if (mysqli_num_rows($rs) > 0) {
                                                                            $cnt = 1;
                                                                            while ($arr = mysqli_fetch_array($rs)) {
                                                                                ?>
                                                                                <tr bgcolor="#FFFFFF">
                                                                                    <td align="center"><?php echo $cnt; ?></td>
                                                                                    <td><?php echo date('m/d/Y', strtotime($arr["dDate"])); ?></td>
                                                                                    <td><?php echo htmlentities($arr["vModeNo"]); ?></td>
                                                                                    <td><?php echo htmlentities($arr["vMode"]); ?></td>
                                                                                    <td><?php echo htmlentities($arr["nAmount"]); ?></td>
                                                                                </tr>
                                                                                <?php
                                                                                $cnt++;
                                                                            }//end while
                                                                        }//end if
                                                                        ?>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td colspan="5" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
                                                                                    <tr>
                                                                                        <td align="left"><?php echo($navigate[2]); ?></td>
                                                                                        <td align="right"><?php echo str_replace('{total_rows}', $totalrows, str_replace('{current_rows}', $navigate[1], TEXT_LISTING_RESULTS)); ?></td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </form>
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