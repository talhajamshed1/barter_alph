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
$PGTITLE='account2';

//for changing status
if($_POST["postback"] == "CS") {
    $affid = $_POST["affid"];
    if($_POST["changeto"] == "A") {
        $newstatus = "0";
    }//end if
    else if($_POST["changeto"] == "D") {
        $newstatus = "1";
    }//end else if
    if(($_POST["changeto"] == "D") || ($_POST["changeto"] == "A")) {
        if(canAffBeDeactivated($affid)) {
            $sqlcs = "UPDATE ".TABLEPREFIX."affiliate   SET vDelStatus   = '$newstatus' where nAffiliateId  ='". addslashes($affid) ."'";
            mysqli_query($conn, $sqlcs);
        }//end if
        else {
            $message = "This affiliate cannot be deactivated since s/he has got some pending transactions.";
        }//end else
    }//end if
}//end if


$qryopt="";
if($_POST["txtSearch"] != "") {
    $txtSearch = $_POST["txtSearch"];
}//end if
else if($_GET["txtSearch"] != "") {
    $txtSearch = $_GET["txtSearch"];
}//end else if
if($_POST["ddlSearchType"] != "") {
    $ddlSearchType = $_POST["ddlSearchType"];
}//end if
else if($_GET["ddlSearchType"] != "") {
    $ddlSearchType = $_GET["ddlSearchType"];
}//end else if

if($txtSearch != "") {
    if($ddlSearchType == "firstname") {
        $qryopt .= "  WHERE vFirstName  like '" . addslashes($txtSearch) . "%'";
    }//end if
    else if($ddlSearchType == "username") {
        $qryopt .= "  WHERE vLoginName like '" . addslashes($txtSearch) . "%'";
    }//end else if
    else if($ddlSearchType == "lastname") {
        $qryopt .= "  WHERE vLastName like '" . addslashes($txtSearch) . "%'";
    }//end else if
    else if($ddlSearchType == "city") {
        $qryopt .= "  WHERE vCity  like '" . addslashes($txtSearch) . "%'";
    }//end else if
}//end if

if(DisplayLookUp('Enable Escrow')=='Yes')
{
	$SaleStatus='';
}//end if
else
{
	$SaleStatus=" OR sd.vSaleStatus ='4'";
}//en



$sql= "SELECT nUserId, vFirstName, vLoginName,vLastName, vCity FROM ".TABLEPREFIX."users   " . $qryopt . " where nUserId IN (SELECT nUserId FROM ".TABLEPREFIX."saledetails sd where sd.vSaleStatus ='2'  OR sd.vSaleStatus ='3' ".$SaleStatus.")"
        . " order by nUserId  DESC ";

if(!isset($begin) || $begin =="") {
    $begin = 0;
}//end if
$sess_back="useracdetails_other.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&ddlSearchType=" . $ddlSearchType . "&txtSearch=" . $txtSearch;

$_SESSION["backurl"] = $sess_back;
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,5,5,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

$sql = $sql.$navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<script LANGUAGE="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmAdminMain.submit();
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
                    <td width="100%" class="heading_admn boldtextblack" align="left">User Sales Account Summary</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                <tr>
                    <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#FFFFFF" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form  name="frmAdminMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>">
                                        <?php
                                        $message=($message!='')?$message:$_SESSION['sessionMsg'];
                                        unset($_SESSION['sessionMsg']);

                                        if(isset($message) && $message!='') {
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="5" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                            <?php  }//end if?>
                                            <tr align="right" bgcolor="#FFFFFF">
                                                <td colspan="5"><a href="accountsummary.php"><b>Back</b></a>
                                                
                                                 <input type="hidden" name="userid" value="">
                                            <input type="hidden" name="changeto" value="">
                                            <input type="hidden" name="postback" value="">
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                            <td colspan="5" align="left" width="100%">
                                               
                                                <table border="0" width="100%" class="maintext">
                                                    <tr>
                                                        <td valign="top" align="right" width="80%">
                                                            Search
                                                            &nbsp; <select name="ddlSearchType" class="textbox2">
                                                                <option value="username" <?php if($ddlSearchType== "username" || $ddlSearchType == "") {
                                                                    echo("selected");
                                                                    } ?>>Login Name</option>
                                                                <option value="firstname"  <?php if($ddlSearchType == "firstname" ) {
                                                                        echo("selected");
                                                                        } ?>>First Name</option>
                                                                <option value="lastname"  <?php if($ddlSearchType == "lastname" ) {
                                                                        echo("selected");
                                                                    } ?>>Last Name</option>
                                                                <option value="city" <?php if($ddlSearchType == "city") {
                                                                    echo("selected");
                                                                } ?>>City</option>
                                                            </select>
                                                            &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo htmlentities($txtSearch)?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                                        </td>
                                                        <td align="left" >
                                                            <a href="javascript:clickSearch();" class="link_style2">
                                                            Go</a>
                                                        </td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                </table> </td>
                                            </tr>  
                                            <tr align="left" bgcolor="#FFFFFF" class="gray">
                                                <td align="center" width="7%">Sl No. </td>
                                                <td width="16%" align="center">User Name</td>
                                                <td width="19%" align="center">First Name</td>
                                                <td width="19%" align="center">Last Name</td>
                                                <td width="19%" align="center">City</td>
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
                                                <td align="center" valign="middle"><?php echo $cnt;?></td>
                                                <td align="center" valign="middle" class="maintext"><?php echo '<a href="useracdetails_other.php?userid='.$arr["nUserId"].'&username='.urlencode($arr["vLoginName"]).'" title="Click here to view details">'.restrict_string_size($arr["vLoginName"],10).'</a>';?></td>
                                                <td align="center" valign="middle"><?php echo '<a href="useracdetails_other.php?userid='.$arr["nUserId"].'&username='.urlencode($arr["vLoginName"]).'" title="Click here to view details">'.restrict_string_size($arr["vFirstName"],10).'</a>';?></td>
                                                <td align="center" valign="middle"><?php echo '<a href="useracdetails_other.php?userid='.$arr["nUserId"].'&username='.urlencode($arr["vLoginName"]).'" title="Click here to view details">'.restrict_string_size($arr["vLastName"],10).'</a>';?></td>
                                                <td align="center" valign="middle"><?php echo '<a href="useracdetails_other.php?userid='.$arr["nUserId"].'&username='.urlencode($arr["vLoginName"]).'" title="Click here to view details">'.restrict_string_size($arr["vCity"],10).'</a>';?></td>
                                            </tr>
                                                    <?php
                                                    $cnt++;
                                                }//end while
                                            }//end if
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="5" class="noborderbottm" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
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