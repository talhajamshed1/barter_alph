<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com ï¿½ 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE='pending_settlement';

if(isset($_GET["userid"]) || $_GET["userid"]!="" ) {
    $userid = $_GET["userid"];
}//end if
else if(isset($_POST["userid"]) || $_POST["userid"]!="" ) {
    $userid = $_POST["userid"];
}//end else if

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=="Settle Amount") {
    $var_account = $_POST["txtAmount"];
    $var_settle  = $_POST["txtSettleAmount"];
    $var_actual  = $_POST["txtActualAmount"];
    $var_comm    = $_POST["txtCommission"];
    $var_mode    = $_POST["cmbMode"];
    $var_modeno  = $_POST["txtReference"];
    $var_date    = date("F d,Y H:i:s");
    $var_proceed = false;

    $sql = "Select nAccount from ".TABLEPREFIX."users where nUserId = '" . addslashes($userid) . "'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
        if($row = mysqli_fetch_array($result)) {
            $var_account = $row["nAccount"];
        }//end if
    }//end if

//    if($var_settle > $var_account) {
//        $message = "Please enter an amount less than or equal to ". $var_account ;
//    }//end if
//    else {
        $input="";
        for( $i = 0; $i < count( $_POST['chk'] ); $i++ ) {
            $input .= $_POST['chk'][$i] . "," ;
        }//end for loop

        $input=substr($input,0,-1);
        if(strlen($input) > 0) {
            $sql = "Select vSwapStatus from ".TABLEPREFIX."swap where vSwapStatus = '2' AND nSwapId IN($input)";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0) {
                $var_proceed = true;
            }//end if
        }//end if

        if(count($_POST["chk2"]) > 0) {
            $array = explode("|",$_POST["chk2"][0]);
            $sql = "Select nSaleId from ".TABLEPREFIX."saledetails where nSaleId='"
                    . $array[0] . "' AND
                             nUserId='" . $array[1] . "' AND
                             dDate='" . $array[2] . "' AND vSaleStatus='2'";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if(mysqli_num_rows($result) > 0) {
                $var_proceed = true;
            }//end if
        }//end if

        if($var_proceed == true) {
            $var_account += $var_actual;
            $sql = "UPDATE ".TABLEPREFIX."users SET ";
            $sql .= " nAccount='". $var_account ."' WHERE nUserId ='" . $userid . "' ";

            mysqli_query($conn, $sql) or die(mysqli_error($conn));

            $input="";
            for( $i = 0; $i < count( $_POST['chk'] ); $i++ ) {
                $input = $_POST['chk'][$i];
                $var_amnt_txt = $_POST["amn". $input];
                $var_comm_txt = $_POST["com". $input];
                $var_key_txt = urldecode($_POST["key" . $input]);

                settype($var_amnt_txt, "double");
                settype($var_comm_txt, "double");
                $var_real= $var_amnt_txt - $var_comm_txt;
                $sql = "insert into ".TABLEPREFIX."cashtxn(nUserId,nAmount,nCommission,dDate,vMode,vModeNo,vReason,vKey)
                                  values('"
                        . addslashes($userid) . "','"
                        . $var_real . "','"
                        . $var_comm_txt ."',now(),'"
                        . addslashes($var_mode) . "','"
                        . addslashes($var_modeno).  "','".TABLEPREFIX."swap','" . addslashes($var_key_txt) . "')";

                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                $var_cashtxnid = mysqli_insert_id($conn);

                $sql = "Update ".TABLEPREFIX."swap set vSwapStatus='3',nCashTxnId='$var_cashtxnid'
                                     where nSwapId='$input'";

                mysqli_query($conn, $sql) or die(mysqli_error($conn));
            }//end for loop

            $input = "";

            for( $i = 0; $i < count( $_POST['chk2'] ); $i++ ) {
                $input=$_POST['chk2'][$i];
                $var_amnt_txt = $_POST[urlencode("amn2". $input)];
                $var_comm_txt = $_POST[urlencode("com2". $input)];
                $var_key_txt = urldecode($_POST[urlencode("key2". $input)]);

                settype($var_amnt_txt, "double");
                settype($var_comm_txt, "double");
                $var_real= $var_amnt_txt - $var_comm_txt;

                $sql = "insert into ".TABLEPREFIX."cashtxn(nUserId,nAmount,nCommission,dDate,vMode,vModeNo,vReason,vKey)
                                            values('" . addslashes($userid) . "','";
                $sql .= $var_real . "','";
                $sql .= $var_comm_txt ."',now(),'";
                $sql .= addslashes($var_mode) . "','";
                $sql .= addslashes($var_modeno).  "','".TABLEPREFIX."saledetails','" . addslashes($var_key_txt) . "')";

                mysqli_query($conn, $sql) or die(mysqli_error($conn));


                $var_cashtxnid = mysqli_insert_id($conn);


                $array=explode("|",$input);
                $var_saleid=$array[0];
                $var_userid=$array[1] ;
                $var_ddate=$array[2];
                $sql = "Update ".TABLEPREFIX."saledetails set vSaleStatus='3',
                                         nCashTxnId='$var_cashtxnid' where
                                         nSaleId=' " . $var_saleid .  "' AND
                                         nUserId='" . $var_userid . "' AND
                                         dDate='" . $var_ddate  . "'";

                mysqli_query($conn, $sql) or die(mysqli_error($conn));
            }//end for loop

            if($var_account==0) {
                header("location:usersettlements.php?msg=s");
                exit();
            }//end if



            //fetching seller information
            $condition="where nUserId='".$userid."'";
            $SellerName = fetchSingleValue(select_rows(TABLEPREFIX.'users','vFirstName',$condition),'vFirstName');
            $EMail      = fetchSingleValue(select_rows(TABLEPREFIX.'users','vEmail',$condition),'vEmail');
            $pref_lang  = fetchSingleValue(select_rows(TABLEPREFIX.'users','preferred_language',$condition),'preferred_language');

            /*
                 * Fetch user language details
            */

            $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$pref_lang."'";
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
                               AND L.lang_id = '".$pref_lang."'";
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = str_replace('{SITE_NAME}',SITE_NAME,$mailRw['content']);
            $mainTextShow   = str_replace('{SITE_URL}',SITE_URL,$mainTextShow);

            $mailcontent1   = $mainTextShow;


            //send mail to seller
            $subject    = $mailRw['content_title'];
            $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

            $StyleContent=MailStyle($sitestyle,SITE_URL);
            //readf file n replace
            $arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
            $arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,addslashes($SellerName),$mailcontent1,$logourl,date('F d, Y'),SITE_NAME,$subject);
            $msgBody        = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody        = str_replace($arrSearch,$arrReplace,$msgBody);

            send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');
            //send mail to seller end

            $message = "Amount settled successfully!";
        }//end if
        else {
            $message = "Settlement Failed. Please check the items are delivered to the user";
        }//end else
   
}//end if

 $sqluserdetails = "SELECT vLoginName, vFirstName,vLastName,nAccount  FROM ".TABLEPREFIX."users  WHERE  nUserId  = '".$userid."'";
