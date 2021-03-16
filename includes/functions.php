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
// Enable full error, warning and notice reporting
error_reporting(1);

if (file_exists('./includes/language.php'))
    include('./includes/language.php'); //language related process
else
    include('../includes/language.php'); //language related process
if (file_exists('../PHPMailer/src/')){
    require_once '../PHPMailer/src/Exception.php';
    require_once '../PHPMailer/src/PHPMailer.php';
    require_once '../PHPMailer/src/SMTP.php';
} else {
    require_once './PHPMailer/src/Exception.php';
    require_once './PHPMailer/src/PHPMailer.php';
    require_once './PHPMailer/src/SMTP.php';
}



$EnablePoint = DisplayLookUp('EnablePoint');
define("ENABLE_POINT",$EnablePoint);
define("PAGINATION_LIMIT",9);

$page = $_REQUEST['p'];
if (!$page) $page = 1;

$limit = PAGINATION_LIMIT;

include_once("lib/pager/class.pager.php");
function isValidUsername($str) {
    if (preg_match("/[^0-9a-zA-Z+_]/", $str)) {
        return false;
    } else {
        return true;
    }
}

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

function get_payment_initial($txtSearch) {//to get payment initial from the payment name
    switch (strtolower($txtSearch)) {
        case strtolower(TEXT_PAYPAL) : $va_method = "pp";
        break;
        case strtolower(TEXT_CREDIT_CARD) : $va_method = "cc";
        break;
        case strtolower(TEXT_BLUEPAY) : $va_method = "bu";
        break;
        case strtolower(TEXT_CASIER_CHECK) : $va_method = "ca";
        break;
        case strtolower(TEXT_MONEY_ORDER) : $va_method = "mo";
        break;
        case strtolower(TEXT_WIRE_TRANSFER) : $va_method = "wt";
        break;
        case strtolower(TEXT_PERSONAL_CHECK) : $va_method = "pc";
        break;
    }//end switch
    return $va_method;
}

function get_payment_name($payment_init) {//to get payment name from the payment initial
    switch ($payment_init) {
        case "pp" : $trnansmode = TEXT_PAYPAL;
        break;
        case "paypal" : $trnansmode = TEXT_PAYPAL;
        break;

        case "wp" : $trnansmode = TEXT_WORLDPAY;
        break;

        case "bp" : $trnansmode = TEXT_BLUEPAY;
        break;

        case "cc" : $trnansmode = TEXT_CREDIT_CARD;
        break;
        case "credit card" : $trnansmode = TEXT_CREDIT_CARD;
        break;

        case "bu" : $trnansmode = TEXT_BUSINESS_CHECK;
        break;

        case "ca" : $trnansmode = TEXT_CASIER_CHECK;
        break;

        case "mo" : $trnansmode = TEXT_MONEY_ORDER;
        break;

        case "wt" : $trnansmode = TEXT_WIRE_TRANSFER;
        break;

        case "pc" : $trnansmode = TEXT_PERSONAL_CHECK;
        break;

        case "yp":
        $trnansmode = TEXT_YOUR_PAY;
        break;

        case "gc":
        $trnansmode = TEXT_GOOGLE_CHECKOUT;
        break;

        case "rp":
        $trnansmode = POINT_NAME;
        break;
        case "sp":
        $trnansmode = TEXT_STRIPE;
        break;
    }//end switch
    return $trnansmode;
}

function transaction_search_area() {//to display the search area of the transaction pages
    $txtSearch = $_REQUEST['txtSearch'];
    $ddlSearchType = $_REQUEST['ddlSearchType'];
    ?>
    <table border="0" width="100%" class="search_table" >
        <tr>
            <td valign="top" align="right" style="border-bottom:none; ">
                <?php echo TEXT_SEARCH; ?>
                &nbsp;
                <select name="ddlSearchType" class="comm_input width1" id="ddlSearchType">
                    <option value="date" <?php if ($ddlSearchType == "date" || $ddlSearchType == "") {
                        echo("selected");
                    } ?>><?php echo TEXT_TRANSACTION_DATE . ' ' . TEXT_MM_DD_YYYY; ?></option>
                    <option value="amount"  <?php if ($ddlSearchType == "amount") {
                        echo("selected");
                    } ?>><?php echo TEXT_AMOUNT; ?></option>
                    <option value="transmode"  <?php if ($ddlSearchType == "transmode") {
                        echo("selected");
                    } ?>><?php echo TEXT_TRANSACTION_MODE; ?></option>
                    <option value="transno" <?php if ($ddlSearchType == "transno") {
                        echo("selected");
                    } ?>><?php echo TEXT_TRANSACTION_NUMBER; ?></option>
                </select>
                &nbsp;
                <input type="text" name="txtSearch" id="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="comm_input width1">
                <select name="txtSearch" id="payments_with_name" class="comm_input width1">
                </select>
            </td>
            <td align="left"  style="border-bottom:none; ">
             <a href="javascript:clickSearch();" class="login_btn comm_btn_orng_tileeffect2"><button type="submit" value="<?php echo BUTTON_SEARCH; ?>"  height="21" class="btn btn-default btn-new login_btn ">
                 <?php echo BUTTON_GO;?></button></a>
                 &nbsp;<!--<a href="javascript:clickSearch();" class="login_btn comm_btn_orng_tileeffect2"><?php //echo BUTTON_GO; ?><!--<img src='./images/gobut.gif'  width="20" height="20" border='0' >-->
             </td>
         </tr>
     </table>
     <?php
 }

/*function getCategoryLinkOld($baseurl, $catid) {
    $link = "<a href='" . $baseurl . "' class=boldtextblack>Categories</b></a> ";
    if ($catid != "") {
        $link .= " <font class=boldtextblack>></font> ";
        $sql = "SELECT * FROM " . TABLEPREFIX . "category where nCategoryId = '" . $catid . "'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) != 0) {
            $row = mysqli_fetch_array($result);
            $currcatid = $row["nCategoryId"];
            $currcatname = $row["vCategoryDesc"];
            $subcats = $row["vRoute"];
        }
        if ($subcats == "") {
            $lastcat = "<a href='" . $baseurl . "?catid=" . $currcatid . "' class=boldtextblack><b>" . $currcatname . "</b></a>";
        } else {
            $sql = "SELECT * FROM " . TABLEPREFIX . "category where nCategoryId in ($subcats) ";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) != 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $subcatname = $row["vCategoryDesc"];
                    $subcatid = $row["nCategoryId"];
                    $link .= " <a href='" . $baseurl . "?catid=" . $subcatid . "' class=boldtextblack><b>" . $subcatname . "</b></a>  <font class=boldtextblack> ></font>";
                }
            }
            $link = substr($link, 0, -1);
        }
    }

    $link .=$lastcat;
    return $link;
}
*/
function getCategoryLink($baseurl, $catid) {
    $link = "<a href='categories.php'>" . LINK_CATEGORIES . "</a> ";
    if ($catid != "") {
        $link .= "&nbsp;&raquo;&nbsp;";
        $sql = "SELECT * FROM " . TABLEPREFIX . "category c
        LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
        where c.nCategoryId = '" . $catid . "'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) != 0) {
            $row = mysqli_fetch_array($result);
            $currcatid = $row["nCategoryId"];
            $currcatname = $row["vCategoryDesc"];
            $subcats = $row["vRoute"];
        }//end if
        if ($subcats == "") {
            $lastcat = "<a href='" . $baseurl . "?catid=" . $currcatid . "'>" . htmlentities($currcatname) . "</a>";
        }//end if
        else {
            $sql = "SELECT * FROM " . TABLEPREFIX . "category c
            LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
            where c.nCategoryId in ($subcats) ";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) != 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $subcatname = $row["vCategoryDesc"];
                    $subcatid = $row["nCategoryId"];
                    $link .= " <a href='" . $baseurl . "?catid=" . $subcatid . "'>" . htmlentities($subcatname) . "</a>&nbsp;&raquo;&nbsp;";
                }//end while
            }//end if
            $link = substr($link, 0, -19);
        }//end else
    }//end if

    $link .=$lastcat;
    return $link;
}

//end function

function getBreadCrumbs($baseurl, $catid) {
    $link='<li ><a href="index.php">Home</a></li>';
    $link .= "<li><a href='" . $baseurl . "'>" . LINK_CATEGORIES . "</a></li>";
    if ($catid != "") {
        //$link .= "&nbsp;&raquo;&nbsp;";
        $sql = "SELECT * FROM " . TABLEPREFIX . "category c
        LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
        where c.nCategoryId = '" . $catid . "'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) != 0) {
            $row = mysqli_fetch_array($result);
            $currcatid = $row["nCategoryId"];
            $currcatname = $row["vCategoryDesc"];
            $subcats = $row["vRoute"];
        }//end if
        if ($subcats == "") {
            $lastcat = "<li><a href='" . $baseurl . "?catid=" . $currcatid . "'>" . htmlentities($currcatname) . "</a></li>";
        }//end if
        else {
            $sql = "SELECT * FROM " . TABLEPREFIX . "category c
            LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
            where c.nCategoryId in ($subcats) ";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) != 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $subcatname = $row["vCategoryDesc"];
                    $subcatid = $row["nCategoryId"];
                    $link .= "<li><a href='" . $baseurl . "?catid=" . $subcatid . "'>" . htmlentities($subcatname) . "</a></li>";
                }//end while
            }//end if
            //$link = substr($link, 0, -19);
        }//end else
    }//end if

    $link .=$lastcat;
    return $link;
}

//end function

function canUserBeDeleted($userid) {
    return false;
}

function canUserBeDeactivated($userid) {
    return true;
}

function canAffBeDeactivated($affid) {
    return false;
}

function getClientIP() {
    // Get REMOTE_ADDR as the Client IP.
    $client_ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : $REMOTE_ADDR);
    // Check for headers used by proxy servers to send the Client IP. We should look for HTTP_CLIENT_IP before HTTP_X_FORWARDED_FOR.
    if ($_SERVER["HTTP_CLIENT_IP"])
        $proxy_ip = $_SERVER["HTTP_CLIENT_IP"];
    elseif ($_SERVER["HTTP_X_FORWARDED_FOR"])
        $proxy_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    // Proxy is used, see if the specified Client IP is valid. Sometimes it's 10.x.x.x or 127.x.x.x... Just making sure.
    if ($proxy_ip) {
        if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $proxy_ip, $ip_list)) {
            $private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.16\..*/', '/^10.\.*/', '/^224.\.*/', '/^240.\.*/');
            $client_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
        }
    }
    // Return the Client IP.
    return $client_ip;
}

function pageBrowser($totalrows, $numLimit, $amm, $queryStr, $numBegin, $start, $begin, $num) {
    $larrow = "&nbsp;&lt;&nbsp;previous&nbsp;"; //You can either have an image or text, eg. Previous
    $rarrow = "&nbsp;next&nbsp;&gt;&nbsp;"; //You can either have an image or text, eg. Next
    $wholePiece = ""; //This appears in front of your page numbers
    if ($totalrows > 0) {
        $numSoFar = 1;
        $cycle = ceil($totalrows / $amm);
        if (!isset($numBegin) || $numBegin < 1) {
            $numBegin = 1;
            $num = 1;
        }//end if
        if (!isset($start) || $start < 0) {
            $minus = $numBegin - 1;
            $start = $minus * $amm;
        }//end if
        if (!isset($begin)) {
            $begin = $start;
        }//end if
        $preBegin = $numBegin - $numLimit;
        $preStart = $amm * $numLimit;
        $preStart = $start - $preStart;
        $preVBegin = $start - $amm;
        $preRedBegin = $numBegin - 1;
        if ($start > 0 || $numBegin > 1) {
            $wholePiece .= "<a href='?num=" . $preRedBegin
            . "&start=" . $preStart
            . "&numBegin=" . $preBegin
            . "&begin=" . $preVBegin
            . $queryStr . "'>"
            . $larrow . "</a>\n";
        }//end if
        for ($i = $numBegin; $i <= $cycle; $i++) {
            if ($numSoFar == $numLimit + 1) {
                $piece = "<a href='?numBegin=" . $i
                . "&num=" . $i
                . "&begin=" . $start
                . $queryStr . "'>"
                . $rarrow . "</a>\n";
                $wholePiece .= $piece;
                break;
            }//end if
            $piece = "<a href='?begin=" . $start
            . "&num=" . $i
            . "&numBegin=" . $numBegin
            . $queryStr
            . "' style='border:1px solid;padding:2px 5px;'>";
            if ($num == $i) {
                $piece .= "<b>$i</b>";
            }//end if
            else {
                $piece .= "$i";
            }//end else
            $piece .= "</a>\n";
            $start = $start + $amm;
            $numSoFar++;
            $wholePiece .= $piece;
        }//end for loop
        $wholePiece .= "\n";
        $wheBeg = $begin + 1;
        $wheEnd = $begin + $amm;
        $wheToWhe = "<b>" . $wheBeg . "</b> - <b>";
        if ($totalrows <= $wheEnd) {
            $wheToWhe .= $totalrows . "</b>";
        }//end if
        else {
            $wheToWhe .= $wheEnd . "</b>";
        }//end else
        $sqlprod = " LIMIT " . $begin . ", " . $amm;
    }//end first if
    else {
        $wholePiece = "<span class='boldtextblack'>" . MESSAGE_SORRY_NO_RECORDS . "</span><br>";
        $wheToWhe = "<b>0</b> - <b>0</b>";
    }//end else
    return array($sqlprod, $wheToWhe, $wholePiece);
}

