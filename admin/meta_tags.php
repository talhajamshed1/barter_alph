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
$PGTITLE='meta_tags';


$ShowContent=($_POST['ddlContent']!='')?$_POST['ddlContent']:'index.php';
$ddlContent=($_POST['ddlContent']!='')?$_POST['ddlContent']:'index.php';

//creating page array
$Rootpage=array();
$dirpath="../";
if($handledir=opendir($dirpath)) {
    while (false!==($file=readdir($handledir))) {
        if ($file!= "." && $file!= "..") {
            $currentstyleselected="";
            /*$stylefile="../".$file;
			$handle = fopen($stylefile, "r");
		  	$contents = fread($handle, filesize($stylefile));
		  	fclose($handle);
			if(strcmp($contents,$currentstyle)==0)
					$currentstyleselected="selected";*/
            $splitbydotarray=explode(".",$file);
            switch($splitbydotarray[1]) {
                case "php":
                    $Rootpage[$file]=$file;
                    break;
            }//end switch
        }//end if
    }//end while
}//end if
closedir($handledir);
//sort array
ksort($Rootpage);

if(isset($_POST['btnGo']) && $_POST['btnGo']=='Update') {

    $tilteArray     = $_POST["txtPName"];
    $kwdArray       = $_POST["txtDes"];
    $descArray      = $_POST["txtDes2"];
    $page           = $_POST["ddlContent"];

    $i=0;

    $check_sql = "SELECT * FROM ". TABLEPREFIX ."metatags WHERE vPageName = '".$page."'";
    $check_rs  = mysqli_query($conn, $check_sql);
    if(mysqli_num_rows($check_rs)<1){
        $insert_sql = "INSERT INTO ". TABLEPREFIX ."metatags(vPageName) VALUES ('".$page."')";
        $insert_rs  = mysqli_query($conn, $insert_sql);
        $last_insert_id = mysqli_insert_id($conn);
    }else{
        $check_rw = mysqli_fetch_array($check_rs);
        $last_insert_id = $check_rw['nId'];
    }

    foreach($tilteArray as $title) {
        $langid = $_POST["lang$i"];

           $sql = "SELECT *
                     FROM " . TABLEPREFIX . "metatags M
                     JOIN " . TABLEPREFIX . "metatags_lang L
                       ON M.nId = L.meta_id
                      AND M.vPageName = '".$page."'
                      AND L.lang_id = '".$langid."'";
            $rs   = mysqli_query($conn, $sql);
            $c_rw = mysqli_fetch_array($rs);

            if(mysqli_num_rows($rs)>0){
               $last_insert_id = $c_rw["meta_lang_id"];
               $updateSql = "UPDATE ". TABLEPREFIX . "metatags_lang
                                 SET vKeywords = '".addslashes($kwdArray[$i])."',
                                      vTitle = '".addslashes($title)."',
                                   vDescription = '".addslashes($descArray[$i])."'
                                 WHERE lang_id = '".$langid."'
                                 AND meta_lang_id = '".$c_rw["meta_lang_id"]."'";
               $updateRs  =  mysqli_query($conn, $updateSql);
               $msg='Contents Updated Successfully !!!!';
            }else{
              $sql = "SELECT *
                     FROM " . TABLEPREFIX . "metatags M WHERE
                      M.vPageName = '".$page."'";
            $rs   = mysqli_query($conn, $sql);
            $c_rw = mysqli_fetch_array($rs);
              $last_insert_id = $c_rw["nId"];
                $insertSql = "INSERT INTO ". TABLEPREFIX . "metatags_lang(meta_id,lang_id,vTitle,vKeywords,vDescription) VALUES (
                             '".$last_insert_id."','".$langid."','".addslashes($title)."','".addslashes($kwdArray[$i])."','".addslashes($descArray[$i])."')";//echo $insertSql.'hai';exit;
                $insertSql = mysqli_query($conn, $insertSql);
                $msg='New title added successfully.';
        }
        $i++;
    }

    $currentPage = $page;

}else{
    $currentPage = 'index.php';
}


