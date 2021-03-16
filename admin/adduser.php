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

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] =="Add User")
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
        $txtNewPassword = $_POST["txtNewPassword"];

                // check if user already exists
        $sqluserexists = "SELECT vLoginName FROM " . TABLEPREFIX . "users  WHERE vLoginName = '" . addslashes($txtUserName) . "' AND vDelStatus!='1'";
        $resultuserexists = mysqli_query($conn, $sqluserexists) or die(mysqli_error($conn));

        //check for duplicate email
        $sqlemailexists = "SELECT vEmail FROM " . TABLEPREFIX . "users  WHERE vEmail = '" . addslashes($txtEmail) . "' AND vDelStatus!='1'";
        $resultemailexists = mysqli_query($conn, $sqlemailexists) or die(mysqli_error($conn));

        if (mysqli_num_rows($resultuserexists) > 0) {
            $message = "Username already exists<br>";
            $notregistered = "1";
        }//end if
        else if (mysqli_num_rows($resultemailexists) > 0) {
            $message = "Email already exists<br>";
            $notregistered = "1";
        } // if username valid
        else if (!isValidUsername($vLoginName)) {
            $message = "Username is inavalid.";
            $notregistered = "1";
        }//end if
        else {
            $notregistered = "0";
        }

        if($notregistered=="0"){

        $sql = "INSERT INTO ".TABLEPREFIX."users SET ";
        $sql .= " vFirstName='". addslashes($txtFirstName) ."', vLastName= '". addslashes($txtLastName) ."' ,vAddress1 ='". addslashes($txtAddress1) ."' ,vAddress2 ='".addslashes($txtAddress2)."' ,vCity='".addslashes($txtCity)."'  , ";
        $sql .="vState = '".addslashes($txtState)."' ,vCountry='".addslashes($ddlCountry)."'  ,nZip = '".addslashes($txtZIP)."' , vPhone = '".addslashes($txtPhone)."' ,vFax = '".addslashes($txtFAX)."'  ,vEmail= '".addslashes($txtEmail)."' ,vUrl = '".addslashes($txtURL)."' , vGender = '".addslashes($ddlGender)."' ,vEducation = '".addslashes($ddlEducation)."',vAdvSource = '".addslashes($txtAdvSource)."',vAdvEmployee='".addslashes($txtAdvEmployee)."',";
        $sql .="dDateReg = now(), vDescription = '".addslashes($txtDescription)."',vPassword = '".md5($txtNewPassword)."',vLoginName='".addslashes($txtUserName)."'";

        
     
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        /*
         * send mail to user
         */


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
               AND C.content_name = 'welcomeMailUser'
               AND C.content_type = 'email'
               AND L.lang_id = '".$_SESSION["lang_id"]."'";
        
        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $mainTextShow   = $mailRw['content'];

        $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}");
        $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($txtUserName),$txtNewPassword );
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

        $mailcontent1   = $mainTextShow;

        $subject    = $mailRw['content_title'];
        $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

        $StyleContent   =  MailStyle($sitestyle,SITE_URL);

        $EMail = $txtEmail;

        //readf file n replace
        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, addslashes($txtUserName), $mailcontent1, $logourl, date('F d, Y'), SITE_NAME, $subject);
        $msgBody    = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
        $msgBody    = str_replace($arrSearch, $arrReplace, $msgBody);

        send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
        header("location:users.php?msg=added");
        exit;
        //$message = "Add user successfully!";
        }
        

}//end if
?>
<script language="javascript" type="text/javascript">
function loadFields()
{
    var frm = window.document.frmUserProfile;
    var country ="<?php echo $ddlCountry?>";
    var gender ="<?php echo $ddlGender?>";
    var education = "<?php echo $ddlEducation?>";
    if(gender == "")
	{
       gender = "M";
    }//end if
    if(education == "")
	{
        education = "GP";
    }//end if
    if(country == "")
	{
        country = "UnitedStates";
    }//end if

   for(i=0;i<frm.ddlCountry.options.length;i++)
   {
       if(frm.ddlCountry.options[i].text == country)
	   {
           frm.ddlCountry.options[i].selected=true;
           break;
       }//end if
    }//end for loop

   for(i=0;i<frm.ddlGender.options.length;i++)
   {
      if(frm.ddlGender.options[i].value == gender)
	  {
          frm.ddlGender.options[i].selected=true;
          break;
      }//end if
    }//end for loop

    for(i=0;i<frm.ddlEducation.options.length;i++)
	{
       if(frm.ddlEducation.options[i].value == education)
	   {
           frm.ddlEducation.options[i].selected=true;
           break;
       }//end if
    }//end for loop
}//end function

