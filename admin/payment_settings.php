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
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE='payment_settings';

function isValidEmail($email) {
    $email = trim($email);
    if ($email == "") {
        return false;
    }//end if
    /*
     * if (!eregi("^" . "[a-z0-9]+([_\\.-][a-z0-9]+)*" . // user
    "@" . "([a-z0-9]+([\.-][a-z0-9]+)*)+" . // domain
    "\\.[a-z]{2,}" . // sld, tld
    "$", $email, $regs)) {
     */
    if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',$email)) {
        return false;
    }//end if
    else {
        return true;
    }
}//end funciton

function isNotNull($value) {
    if (is_array($value)) {
        if (sizeof($value) > 0) {
            return true;
        }//end if
        else {
            return false;
        }
    }//end if
    else {
        if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
            return true;
        }//end if
        else {
            return false;
        }
    }
}//end function

$flag = false;
$var_param="";
$var_value="";

if (function_exists('get_magic_quotes_gpc')) {
    $chkPayPalSupport=stripslashes($_POST['paypalsupport']);
    $chkPayPalMode=stripslashes($_POST['paypalmode']);
    $txtPaypalEmail=stripslashes($_POST['paypalemail']);
    $txtPaypalIDTOKEN=stripslashes($_POST['paypalauthtoken']);
    $txtAuthorizeLoginId = stripslashes($_POST["txtAuthLoginId"]);
    $txtAuthorizeTransKey = stripslashes($_POST["txtAuthTransKey"]);
    //$txtAuthorizePassword = stripslashes($_POST["txtAuthPassword"]);
    $txtAuthorizeEmail =  stripslashes($_POST["txtAuthEmail"]);
    $chkAuthorizeMode = stripslashes($_POST["txtAuthMode"]);
    $chkAuthSupport=stripslashes($_POST['chkAuthSupport']);
    $chkYourpaySupport = stripslashes($_POST["chkYourpaySupport"]);
    $txtYourpayStoreId = stripslashes($_POST["txtYourpayStoreId"]);
    $chkYourpayMode =  stripslashes($_POST["chkYourpayMode"]);
    $frmpemyourfile2=stripslashes($_POST['frmpemyourfile2']);
    $chkGoogleCheckoutSupport=stripslashes($_POST['chkGoogleCheckoutSupport']);
    $chkGoogleCheckoutMode=stripslashes($_POST['chkGoogleCheckoutMode']);
    $txtGoogleCheckoutMerchantId=stripslashes($_POST['txtGoogleCheckoutMerchantId']);
    $txtGoogleCheckoutMerchantKey=stripslashes($_POST['txtGoogleCheckoutMerchantKey']);
    $chkCurrencyCode=stripslashes($_POST['chkCurrencyCode']);
    $chkOtherSupport=stripslashes($_POST['chkOtherSupport']);
    $txtWorldpayInstId=stripslashes($_POST['txtWorldpayInstId']);
    $txtWorldpayEmail=stripslashes($_POST['txtWorldpayEmail']);
    $radWTransactMethod=stripslashes($_POST['radWTransactMethod']);
    $chkWorldPay=stripslashes($_POST['chkWorldPay']);
    $chkWorldPayDemo=stripslashes($_POST['chkWorldPayDemo']);
    $ddlPaymentMethod=stripslashes($_POST['ddlPaymentMethod']);
    $txtBluePayAccId=stripslashes($_POST['txtBluePayAccId']);
    $txtStripePublicId=stripslashes($_POST['txtStripePublicId']);
    $txtStripePublicIdLive=stripslashes($_POST['txtStripePublicIdLive']);
    $txtBluePayKey=stripslashes($_POST['txtBluePayKey']);
    $txtStripeKey=stripslashes($_POST['txtStripeKey']);
    $txtStripeKeyLive=stripslashes($_POST['txtStripeKeyLive']);
    $chkBluePay=stripslashes($_POST['chkBluePay']);
    $chkStripe=stripslashes($_POST['chkStripe']);
    $chkBluePayDemo=stripslashes($_POST['chkBluePayDemo']);
    $chkStripeDemo=stripslashes($_POST['chkStripeDemo']);
}//end if
else {
    $chkPayPalSupport=$_POST['paypalsupport'];
    $chkPayPalMode=$_POST['paypalmode'];
    $txtPaypalEmail=$_POST['paypalemail'];
    $txtPaypalIDTOKEN=$_POST['paypalauthtoken'];
    $txtAuthorizeLoginId = trim($_POST["txtAuthLoginId"]);
    $txtAuthorizeTransKey =  trim($_POST["txtAuthTransKey"]);
    //$txtAuthorizePassword = trim($_POST["txtAuthPassword"]);
    $txtAuthorizeEmail =  trim($_POST["txtAuthEmail"]);
    $chkAuthorizeMode = $_POST["txtAuthMode"];
    $chkAuthSupport=$_POST['chkAuthSupport'];
    $chkYourpaySupport = $_POST["chkYourpaySupport"];
    $txtYourpayStoreId = $_POST["txtYourpayStoreId"];
    $chkYourpayMode =  $_POST["chkYourpayMode"];
    $frmpemyourfile2=$_POST['frmpemyourfile2'];
    $chkGoogleCheckoutSupport=$_POST['chkGoogleCheckoutSupport'];
    $chkGoogleCheckoutMode=$_POST['chkGoogleCheckoutMode'];
    $txtGoogleCheckoutMerchantId=$_POST['txtGoogleCheckoutMerchantId'];
    $txtGoogleCheckoutMerchantKey=$_POST['txtGoogleCheckoutMerchantKey'];
    $chkCurrencyCode=$_POST['chkCurrencyCode'];
    $chkOtherSupport=$_POST['chkOtherSupport'];
    $txtWorldpayInstId=$_POST['txtWorldpayInstId'];
    $txtWorldpayEmail=$_POST['txtWorldpayEmail'];
    $radWTransactMethod=$_POST['radWTransactMethod'];
    $chkWorldPay=$_POST['chkWorldPay'];
    $chkWorldPayDemo=$_POST['chkWorldPayDemo'];
    $ddlPaymentMethod=$_POST['ddlPaymentMethod'];
    $txtBluePayAccId=$_POST['txtBluePayAccId'];
    $txtBluePayAccId=$_POST['txtBluePayAccId'];
    $txtBluePayKey=$_POST['txtBluePayKey'];
    $txtStripeKey=$_POST['txtStripeKey'];
    $txtStripeKeyLive=$_POST['txtStripeKeyLive'];
    $chkBluePay=$_POST['chkBluePay'];
    $chkStripe=$_POST['chkStripe'];
    $chkBluePayDemo=$_POST['chkBluePayDemo'];
    $chkStripeDemo=$_POST['chkStripeDemo'];
} 

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=="Change Settings") {
    //update payment method
    update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($ddlPaymentMethod)."'","where nLookUpCode = 'PaymentMethod'");

    //checking payment method
    switch($ddlPaymentMethod) {
        case "NN":
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enableworldpay'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='NO'","where nLookUpCode = 'authsupport'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='NO'","where nLookUpCode = 'yourpaysupport'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablebluepay'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablestripe'");
            break;

        case "CC":
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enableworldpay'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='NO'","where nLookUpCode = 'yourpaysupport'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablebluepay'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablestripe'");
            break;

        case "YP":
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enableworldpay'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='NO'","where nLookUpCode = 'authsupport'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablebluepay'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablestripe'");
            break;

        case "WP":
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='NO'","where nLookUpCode = 'authsupport'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='NO'","where nLookUpCode = 'yourpaysupport'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablebluepay'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablestripe'");
            break;

        case "BP":
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enableworldpay'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='NO'","where nLookUpCode = 'authsupport'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='NO'","where nLookUpCode = 'yourpaysupport'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablestripe'");
            break;
        case "SP":
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enableworldpay'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablebluepay'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='NO'","where nLookUpCode = 'authsupport'");
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='NO'","where nLookUpCode = 'yourpaysupport'");
            break;
    }

    if($chkPayPalSupport == "on") {
        $chkPayPalSupport = "YES";
    }
    else {
        $chkPayPalSupport = "NO";
    }

    if($chkPayPalMode == "on") {
        $chkPayPalMode = "LIVE";
    }//end if
    else {
        $chkPayPalMode = "TEST";
    }

    if($chkPayPalSupport=="YES") {  //need paypal support
        $message2="";

        if ( !isNotNull($txtPaypalEmail)) {
            $message2 .= "* Paypal Email empty<br>";
        }//end if
        else {
            if(!isValidEmail($txtPaypalEmail)) {
                $message2 .= "* Invalid Paypal Email <br>";
            }//end if
        }

        if ( !isNotNull($txtPaypalIDTOKEN)) {
            $message2 .= "* Paypal Identity token is empty<br>";
        }

        if($message2=="") {
            //updation for paypalsupport
            mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='YES' where nLookUpCode='paypalsupport'") or die(mysqli_error($conn));

            //updation for paypalmode
            mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($chkPayPalMode)."' where nLookUpCode='paypalmode'") or die(mysqli_error($conn));

            //updation for paypalemail
            mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtPaypalEmail)."' where nLookUpCode='paypalemail'") or die(mysqli_error($conn));

            //updation for paypalauthtoken
            mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtPaypalIDTOKEN)."' where nLookUpCode='paypalauthtoken'") or die(mysqli_error($conn));
        }//end if
    }//end if
    else {
        //updation for paypalsupport
        mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='NO' where nLookUpCode='paypalsupport'") or die(mysqli_error($conn));
    }

    if($ddlPaymentMethod=='CC') {
        if(PAYMENT_CURRENCY_CODE=='USD') {
            if($chkAuthSupport == "on") {
                $chkAuthSupport = "YES";
            }//end if
            else {
                $chkAuthSupport = "NO";
            }


            if($chkAuthorizeMode == "on") {
                $chkAuthorizeMode = "LIVE";
            }//end if
            else {
                $chkAuthorizeMode = "TEST";
            }

            //$message2="";
            if (!isNotNull($txtAuthorizeLoginId)) {
                $message2 .= "* Authorize Login Id is required! <br>";
            }//end if
            if (!isNotNull($txtAuthorizeTransKey)) {
                $message2 .= "* Authorize Transaction Key is required! <br>";
            }//end if
            if (!isNotNull($txtAuthorizeEmail)) {
                $message2 .= "* Authorize Email is required! <br>";
            }//end if
            else {
                if (!isValidEmail($txtAuthorizeEmail)) {
                    $message2 .= "* Invalid Authorize Email! <br>";
                }//end if
            }

            //checking currency code
            if($chkCurrencyCode!='USD') {
                $message2 .= "* Authorize.net will support only US Dollar. Please change currency to support authorize.net! <br>";
            }//end if

            if($message2=="") {

                //updation for auth support
                mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($chkAuthSupport)."' where nLookUpCode='authsupport'") or die(mysqli_error($conn));

                //updation for authloginid
                mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtAuthorizeLoginId)."' where nLookUpCode='authloginid'") or die(mysqli_error($conn));

                //updation for authloginpass
                //mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtAuthorizePassword)."' where nLookUpCode='authloginpass'") or die(mysqli_error($conn));

                //updation for authlogintranskey
                mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtAuthorizeTransKey)."' where nLookUpCode='authlogintranskey'") or die(mysqli_error($conn));

                //updation for authemail
                mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtAuthorizeEmail)."' where nLookUpCode='authemail'") or die(mysqli_error($conn));

                //updation for authmode
                mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($chkAuthorizeMode)."' where nLookUpCode='authmode'") or die(mysqli_error($conn));

            }//end if
        }//end currency check if
    }//end payment method if

    if($ddlPaymentMethod=='YP') {
        //yourpay settings
        if($chkYourpaySupport == "on") {
            $chkYourpaySupport = "YES";
        }//end if
        else {
            $chkYourpaySupport = "NO";
        }

        if($chkYourpayMode == "on") {
            $chkYourpayMode = "LIVE";
        }//end if
        else {
            $chkYourpayMode = "TEST";
        }

        if($chkYourpaySupport=="YES") {  //need First Data support
            $message2="";

            if ( !isNotNull($txtYourpayStoreId)) {
                $message2 .= "* First Data Store Id is empty<br>";
            }//end if

            if($frmpemyourfile2=='') {
                if ( !isNotNull($_FILES['frmpemyourfile']['name'])) {
                    $message2 .= "* First Data pem file is empty<br>";
                }//end if
            }//end if

            //your pay pem file
            if($_FILES['frmpemyourfile']['name']!='') {
                $frmpemyourfile=$_FILES['frmpemyourfile']['name'];
            }//end if
            else {
                $frmpemyourfile=$frmpemyourfile2;
            }


            //uploadfile validation start
            if($frmpemyourfile!="") {
                $filename	=	$_FILES['frmpemyourfile']['name'];
                $blacklist  = array("php", "phtml", "php3", "php4", "js", "shtml", "pl" ,"py", "exe");
                foreach ($blacklist as $file) {
                    if(preg_match("/\.$file\$/i", "$filename")) {
                        $message2="Please upload a pem file";
                    }//end if
                }//end foreach
            }
            //uploadfile validation end

            if($message2=="") {
                //uploading pem file
                if($frmpemyourfile!="") {
                    @chmod('../pem/',0777);
                    $up_path='../pem/'.$frmpemyourfile;
                    @move_uploaded_file($_FILES['frmpemyourfile']['tmp_name'], $up_path);
                }//end if

                //updation for yourpaysupport
                mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='YES' where nLookUpCode='yourpaysupport'") or die(mysqli_error($conn));

                //updation for yourpaymode
                mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($chkYourpayMode)."' where nLookUpCode='yourpaymode'") or die(mysqli_error($conn));

                //updation for yourpay store id
                mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtYourpayStoreId)."' where nLookUpCode='yourpaystoreid'") or die(mysqli_error($conn));

                //updation for yourpay pem file
                mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($frmpemyourfile)."' where nLookUpCode='yourpaypemfile'") or die(mysqli_error($conn));

            }//end if
        }//end if
        else {
            //updation for paypalsupport
            mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='NO' where nLookUpCode='yourpaysupport'") or die(mysqli_error($conn));
        }
    }//end payment method if

    //GOOGLE CEHCOUT SETTINGS
    if($chkGoogleCheckoutSupport == "on") {
        $chkGoogleCheckoutSupport = "YES";
    }//end if
    else {
        $chkGoogleCheckoutSupport = "NO";
    }

    if($chkGoogleCheckoutMode == "on") {
        $chkGoogleCheckoutMode = "LIVE";
    }//end if
    else {
        $chkGoogleCheckoutMode = "TEST";
    }

    if($chkGoogleCheckoutSupport=="YES") {  //need paypal support
        $message2="";

        if ( !isNotNull($txtGoogleCheckoutMerchantId)) {
            $message2 .= "* Google Checkout Merchant Id is empty<br>";
        }//end if
        if ( !isNotNull($txtGoogleCheckoutMerchantKey)) {
            $message2 .= "* Google Checkout Merchant Key is empty<br>";
        }//end if

        if($message2=="") {
            //updation for googlesupport
            mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='YES' where nLookUpCode='googlesupport'") or die(mysqli_error($conn));

            //updation for googlemode
            mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($chkGoogleCheckoutMode)."' where nLookUpCode='googlemode'") or die(mysqli_error($conn));

            //updation for googleid
            mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtGoogleCheckoutMerchantId)."' where nLookUpCode='googleid'") or die(mysqli_error($conn));

            //updation for googlekey
            mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($txtGoogleCheckoutMerchantKey)."' where nLookUpCode='googlekey'") or die(mysqli_error($conn));
        }//end if
    }//end if
    else {
        //updation for googlesupport
        mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='NO' where nLookUpCode='googlesupport'") or die(mysqli_error($conn));
    }

    if($ddlPaymentMethod=='WP') {
        //WORLD PAY SETTINGS
        //worldpay enable
        if ($chkWorldPay=='on') {
            $chkWorldPay = "Y";
        }//end if
        else {
            $chkWorldPay = "N";
        }

        //for worldpay demo
        if ($chkWorldPayDemo=='on') {
            $chkWorldPayDemo = "NO";
        }//end if
        else {
            $chkWorldPayDemo = "YES";
        }

        if($chkWorldPay=="Y") {  //need paypal support
            $message2="";

            if ( !isNotNull($txtWorldpayInstId)) {
                $message2 .= "* Worldpay Installation Id is empty<br>";
            }//end if
            if ( !isNotNull($txtWorldpayEmail)) {
                $message2 .= "* Worldpay Email Address is empty<br>";
            }//end if

            if($message2=="") {
                //update worldpay
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($chkWorldPay)."'","where nLookUpCode = 'enableworldpay'");

                //update worldpaydemo
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($chkWorldPayDemo)."'","where nLookUpCode = 'worldpaydemo'");

                //update worldpayid
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($txtWorldpayInstId)."'","where nLookUpCode = 'worldpayid'");

                //update worldpayemail
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($txtWorldpayEmail)."'","where nLookUpCode = 'worldpayemail'");

                //update worldpaytransmode
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($radWTransactMethod)."'","where nLookUpCode = 'worldpaytransmode'");
            }//end if
        }//end if
        else {
            //updation for wordlpaysupport
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enableworldpay'");
        }
    }//end payment method if

    if($ddlPaymentMethod=='BP') {
        //blue PAY SETTINGS
        //bluepay enable
        if ($chkBluePay=='on') {
            $chkBluePay = "Y";
        }//end if
        else {
            $chkBluePay = "N";
        }

        //for bluepay demo
        if ($chkBluePayDemo=='on') {
            $chkBluePayDemo = "NO";
        }//end if
        else {
            $chkBluePayDemo = "YES";
        }

        if($chkBluePay=="Y") {  //need paypal support
            $message2="";

            if ( !isNotNull($txtBluePayAccId)) {
                $message2 .= "* Bluepay Account Id is empty<br>";
            }//end if
            if ( !isNotNull($txtBluePayKey)) {
                $message2 .= "* Bluepay Secret Key is empty<br>";
            }//end if

            if($message2=="") {
                //update bluepay
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($chkBluePay)."'","where nLookUpCode = 'enablebluepay'");

                //update bluepaydemo
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($chkBluePayDemo)."'","where nLookUpCode = 'bluepaydemo'");

                //update bluepayid
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($txtBluePayAccId)."'","where nLookUpCode = 'bluepayid'");

                //update bluepaykey
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($txtBluePayKey)."'","where nLookUpCode = 'bluepaykey'");
            }//end if
        }//end if
        else {
            //updation for wordlpaysupport
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablebluepay'");
        }
    }//end payment method if

    if($ddlPaymentMethod=='SP') {
        //blue PAY SETTINGS
        //bluepay enable
        if ($chkStripe=='on') {
            $chkStripe = "Y";
        }//end if
        else {
            $chkStripe = "N";
        }

        //for bluepay demo
        if ($chkStripeDemo=='on') {
            $chkStripeDemo = "NO";
        }//end if
        else {
            $chkStripeDemo = "YES";
        }

        if($chkStripe=="Y") {  //need stripe support
            $message2="";
            $checkpublicId="";
            $checksecretId="";
            $appendmsg="";
            
            if($chkStripeDemo == "YES"){
                $checkpublicId = $txtStripePublicId;
                $checksecretId = $txtStripeKey;
                $appendmsg ="[Sandbox]";
            } else {
                $checkpublicId = $txtStripePublicIdLive;
                $checksecretId = $txtStripeKeyLive;
                $appendmsg ="[Live]";
                
            }
        
            if ( !isNotNull($checkpublicId)) {
                $message2 .= "* Stripe Public  Id".$appendmsg." is empty<br>";
            }//end if
            if ( !isNotNull($checksecretId)) {
                $message2 .= "* Stripe Secret Key".$appendmsg." is empty<br>";
            }//end if
        

            if($message2=="") {
                //update stripe demo
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($chkStripe)."'","where nLookUpCode = 'enablestripe'");

                //update stripe demo
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($chkStripeDemo)."'","where nLookUpCode = 'stripedemo'");

                //update stripe demo
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($txtStripePublicId)."'","where nLookUpCode = 'stripepublic'");

                //update stripe demo
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($txtStripeKey)."'","where nLookUpCode = 'stripekey'");
                //update stripe demo
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($txtStripePublicIdLive)."'","where nLookUpCode = 'stripepubliclive'");

                //update stripe demo
                update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='".addslashes($txtStripeKeyLive)."'","where nLookUpCode = 'stripekeylive'");
            }//end if
        } 
        else {
            //updation for wordlpaysupport
            update_rows(TABLEPREFIX.'lookup',"vLookUpDesc='N'","where nLookUpCode = 'enablestripe'");
        }
    } // end payment method if

    //OTHER PAYMENT SETTINGS
    if($chkOtherSupport == "on") {
        $chkOtherSupport = "YES";
    }//end if
    else {
        $chkOtherSupport = "NO";
    }

    //updation for googlesupport
    mysqli_query($conn, "Update ".TABLEPREFIX."lookup set vLookUpDesc='".addslashes($chkOtherSupport)."' where
					nLookUpCode='otherpayment'") or die(mysqli_error($conn));

    $flag = true;
    $message_succ="Settings updated.";
    $chkEscrow=DisplayLookUp('Enable Escrow');
}//end if
else {
    $txtPaypalEmail=DisplayLookUp('paypalemail');
    $txtPaypalIDTOKEN=DisplayLookUp('paypalauthtoken');
    $chkPayPalMode=DisplayLookUp('paypalmode');
    $chkPayPalSupport=DisplayLookUp('paypalsupport');
    $chkAuthSupport=DisplayLookUp('authsupport');
    $txtAuthorizeLoginId=DisplayLookUp('authloginid');
    $txtAuthorizePassword='';//DisplayLookUp('authloginpass');
    $txtAuthorizeTransKey=DisplayLookUp('authlogintranskey');
    $txtAuthorizeEmail=DisplayLookUp('authemail');
    $chkAuthorizeMode=DisplayLookUp('authmode');
    $chkEscrow=DisplayLookUp('Enable Escrow');
    $chkYourpaySupport=DisplayLookUp('yourpaysupport');
    $txtYourpayStoreId=DisplayLookUp('yourpaystoreid');
    $chkYourpayMode=DisplayLookUp('yourpaymode');
    $frmpemyourfile=DisplayLookUp('yourpaypemfile');
    $chkGoogleCheckoutSupport=DisplayLookUp('googlesupport');
    $chkGoogleCheckoutMode=DisplayLookUp('googlemode');
    $txtGoogleCheckoutMerchantId=DisplayLookUp('googleid');
    $txtGoogleCheckoutMerchantKey=DisplayLookUp('googlekey');
    $chkOtherSupport=DisplayLookUp('otherpayment');
    //worldpay
    $chkWorldPayDemo=DisplayLookUp('worldpaydemo');
    $txtWorldpayInstId=DisplayLookUp('worldpayid');
    $txtWorldpayEmail=DisplayLookUp('worldpayemail');
    $radWTransactMethod=DisplayLookUp('worldpaytransmode');
    $chkWorldPay=DisplayLookUp('enableworldpay');
    $ddlPaymentMethod=DisplayLookUp('PaymentMethod');
    $txtBluePayAccId=DisplayLookUp('bluepayid');
    $txtStripePublicId=DisplayLookUp('stripepublic');
    $txtStripePublicIdLive=DisplayLookUp('stripepubliclive');
    $txtBluePayKey=DisplayLookUp('bluepaykey');
    $txtStripeKey=DisplayLookUp('stripekey');
    $txtStripeKeyLive=DisplayLookUp('stripekeylive');
    $chkBluePay=DisplayLookUp('enablebluepay');
    $chkStripe=DisplayLookUp('enablestripe');

    $chkBluePayDemo=DisplayLookUp('bluepaydemo');
    $chkStripeDemo=DisplayLookUp('stripedemo');

}//end if            
?>
<script language="javascript" type="text/javascript">
    function validateSettingsForm()
    {
        var frm = window.document.frmSettings;

        if (frm.paypalsupport.checked==true)
        {
            if (!checkMail(frm.paypalemail.value))
            {
                frm.paypalemail.value = 'paypal@yoursite.com';
                frm.paypalemail.focus();
                alert('Please enter a valid paypal email address');
                return false;
            }//end if
            if (trim(frm.paypalauthtoken.value) == "")
            {
                frm.paypalauthtoken.focus();
                alert('Please enter authorization token of paypal');
                return false;
            }//end if

        }
        if(frm.ddlPaymentMethod.value=='CC')
        {
            if (frm.chkAuthSupport.checked==true)
            {
                if(trim(frm.chkCurrencyCode.value)!='USD')
                {
                    alert('Authorize.net will support only US Dollar. Please change currency to support authorize.net');
                    return false;
                }
                if (trim(frm.txtAuthLoginId.value) == "")
                {
                    frm.txtAuthLoginId.focus();
                    alert('Please enter login id of AuthorizeNet');
                    return false;
                }//end if
                if (trim(frm.txtAuthTransKey.value) == "")
                {
                    frm.txtAuthTransKey.focus();
                    alert('Please enter Transaction Key of AuthorizeNet');
                    return false;
                }//end if
                /*if (trim(frm.txtAuthPassword.value) == "")
                                {
                                        frm.txtAuthPassword.focus();
                                        alert('Please enter Password of AuthorizeNet');
                                        return false;
                                }//end if*/
                if (!checkMail(frm.txtAuthEmail.value))
                {
                    frm.txtAuthEmail.value = 'email@yoursite.com';
                    frm.txtAuthEmail.focus();
                    alert('Please enter a valid AuthorizeNet email address');
                    return false;
                }//end if
            }
        }//end method if

        if(frm.ddlPaymentMethod.value=='YP')
        {
            if (frm.chkYourpaySupport.checked==true)
            {
                if (trim(frm.txtYourpayStoreId.value) == "")
                {
                    frm.txtYourpayStoreId.focus();
                    alert('Please enter First Data login Id');
                    return false;
                }//end if
                if(trim(frm.frmpemyourfile2.value)=='')
                {
                    if (trim(frm.frmpemyourfile.value) == "")
                    {
                        frm.frmpemyourfile.focus();
                        alert('Please upload First Data pem file');
                        return false;
                    }//end if
                }//end if
            }
        }//end method if
        if (frm.chkGoogleCheckoutSupport.checked==true)
        {
            if (trim(frm.txtGoogleCheckoutMerchantId.value) == "")
            {
                alert('Please enter Google Checkout Merchant Id');
                frm.txtGoogleCheckoutMerchantId.focus();
                return false;
            }//end if
            if (trim(frm.txtGoogleCheckoutMerchantKey.value) == "")
            {
                alert('Please enter Google Checkout Merchant Key');
                frm.txtGoogleCheckoutMerchantKey.focus();
                return false;
            }//end if
        }

        if(frm.ddlPaymentMethod.value=='WP')
        {
            if (frm.chkWorldPay.checked==true)
            {
                if (trim(frm.txtWorldpayInstId.value) == "")
                {
                    alert('Please enter Worldpay Installation Id');
                    frm.txtWorldpayInstId.focus();
                    return false;
                }//end if
                if (trim(frm.txtWorldpayEmail.value) == "")
                {
                    alert('Please enter Worldpay Email Address');
                    frm.txtWorldpayEmail.focus();
                    return false;
                }//end if
            }
        }//end method if
        if(frm.ddlPaymentMethod.value=='BP')
        {
            if (frm.chkBluePay.checked==true)
            {
                if (trim(frm.txtBluePayAccId.value) == "")
                {
                    alert('Please enter BluePay Account Id');
                    frm.txtBluePayAccId.focus();
                    return false;
                }//end if
                if (trim(frm.txtBluePayKey.value) == "")
                {
                    alert('Please enter BluePay Secret Key');
                    frm.txtBluePayKey.focus();
                    return false;
                }//end if
            }
        }//end method if
        return true;
    }

    //Payment method
    function displayDetails(val)
    {
        var frm = document.frmSettings;
        for(i=1;i<frm.ddlPaymentMethod.options.length;i++)
        {
            if(frm.ddlPaymentMethod.options[i].value == val)
            {
               
                document.getElementById(frm.ddlPaymentMethod.options[i].value).style.display='';
            }//end if
            else
            {
               // alert("here2"+val);
                document.getElementById(frm.ddlPaymentMethod.options[i].value).style.display='none';
            }
        }//end for loop
    }//end function
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
                    <td width="100%" class="heading_admn" align="left"><span class="boldtextblack">Payment Configuration Details</span></td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2">
                            <tr>
                                <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frmSettings" method ="POST" enctype="multipart/form-data" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateSettingsForm();">
                                            <input type="hidden" name="chkCurrencyCode" value="<?php echo PAYMENT_CURRENCY_CODE;?>">