$message=($_GET['msg']!='')?$_GET['msg']:$message;

/*if(isset($_GET['nId']) && $_GET['nId']!='') {
    //fetch contents
    $condition="where nId='".$_GET['nId']."'";
    $txtDes=fetchSingleValue(select_rows(TABLEPREFIX.'MetaTags','vKeywords',$condition),'vKeywords');
    $txtDes2=fetchSingleValue(select_rows(TABLEPREFIX.'MetaTags','vDescription',$condition),'vDescription');
    $txtPName=fetchSingleValue(select_rows(TABLEPREFIX.'MetaTags','vTitle',$condition),'vTitle');
    $ddlContent=fetchSingleValue(select_rows(TABLEPREFIX.'MetaTags','vPageName',$condition),'vPageName');
}//end if*/

$langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                 WHERE lang_status = 'y'";
$langRs      = mysqli_query($conn, $langSql);
?>
<script language="javascript" type="text/javascript" src="../js/metacontents.js"></script>
<!-- <script type="text/javascript" src="../js/floatingLayer.js"></script> -->
<script type="text/javascript" src="../js/dropDown.js"></script>
<style>
    td.top {
        background-color: #000080;
        text-align: right;
    }

    td.bottom {
        background-color: #ffe38c;
        padding: 15px;
    }
    #dhtmlgoodies_contentBox {
        border:1px solid #317082;
        height:0px;
        visibility:hidden;
        position:absolute;
        background-color:#E2EBED;
        overflow:hidden;
        padding:2px;
        width:450px;
    }

    #dhtmlgoodies_content {
        position:relative;
        font-family: Trebuchet MS, Lucida Sans Unicode, Arial, sans-serif;
        width:100%;
        font-size:0.8em;
    }

    #dhtmlgoodies_slidedown {
        position:relative;
        width:450px;
    }
