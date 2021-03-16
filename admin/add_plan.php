<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
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
$PGTITLE='plans';

if($_GET['mode']=='edit') {
    $sql = "SELECT *
              FROM ".TABLEPREFIX."plan P
              JOIN ".TABLEPREFIX."plan_lang L
                ON P.nPlanId = L.plan_id
               AND L.lang_id = '".$_SESSION["lang_id"]."'
               AND P.nPlanId = '".$_GET['nPlanId']."'";
    $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if(mysqli_num_rows($rs)>0) {
        $plan_row   = mysqli_fetch_array($rs);
        $txtTitle   = $plan_row['vPlanName'];
        $txtAmount  = $plan_row['nPrice'];
        $radActive  = $plan_row['vActive'];
        $chkPeriod  = $plan_row['vPeriods'];
    }
    $mode='edit';
    $headingVal = 'Edit Plan';
    $btnVal='Update';
}
else {
	$headingVal = 'Add Plan';
    $btnVal='Add Plan';
    //$txtAmount='1';
}

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]!='')
{
    //print_r($_POST);

    if (function_exists('get_magic_quotes_gpc'))
	{
		$nPlanId = stripslashes($_POST['nPlanId'] );
		$txtAmount = stripslashes($_POST["txtAmount"]);
		$radActive =  stripslashes($_POST["radActive"]);
		$chkPeriod =  stripslashes($_POST["chkPeriod"]);
	}
	else
	{
		$nPlanId = $_POST['nPlanId'] ;
		$txtAmount = $_POST["txtAmount"];
		$radActive =  $_POST["radActive"];
		$chkPeriod =  $_POST["chkPeriod"];
	}
}
if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Add Plan') {
    $txtPlanName    = $_POST["txtTitle"];
    
    $free_plan_check=mysqli_query($conn, "select vPeriods from ".TABLEPREFIX."plan where vPeriods='F'") or die(mysqli_error($conn));
    if(mysqli_num_rows($free_plan_check)>0) {
        $radPlanType2=mysqli_result($free_plan_check,0,'vPeriods');
        //checking newly created plan type and existing plan type
        if($chkPeriod == $radPlanType2) {
            $message= "A free plan already exists! You cannot have more than one free plan!";
        }
        else {
            //checking plan name already exist
             $checkSql   = "SELECT L.vPlanName
                              FROM ".TABLEPREFIX."plan P
                              JOIN ".TABLEPREFIX."plan_lang L
                                ON P.nPlanId = L.plan_id
                               AND L.vPlanName='".addslashes($txtPlanName[0])."'";
            $chekNme    = mysqli_query($conn, $checkSql) or die(mysqli_error($conn));
            if(mysqli_num_rows($chekNme)>0) {
                $message = "Plan name already exist choose another name.";
            }
            else {
                $free_plan_check=mysqli_query($conn, "select vPeriods from ".TABLEPREFIX."plan where vPeriods='F'") or die(mysqli_error($conn));
                $sql = mysqli_query($conn, "SELECT MAX(nPosition) as max from ".TABLEPREFIX."plan") or die(mysqli_error($conn));
                $rw = mysqli_fetch_array($sql);
                $maxorder = $rw['max']+1;

                $sqlIns = " INSERT INTO ".TABLEPREFIX."plan (nPrice,vActive,vPeriods,nPosition) VALUES ('".addslashes($txtAmount)."','".addslashes($radActive)."','".addslashes($chkPeriod)."','".$maxorder."')";
                mysqli_query($conn, $sqlIns) or die(mysqli_error($conn));
                $last_insert_id = mysqli_insert_id($conn);
             
                $i = 0;
                foreach($txtPlanName as $planname) {
                $language_id = $_POST["lang$i"];

                $insert_sql = "INSERT INTO ".TABLEPREFIX."plan_lang (plan_id,lang_id,vPlanName) VALUES('".$last_insert_id."','".$language_id."','".addslashes($planname)."')";
                mysqli_query($conn, $insert_sql);
                $i++;
                }
                header('location:plans.php?msg=a');
                exit();
            }
        }// (no free plan exist)
    }
    else { 
        //checking plan name already exist
        $chekNme=mysqli_query($conn, "SELECT vPlanName FROM ".TABLEPREFIX."plan P
                                               JOIN ".TABLEPREFIX."plan_lang L
                                                 ON P.nPlanId = L.plan_id
                                                AND L.vPlanName='".addslashes($txtPlanName[0])."'") or die(mysqli_error($conn));
        if(mysqli_num_rows($chekNme)>0) {
            $message = "Ist Plan name already exist choose another name.";
        }
        else {
            $free_plan_check=mysqli_query($conn, "select vPeriods from ".TABLEPREFIX."plan where vPeriods='F'") or die(mysqli_error($conn));

            $sql = mysqli_query($conn, "SELECT MAX(nPosition) as max from ".TABLEPREFIX."plan") or die(mysqli_error($conn));
            $rw = mysqli_fetch_array($sql);
            $maxorder = $rw['max']+1;

            $sqlIns = " INSERT INTO ".TABLEPREFIX."plan (nPrice,vActive,vPeriods,nPosition) VALUES ('".addslashes($txtAmount)."','".addslashes($radActive)."','".addslashes($chkPeriod)."','".$maxorder."')";
            mysqli_query($conn, $sqlIns) or die(mysqli_error($conn));
            $last_insert_id = mysqli_insert_id($conn);

            $i = 0;
            foreach($txtPlanName as $planname) {
            $language_id = $_POST["lang$i"];

            $insert_sql = "INSERT INTO ".TABLEPREFIX."plan_lang (plan_id,lang_id,vPlanName) VALUES('".$last_insert_id."','".$language_id."','".addslashes($planname)."')";
            mysqli_query($conn, $insert_sql);
            $i++;
            }

            header('location:plans.php?msg=a');
            exit();
        }
    }
}

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Update') {
     $txtPlanName    = $_POST["txtTitle"];
     
    $free_plan_check=mysqli_query($conn, "select vPeriods from ".TABLEPREFIX."plan where vPeriods='F' and nPlanId!='".$nPlanId."'") or die(mysqli_error($conn));
    if(mysqli_num_rows($free_plan_check)>0) {
        $radPlanType2=mysqli_result($free_plan_check,0,'vPeriods');
        //checking newly created plan type and existing plan type
        if($chkPeriod == $radPlanType2) {
            $message= "A free plan already exists! You cannot have more than one free plan!";
        }
        else {
            //checking plan name already exist
            $chekNme=mysqli_query($conn, "SELECT L.vPlanName
                                    FROM ".TABLEPREFIX."plan P
                                    JOIN ".TABLEPREFIX."plan_lang L
                                      ON P.nPlanId = L.plan_id
                                     AND L.vPlanName='".addslashes($txtPlanName[0])."'
                                     AND P.nPlanId!='".$nPlanId."'") or die(mysqli_error($conn));
            if(mysqli_num_rows($chekNme)>0) {
                $message = "Plan name already exist choose another name.";
            }
            else {
                $sqlUpdate = " UPDATE ".TABLEPREFIX."plan SET vActive='".addslashes($radActive)."',
											nPrice='".addslashes($txtAmount)."',vPeriods='".addslashes($chkPeriod)."'
											WHERE nPlanId='".$nPlanId."'";
                mysqli_query($conn, $sqlUpdate) or die(mysqli_error($conn));

                $i = 0;
                foreach($txtPlanName as $planname) {
                $language_id = $_POST["lang$i"];

                $check_sql  = "SELECT * FROM ".TABLEPREFIX."plan_lang WHERE lang_id = '".$language_id."' AND plan_id='".$nPlanId."'";
                $check_rs   = mysqli_query($conn, $check_sql);

                if(mysqli_num_rows($check_rs)){
                    $insert_sql = "UPDATE ".TABLEPREFIX."plan_lang SET vPlanName = '".addslashes($planname)."'
                                WHERE plan_id='".$nPlanId."' AND lang_id = '".$language_id."'";
                }else{
                    $insert_sql = "INSERT INTO ".TABLEPREFIX."plan_lang (plan_id,lang_id,vPlanName) VALUES('".$nPlanId."','".$language_id."','".addslashes($planname)."')";
                }
                mysqli_query($conn, $insert_sql);
                $i++;
                }

                header('location:plans.php?msg=e');
                exit();
            }
        }//(no free plan exist)
    }
    else {
        //checking plan name already exist
        $chekNme=mysqli_query($conn, "SELECT L.vPlanName
                                    FROM ".TABLEPREFIX."plan P
                                    JOIN ".TABLEPREFIX."plan_lang L
                                      ON P.nPlanId = L.plan_id
                                     AND L.vPlanName='".addslashes($txtPlanName[0])."'
                                     AND P.nPlanId!='".$nPlanId."'") or die(mysqli_error($conn));
        if(mysqli_num_rows($chekNme)>0) {
            $message = "Plan name already exist choose another name.";
        }
        else {
            $sqlUpdate = " UPDATE ".TABLEPREFIX."plan SET vActive='".addslashes($radActive)."',nPrice='".addslashes($txtAmount)."',vPeriods='".addslashes($chkPeriod)."'
			   WHERE nPlanId='".$nPlanId."'";
            mysqli_query($conn, $sqlUpdate) or die(mysqli_error($conn));

            $i = 0;
            foreach($txtPlanName as $planname) {
            $language_id = $_POST["lang$i"];

            $check_sql  = "SELECT * FROM ".TABLEPREFIX."plan_lang WHERE lang_id = '".$language_id."' AND plan_id='".$nPlanId."'";
            $check_rs   = mysqli_query($conn, $check_sql);

            if(mysqli_num_rows($check_rs)){
                $insert_sql = "UPDATE ".TABLEPREFIX."plan_lang SET vPlanName = '".addslashes($planname)."'
                            WHERE plan_id='".$nPlanId."' AND lang_id = '".$language_id."'";
            }else{
                 $insert_sql = "INSERT INTO ".TABLEPREFIX."plan_lang (plan_id,lang_id,vPlanName) VALUES('".$nPlanId."','".$language_id."','".addslashes($planname)."')";
            }
            mysqli_query($conn, $insert_sql);
            $i++;
            }
            header('location:plans.php?msg=e');
            exit();
        }
    }
}

$langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                                                    WHERE lang_status = 'y'";
$langRs      = mysqli_query($conn, $langSql);

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<script src="../js/jquery.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">



    function  validatePlan()
    { 
        var x = document.frmPlan["txtTitle[]"];
        for( var i = 0; i < x.length; i++ ){
        if(x[i].value=="")
            {
             alert("All language titles are mandatory");         
             return false;
            }
        }  

        /*var s=document.frmPlan;
        if($("input[name='txtTitle[]']").val()=='')
        {
            alert("Title can't be blank");         
            return false;
        }*/


                      
        if(s.txtAmount.value=='' || s.txtAmount.value==0)
        {
            // If plan period is not free check the plan amount
            // / plan amount must be greater than zero
            if ($("input[name='chkPeriod']:checked").val() != 'F') {
                alert("Please enter valid plan amount");
                s.txtAmount.focus();
                return false;
            }
        }
        return true;
    }
</script>

<div class="row admin_wrapper">
	<div class="admin_container">
	
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="padding_T_B_td">
    <tr>
        <td width="18%" valign="top"> <!--  Admin menu comes here -->
            <?php require("../includes/adminmenu.php"); ?>
            <!--   Admin menu  comes here ahead --></td>
		<td width="4%" valign="top"></td>
        <td width="78%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
                </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="94%" height="32" class="headerbg">&nbsp;</td>
                    <td width="6%" align="right" valign="bottom" class="headerbg"><a href="adminmain.php"><img src="../images/home-icon1.gif" width="44" height="25" border="0"></a></td>
                </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td width="84%" class="heading_admn boldtextblack" align="left"><?php echo $headingVal;?></td>
                    <td width="16%" class="heading_admn">&nbsp;</td>
                </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frmPlan" method ="POST" action = "" onsubmit="return validatePlan();">
                                            <?php if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                            <?php  }?>
                                            <tr bgcolor="#FFFFFF">
                                            <input type="hidden" name="nPlanId" value="<?php echo $_GET['nPlanId'];?>">
                                            <td colspan="2" align="left" class="warning"> * indicates mandatory fields</td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Plan Period <span class="warning">*</span></td>
                                                <td><input name="chkPeriod" type="radio" value="M" <?php if($chkPeriod=='M') {
                                                    echo 'checked';
                                                }if
                                                ($chkPeriod=='') {
                                                    echo 'checked';
                                                }?>>
						      Monthly  &nbsp;  <input type="radio" name="chkPeriod" value="Y" <?php if($chkPeriod=='Y') {
                                                    echo 'checked';
                                                }?>>Yearly  &nbsp;  <input type="radio" name="chkPeriod" value="F" <?php if($chkPeriod=='F') {
                                                    echo 'checked';
                                                }?>>Free</td>
                                            </tr>

                                            <?php
                                            $i=0;
                                            while($langRow = mysqli_fetch_array($langRs)) {
                                             if($mode=="edit") {
                                               $bl_sql = "SELECT * FROM " .TABLEPREFIX . "plan_lang L
                                                           JOIN " .TABLEPREFIX . "plan P
                                                             ON P.nPlanId = L.plan_id
                                                            AND L.lang_id = '".$langRow["lang_id"]."'
                                                            AND L.plan_id = '".$plan_row["plan_id"]."'";
                                                $bl_rs = mysqli_query($conn, $bl_sql) ;
                                                $bl_rw = mysqli_fetch_array($bl_rs);
                                                $txtTitle   = $bl_rw['vPlanName'];
                                                $txtAmount  = $bl_rw['nPrice'];
                                                $radActive  = $bl_rw['vActive'];
                                                $chkPeriod  = $bl_rw['vPeriods'];
                                            }
                                            ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="31%" align="left"><?php echo $langRow["lang_name"];?> Name <span class="warning">*</span></td>
                                                <td width="69%">
                                                    <input name="txtTitle[]" class="textbox2" id="txtTitle" value="<?php echo $txtTitle?>" size="35" maxlength="100" />
                                                    <input type="hidden" name="lang<?php echo $i; ?>" value="<?php echo $langRow["lang_id"]?>" >
                                                </td>
                                            </tr>
                                            <?php $i++;
                                            }?>
                                            <tr valign="top" bgcolor="#FFFFFF" id="planamount">
                                                <td align="left">Plan Amount  <span class="warning">*</span></td>
                                                <td align="left"><input name="txtAmount" class="textbox2 numbersOnly" id="txtAmount" value="<?php echo $txtAmount;?>" size="35" maxlength="5"></td>
                                            </tr>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left"> Active </td>
                                                <td align="left"><input type="radio" name="radActive" value="1" <?php if($radActive=='1') {
                                                        echo 'checked';
                                                    }if
                                                    ($radActive=='') {
                                                         echo 'checked';
                                                    }?>>Yes  &nbsp;  <input type="radio" name="radActive" value="0" <?php if($radActive=='0') {
                                                        echo 'checked';
                                                    }?>>No
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td><input type="submit" name="btnSubmit" value="<?php echo $btnVal;?>" class="submit"/></td>
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

<script type="text/javascript">
    $(function() {
        $('.numbersOnly').keypress(function(e) { 
            var key_codes = [46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 0, 8];
                if (!($.inArray(e.which, key_codes) >= 0)) {
                    e.preventDefault();
                }
        });
    });

    $("input[name='chkPeriod']").change(function(){    

        if(this.value=='F')
        {
$("#planamount").hide();
        }
        else
        {
$("#planamount").show();
 
        }
    
});
</script>

<?php include_once('../includes/footer_admin.php');?>