<?php if(isset($message_succ) && $message_succ!='') {
    ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="3" align="center" class="success"><?php echo $message_succ;?></td>
                                            </tr>
    <?php  }//end if?>
<?php if(isset($message2) && $message2!='') {
    ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="3" align="center" class="warning"><?php echo $message2;?></td>
                                            </tr>
    <?php  }//end if?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td colspan="2" align="right"><input type="submit" name="btnSubmit" value="Change Settings" class="submit"></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="3" align="left" class="subheader">PayPal Settings</td>
                                            </tr>
<?php
$disabled="";
$disablemessage=" [ Uncheck to disable paypal support] ";
if($chkPayPalSupport!="YES") {
    $disabled="disabled";
    $disablemessage="<span class='warning'>Paypal support disabled</span>&nbsp;&nbsp;[ Check to enable Paypal support ]";
}//end if
?>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="32%">Start A New Account </td>
                                                <td width="43%"><a href="https://www.paypal.com/us/mrb/pal=NCGEZ2VF6YL62" target="_blank"><img src="../images/paypal_small.jpg" width="77" height="30" border="0" alt="Start A New Account" title="Start A New Account"></a></td>
                                                <td width="25%" align="left" valign="middle"><i title="To start a new paypal account click on the paypal logo." id="help1"><strong>?</strong></i></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td>Enable Paypal?</td>
                                                <td><input type="checkbox" name="paypalsupport" <?php if($chkPayPalSupport=="YES") echo "checked";?>>
