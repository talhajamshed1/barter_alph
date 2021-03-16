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
include("./languages/" . $_SESSION['lang_folder'] . "/survey.php"); //language file
//include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

function get_client_ip() {
    // Get REMOTE_ADDR as the Client IP.
    $client_ip = (!empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( (!empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : $REMOTE_ADDR );

    // Check for headers used by proxy servers to send the Client IP. We should look for HTTP_CLIENT_IP before HTTP_X_FORWARDED_FOR.
    if ($_SERVER["HTTP_CLIENT_IP"])
        $proxy_ip = $_SERVER["HTTP_CLIENT_IP"];
    else if ($_SERVER["HTTP_X_FORWARDED_FOR"])
        $proxy_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

    // Proxy is used, see if the specified Client IP is valid. Sometimes it's 10.x.x.x or 127.x.x.x... Just making sure.
    if ($proxy_ip) {
        if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $proxy_ip, $ip_list)) {
            $private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.16\..*/', '/^10.\.*/', '/^224.\.*/', '/^240.\.*/');
            $client_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
        }
    }
    // Return the Client IP.
    return $client_ip;
}

if (isset($_GET["refid"]) and $_GET["refid"] != "") {
    $refid = $_GET["refid"];
}
else if (isset($_POST["refid"]) and $_POST["refid"] != "") {
    $refid = $_POST["refid"];
}

