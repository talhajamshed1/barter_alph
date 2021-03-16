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
$PGTITLE='user_reg';

$vAdvSource1	=	"";
$sql =  "SELECT vLookUpDesc  FROM ".TABLEPREFIX."lookup where nLookUpCode  ='6'";

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if(mysqli_num_rows($result) > 0)
{
    while($row = mysqli_fetch_array($result) )
	{
         $vAdvSource1.="<option value='".htmlentities($row["vLookUpDesc"])."'>".htmlentities($row["vLookUpDesc"])."</option>";
    }//end while loop
}//end if

$arrFields	= array();
$sql		= "SELECT * FROM ".TABLEPREFIX."mandatory 
		   	   WHERE vActiveStatus = 'A' ORDER BY nOrder";
$res		= @mysqli_query($conn, $sql);
if (@mysqli_num_rows($res) > 0) 
{
	while($row = @mysqli_fetch_array($res)) 
	{
		$arrFields[]	= $row;
	}//end while loop
}//end if

if (!empty($arrFields)) 
{
	foreach ($arrFields as $key => $value) 
	{
		${$value['vManFieldName']}	= isset($_POST[$value['vManFieldName']]) ? stripslashes(trim($_POST[$value['vManFieldName']])) : "";
	}//end foreach
}//end if

