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

if (isset($_SESSION["guserid"]) && ($_SESSION["guserid"] != "")) {
    header('Location:usermain.php');
    exit();
}
$flag1 = false;
$act = $_GET["act"];

if ($act == "post") {

    $sql = "Select vLoginName,vEmail,vPassword from " . TABLEPREFIX . "users where  vEmail='" . $_POST["txtEmail"] . "'";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $email = $row["vEmail"];
        $password = $row["vPassword"];
        $vloginname = $row["vLoginName"];

        /*
        * Fetch user language details
        */

        $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
        $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
        $langRw = mysqli_fetch_array($langRs);

        /*
        * Fetch email contents from content table
        */
       $mailSql = "SELECT L.content,L.content_title
                  FROM ".TABLEPREFIX."content C
                  JOIN ".TABLEPREFIX."content_lang L
                    ON C.content_id = L.content_id
                   AND C.content_name = 'forgotpass'
                   AND C.content_type = 'email'
                   AND L.lang_id = '".$_SESSION["lang_id"]."'";
        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $mainTextShow   = $mailRw['content'];
        $reset_link     = '<a href="' . SITE_URL . '/resetpass.php?id=' . $_POST["txtEmail"] . '&p=' . $password . '">' . SITE_URL . '/resetpass.php?id=' . $_POST["txtEmail"] . '&p=' . $password . '</a>';

        $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{reset_link}");
        $arrTReplace	= array(SITE_NAME,SITE_URL,$reset_link);
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

        $mailcontent1   = $mainTextShow;

        $subject    = $mailRw['content_title'];

        $StyleContent=MailStyle($sitestyle,SITE_URL);

        $EMail = $admin_email;
        
        //readf file n replace
        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, $vloginname, $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
        $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
        $msgBody    = str_replace($arrSearch, $arrReplace, $msgBody);
        send_mail($email, $subject, $msgBody, SITE_EMAIL, 'Admin');

        $message = str_replace('{email}',$_POST["txtEmail"],TEXT_MAIL_SENT_TO_ENABLE_PASSWORD_RESET);
        $flag1 = true;
    }//end if
    else {
        $message = ERROR_EMAIL_INVALID;
        $flag1 = false;
    }//end else 
}//end if


include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript" src="js/qTip.js"></script>
<script language="javascript" type="text/javascript">
    function Validate()
    {
        var frm=document.frmPassword;

        if(trim(frm.txtEmail.value)=="")
        {
            alert("<?php echo ERROR_EMAIL_EMPTY; ?>");
            frm.txtEmail.focus();
            return false;
        }//end if
        else if(!checkMail(trim(frm.txtEmail.value)))
        {
            alert("<?php echo ERROR_EMAIL_INVALID; ?>");
            frm.txtEmail.select();
            frm.txtEmail.focus();
            return false;
        }//end else if
        else
        {
            document.frmPassword.submit()
        }//end else
    }//end funciton
</script>

<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"></div>
                <div class="col-lg-6">
					
					<div class="row innersubheader">
                    	
                    </div>
					
					<?php if ($_SESSION["guserid"] == "") {
						include_once("./login_box.php");
					} ?>
					<div class="">
						<div class="login-form-page-inner">
                            <h4><?php echo TEXT_FORGOT_PASSWORD; ?></h4>
						<form name="frmPassword" method ="POST" action ="<?php echo $_SERVER['PHP_SELF'] . "?act=post" ?>">
							<?php
								if (isset($message) && $message != '') {
                                                                 if($flag1 == false){   
									?>
									<div class=" warning"><?php echo $message; ?></div>
								<?php } else if($flag1 == true){?>
                                                                <div class="row success"><?php echo $message; ?></div>           
                                                                <?php }}?>
                                                                <br>			
							
							<div class=" main_form_inner">
								<label><b><?php echo TEXT_ENTER_EMAIL_REGISTRATION; ?></b></label>
							</div>
							
							<div class=" main_form_inner">
								<label><?php echo TEXT_EMAIL; ?></label>
								<input type="text" name="txtEmail" class="textbox2 form-control" size="30" title="<?php echo TEXT_EMAIL; ?>" id="txtEmail">
							</div>
							<div class=" main_form_inner">
								<label>
									<!--<input type="reset" name="btnReset" value="<?php echo BUTTON_RESET; ?>"  class="submit">&nbsp;&nbsp;&nbsp;-->
									<input type="button" name="btnGetpass" class="subm_btt" value="<?php echo BUTTON_RESET_PASSWORD; ?>" onClick="return Validate();">
								</label>
							</div>							
						</form>
						</div>				
					</div>					
                	<div class="row">
						<?php include('./includes/sub_banners.php'); ?>
                    </div>
                </div>
            	<div class="col-lg-3"></div>
            </div>  
        </div>
</div>
 
<?php require_once("./includes/footer.php"); ?>