<?php
error_reporting(E_ALL ^ E_NOTICE);
//if ($_POST['convert_spreadsheet_format']=='yes'){
    if (file_exists($_REQUEST['file_name']) && $_REQUEST['file_name']!=''){
        //file_get_contents($_POST['file_name']);
        include($_REQUEST['file_name']);
        $constant_arr = get_defined_constants(true);
        $contents = "<table>";
        foreach ($constant_arr['user'] as $label => $text){
            $text = str_replace('<br>','##BR##',stripslashes($text));
            $text = str_replace('{','[',$text);
            $text = str_replace('}',']',$text);
            $contents .= "<tr><!--<td>".$label."</td>--><td nowrap='nowrap'>".$text."</td></tr>";
        }
        $contents .= "</table>";
    }
    echo $contents;
//}
?>
<form name="frm" method="post">
    <input type="hidden" name="convert_spreadsheet_format" value="yes" />
    File Path <input type="text" name="file_name" value="" />
    <input type="submit" name="submit_button" value="Convert" />
</form>