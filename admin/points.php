<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE = ClientFilePathName($_SERVER['PHP_SELF']);

if (function_exists('get_magic_quotes_gpc')) {
    $txtName = stripslashes($_POST['txtName']);
    $txtPointValue = stripslashes($_POST['txtPointValue']);
    $txtPointValue2 = stripslashes($_POST['txtPointValue2']);
    $radEnablePoint = stripslashes($_POST['radEnablePoint']);
    $radTransfer = stripslashes($_POST['radTransfer']);
    //$txtFee = stripslashes($_POST['txtFee']);
}//end if
else {
    $txtName = $_POST['txtName'];
    $txtPointValue = $_POST['txtPointValue'];
    $txtPointValue2 = $_POST['txtPointValue2'];
    $radEnablePoint = $_POST['radEnablePoint'];
    $radTransfer = $_POST['radTransfer'];
    //$txtFee = $_POST['txtFee'];
}//end else

if (isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] == "Change Point System") {

    //updation for point name
//    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtName) . "' where nLookUpCode='PointName'") or die(mysqli_error($conn));

    //updation for point value
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtPointValue) . "' where nLookUpCode='PointValue'") or die(mysqli_error($conn));
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtPointValue2) . "' where nLookUpCode='PointValue2'") or die(mysqli_error($conn));

    //updation for point enable/disble in the entire website
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($radEnablePoint) . "' where nLookUpCode='EnablePoint'") or die(mysqli_error($conn));

    switch ($radEnablePoint) {
        case "1":
            //disable sell in this system
            mysqli_query($conn, "UPDATE " . TABLEPREFIX . "client_module_category SET vActive='0' where
											nCategoryId='2'") or die(mysqli_error($conn));
            break;

        case "0":
        case "2":
            //enable sell in this system
            mysqli_query($conn, "UPDATE " . TABLEPREFIX . "client_module_category SET vActive='1' where
											nCategoryId='2'") or die(mysqli_error($conn));
            break;
    }//end switch
    //update point transfer between users
    mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($radTransfer) . "' where nLookUpCode='PointTransfer'") or die(mysqli_error($conn));

    //update point success fee
   // mysqli_query($conn, "Update " . TABLEPREFIX . "lookup set vLookUpDesc='" . addslashes($txtFee) . "' where nLookUpCode='SuccessFee'") or die(mysqli_error($conn));

    $message = "Settings updated";
}//end if
else {
    $txtName = POINT_NAME;
    $txtPointValue = DisplayLookUp('PointValue');
    $txtPointValue2 = DisplayLookUp('PointValue2');
    $radEnablePoint = DisplayLookUp('EnablePoint');
    $radTransfer = DisplayLookUp('PointTransfer');
    //$txtFee = DisplayLookUp('SuccessFee');
}//end if
?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script language="javascript" type="text/javascript">
    function checkpoints(){
        var currentPointSystem = <?php echo $radEnablePoint;?>;
        var selectedPointSystem = $('input[@name="radEnablePoint"]:checked').val();
        if(currentPointSystem != selectedPointSystem){
            if(confirm('Switching the operation mode in between would corrupt all the old data that used the previous operation mode. Do you wish to continue?')){
                return true;
            }else{
                return false;
            }
        }
    }
    function validateSettingsForm()
    {
        var frm = window.document.frmPoints;

 
        if(document.getElementById('ShowPointsYes2').style.display!='none')
        {
            if(frm.txtPointValue.value=='' || parseInt(frm.txtPointValue.value) <= 0)
            {
                alert("Conversion Rate can't be blank or zero");
                frm.txtPointValue.focus();
                return false;
            }//end if
            if(frm.txtPointValue2.value=='' || parseInt(frm.txtPointValue2.value) <= 0)
            {
                alert("Conversion Rate can't be blank or zero");
                frm.txtPointValue2.focus();
                return false;
            }//end if
            /*if(frm.txtFee.value=='')
            {
                alert("Success Fee can't be blank");
                frm.txtFee.focus();
                return false;
            }//end if
            if(frm.txtFee.value<0)
            {
                alert("Success Fee Should be valid");
                frm.txtFee.focus();
                return false;
            }//end if
            */
        }//end if
        return true;
    }

    //function for disable/enable
    function showSettings(nVar)
    {
        if(nVar=='Yes')
        {
            document.getElementById('ShowPointsYes2').style.display='';
            document.getElementById('ShowPointsYes3').style.display='';
        }//end if
        else
        {
            document.getElementById('ShowPointsYes2').style.display='none';
            document.getElementById('ShowPointsYes3').style.display='none';
        }//end else
    }//end function

    function checkValue(t)
    {
        if(isNaN(t.value) || t.value.substring(0,1) == " " || t.value.length == 0 || parseFloat(t.value) < 0 )
        {
            t.value=0;
        }//end if
    }//end function
    
 
