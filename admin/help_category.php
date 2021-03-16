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
$PGTITLE='help_category';

//delete
if(isset($_GET['mode']) && $_GET['mode']=='delete') {
    $nHcId = $_GET['nHcId'];
    //check if it in use
    $check_category=mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."help WHERE nHcId='".$nHcId."'") or die (mysqli_error($conn));
    if(mysqli_num_rows($check_category)>0) {
        header('location:help_category.php?msg=dn&ddlCategory='.$_GET['ddlCategory']);
        exit();
    }//end if
    else {
        $sqlDel = "DELETE FROM ".TABLEPREFIX."helpcategory WHERE nHcId='".$nHcId."'";
        mysqli_query($conn, $sqlDel) or die(mysqli_error($conn));
        mysqli_query($conn, "DELETE FROM ".TABLEPREFIX."helpcategory_lang WHERE help_cat_id='".$nHcId."'");

        header('location:help_category.php?msg=d&ddlCategory='.$_GET['ddlCategory']);
        exit();
    }//end else
}//end if

//ordering content
if(isset($_GET['Action']) && $_GET['Action']=='ordering') {
    $oldId=$_GET['id'];
    $oldPosition=$_GET['pos'];
    $table=TABLEPREFIX."helpcategory";
    $PositionfieldName='nHcposition'; //db field name
    $IdfieldName='nHcId'; //db field name
    $returnPath='location:help_category.php';

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
            sortableDb("Helpcategory", $oldId, $PositionfieldName, $IdfieldName, $_GET['move']);
     }
}//end if
?>
<script language="javascript" type="text/javascript">
    function Validate()
    {
        var s=document.frmHelp;
        if(s.txtTitle.value=='')
        {
            alert("Title can't be blank");
            s.txtTitle.focus();
            return false;
        }//end if
        return true;
    }//end if
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
                    <td width="74%" class="heading_admn boldtextblack" align="left">Help Category </td>
                    <td width="26%" align="right"  class="heading_admn"><a href="add_help_category.php<?php if($_POST['ddlCategory']!='') {
                                    echo '?ddlCategory='.$_POST['ddlCategory'];
                                }?>" class="AddLinks">Add New Category</a></td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top">
                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">
                                        <form name="frmHelp" id="frmHelp" ACTION="<?php echo $_SERVER['PHP_SELF']?>" method="post" onSubmit="return Validate();">
                                            <?php
                                            //selected language for filtering
                                            $ddlCategory=($_POST['ddlCategory']!='')?$_POST['ddlCategory']:$_GET['ddlCategory'];

                                            //add this link to ordering
                                            if(isset($ddlCategory) && $ddlCategory!='') {
                                                $order_category='&ddlCategory='.$ddlCategory;
                                            }//end if

                                            if(isset($ddlCategory)) {
                                                if($ddlCategory!='All') {
                                                    //requested language category listing
                                                    $sql = "SELECT *
                                                              FROM ".TABLEPREFIX."helpcategory H
                                                              JOIN ".TABLEPREFIX."helpcategory_lang L
                                                                ON H.nHcId = L.help_cat_id
                                                               AND L.lang_id = '".$_SESSION["lang_id"]."'
                                                               AND H.vHtype = '".$ddlCategory."'
                                                          ORDER BY H.nHcposition DESC";
                                                }//end if
                                                else {
                                                    //all category listing
                                                    $sql = "SELECT *
                                                              FROM ".TABLEPREFIX."helpcategory H
                                                              JOIN ".TABLEPREFIX."helpcategory_lang L
                                                                ON H.nHcId = L.help_cat_id
                                                               AND L.lang_id = '".$_SESSION["lang_id"]."'
                                                               AND H.vHtype = '".$ddlCategory."'
                                                          ORDER BY H.nHcposition DESC";
                                                }//end else
                                            }//end if
                                            else {
                                                //all category listing
                                                $sql = "SELECT *
                                                          FROM ".TABLEPREFIX."helpcategory H
                                                          JOIN ".TABLEPREFIX."helpcategory_lang L
                                                            ON H.nHcId = L.help_cat_id
                                                           AND L.lang_id = '".$_SESSION["lang_id"]."'                                                          
                                                      ORDER BY H.nHcposition ASC";
                                            }//end else

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
                                                    $message='Help category add successfully.';
                                                    break;

                                                case "e":
                                                    $message='Help category updated successfully.';
                                                    break;

                                                case "d":
                                                    $message='Help category deleted successfully.';
                                                    break;

                                                case "dn":
                                                    $message='This Help category cannot be deleted since it is in use!';
                                                    break;

                                                default:
                                                    $message='';
                                                    break;

                                            }//end if
                                            ?>

                                            <tr align="left" bgcolor="#FFFFFF">
                                                <td colspan="6"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
                                                        <tr>
                                                            <td align="right">Filter By Category :
                                                                <select name="ddlCategory" class="textbox2" onChange="document.frmHelp.submit();">
                                                                    <option value="All">All</option>
                                                                    <?php
                                                                    //fectch language from music category table
                                                                    $language=mysqli_query($conn, "select distinct(vHtype) from ".TABLEPREFIX."helpcategory order by vHtype") or die(mysqli_error($conn));
                                                                    if(mysqli_num_rows($language)>0) {
                                                                        while($arr=mysqli_fetch_array($language)) {?>
                                                                        <option value="<?php echo $arr['vHtype'];?>" <?php if($_POST['ddlCategory']==$arr['vHtype']) {
                                                                            echo 'selected';
                                                                        }else if($_GET['ddlCategory']==$arr['vHtype']) {
                                                                            echo 'selected';
                                                                        }?>><?php echo ucfirst($arr['vHtype']);?>
                                                                        </option>
                                                                    <?php
                                                                            }//end while
                                                                        }//end if
                                                                        else {
                                                                            echo '<option value="Nil">NIL</option>';
                                                                        }//end if?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                                <?php

                                                if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="6" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                            <?php  }//end if?>
                                            <tr align="center" bgcolor="#FFFFFF" class="gray">
                                                <td width="8%" align="center">Sl No. </td>
                                                <td width="15%" align="center">Title</td>
                                                <td width="15%" align="center">Type</td>
                                                <td width="15%" align="center">Set Order </td>
                                                <td width="15%" align="center">Active</td>
                                                <td width="15%" align="center">Action</td>
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

                                                $count=mysqli_num_rows($rs);
                                                while ($arr = mysqli_fetch_array($rs)) {
                                                    ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="center"><?php echo $cnt;?></td>
                                                <td align="center" class="maintext"><?php echo htmlentities($arr["vHctitle"]);?></td>
                                                <td align="center" class="maintext"><?php echo htmlentities($arr["vHtype"]);?></td>
                                                <td align="center" class="maintext"><?php if($cnt!=1) {?>
                                                    <a href='help_category.php?Action=ordering&move=up&id=<?php echo $arr["nHcId"];?>&pos=<?php echo $arr["nHcposition"];?><?php echo $order_category;?>'>
                                                        
                                                        <img src="../images/up.gif" alt="Up" border=0></a><?php } ?>

                                                    <?php if($cnt!=$totalrows) {?>
                                                    <a href="help_category.php?Action=ordering&move=down&id=<?php echo $arr["nHcId"];?>&pos=<?php echo $arr["nHcposition"];?><?php echo $order_category;?>">
                                                    
                                                        <img src="../images/down.gif" alt="Down" border=0></a><?php } ?></td>
                                                <td align="center"><?php if($arr['vActive']=='1') {
                                                        echo 'Yes';
                                                    }else {
                                                        echo 'No';
                                                    }?>
                                                </td>
                                                <td align="center"><?php echo "<a href='add_help_category.php?nHcId=".$arr["nHcId"]."&mode=edit".$order_category."'>Edit</a>";?>&nbsp;|&nbsp;<?php echo "<a href='help_category.php?nHcId=".$arr["nHcId"]."&mode=delete".$order_category."' onClick='javascript:return confirm(\"Are you sure you want to delete this?\");'>Delete</a>";?></td>
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