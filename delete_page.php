<?php
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/session_check.php");
include ("./includes/httprefer_check.php");
include_once('./includes/gpc_map.php');

$var_source         =   $_REQUEST['source'];
$var_category_id    =   $_REQUEST['catid'];
$var_swapid         =   $_REQUEST['delete_id'];
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
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
            $var_message = 'success';
        }//end if
        else
        {
            $var_message = 'failure';
        }
       
  echo   $var_message;    
        
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
?>