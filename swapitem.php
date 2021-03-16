<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/session_check.php");
include ("./includes/httprefer_check.php");
include_once('./includes/gpc_map.php');

if(strlen($_SERVER["QUERY_STRING"])<2 && !$_POST){
    header("Location:usermain.php");
    exit;
}
//echo "<pre>";var_dump($_POST);exit;
$commlmt = 0;
$commlmt = DisplayLookUp('8');
$MaxUploadSize = ini_get('upload_max_filesize');
//list($MaxUploadSize, $ext) = split('M', $MaxUploadSize);
list($MaxUploadSize, $ext) = explode('M', $MaxUploadSize);
//$MaxUploadSize = $MaxUploadSize * 1024;

if ($_GET['source'] == 'sa') {
    include ("./includes/enable_module.php");
}//end if
//function to display categories in nested manner
function make_selectlist($current_cat_id, $count) {
    global $conn;
    static $option_results;
    if (!isset($current_cat_id)) {
        $current_cat_id = 0;
    }//end if

    $count = $count + 1;
    $sql = "SELECT C.nCategoryId as id, L.vCategoryDesc as name from " . TABLEPREFIX . "category C 
                    LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                where C.nParentId = '$current_cat_id' order by name asc";
    $get_options = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $num_options = mysqli_num_rows($get_options);
    if ($num_options > 0) {
        while (list($cat_id, $cat_name) = mysqli_fetch_row($get_options)) {
            if ($current_cat_id != 0) {
                $indent_flag = "&nbsp;&nbsp;";
                for ($x = 2; $x <= $count; $x++) {
                    $indent_flag .= "&raquo;&nbsp;";
                }//end for loop
            }//end if

            $cat_name = $indent_flag . $cat_name;
            $option_results[$cat_id] = $cat_name;
            make_selectlist($cat_id, $count);
        }//end while
    }//end if
    return $option_results;
}

//end function


$del_flag = false;
$update_flag = false;

$var_post_type = "";

$file_size = 0;
$file_type = "";
$file_name = "";


$var_mesg = "";
$ref_file = "";
$sql = "";
$var_url = "";
$var_swapid = "";
$var_source = "";
$var_category_desc = "";
$var_category_id = "";
$var_category_new_id = "";
$var_post_date = "";
$var_title = "";
$var_brand = "";
$var_type = "";
$var_condition = "";
$var_year = "";
$var_value = "";
$txtPoint = '';
$var_shipping = "";
$var_description = "";
$var_quantity = 0;
$var_command = "";
$func_pic_url = "";
$error_mesg = "";
$var_title_url = "";
$txtImgDes = "";
$txtSmallImage = "";
$func_txtSmallImage = "";


// Newly Uploaded Images

$txtPicture = addslashes($_POST["txtPicture"]);
$txtSmallImage = addslashes($_POST["txtPictureSmall"]);


function validate_values_update() {
    global $var_mesg, $file_size, $file_type, $var_source, $var_swapid, $var_value, $txtPoint, $MaxUploadSize, $conn;
    $ret_flag = true;
    $var_mesg = "";

    if ($var_source == "s" || $var_source == "w") {
        $sql = "Select nSwapId from " . TABLEPREFIX . "swap where nSwapId = '"
                . addslashes($var_swapid) . "'   AND
                     vDelStatus='0' AND nUserId='" . $_SESSION["guserid"] . "'";

        if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {
            $var_mesg = ERROR_ITEM_ALREADY_SWAPPED_DELETED . "<br>";
            $ret_flag = false;
        }//end if
    }//end if
    else if ($var_source == "sa") {
        $sql = "Select nSaleId from " . TABLEPREFIX . "sale where nSaleId = '"
                . addslashes($var_swapid) . "'   AND
                      vDelStatus='0' AND nUserId='" . $_SESSION["guserid"] . "'";
        if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {
            $var_mesg = ERROR_ITEM_ALREADY_PURCHASED_DELETED . "<br>";
            $ret_flag = false;
        }//end if
    }//end else

   /* if ($file_size > 0) {
        if ($file_type != "image/gif" && $file_type != "image/jpeg" && $file_type != "image/pjpeg") {
            $ret_flag = false;
            $var_mesg .= ERROR_FILE_REQUIRED_FORMAT . "<br>";
        }//end if
        
        $file_size = $file_size / (1024 * 1024);
        if ($file_size > $MaxUploadSize) {
            $var_mesg .= ERROR_FILE_TOO_LARGE . "<br>";
            $ret_flag = false;
        }//end if
    }//end if*/
    
    //checking point enable in website
    if ($EnablePoint == '1' || $EnablePoint == '2') {
        if (!is_numeric($txtPoint) || intval($txtPoint) <= 0) {
            $ret_flag = false;
        }//end if
    }//end if
    if ($EnablePoint == '0' || $EnablePoint == '2') {
        if (!is_numeric($var_value) || intval($var_value) <= 0) {
            $ret_flag = false;
        }//end if
    }//end else

    return $ret_flag;
}

//end function

function validate_values_delete() {
    global $var_mesg, $file_size, $file_type, $var_source, $var_swapid, $txtPoint, $conn;
    $ret_flag = true;
    $var_mesg = "";

    if ($var_source == "s" || $var_source == "w") {
        $sql = "Select nSwapId from " . TABLEPREFIX . "swap where nSwapId = '"
                . addslashes($var_swapid) . "'   AND
                     vDelStatus='0' AND nUserId='" . $_SESSION["guserid"] . "'";
        if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {
            $var_mesg = ERROR_ITEM_ALREADY_SWAPPED_DELETED . "<br>";
            $ret_flag = false;
        }//end if
    }//end if
    else if ($var_source == "sa") {
        $sql = "Select nSaleId from " . TABLEPREFIX . "sale where nSaleId = '"
                . addslashes($var_swapid) . "'   AND
                         vDelStatus='0' AND nUserId='" . $_SESSION["guserid"] . "'";
        if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {
            $var_mesg = ERROR_ITEM_ALREADY_PURCHASED_DELETED . "<br>";
            $ret_flag = false;
        }//end if
    }//end else
    return $ret_flag;
}