//end function
//fucntion for display file path nmae
function ClientFilePathName($PhpSelf) {
  //  $fpath = split("/", $PhpSelf);
    $fpath = explode("/", $PhpSelf);
    $fpath = $fpath[count($fpath) - 1];
    if ($_SERVER['QUERY_STRING'] != '') {
        $fpath = $fpath . '?' . $_SERVER['QUERY_STRING'];
    }//end if
    return ($fpath);
}

//end function

function ClientFileName($PhpSelf) {
   // $fpath = split("/", $PhpSelf);
    $fpath = explode("/", $PhpSelf);

    $fpath = $fpath[count($fpath) - 1];
    return ($fpath);
}

//end function
//fucntion for highligt link display
function Highligt($slectdPage, $chnkPage, $DispName, $Color) {
    if ($slectdPage == $chnkPage) {

        //$retVal = '<span>' . $DispName . '</span>';

        $retVal = $DispName;
    }//end if
    else {
        $retVal = $DispName;
    }//end else
    return ($retVal);
}

//end if
//fucntion to fech from content table the text and assign it to constants
function ContentText() {
    global $conn;
    $sql = mysqli_query($conn, "select C.content_name, L.content from " . TABLEPREFIX . "content C
        LEFT JOIN " . TABLEPREFIX . "content_lang L on C.content_id = L.content_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
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

//end function
//
//fucntion to fech  from content table

function ContentLookUp($name) {
    global $conn;
    $sql = mysqli_query($conn, "select L.content, L.content_title from " . TABLEPREFIX . "content C
        LEFT JOIN " . TABLEPREFIX . "content_lang L on C.content_id = L.content_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
        where C.content_name='" . $name . "' and C.content_status='y'") or die(mysqli_error($conn));
    $content_arr = array();
    if (mysqli_num_rows($sql) > 0) {
        $content_arr['content_title'] = utf8_encode(mysqli_result($sql, 0, 'content_title'));
        $content_arr['content'] = utf8_encode(mysqli_result($sql, 0, 'content'));
        return $content_arr;
    }//end if
}

//end function
//fucntion to fech
function DisplayLookUp($name) {
    global $conn;
    $sql = mysqli_query($conn, "select vLookUpDesc from " . TABLEPREFIX . "lookup where nLookUpCode='" . addslashes($name) . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($sql) > 0) {
        return (mysqli_result($sql, 0, 'vLookUpDesc'));
    }//end if
}

//end function
//ordering up
function OrderUp($table, $oldId, $oldPosition, $PositionfieldName, $IdfieldName, $returnPath) {   
    global $conn;
    $sql = mysqli_query($conn, "select min($PositionfieldName) as min from " . $table . " where $PositionfieldName>$oldPosition") or die(mysqli_error($conn));
    $newPosition = mysqli_result($sql, 0, 'min');
    
    $sqlId = mysqli_query($conn, "select * from " . $table . " where $PositionfieldName=$newPosition") or die(mysqli_error($conn));
    $newId = mysqli_result($sqlId, 0, $IdfieldName);
    mysqli_query($conn, "update  " . $table . " set $PositionfieldName='" . $newPosition . "' where $IdfieldName='" . $oldId . "'") or die(mysqli_error($conn));
    mysqli_query($conn, "update  " . $table . " set $PositionfieldName='" . $oldPosition . "' where $IdfieldName='" . $newId . "'") or die(mysqli_error($conn));
    if ($_GET['ddlCategory'] != '') {
        header($returnPath . '?ddlCategory=' . $_GET['ddlCategory']);
    }//end else
    else {
        header($returnPath);
    }
}

//ordering down
function OrderDown($table, $oldId, $oldPosition, $PositionfieldName, $IdfieldName, $returnPath) {
    global $conn;
    $sql = mysqli_query($conn, "select max($PositionfieldName) as max from $table where $PositionfieldName<$oldPosition") or die(mysqli_error($conn));
    $newPosition = mysqli_result($sql, 0, 'max');
    $sqlId = mysqli_query($conn, "select * from $table where $PositionfieldName=$newPosition") or die(mysqli_error($conn));
    $newId = mysqli_result($sqlId, 0, $IdfieldName);
    mysqli_query($conn, "update  $table set $PositionfieldName='" . $newPosition . "' where $IdfieldName='" . $oldId . "'") or die(mysqli_error($conn));
    mysqli_query($conn, "update  $table set $PositionfieldName='" . $oldPosition . "' where $IdfieldName='" . $newId . "'") or die(mysqli_error($conn));
    if ($_GET['ddlCategory'] != '') {
        header($returnPath . '?ddlCategory=' . $_GET['ddlCategory']);
    }//end else
    else {
        header($returnPath);
    }
}


//function to return category name
function CategoryName($table, $Id) {
    global $conn;
    $sql = mysqli_query($conn, "select L.vCategoryDesc from " . $table . " c
        LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
        where c.nCategoryId='" . $Id . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($sql) > 0) { 
        return (mysqli_result($sql, 0, 'vCategoryDesc'));
    }//end if
}

//end function
function change_date_format($txtPostDate,$format='mysql-to-mmddyy'){
    $ddt = explode(' ',$txtPostDate);
    $result_Date = '';
    if ($format=='mysql-to-mmddyy'){
        if ($ddt[0] != '0000-00-00'){
            $ddt_arr = explode('-', $ddt[0]);
            $result_Date = $ddt_arr[1].'/'.$ddt_arr[2].'/'.$ddt_arr[0];
        }
    }
    else if ($format=='mmddyy-to-mysql'){
        if ($ddt[0] != '00-00-0000'){
            $ddt_arr = explode('/', $ddt[0]);
            $result_Date = $ddt_arr[2].'-'.$ddt_arr[0].'-'.$ddt_arr[1];
        }
    }

    return $result_Date;
}

//function for mailing purpose
function send_mail($email_to, $subject, $message, $from_email, $from_name='') {
    if ($from_name == '') {
        $from_name = 'Admin';
    }//end if
    $mail = new PHPMailer\PHPMailer\PHPMailer(false);
    $encryptionMethod = DisplayLookUp('encryption');

    try {
    //Server settings
    $mail->SMTPDebug = 0;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = DisplayLookUp('smtp_host');                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = DisplayLookUp('smtp_email');                     // SMTP username
    $mail->Password   = openssl_decrypt(DisplayLookUp('smtp_password'), $encryptionMethod, DisplayLookUp('secret_hash'));     // SMTP password
    $mail->SMTPSecure =  DisplayLookUp('smtp_protocol');        // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = DisplayLookUp('smtp_port');             // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    //Recipients
    $mail->setFrom($from_email, $from_name);
    $mail->addAddress($email_to);               // Name is optional
    $mail->addReplyTo($from_email, $from_name);
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    // Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $message;
    

    @$mail->send();

} catch (Exception $e) {
    //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

}

//end fucntion
//function for mail style
function MailStyle($sName, $Url) {
    $Url = '.';
    if (!file_exists($Url . '/themes/' . $sName)) $Url = '..';
    
    $contents = file_get_contents($Url . '/themes/' . $sName);
    $contents = str_replace('url(', 'url(' . $Url . '/' . $sName . '/', $contents);
    return ($contents);
}


/*
 * generate random password
 */


function generatePassword ($length = 8)
{

    // start with a blank password
    $password = "";

    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);

    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
      $length = $maxlength;
  }

    // set up a counter for how many characters are in the password so far
  $i = 0;

    // add random characters to $password until $length is reached
  while ($i < $length) {

      // pick a random character from the possible ones
      $char = substr($possible, mt_rand(0, $maxlength-1), 1);

      // have we already used this character in $password?
      if (!strstr($password, $char)) {
        // no, so it's OK to add it onto the end of whatever we've already got...
        $password .= $char;
        // ... and increase the counter by one
        $i++;
    }

}

    // done!
return $password;
}


//end if
//function to select values from a table
function select_rows($table, $fieldlist, $condition) {
    $sql = "select $fieldlist from $table $condition ";
    $resArray = QueryResult($sql, 'Select');
    return ($resArray);
}

//end function
//function to select values without execute from a table
function select_rowsExe($table, $fieldlist, $condition) {
    $sql = "select $fieldlist from $table $condition ";
    //$resArray=$this->QueryResult($sql,'Select');
    return ($sql);
}

//end function
//fuction for fetch result
function fetchSingleValue($ResourceArray, $Field) {
    if (mysqli_num_rows($ResourceArray) > 0) {
        return (mysqli_result($ResourceArray, 0, $Field));
    }//end if
}

//end function
//function for execute query
function QueryResult($sql, $PVal) {
    global $conn;
    if ($sql != NULL) {
        $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        return $rs;
    }//end if
    else {
        return '<b>MySQL Error</b>: Empty Query!';
    }//end else
}

//end function
//function to insert values in a table
function insert_fields($table, $in_fieldlist, $in_values) {
    $sql = "insert into $table ($in_fieldlist) values ($in_values)";
    $resArray = QueryResult($sql, 'Insert');
    return ($resArray);
}

//end function
//function to delete values from a table
function delete_rows($table, $condition) {
    $sql = "delete from $table $condition";
    $resArray = QueryResult($sql, 'Delete');
    return ($resArray);
}

//end function
//function to update values in a table
function update_rows($table, $fieldlist, $condition) {
    $sql = "update $table set $fieldlist $condition";
    $resArray = QueryResult($sql, 'Update');
    return ($resArray);
}

//end function
//function for select box
//$sOptionArray contains option vaalue array
function select_tag($sName, $sClass, $sConditions, $sOptionArray, $chkValue) {
    $selctbox = '<select class="' . $sClass . '" name="' . $sName . '" ' . $sConditions . '>';
    //extract array
    foreach ($sOptionArray as $key => $val) {
        $selctbox.=select_option_tag($key, $val, $chkValue);
    }//end for loop
    $selctbox.='</select>';
    return ($selctbox);
}

//end funciton
//function for option
function select_option_tag($key, $val, $chkValue) {
    //checking selected val
    switch ($key) {
        case $chkValue:
        $selectd = "selected";
        break;

        case " ":
        $selectd = "";
        break;

        default:
        $selectd = "";
        break;
    }//end switch

    $opt = '<option value="' . $key . '" ' . $selectd . '>' . $val . '</option>';
    return ($opt);
}

//end funciton

function resizeImg($imgPath, $maxWidth, $maxHeight, $directOutput = true, $quality = 90, $verbose, $imageType) {
    // get image size infos (0 width and 1 height,
    //     2 is (1 = GIF, 2 = JPG, 3 = PNG)
//echo $imgPath ; exit;
    $size = getimagesize($imgPath);

    // break and return false if failed to read image infos
    if (!$size) {
        if ($verbose && !$directOutput)
            echo "<br />".ERROR."<br />";//Not able to read image infos.
        return false;
    }

    // relation: width/height
    $relation = $size[0] / $size[1];
    // maximal size (if parameter == false, no resizing will be made)
    $maxSize = array($maxWidth ? $maxWidth : $size[0], $maxHeight ? $maxHeight : $size[1]);
    // declaring array for new size (initial value = original size)
    $newSize = $size;
    // width/height relation
    $relation = array($size[1] / $size[0], $size[0] / $size[1]);


    if (($newSize[0] > $maxWidth)) {
        $newSize[0] = $maxSize[0];
        $newSize[1] = $newSize[0] * $relation[0];
    }

    if (($newSize[1] > $maxHeight)) {
        $newSize[1] = $maxSize[1];
        $newSize[0] = $newSize[1] * $relation[1];
    }
    $newSize[0] = $maxSize[0];
    $newSize[1] = $maxSize[1];
    // create image

    switch ($size[2]) {
        case 1:
        if (function_exists("imagecreatefromgif")) {
            $originalImage = imagecreatefromgif($imgPath);
        } else {
            if ($verbose && !$directOutput)
                    echo "<br />".ERROR."<br />";//No GIF support in this php installation, sorry.
                return false;
            }
            break;
            case 2: $originalImage = imagecreatefromjpeg($imgPath);
            break;
            case 3: $originalImage = imagecreatefrompng($imgPath);
            break;
            default:
            if ($verbose && !$directOutput)
                echo "<br />".ERROR_INVALID_IMAGE."<br />";//No valid image type.
            return false;
        }


    // create new image

        $resizedImage = imagecreatetruecolor($newSize[0], $newSize[1]);
//print_r($size); exit;
    //checking transparency start here
        if (($size[2] == 1) || ($size[2] == 3)) {
            $trnprt_indx = imagecolortransparent($originalImage);
        // If we have a specific transparent color
            if ($trnprt_indx >= 0) {
            // Get the original image's transparent color's RGB values
                $trnprt_color = @imagecolorsforindex($originalImage, $trnprt_indx);
            // Allocate the same color in the new image resource
                $trnprt_indx = @imagecolorallocate($resizedImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
            // Completely fill the background of the new image with allocated color.
                //@imagefill($resizedImage, 0, 0, $trnprt_indx);
            // Set the background color for new image to transparent
                imagecolortransparent($resizedImage, $trnprt_indx);
        }//end if
        // Always make a transparent background color for PNGs that don't have one allocated already
        else if ($size[2] == 3) {
            // Turn off transparency blending (temporarily)
            imagealphablending($resizedImage, false);

            // Create a new transparent color for image
            $color = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
            

            // Completely fill the background of the new image with allocated color.
            imagefill($resizedImage, 0, 0, $color);

            // Restore transparency blending
            imagesavealpha($resizedImage, true);
        }//end else if
    }//end if transparency check
    //checking transparency end here


    imagecopyresampled($resizedImage, $originalImage, 0, 0, 0, 0, $newSize[0], $newSize[1], $size[0], $size[1]);

    $rz = $imgPath;

    // output or save
    if ($directOutput) {
        imagejpeg($resizedImage);
    } else {

        $rz = preg_replace("/\.([a-zA-Z]{3,4})$/", "" . $imageType . ".jpg", $imgPath);
        imagejpeg($resizedImage, $rz, $quality);
    }
    // return true if successfull
    //echo $rz; exit;
    return $rz;
}


function resizeNew($img_name,$filename,$new_w,$new_h){
    /* Get original image x y*/
    $ext = substr(strrchr($img_name, '.'), 1);
 
                if(!strcmp("jpg",$ext) || !strcmp("jpeg",$ext)){
                         $src_img=imagecreatefromjpeg($img_name);
                }
 
                if(!strcmp("png",$ext)){
                         $src_img=imagecreatefrompng($img_name);
                }
                
                if(!strcmp("gif",$ext)){
                         $src_img=imagecreatefromgif($img_name);   
                }
 
                $old_x=imageSX($src_img);
                $old_y=imageSY($src_img);
 
                if($new_h <= 0){
                         // Calculate aspect ratio
                         $wRatio = $new_w / $old_x ;
 
                         $thumb_h = ceil($wRatio * $old_y);
                         $thumb_w = $new_w;
                 }else{
                         $thumb_w = $new_w;
                 }
 
                if($new_w <= 0){
                        // Calculate aspect ratio
                        $hRatio = $new_h / $old_y ;
 
                        $thumb_w = ceil($hRatio * $old_x);
                        $thumb_h = $new_h;
                }else{
                        $thumb_h = $new_h;
                }
 
                $dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
                imageAlphaBlending($dst_img, false);
                imageSaveAlpha($dst_img, true);
                imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
 
                if(!strcmp("gif",$ext)){
                    $filename.= time().'_logo_thumb.gif';
                         imagegif($dst_img,$filename);
                }else if(!strcmp("png",$ext)){
                    $filename.= time().'_logo_thumb.png';
                         imagepng($dst_img,$filename);
                }
                else{
                         $filename.= time().'_logo_thumb.jpg';
                         imagejpeg($dst_img,$filename);
                }
 
                
            
               //destroys source and destination images.
               imagedestroy($dst_img);
               imagedestroy($src_img);
               return $filename;
}



function ImageType($image) {
    list($width, $height, $type, $attr) = @getimagesize($image);
    switch ($type) {
        case 1 :
        $returntype = "gif";
        break;
        case 2 :
        $returntype = "jpg";
        break;
        case 3 :
        $returntype = "png";
        break;
        case 4 :
        $returntype = "swf";
        break;
        case 5 :
        $returntype = "psd";
        break;
        case 6 :
        $returntype = "bmp";
        break;
        case 7 :
        $returntype = "tiff";
        break;
        case 8 :
        $returntype = "tiff";
        break;
        default :
        $returntype = "notsupportted";
        break;
    }//end switch

    return $returntype . ":$width:$height";
}

//end function

function isValidWebImageType($mimetype) {
    if (($mimetype == "image/pjpeg") || ($mimetype == "image/jpeg") || ($mimetype == "image/x-png") || ($mimetype == "image/png") || ($mimetype == "image/gif")) {// ||
            //($mimetype == "image/x-windows-bmp") || ($mimetype == "image/bmp")) {
        return true;
    }//end if
    else {
        return false;
    }//end else
}

//end function

function isValidWebImageType2($mimetype, $filename, $tempname) {
    $blacklist = array("php", "phtml", "php3", "php4", "js", "shtml", "pl", "py", "exe");
    foreach ($blacklist as $file) {
        if (preg_match("/\.$file\$/i", "$filename")) {
            return false;
        }
    }
    //check if its image file
    if (!getimagesize($tempname)) {
        return false;
    }

    if (($mimetype == "image/pjpeg") || ($mimetype == "image/jpeg") || ($mimetype == "image/x-png") || ($mimetype == "image/png") || ($mimetype == "image/gif") ||
        ($mimetype == "image/x-windows-bmp") || ($mimetype == "image/bmp")) {
        return true;
} else {
    return false;
}
}

//function for image name replace
function ReplaceArray($name) {
    $name = str_replace(' ', '_', $name);
    $name = str_replace("'", '_', $name);
    return ($name);
}

//end fucnction
//function for checking us phone number
/* function VALIDATE_USPHONE($phonenumber,$useareacode=true)
  {
  if ( preg_match("/^[ ]*[(]{0,1}[ ]*[0-9]{3,3}[ ]*[)]{0,1}[-]{0,1}[ ]*[0-9]{3,3}[ ]*[-]{0,1}[ ]*[0-9]{4,4}[ ]*$/",$phonenumber) || (preg_match("/^[ ]*[0-9]{3,3}[ ]*[-]{0,1}[ ]*[0-9]{4,4}[ ]*$/",$phonenumber) && !$useareacode))
  {
  return eregi_replace("[^0-9]", "", $phonenumber);
  }//end if
  else
  {
  return false;
  }//end else
}//end fucntion */

//function for checking phone number
function check_phone($number=0) {
    $flag = true;
    $normal = "/^[0-9+\-]*$/";
    if (!preg_match($normal, $number)) {
        $flag = false;
    }//end if
    return $flag;
}

//end fucntion
//listing client modules and categories
function list_clinet_modules($TablePrefix) {
    global $conn;
    //fetching category details
    $parent = mysqli_query($conn, "select * from " . $TablePrefix . "client_module_category where nParentId='0' order by  vCategoryTitle desc")
    or die(ERROR);

    //check point status
    switch (DisplayLookUp('EnablePoint')) {
        case "1":
        case "2":
        $CheckArrayVal = array('Sell', 'Wish', 'Swap');
        break;

        case "0":
        $CheckArrayVal = array('Wish', 'Swap');
        break;
    }//end switch

    if (mysqli_num_rows($parent) > 0) {
        for ($i = 0; $i < mysqli_num_rows($parent); $i++) {
            $parent_id = mysqli_result($parent, $i, 'nCategoryId');
            $parent_name = mysqli_result($parent, $i, 'vCategoryTitle');

            //fetching modules
            $toplinks = mysqli_query($conn, "select * from " . $TablePrefix . "client_module_category where nTmp_status='1' and
              nParentId='" . $parent_id . "'") or die("Errors found");
            if (mysqli_num_rows($toplinks) > 0) {
                ?>
                <table width="100%" cellspacing="0" cellpadding="2" class="maintext2">
                    <?php
                // Colors
                    $color1 = '#e0e0e0';
                    $color2 = '#ffffff';
                    $bgcolor = '#ffffff';
                    ?>
                    <tr bgcolor="<?php echo $color1; ?>">
                        <td class="clintheader" style="border: 1px solid #a0a0a0">&nbsp;<strong><?php echo ucfirst($parent_name); ?></strong></td>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_array($toplinks)) {
                        if (!in_array($row['vCategoryTitle'], $CheckArrayVal)) {
                            if ($row[vActive] == '1') {
                                $checked = 'checked';
                            } else {
                                $checked = '';
                            }
                            ?>
                            <tr bgcolor="<?php echo $bgcolor; ?>">
                                <td class="maintext">
                                    <input type="checkbox" value="<?php echo $row['nCategoryId']; ?>" name="modules[]" <?php echo $checked; ?>>&nbsp;<strong><?php echo ucfirst($row['vCategoryTitle']); ?></strong>
                                </td>
                            </tr>
                            <?php
                    }//end array if check
                }//end while
                ?>
            </table>
            <?php
            }//end if
        }//end for loop
    }//end if
}

//end function

function toplinks($TablePrefix) {
    global $conn;
   /* echo "select * from " . $TablePrefix . "Client_Module_Category where vActive='1' and nCposition='Top'
   and nParentId!='0'" ; die();*/
   $toplinks = mysqli_query($conn, "select * from " . $TablePrefix . "client_module_category where vActive='1' and nCposition='Top'
       and nParentId!='0'") or die(ERROR);
   if (mysqli_num_rows($toplinks) > 0) {
    return $toplinks;
    }//end if
    else {
        return 0;
    }//end else
}

//end fucntion

function session_toplinks($TablePrefix) {
    global $conn;
   /* echo  "select * from " . $TablePrefix . "Client_Module_Category where vActive='1' and (nCposition='Top' ||  nCposition='Logged') and   
   vCategoryTitle!='Register' and vCategoryTitle!='Login' and nParentId!='0' order by nParentId";*/
   $sessiontoplinks = mysqli_query($conn, "select * from " . $TablePrefix . "client_module_category where vActive='1' and (nCposition='Top' ||  nCposition='Logged') and   
     vCategoryTitle!='Register' and vCategoryTitle!='Login' and nParentId!='0' order by nParentId ") or die(ERROR);
   if (mysqli_num_rows($sessiontoplinks) > 0) {
    return $sessiontoplinks;
    }//end if
    else {
        return 0;
    }//end else
}

function validate_if_user_exists($nUserId,$TablePrefix){
    global $conn;   
    $sessiontoplinks = mysqli_query($conn, "SELECT * FROM `".$TablePrefix."users` WHERE `nUserId` = '".trim($nUserId)."' AND `vDelStatus` = 0") or die(ERROR);
    if (mysqli_num_rows($sessiontoplinks) > 0){
        return 1;
    }
    else {
        return 0;
    }
}
function validate_if_user_active($nUserId,$TablePrefix){
    global $conn;   
    $sessiontoplinks = mysqli_query($conn, "SELECT * FROM `".$TablePrefix."users` WHERE `nUserId` = '".trim($nUserId)."' AND `vStatus` ='0'") or die(ERROR);
    if (mysqli_num_rows($sessiontoplinks) > 0){
        return 1;
    }
    else {
        return 0;
    }
}

//end fucntion

function logged_toplinks($TablePrefix) {
    global $conn;
    $loggedtoplinks = mysqli_query($conn, "select * from " . $TablePrefix . "client_module_category where vActive='1' and nCposition='Logged'
       and nParentId!='0'") or die(ERROR);
    if (mysqli_num_rows($loggedtoplinks) > 0) {
        return $loggedtoplinks;
    }//end if
    else {
        return 0;
    }//end else
}

//end function

function footerlinks($TablePrefix) {
    global $conn;
    $footerlinks = mysqli_query($conn, "select * from " . $TablePrefix . "client_module_category where vActive='1' and nCposition='Footer'
      and nParentId!='0' order by nParentId") or die(ERROR);
    if (mysqli_num_rows($footerlinks) > 0) {
        return $footerlinks;
    }//edn if
    else {
        return 0;
    }//end else
}

//end fuction

function topheaderlinks($TablePrefix) {
    global $conn;
    $topheaderlinks = mysqli_query($conn, "select * from " . $TablePrefix . "client_module_category where vActive='1' and nCposition='TopHeader'
       and nParentId!='0'") or die(ERROR);
    if (mysqli_num_rows($topheaderlinks) > 0) {
        return $topheaderlinks;
    }//edn if
    else {
        return 0;
    }//end else
}

//end fuction

function showRichText($tmpString) {
    $tmpString = str_replace(chr(146), chr(39), $tmpString);
    $tmpString = str_replace("'", "&#39;", $tmpString);

    //convert all types of double quotes
    $tmpString = str_replace(chr(147), chr(34), $tmpString);
    $tmpString = str_replace(chr(148), chr(34), $tmpString);

    //replace carriage returns & line feeds
    $tmpString = str_replace(chr(10), "", $tmpString);
    $tmpString = str_replace(chr(13), "", $tmpString);

    return $tmpString;
}

//currency code array
function CurrencyCodeCheck($chkCur) {
    $crrencyArray = array("AUD" => "A $", "CAD" => "C $", "EUR" => "&euro;", "GBP" => "&pound;", "JPY" => "&yen;", "USD" => "$", "NZD" => "$", "HKD" => "HK$", "SGD" => "$",
        "CZK" => "Kc", "DKK" => "kr", "HUF" => "Ft", "ILS" => "&#8362;", "MXN" => "$", "NOK" => "kr", "PLN" => "zl", "SEK" => "kr", "CHF" => "CHF");

    foreach ($crrencyArray as $ckey => $cval) {
        if ($ckey == $chkCur) {
            return ($cval);
        }//end if
    }//end foreach
}

//end function
//check category
function CheckCategory($cid) {
    global $conn;
    $sql = mysqli_query($conn, "SELECT nCategoryId FROM " . TABLEPREFIX . "category  WHERE nParentId='" . $cid . "'")
    or die(mysqli_error($conn));
    if (mysqli_num_rows($sql) > 0) {
        return (mysqli_num_rows($sql));
    }//end if
    else {
        return 0;
    }//end else
}

/*
 * gret Plan amount
 */

function planAmount($planId)
{
    global $conn;
    $planSql = "SELECT nPrice FROM ".TABLEPREFIX."plan WHERE nPlanId = '".$planId."'";
    $planRs = mysqli_query($conn, $planSql);
    $planRw = mysqli_fetch_array($planRs);
    return $planRw['nPrice'];
}

//end fucntion
//fucntion for disaply style array
function ShowEmailContent($vType) {
    $ContentwArray = array("addsales" => "New Sale Item Addition", "addsurvey" => "New Survey Addition", "soldout" => "Item Sold Out Notification", "contactus" => "Contact Us Alert",
        "featured" => "Featured Item Addition", "forgotpass" => "Forgot Password Notification", "welcomeMailUser" => "Welcome Mail After Registration 1", "payea2" => "Welcome Mail After Registration 2",
        "passwordreset" => "Reset Password Notification", "tellfrnd" => "Tell A Friend Alert", "settled" => "Amount Settlement Notification", "userRegisterEmailFromAdmin" => "Welcome Mail After Registration - Admin",
        "plansubcancel" => "Plan Subscription Cancel", "expired" => "Membership expired alert message", "points" => "Thanks Mail After " . POINT_NAME . " Purchase",
        "SuccessFeeMail" => "Transaction Fee Received");
    asort($ContentwArray);
    echo '<select name="ddlType" class="textbox" onChange="showHint(this.value);">';
    foreach ($ContentwArray as $key => $value) {
        ?>
        <option value="<?php echo $key ?>" <?php if ($key == $vType) {
            echo 'selected';
        } ?>><?php echo $value ?></option>
        <?php
    }//end foreach
    echo '</select>';
}

//end function

function getLicense() {
    global $conn;
    $sql = "SELECT vLookUpDesc FROM " . TABLEPREFIX . "lookup WHERE `nLookUpCode` = 'vLicenceKey'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $var_licencekey = stripslashes($row["vLookUpDesc"]);
    }
    return $var_licencekey;
}

function cancelpaypalOnTestMode($devuser, $devpass, $user, $pass, $subscrid) {
    $paypalurl = "https://www.sandbox.paypal.com";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'login_email=' . urlencode($devuser) . '&login_password=' . urlencode($devpass) . '&cmd=_login-submit');
    curl_setopt($ch, CURLOPT_URL, "https://developer.paypal.com/cgi-bin/devscr?__track=_home:login/main:_login-submit");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, $ref);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);

    preg_match_all("/Set\-Cookie\:.*?\;/", $result, $matches);
    $cookiejar = "";
    foreach ($matches[0] as $cookie) {
        $cookiejar .= str_replace("Set-Cookie: ", "", $cookie) . ' ';
    }

    preg_match("/Location\:.*?\n/", $result, $matches);
    $nexturl = str_replace("\r", "", str_replace("\n", "", str_replace("Location: ", "", $matches[0])));
    $ch = curl_init();
    $nexturl = "https://developer.paypal.com/cgi-bin/devscr?cmd=_login-done&login_access=0";
    curl_setopt($ch, CURLOPT_URL, $nexturl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, "$paypalurl/");
    curl_setopt($ch, CURLOPT_COOKIE, $cookiejar);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'login_email=' . urlencode($user) . '&login_password=' . urlencode($pass) . '&cmd=_login-submit');
    curl_setopt($ch, CURLOPT_URL, "$paypalurl/cgi-bin/webscr");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, "$paypalurl/");
    curl_setopt($ch, CURLOPT_COOKIE, $cookiejar);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);


    preg_match_all("/Set\-Cookie\:.*?\;/", $result, $matches);
    $cookiejar = "";
    foreach ($matches[0] as $cookie) {
        $cookiejar .= str_replace("Set-Cookie: ", "", $cookie) . ' ';
    }

    preg_match("/Location\:.*?\n/", $result, $matches);
    $nexturl = str_replace("\r", "", str_replace("\n", "", str_replace("Location: ", "", $matches[0])));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $nexturl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, "$paypalurl/");
    curl_setopt($ch, CURLOPT_COOKIE, $cookiejar);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);


    $lasturl = $nexturl;
    preg_match("/Refresh\:.*?\n/", $result, $matches);
    $nexturl = str_replace("\r", "", str_replace("\n", "", str_replace("Refresh: 1; URL=", "", $matches[0])));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $nexturl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, $lasturl);
    curl_setopt($ch, CURLOPT_COOKIE, $cookiejar);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'return_to=&history_cache=&item=0&search_type=' . urlencode($subscrid) . '&search_first_type=trans_id&span=broad&for=4&from_a=07&from_b=16&from_c=2004&to_a=08&to_b=15&to_c=2004&cmd=_history-search&submit.x=Submit');
    curl_setopt($ch, CURLOPT_URL, "$paypalurl/row/cgi-bin/webscr?__track=_history-search:p/acc/history_search:_history-search");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, "$paypalurl/row/cgi-bin/webscr?cmd=_history-search");
    curl_setopt($ch, CURLOPT_COOKIE, $cookiejar);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    //echo $result;
    curl_close($ch);
    // if (!strpos($result, 'Active')) {
    // echo "active";
    //}else{
    //echo "***********notactive";
