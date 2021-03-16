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
include_once('./includes/gpc_map.php');

$approval_tag = "0";
$approval_tag = DisplayLookUp('userapproval');
if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else
//checking payment method
$txtPayMethod = ($_GET['paytype'] != '') ? $_GET['paytype'] : $_POST['txtPayMethod'];
$Sscope = "planupgrade";

// get get variables
If ($_GET["id"] != "") {
    $id = $_GET["id"];
}//end if
else if ($_POST["id"] != "") {
    $id = $_POST["id"];
}//end else
$id = $_SESSION["guserid"];

$sql = "Select nAmount,vLoginName,vPhone from " . TABLEPREFIX . "users where nUserId='$id'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    if ($row = mysqli_fetch_array($result)) {
        $amount = $row["nAmount"];
        $var_credit_desc = $row["vLoginName"];
        $var_phone = $row["vPhone"];
    }//end if
}//end if
//if regmode is paid and escrow is disabled
//if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
if (DisplayLookUp('15') != '1' && DisplayLookUp('plan_system')=='yes') {
    $amount = $_SESSION['sess_Plan_Amt'];
}//end if

$condReg = "where plan_id='" . $_SESSION['ChangePlanId'] . "' and lang_id = '" . $_SESSION['lang_id'] . "'";
$PlanName = fetchSingleValue(select_rows(TABLEPREFIX . 'plan_lang', 'vPlanName', $condReg), 'vPlanName');

$sqlUserDetls = mysqli_query($conn, "Select * from " . TABLEPREFIX . "users where nUserId='$id'") or die(mysqli_error($conn));
if (mysqli_num_rows($sqlUserDetls) > 0) {
    $arrUserDetls = mysqli_fetch_array($sqlUserDetls);
}//end if

if (isset($_POST["postback"]) && $_POST["postback"] == "Y") {
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
    // $cost = $_POST["amnt"];
    $saleid = $_POST["saleid"];
    $userid = $_POST["userid"];
    $now = urldecode($_POST["dt"]);
    $cost = $amount;
    $txtACurrency = PAYMENT_CURRENCY_CODE;
    $cc_flag = false;
    $cc_err = "";
    $cc_tran = "";

    /* get the invoice number */
    $sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
    $result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
    $row1 = mysqli_fetch_array($result1);
    $Inv_id = $row1['maxinvid'];
    /*     * *********************** */

    $Cust_ip = getClientIP();
    $Company = '-NA-';
    $Phone = $var_phone;
    $Cust_id = $id;


    //checking payment mode
    if ($txtPayMethod == 'cc') {
        require("credit_inte_upgrade.php");
    }//end if
    else if ($txtPayMethod == 'sp') {
        require("stripepay.php");
    }//end if
    else {
        require("yourpay.php");
    }//end else


    if ($cc_flag == true) {
        // Start of the transaction  for adding a user since payment is successfull
        $txnid = $cc_tran;
        // ////////////////////////////////////////////////////////////////////////////////////
        // check if txnid alredy there to prevent refresh
        $sql = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid'  AND vTxn_mode='" . $txtPayMethod . "'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (1 == 1) {
            $var_id = $_SESSION["guserid"];
            $var_amount = "";
            $var_txnid = "";
            $var_method = "";
            $var_login_name = "";
            $var_password = "";
            $var_first_name = "";
            $var_last_name = "";
            // here the transaction id has to be set that comes from the payment gateway
            $var_txnid = "$txnid";

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
            mysqli_query($conn, "update " . TABLEPREFIX . "users set nPlanId='" . $_SESSION['ChangePlanId'] . "',vStatus='0',dPlanExpDate='" . $nExpDate . "', vMethod='cc', vTxnId='$txnid'
              where nUserId='" . $_SESSION['guserid'] . "'	and
              nPlanId='" . $_SESSION['sess_PlanId'] . "'") or die(mysqli_error($conn));

            //update old plan status in payment table
            mysqli_query($conn, "update " . TABLEPREFIX . "payment set vPlanStatus='I',vComments='Inactive on $today ' where
              nUserId='" . $_SESSION['guserid'] . "'	and vPlanStatus='A' and
              nPlanId='" . $_SESSION['sess_PlanId'] . "'") or die(mysqli_error($conn));
            //insert new entry
            $sqlPayment = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId,
            nSaleId,vInvno,vPlanStatus,nPlanId) VALUES ('R', '$txnid', '" . $_SESSION['sess_Plan_Amt'] . "',
            '".$txtPayMethod."',now(), '" . $_SESSION["guserid"] . "',
            '','$Inv_id','A','" . $_SESSION['ChangePlanId'] . "')";
            $result = @mysqli_query($conn, $sqlPayment) or die(mysqli_error($conn));

            header("Location:plan_upgrade_success.php");
            exit();
        }//end if
        else {
            $message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
            // $message="Please view 'My Garage' for details.";
        }//end else
        // End of the transaction  for adding a user since payment is successfull
    }//end if
}//end if




