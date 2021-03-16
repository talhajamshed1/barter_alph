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
function func_saleoffer_detailed($uid = 0, $fid = 0) {
    global $conn;
    $var_uname = ($_GET["uname"] != "") ? $_GET["uname"] : $_POST["uname"];
    ?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="heading" align="left"><?php
    if ($var_uname != "") {
        echo str_replace('{user_name}',htmlentities($var_uname),HEADING_SALES_LIST_FROM_USER);
    }//end if
    else {
        echo HEADING_MY_SALES_OFFERS;
    }//end else
    ?>
            </td>
        </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
        <tr>
            <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                <form name="frmSale" id="frmSale" ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                                    <input name="postback" type="hidden" id="postback">
                                    <input NAME="rf" TYPE="hidden" id="rf" VALUE="<?php echo  $var_rf ?>">
                                    <input NAME="no" TYPE="hidden" id="no" VALUE="<?php echo  $var_no ?>">
                                    <input name="uname" TYPE="hidden" id="uname" VALUE="<?php echo  htmlentities($var_uname) ?>">
                                    <?php
                                    
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

                                    if ($_GET["rf"] == "sid" || $_POST["rf"] == "sid") {
                                        $var_rf = "sid";
                                        $var_no = (strlen($_GET["no"]) > 0) ? $_GET["no"] : $_POST["no"];
                                    }//end if


                                    $qryopt = "";
                                    if ($txtSearch != "") {
                                        if ($cmbSearchType == "title") {
                                            $qryopt .= " AND s.vtitle like '" . addslashes($txtSearch) . "%'";
                                        }//end if
                                        else if ($cmbSearchType == "login") {
                                            $qryopt .= " AND u.vLoginName like '" . addslashes($txtSearch) . "%'";
                                        }//end else if
                                        else if ($cmbSearchType == "ddate") {
                                            $splitdate = explode("/", $txtSearch);
                                            $newdate = $splitdate[2] . "-" . $splitdate[0] . "-" . $splitdate[1];
                                            $qryopt .= "  AND sd.dDate like '" . addslashes($newdate) . "%'";
                                        }//end else if
                                    }//end if


                                    $targetfile = "usersaledetailed.php";
                                    $detailfile = "buynext.php";

                                //checking escrow status
                                    if (DisplayLookUp('Enable Escrow') == 'Yes') {
                                        $SaleStatus = '';
                                    }//end if
                                    else {
                                        $SaleStatus = " OR sd.vSaleStatus ='4'";
                                    }//end esle

                                    $sql = "Select sd.nSaleId,sd.nAmount,sd.nQuantity,date_format(sd.dDate,'%m/%d/%Y') as 'dDate',dDate as 'dDate2',s.vTitle,u.vLoginName from ";
                                    $sql .= " " . TABLEPREFIX . "saledetails sd inner join " . TABLEPREFIX . "sale s on sd.nSaleId = s.nSaleId inner join " . TABLEPREFIX . "users u on ";
                                    $sql .= " s.nUserId = u.nUserId  where sd.nUserId='" . $_SESSION["guserid"] . "' AND (sd.vSaleStatus ='2'  OR sd.vSaleStatus ='3' " . $SaleStatus . ") ";
                                    $sql .= $qryopt . " ORDER BY s.vFeatured DESC,sd.dDate DESC ";

                                    $sess_back = $targetfile . "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;
                                //get the total amount of rows returned
                                    $totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

                                    /*
                                      Call the function:

                                      I've used the global $_GET array as an example for people
                                      running php with register_globals turned 'off' :)
                                     */

                                    $navigate = pageBrowser($totalrows, 10, 10, "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
                                //execute the new query with the appended SQL bit returned by the function
                                    $sql = $sql . $navigate[0];
                                    $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                                    $message = ($message != '') ? $message : $_SESSION['sessionMsg'];
                                    unset($_SESSION['sessionMsg']);

                                    if (isset($message) && $message != '') {
                                        ?>
                                        <tr bgcolor="#FFFFFF">
                                            <td colspan="4" align="center" class="warning"><?php echo $message; ?></td>
                                        </tr>
                                    <?php }//end if?>
                                    <tr bgcolor="#FFFFFF">
                                        <td colspan="4" align="center"><table border="0" width="100%" class="maintext">
                                                <tr>
                                                    <td valign="top" align="right">
                                                        <?php echo TEXT_SEARCH; ?>
                                                        &nbsp; <select name="cmbSearchType" class="textbox2">
                                                            <option value="title" <?php if ($cmbSearchType == "title") {
                                                                echo("selected");
                                                            } ?>><?php echo TEXT_TITLE; ?></option>
                                                            <option value="login" <?php if ($cmbSearchType == "login") {
                                                                echo("selected");
                                                            } ?>><?php echo TEXT_LOGIN_NAME; ?></option>
                                                            <option value="ddate" <?php if ($cmbSearchType == "ddate") {
                                                            echo("selected");
                                                        } ?>><?php echo TEXT_DATE; ?></option>
                                                        </select>
                                                        &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode=='13'){ return false; }" class="textbox2">
                                                    </td>
                                                    <td align="left" valign="baseline" style="padding-top:5px;">
                                                        <a href="javascript:document.frmSale.submit();" class="login_btn go_button2"><?php echo BUTTON_GO; ?><!--<img src='./images/gobut.gif'  width="20" height="20" border='0' >--></a>
                                                    </td>
                                                </tr>
                                            </table></td>
                                    </tr>
                                    <tr align="center" bgcolor="#FFFFFF" class="gray">
                                        <td width="8%"><?php echo TEXT_SLNO; ?> </td>
                                        <td width="36%"><?php echo TEXT_TITLE; ?></td>
                                        <td width="28%"><?php echo TEXT_USERNAME; ?></td>
                                        <td width="28%"><?php echo TEXT_DATE; ?></td>
                                    </tr>
                                        <?php
                                        if (mysqli_num_rows($rs) > 0) {
                                            $cnt = 1;
                                            while ($arr = mysqli_fetch_array($rs)) {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="center"><?php echo $cnt; ?></td>
                                                <td class="maintext"><?php echo "<a href='" . $detailfile . "?saleid=" . $arr["nSaleId"] . "&tot=" . $arr["nAmount"] . "&reqd=" . $arr["nQuantity"] . "&dt=" . urlencode($arr["dDate2"]) . "&'>" . htmlentities($arr["vTitle"]) . "</a>"; ?></td>
                                                <td><?php echo htmlentities($arr['vLoginName']); ?></td>
                                                <td><?php echo date('m/d/Y', strtotime($arr["dDate"])); ?></td>
                                            </tr>
                                            <?php
                                            $cnt++;
                                        }//end while
                                    }//end if
                                    ?>
                                    <tr bgcolor="#FFFFFF">
                                        <td colspan="4" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
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
                        </td>
                    </tr>
                </table></td>
        </tr>
    </table>
    <?php
}

//end funciton?>