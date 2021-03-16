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
include "./includes/config.php";
include "./includes/session.php";
include "./includes/functions.php";
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include "./includes/logincheck.php";

include_once('./includes/gpc_map.php');

include_once('./includes/title.php');

if (isset($_SESSION["guserid"]) && ($_SESSION["guserid"] != "")) {
    header('Location:usermain.php');
    exit();
}
// referral redirection
/*if (isset($_SESSION["grefid"]) && $_SESSION["grefid"] != "") {
    header("location:registerme.php");
    exit();
}//end if*/
//referal source

if ($_GET["refid"] != "") {
    $_SESSION["grefid"] = $_GET["refid"];
    
}//end if
// $var_refid=NULL;
if($_SESSION["grefid"]){
    $var_refid      = $_SESSION["grefid"];
    $var_uid        = fetchSingleValue(select_rows(TABLEPREFIX . "referrals", "nUserId", "where nRefId='".$_SESSION["grefid"]."'"), "nUserId");
    $vAdvEmployee   = fetchSingleValue(select_rows(TABLEPREFIX . "users", "vLoginName", "where nUserId='".$var_uid."'"), "vLoginName");
    $refBy          = $var_refid;
}else{
    $vAdvEmployee = NULL;
    $refBy = NULL;
    $var_refid = 0;
}


$notregistered = "1";
/*if (get_magic_quotes_gpc()){
    $vLoginName = $_POST['vLoginName'];
    $vPassword = $_POST['vPassword'];
    $vEmail = $_POST['vEmail'];
}
else{*/
    $vLoginName = addslashes($_POST['vLoginName']);
    $vPassword = addslashes($_POST['vPassword']);
    $vEmail = addslashes($_POST['vEmail']);
    $nlstatus = $_POST["chkNewsletters"];
    if ($nlstatus) {
    	$nlstatus = "Y";
    }//end if
    else {
    	$nlstatus = "N";
    }
//}
if ($_POST["btnFreeSubmit"] != "" || $_POST["btnSubmit"] != ""){//if submitted


    $txtSecurity = $_POST['txtSecurity'];
    if (($_SESSION['captchastr'] == $txtSecurity && $_SESSION['captchastr'] != '') ||
            ($_SESSION['captchastr_low'] == strtolower($txtSecurity) && $_SESSION['captchastr_low'] != '')) {//if valid captcha
        
        $guseraffid = $_GET["guseraffid"];

        // check if user already exists
        $sqluserexists = "SELECT vLoginName FROM " . TABLEPREFIX . "users  WHERE vLoginName = '" . addslashes($vLoginName) . "' AND vDelStatus!='1'";
        $resultuserexists = mysqli_query($conn, $sqluserexists) or die(mysqli_error($conn));
        
        //check for duplicate email
        $sqlemailexists = "SELECT vEmail FROM " . TABLEPREFIX . "users  WHERE vEmail = '" . addslashes($vEmail) . "' AND vDelStatus!='1'";
        $resultemailexists = mysqli_query($conn, $sqlemailexists) or die(mysqli_error($conn));

        if (mysqli_num_rows($resultuserexists) > 0) {
            $message = ERROR_USERNAME_EXIST;
            $notregistered = "1";
            $msgClass   =   'error_msg';
        }//end if
        else if (mysqli_num_rows($resultemailexists) > 0) {
            $message = ERROR_EMAIL_EXIST;
            $notregistered = "1";
            $msgClass   =   'error_msg';
        } // if username valid
        else if (!isValidUsername($vLoginName)) {
            $message = ERROR_USERNAME_INVALID_NO_SPECIAL_CHARS;
            $notregistered = "1";
            $msgClass   =   'error_msg';
        }//end if
        else {
            $notregistered = "0";
        }
    }
    else {
        $message = ERROR_SECURITYCODE_INVALID;
        $notregistered = "1";
        $msgClass   =   'error_msg';
    }//end else

    
}

