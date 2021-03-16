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
$PGTITLE='editaffiliate';

if(isset($_GET["affid"]) || $_GET["affid"]!="" )
{
  $affid = $_GET["affid"];
}//end if
else if(isset($_POST["affid"]) || $_POST["affid"]!="" )
{
   $affid = $_POST["affid"];
}//end else if

if($_POST["btnSubmit"] =="Save Changes")
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
        $txtURL = $_POST["txtURL"];
        $txtAmount = $_POST["txtAmount"];
        $ddlCountry = $_POST["ddlCountry"];


        $sql = "UPDATE ".TABLEPREFIX."affiliate SET ";
        $sql .= " vFirstName='".$txtFirstName."', vLastName= '".$txtLastName."' ,vAddress1 ='".$txtAddress1."' ,vAddress2 ='".$txtAddress2."' ,vCity='".$txtCity."'  , ";
        $sql .="vState = '".$txtState."' ,vCountry='".$ddlCountry."'  ,nZip = '".$txtZIP."' , vPhone = '".$txtPhone."' ,vFax = '".$txtFAX."'  ,vEmail= '".$txtEmail."' ,vUrl = '".$txtURL."' ,";
        $sql .=" nAmount ='".$txtAmount."' WHERE nAffiliateId ='".$affid."' ";
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
        $txtURL = $_POST["txtURL"];
        $txtAmount = $_POST["txtAmount"];
        $ddlCountry = $_POST["ddlCountry"];

        $sql = "UPDATE ".TABLEPREFIX."affiliate  SET vPassword = '".md5($txtNewPassword)."' ";
        $sql .="WHERE nAffiliateId ='".$affid."' ";
        //echo $sql;
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $message = "Password changed successfully!";
}//end else if

