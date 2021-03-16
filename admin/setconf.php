<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>                              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE = 'settings';

function isValidEmail($email) {
    $email = trim($email);
    if ($email == "") {
        return false;
    }//end if
  /*  if (!eregi("^" . "[a-z0-9]+([_\\.-][a-z0-9]+)*" . // user
    "@" . "([a-z0-9]+([\.-][a-z0-9]+)*)+" . // domain
    "\\.[a-z]{2,}" . // sld, tld
    "$", $email, $regs)) {*/
        if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',$email)) {
            return false;
    }//end if
    else {
        return true;
    }//end else
}

//end funciton

function isNotNull($value) {
    if (is_array($value)) {
        if (sizeof($value) > 0) {
            return true;
        }//end if
        else {
            return false;
        }//end else
    }//end if
    else {
        if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
            return true;
        }//end if
        else {
            return false;
        }//end else
    }//end else
}

//end function

$act = $_GET["act"];
$ddlFree = $_POST["ddlFree"];
$txtDate = $_POST["txtDate"];

$flag = false;
$var_param = "";
$var_value = "";
$message = '';
$encryptionMethod = DisplayLookUp('encryption');

if (function_exists('get_magic_quotes_gpc')) {
    $txtLicenseKey = stripslashes($_POST['txtLicenseKey']);
    $txtEmail = stripslashes($_POST['txtEmail']);
    $txtSMTPPassword = stripslashes($_POST['txtSMTPPassword']);
    $txtSMTPHost = stripslashes($_POST['txtSMTPHost']);
    $txtSMTPEmail = stripslashes($_POST['txtSMTPEmail']);
    $txtSMTPPort = stripslashes($_POST['txtSMTPPort']);
    $txtSMTPProtocol = stripslashes($_POST['txtSMTPProtocol']);
    $txtHash = stripslashes($_POST['txtHash']);
    $txtSMTPPassword = openssl_encrypt($txtSMTPPassword, $encryptionMethod, $txtHash);
    $txtFees = stripslashes($_POST['txtFees']);
    
    $txtTitle = stripslashes($_POST['txtTitle']);
    $txtName = stripslashes($_POST['txtName']);
    $ddlStyle = stripslashes($_POST['ddlStyle']);
    $radUType = stripslashes($_POST['radUType']);
    $txtRAmount = stripslashes($_POST["txtRAmount"]);
    $txtSurvey = stripslashes($_POST["txtSurvey"]);
    $txtRReg = stripslashes($_POST["txtRReg"]);
    $radGoogleAds = stripslashes($_POST["radGoogleAds"]);
    $txtGoogleValue = stripslashes($_POST["txtGoogleValue"]);
    $radBookmark = stripslashes($_POST["radBookmark"]);
    $txtBookmarkValue = stripslashes($_POST["txtBookmarkValue"]);
    $radFeedName = stripslashes($_POST["radFeedName"]);
    $txtSUrl = stripslashes($_POST["txtSUrl"]);
    $txtMetaK = stripslashes($_POST["txtMetaK"]);
    $txtMetaD = stripslashes($_POST["txtMetaD"]);
    $txtMaxImg = stripslashes($_POST["txtMaxImg"]);
    $radBanner = stripslashes($_POST["radBanner"]);
    $language_by_ip = stripslashes($_POST["language_by_ip"]);
    $plan_system = stripslashes($_POST["plan_system"]);
    $radThumb = stripslashes($_POST["radThumb"]);
    $txtMonthlyFee = stripslashes($_POST["txtMonthlyFee"]);
    $txtnotrans = stripslashes($_POST["txtnotrans"]);
    $txtFee = stripslashes($_POST["txtFee"]);


    //$txtSessionTime=stripslashes($_POST["txtSessionTime"]);
    $txtHeadCap = stripslashes($_POST["txtHeadCap"]);
    $chkEscrowType = stripslashes($_POST['chkEscrow']);
    if($chkEscrowType=="fixed"){
        $chkEscrow      = "Yes";
        $txtEscrow      = stripslashes($_POST['txtEscrowFixed']);
        if($txtEscrow=='' || !$txtEscrow){
            $message = "Escrow Fee cannot be null";
        }
    }else if($chkEscrowType=="percentage"){
        $chkEscrow      = "Yes";
        $txtEscrow      = stripslashes($_POST['txtEscrowPercentage']);
        if($txtEscrow=='' || !$txtEscrow){
            $message = "Escrow Fee Percentage cannot be null";
        }
    }else if($chkEscrowType=="range"){
        $chkEscrow = "Yes";
    }else{
        $chkEscrow = "No";
    }
    $ddlCurrencySymbol = CurrencyCodeCheck($_POST["ddlCurrency"]); 
    $ddlCurrency = stripslashes($_POST["ddlCurrency"]);
    $radType = stripslashes($_POST['radType']);
}//end if
else {
    $txtLicenseKey = $_POST['txtLicenseKey'];
    $txtEmail = $_POST['txtEmail'];
    $txtSMTPPassword = $_POST['txtSMTPPassword'];
    $txtSMTPHost = $_POST['txtSMTPHost'];
    $txtSMTPEmail = $_POST['txtSMTPEmail'];
    $txtSMTPPort = $_POST['txtSMTPPort'];
    $txtSMTPProtocol = $_POST['txtSMTPProtocol'];    
    $txtHash = $_POST['txtHash'];
    $txtSMTPPassword = openssl_encrypt($txtSMTPPassword, $encryptionMethod, $txtHash);
    $txtFees = $_POST['txtFees'];
    $txtTitle = $_POST['txtTitle'];
    $txtName = $_POST['txtName'];
    $ddlStyle = $_POST['ddlStyle'];
    $radUType = $_POST['radUType'];
    $txtRAmount = $_POST["txtRAmount"];
    $txtSurvey = $_POST["txtSurvey"];
    $txtRReg = $_POST["txtRReg"];
    $radGoogleAds = $_POST["radGoogleAds"];
    $txtGoogleValue = $_POST["txtGoogleValue"];
    $radBookmark = $_POST["radBookmark"];
    $txtBookmarkValue = $_POST["txtBookmarkValue"];
    $radFeedName = $_POST["radFeedName"];
    $txtSUrl = $_POST["txtSUrl"];
    $txtMetaK = $_POST["txtMetaK"];
    $txtMetaD = $_POST["txtMetaD"];
    $txtMaxImg = $_POST["txtMaxImg"];
    $radBanner = $_POST["radBanner"];
    $language_by_ip = $_POST["language_by_ip"];
    $plan_system = $_POST["plan_system"];
    $radThumb = $_POST["radThumb"];
    $txtMonthlyFee = $_POST["txtMonthlyFee"];
    $txtnotrans =   $_POST['txtnotrans'];
    $txtFee =   $_POST['txtFee'];
    //$txtSessionTime=$_POST["txtSessionTime"];
    $txtHeadCap = $_POST["txtHeadCap"];
    $chkEscrowType = $_POST['chkEscrow'];
    if($chkEscrowType=="fixed"){
        $chkEscrow      = "Yes";
        $txtEscrow      = $_POST['txtEscrowFixed'];
        if($txtEscrow=='' || !$txtEscrow){
            $message = "Escrow Fee cannot be null";
        }
    }else if($chkEscrowType=="percentage"){
        $chkEscrow      = "Yes";
        $txtEscrow      = $_POST['txtEscrowPercentage'];
        if($txtEscrow=='' || !$txtEscrow){
            $message = "Escrow Fee Percentage cannot be null";
        }
    }else if($chkEscrowType=="range"){
        $chkEscrow = "Yes";
    }else{
        $chkEscrow = "No";
    }
    $ddlCurrencySymbol = CurrencyCodeCheck($_POST["ddlCurrency"]);
    $ddlCurrency = $_POST["ddlCurrency"];
    $radType = $_POST['radType'];
}//end else

