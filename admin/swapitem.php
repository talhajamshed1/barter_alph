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
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');

$MaxUploadSize = ini_get('upload_max_filesize');
$max_additional_imgs = DisplayLookUp('MaxOfImages');
$MaxUploadSize = $MaxUploadSize * (1024 * 1024);

//list($MaxUploadSize, $ext) = split('M', $MaxUploadSize);
//list($MaxUploadSize, $ext) = explode('M', $MaxUploadSize);
//$MaxUploadSize = $MaxUploadSize * 1024;
$enable_pt = DisplayLookUp('EnablePoint'); 
switch($_GET['source']) {
    case "sa":
    $PGTITLE='sales';
    break;

    case "s":
    $PGTITLE='swap';
    break;

    case "w":
    $PGTITLE='wish';
    break;
}//end switch



//function to display categories in nested manner
function make_selectlist($current_cat_id, $count) {
    static $option_results;
    global $conn;
    if (!isset($current_cat_id)) {
        $current_cat_id =0;
    }//end if
    

    $count = $count+1;
    $sql = "SELECT C.nCategoryId as id, L.vCategoryDesc as name from ".TABLEPREFIX."category C
    LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
    where C.nParentId = '$current_cat_id' order by name asc";
    $get_options = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $num_options = mysqli_num_rows($get_options);
    if ($num_options > 0) {
        while (list($cat_id, $cat_name) = mysqli_fetch_row($get_options)) {
            if ($current_cat_id!=0) {
                $indent_flag = "&nbsp;&nbsp;&nbsp;&nbsp;";
                for ($x=2; $x<=$count; $x++) {
                    $indent_flag .= "&raquo;&nbsp;";
                }//end for loop
            }//end if

            $cat_name = $indent_flag.$cat_name;
            $option_results[$cat_id] = $cat_name;
            make_selectlist($cat_id, $count );
        }//end while
    }//end if
    return $option_results;
}//end fucntion


$del_flag=false;
$update_flag=false;
$var_button_flag = true;
$var_post_type="";
$file_size=0;
$file_type="";
$file_name="";
$var_mesg="";
$ref_file = "";
$sql="";
$var_url="";
$var_swapid="";
$var_source="";
$var_category_desc="";
$var_category_id="";
$var_category_new_id="";
$var_post_date="";
$var_title="";
$var_brand="";
$var_type="";
$var_condition="";
$var_year="";
$var_value="";
$var_point='';
$var_shipping="";
$var_description="";
$var_quantity=0;
$var_command="";
$func_pic_url="";
$error_mesg="";
$var_title_url="";
$txtImgDes="";
$txtSmallImage="";
$func_txtSmallImage="";



function validate_values_update() { 
    global $var_mesg,$file_size,$file_type,$var_source,$var_swapid,$var_value,$var_point,$enable_pt,$conn;
    $ret_flag=true;
    $var_mesg="";
    $MaxUploadSize = ini_get('upload_max_filesize');
    $MaxUploadSize = $MaxUploadSize * (1024 * 1024);
    if($var_source=="s" || $var_source=="w") {
        $sql = "Select nSwapId from ".TABLEPREFIX."swap where nSwapId = '". addslashes($var_swapid) .  "'   AND
        vDelStatus='1'";
        if(mysqli_num_rows(mysqli_query($conn, $sql)) > 0) {
            $var_mesg = "Sorry,this swap item has already been swapped or deleted.<br>";
            $ret_flag=false;
        }//end if
    }//end if
    else if($var_source=="sa") { 
        $sql = "Select nSaleId from ".TABLEPREFIX."sale where nSaleId='".addslashes($var_swapid). "'   AND
        vDelStatus='1'";
        if(mysqli_num_rows(mysqli_query($conn, $sql)) > 0) { 
            $var_mesg = "Sorry,this item has already been purchased or deleted.<br>";
            $ret_flag=false;
        }//end if
    }//end else if

    if($file_size > 0) {
        if($file_type != "image/gif" && $file_type != "image/png" && $file_type != "image/jpeg" && $file_type != "image/pjpeg") {
            $ret_flag = false;
            $var_mesg .= "Image should be any of the following formats (gif/jpg/png). Please try again.<br>";
        }//end if
        //$file_size = $file_size / (1024 * 1024);
        
        if ($file_size > $MaxUploadSize) {
            $var_mesg .= "File Too Large. Please try again.<br>";
            $ret_flag = false;
        }//end if
    }//end if


    //checking point enable in website
    if($enable_pt=='1') {
        if(!is_numeric($var_point) || intval($var_point) <= 0) {
            $var_mesg .= "Your current point mode is 'Points Only'. So points must be greater than zero. Please try again.<br>";
            $ret_flag=false;
        }//end if
    }//end if
    if ($enable_pt!='1' && $var_source!="w") {  
        if(!is_numeric($var_value) || intval($var_value) <= 0) {
            $var_mesg .= "Values must be greater than zero. Please try again.<br>";
            $ret_flag=false;
        }//end if
    }//end else

    return $ret_flag;
}//end function

