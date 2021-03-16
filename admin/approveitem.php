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

if(isset($_GET["id"]) && $_GET["id"]!="" ) {
    $var_id = $_GET["id"];
}//end if
else if(isset($_POST["id"]) && $_POST["id"]!="" ) {
    $var_id = $_POST["id"];
}//end else if

$add_flag = false;
$flag_proceed=false;


$sql = "SELECT * FROM ".TABLEPREFIX."saleextra  WHERE  nSaleextraId  = '". addslashes($var_id)."'";

$result  = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if(mysqli_num_rows($result) > 0) {
    if($row = mysqli_fetch_array($result)) {
        $var_nSaleextraId = $row["nSaleId"];
        $var_vTitle = $row["vTitle"];
        $var_vBrand = $row["vBrand"];
        $var_vType = $row["vType"];
        $var_vCondition = $row["vCondition"];
        $var_vYear = $row["vYear"];
        $var_nValue = $row["nValue"];
        $var_nPoint = $row["nPoint"];
        $var_nShipping = $row["nShipping"];
        $var_vUrl = $row["vUrl"];
        $var_vDescription = $row["vDescription"];
        $var_dPostDate = $row["dPostDate"];
        $var_nQuantity = $row["nQuantity"];
        $var_nFeatured = $row["nFeatured"];
        $var_nCommission = $row["nCommission"];
        $var_vReferenceNo = $row["vReferenceNo"];
        $var_vName = $row["vName"];
        $var_vBank = $row["vBank"];
        $var_dReferenceDate = $row["dReferenceDate"];
        $var_vMode = $row["vMode"];

        switch($var_vMode) {
            case "bu" :
                $disp_method = "Business Check";
                break;
            case "ca" :
                $disp_method = "Cashiers Check";
                break;
            case "wt" :
                $disp_method = "Wire Transfer";
                break;
            case "mo" :
                $disp_method = "Money Order";
                break;
            case "pc" :
                $disp_method = "Personal Check";
                break;
        }//end switch
    }//end if
}//end if
else {
    echo "<script>alert('Please try again!'); window.location.href='saleapproval.php'</script>";
    exit();
}//end else