//	}
    //if (!strpos($result, 'Active')) {
    // return false;
    //}


    preg_match("/\<input type\=\"hidden\" id=\"\"  name\=\"info\".*?\"\>/", $result, $matches);
    if (count($matches) > 0) {
        $info = str_replace('">', "", str_replace('<input type="hidden" name="info" value="', "", $matches[0]));
    } else {
        preg_match("<name\=\"info\".*?\".*?\"\>>", $result, $matches);
        $info = str_replace('">', "", str_replace('name="info" value="', "", $matches[0]));
    }
    //echo "info==".$info;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'return_to=&history_cache=&info=' . urlencode($info) . '&sub_info=&email_alias=&cmd=_subscr-cancel-submit&reverse.x=Cancel Subscription');
    curl_setopt($ch, CURLOPT_URL, "$paypalurl/row/cgi-bin/webscr?__track=_subscr-details-submit:p/acc/subscribe-cancel-confirm:_subscr-cancel-submit");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, "$paypalurl/row/cgi-bin/webscr?__track=_history-search:p/acc/history-subscribe:_subscr-details-submit");
    curl_setopt($ch, CURLOPT_COOKIE, $cookiejar);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);

    if (strpos($result, 'This subscription has been cancelled.')) {
        return true;
    }//end if
    else {
        return false;
    }//end else
}