<?php echo $disablemessage;?>
                                                </td>
                                                <td align="left" valign="middle"><i title="Click to enable/disable paypal payment on your site." id="help2"><strong>?</strong></i></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td>Paypal Email</td>
                                                <td><input type="text" class="textbox2" name="paypalemail"   value="<?php echo htmlentities($txtPaypalEmail);?>" size="50"></td>
                                                <td align="left" valign="middle"><i title="Provide your paypal email id." id="help3"><strong>?</strong></i></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td>Identity Token</td>
                                                <td><input type="text" class="textbox2" name="paypalauthtoken" value="<?php echo htmlentities($txtPaypalIDTOKEN);?>" size="50"></td>
                                                <td align="left" valign="middle"><i title="Provide your payal identity token generated from paypal.com." id="help4"><strong>?</strong></i></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td>Make Paypal Module live?</td>
                                                <td><input type="checkbox" name="paypalmode" <?php if($chkPayPalMode !="TEST") echo "checked";?>>&nbsp;
<?php if($chkPayPalMode !="TEST") echo "[ Uncheck  to make Paypal module in test mode ]"; else echo "[ Check to make Paypal module live ]" ?>
                                                </td>
                                                <td align="left" valign="middle"><i title="Switch between live and test mode." id="help5"><strong>?</strong></i></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="3" align="left" class="warning">* Note :While enabling PayPal, you need to set the return page in PayPal merchant Panel. You should also enable auto return and payment data transfer to generate identity token.
                                                    Given below are the details of doing it.<br /><br />

                                                    1. Click the Profile tab.<br />
                                                    2. Click the Website Payment Preferences link under Selling Preferences.<br />
                                                    3. Click the On radio button to enable Auto Return.<br />
                                                    4. Enter the Return URL.<br />
                                                    The return url is<br />
                                                    Success: <?php echo SECURE_SITE_URL;?>/success.php<br />
	  5. Click on the radio button to enable Payment Data Transfer<br />
	  6 Click save.<br />
	  7. Your identity token would be displayed just below Payment Data Transfer button</td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="3" style="color: #0A6500;">
                                                    Note:- 1) All Payments except paypal will work if the escrow payments is enabled by the admin.<br>
                                                    2) For authorize.net, currency used in the website should be US Dollars.
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" class="subheader">Payment Gateway</td>
                                                <td colspan="2" align="left" class="subheader"><span class="main_txt"><select name="ddlPaymentMethod" class="textbox2" onChange="javascript:displayDetails(this.value);">
