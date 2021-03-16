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
$PGTITLE='featuredapproval';

$activateflag ="";
if($_POST["postback"] == "CS") { //for changing status

    $var_id = $_POST["saleextraid"];

    if($_POST["changeto"] == "A") {
        //start of approval of sale
        $sql = "Select * from ".TABLEPREFIX."saleextra ";
        $sql .= " where nSaleextraId='" . addslashes($var_id) . "' ";

        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if(mysqli_num_rows($result) > 0) {
            if($row = mysqli_fetch_array($result)) {
                //Begin
                if($row["nFeatured"]>0) {
                    $vfeatured="Y";
                    $action.="F";
                }//end if
                else {
                    $vfeatured="N";
                }//end else

                if($row["nCommission"]>0) {
                    $action.="C";
                }//end if

                $totalamt= $row["nCommission"] + $row["nFeatured"];

                //insert data to sales table


                $txtCategory =  $row["nCategoryId"];
                $txtTitle = $row["vTitle"];
                $txtBrand = $row["vBrand"];
                $ddlType  = $row["vType"];
                $txtYear = $row["vYear"];
                $ddlCondition = $row["vCondition"];
                $txtValue =  $row["nValue"];
                $txtPostDate = $row["dPostDate"];
                $txtUser = $row["nUserId"];
                $txtReference =  $row["vReferenceNo"];
                $txtName1 = $row["vName"];
                $txtBank = $row["vBank"];
                $txtRefDate  = $row["dReferenceDate"];
                $txtMode  = $row["vMode"];

                $sql = "INSERT INTO ".TABLEPREFIX."sale (nSaleId, nCategoryId, nUserId,";
                $sql .= "vTitle, vBrand, vType, vCondition, vYear, nValue,";
                $sql .= "nShipping, vUrl, vDescription, dPostDate, nQuantity,vFeatured,vDelStatus,vSmlImg,vImgDes)";
                $sql .= "VALUES ('', '".$row["nCategoryId"]."', '";
                $sql .= $row["nUserId"];
                $sql .= "', '".addslashes(stripslashes($row["vTitle"]))."',";
                $sql .= "'".addslashes(stripslashes($row["vBrand"]))."', '".$row["vType"]."', '".$row["vCondition"]."', '".$row["vYear"]."', '".$row["nValue"]."',";
                $sql .= "'".$row["nShipping"]."', '".$row["vUrl"]."', '".addslashes(stripslashes($row["vDescription"]))."','";
                $sql .= $row["dPostDate"]. "'";
                $sql .= ", '".$row["nQuantity"]."','$vfeatured','0','".$row["vSmlImg"]."','".addslashes(stripslashes($row["vImgDes"]))."')";
                $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));

                //inert multiple images
                $NewId=mysqli_insert_id($conn);

                //update table with new id
                mysqli_query($conn, "update ".TABLEPREFIX."gallery  set nTempId='', nSaleId='".$NewId."'
						where nTempId='".$row["nUserId"]."'") or die(mysqli_error($conn));

                //get last posted sale id
                $sql = "Select nSaleId from ".TABLEPREFIX."sale where dPostDate='$txtPostDate' AND nUserId = '$txtUser'";
                $result=mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) > 0) {
                    if($row=mysqli_fetch_array($result)) {
                        $saleid1= $row["nSaleId"];
                    }//end if
                }//end if

                //insert to payemnt table
                $sql="INSERT INTO ".TABLEPREFIX."paymentdetails (nPaymentId, vName, vReferenceNo, vBank, dReferenceDate, dEntryDate) VALUES ('', '$txtName1', '$txtReference', '$txtBank', '$txtRefDate', now())";
                $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $tid=mysqli_insert_id($conn);

                $sql="INSERT INTO ".TABLEPREFIX."payment (nTxn_no, vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId) VALUES ('', '$action', '$tid', ' $totalamt', '$txtMode', '$txtPostDate', '', '$saleid1')";
                $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));

                //delete from temp table
                $sql="delete from ".TABLEPREFIX."saleextra where nSaleextraId='$var_id'";
                $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));

                //update category listings
                $routesql="Select vRoute from ".TABLEPREFIX."category where nCategoryId ='$txtCategory'";
                $result=mysqli_query($conn, $routesql) or die(mysqli_error($conn));
                $row = mysqli_fetch_array($result);
                $route = $row["vRoute"];
                if($route){
                $countsql="UPDATE ".TABLEPREFIX."category SET nCount=nCount+1 WHERE nCategoryId in($route)";
                $result=mysqli_query($conn, $countsql) or die(mysqli_error($conn));               
                }
                
                $sql = "Select vFirstName,vEmail,preferred_language,vLoginName from ".TABLEPREFIX."users where vAlertStatus='Y' OR nUserId = '" . $_SESSION["guserid"] . "'";
                $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if(mysqli_num_rows($result) > 0) {
                    while($row=mysqli_fetch_array($result)) {

                        $categorysql = "Select L.vCategoryDesc from " . TABLEPREFIX . "category C
                                LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" .$row["preferred_language"]. "'
                            where C.nCategoryId ='$txtCategory'";
                        $resultcategory=mysqli_query($conn, $categorysql) or die(mysqli_error($conn));
                        $crow = mysqli_fetch_array($resultcategory);
                        
                        $txtCategoryname = $crow["vCategoryDesc"];

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
                                       AND C.content_name = 'addsales'
                                       AND C.content_type = 'email'
                                       AND L.lang_id = '".$row["preferred_language"]."'";
                        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                        $mailRw  = mysqli_fetch_array($mailRs);

                        $mainTextShow   = $mailRw['content'];

                        if($enbPnt=="2"){
                            if(!$newValue && !$txtValue){
                                $mainTextShow   = str_replace("{pricename}", "", $mainTextShow);
                                $mainTextShow   = str_replace("{txtValue}", "", $mainTextShow);
                            }
                            $mainTextShow   = str_replace("{pricename}",POINT_NAME.'/'.TEXT_PRICE, $mainTextShow);
                            $itemValue  =   $txtPoint."/".CURRENCY_CODE.$txtValue;
                        }else if($enbPnt=="1"){
                            if(!$newValue){
                                $mainTextShow   = str_replace("{pricename}", "", $mainTextShow);
                                $mainTextShow   = str_replace("{txtValue}", "", $mainTextShow);
                            }
                            $mainTextShow   = str_replace("{pricename}",POINT_NAME, $mainTextShow);
                            $itemValue  =   $txtPoint;

                        }else{
                            if(!$newValue){
                                $mainTextShow   = str_replace("{pricename}", "", $mainTextShow);
                                $mainTextShow   = str_replace("{txtValue}", "", $mainTextShow);
                            }
                            $mainTextShow   = str_replace("{pricename}",TEXT_PRICE, $mainTextShow);
                            $itemValue  =   $txtValue;
                            if($itemValue)
                               $itemValue  =   CURRENCY_CODE.$itemValue;
                        }

                        $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{TYPE}","{txtCategoryname}","{txtTitle}","{txtBrand}","{ddlType}","{ddlCondition}","{txtYear}","{txtValue}");
                        $arrTReplace	= array(SITE_NAME,SITE_URL,"sale",$txtCategoryname,$txtTitle,$txtBrand,$ddlType,$ddlCondition,$txtYear,CURRENCY_CODE.$txtValue);
                        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                        $mailcontent1   = $mainTextShow;

                        $subject      = $mailRw['content_title'];
                        $subject      = str_replace('{SITE_NAME}',SITE_NAME,$subject);
                        $StyleContent = MailStyle($sitestyle,SITE_URL);

                        $EMail = stripslashes($row["vEmail"]);
                        if($subject){
                        //readf file n replace
                        $arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
                        $arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,htmlentities($row["vLoginName"]),$mailcontent1,$logourl,date('F d, Y'),SITE_NAME,$subject);
                        $msgBody        = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                        $msgBody=str_replace($arrSearch,$arrReplace,$msgBody);

                        send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');
                        }
                    }//end while
                }//end if

                echo "<script>alert('Transaction approved.')</script>";
                //echo "<br>Featured item added<br><br><br><br>";
            }//end if

