<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE = 'languages';

//delete 
if (isset($_GET['mode']) && $_GET['mode'] == 'delete') {
    //remove deleted image from the folder
    @unlink("../lang_flags/" . $_GET['imgname']);

    mysqli_query($conn, "delete from " . TABLEPREFIX . "lang where lang_id='" . $_GET['id'] . "'") or die(mysqli_error($conn));
    header('location:languages.php?msg=d');
    exit();
}//end if
?>

<div class="row admin_wrapper">
	<div class="admin_container">

 
 
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="18%" valign="top"> <!--  Admin menu comes here -->
            <?php require("../includes/adminmenu.php"); ?>
            <td width="4%"></td>
            <!--   Admin menu  comes here ahead --></td>
        <td width="78%" valign="top">
            

            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td width="100%" class="heading_admn boldtextblack" align="left">Languages</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top">
                        <table width="100%"  border="0" cellpadding="5" cellspacing="1" class="">
                            <tr>
                                <td align="right"  height="46" valign="middle" ><a href="add_languages.php" class="AddLinks"> 
                                <span class="glyphicon glyphicon-plus"></span>Add Language</a></td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2">
                            <tr>
                                <td bgcolor="#ffffff" class="noborderbottm">
                                    <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frm" id="frm" ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                                            <?php
                                            $sql = "SELECT * from " . TABLEPREFIX . "lang";
                                            $sess_back = $targetfile . "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;
                                            //get the total amount of rows returned
                                            $totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

                                            /*
                                              Call the function:

                                              I've used the global $_GET array as an example for people
                                              running php with register_globals turned 'off' :)
                                             */

                                            $navigate = pageBrowser($totalrows, 10, 10, "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
                                            //execute the new query with the appended SQL bit returned by the function
                                            $sql = $sql . $navigate[0];
                                            $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                                            switch ($_GET['msg']) {
                                                case "a":
                                                    $message = 'Language added successfully.';
                                                    break;

                                                case "e":
                                                    $message = 'Language updated successfully.';
                                                    break;

                                                case "d":
                                                    $message = 'Language deleted successfully.';
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
                                                    <td colspan="7" align="center" class="warning"><?php echo $message; ?></td>
                                                </tr>
                                            <?php }//end if?>			
                                            <tr align="center" bgcolor="#FFFFFF" class="gray">
                                                <td width="8%">Sl No. </td>
                                                <td width="15%">Language</td>
                                                <td width="15%">Flag</td>
                                                <td width="15%">Active</td>
                                                <td width="15%">Action</td>
                                            </tr>
                                            <?php
                                            if (mysqli_num_rows($rs) > 0) {
                                                $count = mysqli_num_rows($rs);
                                                switch ($_GET['begin']) {
                                                    case "":
                                                        $cnt = 1;
                                                        break;

                                                    default:
                                                        $cnt = $_GET['begin'] + 1;
                                                        break;
                                                }//end switch

                                                while ($arr = mysqli_fetch_array($rs)) {
                                                    ?>
                                                    <tr bgcolor="#FFFFFF">
                                                        <td align="center"><?php echo $cnt; ?></td>
                                                        <td class="maintext"><?php echo htmlentities($arr["lang_name"]); ?></td>
                                                        <td class="maintext" align="center">
                                                            <?php
                                                            if ($arr["flag_file"] != '') {
                                                                echo '<img src="../lang_flags/' . $arr["flag_file"] . '" name="img1" border="0">';
                                                            }//end if
                                                            else {
                                                                echo 'N/A';
                                                            }//end else
                                                            ?>
                                                        </td>
                                                        <td align="center">
                                                            <?php if ($arr['lang_status'] == 'y') {
                                                                echo 'Yes';
                                                            } else {
                                                                echo 'No';
                                                            } ?>
                                                        </td>
                                                        <td align="center"><?php echo "<a href='add_languages.php?id=" . $arr["lang_id"] . "&mode=edit'>Edit</a>"; ?>&nbsp;|&nbsp;<?php echo "<a href='languages.php?id=" . $arr["lang_id"] . "&mode=delete&imgname=" . $arr["flag_file"] . "' onClick=\"javascript:return confirm('Are you sure you want to delete?')\">Delete</a>"; ?></td>
                                                    </tr>
                                                        <?php
                                                        $cnt++;
                                                    }//end while
                                                }//end if
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="7" align="left" class="noborderbottm">
                                                    <table width="100%"  border="0" cellspacing="1" cellpadding="5">
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
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</div>
</div>
<?php include_once('../includes/footer_admin.php'); ?>