<option value="NN">None</option>                                              
  <?php
if($chkEscrow=='Yes') {
    ?>                                                        
<?php
if(PAYMENT_CURRENCY_CODE=='USD') {
    ?>
                                                            <option value="CC" <?php if ($ddlPaymentMethod == "CC") {
        echo 'selected';
    } ?>>Authorize.net</option>
    <?php
                                            }//end if
                                            ?>

                                                            <option value="YP" <?php if ($ddlPaymentMethod == "YP") {
                                                    echo 'selected';
                                                } ?>>First Data</option>
                                                            <option value="WP" <?php if ($ddlPaymentMethod == "WP") {
                                                    echo 'selected';
    } ?>>WorldPay</option>
<!--                                                            <option value="BP" <?php if ($ddlPaymentMethod == "BP") {
        echo 'selected';
                                                } ?>>BluePay</option>-->
                                                 <option value="SP" <?php if ($ddlPaymentMethod == "SP") {
        echo 'selected';
                                                } ?>>Stripe</option>
    <?php
}//end if
?>
                                                        </select></span></td>
                                            </tr>
<?php
if(PAYMENT_CURRENCY_CODE=='USD' && $chkEscrow=='Yes') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" class="subheader" colspan="3">

                                                    <div id="CC" <?php if ($ddlPaymentMethod != "CC") {
                                                    echo "style=display:none";
                                                } ?>>
                                                        <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2" bgcolor="#EEEEEE">
                                                            <tr bgcolor="#FFFFFF">
                                                                <td colspan="3" align="left" class="subheader">Authorize.net Settings</td>
                                                            </tr>
    <?php
    $disabled="";
    $disablemessage=" [ Uncheck to disable authorize.net support] ";
    if($chkAuthSupport !="YES") {
        $disabled="disabled";
        $disablemessage="<span class='warning'>Authorize.net support disabled</span>&nbsp;&nbsp;[ Check to enable authorize.net support ]";
                                                        }//end if
    ?>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td width="32%" height="49">Start A New Account </td>
                                                                <td width="43%"><a href="http://www.authorize.net/" target="_blank"><img src="../images/authorize_small.jpg" width="49" height="36" border="0" alt="Start A New Account" title="Start A New Account"></a></td>
                                                                <td width="25%" align="left" valign="middle"><i title="To start a new authorize.net account click on the authorize.net logo." id="help6"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td width="32%">Enable Authorize.net?</td>
                                                                <td width="43%"><input type="checkbox" name="chkAuthSupport" <?php if($chkAuthSupport=="YES") echo "checked"; ?> value="on">
    <?php echo $disablemessage;?>
                                                                </td>
                                                                <td width="25%" align="left" valign="middle"><i title="Click to enable/disable authorize.net payment on your site." id="help7"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td height="32">Authorize Login Id </td>
                                                                <td><input type="text" name="txtAuthLoginId" class="textbox2" value="<?php echo htmlentities($txtAuthorizeLoginId);?>" size="50"></td>
                                                                <td align="left" valign="middle"><i title="Provide your authorize.net login id." id="help8"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td>Authorize Transaction Key </td>
                                                                <td><input type="text" name="txtAuthTransKey" class="textbox2" value="<?php echo htmlentities($txtAuthorizeTransKey);?>" size="50"></td>
                                                                <td align="left" valign="middle"><i title="Provide your authorize transaction key generated from authorize.net." id="help9"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td>Authorize Email</td>
                                                                <td><input type="text" name="txtAuthEmail" class="textbox2" value="<?php echo htmlentities($txtAuthorizeEmail);?>" size="50"></td>
                                                                <td align="left" valign="middle"><i title="Provide your authorize email id." id="help11"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td>Make Authorize.net Module live?</td>
                                                                <td><input type="checkbox" name="txtAuthMode" class=checkbox <?php if($chkAuthorizeMode !="TEST") echo "checked"; ?>>
                                                        <?php if($chkAuthorizeMode =="TEST") echo "[ Check to make Authorize.net module live]"; else echo "[ Uncheck to make Authorize.net module in test mode]";?>
                                                                </td>
                                                                <td align="left" valign="middle"><i title="Switch between live and test mode." id="help12"><strong>?</strong></i></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td></tr>
    <?php
}//end if
?>				
                                            <tr bgcolor="#FFFFFF" id="showAuth" style="<?php if($chkEscrow=='No') {
    echo 'display:none;';
}?>">
                                                <td colspan="3" align="left">
                                                    <div id="YP" <?php if ($ddlPaymentMethod != "YP") {
                                                                echo "style=display:none";
                                                            } ?>>
                                                        <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2" bgcolor="#EEEEEE">
                                                            <?php
                                                            $disabled="";
                                                            $disablemessage=" [ Uncheck to disable First Data support] ";
                                                            if($chkYourpaySupport!="YES") {
                                                                $disabled="disabled";
                                                                $disablemessage="<span class='warning'>First Data support disabled</span>&nbsp;&nbsp;[ Check to enable First Data support ]";
                                                            }//end if