$sqlaffdetails = "SELECT * FROM ".TABLEPREFIX."affiliate  WHERE  nAffiliateId   = '".$affid."'";
$resultaffdetails  = mysqli_query($conn, $sqlaffdetails ) or die(mysqli_error($conn));
$rowaff = mysqli_fetch_array($resultaffdetails);
$txtUserName = $rowaff["vLoginName"];
$txtFirstName = $rowaff["vFirstName"];
$txtLastName = $rowaff["vLastName"];
$txtAddress1 = $rowaff["vAddress1"];
$txtAddress2 = $rowaff["vAddress2"];
$txtCity = $rowaff["vCity"];
$txtState = $rowaff["vState"];
$txtZIP = $rowaff["nZip"];
$txtPhone = $rowaff["vPhone"];
$txtFAX = $rowaff["vFax"];
$txtEmail = $rowaff["vEmail"];
$ddlCountry = $rowaff["vCountry"];
$txtURL = $rowaff["vUrl"];
$txtAmount = $rowaff["nAmount"];
?>
<script language="javascript" type="text/javascript">
function loadFields(){
        var frm = window.document.frmAffProfile;
        var country ="<?php echo $ddlCountry?>";
        if(country == ""){
                country = "UnitedStates";
        }
        for(i=0;i<frm.ddlCountry.options.length;i++){
            if(frm.ddlCountry.options[i].text == country){
                        frm.ddlCountry.options[i].selected=true;
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
        var frm = window.document.frmAffProfile;

        if(trim(frm.txtFirstName.value) == ""){
                alert("First Name cannot be empty.");
                frm.txtFirstName.focus();
                return false;
        }else if(trim(frm.txtAddress1.value) == ""){
                alert("Address Line 1 cannot be empty.");
                frm.txtAddress1.focus();
                return false;
        }else if(trim(frm.txtCity.value) == ""){
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
        }else if(isNaN(frm.txtZIP.value)){
                alert("Please enter a valid ZIP.");
                frm.txtZIP.focus();
                return false;
        }else if(trim(frm.txtPhone.value) == ""){
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
        }else if(isNaN(frm.txtAmount.value)){
                alert("Amount must be numeric.");
                frm.txtAmount.focus();
                return false;
        }
        return true;
}
</script>
<!--onload='loadFields();' -->


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="19%" valign="top"> <!--  Admin menu comes here -->
		                                 <?php require("../includes/adminmenu.php"); ?>
									<!--   Admin menu  comes here ahead --></td>
                  <td width="81%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="94%" height="32" class="headerbg">&nbsp;</td>
                      <td width="6%" align="right" valign="top" class="headerbg"><a href="adminmain.php"><img src="../images/home-icon1.gif" width="44" height="25" border="0"></a></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="84%" class="heading_admn boldtextblack" align="left">Edit Affiliate Profile</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmAffProfile" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateProfileForm();">
<input type="hidden"  name="affid" value="<?php echo $affid; ?>" />
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="success"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="right"><a href="<?php echo $_SESSION["backurl"]?>"><strong>Back</strong></a></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><strong>General Details</strong></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="20%" align="left">User Name</td>
                                <td width="80%" align="left"><?php echo stripslashes($txtUserName); ?></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">First Name <span class="warning">*</span></td>
                                <td align="left"><input type="text"  name="txtFirstName" size="40" maxlength="100" value="<?php echo stripslashes($txtFirstName); ?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Last Name</td>
                                <td align="left"><input type="text" name="txtLastName" size="40" maxlength="100" value="<?php echo stripslashes($txtLastName);?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Address Line 1 <span class="warning">*</span></td>
                                <td align="left"><input type="text" name="txtAddress1" size="40" maxlength="100" value="<?php echo stripslashes($txtAddress1);?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Address Line 2</td>
                                <td align="left"><input type="text" name="txtAddress2" size="40" maxlength="100" value="<?php echo stripslashes($txtAddress2);?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">City <span class="warning">*</span></td>
                                <td align="left"><input type="text" name="txtCity" size="40" maxlength="100" value="<?php echo stripslashes($txtCity);?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">State <span class="warning">*</span></td>
                                <td align="left"><input type="text" name="txtState" size="40" maxlength="100" value="<?php echo stripslashes($txtState);?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Country</td>
                                <td align="left"><SELECT name="ddlCountry" class="textbox">
                                                                        <option>Afghanistan
                                                                <option>Albania
                                                                <option>Algeria
                                                                <option>Andorra
                                                                <option>Angola
                                                                <option>Antigua&nbsp; and&nbsp; Barbuda
                                                                        <option>Argentina
                                                                        <option>Armenia
                                                                        <option>Australia
                                                                        <option>Austria
                                                                        <option>Azerbaijan
                                                                        <option>Bahamas
                                                                        <option>Bahrain
                                                                        <option>Bangladesh
                                                                        <option>Barbados
                                                                        <option>Belarus
                                                                        <option>Belgium
                                                                        <option>Belize
                                                                        <option>Benin
                                                                        <option>Bhutan
                                                                        <option>Bolivia
                                                                        <option>Bosnia &amp; Herzegovina
                                                                        <option>Botswana
                                                                        <option>Brazil
                                                                        <option>Brunei
                                                                        <option>Bulgaria
                                                                        <option>Burkina Faso
                                                                        <option>Burundi
                                                                        <option>Cambodia
                                                                        <option>Cameroon
                                                                        <option>Canada
                                                                        <option>Cape Verde
                                                                        <option>Cent African Rep
                                                                        <option>Chad
                                                                        <option>Chile
                                                                        <option>China
                                                                        <option>Colombia
                                                                        <option>Comoros
                                                                        <option>Congo
                                                                        <option>Costa Rica
                                                                        <option>Croatia
                                                                        <option>Cuba
                                                                        <option>Cyprus
                                                                        <option>Czech Republic
                                                                        <option>C&ocirc;te d'Ivoire
                                                                        <option>Denmark
                                                                        <option>Djibouti
                                                                        <option>Dominica
                                                                        <option>Dominican Republic
                                                                        <option>East Timor
                                                                        <option>Ecuador
                                                                        <option>Egypt
                                                                        <option>El Salvador
                                                                        <option>Equatorial Guinea
                                                                        <option>Eritrea
                                                                        <option>Estonia
                                                                        <option>Ethiopia
                                                                        <option>Fiji
                                                                        <option>Finland
                                                                        <option>France
                                                                        <option>Gabon
                                                                        <option>Gambia
                                                                        <option>Georgia
                                                                        <option>Germany
                                                                        <option>Ghana
                                                                        <option>Greece
                                                                        <option>Grenada
                                                                        <option>Guatemala
                                                                        <option>Guinea
                                                                        <option>Guinea-Bissau
                                                                        <option>Guyana
                                                                        <option>Haiti
                                                                        <option>Honduras
                                                                        <option>Hungary
                                                                        <option>Iceland
                                                                        <option>India
                                                                        <option>Indonesia
                                                                        <option>Iran
                                                                        <option>Iraq
                                                                        <option>Ireland
                                                                        <option>Israel
                                                                        <option>Italy
                                                                        <option>Jamaica
                                                                        <option>Japan
                                                                        <option>Jordan
                                                                        <option>Kazakhstan
                                                                        <option>Kenya
                                                                        <option>Kiribati
                                                                        <option>Korea, North
                                                                        <option>Korea, South
                                                                        <option>Kuwait
                                                                        <option>Kyrgyzstan
                                                                        <option>Laos
                                                                        <option>Latvia
                                                                        <option>Lebanon
                                                                        <option>Lesotho
                                                                        <option>Liberia
                                                                        <option>Libya
                                                                        <option>Liechtenstein
                                                                        <option>Lithuania
                                                                        <option>Luxembourg
                                                                        <option>Macedonia
                                                                        <option>Madagascar
                                                                        <option>Malawi
                                                                        <option>Malaysia
                                                                        <option>Maldives
                                                                        <option>Mali
                                                                        <option>Malta
                                                                        <option>Marshall Islands
                                                                        <option>Mauritania
                                                                        <option>Mauritius
                                                                        <option>Mexico
                                                                        <option>Micronesia
                                                                        <option>Moldova
                                                                        <option>Monaco
                                                                        <option>Mongolia
                                                                        <option>Morocco
                                                                        <option>Mozambique
                                                                        <option>Myanmar
                                                                        <option>Namibia
                                                                        <option>Nauru
                                                                        <option>Nepal
                                                                        <option>Netherlands
                                                                        <option>New Zealand
                                                                        <option>Nicaragua
                                                                        <option>Niger
                                                                        <option>Nigeria
                                                                        <option>Norway
                                                                        <option>Oman
                                                                        <option>Pakistan
                                                                        <option>Palau
                                                                        <option>Panama
                                                                        <option>Papua New Guinea
                                                                        <option>Paraguay
                                                                        <option>Peru
                                                                        <option>Philippines
                                                                        <option>Poland
                                                                        <option>Portugal
                                                                        <option>Qatar
                                                                        <option>Romania
                                                                        <option>Russia
                                                                        <option>Rwanda
                                                                        <option>Saint Kitts
                                                                        <option>Saint Lucia
                                                                        <option>Saint Vincent
                                                                        <option>Samoa
                                                                        <option>San Marino
                                                                        <option>Sao Tome
                                                                        <option>Saudi Arabia
                                                                        <option>Senegal
                                                                        <option>Seychelles
                                                                        <option>Sierra Leone
                                                                        <option>Singapore
                                                                        <option>Slovakia
                                                                        <option>Slovenia
                                                                        <option>Solomon Islands
                                                                        <option>Somalia
                                                                        <option>South Africa
                                                                        <option>Spain
                                                                        <option>Sri Lanka
                                                                        <option>Sudan
                                                                        <option>Suriname
                                                                        <option>Swaziland
                                                                        <option>Sweden
                                                                        <option>Switzerland
                                                                        <option>Syria
                                                                        <option>Taiwan
                                                                        <option>Tajikistan
                                                                        <option>Tanzania
                                                                        <option>Thailand
                                                                        <option>Togo
                                                                        <option>Tonga
                                                                        <option>Trinidad and Tobago
                                                                        <option>Tunisia
                                                                        <option>Turkey
                                                                        <option>Turkmenistan
                                                                        <option>Tuvalu
                                                                        <option>Uganda
                                                                        <option>Ukraine
                                                                        <option>United Arab Emirates
                                                                        <option>United Kingdom
                                                                        <option selected>UnitedStates
                                                                        <option>Uruguay
                                                                        <option>Uzbekistan
                                                                        <option>Vanuatu
                                                                        <option>Vatican City
                                                                        <option>Venezuela
                                                                        <option>Vietnam
                                                                        <option>Western Sahara
                                                                        <option>Yemen
                                                                        <option>Yugoslavia
                                                                        <option>Zambia
                                                                        <option>Zimbabwe</option>
                                                                </SELECT></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">ZIP <span class="warning">*</span></td>
                                <td align="left"><input type="text" name="txtZIP" size="25" maxlength="11" value="<?php echo stripslashes($txtZIP); ?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Phone <span class="warning">*</span></td>
                                <td align="left"><input type="text" name="txtPhone" size="40" maxlength="50" value="<?php echo stripslashes($txtPhone); ?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">FAX</td>
                                <td align="left"><input type="text" name="txtFAX" size="40" maxlength="100" value="<?php echo stripslashes($txtFAX); ?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Email <span class="warning">*</span></td>
                                <td align="left"><input type="text" name="txtEmail" size="40" maxlength="100" value="<?php echo $txtEmail; ?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">URL</td>
                                <td align="left"><input type="text" name="txtURL" size="40" maxlength="100" value="<?php echo $txtURL; ?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Amount</td>
                                <td align="left"><input type="text" name="txtAmount" size="40" maxlength="100" value="<?php echo $txtAmount; ?>" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td align="left"><input type="submit" name="btnSubmit" value="Save Changes" class="submit" />&nbsp;&nbsp;<input type="reset" name="btnReset1" value="Reset" class="submit"/></td>
                              </tr>
							   </form>
                                                <form name="frmChangePassword" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateChangePasswordForm();" >
                                                <input type="hidden"  name="affid" value="<?php echo $affid;?>" />
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><strong>Change Password</strong></td>
                                </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">New Password <span class="warning">*</span></td>
                                <td align="left"><input type="password" name="txtNewPassword" size="40" maxlength="100" class="textbox"  /></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Confirm New Password <span class="warning">*</span></td>
                                <td align="left"><input type="password"  name="txtConfirmNewPassword" size="40" maxlength="100" class="textbox"/></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td align="left"><input type="submit" name="btnSubmit" value="Change Password" class="submit"/></td>
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
<?php include_once('../includes/footer_admin.php');?>