include_once('./includes/title.php');
if ($txtPayMethod == 'sp') {
    ?>
    <script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<?php } ?>


<script language="javascript" type="text/javascript">

    function clickConfirm(submitForm = 1)
    {
        var frm = document.frmBuy;
        var flag = false;

        if(frm.txtFirstName.value.length==0)
        {
            alert("<?php echo ERROR_EMPTY_FIRST_NAME; ?>");
            frm.txtFirstName.focus();
            return false;
        }//end if
        else if(frm.txtLastName.value.length==0)
        {
            alert("<?php echo ERROR_EMPTY_LAST_NAME; ?>");
            frm.txtLastName.focus();
            return false;
        }//end if
        else if (frm.txtccno.value.length==0 || frm.txtMM.value.length==0 || frm.txtYY.value.length==0)
        {
            alert('<?php echo ERROR_CREDIT_CARD_DETAILS_INVALID; ?>');
        }//end if
        else if(frm.txtAddress.value.length==0)
        {
            alert("<?php echo ERROR_EMPTY_ADDRESS; ?>");
            frm.txtAddress.focus();
            return false;
        }//end if
        else if(frm.txtCity.value.length==0)
        {
            alert("<?php echo ERROR_EMPTY_CITY; ?>");
            frm.txtCity.focus();
            return false;
        }//end if
        else if(frm.txtState.value.length==0)
        {
            alert("<?php echo ERROR_EMPTY_STATE; ?>");
            frm.txtState.focus();
            return false;
        }//end if
        else if(frm.txtPostal.value.length==0)
        {
            alert("<?php echo ERROR_EMPTY_ZIP; ?>");
            frm.txtPostal.focus();
            return false;
        }//end if
        else if(frm.cmbCountry.value.length==0)
        {
            alert("<?php echo ERROR_EMPTY_COUNTRY; ?>");
            frm.cmbCountry.focus();
            return false;
        }//end if
        else if(frm.txtEmail.value.length==0)
        {
            alert("<?php echo ERROR_EMAIL_EMPTY; ?>");
            frm.txtEmail.focus();
            return false;
        }//end if
        else
        {
            flag = true;
        }//end else

        if (flag==true)
        {
            document.frmBuy.postback.value='Y';
            document.frmBuy.method='post';
            if(submitForm){
                document.frmBuy.submit();
            }else {
                return true;
            }
        }//end if
    }//end function

    function checkValue(t)
    {
        if(isNaN(t.value) || t.value.substring(0,1)==" " || t.value.length==0 || parseFloat(t.value) < 0 )
        {
            if(t.name=="txtccno")
            {
                t.value="";
            }//end if
            else
            {
                t.value="000";
            }//end else
        }//end if
    }//end function
</script>