function checkNewPasswordEntered()
{
    var frm = window.document.frmUserProfile;
    if(frm.txtNewPassword.value == ""  || frm.txtConfirmNewPassword.value == "")
    {
         return false;
    }//end if
    return true;
}//end function

function isAnyPasswordEntered()
{
    var frm = window.document.frmUserProfile;
    if(frm.txtOldPassword.value != ""  || frm.txtNewPassword.value != ""  || frm.txtConfirmNewPassword.value != "")
	{
        return true;
    }//end if
    return false;
}//end function

function isNewPasswordsValid()
{
   var frm = window.document.frmUserProfile;

   if(frm.txtNewPassword.value.length < 6)
   {
       alert("New Password should be atleast six characters long.");
       frm.txtNewPassword.focus();
       return false;
   }//end if
   else if(frm.txtConfirmNewPassword.value != frm.txtNewPassword.value)
   {
       alert("New Password and Confirm New Password should match.");
       frm.txtConfirmNewPassword.select();
       frm.txtConfirmNewPassword.focus();
       return false;
   }//end else if
   return true;
}//end function

function validateChangePasswordForm()
{
  var frm = window.document.frmUserProfile;
  return true;
}//end function

function validateProfileForm()
{
   var frm = window.document.frmUserProfile;

   if(trim(frm.txtUserName.value) == "")
   {
       alert("User Name cannot be empty.");
       frm.txtUserName.focus();
       return false;
   }//end if
   else if(trim(frm.txtFirstName.value) == "")
   {
       alert("First Name cannot be empty.");
       frm.txtFirstName.focus();
       return false;
  }//end else if
  else if(!checkNewPasswordEntered())
  {
      alert("Please enter Password and Confirm New Password.");
      frm.txtNewPassword.focus();
      return false;
  }//end else if
  else if(!isNewPasswordsValid())
  {
       return false;
  }//end else if
  else if(trim(frm.txtAddress1.value) == "")
  {
     alert("Address Line 1 cannot be empty.");
     frm.txtAddress1.focus();
     return false;
  }//end else if
  /*else if(trim(frm.txtCity.value) == "")
  {
     alert("City cannot be empty.");
     frm.txtCity.focus();
     return false;
  }//end else if
  else if(trim(frm.txtState.value) == "")
  {
      alert("State cannot be empty.");
      frm.txtState.focus();
      return false;
  }//end else if
  else if(trim(frm.txtZIP.value) == "")
  {
      alert("ZIP cannot be empty.");
      frm.txtZIP.focus();
      return false;
  }//end else if
  else if(isNaN(frm.txtZIP.value))
  {
     alert("Please enter a valid ZIP.");
     frm.txtZIP.focus();
     return false;
  }//end else if
  */
  else if(trim(frm.txtPhone.value) == "")
  {
      alert("Phone cannot be empty.");
      frm.txtPhone.focus();
      return false;
  }//end else if
  else if(trim(frm.txtEmail.value) == "")
  {
      alert("Email cannot be empty.");
      frm.txtEmail.focus();
      return false;
  }//end else if
  else if(! checkMail(trim(frm.txtEmail.value)))
  {
      alert("Please enter a valid email.");
      frm.txtEmail.select();
      frm.txtEmail.focus();
      return false;
  }//end else if
  return true;
}//end function

