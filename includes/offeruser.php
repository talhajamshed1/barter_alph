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
function func_offer_user($uid = 0, $fid = 0) { $errsflag=0; global $conn;
    ?>
    <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
        <form name="frmSwap" id="frmSwap" ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <input name="postback" type="hidden" id="postback">
            <?php
            $title = HEADING_OFFER_LIST_USER;

            $txtSearch = "";
            $cmbSearchType = "";
            $var_rf = "";
            $var_no = "";
            if ($_GET["txtSearch"] != "") {
                $txtSearch = $_GET["txtSearch"];
                $cmbSearchType = $_GET["cmbSearchType"];
            }//end if
            else if ($_POST["txtSearch"] != "") {
                $txtSearch = $_POST["txtSearch"];
                $cmbSearchType = $_POST["cmbSearchType"];
            }//end else if

            $qryopt = "";
            if ($txtSearch != "") {
                if ($cmbSearchType == "title") {
                    $qryopt .= " AND S.vtitle like '" . addslashes($txtSearch) . "%'";
                }//end if
                else if ($cmbSearchType == "user") {
                    $qryopt .= " AND U.vLoginName like '" . addslashes($txtSearch) . "%'";
                }//end else if
                else if ($cmbSearchType == "type") {
                    $qryopt .= "  AND ST.vPostType like '" . addslashes($txtSearch) . "%'";
                }//end else if
            }//end if


            $sql = "Select ST.nSwapId,ST.nUserId,ST.vPostType,ST.vStatus,ST.nSTId,ST.nParentId,
                U.vLoginName as 'UserName',
                S.vTitle,date_format(ST.dDate,'%m/%d/%Y') as 'dPostDate'  from
                " . TABLEPREFIX . "swaptxn ST 
                    Left outer join " . TABLEPREFIX . "swap S on ST.nSwapId = S.nSwapId
                    Left Outer Join " . TABLEPREFIX . "users U on ST.nUserId = U.nUserId
                where ST.nUserReturnId = '" . $_SESSION["guserid"] . "' ";

            $targetfile = "";
            $detailfile = "";
            if ($uid === 0) {
                $targetfile = "offeruserdetailed.php";
                $detailfile = "offermanip.php";
            }//end if

            $sql .= $qryopt . " Order By ST.dDate DESC ";
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

            $message = ($message != '') ? $message : $_SESSION['sessionMsg'];
            unset($_SESSION['sessionMsg']);

            if (isset($message) && $message != '') {
                ?>
                <tr bgcolor="#FFFFFF">
                    <td colspan="6" align="center" class="warning"><?php echo $message; ?></td>
                </tr>
            <?php }//end if?>
            <tr bgcolor="#FFFFFF">
                <td colspan="6" align="center"><table border="0" width="100%" class="maintext">
                        <tr>
                            <td valign="top" align="right">
                                <?php echo TEXT_SEARCH; ?>
                                &nbsp; <select name="cmbSearchType" class="textbox2">
                                    <option value="title" <?php if ($cmbSearchType == "title") {
                                        echo("selected");
                                    } ?>><?php echo TEXT_TITLE; ?></option>
                                    <option value="user" <?php if ($cmbSearchType == "user") {
                                        echo("selected");
                                    } ?>><?php echo TEXT_FIRST_NAME; ?></option>
                                    <option value="type" <?php if ($cmbSearchType == "type") {
                                        echo("selected");
                                    } ?>><?php echo TEXT_TYPE; ?></option>
                                </select>
                                &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode=='13'){ return false; }" class="textbox2">
                            </td>
                            <td align="left" valign="baseline" style="padding-top:5px;">
                                <a href="javascript:document.frmSwap.submit();" class="login_btn go_button2"><?php echo BUTTON_GO; ?><!--<img src='./images/gobut.gif'  width="20" height="20" border='0' >--></a>
                            </td>
                        </tr>
                    </table></td>
            </tr>
            <tr align="center" bgcolor="#FFFFFF" class="gray">
                <td width="7%"><?php echo TEXT_SLNO; ?></td>
                <td width="19%"><?php echo TEXT_TITLE; ?></td>
                <td width="19%"><?php echo TEXT_USERNAME; ?></td>
                <td width="19%"><?php echo TEXT_DATE; ?></td>
                <td width="16%"><?php echo TEXT_OFFER_TYPE; ?></td>
                <td width="20%"><?php echo TEXT_STATUS; ?></td>
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

        while ($arr = mysqli_fetch_array($rs)) {
            $newStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'vStatus', "WHERE nSwapId= '" . addslashes($arr['nSwapId']) . "'
                  											AND nUserId = '" . addslashes($arr['nUserId']) . "' AND nSTId='" . $arr['nSTId'] . "'"), 'vStatus');

            //checking offer status
            switch ($newStatus) {
                case "A":
                    $shwOfferStatus = TEXT_ACCEPTED;
                    $errsflag=0;
                    break;

                case "R":
                    $shwOfferStatus = TEXT_REJECTED;
                    $errsflag=1;
                    break;

                case "N":
                    $shwOfferStatus = TEXT_INVALID;
                    $errsflag=1;
                    break;

                default:
                    $shwOfferStatus = TEXT_INPROGRESS;
                    $errsflag=0;
                    break;
            }//end switch
            ?>
                    <tr bgcolor="#FFFFFF">
                        <td align="center"><?php echo $cnt; ?></td>
                        <td class="maintext">
                    <?php
                    if ($shwOfferStatus != 'Invalid')
                        echo "<a href='" . (($arr["vPostType"] == "swap") ? "makeoffer.php" : "makeoffer.php") . "?post_type=".$arr["vPostType"]."&userid=" . $arr["nUserId"] . "&uname=" . urlencode($arr["UserName"]) . "&nSTId=" . $arr['nSTId'] . "'>" . htmlentities($arr["vTitle"]) . "</a>";
                    else
                        echo htmlentities($arr["vTitle"]);
                    ?>
                        </td>
                        <td><?php echo htmlentities($arr['UserName']); ?></td>
                        <td><?php echo date('m/d/Y', strtotime($arr["dPostDate"])); ?></td>
                        <td><?php echo (($arr["vPostType"] == "swap") ? TEXT_SWAP_OFFER : TEXT_WISH_OFFER); ?></td>
                        <td class="<?php if($errsflag == 0){ ?>success<?php }else{?>warning<?php } ?>"><?php echo $shwOfferStatus; ?></td>
                    </tr>
            <?php
            $cnt++;
        }//end while
    }//end if
    ?>
            <tr bgcolor="#FFFFFF">
                <td colspan="6" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
                        <tr>
                            <td align="left"><?php echo($navigate[2]); ?></td>
                            <td align="right"><?php echo str_replace('{total_rows}',$totalrows,str_replace('{current_rows}',$navigate[1],TEXT_LISTING_RESULTS)); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center"  colspan="6"><div class="AddLinks" style="width:150px;"><a href="usermain.php"><b><?php echo LINK_BACK_TO_DASHBOARD; ?></b></a></div></td>
            </tr>
        </form>
    </table>
    <?php
}

//end function
?>







