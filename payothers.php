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

if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else
// get get variables
If ($_GET["id"] != "") {
    $id = $_GET["id"];
}//end if
else if ($_POST["id"] != "") {
    $id = $_POST["id"];
}//end else if
$id = $_SESSION["gtempid"];

$sql = "Select nAmount,vLoginName,vMethod from " . TABLEPREFIX . "users where nUserId='$id'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    if ($row = mysqli_fetch_array($result)) {
        $amount = $row["nAmount"];
        $var_credit_desc = $row["vLoginName"];
        $var_method = $row["vMethod"];
    }//end if
}//end if

$showPlanOrReg = '';

//if regmode is paid and escrow is disabled
//if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
if (DisplayLookUp('15') != '1' && DisplayLookUp('plan_system')=='yes') {
    switch ($_SESSION['sess_Plan_Mode']) {
        case "M":
            $year_show = TEXT_PER_MONTH;
            break;

        case "Y":
            $year_show = TEXT_PER_YEAR;
            break;

        case "F":
            $year_show = TEXT_FREE;
            break;
    }//end switch

    $amount = $_SESSION['sess_Plan_Amt'];
    $condReg = "where plan_id='" . $_SESSION['nPlanId'] . "' and lang_id = '" . $_SESSION['lang_id'] . "'";
    $PlanName = fetchSingleValue(select_rows(TABLEPREFIX . 'plan_lang', 'vPlanName', $condReg), 'vPlanName');
    $showPlanOrReg = '<tr bgcolor="#FFFFFF"><td align="left">'.TEXT_PLAN_NAME.' </td><td> <b>' . $PlanName . ' ( ' . $year_show . ' )</b></td></tr>';
}//end if

$disp_method = "";
$disp_method = get_payment_name($var_method);
if (isset($_POST["postback"]) && $_POST["postback"] == "Y") {
    $Name = $_POST["txtName"];
    $Bank = $_POST["txtBank"];
    $RefNo = $_POST["txtrefno"];
    $Email = $_POST["txtEmail"];

    $Month = $_POST["txtMM"];
    $Year = $_POST["txtYY"];
    $Date = $_POST["txtYY"] . "-" . $_POST["txtMM"] . "-" . $_POST["txtDD"];
    
    $cost = $amount;
    // Start of the transaction  for adding a user since payment is successfull
    // ////////////////////////////////////////////////////////////////////////////////////
    // check if txnid alredy there to prevent refresh
    $var_id = $_SESSION["gtempid"];
    $var_txnid = "";
    $var_amount = "";
    $var_txnid = "";
    $var_method = "";
    $var_login_name = "";
    $var_password = "";
    $var_first_name = "";
    $var_last_name = "";
    // here the transaction id has to be set that comes from the payment gateway
    // $var_txnid="$txnid";
    $sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
    $sql .= "vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
    $sql .= "vDescription  ,dDateReg   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee,nRefId
				from " . TABLEPREFIX . "users where nUserId='" . $var_id . "'";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    if (mysqli_num_rows($result) > 0) {
        if ($row = mysqli_fetch_array($result)) {
            // if you have data for the transaction
            $var_login_name = $row["vLoginName"];
            $var_password = $row["vPassword"];
            $var_first_name = $row["vFirstName"];
            $var_last_name = $row["vLastName"];
            $var_email = $row["vEmail"];
            $totalamt = $row["nAmount"];
            $paytype = $row["vMethod"];

            $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',
						vReferenceNo='" . addslashes($RefNo) . "',vName='" . addslashes($Name) . "',vBank='" . addslashes($Bank) . "',
						dReferenceDate='" . addslashes($Date) . "', vDelStatus = '0' WHERE nUserId='" . $row['nUserId'] . "'";
            @mysqli_query($conn, $sql) or die(mysqli_error($conn));

//            $var_new_id = @mysqli_insert_id($conn);
           $var_new_id = $row['nUserId'];
            $_SESSION["gtempid"] = "";


            /*
            * Fetch user language details
            */

            $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
            $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
            $langRw = mysqli_fetch_array($langRs);

            /*
            * Fetch email contents from content table
            */
            if ($approval_tag == "E") {
                $mailSql = "SELECT L.content,L.content_title
                  FROM ".TABLEPREFIX."content C
                  JOIN ".TABLEPREFIX."content_lang L
                    ON C.content_id = L.content_id
                   AND C.content_name = 'activationLinkOnRegister'
                   AND C.content_type = 'email'
                   AND L.lang_id = '".$_SESSION["lang_id"]."'";
            }else{
                $mailSql = "SELECT L.content,L.content_title
                  FROM ".TABLEPREFIX."content C
                  JOIN ".TABLEPREFIX."content_lang L
                    ON C.content_id = L.content_id
                   AND C.content_name = 'welcomeMailUser'
                   AND C.content_type = 'email'
                   AND L.lang_id = '".$_SESSION["lang_id"]."'";
            }
            $activate_link = '<a href="' . SITE_URL . '/activation.php?uid=' . $var_new_id . '&status=eactivate">Activate</a>';
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            if(!$_SESSION["tmp_pd"] || $_SESSION["tmp_pd"]==''){
                $mainTextShow = str_replace("Password", "", $mainTextShow);
                $mainTextShow = str_replace("{Password}", "", $mainTextShow);
            }

            $arrTSearch	    = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($row["vLoginName"]),$_SESSION["tmp_pd"],$activate_link );
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

            $StyleContent=MailStyle($sitestyle,SITE_URL);

            $EMail = $row["vEmail"];

            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');



            $var_admin_email = ADMIN_EMAIL;

            if (DisplayLookUp('4') != '') {
                $var_admin_email = DisplayLookUp('4');
            }//end if


            /*
            * Fetch email contents from content table
            */
            $mailRw = array();
                $mailSql = "SELECT L.content,L.content_title
                  FROM ".TABLEPREFIX."content C
                  JOIN ".TABLEPREFIX."content_lang L
                    ON C.content_id = L.content_id
                   AND C.content_name = 'registrationNotificationAdmin'
                   AND C.content_type = 'email'
                   AND L.lang_id = '".$_SESSION["lang_id"]."'";

            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($row["vLoginName"]),htmlentities($vFirstName),$row["vEmail"]);
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject        = $mailRw['content_title'];
            $subject        = str_replace('{SITE_NAME}',SITE_NAME,$subject);
            $StyleContent   = MailStyle($sitestyle,SITE_URL);
            $EMail          = $var_admin_email;
            $StyleContent   = MailStyle($sitestyle, SITE_URL);

            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
             
