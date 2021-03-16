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
$PGTITLE='sliders';

//delete 
if(isset($_GET['mode']) && $_GET['mode']=='delete') {
    $sliderid=$_GET['id'];

    //remove deleted slider image from the sliders folder
    @unlink("../sliders/".$_GET['imgname']);

    mysqli_query($conn, "delete from ".TABLEPREFIX."sliders where nSId='".$_GET['id']."'") or die(mysqli_error($conn));
    //mysqli_query($conn, "delete from ".TABLEPREFIX."banners_lang where slider_id='".$_GET['id']."'") or die(mysqli_error($conn));

    header('location:sliders.php?msg=d');
    exit();
}//end if

//ordering content
if(isset($_GET['Action']) && $_GET['Action']=='ordering') {
    $oldId=$_GET['id'];
    $oldPosition=$_GET['pos'];
    $table=TABLEPREFIX."sliders";
    $PositionfieldName='nPosition'; //db field name
    $IdfieldName='nSId'; //db field name
    $returnPath='location:sliders.php';

    /*
    if(isset($_GET['move']) && $_GET['move']=='up') {
        OrderUp($table,$oldId,$oldPosition,$PositionfieldName,$IdfieldName,$returnPath);
        listing();
    }//end if

    if(isset($_GET['move']) && $_GET['move']=='down') {
        OrderDown($table,$oldId,$oldPosition,$PositionfieldName,$IdfieldName,$returnPath);
        listing();
    }//end if
     */
    if(isset($_GET['move'])) {
        sortableDb("sliders", $oldId, $PositionfieldName, $IdfieldName, $_GET['move']);
    }
    
}//end if
?>

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
                    <td width="100%" class="heading_admn boldtextblack" align="left">Sliders</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellpadding="5" cellspacing="1" class="">
                            <tr>
                                <td align="right" height="46"><a href="add_sliders.php" class="AddLinks"><b>Add Sliders</b></a></td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2">
                            <tr>
                                <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frmSliders" id="frmSliders" ACTION="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                                            <?php
                                           $sql = "SELECT *
                                                      FROM ".TABLEPREFIX."sliders ORDER BY nDate DESC";

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

                                            switch($_GET['msg']) {
                                                case "a":
                                                    $message='Slider added successfully.';
                                                    break;

                                                case "e":
                                                    $message='Slider updated successfully.';
                                                    break;

                                                case "d":
                                                    $message='Slider deleted successfully.';
                                                    break;

                                                default:
                                                    $message='';
                                                    break;

                                            }//end if
                                            ?>
                                            <?php
                                            if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="7" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                                <?php  }//end if?>
                                            <tr align="center" bgcolor="#FFFFFF" class="gray">
                                                <td width="8%">Sl No. </td>
                                                <td width="15%">Title</td>
                                                <td width="15%">Sliders</td>
                                                <td width="15%">Create Date </td>
                                                <td width="15%">Active</td>
                                                <td width="15%">Action</td>
                                            </tr>
                                                <?php
                                                if(mysqli_num_rows($rs)>0) {
                                                    $count=mysqli_num_rows($rs);
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
                                                <td class="maintext"><?php echo htmlentities($arr["vName"]);?></td>
                                                <td class="maintext" align="center"><?php if($arr["vImg"]!='') {
                                                        echo '<a  class="thumbnail" style="cursor:pointer;"><b>Mouse over to view slider</b>
                                                                <span><img src="../sliders/'.$arr["vImg"].'" width="468" height="60" border="0"/></span>
                                                             </a>';
                                                    }//end if
                                                    else {
                                                        echo ' <img src="images/default_album.gif" name="img1" width="70" height="70" border="0">';
                                                 }//end else?>
                                                </td>
                                                <td class="maintext"><?php echo date('F d, Y',strtotime($arr['nDate']));?></td>
                                                <td align="center"><?php if($arr['vActive']=='1') {
                                                                echo 'Yes';
                                                            }else {
                                                                echo 'No';
                                                                }?></td>
                                                <td align="center"><?php echo "<a href='add_sliders.php?id=".$arr["nSId"]."&mode=edit'><span class='glyphicon glyphicon-edit'></span></a>";?>&nbsp; &nbsp;<?php echo "<a href='sliders.php?id=".$arr["nSId"]."&langId=".$arr["lang_id"]."&mode=delete&imgname=".$arr["vImg"]."' onClick=\"javascript:return confirm('Are you sure you want to delete this slider?')\">
												<span class='glyphicon glyphicon-trash'></span>
												</a>";?></td>
                                            </tr>
                                                            <?php
                                                                $cnt++;
                                                            }//end while
                                            }//end if
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="7" align="left" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
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
</table>

</div>
</div>
<?php include_once('../includes/footer_admin.php');?>