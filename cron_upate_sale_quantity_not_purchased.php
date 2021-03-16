<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include_once('./includes/gpc_map.php');

//checking any sale details page without payment
$sqlSaleCheck = mysqli_query($conn, "select nSaleId,nQuantity from " . TABLEPREFIX . "saledetails where vMethod is null and dTxnDate is null") or die(mysqli_error($conn));
if (mysqli_num_rows($sqlSaleCheck) > 0) {
    while ($arr = mysqli_fetch_array($sqlSaleCheck)) {
        //back to added quantity to each row
        mysqli_query($conn, "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity + " . $arr['nQuantity'] . " where nSaleId ='" . addslashes($arr['nSaleId']) . "'") or die(mysqli_error($conn));
    }//end while loop
    //delete records
    mysqli_query($conn, "delete from " . TABLEPREFIX . "saledetails where vMethod is null and dTxnDate is null") or die(mysqli_error($conn));
}//end if
?>