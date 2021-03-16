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
$PGTITLE='email_contents';

$defaultcontent = 15;

if(isset($_POST["btnSubmit"])) {
    $tilteArray     = $_POST["title"];
    $content_id     = $_POST["content_type"];
    $i=0;
   
    foreach($tilteArray as $title) {
        
        $langid = $_POST["lang$i"];
        $content = $_POST["content_$i"];
        $sql = "SELECT C.content_id,L.content_lang_id 
                 FROM " . TABLEPREFIX . "content C
                 JOIN " . TABLEPREFIX . "content_lang L
                   ON C.content_id = L.content_id
                  AND C.content_type = 'email'
                  AND C.content_id = '".$content_id."'
                  AND L.lang_id = '".$langid."'";
        $rs   = mysqli_query($conn, $sql);
        $c_rw = mysqli_fetch_array($rs);
        
        if(mysqli_num_rows($rs)>0) {
             $updateSql = "UPDATE ". TABLEPREFIX . "content_lang
                             SET content = '".addslashes($content)."',
                                 content_title = '".addslashes($title)."'
                           WHERE content_id = '".$content_id."'
                             AND lang_id = '".$langid."'
                             AND content_lang_id = '".$c_rw["content_lang_id"]."'";
           $updateRs  =  mysqli_query($conn, $updateSql);
        }else {
           $insertSql = "INSERT INTO ". TABLEPREFIX . "content_lang(content_id,lang_id,content,content_title) VALUES (
                         '".$content_id."','".$langid."','".addslashes($content)."','".addslashes($title)."')";
           
            $insertSql = mysqli_query($conn, $insertSql);
        }
        
        $i++;
    }
    $defaultcontent = $content_id;
    $message="Settings updated";
}//end if

$langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                 WHERE lang_status = 'y'";
$langRs      = mysqli_query($conn, $langSql);


$contSql     = "SELECT content_id,content_name FROM " . TABLEPREFIX . "content
                 WHERE content_status = 'y'
                   AND content_type = 'email'";
$contRs      = mysqli_query($conn, $contSql);



?>
<?php include "editor.php";//editor ?>
<script language="javascript" type="text/javascript">
function updateValue()
{
    updateRTEs();

}//end function
</script>
<script language="javascript" type="text/javascript" src="../js/contents.js"></script>
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
                    <td width="100%" class="heading_admn" align="left"><span class="boldtextblack">Edit Email Contents</span></td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#EEEEEE">
                                    <form name="frmSettings" method="POST" action="<?php echo $_SERVER["PHP_SELF"]?>" onsubmit="return updateValue();">
                                        <table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">
                                            <?php if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="center" class="success"><?php echo $message;?></td>
                                            </tr>
                                            <?php  }//end if?>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="15%" align="left" valign="top">Select a content</td>
                                                <td width="85%" align="left" valign="top">
                                                    <select name="content_type" id="content_type" class="textbox2" onchange="getContent(this.value)">
                                                    <?php
                                                    $k = 0;
                                                    if($_POST["cid"]!=""){
                                                    $defaultcontent = $_POST["cid"];
                                                    }
                                                    while ($controw = mysqli_fetch_array($contRs)) {
                                                            if($controw['content_id'] == $defaultcontent){
                                                                $selected = "selected";
                                                            }else{
                                                                $selected = "";
                                                            }
                                                    ?>
                                                        <option value="<?php echo $controw['content_id'];?>" <?php echo $selected;?>>
                                                        <?php echo $controw['content_name'];?>
                                                        </option>
                                                        <?php
                                                        $k++;
                                                        }
                                                        ?>
                                                    </select>
                                                   
                                                </td>
                                            </tr>
                                            <?php

                                            $i=0;
                                             
                                            while($langRow = mysqli_fetch_array($langRs)) {
                                                
                                                   $defaultCont = "SELECT C.content_id,L.content_lang_id,C.content_name,L.content_title,L.content
                                                                     FROM " . TABLEPREFIX . "content C
                                                                     JOIN " . TABLEPREFIX . "content_lang L
                                                                       ON C.content_id = L.content_id
                                                                      AND C.content_type = 'email'
                                                                      AND C.content_id = '".$defaultcontent."'
                                                                      AND L.lang_id = '".$langRow["lang_id"]."'";
                                                   $default_rs  = mysqli_query($conn, $defaultCont);
                                                   $default_row = mysqli_fetch_array($default_rs);                                               
                                                   
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="15%" align="left" valign="top"><?php echo ucwords($langRow["lang_name"])?> Subject </td>
                                                <td>
                                                    <input class="textbox2" type="text" name="title[]" id="title" style="width:250px;" value="<?php echo $default_row["content_title"]?>">
                                                    <input type="hidden" name="lang<?php echo $i;?>" style="width:250px;" value="<?php echo $langRow["lang_id"]?>">
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="15%" align="left" valign="top"><?php echo ucwords($langRow["lang_name"])?> Content </td>
                                                <td>
                                                    <!-- <textarea type="text" name="content[]" id="title" class="textbox2" cols="65" rows="10"><?php echo $default_row["content"]?></textarea> -->
                                                    <script language="JavaScript" type="text/javascript">
                                                        var rte1 = new richTextEditor('content_<?php echo $i;?>');
                                                        rte1.html = '<?php echo rteSafe($default_row["content"]); ?>';
                                                        rte1.width = 600;
                                                        rte1.height = 200;
                                                        //rte1.toolbar1 = false;
                                                        //rte1.toolbar2 = false;
                                                        //rte1.toggleSrc = false;
                                                        rte1.build();
                                                    </script>
                                                </td>
                                            </tr>
                                            <?php
                                                $i++;
                                            }
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td><input type="hidden" name="cid" id="cid"></td>
                                                <td>
                                                    <input type="submit" name="btnSubmit" value="Submit" class="submit">
                                                </td>
                                            </tr>

                                        </table>
                                    </form>
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