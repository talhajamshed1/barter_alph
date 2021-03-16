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
include("./languages/".$_SESSION['lang_folder']."/user.php");//additional language file

include_once('./includes/gpc_map.php');

include_once('./includes/title.php');

if (isset($_POST['tellafrnd']) && $_POST['tellafrnd'] != '') {
    $txtSecurity = $_POST['txtSecurity'];
    if (($_SESSION['captchastr'] == $txtSecurity) && $_SESSION['captchastr'] != '' ||
            ($_SESSION['captchastr_low'] == $txtSecurity) && $_SESSION['captchastr_low'] != '') {
        if (function_exists('get_magic_quotes_gpc')) {
            //your name
            $from_name = stripslashes($_POST['txtYourname']);
            $from_email = stripslashes($_POST['txtYourEmail']);
            $comments = stripslashes($_POST['comments']);

            //friends name
            $fndname1 = stripslashes($_POST['txtFrndname1']);
            $fndname2 = stripslashes($_POST['txtFrndname2']);
            $fndname3 = stripslashes($_POST['txtFrndname3']);
            $fndname4 = stripslashes($_POST['txtFrndname4']);

            //friends email address
            $fndemail1 = stripslashes($_POST['txtFrndemail1']);
            $fndemail2 = stripslashes($_POST['txtFrndemail2']);
            $fndemail3 = stripslashes($_POST['txtFrndemail3']);
            $fndemail4 = stripslashes($_POST['txtFrndemail4']);
        }//end if
        else {
            //your name
            $from_name = $_POST['txtYourname'];
            $from_email = $_POST['txtYourEmail'];
            $comments = $_POST['comments'];

            //friends name
            $fndname1 = $_POST['txtFrndname1'];
            $fndname2 = $_POST['txtFrndname2'];
            $fndname3 = $_POST['txtFrndname3'];
            $fndname4 = $_POST['txtFrndname4'];

            //friends email address
            $fndemail1 = $_POST['txtFrndemail1'];
            $fndemail2 = $_POST['txtFrndemail2'];
            $fndemail3 = $_POST['txtFrndemail3'];
            $fndemail4 = $_POST['txtFrndemail4'];
        }//end else

        $EmalArray = array($fndemail1 => $fndname1, $fndemail2 => $fndname2, $fndemail3 => $fndname3, $fndemail4 => $fndname4);

        if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
            $from_name = $_SESSION["guserFName"];
            $from_email = $_SESSION["guseremail"];
        }//end if


        /*
        * Fetch user language details
        */

        $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
        $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
        $langRw = mysqli_fetch_array($langRs);

 
        $mailSql = "SELECT L.content,L.content_title
          FROM ".TABLEPREFIX."content C
          JOIN ".TABLEPREFIX."content_lang L
            ON C.content_id = L.content_id
           AND C.content_name = 'tellfrnd'
           AND C.content_type = 'email'
           AND L.lang_id = '".$_SESSION["lang_id"]."'";
        
        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $mainTextShow   = $mailRw['content'];

        $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{comments}");
        $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($comments));
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

        $mailcontent1   = $mainTextShow;

        $subject    = $mailRw['content_title'];

        $StyleContent=MailStyle($sitestyle,SITE_URL);   

        if (is_array($EmalArray)) {
            foreach ($EmalArray as $key => $val) {
                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, $val, $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);

                //open email text file for replace	
                $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                send_mail($key, $subject, $msgBody, $from_email, $from_name);
            }//end foreach
        }//end if

        $thanks = MESSAGE_EMAIL_SENT_THANKYOU_REFERING;
        //clear captcha session
        $_SESSION['captchastr_low'] = '';
        $_SESSION['captchastr'] = '';
    }//end if
    else {
        $message = ERROR_SECURITYCODE_INVALID;
    }//end else
}//end if
?>
<body onLoad="timersOne();">
    <script language="javascript" type="text/javascript">
        function validate(frmTellafriend)
        {
<?php
if (!isset($_SESSION['guserid']) && $_SESSION['guserid'] == '') {
    ?>
                        if(document.frmTellafriend.txtYourname.value=="")
                        {
                            alert("<?php echo ERROR_YOUR_NAME_EMPTY; ?>");
                            document.frmTellafriend.txtYourname.focus();
                            return false;
                        }//end if
                        if(document.frmTellafriend.txtYourEmail.value=="")
                        {
                            alert("<?php echo ERROR_YOUR_EMAIL_EMPTY; ?>");
                            document.frmTellafriend.txtYourEmail.focus();
                            return false;
                        }//end else if
                        if(!checkMail(document.frmTellafriend.txtYourEmail.value))
                        {
                            alert('<?php echo ERROR_YOUR_EMAIL_INVALID; ?>');
                            document.frmTellafriend.txtYourEmail.focus();
                            return false;
                        }//end else if
<?php }//end if ?>
                    if(document.frmTellafriend.txtFrndname1.value=="")
                    {
                        alert("<?php echo ERROR_NAME_EMPTY; ?>");
                        document.frmTellafriend.txtFrndname1.focus();
                        return false;
                    }//end else if
                    if(document.frmTellafriend.txtFrndemail1.value=="")
                    {
                        alert("<?php echo ERROR_EMAIL_EMPTY; ?>");
                        document.frmTellafriend.txtFrndemail1.focus();
                        return false;
                    }//end else if
                    if(!checkMail(document.frmTellafriend.txtFrndemail1.value))
                    {
                        alert('<?php echo ERROR_EMAIL_INVALID; ?>');
                        document.frmTellafriend.txtFrndemail1.focus();
                        return false;
                    }//end else if
                    if(document.frmTellafriend.txtFrndemail2.value!="")
                    {
                        if(!checkMail(document.frmTellafriend.txtFrndemail2.value))
                        {
                            alert('<?php echo ERROR_EMAIL_INVALID; ?>');
                            document.frmTellafriend.txtFrndemail2.focus();
                            return false;
                        }//end if
                        else if(document.frmTellafriend.txtFrndname2.value=="")
                        {
                            alert("<?php echo ERROR_NAME_EMPTY; ?>");
                            document.frmTellafriend.txtFrndname2.focus();
                            return false;
                        }//end else if
                    }//end else if
                    if(document.frmTellafriend.txtFrndemail3.value!="")
                    {
                        if(!checkMail(document.frmTellafriend.txtFrndemail3.value))
                        {
                            alert('<?php echo ERROR_EMAIL_INVALID; ?>');
                            document.frmTellafriend.txtFrndemail3.focus();
                            return false;
                        }//end if
                        else if(document.frmTellafriend.txtFrndname3.value=="")
                        {
                            alert("<?php echo ERROR_NAME_EMPTY; ?>");
                            document.frmTellafriend.txtFrndname3.focus();
                            return false;
                        }//end else if
                    }//end else if
                    if(document.frmTellafriend.txtFrndemail4.value!="")
                    {
                        if(!checkMail(document.frmTellafriend.txtFrndemail4.value))
                        {
                            alert('<?php echo ERROR_EMAIL_INVALID; ?>');
                            document.frmTellafriend.txtFrndemail4.focus();
                            return false;
                        }//end if
                        else if(document.frmTellafriend.txtFrndname4.value=="")
                        {
                            alert("<?php echo ERROR_NAME_EMPTY; ?>");
                            document.frmTellafriend.txtFrndname4.focus();
                            return false;
                        }//end else if
                    }//end else if
                    if(trim(document.frmTellafriend.txtSecurity.value)=="")
                    {
                        alert("<?php echo ERROR_SECURITYCODE_EMPTY; ?>");
                        document.frmTellafriend.txtSecurity.focus();
                        return false;
                    }//end else if
                    return true;
                }
    </script>
