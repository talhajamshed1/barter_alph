<?php
error_reporting(E_ALL ^ E_NOTICE);
if ($_POST['convert_spreadsheet_format']=='yes'){
    if (file_exists($_POST['file_name']) && $_POST['file_name']!='' && $_POST['translated_text']!=''){
        include($_POST['file_name']);
        $constant_arr = get_defined_constants(true);
        
        $translated_text = (stripslashes($_POST['translated_text']));//utf8_encode
        $text_arr = explode("\n",$translated_text);
        
        $contents = "<table>";
        $i = 0;
        foreach ($constant_arr['user'] as $label => $text){
            /*while (trim($text_arr[$i])==''){
                $i++;
            }*/
            $text = str_ireplace('# # BR # #','<br>',addslashes($text_arr[$i]));
            $text = str_ireplace('# # # # BR','<br>',$text);
            $text = str_ireplace('BR # # # #','<br>',$text);
            $text = str_replace('[','{',$text);
            $text = str_replace(']','}',$text);
            $contents .= "<tr><td>define(\"".$label."\",'".$text."');</td></tr>";
            $i++;
        }
        $contents .= "</table>";
    }
    //echo $contents;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
    <?php echo $contents; ?>
<form name="frm" method="post">
    <input type="hidden" name="convert_spreadsheet_format" value="yes" />
    File Path to put match <input type="text" name="file_name" value="" /><br />
    Converted Text <textarea name="translated_text" rows="20" cols="80"><?php echo stripslashes($translated_text); ?></textarea>
    <input type="submit" name="submit_button" value="Convert" />
</form>
</body>
</html>