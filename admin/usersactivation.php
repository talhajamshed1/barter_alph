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
$PGTITLE='useractivation';

$activateflag ="";

if(isset($_POST["postback"]) && $_POST["postback"]=="CSR") { //for changing status
    $userid = $_POST["userid"];
    mysqli_query($conn, "update ".TABLEPREFIX."users set vStatus='1',vDelStatus='1' where nUserId='".addslashes($userid)."'")
            or die(mysqli_error($conn));
    $message = "User deactivated successfully.";
    $activateflag = false;

}//end if

if(isset($_POST["postback"]) && $_POST["postback"]=="CS") { //for changing status
    $userid = $_POST["userid"];

    if($_POST["changeto"] == "A") {
        $sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , vTxnId, ";
        $sql .="vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
        $sql .="vDescription  ,dDateReg,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee,nRefId,vReferenceNo,vName,
					vBank,dReferenceDate,preferred_language, nPlanId  from ".TABLEPREFIX."users where nUserId='" . addslashes($userid) . "'";

        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if(mysqli_num_rows($result) > 0) {
            if($row = mysqli_fetch_array($result)) {
                //start the activation
                $var_txnid		= $row["vTxnId"];
                $var_first_name = $row["vFirstName"];
                $totalamt		= $row["nAmount"];
                $paytype		= $row["vMethod"];
                $var_email		= $row["vEmail"];

                if ($paytype <> "cc" AND $paytype <> "pp" AND $paytype <> "free") {
                    //II - Store check details
                    $sql = "insert into ".TABLEPREFIX."paymentdetails(vName,vReferenceNo,vBank,dReferenceDate,dEntryDate) ";
                    $sql .= " Values('" . addslashes($row["vName"]) . "','" . addslashes($row["vReferenceNo"]) . "','" . addslashes($row["vBank"]) . "','" . addslashes($row["dReferenceDate"]) . "',now())";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    $var_txnid = mysqli_insert_id($conn);
                }//end if

                //III - add the user table entry
                $var_first_name = $row["vFirstName"];
                $totalamt		= $row["nAmount"];
                $paytype		= $row["vMethod"];
                $var_email		= $row["vEmail"];

                $sql = "UPDATE ".TABLEPREFIX."users SET vTxnId='".addslashes($var_txnid)."',
						vStatus='0',vDelStatus='0'	WHERE nUserId='".$row['nUserId']."'";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                $var_new_id = mysqli_insert_id($conn);
                if(is_null($var_new_id) || $var_new_id == 0)
                    $var_new_id = $row['nUserId'];

                //if ($paytype <> "cc" AND $paytype <> "pp" AND $paytype <> "free") {
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
                    }//end if of referrals
                    //V - Update transaction table

                    $sql="INSERT INTO ".TABLEPREFIX."payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId)
                                                                                    VALUES ('R', '$var_txnid', ' $totalamt', '$paytype',now(), '". $var_new_id ."', '')";
                    //$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    $sql = "UPDATE " . TABLEPREFIX . "payment
                         SET nUserId = $userid, nPlanId = " . $row['nPlanId'] . " where vTxn_id = '" . $var_txnid . "'";
                    //$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                /* }else {
                    $sql = "UPDATE " . TABLEPREFIX . "payment
                         SET nUserId = $userid where vTxn_id = '" . $row['vTxnId'] . "'";
                    //$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    if(!is_null($var_new_id) && $var_new_id) {
                        $sql	= "UPDATE ".TABLEPREFIX."payment
                                                 SET nUserId = '{$var_new_id}'
                                                 WHERE nUserId = '".addslashes($userid)."'";
                        $res	= mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }
                } */

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
            }//end if
        }//end if
        else {
            $message = "Sorry,the given user doesn't exist as deactivated.Please check the user list for the username.";
            $activateflag = false;
        }//end else
    }//end if
}//end if

$qryopt="";
if($_POST["txtSearch"] != "") {
    $txtSearch = $_POST["txtSearch"];
}//end if
else if($_GET["txtSearch"] != "") {
    $txtSearch = $_GET["txtSearch"];
}//end else if
if($_POST["ddlSearchType"] != "") {
    $ddlSearchType = $_POST["ddlSearchType"];
}//end if
else if($_GET["ddlSearchType"] != "") {
    $ddlSearchType = $_GET["ddlSearchType"];
}//end else if

if($txtSearch != "") {
    if($ddlSearchType == "username") {
        $qryopt .= " AND vLoginName like '" . addslashes($txtSearch) . "%'";
    }//end if
    // else if($ddlSearchType == "city") {
    //     $qryopt .= " AND vCity  like '" . addslashes($txtSearch) . "%'";
    // }//end else if
    else if($ddlSearchType == "date") {
        $date = $txtSearch;
       // $arr = split("/",$date);
        $arr = explode("/",$date);
        if(strlen($arr[0]) < 2) {
            $month = "0".$arr[0];
        }//end fi
        else {
            $month = $arr[0];
        }//end else
        if(strlen($arr[1]) < 2) {
            $day = "0".$arr[1];
        }//end else
        else {
            $day = $arr[1];
        }//end else

        $year = $arr[2];
        $newdate = $year ."-". $month ."-". $day;
        $qryopt .= " AND dDateReg  like '" . addslashes($newdate) . "%'";
    }//end if
}//end if

if(!isset($begin) || $begin =="") {
    $begin = 0;
}

$sql = "Select nUserId,vLoginName,vCity,date_format(dDateReg,'%m/%d/%Y') as 'dDateReg',vReferenceNo,dReferenceDate from ".TABLEPREFIX."users
			WHERE vStatus='1' AND vDelStatus='0'";

