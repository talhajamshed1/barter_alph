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

$txtPaypalEmail = DisplayLookUp('paypalemail');
$txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
$txtPaypalSandbox = DisplayLookUp('paypalmode');

if ($txtPaypalSandbox == "TEST") {
    $paypalurl = "www.sandbox.paypal.com";
}//end if
else {
    $paypalurl = "www.paypal.com";
}//end else

$flag_to_continue = false;

//Data check for PDT and proceeded further
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-synch';

$tx_token = $_GET['tx'];
//$auth_token = "Yl95T6hgNiiI3TsU4VYxHabtIiy-AZzCP5ks92AzLz3We2Jknt5lhx-wQtK";
$auth_token = $txtPaypalAuthtoken;
$req .= "&tx=$tx_token&at=$auth_token";

// post back to PayPal system to validate
$header .= "POST https://".$paypalurl."/cgi-bin/webscr HTTP/1.1\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Host: ".$paypalurl."\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n";
$header .= "Connection: close\r\n\r\n";
$fp = fsockopen ("ssl://".$paypalurl, 443, $errno, $errstr, 30);

// If possible, securely post back to paypal using HTTPS
// Your PHP server will need to be SSL enabled
// $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

if (trim($txtPaypalAuthtoken)==''){
    $flag_to_continue = true;
}
else if (!$fp) {
    // HTTP ERROR
}//end if
else {
    fputs($fp, $header . $req);
    // read the body data
    $res = '';
    $headerdone = false;
    while (!feof($fp)) {
        $line = fgets($fp, 1024);
        if (strcmp($line, "\r\n") == 0) {
            // read the header
            $headerdone = true;
        }//end if
        else if ($headerdone) {
            // header has been read. now read the contents
            $res .= $line;
        }//end else
    }//end while
    // parse the data
    $lines = explode("\n", $res);
    $keyarray = array();
    //echo($lines[0] . " here");
    if (strcmp($lines[1], "SUCCESS") == 0 || strcmp($lines[0], "SUCCESS") == 0) {
        for ($i = 1; $i < count($lines); $i++) {
            list($key, $val) = explode("=", $lines[$i]);
            $keyarray[urldecode($key)] = urldecode($val);
            //echo("key : " . urldecode($key) . "   value : " . urldecode($val) . "<br>");
        }//end for loop
        // check the payment_status is Completed
        // check that txn_id has not been previously processed
        // check that receiver_email is your Primary PayPal email
        // check that payment_amount/payment_currency are correct
        // process payment
        $flag_to_continue = true;
    }//end if
    else if (strcmp($lines[1], "FAIL") == 0 || strcmp($lines[0], "FAIL") == 0) {
        // log for manual investigation
        //echo("Fail in result");
        $flag_to_continue = false;
    }//end else if
}//end if

fclose($fp);

$message = "";
$flag = false;

if ($keyarray['receiver_email'] != $txtPaypalEmail && trim($txtPaypalAuthtoken)!='') {
    $flag_to_continue = false;
}//end if

if ($flag_to_continue == true) {

    // $txnid=$_GET["txnid"];
    $txnid = $keyarray['txn_id'];
    $var_swapid = $keyarray['option_selection1'];
    $var_tmpid = $_SESSION["gstempid"];

//////////////////////////////////////////////////////////////////////////////////////
//check if txnid alredy there to prevent refresh
    $sql3 = "Select nTempId from " . TABLEPREFIX . "swaptemp where nTempId='$var_tmpid'";
    $result3 = mysqli_query($conn, $sql3) or die(mysqli_error($conn));
    if (mysqli_num_rows($result3) > 0) {
        $sql = "Select vTxnId from " . TABLEPREFIX . "swap where vTxnId='" . addslashes($txnid) . "' AND vMethod='pp'";
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

                    $message = MESSAGE_THANKYOU_PAYMENT_TRANSACTION_COMPLETED;
                }//end if
            }//end if
            else {
                $message =str_replace('{site_email}',SITE_EMAIL,ERROR_TRANSACTION_ALREADY_PERFORMED_CONTACT_EMAIL);
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
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3">
				<?php include_once ("./includes/usermenu.php"); ?>
			</div>
			<div class="col-lg-9">		
				<div class="innersubheader">
					<h4><?php echo HEADING_PAYMENT_STATUS; ?></h4>
				</div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<?php
			if (isset($flag) && $flag == false) {
				?>
						<div class="warning"><?php echo stripslashes($message); ?></div>
					<?php
					}//end if
					else {
						?>
						<div class="row">
							<h4><?php echo HEADING_TRANSACTION_DETAILS; ?></h4>
						</div>
						
						<div class="row main_form_inner">
							<label><?php echo TEXT_TITLE; ?></label>
							<?php echo  $var_title ?>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_AMOUNT; ?></label>
							<?php echo CURRENCY_CODE; ?><?php echo  $db_amount ?>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_TYPE; ?></label>
							<?php echo  ($db_post_type == "swap") ? TEXT_SWAP_TRANSACTION : TEXT_WISH_TRANSACTION ?>
						</div>
						<div class="row main_form_inner">
							<div class="row success"><b><?php echo  $message ?></b></div>
						</div>
							<?php
						}//end else
						?>
					</div>	
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>			
				</div>
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>		
			</div>
		</div>  
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>