?>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td colspan="3" align="left" class="subheader">First Data Settings</td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td width="32%">Start A New Account </td>
                                                                <td width="45%"><a href="https://registration.altpayfirstdata.com/" target="_blank"><img src="../images/yourpay_small.jpg" width="100" border="0" alt="Start A New Account" title="Start A New Account"></a></td>
                                                                <td width="23%" align="left" valign="middle"><i title="To start a new First Data account click on the First Data logo." id="help13"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Enable First Data?</td>
                                                                <td><input type="checkbox" name="chkYourpaySupport" <?php if($chkYourpaySupport=="YES") echo "checked";?>>
<?php echo $disablemessage;?></td>
                                                                <td align="left" valign="middle"><i title="Click to enable/disable First Data payment on your site." id="help14"><strong>?</strong></i> (Make sure that the port no. 1129 is open in the hosting server for First Data to work)</td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">First Data Store Id</td>
                                                                <td><input type="text" class="textbox2" value="<?php echo htmlentities($txtYourpayStoreId);?>" name="txtYourpayStoreId" size="50" maxlength="100"></td>
                                                                <td align="left" valign="middle"><i title="Provide your First Data store id." id="help15"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Make First Data Module live?</td>
                                                                <td><input type="checkbox" name="chkYourpayMode" <?php if($chkYourpayMode!="TEST") echo "checked";?>>&nbsp;
                                                            <?php if($chkYourpayMode!="TEST") echo "[ Uncheck  to make First Data module in test mode ]"; else echo "[ Check to make First Data module live ]" ?></td>
                                                                <td align="left" valign="middle"><i title="Switch between live and test mode." id="help17"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Pem File</td>
                                                                <td><input type="file" class="textbox2" name="frmpemyourfile">
                                                                    <input type="hidden" value="<?php echo $frmpemyourfile;?>" name="frmpemyourfile2"></td>
                                                                <td align="left" valign="middle"><i title="Provide your First Data pem file generated from First Data" id="help16"><strong>?</strong></i></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div id="WP" <?php if ($ddlPaymentMethod != "WP") {
    echo "style=display:none";
} ?>>				
                                                        <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2" bgcolor="#EEEEEE">