if($_POST["postback"] == "Y") { //for changing status

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
            $txtPoint =  $row["nPoint"];
            $txtPostDate = $row["dPostDate"];
            $txtUser = $row["nUserId"];
            $txtReference =  $row["vReferenceNo"];
            $txtName1 = $row["vName"];
            $txtBank    = $row["vBank"];
            $txtRefDate = $row["dReferenceDate"];
            $txtMode    = $row["vMode"];

            $sql = "INSERT INTO ".TABLEPREFIX."sale (nSaleId, nCategoryId, nUserId,";
            $sql .= "vTitle, vBrand, vType, vCondition, vYear, nValue, nPoint,";
            $sql .= "nShipping, vUrl, vDescription, dPostDate, nQuantity,vFeatured,vDelStatus,vSmlImg,vImgDes)";
            $sql .= "VALUES ('', '".$row["nCategoryId"]."', '";
            $sql .= $row["nUserId"];
            $sql .= "', '".addslashes(stripslashes($row["vTitle"]))."',";
            $sql .= "'".addslashes(stripslashes($row["vBrand"]))."', '".$row["vType"]."', '".$row["vCondition"]."', '".$row["vYear"]."', '".$row["nValue"]."',  '".$row["nPoint"]."',";
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
            $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
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
            $countsql="UPDATE ".TABLEPREFIX."category SET nCount=nCount+1 WHERE nCategoryId in($route)";
            $result=mysqli_query($conn, $countsql) or die(mysqli_error($conn));

            $categorysql = "Select L.vCategoryDesc from " . TABLEPREFIX . "category C
                                LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                            where C.nCategoryId ='$txtCategory'";
            $resultcategory=mysqli_query($conn, $categorysql) or die(mysqli_error($conn));
            $row = mysqli_fetch_array($resultcategory);
            $txtCategoryname = $row["vCategoryDesc"];


            $sql    = "Select vFirstName,vEmail,preferred_language from ".TABLEPREFIX."users where vAlertStatus='Y' OR nUserId = '" . $_SESSION["guserid"] . "'";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if(mysqli_num_rows($result) > 0) {
                while($row=mysqli_fetch_array($result)) {

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

                    $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{txtCategoryname}","{txtTitle}","{txtBrand}","{txtBrand}","{ddlType}","{txtYear}","{txtValue}");
                    $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_URL,$txtCategoryname,$txtTitle,$txtBrand,$txtBrand,$ddlType,$txtYear,$txtValue);
                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                    $mailcontent1   = $mainTextShow;

                    $subject      = $mailRw['content_title'];
                    $subject      = str_replace('{SITE_NAME}',SITE_NAME,$subject);
                    $StyleContent = MailStyle($sitestyle,SITE_URL);

                    $EMail = stripslashes($row["vEmail"]);
                    //readf file n replace
                    $arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
                    $arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,htmlentities($row["vFirstName"]),$mailcontent1,$logourl,date('F d, Y'),SITE_NAME,$subject);
                    $msgBody    = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody    = str_replace($arrSearch,$arrReplace,$msgBody);

                    send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');
                }//end while
            }//end if
            echo "<script>alert('Transaction Approved!');</script>";
            header('location:featuredapproval.php');
            exit();
        }//end if
    }//end if
}//end if
else if($_POST["postback"] == "D") {
    $sql = "Delete from  ".TABLEPREFIX."saleextra where nSaleextraId='" . addslashes($var_id) . "'";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
    echo "<script>alert('Transaction entry deleted!'); window.location.href='featuredapproval.php'</script>";
    exit();
}//end else if
?>
<script language="javascript1.1" type="text/javascript">
    function validateForm()
    {
        if(document.frmUserProfile.postback.value.length == 0)
        {
            return false;
        }//end if
        else
        {
            return true;
        }//end else
    }//end fucntion

    function clickButton(i)
    {
        if(i == 0)
        {
            document.frmUserProfile.postback.value = 'D';
        }//end if
        else if(i == 1)
        {
            document.frmUserProfile.postback.value = 'Y';
        }//end else
        document.frmUserProfile.submit();
    }//end fucntion
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
                    <td width="84%" class="heading_admn boldtextblack" align="left">Item addition to be approved</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
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
                                            <tr align="right" bgcolor="#FFFFFF"><input type="hidden"  name="id" value="<?php echo $var_id; ?>" />
                                            <input type="hidden"  name="postback"  id="postback" value="" />
                                            <td colspan="2"><a href="<?php echo $_SESSION["backurl"]?>" class="style2"><b>Back</b></a>&nbsp;</td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="left"><strong>Item Details</strong></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="20%" align="left">Title</td>
                                                <td width="80%" align="left"><?php echo htmlentities($var_vTitle); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Brand</td>
                                                <td align="left"><?php echo htmlentities($var_vBrand); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Type</td>
                                                <td align="left"><?php echo htmlentities($var_vType); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Condition</td>
                                                <td align="left"><?php echo htmlentities($var_vCondition); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Year</td>
                                                <td align="left"><?php echo htmlentities($var_vYear); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left"><?php echo POINT_NAME; ?></td>
                                                <td align="left"><?php echo htmlentities($var_nPoint); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Price</td>
                                                <td align="left"><?php echo CURRENCY_CODE.htmlentities($var_nValue); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Shipping</td>
                                                <td align="left"><?php echo CURRENCY_CODE.htmlentities($var_nShipping); ?></td>
                                            </tr>
                                            <!--<tr bgcolor="#FFFFFF">
                                                <td align="left">URL</td>
                                                <td align="left"><?php //echo htmlentities($var_vUrl); ?></td>
                                            </tr>-->
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Description</td>
                                                <td align="left"><?php echo htmlentities($var_vDescription); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Posted On (mm/dd/yyyy)</td>
                                                <td align="left"><?php echo change_date_format(htmlentities($var_dPostDate),'mysql-to-mmddyy'); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Quantity</td>
                                                <td align="left"><?php echo htmlentities($var_nQuantity); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Featured Amount</td>
                                                <td align="left"><?php echo CURRENCY_CODE.htmlentities($var_nFeatured); ?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Commission</td>
                                                <td align="left"><?php echo CURRENCY_CODE.htmlentities($var_nCommission); ?></td>
                                            </tr>
                                            <!--<tr bgcolor="#FFFFFF">
                                                <td align="left">Title</td>
                                                <td align="left"><?php echo htmlentities($var_title); ?></td>
                                            </tr>-->
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Name</td>
                                                <td align="left"><input name="txtName" type="text" class="TextBox" id="txtName" size="40" maxlength="100" value="<?php echo htmlentities($var_vName)?>" Readonly></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Reference No.</td>
                                                <td align="left"><input name="txtReferenceNo" type="text" class="TextBox" value="<?php echo htmlentities($var_vReferenceNo); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Bank(If Applicable)</td>
                                                <td align="left"><input name="txtBank" type="text" class="TextBox" value="<?php echo htmlentities($var_vBank); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Reference Date (mm/dd/yyyy)</td>
                                                <td align="left"><input name="txtRefDate" type="text" class="TextBox" value="<?php echo change_date_format(htmlentities($var_dReferenceDate),'mysql-to-mmddyy'); ?>" size="40" maxlength="100" readonly /></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Payment Method</td>
                                                <td align="left"><?php echo $disp_method?></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td align="left"><input name="btnSubmit" type="button" class="submit" value="Approve Item"  onClick="javascript:clickButton(1);"/>
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
</div>
</div>
<?php include_once('../includes/footer_admin.php');?>