function ChangeStatus(id,status)
{
   var frm = document.frmUsers;
   if(status == "D")
   {
        changeto = "deactivate";
   }//end else if
   else
   {
       changeto = "activate";
   }//end else if
   if(confirm("Are you sure you want to "+ changeto +" this user?"))
   {
      frm.postback.value="CS";
      frm.id.value=id;
      frm.changeto.value=status;
      frm.action="manageusers.php";
      frm.submit();
   }//end else if
}//end function
</script>

<div class="row admin_wrapper">
	<div class="admin_container">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="padding_T_B_td">
                <tr>
                  <td width="18%" valign="top"> <!--  Admin menu comes here -->
		                                 <?php require("../includes/adminmenu.php"); ?>
									<!--   Admin menu  comes here ahead --></td>
                  <td width="4%" valign="top"></td>
				  <td width="78%" valign="top">
				  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="94%" height="32" class="headerbg">&nbsp;</td>
                      <td width="6%" align="right" valign="bottom" class="headerbg"><a href="adminmain.php"><img src="../images/home-icon1.gif" width="44" height="25" border="0"></a></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="84%" class="heading_admn boldtextblack" align="left">Add User Panel</td>
                        <td width="16%" class="heading_admn">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td valign="top"><table width="100%"  border="0" cellspacing="1" cellpadding="5" class="maintext2">
<form name="frmUserProfile" method ="POST" action = "" onsubmit="return validateProfileForm();">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr align="right" bgcolor="#FFFFFF"><input type="hidden"  name="userid" value="<?php echo $userid; ?>" />
<input type="hidden"  name="userid" value="<?php echo $userid; ?>" />

                                <td colspan="2"><a href="users.php" class="style2"><strong>Back</strong></a>&nbsp;</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="subheader">General Details</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td width="22%" align="left">User Name <span class='warning'>*</span></td>
                                <td width="78%" align="left"><input type="text"  name="txtUserName" value="<?php echo htmlentities($txtUserName);?>" class="textbox2" size="40"/></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">First Name <span class='warning'>*</span></td>
                                <td align="left"><input  name="txtFirstName" type="text" class="textbox2" value="<?php echo htmlentities($txtFirstName); ?>" size="40" maxlength="100" /></td>
                              </tr>
							 <tr bgcolor="#FFFFFF">
                                <td align="left">Password <span class='warning'>*</span></td>
                                <td align="left"><input name="txtNewPassword" type="password" class="textbox2" size="40" maxlength="100"  /></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Confirm New Password<span class='warning'>*</span></td>
                                <td align="left"><input  name="txtConfirmNewPassword" type="password" class="textbox2" size="40" maxlength="100" /></td>
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
                                <td align="left"><SELECT name="ddlCountry" class="textbox2">
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
                                <td align="left"><input name="txtURL" type="text" class="textbox2" value="<?php echo htmlentities($txtURL); ?>" size="40" maxlength="100" /></td>
                              </tr>-->
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="subheader">Other Details</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Gender</td>
                                <td align="left"><SELECT name="ddlGender" class="textbox2">
                                                  <OPTION value="M" <?php if($ddlGender=="M") {  echo "selected"; } ?>>Male</OPTION><OPTION value="F" <?php if($ddlGender=="F") {  echo "selected"; } ?>>Female</OPTION></SELECT></td>
                              </tr>
                              <!--<tr bgcolor="#FFFFFF">
                                <td align="left">Heard From</td>
                                <td align="left"><input name="txtAdvSource" type="text" class="textbox2" value="<?php echo htmlentities($txtAdvSource); ?>" size="40" maxlength="100" readonly/></td>
                              </tr>-->
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Referred By</td>
                                <td align="left"><input name="txtAdv<?php if($ddlGender=="M") {  echo "selected"; } ?>Employee" type="text" class="textbox2" value="<?php echo htmlentities($txtAdvEmployee); ?>" size="40" maxlength="100"  /></td>
                              </tr>
                          <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td align="left"><input name="btnSubmit" type="submit" class="submit" value="Add User"/>
                                                &nbsp;&nbsp;<input name="btnReset1" type="reset" class="submit" value="Reset" /></td>
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
<?php include_once('../includes/footer_admin.php');?>