<?php
$disabled="";
$disablemessage=" [ Uncheck to disable WorldPay support] ";
if($chkWorldPay!="Y") {
    $disabled="disabled";
    $disablemessage="<span class='warning'>WorldPay support disabled</span>&nbsp;&nbsp;[ Check to enable WorldPay support ]";
}//end if
?>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td colspan="3" align="left" class="subheader">WorldPay Settings</td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td width="32%" align="left">Start A New Account</td>
                                                                <td width="65%"><a href="http://www.worldpay.com/" target="_blank"><img src="../images/worldpay_small.jpg" width="78" height="20" border="0" alt="Get A New WorldPay Account" title="Get A New WorldPay Account"></a></td>
                                                                <td width="3%" align="left" valign="middle"><i title="To start a new worldpay account click on the world pay logo." id="help24"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Enable WorldPay?</td>
                                                                <td><input type="checkbox" name="chkWorldPay" <?php if($chkWorldPay=="Y") echo "checked";?>>
<?php echo $disablemessage;?></td>
                                                                <td align="left" valign="middle"><i title="Click to enable/disable WorldPay payment on your site." id="help25"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Worldpay Installation Id</td>
                                                                <td><input name="txtWorldpayInstId" type="text" value="<?php echo $txtWorldpayInstId;?>" size="50" maxlength="100" class="textbox2"></td>
                                                                <td align="left" valign="middle"><i title="Provide your worldpay installation id." id="help26"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Worldpay Email Address</td>
                                                                <td><input name="txtWorldpayEmail" type="text" value="<?php echo $txtWorldpayEmail;?>" size="50" maxlength="100" class="textbox2"></td>
                                                                <td align="left" valign="middle"><i title="Provide your worldpay email address." id="help27"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Transaction Method</td>
                                                                <td><input name="radWTransactMethod" type="radio" value="A" <?php if($radWTransactMethod=="A") {
                                                                echo "checked";
                                                            }?>>
												 AUTHORISED 
                                                                    <input name="radWTransactMethod" type="radio" value="C" <?php if($radWTransactMethod=="C") {
                                                                echo "checked";
                                                            } ?>>CAPTURED</td>
                                                                <td align="left" valign="middle"><i title="Provide your worldpay transaction method." id="help28"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Make WorldPay Module live?</td>
                                                                <td><input type="checkbox" name="chkWorldPayDemo" <?php if($chkWorldPayDemo!="YES") echo "checked";?>>&nbsp;
