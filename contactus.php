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
include("./languages/".$_SESSION['lang_folder']."/user.php");//language file
include_once('./includes/gpc_map.php');

include_once('./includes/title.php');


//send mail to admin
if (isset($_POST['btnSubmit']) && $_POST['btnSubmit'] != '') {
    if (function_exists('get_magic_quotes_gpc')) {
        $txtName = stripslashes($_POST['txtName']);
        $txtEmail = stripslashes($_POST['txtEmail']);
        $txtAddress = stripslashes($_POST['txtAddress']);
        $txtPhone = stripslashes($_POST['txtPhone']);
        $txtMsg = stripslashes($_POST['txtMsg']);
        $txtSecurity = stripslashes($_POST['txtSecurity']);
    }//end if
    else {
        $txtName = $_POST['txtName'];
        $txtEmail = $_POST['txtEmail'];
        $txtAddress = $_POST['txtAddress'];
        $txtPhone = $_POST['txtPhone'];
        $txtMsg = $_POST['txtMsg'];
        $txtSecurity = $_POST['txtSecurity'];
    }//end else

    if (($_SESSION['captchastr'] == $txtSecurity && $_SESSION['captchastr'] != '') ||
            ($_SESSION['captchastr_low'] == strtolower($txtSecurity) && $_SESSION['captchastr_low'] != '')) {
        if (DisplayLookUp('4') != '') {
            $admin_email = DisplayLookUp('4');
        }//end if

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
                   AND C.content_name = 'contactus'
                   AND C.content_type = 'email'
                   AND L.lang_id = '".$_SESSION["lang_id"]."'";
        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $mainTextShow   = $mailRw['content'];

        $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{txtName}","{txtEmail}","{txtAddress}","{txtPhone}","{txtMsg}");
        $arrTReplace	= array(SITE_NAME,SITE_URL,$txtName,$txtEmail,nl2br(htmlentities(stripslashes($txtAddress))),$txtPhone, nl2br(htmlentities($txtMsg)));
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

        $mailcontent1   = $mainTextShow;

        $subject    = $mailRw['content_title'];
        $subject    = str_replace('{txtName}',$txtName,$subject);

        $StyleContent=MailStyle($sitestyle,SITE_URL);

        $EMail = $admin_email;
        //$EMail = $txtEmail;
        //readf file n replace
        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Administrator', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
        $msgBody    = file_get_contents('./languages/'.$langRw["folder_name"].'/mail.html');
        $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);


        send_mail($EMail, $subject, $msgBody, $txtEmail, $txtName);
        $message = MESSAGE_THANKYOU;
        //clear captcha session
        $_SESSION['captchastr_low'] = '';
        $_SESSION['captchastr'] = '';
    }//end captcha cheking
    else {
        $msg = ERROR_SECURITYCODE_INVALID;
    }//end else
}//end if
?>
<script language="javascript" type="text/javascript">
    function validateContactForm()
    {
        var frm = window.document.frmContact;

        if(trim(frm.txtName.value)=="")
        {
            alert("<?php echo ERROR_NAME_EMPTY; ?>");
            frm.txtName.focus();
            return false;
        }//end if
        else if(trim(frm.txtEmail.value)=="")
        {
            alert("<?php echo ERROR_EMAIL_EMPTY; ?>");
            frm.txtEmail.focus();
            return false;
        }//end else if
        else if(!checkMail(trim(frm.txtEmail.value)))
        {
            alert("<?php echo ERROR_EMAIL_INVALID; ?>");
            frm.txtEmail.select();
            frm.txtEmail.focus();
            return false;
        }//end else if
        else if(trim(frm.txtMsg.value)=="")
        {
            alert("<?php echo ERROR_MESSAGE_EMPTY; ?>");
            frm.txtMsg.focus();
            return false;
        }//end else if
        else if(trim(frm.txtSecurity.value)=="")
        {
            alert("<?php echo ERROR_SECURITYCODE_EMPTY; ?>");
            frm.txtSecurity.focus();
            return false;
        }//end else if
        return true;
    }//end function
</script>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
	
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			
			<div class="col-lg-12">
				
				
				
				<div class="clearfix">
					<div class="clearfix contact-form-section">
                        <?php $content_arr = ContentLookUp('contact'); ?>
                <div class="clearfix innersubheader">
                    <h4><?php echo $content_arr['content_title']; ?></h4>
                </div>
                <br>
					
					<?php if ($message == ''){ ?>
					<div class="contact-form-content">
						<?php echo $content_arr['content']; ?>
					</div>
				<?php } ?>
				
					
					<form name="frmContact" method="post" action="" onSubmit="return validateContactForm();">

						<?php
																	if (isset($message) && $message != '') {
																		?>
							<div class="alert alert-success"><?php echo $message; ?></div>
						 <?php
							}//end if
							else {
								if (isset($msg) && $msg != '') {
									echo ' <div class="alert alert-warning">' . $msg . '</div>';
								}//end if
							?>   
                            <div class="text-right mandatory"><?php echo TEXT_MANDATORY_FIELDS; ?></div>
                <br>                  
						<div class="row">	
                            <div class="col-lg-6">										 
        						<div class="contact-form-tile">
        							<label><?php echo TEXT_NAME; ?> <span class="warning">*</span></label>
        							<input type="text"  class="form-control"  name="txtName" size="40" maxlength="100" value="<?php echo htmlentities($txtName); ?>" />
        						</div>
        						<div class="contact-form-tile">
        							<label><?php echo TEXT_EMAIL; ?> <span class="warning">*</span></label>
        							<input type="text" class="form-control" name="txtEmail" size="40" maxlength="100" value="<?php echo htmlentities($txtEmail); ?>" />
        						</div>
                                <div class="contact-form-tile">
                                   
                                    <label><?php echo TEXT_PHONE; ?></label>
                                    <input type="text" class="form-control"  name="txtPhone" size="40" maxlength="100" value="<?php echo htmlentities($txtPhone); ?>" />
                                    
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="contact-form-tile">
                                    <label><?php echo TEXT_ADDRESS; ?></label>
                                    <textarea  name="txtAddress" cols="35" rows="8" class="form-control"><?php echo htmlentities($txtAddress); ?></textarea>
                                </div>
                            </div>
                        </div>
						
						
						<div class="contact-form-tile">
							<label><?php echo TEXT_COMMENT; ?> <span class="warning">*</span></label>
							<textarea class="form-control"  name="txtMsg" rows="8" cols="40"><?php echo htmlentities($txtMsg); ?></textarea>
						</div>
						<div class="contact-form-tile">
                            <div class="clearfix">
							<label><?php echo TEXT_SECURITYCODE; ?> <span class="warning">*</span></label>
                        </div>
							<div class="col-lg-2 col-sm-3 col-md-2 col-xs-4 no_padding marg_B_five"><img src="<?php echo 'captcha.php'; ?>"></div>
							<div class="col-lg-5 col-sm-9 col-md-9 col-xs-8 no_padding captchacode">
							<input name="txtSecurity" type="text" class="form-control" size="40" maxlength="10">
							</div>
						</div>
						<div class="clearfix main_form_inner">
                            <br>
							<label>
                                <div class="text-center">
								<input type="submit" name="btnSubmit" value="<?php echo BUTTON_SEND; ?>" class="subm_btt"/>
								<!--<input type="reset" name="btnReset" value="<?php echo BUTTON_RESET; ?>" class="submit" />-->
                            </div>
							</label>
						</div>
						<?php }//end else ?>	
					</form>
					</div>				
				</div>				
				<div class="row subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>
			</div>
			
		</div>  
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>

