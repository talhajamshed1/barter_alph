<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: 			*/
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004-2008 ARMIA INC                                    |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts SocialWare                    |
// +----------------------------------------------------------------------+
// | Authors: simi<simi@armia.com>             		                      |
// |          										                      |
// +----------------------------------------------------------------------+
include_once('../includes/config.php');
include_once('../includes/adminsession.php');
include_once('../includes/functions.php');

$id =   $_GET['q'];
$i  =   0;

$langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                 WHERE lang_status = 'y'";
$langRs      = mysqli_query($conn, $langSql);

?>

<table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
    <?php
     $i=0;
        //fetch contents from metatags table

    $meta_c_sql   = "SELECT *
                      FROM ".TABLEPREFIX."metatags M
                      JOIN ".TABLEPREFIX."metatags_lang L
                        ON M.nId = L.meta_id
                       AND M.vPageName = '".$id."'
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
                            AND M.vPageName = '".$id."'
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