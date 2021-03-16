<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: 			*/
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004-2008 Armia Systems, Inc                                    |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts SocialWare                    |
// +----------------------------------------------------------------------+
// | Authors: simi<simi@armia.com>             		                      |
// |          										                      |
// +----------------------------------------------------------------------+
include_once('../includes/config.php');
include_once('../includes/adminsession.php');
include_once('../includes/functions.php');

$txtDes=DisplayLookUp($_GET['q']);

$defaultcontent = $_GET['q'];

$langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                 WHERE lang_status = 'y'";
$langRs      = mysqli_query($conn, $langSql);

?>
<table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
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
 