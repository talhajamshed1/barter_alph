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
$PGTITLE='settings';

//checking admin enabled listing fee
if(DisplayLookUp('Listing Type')!='1')
{
	header('location:setconf.php');
	exit();					
}//end if		
		
//old id
$oldId=($_GET['oldId']!='')?$_GET['oldId']:$_POST['oldId'];						
						
//checking db first entry or not
$firstEntry=fetchSingleValue(select_rows(TABLEPREFIX.'listingfee','count(nLId) as newnLId',""),'newnLId');	
			
if($_GET['mode']=='edit')
{
	$sql=mysqli_query($conn, "select * from ".TABLEPREFIX."listingfee where nLId='".$_GET['nLId']."'") or die(mysqli_error($conn));
	if(mysqli_num_rows($sql)>0)
	{
		
		$txtFrom=mysqli_result($sql,0,'nFrom');
		$txtTo=mysqli_result($sql,0,'nTo');
		$radActive=mysqli_result($sql,0,'vActive');
		$txtAmount=mysqli_result($sql,0,'nPrice');
                $above = mysqli_result($sql,0,'above');
	}//end if
	$mode='edit';
	$btnVal='Edit';
	
	$increaeVal=1;
	
	$defaultValue=fetchSingleValue(select_rows(TABLEPREFIX.'listingfee','nTo',"WHERE vActive='1' AND nLId='".$oldId."'"),'nTo');
	
	if(trim($defaultValue)!='')
	{
		$defaultValue=round($defaultValue,2);
	}//end if
	else
	{
		$defaultValue=0;
	}//end else
	
	//check decimal start with .1 or or .01
	//@list($val,$dec)=@split("[.]",$defaultValue);
        @list($val,$dec)=explode("[.]",$defaultValue);

	switch(strlen($dec))
	{
		case "1":
			$defaultValue=$defaultValue+0.1;
			$increaeVal=0.1;
		break;
			
		case "2":
			$defaultValue=$defaultValue+0.01;
			$increaeVal=0.01;
		break;
			
		case "0":
			if($defaultValue==0)
			{
				$defaultValue=$defaultValue+1;
			}//end if
			else
			{
				$defaultValue=$defaultValue+0.01;
			}//end else
			$increaeVal=0.01;
		break;
	}//end switch	
}//end if
else
{
	$btnVal='Add';
	$increaeVal=1;
	$mode='add';
	
	$defaultValue=fetchSingleValue(select_rows(TABLEPREFIX.'listingfee','nTo',"WHERE vActive='1' ORDER BY nTo DESC limit 0,1"),'nTo');
	
	if(trim($defaultValue)!='')
	{
		$defaultValue=round($defaultValue,2);
	}//end if
	else
	{
		$defaultValue=0;
	}//end else
	
	//check decimal start with .1 or or .01
	//@list($val,$dec)=@split("[.]",$defaultValue);
        @list($val,$dec)=explode("[.]",$defaultValue);
	
	switch(strlen($dec))
	{
		case "1":
			$defaultValue=$defaultValue+0.1;
			$increaeVal=0.1;
		break;
			
		case "2":
			$defaultValue=$defaultValue+0.01;
			$increaeVal=0.01;
		break;
			
		case "0":
			if($defaultValue==0)
			{
				switch($firstEntry)
				{
					case "0";
						$defaultValue=0;
					break;
					
					defualt;
						$defaultValue=$defaultValue+1;
					break;
				}//end switch
			}//end if
			else
			{
				$defaultValue=$defaultValue+0.01;
			}//end else
			$increaeVal=0.01;
		break;
	}//end switch	
	
	$txtFrom=$defaultValue;
}//end else


