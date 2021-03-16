<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE='users';

if(isset($_GET["userid"]) || $_GET["userid"]!="" )
{
   $userid = $_GET["userid"];
}//end if
else if(isset($_POST["userid"]) || $_POST["userid"]!="" )
{
   $userid = $_POST["userid"];
}//end else if

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] =="Save Changes")
{
        $txtUserName = $_POST["txtUserName"];
        $txtFirstName = $_POST["txtFirstName"];
        $txtLastName = $_POST["txtLastName"];
        $txtAddress1 = $_POST["txtAddress1"];
        $txtAddress2 = $_POST["txtAddress2"];
        $txtCity = $_POST["txtCity"];
        $txtState = $_POST["txtState"];
        $txtZIP = $_POST["txtZIP"];
        $txtPhone = $_POST["txtPhone"];
        $txtFAX = $_POST["txtFAX"];
        $txtEmail = $_POST["txtEmail"];
        $ddlCountry = $_POST["ddlCountry"];
        $ddlGender = $_POST["ddlGender"];
        $txtURL = $_POST["txtURL"];
        $ddlEducation = $_POST["ddlEducation"];
        $txtDescription = $_POST["txtDescription"];
        $txtAdvSource = $_POST["txtAdvSource"];
        $txtAdvEmployee = $_POST["txtAdvEmployee"];

        $sql = "UPDATE ".TABLEPREFIX."users SET ";
        $sql .= " vFirstName='". addslashes($txtFirstName) ."', vLastName= '". addslashes($txtLastName) ."' ,vAddress1 ='". addslashes($txtAddress1) ."' ,vAddress2 ='".addslashes($txtAddress2)."' ,vCity='".addslashes($txtCity)."'  , ";
        $sql .="vState = '".addslashes($txtState)."' ,vCountry='".addslashes($ddlCountry)."'  ,nZip = '".addslashes($txtZIP)."' , vPhone = '".addslashes($txtPhone)."' ,vFax = '".addslashes($txtFAX)."'  ,vEmail= '".addslashes($txtEmail)."' ,vUrl = '".addslashes($txtURL)."' , vGender = '".addslashes($ddlGender)."' ,vEducation = '".addslashes($ddlEducation)."',vAdvSource = '".addslashes($txtAdvSource)."',vAdvEmployee='".addslashes($txtAdvEmployee)."',";
        $sql .="vDescription = '".addslashes($txtDescription)."' WHERE nUserId ='".addslashes($userid)."' ";
        //echo $sql;
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $message = "Changes saved successfully!";

}//end if
else if ($_POST["btnSubmit"] == "Change Password") 
{
        $txtUserName = $_POST["txtUserName"];
        $txtNewPassword = $_POST["txtNewPassword"];
        $txtFirstName = $_POST["txtFirstName"];
        $txtLastName = $_POST["txtLastName"];
        $txtAddress1 = $_POST["txtAddress1"];
        $txtAddress2 = $_POST["txtAddress2"];
        $txtCity = $_POST["txtCity"];
        $txtState = $_POST["txtState"];
        $txtZIP = $_POST["txtZIP"];
        $txtPhone = $_POST["txtPhone"];
        $txtFAX = $_POST["txtFAX"];
        $txtEmail = $_POST["txtEmail"];
        $ddlCountry = $_POST["ddlCountry"];
        $ddlGender = $_POST["ddlGender"];
        $txtURL = $_POST["txtURL"];
        $ddlEducation = $_POST["ddlEducation"];
        $txtDescription = $_POST["txtDescription"];


        $sql = "UPDATE ".TABLEPREFIX."users SET vPassword = '".md5($txtNewPassword)."' ";
        $sql .="WHERE nUserId ='".$userid."' ";
        //echo $sql;
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $message = "Password changed successfully!";
}//end else if

