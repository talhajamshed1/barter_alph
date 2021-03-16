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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
include_once('./includes/gpc_map.php');
// O - On Progress    N - Invalid   R - Rejected   A - Accepted
$message = "";
$var_flag = false;
$var_pflag = false;
$var_update_flag = false;
$var_error_message = "";
$var_mpay = "";
$var_hpay = "";
$var_description = "";
$var_swapid = "";
if ($_SESSION['msg']!='') {
    $message = $_SESSION['msg'];
    $_SESSION['msg'] = '';
}
//print_r($_SESSION);exit();
$_SESSION['gadminid'] = '';
if ($_SESSION['gadminid']!='' && trim($_REQUEST['this_user'])!=''){//for admin view
    $this_user = $_REQUEST['this_user'];
}
else {//for the main site users
    $this_user = $_SESSION['guserid'];
    include ("./includes/session_check.php");
}


$lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
$langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
$langRw = mysqli_fetch_array($langRs);

if ($_REQUEST["swapid"] != "") {
    $var_swapid = $_REQUEST["swapid"];
}
//Login section
/*if (isset($_POST["btnLogin"]) && $_POST["btnLogin"] != "") {
    $var_swapid = $_POST["swapid"];

    $txtUserName = $_POST["txtUserName"];
    $txtPassword = $_POST["txtPassword"];

    $txtUserName = addslashes($txtUserName);
    $sqluserdetails = "SELECT nUserId, vEmail,vStatus  FROM " . TABLEPREFIX . "users WHERE vLoginName = '$txtUserName' AND vPassword = '" . md5($txtPassword) . "' ";
    $resultuserdetails = mysqli_query($conn, $sqluserdetails) or die(mysqli_error($conn));
    if (mysqli_num_rows($resultuserdetails) != 0) {
        $row = mysqli_fetch_array($resultuserdetails);
        if ($row["vStatus"] == "0") {
            $_SESSION["guserid"] = $row["nUserId"];
            $_SESSION["guseremail"] = $row["vEmail"];
            $_SESSION["gloginname"] = stripslashes($txtUserName);
            $var_flag = true;
        }
        else {
            $message = ERROR_ACCESS_DENIED_CONTACT_EMAIL." <a href=\"mailto:" . SITE_EMAIL . "\">" . SITE_EMAIL . "</a>";
            $var_flag = false;
        }
    }
    else {
        $message = ERROR_INVALID_USERNAME_PASSWORD;
        $var_flag = false;
    }
}*/
//End of login section

$nSTId = $_REQUEST["nSTId"];//primary key
$other_user = $_REQUEST["userid"];
$post_type = $_REQUEST["post_type"];//wish or swap
//if ($var_flag == true) {
$var_swapid = $_REQUEST["swapid"];
$var_swap_id = $_POST["chkSwap_hidden"];
$var_swap_user_id = $_POST["chkSwap_user_hidden"];
$var_mpay = $_POST["txtMpay"];
$var_hpay = $_POST["txtHpay"];
$var_mpoint = $_POST["txtMpoint"];
$var_hpoint = $_POST["txtHpoint"];
//if (get_magic_quotes_gpc())
//    echo $var_description = $_POST["txtAdditional"];
//else
$var_description = addslashes($_POST["txtAdditional"]);
$var_other_user = $_POST["other_user"];
$parent_id = $_REQUEST["parent_id"];//parent offer
/* echo "<pre>";
print_r($_POST);
echo "</pre>";
exit();*/