//            $_SESSION["gtempid"] = $_SESSION["guserid"];
            $_SESSION["gtempid"] = $var_new_id;

            $_SESSION["guserid"] = "";
            header("location:othersconfirm.php");
            exit();
        }//end if
    }//end if
    else {
        header("location:index.php?paid=yes");
        exit();
    }//end else
    // End of the transaction  for adding a user since payment is successfull
}//end if

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function varify()
    {
        reqd= document.frmBuy.quantityREQD.value;
        avail = document.frmBuy.quantityAVL.value;
        if(isNaN(reqd) || reqd.substring(0,1)==" " || reqd.length <= 0 || parseInt(reqd) > parseInt(avail) || parseInt(reqd) < 1)
        {
            alert("<?php echo ERROR_QUANTITY_INVALID; ?>");
            document.frmBuy.quantityREQD.value="1";
        }//end if
        else
        {
            document.frmBuy.quantityREQD.value=parseInt(reqd);
        }//end else
        document.frmBuy.total.value=parseInt(document.frmBuy.amount.value)*parseInt(document.frmBuy.quantityREQD.value);
    }//end function


    function proceed(cc)
    {
        if(parseInt(document.frmBuy.quantityREQD.value) > parseInt(document.frmBuy.quantityAVL.value))
        {
            alert("<?php echo ERROR_QUANTITY_INVALID; ?>");
        }//end if
        else
        {
            document.frmBuy.cctype.value=cc;
            document.frmBuy.submit();
        }//end else
    }//end funciton

    function clickConfirm()
    {
        if(document.frmBuy.txtName.value.length <= 0 ||  document.frmBuy.txtrefno.value.length <= 0 || document.frmBuy.txtMM.value.length <= 0 || parseInt(document.frmBuy.txtMM.value) > 12 || document.frmBuy.txtDD.value.length <= 0 || parseInt(document.frmBuy.txtDD.value) > 31 || document.frmBuy.txtYY.value.length <= 0)
        {
            alert('<?php echo ERROR_GIVEN_INFO_EMPTY_INVALID; ?>');
        }//end if
        else
        {
            document.frmBuy.postback.value='Y';
            document.frmBuy.method='post';
            document.frmBuy.submit();
        }//end else
    }//end funciton

    function checkValue(t)
    {
        if(t.value.length==0)
        {
            if(t.name=="txtccno")
            {
                t.value="";
            }//end if
            else
            {
                t.value="000";
            }//end else
        }//endif
    }//end funciton
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="headerbg"><?php require_once("./includes/header.php"); ?>
            <?php require_once("menu.php"); ?>
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="10%" height="688" valign="top"><?php include_once ("./includes/categorymain.php"); ?>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td id="leftcoloumnbtm"></td>
                                            </tr>
                                        </table></td>
                                    <td width="74%" valign="top">
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td class="link3">&nbsp;</td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="heading" align="left"><?php echo HEADING_PAYMENT_FORM; ?></td>
                                            </tr>
                                        </table>
                                        <table width="70%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="5" class="maintext2">
                                                                    <form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
                                                                        <input type="hidden" name="postback" id="postback" value="">
                                                                        <input type="hidden" name="amnt" id="amnt" value="<?php echo  $amount ?>">
                                                                        <input type="hidden" name="id" id="id" value="<?php echo  $id ?>">
                                                                        <?php
                                                                        if ($cc_flag == false && $cc_err != '') {
                                                                            ?>
                                                                            <tr bgcolor="#FFFFFF">
                                                                                <td colspan="2" align="center" class="warning"><?php echo $cc_err; ?></td>
                                                                            </tr>
                                                                            <?php
                                                                            }//end if
                                                                            if (isset($var_method) && $var_method == 'wt') {
                                                                                ?>
                                                                            <tr bgcolor="#FFFFFF">
                                                                                <td colspan="2" align="center" class="warning">
                                                                                <?php 
                                                                                $elink = '<a href="mailto:' . SITE_EMAIL . '">' . SITE_EMAIL . '</a>';
                                                                                echo "<br>".str_replace("{email_link}",$elink,str_replace("{site_name}",SITE_NAME,CONTACT_ADMIN_GET_ACCOUNT_NUMBER))."<br>"; 
                                                                                ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php }//end if?>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td colspan="2" align="left" class="subheader"><?php echo HEADING_REGISTRATION_DETAILS; ?></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td width="39%" align="left"><?php echo TEXT_DESCRIPTION; ?></td>
                                                                            <td width="61%" align="left"><?php echo TEXT_USER_REGISTRATION; ?></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td align="left"><?php echo TEXT_AMOUNT; ?></td>
                                                                            <td align="left"><?php echo CURRENCY_CODE; ?><?php echo  $amount ?></td>
                                                                        </tr>
                                                                        <?php echo $showPlanOrReg; ?>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td colspan="2" align="left" class="subheader"><?php echo TEXT_DETAILS; ?></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td align="left"><?php echo TEXT_NAME; ?> <span class="warning">*</span></td>
                                                                            <td align="left"><input type="text" name="txtName" id="txtName" value="" size="24" maxlength="40" class="textbox2"></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td align="left"><?php echo TEXT_BANK; ?> (<?php echo TEXT_IF_APPLICABLE; ?>)</td>
                                                                            <td align="left"><input type="text" name="txtBank" id="txtBank" value="" size="24" maxlength="40" class="textbox2"></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td align="left"><?php echo TEXT_REFERENCE_NUMBER; ?> <span class="warning">*</span></td>
                                                                            <td align="left"><input type="text" name="txtrefno" class="textbox2" id="txtrefno" size="24" maxlength="16"></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td align="left"><?php echo TEXT_PAYMENT_MODE; ?></td>
                                                                            <td align="left"><input type="text" name="txtpaymode" class="textbox2" id="txtpaymode" size=15 maxlength="20" value="<?php echo  $disp_method ?>" readonly></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td align="left"><?php echo TEXT_DATE; ?> (<?php echo TEXT_MM_DD_YYYY; ?>) <span class="warning">*</span></td>
                                                                            <td align="left"><input type="text" name="txtMM" class="textbox2" id="txtMM" size="3" maxlength="2"> /
                                                                                <input type="text" name="txtDD" class="textbox2" id="txtDD" size="3" maxlength="2"> /
                                                                                <input type="text" name="txtYY" class="textbox2" id="txtYY" size="4" maxlength="4"></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td align="left">&nbsp;</td>
                                                                            <td align="left"><input type="button" name="btConfirm" id="btConfirm" class="submit"  value="<?php echo BUTTON_CONFIRM; ?>" onClick="javascript:clickConfirm();"></td>
                                                                        </tr>
                                                                    </form>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                        </table>
										<?php include('./includes/sub_banners.php'); ?>
                                    </td>
                                </tr>
                            </table></td>
                    </tr>
                </table>
<?php require_once("./includes/footer.php"); ?>