//end function

function ppsubscrcancel($paypalurl, $user, $pass, $subscrid) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'login_email=' . urlencode($user) . '&login_password=' . urlencode($pass) . '&cmd=_login-submit');
    curl_setopt($ch, CURLOPT_URL, "$paypalurl/cgi-bin/webscr");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, "$paypalurl/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);

    preg_match_all("/Set\-Cookie\:.*?\;/", $result, $matches);
    $cookiejar = "";
    foreach ($matches[0] as $cookie) {
        $cookiejar .= str_replace("Set-Cookie: ", "", $cookie) . ' ';
    }

    preg_match("/Location\:.*?\n/", $result, $matches);
    $nexturl = str_replace("\r", "", str_replace("\n", "", str_replace("Location: ", "", $matches[0])));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $nexturl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, "$paypalurl/");
    curl_setopt($ch, CURLOPT_COOKIE, $cookiejar);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);

    $lasturl = $nexturl;
    preg_match("/Refresh\:.*?\n/", $result, $matches);
    $nexturl = str_replace("\r", "", str_replace("\n", "", str_replace("Refresh: 1; URL=", "", $matches[0])));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $nexturl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, $lasturl);
    curl_setopt($ch, CURLOPT_COOKIE, $cookiejar);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'return_to=&history_cache=&item=0&search_type=' . urlencode($subscrid) . '&search_first_type=trans_id&span=broad&for=4&from_a=07&from_b=16&from_c=2004&to_a=08&to_b=15&to_c=2004&cmd=_history-search&submit.x=Submit');
    curl_setopt($ch, CURLOPT_URL, "$paypalurl/row/cgi-bin/webscr?__track=_history-search:p/acc/history_search:_history-search");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, "$paypalurl/row/cgi-bin/webscr?cmd=_history-search");
    curl_setopt($ch, CURLOPT_COOKIE, $cookiejar);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);
    
    if (!strpos($result, 'Active')) {
        return false;
    }

    preg_match("/\<input type\=\"hidden\" name\=\"info\".*?\"\>/", $result, $matches);
    $info = str_replace('">', "", str_replace('<input type="hidden" name="info" value="', "", $matches[0]));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'return_to=&history_cache=&info=' . urlencode($info) . '&sub_info=&email_alias=&cmd=_subscr-cancel-submit&reverse.x=Cancel Subscription');
    curl_setopt($ch, CURLOPT_URL, "$paypalurl/row/cgi-bin/webscr?__track=_subscr-details-submit:p/acc/subscribe-cancel-confirm:_subscr-cancel-submit");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_REFERER, "$paypalurl/row/cgi-bin/webscr?__track=_history-search:p/acc/history-subscribe:_subscr-details-submit");
    curl_setopt($ch, CURLOPT_COOKIE, $cookiejar);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $result = curl_exec($ch);
    curl_close($ch);
    
    if (strpos($result, 'This subscription has been cancelled.')) {
        return true;
    }//end if
    else {
        return false;
    }//end else
}

//end function

function dateFormat($input_date, $input_format, $output_format) {
    preg_match("/^([\w]*)/i", $input_date, $regs);
    $sep = substr($input_date, strlen($regs[0]), 1);
    $label = explode($sep, $input_format);
    $value = explode($sep, $input_date);
    $array_date = array_combine($label, $value);
    if (in_array('Y', $label)) {
        $year = $array_date['Y'];
    } elseif (in_array('y', $label)) {
        $year = $year = $array_date['y'];
    } else {
        return false;
    }

    $output_date = date($output_format, mktime(0, 0, 0, $array_date['m'], $array_date['d'], $year));
    return $output_date;
}

