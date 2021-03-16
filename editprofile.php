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
include ("./includes/session_check.php");

include_once('./includes/gpc_map.php');

if (isset($_POST["btnImageSubmit"]) && $_POST["btnImageSubmit"] != "") {
    if ($_FILES['txtPic']['name']=='' || $_FILES['txtPic']['size'] <= 0 || !isValidWebImageType($_FILES['txtPic']['type'],$_FILES['txtPic']['name'],$_FILES['txtPic']['tmp_name'])){
        $message = ERROR_INVALID_IMAGE;
        $class1 = "error_msg_outer";
        $class2 = "remove-circle";
    }
    else {
        @chmod('./pics/profile/',0777);
        @unlink('./pics/profile/'.$_POST['assignedname']);
        $img_name_arr = explode(".",$_FILES['txtPic']['name']);
        $assignedname = "profile_".time().".".$img_name_arr[(count($img_name_arr)-1)];
        @copy($_FILES['txtPic']['tmp_name'], "./pics/profile/" . $assignedname);
        @chmod("./pics/profile/$assignedname", 0777);
        resizeImg("./pics/profile/" . $assignedname, 200 ,200, false,100, 0,"");
        $sql = "UPDATE " . TABLEPREFIX . "users SET ";
        $sql .="profile_image = '" . addslashes($assignedname) . "' WHERE nUserId ='" . $_SESSION["guserid"] . "' ";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $message = ERROR_IMAGE_UPLOADED;
        $class1 = "success_msg_outer";
        $class2 = "ok";
    }
}
else if (isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] != "") {
    $vFirstName = $_POST["vFirstName"];
    $vLastName = $_POST["vLastName"];
    $txtUserLat = ($_POST["txtUserLat"])?$_POST["txtUserLat"]:"";
    $txtUserLng = ($_POST["txtUserLng"])?$_POST["txtUserLng"]:"";
    $vAddress1 = $_POST["vAddress1"];
    $vAddress2 = $_POST["vAddress2"];
    $vCountry = $_POST["vCountry"];

    $vCity = $_POST["vCity2"];
    $vState = $_POST["vState"];
    $nZip = $_POST["nZip"];
    $vPhone = $_POST["vPhone"];
    $vFax = $_POST["vFax"];
    $vEmail = $_POST["vEmail"];

    $vGender = $_POST["vGender"];
    $vUrl = $_POST["vUrl"];
    $vEducation = $_POST["vEducation"];
    $vDescription = $_POST["vDescription"];
    $vPaypalEmail = $_POST["txtPaypalEmail"];
    $stripe_pub_key = ($_POST["stripe_pub_key"])?$_POST["stripe_pub_key"]:"";
    $stripe_secret_key = ($_POST["stripe_secret_key"])?$_POST["stripe_secret_key"]:"";

    if (trim($vFirstName)!='') $_SESSION["guserFName"] = $vFirstName;

    $sql = "UPDATE " . TABLEPREFIX . "users SET ";
    $sql .= " vFirstName='" . addslashes($vFirstName) . "', vLastName= '" . addslashes($vLastName) . "' ,vAddress1 ='" . addslashes($vAddress1) . "' ,vAddress2 ='" . addslashes($vAddress2) . "' ,vCity='" . addslashes($vCity) . "'  , ";
    $sql .="vState = '" . addslashes($vState) . "' ,vCountry='" . addslashes($vCountry) . "'  ,nZip = '" . addslashes($nZip) . "' , vPhone = '" . addslashes($vPhone) . "' ,vFax = '" . addslashes($vFax) . "'  ,vEmail= '" . addslashes($vEmail) . "' ,vUrl = '" . addslashes($vUrl) . "' , vGender = '" . addslashes($vGender) . "' ,vEducation = '" . addslashes($vEducation) . "',
        vPaypalEmail='" . addslashes($vPaypalEmail) . "',";
    $sql.="latitude='".addslashes($txtUserLat)."' , longitude= '".addslashes($txtUserLng)."',"; 
    $sql .="vDescription = '" . addslashes($vDescription) . "' WHERE nUserId ='" . $_SESSION["guserid"] . "' ";
    @mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $message = MESSAGE_CHANGES_SAVED;
    $class1 = "success_msg_outer";
    $class2 = "ok";
}
else if (isset($_POST["btnPassSubmit"]) && $_POST["btnPassSubmit"] != "") {
    $txtOldPassword = stripslashes(trim($_POST["txtOldPassword"]));
    $txtNewPassword = stripslashes(trim($_POST["txtNewPassword"]));
    $txtConfirmNewPassword = stripslashes(trim($_POST["txtConfirmNewPassword"]));

    if ($txtOldPassword != "" AND $txtNewPassword != "" AND $txtConfirmNewPassword != "") {
        $sqluserdetails = "SELECT vPassword FROM " . TABLEPREFIX . "users
                                       WHERE nUserId = '" . $_SESSION["guserid"] . "' ";
        $resultuserdetails = @mysqli_query($conn, $sqluserdetails);
        if (@mysqli_num_rows($resultuserdetails) != 0) {
            $row = @mysqli_fetch_array($resultuserdetails);
            $password = $row["vPassword"];
            if (md5(addslashes($txtOldPassword)) == $password) {
                $sql = "UPDATE " . TABLEPREFIX . "users
                                        SET vPassword = '" . md5(addslashes($txtNewPassword)) . "'
                                        WHERE nUserId ='" . $_SESSION["guserid"] . "' ";
                @mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $message = MESSAGE_PASSWORD_CHANGED;
                $class1 = "success_msg_outer";
                $class2 = "ok";
            }
            else {
                $message = ERROR_INVALID_OLD_PASSWORD;
                $class1 = "error_msg_outer";
                $class2 = "remove-circle";
            }
        }
    }
}