if ($_POST["postback"] == "Y") {//add

    $counter_offer  = 'N';
    if($_REQUEST['counter_offer']=='Y')
    {
        $counter_offer  =   'Y';
    }

    if ($parent_id!=0){
        $wishsql = "Select wishedId from " . TABLEPREFIX . "swaptxn where nSTId ='$parent_id'";
        $wishRs = mysqli_query($conn, $wishsql) or die(mysqli_error($conn));
        $wishRw = mysqli_fetch_array($wishRs);
        $wishedId   = $wishRw['wishedId'];

    }

    $wishid = $post_type =='wish' ? ($_REQUEST['swapid'] ? $_REQUEST['swapid'] : $wishedId) : '';

    $sql = "INSERT INTO ".TABLEPREFIX."swaptxn (nSwapId, nSwapReturnId, nUserId, nUserReturnId, nAmountGive, nAmountTake, 
    nPointGive, nPointTake, nParentId, vPostType, vStatus, dDate, vText, wishedId) VALUES 
    ('".$var_swap_id."', '".$var_swap_user_id."', '".$_SESSION["guserid"]."', '".$var_other_user."', '".$var_mpay."', 
    '".$var_hpay."', '".$var_mpoint."', '".$var_hpoint."', '".$parent_id."', '".$post_type."', 'O', now(), 
    '".$var_description."', '".$wishid."')";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
    //exit;
    // send email notification to other user

    if($counter_offer=="N"){
        $mailRw = array();
        $mailSql = "SELECT L.content,L.content_title
        FROM ".TABLEPREFIX."content C
        JOIN ".TABLEPREFIX."content_lang L
        ON C.content_id = L.content_id
        AND C.content_name = 'newofferReceived'
        AND C.content_type = 'email'
        AND L.lang_id = '".$_SESSION["lang_id"]."'";

        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $condition = "where nUserId='" . $var_other_user . "'";
        $UserName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');

        $UserEmail  =  getUserEmail($var_other_user);
        $login_username = ucfirst($_SESSION["gloginname"]);
        $mainTextShow   = $mailRw['content'];
        $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{receiver_user_name}");
        $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($login_username));
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

        $mailcontent1   = $mainTextShow;

        $subject    = $mailRw['content_title'];
        $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
        $StyleContent=MailStyle($sitestyle,SITE_URL);
        $EMail = $UserEmail; 



        //readf file n replace
        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, ucfirst($UserName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);

        $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');

        $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

        send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
    }
       // echo $counter_offer;exit;

    if($counter_offer=="Y"){
        $mailRw = array();
        $mailSql = "SELECT L.content,L.content_title
        FROM ".TABLEPREFIX."content C
        JOIN ".TABLEPREFIX."content_lang L
        ON C.content_id = L.content_id
        AND C.content_name = 'counterOfferReceived'
        AND C.content_type = 'email'
        AND L.lang_id = '".$_SESSION["lang_id"]."'";
            //echo $mailSql;exit;
        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $condition = "where nUserId='" . $var_other_user . "'";
        $UserName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');

        $UserEmail  =  getUserEmail($var_other_user);
        $login_username = ucfirst($_SESSION["gloginname"]);
        $mainTextShow   = $mailRw['content'];
            //echo $mainTextShow;exit;
        $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{receiver_user_name}");
        $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($login_username));
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

        $mailcontent1   = $mainTextShow;

        $subject    = $mailRw['content_title'];
        $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
        $StyleContent=MailStyle($sitestyle,SITE_URL);
        $EMail = $UserEmail; 


        //readf file n replace
        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, ucfirst($UserName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);

        $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');

        $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

        //echo $subject.'<br />'.$EMail.'<br />'.$msgBody;exit;
        //send_mail('nirmala.v@armiasystems.com', $subject, $msgBody, SITE_EMAIL, 'Admin');

        send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
    }
    if ($parent_id!=0){
        $sql_up = "Update " . TABLEPREFIX . "swaptxn set vStatus = 'N' where nSTId='".$parent_id."' and vStatus!='A'";
            mysqli_query($conn, $sql_up) or die(mysqli_error($conn));//invalidating the parent offer
        }

        header('location:makeofferconfirm.php?mode=add&flag=true&swapid=' . $var_swapid);
        exit();
    }
    else if ($_POST["postback"] == "E") {//edit
        $sql = "update ".TABLEPREFIX."swaptxn set 
        nSwapId = '".$var_swap_id."',
        nSwapReturnId = '".$var_swap_user_id."',
        nAmountGive = '".$var_mpay."',
        nAmountTake = '".$var_hpay."',
        nPointGive = '".$var_mpoint."',
        nPointTake = '".$var_hpoint."',
        dDate = now(),
        vText = '".$var_description."'
        where nSTId='" . $nSTId . "' AND nUserId= '" . $_SESSION["guserid"] . "' 
        ";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        header("location:makeofferconfirm.php?mode=edit&flag=true");
        exit();
    }
    else if ($_POST["postback"] == "D") {//delete
        $sql = "Delete from " . TABLEPREFIX . "swaptxn where nSTId='" . $nSTId . "' AND nUserId= '" . $_SESSION["guserid"] . "' ";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        header("location:makeofferconfirm.php?mode=delete&flag=true");
        exit();
    }
    else if ($_POST["postback"] == "A") {//accepted
        $sql = "SELECT * from " . TABLEPREFIX . "swaptxn where nSTId='" . $nSTId . "'";
        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if ($srow = mysqli_fetch_array($res)){
            $nSwapId_array = explode(',',$srow['nSwapId']);
            $nSwapId_user_array = explode(',',$srow['nSwapReturnId']);
            
            $points = ($srow['nPointGive'] - $srow['nPointTake']);
            if ($points != 0){
                $sql = "UPDATE " . TABLEPREFIX . "usercredits SET nPoints=nPoints-".$points." WHERE nUserId='" . $srow['nUserId'] . "'";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//points for offered user

                $sql = "UPDATE " . TABLEPREFIX . "usercredits SET nPoints=nPoints+".$points." WHERE nUserId='" . $srow['nUserReturnId'] . "'";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//points for accepting user
            }
            
            /******** Check if the transaction limit for the swap transactions users are over or not ******/
            $success_fee        = DisplayLookUp('SuccessFee');//success fee for the transaction
            $free_trans_level   = DisplayLookUp('freeTransactionsPerMonth');//no. of free trans per month
            
            $paid_trans         = 'N';
            $succ_trans_sql_1     = "SELECT s.nUserId from " . TABLEPREFIX . "saledetails as sd "
            . "LEFT JOIN " . TABLEPREFIX . "sale s on s.nSaleId = sd.nSaleId "
            . "WHERE sd.vSaleStatus >= 2 "
            . "and s.nUserId = '".$srow['nUserId']."' "
            . "and sd.dDate > '".date('Y-m-').'01 00:00:00'."'";
            $succ_trans_res_1     = mysqli_query($conn, $succ_trans_sql_1) or die(mysqli_error($conn));
            
            $succ_trans_sql_2     = "SELECT st.nUserId from ".TABLEPREFIX."swaptxn as st "
            . "WHERE st.vStatus = 'A' "
            . "AND st.dDate > '".date('Y-m-').'01 00:00:00'."' "
            . "AND st.nUserId = '".$srow['nUserId']."'";
            $succ_trans_res_2     = mysqli_query($conn, $succ_trans_sql_2) or die(mysqli_error($conn));
            
            $succ_trans_sql_3     = "SELECT st2.nUserReturnId from ".TABLEPREFIX."swaptxn as st2 "
            . "WHERE st2.vStatus = 'A' "
            . "AND st2.dDate > '".date('Y-m-').'01 00:00:00'."' "
            . "AND st2.nUserReturnId = '".$srow['nUserId']."'";
            $succ_trans_res_3     = mysqli_query($conn, $succ_trans_sql_3) or die(mysqli_error($conn));

            /*echo "Count 1 = ".mysqli_num_rows($succ_trans_res_1);
            echo "Count 2 = ".mysqli_num_rows($succ_trans_res_2);
            echo "Count 3 = ".mysqli_num_rows($succ_trans_res_3);*/
            
            if (mysqli_num_rows($succ_trans_res_1) >= $free_trans_level || mysqli_num_rows($succ_trans_res_2) >= $free_trans_level || mysqli_num_rows($succ_trans_res_3) >= $free_trans_level){  echo "h1111";
                $paid_trans = 'Y'; //If transactions are more than the allowed free limit
            }
            
            if ($success_fee > 0 && $paid_trans == 'Y'){//if transaction fee needs to be paid make the entries
                $sql = "INSERT INTO " . TABLEPREFIX . "successfee (
                nUserId,
                nPurchaseBy,
                nProdId,
                nAmount,
                nPoints,
                dDate,
                vType
                ) 
                VALUES(
                '".$srow['nUserId']."',"
                . "'" . $srow['nUserReturnId']."',"
                . "'" . $nSwapId_array[0]."',"
                . "'" . $success_fee."',"
                . "'0',"
                . "now(),"
                . "'".(($srow['vPostType']=='swap')?'s':'w')."'"
                . ")";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//success fee
            }
            
            $paid_trans = 'N';
            $succ_trans_sql_1 = "select s.nUserId FROM " . TABLEPREFIX . "saledetails as sd "
            . "LEFT JOIN " . TABLEPREFIX . "sale s on s.nSaleId = sd.nSaleId "
            . "WHERE sd.vSaleStatus >= 2 "
            . "and s.nUserId = '".$srow['nUserReturnId']."' "
            . "and sd.dDate > '".date('Y-m-').'01 00:00:00'."'";
            $succ_trans_res_1 = mysqli_query($conn, $succ_trans_sql_1) or die(mysqli_error($conn));//to count the no. of trans
            
            $succ_trans_sql_2 = "SELECT st.nUserId FROM `".TABLEPREFIX."swaptxn` as st "
            . "WHERE st.vStatus = 'A' "
            . "and st.dDate > '".date('Y-m-').'01 00:00:00'."' "
            . "and st.nUserId = '".$srow['nUserReturnId']."'";
            $succ_trans_res_2 = mysqli_query($conn, $succ_trans_sql_2) or die(mysqli_error($conn));//to count the no. of trans
            
            $succ_trans_sql_3 = "SELECT st2.nUserReturnId FROM `".TABLEPREFIX."swaptxn` as st2 "
            . "WHERE st2.vStatus = 'A' "
            . "and st2.dDate > '".date('Y-m-').'01 00:00:00'."' "
            . "and st2.nUserReturnId = '".$srow['nUserReturnId']."'";
            $succ_trans_res_3 = mysqli_query($conn, $succ_trans_sql_3) or die(mysqli_error($conn));//to count the no. of trans
            
            /*echo "Count 1 = ".mysqli_num_rows($succ_trans_res_1);
            echo "Count 2 = ".mysqli_num_rows($succ_trans_res_2);
            echo "Count 3 = ".mysqli_num_rows($succ_trans_res_3);*/
            
            if (mysqli_num_rows($succ_trans_res_1) >= $free_trans_level || mysqli_num_rows($succ_trans_res_2) >= $free_trans_level || mysqli_num_rows($succ_trans_res_3) >= $free_trans_level){ 
                $paid_trans = 'Y'; //If transactions are more than the allowed free limit
            }
            
            if ($success_fee > 0 && $paid_trans == 'Y'){//if transaction fee needs to be paid make the entries
                $sql = "INSERT INTO " . TABLEPREFIX . "successfee (
                nUserId,
                nPurchaseBy,
                nProdId,
                nAmount,
                nPoints,
                dDate,
                vType
                ) VALUES(
                '" . $srow['nUserReturnId'] . "',"
                . "'" . $srow['nUserId'] . "',"
                . "'" . $nSwapId_user_array[0] . "',"
                . "'" . $success_fee . "',"
                . "'0',"
                . "now(),"
                . "'".(($srow['vPostType']=='swap')?'s':'w')."'"
                . ")";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//success fee for the other user
            }
            /******** Check if the transaction limit for the swap transactions users are over or not ******/

            $sql = "Update " . TABLEPREFIX . "swaptxn set vStatus ='A' where nUserId = '" . $var_other_user . "'  AND nSTId='" . $nSTId . "'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));//Update the status of the offer
            
            //$swaps = $srow['nSwapId'].','.$srow['nSwapReturnId'];
            $sql = "Update " . TABLEPREFIX . "swap set vSwapStatus='1', nSwapMember = '".$srow['nUserReturnId']."' where nSwapId IN(".$srow['nSwapId'].") ";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));//update thes swap status
            
            $sql = "Update " . TABLEPREFIX . "swap set vSwapStatus='1', nSwapMember = '".$srow['nUserId']."' where nSwapId IN(".$srow['nSwapReturnId'].") ";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));//update thes swap status

            $sql = "UPDATE " . TABLEPREFIX . "swap SET vSwapStatus='1' WHERE nSwapId='" . $srow['wishedId'] . "'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            
            $parent_id = $srow['nParentId'];
            while ($parent_id != 0){
                $sql_counter = "SELECT * from " . TABLEPREFIX . "swaptxn where nSTId='" . $parent_id . "'";
                $res_counter = mysqli_query($conn, $sql_counter) or die(mysqli_error($conn));
                if ($srow_counter = mysqli_fetch_array($res_counter)){
                    $sql_up = "Update " . TABLEPREFIX . "swaptxn set vStatus = 'N' where nSTId='".$srow_counter['nSTId']."' and vStatus!='A'";
                    mysqli_query($conn, $sql_up) or die(mysqli_error($conn));//if counter offer exits make the previous offers invalid
                    $parent_id = $srow_counter['nParentId'];
                }
            }
            
            $all_swap_array = explode(',',$srow['nSwapId'].','.$srow['nSwapReturnId']);
            foreach($all_swap_array as $allkey => $allval){//loop all the swap and swapreturn ids
                $sql_up = "Update " . TABLEPREFIX . "swaptxn set vStatus = 'N' 
                where (nSwapId like '".$allval.",%' or nSwapId like '%,".$allval.",%' or nSwapId like '%,".$allval."' or nSwapId = '".$allval."'
                or
                nSwapReturnId like '".$allval.",%' or nSwapReturnId like '%,".$allval.",%' or nSwapReturnId like '%,".$allval."' or nSwapReturnId = '".$allval."'
                ) 
                and vStatus!='A'";
                mysqli_query($conn, $sql_up) or die(mysqli_error($conn));//if the swap item is present in another offer, make the offer invalid
            }
            
            if ($points<0)
                $_SESSION['msg'] = str_replace('{point_name}',POINT_NAME,str_replace('{points}', (-1 * $points), MESSAGE_POINT_SUCCESSFULLY_DEDUCTED_FROM_ACCOUNT));
            elseif ($points>0)
                $_SESSION['msg'] = $msg = str_replace('{point_name}',POINT_NAME,str_replace('{points}', $points, MESSAGE_POINT_SUCCESSFULLY_ADDED_TO_ACCOUNT));
            
            $mailRw = array();
            $mailSql = "SELECT L.content,L.content_title
            FROM ".TABLEPREFIX."content C
            JOIN ".TABLEPREFIX."content_lang L
            ON C.content_id = L.content_id
            AND C.content_name = 'offerAccepted'
            AND C.content_type = 'email'
            AND L.lang_id = '".$_SESSION["lang_id"]."'";
            //echo $mailSql;exit;
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);
            
            $condition = "where nUserId='" . $var_other_user . "'";
            $UserName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
            
            $UserEmail  =  getUserEmail($var_other_user);
            $login_username = ucfirst($_SESSION["gloginname"]);
            $mainTextShow   = $mailRw['content'];
            //echo $mainTextShow;exit;
            $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{user_name}");
            $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($login_username));
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
            $StyleContent=MailStyle($sitestyle,SITE_URL);
            $EMail = $UserEmail; 
            


            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, ucfirst($UserName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            
            $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');

            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
            
            //echo $subject.'<br />'.$EMail.'<br />'.$msgBody;exit;
            //send_mail('nirmala.v@armiasystems.com', $subject, $msgBody, SITE_EMAIL, 'Admin');

            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
            
            header("location:makeoffer.php?mode=A&nSTId=" . $nSTId);
            //header("location:showaddress.php?mode=A&show=true&nSTId=" . $nSTId);
            exit();
        }
        else {
            header("location:showaddress.php?mode=A&show=false&nSTId=" . $nSTId);
            exit();
        }
    }
    else if ($_POST["postback"] == "R") {//rejected
        $sql = "Update " . TABLEPREFIX . "swaptxn set vStatus ='R' where  nUserId = '" . $var_other_user . "'  AND nSTId='" . $nSTId . "' ";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));//updating to rejected status
        header("location:showaddress.php?mode=R&show=true&swapid=" . $var_swapid . "&userid=" . $var_other_user);
        exit();
    }
    else if ($_POST["postback"] == "O") {//delivered
        $sql = "SELECT * from " . TABLEPREFIX . "swaptxn where nSTId='" . $nSTId . "'";
        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if ($srow = mysqli_fetch_array($res)){
            //owner fields not required
            //$sql = "Update " . TABLEPREFIX . "swap set vOwnerDelivery='Y',dOwnerDate=now() where nSwapId in (" . $srow['nSwapId'] . ")";
            if ($srow['nUserId']==$_SESSION["guserid"])//offered person updates delivery
            $sql = "Update " . TABLEPREFIX . "swap set vPartnerDelivery='Y',dPartnerDate=now() where nSwapId in (" . $srow['nSwapReturnId'] . ")";
            else//accepting person updates delivery
            $sql = "Update " . TABLEPREFIX . "swap set vPartnerDelivery='Y',dPartnerDate=now() where nSwapId in (" . $srow['nSwapId'] . ")";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }
        echo "<script>alert('".MESSAGE_DELIVERY_STATUS_UPDATED."');</script>";
        header("location:makeoffer.php?post_type=".$_POST['post_type']."&uname=".$_POST['uname']."&nSTId=".$nSTId);
        exit;
        /* $_SESSION["updated_msg"] = MESSAGE_DELIVERY_STATUS_UPDATED;
        header("Location:usermain.php");
        exit;*/
        // echo "<script>alert('".MESSAGE_DELIVERY_STATUS_UPDATED."');window.location='usermain.php';</script>";
    }

    else if ($_REQUEST["nSTId"] != "") {//edit and view modes

        mysqli_query($conn, "Update " . TABLEPREFIX . "swaptxn set vBlink ='N' WHERE nSTId='" . $nSTId . "'") or die(mysqli_error($conn));//update the (new)blink to 'N'
        

        //$sql = "SELECT ST.* from " . TABLEPREFIX . "swaptxn ST where ST.nSTId='" . $nSTId . "'";
        $sql = "SELECT ST.*, U.vLoginName as user, UR.vLoginName as return_user  from " . TABLEPREFIX . "swaptxn ST
        left join " . TABLEPREFIX . "users U on U.nUserId = ST.nUserId
        left join " . TABLEPREFIX . "users UR on UR.nUserId = ST.nUserReturnId
        where ST.nSTId='" . $nSTId . "'";
        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if ($srow = mysqli_fetch_array($res)){
            $other_user_name = $srow['return_user'];
            $this_user_name = $srow['user'];
            
            //echo "thisUser=>".$this_user."---Row-----".$srow['nUserId'];
            
            if ($srow['nUserId']==$this_user && $srow['vStatus']=='O') {//edit mode for the offering person

                $mode = 'edit';
                $other_user = $srow['nUserReturnId'];
                $other_user_name = $srow['return_user'];
                $this_user_name = $srow['user'];
                $nSwapId_array = explode(',',$srow['nSwapId']);
                $nSwapId_user_array = explode(',',$srow['nSwapReturnId']);
                /*if ($srow['vStatus']<>'O'){
                    $var_error_message = ERROR_CANNOT_COMPLETE_THIS_OFFER_REASON."<br>&nbsp;&nbsp;<br>1)".ERROR_ITEM_POSTED_BY_YOU."<br>2)".ERROR_ITEM_NOT_VALID;
                }*/
            }
            else {

                $mode = 'view';
                if ($srow['nUserId']==$this_user){//view mode for the offering person
                    $other_user = $srow['nUserReturnId'];
                    $other_user_name = $srow['return_user'];
                    $this_user_name = $srow['user'];
                    $nSwapId_array = explode(',',$srow['nSwapId']);
                    $nSwapId_user_array = explode(',',$srow['nSwapReturnId']);
                }
                else {//view mode for the accepting person
                    $other_user = $srow['nUserId'];
                    $other_user_name = $srow['user'];
                    $this_user_name = $srow['return_user'];
                    $nSwapId_array = explode(',',$srow['nSwapReturnId']);
                    $nSwapId_user_array = explode(',',$srow['nSwapId']);
                }

            }

            if($other_user_name ==''){
                $other_user_name = $_GET["uname"];
            }
            else{
                if($srow['nUserId']!='')  
                    $other_user_name = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', "WHERE nUserId='" . $srow['nUserId'] . "'"), 'vLoginName');
            }
        }
    }


    if ($mode=='') {//add mode
        $mode = 'add';
        $nSwapId_array = array();
        $nSwapId_user_array = array();
        if($_REQUEST['parent_id']!='' && $_REQUEST['post_type']=='swap')
        {
            $sql = "SELECT ST.*, U.vLoginName as user,UR.vLoginName as return_user from " . TABLEPREFIX . "swaptxn ST
            left join " . TABLEPREFIX . "users U on U.nUserId = ST.nUserId
            left join " . TABLEPREFIX . "users UR on UR.nUserId = ST.nUserReturnId
            where ST.nSTId='" . $_REQUEST['parent_id'] . "'";
            $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            

            if ($srow1 = mysqli_fetch_array($res)){

            if ($srow1['nUserId']==$this_user && $srow1['vStatus']=='O') {//edit mode for the offering person
                $nSwapId_array = explode(',',$srow1['nSwapId']);
                $nSwapId_user_array = explode(',',$srow1['nSwapReturnId']);
                $this_user_points_give  = $srow1['nPointTake'];
                $other_user_points      = $srow1['nPointGive'];
                $this_user_points       = $srow1['nPointGive'];
                $this_user_amount       = $srow['nAmountGive'];
                $other_user_amount      = $srow['nAmountTake'];
            }
            else {

                if ($srow1['nUserId']==$this_user){//view mode for the offering person

                    $nSwapId_array = explode(',',$srow1['nSwapId']);
                    $nSwapId_user_array = explode(',',$srow1['nSwapReturnId']);
                    $this_user_points_give  = $srow1['nPointGive'];
                    $other_user_points      = $srow1['nPointTake'];
                    $this_user_points       = $srow1['nPointGive'];
                    $this_user_amount       = $srow['nAmountGive'];
                    $other_user_amount      = $srow['nAmountTake'];
                }
                else {//view mode for the accepting person

                    $nSwapId_array = explode(',',$srow1['nSwapReturnId']);
                    $nSwapId_user_array = explode(',',$srow1['nSwapId']);
                    
                    $other_user_points    = $srow1['nPointGive'];
                    $this_user_points     = $srow1['nPointTake'];
                    $this_user_amount       = $srow['nAmountTake'];
                    $other_user_amount      = $srow['nAmountGive'];
                    
                }

            }

            $srow =$srow1;
        }


        }   // Swap Item check ends 
        
        if($_REQUEST['parent_id']!='' && $_REQUEST['post_type']=='wish')
        {
            $sql = "SELECT ST.*, U.vLoginName as user, UR.vLoginName as return_user from " . TABLEPREFIX . "swaptxn ST
            left join " . TABLEPREFIX . "users U on U.nUserId = ST.nUserId
            left join " . TABLEPREFIX . "users UR on UR.nUserId = ST.nUserReturnId
            where ST.nSTId='" . $_REQUEST['parent_id'] . "'";
            $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));


            if ($srow1 = mysqli_fetch_array($res)){

            if ($srow1['nUserId']==$this_user && $srow1['vStatus']=='O') {//edit mode for the offering person
                $nSwapId_array = explode(',',$srow1['nSwapId']);
                $nSwapId_user_array = explode(',',$srow1['nSwapReturnId']);
                $this_user_points_give = $srow1['nPointTake'];
                $other_user_points    = $srow1['nPointGive'];

                $this_user_points = $srow1['nPointGive'];

            }
            else {

                if ($srow1['nUserId']==$this_user){//view mode for the offering person

                    $nSwapId_array = explode(',',$srow1['nSwapId']);
                    $nSwapId_user_array = explode(',',$srow1['nSwapReturnId']);
                    $this_user_points_give = $srow1['nPointGive'];
                    $other_user_points    = $srow1['nPointTake'];
                    $this_user_points = $srow1['nPointGive'];
                }
                else {//view mode for the accepting person

                    $nSwapId_array = explode(',',$srow1['nSwapReturnId']);
                    $nSwapId_user_array = explode(',',$srow1['nSwapId']);
                    
                    $other_user_points    = $srow1['nPointGive'];
                    $this_user_points     = $srow1['nPointTake'];
                }

            }
            
            $srow =$srow1;

        }
    }        

}



