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

if ($_SESSION['lang_id']==''){
    if (DisplayLookUp('language_by_ip')=='yes'){
        if (getenv(HTTP_X_FORWARDED_FOR)) {
            $pipaddress = getenv(HTTP_X_FORWARDED_FOR);
            $ipaddress = getenv(REMOTE_ADDR);
            //echo "Your Proxy IPaddress is : ".$pipaddress. "(via $ipaddress)" ;
        } else {
            $ipaddress = getenv(REMOTE_ADDR);
            //echo "Your IP address is : $ipaddress";
        }
        //echo $IP = $_SERVER['REMOTE_ADDR'];
        //"152.122.1.0"
        $geo_location_arr = IPtoLocation($ipaddress);
        $country_abbrev = $geo_location_arr['country_abbrev'];
        $sql_lang = "select lang_id,folder_name,country_abbrev from ".TABLEPREFIX."lang where lang_status='y'";
        $res_lang = mysqli_query($conn, $sql_lang) or die(mysqli_error($conn));
        while ($obj_row = mysqli_fetch_object($res_lang)){
            $abbrev_arr = explode(',',strtolower($obj_row->country_abbrev));
            if (in_array(strtolower($country_abbrev),$abbrev_arr)) {
                $_SESSION['lang_id']= $obj_row->lang_id;
                $_SESSION['lang_folder']= $obj_row->folder_name;
                break;
            }
        }
        if ($_SESSION['lang_id']=='') {
            $_SESSION['lang_id']= "1";
            $_SESSION['lang_folder']= "en";
        }
    }
    else {
        $_SESSION['lang_id']= "1";
        $_SESSION['lang_folder']= "en";
    }
}else{
    
        $sql_lang = "select folder_name,country_abbrev from ".TABLEPREFIX."lang where lang_id=".mysqli_real_escape_string($conn, $_SESSION['lang_id'])."";
        $res_lang = mysqli_query($conn, $sql_lang) or die(mysqli_error($conn));
        $obj_row = mysqli_fetch_object($res_lang);
        $_SESSION['lang_folder']= $obj_row->folder_name;
             
           
       
}

//echo $_SESSION['lang_folder'];
ContentText();//to assign the content text to constants
if (file_exists("./languages/".$_SESSION['lang_folder']."/common.php"))
    include("./languages/".$_SESSION['lang_folder']."/common.php");//common language file
else
    include("../languages/".$_SESSION['lang_folder']."/common.php");//common language file

?>