//end function
//if the user is tring to update/delete information
//print_r($_POST);exit;
if ($_POST["command"] == "update" || $_POST["command"] == "delete") {
    
 //print_r($_POST);exit;
    $var_command = $_POST["command"];
    $var_swapid = $_POST["swapid"];
    $var_post_date = $_POST["txtPostDate"];
    $var_post_type = $_POST["txtPostType"];
    $var_source = $_POST["source"];
    $var_url = $_POST["var_url"];
    $var_category_id = $_POST["txtCategory"];
    $var_category_new_id = $_POST["cat_id"];
    $var_title = $_POST["txtTitle"];
    $var_brand = $_POST["txtBrand"];
    $var_type = $_POST["txtType"];
    $var_condition = $_POST["txtCondition"];
    $var_year = $_POST["txtYear"];
    $var_value = $_POST["txtValue"];
    $txtPoint = $_POST["txtPoint"];
    $var_shipping = $_POST["txtShipping"];
    $var_description = $_POST["txtDescription"];
    $var_quantity = $_POST["txtQuantity"];
    $txtImgDes = $_POST["txtImgDes"];

    $var_title_url = ($var_source == "s") ? TEXT_SWAP_ITEM : ($var_source == "w" ? TEXT_WISH_ITEM : TEXT_SALE_ITEM);
    $ref_file = ($var_source == "s") ? "userswapdetailed.php" : ($var_source == "w" ? "userwishdetailed.php" : "usersaledetailed.php");

    //manage uploads
    /*if (is_uploaded_file($_FILES['txtUrl']['tmp_name'])) {
        //get file size
        $file_size = $_FILES['txtUrl']['size'];

        //set file size limit
        $file_type = $_FILES['txtUrl']['type'];

        $file_name = "";
        if ($file_type == "image/pjpeg" || $file_type == "image/jpeg") {
            $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . ".jpg";
        }//end if
        else if ($file_type == "image/gif") {
            $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . ".gif";
        }//end else
        /*else if ($file_type == "image/bmp") {
            $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . ".bmp";
        }//end else
         
    }//end if*/

    $sql_update = "";
    if ($var_command == "delete") {
        if (validate_values_delete()) {
            if ($var_source == "s" || $var_source == "w") {
                //block that handles the decrement
                $sql = "Select nCategoryId,vRoute from " . TABLEPREFIX . "category
                                   where nCategoryId ='" . addslashes($var_category_id) . "'";
                $sub_route_old = "";
               
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if (mysqli_num_rows($result) > 0) {
                    if ($row = mysqli_fetch_array($result)) {
                        $sub_route_old = $row["vRoute"];
                    }//end if
                }//end if
                if ($sub_route_old!=''){
                    $sql = "Update " . TABLEPREFIX . "category set nCount = nCount - 1
                                        where nCategoryId  IN($sub_route_old) ";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }
                //end of block

                $result_pic = mysqli_query($conn, "Select vUrl,vSmlImg from " . TABLEPREFIX . "swap where nSwapId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                if (mysqli_num_rows($result_pic) > 0) {
                    if ($row = mysqli_fetch_array($result_pic)) {
                        $func_pic_url = $row["vUrl"];
                        $func_txtSmallImage = $row["vSmlImg"];
                    }//end if
                }//end if
                $result_pic = null;
                $row = null;

                //get the main  swapid's where the present swapid is present
                //in the swaptxn table.Update the swaptxn table.

                $sql = "Select distinct nSwapId from " . TABLEPREFIX . "swapreturn where
                                   nSwapReturnId= '" . addslashes($var_swapid) . "' ";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $sub_slist = $var_swapid;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $sub_slist .= "," . $row["nSwapId"];
                    }//end while
                }//end if

                $sql = "Update " . TABLEPREFIX . "swaptxn set vStatus='N'  where
                                 nSwapId IN($sub_slist) AND vStatus != 'A'";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                //End of changes in the swaptxn table
                //Delete the item from the ".TABLEPREFIX."swap table by changing the vDelStatus to 1
                $sql = "Update " . TABLEPREFIX . "swap set vDelStatus='1' where
                                 nSwapId= '" . addslashes($var_swapid) . "' ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                //Delete the item from the ".TABLEPREFIX."gallery table by changing the vDelStatus to 1
                mysqli_query($conn, "update " . TABLEPREFIX . "gallery set vDelStatus='1' where nUserId='" . $_SESSION["guserid"] . "' and
											nSwapId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));

                //Deletion s/w $sql_update="delete from ".TABLEPREFIX."swap where nSwapid='" . addslashes($var_swapid) . "'";
                $gTableId = 'nSwapId';
            }//end if
            else if ($var_source == "sa") {
                $result_pic = mysqli_query($conn, "Select vUrl,nQuantity,vSmlImg from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                if (mysqli_num_rows($result_pic) > 0) {
                    if ($row = mysqli_fetch_array($result_pic)) {
                        $func_pic_url = $row["vUrl"];
                        $quantity_to_check = $row["nQuantity"];
                        $func_txtSmallImage = $row["vSmlImg"];
                    }//end if
                }//end if
                $result_pic = null;
                $row = null;

                //block that handles the decrementation of the count of categories
                settype($quantity_to_check, double);
                if ($quantity_to_check > 0) {
                    $sql = "Select nCategoryId,vRoute from " . TABLEPREFIX . "category
                                     where nCategoryId ='" . addslashes($var_category_id) . "'";
                    $sub_route_old = "";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result) > 0) {
                        if ($row = mysqli_fetch_array($result)) {
                            $sub_route_old = $row["vRoute"];
                        }//end if
                    }//end if
                    
                    if ($sub_route_old!=''){
                        $sql = "Update " . TABLEPREFIX . "category set nCount = nCount - 1
                                              where nCategoryId  IN($sub_route_old) ";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }
                }//end if
                //end of block

                $sql = "Update " . TABLEPREFIX . "sale set vDelStatus='1' where
                                nSaleid= '" . addslashes($var_swapid) . "' ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                //Delete the item from the ".TABLEPREFIX."gallery table by changing the vDelStatus to 1
                mysqli_query($conn, "update " . TABLEPREFIX . "gallery set vDelStatus='1' where nUserId='" . $_SESSION["guserid"] . "' and
											nSaleId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                $gTableId = 'nSaleId';
            }//end else if

            $func_pic_url = "";
            $func_txtSmallImage = "";

            //on successful deletion
            // mysqli_query($conn, $sql_update);
            $del_flag = true;
            
            $_SESSION["updated_msg"] = MESSAGE_SELECTED_ITEM_DELETED;
            
            if($var_source == "sa")
            {
                header("Location:usermain.php?page=saleslist");
                exit;
            }
            elseif($var_source=="s")
            {
                header("Location:usermain.php?page=swaplist");
                exit;
            }
            elseif($var_source=="w")
            {
                header("Location:usermain.php?page=wishslist");
                exit;
            }
        }//end if
        else {
            header("location:swapitemconfirm.php?mode=delete&flag=false");
            exit();
        }//end else
    }//end if
    else if ($var_command == "update") { 
        if (validate_values_update()) {
            //update the category table
            //1. decrement the ncount for the old vRoute
            //2. increment the ncount for the new vroute
            //this is done only if the category id changes

            if ($var_category_id != $var_category_new_id) {
                $sql = "Select nCategoryId,vRoute from " . TABLEPREFIX . "category
                             where nCategoryId IN($var_category_id,$var_category_new_id)";
                $sub_route_old = "";
                $sub_route_new = "";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        if ($row["nCategoryId"] == $var_category_id) {
                            $sub_route_old = $row["vRoute"];
                        }//end if
                        else {
                            $sub_route_new = $row["vRoute"];
                        }//end else
                    }//end while loop
                }//end if

                if ($var_source == "s" || $var_source == "w") {
                    if ($sub_route_old!=''){
                        $sql = "Update " . TABLEPREFIX . "category set nCount = nCount - 1
                       where nCategoryId  IN($sub_route_old) ";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }
                    if ($sub_route_new!=''){
                        $sql = "Update " . TABLEPREFIX . "category set nCount = nCount + 1
                                       where nCategoryId  IN($sub_route_new) ";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }
                }//end if
                else if ($var_source == "sa") {
                    $sql = "Select nQuantity from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($var_swapid) . "'";
                    $resultcheck = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($resultcheck) > 0) {
                        $row = mysqli_fetch_array($resultcheck);
                        $quantity_to_check = $row["nQuantity"];
                        settype($quantity_to_check, double);
                        if ($quantity_to_check > 0 && $sub_route_old!='') {
                            $sql = "Update " . TABLEPREFIX . "category set nCount = nCount - 1
                                         where nCategoryId  IN($sub_route_old) ";
                            mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        }//endi f
                    }//end if
                    settype($var_quantity, double);
                    if ($var_quantity > 0) {
                        $sql = "Update " . TABLEPREFIX . "category set nCount = nCount + 1
                                          where nCategoryId  IN($sub_route_new) ";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }//end if
                }//end if
            }//end else
            else {
                if ($var_source == "sa") {
                    $sql = "Select nCategoryId,vRoute from " . TABLEPREFIX . "category
                                     where nCategoryId ='$var_category_id'";
                    $sub_route_old = "";
                    $sub_route_new = "";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result) > 0) {
                        if ($row = mysqli_fetch_array($result)) {
                            $sub_route_old = $row["vRoute"];
                        }//end if
                    }//end if
                    settype($var_quantity, double);
                    $sql = "Select nQuantity from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($var_swapid) . "'";
                    $resultcheck = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($resultcheck) > 0) {
                        $row = mysqli_fetch_array($resultcheck);
                        $quantity_to_check = $row["nQuantity"];
                        settype($quantity_to_check, double);
                        if ($quantity_to_check == 0 && $var_quantity > 0 && $sub_route_old!='') {
                            $sql = "Update " . TABLEPREFIX . "category set nCount = nCount + 1
                                             where nCategoryId  IN($sub_route_old) ";
                            mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        }//end if
                        else if ($quantity_to_check > 0 && $var_quantity == 0 && $sub_route_old!='') {
                            $sql = "Update " . TABLEPREFIX . "category set nCount = nCount - 1
                                              where nCategoryId  IN($sub_route_old) ";
                            mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        }//end else if
                    }//end if
                    
                }//end if
            }//end else

            $var_category_id = $var_category_new_id;

            if ($var_source == "s" || $var_source == "w") {
                $result_pic = mysqli_query($conn, "Select vUrl,vSmlImg from " . TABLEPREFIX . "swap where nSwapId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                if (mysqli_num_rows($result_pic) > 0) {
                    if ($row = mysqli_fetch_array($result_pic)) {                        
                        $func_pic_url = $row["vUrl"];
                        $func_txtSmallImage = $row["vSmlImg"];
                    }//end if
                }//end if
                $result_pic = null;
                $row = null;

                if ($txtPicture=='') {
                    $file_name = $txtPicture = $func_pic_url;
                    $txtSmallImage = $func_txtSmallImage;
                }//end if
                else if ($txtPicture!='') {
                    @unlink($func_pic_url);
                    @unlink($func_txtSmallImage);
                   
                }//end else if
                //check point status

                switch ($EnablePoint) {
                    case "2":
                    case "1":
                        $newField = ",nPoint='" . $txtPoint . "'";
                        break;

                    case "0":
                        $newField = '';
                        break;
                }//end switch

                $var_url = $file_name;
                $sql_update = "Update " . TABLEPREFIX . "swap set nCategoryId='" . addslashes($var_category_id) . "',"
                        . "vTitle='" . addslashes($var_title) . "',"
                        . "vBrand='" . addslashes($var_brand) . "',"
                        . "vType='" . addslashes($var_type) . "',"
                        . "vCondition='" . addslashes($var_condition) . "',"
                        . "vYear='" . addslashes($var_year) . "',"
                        . "nValue='" . addslashes($var_value) . "',"
                        . "nShipping='" . addslashes($var_shipping) . "',"
                        . "vUrl='" . addslashes($txtPicture) . "',"
                        . "vDescription='" . addslashes($var_description) . "',"
                        . "vSmlImg='" . addslashes($txtSmallImage) . "',"
                        . "vImgDes='" . addslashes($txtImgDes) . "' "
                        . $newField
                        . " where nSwapId='" . addslashes($var_swapid) . "'";

                mysqli_query($conn, $sql_update) or die(mysqli_error($conn));

                //update gallery starts here
                if(is_array($_POST['productMoreImage']))
                {
                     $moreFiles = $_POST['productMoreImage'];
                      for ($x = 0; $x < count($moreFiles); $x++) {
                        $moreImageName = $moreFiles[$x];
                        if ($moreImageName != "") {

                            $moreImage_large            = "pics/large_".$moreImageName;
                            $moreImage_medium            = "pics/medium_".$moreImageName;
                            $txtSmallImage             = "pics/small_".$moreImageName;

                      if($_POST['nGalId'][$x]!='')      { 
                          
                          
                        $update_query =  "update " . TABLEPREFIX . "gallery set vImg='" .mysqli_real_escape_string($conn, $moreImage_large) . "',vSmlImg='" . mysqli_real_escape_string($conn, $txtSmallImage) . "' ,vMedImg='" . mysqli_real_escape_string($conn, $moreImage_medium) . "' where nUserId='" . $_SESSION["guserid"] . "' and nSwapId='" . addslashes($var_swapid) . "' 
											and nId='" . $_POST['nGalId'][$x] . "'";
                            
                         mysqli_query($conn, $update_query) or die(mysqli_error($conn));
                      }       
                      else{
                         
                         $insert_query         = "insert into " . TABLEPREFIX . "gallery (nUserId,nSwapId,vDes,vImg,vSmlImg,vMedImg) values
                                                     ('" . $_SESSION["guserid"] . "','" . addslashes($var_swapid) . "',
                                                      '" . addslashes($_POST['txtImgDesGal'][$x]) . "','" . mysqli_real_escape_string($conn, $moreImage_large) . "',
                                                      '" . mysqli_real_escape_string($conn, $txtSmallImage) . "' , '".mysqli_real_escape_string($conn, $moreImage_medium)."' )";
                          
                          mysqli_query($conn, $insert_query) or die(mysqli_error($conn));
                      }
                      
                }
            }
                     
          }
           
          if (is_array($_POST['txtImgDesGal'])) {
                $k = 0;
            foreach ($_POST['txtImgDesGal'] as $val) {
                //update into gallery table
            if($_POST['nGalId'][$k]!='')      {     
                mysqli_query($conn, "update " . TABLEPREFIX . "gallery set vDes='" . mysqli_real_escape_string($conn, $val) . "'
                                            where nUserId='" . $_SESSION["guserid"] . "' and nSaleId='" . addslashes($var_swapid) . "' 
                                                and nId='" . $_POST['nGalId'][$k] . "'") or die(mysqli_error($conn));
            }
           
                $k++;
            }//end foreach
        }//end if
                
               
    //update gallery stops here
                //update gallery contents
                
              

                $gTableId = 'nSwapId';
            }//end if
            else if ($var_source == "sa") {
                $result_pic = mysqli_query($conn, "Select vUrl,vSmlImg from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                if (mysqli_num_rows($result_pic) > 0) {
                    if ($row = mysqli_fetch_array($result_pic)) {
                        $func_pic_url = $row["vUrl"];
                        $func_txtSmallImage = $row["vSmlImg"];
                    }//end if
                }//end if
                $result_pic = null;
                $row = null;

                if ($txtPicture=='') {
                    $txtPicture = $file_name = $func_pic_url;
                    $txtSmallImage = $func_txtSmallImage;
                }//end if
                else if ($txtPicture!='') {
                     @unlink($func_pic_url);
                    @unlink($func_txtSmallImage);
                    
                   /* $file_name = ReplaceArray($file_name);
                    move_uploaded_file($_FILES['txtUrl']['tmp_name'], $file_name);
					
					
                    // new code to resize the image if it is too large
                    list($old_width, $old_height, $type, $attr) = getimagesize($file_name);
                    if($old_width > 800)
                    {
                            $max_width = 800;
                            $max_height = 600;
                            // Calculate the scaling we need to do to fit the image inside our frame
                            $scale      = min($max_width/$old_width, $max_height/$old_height);
                            // Get the new dimensions
                            $new_width  = ceil($scale*$old_width);
                            $new_height = ceil($scale*$old_height);
                            $file_name = resizeImg($file_name, $new_width, $new_height, false, 100, 0, "");
                    }
                    // new resize code ends
														
					
                    $txtSmallImage = resizeImg($file_name, 120, 120, false, 100, 0, "_thumb");
                    chmod($file_name, 0755);*/
                }//end if
                //check point status
                switch ($EnablePoint) {
                    case "2":
                    case "1":
                        $newField = ",nPoint='" . $txtPoint . "'";
                        break;

                    case "0":
                        $newField = '';
                        break;
                }//end switch
                $var_url = $txtPicture;
                $sql_update = "Update " . TABLEPREFIX . "sale set nCategoryId='" . addslashes($var_category_id) . "',"
                        . "vTitle='" . mysqli_real_escape_string($conn, $var_title) . "',"
                        . "vBrand='" . mysqli_real_escape_string($conn, $var_brand) . "',"
                        . "vType='" . mysqli_real_escape_string($conn, $var_type) . "',"
                        . "vCondition='" . mysqli_real_escape_string($conn, $var_condition) . "',"
                        . "vYear='" . mysqli_real_escape_string($conn, $var_year) . "',"
                        . "nValue='" . mysqli_real_escape_string($conn, $var_value) . "',"
                        . "nShipping='" . mysqli_real_escape_string($conn, $var_shipping) . "',"
                        . "vUrl='" . mysqli_real_escape_string($conn, $txtPicture) . "',"
                        . "vDescription='" . mysqli_real_escape_string($conn, $var_description) . "',"
                        . "vSmlImg='" . mysqli_real_escape_string($conn, $txtSmallImage) . "',"
                        . "vImgDes='".mysqli_real_escape_string($conn, $txtImgDes)."',"
                        . "nQuantity='" . mysqli_real_escape_string($conn, $var_quantity) . "'  "
                        . $newField. " where nSaleId='" . addslashes($var_swapid) . "'";
                mysqli_query($conn, $sql_update) or die(mysqli_error($conn));
               
//echo $sql_update;exit;
                //update gallery starts here
                
                if(is_array($_POST['productMoreImage']))
                {
                     $moreFiles = $_POST['productMoreImage'];
                      for ($x = 0; $x < count($moreFiles); $x++) {
                        $moreImageName = $moreFiles[$x];
                        if ($moreImageName != "") {
                          //  insert into gallery table

                            $moreImage_large            = "pics/large_".$moreImageName;
                            $moreImage_medium            = "pics/medium_".$moreImageName;
                            $txtSmallImage             = "pics/small_".$moreImageName;

                            //$more_image_description     =  $_POST['txtImgDes'][$x];
                 
                  //Update Alreday Existing Iamge
                      if($_POST['nGalId'][$x]!='')      { 
                        $update_query =  "update " . TABLEPREFIX . "gallery set vImg='" . mysqli_real_escape_string($conn, $moreImage_large). "',vSmlImg='" . mysqli_real_escape_string($conn, $txtSmallImage) . "'
                                                    where nUserId='" . $_SESSION["guserid"] . "' and nSaleId='" . addslashes($var_swapid) . "' 
                                                        and nId='" . $_POST['nGalId'][$x] . "'";
                            
                         mysqli_query($conn, $update_query) or die(mysqli_error($conn));
                      }       
                // End Updation  
                      
                      //Insert More Images 
                      else{
                         
                         $insert_query         = "insert into " . TABLEPREFIX . "gallery (nUserId,nSaleId,vDes,vImg,vSmlImg,vMedImg) values
                                                     ('" . $_SESSION["guserid"] . "','" . addslashes($var_swapid) . "',
                                                      '" . addslashes($_POST['txtImgDesGal'][$x]) . "','" . mysqli_real_escape_string($conn, $moreImage_large) . "',
                                                      '" . mysqli_real_escape_string($conn, $txtSmallImage) . "' , '".mysqli_real_escape_string($conn, $moreImage_medium)."')";
                          
                          mysqli_query($conn, $insert_query) or die(mysqli_error($conn));
                      }
                      
                }
            }
                     
          }
                
          if (is_array($_POST['txtImgDesGal'])) {
                $k = 0;
            foreach ($_POST['txtImgDesGal'] as $val) {
                //update into gallery table
            if($_POST['nGalId'][$k]!='')      {     
                mysqli_query($conn, "update " . TABLEPREFIX . "gallery set vDes='" . mysqli_real_escape_string($conn, $val) . "'
                                            where nUserId='" . $_SESSION["guserid"] . "' and nSaleId='" . addslashes($var_swapid) . "' 
                                                and nId='" . $_POST['nGalId'][$k] . "'") or die(mysqli_error($conn));
            }
           
                $k++;
            }//end foreach
        }//end if
                
               /* if (is_array($_FILES)) {
                    for ($i = 0; $i < count($_FILES); $i++) {
                        if ($_FILES['txtPic']['name'][$i] != '') {
                           // list($oldName, $ext) = split('[.]', $_FILES['txtPic']['name'][$i]);
                            list($oldName, $ext) = explode('[.]', $_FILES['txtPic']['name'][$i]);

                            //manage uploads
                            if (is_uploaded_file($_FILES['txtPic']['tmp_name'][$i])) {
                                //get file size
                                $size = $_FILES['txtPic']['size'][$i]/(1024 * 1024);

                                //set file size limit
                                if ($size > $MaxUploadSize) {
                                    $message = ERROR_FILE_TOO_LARGE;
                                }//end if
                                //set file type
                                $file_type = $_FILES['txtPic']['type'][$i];
                                $file_tempname = $_FILES['txtPic']['tmp_name'][$i];
                                //check if its image file
                                if (!getimagesize($file_tempname)) {
                                    $message = ERROR_FILE_REQUIRED_FORMAT."<br>";
                                }//end if

                                if (($file_type != "image/gif") && ($file_type != "image/jpeg") && ($file_type != "image/pjpeg")) {
                                    $message = ERROR_FILE_REQUIRED_FORMAT;
                                }//end if
                                //move file to the pics directory
                                $file_name = "";
                                if ($file_type == "image/pjpeg" || $file_type == "image/jpeg") {
                                    $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . '_' . $oldName . ".jpg";
                                }//end if
                                else if ($file_type == "image/gif") {
                                    $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . '_' . $oldName . ".gif";
                                }//end else if
                                else if ($file_type == "image/bmp") {
                                    $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . '_' . $oldName . ".bmp";
                                }//end else if

                                $_SESSION['error_msg'].=$message . '<br>';

                                if ($message == "") {
                                    $file_name = ReplaceArray($file_name);
                                    move_uploaded_file($_FILES['txtPic']['tmp_name'][$i], $file_name);
                                    chmod($file_name, 0755);
									
									// new code to resize the image if it is too large
									list($old_width, $old_height, $type, $attr) = getimagesize($file_name);
									if($old_width > 800)
									{
										$max_width = 800;
										$max_height = 600;
										// Calculate the scaling we need to do to fit the image inside our frame
										$scale      = min($max_width/$old_width, $max_height/$old_height);
										// Get the new dimensions
										$new_width  = ceil($scale*$old_width);
										$new_height = ceil($scale*$old_height);
										$file_name = resizeImg($file_name, $new_width, $new_height, false, 100, 0, "");
									}
									// new resize code ends
									
									
									
                                    
                                    $txtSmallImage3 = resizeImg($file_name, 120, 120, false, 100, 0, "_thumb");
                                   $txtPic3 = $file_name;
                                  
                                    //update into gallery table
                                    mysqli_query($conn, "update " . TABLEPREFIX . "gallery set vImg='" . mysqli_real_escape_string($conn, $txtPic3). "',vSmlImg='" . mysqli_real_escape_string($conn, $txtSmallImage3) . "'
                                                    where nUserId='" . $_SESSION["guserid"] . "' and nSaleId='" . addslashes($var_swapid) . "' 
                                                        and nId='" . $_POST['nGalId'][$i] . "'") or die(mysqli_error($conn));
                                }//end if
                            }//end if
                        }//end if
                    }//end for loop
                }//end if*/
                //
                //
                //
                //update gallery stops here
                //update gallery contents
              
                //insert New Images starts here
               /* if (is_array($_FILES)) {
                    for ($ii = 0; $ii < count($_FILES); $ii++) {
                        if ($_FILES['txtPicNew']['name'][$ii] != '') {
                            //list($oldName, $ext) = split('[.]', $_FILES['txtPicNew']['name'][$ii]);
                            list($oldName, $ext) = explode('[.]', $_FILES['txtPicNew']['name'][$ii]);

                            //manage uploads
                            if (is_uploaded_file($_FILES['txtPicNew']['tmp_name'][$ii])) {
                                //get file size
                                $size = $_FILES['txtPicNew']['size'][$ii]/(1024 * 1024);

                                //set file size limit
                                if ($size > $MaxUploadSize) {
                                    $message = ERROR_FILE_TOO_LARGE;
                                }//end if
                                //set file type
                                $file_type = $_FILES['txtPicNew']['type'][$ii];
                                $file_tempname = $_FILES['txtPicNew']['tmp_name'][$ii];
                                //check if its image file
                                if (!getimagesize($file_tempname)) {
                                    $message = ERROR_FILE_REQUIRED_FORMAT."<br>";
                                }//end if

                                if (($file_type != "image/gif") && ($file_type != "image/jpeg") && ($file_type != "image/pjpeg")) {
                                    $message = ERROR_FILE_REQUIRED_FORMAT;
                                }//end if
                                //move file to the pics directory
                                $file_name = "";
                                if ($file_type == "image/pjpeg" || $file_type == "image/jpeg") {
                                    $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . '_' . $oldName . ".jpg";
                                }//end if
                                else if ($file_type == "image/gif") {
                                    $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . '_' . $oldName . ".gif";
                                }//end else if
                                else if ($file_type == "image/bmp") {
                                    $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . '_' . $oldName . ".bmp";
                                }//end else if

                                $_SESSION['error_msg'].=$message . '<br>';

                                if ($message == "") {
                                    $file_name = ReplaceArray($file_name);
                                    move_uploaded_file($_FILES['txtPicNew']['tmp_name'][$ii], $file_name);
                                    chmod($file_name, 0755);
															
									// new code to resize the image if it is too large
									list($old_width, $old_height, $type, $attr) = getimagesize($file_name);
									if($old_width > 800)
									{
										$max_width = 800;
										$max_height = 600;
										// Calculate the scaling we need to do to fit the image inside our frame
										$scale      = min($max_width/$old_width, $max_height/$old_height);
										// Get the new dimensions
										$new_width  = ceil($scale*$old_width);
										$new_height = ceil($scale*$old_height);
										$file_name = resizeImg($file_name, $new_width, $new_height, false, 100, 0, "");
									}
									// new resize code ends
									
									
                                     $txtPicNew4 = $file_name;
                                     $txtSmallImage4 = resizeImg($txtPicNew4, 120, 120, false, 100, 0, "_thumb");
                                    
                                    
                                    //update into gallery table
                                    mysqli_query($conn, "insert into " . TABLEPREFIX . "gallery (nUserId,nSaleId,vDes,vImg,vSmlImg) values
                                                     ('" . $_SESSION["guserid"] . "','" . addslashes($var_swapid) . "',
                                                      '" . addslashes($_POST['txtImgDesGalNew'][$ii]) . "','" . mysqli_real_escape_string($conn, $txtPicNew4) . "',
                                                      '" . mysqli_real_escape_string($conn, $txtSmallImage4) . "')") or die(mysqli_error($conn));
                                }//end if
                            }//end if
                        }//end if
                    }//end for loop
                }//end if */
                //insert gallery stops here

                $gTableId = 'nSaleId';
       }//end else if
            // mysqli_query($conn, $sql_update);
            $update_flag = true;
            $_SESSION["updated_msg"] = MESSAGE_SELECTED_ITEM_UPDATED;
            
           
            if($var_source == "sa")
            {
                header("Location:usermain.php?page=saleslist");
                exit;
            }
            elseif($var_source=="s")
            {
                header("Location:usermain.php?page=swaplist");
                exit;
            }
            elseif($var_source=="w")
            {
                header("Location:usermain.php?page=wishlist");
                exit;
            }
        }//end if
        else {
            //header("location:swapitemconfirm.php?mode=edit&flag=false");
        }//end else
    }//end if
}//end if
//if the user is trying to view the details for edition/deletion
else {
    if ($_GET["source"] == "s") {
        //set the target file links to userwish
        $ref_file = "userswapdetailed.php";

        //set the sql to retreive data from swap table where vPostType=wish
        $sql = "select nSwapId,S.nCategoryId,
                                L.vCategoryDesc,S.nUserId,S.vPostType,
                                CONCAT(CONCAT(U.vFirstName,'  '),U.vLastName) as UserName,
                                S.vTitle,S.vBrand,S.vType,S.vCondition,
                                S.vYear,S.nValue,S.nShipping,S.vUrl,S.vDescription,S.dPostDate,
                                S.vSmlImg,S.vImgDes,S.nPoint from
                                " . TABLEPREFIX . "swap S
                                    left join " . TABLEPREFIX . "users U on S.nUserId = U.nUserId
                                    left join " . TABLEPREFIX . "category C on S.nCategoryId = C.nCategoryId
                                    LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                where  
                                  S.nSwapId = '" . addslashes($_GET["swapid"]) . "' AND S.nUserId='" . $_SESSION["guserid"] . "' ";
        $gTableId = 'nSwapId';
        $getId = 'swapid';
        $gCheckCond = " and nSaleId='0'";
    }//end if
    else if ($_GET["source"] == "w") {
        //set the target file links to userwish
        $ref_file = "userwishdetailed.php";

        //set the sql to retreive data from swap table where vPostType=wish
        $sql = "select nSwapId,S.nCategoryId,
                                L.vCategoryDesc,S.nUserId,S.vPostType,'0' as 'nQuantity',
                                CONCAT(CONCAT(U.vFirstName,'  '),U.vLastName) as UserName,
                                S.vTitle,S.vBrand,S.vType,S.vCondition,
                                S.vYear,S.nValue,S.nShipping,S.vUrl,S.vDescription,S.dPostDate,
                                S.vSmlImg,S.vImgDes,S.nPoint from
                                " . TABLEPREFIX . "swap S
                                    left join " . TABLEPREFIX . "users U on S.nUserId = U.nUserId
                                    left join " . TABLEPREFIX . "category C on S.nCategoryId = C.nCategoryId
                                    LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                where  
                                 S.nSwapId = '" . addslashes($_GET["swapid"]) . "' AND S.nUserId='" . $_SESSION["guserid"] . "' ";
        $gTableId = 'nSwapId';
        $getId = 'swapid';
        $gCheckCond = " and nSaleId='0'";
    }//end else if
    else if ($_GET["source"] == "sa") {
        //set the target file links to usersale
        $ref_file = "usersaledetailed.php";

        //set the sql to retreive data from sale table
        $sql = "select nSaleId as 'nSwapId',S.nCategoryId,S.nPoint,
                                L.vCategoryDesc,S.nUserId,'sale' as 'vPostType',
                                CONCAT(CONCAT(U.vFirstName,'  '),U.vLastName) as UserName,
                                S.vTitle,S.vBrand,S.vType,S.vCondition,S.nQuantity,
                                S.vYear,S.nValue,S.nShipping,S.vUrl,S.vDescription,S.dPostDate,
                                S.vSmlImg,S.vImgDes from
                                " . TABLEPREFIX . "sale S
                                    left join " . TABLEPREFIX . "users U on S.nUserId = U.nUserId
                                    left join " . TABLEPREFIX . "category C on S.nCategoryId = C.nCategoryId
                                    LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                where  
                                 S.nSaleId = '" . addslashes($_GET["saleid"]) . "' AND S.nUserId='" . $_SESSION["guserid"] . "' ";
        $gTableId = 'nSaleId';
        $getId = 'saleid';
        $gCheckCond = " and nSwapId='0'";
    }//end else if

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) != 0) {
        if ($row = mysqli_fetch_array($result)) {
            if ($row["vPostType"] == "swap") {
                $var_post_type = 'Swap Item';
                $var_title_url = TEXT_SWAP_ITEM;
            }//end if
            else if ($row["vPostType"] == "wish") {
                $var_post_type = 'Wish Item';
                $var_title_url = TEXT_WISH_ITEM;
            }//end else if
            else if ($row["vPostType"] == "sale") {
                $var_post_type = 'Item For Sale';
                $var_title_url = TEXT_SALE_ITEM;
            }//end else if

            $var_post_date = date('m/d/Y H:i:s', strtotime($row["dPostDate"]));
            //dateFormat($row["dPostDate"],"Y-m-d","m/d/Y");;
            $var_swapid = $row["nSwapId"];
            $var_source = $_GET["source"];
            $var_url = $row["vUrl"];
            $var_category_desc = $row["vCategoryDesc"];
            $var_category_id = $row["nCategoryId"];
            $var_title = $row["vTitle"];
            $var_brand = $row["vBrand"];
            $var_type = $row["vType"];
            $var_condition = $row["vCondition"];
            $var_year = $row["vYear"];
            $var_value = $row["nValue"];
            $txtPoint = $row["nPoint"];
            $var_shipping = $row["nShipping"];
            $var_quantity = $row["nQuantity"];
            $var_description = $row["vDescription"];
            $txtSmallImage = $row["vSmlImg"];
            $txtImgDes = $row["vImgDes"];
            $var_command = "";
        }//end if
    }//end if
}//end if