<?php if($chkWorldPayDemo!="YES" && $chkEscrow=='Yes') echo "[ Uncheck  to make WorldPay module in test mode ]"; else echo "[ Check to make WorldPay module live ]" ?></td>
                                                                <td align="left" valign="middle"><i title="Switch between live and test mode." id="help29"><strong>?</strong></i></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div id="BP" <?php if ($ddlPaymentMethod != "BP") {
    echo "style=display:none";
} ?>>				
                                                        <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2" bgcolor="#EEEEEE">
                                                                    <?php
$disabled="";
$disablemessage=" [ Uncheck to disable BluePay support] ";
if($chkBluePay!="Y") {
    $disabled="disabled";
    $disablemessage="<span class='warning'>BluePay support disabled</span>&nbsp;&nbsp;[ Check to enable BluePay support ]";
}//end if
?>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td colspan="3" align="left" class="subheader">BluePay Settings</td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td width="32%" align="left">Start A New Account</td>
                                                                <td width="65%"><a href="https://onlineapp.bluepay.com/interfaces/create?hostid=17d6913481c995869f09844787c72ef5" target="_blank"><img src="../images/bluepay.jpg" width="148" height="45" border="0" alt="Get A New BluePay Account" title="Get A New BluePay Account"></a></td>
                                                                <td width="3%" align="left" valign="middle"><i title="To start a new bluepay account click on the world pay logo." id="help30"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Enable BluePay?</td>
                                                                <td><input type="checkbox" name="chkBluePay" <?php if($chkBluePay=="Y") echo "checked";?>>
<?php echo $disablemessage;?></td>
                                                                <td align="left" valign="middle"><i title="Click to enable/disable BluePay payment on your site." id="help31"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">BluePay Account Id</td>
                                                                <td><input name="txtBluePayAccId" type="text" value="<?php echo $txtBluePayAccId;?>" size="50" maxlength="100" class="textbox2"></td>
                                                                <td align="left" valign="middle"><i title="Provide your bluepay account id." id="help32"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">BluePay Secret Key </td>
                                                                <td><input name="txtBluePayKey" type="text" value="<?php echo $txtBluePayKey;?>" size="50" maxlength="100" class="textbox2"></td>
                                                                <td align="left" valign="middle"><i title="Provide your bluepay secret key." id="help33"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Make BluePay Module live?</td>
                                                                <td><input type="checkbox" name="chkBluePayDemo" <?php if($chkBluePayDemo!="YES") echo "checked";?>>&nbsp;
<?php if($chkBluePayDemo!="YES") echo "[ Uncheck  to make BluePay module in test mode ]"; else echo "[ Check to make BluePay module live ]" ?></td>
                                                                <td align="left" valign="middle"><i title="Switch between live and test mode." id="help34"><strong>?</strong></i></td>
                                                            </tr>
                                                        </table>
                                                    </div>


                                                    <div id="SP" <?php if ($ddlPaymentMethod != "SP") {
    echo "style=display:none";
} ?>>				
                                                        <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2" bgcolor="#EEEEEE">
                                                                    <?php
$disabled="";
$disablemessage=" [ Uncheck to disable Stripe support] ";
if($chkStripe!="Y") {
    $disabled="disabled";
    $disablemessage="<span class='warning'>Strie support disabled</span>&nbsp;&nbsp;[ Check to enable Stripe support ]";
}//end if
?>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td colspan="3" align="left" class="subheader">Stripe Settings</td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td width="32%" align="left">Start A New Account</td>
                                                                <td width="65%"><a href="https://onlineapp.bluepay.com/interfaces/create?hostid=17d6913481c995869f09844787c72ef5" target="_blank"><img src="../images/stripe.png" width="148" height="45" border="0" alt="Get A New BluePay Account" title="Get A New BluePay Account"></a></td>
                                                                <td width="3%" align="left" valign="middle"><i title="To start a new bluepay account click on the world pay logo." id="help30"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Enable Stripe?</td>
                                                                <td><input type="checkbox" name="chkStripe" <?php if($chkStripe=="Y") echo "checked";?>>
