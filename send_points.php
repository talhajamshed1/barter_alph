<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');
include_once('./includes/title.php');

$errsflag = 0;

//checking admin allow transfer between users
if (DisplayLookUp('PointTransfer') != '1') {
    header('location:my_points.php');
    exit();
}//end if
//fetch logged user total points
$showUserTotalPoints = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');

if ($showUserTotalPoints > 0) {
    $showUserTotalPoints = $showUserTotalPoints;
}//end if
else {
    $showUserTotalPoints = '0';
}//end else
//send points to friend
if (isset($_POST['btnGo']) && $_POST['btnGo'] != '') {
    if (function_exists('get_magic_quotes_gpc') ) {
        $txtRedeemPoint = stripslashes($_POST['txtRedeemPoint']);
        $ddlUser = stripslashes($_POST['ddlUser']);
    }//end if
    else {
        $txtRedeemPoint = $_POST['txtRedeemPoint'];
        $ddlUser = $_POST['ddlUser'];
    }//end else
    //checking enter user points and avilable points
    if ($txtRedeemPoint < $showUserTotalPoints) {
        //add points to track history
        mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "pointhistory (nSendBy,nSendTo,nPoints,dDate) VALUES ('" . $_SESSION["guserid"] . "',
							'" . $ddlUser . "','" . $txtRedeemPoint . "',now())") or die(mysqli_error($conn));

        //redeem points from user
        mysqli_query($conn, "UPDATE " . TABLEPREFIX . "usercredits set nPoints=nPoints-$txtRedeemPoint WHERE
								nUserId='" . $_SESSION["guserid"] . "'") or die(mysqli_error($conn));

        //checking alredy exits
        $chkPoint = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "WHERE nUserId='" . $ddlUser . "'"), 'nPoints');
        if (trim($chkPoint) != '') {
            //update points to frined credit
            mysqli_query($conn, "UPDATE " . TABLEPREFIX . "usercredits set nPoints=nPoints+$txtRedeemPoint WHERE
									nUserId='" . $ddlUser . "'") or die(mysqli_error($conn));
        }//end if
        else {
            //add points to frined credit
            mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "usercredits (nPoints,nUserId) VALUES ('" . $txtRedeemPoint . "','" . $ddlUser . "')") or die(mysqli_error($conn));
        }//end else
        //fetch logged user total points
        $showUserTotalPoints = fetchSingleValue(select_rows(TABLEPREFIX . 'usercredits', 'nPoints', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'nPoints');

        $msg = str_replace('{point_name}',POINT_NAME,MESSAGE_POINT_SENT_SUCCESSFULLY);// . '';
        $errsflag = 0;
    }//end if
    else {
        $msg = '<b>'.str_replace('{point_name}',POINT_NAME,ERROR_POINT_INVALID).'</b>';
        $errsflag = 1;
    }//end else
}//end if
?>
<script language="javascript1.1" type="text/javascript" src="js/points.js"></script>
<script language="javascript1.1" type="text/javascript">
    function checkNumeric(ids)
    {
        var val=document.getElementById(ids).value;

        if ((isNaN(val))||(val<0)||(parseInt(val,10)<0))
        {
            alert("<?php echo ERROR_POINT_POSITIVE_VALUE; ?>");
            document.getElementById(ids).value="0";
            document.getElementById(ids).focus();
        }//end if
    }//end function

    function Validate()
    {
        var s=document.frmSend;
        if(s.ddlUser.value=='')
        {
            alert("<?php echo ERROR_USERNAME_SELECT; ?>");
            s.ddlUser.focus();
            return false;
        }
        if(s.txtRedeemPoint.value=='' || s.txtRedeemPoint.value=='0')
        {
            alert("<?php echo str_replace('{point_name}',POINT_NAME,ERROR_INVALID_POINT); ?>");
            s.txtRedeemPoint.focus();
            return false;
        }//end if
        return true;
    }//end function
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
                <div class="col-lg-9">
                    <div class="full-width">
                        <div class="innersubheader2 row">
                            <div class="col-lg-12">
                            <h3><?php echo str_replace('{point_name}',POINT_NAME,HEADING_SEND_POINT); ?></h3>
                            </div>
                        </div>
                        <div class="space">&nbsp;</div>
                    </div>
                    <div class="row">
                    	<div class="col-lg-12">
                        	<div class="table-responsive">
                            <table width="100%"  border="0" cellspacing="0" cellpadding="table">
                                            <tr>
                                                <td align="left" valign="top"><?php include('./includes/points_menu.php'); ?>
                                                    <form action="" method="post" name="frmSend" onSubmit="return Validate();">
                                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td class="tabContent tabcontent_wrapper">
                                                                    <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="table table-bordered">
                                                                        <?php
                                                                        if (isset($msg) && $msg != '') {
                                                                            if($errsflag == 0) $cls="success";else $cls="warning";
                                                                            echo '<tr align="center"  class='.$cls.'><td colspan="3"><b>' . $msg . '</b></td></tr>';
                                                                        }//end if
                                                                        if ($showUserTotalPoints > 0) {
                                                                            ?>
                                                                            <tr align="right" >
                                                                                <td align="left"><?php echo TEXT_AVAILABLE; ?> <?php echo POINT_NAME; ?></td>
                                                                                <td colspan="2" align="left"><?php echo $showUserTotalPoints; ?></td>
                                                                            </tr>
                                                                            <tr align="right" >
                                                                                <td width="32%" align="left"><?php echo TEXT_SELECT_USER; ?> </td>
                                                                                <td colspan="2" align="left">
                                                                                    <select name="ddlUser" class="comm_input width1a">
                                                                                    <?php
                                                                                    //create user list
                                                                                    $sqlUserList = mysqli_query($conn, "SELECT vLoginName,nUserId from " . TABLEPREFIX . "users WHERE vStatus='0' AND vDelStatus='0' AND nUserId!='" . $_SESSION["guserid"] . "'
                                                                                                                                                                 ORDER BY dDateReg DESC") or die(mysqli_error($conn));
                                                                                    if (mysqli_num_rows($sqlUserList) > 0) {
                                                                                        while ($arrUser = mysqli_fetch_array($sqlUserList)) {
                                                                                            echo '<option value="' . $arrUser['nUserId'] . '">' . $arrUser['vLoginName'] . '</option>';
                                                                                        }//end while loop
                                                                                    }//end if
                                                                                    ?>
                                                                                    </select>
                                                                                </td>
                                                                            </tr>
                                                                            <tr align="right" >
                                                                                <td align="left"><?php echo str_replace('{point_name}',POINT_NAME,TEXT_NO_OF_POINTS_TO_SEND); ?></td>
                                                                                <td width="22%" align="left"><input type="text" class="comm_input width1"  id="txtRedeemPoint" onChange="checkNumeric(this.id)"  name="txtRedeemPoint" size="4" maxlength="5" onKeyUp="showPoints(this.value);" onMouseDown="showPoints(this.value);"/></td>
                                                                                <td width="46%" align="left"><span id="txtDisplayPoints" class="warning"></span></td>
                                                                            </tr>
                                                                            <tr align="right" >
                                                                                <td align="left">&nbsp;</td>
                                                                                <td colspan="2" align="left"><input type="submit" name="btnGo" value="<?php echo MENU_SEND; ?>" class="submit">
                                                                                    <input type="reset" name="btnReset" value="<?php echo BUTTON_RESET; ?>" class="submit_grey"></td>
                                                                            </tr>
                                                                                        <?php
                                                                                    }//end if
                                                                                    else {
                                                                                        echo '<tr align="center"  class="warning"><td colspan="3"><b>'.  str_replace('{points}', $showUserTotalPoints, str_replace('{point_name}', POINT_NAME, ERROR_INSUFFICIENT_POINTS)).'</b></td></tr>';
                                                                                    }//end else
                                                                                    ?>
                                                                    </table>

                                                                </td></tr>
                                                        </table></form></td>
                                            </tr>
                                        </table>
                            </div>
                        </div>
                    </div>
                    
                	<div class="full-width subbanner">
						<?php include('./includes/sub_banners.php'); ?>
                    </div>
                    </div>
				</div>
			</div>
		</div>
    
<?php require_once("./includes/footer.php"); ?>