//expor as csv
function exportMysqlToCsv($filename = 'export.csv', $headerArray, $listArray) {
    //append date and time
    $filename = date('Y-m-d') . '_' . time() . '_' . $filename;

    $csv_terminated = "\n";
    $csv_separator = ",";
    $csv_enclosed = '"';
    $csv_escaped = "\\";
    $sql_query = $sqlValue;

    $schema_insert = '';

    foreach ($headerArray as $val) {
        $l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, stripslashes($val)) . $csv_enclosed;
        $schema_insert .= $l;
        $schema_insert .= $csv_separator;
    }//end for each

    $out = trim(substr($schema_insert, 0, -1));
    $out .= $csv_terminated;

    // Format the data
    foreach ($listArray as $lval) {
    //while ($row = mysqli_fetch_array($result))
        $schema_insert = '';
        for ($j = 0; $j < count($lval); $j++) {
            if ($lval[$j] == '0' || $lval[$j] != '') {
                if ($csv_enclosed == '') {
                    $schema_insert .= $lval[$j];
                } else {
                    $schema_insert .= $csv_enclosed .
                    str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $lval[$j]) . $csv_enclosed;
                }
            } else {
                $schema_insert .= '';
            }
            if ($j < count($lval) - 1) {
                $schema_insert .= $csv_separator;
            }
        } // end for
        $out .= $schema_insert;
        $out .= $csv_terminated;
        $cnt++;
    } // end while
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Length: " . strlen($out));
    // Output to browser with appropriate mime type, you choose ;)
    header("Content-type: text/x-csv");
    //header("Content-type: text/csv");
    //header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=$filename");
    echo $out;
    exit();
}

//function to return user details
function userProfiles($nUId) {
    global $conn;
    $sqlUserProfile = mysqli_query($conn, "SELECT * FROM " . TABLEPREFIX . "users WHERE nUserId = '" . $nUId . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($sqlUserProfile) > 0) {
        $array = mysqli_fetch_array($sqlUserProfile);
    }//end if
    else $array['vCountry'] = 'United States';
    mysqli_free_result($sqlUserProfile);
    return $array;
}

//end function
//fucntion for disaply style array
function ShowTemplates($style) {
    $StylewArray = array();
    //create array
    foreach (showThemes() as $key => $val) {
        $StylewArray[$val['theme_file']] = $key;
    }//end foreach

    asort($StylewArray);
    echo '<select name="ddlStyle" class="textbox" id="ddlStyle" onChange="showHint(this.value);">';
    foreach ($StylewArray as $key => $value) {
        ?>
        <option value="<?php echo $key ?>" <?php if ($key == $style) {
            echo 'selected';
        } ?>><?php echo $value ?></option>
        <?php
    }//end foreach
    echo '</select>';
}

//end function
//function for template preview
function TemplatePreview($stylePath) {
    //split style path
    //$styleFolder = @split("[/]", $stylePath);
   $styleFolder = @explode("/", $stylePath);

   $theme_root = '../themes/' . $styleFolder[0];
   $themes_dir = @ opendir($theme_root);
   while (($preview_file = readdir($themes_dir)) !== false) {
    if ($preview_file == 'preview.gif') {
        $preview_file = $theme_root . '/' . $preview_file;
        break;
        }//end if
        @closedir($stylish_dir);
    }//end whil loop
    if (is_dir($theme_dir))
        @closedir($theme_dir);
    return $preview_file;
}

//end function


function checkEscrowRange($Amount,$chkId)
{
    global $conn;
	//fetch listing fee from table
    $sqlRange=mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."escrowrangefee WHERE vActive='1' and nLId!='".$chkId."' ORDER BY nLPosition DESC") or die(mysqli_error($conn));
    if(mysqli_num_rows($sqlRange)>0)
    {
      $rangeArray=array();
      while($arrRange=mysqli_fetch_array($sqlRange))
      {
			//create range array
         for($i=$arrRange['nFrom'];$i<=$arrRange['nTo'];$i++)
         {
            $rangeArray[]=round($i,2);
			}//end for loop

			//checking value comes in which range
			if(in_array($Amount,$rangeArray))
			{
				$exits=1;
				break;
			}//end if
			else
			{
				$exits=0;
			}//end else

			//clear array
			$rangeArray=array();
		}//end while loop
		return ($exits);
	}//end if
}//end function


/*
 * Function to get equivalent price from points
 */

function getEquivalentPriceFromPoint($points){

    $currency = DisplayLookUp("pointvalue");
    $point_value = DisplayLookUp("pointvalue2");

    $amount = ($points*$currency)/$point_value;
    return $amount;
    
}

//checkin range already exists or not
function checkRange($Amount, $chkId) {
    global $conn;
    //fetch listing fee from table
    $sqlRange = mysqli_query($conn, "SELECT * FROM " . TABLEPREFIX . "listingfee WHERE vActive='1' and nLId!='" . $chkId . "' ORDER BY nLPosition DESC") or die(mysqli_error($conn));
    if (mysqli_num_rows($sqlRange) > 0) {
        $rangeArray = array();
        while ($arrRange = mysqli_fetch_array($sqlRange)) {
            //create range array
            for ($i = $arrRange['nFrom']; $i <= $arrRange['nTo']; $i++) {
                $rangeArray[] = round($i, 2);
            }//end for loop
            //checking value comes in which range
            if (in_array($Amount, $rangeArray)) {
                $exits = 1;
                break;
            }//end if
            else {
                $exits = 0;
            }//end else
            //clear array
            $rangeArray = array();
        }//end while loop
        return ($exits);
    }//end if
}

//end function

function get_swaps_ids($db_swapid){//to returns all swap ids of the swaptransaction with the main one
    /*$swap_sql = "select concat(nSwapId,',',nSwapReturnId) as swap_ids from " . TABLEPREFIX . "swaptxn 
                    where nSwapId like %".$db_swapid."% or nSwapReturnId like %".$db_swapid."%";
    $swap_res = mysqli_query($conn, $swap_sql) or die(mysqli_error($conn));
    if ($swap_row = mysqli_fetch_array($swap_res)){
        return $swap_row['swap_ids'];
    }
    else*/
        return $db_swapid;
}

//


//checking given range already exists or not
function checkNewEscrowRange($from, $to, $chkId) {
    global $conn;
    $escrowFeeSql  = "SELECT * FROM " . TABLEPREFIX . "escrowrangefee 
    WHERE vActive='1' 
    AND nLId!='" . $chkId . "' 
    AND ('".$from."' BETWEEN nFrom AND nTo) 
    AND ('".$to."' BETWEEN nFrom AND nTo) 
    AND (above >= $from OR above <= $to) 
    ORDER BY nLPosition DESC"; 
    $sqlRangeRes    = mysqli_query($conn, $escrowFeeSql) or die(mysqli_error($conn));
    
    if(mysqli_num_rows($sqlRangeRes)>0){
        return true;
    }else{
        return false;
    }
}
/*
function checkNewEscrowRange($from,$to,$chkId)
{
	//check decimal start with .1 or or .01
	//@list($val,$dec)=@split("[.]",$defaultValue);
        @list($val,$dec)=@explode("[.]",$defaultValue);

	switch(strlen($dec))
	{
		case "1":
			$increaeVal=0.1;
		break;

		case "2":
			$increaeVal=0.01;
		break;

		case "0":
			$increaeVal=0.01;
	}//end switch

	//fetch listing fee from table
	$sqlRange=mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."escrowrangefee WHERE vActive='1' and nLId!='".$chkId."'  ORDER BY nLPosition DESC") or die(mysqli_error($conn));
	if(mysqli_num_rows($sqlRange)>0)
	{
		$rangeArray=array();
		//create range array
		for($j=$from;$j<=$to;$j+=$increaeVal)
		{
				$checkArray[round($j,2)]=round($j,2);
		}//end for loop
		$checkArray[$to]=$to;

		while($arrRange=mysqli_fetch_array($sqlRange))
		{
			//check decimal start with .1 or or .01
			//@list($val,$dec2)=@split("[.]",$arrRange['nFrom']);
                        @list($val,$dec2)=@explode("[.]",$arrRange['nFrom']);

			switch(strlen($dec2))
			{
				case "1":
					$increaeVal2=0.1;
				break;

				case "2":
					$increaeVal2=0.01;
				break;

				case "0":
					$increaeVal2=0.01;
			}//end switch

			for($i=$arrRange['nFrom'];$i<=$arrRange['nTo'];$i+=$increaeVal2)
			{
				$rangeArray[round($i,2)]=round($i,2);
			}//end for loop
			$rangeArray[$arrRange['nTo']]=$arrRange['nTo'];
		}//end while loop

		$rangeArray=array_unique($rangeArray);
		$checkArray=array_unique($checkArray);

		$identical=array_intersect_assoc($checkArray,$rangeArray);

		$result=array('identical'=>$identical,
						'added'=>array_diff($rangeArray,$checkArray),
						'discarded'=>array_diff($checkArray, $identical)//discarded means present only into OLD
						);

		switch(count($result['identical']))
		{
			case "0":
				$exits=0;
			break;

			default:
				$exits=1;
			break;

		}//end switch
                if(!$exits){
                    $sql = "select * from ".TABLEPREFIX."escrowrangefee WHERE vActive='1' and nLId!='".$chkId."' AND above > '".$to."'";
                    $rs = mysqli_query($conn, $sql);
                    if(mysqli_num_rows($rs)){
                        $exits=1;
                    }
                }
		return ($exits);
	}//end if
}//end function
*/
//
//fuction for listing themes from themes folder
function showThemes() {
    $theme_root = '../themes';
    $themes_dir = @ opendir($theme_root);
    while (($theme_dir = readdir($themes_dir)) !== false) {
        if (is_dir($theme_root . '/' . $theme_dir) && is_readable($theme_root . '/' . $theme_dir)) {
            
            /*deprecated line in 7.4 */
            // if ($theme_dir{0} == '.' || $theme_dir == 'CVS')
            if ($theme_dir[0] == '.' || $theme_dir == 'CVS')
                continue;

            $stylish_dir = @opendir($theme_root . '/' . $theme_dir);
            $found_stylesheet = false;

            while (($theme_file = readdir($stylish_dir)) !== false) {
                if ($theme_file == 'style.css') {
                    $theme_files[$theme_dir] = array('theme_file' => $theme_dir . '/' . $theme_file, 'theme_root' => $theme_root);
                    $found_stylesheet = true;
                    break;
                }//end if
            }//end while loop
            @closedir($stylish_dir);
        }//end first if
    }//end whil loop
    if (is_dir($theme_dir))
        @closedir($theme_dir);
    return $theme_files;
}

//end function
//checking given range already exists or not
/*
function checkNewRange($from, $to, $chkId) {
    //check decimal start with .1 or or .01
   // @list($val, $dec) = @split("[.]", $defaultValue);
    @list($val, $dec) = @explode("[.]", $defaultValue);

    switch (strlen($dec)) {
        case "1":
            $increaeVal = 0.1;
            break;

        case "2":
            $increaeVal = 0.01;
            break;

        case "0":
            $increaeVal = 0.01;
    }//end switch
    //fetch listing fee from table
    $sqlRange = mysqli_query($conn, "SELECT * FROM " . TABLEPREFIX . "ListingFee WHERE vActive='1' and nLId!='" . $chkId . "' ORDER BY nLPosition DESC") or die(mysqli_error($conn));
    if (mysqli_num_rows($sqlRange) > 0) {
        $rangeArray = array();
        //create range array
        for ($j = $from; $j <= $to; $j+=$increaeVal) {
            $checkArray[round($j, 2)] = round($j, 2);
        }//end for loop
        $checkArray[$to] = $to;

        while ($arrRange = mysqli_fetch_array($sqlRange)) {
            //check decimal start with .1 or or .01
           // @list($val, $dec2) = @split("[.]", $arrRange['nFrom']);
             @list($val, $dec2) = @explode("[.]", $arrRange['nFrom']);

            switch (strlen($dec2)) {
                case "1":
                    $increaeVal2 = 0.1;
                    break;

                case "2":
                    $increaeVal2 = 0.01;
                    break;

                case "0":
                    $increaeVal2 = 0.01;
            }//end switch

            for ($i = $arrRange['nFrom']; $i <= $arrRange['nTo']; $i+=$increaeVal2) {
                $rangeArray[round($i, 2)] = round($i, 2);
            }//end for loop
            $rangeArray[$arrRange['nTo']] = $arrRange['nTo'];
        }//end while loop

        $rangeArray = array_unique($rangeArray);
        $checkArray = array_unique($checkArray);

        $identical = array_intersect_assoc($checkArray, $rangeArray);

        $result = array('identical' => $identical,
            'added' => array_diff($rangeArray, $checkArray),
            'discarded' => array_diff($checkArray, $identical)// discarded means present only into OLD 
        );

        switch (count($result['identical'])) {
            case "0":
                $exits = 0;
                break;

            default:
                $exits = 1;
                break;
        }//end switch
        return ($exits);
    }//end if
}*/


