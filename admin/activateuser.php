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
$PGTITLE='usersactivation';

if(isset($_GET["id"]) && $_GET["id"]!="" ) {
    $userid = $_GET["id"];
}//end if
else if(isset($_POST["id"]) && $_POST["id"]!="" ) {
    $userid = $_POST["id"];
}//end else if

$add_flag	= false;
$var_txnid	= "0";
$sqluserdetails = "SELECT * FROM ".TABLEPREFIX."users  WHERE  nUserId  = '". addslashes($userid)."'";
$resultuserdetails  = mysqli_query($conn, $sqluserdetails ) or die(mysqli_error($conn));
if(mysqli_num_rows($resultuserdetails) > 0) {
    if($row = mysqli_fetch_array($resultuserdetails)) {
        $add_flag = true;
        $txtUserName = $row["vLoginName"];
        $txtFirstName = $row["vFirstName"];
        $txtLastName = $row["vLastName"];
        $txtAddress1 = $row["vAddress1"];
        $txtAddress2 = $row["vAddress2"];
        $txtCity = $row["vCity"];
        $txtState = $row["vState"];
        $txtZIP = $row["nZip"];
        $txtPhone = $row["vPhone"];
        $txtFAX = $row["vFax"];
        $txtEmail = $row["vEmail"];
        $ddlCountry = $row["vCountry"];
        $ddlGender = $row["vGender"];
        $txtURL = $row["vUrl"];
        $ddlEducation = $row["vEducation"];
        $txtDescription = $row["vDescription"];
        $txtAdvSource = $row["vAdvSource"];
        $txtAdvEmployee = $row["vAdvEmployee"];
        $var_referenceno = $row["vReferenceNo"];
        $var_bank = $row["vBank"];
        $var_refdate = $row["dReferenceDate"];
        $var_method = $row["vMethod"];
        $var_txnid	= $row["vTxnId"];
        switch($var_method) {
            case "bu" :
                $var_method = "Business Check";
                break;
            case "ca" :
                $var_method = "Cashiers Check";
                break;
            case "wt" :
                $var_method = "Wire Transfer";
                break;
            case "mo" :
                $var_method = "Money Order";
                break;
            case "pc" :
                $var_method = "Personal Check";
                break;
        }//end switch
    }//end if
}//end if
else {
    echo "<script>alert('Please try again!'); window.location.href='usersactivation.php'</script>";
    exit();
}//end else

