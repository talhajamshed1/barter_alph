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
ob_start();  
include_once('../includes/config.php');  
include_once('../includes/adminsession.php');  
include_once('../includes/functions.php');

switch($_GET['sType'])
{
	case "sale":
		$saleArray=array();
		$saleValueArray=array();
			
		//checking point enable in website
		if($EnablePoint!='0')
		{
			$saleArray=array('Sl No.','Seller','Transaction Date','Transaction Number','Mode','Amount',POINT_NAME,'Buyer');
			$sqlArray=mysqli_query($conn, $_SESSION['sess_query']) or die(mysqli_error($conn));
			if(mysqli_num_rows($sqlArray)>0)
			{
				$cnt=1;
				while($arr=mysqli_fetch_array($sqlArray))
				{
					 $trnansmode ="";
								 
					 switch($arr["vMethod"]) 
					 {
						case "pp" : $trnansmode  = "PayPal";
						break;
						
						case "wp" : $trnansmode  = "WorldPay";
						break;
										
						case "bp" : $trnansmode  = "BluePay";
						break;
										
						case "cc" :	$trnansmode ="Credit Card";
						break;
										
						case "bu" : $trnansmode ="Business Check";
						break;
										
						case "ca" : $trnansmode ="Cashiers Check";
						break;

						case "mo" : $trnansmode ="Money Order";
						break;

						case "wt" : $trnansmode ="Wire Transfer";
						break;
																	
						case "pc" : $trnansmode ="Personal Check";
						break;

						case "yp":
							$trnansmode = "Yourpay";
						break;

						case "gc":
							$trnansmode = "Google Checkout";
						break;
										
						case "rp":
							$trnansmode = POINT_NAME;
						break;
					}//end switch			
								
					//fetch seller name
					$SellerName=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',"WHERE nUserId='".$arr['sellerId']."'"),'vLoginName');
								
					//fetch buyer name
					$userName=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',"WHERE nUserId='".$arr['nUserId']."'"),'vLoginName');
					
					//$pointValue=round(($arr["nAmount"]/DisplayLookUp('PointValue'))*DisplayLookUp('PointValue2'),2);
					
					//create list array
					$saleValueArray[$cnt]=array($cnt,htmlentities($SellerName),date('F d, Y',strtotime($arr["dTxnDate"])),htmlentities($arr["vTxnId"]),htmlentities($trnansmode),CURRENCY_CODE.htmlentities($arr["nAmount"]),htmlentities($arr["nPoint"]),htmlentities($userName));
					$cnt++;
				}//end while loop
			}//end if
		}//end if
		else
		{
			$saleArray=array('Sl No.','Seller','Transaction Date','Transaction Number','Mode','Amount','Buyer');
			$sqlArray=mysqli_query($conn, $_SESSION['sess_query']) or die(mysqli_error($conn));
			if(mysqli_num_rows($sqlArray)>0)
			{
				$cnt=1;
				while($arr=mysqli_fetch_array($sqlArray))
				{
					 $trnansmode ="";
								 
					 switch($arr["vMethod"]) 
					 {
						case "pp" : $trnansmode  = "PayPal";
						break;
						
						case "wp" : $trnansmode  = "WorldPay";
						break;
						
						case "bp" : $trnansmode  = "BluePay";
						break;
										
						case "cc" :	$trnansmode ="Credit Card";
						break;
										
						case "bu" : $trnansmode ="Business Check";
						break;
										
						case "ca" : $trnansmode ="Cashiers Check";
						break;

						case "mo" : $trnansmode ="Money Order";
						break;

						case "wt" : $trnansmode ="Wire Transfer";
						break;
																	
						case "pc" : $trnansmode ="Personal Check";
						break;

						case "yp":
							$trnansmode = "Yourpay";
						break;

						case "gc":
							$trnansmode = "Google Checkout";
						break;
										
						case "rp":
							$trnansmode = POINT_NAME;
						break;
					}//end switch			
								
					//fetch seller name
					$SellerName=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',"WHERE nUserId='".$arr['sellerId']."'"),'vLoginName');
								
					//fetch buyer name
					$userName=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',"WHERE nUserId='".$arr['nUserId']."'"),'vLoginName');
					
					//create list array
					$saleValueArray[$cnt]=array($cnt,htmlentities($SellerName),date('F d, Y',strtotime($arr["dTxnDate"])),htmlentities($arr["vTxnId"]),htmlentities($trnansmode),CURRENCY_CODE.htmlentities($arr["nAmount"]),htmlentities($userName));
					$cnt++;
				}//end while loop
			}//end if
		}//end else
		exportMysqlToCsv('sale.csv',$saleArray,$saleValueArray);
	break;
	
	case "success":
			$sucessArray=array();
			$sucessValueArray=array();
		
			$sucessArray=array('Sl No.','UserName','Transaction Date','Transaction Number','Mode','Amount','Type');
			$sqlArray=mysqli_query($conn, $_SESSION['sess_query']) or die(mysqli_error($conn));
			if(mysqli_num_rows($sqlArray)>0)
			{
				$cnt=1;
				while($arr=mysqli_fetch_array($sqlArray))
				{
					 $trnansmode ="";
								 
					 switch($arr["vMethod"]) 
					 {
						case "pp" : $trnansmode  = "PayPal";
						break;
						
						case "wp" : $trnansmode  = "WorldPay";
						break;
						
						case "bp" : $trnansmode  = "BluePay";
						break;
										
						case "cc" :	$trnansmode ="Credit Card";
						break;
										
						case "bu" : $trnansmode ="Business Check";
						break;
										
						case "ca" : $trnansmode ="Cashiers Check";
						break;

						case "mo" : $trnansmode ="Money Order";
						break;

						case "wt" : $trnansmode ="Wire Transfer";
						break;
																	
						case "pc" : $trnansmode ="Personal Check";
						break;

						case "yp":
							$trnansmode = "Yourpay";
						break;

						case "gc":
							$trnansmode = "Google Checkout";
						break;
										
						case "rp":
							$trnansmode = POINT_NAME;
						break;
					}//end switch			
								
					switch($arr['vType'])
					{
						case "sa":
							$showType='Sale';
						break;
									
						case "s":
							$showType='Swap';
						break;
									
						case "w":
							$showType='Wish';
						break;
					}//end if
								
					//fetch buyer name
					$userName=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',"WHERE nUserId='".$arr['nUserId']."'"),'vLoginName');
					
					//create list array
					$sucessValueArray[$cnt]=array($cnt,htmlentities($userName),date('F d, Y',strtotime($arr["sentDate"])),htmlentities($arr["vTxnId"]),htmlentities($trnansmode),CURRENCY_CODE.htmlentities($arr["nAmount"]),htmlentities($showType));
					$cnt++;
				}//end while loop
			}//end if
		exportMysqlToCsv('success.csv',$sucessArray,$sucessValueArray);
	break;
	
	case "point":
			$pointArray=array();
			$pointValueArray=array();
			
			$pointArray=array('Sl No.','UserName','Transaction Date','Transaction Number','Mode',POINT_NAME,'Amount');
			$sqlArray=mysqli_query($conn, $_SESSION['sess_query']) or die(mysqli_error($conn));
			if(mysqli_num_rows($sqlArray)>0)
			{
				$cnt=1;
				while($arr=mysqli_fetch_array($sqlArray))
				{
					 $trnansmode ="";
								 
					 switch($arr["vMethod"]) 
					 {
						case "pp" : $trnansmode  = "PayPal";
						break;
						
						case "wp" : $trnansmode  = "WorldPay";
						break;
						
						case "bp" : $trnansmode  = "BluePay";
						break;
										
						case "cc" :	$trnansmode ="Credit Card";
						break;
										
						case "bu" : $trnansmode ="Business Check";
						break;
										
						case "ca" : $trnansmode ="Cashiers Check";
						break;

						case "mo" : $trnansmode ="Money Order";
						break;

						case "wt" : $trnansmode ="Wire Transfer";
						break;
																	
						case "pc" : $trnansmode ="Personal Check";
						break;

						case "yp":
							$trnansmode = "Yourpay";
						break;

						case "gc":
							$trnansmode = "Google Checkout";
						break;
										
						case "rp":
							$trnansmode = POINT_NAME;
						break;
					}//end switch			
								
					$userName=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',"WHERE nUserId='".$arr['nUserId']."'"),'vLoginName');
								
					$pointValue=round(($arr["nAmount"]/DisplayLookUp('PointValue'))*DisplayLookUp('PointValue2'),2);
					
					//create list array
					$pointValueArray[$cnt]=array($cnt,htmlentities($userName),date('F d, Y',strtotime($arr["sentDate"])),htmlentities($arr["vTxnId"]),htmlentities($trnansmode),htmlentities($pointValue),CURRENCY_CODE.htmlentities($arr["nAmount"]));
					$cnt++;
				}//end while loop
			}//end if
		exportMysqlToCsv('point.csv',$pointArray,$pointValueArray);
	break;
}//end switch


//clear sessions
$_SESSION['sess_query']='';
?>