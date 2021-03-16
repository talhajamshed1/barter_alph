<?php
error_reporting(E_ALL);
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

if($_GET['mode']=='edit') {
    $mode='edit';
    $btnVal='Edit';
}//end if
else {
    $btnVal='Add';
}//end else


if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]!='') {
    
    if (function_exists('get_magic_quotes_gpc')) {
        $nHcId = addslashes($_POST['nHcId'] );
        $txtTitle = $_POST["txtTitle"];
        $chkType = addslashes($_POST["chkType"]);
        $radActive =  addslashes($_POST["radActive"]);
        $ddlCategory =  addslashes($_POST["ddlCategory"]);
    }//end if
    else {
        $nHcId = $_POST['nHcId'] ;
        $txtTitle = $_POST["txtTitle"];
        $chkType = $_POST["chkType"];
        $radActive =  $_POST["radActive"];
        $ddlCategory =  $_POST["ddlCategory"];
    }//end else

    if ($txtTitle[0] == "") {
        $message .= "* First category cannot be blank<br>";
        $error = true;
    } //end if

}//end if


if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Add Category' && !$error) {
    $sql = mysqli_query($conn, "SELECT MAX(nHcposition) as max from ".TABLEPREFIX."helpcategory") or die(mysqli_error($conn));
    $rw = mysqli_fetch_array($sql);
    $maxorder = $rw['max']+1;

    mysqli_query($conn, "insert into ".TABLEPREFIX."helpcategory (vHtype,nHcposition,vActive) values ('".addslashes($chkType)."',
						'".$maxorder."','".addslashes($radActive)."')") or die(mysqli_error($conn));
    $last_insert_id = mysqli_insert_id($conn);
    $i = 0;
    foreach($txtTitle as $title) {
        $language_id = $_POST["lang$i"];
        $title_sql = "INSERT INTO ".TABLEPREFIX."helpcategory_lang(help_cat_id,lang_id,vHctitle) VALUES (
                                        '".$last_insert_id."','".$language_id."','".addslashes($title)."')";
        $title_rs  = mysqli_query($conn, $title_sql);
        $i++;
    }
    header('location:help_category.php?msg=a&ddlCategory='.$chkType);
    exit();
}//end if

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Edit Category' && !$error) {    echo "<pre>";

    mysqli_query($conn, "update ".TABLEPREFIX."helpcategory set vActive='".addslashes($radActive)."',
						vHtype='".addslashes($chkType)."' WHERE nHcId='".$_POST['nHcId']."'") or die(mysqli_error($conn));

    $i = 0;
    foreach($txtTitle as $title) {
        $language_id = $_POST["lang$i"];
        $sel = "select * from ".TABLEPREFIX."helpcategory_lang WHERE help_cat_id ='".$_POST['nHcId']."' and lang_id = '".$language_id."' ";
        $srs = mysqli_query($conn, $sel);

        if(mysqli_num_rows($srs)){
        $title_sql = "UPDATE ".TABLEPREFIX."helpcategory_lang SET vHctitle = '".addslashes($title)."'
                          WHERE help_cat_id = '".$_POST['nHcId']."' AND lang_id = '".$language_id."'";
        $title_rs  = mysqli_query($conn, $title_sql);
        }else{
            $title_sql = "INSERT INTO ".TABLEPREFIX."helpcategory_lang(help_cat_id,lang_id,vHctitle) VALUES (
                                        '".$_POST['nHcId']."','".$language_id."','".addslashes($title)."')";
            $title_rs  = mysqli_query($conn, $title_sql);
        }
        $i++;
    }

    header('location:help_category.php?msg=e');
    exit();
}//end if

$langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                                                    WHERE lang_status = 'y'";
$langRs      = mysqli_query($conn, $langSql);

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<script language="javascript" type="text/javascript">
    function  validateCategory()
    {
        var s=document.frmCategory;
        if(s.txtTitle.value=='')
        {
            alert("Question can't be blank");
            s.txtTitle.focus();
            return false;
        }//end if
        if(s.txtDes.value=='')
        {
            alert("Description can't be blank");
            s.txtDes.focus();
            return false;
        }//end if
        return true;
    }//end function
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
                    <td width="100%" class="heading_admn boldtextblack" align="left"><?php echo $btnVal;?> Category</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">
                                        <form name="frmCategory" method ="POST" action = "" onsubmit="return validateCategory();">
                                            <?php if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                                <?php  }//end if?>
                                            <tr bgcolor="#FFFFFF">
                                            <input type="hidden" name="nHcId" value="<?php echo $_GET['nHcId'];?>">
                                            <input type="hidden" name="ddlCategory" value="<?php echo $_GET['ddlCategory'];?>">
                                            <td colspan="2" align="left" class="warning"> * indicates mandatory fields</td>
                                            </tr>
                                            <?php
                                            $i=0;
                                            while($langRow = mysqli_fetch_array($langRs)) {
                                                if($mode=="edit") {
                                                    $c_sql = "SELECT *
                                                    FROM " . TABLEPREFIX . "helpcategory_lang L
                                                    JOIN " . TABLEPREFIX . "helpcategory H
                                                      ON L.help_cat_id = H.nHcId
                                                     AND L.lang_id = '".$langRow["lang_id"]."'
                                                     AND L.help_cat_id = '".$_GET['nHcId']."'";
                                                    $rs_c =   mysqli_query($conn, $c_sql);
                                                    if(mysqli_num_rows($rs_c)>0) {
                                                        $txtTitleOld=mysqli_result($rs_c,0,'vHctitle');
                                                        $chkType=mysqli_result($rs_c,0,'vHtype');
                                                        $radActive=mysqli_result($rs_c,0,'vActive');
                                                    }//end if
                                                }
                                             if ($i==0){//only to execute for the first time
                                             ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Type <span class="warning">*</span></td>
                                                <td><input name="chkType" type="radio" value="client" <?php if($chkType=='client') {
                                                        echo 'checked';
                                                    }if

                                                    ($chkType=='') {
                                                               echo 'checked';
                                                    }?>>
						      Client <input type="radio" name="chkType" value="admin" <?php if($chkType=='admin') {
                                                                      echo 'checked';
                                                }?>>Admin</td>
                                            </tr>
                                            <?php } ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="31%" align="left"><?php echo ucwords($langRow["lang_name"])?> Category <span class="warning">*</span></td>
                                                <td width="69%"><input type="text" class="textbox2" name="txtTitle[]" size="70" value="<?php if($txtTitleOld){echo stripslashes($txtTitleOld);}else{echo stripslashes($txtTitle[$i]);}?>"/></td>
                                                <input type="hidden" name="lang<?php echo $i; ?>" value="<?php echo $langRow["lang_id"]?>" >
                                            </tr>
                                                <?php
                                                $i++;
                                             }?>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left"> Active </td>
                                                <td align="left"><input type="radio" name="radActive" value="1" <?php if($radActive=='1') {
                                                        echo 'checked';
                                                    }if

                                                                        ($radActive=='') {
                                                                            echo 'checked';
                                                                        }?>>Yes <input type="radio" name="radActive" value="0" <?php if($radActive=='0') {
    echo 'checked';
}?>>No
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td><input type="submit" name="btnSubmit" value="<?php echo $btnVal;?> Category" class="submit"/></td>
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
<?php include_once('../includes/footer_admin.php');?>