$resultuserdetails  = mysqli_query($conn, $sqluserdetails ) or die(mysqli_error($conn));
$rowuser = mysqli_fetch_array($resultuserdetails);
$txtUserName = $rowuser["vLoginName"];
$txtFirstName = $rowuser["vFirstName"];
$txtLastName = $rowuser["vLastName"];

if($txtLastName !="") {
    $userfullname = $txtFirstName. " ". $txtLastName;
}//end if
else {
    $userfullname = $txtFirstName;
}//end else
if($userfullname=='')
    $userfullname = $rowuser["vLoginName"];
$var_account = $rowuser["nAccount"];


$var_escrow_percent = '';
/*if(DisplayLookUp('14')!='')
{
	$var_escrow_percent = DisplayLookUp('14');
	settype($var_escrow_percent,"double");
}//end if*/

if(DisplayLookUp("Enable Escrow")=="Yes") {
    $escrowType = DisplayLookUp("EscrowCommissionType");
    if($escrowType=="fixed" || $escrowType=="percentage") {
        $var_escrow_percent = DisplayLookUp("14");
        $var_escrow_percent_txt = $var_escrow_percent;
    }else {
        if($escrowType=="range") {
            $var_escrow_percent_txt = "Based on the amount";
        }
    }
}

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<script type="text/javascript" language="javascript">
    var total=0;
    var comm=0;
    var famnt=0;
    function validateAmountForm()
    {
        var frm = window.document.frmUserProfile;
        if(trim(frm.txtAmount.value) == ""){
            alert("Amount cannot be empty.");
            frm.txtAmount.focus();
            return false;
        }else if(isNaN(frm.txtAmount.value)){
            alert("Please enter a valid amount.");
            frm.txtAmount.focus();
            return false;
        }else{
            return true;
        }

    }



