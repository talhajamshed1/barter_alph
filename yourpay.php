<?php
if (!$FirstName || !$LastName || !$Address || !$City || !$State || !$Zip || !$CardNum || !$Email || !$CardCode || !$Country || !$Month || !$Year) {// || !$Phone
    $cc_flag = false;
    $cc_err = ERROR_FORGOT_NECESSARY_INFORMATION;
    // exit;
}//end if
else {
    $txtYourpayStoreId = DisplayLookUp('yourpaystoreid');
    $pemfolder = DisplayLookUp('yourpaypemfile');
    $txtUrPayDemo = DisplayLookUp('yourpaymode');
    $reccuringpayment = false;

    include_once "firstData.php";
    $mylphp = new firstData;
    
    $myorder["port"] = "1129";
    $myorder["keyfile"] = "./pem/" . $pemfolder; # Change this to the name and location of your certificate file
    $myorder["configfile"] = urlencode($txtYourpayStoreId);        # Change this to your store number
    //$x_currency_code = urlencode($txtACurrency); //currency code

    $myorder["ordertype"] = "SALE";
    if ($txtUrPayDemo == "TEST") {
        $myorder["host"] = "staging.linkpt.net";
        $myorder["result"] = "GOOD"; # For a test, set result to GOOD, DECLINE, or DUPLICATE
    }//end if
    else {
        $myorder["host"] = "secure.linkpt.net";
        $myorder["result"] = "LIVE";
    }//end else

    $myorder["cardnumber"] = urlencode($CardNum);
    $myorder["cardexpmonth"] = $Month;
    $myorder["cardexpyear"] = $Year;
    $myorder["cvmindicator"] = "provided";    // Indicates whether CVM was supplied and, if not, why. The possible values are â€œprovidedâ€?,â€œnot_providedâ€?,â€œillegibleâ€?,â€œnot_presentâ€?, and â€œno_imprintâ€?.
    $myorder["cvmvalue"] = urlencode($CardCode);       //  CVV2 3-digit numeric valued typically printed on the signature panel on the back of the credit card integer from 000 to 999.
    $myorder["chargetotal"] = urlencode($cost);

    if ($reccuringpayment == true and $reccuringmode == "SUBMIT") {
        //echo " <br>Yourpay submited::$recurringinstallment::$recurringstartdate:$recurringperiodicity";
        $myorder["action"] = "SUBMIT";
        $myorder["installments"] = $recurringinstallment;
        $myorder["threshold"] = "3";
        $myorder["startdate"] = $recurringstartdate;
        $myorder["periodicity"] = $recurringperiodicity;
    }//end if

    if ($reccuringpayment == true and $reccuringmode == "CANCEL") {
        //echo " <br>Yourpay cancel ::$recurringinstallment::$recurringstartdate:$recurringperiodicity::$recurringorderid";
        $myorder["action"] = "CANCEL";
        $myorder["installments"] = $recurringinstallment;
        $myorder["threshold"] = "3";
        $myorder["startdate"] = $recurringstartdate;
        $myorder["periodicity"] = $recurringperiodicity;
        $myorder["oid"] = $recurringorderid;
    }//end if
    # BILLING INFO 4111111111111111
    $myorder["name"] = urlencode($FirstName) . " " . urlencode($LastName);
    $myorder["company"] = "-NA-";
    $myorder["address1"] = urlencode($Address);
    $myorder["city"] = urlencode($City);
    $myorder["state"] = urlencode($State);
    $myorder["country"] = urlencode($Country);
    $myorder["phone"] = urlencode($Phone);
    $myorder["email"] = urlencode($Email); //CURRENCY
    $myorder["debugging"] = "false";  # for development only - not intended for production use
    $result = $mylphp->curl_process($myorder);  # use curl methods
    
    /* echo "<br>result::$result<br>";
      echo "<br>approved==".$result["r_approved"];
      echo "<br>ordernum===".$result[r_ordernum];
      echo "<br>ordernum1===".$result["r_ordernum"];
      echo "<br>resultarray<br>";
      foreach($result as $key=>$value){
      echo "<br>$key::$value";
      }
      echo "<br>"; */
    if (!is_array($result)) {
        $cc_flag = false;
        $cc_err = ERROR;
        //$cc_err .= "<br>Transcation declined. Please contact the administrator." . $result;
        $cc_err = $result;
    }//end if
    else {
        if ($result["r_approved"] != "APPROVED") { // transaction failed, print the reason
            $cc_flag = false;
            $cc_err = ERROR;
            $err = explode(":", $result[r_error]);
            if (is_array($err))
                $len = strlen($err[0]) + 1;
            else
                $len=0;
            $cc_err .= "<br>" . substr($result[r_error], $len);
            //print "Status: $result[r_approved]\n";
            //print "Error: $result[r_error]\n";
        }//end if
        else { // success
            $cc_flag = true;
            if ($txtUrPayDemo == "TEST" && $result[r_ordernum]==0)  $cc_tran = rand (1000, 10000);
            else $cc_tran = $result[r_ordernum];
            //	print "Status: $result[r_approved]\n";
            //print "Code: $result[r_code]\n";
            //print "OID: $result[r_ordernum]\n\n";
        }//end else
    }//end else
}//end else
?>