</style>
<script language="javascript" type="text/javascript">
    function Validate()
    {
        var s=document.frmContentMgmt;
        if(trim(s.txtDes.value)=='')
        {
            alert("Keywords can't be blank");
            s.txtDes.focus();
            return false;
        }//end if
        if(trim(s.txtDes2.value)=='')
        {
            alert("Description can't be blank");
            s.txtDes2.focus();
            return false;
        }//end if
        return true;
    }//end fucntion

    //show title
    function ShowTitle(sName)
    {
        document.getElementById('showTitle').innerHTML=sName;
    }//end funciton
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
  
            <table width="100%"  border="0" cellspacing="0" cellpadding="10" >
                <tr>
                    <td align="left" class="heading_admn"><span class="boldtextblack">Meta Tags Management</span> - <span id="showTitle"></span></td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                <tr>
                    <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#FFFFFF" class="noborderbottm"><?php echo '<form name="frmContentMgmt" method ="POST" action="">';?>
                                    <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                    <?php if(isset($message) && $message!='') {
                                        ?>

                                        <tr bgcolor="#FFFFFF">
                                            <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                                        </tr>
                                         <?php  }//end if?>
                                        <tr bgcolor="#FFFFFF">
                                            <td width="25%" height="27" align="left" valign="top">Page Name</td>
                                            <td width="75%" align="left" valign="top"><?php echo select_tag('ddlContent','textboxadmin3','onChange="ShowTitle(this.value);showHint(this.value);"',$Rootpage,$currentPage);?></td>
                                        </tr>

                                    </table>
                                    <span id="showContent">
                                        <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                            <?php
                                             $i=0;
                                             //fetch contents from metatags table

                                            $meta_c_sql   = "SELECT *
                                                             FROM ".TABLEPREFIX."metatags M
                                                             JOIN ".TABLEPREFIX."metatags_lang L
                                                               ON M.nId = L.meta_id
                                                              AND M.vPageName = '".$currentPage."'
                                                              AND L.lang_id = '".$_SESSION["lang_id"]."'";
                                            $meta_c_rs    = mysqli_query($conn, $meta_c_sql);
                                            while($langRow = mysqli_fetch_array($langRs)) {

                                                $txtCnt     = mysqli_num_rows($meta_c_rs);
                                                if($txtCnt==0) {
                                                    $meta_sql   = "SELECT *
                                                                     FROM ".TABLEPREFIX."content C
                                                                     JOIN ".TABLEPREFIX."content_lang L
                                                                       ON C.content_id = L.content_id
                                                                      AND C.content_type =  ''
                                                                      AND L.lang_id = '".$langRow["lang_id"]."'";
                                                    $meta_sql_1   = $meta_sql."AND content_name = 'sitetitle'";
                                                    $meta_rs_1    = mysqli_query($conn, $meta_sql_1);
                                                    $meta_row_1   = mysqli_fetch_array($meta_rs_1);
                                                    $txtPName     = $meta_row_1["content"];

                                                    $meta_sql_2   = $meta_sql."AND content_name = 'Meta Keywords'";
                                                    $meta_rs_2    = mysqli_query($conn, $meta_sql_2);
                                                    $meta_row_2   = mysqli_fetch_array($meta_rs_2);
                                                    $txtDes       = $meta_row_2["content"];

                                                    $meta_sql_3   = $meta_sql."AND content_name = 'Meta Description'";
                                                    $meta_rs_3    = mysqli_query($conn, $meta_sql_3);
                                                    $meta_row_3   = mysqli_fetch_array($meta_rs_3);
                                                    $txtDes2      = $meta_row_3["content"];

                                                }else{
                                                    $meta_sql   = "SELECT *
                                                                     FROM ".TABLEPREFIX."metatags M
                                                                     JOIN ".TABLEPREFIX."metatags_lang L
                                                                       ON M.nId = L.meta_id
                                                                      AND M.vPageName = '".$currentPage."'
                                                                      AND L.lang_id = '".$langRow["lang_id"]."'";
                                                    $meta_rs    = mysqli_query($conn, $meta_sql);

                                                    $meta_row   = mysqli_fetch_array($meta_rs);
                                                    $txtPName   = $meta_row["vTitle"];
                                                    $txtDes     = $meta_row["vKeywords"];
                                                    $txtDes2    = $meta_row["vDescription"];
                                                }
                                                
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="25%" bgcolor="#FFFFFF" class="factlisting"><?php echo $langRow["lang_name"];?> Page Title </td>
                                                <td width="75%" class="factlisting"><input type="text" name="txtPName[]" class="textbox2" value="<?php echo $txtPName;?>" size="75"></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" valign="top" class="factlisting"><?php echo $langRow["lang_name"];?> Keywords</td>
                                                <td class="factlisting">
                                                    <textarea name="txtDes[]" cols="55" rows="6" class="textbox2"><?php echo htmlentities(stripslashes($txtDes));?></textarea>
                                                    <input type="hidden" name="lang<?php echo $i;?>" style="width:250px;" value="<?php echo $langRow["lang_id"]?>">
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" valign="top" class="factlisting"><?php echo $langRow["lang_name"];?> Description</td>
                                                <td class="factlisting"><textarea name="txtDes2[]" cols="55" rows="6" class="textbox2"><?php echo htmlentities(stripslashes($txtDes2));?></textarea></td>
                                            </tr>
                                            <?php
                                            $i++;
                                            }
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td class="factlisting">&nbsp;</td>
                                                <td class="factlisting"><input type="submit" name="btnGo" value="Update" onSubmit="return Validate();" class="submit"></td>
                                            </tr>
                                        </table>
                                    </span>
<?php echo '</form>';?>
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
<script type="text/javascript">
    setSlideDownSpeed(4);
</script>
<?php include_once('../includes/footer_admin.php');?>