$sqluserdetails = "SELECT * FROM ".TABLEPREFIX."users  WHERE  nUserId  = '".$userid."'";
$resultuserdetails  = mysqli_query($conn, $sqluserdetails ) or die(mysqli_error($conn));
$rowuser = mysqli_fetch_array($resultuserdetails);
$txtUserName = $rowuser["vLoginName"];
$txtFirstName = $rowuser["vFirstName"];
$txtLastName = $rowuser["vLastName"];
$txtAddress1 = $rowuser["vAddress1"];
$txtAddress2 = $rowuser["vAddress2"];
$txtCity = $rowuser["vCity"];
$txtState = $rowuser["vState"];
$txtZIP = $rowuser["nZip"];
$txtPhone = $rowuser["vPhone"];
$txtFAX = $rowuser["vFax"];
$txtEmail = $rowuser["vEmail"];
$ddlCountry = $rowuser["vCountry"];
$ddlGender = $rowuser["vGender"];
$txtURL = $rowuser["vUrl"];
$ddlEducation = $rowuser["vEducation"];
$txtDescription = $rowuser["vDescription"];
$txtAdvSource = $rowuser["vAdvSource"];
$txtAdvEmployee = $rowuser["vAdvEmployee"];
$loadFunction2="<script type='text/javascript'>document.getElementById('ddlCountry').value='".$ddlCountry."';</script>";
?>
<script language="javascript" type="text/javascript">
function loadFields(){
        var frm = window.document.frmUserProfile;
        var country ="<?php echo $ddlCountry?>";
        var gender ="<?php echo $ddlGender?>";
        var education = "<?php echo $ddlEducation?>";
        if(gender == ""){
                gender = "M";
        }
        if(education == ""){
                education = "GP";
        }
        if(country == ""){
                country = "UnitedStates";
        }
        for(i=0;i<frm.ddlCountry.options.length;i++){
            if(frm.ddlCountry.options[i].text == country){
                        frm.ddlCountry.options[i].selected=true;
                        break;
            }
    }
        for(i=0;i<frm.ddlGender.options.length;i++){
            if(frm.ddlGender.options[i].value == gender){
                        frm.ddlGender.options[i].selected=true;
                        break;
            }
    }
        for(i=0;i<frm.ddlEducation.options.length;i++){
            if(frm.ddlEducation.options[i].value == education){
                        frm.ddlEducation.options[i].selected=true;
                        break;
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
                alert("New Password should be atleast six characters long.");
                frm.txtNewPassword.focus();
                return false;
        }else if(frm.txtConfirmNewPassword.value != frm.txtNewPassword.value){
                alert("New Password and Confirm New Password should match.");
                frm.txtConfirmNewPassword.select();
                frm.txtConfirmNewPassword.focus();
                return false;
        }
        return true;
}
function validateChangePasswordForm(){
        var frm = window.document.frmChangePassword;
        if(! checkNewPasswordEntered()){
                alert("Please enter New Password and Confirm New Password.");
                frm.txtNewPassword.focus();
                return false;
        }else if(!isNewPasswordsValid()){
                        return false;
        }
        return true;
}
function validateProfileForm()
{
        var frm = window.document.frmUserProfile;

        if(trim(frm.txtFirstName.value) == ""){
                alert("First Name cannot be empty.");
                frm.txtFirstName.focus();
                return false;
        }else if(trim(frm.txtAddress1.value) == ""){
                alert("Address Line 1 cannot be empty.");
                frm.txtAddress1.focus();
                return false;
        }
		/*else if(trim(frm.txtCity.value) == ""){
                alert("City cannot be empty.");
                frm.txtCity.focus();
                return false;
        }else if(trim(frm.txtState.value) == ""){
                alert("State cannot be empty.");
                frm.txtState.focus();
                return false;
        }else if(trim(frm.txtZIP.value) == ""){
                alert("ZIP cannot be empty.");
                frm.txtZIP.focus();
                return false;
        }
		else if(isNaN(frm.txtZIP.value)){
                alert("Please enter a valid ZIP.");
                frm.txtZIP.focus();
                return false;
        }*/
		else if(trim(frm.txtPhone.value) == ""){
                alert("Phone cannot be empty.");
                frm.txtPhone.focus();
                return false;
        }else if(trim(frm.txtEmail.value) == ""){
                alert("Email cannot be empty.");
                frm.txtEmail.focus();
                return false;
        }else if(! checkMail(trim(frm.txtEmail.value))){
                alert("Please enter a valid email.");
                frm.txtEmail.select();
                frm.txtEmail.focus();
                return false;
        }
        return true;
}

function ChangeStatus(id,status){
        var frm = document.frmUsers;
        if(status == "D"){
                changeto = "deactivate";
        }else{
                changeto = "activate";
        }
        if(confirm("Are you sure you want to "+ changeto +" this user?")){
                frm.postback.value="CS";
                frm.id.value=id;
                frm.changeto.value=status;
                frm.action="manageusers.php";
                frm.submit();
        }
}

</script>

<div class="row admin_wrapper">
	<div class="admin_container">


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="18%" valign="top"> <!--  Admin menu comes here -->
		                                 <?php require("../includes/adminmenu.php"); ?>
									<!--   Admin menu  comes here ahead --></td>
                                    <td width="4%"></td>
                  <td width="78%" valign="top">
               
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="100%" class="heading_admn boldtextblack" align="left">Edit User Panel</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td valign="top" bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2">
<form name="frmUserProfile" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateProfileForm();">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="success"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr align="right" bgcolor="#FFFFFF"><input type="hidden"  name="userid" value="<?php echo $userid; ?>" />
<input type="hidden"  name="userid" value="<?php echo $userid; ?>" />

                                <td colspan="2"><a href="<?php echo $_SESSION["backurl"]?>" class="style2"><strong>Back</strong></a>&nbsp;</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="subheader">General Details</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="27%" align="left">User Name</td>
                                <td width="73%" align="left"><input type="hidden"  name="txtUserName" value="<?php echo htmlentities($txtUserName); ?>" /><?php echo htmlentities($txtUserName); ?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">First Name <span class='warning'>*</span></td>
                                <td align="left"><input  name="txtFirstName" type="text" class="textbox2" value="<?php echo htmlentities($txtFirstName); ?>" size="40" maxlength="100" /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Last Name</td>
                                <td align="left"><input name="txtLastName" type="text" class="textbox2" value="<?php echo htmlentities($txtLastName);?>" size="40" maxlength="100" /></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Address Line 1 <span class='warning'>*</span></td>
                                <td align="left"><input name="txtAddress1" type="text" class="textbox2" value="<?php echo htmlentities($txtAddress1);?>" size="40" maxlength="100" /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Address Line 2</td>
                                <td align="left"><input name="txtAddress2" type="text" class="textbox2" value="<?php echo htmlentities($txtAddress2);?>" size="40" maxlength="100" /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">City <!--<span class='warning'>*</span>--></td>
                                <td align="left"><input name="txtCity" type="text" class="textbox2" value="<?php echo htmlentities($txtCity);?>" size="40" maxlength="100" /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">State <!--<span class='warning'>*</span>--></td>
                                <td align="left"><input name="txtState" type="text" class="textbox2" value="<?php echo htmlentities($txtState);?>" size="40" maxlength="100" /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Country</td>
                                <td align="left"><select name="ddlCountry" class="textbox22" id="ddlCountry"><?php include("../includes/country_select.php"); ?></select></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">ZIP <!--<span class='warning'>*</span>--></td>
                                <td align="left"><input name="txtZIP" type="text" class="textbox2" value="<?php echo htmlentities($txtZIP); ?>" size="25" maxlength="11"  style="width:140px; "/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Phone <span class='warning'>*</span></td>
                                <td align="left"><input name="txtPhone" type="text" class="textbox2" value="<?php echo htmlentities($txtPhone); ?>" size="40" maxlength="50" /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">FAX</td>
                                <td align="left"><input name="txtFAX" type="text" class="textbox2" value="<?php echo htmlentities($txtFAX); ?>" size="40" maxlength="50" /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Email <span class='warning'>*</span></td>
                                <td align="left"><input name="txtEmail" type="text" class="textbox2" value="<?php echo htmlentities($txtEmail); ?>" size="40" maxlength="100" /></td>
                              </tr>
                              <!--<tr bgcolor="#FFFFFF">
                                <td align="left">URL</td>
                                <td align="left"><input name="txtURL" type="text" class="textbox2" value="<?php //echo htmlentities($txtURL); ?>" size="40" maxlength="100" /></td>
                              </tr>-->
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="subheader">Other Details</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Gender</td>
                                <td align="left"><SELECT name="ddlGender" class="textbox2">
                                                  <OPTION value="M" <?php if($ddlGender=='M'){echo 'selected';}?>>Male</OPTION>
												  <OPTION value="F" <?php if($ddlGender=='F'){echo 'selected';}?>>Female</OPTION></SELECT></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Referred By</td>
                                <td align="left"><input name="txtAdvEmployee" type="text" class="textbox2" value="<?php echo htmlentities($txtAdvEmployee); ?>" size="40" maxlength="100"  readonly/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td align="left"><input name="btnSubmit" type="submit" class="submit" value="Save Changes"/>
                                                &nbsp;&nbsp;<input name="btnReset1" type="reset" class="submit_grey" value="Reset" /></td>
                              </tr>
						      </form>
                                                <form name="frmChangePassword" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateChangePasswordForm();">
                                                <input type="hidden"  name="userid" value="<?php echo $userid; ?>" />
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="subheader">Change Password</td>
                                </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">New Password <span class='warning'>*</span></td>
                                <td align="left"><input name="txtNewPassword" type="password" class="textbox2" size="40" maxlength="100"  /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Confirm New Password<span class='warning'>*</span></td>
                                <td align="left"><input  name="txtConfirmNewPassword" type="password" class="textbox2" size="40" maxlength="100" /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td align="left"><input name="btnSubmit" type="submit" class="submit" value="Change Password"/></td>
                              </tr>
							  </form>
                            </table>
</td>
                          </tr>
                        </table></td>
                      </tr>
                    </table>
                  </td>
                </tr>
</table>
</div>
</div>
<?php 
	echo $loadFunction2;//for the country selection after the update
	include_once('../includes/footer_admin.php');
?>