if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]!='')
{
	if (function_exists('get_magic_quotes_gpc') )
	{
   			 $nLId = stripslashes($_POST['nLId'] );
			 $txtAmount = stripslashes($_POST["txtAmount"]);
			 $txtFrom = stripslashes($_POST["txtFrom"]);
			 //$radActive =  stripslashes($_POST["radActive"]);
                         
			 $txtTo =  stripslashes($_POST["txtTo"]);
			 $defaultValue=stripslashes($_POST['defaultValue']);
			 //old values
			 $txtFromOld = stripslashes($_POST["txtFromOld"]);
			 $txtToOld =  stripslashes($_POST["txtToOld"]);
			 $oldId =  stripslashes($_POST["oldId"]);
	}//end if
	else
	{
                        $nLId = $_POST['nLId'] ;
			$txtAmount = $_POST["txtAmount"];
                        $txtFrom = $_POST["txtFrom"];
			$txtTo = $_POST["txtTo"];
			//$radActive = $_POST["radActive"];
			$defaultValue=$_POST['defaultValue'];
			//old values
			$txtFromOld = $_POST["txtFromOld"];
			$txtToOld =  $_POST["txtToOld"];
			$oldId =  $_POST["oldId"];
	}//end else
	$radActive =  1;
	//rounded value 2 decimals
	$txtFrom=round($txtFrom,2);
	$txtTo=round($txtTo,2);
	
	$rangeArray=array();
	
	$newValu=round($defaultValue,2)+1;
			
	//create range array		
	for($i=$defaultValue;$i<$newValu;$i+=$increaeVal)
	{
		if($i!=$newValu)
		{
			$rangeArray[]=$i;
		}//end if
	}//end for loop
}//end if


