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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

$flag_to_continue = false;

$transstatus = $_REQUEST['transStatus'];

if (isset($_REQUEST['transStatus']) && $_REQUEST['transStatus'] == 'Y') {
    $flag_to_continue = true;
}//end if

$message = "";
$flag = false;

if ($flag_to_continue == true) {

    if (DisplayLookUp('worldpaydemo') == "YES") {
        $txnid = 'TEST-' . time();
    }//end if
    else {
        $txnid = time();
    }//end if

    $var_swapid = $_SESSION['sess_swapid'];
    $var_tmpid = $_SESSION["gstempid"];

//////////////////////////////////////////////////////////////////////////////////////
//check if txnid alredy there to prevent refresh
    $sql3 = "Select nTempId from " . TABLEPREFIX . "swaptemp where nTempId='$var_tmpid'";
    $result3 = mysqli_query($conn, $sql3) or die(mysqli_error($conn));
    if (mysqli_num_rows($result3) > 0) {
        $sql = "Select vTxnId from " . TABLEPREFIX . "swap where vTxnId='" . addslashes($txnid) . "' AND vMethod='wp'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) <= 0) {
            //Start of the process of performing the transaction entry
            $db_swapid = "";
            $db_userid = "";
            $db_amount = 0;
            $db_method = "";
            $db_mode = "";
            $db_post_type = "";

            //here the transaction id has to be set that comes from the payment gateway
            $var_txnid = $txnid;


            $sql = "Select st.nTempId,st.nSwapId,st.nUserId,st.nAmount,st.vMethod,st.vMode,st.vPostType,st.dDate,s.vTitle
                                        from " . TABLEPREFIX . "swaptemp st inner join  " . TABLEPREFIX . "swap s on st.nSwapId=s.nSwapId where st.nTempId='" . addslashes($var_tmpid) . "' AND s.vSwapStatus='1' ";

            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

            if (mysqli_num_rows($result) > 0) {
                if ($row = mysqli_fetch_array($result)) {
                    //if you have data for the transaction
                    $var_title = $row["vTitle"];
                    $db_swapid = $row["nSwapId"];
                    $db_userid = $row["nUserId"];
                    $db_amount = $row["nAmount"];
                    $db_method = $row["vMethod"];
                    $db_mode = $row["vMode"];
                    $db_post_type = $row["vPostType"];
                    $var_swapmember = "";
                    $var_incmember = "";

                    if ($db_mode == "od") {
                        //if the payment is being made by the person who made the offer
                        //that means the present userid is the one that is present in the swaptxn table
                        //and this user is giving money to the person who made the swap table entry
                        //and the userid is fetched from the table swap
                        //swapmember --> the one in the temporary table
                        //incmember --> the one who receives the money(comes from the swap table)
                        $var_swapmember = $db_userid;

                        $sql = "Select nUserId from " . TABLEPREFIX . "swap where nSwapId='"
                                . $db_swapid . "' ";
                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        if (mysqli_num_rows($result) > 0) {
                            if ($row = mysqli_fetch_array($result)) {
                                $var_incmember = $row["nUserId"];
                            }//end if
                        }//end if
                    }//end if
                    else if ($db_mode == "om") {
                        //if the payment is being made by the person who accepts the offer(ie. the one who
                        //made the main swap item),here the userid is the one in the swap table,hence
                        //he has to fetch the swapuserid from the swaptxn table,and give money to him
                        //swapmember --> the one in the swaptxn table
                        //incmember --> the one who receives the money(comes from the swaptxn table)

                        $db_amount = -1 * $db_amount;

                        $sql = "Select nUserId from " . TABLEPREFIX . "swaptxn where nSwapId='"
                                . $db_swapid . "' and vStatus='A'";
                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        if (mysqli_num_rows($result) > 0) {
                            if ($row = mysqli_fetch_array($result)) {
                                $var_swapmember = $row["nUserId"];
                                $var_incmember = $row["nUserId"];
                            }//end if
                        }//end if
                    }//end else if
                    else if ($db_mode == "wm") {
                        //if the payment is being made by the person who accepts the offer(ie. the one who
                        //made the main swap item),here the userid is the one in the swap table,hence
                        //he has to fetch the swapuserid from the swaptxn table,and give money to him
                        //swapmember --> the one in the swaptxn table
                        //incmember --> the one who receives the money(comes from the swaptxn table)

                        $db_amount = -1 * $db_amount;

                        $sql = "Select nUserId from " . TABLEPREFIX . "swaptxn where nSwapId='"
                                . $db_swapid . "' and vStatus='A'";
                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        if (mysqli_num_rows($result) > 0) {
                            if ($row = mysqli_fetch_array($result)) {
                                $var_swapmember = $row["nUserId"];
                                $var_incmember = $row["nUserId"];
                            }//end if
                        }//end if
                    }//end else if
                    
                    $db_swap_ids = get_swaps_ids($db_swapid);
                    
                    $db_amount = ($db_amount < 0) ? (-1 * $db_amount) : $db_amount;
                    
                    $sql = "Update " . TABLEPREFIX . "swap set 
                                                     nSwapAmount='$db_amount',
                                                     vEscrow='1',

                                                     vMethod='$db_method',
                                                     vTxnId='$var_txnid',
                                                     vSwapStatus='2',dTxnDate=now() where
                                                     nSwapId in (" . $db_swap_ids . ") ";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));//nSwapMember='$var_swapmember',

                    

//                    $sql = "Update " . TABLEPREFIX . "users set nAccount=nAccount + $db_amount
//                                                                          where nUserId='" . $var_incmember . "' ";
//                    mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    $sql = "delete from " . TABLEPREFIX . "swaptemp where nTempId='"
                            . addslashes($var_tmpid) . "' ";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    $_SESSION["gstempid"] = "";
                    $flag = true;

                    $message = MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU;
                }//end if
            }//end if
            else {
                $message = str_replace('{site_email}',SITE_EMAIL,ERROR_TRANSACTION_ALREADY_PERFORMED_CONTACT_EMAIL);
            }//end else
            //End of the process of performing the transaction entry
        }//end if
        else {
            $message = str_replace('{site_email}',SITE_EMAIL,MESSAGE_CHECK_PAYMENT_FURTHER_CONTACT_EMAIL);
        }//end else
    }//end if
    else { // if this transaction has already been done by ipn
        $sql = "Select vTitle,nSwapAmount,vPostType from " . TABLEPREFIX . "swap where nSwapId='" . addslashes($var_swapid) . "' AND vSwapStatus IN ('2','3')";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) > 0) {
            if ($row = mysqli_fetch_array($result)) {
                $var_title = $row["vTitle"];
                $db_amount = abs($row["nSwapAmount"]);
                $db_post_type = $row["vPostType"];
                $flag = true;
                $_SESSION["gstempid"] = "";
                $_SESSION['sess_swapid'] = '';
                $message = MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU;
            }//end if
        }//end if
        else {
            $message = str_replace('{site_email}',SITE_EMAIL,MESSAGE_CHECK_PAYMENT_FURTHER_CONTACT_EMAIL);
        }//end else
    }//end if
}//end if
else {
    $message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
}//end else

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="headerbg"><?php require_once("./includes/header.php"); ?>
                <?php require_once("menu.php"); ?>
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="10%" height="688" valign="top"><?php include_once ("./includes/usermenu.php"); ?>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td id="leftcoloumnbtm"></td>
                                            </tr>
                                        </table></td>
                                    <td width="74%" valign="top">
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td class="link3">&nbsp;</td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="heading" align="left"><?php echo HEADING_PAYMENT_STATUS; ?></td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                                <?php
                                                                if (isset($flag) && $flag == false) {
                                                                    ?>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td colspan="2" align="center" class="warning"><?php echo $message; ?></td>
                                                                        </tr>
                                                                        <?php
                                                                        }//end if
                                                                        else {
                                                                            ?>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td colspan="2" align="left" class="subheader"><?php echo HEADING_TRANSACTION_DETAILS; ?>  </td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td width="20%" align="left"><?php echo TEXT_TITLE; ?></td>
                                                                            <td width="80%" align="left"><?php echo  $var_title ?></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td align="left"><?php echo TEXT_AMOUNT; ?></td>
                                                                            <td align="left"><?php echo CURRENCY_CODE; ?><?php echo  $db_amount ?></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td align="left"><?php echo TEXT_TYPE; ?></td>
                                                                            <td align="left"><?php echo  ($db_post_type == "swap") ? TEXT_SWAP_TRANSACTION : TEXT_WISH_TRANSACTION ?></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td colspan="2" align="center"><b><?php echo  $message ?></b></td>
                                                                        </tr>
                                                                        <?php
                                                                    }//end else
                                                                    ?>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                        </table>
										<?php include('./includes/sub_banners.php'); ?>
                                    </td>
                                </tr>
                            </table></td>
                    </tr>
                </table>
<?php require_once("./includes/footer.php"); ?>