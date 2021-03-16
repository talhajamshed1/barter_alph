<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/session_check.php");

if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}
else {
    $imagefolder = $secureserver;
}
//store user profile
$userProfileData = userProfiles($_SESSION["guserid"]);

//checking payment method
$txtPayMethod = ($_GET['paymethod'] != '') ? $_GET['paymethod'] : $_POST['txtPayMethod'];

if ($_GET["saleid"] != "") {
    $amnt = $_GET["amnt"];
    $saleid = $_GET["saleid"];
    //$userid=$_GET["userid"];
    $now = $_GET["dt"];
}
else if ($_POST["saleid"] != "") {
    $saleid = $_POST["saleid"];
    $now = urldecode($_POST["dt"]);
    $cost = $_POST["amnt"];
    $amnt = $cost;
}

$userid = $_SESSION["guserid"];
$cc_err = "";
$cc_flag = false;
$var_insert_flag = false;

$var_sale_flag = false;
$var_rej_flag = false;
$var_validation = true;
$sql = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected,sd.vMethod from " . TABLEPREFIX . "saledetails
					sd inner join " . TABLEPREFIX . "sale s ";
$sql .= " on sd.nSaleId = s.nSaleId ";
$sql .= " where  sd.nSaleId='" . addslashes($saleid) . "' AND sd.nUserId='" . $_SESSION["guserid"] . "' AND sd.dDate='";
$sql .= addslashes($now) . "' ";

$result = mysql_query($sql) or die(mysql_error());
if (mysql_num_rows($result) > 0) {
    if ($row = mysql_fetch_array($result)) {
        $cost = $row["nAmount"];
        $amnt = $cost;
        $var_title = $row["vTitle"];
        if (strlen($var_title) > 80) {
                $var_title = substr($var_title, 0, 80).'...';
            }
        $var_method = $row["vMethod"];
        $reqd = $row["nQuantity"];
        if ($row["vSaleStatus"] == "1") {
            $var_sale_flag = true;
        }
        if ($row["vRejected"] == "0") {
            $var_rej_flag = true;
        }
    }
}
else {
    $cc_err = "<font color='FF0000'>".ERROR_CHECK_YOUR_INPUT."</font>";
}

$sql = "Select nSaleInterId,vName,vBank,vReferenceNo,date_format(dReferenceDate,'%m/%d/%Y') as 'dReferenceDate',date_format(dEntryDate,'%m/%d/%Y  %H:%i') as 'dEntryDate',vMethod from " . TABLEPREFIX . "saleinter where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
$sql .= addslashes($now) . "' AND vDelStatus='0' ";
$result = mysql_query($sql) or die(mysql_error());
if (mysql_num_rows($result) > 0) {
    $row = mysql_fetch_array($result);
    $var_insert_flag = false;
    $disp_name = $row["vName"];
    $disp_bank = $row["vBank"];
    $var_method = $row["vMethod"];
    $disp_refno = $row["vReferenceNo"];
    $disp_refdate = $row["dReferenceDate"];
    $disp_entrydate = $row["dEntryDate"];
}
else {
    $var_insert_flag = true;
}

$disp_method = get_payment_name($var_method);

