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
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

$approval_tag = "0";
if (DisplayLookUp('userapproval') != '') {
    $approval_tag = DisplayLookUp('userapproval');
}//end if

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
$auth_token = $txtPaypalAuthtoken;
$req .= "&tx=$tx_token&at=$auth_token";

// post back to PayPal system to validate
$header .= "POST https://".$paypalurl."/cgi-bin/webscr HTTP/1.1\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Host: ".$paypalurl."\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n";
$header .= "Connection: close\r\n\r\n";
$fp = fsockopen ("ssl://".$paypalurl, 443, $errno, $errstr, 30);


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
        }//end else if
    }//end while
    // parse the data
    $lines = explode("\n", $res);
    $keyarray = array();
    //echo($lines[0] . " here");
    if (strcmp($lines[1], "SUCCESS") == 0 || strcmp($lines[0], "SUCCESS") == 0) {
        for ($i = 1; $i < count($lines); $i++) {
            list($key, $val) = explode("=", $lines[$i]);
            $keyarray[urldecode($key)] = urldecode($val);
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
        //		echo("Fail in result");
        $flag_to_continue = false;
    }//end else if
}//end if

fclose($fp);


$var_id = "";
$var_new_id = "";
$message = "";
$flag = false;

if ($flag_to_continue == true) {
    // $txnid=$_GET["txnid"];
    $txnid = $keyarray['txn_id'];
    $var_id = $_SESSION["guserid"];
    $var_amount = "";
    $var_txnid = "";
    $var_method = "";
    $var_login_name = "";
    $var_password = "";
    $var_first_name = "";
    $var_last_name = "";
    $var_date = "";
    $var_txnid = $txnid;

    $sqltxn = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid' AND vTxn_mode='pp'";

    $resulttxn = @mysqli_query($conn, $sqltxn) or die(mysqli_error($conn));
    if (@mysqli_num_rows($resulttxn) <= 0) {  // the tran id not present in the database
        $var_date = date('m/d/Y');
        $today = date("Y-m-d");

        //calculate end date
        switch ($_SESSION['sess_Plan_Mode']) {
            case "M":
                $addInterval = 'MONTH';
                break;

            case "Y":
                $addInterval = 'YEAR';
                break;
        }//end switch

        $expDate = mysqli_query($conn, "SELECT DATE_ADD(now(),INTERVAL 1 " . $addInterval . ") as expPlanDate") or die(mysqli_error($conn));
        if (mysqli_num_rows($expDate) > 0) {
            $nExpDate = mysqli_result($expDate, 0, 'expPlanDate');
        }//end if

        $userUpdate = "";

        //update member tbl in new plan
        mysqli_query($conn, "update " . TABLEPREFIX . "users set nPlanId='" . $_SESSION['ChangePlanId'] . "',vStatus='0',dPlanExpDate='" . $nExpDate . "', vMethod='pp', vTxnId='$txnid'
										where nUserId='" . $_SESSION['guserid'] . "'	and
										nPlanId='" . $_SESSION['sess_PlanId'] . "'") or die(mysqli_error($conn));

        //update old plan status in payment table
        mysqli_query($conn, "update " . TABLEPREFIX . "payment set vPlanStatus='I',vComments='Inactive on $today ' where
										nUserId='" . $_SESSION['guserid'] . "'	and vPlanStatus='A' and
										nPlanId='" . $_SESSION['sess_PlanId'] . "'") or die(mysqli_error($conn));
        //insert new entry
        $sqlPayment = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId,
								nSaleId,vInvno,vPlanStatus,nPlanId) VALUES ('R', '$txnid', '" . $_SESSION['sess_Plan_Amt'] . "',
								'pp',now(), '" . $_SESSION["guserid"] . "',
								'','$Inv_id','A','" . $_SESSION['ChangePlanId'] . "')";
        $result = @mysqli_query($conn, $sqlPayment) or die(mysqli_error($conn)); 

    //This is commented to avoid double entry in db

          
        
        $flag = true;

        header("Location:plan_upgrade_success.php");
        exit();
    }//end if
    else {
        $message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
    }//end else
}//end if

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
<div class="container">
	<div class="row">
		<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
		<div class="col-lg-9">					
			<div class="innersubheader">
				<h4><?php echo HEADING_PAYMENT_STATUS; ?></h4>
			</div>
			
			<div class="row">
				<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
				<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
					
					<?php
					if (isset($flag) && $flag == false) {
						?>
						<div class="row warning"><?php echo $message; ?></div>
					<?php }//end if ?>
										
				</div>
				<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>	
				
				<div class="clear"></div>					
				
				<div class="col-lg-12 col-sm-12 col-md-12">
					<div class="subbanner">
						<?php include('./includes/sub_banners.php'); ?>
					</div>
				</div>
				
			</div>					
		</div>
	</div>  
</div>
</div>



<?php require_once("./includes/footer.php"); ?>