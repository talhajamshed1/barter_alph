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
$PGTITLE='faq';

if($_GET['mode']=='edit') {
    $mode='edit';
    $btnVal='Edit';
}//end if
else {
    $btnVal='Add';
}//end else


if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]!='') {
    if (function_exists('get_magic_quotes_gpc')) {
        $txtQues    =   addslashes($_POST['txtQues']);
        $txtDes     =   addslashes($_POST['txtDes']);
        $radActive  =   addslashes($_POST['radActive']);
        $txtQues    =   $_POST['txtQues'];
        $txtDes     =   $_POST['txtDes'];
    }//end if
    else {
        $txtQues    =   $_POST['txtQues'];
        $txtDes     =   $_POST['txtDes'];
        $radActive  =   $_POST['radActive'];
    }//end else

   if ($txtQues[0] == "") {
        $message .= "* First question is mandatory<br>";
        $error = true;
    } //end if

    if ($txtDes[0] == "") {
        $message .= "* First description is mandatory<br>";
        $error = true;
    } //end if
}//end if


if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Add FAQ' && !$error) {

    $txtQues    =   $_POST['txtQues'];
    $txtDes     =   $_POST['txtDes'];
    $radActive  =   $_POST['radActive'];

    $sql=mysqli_query($conn, "select * from ".TABLEPREFIX."faq_lang where vTitle ='".addslashes($txtQues[0])."'") or die(mysqli_error($conn));
    if(mysqli_num_rows($sql)>0) {
        header('location:add_faq.php?msg=This title already exists!');
        exit();
    }//end if

    $sql = mysqli_query($conn, "SELECT MAX(nPosition) as max from ".TABLEPREFIX."faq") or die(mysqli_error($conn));
    $rw = mysqli_fetch_array($sql);

    $maxorder = $rw['max']+1;

    mysqli_query($conn, "insert into ".TABLEPREFIX."faq (vActive,nPosition) values ('".addslashes($radActive)."','".$maxorder."')") or die(mysqli_error($conn));

    $last_insert_id = mysqli_insert_id($conn);
    $k = 0;
    foreach($txtQues as $quest) {

        $langid        = $_POST["lang$k"];
        $description    = addslashes($txtDes[$k]);

        $sql_faq = "INSERT INTO ".TABLEPREFIX."faq_lang(faq_id,lang_id,vTitle,vDes) VALUES (
                    '".$last_insert_id."','".$langid."','".addslashes($quest)."','".$description."')";
        $rs      = mysqli_query($conn, $sql_faq);
        $k++;
    }
    header('location:faq.php?msg=a');
    exit();
}//end if

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Edit FAQ') {

    mysqli_query($conn, "update ".TABLEPREFIX."faq set vActive='".addslashes($radActive)."' where nFId='".$_POST['id']."'") or die(mysqli_error($conn));
    $k = 0;
    foreach($txtQues as $quest) {

        $langid        = $_POST["lang$k"];
        $description    = addslashes($txtDes[$k]);

        $sel_sql  = "SELECT * FROM ".TABLEPREFIX."faq_lang
                      WHERE faq_id = '".$_POST['id']."'
                        AND lang_id = '".$langid."'";
        $sel_num  = mysqli_num_rows(mysqli_query($conn, $sel_sql));
        if($sel_num>0) {
            $sql_faq = "UPDATE ".TABLEPREFIX."faq_lang SET vTitle='".addslashes($quest)."',vDes='".$description."'
                      WHERE faq_id = '".$_POST['id']."'
                        AND lang_id = '".$langid."'";
        }else {
            $sql_faq = "INSERT INTO ".TABLEPREFIX."faq_lang(faq_id,lang_id,vTitle,vDes) VALUES (
                    '".$_POST['id']."','".$langid."','".addslashes($quest)."','".$description."')";
        }
        $rs      = mysqli_query($conn, $sql_faq) or die(mysqli_error($conn));
        $k++;
    }
    header('location:faq.php?msg=e');
    exit();
}//end if


$langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                 WHERE lang_status = 'y'";
$langRs      = mysqli_query($conn, $langSql);


$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<script language="javascript" type="text/javascript">
    function  validateFaq()
    {
        var s=document.frmFaq;
        if(s.txtQues.value=='')
        {
            alert("Question can't be blank");
            s.txtQues.focus();
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
                <?php
                if($_GET["msg"]) {
                    ?>
                <tr>
                    <td colspan="2" style="text-align: center" class="warning"><?php echo $_GET["msg"];?></td>
                </tr>
                    <?php
                }
                ?>
                <tr>
                    <td width="100%" class="heading_admn boldtextblack" align="left"><?php echo $btnVal;?> FAQ</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">
                                        <form name="frmFaq" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateFaq();">
                                            <?php if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                                <?php  }//end if?>
                                            <tr bgcolor="#FFFFFF">
                                            <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
                                            <td colspan="2" align="left" class="warning"> * indicates mandatory fields</td>
                                            </tr>
                                            <?php

                                            $i=0;

                                            while($langRow = mysqli_fetch_array($langRs)) {
                                                if($mode=="edit") {
                                                    $sql_f = "SELECT *
                                                            FROM ".TABLEPREFIX."faq F
                                                            JOIN ".TABLEPREFIX."faq_lang L
                                                              ON F.nFId = L.faq_id
                                                             AND F.nFId = '".$_GET['id']."'
                                                             AND L.lang_id = '".$langRow["lang_id"]."'";
                                                    $rs_f =   mysqli_query($conn, $sql_f);
                                                    if(mysqli_num_rows($rs_f)>0) {
                                                        $txtQuesOld    =   mysqli_result($rs_f,0,'vTitle');
                                                        $txtDesOld     =   mysqli_result($rs_f,0,'vDes');
                                                        $radActive  =   mysqli_result($rs_f,0,'vActive');
                                                    }//end if
                                                }
                                                ?>

                                            <tr bgcolor="#FFFFFF">
                                                <td width="31%" align="left"><?php echo ucwords($langRow["lang_name"])?> Question </td>
                                                <td width="69%">
                                                    <input type="text" class="textbox" name="txtQues[]" size="70" value="<?php if($txtQuesOld){echo stripslashes($txtQuesOld);}else{echo stripslashes($txtQues[$i]);}?>"/>
                                                    <input type="hidden" name="lang<?php echo $i;?>" style="width:250px;" value="<?php echo $langRow["lang_id"]?>">
                                                </td>
                                            </tr>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left"><?php echo ucwords($langRow["lang_name"])?> Description </td>
                                                <td align="left"><textarea  class="textbox2"  name="txtDes[]" rows="10" cols="50" wrap><?php if($txtDesOld){echo stripslashes($txtDesOld);}else{echo stripslashes($txtDes[$i]);}?></textarea></td>
                                            </tr>
                                                <?php
                                                $txtQues = '';
                                                $txtDes = '';
                                                $i++;
                                            }
                                            ?>

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
                                                <td><input type="submit" name="btnSubmit" value="<?php echo $btnVal;?> FAQ" class="submit"/></td>
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