<?php
if ($txtPayMethod == 'sp') {

    if(DisplayLookUp('stripedemo')=="YES"){ ?>
        <script>
            Stripe.setPublishableKey("<?php echo DisplayLookUp('stripepublic'); ?>");
        </script>
        <?php
    }
    if(DisplayLookUp('stripedemo')=="NO"){  
        ?>
        <script>
            Stripe.setPublishableKey("<?php echo DisplayLookUp('stripepubliclive'); ?>");
        </script>
        <?php
    }
    ?>

    <script>
        //callback to handle the response from stripe
        function stripeResponseHandler(status, response) {
            if (response.error) {

                //display the errors on the form
                $jqr("#error-message").html(response.error.message).show();
            } else {
                //get token id
                var token = response['id'];
                
                //insert the token into the form
                $jqr("#frmStripePayment").append("<input type='hidden' name='token' value='" + token + "' />");
                //submit form to the server
                $jqr("#frmStripePayment").submit();
            }
        }

        function stripePay() {
            //e.preventDefault();
            var valid = clickConfirm(0);

            if(valid == true) {

                Stripe.createToken({
                    number: $jqr( "#txtccno" ).val(),
                    cvc: $jqr( "#txtcvv2").val(),
                    exp_month: $jqr( "#txtMM" ).val(),
                    exp_year: $jqr( "#txtYY" ).val(),
                }, stripeResponseHandler);

                //submit from callback
                return false;
            }
        }
    </script>
    <?php 
}
?>