//Free Registration
if (isset($_POST["btnFreeSubmit"]) && $_POST["btnFreeSubmit"] != "" && $notregistered == "0") {
        $approval_tag = "0";

        if (DisplayLookUp('userapproval') != '') {
            $approval_tag = DisplayLookUp('userapproval');
        }//end if
        //approve by admin
        if ($approval_tag == "1") $vStatus = 1;
        else if ($approval_tag == "E") $vStatus = 4;
        else $vStatus = 0;

        // database entry
        $sql = "INSERT INTO " . TABLEPREFIX . "users(vLoginName,vPassword,vStatus,vEmail,dDateReg,vMethod,nAmount,vTxnId,vDelStatus,nRefId,vNLStatus,vAdvEmployee)";
        $sql .= " Values('" . addslashes($vLoginName) . "',";
        $sql .= "'" . md5(addslashes($vPassword)) . "',";
        $sql .= "'" . addslashes($vStatus) . "',";
        $sql .= "'" . addslashes($vEmail) . "',";
        $sql .= "now(),";
        $sql .= "'free','0','free','0',";
        $sql .= (empty($refBy)) ? "NULL," : "'" . $refBy . "',";
        $sql .= "'" . addslashes($nlstatus) . "',";
        $sql .= "'".$vAdvEmployee."')";                       
        
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $uid = mysqli_insert_id($conn);

        if($var_refid){
            //mysqli_query($conn, "update " . TABLEPREFIX . "referrals set vRegStatus = 0 where nRefId = '".$var_refid."'");
        }

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
        $activate_link = '<a href="' . SITE_URL . '/activation.php?uid=' . $uid . '&status=eactivate">Activate</a>';
        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $mainTextShow   = $mailRw['content'];

        $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
        $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($vLoginName),$vPassword,$activate_link );
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

        $mailcontent1   = $mainTextShow;

        $subject    = $mailRw['content_title'];
        $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

        $StyleContent   =  MailStyle($sitestyle,SITE_URL);
        
        $EMail = $vEmail;

        //readf file n replace
        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, addslashes($vLoginName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
        $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
        $msgBody    = str_replace($arrSearch, $arrReplace, $msgBody);
        
        

        send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

        $sql = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, 
                                                nSaleId) VALUES ('R', 'free', ' 0', 'free',now(), $uid, 0)";
		
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        // mail send to admin
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

        $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
        $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($vLoginName),htmlentities($vLoginName),$vEmail );
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

        $mailcontent1   = $mainTextShow;

        $subject    = $mailRw['content_title'];
        $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
        $StyleContent=MailStyle($sitestyle,SITE_URL);

        
        $EMail = $var_admin_email;    

        //readf file n replace
        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Administrator', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
        $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
        $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
        

        send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
        //clear captcha session
        $_SESSION['captchastr_low'] = '';
        $_SESSION['captchastr'] = '';
        unset($_SESSION["grefid"]);
        header("location:freereg.php?id=" . $uid);
        exit();
}//end if
// =======================/Free Registration============================
if (isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] != "" && $notregistered == "0") {
        $_SESSION['nPlanId'] = $_POST['ddlPlan'] ?? 0;
        $condReg = "where nPlanId='" . $_SESSION['nPlanId'] . "'";
        $PlanMode = fetchSingleValue(select_rows(TABLEPREFIX . 'plan', 'vPeriods', $condReg), 'vPeriods');
        $_SESSION['sess_Plan_Amt'] = fetchSingleValue(select_rows(TABLEPREFIX . 'plan', 'nPrice', $condReg), 'nPrice');
        $_SESSION['sess_Plan_Mode'] = $PlanMode;
        
        $isFree = 0; 
        if(DisplayLookUp('plan_system') == 'yes'){
            if($PlanMode == 'F'){
                $isFree = 1;
            }
        }else{
            if(DisplayLookUp('3') == '0'){
                $isFree = 1;
            }
        }

        //Either free plan OR registration fee is set as zero
        //if ($PlanMode == 'F' || DisplayLookUp('3') == '0') {
        if($isFree){
            $approval_tag = "0";

            if (DisplayLookUp('userapproval') != '') {
                $approval_tag = DisplayLookUp('userapproval');
            }//end if
            //approve by admin
            if ($approval_tag == "1") $vStatus = 1;
            else if ($approval_tag == "E") $vStatus = 4;
            else $vStatus = 0;

                // database entry
                $sql = "INSERT INTO " . TABLEPREFIX . "users(vLoginName,vPassword,vEmail,vStatus,vMethod,dDateReg,nAmount,vTxnId,vDelStatus,vNLStatus,nPlanId,nRefId,vAdvEmployee)";
                $sql .= " Values('" . addslashes($vLoginName) . "',";
                $sql .= "'" . md5(addslashes($vPassword)) . "',";
                $sql .= "'" . addslashes($vEmail) . "',";
                $sql .= "'".$vStatus."',";
                $sql .= "'free',";
                $sql .= "now(),";
                $sql .= "'0',";
                $sql .= "'free','0',";
                $sql .= "'" . addslashes($nlstatus) . "',";
                $sql .= "'" . $_SESSION['nPlanId'] . "','" . $var_refid . "','".$vAdvEmployee."')";
				
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $uid = mysqli_insert_id($conn);

            if($var_refid){
            mysqli_query($conn, "update " . TABLEPREFIX . "referrals set vRegStatus = 1 where nRefId = '".$var_refid."'");
            }

            //send mail to user
            $EMail = $vEmail;

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
            $activate_link = '<a style="color:black !important;" href="' . SITE_URL . '/activation.php?uid=' . $uid . '&status=eactivate">Activate</a>';
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($vLoginName),$vPassword,$activate_link );
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

            $StyleContent   =  MailStyle($sitestyle,SITE_URL);
            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, addslashes($vLoginName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

            $sql = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, 
                                                                nSaleId,vPlanStatus, nPlanId) VALUES ('R', 'free', ' 0', 'free',now(), $uid, 0,'A', '".$_SESSION['nPlanId']."')";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));

            // mail send to admin
            if (DisplayLookUp('4') != '') {
                $var_admin_email = DisplayLookUp('4');
            }//end if


            $EMail = $var_admin_email;

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

            $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
            $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($vLoginName),htmlentities($vLoginName),$vEmail );
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
            $StyleContent=MailStyle($sitestyle,SITE_URL);

            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Administrator', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
      
            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
            //clear captcha session
            $_SESSION['captchastr_low'] = '';
            $_SESSION['captchastr'] = '';
            unset($_SESSION["grefid"]);
            header("location:freereg.php?id=" . $uid);
        }//end if free plan mode check
        else {
            $approval_tag = "0";

            if (DisplayLookUp('userapproval') != '') {
                $approval_tag = DisplayLookUp('userapproval');
            }//end if
            //approve by admin
            if ($approval_tag == "1") $vStatus = 1;
            else if ($approval_tag == "E") $vStatus = 4;
            else $vStatus = 0;
            
            $sql = "INSERT INTO " . TABLEPREFIX . "users(vLoginName,vPassword,vEmail,dDateReg,vStatus,nPlanId,vDelStatus,nRefId,vNLStatus,vAdvEmployee)";
            $sql .= " VALUES ('" . addslashes($vLoginName) . "',
                        '" . md5(addslashes($vPassword)) . "',
                        '" . addslashes($vEmail) . "',
                        now(),'".$vStatus."','" . $_SESSION['nPlanId'] . "','0','" . $refBy . "','".$nlstatus."','".$vAdvEmployee."')";

            
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $sqluserid = mysqli_insert_id($conn);

            if($var_refid){
//            mysqli_query($conn, "update " . TABLEPREFIX . "referrals set vRegStatus = 1 where nRefId = '".$var_refid."'");
            }

            $notregistered = "0";
            $_SESSION["gtempid"] = $sqluserid;
            $_SESSION["guserpass"] = addslashes($vPassword);
            $_SESSION["tmp_pd"] = addslashes($vPassword);
            //clear captcha session
            $_SESSION['captchastr_low'] = '';
            $_SESSION['captchastr'] = '';
            unset($_SESSION["grefid"]);
            header("location:pay.php?id=" . $sqluserid);
        }//end else
        exit();
}//end if
?>
<script language="javascript" type="text/javascript">

    function validateRegistrationForm()
    {
        var frm = window.document.frmRegistration;

        if(trim(frm.vLoginName.value)==""){
            alert("<?php echo ERROR_USERNAME_EMPTY; ?>");
            frm.vLoginName.focus();
            return false;
        }
         if(trim(frm.vPassword.value)==""){
            alert("<?php echo ERROR_PASSWORD; ?>");
            frm.vPassword.focus();
            return false;
        }
        if(trim(frm.vPassword.value.length) < 6 ){
           alert("<?php echo ERROR_PASSWORD_SIX_CHAR; ?>");
           frm.vPassword.focus();
           return false;
        }
         if(trim(frm.vConfirmPassword.value)==""){
            alert("<?php echo ERROR_CONFIRM_PASSWORD; ?>");
            frm.vConfirmPassword.focus();
            return false;
        }
        if((frm.vConfirmPassword.value) != (frm.vPassword.value)){
           alert("<?php echo ERROR_PASSWORD_CONFIRM_PASSWORD; ?>");
           frm.vConfirmPassword.focus();
           return false;
        }	
        if(trim(frm.vEmail.value)==""){
            alert("<?php echo ERROR_EMAIL_EMPTY; ?>");
            frm.vEmail.focus();
            return false;
        }
        if(!checkMail(trim(frm.vEmail.value))){
           alert("<?php echo ERROR_EMAIL_INVALID; ?>");
           frm.vEmail.focus();
           return false;
        }
        
        if(trim(frm.txtSecurity.value)=="")
        {
            alert("<?php echo ERROR_SECURITYCODE_EMPTY; ?>");
            frm.txtSecurity.focus();
            return false;
        }//end if
        return true;
    }

        function confirmPassCheck(password)
        {
            var frm = window.document.frmRegistration;
            var eFlag = true;

            var str1 = frm.vPassword.value;
            var str2 = frm.vConfirmPassword.value;

            if((str1 != str2) && (str2 != ""))
            {
                document.getElementById("mismatchpass").innerHTML =  "<b><font color='red'><?php echo ERROR_MISMATCH; ?><\/font><\/b>";
            }//end if
            else if((str1 == str2) && (str1 != "") && (str2 != ""))
            {
                document.getElementById("mismatchpass").innerHTML =  "<b><font color='green'><?php echo ERROR_CORRECT; ?><\/font><\/b>";
            }//end else
        }//end function