//=================================== /validated by ipn================
        }//end if
    }//end if
    else {
        echo "<script>alert('Transaction already approved.')</script>";
    }//end else
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
        $qryopt .= " AND u.vLoginName like '" . addslashes($txtSearch) . "%'";
    }//end if
    else if($ddlSearchType == "bank") {
        $qryopt .= " AND s.vBank  like '" . addslashes($txtSearch) . "%'";
    }//end else if
    else if($ddlSearchType == "referenceno") {
        $qryopt .= " AND s.vReferenceNo  like '" . addslashes($txtSearch) . "%'";
    }//end else if
}//end if

if(!isset($begin) || $begin =="") {
    $begin = 0;
}//end if

$sql = "Select s.vTitle,u.vLoginName,s.nSaleextraId,s.nUserId,date_format(s.dPostDate,'%m/%d/%Y') as dPostDate1 ,s.vBank,s.vReferenceNo,s.dReferenceDate,s.vMode from ".TABLEPREFIX."saleextra s ";
$sql .= "inner join ".TABLEPREFIX."users u on s.nUserId = u.nUserId ";
$sql .= " Where s.vMode!=''";

$sess_back="featuredapproval.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $ddlSearchType . "&txtSearch=" . urlencode($txtSearch) . "&";

$_SESSION["backurl"] = $sess_back;