if (isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] == "Change Settings" && $message == '') {
    $txtLogo = $_FILES['txtLogo']['name'];
    $txtWelcomePic = $_FILES["txtWelcomePic"]["name"];
    //checking style
    if (isset($txtLogo) && $txtLogo != '') {
        $imagedir = "../images/";
        $logofiletype = $_FILES['txtLogo']['type'];
        $logofilename = ReplaceArray($_FILES['txtLogo']['name']);
        $logotempname = $_FILES['txtLogo']['tmp_name'];
        $logoimagedest = $imagedir . $logofilename;
        $error = false;
        if (!is_writable($imagedir) || !is_readable($imagedir) || !@file_exists("$imagedir" . ".")) {
            $error = true;
            $message .= " * Change the permission of 'images' folder in the root to 777 <br>";
        }//end if

        if ($logofiletype != "") {
            if (!isValidWebImageType2($logofiletype, $logofilename, $logotempname)) {
                $message .= " * Invalid Logo file ! Upload an image (jpg/gif/bmp/png)" . "<br>";
                $error = true;
            }//end if
            else {
                if (file_exists($logoimagedest)) {
                    //$message .= " * Logo file with the same name exists! Please rename the logo file and upload! " . "<br>";
                    //$error = true;
                    @rename($imagedir . $logofilename, $imagedir . 'old_' . $logofilename);
                }//end if
            }//end else
        }//end if
        
        
        @list($width_new, $height_new) = @getimagesize($logotempname);
        

        if($width_new < 184 || $height_new < 57 || $height_new > $width_new)
        {
            $message .= " * Image should be horizontal and should be of standard size (greater than or equal to 184x57 pixels)" . "<br>";
            $error = true;
            
        }
        

        if ($error == false) {
            if ($logofilename == "") {
                $message = "Please upload logo image";
            }//end if
            else {
                $imgResize = false;
                //checking size
                @list($width_new, $height_new) = @getimagesize($logotempname);
                
                
                
                if ($width_new > 184) { 
                    $width_new = '184';
                    $imgResize = true;
                }
                else {
                    $width_new = $width_new;
                }
                //checking height
                if ($height_new > 57) {
                    $height_new = '57';
                    $imgResize = true;
                }
                else {
                    $height_new = $height_new;
                }//end else

                if (move_uploaded_file($_FILES['txtLogo']['tmp_name'], $logoimagedest)) {
                    chmod($logoimagedest, 0777);
                    if ($imgResize == ture) {
                        //$txtSmallImage = resizeImg($logoimagedest, $width_new, $height_new, false, 100, 0, "_thumb");
                        
                        $txtSmallImage = resizeNew($logoimagedest,'../images/',$width_new, $height_new);
                        
                        $txtSmallImage = substr($txtSmallImage, 10, strlen($txtSmallImage));
                    }//end if
                    else {
                        $txtSmallImage = $logofilename;
                    }//end else
                    //updation for Site Logo
                    
                    //echo $txtSmallImage; exit;
                    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtSmallImage) . "'
                        where nLookUpCode='sitelogo'") or die(mysqli_error($conn));
                    $flag = true;
                    $message2 = "Settings updated";
                    $txtLogoDisp = DisplayLookUp('sitelogo');
                }//end if
            }//end else
        }//end if
    }//end if
    //Upload welcome Image

    if (isset($txtWelcomePic) && $txtWelcomePic != '') {
        $imagedir = "../images/";
        $logofiletype = $_FILES['txtWelcomePic']['type'];
        $logofilename = ReplaceArray($_FILES['txtWelcomePic']['name']);
        $logotempname = $_FILES['txtWelcomePic']['tmp_name'];
        $logoimagedest = $imagedir . $logofilename;
        $error = false;
        if (!is_writable($imagedir) || !is_readable($imagedir) || !@file_exists("$imagedir" . ".")) {
            $error = true;
            $message .= " * Change the permission of 'images' folder in the root to 777 <br>";
        }//end if

        if ($logofiletype != "") {
            if (!isValidWebImageType2($logofiletype, $logofilename, $logotempname)) {
                $message .= " * Invalid Image Type ! Upload an image (jpg/gif/bmp/png)" . "<br>";
                $error = true;
            }//end if
            @list($width_new, $height_new) = @getimagesize($logotempname);  
            if ($width_new < 196 || $height_new < 150) {
                $message .= "Welcome image size should be ".$width_new."x".$height_new." or greater ";
                $error = true;
            }else {
                if (file_exists($logoimagedest)) {
                    //$message .= " * Logo file with the same name exists! Please rename the logo file and upload! " . "<br>";
                    //$error = true;
                    @rename($imagedir . $logofilename, $imagedir . 'old_' . $logofilename);
                }//end if
            }//end else
        }//end if

        if ($error == false) { 
            if ($logofilename == "") {
                $message = "Please upload a welcome image";
                $error = true;
            }//end if
            else {
                $imgResize = false;

                if ($width_new > 1366) {
                    $width_new = '1366';
                    $imgResize = true;
                }//end if
                else {
                    //$width_new = $width_new;
                    $width_new = '1366';
                    $imgResize = true;
                }//end else
                //checking height
                if ($height_new > 421) {
                    $height_new = '421';
                    $imgResize = true;
                }//end if
                else {
                    //$height_new = $height_new;
                    $height_new = '421';
                    $imgResize = true;
                }//end else

                if (move_uploaded_file($_FILES['txtWelcomePic']['tmp_name'], $logoimagedest)) {
                    chmod($logoimagedest, 0777);
                    if ($imgResize == ture) {                        
                        $txtSmallImage = resizeImg($logoimagedest, $width_new, $height_new, false, 100, 0, "_thumb");
                        $txtSmallImage = substr($txtSmallImage, 10, strlen($txtSmallImage));
                    }//end if
                    else {
                        $txtSmallImage = $logofilename;
                    }//end else
                    //updation for Site Logo

                    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtSmallImage) . "'
                        where nLookUpCode='welcomeImage'") or die(mysqli_error($conn));
                    $flag = true;
                    $message2 = "Settings updated";
                    $txtwelcomePicDisp = DisplayLookUp('welcomeImage');
                }//end ifwelcomeImagewelcomeImage
           // }//end else
            } //end else
        }//end if
    }//end if
    if($error == false){
    //updation for user approval
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($radUType) . "' where nLookUpCode='userapproval'") or die(mysqli_error($conn));

    //updation for Site Style
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($ddlStyle) . "' where nLookUpCode='sitestyle'") or die(mysqli_error($conn));

    //updation for admin email
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtEmail) . "' where nLookUpCode='4'") or die(mysqli_error($conn));

    //updation for SMTP Host
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtSMTPHost) . "' where nLookUpCode='smtp_host'") or die(mysqli_error($conn));

    //updation for SMTP Email
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtSMTPEmail) . "' where nLookUpCode='smtp_email'") or die(mysqli_error($conn));

    // updation for SMTP password
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtSMTPPassword) . "' where nLookUpCode='smtp_password'") or die(mysqli_error($conn));

    // updation for SMTP Port
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtSMTPPort) . "' where nLookUpCode='smtp_port'") or die(mysqli_error($conn));

    // updation for SMTP Protocol 
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtSMTPProtocol) . "' where nLookUpCode='smtp_protocol'") or die(mysqli_error($conn));

    //updation for secret hash 
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtHash) . "' where nLookUpCode='secret_hash'") or die(mysqli_error($conn));

    //updation for Featured Fees
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtFees) . "' where nLookUpCode='5'") or die(mysqli_error($conn));

    //updation for Escrow Service Commission
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtEscrow) . "' where nLookUpCode='14'") or die(mysqli_error($conn));

    //updation for Site title
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtTitle) . "' where nLookUpCode='sitetitle'") or die(mysqli_error($conn));

    //updation for Site Name
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtName) . "' where nLookUpCode='sitename'") or die(mysqli_error($conn));

    //updation for securl url
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtSUrl) . "' where nLookUpCode='surl'") or die(mysqli_error($conn));

        if ($ddlCurrency != 'USD') {
        //disable if authorize.net is enabled
            if (DisplayLookUp('PaymentMethod') == 'CC') {
            //update payment method
                update_rows(TABLEPREFIX . 'lookup', "vLookUpDesc='NN'", "where nLookUpCode = 'PaymentMethod'");
        }//end if
    }//end if
    
    //updation for currency
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($ddlCurrencySymbol) . "' where nLookUpCode='currency'") or die(mysqli_error($conn));

    //updation for currencycode
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($ddlCurrency) . "' where nLookUpCode='currencycode'") or die(mysqli_error($conn));

    //updation for User Approval Type
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($radUType) . "' where nLookUpCode='userapproval'") or die(mysqli_error($conn));

    $sql = "select vLookUpDesc from " . TABLEPREFIX . "lookup where nLookupcode=15 and vLookUpDesc='0'";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
    //updation for Registration Amount
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtRAmount) . "' where nLookUpCode='3'") or die(mysqli_error($conn));

    //updation for Referral fee for Survey
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtSurvey) . "' where nLookUpCode='9'") or die(mysqli_error($conn));

    //updation for Referral Fee For Registration
        mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtRReg) . "' where nLookUpCode='10'") or die(mysqli_error($conn));

    }
    //updation forgoogleaddemo
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($radGoogleAds) . "' where nLookUpCode='googleaddemo'") or die(mysqli_error($conn));

    //updation for googleadvalue
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtGoogleValue) . "' where nLookUpCode='googleadvalue'") or die(mysqli_error($conn));

    //updation for bookmark_enable
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($radBookmark) . "' where nLookUpCode='bookmark_enable'") or die(mysqli_error($conn));

    //updation for bookmark_value
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtBookmarkValue) . "' where nLookUpCode='bookmark_value'") or die(mysqli_error($conn));

    //updation for bookmark_value
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($radFeedName) . "' where nLookUpCode='feedname'") or die(mysqli_error($conn));

    //updation for Meta Keywords
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtMetaK) . "' where nLookUpCode='Meta Keywords'") or die(mysqli_error($conn));

    //updation for Meta Description
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtMetaD) . "' where nLookUpCode='Meta Description'") or die(mysqli_error($conn));

    //updation for max number of images
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtMaxImg) . "' where nLookUpCode='MaxOfImages'") or die(mysqli_error($conn));

    //updation for banner display status
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($radBanner) . "' where nLookUpCode='BannerDisplay'") or die(mysqli_error($conn));

    //updation for plan system
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($plan_system) . "' where nLookUpCode='plan_system'") or die(mysqli_error($conn));

    //updation for ip based language selection
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($language_by_ip) . "' where nLookUpCode='language_by_ip'") or die(mysqli_error($conn));

    //updation for thumbnail toop tip
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($radThumb) . "' where nLookUpCode='ThumbToolTip'") or die(mysqli_error($conn));

    //updation for SessionTimeout
    //mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtSessionTime)."' where nLookUpCode='SessionTimeout'") or die(mysqli_error($conn));
    //updation for header caption
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtHeadCap) . "' where nLookUpCode='headerCaption'") or die(mysqli_error($conn));

    //update escrow payments
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($chkEscrow) . "' where nLookUpCode='Enable Escrow'") or die(mysqli_error($conn));

    //updation for listing fee type
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($radType) . "' where nLookUpCode='Listing Type'") or die(mysqli_error($conn));

    //update commission Type
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($chkEscrowType) . "' where nLookUpCode='EscrowCommissionType'") or die(mysqli_error($conn));

    // Update monthle fee for use
   // mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtMonthlyFee) . "' where nLookUpCode='monthlyFeePerTransaction'") or die(mysqli_error($conn));

    //upadte no of transaction per month
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtnotrans) . "' where nLookUpCode='freeTransactionsPerMonth'") or die(mysqli_error($conn));

    //upadte success fee amount
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtFee) . "' where nLookUpCode='SuccessFee'") or die(mysqli_error($conn));

    $flag = true;
    $message2 = "Settings updated";
    $ddlStyle = DisplayLookUp('sitestyle');
    $txtSMTPPassword = openssl_decrypt($txtSMTPPassword, $encryptionMethod, $txtHash);
}
}//end if
else {
    $txtLicenseKey = DisplayLookUp('vLicenceKey');
    $txtEmail = DisplayLookUp('4');
    $txtSMTPHost = DisplayLookUp('smtp_host');
    $txtSMTPPassword = DisplayLookUp('smtp_password');
    $txtSMTPEmail = DisplayLookUp('smtp_email');
    $txtSMTPPort = DisplayLookUp('smtp_port');
    $txtSMTPProtocol = DisplayLookUp('smtp_protocol');
    $txtHash = DisplayLookUp('secret_hash');
    $txtSMTPPassword = openssl_decrypt($txtSMTPPassword, $encryptionMethod, $txtHash);
    $txtFees = DisplayLookUp('5');
    $txtEscrow = DisplayLookUp('14');
    $txtTitle = DisplayLookUp('sitetitle');
    $txtName = DisplayLookUp('sitename');
    $txtLogoDisp = DisplayLookUp('sitelogo');
    $txtwelcomePicDisp = DisplayLookUp('welcomeImage');
    $ddlStyle = DisplayLookUp('sitestyle');
    $radUType = DisplayLookUp('userapproval');
    $txtRAmount = DisplayLookUp('3');
    $txtSurvey = DisplayLookUp('9');
    $txtRReg = DisplayLookUp('10');
    $radGoogleAds = DisplayLookUp('googleaddemo');
    $txtGoogleValue = DisplayLookUp('googleadvalue');
    $radBookmark = DisplayLookUp('bookmark_enable');
    $txtBookmarkValue = DisplayLookUp('bookmark_value');
    $radFeedName = DisplayLookUp('feedname');
    $txtSUrl = DisplayLookUp('surl');
    $txtMetaK = DisplayLookUp('Meta Keywords');
    $txtMetaD = DisplayLookUp('Meta Description');
    $txtMaxImg = DisplayLookUp('MaxOfImages');
    $radBanner = DisplayLookUp('BannerDisplay');
    $language_by_ip = DisplayLookUp('language_by_ip');
    $plan_system = DisplayLookUp('plan_system');
    $radThumb = DisplayLookUp('ThumbToolTip');
    //$txtSessionTime=DisplayLookUp('SessionTimeout');
    $txtHeadCap = HEADER_CAPTION;
    $chkEscrow = DisplayLookUp('Enable Escrow');
    $ddlCurrency = DisplayLookUp('currencycode');
    $ddlCurrencySymbol=CurrencyCodeCheck($ddlCurrency);
    $radType = DisplayLookUp('Listing Type');
    $chkEscrowType = DisplayLookUp('EscrowCommissionType');
    $txtnotrans = DisplayLookUp('freeTransactionsPerMonth');
    $txtFee = DisplayLookUp('SuccessFee');
    $txtMonthlyFee = DisplayLookUp('monthlyFeePerTransaction');
    
}//end if
?>
<link href="../styles/tabcontent.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="../js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="../js/style_preview.js"></script>
<script language="javascript" type="text/javascript">

    $(document).ready(function() {
       var styleStr = '<?php echo $ddlStyle; ?>';
       showHint(styleStr);
   });
    
    function validateSettingsForm()
    {
        var frm = window.document.frmSettings;
        
        
        if(isNaN(frm.txtnotrans.value))
        {
            alert("Please enter a valid number for Free Transaction");
            frm.txtnotrans.focus();
            return false;
        }
        if(frm.txtFee.value=='')
        {
            alert("Success Fee can't be blank");
            frm.txtFee.focus();
            return false;
        }
        if(frm.txtHash.value==''){
            alert("Secret Hash can't be blank");
            frm.txtHash.focus();
            return false;
        }
        if(frm.txtSMTPHost.value==''){
            alert("SMTP HOST can't be blank");
            frm.txtSMTPHost.focus();
            return false;
        }
        if(frm.txtSMTPPassword.value==''){
            alert("SMTP Password can't be blank");
            frm.txtSMTPPassword.focus();
            return false;
        }
        if(frm.txtSMTPPort.value==''){
            alert("SMTP Port can't be blank");
            frm.txtSMTPPort.focus();
            return false;
        }
        if(frm.txtSMTPProtocol.value==''){
            alert("SMTP Protocol can't be blank");
            frm.txtSMTPProtocol.focus();
            return false;
        }
        if(frm.txtSUrl.value==''){
            alert("Site Secure Url can't be blank");
            frm.txtSUrl.focus();
            return false;
        }
        /*if(frm.txtMonthlyFee.value=='')
        {
            alert("Monthly fee can't be blank");
            frm.txtMonthlyFee.focus();
            return false;
        }*/

        if(document.getElementById('googleadvalue').style.display!='none')
        {
            if(frm.txtGoogleValue.value=='')
            {
                alert("Google AdSense Value can't be blank");
                frm.txtGoogleValue.focus();
                return false;
            }//end if
        }//end if
        if(document.getElementById('BookmarkValue').style.display!='none')
        {
            if(frm.txtBookmarkValue.value=='')
            {
                alert("Bookmark Value can't be blank");
                frm.txtBookmarkValue.focus();
                return false;
            }//end if
        }//end if
        if(isNaN(frm.txtFees.value) || frm.txtFees.value.length == 0 || frm.txtFees.value.substring(0,1) == " ")
        {
            frm.txtFees.value=0;
            frm.txtFees.focus();
            alert('Please enter a positive number');
            return false;
        }//end if
        <?php
        if ($_POST['chkEscrow'] == 'Yes') {
            ?>
            if(isNaN(frm.txtEscrow.value) || frm.txtEscrow.value.length == 0 || frm.txtEscrow.value.substring(0,1) == " ")
            {
                frm.txtEscrow.value=0;
                frm.txtEscrow.focus();
                alert('Please enter a positive number');
                return false;
            }//end if

            <?php
}//end if
?>
if(isNaN(frm.txtMaxImg.value) || frm.txtMaxImg.value.length == 0 || frm.txtMaxImg.value.substring(0,1) == " " || parseFloat(frm.txtMaxImg.value) <= 0)
{
    frm.txtMaxImg.value=0;
    frm.txtMaxImg.focus();
    alert('Please enter a positive number');
    return false;
        }//end if
        if(!checkMail(frm.txtEmail.value))
        {
            frm.txtEmail.value='email@yoursite.com';
            frm.txtEmail.focus();
            alert('Please enter a valid email address');
            return false;
        }//end else if
        if(!checkMail(frm.txtSMTPEmail.value))
        {
            frm.txtSMTPEmail.value='user@example.com';
            frm.txtSMTPEmail.focus();
            alert('Please enter a valid email address');
            return false;
        }
        if(isNaN(frm.txtRAmount.value) || frm.txtRAmount.value.length == 0 || frm.txtRAmount.value.substring(0,1) == " ")
        {
            frm.txtRAmount.value=0;
            frm.txtRAmount.focus();
            alert('Please enter a positive number');
            return false;
        }//end if
        if(isNaN(frm.txtSurvey.value) || frm.txtSurvey.value.length == 0 || frm.txtSurvey.value.substring(0,1) == " ")
        {
            frm.txtSurvey.value=0;
            frm.txtSurvey.focus();
            alert('Please enter a positive number');
            return false;
        }//end if
        if(isNaN(frm.txtRReg.value) || frm.txtRReg.value.length == 0 || frm.txtRReg.value.substring(0,1) == " ")
        {
            frm.txtRReg.value=0;
            frm.txtRReg.focus();
            alert('Please enter a positive number');
            return false;
        }//end if
        return true;
    }

    function changeParameter()
    {
        var frm = window.document.frmSettings;
        frm.btnSubmit.value='';
        frm.method="post";
        frm.submit();
    }//end fucntion

    function checkAdd()
    {
        //if yes show the value portion
        if(document.getElementById('radGoogleAdsy').value=='yes')
        {
            document.getElementById('googleadvalue').style.display='';
        }//end if
    }//end funciton

    function checkAddN()
    {
        if(document.getElementById('radGoogleAdsn').value=='no')
        {
            document.getElementById('googleadvalue').style.display='none';
        }//end if
    }//end funciton

    //Bookmark
    function checkAddBookmark()
    {
        //if yes show the value portion
        if(document.getElementById('radBookmarky').value=='Yes')
        {
            document.getElementById('BookmarkValue').style.display='';
        }//end if
    }//end function

    function checkAddBookmarkN()
    {
        if(document.getElementById('radBookmarkn').value=='No')
        {
            document.getElementById('BookmarkValue').style.display='none';
        }//end if
    }//end function

    //to show banner link
    function BannerShow(chkVal)
    {
        if(chkVal=='n')
        {
            document.getElementById('bannerHide').style.display='none';
        }//end if
        else
        {
            document.getElementById('bannerHide').style.display='';
        }//end else
    }//end function

    //function for disable/enable
    function showNote(nVar)
    {//cmFixed - cmPer - cmRange

        if(nVar=='fixed' || nVar=='percentage' || nVar=='range')
        { 
            document.getElementById('ShowNoteYes').style.display='block';
//            document.getElementById('ShowNoteNo').style.display='none';
if(nVar=='fixed'){
 document.getElementById('cmFixed').style.display='block';
 document.getElementById('cmPer').style.display='none';
 document.getElementById('cmRange').style.display='none';
}else if(nVar=='percentage'){ 
 document.getElementById('cmFixed').style.display='none';
 document.getElementById('cmPer').style.display='block';
 document.getElementById('cmRange').style.display='none';
}else if(nVar=='range'){
 document.getElementById('cmFixed').style.display='none';
 document.getElementById('cmPer').style.display='none';
 document.getElementById('cmRange').style.display='block';
}            
        }//end if
        else
        {
//            document.getElementById('ShowNoteNo').style.display='';
document.getElementById('ShowNoteYes').style.display='none';

        }//end else
    }//end function

    function showAuthMsg(ccode)
    {
        if(ccode!='USD')
        {
            document.getElementById('showErrorMsg').style.display='';
        }//end if
        else
        {
            document.getElementById('showErrorMsg').style.display='none';
        }//end else
    }//end function

    //function for disable/enable
    function showSettings(nVar)
    {
        if(nVar=='1')
        {
            document.getElementById('ShowSingle').style.display='';
        }//end if
        else
        {
            document.getElementById('ShowSingle').style.display='none';
        }//end else
    }//end function
    function checkValue(t,mode)
    {   
        if (typeof(mode)==='undefined') mode = 0;
        
        if(isNaN(t.value) || t.value.substring(0,1) == " " || t.value.length == 0 || parseFloat(t.value) < 0 || (mode==1 && (Math.round(t.value) != t.value)))
        {
            t.value=0;
            alert("Please enter a valid number for Free Transaction");
        }//end if
    }//end function
    function showPassword(){
        var x = document.getElementById("txtSMTPPassword");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>
<script language="javascript" type="text/javascript" src="../js/qTip.js"></script>

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
                                <td width="100%" class="heading_admn" align="left"><span class="boldtextblack">Configuration Details</span></td>
                            </tr>
                        </table>
                        <?php include_once('../includes/settings_menu.php');
                        ?>
                        <div class="tabcontentstyle">
                            <table width="100%"  border="0" cellspacing="0" cellpadding="0" >
                                <tr>
                                    <td align="left" valign="top"><table width="100%"  border="0" class="admin_tble_2" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td bgcolor="#ffffff"><form name="frmSettings" method ="POST" enctype="multipart/form-data" action = "<?php echo $_SERVER['PHP_SELF'] ?>" onsubmit="return validateSettingsForm();">
                                                <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="maintext2">

                                                    <?php
                                                    if (isset($message) && $message != '') {
                                                        ?>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td colspan="3" align="center" class="warning"><?php echo $message; ?></td>
                                                        </tr>
                                                    <?php  } else if(isset($message2) && $message2!='') {

                                                        ?>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td colspan="3" align="center" class="success"><b><?php echo $message2;?></b></td>
                                                        </tr>
                                                        <?php


                                                    } //end if  ?>  
                                                    <tr bgcolor="#FFFFFF">
                                                        <td align="left"><input type="submit" name="btnSubmit" value="Change Settings" class="submit"></td>
                                                        <td colspan="2" align="right">&nbsp;</td>
                                                    </tr>
                                                    <tr bgcolor="#FFFFFF">
                                                        <td width="40%" align="left">License Key</td>
                                                        <td colspan="2">
                                                            <input type="text" class="textbox2" name="txtLicenseKey" value="<?php echo htmlentities($txtLicenseKey); ?>" size="40" maxlength="40" readonly> 
                                                            <i title="This is the licence key which has been provided while installation" id="help1"><strong>?</strong></i>
                                                        </td>
                                                    </tr>

                                                    <?php
                                                    $sql = "select vLookUpDesc from " . TABLEPREFIX . "lookup where nLookupcode=15 and vLookUpDesc='0'";
                                                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                    if (mysqli_num_rows($result) > 0) {
                                                        ?>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td width="40%" align="left">Registration Amount (If plan is disabled)</td>
                                                            <td colspan="2">
                                                                <input name="txtRAmount" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtRAmount); ?>" onBlur="javascript:checkValue(this);">
                                                                <i title="The amount to be paid for registration if the plan system is disabled" id="help2"><strong>?</strong></i>
                                                            </td>
                                                        </tr>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td width="40%" align="left">Referral Fee For Survey(<?php echo $ddlCurrencySymbol; ?>)</td>
                                                            <td colspan="2">
                                                                <input name="txtSurvey" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtSurvey); ?>" onBlur="javascript:checkValue(this);">
                                                                <i title="The amount which would be paid to the users on successfull survey" id="help3"><strong>?</strong></i>
                                                            </td>
                                                        </tr>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td width="40%" align="left">Referral Fee For Registration(<?php echo $ddlCurrencySymbol; ?>)</td>
                                                            <td colspan="2">
                                                                <input name="txtRReg" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtRReg); ?>" onBlur="javascript:checkValue(this);">
                                                                <i title="The amount which would be paid to the referrer on the successfull registration on the referred person" id="help4"><strong>?</strong></i>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                }//end if
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left">Success Fee for Transaction(s) </td>
                                                    <td colspan="2">
                                                        <?php echo $ddlCurrencySymbol; ?><input type="text" class="textbox2" name="txtFee" value="<?php echo htmlentities($txtFee); ?>" size="5" maxlength="40" onBlur="javascript:checkValue(this);"> <!--<a href="#" onclick="javascript:alert(document.getElementById('help_success_fee').innerHTML);return false;">Info</a>-->
                                                        <i title="The fee that has to be paid by users on each successfull transaction" id="help5"><strong>?</strong></i>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left">No of free transaction(s)</td>
                                                    <td colspan="2">
                                                        <input type="text" class="textbox2" name="txtnotrans" value="<?php echo htmlentities($txtnotrans); ?>" size="5" maxlength="40" onBlur="javascript:checkValue(this,1);"> <!--<a href="#" onclick="javascript:alert(document.getElementById('help_success_fee').innerHTML);return false;">Info</a>-->
                                                        <i title="The no of free make offer transactions. After this the user will need to pay a success fee for each transactions " id="help5"><strong>?</strong></i>
                                                    </td>
                                                </tr>
                                                
                                                <tr bgcolor="#FFFFFF">
                                                    <td width="40%" align="left">Administrator Email</td>
                                                    <td colspan="2">
                                                        <input name="txtEmail" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtEmail); ?>">
                                                        <i title="Official site administrator's email address" id="help6"><strong>?</strong></i>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                    <td width="40%" align="left">Secret Hash</td>
                                                    <td colspan="2">
                                                        <input name="txtHash" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtHash); ?>">
                                                        <i title="Secret Hash" id="help6"><strong>?</strong></i>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                    <td width="40%" align="left">SMTP HOST</td>
                                                    <td colspan="2">
                                                        <input name="txtSMTPHost" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtSMTPHost); ?>">
                                                        <i title="SMTP HOST" id="help6"><strong>?</strong></i>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                    <td width="40%" align="left">SMTP Email</td>
                                                    <td colspan="2">
                                                        <input name="txtSMTPEmail" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtSMTPEmail); ?>">
                                                        <i title="SMTP Username" id="help6"><strong>?</strong></i>
                                                    </td>
                                                </tr><tr bgcolor="#FFFFFF">
                                                    <td width="40%" align="left">SMTP Password</td>
                                                    <td colspan="2">
                                                        <input id="txtSMTPPassword" name="txtSMTPPassword" type="password" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtSMTPPassword); ?>">
                                                        <i title="SMTP Password" id="help6"><strong>?</strong></i>
                                                        <input name="showpass" type="checkbox" class="textbox2"  onclick="showPassword()"/> Show Password

                                                    </td>
                                                </tr>

                                                <tr bgcolor="#FFFFFF">
                                                    <td width="40%" align="left">SMTP Port</td>
                                                    <td colspan="2">
                                                        <input name="txtSMTPPort" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtSMTPPort); ?>">
                                                        <i title="SMTP PORT" id="help6"><strong>?</strong></i>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                    <td width="40%" align="left">SMTP Protocol</td>
                                                    <td colspan="2">
                                                        <input name="txtSMTPProtocol" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtSMTPProtocol); ?>">
                                                        <i title="SMTP Protocol , tls or ssl" id="help6"><strong>?</strong></i>
                                                    </td>
                                                </tr>

                                                <tr bgcolor="#FFFFFF">
                                                    <td width="40%" align="left">User Approval Type</td>
                                                    <td colspan="2"><input name="radUType" type="radio" value="1" <?php echo  $radUType == "1" ? "checked" : "" ?>>
                                                      Admin Approval
                                                      &nbsp;&nbsp;<input name="radUType" type="radio" value="0" <?php echo  $radUType == "0" ? "checked" : "" ?>>Automatic Approval
                                                      &nbsp;&nbsp;<input name="radUType" type="radio" value="E" <?php echo  $radUType == "E" ? "checked" : "" ?>>Email Approval
                                                      &nbsp;&nbsp;&nbsp;<i title="The type of approval for the registrations could be selected here" id="help7"><strong>?</strong></i>
                                                  </td>
                                              </tr>
                                              <tr bgcolor="#FFFFFF">
                                                <td align="left">Site Secure Url </td>
                                                <td colspan="2">
                                                    <input name="txtSUrl" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtSUrl); ?>">
                                                    <i title="If SSL is enabled in the server, the secured url which begins with https:// has to be provided here. Otherwise normal site url has to be provided." id="help8"><strong>?</strong></i>
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="40%" align="left">Site Logo</td>
                                                <td colspan="2">
                                                    <input type="file" name="txtLogo" class="textbox2" style="float:left;"> &nbsp; 
                                                    <i title="Site Logo which appears in the left top of the site,Please upload image with resolution greater or equal to 184x57" id="help9"><strong>?</strong></i>
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td colspan="2"><?php
                                                if(!$txtLogoDisp)
                                                    $txtLogoDisp = DisplayLookUp('sitelogo');
                                                        //checking size
                                                @list($width_new, $height_new) = @getimagesize('../images/' . $logourl);
                                                if ($width_new > 300) {
                                                    $width_new = '300';
                                                        }//end if
                                                        else {
                                                            $width_new = $width_new;
                                                        }//end else
                                                        //checking height
                                                        if ($height_new > 100) {
                                                            $height_new = '100';
                                                        }//end if
                                                        else {
                                                            $height_new = $height_new;
                                                        }//end else
                                                        if ($txtLogoDisp != '') {
                                                            echo '<img src="../images/' . $txtLogoDisp . '" width="' . $width_new . '" height="' . $height_new . '" border="0" title="Site Logo" alt="Site Logo">';
                                                        }//end if
                                                        ?></td>
                                                    </tr>
                                                    <tr valign="top" bgcolor="#FFFFFF" style="display: none;">
                                                        <td width="40%" align="left">Site Style</td>
                                                        <td width="23%" nowrap="nowrap">
                                                            <?php ShowTemplates($ddlStyle); ?>
                                                            <i title="Changing this will change the theme of the site" id="help10"><strong>?</strong></i>
                                                        </td>
                                                        <td width="37%" align="left"><span id="txtHint"></span></td>
                                                    </tr>
                                                    <tr bgcolor="#FFFFFF" style="display: none;">
                                                        <td align="left">Site Welcome Picture </td>
                                                        <td colspan="2">
                                                            <input type="file" name="txtWelcomePic" class="textbox2" style="float:left; ">  &nbsp; 
                                                            <i title="The welcome picture of the site could be changed here size" id="help11"><strong>?</strong></i>
                                                        </td>
                                                    </tr>
                                                    <tr bgcolor="#FFFFFF"  style="display: none;">
                                                        <td align="left">&nbsp;</td>
                                                        <td colspan="2">
                                                            <?php
                                                            if(!$txtwelcomePicDisp)
                                                                $txtwelcomePicDisp = DisplayLookUp('welcomeImage');
                                                        //checking size
                                                            @list($width_new1, $height_new1) = @getimagesize('../images/' . $txtwelcomePicDisp);
                                                            if ($width_new1 > 150) {
                                                                $width_new1 = '150';
                                                        }//end if
                                                        else {
                                                            $width_new1 = $width_new1;
                                                        }//end else
                                                        //checking height
                                                        if ($height_new1 > 150) {
                                                            $height_new1 = '150';
                                                        }//end if
                                                        else {
                                                            $height_new1 = $height_new1;
                                                        }//end else
                                                        if ($txtwelcomePicDisp != '') {
                                                            echo '<img src="../images/' . $txtwelcomePicDisp . '" width="' . $width_new1 . '" height="' . $height_new1 . '" border="0" title="Site welcome Image" alt="Site welcome Image" >';
                                                        }//end if
                                                        ?>
                                                        <br>
                                                        Image size should be 1366 x 421 
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left">Select Currency</td>
                                                    <td colspan="2"><select name="ddlCurrency" class="textbox2" onChange="showAuthMsg(this.value);">
                                                        <option value="USD" <?php if ($ddlCurrency == 'USD') {
                                                            echo 'selected';
                                                        } ?>>U.S. Dollars</option>
                                                        <option value="AUD" <?php if ($ddlCurrency == 'AUD') {
                                                            echo 'selected';
                                                        } ?>>Australian Dollars</option>
                                                        <option value="GBP" <?php if ($ddlCurrency == 'GBP') {
                                                            echo 'selected';
                                                        } ?>>British Pounds</option>
                                                        <option value="CAD" <?php if ($ddlCurrency == 'CAD') {
                                                            echo 'selected';
                                                        } ?>>Canadian Dollars</option>
                                                        <option value="CZK" <?php if ($ddlCurrency == 'CZK') {
                                                            echo 'selected';
                                                        } ?>>Czech Koruna</option>
                                                        <option value="DKK" <?php if ($ddlCurrency == 'DKK') {
                                                            echo 'selected';
                                                        } ?>>Danish Kroner</option>
                                                        <option value="EUR" <?php if ($ddlCurrency == 'EUR') {
                                                            echo 'selected';
                                                        } ?>>Euros</option>
                                                        <option value="HKD" <?php if ($ddlCurrency == 'HKD') {
                                                            echo 'selected';
                                                        } ?>>Hong Kong Dollar</option>
                                                        <option value="HUF" <?php if ($ddlCurrency == 'HUF') {
                                                            echo 'selected';
                                                        } ?>>Hungarian Forint</option>
                                                        <option value="ILS" <?php if ($ddlCurrency == 'ILS') {
                                                            echo 'selected';
                                                        } ?>>Israeli New Shekels</option>
                                                        <option value="JPY" <?php if ($ddlCurrency == 'JPY') {
                                                            echo 'selected';
                                                        } ?>>Japanese Yen</option>
                                                        <option value="MXN" <?php if ($ddlCurrency == 'MXN') {
                                                            echo 'selected';
                                                        } ?>>Mexican Pesos</option>
                                                        <option value="NZD" <?php if ($ddlCurrency == 'NZD') {
                                                            echo 'selected';
                                                        } ?>>New Zealand Dollar</option>
                                                        <option value="NOK" <?php if ($ddlCurrency == 'NOK') {
                                                            echo 'selected';
                                                        } ?>>Norwegian Kroner</option>
                                                        <option value="PLN" <?php if ($ddlCurrency == 'PLN') {
                                                            echo 'selected';
                                                        } ?>>Polish Zlotych</option>
                                                        <option value="SGD" <?php if ($ddlCurrency == 'SGD') {
                                                            echo 'selected';
                                                        } ?>>Singapore Dollar</option>
                                                        <option value="SEK" <?php if ($ddlCurrency == 'SEK') {
                                                            echo 'selected';
                                                        } ?>>Swedish Kronor</option>
                                                        <option value="CHF" <?php if ($ddlCurrency == 'CHF') {
                                                            echo 'selected';
                                                        } ?>>Swiss Francs</option>
                                                    </select>
                                                    <i title="The currency to be displayed on the site could be selected here" id="help12"><strong>?</strong></i>
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF" id="showErrorMsg" style="<?php if ($ddlCurrency == 'USD') {
                                                echo 'display:none;';
                                            } ?>">
                                            <td align="left">&nbsp;</td>
                                            <td colspan="2" class="warning"><b>Authorize.net will support only US Dollar</b></td>
                                        </tr>
                                        <tr bgcolor="#FFFFFF">
                                            <td align="left">Fees for Making a Listing Featured(<?php echo $ddlCurrencySymbol; ?>)</td>
                                            <td colspan="2">
                                                <input name="txtFees" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtFees); ?>" onBlur="javascript:checkValue(this);">
                                                <i title="This is the fees the user has to pay to make the sale item featured" id="help13"><strong>?</strong></i>
                                            </td>
                                        </tr>
                                        <?php
                                                    //checking point enable in website
                                        if (DisplayLookUp('EnablePoint') != '1') {
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" valign="top">Enable Listing Fee for Sale Item</td>
                                                <td colspan="2" valign="top"><input type="radio" name="radType" value="1" <?php if ($radType != '0') {
                                                    echo 'checked';
                                                } ?> onClick="showSettings('1');">Yes
                                                <input type="radio" name="radType" value="0" <?php if ($radType == '0') {
                                                    echo 'checked';
                                                } ?> onClick="showSettings('0');">No
                                                &nbsp;&nbsp;&nbsp;<i title="This is the listing fee for the sale item addition" id="help14"><strong>?</strong></i>
                                                <div id="ShowSingle" style="<?php if ($radType != '1') {
                                                    echo 'display:none;';
                                                } ?>">
                                                <!--<a href="listing_combinations.php"><b>Click Here To Manage Range</b> </a> -->
                                                <?php if ($radType != '1') {?><br><font color="#ff0000">(Please save this settings and edit the listing fee from listing fee range)</font><?php }?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                                }//end if
                                                
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left" valign="top">Enable Escrow Payments</td>
                                                    <td colspan="2" valign="top" nowrap="nowrap">
                                                        <input type="radio" name="chkEscrow" value="fixed" <?php if ($chkEscrowType == 'fixed') {
                                                            echo 'checked';
                                                        } ?> onClick="showNote(this.value);">Fixed
                                                        <input type="radio" name="chkEscrow" value="percentage" <?php if ($chkEscrowType == 'percentage') {
                                                            echo 'checked';
                                                        } ?> onClick="showNote(this.value);">Percentage 
                                                        <input type="radio" name="chkEscrow" value="range" <?php if ($chkEscrowType == 'range') {
                                                            echo 'checked';
                                                        } ?> onClick="showNote(this.value);">Range
                                                        <input type="radio" name="chkEscrow" value="No" <?php if ($chkEscrowType == 'No') {
                                                            echo 'checked';
                                                        } ?> onClick="showNote('No');">No
                                                        &nbsp;&nbsp;&nbsp;<i title="Escrow payment fee type to be selected here" id="help15"><strong>?</strong></i>
                                                        <div id="ShowNoteYes" style="<?php if ($chkEscrowType == 'No') {
                                                           echo 'display:none;';
                                                       } ?>">
                                                       <br>

                                                       <span id="cmFixed" style="<?php if ($chkEscrowType != 'fixed') {?>display: none;<?php }?>">Set Commission <?php echo $ddlCurrencySymbol;?> <input name="txtEscrowFixed" type="text" class="textbox2" size="8" value="<?php echo htmlentities($txtEscrow); ?>"></span>

                                                       <span id="cmPer" style="<?php if ($chkEscrowType != 'percentage') {?>display: none;<?php }?>">Set Commission <input name="txtEscrowPercentage" type="text" class="textbox2" size="8" value="<?php echo htmlentities($txtEscrow); ?>">%</span>

                                                       <span id="cmRange" style="<?php if ($chkEscrowType != 'range') {?>display: none;<?php }?>">
                                                           <!-- <a href="manage_commission_range.php"><b>Click Here To Manage Escrow Commission Range</b></a> -->
                                                           <?php //if ($chkEscrowType != 'range' ) {?><br><font color="#ff0000">(Please save this settings and edit the escrow fee from escrow fee range)</font><?php //}?>
                                                       </span>

                                                             <!--<br><span class="warning"><b>Note: In this mode the buyer will pay the admin, admin in turn deducts his commission and pays the rest to the seller.
                                                             User registration type could be free or paid.</b></span>-->
                                                         </div>                                                       
                                                        <!--<div id="ShowNoteNo" style="<?php //if ($chkEscrowType == 'fixed' || $chkEscrowType == 'percentage' || $chkEscrowType == 'range') { echo 'display:none;'; } ?>" class="warning">
                                                                <br><b>Note: In this mode the seller gets the payment directly from the buyer. User registration type could be free or paid.</b>
                                                            </div>-->
                                                        </td>
                                                    </tr>

                                                    <tr bgcolor="#FFFFFF">
                                                        <td align="left">Max Number of Images Allowed for a Swap/Sale Item</td>
                                                        <td colspan="2">
                                                            <input name="txtMaxImg" type="text" class="textbox2" size="40" maxlength="100" value="<?php echo htmlentities($txtMaxImg); ?>" onBlur="javascript:checkValue(this,1);">
                                                            <i title="The no. of images allowed for each swap/sale item" id="help16"><strong>?</strong></i>
                                                        </td>
                                                    </tr>
                                                    <tr bgcolor="#FFFFFF">
                                                        <td align="left">Enable Banner Display</td>
                                                        <td colspan="2"><input type="radio" name="radBanner" id="radBanner" value="Yes" <?php if ($radBanner == 'Yes') {
                                                            echo 'checked';
                                                        } ?> onClick="BannerShow('y');">Yes <input name="radBanner" type="radio" id="radBanner" value="No" <?php if ($radBanner == 'No') {
                                                         echo 'checked';
                                                     } ?> onClick="BannerShow('n');">No
                                                     <?php
                                                     if ($radBanner == 'No') {
                                                         $shStyle = 'display:none;';
                                                        }//end if
                                                        ?>
                                                        &nbsp;&nbsp;&nbsp;<i title="Click 'Yes' to enable banner display in the site" id="help17"><strong>?</strong></i>
                                                        <?php echo '<br><a href="banners.php" id="bannerHide" style="' . $shStyle . '">Click Here to Manage Banners</a>'; ?>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left">Plan System</td>
                                                    <td colspan="2">
                                                        <input type="radio" name="plan_system" id="plan_system" value="yes" <?php if ($plan_system == 'yes') {
                                                            echo 'checked';
                                                        } ?>>Yes
                                                        <input type="radio" name="plan_system" id="plan_system" value="no" <?php if ($plan_system == 'no') {
                                                            echo 'checked';
                                                        } ?>>No
                                                        &nbsp;&nbsp;&nbsp;<i title="Click 'Yes' to enable plan system for the user account" id="help18"><strong>?</strong></i>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left">Enable IP Based Language Display</td>
                                                    <td colspan="2">
                                                        <input type="radio" name="language_by_ip" id="language_by_ip" value="yes" <?php if ($language_by_ip == 'yes') {
                                                            echo 'checked';
                                                        } ?>>Yes
                                                        <input type="radio" name="language_by_ip" id="language_by_ip" value="no" <?php if ($language_by_ip == 'no') {
                                                            echo 'checked';
                                                        } ?>>No
                                                        &nbsp;&nbsp;&nbsp;<i title="Click 'Yes' to enable automatic detection of the user's location to display the site in appropriate language" id="help19"><strong>?</strong></i>
                                                    </td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left">Item Details Tool Tip in Listing Page </td>
                                                    <td colspan="2"><input type="radio" name="radThumb" id="radThumb" value="Yes" <?php if ($radThumb == 'Yes') {
                                                        echo 'checked';
                                                    } ?>>Yes <input name="radThumb" type="radio" id="radThumb" value="No" <?php if ($radThumb == 'No') {
                                                        echo 'checked';
                                                    } ?>>No
                                                    &nbsp;&nbsp;&nbsp;<i title="Click 'Yes' to enable tool tip in the items listing page" id="help19"><strong>?</strong></i>
                                                </td>
                                            </tr>
                                            <!--
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Default Logout Time</td>
                                                <td><input type="text" name="txtSessionTime" maxlength="200" size="40" class="textbox2" value="<?php //echo htmlentities($txtSessionTime); ?>"></td>
                                            </tr>-->
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Enable Google AdSense</td>
                                                <td colspan="2"><input type="radio" name="radGoogleAds" id="radGoogleAdsy" value="yes" <?php if ($radGoogleAds == 'yes') {
                                                    echo 'checked';
                                                } ?> onClick="checkAdd();">Yes <input name="radGoogleAds" type="radio" id="radGoogleAdsn" onClick="checkAddN();" value="no" <?php if ($radGoogleAds == 'no') {
                                                    echo 'checked';
                                                } ?>>No
                                                &nbsp;&nbsp;&nbsp;<i title="Click 'Yes' to enable Google advertisement in the site" id="help19"><strong>?</strong></i>
                                            </td>
                                        </tr>
                                        <tr bgcolor="#FFFFFF" id="googleadvalue" style="<?php if ($radGoogleAds == 'no') {
                                            echo 'display:none;';
                                        } ?>">
                                        <td align="left" valign="top">Google AdSense Value</td>
                                        <td colspan="2" valign="top">
                                            <textarea name="txtGoogleValue" class="textbox2" rows="8" cols="45" style="float:left; "><?php echo $txtGoogleValue; ?></textarea>
                                            &nbsp;&nbsp;&nbsp;<i title="Provide here the Google Adsense code provided by Google" id="help20"><strong>?</strong></i>
                                        </td>
                                    </tr>
                                    <tr bgcolor="#FFFFFF">
                                        <td align="left">Enable Bookmark</td>
                                        <td colspan="2"><input type="radio" name="radBookmark" id="radBookmarky" value="Yes" <?php if ($radBookmark == 'Yes') {
                                            echo 'checked';
                                        } ?> onClick="checkAddBookmark();">Yes <input name="radBookmark" type="radio" id="radBookmarkn" onClick="checkAddBookmarkN();" value="No" <?php if ($radBookmark == 'No') {
                                            echo 'checked';
                                        } ?>>No
                                        &nbsp;&nbsp;&nbsp;<i title="Click 'Yes' to enable bookmark feature in the site" id="help21"><strong>?</strong></i>
                                    </td>
                                </tr>
                                <tr bgcolor="#FFFFFF" id="BookmarkValue" style="<?php if ($radBookmark == 'No') {
                                    echo 'display:none;';
                                } ?>">
                                <td align="left" valign="top">Bookmark Value</td>
                                <td colspan="2" valign="top"><textarea name="txtBookmarkValue" class="textbox2" rows="8" cols="45" style="float:left; "><?php echo $txtBookmarkValue; ?></textarea>
                                   &nbsp;   <i title="Provide here the code for bookmark feature provided by AddThis site" id="help22"><strong>?</strong></i>
                                   <br>
                                   <br>
                                   <div class="clear"></div>
                                   You can get your Bookmark Value from here : <a href="http://www.addthis.com" target="_blank">http://www.addthis.com/</a></td>
                               </tr>
                               <tr bgcolor="#FFFFFF">
                                <td align="left" valign="top">Enable RSS Feed </td>
                                <td colspan="2" valign="top"><input type="radio" name="radFeedName" value="Yes" <?php if ($radFeedName == 'Yes') {
                                    echo 'checked';
                                } ?>>Yes <input name="radFeedName" type="radio" value="No" <?php if ($radFeedName == 'No') {
                                    echo 'checked';
                                } ?>>No
                                &nbsp;&nbsp;&nbsp;<i title="Click 'Yes' to enable RSS feeds feature in the site" id="help23"><strong>?</strong></i>
                                <br>
                                <?php echo '<a href="' . SITE_URL . '/feed.php" target="_blank">' . SITE_URL . '/feed.php</a>'; ?></td></tr>
                                <tr bgcolor="#FFFFFF">
                                    <td align="left">&nbsp;</td>
                                    <td colspan="2"><input type="submit" name="btnSubmit" value="Change Settings" class="submit"></td>
                                </tr>

                            </table></form>
                        </td>
                    </tr>
                </table></td>
            </tr>
        </table></div>
    </td>
</tr>
</table>
</div>
</div>
<?php include_once('../includes/footer_admin.php'); ?>