if(isset($_POST["postback"]) && $_POST["postback"]=="Y") {
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

    if($add_flag == true) {
        //start the activation
        if ($paytype <> "cc" AND $paytype <> "pp" AND $paytype <> "free") {
            //II - Store check details
            $sql = "insert into ".TABLEPREFIX."paymentdetails(vName,vReferenceNo,vBank,dReferenceDate,dEntryDate) ";
            $sql .= " Values('" . addslashes($row["vName"]) . "','" . addslashes($row["vReferenceNo"]) . "','" . addslashes($row["vBank"]) . "','" . addslashes($row["dReferenceDate"]) . "',now())";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));

            $var_txnid = mysqli_insert_id($conn);
        }//end if

        //III - add the user table entry
        $var_first_name = $row["vFirstName"];
        $totalamt=$row["nAmount"];
        $paytype=$row["vMethod"];
        $var_email=$row["vEmail"];

        $sql = "UPDATE ".TABLEPREFIX."users SET vTxnId='".addslashes($var_txnid)."',
						vStatus='0',vDelStatus='0'	WHERE nUserId='".$row['nUserId']."'";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        $var_new_id = mysqli_insert_id($conn);

        if ($paytype <> "cc" AND $paytype <> "pp" AND $paytype <> "free") {
            //IV - check and add referral table
            //Addition for referrals
            $var_reg_amount=0;

            if($row["nRefId"] != "0") {
                $sql = "Select nRefId,nUserId,nRegAmount from ".TABLEPREFIX."referrals where vRegStatus='0' AND nRefId='" . $row["nRefId"] . "'";
                $result_test=mysqli_query($conn, $sql) or die(mysqli_error($conn));

                if(mysqli_num_rows($result_test) > 0) {
                    if($row_final = mysqli_fetch_array($result_test)) {
                        $var_reg_amount = $row_final["nRegAmount"];

                        $sql = "Update ".TABLEPREFIX."referrals set vRegStatus='1',";
                        $sql .= "nUserRegId='" .  $var_new_id . "',dRegDate=now() where nRefId='" . $row_final["nRefId"] . "'";

                        mysqli_query($conn, $sql) or die(mysqli_error($conn));

                        $sql = "Select nUserId from ".TABLEPREFIX."user_referral where nUserId='" . $row_final["nUserId"] . "'";
                        $result_ur = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        if(mysqli_num_rows($result_ur) > 0) {
                            $sql = "Update ".TABLEPREFIX."user_referral set nRegCount = nRegCount + 1,nRegAmount=nRegAmount + $var_reg_amount where nUserId='" . $row_final["nUserId"] . "'";
                        }//end if
                        else {
                            $sql = "insert into ".TABLEPREFIX."user_referral(nUserId,nRegCount,nRegAmount) values('"
                                    . $row_final["nUserId"] . "','1','$var_reg_amount')";
                        }//end else
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }//end if
                }//end if
            }//end if
            //end of referrals

            //V - Update transaction table
            $sql="INSERT INTO ".TABLEPREFIX."payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId)
							VALUES ('R', '$var_txnid', ' $totalamt', '$paytype',now(), '". $var_new_id ."', '')";
            $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }//end if
        else {
            $sql	= "UPDATE ".TABLEPREFIX."payment
			  				 SET nUserId = '{$var_new_id}'
			   				WHERE nUserId = '".addslashes($userid)."'";
            $res	= mysqli_query($conn, $sql);
        }//end else
    }//end if


    /*
    * Fetch user language details
    */

    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$row["preferred_language"]."'";
    $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
    $langRw = mysqli_fetch_array($langRs);

    /*
    * Fetch email contents from content table
    */
    $mailSql = "SELECT L.content,L.content_title
              FROM ".TABLEPREFIX."content C
              JOIN ".TABLEPREFIX."content_lang L
                ON C.content_id = L.content_id
               AND C.content_name = 'userRegisterEmailFromAdmin'
               AND C.content_type = 'email'
               AND L.lang_id = '".$row["preferred_language"]."'";
    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
    $mailRw  = mysqli_fetch_array($mailRs);

    $mainTextShow   = str_replace('{SITE_NAME}',SITE_NAME,$mailRw['content']);
    $mainTextShow   = str_replace('{SITE_URL}',SITE_URL,$mainTextShow);

    $mailcontent1   = $mainTextShow;

    $subject    = $mailRw['content_title'];
    $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

    $EMail = $var_email;

    $StyleContent=MailStyle($sitestyle,SITE_URL);


    //readf file n replace
    $arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
    $arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,'Member',$mailcontent1,$logourl,date('F d, Y'),SITE_NAME,$subject);
    $msgBody    = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
    $msgBody    = str_replace($arrSearch,$arrReplace,$msgBody);

    send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');
    //end of activation
    echo "<script>alert('User activated!');</script>";
    header('location:usersactivation.php');
    exit();
}//end if

