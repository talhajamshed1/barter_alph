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


if(isset($_GET["refid"]) and $_GET["refid"]!="")
{
	$refid = $_GET["refid"];
}//end if
else if(isset($_POST["refid"]) and $_POST["refid"]!="")
{
	$refid = $_POST["refid"];
}//end else if
$surveydone = 0;

$sqlstat = "SELECT vSurveyStatus, vName , vEmail,vSurveyAnswer, vSurveyIP  FROM  ".TABLEPREFIX."referrals  WHERE ";
$sqlstat .=" nRefId = $refid ";
$result = mysqli_query($conn, $sqlstat);
//$result = mysqli_query($conn, $sqlstat) or die(mysqli_error($conn));

if(mysqli_num_rows($result)!=0)
{
	$row = mysqli_fetch_array($result);
	$status = $row["vSurveyStatus"];
	if($status == 0)
	{//not already participated in the survey
		$message = "This user has not yet answered the survey!";
	}//end if
	else
	{
		$surveydone = 1;
		$username = $row["vName"];
		$useremail = $row["vEmail"];
		$refip = $row["vSurveyIP"];
		$userans = $row["vSurveyAnswer"];
		$ch1 = chr(177);
		$ch2 = chr(176);
		//$arruserans = split($ch1,$userans);
                $arruserans = explode($ch1,$userans);
		//$arrtemp =  split($ch2,$arruserans[0]);
                $arrtemp =  explode($ch2,$arruserans[0]);
		if (is_array($arrtemp)) 
		{
			$selid = $arruserans[0];
			$arr1 = array("Daily","Weekly","Never","Other [ enter comment]");
			$out1 = "";
			for($i=1; $i<5 ;$i++)
			{
				$out1 .= "<input type='radio' name='rad1' value='".$i."' ";
				if($i == $selid)
				{
					$out1 .= " CHECKED " ;
				}//end if
				$out1 .= " >&nbsp; ".$arr1[$i-1]."<br>";
			}//end for loop
			//remove the last <br>
			$out1 = substr($out1,0,-4 );
			$out1 .= "<INPUT type='text' name='txtComments' size='30' maxlength='100' value='".$arrtemp[1] ."' >";
		}//end if
		//out 2
		$selid2 = $arruserans[1];
		$arr2 = array("0-1 Times","2-3 Times","3-4 Times","Never");
		$out2 = "";
		for($i=1; $i<5 ;$i++)
		{
			$out2 .= "<input type='radio' name='rad2' value='".$i."'";
			if($i == $selid2)
			{
			$out2 .= " CHECKED " ;
			}//end if
			$out2 .= " >&nbsp; ".$arr2[$i-1]."<br>";
		}//end for loop
		//out 3
		$selid3 = $arruserans[2];
		$arr3 = array("Yes","No");
		$out3 = "";
		for($i=1; $i<3 ;$i++)
		{
			$out3 .= "<input type='radio' name='rad3' value='".$i."'";
			if($i == $selid3)
			{
				$out3 .= " CHECKED " ;
			}//end if
			$out3 .= " >&nbsp; ".$arr3[$i-1]."<br>";
		}//end for loop
		//out 4
		$selid4 = $arruserans[3];
		$arr4 = array("$15-20","$25-30","$35-40","Other");
		$out4 = "";
		for($i=1; $i<5 ;$i++)
		{
			$out4 .= "<input type='radio' name='rad4' value='".$i."'";
			if($i == $selid4)
			{
				$out4 .= " CHECKED " ;
			}//end if
			$out4 .= " >&nbsp; ".$arr4[$i-1]."<br>";
		}//end for loop
		//out 5
		$selid5 = $arruserans[4];
		$arr5 = array("Yes","No");
		$out5 = "";
		for($i=1; $i<3 ;$i++)
		{
			$out5 .= "<input type='radio' name='rad5' value='".$i."'";
			if($i == $selid5)
			{
				$out5 .= " CHECKED " ;
			}//end if
			$out5 .= " >&nbsp; ".$arr5[$i-1]."<br>";
		}//end for loop
		//echo "Selid5: ". $selid5;
		//out 6
		$selid6 = $arruserans[5];
		$arr6 = array("Yes","No");
		$out6 = "";
		for($i=1; $i<3 ;$i++)
		{
			$out6 .= "<input type='radio' name='rad6' value='".$i."'";
			if($i == $selid6)
			{
				$out6 .= " CHECKED " ;
			}//end if
			$out6 .= " >&nbsp; ".$arr6[$i-1]."<br>";
		}//end for loop
		//out 7
		$selid7 = $arruserans[6];
		$arr7 = array("Throw it away","Give it away","Donate it to charity","Sell it");
		$out7 = "";
		//$arrtemp = split("_",$selid7);
                $arrtemp = explode("_",$selid7);
		if(is_array($arrtemp))
		{
			for($i=1; $i<5 ;$i++)
			{
				$out7 .= "<input type='checkbox' name='chk7' value='".$i."'";
				if(in_array($i,$arrtemp) )
				{
					$out7 .= " CHECKED " ;
				}//end in array if
				$out7 .= " >&nbsp; ".$arr7[$i-1]."<br>";
			}//end for loop
		}//end  is array if
		else
		{
			for($i=1; $i<5 ;$i++)
			{
				$out7 .= "<input type='checkbox' name='chk7' value='".$i."'";
				if($i == $selid7 )
				{
					$out7 .= " CHECKED " ;
				}//end if
				$out7 .= " >&nbsp; ".$arr7[$i-1]."<br>";
			}//end for loop
		}//end else
		//out 8
		$selid8 = $arruserans[7];
		$arr8 = array("Yes","No");
		$out8 = "";
		for($i=1; $i<3 ;$i++)
		{
			$out8 .= "<input type='radio' name='rad8' value='".$i."'";
			if($i == $selid8)
			{
				$out8 .= " CHECKED " ;
			}//end if
			$out8 .= " >&nbsp; ".$arr8[$i-1]."<br>";
		}//end for loop
		$out9 = $arruserans[8];
		//print_r($arruserans);
	}//end if
}//end if
else
{
	;
}//end else
?>
<style type="text/css">
<!--
.style1 {color: #517791}
.style10 {color: #AAC0CF}
.style2 {font-family: Verdana, Arial, Helvetica, sans-serif}
.style3 {font-family: Geneva, Arial, Helvetica, sans-serif}
a:hover {
        color: #000000;
}
.style17 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 9px; color: #33455B; }
.style18 {font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 9px; color: #33455B; }
-->
</style>

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
                        <td width="84%" class="heading_admn boldtextblack" align="left">Survey</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmSurvey" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>">
<input type="hidden" name="refid" value="<?php echo $refid;?>">
<input type="hidden" name="result" value="">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							
<?php
					if($surveydone){?>  
                              <tr align="center" bgcolor="#FFFFFF">
                                <td colspan="2">Survey Answers by '<?php echo $username; ?>' &nbsp;&nbsp;&nbsp;( RefId: <?php echo $refid; ?>&nbsp;)</td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left">Survey Completed from IP: <?php echo $refip;?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><ol>
						<li>
						How often do you or somebody in you household throw away items that you no longer have use for?  *
							<br>
							<?php echo $out1;?>
							<br>
							<br>
						</li>
						<li>
						How often in the past year have you attended swap meets or flea markets in your local community or surrounding area?  *
							<br>
							<?php echo $out2;?>
							<br>
						</li>
						<li>
						Have you ever sold or purchased items from a swap meet or flea market?  *
							<br>
							<?php echo $out3;?>
							<br>
						</li>
						<li>
						If you answered yes to the question above. How much did the venue charge you for space, if you were selling at the event that day?  *
							<br>
							<?php echo $out4;?>
							<br>
						</li>
						<li>
						If we gave you similar access to swap or sell unwanted items through the use of a web based swapping or selling community would you be interested?  *
							<br>
							<?php echo $out5;?>
							<br>
						</li>
						<li>
						Would you or somebody you know be interested in finding out more information about <?php echo SITE_NAME;?>?  *
							<br>
							<?php echo $out6;?>
							<br>
						</li>
						<li>
						If you had an item around the house that you no longer found use for what would you do with it? Check those that apply to you. *
							<br>
							<?php echo $out7;?>
							<br>
						</li>
						<li>
						Would you consider joining <?php echo SITE_NAME;?>?  *
							<br>
							<?php echo $out8;?>
							<br>
						</li>
						<li>
						Any questions or comments about <?php echo SITE_NAME;?> and services please enter here
							<br>
							<br>
							<textarea name="txtQuestions" rows="6" class="textbox2" style="height:100px;width:400;"><?php echo $out9?></textarea>
							<br>
						</li>
					</ol></td>
                              </tr>
							  <?php }else{//not completed yet?>
                              <tr align="center" bgcolor="#FFFFFF">
                                <td colspan="2"><?php echo $message;?></td>
                              </tr>
							  <?php }//end if?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center"><input type="button" name="btnBack" onClick="javascript:history.back();" value=" Back " class="submit"></td>
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