<?php include_once('./includes/top_header.php'); ?>
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
			<div class="col-lg-9">
				
				
				<div class="privacy-policy-section clearfix">
                    <div class="innersubheader">
                    <h4><?php echo HEADING_TELL_FRIEND; ?></h4>
                </div>
                <br>
					<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
						<form action="" method="post" name="frmTellafriend" onSubmit="return validate(this);">
							<?php
							if (isset($thanks) && $thanks != '') {
							?>
							<div class="row success"><?php echo $thanks; ?></div>
							<?php
							}//end if
							else {
								if (isset($message) && $message != '') {
							?>
							<div class="row warning"><?php echo $message; ?></div>
							<?php }//end if?>
							
							<div class="col-lg-1 col-sm-12 col-md-12 col-xs-2" align="center">
								<div class="tell_friend_head"><?php echo TEXT_SLNO; ?></div>
								<div class="tell_friend_inner">1</div>
								<div class="tell_friend_inner">2</div>
								<div class="tell_friend_inner">3</div>
								<div class="tell_friend_inner">4</div>
							</div>
							<div class="col-lg-6 col-sm-12 col-md-12 col-xs-5">
								<div class="tell_friend_head"><?php echo TEXT_NAME; ?></div>
								<div class="tell_friend_inner"><input name="txtFrndname1" type="text" class="textbox_contact_flsd form-control" id="txtFrndname1" size="30"></div>
								<div class="tell_friend_inner"><input name="txtFrndname2" type="text" class="textbox_contact_flsd form-control" id="txtFrndname2" size="30"></div>
								<div class="tell_friend_inner"><input name="txtFrndname3" type="text" class="textbox_contact_flsd form-control" id="txtFrndname3" size="30"></div>
								<div class="tell_friend_inner"><input name="txtFrndname4" type="text" class="textbox_contact_flsd form-control" id="txtFrndname4" size="30"></div>
							</div>
							<div class="col-lg-5 col-sm-12 col-md-12 col-xs-5">
								<div class="tell_friend_head"><?php echo TEXT_EMAIL; ?></div>
								<div class="tell_friend_inner"><input name="txtFrndemail1" type="text" class="textbox_contact_flsd form-control" id="txtFrndemail1" size="30"></div>
								<div class="tell_friend_inner"><input name="txtFrndemail2" type="text" class="textbox_contact_flsd form-control" id="txtFrndemail2" size="30"></div>
								<div class="tell_friend_inner"><input name="txtFrndemail3" type="text" class="textbox_contact_flsd form-control" id="txtFrndemail3" size="30"></div>
								<div class="tell_friend_inner"><input name="txtFrndemail4" type="text" class="textbox_contact_flsd form-control" id="txtFrndemail4" size="30"></div>
							</div>
							<?php
							if (!isset($_SESSION['guserid']) && $_SESSION['guserid'] == '') {
							?>
							<div class="main_form_inner">
								<label><?php echo TEXT_YOUR_NAME; ?> <span class="warning">*</span></label>
								<input name="txtYourname" type="text" class="textbox_contact_flsd form-control" size="50">
							</div>
							<div class="main_form_inner">
								<label><?php echo TEXT_YOUR_EMAIL; ?> <span class="warning">*</span></label>
								<input name="txtYourEmail" type="text" class="textbox_contact_flsd form-control" size="50">
							</div>
							<?php }//end if?>
																			
							<div class="main_form_inner">
								<label><?php echo TEXT_MESSAGE; ?> <span class="warning">*</span></label>
								<textarea cols="35" rows="5" class="form-control" name="comments"><?php echo TEXT_FOUND_SITE_YOU_LIKE; ?>:</textarea>
							</div>
							<div class="main_form_inner">
								<label><?php echo TEXT_SECURITYCODE; ?></label>
								<div class="col-lg-12 no_padding marg_B_five"><img src="<?php echo 'captcha.php'; ?>"></div>

								<div class="col-lg-12 no_padding">
									<input name="txtSecurity" type="text" class="form-control" size="40" maxlength="10">
								</div>
							</div>							
							<div class="main_form_inner clearfix">
                                <br>
								<label>
									<input type="submit" class="subm_btt" name="tellafrnd" value="<?php echo BUTTON_TELL_FRIENDS; ?>">
								</label>
							</div>
							<?php
							}//end else
							?>
						</form>
					</div>
				</div>
				
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>
			</div>
		</div>  
	</div>
</div>
    
<?php require_once("./includes/footer.php"); ?>