function checkNewRange($from, $to, $chkId) {
    global $conn;
    $listingFeeSql  = "SELECT * FROM " . TABLEPREFIX . "listingfee 
    WHERE vActive='1' 
    AND nLId!='" . $chkId . "' 
    AND ('".$from."' BETWEEN nFrom AND nTo) 
    AND ('".$to."' BETWEEN nFrom AND nTo) 
    AND (above >= $from OR above <= $to) 
    ORDER BY nLPosition DESC"; 
    $sqlRangeRes    = mysqli_query($conn, $listingFeeSql) or die(mysqli_error($conn));
    
    if(mysqli_num_rows($sqlRangeRes)>0){
        return true;
    }else{
        return false;
    }
}


//end function

function showSwpaWishDetails($type, $nSwapId) { 
    global $conn;
    $var_post_type = "";
    $var_mesg = "";
    $ref_file = "";
    $ref_file2 = "";
    $sql = "";
    $var_url = "";
    $var_url_small = "";
    $var_swapid = "";
    $var_source = "";
    $var_category_desc = "";
    $var_category_id = "";
    $var_post_date = "";
    $var_title = "";
    $var_brand = "";
    $var_type = "";
    $var_condition = "";
    $var_year = "";
    $var_value = "";
    $var_point = "";
    $var_shipping = "";
    $var_description = "";
    $var_quantity = 0;
    $var_command = "";
    $var_user_name = "";
    $var_user_id = "";
    $var_title_url = "";
    $var_img_desc = "";
    $checkSource = ($type != '') ? $type : $_POST["source"];

    if ($type == "s") {
        //fetch seller id
        $sellerId = fetchSingleValue(select_rows(TABLEPREFIX . 'swap', 'nUserId', "where nSwapId='" . $nSwapId . "'"), 'nUserId');

        //seller produt id array
        $sellerProductArray = array();

        //create seller product array
        $selleProd = mysqli_query($conn, "SELECT nSwapId FROM " . TABLEPREFIX . "swap WHERE nUserId='" . $sellerId . "'") or die(mysqli_error($conn));
        if (mysqli_num_rows($selleProd) > 0) {
            while ($arrP = mysqli_fetch_array($selleProd)) {
                $sellerProductArray[] = $arrP['nSwapId'];
            }//end while loop
        }//end if
        $sellerProductArray = join($sellerProductArray, ",");

        $userAllowPostFeedback = '';
        if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
            //checking atleast one post against this
            $cndPost = "where nUserId='" . $_SESSION["guserid"] . "' AND nSwapId IN (" . $sellerProductArray . ") LIMIT 0,1";
            $userAllowPostFeedback = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'nUserId', $cndPost), 'nUserId');
        }//end if
        //set the target file links to userwish
        $ref_file = "swaplistdetailed.php";

        //set the sql to retreive data from swap table where vPostType=wish
        $sql = "select S.nSwapId,S.nCategoryId,
        L.vCategoryDesc,S.nUserId,S.vPostType,
        vLoginName as UserName,
        S.vTitle,S.vBrand,S.vType,S.vCondition,
        S.vYear,S.nValue,S.nShipping,S.vUrl,S.vDescription,date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
        S.vImgDes,S.vSmlImg,S.nPoint from
        " . TABLEPREFIX . "swap S
        left join ".TABLEPREFIX."users U on S.nUserId = U.nUserId
        left join ".TABLEPREFIX."category C on S.nCategoryId = C.nCategoryId
        LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
        where 
        nSwapId = '" . addslashes($nSwapId) . "'"
        . " AND S.vDelStatus = '0' ";


        $gTableId = 'nSwapId';
        $gCheckCond = " and nSaleId='0'";
    }//end if
    else if ($type == "w") {
        //fetch seller id
        $sellerId = fetchSingleValue(select_rows(TABLEPREFIX . 'swap', 'nUserId', "where nSwapId='" . $nSwapId . "'"), 'nUserId');

        //seller produt id array
        $sellerProductArray = array();

        //create seller product array
        $selleProd = mysqli_query($conn, "SELECT nSwapId FROM " . TABLEPREFIX . "swap WHERE nUserId='" . $sellerId . "'") or die(mysqli_error($conn));
        if (mysqli_num_rows($selleProd) > 0) {
            while ($arrP = mysqli_fetch_array($selleProd)) {
                $sellerProductArray[] = $arrP['nSwapId'];
            }//end while loop
        }//end if
        $sellerProductArray = join($sellerProductArray, ",");

        $userAllowPostFeedback = '';
        if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
            //checking atleast one post against this
            $cndPost = "where nUserId='" . $_SESSION["guserid"] . "' AND nSwapId IN (" . $sellerProductArray . ") LIMIT 0,1";
            $userAllowPostFeedback = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'nUserId', $cndPost), 'nUserId');
        }//end if
        //set the target file links to userwish
        $ref_file = "wishlistdetailed.php";

        //set the sql to retreive data from swap table where vPostType=wish
        $sql = "select nSwapId,S.nCategoryId,
        L.vCategoryDesc,S.nUserId,S.vPostType,'0' as 'nQuantity',
        vLoginName as UserName,
        S.vTitle,S.vBrand,S.vType,S.vCondition,
        S.vYear,S.nValue,S.nShipping,S.vUrl,S.vDescription,date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
        S.vImgDes,S.vSmlImg,S.nPoint from
        " . TABLEPREFIX . "swap S
        left join ".TABLEPREFIX."users U on S.nUserId = U.nUserId
        left join ".TABLEPREFIX."category C on S.nCategoryId = C.nCategoryId
        LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
        where  
        nSwapId = '" . addslashes($nSwapId) . "'"
        . " AND S.vDelStatus = '0' ";

        $gTableId = 'nSwapId';
        $gCheckCond = " and nSaleId='0'";
    }//end else if

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) != 0) {
        if ($row = mysqli_fetch_array($result)) {
            if ($row["vPostType"] == "swap") {
                $var_post_type = "Swap Item";
                $var_title_url = HEADING_ITEM_DETAILS;
            }//end if
            else if ($row["vPostType"] == "wish") {
                $var_post_type = "Wish Item";
                $var_title_url = HEADING_ITEM_WISHED;
            }//end else if

            $var_post_date = $row["dPostDate"];
            $var_swapid = $row["nSwapId"];
            $var_source = $type;
            $var_url = $row["vUrl"];
            $var_url_small = $row["vSmlImg"];
            $var_category_desc = $row["vCategoryDesc"];
            $var_category_id = $row["nCategoryId"];
            $var_title = $row["vTitle"];
            $var_brand = $row["vBrand"];
            $var_type = $row["vType"];
            $var_condition = $row["vCondition"];
            $var_year = $row["vYear"];
            $var_value = $row["nValue"];
            $var_point = $row["nPoint"];
            $var_shipping = $row["nShipping"];
            $var_quantity = $row["nQuantity"];
            $var_description = $row["vDescription"];
            $var_command = "";
            $var_user_id = $row["nUserId"];
            $var_user_name = $row["UserName"];
            $ref_file2 = $ref_file . "?rf=sid&no=" . $var_user_id;
            $var_img_desc = $row["vImgDes"];

            //checking user status
            $condition = "where nUserId='" . $row['nUserId'] . "'";
            $userStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'online', 'vVisible', $condition), 'vVisible');

            //if posted user not logged user
            if ($row['nUserId'] != $_SESSION["guserid"]) {
                $chatWindow = '&nbsp;&nbsp;<b>[<a href="javascript:WindowPop(\'' . ucfirst($row['nUserId']) . '\');">'.LINK_CHAT_WITH.' ' . ucfirst($row['UserName']) . ']</b></a>';
            }//end if
            else {
                $chatWindow = '';
            }//end else

            switch ($userStatus) {
                case "Y":
                $userStatus = '<font color="green"><b>('.TEXT_ONLINE.')</b></font>' . $chatWindow;
                break;

                case "N":
                $userStatus = '<font color="red"><b>('.TEXT_OFFLINE.')</b></font>';
                break;

                case "":
                $userStatus = '<font color="red"><b>('.TEXT_OFFLINE.')</b></font>';
                break;
            }//end switch
        }//end if
    }//end if

    if ($_GET["prod"] == "p") {
        $ref_file = "catwiseproducts.php?catid=" . $_GET["catid"]
        . "&cmbSearchType=" . $_GET["cmbSearchType"] . "&txtSearch="
        . urlencode($_GET["txtSearch"]) . "&cmbItemType="
        . $_GET["cmbItemType"];
    }//end if
    $ref_file2 = "home.php?uid=" . $var_user_id . "&uname=" . urlencode($var_user_name);

    if (trim($_GET['catid']) != '') {
        $showCatId = $_GET['catid'];
    }//end if
    else {
        $showCatId = $var_category_id;
    }//end else
    ?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="maintext2">
        <tr>
            <td width="44%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <td>
                        <?php
                        if ($type != 'w') {
                            ?>
                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <?php
                                        if (trim($var_url) == "" || !file_exists(trim($var_url))) {
                                            $var_url = "pics/nophoto.gif";
                                        }
                                        if (trim($var_url_small) == "" || !file_exists(trim($var_url_small))) {
                                            $var_url_small = "pics/nophoto.gif";
                                        }

                                        if (trim($var_img_desc) != '') {
                                            $var_img_desc = $var_img_desc;
                                        }
                                        if ($var_url_small != "pics/nophoto.gif") {
                                            ?>
                                            <a href="<?php echo $var_url; ?>" rel="lightbox" id="itemImagea" title="<?php echo $var_img_desc; ?>" target="_blank"><img src="show_merge_image.php?vImg=<?php echo  $var_url_small ?>" width="393" height="269" border="0" id="itemImage"></a>
                                        </td>
                                        <?php 
                                    } ?>
                                </tr>
                            </table>
                            <?php }?>
                            <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                <?php
                                if ($type != 'w') {
                                    if ($var_url != "pics/nophoto.gif") {
                                        ?>
                                        <tr>
                                            <td colspan="2" align="left"> <a href="<?php echo $var_url; ?>" rel="lightbox" id="itemImagev" title="<?php echo $var_img_desc; ?>" target="_blank"><?php echo LINK_VIEW_LARGE_IMAGE; ?></a></td>
                                        </tr>
                                        <?php
                                    }
                                }//end if
                                $chkId = ($nSwapId != '') ? $nSwapId : $_GET["saleid"];
                                $arrImages = mysqli_query($conn, "select * from " . TABLEPREFIX . "gallery where $gTableId='" . addslashes($chkId) . "' and vDelStatus='0' " . $gCheckCond) or die(mysqli_error($conn));
                                if (mysqli_num_rows($arrImages) > 0) {
                                    ?>
                                    <tr>
                                        <td width="23%" align="left"><?php echo LINK_MORE_IMAGES; ?> &nbsp;&raquo;</td>
                                        <td width="77%" align="left"> <?php
                                            $k = 1;
                                            while ($arr = mysqli_fetch_array($arrImages)) {
                                                ?>
                                                <a onClick="movepic('<?php echo $arr['vSmlImg']; ?>', '<?php echo $arr['vImg']; ?>', '<?php echo $arr['vDes']; ?>')" style="cursor: pointer;"><?php echo $k; ?></a>&nbsp;
                                                <?php
                                                $k++;
                                        }//end while
                                        ?>
                                    </td>
                                </tr>
                                <?php }//end if?>
                                <tr>
                                    <td colspan="2" align="left"><a href="<?php echo  $ref_file ?>" target="_blank"><?php echo  $var_title_url ?></a></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="left"><a href="<?php echo  $ref_file2 ?>" target="_blank"><?php echo  HEADING_POSTINGS_OF.' '.htmlentities($var_user_name) ?></a> <?php echo $userStatus; ?></td>
                                </tr>
                                <?php
                        //checking at least one post agains this user
                                if (trim($userAllowPostFeedback) != '') {
                                    $passId = $nSwapId;
                                    ?>
                                    <tr>
                                        <td colspan="2" align="left"><a href="userfeedback.php?uid=<?php echo $var_user_id . '&uname=' . urlencode($var_user_name) . '&nId=' . $passId . '&source=' . $type; ?>" target="_blank"><b><?php echo HEADING_POST_FEEDBACK; ?></b></a></td>
                                    </tr>
                                    <?php
                        }//end if
                        ?>
                        <tr>
                            <td colspan="2" align="left"><a href="user_profile.php?uid=<?php echo $var_user_id . "&uname=" . urlencode($var_user_name); ?>" target="_blank"><?php echo LINK_VIEW_PROFILE; ?></a></td>
                        </tr>
                    </table></td>
                </tr>
            </table>
        </td>
        <td width="56%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="5">
            <tr>
                <td><table width="100%"  border="0" cellspacing="0" cellpadding="1">
                    <tr>
                        <td align="left" class="welcome"><?php
                                    //checking point enable in website
                            if (ENABLE_POINT != '0') {
                                echo POINT_NAME . '&nbsp;&raquo;&nbsp;' . $var_point;
                                    }//end if
                                    if (ENABLE_POINT != '1') {
                                        echo TEXT_PRICE . '&nbsp;&raquo;&nbsp;' . CURRENCY_CODE . $var_value;
                                    }//end else
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="1">
                            <tr>
                                <td align="left" valign="top" class="smalltext"><?php echo TEXT_DESCRIPTION; ?> &raquo; <?php echo nl2br(htmlentities($var_description)); ?></td>
                            </tr>
                        </table>
                        <table width="100%" height="27"  border="0" cellpadding="1" cellspacing="0">
                            <tr>
                                <td align="left" class="link3"><?php echo TEXT_TITLE; ?> &raquo; <?php echo htmlentities($var_title); ?></td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                            <tr>
                                <td align="left"><?php echo TEXT_CATEGORY; ?> &raquo; <?php echo htmlentities($var_category_desc); ?></td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                            <tr>
                                <td align="left"><?php echo TEXT_POSTED_ON; ?> &raquo; <?php echo date('m/d/Y', strtotime($var_post_date)); ?></td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                            <tr>
                                <td align="left"><?php echo TEXT_POSTED_BY; ?> &raquo; <?php echo htmlentities($var_user_name); ?></td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                            <tr>
                                <td align="left"><?php echo TEXT_BRAND; ?> &raquo; <?php echo htmlentities($var_brand); ?></td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                            <tr>
                                <td align="left"><?php echo TEXT_TYPE; ?> &raquo; <?php echo htmlentities($var_type); ?></td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                            <tr>
                                <td align="left"><?php echo TEXT_CONDITION; ?> &raquo; <?php echo htmlentities($var_condition); ?></td>
                            </tr>
                        </table>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                            <tr>
                                <td align="left"><?php echo TEXT_YEAR; ?> &raquo; <?php echo htmlentities($var_year); ?></td>
                            </tr>
                        </table>
                        <?php if ($_GET["source"] == "sa" || $_POST["source"] == "sa") { ?>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                            <tr>
                                <td align="left"><?php echo TEXT_QUANTITY; ?> &raquo; <?php echo $var_quantity; ?></td>
                            </tr>
                        </table>
                        <?php 
                    }//end if ?>
                    <?php
                    if ($checkSource != "w") {
                                    //checking point enable in website
                        if (ENABLE_POINT != '1') {
                            ?>
                            <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                <tr>
                                    <td align="left"><?php echo TEXT_SHIPPING; ?> &raquo; <?php echo CURRENCY_CODE; ?><?php echo $var_shipping; ?></td>
                                </tr>
                            </table>
                            <?php
                        }
                        ?>
                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                            <tr>
                                <td align="left"><?php echo $showShippping; ?></td>
                            </tr>
                        </table>
                        <?php 
                    }//end if ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>