function validate_values_delete() {
    global $var_mesg,$file_size,$file_type,$var_source,$var_swapid,$var_value,$var_point,$txtPoint,$enable_pt,$conn;
    $ret_flag=true;
    $var_mesg="";

    if($var_source=="s" || $var_source=="w") {
        $sql = "Select nSwapId from ".TABLEPREFIX."swap where nSwapId='".addslashes($var_swapid)."'  AND
        vDelStatus='1'";
        if(mysqli_num_rows(mysqli_query($conn, $sql)) > 0) {
            $var_mesg = "Sorry,this swap item has already been swapped or deleted.<br>";
            $ret_flag=false;
        }//end if
    }//end if
    else if($var_source=="sa") {
        $sql = "Select nSaleId from ".TABLEPREFIX."sale where nSaleId='".addslashes($var_swapid)."'  AND
        vDelStatus='1'";
        if(mysqli_num_rows(mysqli_query($conn, $sql)) > 0) {
            $var_mesg = "Sorry,this item has already been purchased or deleted.<br>";
            $ret_flag=false;
        }//end if
    }//end if
    return $ret_flag;
}//end fucntion
//print_r($_POST);
//print_r($_FILES);
//if the user is tring to update/delete information
if($_POST["command"]=="update" || $_POST["command"]=="delete") {
    $var_command = $_POST["command"];
    $var_swapid=$_POST["swapid"];
    $var_post_date=$_POST["txtPostDate"];
    $var_post_type=$_POST["txtPostType"];
    $var_source=$_POST["source"];
    $var_url=$_POST["var_url"];
    $var_category_id=$_POST["txtCategory"];
    $var_category_new_id=$_POST["cat_id"];
    $var_title=$_POST["txtTitle"];
    $var_brand=$_POST["txtBrand"];
    $var_type=$_POST["txtType"];
    $var_condition=$_POST["txtCondition"];
    $var_year=$_POST["txtYear"];
    $var_value=$_POST["txtValue"];
    $var_point=$_POST["txtPoint"];
    $var_shipping=$_POST["txtShipping"];
    $var_description=$_POST["txtDescription"];
    $var_quantity=$_POST["txtQuantity"];
    $txtImgDes=$_POST["txtImgDes"];
    $var_userid = $_POST["pro_userid"];
    $var_title_url=($var_source=="s")?"Item For Swap":($var_source=="w"?"Item Wished For":"Item For Sale");
    $ref_file=($var_source=="s")?"swap.php":($var_source=="w"?"wish.php":"sales.php");

    //manage uploads
    if (is_uploaded_file($_FILES['txtUrl']['tmp_name'])) {
        //get file size
        $file_size = $_FILES['txtUrl']['size'];

        //set file size limit
        $file_type = $_FILES['txtUrl']['type'];
        
        

        $file_name="";
        if($file_type=="image/pjpeg" || $file_type == "image/jpeg") {
            $file_name = "pics/" . $var_swapid . "_" . time() ."_".mt_rand(10,100).".jpg";
        }//end if
        else if($file_type=="image/gif") {
            $file_name =  "pics/" . $var_swapid . "_" . time() ."_".mt_rand(10,100).".gif";
        }//end else if
        else if($file_type=="image/png") {
            $file_name =  "pics/" . $var_swapid . "_" . time() ."_".mt_rand(10,100).".png";
        }//end else if
    }//end if

    $sql_update="";
    if($var_command == "delete") {
        if(validate_values_delete()) {
            /*
			//update the category table
			//1. decrement the ncount for the old vRoute
			//End of category updation for deletion*/
            if($var_source=="s" || $var_source=="w") {
                //block that handles the decrement#####################################ADD1
                $sql ="Select nCategoryId,vRoute from ".TABLEPREFIX."category
                where nCategoryId ='" . addslashes($var_category_id) . "'";
                $sub_route_old="";
                $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if(mysqli_num_rows($result) > 0) {
                    if($row=mysqli_fetch_array($result)) {
                        $sub_route_old=$row["vRoute"];
                    }//end if
                }//end if
                $sql = "Update ".TABLEPREFIX."category set nCount = nCount - 1
                where nCategoryId  IN($sub_route_old) ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));
                //end of block

                $result_pic=mysqli_query($conn, "Select vUrl,vSmlImg from ".TABLEPREFIX."swap where nSwapId='".addslashes($var_swapid)."'")
                or die(mysqli_error($conn));
                if(mysqli_num_rows($result_pic) > 0) {
                    if($row=mysqli_fetch_array($result_pic)) {
                        $func_pic_url=$row["vUrl"];
                        $func_txtSmallImage=$row["vSmlImg"];
                    }//end if
                }//end if
                $result_pic=null;
                $row=null;

                //get the main  swapid's where the present swapid is present
                //in the swaptxn table.Update the swaptxn table.

                $sql = "Select distinct nSwapId from ".TABLEPREFIX."swapreturn where
                nSwapReturnId= '" . addslashes($var_swapid) . "' ";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $sub_slist=$var_swapid;
                if(mysqli_num_rows($result) > 0) {
                    while($row=mysqli_fetch_array($result)) {
                        $sub_slist .= "," . $row["nSwapId"];
                    }//end while
                }//end if
                $sql = "Update ".TABLEPREFIX."swaptxn set vStatus='N'  where
                nSwapId IN($sub_slist) AND vStatus != 'A'";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                //End of changes in the swaptxn table
                //Delete the item from the ".TABLEPREFIX."swap table by changing the vDelStatus to 1
                $sql = "Update ".TABLEPREFIX."swap set vDelStatus='1' where
                nSwapid= '" . addslashes($var_swapid) . "' ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                //Delete the item from the ".TABLEPREFIX."gallery table by changing the vDelStatus to 1
                mysqli_query($conn, "update ".TABLEPREFIX."gallery set vDelStatus='1' where nSwapId='".addslashes($var_swapid)."'") or die(mysqli_error($conn));

                //Deletion s/w                      $sql_update="delete from ".TABLEPREFIX."swap where nSwapid='" . addslashes($var_swapid) . "'";

            }//end if
            else if($var_source=="sa") {
                $result_pic=mysqli_query($conn, "Select vUrl,nQuantity,vSmlImg from ".TABLEPREFIX."sale where
                   nSaleId='".addslashes($var_swapid)."'") or die(mysqli_error($conn));
                if(mysqli_num_rows($result_pic) > 0) {
                    if($row=mysqli_fetch_array($result_pic)) {
                        $func_pic_url=$row["vUrl"];
                        $quantity_to_check=$row["nQuantity"];
                        $func_txtSmallImage=$row["vSmlImg"];
                    }//end if
                }//end if
                $result_pic=null;
                $row=null;

                //block that handles the decrementation of the count of categories###########ADD2
                settype($quantity_to_check,double);
                if($quantity_to_check > 0) {
                    $sql ="Select nCategoryId,vRoute from ".TABLEPREFIX."category
                    where nCategoryId ='" . addslashes($var_category_id) . "'";
                    $sub_route_old="";
                    $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if(mysqli_num_rows($result) > 0) {
                        if($row=mysqli_fetch_array($result)) {
                            $sub_route_old=$row["vRoute"];
                        }//end if
                    }//end if
                    $sql = "Update ".TABLEPREFIX."category set nCount = nCount - 1
                    where nCategoryId  IN($sub_route_old) ";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }//end if
                //end of block

                $sql = "Update ".TABLEPREFIX."sale set vDelStatus='1' where
                nSaleid= '" . addslashes($var_swapid) . "' ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                //Delete the item from the ".TABLEPREFIX."gallery table by changing the vDelStatus to 1
                mysqli_query($conn, "update ".TABLEPREFIX."gallery set vDelStatus='1' where nSaleId='".addslashes($var_swapid)."'") or die(mysqli_error($conn));
            }//end else if
            if(strlen($func_pic_url) > 0) {
                //we are not deleting the picture since it is required for future reference  for offers
                //@unlink($func_pic_url);
            }//end if
            $func_pic_url="";

            //on successful deletion
            // mysqli_query($conn, $sql_update);
            $del_flag=true;
        }//end if
        else {
            header("location:swapitemconfirm.php?mode=delete&flag=false");
        }//end else
    }//end if
    else if($var_command=="update") { 
        if(validate_values_update()) { 
            //update the category table
            //1. decrement the ncount for the old vRoute
            //2. increment the ncount for the new vroute
            //this is done only if the category id changes

            if($var_category_id != $var_category_new_id) {
                $sql ="Select nCategoryId,vRoute from ".TABLEPREFIX."category
                where nCategoryId IN($var_category_id,$var_category_new_id)";
                $sub_route_old="";
                $sub_route_new="";
                $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if(mysqli_num_rows($result) > 0) {
                    while($row=mysqli_fetch_array($result)) {
                        if($row["nCategoryId"]==$var_category_id) {
                            $sub_route_old=$row["vRoute"];
                        }//end if
                        else {
                            $sub_route_new=$row["vRoute"];
                        }//end else
                    }//end while
                }//end if
                if(($var_source=="s" || $var_source=="w") && ($sub_route_old!='')) {
                    $sql = "Update ".TABLEPREFIX."category set nCount = nCount - 1
                    where nCategoryId  IN($sub_route_old) ";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }
                if ($sub_route_new!=''){
                    $sql = "Update ".TABLEPREFIX."category set nCount = nCount + 1
                    where nCategoryId  IN($sub_route_new) ";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }//end if
                else if($var_source=="sa") { 
                    $sql = "Select nQuantity from ".TABLEPREFIX."sale where nSaleId='" . addslashes($var_swapid) . "'";
                    $resultcheck=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if(mysqli_num_rows($resultcheck) > 0) {
                        $row=mysqli_fetch_array($resultcheck);
                        $quantity_to_check=$row["nQuantity"];
                        settype($quantity_to_check,double);
                        if($quantity_to_check > 0 && $sub_route_old!='') {
                            $sql = "Update ".TABLEPREFIX."category set nCount = nCount - 1
                            where nCategoryId  IN($sub_route_old) ";
                            mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        }//end if
                    }//end if
                    settype($var_quantity,double);
                    if($var_quantity > 0 && $sub_route_new!='') {
                        $sql = "Update ".TABLEPREFIX."category set nCount = nCount + 1
                        where nCategoryId  IN($sub_route_new) ";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }//end if
                }//end else if
            }//end if
            else {
                if($var_source=="sa") {
                    $sql ="Select nCategoryId,vRoute from ".TABLEPREFIX."category
                    where nCategoryId ='$var_category_id'";
                    $sub_route_old="";
                    $sub_route_new="";
                    $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if(mysqli_num_rows($result) > 0) {
                        if($row=mysqli_fetch_array($result)) {
                            $sub_route_old=$row["vRoute"];
                        }//end if
                    }//end if
                    settype($var_quantity,double);
                    $sql = "Select nQuantity from ".TABLEPREFIX."sale where nSaleId='" . addslashes($var_swapid) . "'";
                    $resultcheck=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if(mysqli_num_rows($resultcheck) > 0) {
                        $row=mysqli_fetch_array($resultcheck);
                        $quantity_to_check=$row["nQuantity"];
                        settype($quantity_to_check,double);
                        if($quantity_to_check==0 && $var_quantity > 0 && $sub_route_old!='') {
                            $sql = "Update ".TABLEPREFIX."category set nCount = nCount + 1
                            where nCategoryId  IN($sub_route_old) ";
                            mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        }//end if
                        else if($quantity_to_check > 0 && $var_quantity==0 && $sub_route_old!='') {
                            $sql = "Update ".TABLEPREFIX."category set nCount = nCount - 1
                            where nCategoryId  IN($sub_route_old) ";
                            mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        }//end else if
                    }//end if
                }//end if
            }//end else
            $var_category_id = $var_category_new_id;

            if($var_source=="s" || $var_source=="w") {
                $result_pic=mysqli_query($conn, "Select vUrl,vSmlImg from ".TABLEPREFIX."swap where nSwapId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                if(mysqli_num_rows($result_pic) > 0) {
                    if($row=mysqli_fetch_array($result_pic)) {
                        $func_pic_url=$row["vUrl"];
                        $func_txtSmallImage=$row["vSmlImg"];
                    }//end if
                }//end if
                $result_pic=null;
                $row=null;

                if($file_size <= 0) {
                    $file_name=$func_pic_url;
                    $txtSmallImage=$func_txtSmallImage;
                }//end if
                else if($file_size > 0) {
                    $file_name=ReplaceArray($file_name);
                    move_uploaded_file($_FILES['txtUrl']['tmp_name'], "../" . $file_name);
                    $txtSmallImage=resizeImg('../'.$file_name, 120 ,120, false, 100, 0,"_thumb");
                    $txtSmallImage=substr($txtSmallImage,3,strlen($txtSmallImage));

                    chmod("../" . $file_name,0755);
                    @unlink("../" . $func_pic_url);
                    @unlink("../" . $func_txtSmallImage);
                }//end else if

                //check point status
                switch(DisplayLookUp('EnablePoint')) {
                    case "1":
                    case "2":
                    $newField=",nPoint='".$var_point."'";
                    break;

                    case "0":
                    $newField='';
                    break;
                }//end switch

                $sql_update="Update ".TABLEPREFIX."swap set nCategoryId='" . addslashes($var_category_id) . "',"
                . "vTitle='" . addslashes($var_title) . "',"
                . "vBrand='" . addslashes($var_brand) . "',"
                . "vType='" . addslashes($var_type) . "',"
                . "vCondition='" . addslashes($var_condition) . "',"
                . "vYear='" . addslashes($var_year) . "',"
                . "nPoint='" . addslashes($var_point) . "',"
                . "nValue='" . addslashes($var_value) . "',"
                . "nShipping='" . addslashes($var_shipping) . "',"
                . "vUrl='" . addslashes($file_name) . "',"
                . "vDescription='" . addslashes($var_description) . "',"
                . "vSmlImg='" . addslashes($txtSmallImage) . "',"
                . "vImgDes='" . addslashes($txtImgDes) . "' "
                .$newField
                . " where nSwapId='" . addslashes($var_swapid) . "'";

                mysqli_query($conn, $sql_update) or die(mysqli_error($conn));
                $var_url = $file_name;
                //update gallery starts here
                if(is_array($_FILES)) {
                    for($i=0;$i<count($_FILES['txtPic']['name']);$i++) {
                        if($_FILES['txtPic']['name'][$i]!='') {
                            //manage uploads
                            if (is_uploaded_file($_FILES['txtPic']['tmp_name'][$i])) {
                                //get file size
                                $size = $_FILES['txtPic']['size'][$i]/(1024 * 1024);

                                //set file size limit
                                if ($size > $MaxUploadSize) {
                                    $message="File Too Large. Please try again.";
                                }//end if

                                //set file type
                                $file_type=$_FILES['txtPic']['type'][$i];
                                $file_tempname = $_FILES['txtPic']['tmp_name'][$i];
                                //check if its image file
                                if (!getimagesize($file_tempname)) {
                                    $message = "Image should be any of the following formats (gif/jpg/png). Please try again.<br>";
                                }//end if

                                if(($file_type != "image/gif") && ($file_type != "image/png") && ($file_type != "image/jpeg") && ($file_type != "image/pjpeg") && ($file_type != "image/bmp")) {
                                    $message= "Image should be either gif or jpg or bmp formats.";
                                }//end if

                                //move file to the pics directory
                                $file_name="";
                                if($file_type=="image/pjpeg" || $file_type == "image/jpeg") {
                                    $file_name = "pics/" . $var_swapid . "_" . time() ."_".mt_rand(10,100).".jpg";
                                }//end if
                                else if($file_type=="image/gif") {
                                    $file_name =  "pics/" . $var_swapid . "_" . time() ."_".mt_rand(10,100).".gif";
                                }//end else if
                                else if($file_type=="image/bmp") {
                                    $file_name =  "pics/" . $var_swapid . "_" . time() ."_".mt_rand(10,100).".bmp";
                                }//end else if
                                if($message=="") { 
                                    $file_name=ReplaceArray($file_name);
                                    move_uploaded_file($_FILES['txtPic']['tmp_name'][$i],'../'.$file_name);
                                    chmod('../'.$file_name,0755);
                                    $txtPic=$file_name;
                                    $txtSmallImage=resizeImg('../'.$txtPic, 120 ,120, false, 100, 0,"_thumb");
                                    $txtSmallImage=substr($txtSmallImage,3,strlen($txtSmallImage));

                                    //update into gallery table
                                    if($_POST['nGalId'][$i]!="")
                                    {
                                        mysqli_query($conn, "update ".TABLEPREFIX."gallery set vImg='".$txtPic."',vSmlImg='".$txtSmallImage."'
                                          where nSwapId='".addslashes($var_swapid)."'
                                          and nId='".$_POST['nGalId'][$i]."'") or die(mysqli_error($conn));
                                    }
                                    else { 
                                     mysqli_query($conn, "insert into " . TABLEPREFIX . "gallery (nUserId," . $_POST['gTableId'] . ",vImg,vDes,vSmlImg) values 
                                        ('" . $var_userid. "','" . $var_swapid . "','" . $txtPic . "',
                                        '" . addslashes($_POST['txtImgDesGal'][$i]) . "',
                                        '" . $txtSmallImage . "')") or die(mysqli_error($conn));
                                 }
                                }//end if
                            }//end if
                        }//end if
                    }//end for loop
                }//end if
                //update gallery stops here
                //update gallery contents
                if(is_array($_POST['txtImgDesGal'])) {
                    $k=0;
                    foreach ($_POST['txtImgDesGal'] as $val) {
                        //update into gallery table
                        mysqli_query($conn, "update ".TABLEPREFIX."gallery set vDes='".$val."'
                          where nSwapId='".addslashes($var_swapid)."'
                          and nId='".$_POST['nGalId'][$k]."'") or die(mysqli_error($conn));
                        $k++;
                    }//end foreach
                }//end if

                $gTableId='nSwapId';
            }//end if
            else if($var_source=="sa") {
                $result_pic=mysqli_query($conn, "Select vUrl,vSmlImg from ".TABLEPREFIX."sale where nSaleId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                if(mysqli_num_rows($result_pic) > 0) {
                    if($row=mysqli_fetch_array($result_pic)) {
                        $func_pic_url=$row["vUrl"];
                        $func_txtSmallImage=$row["vSmlImg"];
                    }//end if
                }//end if
                $result_pic=null;
                $row=null;

                if($file_size <= 0) {
                    $file_name=$func_pic_url;
                    $txtSmallImage=$func_txtSmallImage;
                }//end if
                else if($file_size > 0) {
                    @unlink("../" . $func_pic_url);
                    @unlink("../" . $func_txtSmallImage);
                    $file_name=ReplaceArray($file_name);
                    move_uploaded_file($_FILES['txtUrl']['tmp_name'],"../" . $file_name);
                    $txtSmallImage=resizeImg('../'.$file_name, 120 ,120, false, 100, 0,"_thumb");
                    $txtSmallImage=substr($txtSmallImage,3,strlen($txtSmallImage));

                    chmod("../" . $file_name,0755);
                }//end else if

                $sql_update="Update ".TABLEPREFIX."sale set nCategoryId='" . addslashes($var_category_id) . "',"
                . "vTitle='" . addslashes($var_title) . "',"
                . "vBrand='" . addslashes($var_brand) . "',"
                . "vType='" . addslashes($var_type) . "',"
                . "vCondition='" . addslashes($var_condition) . "',"
                . "vYear='" . addslashes($var_year) . "',"
                . "nPoint='" . addslashes($var_point) . "',"
                . "nValue='" . addslashes($var_value) . "',"
                . "nShipping='" . addslashes($var_shipping) . "',"
                . "vUrl='" . addslashes($file_name) . "',"
                . "vDescription='" . addslashes($var_description) . "',"
                . "vSmlImg='" . addslashes($txtSmallImage) . "',"
                . "vImgDes='" . addslashes($txtImgDes) . "',"
                . "nQuantity='" . addslashes($var_quantity) . "'  "
                . " where nSaleId='" . addslashes($var_swapid) . "'";
                mysqli_query($conn, $sql_update) or die(mysqli_error($conn));
                $var_url = $file_name;
                //update gallery starts here 
                //echo count($_FILES['txtPic']['name']); echo "<pre>";print_r($_FILES);echo "</pre>";exit;
                if(is_array($_FILES)) {
                    for($i=0;$i<=count($_FILES['txtPic']['name']);$i++) { 
                        if($_FILES['txtPic']['name'][$i]!='') { 
                            //manage uploads
                            if (is_uploaded_file($_FILES['txtPic']['tmp_name'][$i])) { 
                                //get file size
                                $size = $_FILES['txtPic']['size'][$i]/(1024 * 1024);

                                //set file size limit
                                if ($size > $MaxUploadSize) {
                                    $message="File Too Large. Please try again.";
                                }//end if

                                //set file type
                                $file_type=$_FILES['txtPic']['type'][$i];
                                $file_tempname = $_FILES['txtPic']['tmp_name'][$i];
                                //check if its image file
                                if (!getimagesize($file_tempname)) {
                                    $message = "Image should be any of the following formats (gif/jpg/png). Please try again.<br>";
                                }//end if

                                if(($file_type != "image/gif") && ($file_type != "image/png") && ($file_type != "image/jpeg") && ($file_type != "image/pjpeg") && ($file_type != "image/bmp")) {
                                    $message= "Image should be either gif or jpg or bmp formats.";
                                }//end if

                                //move file to the pics directory
                                $file_name="";
                                if($file_type=="image/pjpeg" || $file_type == "image/jpeg") {
                                    $file_name = "pics/" . $var_swapid . "_" . time() ."_".mt_rand(10,100).".jpg";
                                }//end if
                                else if($file_type=="image/gif") {
                                    $file_name =  "pics/" . $var_swapid . "_" . time() ."_".mt_rand(10,100).".gif";
                                }//end else if
                                else if($file_type=="image/bmp") {
                                    $file_name =  "pics/" . $var_swapid . "_" . time() ."_".mt_rand(10,100).".bmp";
                                }//end else if
                                
                                if($message=="") {
                                    $file_name=ReplaceArray($file_name);
                                    move_uploaded_file($_FILES['txtPic']['tmp_name'][$i],'../'.$file_name);
                                    chmod('../'.$file_name,0755);
                                    $txtPic=$file_name;
                                    $txtSmallImage=resizeImg('../'.$txtPic, 120 ,120, false, 100, 0,"_thumb");
                                    $txtSmallImage=substr($txtSmallImage,3,strlen($txtSmallImage));

                                    //update into gallery table
                                    /*mysqli_query($conn, "update ".TABLEPREFIX."gallery set vImg='".$txtPic."',vSmlImg='".$txtSmallImage."'
										    where nSaleId='".addslashes($var_swapid)."'
                                           and nId='".$_POST['nGalId'][$i]."'") or die(mysqli_error($conn));*/
                                           
                                           if($_POST['nGalId'][$i]!="")
                                           {
                                               
                                            mysqli_query($conn, "update ".TABLEPREFIX."gallery set vImg='".$txtPic."',vSmlImg='".$txtSmallImage."'
                                              where nSaleId='".addslashes($var_swapid)."'
                                              and nId='".$_POST['nGalId'][$i]."'") or die(mysqli_error($conn));
                                        }
                                        else { 
                                         mysqli_query($conn, "insert into " . TABLEPREFIX . "gallery (nUserId," . $_POST['gTableId'] . ",vImg,vDes,vSmlImg) values 
                                            ('" . $var_userid. "','" . $var_swapid . "','" . $txtPic . "',
                                            '" . addslashes($_POST['txtImgDesGal'][$i]) . "',
                                            '" . $txtSmallImage . "')") or die(mysqli_error($conn));
                                     }
                                }//end if
                            }//end if
                        }//end if
                    }//end for loop
                }//end if
                //update gallery stops here
                //update gallery contents
                if(is_array($_POST['txtImgDesGal'])) {
                    $k=0;
                    foreach ($_POST['txtImgDesGal'] as $val) {
                        //update into gallery table
                        mysqli_query($conn, "update ".TABLEPREFIX."gallery set vDes='".$val."'
                          where nSaleId='".addslashes($var_swapid)."'
                          and nId='".$_POST['nGalId'][$k]."'") or die(mysqli_error($conn));
                        $k++;
                    }//end foreach
                }//end if
                $gTableId='nSaleId';
            }//end else if

            // mysqli_query($conn, $sql_update);
            $update_flag=true;
        }//end if
        else {
            //new section added by sreebu
           if($var_source=="s" || $var_source=="w") 
              $gTableId='nSwapId'; 
          else if($var_source=="sa") 
           $gTableId='nSaleId';
               //end of new section
            //header("location:swapitemconfirm.php?mode=edit&flag=false");
        }//end else
    }//end if  
    
}//end if
//if the user is trying to view the details for edition/deletion
else {
    if ($_GET["source"]=="s") {
        //set the target file links to userwish
        $ref_file = "swap.php";

        //set the sql to retreive data from swap table where vPostType=wish
        $sql = "select nSwapId,S.nCategoryId,
        L.vCategoryDesc,S.nUserId,S.vPostType,
        CONCAT(CONCAT(U.vFirstName,'  '),U.vLastName) as UserName,
        S.vTitle,S.vBrand,S.vType,S.vCondition,
        S.vYear,S.nValue,S.nPoint,S.nShipping,S.vUrl,S.vDescription,S.dPostDate,S.vDelStatus,
        S.vSmlImg,S.vImgDes,S.nPoint from
        ".TABLEPREFIX."swap S,".TABLEPREFIX."users U,".TABLEPREFIX."category C,".TABLEPREFIX."category_lang L
        where  S.nUserId = U.nUserId
        AND S.nCategoryId = C.nCategoryId
        AND C.nCategoryId = L.cat_id
        AND nSwapId = '" . addslashes($_GET["swapid"]) . "'";
        $gTableId='nSwapId';
        $getId='swapid';
        $gCheckCond=" and nSaleId='0'";
    }//end if
    else if($_GET["source"]=="w") {
        //set the target file links to userwish
        $ref_file = "wish.php";

        //set the sql to retreive data from swap table where vPostType=wish
        $sql = "select nSwapId,S.nCategoryId,
        L.vCategoryDesc,S.nUserId,S.vPostType,'0' as 'nQuantity',
        CONCAT(CONCAT(U.vFirstName,'  '),U.vLastName) as UserName,
        S.vTitle,S.vBrand,S.vType,S.vCondition,
        S.vYear,S.nValue,S.nPoint,S.nShipping,S.vUrl,S.vDescription,S.dPostDate,S.vDelStatus,
        S.vSmlImg,S.vImgDes,S.nPoint from
        ".TABLEPREFIX."swap S
        left join ".TABLEPREFIX."users U on S.nUserId = U.nUserId
        left join ".TABLEPREFIX."category C on S.nCategoryId = C.nCategoryId
        LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
        where
        nSwapId = '" . addslashes($_GET["swapid"]) . "'";
        $gTableId='nSwapId';
        $getId='swapid';
        $gCheckCond=" and nSaleId='0'";
    }//end else if
    else if ($_GET["source"]=="sa") {
        //set the target file links to usersale
        $ref_file = "sale.php";

        //set the sql to retreive data from sale table
        $sql = "select nSaleId as 'nSwapId',S.nCategoryId,
        L.vCategoryDesc,S.nUserId,'sale' as 'vPostType',
        CONCAT(CONCAT(U.vFirstName,'  '),U.vLastName) as UserName,
        S.vTitle,S.vBrand,S.vType,S.vCondition,S.nQuantity,
        S.vYear,S.nValue,S.nPoint,S.nShipping,S.vUrl,S.vDescription,S.dPostDate,S.vDelStatus,
        S.vSmlImg,S.vImgDes from
        ".TABLEPREFIX."sale S
        left join ".TABLEPREFIX."users U on S.nUserId = U.nUserId
        left join ".TABLEPREFIX."category C on S.nCategoryId = C.nCategoryId
        LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
        where
        nSaleId = '" . addslashes($_GET["saleid"]) . "'";
        $gTableId='nSaleId';
        $getId='saleid';
        $gCheckCond=" and nSwapId='0'";
    }//end else if

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if(mysqli_num_rows($result)>0) { 
        if($row=mysqli_fetch_array($result)) {
            if ($row["vPostType"]=="swap") {
                $var_post_type="Swap Item";
                $var_title_url="Item For Swap";
            }//end if
            else if($row["vPostType"]=="wish") {
                $var_post_type="Wish Item";
                $var_title_url="Item Wished For";
            }//end else if
            else if($row["vPostType"]=="sale") {
                $var_post_type="Item For Sale";
                $var_title_url="Item For Sale";
            }//end else if

            $var_button_flag = ($row["vDelStatus"]=="0")?true:false;
            $var_post_date = date('m/d/Y H:i:s', strtotime($row["dPostDate"]));
            $var_swapid=$row["nSwapId"];
            $var_source=$_GET["source"];
            $var_url=$row["vUrl"];
            $var_category_desc=$row["vCategoryDesc"];
            $var_category_id=$row["nCategoryId"];
            $var_title=$row["vTitle"];
            $var_brand=$row["vBrand"];
            $var_type=$row["vType"];
            $var_condition=$row["vCondition"];
            $var_year=$row["vYear"];
            $var_value=$row["nValue"];
            $var_point=$row["nPoint"];
            $var_shipping=$row["nShipping"];
            $var_quantity=$row["nQuantity"];
            $var_description=$row["vDescription"];
            $txtSmallImage=$row["vSmlImg"];
            $txtImgDes=$row["vImgDes"];
            $var_command="";
            $var_userid = $row["nUserId"];
            $typeGall = ($_GET["source"]=="sa") ? 'nSaleId' : 'nSwapId';
            $imgArr = getGalleryImages($var_swapid, $typeGall);            
        }//end if
    }//end if
}//end if
?>
<script language="javascript" type="text/javascript">
    var Parent=new Array;
    <?php
    $parentsql = "select distinct nParentId from ".TABLEPREFIX."category";
    $result = mysqli_query($conn, $parentsql);
    $count=0;
    $disp="";
    if(mysqli_num_rows($result)!= 0) {
        while($row = mysqli_fetch_array($result)) {
            $disp .="Parent[$count]=\"";
            $disp .=$row["nParentId"];
            $disp .="\";\n";
            $count=$count+1;
    }//end while
}//end if
echo $disp;
?>

function clickUpdate()
{
    if(validate())
    {
        document.frmSwapItem.command.value='update';
        document.frmSwapItem.submit();
        }//end if
    }//end function

    function clickDelete()
    {
        if(confirm("Are you sure to delete?")){
            document.frmSwapItem.command.value='delete';
            document.frmSwapItem.submit();
        }
        else{
            return false;
        }
    }

    function validate()
    {
        var frm = window.document.frmSwapItem;
        if(trim(frm.cat_id.options[frm.cat_id.options.selectedIndex].value)=="")
        {
            alert("Category cannot be empty.");
            frm.cat_id.focus();
            return false;
        }//end if
        else if(frm.txtTitle.value=="")
        {
            alert("Title cannot be empty.");
            frm.txtTitle.focus();
            return false;
        }//end else if
        <?php
/*if($enable_pt!='0') {
    ?>
                        else if((trim(frm.txtPoint.value) =="")||(trim(frm.txtPoint.value) == "0"))
                        {
                            alert("<?php echo POINT_NAME;?> cannot be empty or zero.");
                            frm.txtPoint.focus();
                            return false;
                        }
    <?php
}//end if*/
if($enable_pt!='1' && $_REQUEST["source"]!="w") {
    ?>
    else if((trim(frm.txtValue.value) =="")||(trim(frm.txtValue.value) == "0"))
    {
        alert("Price cannot be empty or zero.");
        frm.txtValue.focus();
        return false;
    }
    <?php
}//end else
?>
else if(frm.txtDescription.value=="")
{
    alert("Description cannot be empty.");
    frm.txtDescription.focus();
    return false;
                        }//end else if
                        <?php
                        if($_GET["source"]=="sa" || $_POST["source"]=="sa") {
                            ?>
                            else if(frm.txtQuantity.value < 0)
                            {
                                alert("Quantity cannot be negative.");
                                frm.txtQuantity.focus();
                                return false;
            }//end else if
            <?php
}//end if
?>
return true;
        }//end fucntion

        function setcatValue()
        {
            selvalue=document.frmSwapItem.cat_id.options[document.frmSwapItem.cat_id.options.selectedIndex].value;
            flag="false";
            for(i=0;i<Parent.length;i++)
            {
                if(Parent[i]==selvalue)
                {
                    flag="true";
                }//end if
            }//end for loop

            if(flag=="true")
            {
                document.frmSwapItem.cat_id.value=document.frmSwapItem.txtCategory.value;
                alert("There is a subcategory under the category you have selected.\nPlease select a subcategory!");
            }//end if
        }//end function

        function checkValue(t)
        {
            if(isNaN(t.value) || t.value.substring(0,1)==" " || t.value.length==0 || parseFloat(t.value) < 0 )
            {
                t.value=0;
            }//end if
        }//end function
        
        function checkNumeric(ids)
        {
            
            var val=document.getElementById(ids).value;
       // var pointValue=document.getElementById(pointValue).value;
       // alert(pointValue);

       if ((isNaN(val))||(val<0)||(parseInt(val,10)<0))
       {
        alert("<?php echo ERROR_POINT_POSITIVE_VALUE; ?>");
        document.getElementById(ids).value="0";
        document.getElementById(ids).focus();
        }//end if
    }//end function
    
</script>
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="../js/lightbox_admin.js"></script>
<link rel="stylesheet" href="../styles/lightbox.css" type="text/css" media="screen" />

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
                                <td width="100%" class="heading_admn boldtextblack" align="left"><?php echo $var_title_url?></td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                            <tr>
                                <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td  class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="5" class="maintext2">
                                            <form enctype="multipart/form-data" name="frmSwapItem" method="POST" action = "">
                                                <?php if(isset($message) && $message!='') {
                                                    ?>
                                                    <tr bgcolor="#FFFFFF">
                                                        <td colspan="3" align="center" class="warning"><?php echo $message;?></td>
                                                    </tr>
                                            <?php  }//end if
                                            if(isset($var_mesg) && $var_mesg!='') {
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td colspan="3" align="center" class="warning"><?php echo $var_mesg;?></td>
                                                </tr>
                                                <?php }//end if?>
                                                <?php if($_GET["source"] != "w" && $_POST["source"] != "w") { ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left" valign="top">Picture</td>
                                                    <td align="left" valign="top"><input type="File" NAME="txtUrl" id="txtUrl" class="textbox2"><br>
                                                        <b>Image Description</b><br>
                                                        <textarea name="txtImgDes" id="txtImgDes" cols="32" ROWS="4" class="textbox2"><?php echo htmlentities($txtImgDes)?></textarea>
                                                        
                                                    </td>
                                                    <!-- New td added -->
                                                <?php // if($_GET["source"] != "w" && $_POST["source"] != "w") {
                                                    if (trim($var_url)=="") {
                                                        $var_url="pics/nophoto.gif";
                                                        ?>
                                                        <td id="main_image_sec" align="center" background="../images/bg2.gif"><a href="../<?php echo $var_url?>" rel="lightbox"><img src="<?php echo "../" . $var_url?>" width="75" height="75" border="0"></a>
                                                            <?php    
                                                        }else{
//end if
                                                            ?>
                                                            <td id="main_image_sec" align="center" background="../images/bg2.gif"><a href="../<?php echo $var_url?>" rel="lightbox"><img src="<?php echo "../" . $var_url?>" width="75" height="75" border="0"></a><br><a id="main_image" style="color:#FF0000;" href="#"> <img width="15" height="15" src="../images/delete_icon.png"> ( Delete Image )  </a></td>
                                                            <?php } ?>    
                                                            <?php //if($_REQUEST["source"] != "w" ) { ?>
                                                            <!--<td id="main_image_sec" align="center" background="../images/bg2.gif"><a href="../<?php //echo $var_url?>" rel="lightbox"><img src="<?php //echo "../" . $var_url?>" width="75" height="75" border="0"></a><br><a id="main_image" style="color:#FF0000;" href="#"> <img width="15" height="15" src="../images/delete_icon.png"> ( Delete Image )  </a></td> -->
                                                            <?php //} ?>
                                                            <!-- End of td -->
                                                            
                                                        </tr>
                                                        <?php }//end if?>
                                                        <tr bgcolor="#FFFFFF"><input type="hidden" name="swapid" id="swapid" value="<?php echo $var_swapid; ?>">
                                                            <input type="hidden" name="source" id="source" value="<?php echo $var_source; ?>">
                                                            <input type="hidden" name="command" id="command" value="">
                                                            <input type="hidden" name="var_url" value="<?php echo $var_url?>">
                                                            <input TYPE="hidden" NAME="txtPostType" id="txtPostType" VALUE="<?php echo $var_post_type?>">
                                                            <input type="hidden" name="txtCategory" id="txtCategory" value="<?php echo $var_category_id; ?>" >
                                                            <input type="hidden" value="<?php echo $gTableId;?>" name="gTableId">
                                                            <input type="hidden" value="<?php echo $var_userid;?>" name="pro_userid">
                                                            <td align="left">Posted On</td>
                                                            <td width="51%" align="left"><input type="text" name="txtPostDate" id="txtPostDate" value="<?php echo htmlentities($var_post_date); ?>" class="textbox2"></td>
                                            <!-- <td width="29%" rowspan="13" align="center" valign="top"><table width="100" height="100" border="0" cellpadding="10" cellspacing="0">-->   <?php // if($_GET["source"] != "w" && $_POST["source"] != "w") {
                                            /*if (trim($var_url)=="") {
                                                $var_url="pics/nophoto.gif";
                                            }//end if*/
                                            ?>
                                            <?php //if($_REQUEST["source"] != "w" ) { ?>
                                                    <!--<tr>
                                                        <td align="center" background="../images/bg2.gif"><a href="../<?php //echo $var_url?>" rel="lightbox"><img src="<?php //echo "../" . $var_url?>" width="75" height="75" border="0"></a></td>
                                                    </tr> -->
                                                    
                                                    <?php //} ?>
                                                    
                                                    <?php // }//end if?>
                                                    
                                                    <?php
                                                //if(!empty($imgArr)){
                                                    ?>
                                                    
                                                    <?php
                                                /*foreach($imgArr as $imgItem){

                                                if (trim($var_url)=="") {
                                                    $var_url="pics/nophoto.gif";
                                                }else {
                                                    $var_url=$imgItem['vImg'];
                                                }*/

                                                ?>
                                                  <!--  <tr>
                                                        <td align="center" background="../images/bg2.gif"><a href="../<?php //echo $var_url?>" rel="lightbox"><img src="<?php //echo "../" . $var_url?>" width="75" height="75" border="0"></a></td>
                                                    </tr> -->
                                                    <?php
                                                //}
                                                    ?>
                                                    
                                                    
                                                    <?php
                                                //}
                                                    ?>
                                             <!-- </table>
                                         </td> -->
                                     </tr>
                                     <tr bgcolor="#FFFFFF">
                                        <td align="left">Category</td>
                                        <td align="left"><select id="cat_id" name="cat_id" onChange="setcatValue();" class="textbox2">
                                            <option value="">-- Select One -- </option>
                                            <?php
                                            $get_options =    make_selectlist(0, 0);
                                            if (count($get_options) > 0) {
                                                $categories = $_POST['cat_id'];
                                                foreach ($get_options  as $key => $value) {
                                                    $options .="<option value=\"$key\"";
                                                    if ($_POST['cat_id']=="$key") {
                                                        $options .=" selected=\"selected\"";
                                                    }


                                                    $options .=">$value</option>\n";
                                                }
                                            }
                                            echo $options;
                                            ?>
                                        </select></td>
                                    </tr>
                                    <tr bgcolor="#FFFFFF">
                                        <td align="left">Title</td>
                                        <td align="left"><input name="txtTitle" type="text" class="textbox2" id="txtTitle" value="<?php echo htmlentities($var_title); ?>" size="32" maxlength="100"></td>
                                    </tr>
                                    <tr bgcolor="#FFFFFF">
                                        <td align="left">Brand</td>
                                        <td align="left"><input name="txtBrand" type="text" class="textbox2" id="txtBrand" value="<?php echo htmlentities($var_brand); ?>" size="32" maxlength="100"></td>
                                    </tr>
                                    <tr bgcolor="#FFFFFF">
                                        <td align="left">Type</td>
                                        <td align="left"><select name="txtType" class="textbox2">
                                            <option value='New'>New</option>
                                            <option value='Used'>Used</option>
                                        </select></td>
                                    </tr>
                                    <tr bgcolor="#FFFFFF">
                                        <td align="left">Condition</td>
                                        <td align="left"><select name="txtCondition" class="textbox2">
                                            <option value='New'>New</option>
                                            <option value='Like New'>Like New</option>
                                            <option value='Very good'>Very good</option>
                                            <option value='Good'>Good</option>
                                        </select></td>
                                    </tr>
                                    <tr bgcolor="#FFFFFF">
                                        <td align="left">Year</td>
                                        <td align="left"><select name="txtYear" id="txtYear" class="textbox2">
                                            <option value="">Select Year</option>
                                            <?php
                                            for($i=1900; $i<=2050; $i++) {
                                                $showCheckd='';
                                                if($i==$var_year) {
                                                    $showCheckd='selected';
                                                    }//end if
                                                    echo '<option value="'.$i.'" '.$showCheckd.'>'.$i.'</option>';
                                                }//end for loop
                                                ?>
                                            </select></td>
                                        </tr>
                                        <?php
                                                //checking point stats
                                        if($enable_pt!='0' && $var_source!="w") {

                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left"><?php echo POINT_NAME;?> </td>
                                                <td align="left"><input type="text" class="textbox2" name="txtPoint"  id="txtPoint" onChange="checkNumeric(this.id)" size="5" maxlength="10" value="<?php echo htmlentities(stripslashes($var_point))?>" />
                                                </td>
                                            </tr>
                                            <?php
                                            }//end if
                                            //checking point enable in website
                                            if($enable_pt!='1' && $var_source!="w") {
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left">Price <span class="warning">*</span></td>
                                                    <td align="left"><input type="text" name="txtValue" id="txtValue" value="<?php echo $var_value; ?>" onBlur="javascript:checkValue(this);"  class="textbox2"  size="32" maxlength="10"></td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left">Shipping Charge </td>
                                                    <td align="left"><input type="text" name="txtShipping" id="txtShipping" value="<?php echo $var_shipping; ?>" onBlur="javascript:checkValue(this);"  class="textbox2"  size="32" maxlength="10"></td>
                                                </tr>
                                                <?php
                                            }//end point check if
                                            if($_GET["source"]=="sa" || $_POST["source"]=="sa") { ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Quantity</td>
                                                <td align="left" valign="top"><input TYPE="text" NAME="txtQuantity" id="txtQuantity" VALUE="<?php echo $var_quantity ?>" onBlur="javascript:checkValue(this);"  class="textbox2"  size="32" maxlength="5"></td>
                                            </tr>
                                            <?php }//end if?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" valign="top">Item Description</td>
                                                <td align="left" valign="top"><textarea name="txtDescription" id="txtDescription"  cols="32" ROWS="4" class="textbox2" style="height:100px"><?php echo htmlentities($var_description); ?></textarea></td>
                                            </tr>
                                            <?php
                                            if($_GET['source']!='w' && $_POST['source']!='w') {
                                                ?>
                                           <!-- <tr bgcolor="#FFFFFF">
                                                <td align="left" valign="top">Image Description</td>
                                                <td align="left" valign="top"><textarea name="txtImgDes" id="txtImgDes" cols="32" ROWS="4" class="textbox2"><?php //echo htmlentities($txtImgDes)?></textarea></td>
                                            </tr> -->
                                            <?php 
                                            $chechId=($_GET[$getId]!='')?$_GET[$getId]:$var_swapid;
                                            $gTableId=($gTableId!='')?$gTableId:$_POST['gTableId'];
                                                /*echo "select * from ".TABLEPREFIX."gallery where $gTableId='".addslashes($chechId)."'
                                                and vDelStatus='0' ".$gCheckCond; */
                                                $arrImages=mysqli_query($conn, "select * from ".TABLEPREFIX."gallery where $gTableId='".addslashes($chechId)."'
                                                    and vDelStatus='0' ".$gCheckCond) or die(mysqli_error($conn));
                                                if(mysqli_num_rows($arrImages)>0) {
                                                    echo ' <tr bgcolor="#FFFFFF">
                                                    <td colspan="3" align="left" class="subheader">More Images</td>
                                                </tr>';
                                                $k=0;$m=1;
                                                while($arr=mysqli_fetch_array($arrImages)) {
                                                    ?>
                                                    <tr bgcolor="#FFFFFF">
                                                        <td align="left" valign="top">Image</td>
                                                        <td align="left" valign="top"><input type="file" name="txtPic[]" class="textbox2"><br>
                                                            <b>Description</b><br>
                                                            <textarea name="txtImgDesGal[]" cols="32" ROWS="4" class="textbox2"><?php echo $arr['vDes'];?></textarea>
                                                            <input type="hidden" name="nGalId[]" value="<?php echo $arr['nId'];?>">
                                                        </td>
                                                        <td align="left" valign="top" id="sub_image_sec_<?php echo $arr['nId'];?>">
                                                            <?php if($arr['vSmlImg']!="") { ?>
                                                            <table width="100" height="100" border="0" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td align="center" background="../images/bg2.gif"><a href="../<?php echo $arr['vImg'];?>" rel="lightbox"><img src="../<?php echo $arr['vSmlImg'];?>" width="75" height="75" border="0"></a></td>
                                                                </tr>
                                                                
                                                                <tr> <td valign="top" colspan="2"><a id="<?php echo $arr['nId'];?>" href="#" class="delete" style="color:#FF0000;"> <img width="15" height="15" src="../images/delete_icon.png"> (  Delete Image )  </a></td> </tr>
                                                            </table>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $k++;$m++;
                                                                }//end while
                                                                
                                                                
                                                            }//end if
                                                            else
                                                                $m= 1;
                                                            while($m<=($max_additional_imgs-1)) {
                                                                ?>
                                                                <tr bgcolor="#FFFFFF">
                                                                    <td align="left" valign="top">Image</td>
                                                                    <td align="left" valign="top"><input type="file" name="txtPic[]" class="textbox2"><br>
                                                                        <b>Description</b><br>
                                                                        <textarea name="txtImgDesGal[]" cols="32" ROWS="4" class="textbox2"><?php echo $arr['vDes'];?></textarea>
                                                                        <input type="hidden" name="nGalId[]" value="">
                                                                    </td>
                                                                    
                                                                </tr>
                                                                <?php
                                                                $m++;
                                                            }
                                                        }//end if
                                                        ?>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td align="left">&nbsp;</td>
                                                            <td colspan="2" align="left"><input type="button" name="Update" value="Update" onClick="javascript:clickUpdate();" class="submit" <?php echo ($var_button_flag==false)?"disabled":"";?>>
                                                                <input type="button" name="Delete" value="Delete" onClick="javascript:clickDelete();"  class="submit_grey" <?php echo ($var_button_flag==false)?"disabled":"";?>></td>
                                                            </tr>
                                                        </form>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div>
                <script LANGUAGE="javascript">
                    <?php
//echo phpinfo();

                    echo("document.frmSwapItem.cat_id.value='$var_category_id';");
                    echo("document.frmSwapItem.txtType.value='$var_type';");
                    echo("document.frmSwapItem.txtCondition.value='$var_condition';");

                    if($del_flag==true) {
                        echo("alert('The selected item has been deleted');");
                        echo("document.location.href='". $ref_file .  "'");
                    }
                    elseif($update_flag==true) {
                        echo("alert('The selected item has been updated');");
                        echo("document.location.href='". $ref_file .  "'");
                    }
                    ?>   

                </script>

                <script type="text/javascript">
                 var jqr = jQuery.noConflict();
                 jqr(document).ready(function()
                 {
                  jqr('#main_image').click(function(e)
                  {
                    
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
                    url: "../delete_image.php",
                    data: data,
                    cache: false,
                    
                    success: function()
                    {  
                       jqr('#main_image_sec').remove();
                       alert ('<?php echo IMAGE_DELETED_SUCCESS?>');
							//parent.fadeOut('slow', function() {$(this).remove();});
                        }
                    });				
			}
		});
                  
                  
                  jqr('.delete').click(function(e)
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
                                    url: "../delete_image.php",
                                    data: data,
                                    cache: false,
                                    
                                    success: function()
                                    {  
                                               //alert('.jQTable_'+delid);
                                               jqr('#sub_image_sec_'+delid).remove();
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
<?php include_once('../includes/footer_admin.php');?>