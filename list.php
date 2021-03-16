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
include "./includes/config.php";
include "./includes/session.php";
include "./includes/functions.php";

$today = date('Y-m-d H:m:s');
echo "$today";

function get_cat_selectlist($current_cat_id, $count) {
    global $conn;
    static $option_results;
    if (!isset($current_cat_id)) {
        $current_cat_id = 0;
    }
    $count = $count + 1;
    $sql = "SELECT C.nCategoryId as id, L.vCategoryDesc as name from " . TABLEPREFIX . "category C
                LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                where C.nParentId = '$current_cat_id' order by name asc";

    $get_options = mysqli_query($conn, $sql);
    $num_options = mysqli_num_rows($get_options);
    if ($num_options > 0) {
        while (list($cat_id, $cat_name) = mysqli_fetch_row($get_options)) {
            if ($current_cat_id != 0) {
                $indent_flag = "&nbsp;&nbsp;";
                for ($x = 2; $x <= $count; $x++) {
                    $indent_flag .= "--&gt;&nbsp;";
                }
            }
            $cat_name = $indent_flag . $cat_name;
            $option_results[$cat_id] = $cat_name;
            get_cat_selectlist($cat_id, $count);
        }
    }
    return $option_results;
}
?>


<html>
    <head>
    </head>
    <body>
        <select name="cat_id">
            <option value=""><?php echo TEXT_SELECT_ONE; ?></option>

<?php
$get_options = get_cat_selectlist(0, 0);
if (count($get_options) > 0) {
    $categories = $_POST['cat_id'];
    foreach ($get_options as $key => $value) {
        $options .="<option value=\"$key\"";
        if ($_POST['cat_id'] == "$key") {
            $options .=" selected=\"selected\"";
        }
        $options .=">$value</option>\n";
    }
}
echo $options;
?>

        </select>
    </body>
</html>