$sess_back="usersactivation.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $ddlSearchType . "&txtSearch=" . urlencode($txtSearch) . "&";

$_SESSION["backurl"] = $sess_back;

$sql .= $qryopt . " Order By nUserId Desc";
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,10,10,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql.$navigate[0];
$rs = mysqli_query($conn, $sql);
?>
<script language="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmAdminMain.submit();
    }
    function ChangeStatus(id,status){
        var frm = document.frmAdminMain;
        if(status == "A"){
            changeto = "bar";
        }else{
            changeto = "unbar";
        }
        if(confirm("Are you sure you want to "+ changeto +" this user?")){
            frm.changeto.value=status;
            frm.userid.value=id;
            frm.postback.value="CS";
            frm.submit();
        }
    }

    function changeStatus(id) {
        var frm = document.frmAdminMain;
        if(confirm("Are you sure you want to activate this user?")){
            frm.changeto.value="A";
            frm.userid.value=id;
            frm.postback.value="CS";
            frm.submit();
        }
    }
    function changeStatus2(id)
    {
        var frm = document.frmAdminMain;
        if(confirm("Are you sure you want to reject this user?"))
        {
            frm.userid.value=id;
            frm.postback.value="CSR";
            frm.submit();
        }//end if
    }//end function

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
                    <td width="100%" class="heading_admn boldtextblack" align="left">Users To Be Activated</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="admin_tble_2">
                                        <form  name="frmAdminMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" >

                                                <?php
                                                            $message=($message!='')?$message:$_SESSION['sessionMsg'];
                                                unset($_SESSION['sessionMsg']);

                                                if(isset($message) && $message!='') {
                                                    ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="6" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                                <?php  }//end if?>
                                            <tr bgcolor="#FFFFFF"><input type="hidden" name="userid" value="">
                                            <input type="hidden" name="changeto" value="">
                                            <input type="hidden" name="postback" value="">
                                            <td colspan="6" align="center"><table border="0" width="100%" class="maintext">
                                                    <tr>
                                                        <td valign="top" align="right">
                                                            Search
                                                            &nbsp; <select name="ddlSearchType" class="textbox2">
                                                                <option value="username" <?php if($ddlSearchType== "username" || $ddlSearchType == "") {
                                                                        echo("selected");
                                                                    } ?>>User Name
                                                                </option>
                                                                <!-- <option value="city" <?php if($ddlSearchType == "city") {
                                                                        echo("selected");
                                                                    } ?>>City
                                                                </option> -->
                                                                <option value="date" <?php if($ddlSearchType== "date") {
                                                                        echo("selected");
                                                                    } ?>>Date Registered(mm/dd/yyyy)
                                                                </option>
                                                            </select>
                                                            &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="textbox2">
                                                        </td>
                                                        <td align="left" valign="baseline">
                                                            <a href="javascript:clickSearch();" class="link_style2">
                                                            GO
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table></td>
                                            </tr>
                                            <tr align="center" bgcolor="#FFFFFF" class="gray">
                                                <td align="center" width="7%">Sl No. </td>
                                                <td align="center" width="16%">User Name </td>
                                                <!-- <td align="center" width="19%">City</td> -->
                                                <td align="center" width="19%">Date of Reg </td>
                                                <!-- <td align="center" width="19%">Reference No </td> -->
                                                <td align="center" width="20%">Activate</td>
                                            </tr>
                                                <?php
                                                if(mysqli_num_rows($rs)>0) {
                                                    switch($_GET['begin']) {
                                                        case "":
                                                            $cnt=1;
                                                            break;

                                                        default:
                                                            $cnt=$_GET['begin']+1;
                                                            break;
                                                    }//end switch

                                                    while ($arr = mysqli_fetch_array($rs)) {

                                                        //echo "<pre>";print_r($arr);
                                                        $statustextorlink = "<a href=\"javascript:changeStatus('".$arr["nUserId"]."');\">Activate</a>";
                                                        $statustextorlink2 = "<a href=\"javascript:changeStatus2('".$arr["nUserId"]."');\">Reject</a>";
                                                        ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="center"><?php echo $cnt;?></td>
                                                <td align="center" class="maintext"><?php echo '<a href="activateuser.php?id='.$arr["nUserId"].'" title="Click here to activate/delete user">'.restrict_string_size($arr["vLoginName"],15).'</a>';?></td>
                                                <!-- <td align="center"><?php echo '<a href="activateuser.php?id='.$arr["nUserId"].'" title="Click here to activate/delete user">'.htmlentities($arr["vCity"]).'</a>';?></td> -->
                                                <td align="center"><?php echo '<a href="activateuser.php?id='.$arr["nUserId"].'" title="Click here to activate/delete user">'.date('F d, Y',strtotime($arr["dDateReg"])).'</a>';?></td>
                                                <!-- <td align="center"><?php echo '<a href="activateuser.php?id='.$arr["nUserId"].'" title="Click here to activate/delete user">'.htmlentities($arr["vReferenceNo"]).'</a>';?></td> -->
                                                <td align="center"><?php echo $statustextorlink;?>&nbsp;|&nbsp;<?php echo $statustextorlink2;?></td>
                                            </tr>
                                                    <?php
                                                    $cnt++;
                                                }//end while
                                            }//end if
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="6" align="left" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
                                                        <tr>
                                                            <td align="left"><?php echo($navigate[2]);?></td>
                                                            <td align="right"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
                                                        </tr>
                                                    </table>
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