switch ($var_source) {
    case "s":
        $gTableId = 'nSwapId';
        $getId = 'swapid';
        $gCheckCond = " and nSaleId='0'";
        break;

    case "w":
        $gTableId = 'nSwapId';
        $getId = 'swapid';
        $gCheckCond = " and nSaleId='0'";
        break;

    case "sa":
        $gTableId = 'nSaleId';
        $getId = 'saleid';
        $gCheckCond = " and nSwapId='0'";
        break;
}//end switch
include_once('./includes/title.php');

?>
<?php include_once('includes/top_header.php'); ?>
<script type="text/javascript">
 var jqr = jQuery.noConflict();
	jqr(document).ready(function()
	{
		jqr('div#JqexistImage  a.delete').click(function(e)
		{
                       // alert('sss');
                       // return false;
                         e.stopPropagation();
                         e.preventDefault();
			if (confirm('<?php echo DELETE_IMAGE_CONFIRMATION;?>'))
			{
				//var id = jqr(this).parent().parent().attr('id');
                                
                                var source  = '<?php echo $_REQUEST['source'];?>';
                                if(source=='sa'){
                                    var id = '<?php echo $_REQUEST['saleid'];?>';
                                }else {
                                    var id = '<?php echo $_REQUEST['swapid'];?>';
                                }
                                
				var data = 'id=' + id + '&source='+source;
                               // alert(data);
				//var parent = jqr(this).parent().parent();

				jqr.ajax(
				{
					   type: "POST",
					   url: "delete_image.php",
					   data: data,
					   cache: false,
					
					   success: function()
					   {  
                                               jqr('div#JqexistImage').remove();
                                               alert ('<?php echo IMAGE_DELETED_SUCCESS?>');
							//parent.fadeOut('slow', function() {$(this).remove();});
					   }
				 });				
			}
		});
                
                
                jqr('div.jQDeleteMoreImage  a.delete').click(function(e)
		{
                         e.stopPropagation();
                         e.preventDefault();
                         
			if(confirm('<?php echo DELETE_IMAGE_CONFIRMATION;?>'))
			{
				var mystring = jqr(this).attr('id');
                                
                                //alert(mystring);
                                 var source  = '<?php echo $_REQUEST['source'];?>';
                                if(source=='sa'){
                                    var id = '<?php echo $_REQUEST['saleid'];?>';
                                }else {
                                    var id = '<?php echo $_REQUEST['swapid'];?>';
                                }
                                
				var data = 'itemid=' + id + '&source='+source+'&delid='+mystring;
                                
                                var delid   = mystring;
                               
                               
                                /*var arr = mystring.split('@');
                                id = arr[0];```````````````````````````````````````
                                delid = arr[1]
                                var data = 'swapid='+id+'delid='+delid;
				
				var parent = jqr(this).parent().parent();*/

                                //````````````````````````````````````````````````````````````````````````````````````````````````````````````````alert()

				jqr.ajax(
				{
					   type: "POST",
					   url: "delete_image.php",
					   data: data,
					   cache: false,
					
					   success: function()
					   {  
                                               //alert('.jQTable_'+delid);
                                               jqr('#JqMoreExistImage'+delid).remove();
                                               //parent.fadeOut('slow', function() {jqr(this).remove();});
                                               alert ('<?php echo IMAGE_DELETED_SUCCESS?>');
							//parent.fadeOut('slow', function() {$(this).remove();});
					   }
				 });				
			}
		});
		
		// style the table with alternate colors
		// sets specified color for every odd row
		
	});
	