if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Add Range')
{
    $message= '';
        $sql = mysqli_query($conn, "SELECT MAX(nLPosition) as max from ".TABLEPREFIX."listingfee") or die(mysqli_error($conn));
        $rw = mysqli_fetch_array($sql);
        $maxorder = $rw['max']+1;

    if($_POST["rangeType"]=="rangeBetween") {

        //checking range
        if(!in_array($txtFrom,$rangeArray) && count($rangeArray)-1!=$newValu) {
            $message='From range should be '.$defaultValue.' - '.$newValu.'<br>';
        }//end if
        if($txtTo<$defaultValue) {
            $message.='To range should be greater than '.$defaultValue.'<br>';
        }//end if
        if($txtTo==$txtFrom) {
            $message.='To range not equal to From range';
        }//end if

        if($message==''){
        mysqli_query($conn, "insert into ".TABLEPREFIX."listingfee (nPrice,nFrom,nTo,nLPosition,vActive) values
						('".addslashes($txtAmount)."','".addslashes($txtFrom)."','".addslashes($txtTo)."',
						'".$maxorder."','".addslashes($radActive)."')") or die(mysqli_error($conn));

        header('location:listing_combinations.php?msg=a');
        exit();
        }
    }else if($_POST["rangeType"]=="above"){
        $maxvalue = fetchSingleValue(select_rows(TABLEPREFIX.'listingfee','max(nTo) as max_value',"WHERE vActive='1'"),'max_value');
        $message = '';
        $above = $_POST["aboveRange"];
        if($above=="") {
            $message.='Please enter a value to above range';
        }//end if
        if($above != $maxvalue) {
            $message.='Above range value should be equal to '.$maxvalue;
        }
        if($message==''){
         mysqli_query($conn, "insert into ".TABLEPREFIX."listingfee (nPrice,above,nLPosition,vActive) values
						('".addslashes($txtAmount)."','".addslashes($above)."',
						'".$maxorder."','".addslashes($radActive)."')") or die(mysqli_error($conn));

        header('location:listing_combinations.php?msg=a');
        exit();
        }
    }

	
	
	/*$message='';
	
	//checking range
	if(!in_array($txtFrom,$rangeArray) && count($rangeArray)-1!=$newValu)
	{
		$message='From range should be '.$defaultValue.' - '.$newValu.'<br>';
	}//end if
	if($txtTo<$defaultValue)
	{
		$message.='To range should be greater than '.$defaultValue.'<br>';
	}//end if
	if($txtTo==$txtFrom)
	{
		$message.='To range not equal to From range';
	}//end if
			
	if($message=='')
	{
		mysqli_query($conn, "insert into ".TABLEPREFIX."listingfee (nPrice,nFrom,nTo,nLPosition,vActive) values 
						('".addslashes($txtAmount)."','".addslashes($txtFrom)."','".addslashes($txtTo)."',
						'".$maxorder."','".addslashes($radActive)."')") or die(mysqli_error($conn));
		
		header('location:listing_combinations.php?msg=a');
		exit();
	}//end if*/
}//end if

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Edit Range')
{


     $message='';

    if($_POST["rangeType"]=="rangeBetween") {
        if($txtToOld!=$txtTo) {
            if($txtTo<$defaultValue) {
                $message.='To range should be greater than '.$defaultValue.'<br>';
            }//end if
        }//end if
        if($txtTo==$txtFrom) {
            $message.='To range not equal to From range<br>';
        }//end if

         if($txtTo<$txtFrom) {
            $message.='To range should be greater than From range<br>';
        }//end if

        //checking range already exists
        if(checkRange($txtFrom,$nLId)==1) {
            $message.='From range already exists<br>';
        }//end if

        //checking range already exists
        if(checkRange($txtTo,$nLId)==1) {
            $message.='To range already exists<br>';
        }//end if
        else {//checking new range
            if(checkNewRange($txtFrom,$txtTo,$nLId)==1) {
//                $message.='Given range already exists<br>';
                $message.='Certain values in the given range already exists<br>. Please check the existing listing fee range and add a valid range.<br>';
            }//end if
        }//end else

        if($message=='') {
            mysqli_query($conn, "update ".TABLEPREFIX."listingfee set nFrom='".addslashes($txtFrom)."',
                                                             nPrice='".addslashes($txtAmount)."',nTo='".addslashes($txtTo)."',above='0' WHERE nLId='".$nLId."'") or die(mysqli_error($conn));
            header('location:listing_combinations.php?msg=e');
            exit();
        }
    }else if($_POST["rangeType"]=="above"){
        $maxvalue = fetchSingleValue(select_rows(TABLEPREFIX.'listingfee','max(nTo) as max_value',"WHERE vActive='1'"),'max_value');
        $checkAbove = fetchSingleValue(select_rows(TABLEPREFIX.'listingfee','above',"WHERE vActive='1' AND nLId='".$nLId."'"),'above');
        $above = $_POST["aboveRange"];
        if($above=="") {
            $message.='Please enter a value to above range';
        }//end if
        if($maxvalue != $above && $checkAbove==0)
             $message.='Above range value should be equal to '.$maxvalue;
        if($message==''){

            mysqli_query($conn, "update ".TABLEPREFIX."listingfee set nFrom='0',nTo='0',
							 nPrice='".addslashes($txtAmount)."',above='".addslashes($above)."' WHERE nLId='".$nLId."'") or die(mysqli_error($conn));
            header('location:listing_combinations.php?msg=e');
            exit();
        }
    }//end if


   /* $message='';
	
	//checking range
//	if($txtFromOld!=$txtFrom)
//	{
//		if(!in_array($txtFrom,$rangeArray) && count($rangeArray)-1!=$newValu)
//		{
//			$message='From range should be '.$defaultValue.' - '.$newValu.'<br>';
//		}//end if
//	}//end if
	if($txtToOld!=$txtTo)
	{
		if($txtTo<$defaultValue)
		{
			//$message.='To range should be greater than '.$defaultValue.'<br>';
		}//end if
	}//end if
	if($txtTo==$txtFrom)
	{
		$message.='To range not equal to From range<br>';
	}//end if
	
	//checking range already exists
	if(checkRange($txtFrom,$nLId)==1)
	{
		$message.='From range already exists';
	}//end if
	
	//checking range already exists
	if(checkRange($txtTo,$nLId)==1)
	{
		$message.='To range already exists';
	}//end if
	else
	{//checking new range
		if(checkNewRange($txtFrom,$txtTo,$nLId)==1)
		{
			$message.='<br>Given range already exists';
		}//end if
	}//end else
	
	if($message=='')
	{
		mysqli_query($conn, "update ".TABLEPREFIX."listingfee set nFrom='".addslashes($txtFrom)."',vActive='".addslashes($radActive)."',
							 nPrice='".addslashes($txtAmount)."',nTo='".addslashes($txtTo)."' WHERE nLId='".$nLId."'") or die(mysqli_error($conn));
		header('location:listing_combinations.php?msg=e');
		exit();
	}//end if */
}//end if

$maxvalue = fetchSingleValue(select_rows(TABLEPREFIX.'listingfee','max(nTo) as max_value',"WHERE vActive='1'"),'max_value');
$abrs = select_rows(TABLEPREFIX.'listingfee','nLId',"WHERE vActive='1' and above <> ''");



$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<link href="../styles/tabcontent.css" rel="stylesheet" type="text/css">
<script language="javascript1.1" type="text/javascript" src="../js/range.js"></script>
<script language="javascript" type="text/javascript">
function category_chk()
{
    var radioElm = document.getElementsByName('rangeType');
	 var frm=document.frmListing;
	 if(isNaN(frm.txtFrom.value) || frm.txtFrom.value.length == 0 || frm.txtFrom.value.substring(0,1) == " ")
	 {
          frm.txtFrom.value=0;
          frm.txtFrom.focus();
          alert('Please enter a positive number in Range From');
          return false;
     }//end if
     if(getCheckedValue(radioElm)=="rangeBetween"){
	if(isNaN(frm.txtTo.value) || frm.txtTo.value.length == 0 || frm.txtTo.value.substring(0,1) == " ")
	 {
          frm.txtTo.value=0;
          frm.txtTo.focus();
          alert('Please enter a positive number in Range To');
          return false;
     }//end if
     }
	if(isNaN(frm.txtAmount.value) || frm.txtAmount.value.substring(0,1) == " " || frm.txtAmount.value.substring(0,1) == "")
	 {
          frm.txtAmount.value=0;
          frm.txtAmount.focus();
          alert('Please enter a positive number in Listing Price(%)');
          return false;
     }//end if	 
	return true;
}

// return the value of the radio button that is checked
// return an empty string if none are checked, or
// there are no radio buttons
function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function setDisplay(id)
    {
        if(id=="range"){
            document.getElementById('range').style.display = 'block';
            document.getElementById('above').style.display = 'none';
        }else if(id=="above"){
            document.getElementById('range').style.display = 'none';
            document.getElementById('above').style.display = 'block';
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
                        <td width="100%" class="heading_admn boldtextblack" align="left"><?php echo $btnVal;?> Range</td>
                      </tr>
                    </table>
					<?php include_once('../includes/settings_menu.php');?>
					<div class="tabcontentstyle">
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                      <tr>
                        <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
							
                                    <form name="frmListing" method ="POST" action = "" onSubmit="return category_chk()" enctype="multipart/form-data">
                                                <input type="hidden" value="<?php echo $defaultValue;?>" name="defaultValue">
                                                <input type="hidden" value="<?php echo $txtFrom;?>" name="txtFromOld">
                                                <input type="hidden" value="<?php echo $txtTo;?>" name="txtToOld">
                                                <input type="hidden" value="<?php echo $oldId;?>" name="oldId">
                                                    <?php if(isset($message) && $message!='') {
                                                        ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td colspan="3" align="center" class="warning"><?php echo $message;?></td>
                                                </tr>
                                                    <?php  }//end if?>
                                                <tr align="right" bgcolor="#FFFFFF">
                                                    <td colspan="3" style="border:0;"><a href="listing_combinations.php"><b>Back</b></a></td>
                                                </tr>
                                                <tr bgcolor="#FFFFFF">
                                                <input type="hidden" name="nLId" value="<?php echo $_GET['nLId'];?>">
                                                <td colspan="2" align="left" valign="top" class="warning"> * indicates mandatory fields</td>
                                                <td width="45%" align="left" valign="top" class="warning"><span id="txtRange"></span>
                                                </tr>

                                                 <tr bgcolor="#FFFFFF">
                                                    <td width="28%" valign="top">Select Range<span class="warning">*</span></td>
                                                    <td colspan="2">
                                                        <input type="radio" name="rangeType" value="rangeBetween" onchange="setDisplay('range')" <?php if($above=="" || $above==0) echo "checked";?>>Between To Values &nbsp; 
                                                        <?php if($mode=="add" || ($mode=="edit" && !(mysqli_num_rows($abrs))) || ($above || $above!=0)){?>
                                                        <input type="radio" name="rangeType" value="above" onchange="setDisplay('above')" <?php if($above || $above!=0) echo "checked";?>>Above A Range
                                                        <?php }?>
                                                        <br><br>
                                                        <div id="range" <?php if($above || $above!=0){?>style="display: none;"<?php }?>>
                                                            <table>
                                                                <tr bgcolor="#FFFFFF">
                                                                        <td width="18%" align="left" style="border:0; ">Range </td>
                                                                        <td colspan="2" style="border:0; ">From <?php echo CURRENCY_CODE;?>
                                                                            <input type="text" class="textbox2" name="txtFrom" size="5" <?php if($mode=="add"){?> readonly <?php }?> maxlength="8" value="<?php echo round($txtFrom,2);?>" onKeyUp="showRange('f',this.value,'<?php echo $mode;?>','<?php echo $_GET['nLId'];?>');" onMouseDown="showRange('f',this.value,'<?php echo $mode;?>','<?php echo $_GET['nLId'];?>');"/>								To <?php echo CURRENCY_CODE;?>
                                                                            <input type="text" class="textbox2" name="txtTo" size="5" maxlength="8" value="<?php if($mode=="edit"){echo round($txtTo,2);}?>" onKeyUp="showRange('t',this.value,'<?php echo $mode;?>','<?php echo $_GET['nLId'];?>');" onMouseDown="showRange('t',this.value,'<?php echo $mode;?>','<?php echo $_GET['nLId'];?>');"/>
                                                                        </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div id="above" <?php if($above=="" || $above==0 ){?>style="display: none;"<?php }?>>
                                                            <table>
                                                                <tr bgcolor="#FFFFFF">
                                                                    <td align="left" valign="top" style="border:0;">Above</td>
                                                                    <td colspan="2" align="left" valign="top" style="border:0;">
                                                                        <input type="text" class="textbox2" name="aboveRange" size="5" value="<?php if($above!=0)echo $above;else echo $maxvalue;?>"/>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>

                                                    </td>
                                                </tr>



                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left" valign="top">Listing Price (%) <span class="warning">*</span></td>
                                                    <td colspan="2" align="left" valign="top"><input type="text" class="textbox2" name="txtAmount" size="5" value="<?php echo $txtAmount;?>"/></td>
                                                </tr>
                                                <?php if($mode!="edit"){?>
                                                <!--<tr valign="top" bgcolor="#FFFFFF">
                                                    <td align="left"> Active </td>
                                                    <td colspan="2" align="left">
                                                        <input type="radio" name="radActive" value="1" <?php if($radActive=='1') {
                                                            //echo 'checked';
                                                        }if ($radActive=='') {
                                                            //echo 'checked';
                                                        }?>>Yes <input type="radio" name="radActive" value="0" <?php if($radActive=='0') {
                                                            //echo 'checked';
                                                        }?>>No
                                                    </td>
                                                </tr>--><?php }?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td align="left" style="border:0; ">&nbsp;</td>
                                                    <td colspan="2" style="border:0; ">
                                                        <?php 
                                                        if(!(mysqli_num_rows($abrs))){
                                                        ?>
                                                        <input type="submit" name="btnSubmit" value="<?php echo $btnVal;?> Range" class="submit"/>
                                                        <?php 
                                                        }else{
                                                            echo "<span class='warning'> Editing will not be allowed if 'Above' range is mentioned";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </form>
                            </table>
</td>
                          </tr>
                        </table></td>
                      </tr>
                    </table>
					</div>
                  </td>
                </tr>
              </table>
              </div>
              </div>
<?php include_once('../includes/footer_admin.php');?>