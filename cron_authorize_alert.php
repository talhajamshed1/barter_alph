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


$sqlChkExpCrone = mysqli_query($conn, "SELECT datediff(now(),u.dPlanExpDate) as expired,p.nPlanId,u.nUserId FROM " . TABLEPREFIX . "users u LEFT JOIN
                                         " . TABLEPREFIX . "plan p ON u.nPlanId=p.nPlanId WHERE p.vPeriods!='F' AND
                                         u.dPlanExpDate!='0000-00-00'") or die(mysqli_error($conn));
if (mysqli_num_rows($sqlChkExpCrone) > 0) {
    while ($arrCrone = mysqli_fetch_array($sqlChkExpCrone)) {
        $today = date("Y-m-d");
        if ($arrCrone['expired'] == '5') {
            $condUser = "where nUserId='" . $arrCrone['nUserId'] . "'";
            $useFName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vFirstName', $condUser), 'vFirstName');

            //from look up table
            $url =  '<a href="' . SITE_URL . '/login.php">';

            /*
            * Fetch user language details
            */

            $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
            $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
            $langRw = mysqli_fetch_array($langRs);

            /*
            * Fetch email contents from content table
            */
            $mailSql = "SELECT L.content,L.content_title
              FROM ".TABLEPREFIX."content C
              JOIN ".TABLEPREFIX."content_lang L
                ON C.content_id = L.content_id
               AND C.content_name = 'expired'
               AND C.content_type = 'email'
               AND L.lang_id = '".$_SESSION["lang_id"]."'";

            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{url}","{url_end}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($url),"</a>");
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace("{SITE_NAME}", SITE_NAME, $subject);

            $StyleContent = MailStyle($sitestyle, SITE_URL);

            //readf file n replace
            $arrSearch  = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, $useFName, $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody    = str_replace($arrSearch, $arrReplace, $msgBody);

            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
        }//end if
        if ($arrCrone['expired'] == '0') {
            //update user plan status
            mysqli_query($conn, "UPDATE " . TABLEPREFIX . "users set vStatus='1' WHERE nUserId='" . $arrCrone['nUserId'] . "' AND
										nPlanId='" . $arrCrone['nPlanId'] . "'") or die(mysqli_error($conn));

            //update old plan status in payment table
            mysqli_query($conn, "UPDATE " . TABLEPREFIX . "payment set vPlanStatus='I',vComments='Inactive on $today ' where
										nUserId='" . $arrCrone['nUserId'] . "'	AND vPlanStatus='A' AND
										nPlanId='" . $arrCrone['nPlanId'] . "'") or die(mysqli_error($conn));
        }//end if
    }//end while loop
}//end if
?>