<?php
}

//end function


//fuction for get total count
function toGetTotal($nCatId, $chkTopLinks) {     
    global $conn;                          
    $sqlCnt = mysqli_query($conn, "SELECT vRoute FROM " . TABLEPREFIX . "category WHERE FIND_IN_SET( '".$nCatId."', vRoute ) order by CHAR_LENGTH(vRoute) DESC limit 0,1") or die(mysqli_error($conn));
    if (mysqli_num_rows($sqlCnt) > 0) {
        $catId = mysqli_result($sqlCnt, 0, 'vRoute');
        if (trim(ParentId($nCatId)) != '') {
            $catId.="," . ParentId($nCatId);
        }

        $sqlCount = mysqli_query($conn, "SELECT count(s.nCategoryId) as cnt FROM " . TABLEPREFIX . "swap s 
            LEFT JOIN " . TABLEPREFIX . "users u ON s.nUserId=u.nUserId
            WHERE s.nCategoryId IN (" . $catId . ") AND u.vStatus='0' AND s.vDelStatus = '0' AND s.vSwapStatus = '0' AND u.vDelStatus = '0' and u.nUserId <> '".$_SESSION['guserid']."'") or die(mysqli_error($conn));
        if (mysqli_num_rows($sqlCount) > 0) {
            $cntReturn = mysqli_result($sqlCount, 0, 'cnt');
        }
        else {
            $cntReturn = 0;
        }

        if (in_array('Sell', ModuleAcess($chkTopLinks))) {
            //taking from sale table too

            $sqlCount2 = mysqli_query($conn, "SELECT count(s.nCategoryId) as cnt FROM " . TABLEPREFIX . "sale s
              LEFT JOIN " . TABLEPREFIX . "users u ON s.nUserId=u.nUserId
              WHERE s.nCategoryId IN (" . $catId . ") AND u.vStatus='0' AND s.vDelStatus = '0' AND u.vDelStatus = '0' AND s.nQuantity > '0' and u.nUserId <> '".$_SESSION['guserid']."'") or die(mysqli_error($conn));
            if (mysqli_num_rows($sqlCount2) > 0) {
                $cntReturn2 = mysqli_result($sqlCount2, 0, 'cnt');
            }
            else {
                $cntReturn2 = 0;
            }
        }
    }
    return ($cntReturn + $cntReturn2);
}

//end function


//function to get all parentid
function ParentIdOld($nCatId) {
    global $conn;
    $parentArray = array();
    $sqlParent = mysqli_query($conn, "SELECT nCategoryId FROM " . TABLEPREFIX . "category WHERE nParentId='" . $nCatId . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($sqlParent) > 0) {
        while ($arrParent = mysqli_fetch_array($sqlParent)) {
            $parentArray[] = $arrParent['nCategoryId'];
        }
    }
    $parentArray = join(",", $parentArray);
    return ($parentArray);
}


function ParentId($nCatId) {
    global $conn;
    $parentArray = array();
    $parentUArray = array();
    $sqlParent = mysqli_query($conn, "SELECT nCategoryId FROM " . TABLEPREFIX . "category WHERE nParentId='" . $nCatId . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($sqlParent) > 0) {
        while ($arrParent = mysqli_fetch_array($sqlParent)) {
            $parentArray[] = $arrParent['nCategoryId'];
            $checkarray = array();
            $checkarray = ParentId($arrParent['nCategoryId']);
            if(!empty($checkarray)){
                $parentArray[] = $checkarray;
            }
        }
    }
    $parentUArray = array_unique($parentArray);
    if(!empty ($parentUArray)){
        $parentArray = join(",", $parentUArray);
    } else {
        $parentArray = NULL;
    }
    return ($parentArray);
}

//end function


//function for checking active modules
function ModuleAcess($checkToplinks) {
    global $conn;
    for ($i = 0; $i < mysqli_num_rows($checkToplinks); $i++) {
        $MenuEnableCheckArray[] = mysqli_result($checkToplinks, $i, 'vCategoryTitle');
    }
    return ($MenuEnableCheckArray);
}
//end function

function reduceQuantity($nSaleId,$reqQty){
    global $conn;
    $sql = "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity - ".$reqQty." where nSaleId ='" . addslashes($nSaleId) . "'";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
}

// check transaction(swap/sale) of a user with logged in user

function getTransactionStatus($userId){
    global $conn;
    $swapTransActionSql = "SELECT nSTId FROM " . TABLEPREFIX . "swaptxn WHERE nUserId IN ('".$userId."','".$_SESSION['guserid']."')";
    $swapTransActionRes = mysqli_query($conn, $swapTransActionSql) or die(mysqli_error($conn));
    if(mysqli_num_rows($swapTransActionRes)>0){
        return true;
    }else{
        $saleTransactionsSql    = "SELECT nSaleId 
        FROM " .TABLEPREFIX. "sale
        WHERE nUserId = '$userId' AND nSaleId IN (
        SELECT nSaleId FROM " .TABLEPREFIX. "saledetails WHERE nUserId = '".$_SESSION["guserid"]."')";
        $saleTransactionsRes    = mysqli_query($conn, $saleTransactionsSql);

        if(mysqli_num_rows($saleTransactionsRes)>0){
            return true;
        }else{
            return false;
        }

    }
}

function getGalleryImages($parentID, $type = 'nSaleId'){ // $type could be nSwapId / nSaleId
    global $conn;
    $dataArr = array();
    $sql    = "SELECT nUserId, nSwapId, nSaleId, vImg, vDes
    FROM " .TABLEPREFIX. "gallery
    WHERE ".$type." = '$parentID'";
    $res    = mysqli_query($conn, $sql);

    if(mysqli_num_rows($res) > 0) {
        while($row = mysqli_fetch_assoc($res)){
            // ...
            $dataArr[] = $row;
        } // End while
    } // End
    return $dataArr;
} // End Function

//sortable
function sortableDb($table,$ID,$PositionfieldName, $IdfieldName, $direction, $sqlN=NULL) {
    global $conn;
    $dataArr = array();
    $sortedArr = array();
    $sql = "SELECT ".$IdfieldName." FROM ".TABLEPREFIX.$table." ORDER BY ".$PositionfieldName." ASC";
    if(!empty($sqlN)){
        $sql = $sqlN;
    }
    $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if(mysqli_num_rows($res) > 0){
        while($rows = mysqli_fetch_assoc($res)){
            $dataArr[]= $rows[$IdfieldName];
        } // End While
        
        $sortedArr = sortableArr($dataArr,$ID,$direction);
        
        if(!empty($sortedArr)){
            foreach($sortedArr as $newPosition => $Id){
                mysqli_query($conn, "UPDATE ".TABLEPREFIX.$table." SET $PositionfieldName='" . $newPosition . "' WHERE $IdfieldName='" . $Id . "'") or die(mysqli_error($conn));
            }
        }
    } // end if
} //End Function

function sortableArr($arr,$item,$sort='down'){
  $dataArr = array();
  if(!empty($arr)){
      $keys = array_keys($arr);
      $vals = array_values($arr);

      $keyMax = max($keys);
      $keyMin = min($keys);

      $key1 = array_search($item, $arr);
      $key2 = ($sort=='down') ? $key1+1 : $key1-1;
      if($sort=='down'){
        $key2 = ($key1==$keyMax) ? 0 : $key2;

    } else {
        $key2 = ($key1==0) ? $keyMax : $key2;
    }

    $dataArr = $arr;

    $tmp = $dataArr[$key1];
    $dataArr[$key1] = $dataArr[$key2];
    $dataArr[$key2] = $tmp;

}

return $dataArr;

}


//function to get the current page with Querystring
function getCurrentFile(){

    if($_SERVER["QUERY_STRING"]!='')
    {

        $paginationUrl = basename($_SERVER['PHP_SELF'])."?".$_SERVER['QUERY_STRING']."&p=[p]";
    }
    else{
        $paginationUrl = basename($_SERVER['PHP_SELF'])."?"."p=[p]";
    }

    return $paginationUrl;
    
}

function getCurrentPageNum()
{
    $pagenumber  =1;
    if(isset($_REQUEST['p']) && $_REQUEST['p']!='')
    {
        $pagenumber = $_REQUEST['p'];
    }
    return $pagenumber;
}

//Pagination Class

function dopaging($sql,$numrecords=0,$limit=15) {
    global $conn;

    global $clspg;
    global $pages;
    global $num;
    global $start;
    $clspg = new Pager;
    $temp = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $numrows = mysqli_num_rows($temp);

    $start = $clspg->findStart($limit,$numrows);
    if ($numrecords == 0)
        $pages = $clspg->findPages($numrows, $limit);
    else {
        if ($numrows > $numrecords)
            $pages = $clspg->findPages($numrecords, $limit);
        else
            $pages = $clspg->findPages($numrows, $limit);
    }
    if ($numrecords == 0)
        $sql1 = $sql . " LIMIT " . $start. ", ". $limit ;
    else {
        if (($start + $limit) > $numrecords)
            $sql1 = $sql . " LIMIT " . $start. ", ". ($numrecords - $start);
        else
            $sql1 = $sql . " LIMIT " . $start. ", ". $limit ;
    }

   // echo $sql1;

    return $sql1;


}

function getnavigation($totalRows,$limit=0)
{
    global $start;
    
    $startString  = $start;
    
    if($startString==0)
    {
        $startString = 1;
    }
    
    if($limit==0)
    {
        $limit = PAGINATION_LIMIT;
    }
    
    $endRow       =     $start+$limit;
    if($totalRows<$endRow)
    {
        $endRow = $totalRows;
    }
    $returnString    = $startString ." - ". $endRow;

    return $returnString;
}


function getFeedBackCount($saleId,$userId)
{
    global $conn;
    $sql    = "select * from " . TABLEPREFIX . "userfeedback f  where f.nUserFBId='" . mysqli_real_escape_string($conn, $userId). "' 
    and f.nSaleId='" . mysqli_real_escape_string($conn, $saleId). "'  ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $numRecords =   mysqli_num_rows($result);
    if($numRecords==0)
    {
        return false;
    }else{
        return true;
    }


}


//New Function for Image Handling  

function resizeImage($sourcePath,$imageName,$imageType,$oldWidth,$oldHeight,$newImageWidth,$newImageHeight,$type){

    $originalsrc = $sourcePath.$imageName;
    $src         = $sourcePath.$type.$imageName;
    copy($originalsrc,$src);

    $jpeg_quality = 100;
    
    switch ($imageType) {
        case jpeg:
        $image = imagecreatefromjpeg($src);
        break;
        case jpg:
        $image = imagecreatefromjpeg($src);

        case png:
        $image = imagecreatefrompng($src);
        break;
        case gif:
        $image = imagecreatefromgif($src);
        break;
    }

    $aspect = $oldWidth / $oldHeight; 
    if($oldWidth>=$oldHeight) {
        $newWidth = $newImageWidth;
        $newHeight = $newWidth / $aspect;
        if($newHeight>$newImageHeight) {
            $newWidth = ($newImageHeight*$newImageWidth)/$newHeight;
            $newHeight = $newImageHeight;
        }
    }
    else {
        $newHeight = $newImageHeight;
        $newWidth  = $newHeight *  $aspect;
        if($newWidth>$newImageWidth) {
            $newHeight = ($newImageHeight*$newImageWidth)/$newWidth;
            $newWidth = $newImageWidth;
        }
    }
    
    $dst_r = ImageCreateTrueColor($newWidth, $newHeight ); 
    imagealphablending( $dst_r, false );
    imagesavealpha( $dst_r, true );
    imagecopyresampled($dst_r, $image, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);

    $destImage = $src;

    switch ($imageType) {
        case jpeg: {
            imagejpeg($dst_r,$destImage,$jpeg_quality);
            break;
        }
        case jpg: {
            imagejpeg($dst_r,$destImage,$jpeg_quality);
            break;
        }
        case png: {
            imagepng($dst_r,$destImage,9);
            break;
        }
        case gif: {
            imagegif($dst_r,$destImage);
            break;
        }
    }
}


function boxResize($path, $sourceFileName, $destFileName,$destination_path, $img_width, $img_height,$method,$bgcolor='FFFFFF') {

    include_once ('imagehandle.php');
    $ObjImagehandle                         = new ImagehandleComponent();
    $ObjImagehandle->source_path            = $path.$sourceFileName;
    $ObjImagehandle->preserve_aspect_ratio  = true;
    $ObjImagehandle->enlarge_smaller_images = true;
    $ObjImagehandle->preserve_time          = true;
    $ObjImagehandle->target_path = $destination_path.$destFileName;
    $ObjImagehandle = $ObjImagehandle->resize($img_width, $img_height,$method,$bgcolor);
}


function getAllPaymentMethods()
{
    $paymentMethod = array("pp"=>TEXT_PAYPAL,
        "wp"=>TEXT_WORLDPAY,
        "bp"=>TEXT_BLUEPAY,
        "cc"=>TEXT_CREDIT_CARD,
        "bu"=>TEXT_BUSINESS_CHECK,
        "ca"=>TEXT_CASIER_CHECK,
        "mo"=>TEXT_MONEY_ORDER,
        'wt'=>TEXT_WIRE_TRANSFER,
        "pc"=>TEXT_PERSONAL_CHECK,
        "yp"=>TEXT_YOUR_PAY,
        "gc"=>TEXT_GOOGLE_CHECKOUT,
        "rp"=>POINT_NAME);

    return $paymentMethod;

}
function getUserEmail($user_id){
    global $conn;
    $sql = "SELECT vEmail FROM " . TABLEPREFIX . "users where nUserId = '" . $user_id . "'";
    $result =mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    return $row['vEmail'];

    
}

// function to cut short a string if it is long 
// by linu
function restrict_string_size($string, $size)
{
	$value = htmlentities($string);
	if(strlen($string)>$size)
	{
		$value = substr(htmlentities($string),0,$size)."...";
	}

	return $value;
}

function getDisplayImage($chkId,$type,$imgUrlT)
{    
    global $conn;
    if($imgUrlT && file_exists($imgUrlT)) return $imgUrlT;  
    $var_burl = '';
    if($type=="s")
    {
        $gTableId = 'nSwapId';
        $gCheckCond = " and nSaleId='0'";              
    }  
    else if($type=="sa")
    {
        $gTableId = 'nSaleId';
        $gCheckCond = " and nSwapId='0'";              
    }  
    else if($type=="w")
    {
        $gTableId = 'nSwapId';
        $gCheckCond = " and nSaleId='0'";              
    }  
    $imagesArray = mysqli_query($conn, "select * from " . TABLEPREFIX . "gallery where $gTableId='" . addslashes($chkId) . "' and vImg!='' and vDelStatus='0' " . $gCheckCond) or die(mysqli_error($conn));

    if($imagesArray){                    
        $noImages  = mysqli_num_rows($imagesArray);        
        if($noImages>0)
        {
            while ($arrnew = mysqli_fetch_array($imagesArray)) {

                if (@file_exists($arrnew["vMedImg"])) {
                    $var_burl = $arrnew["vMedImg"];
                    break;
                } 
            }
        }
    }
    if(!$var_burl)$var_burl = "images/nophoto.gif"; 
    return $var_burl;

}

function getEscrowSettleAmount($amount) {
    global $conn;
    if(DisplayLookUp("Enable Escrow")=="Yes") {
        $escrowType = DisplayLookUp("EscrowCommissionType");
        if($escrowType=="range") {
            if($amount<=0) return 0;
            $var_calc_amnt=$amount;
            $es_sql_1 = "SELECT * FROM ".TABLEPREFIX."escrowrangefee WHERE vActive = '1' AND above < '".$var_calc_amnt."' AND above != 0";
            $es_rs_1  = mysqli_query($conn, $es_sql_1);
            if(mysqli_num_rows($es_rs_1)){
                $es_rw = mysqli_fetch_array($es_rs_1);
                $fee_percent = $es_rw["nPrice"];
                $var_escrow=$var_calc_amnt * $fee_percent / 100;
            }else{


                $es_sql = "SELECT * FROM ".TABLEPREFIX."escrowrangefee WHERE vActive = '1' AND (ROUND(nFrom) <= '".$var_calc_amnt."' AND ROUND(nTo) >= '".$var_calc_amnt."')";
                $es_rs  = mysqli_query($conn, $es_sql);
                if(mysqli_num_rows($es_rs)>0) {
                    $es_rw = mysqli_fetch_array($es_rs);
                    $fee_percent = $es_rw["nPrice"];
                    $var_escrow=$var_calc_amnt * $fee_percent / 100;
                }
            }
        }else {
            if($escrowType=="percentage"){
                $fee_percent = DisplayLookUp('14');
                if($amount<=0) return 0;
                $var_calc_amnt=$amount;
                $var_escrow=$var_calc_amnt * $fee_percent / 100;
            }else{
                $fee_percent = DisplayLookUp('14');
                $var_escrow = $fee_percent;
            }
        }
    }
    return  round($var_escrow,2);       
}

function getEscrowPercentage($amount) {
    global $conn;
    $var_escrow_value = $var_escrow_percent_txt = '0';
    if(DisplayLookUp("Enable Escrow")=="Yes") {        
        $escrowType = DisplayLookUp("EscrowCommissionType");
        
        if($escrowType=="fixed" || $escrowType=="percentage") {
            if (DisplayLookUp('14') != '') {
                $var_escrow_value = DisplayLookUp('14');
                settype($var_escrow_value, "double");
            }
            if($escrowType=="fixed")
                return CURRENCY_CODE.$var_escrow_value;
            else if($escrowType=="percentage")
                return $var_escrow_value.'%';
            
        }else {
            if($escrowType=="range") {
                $es_sql_1 = "SELECT * FROM ".TABLEPREFIX."escrowrangefee WHERE vActive = '1' AND above < '".$amount."' AND above != 0";
                $es_rs_1  = mysqli_query($conn, $es_sql_1);
                if(mysqli_num_rows($es_rs_1)){
                    $es_rw = mysqli_fetch_array($es_rs_1);
                    $fee_percent = $es_rw["nPrice"];                    
                }else{
                    $es_sql = "SELECT * FROM ".TABLEPREFIX."escrowrangefee WHERE vActive = '1' AND (ROUND(nFrom) <= '".$amount."' AND ROUND(nTo) >= '".$amount."')";
                    $es_rs  = mysqli_query($conn, $es_sql);
                    if(mysqli_num_rows($es_rs)>0) {
                        $es_rw = mysqli_fetch_array($es_rs);
                        $fee_percent = $es_rw["nPrice"];                        
                    }
                }
                
                return $fee_percent.'%';   
            }
            
        }
        return '';
    }
    
}

function getGeneralPercentageText() {    
    if(DisplayLookUp("Enable Escrow")=="Yes") {        
        $escrowType = DisplayLookUp("EscrowCommissionType");        
        if($escrowType=="fixed") 
            return '$';       
        else
            return '%'; 
    }
    
}

function getTotalPendingSettleAmount($userId) {    
    global $conn;
    if(DisplayLookUp("Enable Escrow")=="Yes") { 
        $amt_to_be_settled = 0;
        $sql = "SELECT s.nSwapId,s.nUserId,s.vTitle,s.nSwapMember,s.nSwapAmount,u.vLoginName,s.vOwnerDelivery,s.vPartnerDelivery,s.vPostType FROM " . TABLEPREFIX . "swap s inner join " .
        TABLEPREFIX . "users u on s.nSwapMember = u.nUserId where s.vSwapStatus= '2' AND s.nSwapAmount > 0 AND s.nSwapMember='". addslashes($userId)."'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $var_escrow = getEscrowSettleAmount($row["nSwapAmount"]);
                if($row["nSwapAmount"]>0)
                    $amt_to_be_settled += ($row["nSwapAmount"]-$var_escrow);
            }            
        }
        $sql = "SELECT s.nSaleId,st.nQuantity,st.dDate,st.nAmount,st.vMethod,st.vDelivered,s.vTitle,st.nUserId from " . TABLEPREFIX . "sale s inner join  " . TABLEPREFIX . "saledetails st
        on s.nSaleId = st.nSaleId where st.vSaleStatus = '2' AND s.nUserId='". addslashes($userId) . "'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $var_escrow = getEscrowSettleAmount($row["nAmount"]);
                if($row["nAmount"]>0)
                    $amt_to_be_settled += ($row["nAmount"]-$var_escrow);
            }
        }
        return $amt_to_be_settled;
    }
    return 0;
    
}