<?php echo $disablemessage;?></td>
                                                                <td align="left" valign="middle"><i title="Click to enable/disable BluePay payment on your site." id="help31"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Stripe Public Key[Sandbox]</td>
                                                                <td><input name="txtStripePublicId" type="text" value="<?php echo $txtStripePublicId;?>" size="50"  class="textbox2"></td>
                                                                <td align="left" valign="middle"><i title="Provide your strip public key." id="help32"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left"> Stripe Secret Token[Sandbox] </td>
                                                                <td><input name="txtStripeKey" type="text" value="<?php echo $txtStripeKey;?>" size="50"  class="textbox2"></td>
                                                                <td align="left" valign="middle"><i title="Provide your stripe secret key." id="help33"><strong>?</strong></i></td>
                                                            </tr>
                                                            <!-- Live Start -->
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Stripe Public Key[Live]</td>
                                                                <td><input name="txtStripePublicIdLive" type="text" value="<?php echo $txtStripePublicIdLive;?>" size="50"  class="textbox2"></td>
                                                                <td align="left" valign="middle"><i title="Provide your strip public key." id="help32"><strong>?</strong></i></td>
                                                            </tr>
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left"> Stripe Secret Token </td>
                                                                <td><input name="txtStripeKeyLive" type="text" value="<?php echo $txtStripeKeyLive;?>" size="50"  class="textbox2"></td>
                                                                <td align="left" valign="middle"><i title="Provide your stripe secret key." id="help33"><strong>?</strong></i></td>
                                                            </tr>
                                                            <!-- Live end -->
                                                            <tr bgcolor="#FFFFFF">
                                                                <td align="left">Make Stripe Module live?</td>
                                                                <td><input type="checkbox" name="chkStripeDemo" <?php if($chkStripeDemo!="YES") echo "checked";?>>&nbsp;
<?php if($chkStripeDemo!="YES") echo "[ Uncheck  to make Stripe module in test mode ]"; else echo "[ Check to make Stripe module live ]" ?></td>
                                                                <td align="left" valign="middle"><i title="Switch between live and test mode." id="help34"><strong>?</strong></i></td>
                                                            </tr>
                                                        </table>
                                                    </div>



                                                    <!--<table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2" bgcolor="#EEEEEE">
<?php
/*$disabled="";
$disablemessage=" [ Uncheck to disable Google Checkout support] ";
                                                                    if($chkGoogleCheckoutSupport!="YES") {
    $disabled="disabled";
    $disablemessage="<span class='warning'>Google Checkout support disabled</span>&nbsp;&nbsp;[ Check to enable Google Checkout support ]";
}//end if
 * 
 */
?>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td colspan="3" align="left" class="subheader">Google Checkout Settings</td>
                                                        </tr>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td width="32%" align="left">Start A New Account</td>
                                                            <td width="65%"><a href="http://checkout.google.com/sell?promo=searmiasystemsinc" target="_blank"><img src="../images/google_small.gif" width="114" height="20" border="0" alt="Start A New Account" title="Start A New Account"></a></td>
                                                            <td width="3%" align="left" valign="middle"><i title="To start a new google checkout account click on the google checkout logo." id="help18"><strong>?</strong></i></td>
                                                        </tr>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td align="left">Enable Google Checkout?</td>
                                                            <td><input type="checkbox" name="chkGoogleCheckoutSupport" <?php if($chkGoogleCheckoutSupport=="YES") echo "checked";?>>
                                                                    <?php echo $disablemessage;?></td>
                                                            <td align="left" valign="middle"><i title="Click to enable/disable google checkout payment on your site." id="help19"><strong>?</strong></i></td>
                                                        </tr>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td align="left">Google Checkout Merchant ID</td>
                                                            <td><input type="text" class="textbox2" value="<?php echo htmlentities($txtGoogleCheckoutMerchantId);?>" name="txtGoogleCheckoutMerchantId" size="50" maxlength="100"></td>
                                                            <td align="left" valign="middle"><i title="Provide your google checkout merchant id." id="help20"><strong>?</strong></i></td>
                                                        </tr>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td align="left">Google Checkout Merchant Key</td>
                                                            <td><input type="text" class="textbox2" value="<?php echo htmlentities($txtGoogleCheckoutMerchantKey);?>" name="txtGoogleCheckoutMerchantKey" size="50" maxlength="100"></td>
                                                            <td align="left" valign="middle"><i title="Provide your google checkout merchant key generated from google checkout.com." id="help21"><strong>?</strong></i></td>
                                                        </tr>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td align="left">Make Google Checkout Module live?</td>
                                                            <td><input type="checkbox" name="chkGoogleCheckoutMode" <?php if($chkGoogleCheckoutMode!="TEST") echo "checked";?>>&nbsp;
                                                            <?php if($chkGoogleCheckoutMode!="TEST") echo "[ Uncheck  to make Google Checkout module in test mode ]"; else echo "[ Check to make Google Checkout module live ]" ?></td>
                                                            <td align="left" valign="middle"><i title="Switch between live and test mode." id="help22"><strong>?</strong></i></td>
                                                        </tr>
                                                        <tr bgcolor="#FFFFFF">
                                                            <td colspan="3" align="left" class="maintext warning">* Note :Please add the url "<?php echo SECURE_SITE_URL.'/response.php';?>" to the "API callback URL" field in googlecheckout merchant panel. You can navigate to this in googlecheckout merchant panel screen as follows My Sales -> Settings -> Integration -> set API callback URL</td>
                                                        </tr>
                                                    </table>-->
                                                </td></tr>
<?php
$disabled="";
$disablemessage=" [ Uncheck to disable Other Payments support] ";
if($chkOtherSupport!="YES") {
    $disabled="disabled";
    $disablemessage="<span class='warning'>Other Payments support disabled</span>&nbsp;&nbsp;[ Check to enable Other Payments support ]";
}//end if
?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="3" align="left" class="subheader">Other Payments (Cashiers/Business/Personal Check/Money Order/Wire Transfer)</td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Enable Other Payments</td>
                                                <td><input type="checkbox" name="chkOtherSupport" <?php if($chkOtherSupport=="YES") echo "checked";?>>
<?php echo $disablemessage;?></td>
                                                <td align="left" valign="middle"><i title="Click to enable/disable other payments on your site." id="help123"><strong>?</strong></i></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td colspan="2"><input type="submit" name="btnSubmit" value="Change Settings" class="submit"></td>
                                            </tr>
                                        </form>
                                    </table>
                                </td>
                            </tr>
                        </table></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</div>
</div>
<?php include_once('../includes/footer_admin.php');?>