if ($mode!='view'){//validations to be done only if in add/edit mode
    if ($_REQUEST['post_type']=='wish')
        $sql = "Select nSwapId from " . TABLEPREFIX . "swap where
    (nUserId= '" . $_SESSION["guserid"] . "' OR vPostType='swap' OR vDelStatus='1') AND nSwapId = '" . addslashes($var_swapid) . "' ";
    else
        $sql = "Select nSwapId from " . TABLEPREFIX . "swap where
    (nUserId= '" . $_SESSION["guserid"] . "' OR vPostType='wish' OR vDelStatus='1') AND nSwapId = '" . addslashes($var_swapid) . "' ";

    if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {

        $var_pflag = true;
        $sql = "Select * from " . TABLEPREFIX . "swaptxn where
        nUserId= '" . $_SESSION["guserid"] . "' AND nSwapId = '" . addslashes($var_swapid) . "' ";

        if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {

            $other_user_name = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', "WHERE nUserId='" . $_GET["userid"] . "'"), 'vLoginName');
            $this_user_name  = $_SESSION['gloginname'];
            $var_pflag = true;
        }
        else {
            $var_pflag = false;
            $var_error_message = ERROR_ALREADY_OFFERED_AGAINST;
        }
    }
    else {
        $var_error_message = ERROR_CANNOT_COMPLETE_THIS_OFFER_REASON."<br>&nbsp;&nbsp;<br>1)".ERROR_ITEM_POSTED_BY_YOU."<br>2)".ERROR_ITEM_NOT_VALID;
    }
} else $var_pflag = true;//if mode is view
//}
//$var_pflag = true;

   // echo $mode;




