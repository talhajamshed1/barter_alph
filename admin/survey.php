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
$PGTITLE='survey';
?>
<script language="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmSurvey.submit();
    }
</script>
<div class="row admin_wrapper">
	<div class="admin_container">

 
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="18%" valign="top"> <!--  Admin menu comes here -->
            <?php require("../includes/adminmenu.php"); ?>
            <!--   Admin menu  comes here ahead --></td>
            <td width="4%"></td>
        <td width="78%" valign="top">
           
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td width="100%" class="heading_admn boldtextblack" align="left">Referral Payments</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                <tr>
                    <td align="left" valign="top" class="noborderbottm" ><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frmSurvey" id="frmSurvey" ACTION="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                                            <?php
                                            $txtSearch="";
                                            $cmbSearchType="";
                                            $var_rf="";
                                            $var_no="";

                                            if($_GET["txtSearch"] != "") {
                                                $txtSearch = $_GET["txtSearch"];
                                                $cmbSearchType =  $_GET["cmbSearchType"];
                                            }//end if
                                            else if($_POST["txtSearch"] != "") {
                                                $txtSearch = $_POST["txtSearch"];
                                                $cmbSearchType =  $_POST["cmbSearchType"];
                                            }//end else if

                                            $qryopt="";
                                            if($txtSearch != "") {
                                                if($cmbSearchType == "user") {
                                                    $qryopt .= "  AND U.vLoginName like '" . addslashes($txtSearch) . "%'";
                                                }//end if
                                            }//end if

                                            $sql = "Select UR.nUserId,"
                                                    . "UR.nSurveyCount,"
                                                    . "UR.nRegCount,"
                                                    . "UR.nSurveyAmount,"
                                                    . "UR.nRegAmount,"
                                                    . "UR.nSurveyPaid,"
                                                    . "UR.nRegPaid,U.vLoginName FROM "
                                                    . " `".TABLEPREFIX."user_referral` UR INNER JOIN ".TABLEPREFIX."users U ON UR.nUserId = U.nUserId";                                          
                                            $targetfile="";
                                            $detailfile="";
                                            $detailfile="referraldetails.php";
                                            $sql .= $qryopt . "  ORDER BY U.vLoginName DESC ";

                                            $sess_back= $targetfile .  "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;

//get the total amount of rows returned
                                            $totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

                                            /*
Call the function:

I've used the global $_GET array as an example for people
running php with register_globals turned 'off' :)
                                            */

                                            $navigate = pageBrowser($totalrows,10,10,"&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
                                            $sql = $sql.$navigate[0];
                                            
                                            $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                                            $message=($message!='')?$message:$_SESSION['sessionMsg'];
                                            unset($_SESSION['sessionMsg']);

                                            if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="6" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                                <?php  }//end if?>

                                            <tr bgcolor="#FFFFFF"><input name="postback" type="hidden" id="postback">
                                            <td colspan="6" align="center"><table border="0" width="100%" class="maintext">
                                                    <tr>
                                                        <td valign="top" align="right">
                                                            Search
                                                            &nbsp; <select name="cmbSearchType" class="textbox2">
                                                                <option value="user"  <?php if($cmbSearchType == "user" || $cmbSearchType == "") {
                                                                        echo("selected");
                                                                    } ?>>User Name
                                                                </option>
                                                            </select>
                                                            &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                                        </td>
                                                        <td align="left" valign="baseline">
                                                            <a href="javascript:document.forms['frmSurvey'].submit();" class="link_style2">Go</a>
                                                        </td>
                                                    </tr>
                                                </table></td>
                                            </tr>
                                            <tr align="center" bgcolor="#FFFFFF" class="gray">
                                                <td width="7%">Sl No. </td>
                                                <td width="16%" align="center" valign="middle">User Name</td>
                                                <td width="16%" align="center" valign="middle">Referrals added</td>
                                                <td width="16%" align="center" valign="middle">Survey Pending</td>
                                                <td width="15%" align="center" valign="middle">Reg Pending</td>
                                                <td width="15%" align="center" valign="middle">Survey Paid</td>
                                                <td width="15%" align="center" valign="middle">Reg Paid</td>
                                            </tr>
                                        <?php
                                        if(mysqli_num_rows($rs)>0) {
                                            switch($_GET['begin']) {
                                                    case "":
                                                        $cnt=1;
                                                        break;

                                                    default:
                                                        $cnt=$_GET['begin']+1;
                                                        break;
                                                }//end switch

                                                while ($arr = mysqli_fetch_array($rs)) {
                                                    ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="center"><?php echo $cnt;?></td>
                                                <td align="center" valign="middle" class="maintext"><?php echo '<a href="'.$detailfile.'?user='.$arr["nUserId"].'&name='.urlencode($arr["vLoginName"]).'&" title="Click Here to View/Settle Amount">'.htmlentities(ucfirst($arr["vLoginName"])).'</a>';?></td>
                                                <td align="center" valign="middle"><?php echo '<a href="'.$detailfile.'?user='.$arr["nUserId"].'&name='.urlencode($arr["vLoginName"]).'&" title="Click Here to View/Settle Amount">'.$arr["nRegCount"].'</a>';?></td>
                                                <td align="center" valign="middle"><?php echo '<a href="'.$detailfile.'?user='.$arr["nUserId"].'&name='.urlencode($arr["vLoginName"]).'&" title="Click Here to View/Settle Amount">'.CURRENCY_CODE.$arr["nSurveyAmount"].'</a>';?></td>
                                                <td align="center" valign="middle"><?php echo '<a href="'.$detailfile.'?user='.$arr["nUserId"].'&name='.urlencode($arr["vLoginName"]).'&" title="Click Here to View/Settle Amount">'.CURRENCY_CODE.$arr["nRegAmount"].'</a>';?></td>
                                                <td align="center" valign="middle"><?php echo '<a href="'.$detailfile.'?user='.$arr["nUserId"].'&name='.urlencode($arr["vLoginName"]).'&" title="Click Here to View/Settle Amount">'.CURRENCY_CODE.$arr["nSurveyPaid"].'</a>';?></td>
                                                <td align="center" valign="middle"><?php echo '<a href="'.$detailfile.'?user='.$arr["nUserId"].'&name='.urlencode($arr["vLoginName"]).'&" title="Click Here to View/Settle Amount">'.CURRENCY_CODE.$arr["nRegPaid"].'</a>';?></td>
                                            </tr>
                                                    <?php
                                                    $cnt++;
                                                }//end while
                                            }//end if
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="6" align="left" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
                                                        <tr>
                                                            <td align="left"><?php echo($navigate[2]);?></td>
                                                            <td align="right"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
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
        </td>
    </tr>
</table>
</div>
</div>
<?php include_once('../includes/footer_admin.php');?>