$sql .= $qryopt . " Order By s.dPostDate Desc ";
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$navigate = pageBrowser($totalrows,10,10,"&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql.$navigate[0];

$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
?>
<script language="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmAdminMain.submit();
    }
    function changeStatus(id) {
        var frm = document.frmAdminMain;
        if(confirm("Are you sure you want to approve this item addition?")){
            frm.changeto.value="A";
            frm.saleextraid.value=id;
            frm.postback.value="CS";
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
                    <td width="100%" class="heading_admn boldtextblack" align="left">Item Addition To Be Approved</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">
                                        <form  name="frmAdminMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" >

<?php
$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);

if(isset($message) && $message!='') {
    ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="6" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
    <?php  } //end if
    else {
    ?>
    										<tr bgcolor="#FFFFFF">
                                                <td colspan="6" align="center" class="warning"><?php echo ITEM_ADDITION_APPROVAL_TEXT;?></td>
                                            </tr>
    <?php 
    }
    ?>
                                            <tr bgcolor="#FFFFFF"><input type="hidden" name="saleextraid" value="">
                                            <input type="hidden" name="changeto" value="">
                                            <input type="hidden" name="postback" value="">
                                            <td colspan="6" align="center"><table border="0" width="100%" class="maintext">
                                                    <tr>
                                                        <td valign="top" align="right">
                                                            Search
                                                            &nbsp; <select name="ddlSearchType" class="textbox2">
                                                                <option value="username" <?php if($ddlSearchType== "username" || $ddlSearchType == "") {
    echo("selected");
} ?>>User Name</option>
                                                                <option value="bank" <?php if($ddlSearchType == "bank") {
                                                echo("selected");
                                            } ?>>Bank</option>
                                                                <option value="referenceno" <?php if($ddlSearchType== "referenceno") {
                                                echo("selected");
                                            } ?>>Reference No</option>
                                                            </select>
                                                            &nbsp;<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo htmlentities($txtSearch)?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }"  class="textbox2">
                                                        </td>
                                                        <td align="left" valign="baseline">
                                                            <a href="javascript:clickSearch();" class="link_style2">
                                                            GO</a>
                                                        </td>
                                                    </tr>
                                                </table></td>
                                            </tr>
                                            <tr align="center" bgcolor="#FFFFFF" class="gray">
                                                <td align="center" width="7%">Sl No. </td>
                                                <td align="center" width="16%">Title</td>
                                                <td align="center" width="19%">User Name </td>
                                                <td align="center" width="19%">Date</td>
                                                <td align="center" width="19%">Reference No </td>
                                                <td align="center" width="20%">Approve</td>
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
        ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="center"><?php echo $cnt;?></td>
                                                <td align="center" class="maintext"><?php echo "<a href='approveitem.php?id=".$arr["nSaleextraId"]."'>".restrict_string_size($arr["vTitle"],20)."</a>";?></td>
                                                <td align="center"><?php echo "<a href='approveitem.php?id=".$arr["nSaleextraId"]."'>".restrict_string_size($arr["vLoginName"],20)."</a>";?></td>
                                                <td align="center"><?php echo "<a href='approveitem.php?id=".$arr["nSaleextraId"]."'>".date('F d, Y',strtotime($arr["dPostDate1"]))."</a>";?></td>
                                                <td align="center"><?php echo "<a href='approveitem.php?id=".$arr["nSaleextraId"]."'>".htmlentities($arr['vReferenceNo'])."</a>";?></td>
                                                <td align="center"><?php
                                                    $statustextorlink = "<a href=\"javascript:changeStatus('".$arr["nSaleextraId"]."');\">Activate</a>";
                                                    echo $statustextorlink;?></td>
                                            </tr>
                                                    <?php
                                                    $cnt++;
                                                }//end while
                                            }//end if
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="6" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
                                                        <tr>
                                                            <td class="noborderbottm" align="left"><?php echo($navigate[2]);?></td>
                                                            <td class="noborderbottm" align="right"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
                                                        </tr>
                                                    </table>
                                                </td>
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