$sqluserdetails = "SELECT * FROM " . TABLEPREFIX . "users  WHERE  nUserId  = '" . $_SESSION["guserid"] . "'";
$resultuserdetails = @mysqli_query($conn, $sqluserdetails) or die(mysqli_error($conn));
$rowuser = @ mysqli_fetch_array($resultuserdetails);

$vFirstName = $rowuser["vFirstName"];
$vLastName = $rowuser["vLastName"];
$vAddress1 = $rowuser["vAddress1"];
$vAddress2 = $rowuser["vAddress2"];
$txtUserLat = ($rowuser["latitude"]);
$txtUserLng = $rowuser["longitude"];
$vCity = $rowuser["vCity"];
$vState = $rowuser["vState"];
$nZip = $rowuser["nZip"];
$vPhone = $rowuser["vPhone"];
$vFax = $rowuser["vFax"];
$vEmail = $rowuser["vEmail"];
$vCountry = $rowuser["vCountry"];
$vGender = $rowuser["vGender"];
$vUrl = $rowuser["vUrl"];
$vEducation = $rowuser["vEducation"];
$vDescription = $rowuser["vDescription"];
$txtLoginName = $rowuser["vLoginName"];
$txtPaypalEmail = $rowuser["vPaypalEmail"];
$profile_image = $rowuser["profile_image"];
$stripe_secret_key = $rowuser["stripe_secret_key"];
$stripe_pub_key = $rowuser["stripe_pub_key"];

if ($profile_image=='' || !file_exists('./pics/profile/'.$profile_image)) {
    $assignedname = '';
    if ($vGender=='F')
        $profile_image = './images/nophoto_available_women.gif';
    else
        $profile_image = './images/nophoto_available_men.gif';
}
else {
    $assignedname = $profile_image;
    $profile_image = './pics/profile/'.$profile_image;
}
$available_image_name   = $profile_image;

// echo "<pre>";
// print_r(file_exists('./pics/profile/'.$profile_image));
// echo "<pre>";

