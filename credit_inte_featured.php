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
// I had a lot of trouble getting PHP & Curl to work with Authorize.net
// I don't want others to go through the same problems, so I am submitting this example script
// This script will work with Authorize.net's AIM method of processing.
// This code has been heavily borrowed from several sources.
// It requires a server that supports PHP and cURL.
// If you have any comments, please contact erik@grossmontdesigns.com
// From a previous HTML Form, pass the following fields:
// $FirstName = Customer's First Name
// $LastName = Customer's Last Name
// $CardNum = Customer's Credit Card Number
// $Month = Customer's Credit Card Expiration Month (Should be 01, 02, etc.)
// $Year = Customer's Credit Card Expiration Year (Should be 2003, 2004, etc.)
// $Address = Customer's Address
// $City = Customer's City
// $State = Customer's State (Should be 2 letter code, CA, AZ, etc.)
// $Zip = Customer's Zip Code
// $Email = Customer's Email Address
// $cost = Total Price of purchase
// Check to make sure customer entered all relevant information
if (!$FirstName || !$LastName || !$Address || !$City || !$State || !$Zip || !$CardNum || !
        $Email || !$CardCode || !$Country) {
    $cc_flag = false;
    $cc_err = ERROR_FORGOT_NECESSARY_INFORMATION;
}//end if
else {
    $txtAuthorizeLoginId = DisplayLookUp('authloginid');
    $txtAuthorizePassword = '';//DisplayLookUp('authloginpass');
    $txtAuthorizeTransKey = DisplayLookUp('authlogintranskey');
    $txtAuthorizeEmail = DisplayLookUp('authemail');
    $chkAuthorizeMode = DisplayLookUp('authmode');

    $x_Login = urlencode($txtAuthorizeLoginId); // Replace LOGIN with your login
    $x_Password = urlencode($txtAuthorizePassword); // Replace PASS with your password
    $x_tran_key = urlencode($txtAuthorizeTransKey); // Tran Key

    $x_Delim_Data = urlencode("TRUE");
    $x_Delim_Char = urlencode(",");
    $x_Encap_Char = urlencode("");
    $x_Type = urlencode("AUTH_CAPTURE");

    $x_ADC_Relay_Response = urlencode("FALSE");
    if ($chkAuthorizeMode != "LIVE") {
        $x_Test_Request = urlencode("TRUE");
    }//end if
    # Customer Information

    $x_Method = urlencode("CC");
    $x_Amount = urlencode($cost);
    $x_First_Name = urlencode($FirstName);
    $x_Last_Name = urlencode($LastName);
    $x_Card_Num = urlencode($CardNum);
    $ExpDate = ($Month . $Year);
    $x_Exp_Date = urlencode($ExpDate);
    $x_card_code = urlencode($CardCode);

    $x_Address = urlencode($Address);
    $x_City = urlencode($City);
    $x_State = urlencode($State);
    $x_Zip = urlencode($Zip);
    $x_country = urlencode($Country);

    $x_saleid = urlencode($saleid);
    $x_paytype = urlencode($paytype);
    //$x_dDate = urlencode($now);

    $x_Email = urlencode($Email);
    $x_Email_Customer = urlencode("TRUE");
    $x_Merchant_Email = urlencode($txtAuthorizeEmail);

    $x_cust_ip = urlencode($Cust_ip);
    $x_company = urlencode($Company);
    $x_phone = urlencode($Phone);
    $x_cust_id = urlencode($Cust_id);
    $x_invno = urlencode($Inv_id);
    $x_description = urlencode(SITE_NAME . "::".TEXT_PAYMENT_FOR_FEATURED_SALE."::$x_saleid");

    # Build fields string to post

    $fields = "x_Version=3.1&x_Login=$x_Login&x_tran_key=$x_tran_key&x_Delim_Data=$x_Delim_Data&x_Delim_Char=$x_Delim_Char&x_Encap_Char=$x_Encap_Char";
    $fields .= "&x_Type=$x_Type&x_Test_Request=$x_Test_Request&x_Method=$x_Method&x_Amount=$x_Amount&x_First_Name=$x_First_Name";
    $fields .= "&x_Last_Name=$x_Last_Name&x_Card_Num=$x_Card_Num&x_Exp_Date=$x_Exp_Date&x_card_code=$x_card_code&x_Address=$x_Address&x_City=$x_City&x_State=$x_State&x_Zip=$x_Zip&x_country=$x_country&x_Email=$x_Email&x_Email_Customer=$x_Email_Customer&x_Merchant_Email=$x_Merchant_Email&x_ADC_Relay_Response=$x_ADC_Relay_Response&x_invid=$x_invid&x_cust_ip=$x_cust_ip&x_company=$x_company&x_phone=$x_phone&x_cust_id=$x_cust_id&x_invoice_num=$x_invno&x_description=$x_description";
    if ($x_Password != '') {
        $fields .= "&x_Password=$x_Password";
    }//end if
    # Start CURL session

    $agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
    $ref = SECURE_SITE_URL . "/paycc.php"; // Replace this URL with the URL of this script

    $ch = curl_init();

    if ($chkAuthorizeMode != "LIVE") {
        $authurl = "https://test.authorize.net/gateway/transact.dll";
    }//end if
    else {
        $authurl = "https://secure.authorize.net/gateway/transact.dll";
    }//end else

    curl_setopt($ch, CURLOPT_URL, $authurl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_NOPROGRESS, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_REFERER, $ref);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $buffer = curl_exec($ch);
    curl_close($ch);
    $return = preg_split("/[,]+/", "$buffer");
    $details = $return[0];
    switch ($details) {
        case "1":
            $cc_flag = true;
            $cc_tran = $return[6];
            break;

        case "2":
            $cc_flag = false;
            $cc_err = ERROR_CARD_DECLINED;
            $cc_err .="<br>" . $return[3];
            break;

        case "4":
            $cc_flag = false;
            $cc_err = ERROR_CARD_HELD_REVIEW;
            $cc_err .="<br>" . $return[3];
            break;

        default:
            $cc_flag = false;
            $cc_err = ERROR;
            $cc_err .="<br>" . $return[3];
            break;
    }//end switch
}//end else
?>