<body onLoad="timersOne();">
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
                   <div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>
                   <div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
                      <?php
                      if ($txtPayMethod != 'gc') {
                         ?>
                         <form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" id="<?php echo ($txtPayMethod == 'sp')? 'frmStripePayment':''; ?>">

                             <input type="hidden" name="postback" id="postback" value="">
                             <input type="hidden" name="amnt" id="amnt" value="<?php echo  $amount ?>">
                             <input type="hidden" name="id" id="id" value="<?php echo  $id ?>">
                             <input type="hidden" name="txtPayMethod" value="<?php echo $txtPayMethod; ?>">

                             <?php
                             if ($message == false && $message != '') {
                                ?>
                                <div class="row warning"><?php echo $message; ?></div>
                                <?php
                            }
                            if ($cc_flag == false && $cc_err != '') {
                                ?>
                                <div class="row warning"><?php echo $cc_err; ?></div>
                            <?php }?>


                            <h3 class="subheader row"><?php echo HEADING_PURCHASE_DETAILS; ?></h3>
                            <div class="row main_form_inner">
                                <label><?php echo TEXT_PLAN_NAME; ?></label>
                                <?php echo $PlanName ?>
                            </div>
                            <div class="row main_form_inner">
                                <label><?php echo TEXT_AMOUNT; ?></label>
                                <?php echo CURRENCY_CODE; ?><?php echo  $amount ?>
                            </div>

                            <h3 class="subheader row"><?php echo TEXT_CREDIT_CARD_DETAILS; ?></h3>

                            <div class="row main_form_inner">
                                <label><?php echo TEXT_FIRST_NAME; ?> <span class="warning">*</span></label>
                                <input type="text" name="txtFirstName" id="txtFirstName" value="<?php echo $arrUserDetls['vFirstName']; ?>" size="24" maxlength="40" class="form-control">
                            </div>
                            <div class="row main_form_inner">
                                <label><?php echo TEXT_LAST_NAME; ?> <span class="warning">*</span></label>
                                <input type="text" name="txtLastName" id="txtLastName" value="<?php echo $arrUserDetls['vLastName']; ?>" size="24" maxlength="40" class="form-control">
                            </div>

                            <div class="row main_form_inner">
                                <label><?php echo TEXT_CARD_NUMBER; ?> <span class="warning">*</span></label>
                                <div class="col-lg-10 col-sm-12 col-md-10 col-xs-12 row">
                                   <input type="text" name="txtccno" class="form-control" id="txtccno"  size="24" maxlength="16" onBlur="javascript:checkValue(this);">
                               </div>
                               <div class="col-lg-2 col-sm-12 col-md-10 col-xs-12">
                                   <img src="<?php echo $imagefolder ?>/images/visa_amex.gif">
                               </div>
                           </div>

                           <div class="row main_form_inner">
                            <label><?php echo TEXT_CARD_VALIDATION_CODE; ?> <span class="warning">*</span></label>
                            <input type="password" name="txtcvv2" class="form-control" id="txtcvv2" size=10 maxlength="4" onBlur="javascript:checkValue(this);">
                            <a href="http://www.cvvnumber.com/cvv.html" target="_blank" style="font-size:11px">What is my CVV code?</a>
                        </div>

                        <div class="row main_form_inner">
                            <label><?php echo TEXT_EXPIRATION_DATE; ?><span class="warning">*</span></label>
                            <div class="col-lg-6 col-sm-12 col-md-12 col-xs-12 row">
                               <input type="text" name="txtMM" class="form-control" id="txtMM" size=3 maxlength="2">
                           </div>
                           <div align="center" class="col-lg-2 col-sm-12 col-md-12 col-xs-12 row">/</div>
                           <div class="col-lg-6 col-sm-12 col-md-12 col-xs-12 row">
                               <input type="text" name="txtYY" class="form-control" id="txtYY" size=4 maxlength="4">
                           </div>
                       </div>

                       <h3 class="subheader row"><?php echo TEXT_BILLING_ADDRESS_DETAILS; ?></h3>

                       <div class="row main_form_inner">
                        <label><?php echo TEXT_ADDRESS; ?> <span class="warning">*</span></label>
                        <input type="text" name="txtAddress" class="form-control" id="txtAddress" size=24 maxlength=30 value="<?php
                        echo $arrUserDetls['vAddress1'];
                        if ($arrUserDetls['vAddress2'] != '') {
                           echo ',' . $arrUserDetls['vAddress2'];
                       }
                       ?>">
                   </div>
                   <div class="row main_form_inner">
                    <label><?php echo TEXT_CITY; ?> <span class="warning">*</span></label>
                    <input type="text" name="txtCity" class="form-control" id="txtCity" size=24 maxlength="30" value="<?php echo $arrUserDetls['vCity']; ?>">
                </div>
                <div class="row main_form_inner">
                    <label><?php echo TEXT_STATE; ?> <span class="warning">*</span></label>
                    <input type="text" name="txtState" class="form-control" id="txtState" size=24 maxlength=30 value="<?php echo $arrUserDetls['vState']; ?>">
                </div>
                <div class="row main_form_inner">
                    <label><?php echo TEXT_ZIP; ?> <span class="warning">*</span></label>
                    <input type="text" name="txtPostal" class="form-control" id="txtPostal" size=24 maxlength=10 value="<?php echo $arrUserDetls['nZip']; ?>">
                </div>
                <div class="row main_form_inner">
                    <label><?php echo TEXT_COUNTRY; ?> <span class="warning">*</span></label>
                    <select name="cmbCountry" class="form-control">
                     <?php include('./includes/country_select.php'); ?>
                 </select>
             </div>
             <div class="row main_form_inner">
                <label><?php echo TEXT_EMAIL; ?> <span class="warning">*</span></label>
                <input type="text" name="txtEmail" class="form-control" id="txtEmail" size=24 maxlength=50  value="<?php echo $arrUserDetls['vEmail']; ?>">
            </div>
            <div class=" main_form_inner">
                <label><input type="button" name="btPay" id="btPay" class="subm_btt"  value="<?php echo BUTTON_PAY_NOW; ?>" onClick="javascript:<?php echo ($txtPayMethod != 'sp')?'clickConfirm();':'stripePay()'?>"></label>
            </div>


        </form>
        <?php
    }
    ?>
</div>
<div class="col-lg-2 col-sm-12 col-md-1 col-xs-12"></div>	

<div class="clear"></div>					

<div class="col-lg-12 col-sm-12 col-md-12">
  <div class="subbanner"><?php include('./includes/sub_banners.php'); ?></div>
</div>

</div>					
</div>
</div>  
</div>
</div>

<?php require_once("./includes/footer.php"); ?>