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

$userid = ($userid)?$userid:$user;
if($userid && !isset($lang_id)){
    $sql = "Select preferred_language from  ".TABLEPREFIX."users  where nUserId='".$userid."'";
    $result = mysqli_query($conn, $sql);
    $lang_id = mysqli_fetch_row($result)[0];
}
if(!isset($lang_id)){
    $lang_id ='';
}
if ($lang_id==''){
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
                $lang_id= $obj_row->lang_id;
                $lang_folder= $obj_row->folder_name;
                break;
            }
        }
        if ($lang_id=='') {
            $lang_id= "1";
            $lang_folder= "en";
        }
    }
    else {
        $lang_id= "1";
        $lang_folder= "en";
    }
}else{
        $lang_id  = ($lang_id == "0") ? "1":$lang_id;
        $sql_lang = "select folder_name,country_abbrev from ".TABLEPREFIX."lang where lang_id=".mysqli_real_escape_string($conn, $lang_id)."";
        $res_lang = mysqli_query($conn, $sql_lang) or die(mysqli_error($conn));
        $obj_row = mysqli_fetch_object($res_lang);
        $lang_folder= $obj_row->folder_name;
             
           
       
}

ContentText($lang_id);//to assign the content text to constants
if (file_exists("./languages/".$lang_folder."/common.php"))
    include("./languages/".$lang_folder."/common.php");//common language file
else
    include("../languages/".$lang_folder."/common.php");//common language file

function IPtoLocation($ip) {
    $latlngValue = array();
    $dom = new DOMDocument();
    $ipcheck = $ip;

    if ($ipcheck == '' || $ipcheck === false) {
        echo ERROR;
        exit;
    } else {
        $uri = "http://api.hostip.info/?ip=$ip&position=true";
        @$dom->load($uri);
        $locationValue['country'] = $dom->getElementsByTagName("countryName")->item(0)->nodeValue;
        $locationValue['country_abbrev'] = $dom->getElementsByTagName("countryAbbrev")->item(0)->nodeValue;
        $name = $dom->getElementsByTagNameNS('http://www.opengis.net/gml', 'name')->item(1)->nodeValue;
        $coordinates = $dom->getElementsByTagNameNS('http://www.opengis.net/gml', 'coordinates')->item(0)->nodeValue;
        $temp = explode(",", $coordinates);
        $locationValue['LNG'] = $temp[0];
        $locationValue['LAT'] = $temp[1];
        $locationValue['NAME'] = $name;
        return $locationValue;
    }
}

function ContentText() {
    global $conn;
    $sql = mysqli_query($conn, "select C.content_name, L.content from " . TABLEPREFIX . "content C
                        LEFT JOIN " . TABLEPREFIX . "content_lang L on C.content_id = L.content_id and L.lang_id = '" . $lang_id . "'
                        where C.content_type='' and C.content_status='y'") or die(mysqli_error($conn));
    
    while ($obj_row = mysqli_fetch_object($sql)) {
        switch ($obj_row->content_name) {
            case 'sitetitle': define('SITE_TITLE', utf8_encode($obj_row->content));
                break;
            case 'sitename': define('SITE_NAME', utf8_encode($obj_row->content));
                break;
            case 'Meta Keywords': define('META_KEYWORDS', utf8_encode($obj_row->content));
                break;
            case 'Meta Description': define('META_DESCRIPTION', utf8_encode($obj_row->content));
                break;
            case 'headerCaption': define('HEADER_CAPTION', utf8_encode($obj_row->content));
                break;
            case 'PointName': define('POINT_NAME', utf8_encode($obj_row->content));
                break;
        }
    }//end if
}

?>