//redeem point code start here
//checking payment mode
if ($txtPayMethod == 'rp') {

    //fetch logged user total points
    $showUserTotalPoints = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');

    if ($showUserTotalPoints > 0) {
        $showUserTotalPoints = $showUserTotalPoints;
        $redeemPoint = round(($cost / DisplayLookUp('PointValue')) * DisplayLookUp('PointValue2'), 2);

        //checking enter user points and avilable points
        if ($redeemPoint <= $showUserTotalPoints) {
            //redeem points from user
            mysql_query("UPDATE " . TABLEPREFIX . "usercredits SET nPoints=nPoints-$redeemPoint WHERE
								nUserId='" . $_SESSION["guserid"] . "'") or die(mysql_error());

            $sql = "Update " . TABLEPREFIX . "saledetails set vSaleStatus='2',vTxnId='$cc_tran',dTxnDate=now() where ";
            $sql .= " nSaleId='" . $saleid . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
            $sql .= $now . "' ";
            mysql_query($sql) or die(mysql_error());

            $sql = "Select nUserId from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($saleid) . "'";
            mysql_query($sql) or die(mysql_error());

            $result = mysql_query($sql) or die(mysql_error());
            if (mysql_num_rows($result) > 0) {
                if ($row = mysql_fetch_array($result)) {
                    if ($cost == '') {
                        $cost = '0';
                    }
                    //store seller id for after successfull payment of transaction fee
                    /*mysql_query("INSERT INTO " . TABLEPREFIX . "SuccessFee (nUserId,nPurchaseBy,nProdId,nAmount,
                                    nPoints,dDate,vType) VALUES ('" . $row["nUserId"] . "','" . $_SESSION["guserid"] . "',
                                    '" . $saleid . "','" . $cost . "','" . $redeemPoint . "',now(),'sa')") or die(mysql_error());*/
                }
            }

            if($_GET['reqd'] != '') {
                $quanity = $_GET['reqd'];
                //reduce requested quantity from the master table
                reduceQuantity($saleid,$quanity);

            }

            header('location:buycon.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=$cost&ptype=rp&');
            exit();
        }
        else {
            $message = str_replace('{point}',$showUserTotalPoints,str_replace('{point_name}',POINT_NAME,ERROR_CANNOT_COMPLETE_POINT_LOW)).'<br><a href="buynext.php?saleid=' . $saleid . '&source=sa&amnt=' . $_GET['amnt'] . '&tot=' . $_GET['tot'] . '&reqd=' . $_GET['reqd'] . '&dt=' . $_GET['dt'] . '&">'.LINK_CONTINUE.'</a>';
        }
    }
    else {
        $showUserTotalPoints = '0';
        $message = str_replace('{point_name}',POINT_NAME,ERROR_CANNOT_COMPLETE_NO_POINT_AVAILABLE).' '.TEXT_CLICK_LINK_TO_CONTINUE.'<a href="buynext.php?saleid=' . $saleid . '&source=sa&amnt=' . $_GET['amnt'] . '&tot=' . $_GET['tot'] . '&reqd=' . $_GET['reqd'] . '&dt=' . $_GET['dt'] . '&">'.LINK_CONTINUE.'</a>';
    }
}
//redeem point code end here

if ($_POST["postback"] == "Y") {
    $FirstName = $_POST["txtFirstName"];
    $LastName = $_POST["txtLastName"];
    $Address = $_POST["txtAddress"];
    $City = $_POST["txtCity"];
    $State = $_POST["txtState"];
    $Zip = $_POST["txtPostal"];
    $CardNum = $_POST["txtccno"];
    $Email = $_POST["txtEmail"];
    $CardCode = $_POST["txtcvv2"];
    $Country = $_POST["cmbCountry"];
    $Month = $_POST["txtMM"];
    $Year = $_POST["txtYY"];
    $txtACurrency = PAYMENT_CURRENCY_CODE;
    $cc_tran = "";
    
   $errormessage = '';
   
   $Month    = trim($Month);
   $Year     = trim($Year);    
   
    settype($Month, 'integer');
    settype($Year, 'integer');
   
        if(!is_numeric($Month) || $Month < 1 || $Month > 12)  
         {    
            $var_validation = false;
            $errormessage        =   PAYMENT_INVALID_EXP_MONTH ."<br>";

         }
         $currentYear = date('Y');    

        settype($currentYear, 'integer');    

        if(!is_numeric($Year) || $Year < $currentYear || $Year  > $currentYear + 10)    
        {    

            $var_validation = false;
            $errormessage.=PAYMENT_INVALID_EXP_YEAR."<br>";   

        }    

    if ($var_sale_flag == true && $var_rej_flag == true && $var_insert_flag == true && $var_validation==true) {
        /* get the invoice number */
        $sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
        $result1 = mysql_query($sql1) or die(mysql_error());
        $row1 = mysql_fetch_array($result1);
        $Inv_id = $row1['maxinvid'];

        $Cust_ip = getClientIP();
        $Company = '-NA-';
        $Phone = $_SESSION["gphone"];
        $Cust_id = $_SESSION["guserid"];

        //checking payment mode
        if ($txtPayMethod == 'cc') {
            require("credit_inte.php");
        }
        if ($txtPayMethod == 'bp') {
            require("Bluepay.php");
        }
        if ($txtPayMethod == 'yp') {
            require("yourpay.php");
        }

        if ($cc_flag == true) {
            $sql = "Update " . TABLEPREFIX . "saledetails set vSaleStatus='2',vTxnId='$cc_tran',dTxnDate=now() where ";
            $sql .= " nSaleId='" . $saleid . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
            $sql .= $now . "' ";
            mysql_query($sql) or die(mysql_error());

            $sql = "Select nUserId, nQuantity from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($saleid) . "'";
            mysql_query($sql) or die(mysql_error());
            // nQuantity
            $result = mysql_query($sql) or die(mysql_error());
            if (mysql_num_rows($result) > 0) {
                if ($row = mysql_fetch_array($result)) {
                    if ($cost == '') {
                        $cost = '0';
                    }
                    $quantityREQD = $reqd;
                    if($quantityREQD!='') {
                        //reduce requested quantity from the master table
                        $sql = "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity - $quantityREQD where nSaleId ='" . addslashes($saleid) . "'";
                        mysql_query($sql) or die(mysql_error());
                    }

//                    $sql = "Update " . TABLEPREFIX . "users set nAccount = nAccount +  $cost  where  nUserId='" . $row["nUserId"] . "'";
//                    mysql_query($sql) or die(mysql_error());

                    if($_POST['reqQty'] != '') {
                        $quanity = $_POST['reqQty'];
                        //reduce requested quantity from the master table
                        reduceQuantity($nSaleId,$quanity);

                    }
                }
            }

            header('location:buycon.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=$cost&');
            exit();
        }
    }
    else {
        $cc_flag = false;
        if (strlen($cc_err) <= 0) {
            if ($var_sale_flag == false) {
                $cc_err = "<br>".MESSAGE_PAYMENT_MADE_FOR_ITEM;
            }

            if ($var_rej_flag == false) {
                $cc_err = "<br>".MESSAGE_SALE_REJECTED_BY_OWNER;
            }

            if ($var_insert_flag == false) {
                $cc_err = "<br>".MESSAGE_PAYMENT_MADE_BY_CHECK;
            }
            if($var_validation==false)
            {
                 $cc_err = "<br>".$errormessage;
            }
        }
    }
}

if(isset($_POST["postback"]) &&  ($_POST["postback"]== "Y")) {
    $FirstName = $_POST["txtFirstName"];
    $LastName = $_POST["txtLastName"];
    $Address = $_POST["txtAddress"];
    $City = $_POST["txtCity"];
    $State = $_POST["txtState"];
    $Zip = $_POST["txtPostal"];
    $CardNum = $_POST["txtccno"];
    $Email = $_POST["txtEmail"];
    $CardCode = $_POST["txtcvv2"];
    $Country = $_POST["cmbCountry"];
    $Month = $_POST["txtMM"];
    $Year = $_POST["txtYY"];
    
}else{
    $FirstName      =   stripslashes($userProfileData['vFirstName']);
    $LastName       =   stripslashes($userProfileData['vLastName']);
    $Address        =   stripslashes($userProfileData['vAddress1']);
    $City           =   stripslashes($userProfileData['vCity']);
    $State          =   stripslashes($userProfileData['vState']);
    $Zip            =   stripslashes($userProfileData['nZip']);
    $Email          =   stripslashes($userProfileData['vEmail']);
    $CardNum        =   '';
    $CardCode       =   '';
    $Country        =   $userProfileData['vCountry'];
    $Month          =   '';
    $Year           =   '';
}
if($Country=='')
{
    $Country = 'United States';
}

include_once('./includes/gpc_map.php');

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function varify()
    {
        reqd= document.frmBuy.quantityREQD.value;
        avail = document.frmBuy.quantityAVL.value;
        if(isNaN(reqd) || reqd.substring(0,1) == " " || reqd.length <= 0 || parseInt(reqd) > parseInt(avail) || parseInt(reqd) < 1)
        {
            alert("<?php echo ERROR_QUANTITY_INVALID; ?>");
            document.frmBuy.quantityREQD.value="1";
        }
        else
        {
            document.frmBuy.quantityREQD.value=parseInt(reqd);
        }
        document.frmBuy.total.value=parseInt(document.frmBuy.amount.value)*parseInt(document.frmBuy.quantityREQD.value);
    }

    function proceed(cc)
    {
                if(parseInt(document.frmBuy.quantityREQD.value) > parseInt(document.frmBuy.quantityAVL.value))
                {
                    alert("<?php echo ERROR_QUANTITY_INVALID; ?>");
                }
                else
                {
                    document.frmBuy.cctype.value=cc;
                    document.frmBuy.submit();
                }
    }    

    function clickConfirm()
    {
        var $card_num = $jqr( "#id_card_num" );
	var card_num = $card_num.val();
        var card_code = $jqr( "#txtcvv2").val();
	//-- Credit card can only be numeric
	if (!$jqr.isNumeric(card_num)) {
		alert('<?php echo PAYMENT_INVALID_CARD_NUMEBR;?>');
		$card_num.focus();
		return false;
	}
        
        if (!$jqr.isNumeric(card_code)) {
		alert('<?php echo PAYMENT_INVALID_CARD_CODE;?>');
		$jqr("#txtcvv2").focus();
		return false;
	}

	var $exp_mon =  $jqr( "#id_exp_mon" );
	var $exp_year = $jqr( "#id_exp_year" );
	var exp_mon = $exp_mon.val();
	var exp_year = $exp_year.val();
	//-- Month can only be numeric
	if (!$jqr.isNumeric(exp_mon)) {
		alert('<?php echo PAYMENT_INVALID_EXP_MONTH;?>');
		$exp_mon.focus();
		return false;
	}
	//-- Year can only be numeric
	if (!$jqr.isNumeric(exp_year)) {
		alert('<?php echo PAYMENT_INVALID_EXP_YEAR;?>');
		$exp_year.focus();
		return false;
	}
	//-- Year must be in YYYY format
	if($jqr.trim(exp_year).length < 4) {
		alert('<?php echo PAYMENT_INVALID_YEAR_FORMAT;?>');
		$exp_year.focus();
		return false;
	}
        
	//-- Card expiration must be future month
	/*var currentYear = (new Date).getFullYear();
	var currentMonth = (new Date).getMonth() + 1;
	if (currentMonth.length < 2) {
		currentMonth = "0" + currentMonth;
	}
	var curYrMn = currentYear.toString() + currentMonth.toString();
	var expYrMn = exp_year + exp_mon;
	if (parseInt(expYrMn, 10) < parseInt(curYrMn, 10)) {
		alert("Please enter a future expiration date.");
		$exp_mon.focus();
		return false;
	}*/

        document.frmBuy.postback.value='Y';
        document.frmBuy.method='post';
        document.frmBuy.submit();
    }

    function checkValue(t)
    {
        if(isNaN(t.value) || t.value.substring(0,1) == " " || t.value.length == 0 || parseFloat(t.value) < 0 )
        {
            if(t.name == "txtccno")
            {
                t.value="";
            }
            else
            {
                t.value="000";
            }
        }
    }
</script>

<body onLoad="timersOne();if (document.getElementById('ddlCountry')){document.getElementById('ddlCountry').value='<?php echo $Country; ?>';}">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">
				<div class="innersubheader">
					<h4><?php echo HEADING_PAYMENT_FORM; ?></h4>
				</div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
					
						<?php
						if ($txtPayMethod == 'rp') {
							if (isset($message) && $message != '') {
                                                                 if($cc_flag == false || $var_insert_flag == false || $var_sale_flag == false || $var_rej_flag == false){   
									?>
									<div class="row warning"><?php echo $message; ?></div>
								<?php } else if($cc_flag == true || $var_insert_flag == true || $var_sale_flag == true || $var_rej_flag == true){?>
                                                                <div class="row success"><?php echo $message; ?></div>           
                                                                <?php }}}?><br>
							
							<?php
							if ($txtPayMethod != 'rp' && $txtPayMethod != 'gc') {
								?>

						<form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
                        <input type="hidden" name="txtPayMethod" value="<?php echo $txtPayMethod; ?>">
						
						<?php
							if (isset($cc_flag) && $cc_flag == false) {
						?>
                        <div class="row warning"><?php echo $cc_err; ?></div>
						<?php } ?>
						
						
						<div class="row main_form_inner subheader">
							<h4><?php echo HEADING_PAYMENT_DETAILS; ?></h4>
						</div>
						
						<div class="row main_form_inner">
							<label><?php echo TEXT_TITLE; ?></label>
							<label><?php echo htmlentities($var_title) ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_AMOUNT; ?></label>
							<label><?php echo CURRENCY_CODE; ?><?php echo  $cost ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_QUANTITY; ?></label>
							<label>
								<?php echo $reqd ?>
								<input type="hidden" name="reqQty" id="reqQty" value="<?php echo  $reqd ?>">
							</label>
						</div>
						
						<?php
							if ($var_insert_flag == true) {
						?>
						<input type="hidden" name="postback" id="postback" value="">
						<input type="hidden" name="amnt" id="amnt" value="<?php echo $amnt ?>">
						<input type="hidden" name="saleid" id="saleid" value="<?php echo $saleid ?>">
						<input type="hidden" name="userid" id="userid" value="<?php echo $userid ?>">
						<input type="hidden" name="dt" id="dt" value="<?php echo urlencode($now) ?>">

						<div class="row main_form_inner subheader">
							<h4><?php echo TEXT_CREDIT_CARD_DETAILS; ?></h4>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_FIRST_NAME; ?><span class="warning">*</span></label>
							<input type="text" name="txtFirstName" id="txtFirstName" value="<?php echo $FirstName; ?>" size="24" maxlength="40" class="form-control">
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_LAST_NAME; ?><span class="warning">*</span></label>
							<input type="text" name="txtLastName" id="txtLastName" value="<?php echo $LastName; ?>" size="24" maxlength="40" class="form-control">
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_CARD_NUMBER; ?><span class="warning">*</span></label>
							<div class="col-lg-10 col-sm-12 col-md-10 col-xs-12 row">
								<input type=text name="txtccno" class="form-control" id="id_card_num" size="24" maxlength="16" onBlur="javascript:checkValue(this);" value="<?php echo $CardNum;?>">
							</div>
							<div class="col-lg-2 col-sm-12 col-md-10 col-xs-12">
								<img src="<?php echo $imagefolder ?>/images/visa_amex.gif">
							</div>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_CARD_VALIDATION_CODE; ?><span class="warning">*</span></label>
							<input type=password name="txtcvv2" class="form-control numbersOnly" id="txtcvv2" size=10 maxlength="4" onBlur="javascript:checkValue(this);" value="<?php echo $CardCode;?>">
							<a href="http://www.cvvnumber.com/cvv.html" target="_blank" style="font-size:11px">What is my CVV code?</a>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_EXPIRATION_DATE; ?><span class="warning">*</span></label>
							<div class="col-lg-6 col-sm-12 col-md-12 col-xs-12 row">
								<input type=text name="txtMM" class="form-control numbersOnly" id="id_exp_mon" maxlength="2" value="<?php echo $Month;?>">
							</div>
							<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12 row" align="center">/</div>
							<div class="col-lg-6 col-sm-12 col-md-12 col-xs-12 row">
								<input type=text name="txtYY" class="form-control numbersOnly" id="id_exp_year" maxlength="4" value="<?php echo $Year;?>">
							</div>
						</div>
						
						<div class="row main_form_inner subheader">
							<h4><?php echo TEXT_BILLING_ADDRESS_DETAILS; ?></h4>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_ADDRESS; ?><span class="warning">*</span></label>
							<input type="text" name="txtAddress" class="form-control" id="txtAddress" size="24" maxlength="30" value="<?php echo $Address; ?>">
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_CITY; ?><span class="warning">*</span></label>
							<input type="text" name="txtCity" class="form-control" id="txtCity" size="24" maxlength="30"  value="<?php echo $City; ?>">
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_STATE; ?><span class="warning">*</span></label>
							<input type="text" name="txtState" class="form-control" id="txtState" size="24" maxlength=30 value="<?php echo $State; ?>">
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_ZIP; ?><span class="warning">*</span></label>
							<input type="text" name="txtPostal" class="form-control numbersOnly" id="txtPostal" size="24" maxlength="10" value="<?php echo $Zip; ?>">
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_COUNTRY; ?><span class="warning">*</span></label>
							<select name="cmbCountry" class="form-control" id="ddlCountry">
								<?php include("includes/country_select.php"); ?>
							</select>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_EMAIL; ?><span class="warning">*</span></label>
							<input type=text name="txtEmail" class="form-control" id="txtEmail" size="24" maxlength="50" value="<?php echo $Email; ?>">
						</div>
						<div class="row main_form_inner">
							<label><input type="button" name="btPay" id="btPay" class="subm_btt"  value="Pay Now" onClick="javascript:clickConfirm();"></label>
						</div>
							<?php
							   }
								else {
							?>
						<div class="row main_form_inner">
							<label><?php echo TEXT_NAME; ?></label>
							<label><?php echo  $disp_name ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_BANK; ?> (<?php echo TEXT_IF_APPLICABLE; ?>)</label>
							<label><?php echo  $disp_bank ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_REFERENCE_NUMBER; ?></label>
							<label><?php echo  $disp_refno ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_PAYMENT_MODE; ?></label>
							<label><?php echo  $disp_method ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_REFERENCE_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>)</label>
							<label><?php echo  $disp_refdate ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_ENTRY_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>)</label>
							<label><?php echo  $disp_entrydate ?></label>
						</div>
						<div class="row main_form_inner">
							<label>asdasdasda</label>
							<label>asdasdasda</label>
						</div>
						<?php } ?>
					</form>
						
						<div class="clear"></div>
						<?php
							}
							if ($txtPayMethod == 'gc') {
	
						require_once('gc/library/googlecart.php');
						require_once('gc/library/googleitem.php');
						require_once('gc/library/googleshipping.php');
						require_once('gc/library/googletax.php');
						require_once('gc/library/googleresponse.php');
						require_once('gc/library/googlemerchantcalculations.php');
						require_once('gc/library/googleresult.php');
	
						$_SESSION['txtGoogleId'] = DisplayLookUp('googleid');
						$_SESSION['txtGoogleKey'] = DisplayLookUp('googlekey');
						$_SESSION['chkGoogleSandbox'] = DisplayLookUp('googlemode');
						$_SESSION['sess_gc_saleid'] = $saleid;
						$_SESSION['sess_gc_paytype'] = $paytype;
						$_SESSION['sess_gc_txtPayMethod'] = $txtPayMethod;
						$_SESSION['sess_gc_amount'] = $cost;
						$_SESSION['sess_gc_txtACurrency'] = PAYMENT_CURRENCY_CODE;
						$_SESSION["sess_gc_var_title"] = $var_title;
						$_SESSION["sess_gc_amnt"] = $amnt;
						$_SESSION["sess_gc_userid"] = $userid;
						$_SESSION["sess_gc_dt"] = urlencode($now);
	
						$gc_status = ($_GET['gc_status'] != '') ? $_GET['gc_status'] : 'failure';
	
	
	
						function UseCase1() {
							$google_id = $_SESSION['txtGoogleId']; // Merchant ID
							$google_key = $_SESSION['txtGoogleKey']; // Merchant Key
							$google_demo = $_SESSION['chkGoogleSandbox']; // "YES" if in test mode, "NO" if in live mode
							$cost = $_SESSION['sess_gc_amount']; // price
							$currency = $_SESSION['sess_gc_txtACurrency'];
	
							$saleid = $_SESSION['sess_gc_saleid'];
							$paytype = $_SESSION['sess_gc_paytype'];
							$txtPayMethod = $_SESSION['sess_gc_txtPayMethod'];
							$amount = $_SESSION['sess_gc_amount'];
							$txtACurrency = $_SESSION['sess_gc_txtACurrency'];
							$var_title = $_SESSION["sess_gc_var_title"];
	
							$amnt = $_SESSION["sess_gc_amnt"];
							$userid = $_SESSION["sess_gc_userid"];
							$dt = $_SESSION["sess_gc_dt"];
	
	
	
							if ($google_demo == "TEST")
								$server_type = "sandbox";
							else
								$server_type = "checkout";
	
	
							// Create a new shopping cart object
							$cart = new GoogleCart($google_id, $google_key, $server_type, $currency);
	
							// Add items to the cart
							$item_1 = new GoogleItem(SITE_NAME, // Item name
									$var_title, // Item description
									1, // Quantity
									$cost); // Unit price
	
							$cart->AddItem($item_1);
	
							// continue link page
							$cart->SetContinueShoppingUrl(SECURE_SITE_URL . "/buycc.php?saleid=" . $saleid . "&userid=" . $userid . "&dt=" . $dt . "&amnt=" . $cost . "&paymethod=gc&gc_status=success&reqd=".$_GET['reqd']);
	
							$cart->AddRoundingPolicy("HALF_UP", "PER_LINE");
							// Display XML data
							// echo "<pre>";
							// echo htmlentities($cart->GetXML());
							// echo "</pre>";
	
							$cart->SetMerchantPrivateData('buycc-' . $userid . '-' . $saleid . '-' . $_SESSION["gphone"] . '-' . $dt . '-' . $cost . '-' . $txtACurrency);
	
							// Display Google Checkout button
							echo $cart->CheckoutButtonCode("LARGE");
						}
	
						//end google usecase
	
						$_SESSION['sess_page_name'] = 'buycc.php';
						$_SESSION['sess_page_return_url_suc'] = SITE_URL . "/buycc.php?saleid=" . $saleid . "&userid=" . $userid . "&dt=" . $dt . "&amnt=" . $cost . "&paymethod=gc&gc_status=success&";
						$_SESSION['sess_page_return_url_fail'] = SECURE_SITE_URL . "/buycc.php?saleid=" . $saleid . "&userid=" . $userid . "&dt=" . $dt . "&amnt=" . $cost . "&paymethod=gc&gc_status=failure&";
	
						//calculation starts here
						if (isset($gc_status) && $gc_status == 'success') {
							$txtACurrency = $txtACurrency;
							$gc_tran = "";
							$gc_flag = true;
	
							if ($var_sale_flag == true && $var_rej_flag == true && $var_insert_flag == true) {
								/* get the invoice number */
								$sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
								$result1 = mysql_query($sql1) or die(mysql_error());
								$row1 = mysql_fetch_array($result1);
								$Inv_id = $row1['maxinvid'];
	
								$Cust_ip = getClientIP();
								$Company = '-NA-';
								$Phone = $_SESSION["gphone"];
								$Cust_id = $_SESSION["guserid"];
	
								if ($gc_flag == true) {
									$sql = "Update " . TABLEPREFIX . "saledetails set vSaleStatus='2',vTxnId='$gc_tran',dTxnDate=now() where ";
									$sql .= " nSaleId='" . $saleid . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
									$sql .= $now . "' ";
									mysql_query($sql) or die(mysql_error());
	
									$sql = "Select nUserId from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($saleid) . "'";
									mysql_query($sql) or die(mysql_error());
	
									$result = mysql_query($sql) or die(mysql_error());
									if (mysql_num_rows($result) > 0) {
										if ($row = mysql_fetch_array($result)) {
											if ($cost == '') {
												$cost = '0';
											}
	
//											$sql = "Update " . TABLEPREFIX . "users set nAccount = nAccount +  $cost  where  nUserId='" . $row["nUserId"] . "'";
//											mysql_query($sql) or die(mysql_error());
										}
									}
									if($_GET['reqd'] != '') {
										$quanity = $_GET['reqd'];
										//reduce requested quantity from the master table
										reduceQuantity($saleid,$quanity);
									}
	
									$_SESSION["gsaleextraid"] = "";
									$_SESSION['txtGoogleId'] = "";
									$_SESSION['txtGoogleKey'] = "";
									$_SESSION['chkGoogleSandbox'] = "";
									$_SESSION['sess_gc_saleid'] = "";
									$_SESSION['sess_gc_paytype'] = "";
									$_SESSION['sess_gc_txtPayMethod'] = "";
									$_SESSION['sess_gc_amount'] = "";
									$_SESSION['sess_gc_txtACurrency'] = "";
									$_SESSION["sess_gc_tmpid"] = "";
									$_SESSION["sess_gc_var_title"] = "";
									$_SESSION["sess_gc_amnt"] = "";
									$_SESSION["sess_gc_userid"] = "";
									$_SESSION["sess_gc_dt"] = "";
									$_SESSION['sess_page_name'] = '';
									$_SESSION['sess_page_return_url_suc'] = '';
									$_SESSION['sess_page_return_url_fail'] = '';
									$_SESSION['sess_flag_failure'] = '';
	
	
									header('location:buycon.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=$cost&');
									exit();
								}
							}
							else {
								$gc_flag = false;
								if (strlen($gc_err) <= 0) {
									if ($var_sale_flag == false) {
										$gc_err = "<br>".MESSAGE_PAYMENT_MADE_FOR_ITEM;
									}
	
									if ($var_rej_flag == false) {
										$gc_err = "<br>".MESSAGE_SALE_REJECTED_BY_OWNER;
									}
	
									if ($var_insert_flag == false) {
										$gc_err = "<br>".MESSAGE_PAYMENT_MADE_BY_CHECK;
									}
								}
							}
						}
						//calculation ends here
						?>
						
                        <div class="full_width">
							<?php
						 		if ($gc_flag == false && $gc_err != '') {
					        ?>
							<div class="row warning"><?php echo $gc_err; ?></div>
							<?php
								}
								if (isset($gc_status) && $gc_status != 'success') {
							?>
							
							<div class="row main_form_inner subheader">
								<h4><?php echo HEADING_PAYMENT_DETAILS; ?></h4>
							</div>
							
							<div class="row main_form_inner">
								<label><?php echo TEXT_ITEM; ?></label>
								<label><?php echo  htmlentities($var_title) ?></label>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_AMOUNT; ?></label>
								<label><?php echo CURRENCY_CODE; ?><?php echo  $cost ?></label>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_QUANTITY; ?></label>
								<label><?php echo  $reqd ?></label>
							</div>
							<div class="row main_form_inner">
								<label>
									<?php echo MESSAGE_GOOGLE_CHECKOUT_INSTRUCTION; ?>
									<br><br>
									<b><?php echo MESSAGE_WAITING_FOR_SECURE_PAYMENT_INTERFACE; ?>....</b>
									<br><br><br>
									<?php UseCase1(); ?>
								</label>
							</div>
								<?php
									}
                                ?>						
						</div>
						 <?php
						}
						?>
					</div>	
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>			
				</div>				
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>
			</div>
		</div>  
	</div>
</div>
<script type="text/javascript">
    var jqr = jQuery.noConflict();
    jqr(document).ready(function() {
        jqr('.numbersOnly').keypress(function(e) { 
            var key_codes = [46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 0, 8];
                if (!(jqr.inArray(e.which, key_codes) >= 0)) {
                    e.preventDefault();
                }
        });
    });
</script>
<?php require_once("./includes/footer.php"); ?>