function mysqli_result($res, $row, $field=0) { 
    $res->data_seek($row); 
    $datarow = $res->fetch_array(); 
    return $datarow[$field]; 
} 


function getMoreImages($chkId,$type){ 
    global $conn;
    $var_burl = array();
    
    if($type=="s")
    {
        $gTableId = 'nSwapId';
        $gCheckCond = " and nSaleId='0'";              
    }  
    else if($type=="sa")
    {
        $gTableId = 'nSaleId';
        $gCheckCond = " and nSwapId='0'";              
    }  

    
    $sql = mysqli_query($conn, "select * from " . TABLEPREFIX . "gallery where $gTableId='" . addslashes($chkId) . "'and vDelStatus='0' " . $gCheckCond) or die(mysqli_error($conn));

    if($sql) {

        $noImages  = mysqli_num_rows($sql);  
        $arr = array();
        if($noImages>0)
        {
            while ($arr = mysqli_fetch_array($sql)) {

                $image = str_replace('large_', '', $arr["vImg"]);
                if(@file_exists($image)) {
                    $var_burl[] = $image;
                } else {
                    if(@file_exists($arr["vImg"])){
                        $var_burl[] = $arr["vImg"];
                    }
                }
            }
        }
    } // End Function

    return $var_burl;
}


?>