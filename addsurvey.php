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

$var_userid = $_SESSION["guserid"];

$errsflag=0;

if (DisplayLookUp('9') != '') {
    $sur_amnt = DisplayLookUp('9');
}//end if

if (DisplayLookUp('10') != '') {
    $reg_amnt = DisplayLookUp('10');
}//end if

settype($sur_amnt, double);
settype($reg_amnt, double);

if ($_POST["postback"] == "Y") {

    $email_ids = "";
    $addflag = true;

    //validation
    for ($i = 1; $i <= 3; $i++) {
        if ($_POST["chk$i"] == $i) {
            $email_ids .= "'" . addslashes($_POST["txtEmail$i"]) . "',";
        }//end if
    }//end for loop

    $email_ids = substr($email_ids, 0, -1);
    //end of validation list builiding

    $sql = "Select distinct vEmail from " . TABLEPREFIX . "referrals where vEmail IN($email_ids)";
    $result_chk = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result_chk) > 0) {  //if any of the email addresses already present
        $addflag = false;
        $message = "";
        while ($row = mysqli_fetch_array($result_chk)) {
            $message .= $row["vEmail"] . ",";
        }//end while loop

        $message = substr($message, 0, -1);
        $message .= ERROR_ALREADY_REFERRED . "<br>&nbsp;<br>";
        $errsflag=1;
    }//end if
    else {
        $sql = "Select distinct vEmail from " . TABLEPREFIX . "users where vEmail IN($email_ids) and vDelStatus='0'";
        $result_chk = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result_chk) > 0) {
            $addflag = false;
            $message = "";
            while ($row = mysqli_fetch_array($result_chk)) {
                $message .= $row["vEmail"] . ",";
            }//end while
            $message = substr($message, 0, -1);
            $message .= ERROR_ALREADY_IN_SYSTEM . "<br>&nbsp;<br>";
            $errsflag=1;
        }//end if
      /*  else {
            $sql = "Select distinct vEmail from " . TABLEPREFIX . "users where vEmail IN($email_ids) and vDelStatus='0'";
            $result_chk = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result_chk) > 0) {
                $addflag = false;
                $message = "";
                while ($row = mysqli_fetch_array($result_chk)) {
                    $message .= $row["vEmail"] . ",";
                }//end while

                $message = substr($message, 0, -1);
                $message .= ERROR_ALREADY_IN_SYSTEM . "<br>&nbsp;<br>";
            }//end if
        }//end else*/
    }//end if


    if ($addflag == true) {  // if valid to add the referrals
        for ($i = 1; $i <= 3; $i++) {
            if ($_POST["chk$i"] == $i) {
                $sql = "INSERT INTO `".TABLEPREFIX."referrals` (nRefId,nUserId,vName,vAddress,vPhone,vFax,vEmail,nSurveyAmount,nRegAmount) Values('',";
                $sql .= "'" . $var_userid . "',";
                $sql .= "'" . addslashes($_POST["txtName$i"]) . "',";
                $sql .= "'" . addslashes($_POST["txtAddress$i"]) . "',";
                $sql .= "'" . addslashes($_POST["txtPhone$i"]) . "',";
                $sql .= "'" . addslashes($_POST["txtFax$i"]) . "',";
                $sql .= "'" . addslashes($_POST["txtEmail$i"]) . "',";
                $sql .= "'" . $sur_amnt . "',";
                $sql .= "'" . $reg_amnt . "')";
                
                mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $var_insertedid = mysqli_insert_id($conn);                               
                
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
                AND C.content_name = 'addsurvey'
                AND C.content_type = 'email'
                AND L.lang_id = '".$_SESSION["lang_id"]."'";
                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                $mailRw  = mysqli_fetch_array($mailRs);


                $mainTextShow   = $mailRw['content'];

                $linkf ="<a href='".SITE_URL."/survey.php?refid=".$var_insertedid."'>Here</a>";
                $linkr ="<a href='".SITE_URL."/register.php?refid=".$var_insertedid."'>register</a>";
                
                $urlf = SITE_URL."/survey.php?refid=".$var_insertedid;
                $urlr = SITE_URL."/register.php?refid=".$var_insertedid;

                $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{LINK_F}","{LINK_R}","{URL_F}","{URL_R}");
                $arrTReplace	= array(SITE_NAME,SITE_URL,$linkf,$linkr,$urlf,$urlr);
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent1   = $mainTextShow;

                $subject        = $mailRw['content_title'];
                $subject        = str_replace('{SITE_NAME}',SITE_NAME,$subject);

                $StyleContent   = MailStyle($sitestyle,SITE_URL);

                $EMail = $_POST["txtEmail$i"];

                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}","{SITE-NAME}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL,SITE_NAME, htmlentities($_POST["txtName$i"]), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody    = str_replace($arrSearch, $arrReplace, $msgBody);

                
                send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
            }//end if(post == i)
        }//end for

        for ($i = 1; $i <= 3; $i++) {
            $str = "txtName" . $i;
            $$str = "";
            $str = "txtAddress" . $i;
            $$str = "";
            $str = "txtPhone" . $i;
            $$str = "";
            $str = "txtFax" . $i;
            $$str = "";
            $str = "txtEmail" . $i;
            $$str = "";
        }//end for loop

        $message = "<font color='green'>" . MESSAGE_REFERRAL_ADDED . "&nbsp;<br></font>";
        $errsflag=0;
    }//end if(addflag == true)
    else {
        for ($i = 1; $i <= 3; $i++) {
            $str = "txtName" . $i;
            $$str = $_POST["txtName$i"];
            $str = "txtAddress" . $i;
            $$str = $_POST["txtAddress$i"];
            $str = "txtPhone" . $i;
            $$str = $_POST["txtPhone$i"];
            $str = "txtFax" . $i;
            $$str = $_POST["txtFax$i"];
            $str = "txtEmail" . $i;
            $$str = $_POST["txtEmail$i"];
        }//end for loop
    }//end if
}//end if

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function checkMail(email)
    {
        var str1=email;
        var arr=str1.split('@');
        var eFlag=true;
        if(arr.length != 2)
        {
            eFlag = false;
        }
        else if(arr[0].length <= 0 || arr[0].indexOf(' ') != -1 || arr[0].indexOf("'") != -1 || arr[0].indexOf('"') != -1 || arr[1].indexOf('.') == -1)
        {
            eFlag = false;
        }
        else
        {
            var dot=arr[1].split('.');
            if(dot.length < 2)
            {
                eFlag = false;
            }
            else
            {
                if(dot[0].length <= 0 || dot[0].indexOf(' ') != -1 || dot[0].indexOf('"') != -1 || dot[0].indexOf("'") != -1)
                {
                    eFlag = false;
                }

                for(i=1;i < dot.length;i++)
                {
                    if(dot[i].length <= 0 || dot[i].indexOf(' ') != -1 || dot[i].indexOf('"') != -1 || dot[i].indexOf("'") != -1 || dot[i].length > 4)
                    {
                        eFlag = false;
                    }
                }
            }
        }
        return eFlag;
    }

    function checkForValues() {

        /*for(i=1;i<=3;i++) {
            if(eval("document.frmSurvey.txtEmail" + i + ".value.length") <= 0) {
                flag = false;
            }
            else if(eval("document.frmSurvey.txtName" + i + ".value.length") <= 0) {
                flag = false;
            }
            else if(eval("document.frmSurvey.txtAddress" + i + ".value.length") <= 0){
                flag = false;
            }
        }
        */
        var str1='~',str2='`',str3='^';
        flag = false;
        for(i=1;i<=3;i++) {
            try{
                if(eval("document.frmSurvey.chk" + i + ".checked") == true) {
                    if(eval("document.frmSurvey.txtEmail" + i + ".value.length") <= 0) {
                        flag = false;
                    }
                    else if(eval("document.frmSurvey.txtName" + i + ".value.length") <= 0) {
                        flag = false;
                    }
                    else if(eval("document.frmSurvey.txtAddress" + i + ".value.length") <= 0){
                        flag = false;
                    }
                    else {
                        flag = true;
                        eval("str" + i + "=document.frmSurvey.txtEmail" + i + ".value");
                    }
                }
                /*if(flag == true) {

                }
                else {
                    break;
                }*/

            }
            catch(e){}
        }
        if(flag == true) {
            if(str1 == str2 || str1 == str3 || str2 == str3) {
                alert('<?php echo ERROR_EMAIL_NOT_UNIQUE; ?>');
                flag = false;
            }
        }
        else {
            alert('<?php echo ERROR_ATLEAST_ONE_REFERRAL_REQUIRED; ?>');
        }


        return flag;
    }

    function checkValue(t) {
        if(checkMail(t.value) == false) {
            alert('<?php echo ERROR_EMAIL_INVALID; ?>');
            t.value='';
        }

        if(t.value.length > 0) {
            for(i=1;i<=3;i++) {
                if(t.id == ('txtEmail' + i)) {
                    eval("document.frmSurvey.chk" + i + ".checked=true");
                }
            }
            /*if(t.id == 'txtEmail1') {
                document.frmSurvey.chk1.checked=true;
            }
            else if(t.id == 'txtEmail2') {
                document.frmSurvey.chk2.checked=true;
            }
            else if(t.id == 'txtEmail3') {
                document.frmSurvey.chk3.checked=true;
            }*/
        }
        else if(t.value.length <= 0) {
            for(i=1;i<=3;i++) {
                if(t.id == ('txtEmail' + i)) {
                    eval("document.frmSurvey.chk" + i + ".checked=false");
                }
            }

            /*if(t.id == 'txtEmail1') {
                document.frmSurvey.chk1.checked=false;
            }
            else if(t.id == 'txtEmail2') {
                document.frmSurvey.chk2.checked=false;
            }
            else if(t.id == 'txtEmail3') {
                document.frmSurvey.chk3.checked=false;
            }*/
        }

    }


    function clickSubmit() {
        var frm = document.frmSurvey;
        if(checkForValues() == true) {
            document.frmSurvey.postback.value="Y";
            document.frmSurvey.method="post";
            document.frmSurvey.action="addsurvey.php";
            document.frmSurvey.submit();
        }
    }

    function validate(evt){
        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;

        if(key == 8 || key == 127){
            theEvent.returnValue = true;
        }else{
            key = String.fromCharCode( key );
            var regex = /[0-9]|\.|\-/; 
            if(!regex.test(key)){
                theEvent.returnValue = false;
                if(theEvent.preventDefault) theEvent.preventDefault();
            }
        }
    }
