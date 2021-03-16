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
function func_feed_detailed($uid = 0, $fid = 0) {
    global $conn;
    $var_uname = ($_GET["uname"] != "") ? $_GET["uname"] : $_POST["uname"];
    ?>
    <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
        <form name="frmSale" id="frmSale" ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <?php
            $txtSearch = "";
            $cmbSearchType = "";
            $var_rf = "";
            $var_no = "";

            
            $txtSearch          =   $_REQUEST['txtSearch'];
            $cmbSearchType      =   $_REQUEST['cmbSearchType'];
            
            
            if ($_GET["rf"] == "sid" || $_POST["rf"] == "sid") {
                $var_rf = "sid";
                $var_no = (strlen($_GET["no"]) > 0) ? $_GET["no"] : $_POST["no"];
            }//end if

            $qryopt = "";
            if($_REQUEST['num']!='') { 
                $page   = $_REQUEST['num'];
                }
                else {
                    $page   =   1 ;
                }

            if ($txtSearch != "") {
                $txtSearch = strip_tags($txtSearch);
                $txtSearch = trim($txtSearch);
                if ($cmbSearchType == "posteduser") {
                     $qryopt .= "  AND ( u.vFirstName like '%" . addslashes($txtSearch) . "%' || vLoginName like '%" . addslashes($txtSearch) . "%' ) ";
                }//end if
                else if ($cmbSearchType == "title") {
                    $qryopt .= " AND f.vTitle like '" . addslashes($txtSearch) . "%'";
                }//end else if
                else if ($cmbSearchType == "date") {
                    $qryopt .= " AND date_format(dPostDate,'%m/%d/%Y') like '" . addslashes($txtSearch) . "%'";
                }//end else if
                else if ($cmbSearchType == "user") {
                     $qryids = "select nUserId from " . TABLEPREFIX . "users where vLoginName like '%" . addslashes($txtSearch) . "%'";

                    $excqryids = mysqli_query($conn, $qryids) or die(mysqli_error($conn));
                    $idlists = "";
                    while (list($ids) = mysqli_fetch_array($excqryids)) {
                        if ($idlists == "")
                            $idlists = $ids;
                        else
                            $idlists=$idlists . "," . $ids;
                    }//end while loop
                    if ($idlists == "")
                        $idlists = 0;

                    $qryopt .= " AND f.nUserFBId in($idlists) ";
                }//end else if
            }//end if

            $targetfile = "";
            $detailfile = "";
            $targetfile = "feedbacks.php";
            $detailfile = "feedbackdesc.php";

            $sql = "select f.nFBId ,f.nUserId ,f.vTitle ,f.nUserFBId ,date_format(f.dPostDate,'%m/%d/%Y') as dPostDate,u.vLoginName,f.vStatus
				 from " . TABLEPREFIX . "userfeedback as f," . TABLEPREFIX . "users as u where f.nUserFBId=u.nUserId ";

            $sql .= $qryopt . " ORDER BY f.dPostDate DESC ";

            foreach ($_GET as $key => $value) {
                $$key = $value;
            }//end foreach loop
            if (!isset($begin) || $begin == "") {
                $begin = 0;
            }//end if

            $sess_back = $targetfile . "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;
            $_SESSION["backurl_feed"] = $sess_back;

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
            $sql_againt_user = "select ";


            $message = ($message != '') ? $message : $_SESSION['sessionMsg'];
            unset($_SESSION['sessionMsg']);

            if (isset($message) && $message != '') {
                ?>
                <tr bgcolor="#FFFFFF">
                    <td colspan="6" align="center" class="warning"><?php echo $message; ?></td>
                </tr>
            <?php }//end if?>
            <tr bgcolor="#FFFFFF"><input name="postback" type="hidden" id="postback">
            <input NAME="rf" TYPE="hidden" id="rf" VALUE="<?php echo  $var_rf ?>">
            <input NAME="no" TYPE="hidden" id="no" VALUE="<?php echo  $var_no ?>">
            <input name="uname" TYPE="hidden" id="uname" VALUE="<?php echo  htmlentities($var_uname) ?>">

            <td colspan="6" align="center"><table border="0" width="100%" class="maintext">
                    <tr>
                        <td valign="top" align="right">
                            Search
                            &nbsp; <select name="cmbSearchType" class="textbox2">

                                <option value="posteduser"  <?php if ($cmbSearchType == "posteduser" || $cmbSearchType == "") {
                                    echo("selected");
                                } ?>>User</option>
                                <option value="user"  <?php if ($cmbSearchType == "user" || $cmbSearchType == "") {
                                    echo("selected");
                                } ?>>PostedBy</option>
                                <option value="title" <?php if ($cmbSearchType == "title") {
                                    echo("selected");
                                } ?>>Title</option>
                                <option value="date" <?php if ($cmbSearchType == "date") {
                                    echo("selected");
                                } ?>>Date</option>
                            </select>

                            &nbsp;<input type="text" name="txtSearch" size="20" class="textbox2" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }">
                        </td>
                        <td align="left" valign="baseline">
                            <a href="javascript:document.frmSale.submit();" class="link_style2">Go</a>
                        </td>
                    </tr>
                </table></td>
            </tr>
            <tr align="center" bgcolor="#FFFFFF" class="gray">
                <td width="7%" align="center" valign="middle">Sl No. </td>
                <td width="16%" align="center" valign="middle">User</td>
                <td width="19%" align="center" valign="middle">Posted By</td>
                <td width="19%" align="center" valign="middle">Title</td>
                <td width="19%" align="center" valign="middle">Date</td>
                <td width="19%" align="center" valign="middle">Status</td>
            </tr>
            <?php
            if (mysqli_num_rows($rs) > 0) {
                $cnt = 1;
                if ($page == 1) {
                    $cnt = 1;
                    } else {
                $cnt = (($page - 1) * 10) + 1;
                }
                while ($arr = mysqli_fetch_array($rs)) {
                    $sql_against = "select  nUserId,vLoginName from " . TABLEPREFIX . "users where  nUserId=" . $arr['nUserId'];
                    $excqry = mysqli_query($conn, $sql_against) or die(mysqli_error($conn));
                    $a_user = mysqli_fetch_array($excqry);

                    //check status
                    switch ($arr['vStatus']) {
                        case "S":
                            $showStatus = 'Satisfied';
                            break;

                        case "D":
                            $showStatus = 'Dissatisfied';
                            break;

                        case "N":
                            $showStatus = 'Neutral';
                            break;
                    }//end switch
                    ?>
                    <tr bgcolor="#FFFFFF">
                        <td align="center" valign="middle"><?php echo $cnt; ?></td>
                        <td align="center" valign="middle"><?php echo '<a title="Click Here To View More Details." href="' . $detailfile . '?nFBid=' . $arr["nFBId"] . '&source=sa">' . htmlentities($a_user["vLoginName"]) . '</a>'; ?></td>
                        <td align="center" valign="middle"><?php echo '<a title="Click Here To View More Details." href="' . $detailfile . '?nFBid=' . $arr["nFBId"] . '&source=sa">' . htmlentities($arr["vLoginName"]) . '</a>'; ?></td>
                        <td align="center" valign="middle" class="maintext2"><?php echo '<a title="Click Here To View More Details." href="' . $detailfile . '?nFBid=' . $arr["nFBId"] . '&source=sa">' . htmlentities($arr["vTitle"]) . '</a>'; ?></td>
                        <td align="center" valign="middle"><?php echo '<a title="Click Here To View More Details." href="' . $detailfile . '?nFBid=' . $arr["nFBId"] . '&source=sa">' . date('m/d/Y', strtotime($arr["dPostDate"])) . '</a>'; ?></td>
                        <td align="center" valign="middle"><?php echo htmlentities($showStatus); ?></td>
                    </tr>
                    <?php
                    $cnt++;
                    mysqli_free_result($excqry);
                    $a_user = array();
                }//end while
            }//end if
            ?>
            <tr bgcolor="#FFFFFF">
                <td colspan="6" class="noborderbottm" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
                        <tr>
                            <td align="left"><?php echo($navigate[2]); ?></td>
                            <td align="right"><?php echo("Listing $navigate[1] of $totalrows results."); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </form>
    </table>
    <?php
    }
    //end function
    ?>