//show paypal identity token
include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <script language="javascript" type="text/javascript">
        function loadFields(){
            var frm = window.document.frmProfile;
            var country ="<?php echo  htmlentities($vCountry) ?>";
            var gender ="<?php echo  htmlentities($vGender) ?>";
            var education = "<?php echo  htmlentities($vEducation) ?>";

            if(gender == ""){
                gender = "F";
            }
            if(education == ""){
                education = "GP";
            }
            if(country == ""){
                country = "United States";
            }

            for(i=0;i<frm.vCountry.options.length;i++){

                if(frm.vCountry.options[i].value == country){
                    frm.vCountry.options[i].selected=true;
                    break;
                }
            }

            for(i=0;i<frm.vGender.options.length;i++){
                if(frm.vGender.options[i].value == gender){
                    frm.vGender.options[i].selected=true;
                    break;
                }
            }
            if (frm.vEducation){
                for(i=0;i<frm.vEducation.options.length;i++){
                    if(frm.vEducation.options[i].value == education){
                        frm.vEducation.options[i].selected=true;
                        break;
                    }
                }
            }
        }
        function checkNewPasswordEntered(){
            var frm = window.document.frmChangePassword;
            if(frm.txtNewPassword.value == ""  || frm.txtConfirmNewPassword.value == ""){
                return false;
            }
            return true;
        }
        function isAnyPasswordEntered(){
            var frm = window.document.frmChangePassword;
            if(frm.txtOldPassword.value != ""  || frm.txtNewPassword.value != ""  || frm.txtConfirmNewPassword.value != ""){
                return true;
            }
            return false;
        }
        function isNewPasswordsValid(){
            var frm = window.document.frmChangePassword;
            if(frm.txtNewPassword.value.length < 6){
                alert("<?php echo ERROR_PASSWORD_SIX_CHAR; ?>");
                frm.txtNewPassword.focus();
                return false;
            }else if(frm.txtConfirmNewPassword.value != frm.txtNewPassword.value){
                alert("<?php echo ERROR_PASSWORD_CONFIRM_PASSWORD; ?>");
                frm.txtConfirmNewPassword.select();
                frm.txtConfirmNewPassword.focus();
                return false;
            }
            return true;
        }
        /*
         *else if(frm.txtOldPassword.value.length < 6){
                alert("<?php //echo ERROR_PASSWORD_SIX_CHAR; ?>");
                frm.txtOldPassword.focus();
                return false;
            }*/
        function validateChangePasswordForm(){
            var frm = window.document.frmChangePassword;
            //if(isAnyPasswordEntered()){
            if(frm.txtOldPassword.value == ""){
                alert("<?php echo ERROR_PASSWORD_EMPTY; ?>");
                frm.txtOldPassword.focus();
                return false;
            }else if(! checkNewPasswordEntered()){
                alert("<?php echo ERROR_ENTER_PASSWORDS; ?>");
                frm.txtNewPassword.focus();
                return false;
            }else if(!isNewPasswordsValid()){
                return false;
            }
            //}
            return true;
        }

        function validateProfileForm()
        {
            var frm = window.document.frmProfile;

            if(trim(frm.vFirstName.value) == ""){
                alert("<?php echo ERROR_EMPTY_FIRST_NAME; ?>");
                frm.vFirstName.focus();
                return false;
            }
            if(trim(frm.vAddress1.value) == ""){
                alert("<?php echo ERROR_EMPTY_ADDRESS1; ?>");
                frm.vAddress1.focus();
                return false;
            }
            /*if(trim(frm.vPhone.value) == ""){
                alert("<?php echo ERROR_EMPTY_PHONE; ?>");
                frm.vPhone.focus();
                return false;
            }*/
            if(trim(frm.vEmail.value) == ""){
                alert("<?php echo ERROR_EMAIL_EMPTY; ?>");
                frm.vEmail.focus();
                return false;
            }
            if(!checkMail(trim(frm.vEmail.value))){
                alert("<?php echo ERROR_EMAIL_INVALID; ?>");
                frm.vEmail.focus();
                return false;
            }
            return true;
        }

        function confirmPassCheck(password)
        {
            var frm = window.document.frmChangePassword;
            var eFlag = true;

            var str1 = frm.txtNewPassword.value;
            var str2 = frm.txtConfirmNewPassword.value;

            if((str1 != str2) && (str2 != ""))
            {
                document.getElementById("mismatchpass").innerHTML =  "<b><font color='red'><?php echo ERROR_MISMATCH; ?><\/font><\/b>";
            }
            else if((str1 == str2) && (str1 != "") && (str2 != ""))
            {
                document.getElementById("mismatchpass").innerHTML =  "<b><font color='green'><?php echo ERROR_CORRECT; ?><\/font><\/b>";
            }
        }

        function validateImageForm()
        {
            var frm = window.document.frmImage;

            if (frm.txtPic.value=='' && frm.exist_image.value==''){
                alert("<?php echo ERROR_IMAGE_REQUIRED; ?>");
                frm.txtPic.focus();
                return false;
            }
            return true;
        }

        function open_container(container_id){
            document.getElementById("table_image").style.display = 'none';
            document.getElementById("table_profile").style.display = 'none';
            document.getElementById("table_password").style.display = 'none';
            if(document.getElementById("table_settings"))
                document.getElementById("table_settings").style.display = 'none';
            document.getElementById(container_id).style.display = 'block';
        }

        function checkCountry(targ,selObj,restore){ //v3.0
            selCountry = selObj.options[selObj.selectedIndex].value;
            /*if(selCountry == 'Australia'){
                                document.getElementById("cntOthers").style.display = 'none';
                                document.getElementById("cntAus").style.display = 'block';
                        }
                        else {
                                document.getElementById("cntOthers").style.display = 'block';
                                document.getElementById("cntAus").style.display = 'none';

                        }*/
        }
    </script>
    
    <script language="javascript" type="text/javascript" src="./js/suggestMessages.js"></script>

    <?php include_once('./includes/top_header.php'); ?>
	
	<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
                <div class="col-lg-9">					
					<div class="innersubheader">
                    	<h4><?php echo HEADING_EDIT_PROFILE; ?></h4>
                    </div>
                    <br>
					
					<div class="profile-edit-section">
						
						<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 form-section">
							<?php //if (isset($message) && $message != '') { ?>
							<!--<div class="row warning"><?php //echo $message; ?></div>-->
							<?php //} ?>
                                                        <?php if (isset($message) && $message != '') { ?>
                                                        <div class="<?php echo $class1; ?>">
						             <span class="glyphicon glyphicon-<?php echo $class2; ?>"></span><?php echo $message; ?>
					                </div>
							<?php } ?>
							<div class="row warning" style="text-align: right;font-size: 13px;margin-bottom: 10px"><?php echo TEXT_MANDATORY_FIELDS; ?></div>
							<div class="editprofile_step_outer">
								<form name="frmImage" method ="POST" enctype="multipart/form-data" action = "<?php echo $_SERVER['PHP_SELF'] ?>" onSubmit="return validateImageForm();">
								<input type ="hidden" name="assignedname" value="<?php echo $assignedname; ?>" />
                                   <input type="hidden" name="exist_image" id="exist_image" value="<?php echo $available_image_name;?>">
								<div class="row main_form_inner hor_bar">
									<a href="#" class="btn-tggle" onClick="javascript:open_container('table_image');return false;" ><?php echo HEADING_EDIT_IMAGE; ?></a>
								</div>								
								<div class="sub_box clearfix" id="table_image" style="position:relative;display:<?php echo ($_POST['btnImageSubmit']!='')?'block':'none'; ?>;" >
                                    <a href="#" onClick="javascript:open_container('table_settings');return false;" class="login_btn comm_btn_orng_tileeffect2 width2 img-edit"><?php echo LINK_IMAGE_SETTING; ?></a>

                                    <div class="col-sm-12">
                                    <label style="display: block;"><?php echo TEXT_PROFILE_IMAGE; ?><span class='warning'>*</span></label><br>
                                </div>
									<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
										
                                            <img src="<?php echo $profile_image; ?>" alt=""  width="150" height="150"/>
									</div>
									<div class="col-lg-7 col-sm-9 col-md-8 col-xs-12">
										<div class="comm_div">
											<span class='warning'><i><?php echo ERROR_FILE_REQUIRED_FORMAT; ?></i></span>
										</div>
										<div class="comm_div">
											<div style="position:relative;">
                                                <a href="#" onClick="javascript:return false;"  class="login_btn comm_btn_orng_tileeffect2 width2"><button type="submit" value="<?php echo TEXT_CHOOSE_IMAGE; ?>"  height="21" class="btn btn-default btn-new">
                                                <?php echo TEXT_CHOOSE_IMAGE;?></button> </a>
												
												<input type="file" class="textbox2" name="txtPic" id="txtPic" size="1" onChange="javascript:document.getElementById('file_name').innerHTML=this.value;" style="position:absolute;top:0px;left:-30px;opacity:0;filter:alpha(opacity=0);" />
												<span id="file_name"></span>
											</div>
										</div>
										<div class="comm_div">
											<input type="submit" name="btnImageSubmit" value="<?php echo BUTTON_SAVE_IMAGE; ?>" class="login_btn width2">
										</div>		
										<div class="comm_div">
											<!-- <a href="#" onClick="javascript:open_container('table_settings');return false;" class="login_btn comm_btn_orng_tileeffect2 width2"><?php echo LINK_IMAGE_SETTING; ?></a> -->
										</div>							
									</div>
									
								</div>
								</form>
							</div>
							
							<div class="clear"></div>
							
							<div class="editprofile_step_outer">
								<form name="frmProfile" method ="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" onSubmit="return validateProfileForm();">
								<div class="row main_form_inner hor_bar">
									<a href="#" class="btn-tggle" onClick="javascript:open_container('table_profile');return false;"><?php echo HEADING_CONTACT_DETAILS; ?></a>
								</div>
								<div class="sub_box clearfix" id="table_profile" style="display:<?php echo ($_POST['btnSubmit']!='')?'block':'none'; ?>;">
									<div class="row main_form_inner">
										<label><?php echo TEXT_USERNAME; ?> <span class='warning'>*</span></label>
										<input type="text" class="comm_input form-control" name="vLoginName" size="40" maxlength="100" value="<?php echo $txtLoginName; ?>" readonly="true"/>
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_FIRST_NAME; ?> <span class='warning'>*</span></label>
										<input type="text" class="comm_input form-control" name="vFirstName" size="40" maxlength="100" value="<?php echo $vFirstName; ?>" />
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_LAST_NAME; ?></label>
										<input type="text" class="comm_input form-control" name="vLastName" size="40" maxlength="100" value="<?php echo $vLastName; ?>" />
									</div>
									<div class="row main_form_inner">
                                        <br>
										<label style="float: left;width: auto;font-size: 15px;"><?php echo SET_LOCATION; ?> <span class='warning'>*</span></label>
                                        <a href=""  id="update_location"><?php echo SET_LOCATION_HERE; ?></a>
										<input type="hidden"  id="user_lat1" name="txtUserLat" value="<?php echo $txtUserLat;?>"/><input type="hidden" value="<?php echo $txtUserLng;?>" id="user_lng1" name="txtUserLng"/>
									</div>
                                    <div class="row main_form_inner">
										<label><?php echo TEXT_ADDRESS_LINE1; ?> <span class='warning'>*</span></label>
										<input type="text" class="comm_input form-control" name="vAddress1" size="40" maxlength="100" value="<?php echo $vAddress1; ?>" />
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_ADDRESS_LINE2; ?></label>
										<input type="text" class="comm_input form-control" name="vAddress2" size="40" maxlength="100" value="<?php echo $vAddress2; ?>" />
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_COUNTRY; ?></label>
										<select name="vCountry" class="comm_input form-control"  onchange="checkCountry('parent',this,0)">
											<?php include('./includes/country_select.php'); ?>
										</select>
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_STATE; ?></label>
										<input type="text" class="comm_input form-control" name="vState" size="40" maxlength="100" value="<?php echo $vState; ?>" />
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_CITY; ?></label>
										<input type="text" class="comm_input form-control" name="vCity2" size="40" maxlength="100" value="<?php echo $vCity; ?>" />
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_ZIP; ?></label>
										<input type="text" class="comm_input form-control jQNumericOnly" name="nZip" size="40" maxlength="6" value="<?php echo $nZip; ?>" />
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_PHONE; ?></label>
										<input type="text" class="comm_input form-control jQNumericOnly" name="vPhone" size="40" maxlength="14" value="<?php echo $vPhone; ?>" />
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_FAX; ?></label>
										<input type="text" class="comm_input form-control jQNumericOnly" name="vFax" size="40" maxlength="50" value="<?php echo $vFax; ?>" />
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_EMAIL; ?> <span class='warning'>*</span></label>
										<input type="text" class="comm_input form-control" name="vEmail" size="40" maxlength="100" value="<?php echo $vEmail; ?>" />
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_URL; ?></label>
										<input type="url" class="comm_input form-control" name="vUrl" size="40" maxlength="100" value="<?php echo $vUrl; ?>" />
									</div>
									<div class="row main_form_inner">
										<label><?php echo TEXT_GENDER; ?></label>
										<?php
											$maleSelect = '';
											$femaleSelect = '';
											$vGender == 'M' ? $maleSelect = "selected=selected" : $femaleSelect = "selected=selected";
											?>
										<select class="comm_input form-control" name="vGender">
											<option value="F" <?php echo $femaleSelect ?> ><?php echo TEXT_FEMALE; ?></option>
											<option value="M" <?php echo $maleSelect ?> ><?php echo TEXT_MALE; ?></option>
										</select>
									</div>
                                                                    <div class="row main_form_inner" style="display: none;">
										<label><?php echo TEXT_STRIPE_PUBLIC; ?></label>
										<input type="text" class="comm_input form-control" name="stripe_pub_key" size="40" maxlength="100" value="<?php echo $stripe_pub_key; ?>"/>
									</div>
                                    <div class="row main_form_inner" style="display: none;">
										<label><?php echo TEXT_STRIPE_SECRET; ?></label>
										<input type="text" class="comm_input form-control" name="stripe_secret_key" size="40" maxlength="100" value="<?php echo $stripe_secret_key; ?>"/>
									</div>
									<?php
										//escropayements
									//if (DisplayLookUp('Enable Escrow') != 'Yes') {
										?>
									<div class="row main_form_inner">
										<label><?php echo TEXT_PAYPAL_EMAIL; ?></label>
										<input type="email" class="comm_input form-control" name="txtPaypalEmail" size="40" maxlength="100" value="<?php echo $txtPaypalEmail; ?>"/>
									</div>
									<?php //} ?>
                                                                    
                                                                        <div class="row main_form_inner">
										<label><?php echo TEXT_PAYPAL_MANDATORY_SETTINGS; ?></label>
										<div class="comm_input">
                                                                                    <b>Note:</b> While using Paypal for payments in eSwap, you need to configure the below mentioned settings in your Paypal account. Given below are the details for doing it.<br>
                                                                                    1. Log in to your Paypal account.<br>
                                                                                    2. On the Profile tab, click Website Payment Preferences in the Seller Preferences column.<br>
                                                                                    3. Under Auto Return for Website Payments, click the On radio button.<br>
                                                                                    4. Under Payment Data Transfer, click the On radio button.<br>
                                                                                    5. Click on the radio button to enable Payment Data Transfer.<br>
                                                                                    6. Click Save.

                                                                               </div> 
									</div>
											
									<div class="row main_form_inner">
										<label><input type="submit" name="btnSubmit" value="<?php echo BUTTON_SAVE; ?>" class="subm_btt"></label>
									</div>
								</div>
								</form>								
							</div>
							
							<div class="clear"></div>
							
							<div class="editprofile_step_outer">
								<form name="frmChangePassword" method ="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" onSubmit="return validateChangePasswordForm();">
									<div class="row main_form_inner hor_bar">
										<a href="#" class="btn-tggle" onClick="javascript:open_container('table_password');return false;"><?php echo HEADING_CHANGE_PASSWORD; ?></a>
									</div>
									<div class="sub_box clearfix" id="table_password" style="display:<?php echo ($_POST['btnPassSubmit']!='')?'block':'none'; ?>;">
										<div class="row main_form_inner">
											<label><?php echo TEXT_OLD_PASSOWRD; ?> <span class='warning'>*</span></label>
											<input type="password"  class="comm_input form-control" name="txtOldPassword" size="40" maxlength="100"/>
										</div>
										<div class="row main_form_inner">
											<label><?php echo TEXT_NEW_PASSWORD; ?> <span class='warning'>*</span></label>
											<input type="password"  class="comm_input form-control" name="txtNewPassword" size="40" maxlength="100" onFocus="javascript:toggleMsg('txtPasswordY');" onBlur="javascript:toggleMsg('txtPasswordY');"/>
											<span id="txtPasswordY" class="warning" style="display:none;"><?php echo ERROR_PASSWORD_SIX_CHAR; ?></span>
										</div>
										<div class="row main_form_inner">
											<label><?php echo TEXT_CONFIRM_NEW_PASSWORD; ?> <span class='warning'>*</span></label>
											<input   class="comm_input form-control" type="password"  name="txtConfirmNewPassword" size="40" maxlength="100" onFocus="javascript:toggleMsg('txtConfirmPasswordN');" onBlur="javascript:toggleMsg('txtConfirmPasswordN');" onKeyUp="confirmPassCheck(this);"/>
											<span id="txtConfirmPasswordN" class="warning" style="display:none;"><?php echo ERROR_PASSWORD_SIX_CHAR; ?></span><br><div id="mismatchpass">&nbsp;</div>
										</div>
										<div class="row main_form_inner">
											<label><input type="submit" name="btnPassSubmit" value="<?php echo HEADING_CHANGE_PASSWORD; ?>" class="subm_btt"/></label>
										</div>
									</div>
								</form>
								<?php include('settings.php'); ?>
							</div>
							
						</div>
												
					</div>					
                	<div class="subbanner">
						<?php include('./includes/sub_banners.php'); ?>
                    </div>
                </div>
            </div>  
        </div>
 </div>
 
<script>
	
    loadFields();
    var jqr = jQuery.noConflict();
    jqr(document).ready(function() {
        jqr('.jQNumericOnly').keypress(function(e) { 
          var key_codes = [45, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 0, 8];
            if (!(jqr.inArray(e.which, key_codes) >= 0)) {
                e.preventDefault();
            }
          });
    });
</script>
<script>
    jqr('#update_location').click(function(e){
        e.preventDefault();
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else { 
            alert("Geolocation is not supported by this browser.");
        }

    })
   

function showPosition(position) {
  console.log(position.coords.latitude);
  console.log(position.coords.longitude);
  jqr("#user_lat1").val(position.coords.latitude);
  jqr("#user_lng1").val(position.coords.longitude);
  jqr("#update_location").text("Location Updated");  
  jqr('#update_location').click(function () {return false;});
}
    </script>
<?php require_once("./includes/footer.php"); ?>