</script>
<script language="javascript" type="text/javascript">
    var Parent=new Array;
    var Comm="";
    var Act="";
    Comm="<?php echo  $commlmt ?>";
    Act="<?php echo  $var_value ?>";
    <?php
    $parentsql = "select distinct nParentId from " . TABLEPREFIX . "category";
    $result = mysqli_query($conn, $parentsql);
    $count = 0;
    $disp = "";
    if (mysqli_num_rows($result) != 0) {
        while ($row = mysqli_fetch_array($result)) {
            $disp .="Parent[$count]=\"";
            $disp .=$row["nParentId"];
            $disp .="\";\n";
            $count = $count + 1;
        }
    }
    echo $disp;
    ?>

    function clickUpdate()
    { 
        if(validate()){
            document.frmSwapItem.command.value='update';
            document.frmSwapItem.submit();
        }
    }
    function clickDelete()
    {
//        if(validate()){
            if(confirm('<?php echo TEXT_CONFIRM_DELETE;?>'))
            {
                document.frmSwapItem.command.value='delete';
                document.frmSwapItem.submit();
                return true;
        }
        else{
                    return false;
        }
//        }
    }
    /*function checkNumeric(ids){

        var val=document.getElementById(ids).value;

        if ((isNaN(val))||(val<0)||(parseInt(val,10)==0)){
            alert("<?php //echo ERROR_POSITIVE_VALUE; ?>");
            document.getElementById(ids).value="0";
            document.getElementById(ids).focus();
        }
    }*/
    function validate()
    {
        var frm = window.document.frmSwapItem;
        if(trim(frm.cat_id.options[frm.cat_id.options.selectedIndex].value) == ""){
            alert("<?php echo ERROR_EMPTY_CATEGORY; ?>");
            frm.cat_id.focus();
            return false;
        }else if(frm.txtTitle.value == ""){
            alert("<?php echo ERROR_EMPTY_TITLE; ?>");
            frm.txtTitle.focus();
            return false;
        }
<?php

if($_REQUEST["source"]!="w") {  // Hide Price and Pints for wish items
    
if ($EnablePoint == '1' || $EnablePoint == '2') {
    ?>
        if((trim(frm.txtPoint.value) =="")||(parseInt(trim(frm.txtPoint.value)) < 0))
        {
            alert("<?php echo str_replace('{point_name}',POINT_NAME,ERROR_INVALID_POINT); ?>");
            frm.txtPoint.focus();
            return false;
        }
    <?php
}//end if
if ($EnablePoint == '0' || $EnablePoint == '2') {
    ?>
        if((trim(frm.txtValue.value) =="")||(parseInt(trim(frm.txtValue.value)) < 0))
        {
            alert("<?php echo ERROR_INVALID_PRICE; ?>");
            frm.txtValue.focus();
            return false;
        }		
    <?php
}//end else

}
?>
        if(frm.txtDescription.value == ""){
            alert("<?php echo ERROR_EMPTY_DESCRIPTION; ?>");
            frm.txtDescription.focus();
            return false;
        }
<?php
if ($_GET["source"] == "sa" || $_POST["source"] == "sa") {
    ?>
        if((frm.txtQuantity.value < 0)){
            alert("<?php echo ERROR_EMPTY_QUANTITY; ?>");
            frm.txtQuantity.focus();
            return false;
        }
    <?php
}
?>
                //        alert('has to be validated');
                //        return false;
                return true;
            }


            function setcatValue(){

                selvalue=document.getElementById("cat_id").options[document.getElementById("cat_id").options.selectedIndex].value;
                flag="false";
                for(i=0;i<Parent.length;i++){
                    if(Parent[i]==selvalue){
                        flag="true";
                    }
                }
                /*     if(flag=="false"){
          document.all("txtCategory").value=document.all("cat_id").options[document.all("cat_id").options.selectedIndex].value;
     }else{
          document.frmSwapItem.cat_id.value=document.all("txtCategory").value;
          //document.all("txtCategory").value = "";
          //document.all("cat_id").options.selectedIndex=0;
          alert("There is a Subcategory under Category you have selected.\nPlease select a Subcategory!");

     }
                 */
                if(flag == "true"){
                    document.frmSwapItem.cat_id.value=document.getElementById("txtCategory").value;
                    alert("<?php echo ERROR_SELECT_SUBCATEGORY; ?>");
                }
            }

            function checkValue(t){
                if(isNaN(t.value) || t.value.substring(0,1) == " " || t.value.length == 0 || parseFloat(t.value) < 0 ){
                    t.value=0;
                }
            }

            function checkCommission(obj){
                check_float_value(obj);
                if (parseInt(obj.value)!=0) {
                    if(parseInt(document.frmSwapItem.txtValue.value)>=Comm && parseInt(document.frmSwapItem.txtValue.value)!=Act){
                        alert("<?php echo ERROR_MAX_VALUE_ALLOWED_FOR_POSTING; ?> " + Comm);
                        document.frmSwapItem.txtValue.value=Act;
                    }
                }
            }