$vAdvEmployee	= "";

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=="Register")
{
		$guseraffid = $_GET["guseraffid"];

        $sqluserexists = "SELECT vLoginName FROM ".TABLEPREFIX."users  WHERE vLoginName = '" . addslashes($vLoginName) . "'";
        $resultuserexists = mysqli_query($conn, $sqluserexists) or die(mysqli_error($conn));

        if(mysqli_num_rows($resultuserexists)>0)
		{
            $message = "This User Name '$vLoginName' is in use!. Please select a different one!";
            $notregistered = "1";
        }//end if
		else
		{
			if(!isValidUsername($vLoginName))
			{
				$message = "Invalid User Name '".htmlentities($vLoginName)."'!. Please select a user name with no special characters (@,#,^,& etc. ) and spaces!";
				$notregistered = "1";
			}//end if
			else
			{
						
				if(DisplayLookUp('3')!='')
				{
					$var_real_amount=DisplayLookUp('3');
					$var_amount = $var_real_amount;
				}//end if
						
				$sql="";
				$sql = "INSERT INTO ".TABLEPREFIX."users(vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
				$sql .="vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
				$sql .="vDescription  ,nAmount,vMethod ,vTxnId,dDateReg   ,nAffiliateId,vAdvSource,vAdvEmployee) ";
				$sql .="VALUES ('".addslashes($vLoginName)."','".md5(addslashes($vPassword))."','".addslashes($vFirstName)."','".addslashes($vLastName)."','".addslashes($vAddress1)."','".addslashes($vAddress2)."','".addslashes($vCity)."',";
				$sql .="'".addslashes($vState)."','".addslashes($vCountry)."','" . addslashes($nZip) . "','".addslashes($vPhone)."','". addslashes($vFax) ."','". addslashes($vEmail) ."','". addslashes($vUrl) ."','". addslashes($vGender) ."','". addslashes($vEducation) ."',";
				$sql .="'".addslashes($vDescription)."','$var_amount','admin','a',now(),' " . addslashes($guseraffid) . "','".addslashes($vAdvSource)."','".addslashes($vAdvEmployee)."')";


				mysqli_query($conn, $sql) or die(mysqli_error($conn));
				$sqluserid = mysqli_insert_id($conn);
				$sql="";
				$sql="INSERT INTO ".TABLEPREFIX."payment(vTxn_type,vTxn_id,nTxn_amount,vTxn_mode,dTxn_date,nUserId,nSaleId ) ";
				$sql .=" values('a','a','$var_amount','a',now(),'$sqluserid','0') ";
				mysqli_query($conn, $sql) or die(mysqli_error($conn));

				foreach($_POST as $key=>$value)			
				{
					$$key="";
				}//end foreach
				$message="Registration Complete";
			}//end else
		}//end else
}//end if

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<SCRIPT language="JavaScript" type="text/javascript">
function loadFields(){
        var frm = window.document.frmRegistration;
	var country ="<?php echo htmlentities($vCountry)?>";
	var gender ="<?php echo htmlentities($vGender)?>";
	var education = "<?php echo htmlentities($vEducation)?>";
        if(gender == ""){
                gender = "M";
        }
        if(education == ""){
                education = "GP";
        }
        if(country == ""){
                country = "UnitedStates";
        }
	for(i=0;i<frm.vCountry.options.length;i++){
		if(frm.vCountry.options[i].text == country){
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
	for(i=0;i<frm.vEducation.options.length;i++){
		if(frm.vEducation.options[i].value == education){
			frm.vEducation.options[i].selected=true;
			break;
		}
	}
}

function validateRegistrationForm()
{
        var frm = window.document.frmRegistration;
		<?php if (!empty($arrFields)) {
		foreach ($arrFields as $key => $value) {?>
			<?php if ($value['vManStatus'] == "A") {
				if ($value['vManFieldType'] == 'TB') { ?>
					if(trim(frm.<?php echo $value['vManFieldName']?>.value) == ""){
						alert("<?php echo $value['vManLabelName']?> cannot be empty.");
						frm.<?php echo $value['vManFieldName']?>.focus();
						return false;
					}			
		 <?php }
		 	   if ($value['vManFieldName'] == 'vPassword') { ?>
					if(trim(frm.<?php echo $value['vManFieldName']?>.value.length) < 6 ){
						alert("<?php echo $value['vManLabelName']?>  should be atleast six characters long.");
						frm.<?php echo $value['vManFieldName']?>.focus();
						return false;
					}			
		 <?php }
		 	   if ($value['vManFieldName'] == 'vConfirmPassword') { ?>
					if(((frm.vConfirmPassword.value)) != ((frm.vPassword.value))){
						alert("Password and Confirmation Password should match.");
						frm.<?php echo $value['vManFieldName']?>.focus();
						return false;
					}			
		 <?php }
		 	   if ($value['vManFieldName'] == 'vEmail') { ?>
					if(!checkMail(trim(frm.<?php echo $value['vManFieldName']?>.value))){
						alert("<?php echo $value['vManLabelName']?> should be a valid one.");
						frm.<?php echo $value['vManFieldName']?>.focus();
						return false;
					}			
		<?php }
			}?>
		<?php }
		}?>
        return true;
}


</SCRIPT>
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
                        <td width="84%" class="heading_admn boldtextblack" align="left">Registration Form</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmRegistration" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateRegistrationForm();">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="warning"> * indicates mandatory fields</td>
                              </tr>
							  <?php if (!empty($arrFields)) 
							   {
								foreach ($arrFields as $key => $value) 
								{ 
									if ($value['vManFieldType'] == "SB") 
									{ 
										if ($value['vManFieldName'] == "vCountry") { ?>
                              <tr bgcolor="#FFFFFF">
                                <td width="31%" align="left"><?php echo $value['vManLabelName']?> <?php echo $value['vManStatus'] == "A" ? "<span class='warning'>*</span>" : ""?></td>
                                <td width="69%"><select name="<?php echo $value['vManFieldName']?>" class="textbox2">
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
                                  </select></td>
                      </tr>
					  <?php } else if ($value['vManFieldName'] == "vGender") { ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="left"><?php echo $value['vManLabelName']?> <?php echo $value['vManStatus'] == "A" ? "<span class='warning'>*</span>" : ""?></td>
                                <td><select class="textbox2" name="<?php echo $value['vManFieldName']?>">
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select></td>
                      </tr>
					  <?php } else if ($value['vManFieldName'] == "vEducation") { ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="left"><?php echo $value['vManLabelName']?> <?php echo $value['vManStatus'] == "A" ? "<span class='warning'>*</span>" : ""?></td>
                                <td><select class="textbox2" name="<?php echo $value['vManFieldName']?>">
									<option value="GP">Graduate/Post Graduate-Professional</option>
									<option value="GG">Graduate/Post Graduate-General</option>
									<option value="SC">Some College but not Graduate</option>
									<option value="SH">SSC/HSC</option>
									<option value="SS">Some School</option>
									<option value="OT">Other</option>
                                </select></td>
                      </tr>
					  <?php } else if ($value['vManFieldName'] == "vAdvSource") { ?>
                              <tr bgcolor="#FFFFFF">
                                <td align="left"><?php echo $value['vManLabelName']?> <?php echo $value['vManStatus'] == "A" ? "<span class='warning'>*</span>" : ""?></td>
                                <td><select class="textbox2" name="<?php echo $value['vManFieldName']?>">
										<?php echo $vAdvSource1?>
                                </select></td>
                      </tr>
					  <?php } ?>
								<?php } else if ($value['vManFieldType'] == "TA") { ?>
                              <tr valign="top" bgcolor="#FFFFFF">
                                <td align="left"><?php echo $value['vManLabelName']?> <?php echo $value['vManStatus'] == "A" ? "<span class='warning'>*</span>" : ""?></td>
                                <td align="left"><textarea  class="textbox22"  name="<?php echo $value['vManFieldName']?>" rows="10" cols="50" wrap><?php echo (${$value['vManFieldName']})?></textarea></td>
                      </tr>
					  <?php } else { 
								if ($value['vManFieldName'] == "vPassword" OR $value['vManFieldName'] == "vConfirmPassword") {?>	
                              <tr valign="top" bgcolor="#FFFFFF">
                                <td align="left"><?php echo $value['vManLabelName']?> <?php echo $value['vManStatus'] == "A" ? "<span class='warning'>*</span>" : ""?></td>
                                <td align="left"><input type="password" class="textbox2" name="<?php echo $value['vManFieldName']?>" size="40" maxlength="100" value="<?php echo (${$value['vManFieldName']})?>"/></td>
                              </tr>
							 <?php } else { ?>
                              <tr valign="top" bgcolor="#FFFFFF">
                                <td align="left"><?php echo $value['vManLabelName']?> <?php echo $value['vManStatus'] == "A" ? "<span class='warning'>*</span>" : ""?></td>
                                <td align="left"><input type="text" class="textbox2" name="<?php echo $value['vManFieldName']?>" size="40" maxlength="100" value="<?php echo (${$value['vManFieldName']})?>"/></td>
                              </tr>
							  <?php }//end else
							  	 }//end if 
							   }//end foreach
							}//end if
						?>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td><input type="submit" name="btnSubmit" value="Register" class="submit"/></td>
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