$flag1 = false;
$surveydone = 0;
$alreadyparticipated = 0;
if ($refid == "") {
    $message = ERROR_PLEASE_RETRY_USING_LINK_FROM_MAIL;
    $flag1 = false;
}
else {
    $sqlstat = "SELECT vSurveyStatus FROM " . TABLEPREFIX . "referrals WHERE nRefId = $refid ";
    $result = mysqli_query($conn, $sqlstat) or die(mysqli_error($conn)); 
    if (mysqli_num_rows($result) != 0) {
        $row = mysqli_fetch_array($result);
        $status = $row["vSurveyStatus"];
        if ($status == 0) {
            if (isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] != "") { 
                //print_r($_POST);
                $res = "";
                $ch1 = chr(177);
                $ch2 = chr(176);
                if ($_POST["rad1"] == 4) {
                    $res .= "4" . $ch2 . $_POST["txtComments"] . $ch1;
                }
                else {
                    $res .= $_POST["rad1"] . $ch1;
                }
                $res .= $_POST["rad2"] . $ch1 . $_POST["rad3"] . $ch1 . $_POST["rad4"] . $ch1;
                $res .= $_POST["rad5"] . $ch1 . $_POST["rad6"] . $ch1;
                $arr = $_POST["chk7"];
                $a = implode("_", $arr);
                $res .=$a . $ch1;
                $res .=$_POST["rad8"] . $ch1;
                $res .=$_POST["txtQuestions"];
                $sql = "SELECT nUserId, nSurveyAmount FROM " . TABLEPREFIX . "referrals WHERE nRefId='$refid' ";
                $resultuser = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if (mysqli_num_rows($resultuser) != 0) {
                    $rowuser = mysqli_fetch_array($resultuser);
                    $userreferred = $rowuser["nUserId"];
                    $amounttoadd = $rowuser["nSurveyAmount"];
                    $sql = "SELECT nUserId FROM " . TABLEPREFIX . "user_referral WHERE nUserId='" . $userreferred . "'";
                    $result_ur = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result_ur) > 0) {//Update the Survey status of user who has referred this...
                        $sql = "UPDATE " . TABLEPREFIX . "user_referral SET nSurveyCount = nSurveyCount + 1, nSurveyAmount= nSurveyAmount + $amounttoadd WHERE nUserId='" . $userreferred . "'";
                    }
                    else {//Insert into ".TABLEPREFIX."user_referral the Survey status of user who has referred this...
                        $sql = "Insert into " . TABLEPREFIX . "user_referral(nUserId, nSurveyCount, nSurveyAmount) values('"
                                . $userreferred . "', '1', '$amounttoadd')";
                    }
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    //now enter the survey answer into the Referral Table
                    //$refip = $SERVER_ADDR;
                    $refip = get_client_ip();
                    $sqlupdatestat = "UPDATE " . TABLEPREFIX . "referrals SET vSurveyStatus ='1', dSurveyDate = now(), vSurveyAnswer='".addslashes($res)."', vSurveyIP='$refip'  WHERE";
                    $sqlupdatestat .=" nRefId = $refid ";
                    mysqli_query($conn, $sqlupdatestat) or die(mysqli_error($conn));
                    $message = MESSAGE_THANKYOU_PARTICIPATION_SURVERY;
                    $flag1 = true;
                    $surveydone = 1;
                }
            }
        }
        else {
            $message = ERROR_ALREADY_PARTICIPATED_SURVEY;
            $flag1 = false;
            $alreadyparticipated = 1;
        }
    }
}

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <script language="javascript" type="text/javascript">
        function validateForm()
        {
            var frm = window.document.frmSurvey;
            var errorlist= "<?php echo ERROR_FORM_SUBMISSION; ?>\n";
            var errorsfound = false;

            if(!anyOneSelected("rad1"))
            {
                errorlist += "* <?php echo str_replace('{question_no}','1',ERROR_QUESTION_MUST_ANSWERED); ?>\n";
                errorsfound = true;
            }

            if((getSelectedId("rad1")==4) && (frm.txtComments.value==""))
            {
                errorlist += "* <?php echo str_replace('{question_no}','1',ERROR_QUESTION_EMPTY_COMMENT); ?>\n";
                errorsfound = true;
            }

            if(!anyOneSelected("rad2"))
            {
                errorlist += "* <?php echo str_replace('{question_no}','2',ERROR_QUESTION_MUST_ANSWERED); ?>\n";
                errorsfound = true;
            }

            if(!anyOneSelected("rad3"))
            {
                errorlist += "* <?php echo str_replace('{question_no}','3',ERROR_QUESTION_MUST_ANSWERED); ?>\n";
                errorsfound = true;
            }

            if(!anyOneSelected("rad4"))
            {
                errorlist += "* <?php echo str_replace('{question_no}','4',ERROR_QUESTION_MUST_ANSWERED); ?>\n";
                errorsfound = true;
            }
            if(!anyOneSelected("rad5"))
            {
                errorlist += "* <?php echo str_replace('{question_no}','5',ERROR_QUESTION_MUST_ANSWERED); ?>\n";
                errorsfound = true;
            }
            if(!anyOneSelected("rad6"))
            {
                errorlist += "* <?php echo str_replace('{question_no}','6',ERROR_QUESTION_MUST_ANSWERED); ?>\n";
                errorsfound = true;
            }
            if(!anyOneSelected("chk7"))
            {
                errorlist += "* <?php echo str_replace('{question_no}','7',ERROR_QUESTION_MUST_ANSWERED); ?>\n";
                errorsfound = true;
            }
            if(!anyOneSelected("rad8"))
            {
                errorlist += "* <?php echo str_replace('{question_no}','8',ERROR_QUESTION_MUST_ANSWERED); ?>\n";
                errorsfound = true;
            }
            if(errorsfound==true)
            {
                alert(errorlist);
                return false;
            }
            else
            {
                return true;
            }
        }

        function getSelectedId(elname)
        {
            var frm = window.document.frmSurvey;
            var currentelement,oldelement;
            var varAnyOneSelected = true;
            for(i=0;i< frm.elements.length;i++)
            {
                var currentelement = frm.elements[i].name;
                if(currentelement.indexOf(elname) >= 0  )
                {
                    if(frm.elements[i].checked==true)
                    {
                        return(frm.elements[i].value);
                    }
                }
            }
            return -1;
        }

        function anyOneSelected(elname)
        {
            var frm = window.document.frmSurvey;
            var currentelement,oldelement;
            var varAnyOneSelected = true;
            for(i=0;i< frm.elements.length;i++)
            {
                var currentelement = frm.elements[i].name;
                if(currentelement.indexOf(elname) >= 0  )
                {
                    if(frm.elements[i].checked==true)
                    {
                        return true;
                    }
                }
            }
            return false;
        }
    </script>
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3">
				<?php include_once ("./includes/categorymain.php"); ?>
			</div>
			<div class="col-lg-9">			
				<div class="innersubheader">
					<h4><?php echo HEADING_SURVEY; ?></h4>
				</div>
				<?php if ($_SESSION["guserid"] == "") {
					include_once("./login_box.php");
				} ?>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<?php
						if ($surveydone != 1 and $alreadyparticipated != 1 and $refid != "") {
							?>
						<form name="frmSurvey" method ="POST" action = "">
							<input type="hidden" name="refid" value="<?php echo $refid; ?>">
							<input type="hidden" name="result" value="">
							<div class="row main_form_inner">
								<label><?php echo TEXT_SURVEY_INTRO; ?></label>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_QUESTION_1; ?> *</label>
								<div class="full_width">
									<input type="radio" name="rad1" value="1">&nbsp; <?php echo TEXT_DAILY; ?> <br>
									<input type="radio" name="rad1" value="2">&nbsp; <?php echo TEXT_WEEKLY; ?> <br>
									<input type="radio" name="rad1" value="3">&nbsp; <?php echo TEXT_NEVER; ?> <br>
									<input type="radio" name="rad1" value="4">&nbsp; <?php echo TEXT_OTHERS; ?>
									<INPUT class="form-control" style="width:40%;" type="text" name="txtComments" size="30" maxlength="100" ><br>
								</div>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_QUESTION_2; ?> *</label>
								<div class="full_width">
									<input type="radio" name="rad2" value="1">&nbsp; 0-1 <?php echo TEXT_TIMES; ?> <br>
									<input type="radio" name="rad2" value="2">&nbsp; 2-3 <?php echo TEXT_TIMES; ?> <br>
									<input type="radio" name="rad2" value="3">&nbsp; 3-4 <?php echo TEXT_TIMES; ?> <br>
									<input type="radio" name="rad2" value="4">&nbsp; <?php echo TEXT_NEVER; ?> <br>
								</div>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_QUESTION_3; ?> *</label>
								<div class="full_width">
									<input type="radio" name="rad3" value="1">&nbsp; <?php echo TEXT_YES; ?> <br>
									<input type="radio" name="rad3" value="2">&nbsp; <?php echo TEXT_NO; ?> <br>													
								</div>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_QUESTION_4; ?>  *</label>
								<div class="full_width">
									<input type="radio" name="rad4" value="1">&nbsp; <?php echo CURRENCY_CODE; ?> 15-20 <br>
									<input type="radio" name="rad4" value="2">&nbsp; <?php echo CURRENCY_CODE; ?> 25-30 <br>
									<input type="radio" name="rad4" value="3">&nbsp; <?php echo CURRENCY_CODE; ?> 35-40 <br>
									<input type="radio" name="rad4" value="4">&nbsp; <?php echo TEXT_OTHERS; ?> <br>													
								</div>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_QUESTION_5; ?> *</label>
								<div class="full_width">
									<input type="radio" name="rad5" value="1">&nbsp; <?php echo TEXT_YES; ?> <br>
									<input type="radio" name="rad5" value="2">&nbsp; <?php echo TEXT_NO; ?> <br>													
								</div>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_QUESTION_6; ?> *</label>
								<div class="full_width">
									<input type="radio" name="rad6" value="1">&nbsp; <?php echo TEXT_YES; ?> <br>
									<input type="radio" name="rad6" value="2">&nbsp; <?php echo TEXT_NO; ?> <br>
								</div>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_QUESTION_7; ?> *</label>
								<div class="full_width">
									<input type="checkbox" name="chk7[]" value="1">&nbsp; <?php echo TEXT_THROW_AWAY; ?> <br>
									<input type="checkbox" name="chk7[]" value="2">&nbsp; <?php echo TEXT_GIVE_AWAY; ?> <br>
									<input type="checkbox" name="chk7[]" value="3">&nbsp; <?php echo TEXT_DONATE_TO_CHARITY; ?> <br>
									<input type="checkbox" name="chk7[]" value="4">&nbsp; <?php echo TEXT_SELL_IT; ?> <br>													
								</div>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_QUESTION_8; ?> *</label>
								<div class="full_width">
									<input type="radio" name="rad8" value="1">&nbsp; <?php echo TEXT_YES; ?> <br>
									<input type="radio" name="rad8" value="2">&nbsp; <?php echo TEXT_NO; ?> <br>													
								</div>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_COMMENTS; ?></label>
								<div class="full_width">
									<textarea name="txtQuestions" class="form-control"></textarea>
								</div>
							</div>
							<div class="row main_form_inner">
								<label><?php echo TEXT_THANING_NOTE; ?></label>
							</div>
							<div class="row main_form_inner">
								<label><input type="submit" name="btnSubmit" onClick="return validateForm();" value="<?php echo BUTTON_SUBMIT_FORM; ?>" class="subm_btt"></label>
							</div>
						</form>
								<?php
							}
							else {
								?>
							<div class="row main_form_inner">
									
                                                                        <?php
								if (isset($message) && $message != '') {
                                                                 if($flag1 == false){   
									?>
									<label class="row warning"><?php echo $message; ?></label>
								<?php } else if($flag1 == true){?>
                                                               <label class="row success"><?php echo $message; ?></label>     
                                                                <?php }}?>
                                                                <br>
							</div>
						<?php }?>
						
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