</script>

<style>
            .cropselection_popup
            {
            right: 0px !important;
            margin-top: 47px !important;
            }
        </style>


<body >
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3">
				<?php include_once ("./includes/usermenu.php"); ?>
			</div>
			<div class="col-lg-9">			
				<div class="innersubheader">
					<h4><?php echo  $var_title_url ?></h4>
				</div>
				
				<div class="">
					
					<div class="col-lg-12 form-section">
						<form enctype="multipart/form-data" name="frmSwapItem" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
							<?php
							//if(isset($_SESSION['error_msg']) && $_SESSION['error_msg']!='')
							//{
							?>
								<div class="full_width warning"><?php //echo $_SESSION['error_msg']; ?></div>
							<?php //unset($_SESSION['error_msg']);}//end if ?>	
							<?php
							if (isset($var_mesg) && $var_mesg != '') {
								?>
									<div class="full_width warning"><?php echo $var_mesg; ?></div>
							<?php }//end if ?>
							
							<div class="row main_form_inner">
								<input TYPE="hidden" NAME="txtPostType" id="txtPostType" VALUE="<?php echo  $var_post_type ?>">
								<input type="hidden" name="swapid" id="swapid" value="<?php echo  $var_swapid ?>">
								<input type="hidden" name="source" id="source" value="<?php echo  $var_source ?>">
								<input type="hidden" name="command" id="command" value="">
								<input type="hidden" name="commlmt" id="commlmt" value="<?php echo  $commlmt ?>">
								<input type="hidden" name="txtCategory" id="txtCategory" value="<?php echo  $var_category_id ?>" >
								<label><?php echo TEXT_POSTED_ON; ?></label>
								<input type="text" name="txtPostDate" id="txtPostDate" class="textbox_contact_flsd  form-control" value="<?php echo htmlentities($var_post_date); ?>">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_CATEGORY; ?><span class="warning">*</span></label>
								<select id="cat_id" name="cat_id" onChange="setcatValue();" class="comm_input width1  form-control">
									<option value="">--<?php echo TEXT_SELECT_ONE; ?>-- </option>
									<?php
									$get_options = make_selectlist(0, 0);
									if (count($get_options) > 0) {
										$categories = $_POST['cat_id'];
										foreach ($get_options as $key => $value) {
											$options .="<option value=\"$key\"";
											if ($_POST['cat_id'] == "$key") {
												$options .=" selected=\"selected\"";
											}
											$options .=">".utf8_encode($value)."</option>\n";
										}
									}
									echo $options;
									?>
								</select>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_TITLE; ?><span class="warning">*</span></label>
								<input type="text" name="txtTitle" id="txtTitle" value="<?php echo  htmlentities($var_title) ?>" class="textbox_contact_flsd  form-control" size="32" maxlength="100">
								
								<input type="hidden" name="var_url" value="<?php echo  $var_url ?>">
								
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_BRAND; ?></label>
								<input name="txtBrand" type="text" class="textbox_contact_flsd  form-control" id="txtBrand" value="<?php echo  htmlentities($var_brand) ?>"  size="32" maxlength="100">
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_TYPE; ?></label>
								<select name="txtType" class="comm_input width1 form-control">
									<option value='New'><?php echo TEXT_NEW; ?></option>
									<option value='Used'><?php echo TEXT_USED; ?></option>
								</select>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_CONDITION; ?></label>
								<select name="txtCondition" class="comm_input width1 form-control">
									<option value='New'><?php echo TEXT_NEW; ?></option>
									<option value='Like New'><?php echo TEXT_LIKE_NEW; ?></option>
									<option value='Very good'><?php echo TEXT_VERY_GOOD; ?></option>
									<option value='Good'><?php echo TEXT_GOOD; ?></option>
								</select>
							</div>
                                                                        <div class="row main_form_inner">
								<label><?php echo TEXT_ITEM_DESCRIPTION; ?><span class="warning">*</span></label>
								<textarea name="txtDescription" id="txtDescription" cols="32" ROWS="4" class="textbox_contact2 form-control"><?php echo  htmlentities($var_description) ?></textarea>
							</div>
							<?php if($_REQUEST["source"] != "w") {  // Hide Points , Price , Year for wish items 
								?> 
							<div class="row main_form_inner">
								<label><?php echo TEXT_YEAR; ?></label>
								<select name="txtYear" id="txtYear" class="comm_input width1 form-control">
									<option value=""><?php echo TEXT_SELECT_YEAR; ?></option>
									<?php
									for ($i = 1900; $i <= 2050; $i++) {
										$showCheckd = '';
										if ($i == $var_year) {
											$showCheckd = 'selected';
										}//end if
										echo '<option value="' . $i . '" ' . $showCheckd . '>' . $i . '</option>';
									}//end for loop
									?>
								</select>
							</div>
							<?php
								//checking point stats
								if ($EnablePoint == '1' || $EnablePoint == '2') {
							?>
							<div class="row main_form_inner">
								<label><?php echo POINT_NAME; ?> <!--<span class="warning">*</span>--></label>
								<input type="text" class="textbox_contact_flsd form-control" name="txtPoint"  id="txtPoint" onChange="javascript:check_float_value(this)" size="5" maxlength="10" value="<?php echo  htmlentities(stripslashes($txtPoint)) ?>" />
							</div>
							 <?php
								}//end if
								//checking point enable in website
								if ($EnablePoint != '1') {
							?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_PRICE; ?><span class="warning">*</span> &nbsp; ( <?php echo CURRENCY_CODE; ?> )</label>
								<input type="text" name="txtValue" id="txtValue" value="<?php echo  $var_value ?>"
								<?php
								if ($_GET["source"] == "sa" || $_POST["source"] == "sa") {

									echo "onBlur='javascript:checkCommission(this);'";
								} else {

									echo " onChange='javascript:check_float_value(this);'";
								}
								?>
								 class="textbox_contact_flsd form-control"  size="32" maxlength="10">
							</div>
							 <?php
									
								}  // Hiding Price , Year , Ponits end 
									}//end point check if
									if ($_GET["source"] != "w" && $_POST["source"] != "w") {
										//checking point enable in website
										if ($EnablePoint != '1') {
                                                                            ?>
                                                                         <div class="row main_form_inner">
                                                                                 <label><?php echo TEXT_SHIPPING_CHARGE; ?> &nbsp; ( <?php echo CURRENCY_CODE; ?> )</label>
                                                                                 <input type="text" name="txtShipping" id="txtShipping" value="<?php echo  $var_shipping ?>" onChange="javascript:check_float_value(this)" class="textbox_contact_flsd form-control"  size="32" maxlength="10" <?php echo  ($_GET["source"] == "w" || $_POST["source"] == "w") ? "readonly " : "" ?>>
                                                                         </div>
                                                                         <?php
                                                                                 }//end point check if
                                                                        }
                                                                        ?>
                                                                        <div class="row main_form_inner">
                                                                            
                                                                   
								<?php //}//end if ?>
								
                                                                        </div>
                                                                        
                                                         <?php if ($_REQUEST["source"] != "w" ) { ?>  
							<div class="row main_form_inner">
								
                                                                        <input type="hidden" id="cropButtonClicked" value="0">
									<label><?php echo TEXT_PICTURE_IF_ANY; ?></label>
                                                                                 <?php
                                                                            //echo $var_url;exit;
								//if ($_GET["source"] != "w" && $_POST["source"] != "w") {
									if (trim($var_url) != "" && file_exists(trim($var_url)) && $_REQUEST["source"] != "w" ) {
										//$var_url = "pics/nophoto.gif";
									?>
									<div class="full_width" id="JqexistImage">
                                                                            <input type="hidden" name="jQImage"  id="jQImage" value="<?php echo $var_url; ?>" >
										<a href="<?php echo $var_url; ?>" rel="lightbox" title=""><img src="<?php echo $var_url; ?>" width="75" height="75" border="0"></a>
                                                                                <br>
                                                                                <a href="#" class="delete" style="color:#FF0000;" id="<?php echo $_REQUEST['swapid'];?>"> <img src="images/delete_icon.png" width="15" height="15"> ( <?php echo DELETE_IMAGE;?> )  </a>
									</div>
									<?php
										}//end if
								?>
										<div style="clear: both; position: relative;">
                                                                                <div style="float: left;" id="mulitplefileuploader" class="prduct_images"><?php echo TEXT_CHOOSE_IMAGE; ?></div>
                                                                                <input type="hidden" name="pType" id="pType" value="product" />
                                                                                     <div style="float: left;">
                                                                                        <div class="warning"><?php echo str_replace('{max_images}',DisplayLookUp('MaxOfImages'),TEXT_MAX_NO_IMAGES); ?></div>
											<!--<br><span class="warning"><?php //echo TEXT_IMAGE_SIZE_SHOULD_BE; ?> 393 x 269</span>-->
											<div class="warning"><?php echo str_replace('{MaxUploadSize}',$MaxUploadSize,TEXT_MAX_UPLOAD_SIZE_IS); ?></div>
											<div class="warning"><?php echo ERROR_FILE_REQUIRED_FORMAT; ?></div>
                                                                                   </div>
                                                                               
                                                                            </div>	
                                                        <div id="jqCropImageDiv" class="cropselection_popup" style="display: none;">
                                                        <span><a href="#" class="banner_close jqCloseCropImage"></span></a>
                                                        <div class="imgcrop_btncontainer">
                                                            <input type="button" value="<?php echo IMAGE_CROP_VALUE;?>" class="button left jqCropImage"  />
                                                            <div class="clear"></div>
                                                        </div>
                                                        <input type="hidden" id="x" name="x" />
                                                        <input type="hidden" id="y" name="y" />
                                                        <input type="hidden" id="w" name="w" />
                                                        <input type="hidden" id="h" name="h" />
                                                        <input type="hidden" id="bannerName" name="bannerName" value="<?php echo $_POST['bannerName'];?>" />
                                                        <input type="hidden" id="txtPicture" name="txtPicture" value="<?php echo $_POST['txtPicture'];?>" />
                                                        <input type="hidden" id="txtPictureSmall" name="txtPictureSmall" value="<?php echo $_POST['txtPictureSmall'];?>" />
                                                        
                                                        <input type="hidden" id="bannerFileId" name="bannerFileId" />
                                                        <div class="notificationdivstyle1"><?php echo IMAGE_SELCTION_NOTE;?></div>
                                                        <div class="jqImageHoldingDiv"></div>
                                                     </div>
                                                                        
                                                    <div class="row">
                                                    <div class="row-rightcol">
                                                    <p class="text-style13" id="jqUploadFileStartTxt" style="display: none;">
                                                        <img src="images/loading.gif" id="uploaded_banner_image" class="jqAjaxLoaderImage">
                                                    </p>
                                                    <p class="error_msg" style="color:red; font-size: 13px; font-weight: bold;" id="error_product_image"></p>
                                                    </div>

                                                <div id="status"></div>
                                                    <div class="clear"></div>
                                                </div>
                                                 <div id="JQUploadedImageDiv">
                                                      <div class="jqproductImageDiv" id="jqproductImageDiv" >
                                                            <?php if($_POST['bannerName']){ ?>
                                                            <img src="pics/medium_<?php echo $_POST['bannerName'];?>" border="0" width="183" height="191">
                                                            <?php } ?>
                                                    </div>
                                                </div>
                                                                        
								<!--<div style="position:relative;">
									<a href="#" onClick="javascript:return false;" class="submit"><b><?php echo TEXT_CHOOSE_IMAGE; ?></b></a>
									<input type="File" name="txtUrl" id="txtUrl" class="textbox2  form-control" size="1" onChange="javascript:document.getElementById('file_name').innerHTML=this.value;" style="position:absolute;top:0px;left:-30px;opacity:0;filter:alpha(opacity=0)" />
									<span id="file_name"></span>
								</div>-->
                                            
                                                                <div class="full_width" style="display:none;"> <label><?php echo TEXT_DESCRIPTION; ?> </label>
                                                                    <textarea style="display:none;" name="txtImgDes" id="txtImgDes" cols="32" ROWS="4" class="textbox_contact2  form-control"><?php echo  htmlentities($txtImgDes) ?></textarea>
                                                            </div>
							</div>
							<?php }//end if ?>
							<?php if ($_GET["source"] == "sa" || $_POST["source"] == "sa") { ?>
							<div class="row main_form_inner">
								<label><?php echo TEXT_QUANTITY; ?><span class="warning">*</span></label>
								<input TYPE="text" NAME="txtQuantity" id="txtQuantity" VALUE="<?php echo  $var_quantity ?>" onChange="javascript:check_numeric_value(this)" class="textbox_contact_flsd form-control"  size="32">
							</div>
							<?php } ?>
							
							<?php if ($_GET["source"] != "w" && $_POST["source"] != "w") { ?>
								
								<?php
								$chechId = ($_GET[$getId] != '') ? $_GET[$getId] : $var_swapid;                                                                            
								$arrImages = mysqli_query($conn, "select * from " . TABLEPREFIX . "gallery where $gTableId='" . addslashes($chechId) . "'
											 and vDelStatus='0' " . $gCheckCond) or die(mysqli_error($conn));
								if (mysqli_num_rows($arrImages) > 0) {
								?>
							<div class="row main_form_inner">
								<h3><?php echo UPLOAD_MORE_IMAGES; ?></h3>
							</div>
							<?php 
                                                        $iCount = 0;
                                                        $cnt= 0 ;
                                                        while ($arr = mysqli_fetch_array($arrImages)) {
                                                        ?> 
							<div class="row main_form_inner">
                                                            
                                                            <?php 
                                                            if($arr['vSmlImg']!=""){
                                                            ?>
								<div class="full_width jQDeleteMoreImage"  id="JqMoreExistImage<?php echo $arr['nId']; ?>">
                                                                    <input type="hidden" name="txtExist_moreImage" id="txtExist_moreImage<?php echo $iCount?>" value="<?php echo $arr['vImg'];?>">
									<a href="<?php echo $arr['vImg']; ?>" id="<?php echo $arr['nId']; ?>" rel="lightbox"><img src="<?php echo $arr['vSmlImg']; ?>" width="75" height="75" border="0"></a>
                                                                        <br>
                                                                        <a href="#" class="delete" id="<?php echo $arr['nId']; ?>" style="color:#FF0000;"> <img src="images/delete_icon.png" width="15" height="15"> ( <?php echo DELETE_IMAGE;?> )  </a>
								</div>
                                                            <?php } ?>
                                                 <div class="jqMoreImageContainer" style="padding:5px;border:1px solid #D3D1D1;margin:10px 1px 10px 1px;float:left;">
                                                    <div class="fileuploader" data="<?php echo $iCount; ?>" id="multi_file_upload_<?php echo $iCount?>" ><b><?php echo CHOOSE_MORE_IMAGE; ?> <?php echo $cnt + 1 ; ?></b></div>
                                                    
                                                    <div class="jqproductImageMoreDiv" id="jqproductImageMoreDiv_<?php echo $iCount;?>">
                                                        <?php
                                                        if($_POST['productMoreImage']){
                                                            $pImage = $_POST['productMoreImage'][$iCount];
                                                            if($pImage!=""){
                                                        ?>
                                                            <img src="pics/medium_<?php echo $pImage;?>" border="0" width= "187" height="170" />

                                                        <?php  } }?>
                                                    </div>
                                                    
                                               <div id="jqCropMoreImageDiv_<?php echo $iCount;?>" class="cropselection_popup" style="display: none;">
                                                        <a href="#" class="banner_close jqCloseCropMoreImage"><span></span></a>
                                                        <div class="imgcrop_btncontainer">
                                                            <input type="button" value="<?php echo IMAGE_CROP_VALUE;?>" class="button left jqCropMoreImage" />
                                                            <div class="clear"></div>
                                                        </div>
                                                        <input type="hidden" id="x" name="x" />
                                                        <input type="hidden" id="y" name="y" />
                                                        <input type="hidden" id="w" name="w" />
                                                        <input type="hidden" id="h" name="h" />
                                                        <input type="hidden" id="productMoreImage_<?php echo $iCount;?>" name="productMoreImage[]" value="<?php echo $_POST['productMoreImage'][$iCount];?>" />
                                                        <input type="hidden" id="productMoreImageId_<?php echo $iCount;?>" name="productMoreImageId[]" value="<?php echo $_POST['productMoreImageId'][$iCount];?>" />
                                                        <div class="notificationdivstyle1"><?php echo IMAGE_SELCTION_NOTE;?></div>
                                                        <div class="jqImageHoldingDiv"></div>
                                                  </div>            
                                                                        
                                                    <div class="row">
                                                    <div class="row-rightcol">
                                                    <p class="text-style13" id="jqUploadFileStartTxt" style="display: none;">
                                                        <img src="images/loading.gif" id="uploaded_banner_image" class="jqAjaxLoaderImage">
                                                    </p>
                                                    <p class="error_msg" style="color:red; font-size: 13px; font-weight: bold;" id="error_product_image"></p>
                                                    </div>

                                                    <div id="status"></div>
                                                    <div class="clear"></div>
                                                </div>
                                                                        
                                                <div id="JQUploadedImageDiv">
                                                      <div class="jqproductImageDiv" id="jqproductImageDiv">
                                                    <?php if($_POST['bannerName']){ ?>
                                                    <img src="pics/medium_<?php echo $_POST['bannerName'];?>" border="0" width="183" height="191">
                                                    <?php } ?>
                                                </div>
                                                </div>     
                                                    
                                                </div>
                                                                
								<!--<div class="full_width">
									<a href="#" onClick="javascript:return false;" class="submit"><b><?php echo TEXT_CHOOSE_IMAGE; ?></b></a>
									<input type="file" name="txtPic[]" class="textbox2 form-control" id="txtPic_<?php echo $arr['nId']; ?>" size="1" onChange="javascript:document.getElementById('file_name_<?php echo $arr['nId']; ?>').innerHTML=this.value;" style="position:absolute;top:0px;left:-30px;opacity:0;filter:alpha(opacity=0)">
									<span id="file_name_<?php echo $arr['nId']; ?>"></span>
                                </div>-->
                                                                <div class="full_width" style="display:none;"> <label><?php echo TEXT_DESCRIPTION; ?> </label>
								<textarea style="display:none;" name="txtImgDesGal[]" cols="32" ROWS="4" class="textbox_contact2 form-control"><?php echo $arr['vDes']; ?></textarea>
								<input type="hidden" name="nGalId[]" value="<?php echo $arr['nId']; ?>">
								
								</div>
							</div>
							<?php
                                                        $iCount ++ ;
                                                        $cnt++;
								}//end while
							}//end if
							?>
							<?php
							$chechId = ($_GET[$getId] != '') ? $_GET[$getId] : $var_swapid;
							$sqlcount = mysqli_query($conn, "select count(*) as cnt from " . TABLEPREFIX . "gallery where $gTableId='" . addslashes($chechId) . "'
							and vDelStatus='0'") or die(mysqli_error($conn));
							if (mysqli_num_rows($sqlcount) > 0) {
								$sqltotCnt = mysqli_result($sqlcount, 0, 'cnt');
							}//end if
							$TotalNew = DisplayLookUp('MaxOfImages') - 1;
							?>
							<div class="row main_form_inner">
								<div class="full_width warning"><?php echo str_replace('{MaxUploadSize}',$MaxUploadSize,TEXT_MAX_UPLOAD_SIZE_IS); ?></div>
								<div class="full_width warning"><?php echo ERROR_FILE_REQUIRED_FORMAT; ?></div>
							</div>
							<?php
								for ($k = $sqltotCnt; $k < $TotalNew; $k++) {
							?>
							<div class="row main_form_inner">
                                                            <div class="jqMoreImageContainer" style="padding:5px;border:1px solid #D3D1D1;margin:10px 1px 10px 1px;float:left;">
                                                    <div class="fileuploader" data="<?php echo $k; ?>" id="multi_file_upload_<?php echo $k;?>" ><b><?php echo CHOOSE_MORE_IMAGE; ?> <?php echo $k + 1; ?></b></div>
                                                    
                                                    <div class="jqproductImageMoreDiv" id="jqproductImageMoreDiv_<?php echo $k;?>">
                                                        <?php
                                                        if($_POST['productMoreImage']){
                                                            $pImage = $_POST['productMoreImage'][$k];
                                                            if($pImage!=""){
                                                        ?>
                                                            <img src="pics/medium_<?php echo $pImage;?>" border="0" width= "187" height="170" />

                                                        <?php  } }?>
                                                    </div>
                                                    
                                                <div id="jqCropMoreImageDiv_<?php echo $k;?>" class="cropselection_popup" style="display: none;">
                                                        <a href="#" class="banner_close jqCloseCropMoreImage"><span></span></a>
                                                        <div class="imgcrop_btncontainer">
                                                            <input type="button" value="<?php echo IMAGE_CROP_VALUE;?>" class="button left jqCropMoreImage" />
                                                            <div class="clear"></div>
                                                        </div>
                                                        <input type="hidden" id="x" name="x" />
                                                        <input type="hidden" id="y" name="y" />
                                                        <input type="hidden" id="w" name="w" />
                                                        <input type="hidden" id="h" name="h" />
                                                        <input type="hidden" id="productMoreImage_<?php echo $k;?>" name="productMoreImage[]" value="<?php echo $_POST['productMoreImage'][$k];?>" />
                                                        <input type="hidden" id="productMoreImageId_<?php echo $k;?>" name="productMoreImageId[]" value="<?php echo $_POST['productMoreImageId'][$k];?>" />
                                                        <div class="notificationdivstyle1"><?php echo IMAGE_SELCTION_NOTE;?></div>
                                                        <div class="jqImageHoldingDiv"></div>
                                                    </div>            
                                                                        
                                                    <div class="row">
                                                    <div class="row-rightcol">
                                                    <p class="text-style13" id="jqUploadFileStartTxt" style="display: none;">
                                                        <img src="images/loading.gif" id="uploaded_banner_image" class="jqAjaxLoaderImage">
                                                    </p>
                                                    <p class="error_msg" style="color:red; font-size: 13px; font-weight: bold;" id="error_product_image"></p>
                                                    </div>

                                                    <div id="status"></div>
                                                    <div class="clear"></div>
                                                </div>
                                                                        
                                                <div id="JQUploadedImageDiv">
                                                      <div class="jqproductImageDiv" id="jqproductImageDiv">
                                                    <?php if($_POST['bannerName']){ ?>
                                                    <img src="pics/medium_<?php echo $_POST['bannerName'];?>" border="0" width="183" height="191">
                                                    <?php } ?>
                                                </div>
                                                </div>     
                                                    
                                                </div>
								
								
                                                            <div class="full_width" >
								<label style="display:none;" class="margin_t_20"><?php echo TEXT_DESCRIPTION; ?></label>
								<textarea style="display:none;" name="txtImgDesGalNew[]" cols="32" ROWS="4" class="textbox2 form-control"></textarea>
                                                            </div>
							</div>
									<?php
						}//end for loop
							}//end chekc if
							if ($_GET["source"] == "w" || $_POST["source"] == "w") {
							?>
							<div class="row main_form_inner">
                                                                            
                                                                            <?php
								//if ($_GET["source"] != "w" && $_POST["source"] != "w") {
									if (trim($var_url) != "" && file_exists(trim($var_url)) ) {
										//$var_url = "pics/nophoto.gif";
									?>
									<div class="full_width" id="JqexistImage">
                                                                            <input type="hidden" name="jQImage"  id="jQImage" value="<?php echo $var_url; ?>" >
										<a href="<?php echo $var_url; ?>" rel="lightbox" title=""><img src="<?php echo $var_url; ?>" width="75" height="75" border="0"></a>
									</div>
									<?php
										}//end if
								?>
																
                                                                        </div>
							<div class="row main_form_inner">
                                                            
                                                             <input type="hidden" id="cropButtonClicked" value="0">
									<label><?php echo TEXT_PICTURE_IF_ANY; ?></label>
										<div style="clear: both; position: relative;">
                                                                                <div style="float: left;" id="mulitplefileuploader" class="prduct_images"><?php echo TEXT_CHOOSE_IMAGE; ?></div>
                                                                                <input type="hidden" name="pType" id="pType" value="product" />
                                                                                     <div style="float: left;">
                                                                                        <div class="warning"><?php echo str_replace('{max_images}',DisplayLookUp('MaxOfImages'),TEXT_MAX_NO_IMAGES); ?></div>
											<!--<br><span class="warning"><?php //echo TEXT_IMAGE_SIZE_SHOULD_BE; ?> 393 x 269</span>-->
											<div class="warning"><?php echo str_replace('{MaxUploadSize}',$MaxUploadSize,TEXT_MAX_UPLOAD_SIZE_IS); ?></div>
											<div class="warning"><?php echo ERROR_FILE_REQUIRED_FORMAT; ?></div>
                                                                                   </div>
                                                                               
                                                                            </div>	
                                                        <div id="jqCropImageDiv" class="cropselection_popup" style="display: none;">
                                                        <a href="#" class="banner_close jqCloseCropImage"><span></span></a>
                                                        <div class="imgcrop_btncontainer">
                                                            <input type="button" value="<?php echo IMAGE_CROP_VALUE;?>" class="button left jqCropImage"  />
                                                            <div class="clear"></div>
                                                        </div>
                                                        <input type="hidden" id="x" name="x" />
                                                        <input type="hidden" id="y" name="y" />
                                                        <input type="hidden" id="w" name="w" />
                                                        <input type="hidden" id="h" name="h" />
                                                        <input type="hidden" id="bannerName" name="bannerName" value="<?php echo $_POST['bannerName'];?>" />
                                                        <input type="hidden" id="txtPicture" name="txtPicture" value="<?php echo $_POST['txtPicture'];?>" />
                                                        <input type="hidden" id="txtPictureSmall" name="txtPictureSmall" value="<?php echo $_POST['txtPictureSmall'];?>" />
                                                        
                                                        <input type="hidden" id="bannerFileId" name="bannerFileId" />
                                                        <div class="notificationdivstyle1"><?php echo IMAGE_SELCTION_NOTE;?></div>
                                                        <div class="jqImageHoldingDiv"></div>
                                                </div>
                                                                        
                                                    <div class="row">
                                                    <div class="row-rightcol">
                                                    <p class="text-style13" id="jqUploadFileStartTxt" style="display: none;">
                                                        <img src="images/loading.gif" id="uploaded_banner_image" class="jqAjaxLoaderImage">
                                                    </p>
                                                    <p class="error_msg" style="color:red; font-size: 13px; font-weight: bold;" id="error_product_image"></p>
                                                    </div>

                                                <div id="status"></div>
                                                    <div class="clear"></div>
                                                </div>
                                                 <div id="JQUploadedImageDiv">
                                                      <div class="jqproductImageDiv" id="jqproductImageDiv">
                                                            <?php if($_POST['bannerName']){ ?>
                                                            <img src="pics/medium_<?php echo $_POST['bannerName'];?>" border="0" width="183" height="191">
                                                            <?php } ?>
                                                    </div>
                                                </div>
								<!--<label><?php echo TEXT_IMAGE; ?></label>
								<div style="position:relative;">
									<a href="#" onClick="javascript:return false;" class="submit"><b><?php echo TEXT_CHOOSE_IMAGE; ?></b></a>
									<input type="File" name="txtUrl" id="txtUrl" class="textbox2" size="1" onChange="javascript:document.getElementById('file_name').innerHTML=this.value;" style="position:absolute;top:0px;left:-30px;opacity:0;filter:alpha(opacity=0)" />
									<span id="file_name"></span>
								</div>
								<br><span class="warning"><?php echo str_replace('{MaxUploadSize}',$MaxUploadSize,TEXT_MAX_UPLOAD_SIZE_IS); ?></span>
								<br><span class="warning"><?php echo ERROR_FILE_REQUIRED_FORMAT; ?></span>-->
							</div>
                            <?php }//end if ?>
                                                                        
							<div class="row main_form_inner">
								<label>
									<input type="button" name="Update" VALUE="<?php echo BUTTON_UPDATE; ?>" onClick="javascript:clickUpdate();" class="subm_btt">

								</label>
								<label>
									<input type="button" NAME="Delete" VALUE="<?php echo BUTTON_DELETE; ?>" ONCLICK="javascript:clickDelete();"  class="subm_btt">

									
								</label>
							</div>
					</form>
					</div>	
								
				</div>
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>	
			</div>
		</div>  
	</div>
</div>

<script language="javascript" type="text/javascript">
	<?php
	echo("document.frmSwapItem.cat_id.value='$var_category_id';");
	echo("document.frmSwapItem.txtType.value='$var_type';");
	echo("document.frmSwapItem.txtCondition.value='$var_condition';");

	/*if ($del_flag == true) {
		echo("alert('".MESSAGE_SELECTED_ITEM_DELETED."');");
		//echo("document.location.href='" . $ref_file . "'");
	} elseif ($update_flag == true) {
		//echo("alert('".MESSAGE_SELECTED_ITEM_UPDATED."');");
	}*/
	?>  
</script>
<?php require_once("./includes/footer.php"); ?>

