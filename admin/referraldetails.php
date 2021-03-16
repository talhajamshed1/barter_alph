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
$PGTITLE='survey';

if($_POST["txtPostback"]=="true") {

    $refresh="false";
    if(count( $_POST['sur'])>0) {
        $tempref=$_POST['sur'][0];
        $sql="select nRefId from ".TABLEPREFIX."referrals where nRefId='$tempref' and nSurveyCashTxn!='0'";
        $result=mysqli_query($conn, $sql);
        if(mysqli_num_rows($result)> 0) {
            $refresh="true";
        }//end if
    }//end if
    else if(count( $_POST['reg'])>0) {
        $tempref=$_POST['reg'][0];
        $sql="select nRefId from ".TABLEPREFIX."referrals where nRefId='$tempref' and nRegCashTxn!='0'";
        $result=mysqli_query($conn, $sql);
        if($row = mysqli_num_rows($result)> 0 ) {
            $refresh="true";
        }//end if
    }//end else if

    if($refresh=="false") {
        $pbamount=$_POST["txtAmount"];
        $pbreference=$_POST["txtReference"];
        $pbuserid=$_POST["txtUserId"];
        $pbmode=$_POST["cmbMode"];

        /*
     	$sql="select nSurveyAmount,nRegAmount from ".TABLEPREFIX."referrals where nUserId='$pbuserid'";

     	 $result=mysqli_query($conn, $sql);
      	if($row = mysqli_fetch_array($result)) {
			  $samount=$row["nSurveyAmount"];
          	$ramount=$row["nRegAmount"];
		  }

        */

        $sur=0;
        for( $i = 0; $i < count( $_POST['sur'] ); $i++ ) {
            $sql="select nSurveyAmount,nRegAmount from ".TABLEPREFIX."referrals
       					where nRefId='".$_POST['sur'][$i]."'";
            $result=mysqli_query($conn, $sql);
            if($row = mysqli_fetch_array($result)) {
                $samount=$row["nSurveyAmount"];
            }//end if



            $sql = "insert into ".TABLEPREFIX."cashtxn(nUserId,nAmount,nCommission,dDate,vMode,vModeNo,vReason,vKey) values('". addslashes($pbuserid) . "','". $samount . "','0',now(),'". addslashes($pbmode) . "','". addslashes($pbreference).  "','survey','" . addslashes($_POST['sur'][$i]) . "')";

            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $cashtxnid = mysqli_insert_id($conn);

            $sql="UPDATE ".TABLEPREFIX."referrals SET nSurveyCashTxn = '$cashtxnid' WHERE nRefId = '".$_POST['sur'][$i]."'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));

            $sur=$sur + $samount;
        }//end for loop

        $sql="UPDATE ".TABLEPREFIX."user_referral SET nSurveyAmount = nSurveyAmount - $sur  ,nSurveyPaid =nSurveyPaid  + $sur WHERE nUserId = '$pbuserid'";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        $reg=0;
        for( $i = 0; $i < count( $_POST['reg'] ); $i++ ) {
            $sql="select nSurveyAmount,nRegAmount from ".TABLEPREFIX."referrals where nRefId='".$_POST['reg'][$i]."'";
            $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));

            if($row = mysqli_fetch_array($result)) {
                $ramount=$row["nRegAmount"];
            }//end if

            $sql = "insert into ".TABLEPREFIX."cashtxn(nUserId,nAmount,nCommission,dDate,vMode,vModeNo,vReason,vKey) values('". addslashes($pbuserid) . "','". $ramount . "','0',now(),'". addslashes($pbmode) . "','". addslashes($pbreference).  "','registration','" . addslashes($_POST['reg'][$i]) . "')";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $cashtxnid = mysqli_insert_id($conn);
            $sql="UPDATE ".TABLEPREFIX."referrals SET nRegCashTxn = '$cashtxnid' WHERE nRefId = '".$_POST['reg'][$i]."'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $reg=$reg+$ramount;
        }//end for loop


        $sql="UPDATE ".TABLEPREFIX."user_referral SET nRegAmount = nRegAmount - $reg  ,nRegPaid =nRegPaid  + $reg WHERE nUserId = '$pbuserid'";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
    }//end if
}//end if
?>
<script language="javascript" type="text/javascript">
    function calc(id1){
        var xx="t" + id1.id
        if(id1.checked==true){


            document.frmRefferal.txtAmount.value=parseFloat(document.frmRefferal.txtAmount.value) + parseFloat(document.getElementById(xx).value);

        }else{
            document.frmRefferal.txtAmount.value=parseFloat(document.frmRefferal.txtAmount.value) - parseFloat(document.getElementById(xx).value);


        }


    }

    function settle(){
        if((document.frmRefferal.txtReference.value=="")){
            alert("Please enter the reference number");
        }else{
            document.frmRefferal.txtPostback.value="true";
            document.frmRefferal.submit();
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
                    <td width="100%" class="heading_admn boldtextblack" align="left">Referral Settlements</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                <tr>
                    <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="4" class="maintext2">
                                        <form name="frmRefferal" method="POST" action ="referraldetails.php?user=<?php echo $_GET["user"]?>&name=<?php echo $_GET["name"]?>">

<?php if(isset($message) && $message!='') {
    ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                                <?php  }//end if?>
                                            <?php
                                            $var_userid=$_GET["user"];
                                            $referer=$_GET["name"];
                                            $sql = "SELECT R.nRefId,R.nUserId,R.vName,R.vAddress,R.vPhone,R.vFax,R.vEmail,R.vSurveyStatus,R.dSurveyDate,R.vSurveyAnswer,R.nSurveyAmount";
                                            $sql .= ",R.vRegStatus,R.dRegDate,R.nRegAmount,R.vPayStatus,R.nSurveyCashTxn,R.nRegCashTxn,R.nUserRegId";
                                            $sql .= " FROM ".TABLEPREFIX."referrals R WHERE  nUserId ='$var_userid'";
                                            $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                            ?>

                                            <tr bgcolor="#FFFFFF"> <input type="hidden" name="txtPostback">
                                            <input type="hidden" name="txtUserId" value="<?php echo $var_userid?>">
                                            <td align="left" class="gray"><b>Referral Details of <?php echo $referer;?></b></td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                            <td align="left">
                                            <?php

                                            $Result="<table width='100%'  border='0' cellspacing='0' cellpadding='0'>
                                                                      <tr>
                                                                        <td bgcolor='#ffffff' class='noborderbottm'><table width='100%'  border='0' cellspacing='1' cellpadding='4' class='maintext2'>";


                                            $Settle="<table width='100%'  border='0' cellspacing='0' cellpadding='0'>
                                                                      <tr>
                                                                        <td bgcolor='#ffffff'><table width='100%'  border='0' cellspacing='1' cellpadding='4' class='maintext2'>";
                                            $Settle.="<tr bgcolor='#ffffff' class='gray'><td colspan=3 width=100% align=center><b>Settlement Details of $referer</b></td></tr>";
                                            $Settle.="<tr bgcolor='#FFFFFF'><td  align=center class='gray'>Referral</td><td  align=center class='gray'>Registration</td><td  align=center class='gray'>Survey</td></tr>";


                                                    if(mysqli_num_rows($result) > 0) {

                                                        $settleavailable=false;
                                                        $counter = 1;
                                                        while($row = mysqli_fetch_array($result)) {




//==============================Disply Details==================================

                                                            $Result.="<tr bgcolor='#FFFFFF'><td colspan=2 width=100% align=left><b>Referral $counter</b></td></tr>";
                                                            $Result.="<tr bgcolor='#FFFFFF'><td align =left width=50%>";
                                                            $Result.="<font class=textblack>Name</font></td>";
                                                            if($row["vRegStatus"]=="1") {
                                                                $link="<a href=regdetails.php?userid=".$row["nUserRegId"]."><font class=textblack>".$row["vName"]."</font></a>";
                                                            }else {
                                                                $link=$row["vName"];
                                                            }
                                                            $Result.="<td align =left width=50%   class=textblack>&nbsp;".$link."</td></tr>";

                                                            $Result.="<tr bgcolor='#FFFFFF'><td align =left width=50%>";
                                                            $Result.="<font class=textblack>Address</font></td>";
                                                            $Result.="<td align =left width=50%   class=textblack>&nbsp;".$row["vAddress"]."</td></tr>";

                                                            $Result.="<tr bgcolor='#FFFFFF'><td align =left width=50%>";
                                                            $Result.="<font class=textblack>Phone</font></td>";
                                                            $Result.="<td align =left width=50%   class=textblack>&nbsp;".$row["vPhone"]."</td></tr>";

                                                            $Result.="<tr bgcolor='#FFFFFF'><td align =left width=50%>";
                                                            $Result.="<font class=textblack>Fax</font></td>";
                                                            $Result.="<td align =left width=50%   class=textblack>&nbsp;".$row["vFax"]."</td></tr>";

                                                            $Result.="<tr bgcolor='#FFFFFF'><td align =left width=50%>";
                                                            $Result.="<font class=textblack>E-Mail</font></td>";
                                                            $Result.="<td align =left width=50%   class=textblack>&nbsp;".$row["vEmail"]."</td></tr>";

                                                            $Result.="<tr bgcolor='#FFFFFF'><td align =left width=50%>";
                                                            $Result.="<font class=textblack>Survey Status</font></td>";
                                                            if($row["vSurveyStatus"]=="1") {

                                                                $status="<a href=viewsurvey.php?refid=".$row["nRefId"].">Completed</a> on ".$row["dSurveyDate"];

                                                            }else {

                                                                $status="InComplete";

                                                            }
                                                            $Result.="<td align =left width=50%  class=textblack>&nbsp;".$status."</td></tr>";




                                                            $Result.="<tr bgcolor='#FFFFFF'><td align =left width=50%>";
                                                            $Result.="<font class=textblack>Registration Status</font></td>";
                                                            if($row["vRegStatus"]=="1") {

                                                                $status="Completed on ".$row["dRegDate"];

                                                            }else {

                                                                $status="InComplete";

                                                            }
                                                            $Result.="<td align =left width=50%   class=textblack>&nbsp;".$status."</td></tr>";

                                                            $Result.="<tr bgcolor='#FFFFFF'><td align='left' colspan='2'>&nbsp;</td></tr>";


//==============================/Disply Details==================================


//==============================Settlement Details==================================
                                                            $Settle.="<tr bgcolor='#FFFFFF'><td align =left width=30% class=textblack>";
                                                            $Settle.="Referral $counter<br>&nbsp;</td>";
                                                            $Settle.="<td align =center width=35%   class=textblack>&nbsp;";


                                                            if(($row["nRegCashTxn"]=="0") && ($row["vRegStatus"]=="1")) {

                                                                $Settle.="<input type=checkbox name=reg[] onClick=calc(this) value=".$row["nRefId"]." id=r".$row["nRefId"]."> &nbsp; $ &nbsp; <input type=text class=textbox style='width:30px' id=tr".$row["nRefId"]." value=".$row["nRegAmount"]." size=1 readonly > &nbsp;";
                                                                $settleavailable=true;
                                                            }elseif(($row["nRegCashTxn"]!="0") && ($row["vRegStatus"]=="1")) {
                                                                $Settle.="<a href=settlement.php?cashid=".$row["nRegCashTxn"]."><font class=textblack>Settled</font></a>";

                                                            }else {

                                                                $Settle.="Incomplete";
                                                            }


                                                            $Settle.="</td>";

                                                            $Settle.="<td align =center width=35%   class=textblack>";

                                                            if(($row["nSurveyCashTxn"]=="0")&&($row["vSurveyStatus"]=="1")) {
                                                                $Settle.="<input  onClick=calc(this)  class=checkbox type=checkbox name=sur[] id=s".$row["nRefId"]."  value=".$row["nRefId"]."> &nbsp; $ &nbsp; <input class=textbox style='width:30px' type=text id=ts".$row["nRefId"]."  value=".$row["nSurveyAmount"]." size=1 readonly > &nbsp;";
                                                                $settleavailable=true;
                                                            }elseif(($row["nSurveyCashTxn"]!="0")&&($row["vSurveyStatus"]=="1")) {
                                                                $Settle.="<a href=settlement.php?cashid=".$row["nSurveyCashTxn"]."><font class=textblack>Settled</font></a>";
                                                            }else {
                                                                $Settle.="Incomplete";
                                                            }

                                                            $Settle.="</td></tr>";
//==============================/Settlement Details==================================



                                                            $counter++;






                                                        }

                                                    }else {

                                                        $Result.="<tr bgcolor='#FFFFFF'><td align =center colspan=3 width=100%><font>No Referral Details Available ! </font></td></tr>";
                                                        $Settle.="<tr bgcolor='#FFFFFF'><td align =center colspan=3 width=100%><font>No Settlement Details Available ! </font></td></tr>";

                                                    }
                                                    $Result.="</table></td>
                          </tr>
                        </table>";


                                                    if($settleavailable==true) {
                                                        $Settle.="<tr bgcolor='#FFFFFF'><td colspan='3'>&nbsp;</td></tr>
											<tr bgcolor='#FFFFFF' align='left'><td>Amount</td>
											<td width='100%' colspan='2' align='left'><input type='text' name='txtAmount' id='txtAmount' class='textbox2' value='0' readonly></td></tr>
											<tr bgcolor='#FFFFFF' align='left'><td>Mode</td>
											<td width='100%' colspan='2' align='left'> <select name='cmbMode' class='textbox2' id='cmbMode'>
                  <option value='Cheque'>Cheque</option>
                  <option value='Demand Draft'>Demand Draft</option>
                  <option value='Cash'>Cash</option>
              </select></td></tr>
											<tr bgcolor='#FFFFFF' align='left'><td>Reference</td>
											<td width='100%' colspan='2' align='left'><input type='text' name='txtReference' id='txtReference' class='textbox2'></td></tr>
											<tr bgcolor='#FFFFFF'><td align='left'>&nbsp;</td>
											<td width='100%' colspan='2' align='left'><input class='submit' type='button' name='btnSettle' value='Settle' onClick='settle();'></td></tr>";

                                                    }
                                                    $Settle.="</table></td>
                          </tr>
                        </table>";
//======================================================================================

                                                    echo "$Result<br><br><br><br>$Settle<br>";

                                                    ?>
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