</script>
<script language="javascript" type="text/javascript" src="./js/suggestMessages.js"></script>
<script language="javascript" type="text/javascript" src="js/screenname.js"></script>
<body>
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3 col-sm-12 col-md-3 col-xs-12"></div>
                <div class="col-lg-6">
                    <div class="row innersubheader">
                    	
                    </div>
                    
                    <div class=" login-form-page-inner">
                    	 <?php //include_once("./login_box.php"); ?>
						 <div class="">
                            <h4><?php echo HEADING_REGISTRATION_FORM; ?></h4>
						 	<div class="row mandatory"><?php echo TEXT_MANDATORY_FIELDS; ?></div>
							<form name="frmRegistration" method ="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" onSubmit="return validateRegistrationForm();">
                                                                   
							<?php
								if (isset($message) && $message != '') {
								?>
								<div class="row error_msg">
									<span class="glyphicon glyphicon-remove-circle"></span><?php echo $message; ?>
								</div>
								
							<?php }//end if ?>			
							
							
							
							<?php
                                                        
							//if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
							if(DisplayLookUp('15') != '1' ){
								if(DisplayLookUp('plan_system')=='yes'){
								?>		
								<div class=" main_form_inner">
									<label><?php echo TEXT_CHOOSE_PLAN; ?> </label>
									<div class="full_width">
									<select name="ddlPlan" class="textbox_contact">
									<?php
                                                                        $queryString = '' ;
                                                                        if(DisplayLookUp('15')==0)
                                                                        {
                                                                            //$queryString = 'AND P.vPeriods!="F" ';
                                                                        }
                                                                        
									$sqlPlan = mysqli_query($conn, "SELECT * FROM " . TABLEPREFIX . "plan P
													LEFT JOIN " . TABLEPREFIX . "plan_lang L on P.nPlanId = L.plan_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
													WHERE P.vActive='1' ".$queryString." order by P.nPosition ASC")
												 or die(mysqli_error($conn));
													if (mysqli_num_rows($sqlPlan) > 0) {
														while ($arrPlan = mysqli_fetch_array($sqlPlan)) {
															switch ($arrPlan['vPeriods']) {
																case "M":
																	$year = TEXT_PER_MONTH;
																	break;

																case "Y":
																	$year = TEXT_PER_YEAR;
																	break;

																case "F":
																	$year = TEXT_FREE;
																	break;
															}//end switch

															$shwSelcted = '';

															if ($arrPlan['nPlanId'] == $_POST['ddlPlan']) {
																$shwSelcted = 'selected="selected"';
															}//end if

															echo '<option value="' . $arrPlan['nPlanId'] . '" ' . $shwSelcted . '>' . $arrPlan['vPlanName'] . ' ( ' . CURRENCY_CODE . $arrPlan['nPrice'] . ' - ' . $year . ')</option>';
														}//end while loop
													}//end if
													?>
										</select>
										</div>
									</div>
								<?php
								}else{
								?>
							<div class=" main_form_inner">
								<label><?php echo TEXT_REGISTRATION." ".TEXT_AMOUNT; ?> </label>
								<label><?php echo CURRENCY_CODE.DisplayLookUp('3'); ?></label>
								<label><span id="txtHint" class="warning"></span></label>
							</div>
								<?php
								}
								}//end if
								?>
								
								
						 	<div class=" main_form_inner">
								<label><?php echo TEXT_USERNAME; ?> <span class="warning">*</span></label>
								<input type="text" class="form-control textbox_contact" name="vLoginName" size="40" maxlength="100" value="<?php echo $vLoginName; ?>" onKeyUp="showHint(this.value,'username');" onMouseDown="showHint(this.value,'username');">
								<span id="txtHint"><span class="warning"></span></span>
							</div>
						 	<div class=" main_form_inner">
								<label><?php echo TEXT_PASSWORD; ?> <span class="warning">*</span></label>
								<input type="password" class="form-control textbox_contact" name="vPassword" size="40" maxlength="100" value="" onFocus="javascript:toggleMsg('vPasswordY');" onBlur="javascript:toggleMsg('vPasswordY');">
								<span id="vPasswordY" class="warning" style="display: none; "><?php echo ERROR_PASSWORD_SIX_CHAR; ?></span>
							</div>
						 	<div class=" main_form_inner">
								<label><?php echo TEXT_CONFIRM_PASSWORD; ?> <span class="warning">*</span></label>
								<input type="password" class="form-control textbox_contact" name="vConfirmPassword" size="40" maxlength="100" value="" onFocus="javascript:toggleMsg('vConfirmPasswordY');" onBlur="javascript:toggleMsg('vConfirmPasswordY');" onKeyUp="confirmPassCheck(this);">
								<span id="vConfirmPasswordY" class="warning" style="display: none; "><?php echo ERROR_PASSWORD_SIX_CHAR; ?></span>
								<div id="mismatchpass"></div>
							</div>
						 	<div class=" main_form_inner">
								<label><?php echo TEXT_EMAIL; ?> <span class="warning">*</span></label>
								<input type="text" class="form-control textbox_contact" name="vEmail" size="40" maxlength="100" value="<?php echo $vEmail; ?>" onKeyUp="showHint(this.value,'screen');" onMouseDown="showHint(this.value,'screen');">
								<span id="txtHint"></span>
							</div>
							<div class=" main_form_inner">
								<label><input type = "checkbox" name="chkNewsletters">
								<?php echo TEXT_SUBSCRIBE_NEWSLETTER; ?>
								</label>
							</div>
						 	<div class=" main_form_inner">
								<label><?php echo TEXT_SECURITYCODE; ?></label>
								<div class="col-lg-4 col-sm-12 col-md-12 col-xs-12 no_padding marg_B_five"><img src="<?php echo 'captcha.php'; ?>"></div>
								<div class="col-lg-8 col-sm-12 col-md-12 col-xs-12 no_padding">
									<input name="txtSecurity" type="text" class="form-control textbox_contact" size="40" maxlength="10">
								</div>
							</div>
						 	<div class=" main_form_inner">
								<?php
								$sql = "Select vLookUpDesc from " . TABLEPREFIX . "lookup where nLookUpCode = '15' and vLookUpDesc='1'";
								$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
								if (mysqli_num_rows($result) > 0) {
								?>
									<input type='submit'  class='subm_btt' name='btnFreeSubmit' value="<?php echo stripslashes(BUTTON_REGISTER); ?>" />
								<?php } else { ?>
									<input type='submit' class='subm_btt' name='btnSubmit' value="<?php echo stripslashes(BUTTON_REGISTER); ?>" />
								<?php } ?>
                                
                                <label class="new-usr-label"><br><a href="./login.php">Already account ? Sign in</a></label>
							</div>
                                                                     
							</form>
						 </div>
						 
                                        
                    	
                    </div>
                	<div class="row">
                    	<?php include('./includes/sub_banners.php'); ?>
                    </div>
                </div>				
            	<div class="col-lg-3 col-sm-12 col-md-3 col-xs-12"></div>
            </div>  
        </div>
 </div>     
                
    
<?php require_once("./includes/footer.php"); ?>