else if($_POST["postback"] == "D") {
    $sql = "Delete from ".TABLEPREFIX."users where nUserId='" . addslashes($userid) . "'";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
    echo "<script>alert('User entry deleted!'); window.location.href='usersactivation.php'</script>";
    exit();
}//end else if
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


    function validateForm() {
        if(document.frmUserProfile.postback.value.length == 0) {
            return false;
        }
        else {
            return true;
        }
    }

    function clickButton(i) {
        if(i == 0) {
            document.frmUserProfile.postback.value = 'D';
        }
        else if(i == 1) {
            document.frmUserProfile.postback.value = 'Y';
        }
        document.frmUserProfile.submit();
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
                    <td width="100%" class="heading_admn boldtextblack" align="left">Activate User</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">
                                        <form name="frmUserProfile" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateForm();">

                                            <?php if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                                <?php  }//end if?>
                                            <tr align="right" bgcolor="#FFFFFF"><input type="hidden"  name="id" value="<?php echo $userid; ?>" />
                                            <input type="hidden"  name="postback"  id="postback" value="<?php echo $userid; ?>" />
                                            <td colspan="2"><strong><a href="<?php echo $_SESSION["backurl"]?>" class="style2">Back</a>&nbsp;</strong></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="left"><strong>General Details</strong></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="20%" align="left">User Name</td>
                                                <td width="80%" align="left"><input type="hidden"  name="txtUserName" value="<?php echo htmlentities($txtUserName); ?>" /><?php echo htmlentities($txtUserName); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">First Name <span class='warning'>*</span></td>
                                                <td align="left"><input  name="txtFirstName" type="text" class="textbox" value="<?php echo htmlentities($txtFirstName); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Last Name</td>
                                                <td align="left"><input name="txtLastName" type="text" class="textbox" value="<?php echo htmlentities($txtLastName);?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Address Line 1 <span class='warning'>*</span></td>
                                                <td align="left"><input name="txtAddress1" type="text" class="textbox" value="<?php echo htmlentities($txtAddress1);?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Address Line 2</td>
                                                <td align="left"><input name="txtAddress2" type="text" class="textbox" value="<?php echo htmlentities($txtAddress2);?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">City <span class='warning'>*</span></td>
                                                <td align="left"><input name="txtCity" type="text" class="textbox" value="<?php echo htmlentities($txtCity);?>" size="40" maxlength="100"  readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">State <span class='warning'>*</span></td>
                                                <td align="left"><input name="txtState" type="text" class="textbox" value="<?php echo htmlentities($txtState);?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Country</td>
                                                <td align="left"><SELECT name="ddlCountry" class="textbox" style="width:140px; ">
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
                                                <td align="left">ZIP <span class='warning'>*</span></td>
                                                <td align="left"><input name="txtZIP" type="text" class="textbox" value="<?php echo htmlentities($txtZIP); ?>" size="25" maxlength="11"  style="width:140px; " readonly/></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Phone <span class='warning'>*</span></td>
                                                <td align="left"><input name="txtPhone" type="text" class="textbox" value="<?php echo htmlentities($txtPhone); ?>" size="40" maxlength="50" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">FAX</td>
                                                <td align="left"><input name="txtFAX" type="text" class="textbox" value="<?php echo htmlentities($txtFAX); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Email <span class='warning'>*</span></td>
                                                <td align="left"><input name="txtEmail" type="text" class="textbox" value="<?php echo htmlentities($txtEmail); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <!--<tr bgcolor="#FFFFFF">
                                                <td align="left">URL</td>
                                                <td align="left"><input name="txtURL" type="text" class="textbox" value="<?php //echo htmlentities($txtURL); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>-->
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="left"><strong>Other Details</strong></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Gender</td>
                                                <td align="left"><SELECT name="ddlGender" class="textbox"  style="width:220px;">
                                                        <OPTION value="M" <?php if($ddlGender=='M') {
                                                            echo 'selected';
                                                                }?>>Male</OPTION><OPTION value="F" <?php if($ddlGender=='F') {
                                                                    echo 'selected';
                                                                }?>>Female</OPTION></SELECT></td>
                                            </tr>
                                            <!--<tr bgcolor="#FFFFFF">
                                                <td align="left">Education</td>
                                                <td align="left"><SELECT name="ddlEducation" class="textbox" style="width:220px;">
                                                        <OPTION value="GP">Graduate/Post Graduate-Professional</OPTION>
                                                        <OPTION value="GG">Graduate/Post Graduate-General</OPTION>
                                                        <OPTION value="SC">Some College but not Graduate</OPTION>
                                                        <OPTION value="SH">SSC/HSC</OPTION>
                                                        <OPTION value="SS">Some School</OPTION>
                                                        <OPTION value="OT">Other</OPTION>
                                                    </SELECT></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Heard From</td>
                                                <td align="left"><input name="txtAdvSource" type="text" class="textbox" value="<?php //echo htmlentities($txtAdvSource); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>-->
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Referred By</td>
                                                <td align="left"><input name="txtAdvEmployee" type="text" class="textbox" value="<?php echo htmlentities($txtAdvEmployee); ?>" size="40" maxlength="100"  readonly/></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Description</td>
                                                <td align="left"><textarea name="txtDescription" cols="40" rows="10"  wrap class="textbox" onKeyDown="limitLength(this.form.txtDescription, 400);" onKeyUp="limitLength(this.form.txtDescription, 400);" maxlength="400" style="height:100px;"><?php echo htmlentities($txtDescription); ?></textarea></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Reference No.</td>
                                                <td align="left"><input name="txtReferenceNo" type="text" class="textbox" value="<?php echo htmlentities($var_referenceno); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Bank(If Applicable)</td>
                                                <td align="left"><input name="txtBank" type="text" class="textbox" value="<?php echo htmlentities($var_bank); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Reference Date (mm/dd/yyyy)</td>
                                                <td align="left"><input name="txtRefDate" type="text" class="textbox" value="<?php echo change_date_format(htmlentities($var_refdate),'mysql-to-mmddyy'); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Payment Method</td>
                                                <td align="left"><?php echo $var_method?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td align="left"><input name="btnSubmit" type="button" class="submit" value="Activate User"  style="width:145px;" onClick="javascript:clickButton(1);"/>
                                                    &nbsp;&nbsp;<input name="btnDelete" type="button" class="submit_grey" value="Delete"  onClick="javascript:clickButton(0);" /></td>
                                            </tr>
                                        </form>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</div></div>
</option>

<?php include_once('../includes/footer_admin.php');?>