function calculate(t,f){

    var id,amnt_id,comm_id,rid;
    rid=t.id;
    if(f == 1){
        id=t.id.substring(4,t.id.length);
        amnt_id="amn2" + id;
        comm_id="com2" + id;
    }
    else{
        id=t.id.substring(3,t.id.length);
        amnt_id="amn" + id;
        comm_id="com" + id;
    }

    if(document.getElementById(t.id).checked){
        //alert('add');
        js_amnt = parseFloat(document.getElementById(amnt_id).value);
        js_comm = parseFloat(document.getElementById(comm_id).value);
    }
        
     xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}//end if

    var url="../check_escrow.php"
    url=url+"?q="+val
    url=url+"&sid="+Math.random()
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function stateChanged()
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{
		document.getElementById("fees").innerHTML=xmlHttp.responseText
                document.getElementById('txtSettleAmount').value=total;
                document.getElementById('txtCommission').value=comm;
                document.getElementById('txtActualAmount').value=js_actual;
	}//end if
}//end function

function GetXmlHttpObject()
{
	var objXMLHttp=null
	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest()
	}//end if
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
	}//end else if
	return objXMLHttp
}//end function  

    function calculateAmount(t,f){

        var id,amnt_id,comm_id,rid;
        rid=t.id;
        if(f == 1){
            id=t.id.substring(4,t.id.length);
            amnt_id="amn2" + id;
            comm_id="com2" + id;
        }
        else{
            id=t.id.substring(3,t.id.length);
            amnt_id="amn" + id;
            comm_id="com" + id;
        }
        
        //		alert(rid);
        //		alert(document.getElementById(rid).value);
        if(document.getElementById(t.id).checked){
            //alert('add');
            js_amnt = parseFloat(document.getElementById(amnt_id).value);
            js_comm = parseFloat(document.getElementById(comm_id).value);
            js_amnt = Math.round(js_amnt*Math.pow(10,2))/Math.pow(10,2);
            js_comm = Math.round(js_comm*Math.pow(10,2))/Math.pow(10,2);
            /*total += parseFloat(document.getElementById(amnt_id).value);
                comm += parseFloat(document.getElementById(comm_id).value);*/
            total += js_amnt;
            comm += js_comm;

        }
        else{
            /*total -= parseFloat(document.getElementById(amnt_id).value);
                comm -= parseFloat(document.getElementById(comm_id).value);*/
            //alert('subtract');
            js_amnt = parseFloat(document.getElementById(amnt_id).value);
            js_comm = parseFloat(document.getElementById(comm_id).value);
            js_amnt = Math.round(js_amnt*Math.pow(10,2))/Math.pow(10,2);
            js_comm = Math.round(js_comm*Math.pow(10,2))/Math.pow(10,2);
            total -= js_amnt;
            comm -= js_comm;


        }
        js_actual = total - comm;
        js_actual = Math.round(js_actual*Math.pow(10,2))/Math.pow(10,2);
        total = Math.round(total*Math.pow(10,2))/Math.pow(10,2);
        comm = Math.round(comm*Math.pow(10,2))/Math.pow(10,2);
        document.getElementById('txtSettleAmount').value=total;
        document.getElementById('txtCommission').value=comm;
        //        document.getElementById('txtActualAmount').value=total - comm;
        document.getElementById('txtActualAmount').value=js_actual;
        return false;
    }

    function checkBox(t,f){
        //alert('LOSTFOCUS');
        var id,chk_id,amnt_id;
        if(f == 1){
            id=t.id.substring(4,t.name.length);
            chk_id="chk2" + id;
            amnt_id = "amn2" + id;
        }
        else{
            id=t.id.substring(3,t.name.length);
            chk_id="chk" + id;
            amnt_id="amn" + id;
        }
        if(isNaN(t.value) || t.value.substring(0,1) == " " || t.value.length == 0 || parseFloat(t.value) < 0 ){
            t.value=0;
        }
        else if(parseFloat(t.value) >= parseFloat(document.getElementById(amnt_id).value)){
            alert('Commission cannot be greater than or equal to the amount');
            t.value=0;
        }
        if(document.getElementById(chk_id).checked){
            comm -= parseFloat(famnt);
            comm += parseFloat(t.value);
            document.getElementById("txtCommission").value=comm;
            document.getElementById("txtActualAmount").value=total - comm;
        }
    }

    function setAmount(t){
        //alert('ONfOCUS');
        if(isNaN(t.value) || t.value.substring(0,1) == " " || t.value.length == 0 || parseFloat(t.value) < 0 ){
            t.value=0;
        }
        famnt = parseFloat(t.value);
    }

    function viewTransaction(swapid,userid,uname,member){
        var str = 'viewtransaction.php?swapid=' + swapid + '&userid=' + userid + '&uname=' + escape(uname) + '&memberid=' + member + '&';
        var left = Math.floor( (screen.width - 700) / 2);
        var top = Math.floor( (screen.height - 400) / 2);

        var loginWindow=window.open(str,"approvalpage","top=" + top + ",left=" + left + ",toolbars=no,maximize=no,resize=no,width=700,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
    }

    function viewSale(saleid,userid,ddate){
        var str = 'viewsale.php?saleid=' + saleid + '&userid=' + userid + '&dDate=' + escape(ddate) + '&';
        var left = Math.floor( (screen.width - 700) / 2);
        var top = Math.floor( (screen.height - 400) / 2);

        var loginWindow=window.open(str,"approvalpage","top=" + top + ",left=" + left + ",toolbars=no,maximize=no,resize=no,width=700,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
    }

</script>
<div class="row admin_wrapper">
	<div class="admin_container">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="padding_T_B_td">
    <tr>
        <td width="18%" valign="top"> <!--  Admin menu comes here -->
            <?php require("../includes/adminmenu.php"); ?>
            <!--   Admin menu  comes here ahead --></td>
		<td width="4%"></td>
        <td width="78%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
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
                    <td width="84%" class="heading_admn boldtextblack" align="left">Edit Details</td>
                    <td width="16%" class="heading_admn ">&nbsp;</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frmUserProfile" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateAmountForm();">
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="center" class="warning"><b>Settlements could be done only after successful delivery of the sale/swap item</b></td>
                                            </tr>
                                            <?php
                                            if(isset($message) && $message!='') {
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                            <?php  }//end if?>
                                            <tr align="right" bgcolor="#FFFFFF"><input type="hidden"  name="userid" value="<?php echo $userid; ?>" />
                                            <input type="hidden"  name="username" value="<?php echo htmlentities($username); ?>" />
                                            <input type="hidden"  name="txtAccount" value="<?php echo $txtAccount;?>" />
                                            <td colspan="2" class="maintext">&lsaquo;&lsaquo;&nbsp;<a href="<?php echo $_SESSION["backurl"]?>"><strong>Back</strong></a></td>
                                            </tr>
                                            <tr align="center" bgcolor="#FFFFFF">
                                                <td colspan="2"><b>Edit amount for  the user '<?php echo htmlentities($userfullname)?>'</b></td>
                                            </tr>
                                            <!--<tr bgcolor="#FFFFFF">
                                                <td width="20%" align="left">Username Name</td>
                                                <td width="80%"><a href="users.php?txtEditSearch=<?php echo $userid;?>"><?php echo htmlentities($userfullname);?></a></td>
                                            </tr>-->
                                           <!-- <tr bgcolor="#FFFFFF">
                                                <td align="left">Last Name</td>
                                                <td><?php echo htmlentities($txtLastName);?></td>
                                            </tr> -->
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" colspan="2">&nbsp;</td>
                                            </tr>
                                            <?php
                                            $amt_to_be_settled = 0;
                                            $sql = "SELECT s.nSwapId,s.nUserId,s.vTitle,s.nSwapMember,s.nSwapAmount,u.vLoginName,s.vOwnerDelivery,
										s.vPartnerDelivery,s.vPostType
					FROM " . TABLEPREFIX . "swap s inner join " . TABLEPREFIX . "users u on
					s.nSwapMember = u.nUserId where s.vSwapStatus= '2' AND s.nSwapAmount > 0 AND s.nSwapMember='" . addslashes($userid)."'";                                           
                                            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                            if(mysqli_num_rows($result) > 0) {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" colspan="2" bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                        <tr align="center" bgcolor="#FFFFFF" class="gray">                                                            
                                                            <td width="16%">Select</td>
                                                            <td width="19%">Swap/Wish Title</td>
                                                            <td width="10%">Delivered</td>
                                                            <td width="15%">Amount(<?php echo CURRENCY_CODE;?>)</td>
                                                            <td width="20%">Escrow Fees(<?php echo getGeneralPercentageText();?>) </td>
                                                            <td width="19%">Amount to be settled(<?php echo CURRENCY_CODE;?>)</td>
                                                        </tr>
                                                        <?php
                                                        $var_count = 0;
                                                        while($row=mysqli_fetch_array($result)) {
                                                        

                                                                ?>
                                                        <tr bgcolor="#FFFFFF">                                                            
                                                            <td align="center"><input type="checkbox" name="chk[]" id="chk<?php echo $row["nSwapId"]?>" value="<?php echo $row["nSwapId"]?>"  onClick="calculateAmount(this,0);"
                                                                <?php
                                                                $str_status=(($row["vPartnerDelivery"] == "Y")?"":"disabled");

                                                                echo($str_status);
                                                                ?>
                                                                                      >
                                                            </td>
                                                            <td class="maintext" align="center">
                                                                <?php
                                                                $sql = "SELECT nSTId from " . TABLEPREFIX . "swaptxn where (nSwapReturnId like '%".$row["nSwapId"]."%' or nSwapId like '%".$row["nSwapId"]."%') and vStatus ='A' and (nUserId='" . $row["nUserId"] . "' or nUserReturnId='" . $row["nUserId"] . "') ";
                                                                $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                                if ($srow = mysqli_fetch_array($res)) {

                                                                    echo "<a href='../makeoffer.php?nSTId=".$srow['nSTId']."&this_user=".$row["nUserId"]."' target='_blank'>".htmlentities($row["vTitle"])."</a>";

                                                                }
                                                                ?>

                                                                 <!--<a href="javascript:viewTransaction(<?php echo $row["nSwapId"]?>,<?php echo $row["nUserId"]?>,'<?php echo urlencode($row["vLoginName"])?>',<?php echo $row["nSwapMember"]?>);"><?php echo htmlentities($row["vTitle"]);?></a>-->
                                                                <input type="hidden" name="key<?php echo $row["nSwapId"]?>" id="key<?php echo $row["nSwapId"]?>" value="<?php echo urlencode($row["nSwapId"] . chr(236) . $row["nUserId"] . chr(236) . $row["nSwapMember"] . chr(236) . $row["vLoginName"])?>"></td>
                                                            <td align="center"><?php echo ($str_status == "disabled")?"Pending":"Completed";?></td>
                                                            <td align="center">
                                                                <?php echo $namt = ($row["nSwapAmount"] < 0)?(-1 * $row["nSwapAmount"]):$row["nSwapAmount"]; ?>
                                                                <input type="hidden" name="amn<?php echo $row["nSwapId"]?>" id="amn<?php echo $row["nSwapId"]?>" value="<?php echo $namt; ?>" class="textbox" readonly style="border:0 solid #000000; background-color:#B3BBC4; text-align:right" size="16">
                                                            </td>
                                                            <td align="center">                                                               
                                                               <?php echo $var_escrow = getEscrowSettleAmount($row["nSwapAmount"]);?> <?php echo "(". getEscrowPercentage($row["nSwapAmount"]).")";?>
                                                                 <input type="hidden" name="com<?php echo $row["nSwapId"]?>" id="com<?php echo $row["nSwapId"]?>" value="<?php echo $var_escrow?>" class="textbox" onChange="javascript:checkBox(this,0);" onFocus="javascript:setAmount(this,0);" readonly style="border: 0 solid #FFFFFF; background-color:#B3BBC4; text-align:right" size="16">
                                                            </td>
                                                            <td align="center">
                                                                <?php echo ($namt-$var_escrow);?>
                                                                
                                                            </td>
                                                        </tr>
                                                                        <?php
                                                                        $var_count++;
                                                                        $amt_to_be_settled += ($namt-$var_escrow);
                                                                    }//end while
                                                                    ?>
                                                    </table></td>
                                            </tr>
                                                                    <?php }//end if?>
                                           
                                                <?php
                                                $sql = "SELECT s.nSaleId,st.nQuantity,st.dDate,st.nAmount,st.vMethod,st.vDelivered,
									 s.vTitle,st.nUserId from " . TABLEPREFIX . "sale s inner join  " . TABLEPREFIX . "saledetails st
									 on s.nSaleId = st.nSaleId where st.vSaleStatus = '2' AND s.nUserId='"
						. addslashes($userid) . "'";
                                            $result= mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                            if(mysqli_num_rows($result) > 0) {

                                               ?>
                                            <tr bgcolor="#FFFFFF"><td colspan="6"><b> <i>Note : </i> You have to select the check box under the 'SELECT' Column to display the amounts for Amount being Settled, Escrow Fees, Actual Amount etc.</b></td></tr>
                                             <tr bgcolor="#FFFFFF">
                                                <td align="left" colspan="2">&nbsp;</td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                        <tr align="center" bgcolor="#FFFFFF" class="gray">                                                           
                                                            <td width="10%">Select</td>
                                                            <td width="19%">Sale Title</td>
                                                            <td width="10%">Delivered</td>
                                                            <td width="19%">Amount(<?php echo CURRENCY_CODE;?>)</td>
                                                            <td width="20%">Escrow Fees(<?php echo getGeneralPercentageText();?>)</td>
                                                            <td width="20%"><?php echo TEXT_AMOUNT_TO_SETTLE; ?>(<?php echo CURRENCY_CODE; ?>)</td>
                                                        </tr>
                                                <?php
                                                $var_count=0;
                                                while($row = mysqli_fetch_array($result)) {                                             
                                                                ?>
                                                        <tr bgcolor="#FFFFFF">
                                                            
                                                            <td align="center">
                                                                <input type="checkbox" name="chk2[]" id="chk2<?php echo $row["nSaleId"] . "|" . $row["nUserId"] . "|" . $row["dDate"]?>" value="<?php echo $row["nSaleId"] . "|" . $row["nUserId"] . "|" . $row["dDate"]?>"  onClick="calculateAmount(this,1);" <?php echo ($row["vDelivered"] == "Y")?"":"disabled";?>>
                                                            </td>
                                                            <td class="maintext" align="center">
                                                                <a href="javascript:viewSale(<?php echo $row["nSaleId"]?>,<?php echo $row["nUserId"]?>,'<?php echo urlencode($row["dDate"])?>');"><?php echo htmlentities($row["vTitle"])?></a>
                                                                <input type="hidden" name="<?php echo urlencode("key2" . $row["nSaleId"] . "|" . $row["nUserId"] . "|" . $row["dDate"])?>" id="<?php echo "key2" . $row["nSaleId"] . "|" . $row["nUserId"] . "|" . $row["dDate"]?>" value="<?php echo urlencode($row["nSaleId"] . "|" . $row["nUserId"] . "|" . $row["dDate"])?>"></td>
                                                            <td align="center"><?php echo ($row["vDelivered"] == "Y")?"Yes":"No";?></td>
                                                            <td align="center">
                                                                <?php echo $row["nAmount"]?>
                                                                <input type="hidden" name="<?php echo urlencode("amn2" . $row["nSaleId"] . "|" . $row["nUserId"] . "|" . $row["dDate"])?>" id ="<?php echo "amn2" . $row["nSaleId"] . "|" . $row["nUserId"] . "|" . $row["dDate"]?>"  value="<?php echo $row["nAmount"]?>" class="textbox" style="border:0 solid #000000;  text-align:right" size="16" >
                                                            </td>
                                                            <td align="center">
                                                                <?php echo $var_escrow = getEscrowSettleAmount($row["nAmount"]); ?> <?php echo "(". getEscrowPercentage($row["nAmount"]).")";?>
                                                                <input type="hidden" name="<?php echo urlencode("com2" . $row["nSaleId"] . "|" . $row["nUserId"] . "|" . $row["dDate"])?>" id ="<?php echo "com2" . $row["nSaleId"] . "|" . $row["nUserId"] . "|" . $row["dDate"]?>" value="<?php echo $var_escrow?>" class="textbox"  onChange="javascript:checkBox(this,1);"  onFocus="javascript:setAmount(this,1);" readonly style="border: 0 solid #FFFFFF;  text-align:right" size="16">
                                                                
                                                            </td>
                                                            <td align="center">
                                                                <?php echo ($row["nAmount"]-$var_escrow);?>
                                                            </td>
                                                        </tr>
                                                                <?php
                                                                $var_count++;
                                                                $amt_to_be_settled += ($row["nAmount"]-$var_escrow);
                                                            }//end while
                                                        ?>
                                                    </table>
                                                </td>
                                            </tr>
                                            <?php }//end if?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" colspan="2">&nbsp;</td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Total Amount of Transaction(s)</td>
                                                <td><?php echo CURRENCY_CODE;?><input type="text" name="txtAmount" id="txtAmount" value="<?php echo $amt_to_be_settled;//echo $var_account;?>" class="textbox" style="border: 0 solid #FFFFFF; " readonly></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Selected Transaction Amount</td>
                                                <td><?php echo CURRENCY_CODE;?><input name="txtSettleAmount" type="text" class="textbox" id="txtSettleAmount" value="0" size="10"  style="border: 0 solid #FFFFFF; " readonly></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Escrow Fees</td>
                                                <td><?php echo CURRENCY_CODE;?><input name="txtCommission" type="text" class="textbox" id="txtCommission" value="0" size="10"  style="border: 0 solid #FFFFFF; " readonly></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Amount going to Settle</td>
                                                <td><?php echo CURRENCY_CODE;?><input name="txtActualAmount" type="text" class="textbox" id="txtActualAmount" value="0" size="10" maxlength="10"  style="border: 0 solid #FFFFFF; "  readonly/></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="left">&nbsp;</td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Mode</td>
                                                <td><select name="cmbMode" class="textbox" id="cmbMode">
                                                    <?php
                                                    $sql="Select vLookUpDesc from ".TABLEPREFIX."lookup where nLookUpCode='1'";
                                                    $result=mysqli_query($conn, $sql);
                                                    if(mysqli_num_rows($result) > 0) {
                                                        while($row=mysqli_fetch_array($result)) {
                                                            ?>
                                                            <option value="<?php echo $row["vLookUpDesc"]?>"><?php echo $row["vLookUpDesc"]?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Reference Number</td>
                                                <td>
                                                    <input name="txtReference" type="text" class="textbox" id="txtReference" size="30" maxlength="100">
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td>
                                                    <input type="submit" name="btnSubmit" value="Settle Amount" class="submit">
                                                </td>
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
</div>
</div>
<?php include_once('../includes/footer_admin.php');?>