include_once('./includes/title.php');
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/jquery.min.1.11.1.js"></script>
<script language="javascript" type="text/javascript">
    var $jqr=jQuery.noConflict();

    function clickMake(mode,post_type)
    {
        var chkArray = [];

        if ($jqr('input[name=chkSwap]:checked').length <=0) {
            alert('<?php echo ERROR_SELECT_ITEMS_FOR_SWAPPING; ?>');
            return false;
        }

        //this user
        else{  
            if (document.frmMakeOffer.chkSwap)
                var cnt=$jqr('input[name=chkSwap]:checked').length;
            else
                var cnt=0;

            var setVal='';
            //alert(document.frmMakeOffer.chkSwap.value);
            //alert(document.frmMakeOffer.chkSwap_user.value);
            
            $jqr('input[name=chkSwap]:checked').each(function() {
                chkArray.push($jqr(this).val());
            });
            
            var selected;
            if(chkArray.length==1)
            {
                selected = chkArray;
            }
            else{
               // selected = chkArray.join(',') + ",";
               selected = chkArray.join(',') ;
           }
           // alert(selected);

            /*if (cnt == undefined){
                cnt = 1;
            }
            for(i=0;i<cnt;i++)
            {
                if(cnt == 1)
                {
                    if(document.frmMakeOffer.chkSwap.checked == true)//if single item is there
                    {
                        flag=true;
                        setVal = setVal + document.frmMakeOffer.chkSwap.value + ',';
                    }
                    break;
                }
                if(document.frmMakeOffer.chkSwap[i].checked == true)//if multiple items are there
                {
                    flag=true;
                    setVal = setVal + document.frmMakeOffer.chkSwap[i].value + ',';
                }
            }//end for loop*/
            
            if(selected!='')
            {
                //alert(selected);
                document.frmMakeOffer.chkSwap_hidden.value=selected;//assign to hidden control
            }
        }

        //if(post_type=='swap') {
            if ($jqr('input[name=chkSwap_user]:checked').length <=0) {
                alert('<?php echo ERROR_SELECT_ITEMS_FOR_SWAPPING; ?>');
                return false;
            }
        //}
        //other user
        else { 
            var flag_user=false;
            if(document.frmMakeOffer.chkSwap_user)
                var cnt_user=document.frmMakeOffer.chkSwap_user.length;
            else
                var cnt_user=0;

            var setVal_user='';
            if (cnt_user == undefined){
                cnt_user = 1;
            }
            for(i=0;i<cnt_user;i++)
            {
                if(cnt_user == 1)
                {
                    if(document.frmMakeOffer.chkSwap_user.checked == true)//if single item
                    {
                        flag_user=true;
                        setVal_user = setVal_user + document.frmMakeOffer.chkSwap_user.value + ',';
                    }
                    break;
                }

                if(document.frmMakeOffer.chkSwap_user[i].checked == true)//for multiple items
                {
                    flag_user=true;
                    setVal_user = setVal_user + document.frmMakeOffer.chkSwap_user[i].value + ',';
                }
            }//end for loop

            if(setVal_user.length > 0)
            {
                document.frmMakeOffer.chkSwap_user_hidden.value=setVal_user.substring(0,setVal_user.length - 1);//assigning to a hidden control
            }

            <?php if ($EnablePoint!='0'){ ?>
                if(($jqr('#available_points').val()>0 )  && ($jqr('#txtMpoint').val()>0 )){
                    if (parseFloat(document.frmMakeOffer.txtMpoint.value) > parseFloat(document.frmMakeOffer.available_points.value)){//if available point is less
                        alert('<?php echo str_replace('{point_name}',POINT_NAME,ERROR_AVAILABLE_POINT_IS_LESS." ".MESSAGE_CLICK_TO_BUY_POINTS); ?>');
                        return false;

                    }
                }

                if((($jqr('#available_points').val() == "") || ($jqr('#available_points').val() == 0))  && ($jqr('#txtMpoint').val() > 0 )){
                    alert("You have no points available. Please buy points");
                    return false;
                }

                <?php 
            } ?> 
            if (mode=='edit'){
                $jqr("#postback").val ("E");
                //document.frmMakeOffer.postback.value="E";//edit mode
            }
            else{
                $jqr("#postback").val ("Y");
                //document.frmMakeOffer.postback.value="Y";//add mode
            }

            $jqr('#frmMakeOffer').submit();
            // document.frmMakeOffer.submit();

        }   

    }//end function


    function clickDelete(){//on delete
        if (confirm("<?php echo MESSAGE_ARE_YOUR_SURE_TO_DELETE; ?>")){
            document.frmMakeOffer.postback.value="D";
            document.frmMakeOffer.submit();
        }
    }

    function clickAccept()//to accept
    {
        <?php if ($EnablePoint!='0'){ ?>
            if (document.frmMakeOffer.available_points.value == '') document.frmMakeOffer.available_points.value = 0 ;
            if (parseFloat(document.frmMakeOffer.txtHpoint.value) > parseFloat(document.frmMakeOffer.available_points.value)){//if available point is less
                alert('<?php echo str_replace('{point_name}',POINT_NAME,ERROR_AVAILABLE_POINT_IS_LESS." ".MESSAGE_CLICK_TO_BUY_POINTS); ?>');
                return false;
            }
            <?php 
        } ?>
        document.frmMakeOffer.postback.value="A";
        document.frmMakeOffer.submit();
    }

    function clickDelivered()//to change status to delivered
    {
        document.frmMakeOffer.postback.value="O";
        document.frmMakeOffer.submit();
    }//end function

    function clickReject()//to change status to reject
    {
        document.frmMakeOffer.postback.value="R";
        document.frmMakeOffer.submit();
    }//end function

    function viewDetails(i){//view popup
        var str = 'itemdetails.php?swapid=' + i;
        var left = Math.floor( (screen.width - 300) / 2);
        var top = Math.floor( (screen.height - 400) / 2);

        var loginWindow=window.open(str,"approvalpage","top=" + top + ",left=" + left + ",toolbars=no,maximize=no,resize=no,width=300,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
    }

    function clickPhoto(picName)//photo view
    {
        var str="picture.php?url=" + picName;
        var left = Math.floor( (screen.width - 300) / 2);
        var top = Math.floor( (screen.height - 400) / 2);
        picture=window.open(str,"picturedisplay","top=" + top + ",left=" + left + ",toolbars=no,maximize=yes,resize=no,width=300,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
    }//end function

    //function only for point is enable
    /*function CheckPoints(nType)
    {
        document.frmMakeOffer.btMake.disabled=false;
            <?php
            //if ($EnablePoint != '0') {
                ?>
                    if(nType==1)
                    {
                        var availablePoints=document.frmMakeOffer.availablePoints.value;
                        var newValue=document.frmMakeOffer.txtMpay.value;
                    }
                    else
                    {
                        var availablePoints=document.frmMakeOffer.availablePoints2.value;
                        var newValue=document.frmMakeOffer.txtHpay.value;
                    }//end else

                    var redeemPoint=parseFloat(newValue);

                    //checking enter user points and avilable points
                    if(redeemPoint>=availablePoints)
                    {
                        alert('<?php //echo str_replace('{point_name}',POINT_NAME,ERROR_AVAILABLE_POINT_IS_LESS); ?>');
                        document.frmMakeOffer.btMake.disabled=true;
                    }
                <?php
        //}
        ?>
    }//end function
    */


    $jqr(document).ready(function() {

        $jqr('.quickView').click(function(e) {
            var res = new Array();
            var id = $jqr(this).attr('id');  
            
            if($jqr("#data_"+id).val()){
                res['data'] = JSON.parse($jqr("#data_"+id).val());

                $jqr("#ptitle").html(res['data']['title']);
                $jqr("#pdescription").html(res['data']['description']);
                $jqr("#ppostdate").html(res['data']['postdate']);
                $jqr("#plogin_name").html(res['data']['login_name']);
                $jqr("#pcat_description").html(res['data']['cat_description']);
                $jqr("#pbrand").html(res['data']['brand']);
                $jqr("#ptype").html(res['data']['type']);
                $jqr("#pcondition").html(res['data']['condition']);
                $jqr("#pyear").html(res['data']['year']);
                $jqr("#pshipvalue").html(res['data']['shipping']);
                $jqr("#pvalue").html(res['data']['price']);
                $jqr("#ProductImg").attr('src', res['data']['first_image']);
                $jqr("#ProductImg").attr('data-zoom-image', res['data']['first_image']);
            }
            

            if($jqr("#imglist_"+id).val()){
                res['imglist'] = JSON.parse($jqr("#imglist_"+id).val());
                var first_content = '<a id="firstImg" class="product_gallery_item active" data-image="'+res['data']['first_image']+'" data-zoom-image="'+res['data']['first_image']+'" tabindex="0"><img src="'+res['data']['first_image']+'" alt="product_small_img"></a>';
                var more_content = "";
                for (var i = res['imglist'].length - 1; i >= 0; i--) {

                    more_content = more_content+'<a class="product_gallery_item active" data-image="'+res['imglist'][i]+'" data-zoom-image="'+res['imglist'][i]+'" tabindex="0"><img src="'+res['imglist'][i]+'" alt="product_small_img'+i+'"></a>';
                }
            } else{
                res['imglist'] = "";
            }

            $jqr("#product_item_gallery").html(first_content+more_content);

        });

        selectProduct();


        $jqr('#lmore_items').click(function(e) {

            var lbox_select = new Array();
            var lresult = new Array();
            var lcontent = "";

            // If more than one item is selected
            if ($jqr('input[name=chkSwap]:checked').length > 1) {

                $jqr('input[name=chkSwap]:checked').each(function() {
                    lbox_select.push($jqr(this).val());
                });

                for (var i = lbox_select.length - 1; i > 0; i--) {

                    var id = lbox_select[i];
                    if($jqr("#data_"+id).val()){

                        lresult['data'] = JSON.parse($jqr("#data_"+id).val());
                        // Set details in popup
                        var login_name = lresult['data']['login_name'];
                        lcontent = lcontent + '<div class="moreItemspopup-tile"><div class="make-offer-container-tile-inner"><div class="make-offer-inner"><div class="img-sec"><img src="'+lresult['data']['first_image']+'" alt="Product"></div><h5>'+lresult['data']['title']+'</h5></div></div></div>';
                    }
                }
                $jqr("#popup_logname").html(login_name);
                $jqr("#popupContent").html(lcontent);
            }
        });


        $jqr('#rmore_items').click(function(e) {

            var rbox_select = new Array();
            var r_result = new Array();
            var r_content = "";

            if ($jqr('input[name=chkSwap_user]:checked').length > 1) {

                $jqr('input[name=chkSwap_user]:checked').each(function() {
                    rbox_select.push($jqr(this).val());
                });

                for (var i = rbox_select.length - 1; i > 0; i--) {

                    var id = rbox_select[i];
                    if($jqr("#data_"+id).val()){

                        r_result['data'] = JSON.parse($jqr("#data_"+id).val());
                        // Set details in popup
                        var login_name = r_result['data']['login_name'];
                        r_content = r_content + '<div class="moreItemspopup-tile"><div class="make-offer-container-tile-inner"><div class="make-offer-inner"><div class="img-sec"><img src="'+r_result['data']['first_image']+'" alt="Product"></div><h5>'+r_result['data']['title']+'</h5></div></div></div>';
                    }
                }
                $jqr("#popup_logname").html(login_name);
                $jqr("#popupContent").html(r_content);
            }

        });

    });


function selectProduct(){

    var lcheckbox_select = new Array();
    var rcheckbox_select = new Array();
    var lres = new Array();
    var r_res = new Array();

    // left side checkbox - Product Selection
    if ($jqr('input[name=chkSwap]:checked').length > 0) {

        $jqr("#l_img").show();
        $jqr("#l_noItem").hide();
        var lcheck_length = $jqr('input[name=chkSwap]:checked').length;

        $jqr('input[name=chkSwap]:checked').each(function() {
            lcheckbox_select.push($jqr(this).val());
        });

        var id = lcheckbox_select[0];
        if($jqr("#data_"+id).val()){

            lres = JSON.parse($jqr("#data_"+id).val());
            // Only one item is shown here. Rest are in popup
            $jqr("#l_title").html(lres.title);
            $jqr("#l_img").attr('src', lres.first_image);;
        }

        // If more than one item is selected
        if(lcheck_length > 1){
            $jqr("#lmore_items a").html(lcheck_length-1+" More Items");
            $jqr("#lmore_items").show();
        } else{
            $jqr("#lmore_items").hide();
        }

    } else{
        $jqr("#l_img").hide();
        $jqr("#l_noItem").show();
        $jqr("#l_title").html("");
        $jqr("#lmore_items").hide();
    }


    // right side checkbox - Product Selection
    if ($jqr('input[name=chkSwap_user]:checked').length > 0) {

        $jqr("#r_img").show();
        $jqr("#r_noItem").hide();
        var rcheck_length = $jqr('input[name=chkSwap_user]:checked').length;

        $jqr('input[name=chkSwap_user]:checked').each(function() {
            rcheckbox_select.push($jqr(this).val());
        });

        var id = rcheckbox_select[0];
        if($jqr("#data_"+id).val()){

            r_res = JSON.parse($jqr("#data_"+id).val());
            // Only one item is shown here. Rest are in popup
            $jqr("#r_title").html(r_res.title);
            $jqr("#r_img").attr('src', r_res.first_image);;
        }

        // If more than one item is selected
        if(rcheck_length > 1){
            $jqr("#rmore_items a").html(rcheck_length-1+" More Items");
            $jqr("#rmore_items").show();
        } else{
            $jqr("#rmore_items").hide();
        }

    } else{
        $jqr("#r_img").hide();
        $jqr("#r_noItem").show();
        $jqr("#r_title").html("");
        $jqr("#rmore_items").hide();
    }
}



</script>




<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
    <div class="homepage_contentsec" style="padding:0; ">
        <div class="container">
            <div class="row make-offer-page">
                <div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
                <div class="col-lg-9">
                    <div class="innersubheader">
                        <h4><?php echo ($mode=='view') ? HEADING_OFFER_DETAILS : (($_REQUEST['parent_id']=='') ? BUTTON_MAKE_OFFER : HEADING_MAKE_COUNTER_OFFER); ?></h4>
                    </div>
                    <div class="row">
                        <form name="frmMakeOffer" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" id="frmMakeOffer">
                            <?php
                            if ($var_flag == false) {
                                if ($_SESSION["guserid"] == "" && $_SESSION["gadminid"]=="") {
                                    include_once("./login_box.php");
                                }
                            }
                            ?>

                            <div class="full_width">
                                <!--<input type="hidden" name="source" value="<?php echo $var_source ?>">-->
                                <input type="hidden" name="counter_offer" value="<?php echo $_REQUEST['counter_offer']?>">		
                                <input type="hidden" name="swapid" value="<?php echo $var_swapid ?>">
                                <input type="hidden" name="postback" value="" id="postback">
                                <input type="hidden" name="chkSwap_hidden" value="">
                                <input type="hidden" name="uname" value="<?php echo $_REQUEST['uname']?>">
                                <input type="hidden" name="post_type" value="<?php echo $_REQUEST['post_type']?>">
                                <input type="hidden" name="chkSwap_user_hidden" value="">


                                <?php

								if ($EnablePoint!='0'){//if points are applicable
                                    $showUserTotalPoints = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');
                                    ?>
                                    <input type="hidden" name="available_points" id ="available_points" value="<?php echo $showUserTotalPoints;?>">
                                    <?php 
                                } ?>


                                <?php
                                if (isset($message) && $message != '') {
                                    ?>
                                    <div class="success"><?php echo $message; ?></div>
                                    <?php
                                }


                                if ($var_pflag == true) {
                                    ?>

                                    <div class="full_width">
                                        <!-- new-design -->
                                        <div class="make-offer-container">
                                            <div class="row" style="margin: 0">

                                                <!-- First person -->

                                                <div class="col-md-5 col-sm-6">
                                                    <div class="make-offer-container-tile">
                                                        <h3>
                                                            <?php
                                                            if ($srow['nParentId']=='0' && $srow['vPostType']=='wish' && $this_user != $srow['nUserId'])
                                                                echo TEXT_WISH_ITEM_DETAILS;
                                                            else
                                                                echo ($_SESSION['guserid']!='')?ucfirst($this_user_name):TEXT_I_WILL_GIVE; 
                                                            ?>
                                                        </h3>

                                                        <div class="make-offer-container-tile-inner userside">
                                                            <?php 

                                                            //this condition is to handle if the items has to come from wish list

                                                            if ($mode=='view') {
                                                                $sql = "SELECT s.vTitle, s.nSwapId, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
                                                                s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName , s.vBrand, s.vSmlImg, s.nShipping, cl.vCategoryDesc, cl.lang_id FROM " . TABLEPREFIX . "swap s 
                                                                LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
                                                                LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId AND cl.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                WHERE s.nUserId = '" . $this_user . "' ORDER BY s.vTitle ASC ";
                                                            }

                                                            else if ($srow['nParentId']=='0' && $srow['vPostType']=='wish') {  
                                                                $sql = "SELECT s.vTitle, s.nSwapId, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
                                                                s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName , s.vBrand, s.vSmlImg, s.nShipping, cl.vCategoryDesc, cl.lang_id FROM " . TABLEPREFIX . "swap s
                                                                LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
                                                                LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId AND cl.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                WHERE (s.vSwapStatus= '0' or '".$mode."' = 'view')
                                                                AND (s.vDelStatus='0' or '".$mode."' = 'view') AND s.vPostType='swap'
                                                                AND s.nUserId = '" . $this_user . "' ORDER BY s.vTitle ASC ";
                                                            }

                                                            else if ((($srow['nParentId'] != '0' && $mode == 'edit') || ($_REQUEST['parent_id'] != '0' && $mode == 'add')) && $_REQUEST['post_type'] == 'wish'){
                                                                $sql = "SELECT s.vTitle, s.nSwapId, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
                                                                s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName , s.vBrand, s.vSmlImg, s.nShipping, cl.vCategoryDesc, cl.lang_id FROM " . TABLEPREFIX . "swap s
                                                                LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
                                                                LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId AND cl.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                WHERE (s.vSwapStatus= '0' or '".$mode."' = 'view')
                                                                AND (s.vDelStatus='0' or '".$mode."' = 'view') AND s.vPostType='swap' 
                                                                AND s.nUserId = '" . $this_user . "' ORDER BY s.vTitle ASC ";
                                                            }

                                                            else if (($srow['nParentId']==0 && $mode == 'edit')  && $_REQUEST['post_type']=='wish'){

                                                                $sql = "SELECT s.vTitle, s.nSwapId, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
                                                                s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName , s.vBrand, s.vSmlImg, s.nShipping, cl.vCategoryDesc, cl.lang_id FROM " . TABLEPREFIX . "swap s
                                                                LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
                                                                LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId AND cl.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                WHERE (s.vSwapStatus= '0' or '".$mode."' = 'view')
                                                                AND (s.vDelStatus='0' or '".$mode."' = 'view') AND s.vPostType='wish' 
                                                                AND s.nUserId = '" . $this_user . "' ORDER BY s.vTitle ASC ";
                                                            }

                                                            else
                                                                $sql = "SELECT s.vTitle, s.nSwapId, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
                                                            s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName , s.vBrand, s.vSmlImg, s.nShipping, cl.vCategoryDesc, cl.lang_id FROM " . TABLEPREFIX . "swap s
                                                            LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
                                                            LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId AND cl.lang_id = '" . $_SESSION['lang_id'] . "'
                                                            WHERE (s.vSwapStatus= '0' or '".$mode."' = 'view')
                                                            AND (s.vDelStatus='0' or '".$mode."' = 'view') AND s.vPostType='swap' 
                                                            AND s.nUserId = '" . $this_user . "' ORDER BY s.vTitle ASC ";
                                                            
                                                            $var_count = 0;
                                                            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                                                            ?>


                                                            <div class="dropdown">
                                                                <?php
                                                                if ($srow['nParentId']=='0' && $srow['vPostType']=='wish' && $this_user != $srow['nUserId']) {?>
                                                                <button class="dropdown-toggle" type="button" data-toggle="dropdown">Wished item
                                                                    <span class="caret"></span>
                                                                </button>
                                                                <?php } else if ($mode == 'view') {?>
                                                                <button class="dropdown-toggle" type="button" data-toggle="dropdown">Selected item
                                                                    <span class="caret"></span>
                                                                </button>
                                                                <?php } else { ?>
                                                                <button class="dropdown-toggle" type="button" data-toggle="dropdown">Select item
                                                                    <span class="caret"></span> 
                                                                </button>
                                                                <?php } ?>
                                                                <ul class="dropdown-menu makeoffer-dropdown">
                                                                    <?php 

                                                                    if (mysqli_num_rows($result) > 0) {

                                                                        $cnt = 1;
                                                                        while ($row = mysqli_fetch_array($result)) {

                                                                            $var_count++;
                                                                            if ((in_array($row["nSwapId"],$nSwapId_array) && $mode=='view') || $mode!='view') {

                                                                                $img_list = $data = array();

                                                                                if($row['nSwapId']){
                                                                                    $data['swap_id'] = $row['nSwapId'];
                                                                                } else{
                                                                                    $data['swap_id'] = "0";
                                                                                }

                                                                                if($row['vTitle']){
                                                                                    if(strlen(trim($row["vTitle"])) > 28){
                                                                                        $data['title'] = substr(htmlentities($row["vTitle"]),0,28)."...";
                                                                                    }
                                                                                    else{
                                                                                        $data['title'] = htmlentities($row["vTitle"]);
                                                                                    }
                                                                                } else{
                                                                                    $data['title'] = "--";
                                                                                }

                                                                                if($row['vDescription']){
                                                                                    $data['description'] = $row['vDescription'];
                                                                                } else{
                                                                                    $data['description'] = "--";
                                                                                }

                                                                                if($row['vLoginName']){
                                                                                    $data['login_name'] = $row['vLoginName'];
                                                                                } else{
                                                                                    $data['login_name'] = "--";
                                                                                }

                                                                                if($row['vCategoryDesc']){
                                                                                    $data['cat_description'] = $row['vCategoryDesc'];
                                                                                } else{
                                                                                    $data['cat_description'] = "--";
                                                                                }

                                                                                if($row['vBrand']){
                                                                                    $data['brand'] = $row['vBrand'];
                                                                                } else{
                                                                                    $data['brand'] = "--";
                                                                                }

                                                                                if($row['vType']){
                                                                                    $data['type'] = $row['vType'];
                                                                                } else{
                                                                                    $data['type'] = "--";
                                                                                }

                                                                                if($row['vCondition']){
                                                                                    $data['condition'] = $row['vCondition'];
                                                                                } else{
                                                                                    $data['condition'] = "--";
                                                                                }

                                                                                if($row['vYear']){
                                                                                    $data['year'] = $row['vYear'];
                                                                                } else{
                                                                                    $data['year'] = "--";
                                                                                }

                                                                                if($row['nShipping']){
                                                                                    $data['shipping'] = CURRENCY_CODE.$row['nShipping'];
                                                                                } else{
                                                                                    $data['shipping'] = CURRENCY_CODE."0";
                                                                                }

                                                                                if($row['nValue']){
                                                                                    $data['price'] = CURRENCY_CODE.$row['nValue'];
                                                                                } else{
                                                                                    $data['price'] = CURRENCY_CODE."0";
                                                                                }

                                                                                if($row['dPostDate']){
                                                                                    $data['postdate'] = date('m/d/Y', strtotime($row['dPostDate']));
                                                                                } else{
                                                                                    $data['postdate'] = "--";
                                                                                }

                                                                                $vurl = trim($row["vUrl"]);
                                                                                if ($vurl != ''){

                                                                                    $gallery_list = getMoreImages($row["nSwapId"], "s");
                                                                                    $vurl_img = str_replace('medium_', '', $vurl);

                                                                                    if(@file_exists($vurl_img)) {
                                                                                        $data['first_image'] = $vurl_img;
                                                                                    } elseif (@file_exists($vurl)) {
                                                                                        $data['first_image'] = $vurl;
                                                                                    } elseif (!empty($gallery_list)) {
                                                                                        $data['first_image'] = array_shift($gallery_list);
                                                                                    } else{
                                                                                        $data['first_image'] = "images/nophoto.gif";
                                                                                    }

                                                                                    if (!empty($gallery_list)) {
                                                                                        foreach ($gallery_list as $img) {
                                                                                            $img_list[] = $img;
                                                                                        }
                                                                                    }

                                                                                } else{

                                                                                    $gallery_list = getMoreImages($row["nSwapId"], "s");
                                                                                    if(!empty($gallery_list)){
                                                                                        $data['first_image'] = array_shift($gallery_list);
                                                                                        foreach ($gallery_list as $img) {
                                                                                            $img_list[] = $img;
                                                                                        }
                                                                                    } else{
                                                                                        $data['first_image'] = "images/nophoto.gif"; 
                                                                                    }
                                                                                }

                                                                                if(!empty($img_list)){
                                                                                    $imglist_json = json_encode($img_list);
                                                                                } else {
                                                                                    $imglist_json = "";
                                                                                }

                                                                                ?>
                                                                                <li>
                                                                                    <div>
                                                                                        <?php if ($mode!='view'){//if not in view mode display checkbox ?>
                                                                                        <label class="check-container">
                                                                                            <input type="checkbox" name="chkSwap" value="<?php echo  $row["nSwapId"] ?>" <?php if (in_array($row["nSwapId"],$nSwapId_array)) echo "checked='checked'"; ?> onClick="javascript:selectProduct()";>
                                                                                            <span class="checkmark"></span>
                                                                                        </label>
                                                                                        <?php } else {
                                                                                            echo $cnt++.')'; ?>
                                                                                            <label class="check-container" style="display: none;">
                                                                                                <input type="checkbox" name="chkSwap" value="<?php echo  $row["nSwapId"] ?>" checked='checked'>
                                                                                                <span class="checkmark"></span>
                                                                                            </label>
                                                                                            <?php 
                                                                                        } ?>

                                                                                        <a id="<?php echo $row["nSwapId"]?>" class="quickView" href="#" data-toggle="modal" data-target="#detailproductModal">
                                                                                            <?php $prod_img = $data['first_image']; 
                                                                                            $prod_title = htmlentities($row["vTitle"]);
                                                                                            ?>
                                                                                            <img src="<?php echo $prod_img; ?>" alt="Product">
                                                                                            <span><?php  echo ($prod_title)? $prod_title :  MESSAGE_NO_ITEM_AVAILABLE; ?></span>
                                                                                        </a>
                                                                                    </div>
                                                                                    <span class="rate">
                                                                                        <?php if ($EnablePoint!='0') echo $row["nPoint"].' '.POINT_NAME; ?>
                                                                                        <?php if ($EnablePoint=='2') echo '&'; ?> 
                                                                                        <?php if ($EnablePoint!='1') echo ' '.CURRENCY_CODE.$row["nValue"]; 
                                                                                        ?>
                                                                                    </span>
                                                                                </li>

                                                                                <input id="<?php echo "data_".$row["nSwapId"];?>" type="hidden" name="data" value="<?php echo htmlentities(json_encode($data)); ?>" />
                                                                                <input id="<?php echo "imglist_".$row["nSwapId"];?>" type="hidden" value="<?php echo htmlentities($imglist_json); ?>"/>
                                                                                <?php
                                                                            }
                                                                        } ?>
                                                                        <?php 
                                                                    } else {
                                                                        ?>
                                                                        <li>
                                                                            <div>
                                                                                <label class="check-container">
                                                                                    <?php echo MESSAGE_NO_ITEM_AVAILABLE; ?>
                                                                                </label>
                                                                            </div>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </div>

                                                            <div id="lmore_items" class="text-right" style="margin-top: -10px" type="hidden">
                                                                <a class="more-it" href="#" data-toggle="modal" data-target="#moreItems"></a>
                                                            </div>



                                                            <div class="make-offer-inner">
                                                                <div class="img-sec">
                                                                    <span id="l_noItem">No items selected</span>
                                                                    <img id="l_img" src="" alt="No items">
                                                                </div>
                                                                <h5 id="l_title"></h5>
                                                                <?php if ($EnablePoint != '1') {//if not points ONLY ?>
                                                                <div class="text-center">
                                                                    <div class="ammount-slider">
                                                                        <div class="ammount-slider-inner">
                                                                            <span class="doller-move"><?php echo CURRENCY_CODE; ?></span>
                                                                            <?php 
                                                                            if ($mode!='view'){ ?>
                                                                            <input class="autoSize investamountSliderValue" type="text"  name="txtMpay" id="txtMpay" value="<?php echo ($this_user==$srow['nUserId'])?$srow['nAmountGive']:$srow['nAmountTake']; ?>" onblur="javascript:check_float_value(this);" size="6" maxlength="10" onchange="javascript:if(parseFloat(document.getElementById('txtMpay').value)<0) document.getElementById('txtMpay').value=0;">
                                                                            <?php } 
                                                                            else {
                                                                                echo $i_pay = ($this_user==$srow['nUserId'])?$srow['nAmountGive']:$srow['nAmountTake']; ?>
                                                                                <?php 
                                                                            } ?>
                                                                        </div>
                                                                    </div>
                                                                    <span class="willpay-span">Will Pay</span>
                                                                </div>
                                                                <?php } ?>

                                                                <?php if ($EnablePoint != '0') {//if not pice ONLY ?>
                                                                <div class="text-center">
                                                                    <div class="ammount-slider">
                                                                        <div class="ammount-slider-inner">
                                                                            <span class="points-move"><?php echo POINT_NAME; ?>&nbsp;</span>
                                                                            <?php 
                                                                            if ($mode!='view'){ ?>
                                                                            <input class="autoSize investamountSliderValue" type="text" name="txtMpoint" id="txtMpoint" value="<?php echo ($this_user==$srow['nUserReturnId'])?$srow['nPointTake']:$srow['nPointGive']; ?>" onBlur="javascript:check_float_value(this);" size="6" maxlength="10" onchange="javascript:if(parseInt(document.getElementById('txtMpoint').value)<0) document.getElementById('txtMpoint').value=0;">

                                                                            <?php } else { ?>
                                                                            <div class="make_off_L_points">
                                                                                <?php   
                                                                                echo ($this_user==$srow['nUserReturnId'])?$srow['nPointTake']:$srow['nPointGive']; ?>
                                                                            </div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                    <span class="willpay-span">Will Give</span>
                                                                </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- end -->


                                                <!-- Second person -->
                                                <div class="col-md-5 col-sm-6">
                                                    <div class="make-offer-container-tile">
                                                        <h3>
                                                            <?php if($_GET["uname"]!="") $other_user_name = $_GET["uname"];
                                                            if ((($srow['nParentId']=='0') || ($_REQUEST['parent_id']=='' && $mode == 'add')) && ($srow['vPostType']=='wish' || $_REQUEST['post_type']=='wish') && $this_user != $srow['nUserReturnId'])
                                                                echo TEXT_WISH_ITEM_DETAILS;
                                                            else{

                                                                if($_SESSION['guserid']!='' || $other_user_name != '')
                                                                    echo ucfirst($other_user_name);
                                                                else {
                                                                    echo  TEXT_OTHER_USER_WILL_GIVE;
                                                                }
                                                            }
                                                            ?>
                                                        </h3>
                                                        <div class="make-offer-container-tile-inner ">
                                                            <?php

                                                            //this condition is there to handle if the item needs to be displayed from wish.

                                                            if ($mode=='view'){
                                                                $sql = "SELECT s.vTitle, s.nSwapId, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
                                                                s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName , s.vBrand, s.vSmlImg, s.nShipping, cl.vCategoryDesc, cl.lang_id FROM " . TABLEPREFIX . "swap s
                                                                LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
                                                                LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId AND cl.lang_id = '" . $_SESSION['lang_id'] . "' 
                                                                WHERE s.nUserId = '" . $other_user . "' ORDER BY s.vTitle ASC";
                                                            }

                                                            else if ((($srow['nParentId'] == '0' && $mode == 'edit') || ($_REQUEST['parent_id'] == '' && $mode == 'add')) && $_REQUEST['post_type'] == 'wish'){
                                                                $sql = "SELECT s.vTitle, s.nSwapId, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
                                                                s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName , s.vBrand, s.vSmlImg, s.nShipping, cl.vCategoryDesc, cl.lang_id
                                                                FROM " . TABLEPREFIX . "swap s 
                                                                LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
                                                                LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId AND cl.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                WHERE IF (s.vPostType = 'swap', 1, s.vPostType = 'wish' AND s.nSwapId='".$_REQUEST['swapid']."') 
                                                                AND (s.vSwapStatus= '0' or '".$mode."' = 'view')
                                                                AND (s.vDelStatus='0' or '".$mode."' = 'view') 
                                                                AND s.nUserId = '" . $other_user . "' ORDER BY s.vPostType DESC";
                                                                

                                                            }

                                                            else if (($_REQUEST['parent_id']!=0 && $mode == 'add') && ($_REQUEST['post_type']=='wish')){
                                                                $sql = "SELECT s.vTitle, s.nSwapId, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
                                                                s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName , s.vBrand, s.vSmlImg, s.nShipping, cl.vCategoryDesc, cl.lang_id FROM " . TABLEPREFIX . "swap s 
                                                                LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
                                                                LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId AND cl.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                WHERE (s.vSwapStatus= '0' or '".$mode."' = 'view')
                                                                AND (s.vDelStatus='0' or '".$mode."' = 'view') AND s.vPostType='swap' 
                                                                AND s.nUserId = '" . $other_user . "' ORDER BY s.vTitle ASC";

                                                            }

                                                            else{

                                                                $sql = "SELECT s.vTitle, s.nSwapId, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
                                                                s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName , s.vBrand, s.vSmlImg, s.nShipping, cl.vCategoryDesc, cl.lang_id FROM " . TABLEPREFIX . "swap s
                                                                LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
                                                                LEFT JOIN " . TABLEPREFIX . "category_lang cl ON cl.cat_id = s.nCategoryId AND cl.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                WHERE (s.vSwapStatus= '0' or '".$mode."' = 'view')
                                                                AND (s.vDelStatus='0' or '".$mode."' = 'view') AND s.vPostType='swap' 
                                                                AND s.nUserId = '" . $other_user . "' ORDER BY s.vTitle ASC";
                                                                
                                                                //echo $sql;
                                                            }

                                                            $var_count = 0;
                                                            //echo $sql;
                                                            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                            ?> 
                                                            <div class="dropdown">
                                                                <?php
                                                                if ((($srow['nParentId']=='0') || ($_REQUEST['parent_id']=='' && $mode == 'add')) && ($srow['vPostType']=='wish' || $_REQUEST['post_type']=='wish') && $this_user != $srow['nUserReturnId']) {?>
                                                                <button class="dropdown-toggle" type="button" data-toggle="dropdown">Wished item
                                                                    <span class="caret"></span>
                                                                </button>
                                                                <?php } else if ($mode == 'view') {?>
                                                                <button class="dropdown-toggle" type="button" data-toggle="dropdown">Selected item
                                                                    <span class="caret"></span>
                                                                </button>
                                                                <?php } else { ?>
                                                                <button class="dropdown-toggle" type="button" data-toggle="dropdown"><?php $selecttitle =  ($_GET['post_type']=='swap')?'Swapping Item':"Selected Item";
                                                                  echo $selecttitle;  ?>
                                                                  <span class="caret"></span> 
                                                              </button>
                                                              <?php } ?>
                                                              <ul class="dropdown-menu makeoffer-dropdown">
                                                                <?php

                                                                if (mysqli_num_rows($result) > 0) {

                                                                    $cnt = 1;
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                                        $var_count++;

                                                                        if ((in_array($row["nSwapId"],$nSwapId_user_array) && $mode=='view') || $mode!='view') {

                                                                            $img_list = $data = array();

                                                                            if($row['nSwapId']){
                                                                                $data['swap_id'] = $row['nSwapId'];
                                                                            } else{
                                                                                $data['swap_id'] = "0";
                                                                            }

                                                                            if($row['vTitle']){
                                                                                if(strlen(trim($row["vTitle"])) > 28){
                                                                                    $data['title'] = substr(htmlentities($row["vTitle"]),0,28)."...";
                                                                                }
                                                                                else{
                                                                                    $data['title'] = htmlentities($row["vTitle"]);
                                                                                }
                                                                            } else{
                                                                                $data['title'] = "--";
                                                                            }

                                                                            if($row['vDescription']){
                                                                                $data['description'] = $row['vDescription'];
                                                                            } else{
                                                                                $data['description'] = "--";
                                                                            }

                                                                            if($row['vLoginName']){
                                                                                $data['login_name'] = $row['vLoginName'];
                                                                            } else{
                                                                                $data['login_name'] = "--";
                                                                            }

                                                                            if($row['vCategoryDesc']){
                                                                                $data['cat_description'] = $row['vCategoryDesc'];
                                                                            } else{
                                                                                $data['cat_description'] = "--";
                                                                            }

                                                                            if($row['vBrand']){
                                                                                $data['brand'] = $row['vBrand'];
                                                                            } else{
                                                                                $data['brand'] = "--";
                                                                            }

                                                                            if($row['vType']){
                                                                                $data['type'] = $row['vType'];
                                                                            } else{
                                                                                $data['type'] = "--";
                                                                            }

                                                                            if($row['vCondition']){
                                                                                $data['condition'] = $row['vCondition'];
                                                                            } else{
                                                                                $data['condition'] = "--";
                                                                            }

                                                                            if($row['vYear']){
                                                                                $data['year'] = $row['vYear'];
                                                                            } else{
                                                                                $data['year'] = "--";
                                                                            }

                                                                            if($row['nShipping']){
                                                                                $data['shipping'] = CURRENCY_CODE.$row['nShipping'];
                                                                            } else{
                                                                                $data['shipping'] = CURRENCY_CODE."0";
                                                                            }

                                                                            if($row['nValue']){
                                                                                $data['price'] = CURRENCY_CODE.$row['nValue'];
                                                                            } else{
                                                                                $data['price'] = CURRENCY_CODE."0";
                                                                            }

                                                                            if($row['dPostDate']){
                                                                                $data['postdate'] = date('m/d/Y', strtotime($row['dPostDate']));
                                                                            } else{
                                                                                $data['postdate'] = "--";
                                                                            }
                                                                            $vurl = trim($row["vUrl"]);
                                                                            if ($vurl != ''){

                                                                                $gallery_list = getMoreImages($row["nSwapId"], "s");
                                                                                $vurl_img = str_replace('medium_', '', $vurl);

                                                                                if(@file_exists($vurl_img)) {
                                                                                    $data['first_image'] = $vurl_img;
                                                                                } elseif (@file_exists($vurl)) {
                                                                                    $data['first_image'] = $vurl;
                                                                                } elseif (!empty($gallery_list)) {
                                                                                    $data['first_image'] = array_shift($gallery_list);
                                                                                } else{
                                                                                    $data['first_image'] = "images/nophoto.gif";
                                                                                }

                                                                                if (!empty($gallery_list)) {
                                                                                    foreach ($gallery_list as $img) {
                                                                                        $img_list[] = $img;
                                                                                    }
                                                                                }

                                                                            } else{

                                                                                $gallery_list = getMoreImages($row["nSwapId"], "s");
                                                                                if(!empty($gallery_list)){
                                                                                    $data['first_image'] = array_shift($gallery_list);
                                                                                    foreach ($gallery_list as $img) {
                                                                                        $img_list[] = $img;
                                                                                    }
                                                                                } else{
                                                                                    $data['first_image'] = "images/nophoto.gif"; 
                                                                                }
                                                                            }

                                                                            if(!empty($img_list)){
                                                                                $imglist_json = json_encode($img_list);
                                                                            } else {
                                                                                $imglist_json = "";
                                                                            }

                                                                            $loginName = $row["vLoginName"];
                                                                            ?>


                                                                            <?php

                                                                            if (in_array($row["nSwapId"],$nSwapId_user_array) || trim($_REQUEST['swapid'])==$row["nSwapId"]){ ?>
                                                                            <li>
                                                                                <div>
                                                                                    <?php if ($mode!='view' && !isset($_GET['nSTId'])){ 


                                                                                        if (trim($_REQUEST['swapid'])==$row["nSwapId"]) 
                                                                                        { 

                                                                                          $selecttitle =  ($_GET['post_type']=='swap')?'Swapping Item':"Wished Item";
                                                                                                  //echo $selecttitle;  

                                                                                          ?>



                                                                                          <label class="check-container" style="display: none;">
                                                                                            <input type="checkbox" name="chkSwap_user" value="<?php echo  $row["nSwapId"] ?>" <?php if (in_array($row["nSwapId"],$nSwapId_user_array) || trim($_REQUEST['swapid'])==$row["nSwapId"]) echo "checked='checked'"; ?> >
                                                                                            <span class="checkmark"></span>
                                                                                        </label>
                                                                                        <?php } 
                                                                                        else { 
                                                                                            ?>
                                                                                            <label class="check-container">
                                                                                                <input type="checkbox" name="chkSwap_user" value="<?php echo  $row["nSwapId"] ?>" <?php if (in_array($row["nSwapId"],$nSwapId_user_array) || trim($_REQUEST['swapid'])==$row["nSwapId"]) echo "checked='checked'"; ?> onClick="javascript:selectProduct();">
                                                                                                <span class="checkmark"></span>
                                                                                            </label>
                                                                                            <?php 
                                                                                        } 
                                                                                    } else {
                                                                                        echo $cnt++.')'; ?>
                                                                                        <label class="check-container" style="display: none;">
                                                                                            <input type="checkbox" name="chkSwap_user" value="<?php echo  $row["nSwapId"] ?>" checked='checked'>
                                                                                            <span class="checkmark"></span>
                                                                                        </label>
                                                                                        <?php 
                                                                                    } ?>
                                                                                    <a id="<?php echo $row["nSwapId"]?>" class="quickView" href="#" data-toggle="modal" data-target="#detailproductModal">
                                                                                        <?php $prod_img = $data['first_image']; 
                                                                                        $prod_title = htmlentities($row["vTitle"]);
                                                                                        ?>
                                                                                        <img src="<?php echo $prod_img; ?>" alt="Product">
                                                                                        <span><?php  echo ($prod_title)? $prod_title :  MESSAGE_NO_ITEM_AVAILABLE; ?></span>
                                                                                    </a>
                                                                                </div>

                                                                                <span class="rate">
                                                                                    <?php if ($EnablePoint!='0') echo $row["nPoint"].' '.POINT_NAME; ?>
                                                                                    <?php if ($EnablePoint=='2') echo '&'; ?>
                                                                                    <?php if ($EnablePoint!='1') echo ' '.CURRENCY_CODE.$row["nValue"]; ?>
                                                                                </span>

                                                                            </li>

                                                                            <?php } ?>

                                                                            <input id="<?php echo "data_".$row["nSwapId"];?>" type="hidden" name="data" value="<?php echo htmlentities(json_encode($data)); ?>" />
                                                                            <input id="<?php echo "imglist_".$row["nSwapId"];?>" type="hidden" value="<?php echo htmlentities($imglist_json); ?>"/>
                                                                            <?php
                                                                        }?>

                                                                        <?php
                                                                    } ?>
                                                                    <?php 
                                                                } 
                                                                ?>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" value="<?php echo $other_user; ?>" name="other_user" />


                                                        <div id="rmore_items" class="text-right" style="margin-top: -10px" type="hidden">
                                                            <a class="more-it" href="#" data-toggle="modal" data-target="#moreItems"></a>
                                                        </div>


                                                        <div class="make-offer-inner">
                                                            <div class="img-sec">
                                                                <span id="r_noItem">No items selected</span>
                                                                <img id="r_img" src="" alt="No items">
                                                            </div>
                                                            <h5 id="r_title"></h5>
                                                            <?php if ($EnablePoint != '1') {//if not points ONLY ?>
                                                            <div class="text-center">
                                                                <div class="ammount-slider">
                                                                    <div class="ammount-slider-inner">
                                                                        <span class="doller-move"><?php echo CURRENCY_CODE; ?></span>
                                                                        <?php 
                                                                        if ($mode!='view'){ ?>
                                                                        <input class="autoSize investamountSliderValue" type="text" name="txtHpay" id="txtHpay" value="<?php echo ($this_user==$srow['nUserReturnId'])?$srow['nAmountGive']:$srow['nAmountTake']; ?>" onBlur="javascript:check_float_value(this);" size="6" maxlength="10" onchange="javascript:if(parseFloat(document.getElementById('txtMpay').value)<0) document.getElementById('txtMpay').value=0;">
                                                                        <?php } 
                                                                        else {
                                                                            echo $other_pay = ($this_user==$srow['nUserReturnId'])?$srow['nAmountGive']:$srow['nAmountTake']; ?>
                                                                            <?php 
                                                                        } ?>
                                                                    </div>
                                                                </div>
                                                                <span class="willpay-span"><?php echo $loginName;?> Will Pay</span>
                                                            </div>
                                                            <?php } ?>

                                                            <?php if ($EnablePoint != '0') {//if not pice ONLY ?>
                                                            <div class="text-center">
                                                                <div class="ammount-slider">
                                                                    <div class="ammount-slider-inner">
                                                                        <span class="points-move"><?php echo POINT_NAME; ?>&nbsp;</span>
                                                                        <?php 
                                                                        if ($mode!='view'){ ?>
                                                                        <input class="autoSize investamountSliderValue" type="text" name="txtHpoint" id="txtHpoint" value="<?php echo ($this_user==$srow['nUserReturnId'])?$srow['nPointGive']:$srow['nPointTake']; ?>" onBlur="javascript:check_float_value(this);" size="6" maxlength="10" onchange="javascript:if(parseInt(document.getElementById('txtHpoint').value)<0) document.getElementById('txtHpoint').value=0;">

                                                                        <?php  } else { ?> 
                                                                        <div class="make_off_L_points">
                                                                            <?php echo ($this_user==$srow['nUserReturnId'])?$srow['nPointGive']:$srow['nPointTake']; ?>
                                                                        </div>
                                                                        <?php  } ?>
                                                                    </div>
                                                                </div>
                                                                <span class="willpay-span"><?php echo $loginName;?> Will Give</span>
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- end -->

                                        </div>
                                    </div>

                                    <!-- more-product -->
                                    <!-- Detail-modal -->
                                    <div id="moreItems" class="modal moreItemspopup" role="dialog">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <div class="modal-dialog">
                                            <div class="modal-content">   
                                                <div class="modal-body">
                                                    <h4 id="popup_logname"></h4>
                                                    <div id="popupContent" class="moreItemspopup-grid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Detail-modal -->

                                    <div id="detailproductModal" class="modal productdetailpopup" role="dialog">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="productdetailpopup-left">
                                                                <div class="Product-img-container">
                                                                    <img id="ProductImg" src="" data-zoom-image=""/>
                                                                </div>
                                                                <div id="product_item_gallery" class="product_item_gallery">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class="productdetailpopup-right">
                                                                <h3 id="ptitle"></h3>
                                                                <div class="bottom-price">
                                                                    <h4>Price&nbsp;<span id="pvalue"></span></h4>
                                                                    <span class="ship-price">Shipping
                                                                        <i id="pshipvalue"></i>
                                                                    </span>
                                                                    <span class="post-dte">Posted On <i id="ppostdate"></i></span>
                                                                </div>
                                                                <div class="bottom-usr">
                                                                    <span>Posted By<i id="plogin_name"></i></span>
<!--                                                                        <div class="rating-usr">
                                                                            <i class="flaticon-star"></i>
                                                                            <i class="flaticon-star"></i>
                                                                            <i class="flaticon-star"></i>
                                                                            <i class="flaticon-star-1"></i>
                                                                            <i class="flaticon-star-1"></i>
                                                                        </div>-->
                                                                    </div>
                                                                    <p id="pdescription"></p>
                                                                    <table>
                                                                      <tr>
                                                                        <td>Category</td>
                                                                        <td id="pcat_description"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Brand</td>
                                                                        <td id="pbrand"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Type</td>
                                                                        <td id="ptype"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Condition</td>
                                                                        <td id="pcondition"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Year</td>
                                                                        <td id="pyear"></td>
                                                                    </tr>
                                                                </table>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>


                                    <!-- ////New-design -->


                                </div>
                                <!--      </div>	 -->						

                            </div>


                        <!-- <div class="full_width">

                            <?php if ($EnablePoint != '1') {//if not points ONLY ?>
                            <div class="makeoffer_bottom">
                                <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                                    <div class="make_off_L_inner_T">
                                        <?php echo ($_SESSION['guserid']!='')?str_replace('{user_name}',ucfirst($this_user_name),TEXT_USER_WILL_PAY):TEXT_I_WILL_PAY; ?> <?php echo CURRENCY_CODE; ?> 

                                        <?php if ($mode!='view'){ ?>
                                    </div>    
                                    <input class="textbox2 form-control make_off_txtfeild" type="text" name="txtMpay" id="txtMpay" value="<?php echo ($this_user==$srow['nUserId'])?$srow['nAmountGive']:$srow['nAmountTake']; ?>" onblur="javascript:check_float_value(this);" size="6" maxlength="10" onchange="javascript:if(parseFloat(document.getElementById('txtHpay').value)>0) document.getElementById('txtHpay').value=0;">
                                    <?php } else {echo $i_pay = ($this_user==$srow['nUserId'])?$srow['nAmountGive']:$srow['nAmountTake']; ?>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                                <div class="make_off_L_inner_T">
                                    <?php echo ($_SESSION['guserid']!='' || $other_user_name != '')?str_replace('{user_name}',ucfirst($other_user_name),TEXT_USER_WILL_PAY):TEXT_OTHER_USER_WILL_PAY; ?> <?php echo CURRENCY_CODE; ?>
                                    <?php if ($mode!='view'){ ?>
                                </div>
                                <input class="textbox2 form-control make_off_txtfeild" type="text" name="txtHpay" id="txtHpay" value="<?php echo ($this_user==$srow['nUserReturnId'])?$srow['nAmountGive']:$srow['nAmountTake']; ?>" onBlur="javascript:check_float_value(this);" size="6" maxlength="10" onchange="javascript:if(parseFloat(document.getElementById('txtMpay').value)>0) document.getElementById('txtMpay').value=0;">
                                <?php } else echo $other_pay = ($this_user==$srow['nUserReturnId'])?$srow['nAmountGive']:$srow['nAmountTake']; ?>
                            </div>
                        </div>
                        <?php } ?>

                        <?php if ($EnablePoint != '0') {//if not pice ONLY ?>
                        <div class="makeoffer_bottom">
                            <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                                <div class="make_off_L_inner_T">
                                    <?php echo ($_SESSION['guserid']!='' )?str_replace('{user_name}',ucfirst($this_user_name),TEXT_USER_WILL_GIVE):TEXT_I_WILL_GIVE; ?>
                                    <?php if ($mode!='view'){ ?>
                                </div>
                                <input class="textbox2 form-control make_off_txtfeild" type="text" name="txtMpoint" id="txtMpoint" value="<?php echo ($this_user==$srow['nUserId'])?$srow['nPointGive']:$srow['nPointTake']; ?>" onBlur="javascript:check_float_value(this);" size="6" maxlength="10" onchange="javascript:if(parseInt(document.getElementById('txtHpoint').value)>0) document.getElementById('txtHpoint').value=0;">
                                <div class="make_off_L_points">
                                    <?php } else echo ($this_user==$srow['nUserId'])?$srow['nPointGive']:$srow['nPointTake']; ?>
                                    <?php echo POINT_NAME; ?>

                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                                <div class="make_off_L_inner_T">
                                    <?php echo ($_SESSION['guserid']!='' || $other_user_name != '')?str_replace('{user_name}',ucfirst($other_user_name),TEXT_USER_WILL_GIVE):TEXT_OTHER_USER_WILL_GIVE; ?>
                                    <?php if ($mode!='view'){ ?>
                                </div>
                                <input class="textbox2 form-control make_off_txtfeild" type="text" name="txtHpoint" id="txtHpoint" value="<?php echo ($this_user==$srow['nUserReturnId'])?$srow['nPointGive']:$srow['nPointTake']; ?>" onBlur="javascript:check_float_value(this);" size="6" maxlength="10" onchange="javascript:if(parseInt(document.getElementById('txtMpoint').value)>0) document.getElementById('txtMpoint').value=0;">
                                <div class="make_off_L_points">
                                    <?php } else echo ($this_user==$srow['nUserReturnId'])?$srow['nPointGive']:$srow['nPointTake']; ?>
                                    <?php echo POINT_NAME; ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?> -->

                        <!-- <div class="makeoffer_bottom">
                            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                <div class="full_widht">
                                    <h4><?php echo TEXT_ADDITIONAL_INFORMATION; ?></h4>
                                    <?php if ($mode!='view'){ ?>
                                    <textarea name="txtAdditional" cols="60" rows="6" class="textbox22 form-control"><?php echo stripslashes($srow['vText']); ?></textarea>
                                    <?php } else echo nl2br(stripslashes($srow['vText'])); ?>
                                </div>
                            </div>
                        </div> -->
                        <div class="row flex-row-center">
                            <div class="textarea-block col-md-10">
                                <?php if ($mode!='view'){ ?>
                                <textarea name="txtAdditional" cols="60" rows="6" class="textbox22 form-control" placeholder="Additional Information"><?php echo stripslashes($srow['vText']); ?></textarea>
                                <?php } else echo nl2br(stripslashes($srow['vText'])); ?>

                                <?php if ($srow['vStatus']!='A' && $srow['vStatus']!='R' && $srow['vStatus']!='N') { ?>
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12 col-md-6 col-xs-12">
                                        <?php if ($EnablePoint != '0') { ?>
                                        <a href="buy_credits.php" target="_blank"  style="color:blue"><?php echo str_replace('{point_name}',POINT_NAME,TEXT_BUY_POINTS); ?></a>
                                        <?php } ?>
                                    </div>
                                    <div class="col-lg-9 col-sm-12 col-md-6 col-xs-12 text-right">
                                        <input type="hidden" name="post_type" value="<?php echo $_REQUEST['post_type']; ?>" />
                                        <?php if ($_REQUEST['nSTId']!='' && $mode=='edit') {//edit mode ?>
                                        <input type="hidden" name="nSTId" value="<?php echo $_REQUEST['nSTId']; ?>">
                                        <input class="submit" type="button" name="btEdit" value="<?php echo BUTTON_SAVE; ?>" onClick="javascript:clickMake('edit','<?php echo $post_type; ?>');"> 
                                        <input class="submit" type="button" name="btDelete" value="<?php echo BUTTON_DELETE; ?>" onClick="javascript:clickDelete();">
                                        <?php } else if ($mode=='add') {//add mode ?>
                                        <input type="hidden" name="parent_id" value="<?php echo ($_REQUEST['parent_id']!='')?$_REQUEST['parent_id']:'0'; ?>" />
                                        <input type="hidden" name="post_type" value="<?php echo $_REQUEST['post_type']; ?>" />
                                        <input type="button" class="submit" name="btMake" value="<?php echo BUTTON_MAKE_OFFER; ?>" onClick="javascript:clickMake('add','<?php echo $post_type; ?>');"  >
                                        <?php } else {//view mode but possible for accept/reject/counter offer ?>
                                        <input type="hidden" name="nSTId" value="<?php echo $_REQUEST['nSTId']; ?>">
                                        <input type="hidden" name="txtHpoint" value="<?php echo $srow['nPointTake']; ?>">
                                        <input type="hidden" name="parent_id" value="<?php echo ($_REQUEST['parent_id']!='')?$_REQUEST['parent_id']:'0'; ?>" />
                                        <input type="button" name="btAccept" class="submit" value="<?php echo BUTTON_ACCEPT; ?>" onClick="javascript:clickAccept();">
                                        <input type="button" name="btReject" class="submit" value="<?php echo BUTTON_REJECT; ?>" onClick="javascript:clickReject();">
                                        <input type="button" class="submit" name="btOffer" value="<?php echo BUTTON_COUNTER_OFFER; ?>" onClick="javascript:document.location.href='makeoffer.php?userid=<?php echo $srow['nUserId']; ?>&post_type=<?php echo $_REQUEST['post_type']; ?>&parent_id=<?php echo $srow['nSTId']; ?>&counter_offer=Y'">
                                        <?php } ?><br>
                                    </div>
                                </div>

                                <?php } 
                            else if ($mode=='view' && $srow['vStatus']=='A') { //once accepted
                                //to retrieve delivery details
                                if ($this_user==$srow['nUserId']){
                                    $sql_owner = "select * from " . TABLEPREFIX . "swap where nSwapId in (".$srow['nSwapReturnId'].")";
                                    $sql_other_user = "select * from " . TABLEPREFIX . "swap where nSwapId in (".$srow['nSwapId'].")";
                                }
                                else {
                                    $sql_owner = "select * from " . TABLEPREFIX . "swap where nSwapId in (".$srow['nSwapId'].")";
                                    $sql_other_user = "select * from " . TABLEPREFIX . "swap where nSwapId in (".$srow['nSwapReturnId'].")";
                                }
                                $res_swap = mysqli_query($conn, $sql_owner) or die(mysqli_error($conn));
                                $var_owner_delivery = 'Y';
                                while ($row_swap = mysqli_fetch_array($res_swap)){
                                    if ($row_swap['vPartnerDelivery'] == 'N'){
                                        $var_owner_delivery = 'N';
                                    }
                                    $var_owner_date = $row_swap['dPartnerDate'];
                                }								
                                $res_swap = mysqli_query($conn, $sql_other_user) or die(mysqli_error($conn));
                                $var_partner_delivery = 'Y';
                                while ($row_swap = mysqli_fetch_array($res_swap)){
                                    if ($row_swap['vPartnerDelivery'] == 'N'){
                                        $var_partner_delivery = 'N';
                                    }
                                    $var_partner_date = $row_swap['dPartnerDate'];
                                }								
                                ?>
                                <input type="hidden" name="nSTId" value="<?php echo $_REQUEST['nSTId']; ?>">

                                <div class="makeoffer_bottom">
                                    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                        <?php 
                                        //To show address or to display payment link for paying transaction fee
                                        $show_address = 'Y';

                                        if ($this_user == $srow['nUserId']){
                                            $fee_swap_array = explode(',',$srow['nSwapId']);
                                            $fee_sql = "select nSId,vStatus from " . TABLEPREFIX . "successfee where nUserId = '".$srow['nUserId']."' and nProdId = '".$fee_swap_array[0]."'";
                                        }
                                        else{
                                            $fee_swap_array = explode(',',$srow['nSwapReturnId']);
                                            $fee_sql = "select nSId,vStatus from " . TABLEPREFIX . "successfee where nUserId = '".$srow['nUserReturnId']."' and nProdId = '".$fee_swap_array[0]."'";
                                        }
                                        $fee_res = mysqli_query($conn, $fee_sql) or die(mysqli_error($conn));

                                        if ($fee_row = mysqli_fetch_array($fee_res)) {
                                            if ($fee_row['vStatus']!='A') $show_address = 'N';
                                        }

                                        if ($show_address=='Y'){//show address
                                            ?>
                                            <a href="showaddress.php?mode=A&show=true&nSTId=<?php echo $srow['nSTId']; ?>"><b><?php echo LINK_VIEW_CONTACT_DETAILS; ?></b></a>
                                            <?php 
                                        }
                                        else {
                                            //show the payment (transaction fee) link ?>
                                            <a href="pay_success_fee.php?nSId=<?php echo $fee_row['nSId']; ?>" style="color:blue"><?php echo TEXT_PAY_SUCCESS_FEE_EACH_TRANSACTION; ?></a>
                                            <?php 
                                        } ?>

                                        <?php
                                        if ($EnablePoint != '1'){//if not point only
                                            //retrieving payment details
                                            $psql = "select vSwapStatus from " . TABLEPREFIX . "swap where vSwapStatus >= 2 and (nSwapId in (".$srow['nSwapId'].") or nSwapId in (".$srow['nSwapReturnId']."))";
                                            $pres = mysqli_query($conn, $psql) or die(mysqli_error($conn));
                                            if (mysqli_num_rows($pres)>0) $payment_status = 'y';
                                            $payment_to_be = 'no';
                                            if (($srow['nAmountGive'] > $srow['nAmountTake']) && $this_user==$srow['nUserId']){
                                                $payment_to_be = 'yes';
                                                $to_pay = $srow['nAmountGive'] - $srow['nAmountTake'];//calculating if payment is necessary
                                                $update_swap_array = explode(',',$srow['nSwapId']);
                                                $update_swap_id = $update_swap_array[0];
                                            }
                                            else if (($srow['nAmountGive'] < $srow['nAmountTake']) && $this_user==$srow['nUserReturnId']){
                                                $payment_to_be = 'yes';
                                                $to_pay = $srow['nAmountTake'] - $srow['nAmountGive'];//calculating if payment is necessary
                                                $update_swap_array = explode(',',$srow['nSwapReturnId']);
                                                $update_swap_id = $update_swap_array[0];
                                            }
                                            if ($payment_to_be == 'yes') {//if payment is necessary
                                            //$var_payment_string =  ? "<a href=\"./paypage.php?mode=om&swapid=$var_swapid&amount=$var_hpay&\">".LINK_USE_ESCROW."</a>" : "";
                                                if ($payment_status != 'y') { //if payment not done (payment to the other user)?> 
                                                | <a href="./paypage.php?mode=om&swapid=<?php echo $update_swap_id; ?>&amount=<?php echo $to_pay; ?>&" style="color:blue"><b><?php echo TEXT_YOUR_PENDING_SETTLEMENT_IS." ".CURRENCY_CODE.$to_pay; ?></b></a>
                                                <?php 
                                            } ?>
                                            <?php }
                                        }
                                        ?>
                                    </div>
                                </div>

                                <?php if ($EnablePoint != '1'){//if not point only ?>
                                <div class="makeoffer_bottom">
                                    <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                                        <?php echo TEXT_PAYMENT_STATUS; ?>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                                        <?php
                                        $p1 = $srow['nAmountGive']-$srow['nAmountTake'];
                                        $p2 = $srow['nAmountGive']+$srow['nAmountTake'];
                                        if ($p1!=0 && $p2!=0 && $payment_status=='y'){//if payment completed
                                            echo TEXT_COMPLETED;
                                        }
                                        else if ($p1!=0 && $p2!=0){//if payment not completed
                                            echo TEXT_PENDING;
                                        }
                                        else {//if payment not required
                                            echo TEXT_N_A;
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php } ?>


                                <div class="makeoffer_bottom">
                                    <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                                        <?php echo TEXT_MY_DELIVERY_STATUS; ?>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                                        <?php
                                        if (mysqli_num_rows($res_swap)>0) {//delivery status
                                            switch ($var_owner_delivery) {
                                                case "Y" :
                                                echo MESSAGE_ITEMS_DELIVERED_AT_MY_PLACE;
                                                echo " (" . str_replace('{date}',$var_owner_date,MESSAGE_STATUS_UPDATED_ON_DATE) . ")";
                                                break;

                                                case "N" :
                                                echo MESSAGE_NOT_YET_DELIVERED;
                                                echo ($show_address=='Y' && (($payment_to_be == 'yes' && $payment_status=='y') || $payment_to_be == 'no'))?" <a href=\"javascript:clickDelivered();\">".LINK_CLICK_TO_CHANGE_STATUS_TO_DELIVERED."</a>":"";
                                                break;

                                                case "A" :
                                                echo MESSAGE_NO_ITEMS_TO_DELIVER;
                                                break;
                                            }
                                        }
                                        else {
                                            echo MESSAGE_NO_ITEMS_TO_DELIVER;
                                        }
                                        ?>
                                    </div>
                                </div>


                                <div class="makeoffer_bottom">
                                    <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                                        <?php echo TEXT_OTHER_USER_DELIVERY_STATUS; ?>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                                        <?php
                                        switch ($var_partner_delivery) {//delivery status of other user
                                            case "Y" :
                                            echo MESSAGE_ITEMS_DELIVERED_AT_USER_PLACE;
                                            echo " (" . str_replace('{date}',$var_partner_date,MESSAGE_STATUS_UPDATED_ON_DATE) . ")";
                                            break;

                                            case "N" :
                                            echo MESSAGE_NOT_YET_DELIVERED;
                                            break;

                                            case "A" :
                                            echo MESSAGE_NO_ITEMS_TO_DELIVER;
                                            break;
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php 
                            } ?>
                            <?php
                        }
                        else { ?>

                        <div>
                            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                <div class="warning"><?php echo  $var_error_message ?></div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                              <br>
                              <button type="submit" value="<?php echo LINK_BACK_TO_DASHBOARD; ?>"  height="21" class="btn btn-default btn-new" onclick="window.location.href='usermain.php'; return false;">
                                <?php echo LINK_BACK_TO_DASHBOARD;?>

                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </form>
</div>
<div class="subbanner">
    <?php include('./includes/sub_banners.php'); ?>
</div>
</div>
</div>  
</div>
</div>

<?php require_once("./includes/footer.php"); ?>