</script>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>

    <div class="homepage_contentsec">
        <div class="container">
            <div class="row">
                <div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
                <div class="col-lg-9">
                    <div class="full-width">
                        <div class="col-lg-12">
                            <div class="innersubheader2">
                                <h3><?php echo TEXT_ADD_REFERRALS; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="full-width">
                        <div class="col-lg-12">
                            <div class="clear">&nbsp;</div>
                            <form  name="frmSurvey" method ="POST" action  = "">
                                <input type="hidden" name="postback" id="postback" value="">
                                <div class="table-responsive">
                                    <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="table table-bordered" >

                                        <tr align="center"  class="gray">
                                            <th width="7%" align="left"><?php echo TEXT_SLNO; ?></th>
                                            <th width="16%" align="left"><?php echo TEXT_NAME; ?></th>
                                            <th width="19%" align="left"><?php echo TEXT_ADDRESS; ?></th>              
                                            <th width="19%" align="left"><?php echo TEXT_EMAIL; ?></th>
                                            <th width="19%" align="left"><?php echo TEXT_SURVEY; ?></th>
                                            <th width="20%" align="left"><?php echo TEXT_REG; ?></th>
                                        </tr>
                                        <?php
                                        $sql = "Select vName,vAddress,vEmail,vSurveyStatus,vRegStatus from " . TABLEPREFIX . "referrals where nUserId='$var_userid'";
                                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                        $entries = 0;
                                        $survey_count = 0;
                                        $reg_count = 0;
                                        $total = 0;
                                        if (mysqli_num_rows($result) > 0) {
                                            $cnt = 1;
                                            while ($row = mysqli_fetch_array($result)) {
                                                $entries++;
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="center"><?php echo $cnt; ?></td>
                                                    <td><?php echo htmlentities($row["vName"]); ?></td>
                                                    <td><?php echo htmlentities($row["vAddress"]); ?></td>
                                                    <td><?php echo htmlentities($row["vEmail"]); ?></td>
                                                    <td><?php echo (($row["vSurveyStatus"] == "0") ? TEXT_NOT_DONE : TEXT_DONE); ?></td>
                                                    <td><?php echo (($row["vRegStatus"] == "0") ? TEXT_NOT_DONE : TEXT_DONE); ?></td>
                                                </tr>
                                                <?php
                                                if ($row["vSurveyStatus"] == "1") {
                                                    $survey_count++;
                                                }
                                                if ($row["vRegStatus"] == "1") {
                                                    $reg_count++;
                                                }

                                                $cnt++;
                                            }
                                        }
                                        ?>
                                    </table>
                                </div>
                                <div class="clear"></div>

                                <div class="full_width">
                                    <?php
                                    if (isset($message) && $message != '') {
                                        ?>
                                        <div class="full_width <?php if($errsflag==1){ ?>warning<?php }else{ ?>success <?php } ?>"><?php echo $message; ?><br><br></div>
                                        <?php
                                    }

                                    $var_sur_count = 0;
                                    $var_reg_count = 0;
                                    $var_sur_amnt = 0;
                                    $var_reg_amnt = 0;
                                    $var_sur_paid = 0;
                                    $var_reg_paid = 0;
                                    $sql = "Select nUserId,nSurveyCount,nRegCount,nSurveyAmount,nRegAmount,nSurveyPaid,nRegPaid from "
                                    . " " . TABLEPREFIX . "user_referral where nUserId='" . $var_userid . "'";
                                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                    if (mysqli_num_rows($result) > 0) {
                                        $row = mysqli_fetch_array($result);
                                        $var_sur_count = $row["nSurveyCount"];
                                        $var_reg_count = $row["nRegCount"];
                                        $var_sur_amnt = $row["nSurveyAmount"];
                                        $var_reg_amnt = $row["nRegAmount"];
                                        $var_sur_paid = $row["nSurveyPaid"];
                                        $var_reg_paid = $row["nRegPaid"];
                            }//end if
                            ?>			

                            <div class="table-responsive">
                                <table width="200" border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
                                    <tr class="gray">
                                        <th width="21%" align="left"><?php echo TEXT_SUCCESSFULL_SURVEYS; ?></th>
                                        <th width="30%" align="left"><?php echo  $var_sur_count ?></th>
                                        <th width="29%" align="left"><?php echo TEXT_SUCCESSFULL_REGISTRATIONS; ?></th>
                                        <th width="20%" align="left"><?php echo $var_reg_count ?></th>
                                    </tr>
                                    <tr>
                                        <td align="left"><?php echo TEXT_AMOUNT_PENDING; ?></td>
                                        <td align="left"><?php echo  ("$ " . $var_sur_amnt) ?></td>
                                        <td align="left"><?php echo TEXT_AMOUNT_PENDING; ?></td>
                                        <td align="left"><?php echo  ("$ " . $var_reg_amnt) ?></td>
                                    </tr>
                                    <tr>
                                        <td align="left"><?php echo TEXT_AMOUNT_PAID; ?></td>
                                        <td align="left"><?php echo  ("$ " . $var_sur_paid) ?></td>
                                        <td align="left"><?php echo TEXT_AMOUNT_PAID; ?></td>
                                        <td align="left"><?php echo  ("$ " . $var_reg_paid) ?></td>
                                    </tr>
                                </table>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>
                            <div class="col-lg-12">
                                <div class="">
                                    <div class="col-lg-12 col-sm-12 col-md-10 col-xs-12 form-section">
                                        <div class="row main_form_inner">
                                            <h4>
                                                <input type="checkbox" name="chk1" id="chk1" value="1"> <?php echo TEXT_REFERRAL; ?> - I
                                            </h4>
                                        </div>
                                        <div class="row main_form_inner">
                                            <div class="col-lg-6">
                                                <label><?php echo TEXT_NAME ?> <span class="warning">*</span></label>
                                                <input name="txtName1" type="text" class="comm_input form-control" id="txtName1" size="40" value="<?php echo  $txtName1 ?>" maxlength="100" autocomplete="off">
                                            </div>
                                            <div class="col-lg-6">
                                                <label><?php echo TEXT_ADDRESS; ?> <span class="warning">*</span></label>
                                                <input name="txtAddress1" type="text" class="comm_input form-control" id="txtAddress1" size="40"  value="<?php echo  $txtAddress1 ?>" maxlength="100" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="row main_form_inner">
                                            <div class="col-lg-6">
                                                <label><?php echo TEXT_PHONE; ?></label>
                                                <input name="txtPhone1" type="text" class="comm_input form-control" id="txtPhone1" size="40"  value="<?php echo  $txtPhone1 ?>" maxlength="20" onkeypress="validate(event);" autocomplete="off" />
                                            </div>
                                            <div class="col-lg-6">
                                                <label><?php echo TEXT_FAX; ?></label>
                                                <input name="txtFax1" type="text" class="comm_input form-control" id="txtFax1" size="40"  value="<?php echo  $txtFax1 ?>" maxlength="20" onkeypress="validate(event);" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="row main_form_inner">
                                            <div class="col-lg-6">
                                                <label><?php echo TEXT_EMAIL; ?> <span class="warning">*</span></label>
                                                <input name="txtEmail1" type="text" class="comm_input form-control" id="txtEmail1" size="40" maxlength="100"  value="<?php echo  $txtEmail1 ?>" onBlur="javascript:checkValue(this);" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="clear"></div>

                                        <div class="row main_form_inner">
                                            <h4>
                                              <input type="checkbox" name="chk2" id="chk2" value="2"> <?php echo TEXT_REFERRAL; ?> - II
                                          </h4>
                                      </div>												
                                      <div class="row main_form_inner">
                                        <div class="col-lg-6">
                                            <label><?php echo TEXT_NAME ?> <span class="warning">*</span></label>
                                            <input name="txtName2" type="text" class="comm_input form-control" id="txtName2" size="40"  value="<?php echo  $txtName2 ?>" maxlength="100" autocomplete="off">
                                        </div>
                                        <div class="col-lg-6">
                                            <label><?php echo TEXT_ADDRESS ?>  <span class="warning">*</span></label>
                                            <input name="txtAddress2" type="text" class="comm_input form-control" id="txtAddress2" size="40"  value="<?php echo  $txtAddress2 ?>" maxlength="100" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="row main_form_inner">
                                        <div class="col-lg-6">
                                            <label><?php echo TEXT_PHONE ?></label>
                                            <input name="txtPhone2" type="text" class="comm_input form-control" id="txtPhone2" size="40"  value="<?php echo  $txtPhone2 ?>" maxlength="20" onkeypress="validate(event);" autocomplete="off">
                                        </div>
                                        <div class="col-lg-6">
                                            <label><?php echo TEXT_FAX ?></label>
                                            <input name="txtFax2" type="text" class="comm_input form-control" id="txtFax2"  value="<?php echo  $txtFax2 ?>" size="40" maxlength="20" onkeypress="validate(event);" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="row main_form_inner">
                                        <div class="col-lg-6">
                                            <label><?php echo TEXT_EMAIL ?> <span class="warning">*</span></label>
                                            <input name="txtEmail2" type="text" class="comm_input form-control" id="txtEmail2" size="40" maxlength="100"  value="<?php echo  $txtEmail2 ?>"   onBlur="javascript:checkValue(this);" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="clear"></div>

                                    <div class="row main_form_inner">
                                        <h4>
                                          <input type="checkbox" name="chk3" id="chk3" value="3"> <?php echo TEXT_REFERRAL; ?> - III
                                      </h4>
                                  </div>
                                  <div class="row main_form_inner">
                                    <div class="col-lg-6">
                                        <label><?php echo TEXT_NAME ?> <span class="warning">*</span></label>
                                        <input name="txtName3" type="text" class="comm_input form-control" id="txtName3" size="40"  value="<?php echo  $txtName3 ?>" maxlength="100" autocomplete="off">
                                    </div>
                                    <div class="col-lg-6">
                                        <label><?php echo TEXT_ADDRESS ?> <span class="warning">*</span></label>
                                        <input name="txtAddress3" type="text" class="comm_input form-control" id="txtAddress3" size="40"  value="<?php echo  $txtAddress3 ?>" maxlength="100" autocomplete="off">
                                    </div>
                                </div>

                                <div class="row main_form_inner">
                                    <div class="col-lg-6">
                                        <label><?php echo TEXT_PHONE ?></label>
                                        <input name="txtPhone3" type="text" class="comm_input form-control" id="txtPhone3" size="40"  value="<?php echo  $txtPhone3 ?>" maxlength="20" onkeypress="validate(event);" autocomplete="off">
                                    </div>
                                    <div class="col-lg-6">
                                        <label><?php echo TEXT_FAX ?></label>
                                        <input name="txtFax3" type="text" class="comm_input form-control" id="txtFax3" size="40"  value="<?php echo  $txtFax3 ?>" maxlength="20" onkeypress="validate(event);" autocomplete="off">
                                    </div>
                                </div>

                                <div class="row main_form_inner">
                                    <div class="col-lg-6">
                                        <label><?php echo TEXT_EMAIL ?> <span class="warning">*</span></label>
                                        <input name="txtEmail3" type="text" class="comm_input form-control" id="txtEmail3" size="40" maxlength="100"  value="<?php echo  $txtEmail3 ?>"  onBlur="javascript:checkValue(this);" autocomplete="off">
                                    </div>
                                </div>												
                                <div class="row main_form_inner">
                                    <div class="col-lg-12">
                                        <label>														
                                          <?php
                                          $sql = "Select vLookUpDesc from " . TABLEPREFIX . "lookup where nLookUpCode = '15' and vLookUpDesc='1'";
                                          $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                          if (mysqli_num_rows($result) > 0) {
                                            $op = "";
                                            ?>
                                            <input name="btSubmit" type="button" id="btSubmit" value="<?php echo BUTTON_SUBMIT; ?>" onClick="alert('<?php echo ERROR_REFERRAL_DEACTIVATED; ?>');" class="subm_btt">
                                            <?php
                                        }
                                        else {
                                            ?>
                                            <input name="btSubmit" type="button" id="btSubmit" value="<?php echo BUTTON_SUBMIT; ?>" onClick="javascript:clickSubmit();" class="subm_btt">
                                            <?php
                                        }
                                        ?>
                                    </label>
                                </div>
                            </div>
                        </div>				
                    </div>
                </div>
                <div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>
            </div>
        </form>
    </div>
</div>
<div class="full-width subbanner">
    <div class="col-lg-12">
        <?php include('./includes/sub_banners.php'); ?>
    </div>
</div>

</div>

</div>
</div>
</div>


<?php require_once("./includes/footer.php"); ?>