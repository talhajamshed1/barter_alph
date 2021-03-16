<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: 			*/
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004-2008 Armia Systems, Inc                                    |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts SocialWare                    |
// +----------------------------------------------------------------------+
// | Authors: simi<simi@armia.com>             		                      |
// |          										                      |
// +----------------------------------------------------------------------+
include_once('../includes/config.php');
include_once('../includes/adminsession.php');
include_once('../includes/functions.php');

$newValue=round($_GET['q'],2);
$type=$_GET['rtype'];
$vmode=$_GET['vmode'];
$nrId=$_GET['nrId'];

  $firstEntry=fetchSingleValue(select_rows(TABLEPREFIX.'escrowrangefee','count(nLId) as newnLId',""),'newnLId');

  //check mode add or edit
switch($vmode)
{
	case "add":
		$appendCndt='';
                $numRows = select_rows(TABLEPREFIX.'escrowrangefee',"nTo","WHERE vActive='1'");
                if($numRows){
                    $defaultValue=fetchSingleValue(select_rows(TABLEPREFIX.'escrowrangefee','nTo',"WHERE vActive='1' ORDER BY nTo DESC limit 0,1"),'nTo');
                }
                if($defaultValue==0){
                    $defaultValue=fetchSingleValue(select_rows(TABLEPREFIX.'escrowrangefee','max(nTo) as maxTo',"WHERE vActive='1'"),'maxTo');
                }

	break;

	case "edit":
		$appendCndt=" AND nLId='".$nrId."' and above = ''";
		$fetchField='nTo';
                /*$numRows = select_rows(TABLEPREFIX.'escrowrangefee',$fetchField,"WHERE vActive='1' ".$appendCndt."");
                if($numRows){
                    $defaultValue=fetchSingleValue($numRows,'nTo');
                }*/
                if($defaultValue==0){
                    $defaultValue=fetchSingleValue(select_rows(TABLEPREFIX.'escrowrangefee','max(nTo) as maxTo',"WHERE vActive='1'"),'maxTo');
                }
		//fetch old values
		$txtFromOld=fetchSingleValue(select_rows(TABLEPREFIX.'escrowrangefee','nFrom',"WHERE nLId='".$nrId."'"),'nFrom');
		$txtToOld=fetchSingleValue(select_rows(TABLEPREFIX.'escrowrangefee','nTo',"WHERE nLId='".$nrId."'"),'nTo');
	break;
}//end switch




$increaeVal=1;


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
@list($val,$dec)=@explode("[.]",$defaultValue);

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

$rangeArray=array();

$newValu=$defaultValue+1;

for($i=$defaultValue;$i<$newValu;$i+=$increaeVal)
{
	if($i!=$newValu)
	{
		$rangeArray[]=$i;
	}//end if
}//end for loop

switch($type)
{
	case "f":
		if($vmode=='add')
		{
			if(!in_array($newValue,$rangeArray) && count($rangeArray)-1!=$newValu)
			{
				$message='From range should be '.$defaultValue.' - '.$newValu;
			}//end if
		}//end if
		else
		{
			if($txtFromOld!=$newValue)
			{
				if(!in_array($newValue,$rangeArray) && count($rangeArray)-1!=$newValu)
				{
					$message.='<br>From range should be '.$defaultValue.' - '.$newValu;
				}//end if
			}//end if
		}//end else
	break;

	case "t":
		if($vmode=='add')
		{
			if($newValue<$defaultValue)
			{
				$message.='<br>To range should be greater than '.$defaultValue;
			}//end if
		}//end if
		else
		{
			if($txtToOld!=$newValue)
			{
				if($newValue<$defaultValue)
				{
					//$message.='<br>To range should be greater than '.$defaultValue;
				}//end if
			}//end if
			//checking range already exists
                     
                        if(checkEscrowRange($newValue,$nrId)==1)
			{
				$message.='<br>To range already exists';
			}//end if
		}//end else
	break;
}//end switch

echo $message;
?>