</script>
<script language="javascript" type="text/javascript" src="../js/qTip.js"></script>

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
                    <td class="heading_admn newborder" align="left"><span class="boldtextblack">Points Manager</span></td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2">
                            <tr>
                                <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frmPoints" method ="POST"  action = "<?php echo $_SERVER['PHP_SELF'] ?>" onsubmit="return validateSettingsForm();">
                                        <?php
                                        if (isset($message) && $message != '') {
                                            ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td colspan="3" align="center" class="success"><?php echo $message; ?></td>
                                                </tr>
                                            <?php }//end if
                                            ?>
                                            <!--<input name="radEnablePoint" type="hidden" value="2" />
                                            
                                            This is commented because for the baratar customization this changing option is not required
                                            -->
                                            <tr bgcolor="#FFFFFF">
                                                <td width="36%" align="left">Point Mode</td>
                                                <td width="64%" colspan="2">
                                                    <input name="radEnablePoint" type="radio" value="2" <?php echo  $radEnablePoint == "2" ? "checked" : "" ?> onClick="showSettings('Yes');"> Points & Currency
                                                    <input name="radEnablePoint" type="radio" value="1" <?php echo  $radEnablePoint == "1" ? "checked" : "" ?> onClick="showSettings('Yes');"> Points Only 
                                                    <input name="radEnablePoint" type="radio" value="0" <?php echo  $radEnablePoint == "0" ? "checked" : "" ?> onClick="showSettings('No');"> Currency Only
                                                    &nbsp;&nbsp;&nbsp;<i title="Choose the mode in which your site has to run on" id="help1"><strong>?</strong></i>                                                   
                                                </td>
                                            </tr>
                                            
                                            <!--<tr bgcolor="#FFFFFF" id="ShowPointsYes" style="<?php //if ($radEnablePoint != '1' && $radEnablePoint != '2') { echo 'display:none;'; } ?>">
                                                <td width="36%" align="left">Name of Points</td>
                                                <td width="64%" colspan="2"><input type="text" class="textbox2" name="txtName" value="<?php //echo htmlentities($txtName); ?>" readonly size="40" maxlength="40" onKeyPress="document.getElementById('showPointName').innerHTML=document.frmPoints.txtName.value;"></td>
                                            </tr>-->
                                            <tr bgcolor="#FFFFFF" id="ShowPointsYes2" style="<?php if ($radEnablePoint != '1' && $radEnablePoint != '2') {
                                                echo 'display:none;';
                                            } ?>">
                                                <td width="36%" align="left">Point Conversion Rate </td>
                                                <td width="64%" colspan="2"><span id="showPointName"><?php echo POINT_NAME; ?></span> <input name="txtPointValue2" type="text" class="textbox2" size="5" maxlength="100" value="<?php echo htmlentities($txtPointValue2); ?>" onBlur="javascript:checkValue(this);"> = <?php echo CURRENCY_CODE; ?> <input name="txtPointValue" type="text" class="textbox2" size="5" maxlength="100" value="<?php echo htmlentities($txtPointValue); ?>" onBlur="javascript:checkValue(this);">
                                                    <i title="Provide here the value of the <?php echo POINT_NAME; ?> against the currency value" id="help2"><strong>?</strong></i>
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF" id="ShowPointsYes3" style="<?php if ($radEnablePoint != '1' && $radEnablePoint != '2') {
                                                echo 'display:none;';
                                            } ?>">
                                                <td align="left">Allow Point Transfer Between Users</td>
                                                <td colspan="2">
                                                    <input type="radio" name="radTransfer" value="1" <?php if ($radTransfer == '1' || $radEnablePoint == '2') {
                                                        echo 'checked';
                                                    }if ($radTransfer == '') {
                                                        echo 'checked';
                                                    } ?>>Yes
                                                            <input type="radio" name="radTransfer" value="0" <?php if ($radTransfer == '0') {
                                                        echo 'checked';
                                                    } ?>>No
                                                    &nbsp;&nbsp;&nbsp;<i title="Select 'Yes' to allow the points to be transferred between site users" id="help3"><strong>?</strong></i>
                                                </td>
                                            </tr>
                                            <!--<tr bgcolor="#FFFFFF" id="ShowPointsYes4" style="<?php //if ($radEnablePoint != '1' && $radEnablePoint != '2') { echo 'display:none;'; } ?>">
                                                <td align="left">Success Fee for a Completed Transaction </td>
                                                <td colspan="2"><?php //echo CURRENCY_CODE; ?><input type="text" class="textbox2" name="txtFee" value="<?php //echo htmlentities($txtFee); ?>" size="5" maxlength="40" onBlur="javascript:checkValue(this);"></td>
                                            </tr>-->
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="3">
                                                    <font color="#0A6500;">Note :- 'Sale' Module will no longer be active, if point mode set to 'Points Only' option.</font>
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td colspan="2"><input type="submit" name="btnSubmit" value="Change Point System" class="submit" onClick="return checkpoints();"></td>
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
<?php include_once('../includes/footer_admin.php'); ?>