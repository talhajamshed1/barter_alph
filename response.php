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
//include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
// Retrieve the XML sent in the HTTP POST request to the ResponseHandler
$xml_response = $HTTP_RAW_POST_DATA;
/*
if (get_magic_quotes_gpc()) { 
$xml_response = stripslashes($xml_response); 
// Capture the Return Response XML from the Google Checkout. 
fnWriteXml($xml_response);
}
function fnWriteXml($string){ 
$file="google_checkout_testing.txt"; 
if(file_exists($file)) { 
$fileid = fopen($file,"a"); 
$strmsg = ""; 
$strmsg.= "***************************************************\r\n"; 
$strmsg.=$string; 
$strmsg.="\r\n***************************************************\r\n"; 
fwrite($fileid,$strmsg);
fclose($fileid);
}else{ 
$fileid = fopen($file,"a"); 
$strmsg = $string; 
fwrite($fileid,$strmsg);	
fclose($fileid);
}
}
*/


if (function_exists('get_magic_quotes_gpc'))
{
	$xml_response = stripslashes($xml_response);
	// Capture the Return Response XML from the Google Checkout.
}
//checking response ends here
$approval_tag = "0";
$approval_tag = DisplayLookUp('userapproval');
if(trim($xml_response)!='')
{

	$gc_flag=true;
	$gc_status='success';
	$_SESSION['sess_flag_failure']='';

        $merchant_data=preg_match_all("/<google-order-number>(.*?)<\/google-order-number>/",$xml_response, $matchorderno);
	$gc_tran=$matchorderno[1][0];

	$merchant_data=preg_match_all("/<merchant-private-data>(.*?)<\/merchant-private-data>/",$xml_response, $matchkeyword);
	$merchant_data=$matchkeyword[1][0];

	//$merchant_data=split('-',$merchant_data);
        $merchant_data=explode('-',$merchant_data);


        $_SESSION["guserid"]=$merchant_data[1];
        $_SESSION['sess_PointSelected']=$merchant_data[2];
        $_SESSION['sess_PointAmount']=$merchant_data[3];
        $txtACurrency=$merchant_data[4];
        $_SESSION["guserFName"]=$merchant_data[5];
        $_SESSION["guseremail"]=$merchant_data[6];


	switch($merchant_data[0])
	{
		case "buycc":

			$_SESSION["guserid"]=$merchant_data[1];
			$saleid=$merchant_data[2];
			$_SESSION["gphone"]=$merchant_data[3];
			$now=$merchant_data[4];
			$cost=$merchant_data[5];
			$txtACurrency=$merchant_data[6];

                        //$gc_tran="";
			$gc_flag = true;


            /* get the invoice number*/
		   $sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from ".TABLEPREFIX."payment ";
		   $result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
		   $row1 = mysqli_fetch_array($result1);
		   $Inv_id=$row1['maxinvid'];

		   $Cust_ip=getClientIP();
		   $Company='-NA-';
		   $Phone=$_SESSION["gphone"];
		   $Cust_id=$_SESSION["guserid"];

           if($gc_flag == true)
            {
               $sql = "Update ".TABLEPREFIX."saledetails set vSaleStatus='2',vTxnId='$gc_tran',dTxnDate=now() where ";
               $sql .= " nSaleId='" . $saleid . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
               $sql .= $now . "' ";
               mysqli_query($conn, $sql) or die(mysqli_error($conn));

              $sql = "Select nUserId from ".TABLEPREFIX."sale where nSaleId='" . addslashes($saleid) . "'";
              mysqli_query($conn, $sql) or die(mysqli_error($conn));

              $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
//              if(mysqli_num_rows($result) > 0)
//			  {
//                  if($row=mysqli_fetch_array($result))
//				  {
//                      $sql = "Update ".TABLEPREFIX."users set nAccount = nAccount +  $cost  where  nUserId='".$row["nUserId"]."'";
//                      mysqli_query($conn, $sql) or die(mysqli_error($conn));
//                  }
//              }

			 	$_SESSION["gsaleextraid"] = "";
                                $_SESSION['txtGoogleId']="";
				$_SESSION['txtGoogleKey']="";
				$_SESSION['chkGoogleSandbox']="";
				$_SESSION['sess_gc_saleid']="";
				$_SESSION['sess_gc_paytype']="";
				$_SESSION['sess_gc_txtPayMethod']="";
				$_SESSION['sess_gc_amount']="";
				$_SESSION['sess_gc_txtACurrency']="";
				$_SESSION["sess_gc_tmpid"] = "";
				$_SESSION["sess_gc_var_title"] = "";
				$_SESSION["sess_gc_amnt"] = "";
				$_SESSION["sess_gc_userid"] = "";
				$_SESSION["sess_gc_dt"] = "";
				$_SESSION['sess_page_name']='';
				$_SESSION['sess_page_return_url_suc']='';
				$_SESSION['sess_page_return_url_fail']='';
				$_SESSION['sess_flag_failure']='';
			}//end if

		break;

		case "featuredpaycc":

			$_SESSION["guserid"]=$merchant_data[1];
			$saleid=$merchant_data[2];
			$_SESSION["gphone"]=$merchant_data[3];
			$amount=$merchant_data[4];
			$txtACurrency=$merchant_data[5];

			$cost = $amount;
			$userid=$_SESSION["guserid"];
			$amnt=$cost;
			$txtACurrency=$txtACurrency;
			$gc_flag = true;
			$gc_err="";
			//$gc_tran="";


			/* get the invoice number*/
			$sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from ".TABLEPREFIX."payment ";
			$result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
			$row1 = mysqli_fetch_array($result1);
			$Inv_id=$row1['maxinvid'];
			/**************************/


			$Cust_ip=getClientIP();
			$Company='-NA-';
			$Phone=$_SESSION["gphone"];
			$Cust_id=$_SESSION["guserid"];
                        $user_lang = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'preferred_language', "WHERE nUserId='" . $Cust_id . "'"), 'preferred_language');

                        /*$fp = fopen("textres.txt", "w");
                        fwrite($fp, $Cust_id."-".$user_lang);
                        fclose($fp);*/
                        
			if($gc_flag == true)
			{
                                 if ($gc_tran!='')
                                        $sqltxn=@mysqli_query($conn, "Select * from ".TABLEPREFIX."payment where vTxnId ='".$gc_tran."' AND vTxn_mode='gc'") or die(mysqli_error($conn));

                                        if(@mysqli_num_rows($sqltxn)>0)
                                        {
                                                //$gc_flag=false;
                                                //$gc_err=ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
                                                $gc_flag=true;

                                                $message = str_replace('{amount}',CURRENCY_CODE.$_SESSION['sess_PointAmount'],str_replace('{point_name}',POINT_NAME,MESSAGE_SUCCESS_PURCHASED_POINTS));

                                                 //clear sessions
                                                 $_SESSION['sess_PointSelected'] = "";
                                                 $_SESSION['sess_PointAmount'] = "";
                                        }//end if
					else
					{
						//get data from temp table
						$sql ="Select * from ".TABLEPREFIX."saleextra where nSaleextraId='$saleid'";
						$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));

						if(mysqli_num_rows($result) > 0)
						{
						   if($row=mysqli_fetch_array($result))
						   {
							  $action="";

							  if($row["nFeatured"]>0)
							  {
								 $vfeatured="Y";
								 $action.="F";
							  }//end if
							  else
							  {
								 $vfeatured="N";
							  }//end else
							  if($row["nCommission"]>0)
							  {
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
                                                          $newValue = $row["nPoint"];
                                                         


							  $sql = "INSERT INTO ".TABLEPREFIX."sale (nSaleId, nCategoryId, nUserId,";
							  $sql .= "vTitle, vBrand, vType, vCondition, vYear, nValue,";
							  $sql .= "nShipping, vUrl, vDescription, dPostDate, nQuantity,vFeatured,vDelStatus,vSmlImg,vImgDes,nPoint)";
							  $sql .= "VALUES ('', '".$row["nCategoryId"]."', '";
							  $sql .= $row["nUserId"];
							  $sql .= "', '".$row["vTitle"]."',";
							  $sql .= "'".$row["vBrand"]."', '".$row["vType"]."', '".$row["vCondition"]."', '".$row["vYear"]."', '".$row["nValue"]."',";
							  $sql .= "'".$row["nShipping"]."', '".$row["vUrl"]."', '".$row["vDescription"]."','";
							  $sql .= $row["dPostDate"]. "'";
							  $sql .= ", '".$row["nQuantity"]."','$vfeatured','0','".$row["vSmlImg"]."','".$row["vImgDes"]."','".$row["nPoint"]."')";
							  $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
						   }//end if
				   }//end if

					//inert multiple images
					$NewId=mysqli_insert_id($conn);

					//update table with new id
					mysqli_query($conn, "update ".TABLEPREFIX."gallery  set nTempId='', nSaleId='".$NewId."'
														where nTempId='".$_SESSION["guserid"]."'") or die(mysqli_error($conn));

				   //get last posted sale id
				   $sql = "Select nSaleId from ".TABLEPREFIX."sale where dPostDate='$txtPostDate' AND nUserId = '" . $_SESSION["guserid"] . "'";
				   $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
						   if(mysqli_num_rows($result) > 0)
						   {
							  if($row=mysqli_fetch_array($result))
							  {
									  $saleid1= $row["nSaleId"];
							  }//end if
						  }//end if


				   //insert to payemnt table

				   $sql="INSERT INTO ".TABLEPREFIX."payment (nTxn_no, vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId,vInvno) VALUES ('', '$action', '$txnid', ' $totalamt', '$paytype', '$txtPostDate', '', '$saleid1','$Inv_id')";
				   $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));

				   //delete from temp table
				   $sql="delete from ".TABLEPREFIX."saleextra where nSaleextraId='$saleid'";
				   $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));


				   //update category listings
					$routesql="Select vRoute from ".TABLEPREFIX."category where nCategoryId ='$txtCategory'";
					$result=mysqli_query($conn, $routesql) or die(mysqli_error($conn));
					$row = mysqli_fetch_array($result);
					$route = $row["vRoute"];
					$countsql="UPDATE ".TABLEPREFIX."category SET nCount=nCount+1 WHERE nCategoryId in($route)";
					$result=mysqli_query($conn, $countsql) or die(mysqli_error($conn));

                                         $categorysql = "Select L.vCategoryDesc from " . TABLEPREFIX . "category C
                                                            LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $user_lang . "'
                                                        where C.nCategoryId ='$txtCategory'";
					 $resultcategory=mysqli_query($conn, $categorysql) or die(mysqli_error($conn));
					 $row = mysqli_fetch_array($resultcategory);
					 $txtCategoryname = $row["vCategoryDesc"];

					/*
                                        * Fetch user language details
                                        */

                                        $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$user_lang."'";
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
                                                   AND L.lang_id = '".$_SESSION["lang_id"]."'";
                                        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                                        $mailRw  = mysqli_fetch_array($mailRs);

                                        $mainTextShow   = $mailRw['content'];

                                        $enbPnt = DisplayLookUp("EnablePoint");


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

                                        $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{txtCategoryname}","{txtTitle}","{txtBrand}","{ddlType}","{ddlCondition}","{txtYear}","{txtValue}");
                                        $arrTReplace	= array(SITE_NAME,SITE_URL,$txtCategoryname,$txtTitle,$txtBrand,$ddlType,$ddlCondition,$txtYear,$itemValue);
                                        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);
                                        $mailcontent1   = $mainTextShow;

                                        $subject    = $mailRw['content_title'];
                                        $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

                                        $StyleContent = MailStyle($sitestyle,SITE_URL);


					$sql = "Select vFirstName,vLoginName,vEmail from ".TABLEPREFIX."users where vAlertStatus='Y' OR nUserId = '" . $_SESSION["guserid"] . "'";
					$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
					if(mysqli_num_rows($result) > 0)
					{
					   while($row=mysqli_fetch_array($result))
					   {
							 $EMail = stripslashes($row["vEmail"]);
                                                         if (trim($row["vFirstName"])=='') $uname = $row["vLoginName"]; else $uname = $row["vFirstName"];
							//readf file n replace
							$arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
							$arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,htmlentities($uname),$mailcontent1,$logourl,date('m/d/Y'),SITE_NAME,$subject);
							$msgBody        = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
							$msgBody        = str_replace($arrSearch,$arrReplace,$msgBody);

							send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');
					   }//end while
					}//end if

					$_SESSION["gsaleextraid"] = "";
					$_SESSION['txtGoogleId']="";
					$_SESSION['txtGoogleKey']="";
					$_SESSION['chkGoogleSandbox']="";
					$_SESSION['sess_gc_saleid']="";
					$_SESSION['sess_gc_paytype']="";
					$_SESSION['sess_gc_txtPayMethod']="";
					$_SESSION['sess_gc_amount']="";
					$_SESSION['sess_gc_txtACurrency']="";
					$_SESSION['sess_page_name']='';
					$_SESSION['sess_page_return_url_suc']='';
					$_SESSION['sess_page_return_url_fail']='';
					$_SESSION['sess_flag_failure']='';
				}//end if
			}//end if
			break;

		case "paycc":

				$id=$merchant_data[1];
				$saleid=$merchant_data[2];
				$_SESSION["gtempid"]=$id;
				$amount=$merchant_data[3];
				$txtACurrency=$merchant_data[4];

				$saleid = $saleid;
				$userid = $userid;
				$now = urldecode($dt);
				$cost = $amount;
				$txtACurrency=$txtACurrency;
				$gc_flag = true;
				$gc_err = "";
				//$gc_tran = "";

				/* get the invoice number*/
				$sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from ".TABLEPREFIX."payment ";
				$result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
				$row1 = mysqli_fetch_array($result1);
				$Inv_id=$row1['maxinvid'];
				/**************************/

				$Cust_ip=getClientIP();
				$Company='-NA-';
				$Phone=$var_phone;
				$Cust_id=$id;


				if ($gc_flag==true)
				{

                                    //if ($gc_tran!='') 
                                        $sqltxn=@mysqli_query($conn, "Select * from ".TABLEPREFIX."payment where vTxnId ='".$gc_tran."' AND vTxn_mode='gc'") or die(mysqli_error($conn));
                                        $numr = mysqli_num_rows($sqltxn);
                                        //$fp = fopen("res.txt", "w");
                                        //fwrite($fp, $numr);
                                        //fclose($fp);
                                        if($numr>0)
                                        {
                                                //$gc_flag=false;
                                                //$gc_err=ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
                                                $gc_flag=true;

                                                $message = str_replace('{amount}',CURRENCY_CODE.$_SESSION['sess_PointAmount'],str_replace('{point_name}',POINT_NAME,MESSAGE_SUCCESS_PURCHASED_POINTS));

                                                 //clear sessions
                                                 $_SESSION['sess_PointSelected'] = "";
                                                 $_SESSION['sess_PointAmount'] = "";
                                        }//end if
                                        else
					{
						$var_id = $_SESSION["gtempid"];
						$var_amount = "";
						$var_txnid = "";
						$var_method = "";
						$var_login_name = "";
						$var_password = "";
						$var_first_name = "";
						$var_last_name = "";
						// here the transaction id has to be set that comes from the payment gateway
						$var_txnid = "$txnid";

						$sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
						$sql .= "vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
						$sql .= "vDescription  ,dDateReg   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee,nRefId
									from ".TABLEPREFIX."users where nUserId='" . $var_id . "'";

						$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

						if (mysqli_num_rows($result) > 0)
						{
							if ($row = mysqli_fetch_array($result))
							{
								// if you have data for the transaction
								$var_login_name = $row["vLoginName"];
								$var_password = $row["vPassword"];
								if (trim($var_first_name)=='') $var_first_name = $row["vLoginName"];
                                                                else $var_first_name = $row["vFirstName"];
								$var_last_name = $row["vLastName"];
								$var_email = $row["vEmail"];
								$totalamt = $row["nAmount"];
								$paytype = $row["vMethod"];

								if ($approval_tag=="1")
								{
									$sql = "UPDATE ".TABLEPREFIX."users SET dDateReg=now(),vTxnId='".addslashes($var_txnid)."'
													WHERE nUserId='".$row['nUserId']."'";
								}//end if
								else if ($approval_tag=="E")
								{
									$sql = "UPDATE ".TABLEPREFIX."users SET dDateReg=now(),vTxnId='".addslashes($var_txnid)."'
													WHERE nUserId='".$row['nUserId']."'";
								}//end if
								else
								{
									$sql = "UPDATE ".TABLEPREFIX."users SET dDateReg=now(),vTxnId='".addslashes($var_txnid)."',
													vStatus='0',vDelStatus='0' WHERE nUserId='".$row['nUserId']."'";
								}//end else
								@mysqli_query($conn, $sql) or die(mysqli_error($conn));

								$var_new_id = @mysqli_insert_id($conn);

								// Addition for referrals
								$var_reg_amount = 0;

							if ($row["nRefId"]!="0")
							{
								$sql = "Select nRefId,nUserId,nRegAmount from ".TABLEPREFIX."referrals where vRegStatus='0' AND nRefId='" . $row["nRefId"] . "'";
								$result_test = @mysqli_query($conn, $sql) or die(mysqli_error($conn));

								if (@mysqli_num_rows($result_test) > 0)
								{
									if ($row_final = @mysqli_fetch_array($result_test))
									{
										$var_reg_amount = $row_final["nRegAmount"];

										$sql = "Update ".TABLEPREFIX."referrals set vRegStatus='1',";
										$sql .= "nUserRegId='" . $var_new_id . "',dRegDate=now() where nRefId='" . $row_final["nRefId"] . "'";

										@mysqli_query($conn, $sql) or die(mysqli_error($conn));

										$sql = "Select nUserId from ".TABLEPREFIX."user_referral where nUserId='" . $row_final["nUserId"] . "'";
										$result_ur = @mysqli_query($conn, $sql) or die(mysqli_error($conn));
										if (@mysqli_num_rows($result_ur) > 0)
										{
											$sql = "Update ".TABLEPREFIX."user_referral set nRegCount = nRegCount + 1,nRegAmount=nRegAmount + $var_reg_amount where nUserId='" . $row_final["nUserId"] . "'";
										}//end if
										else
										{
											$sql = "insert into ".TABLEPREFIX."user_referral(nUserId,nRegCount,nRegAmount) values('"
											 . $row_final["nUserId"] . "','1','$var_reg_amount')";
										}//end else
										@mysqli_query($conn, $sql) or die(mysqli_error($conn));
									}//end if
								}//end if
							}//end if
							// end of referrals
							$_SESSION["gtempid"] = "";

							/*
                                                        * Fetch user language details
                                                        */
                                                        if($_SESSION["lang_id"])
                                                            $where = "WHERE lang_id = '".$_SESSION["lang_id"]."'";
                                                        else
                                                            $where = "WHERE lower(lang_name) = 'english'";
                                                        $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang $where";
                                                        $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
                                                        $langRw = mysqli_fetch_array($langRs);

                                                        /*
                                                        * Fetch email contents from content table
                                                        */
                                                        if ($approval_tag == "E") {
                                                            $mailSql = "SELECT L.content,L.content_title
                                                              FROM ".TABLEPREFIX."content C
                                                              JOIN ".TABLEPREFIX."content_lang L
                                                                ON C.content_id = L.content_id
                                                               AND C.content_name = 'activationLinkOnRegister'
                                                               AND C.content_type = 'email'
                                                               AND L.lang_id = '".$_SESSION["lang_id"]."'";
                                                        }else{
                                                            $mailSql = "SELECT L.content,L.content_title
                                                              FROM ".TABLEPREFIX."content C
                                                              JOIN ".TABLEPREFIX."content_lang L
                                                                ON C.content_id = L.content_id
                                                               AND C.content_name = 'welcomeMailUser'
                                                               AND C.content_type = 'email'
                                                               AND L.lang_id = '".$_SESSION["lang_id"]."'";
                                                        }
                                                        $activate_link = '<a href="' . SITE_URL . '/activation.php?uid=' . $uid . '&status=eactivate">Activate</a>';
                                                        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                                                        $mailRw  = mysqli_fetch_array($mailRs);

                                                        $mainTextShow   = $mailRw['content'];

                                                        $mainTextShow   = str_replace("{Password}", "", $mainTextShow);
                                                        $mainTextShow   = str_replace("Password", "", $mainTextShow);

                                                        $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
                                                        $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($var_login_name),$_SESSION["tmp_pd"],$activate_link );
                                                        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                                                        $mailcontent1   = $mainTextShow;

                                                        $subject    = $mailRw['content_title'];
                                                        $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

                                                        $StyleContent   =  MailStyle($sitestyle,SITE_URL);
							$EMail = $var_email;
							$StyleContent=MailStyle($sitestyle,SITE_URL);

							//readf file n replace
							$arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
							$arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,$var_login_name,$mailcontent1,$logourl,date('m/d/Y'),SITE_NAME,$subject);
							$msgBody        = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
							$msgBody        = str_replace($arrSearch,$arrReplace,$msgBody);

							send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');

							$_SESSION["guserid"] = $var_new_id;

							$sql = "INSERT INTO ".TABLEPREFIX."payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, nSaleId,vInvno)
											VALUES ('R', '$txnid', ' $totalamt', '$paytype',now(), '" . $_SESSION["guserid"] . "', '','$Inv_id')";
							$result = @mysqli_query($conn, $sql) or die(mysqli_error($conn));

							$var_admin_email = ADMIN_EMAIL;

							if(DisplayLookUp('4')!='')
							{
								$var_admin_email = DisplayLookUp('4');
							}//end if

                                                        /*
                                                        * Fetch email contents from content table
                                                        */
                                                        $mailRw = array();
                                                            $mailSql = "SELECT L.content,L.content_title
                                                              FROM ".TABLEPREFIX."content C
                                                              JOIN ".TABLEPREFIX."content_lang L
                                                                ON C.content_id = L.content_id
                                                               AND C.content_name = 'registrationNotificationAdmin'
                                                               AND C.content_type = 'email'
                                                               AND L.lang_id = '".$_SESSION["lang_id"]."'";

                                                        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                                                        $mailRw  = mysqli_fetch_array($mailRs);

                                                        $mainTextShow   = $mailRw['content'];

                                                        $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
                                                        $arrTReplace	= array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($var_login_name),htmlentities($var_first_name),$var_email );
                                                        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                                                        $mailcontent1   = $mainTextShow;

                                                        $subject2    = $mailRw['content_title'];
                                                        $subject2    = str_replace('{SITE_NAME}',SITE_NAME,$subject2);
                                                        $StyleContent= MailStyle($sitestyle,SITE_URL);


							//readf file n replace
							$arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
							$arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,'Admin',$mailcontent1,$logourl,date('m/d/Y'),SITE_NAME,$subject2);
							$msgBody        = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
							$msgBody        = str_replace($arrSearch,$arrReplace,$msgBody);

							send_mail($var_admin_email,$subject2,$msgBody,SITE_EMAIL,'Admin');

							$_SESSION["gtempid"] = $_SESSION["guserid"];
							$_SESSION["guserid"] = "";
							$_SESSION["gsaleextraid"] = "";
							$_SESSION['txtGoogleId']="";
							$_SESSION['txtGoogleKey']="";
							$_SESSION['chkGoogleSandbox']="";
							$_SESSION['sess_gc_saleid']="";
							$_SESSION['sess_gc_paytype']="";
							$_SESSION['sess_gc_txtPayMethod']="";
							$_SESSION['sess_gc_amount']="";
							$_SESSION['sess_gc_txtACurrency']="";
							$_SESSION['sess_gc_userid']="";
							$_SESSION['sess_gc_id']="";
							$_SESSION['sess_page_name']='';
							$_SESSION['sess_page_return_url_suc']='';
							$_SESSION['sess_page_return_url_fail']='';
							$_SESSION['sess_flag_failure']='';
						}//end if
					}//end if
				}//end if
			}//end if
				// End of the transaction  for adding a user since payment is successfull
			break;

		case "paypagecc":

				$var_tmpid=$merchant_data[1];
				$_SESSION["guserid"]=$merchant_data[2];
				$_SESSION["gphone"]=$merchant_data[3];
				$txtACurrency=$merchant_data[4];

				//$gc_tran="";
				$gc_flag = true;
				/* get the invoice number*/
				$sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from ".TABLEPREFIX."payment ";
				$result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
				$row1 = mysqli_fetch_array($result1);
				$Inv_id=$row1['maxinvid'];
				/**************************/

				$Cust_ip=getClientIP();
				$Company='-NA-';
				$Phone=$_SESSION["gphone"];
				$Cust_id=$_SESSION["guserid"];


				if($gc_flag==true)
				{
						//Start of the process of performing the transaction entry
						$db_swapid="";
						$db_userid="";
						$db_amount=0;
						$db_method="";
						$db_mode="";
						$db_post_type="";

						//here the transaction id has to be set that comes from the payment gateway
						$var_txnid=$gc_tran;

					   $sql= "Select nTempId,nSwapId,nUserId,nAmount,vMethod,vMode,vPostType,dDate
												from ".TABLEPREFIX."swaptemp where nTempId='" . addslashes($var_tmpid) . "' ";

					   $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
					   if(mysqli_num_rows($result) > 0)
					   {
						   if($row=mysqli_fetch_array($result))
						   {
								//if you have data for the transaction
								$db_swapid=$row["nSwapId"];
								$db_userid=$row["nUserId"];
								$db_amount=$row["nAmount"];
								$db_method=$row["vMethod"];
								$db_mode=$row["vMode"];
								$db_post_type=$row["vPostType"];
								$var_swapmember="";
								$var_incmember="";

								if($db_mode=="od")
								{
									//if the payment is being made by the person who made the offer
									//that means the present userid is the one that is present in the swaptxn table
									//and this user is giving money to the person who made the swap table entry
									//and the userid is fetched from the table swap
									//swapmember --> the one in the temporary table
									//incmember --> the one who receives the money(comes from the swap table)
									$var_swapmember=$db_userid;

									$sql = "Select nUserId from ".TABLEPREFIX."swap where nSwapId='"
																		   . $db_swapid  . "' ";
									$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
									if(mysqli_num_rows($result) > 0)
									{
										if($row=mysqli_fetch_array($result))
										{
											 $var_incmember=$row["nUserId"];
										}//end if
									}//end if
							   }//end if
							   else if($db_mode=="om")
							   {
									//if the payment is being made by the person who accepts the offer(ie. the one who
									//made the main swap item),here the userid is the one in the swap table,hence
									//he has to fetch the swapuserid from the swaptxn table,and give money to him
									//swapmember --> the one in the swaptxn table
									//incmember --> the one who receives the money(comes from the swaptxn table)

									$db_amount = -1 * $db_amount;

									$sql = "Select nUserId from ".TABLEPREFIX."swaptxn where nSwapId='"
																		   . $db_swapid  . "' and vStatus='A'";
									$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
									if(mysqli_num_rows($result) > 0)
									{
										 if($row=mysqli_fetch_array($result))
										 {
											 $var_swapmember=$row["nUserId"];
											 $var_incmember=$row["nUserId"];
										 }//end if
									}//end if
							 }//end else
							 else if($db_mode=="wm")
							 {
								//if the payment is being made by the person who accepts the offer(ie. the one who
								//made the main swap item),here the userid is the one in the swap table,hence
								//he has to fetch the swapuserid from the swaptxn table,and give money to him
								//swapmember --> the one in the swaptxn table
							   //incmember --> the one who receives the money(comes from the swaptxn table)

								$db_amount = -1 * $db_amount;

								$sql = "Select nUserId from ".TABLEPREFIX."swaptxn where nSwapId='"
																		   . $db_swapid  . "' and vStatus='A'";
								$result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
								if(mysqli_num_rows($result) > 0)
								{
									 if($row=mysqli_fetch_array($result))
									 {
										 $var_swapmember=$row["nUserId"];
										 $var_incmember=$row["nUserId"];
									 }//end if
								}//end if
							 }//end else if

                                                         $db_swap_ids = get_swaps_ids($db_swapid);
                                                         
                                                         $db_amount = ($db_amount < 0)?(-1 * $db_amount):$db_amount;
                                                         
							 $sql = "Update ".TABLEPREFIX."swap set 
													  nSwapAmount='$db_amount',
													   vEscrow='1',

													   vMethod='$db_method',
													   vTxnId='$var_txnid',
													   vSwapStatus='2',dTxnDate=now() where
													   nSwapId in (" . $db_swap_ids . ") ";
							mysqli_query($conn, $sql) or die(mysqli_error($conn));//nSwapMember='$var_swapmember',

							
//
//							$sql = "Update ".TABLEPREFIX."users set nAccount=nAccount + $db_amount
//																				  where nUserId='" . $var_incmember . "' ";
//							mysqli_query($conn, $sql) or die(mysqli_error($conn));

							$sql = "delete from ".TABLEPREFIX."swaptemp where nTempId='"
												   . addslashes($var_tmpid) . "' ";
							mysqli_query($conn, $sql) or die(mysqli_error($conn));
						}//end else if
				  }//end if
				  else
				  {
						$_SESSION["gsaleextraid"] = "";
						$_SESSION['txtGoogleId']="";
						$_SESSION['txtGoogleKey']="";
						$_SESSION['chkGoogleSandbox']="";
						$_SESSION['sess_gc_saleid']="";
						$_SESSION['sess_gc_paytype']="";
						$_SESSION['sess_gc_txtPayMethod']="";
						$_SESSION['sess_gc_amount']="";
						$_SESSION['sess_gc_txtACurrency']="";
						$_SESSION["sess_gc_tmpid"] = "";
						$_SESSION["sess_gc_var_title"] = "";
						$_SESSION['sess_page_name']='';
						$_SESSION['sess_page_return_url_suc']='';
						$_SESSION['sess_page_return_url_fail']='';
						$_SESSION['sess_flag_failure']='';
				  }//end else
				  //End of the process of performing the transaction entry
				}//end if

			break;

		case "points":

				$_SESSION["guserid"]=$merchant_data[1];
				$_SESSION['sess_PointSelected']=$merchant_data[2];
				$_SESSION['sess_PointAmount']=$merchant_data[3];
				$txtACurrency=$merchant_data[4];
				$_SESSION["guserFName"]=$merchant_data[5];
				$_SESSION["guseremail"]=$merchant_data[6];
                                $_SESSION["lang_id"] = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'preferred_language', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'preferred_language');

				$txtACurrency=$txtACurrency;
					//$gc_tran="";
					$gc_flag = true;

					   if($gc_flag == true)
					   {            
							if ($gc_tran!='') $sqltxn=@mysqli_query($conn, "Select vTxnId from ".TABLEPREFIX."creditpayments where vTxnId ='".$gc_tran."' AND vMethod='gc'") or die(mysqli_error($conn));

							if(@mysqli_num_rows($sqltxn)>0)
							{
								//$gc_flag=false;
								//$gc_err=ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
                                                                $gc_flag=true;

								$message = str_replace('{amount}',CURRENCY_CODE.$_SESSION['sess_PointAmount'],str_replace('{point_name}',POINT_NAME,MESSAGE_SUCCESS_PURCHASED_POINTS));

								 //clear sessions
								 $_SESSION['sess_PointSelected'] = "";
								 $_SESSION['sess_PointAmount'] = "";
							}//end if
							else
							{
								$var_date=date('m/d/Y');

								//checking alredy exits
								$chkPoint=fetchSingleValue(select_rows(TABLEPREFIX.'usercredits','nPoints',"WHERE nUserId='".$_SESSION["guserid"]."'"),'nPoints');
								if(trim($chkPoint)!='')
								{
									//update points to user credit
									mysqli_query($conn, "UPDATE ".TABLEPREFIX."usercredits set nPoints=nPoints+".$_SESSION['sess_PointSelected']." WHERE
															nUserId='".$_SESSION["guserid"]."'") or die(mysqli_error($conn));
								}//end if
								else
								{
									//add points to user credit
									mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."usercredits (nPoints,nUserId) VALUES ('".$_SESSION['sess_PointSelected']."','".$_SESSION["guserid"]."')") or die(mysqli_error($conn));
								}//end else

								//added purchase date point and amount conversion status
								$vComments=CURRENCY_CODE.DisplayLookUp('PointValue').'&nbsp;=&nbsp;'.DisplayLookUp('PointValue2').'&nbsp;'.POINT_NAME;

								//add into user table
								mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."creditpayments (nUserId,nAmount,nPoints,vTxnId,vMethod,dDate,vCurrentTransaction,vStatus) VALUES
												('".$_SESSION["guserid"]."','".$_SESSION['sess_PointAmount']."','".$_SESSION['sess_PointSelected']."','".$gc_tran."',
												'gc',now(),'".addslashes($vComments)."','A')") or die(mysqli_error($conn));



                                                                    /*
                                                                    * Fetch user language details
                                                                    */

                                                                    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
                                                                    $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
                                                                    $langRw = mysqli_fetch_array($langRs);

                                                                    /*
                                                                    * Fetch email contents from content table
                                                                    */
                                                                    $mailSql = "SELECT L.content,L.content_title
                                                                      FROM ".TABLEPREFIX."content C
                                                                      JOIN ".TABLEPREFIX."content_lang L
                                                                        ON C.content_id = L.content_id
                                                                       AND C.content_name = 'pointsPurchasedMailToUser'
                                                                       AND C.content_type = 'email'
                                                                       AND L.lang_id = '".$_SESSION["lang_id"]."'";

                                                                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                                                                    $mailRw  = mysqli_fetch_array($mailRs);

                                                                    $mainTextShow   = $mailRw['content'];
                                                                    $mainTextShow    = str_replace("{POINT_NAME}", "{point_name}", $mainTextShow);

                                                                    $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{POINT_NAME}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
                                                                    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,POINT_NAME,'Google CheckOut',date('m/d/Y'),CURRENCY_CODE.$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
                                                                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                                                                    $mailcontent1   = $mainTextShow;

                                                                    $subject    = $mailRw['content_title'];
                                                                    $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

                                                                    $StyleContent = MailStyle($sitestyle, SITE_URL);

								 
								  $EMail = $_SESSION["guseremail"];

								  //readf file n replace
								  $arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
								  $arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,'Member',$mailcontent1,$logourl,date('m/d/Y'),SITE_NAME,$subject);
								  $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
								  $msgBody=str_replace($arrSearch,$arrReplace,$msgBody);
								  send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');


								  //mail sent to admin
								  $var_admin_email=SITE_NAME;

								  if(DisplayLookUp('4')!='')
								  {
										$var_admin_email=DisplayLookUp('4');
								  }//end if

								  /*
                                                                    * Fetch email contents from content table
                                                                    */
                                                                    $mailSql = "SELECT L.content,L.content_title
                                                                      FROM ".TABLEPREFIX."content C
                                                                      JOIN ".TABLEPREFIX."content_lang L
                                                                        ON C.content_id = L.content_id
                                                                       AND C.content_name = 'pointsPurchasedMailToAdmin'
                                                                       AND C.content_type = 'email'
                                                                       AND L.lang_id = '".$_SESSION["lang_id"]."'";

                                                                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                                                                    $mailRw  = mysqli_fetch_array($mailRs);

                                                                    $mainTextShow   = $mailRw['content'];
                                                                    $mainTextShow    = str_replace("{POINT_NAME}", "{point_name}", $mainTextShow);

                                                                    $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{POINT_NAME}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
                                                                    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,POINT_NAME,"Google CheckOut",date('m/d/Y'),CURRENCY_CODE.$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
                                                                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                                                                    $mailcontent1   = $mainTextShow;

                                                                    $subject    = $mailRw['content_title'];
                                                                    $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

                                                                    $StyleContent = MailStyle($sitestyle, SITE_URL);

                                                                    $EMail = $var_admin_email;


								  $arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
								  $arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,'Admin',$mailcontent1,$logourl,date('m/d/Y'),SITE_NAME,$subject);
								  $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
								  $msgBody=str_replace($arrSearch,$arrReplace,$msgBody);
								  send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');

								 $gc_flag=true;

								$message = str_replace('{amount}',CURRENCY_CODE.$_SESSION['sess_PointAmount'],str_replace('{point_name}',POINT_NAME,MESSAGE_SUCCESS_PURCHASED_POINTS));

								 //clear sessions
								 $_SESSION['sess_PointSelected'] = "";
								 $_SESSION['sess_PointAmount'] = "";
							}//end else
					   }//end if
			break;

		case "successTrans":

				$_SESSION["guserid"]=$merchant_data[1];
				$_SESSION['sess_success_fee_id']=$merchant_data[2];
				$txtACurrency=$merchant_data[3];
				$_SESSION["guserFName"]=$merchant_data[4];
				$_SESSION["guseremail"]=$merchant_data[5];

				$txtACurrency=$txtACurrency;
					//$gc_tran="";
					$gc_flag = true;

					   if($gc_flag == true)
					   {
							$sqltxn=@mysqli_query($conn, "Select vTxnId from ".TABLEPREFIX."successtransactionpayments where vTxnId ='".$gc_tran."' AND vMethod='gc'") or die(mysqli_error($conn));

							if(@mysqli_num_rows($sqltxn)>0)
							{
								$gc_flag=false;
								$gc_err=ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
							}//end if
							else
							{
								$var_date=date('m/d/Y');

								//select value from succes
								$sqlSuccess=mysqli_query($conn, "select nProdId,nAmount,nPoints from ".TABLEPREFIX."successfee where nSId='".$_SESSION['sess_success_fee_id']."'") or die(mysqli_error($conn));
								if(mysqli_num_rows($sqlSuccess)>0)
								{
									$passProdId=mysqli_result($sqlSuccess,0,'nProdId');
									$passAmount=mysqli_result($sqlSuccess,0,'nAmount');
									$passPoints=mysqli_result($sqlSuccess,0,'nPoints');
								}//end if

								//update status in success fee table
								mysqli_query($conn, "UPDATE ".TABLEPREFIX."successfee SET vStatus='A' WHERE nSId='".$_SESSION['sess_success_fee_id']."'") or die(mysqli_error($conn));

//								$sql = "Update ".TABLEPREFIX."users set nAccount = nAccount +  ".$passAmount."  where  nUserId='" . $_SESSION["guserid"] . "'";
//								mysqli_query($conn, $sql) or die(mysqli_error($conn));

								//checking alredy exits
								$chkPoint=fetchSingleValue(select_rows(TABLEPREFIX.'usercredits','nPoints',"WHERE nUserId='".$_SESSION["guserid"]."'"),'nPoints');
								if(trim($chkPoint)!='')
								{
									//update points to user credit
									mysqli_query($conn, "UPDATE ".TABLEPREFIX."usercredits set nPoints=nPoints+".$passPoints." WHERE
																nUserId='".$_SESSION["guserid"]."'") or die(mysqli_error($conn));
								}//end if
								else
								{
									//add points to user credit
									mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."usercredits (nPoints,nUserId) VALUES ('".$passPoints."','".$_SESSION["guserid"]."')") or die(mysqli_error($conn));
								}//end else

								//add into user table
								mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."successtransactionpayments (nUserId,nAmount,nProdId,vTxnId,vMethod,dDate,vStatus,nSId) VALUES
												('".$_SESSION["guserid"]."','".round(DisplayLookUp('SuccessFee'),2)."','".$passProdId."','".$gc_tran."',
												'gc',now(),'A','".$_SESSION['sess_success_fee_id']."')") or die(mysqli_error($conn));

                                                                /*
                                                                * Fetch user language details
                                                                */

                                                                $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
                                                                $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
                                                                $langRw = mysqli_fetch_array($langRs);

                                                                /*
                                                                * Fetch email contents from content table
                                                                */
                                                                $mailSql = "SELECT L.content,L.content_title
                                                                  FROM ".TABLEPREFIX."content C
                                                                  JOIN ".TABLEPREFIX."content_lang L
                                                                    ON C.content_id = L.content_id
                                                                   AND C.content_name = 'SuccessFeeMailMailToUser'
                                                                   AND C.content_type = 'email'
                                                                   AND L.lang_id = '".$_SESSION["lang_id"]."'";

                                                                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                                                                $mailRw  = mysqli_fetch_array($mailRs);

                                                                $mainTextShow   = $mailRw['content'];
                                                                $mainTextShow    = str_replace("{POINT_NAME}", "{point_name}", $mainTextShow);

                                                                $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{POINT_NAME}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}","{purchase_details}");
                                                                $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,POINT_NAME,"Google CheckOut",date('m/d/Y'),CURRENCY_CODE.$_SESSION["sess_PointAmount"],$_SESSION["guserFName"],$passPoints);
                                                                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                                                                $mailcontent1   = $mainTextShow;

                                                                $subject    = $mailRw['content_title'];
                                                                $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

                                                                $StyleContent = MailStyle($sitestyle, SITE_URL);
                                                                $EMail = $_SESSION["guseremail"];

								  //readf file n replace
								  $arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
								  $arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,'Member',$mailcontent1,$logourl,date('m/d/Y'),SITE_NAME,$subject);
								  $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
								  $msgBody=str_replace($arrSearch,$arrReplace,$msgBody);
								  send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');


								  //mail sent to admin
								  $var_admin_email=SITE_NAME;

								  if(DisplayLookUp('4')!='')
								  {
										$var_admin_email=DisplayLookUp('4');
								  }//end if

								  /*
                                                                * Fetch email contents from content table
                                                                */
                                                                $mailSql = "SELECT L.content,L.content_title
                                                                  FROM ".TABLEPREFIX."content C
                                                                  JOIN ".TABLEPREFIX."content_lang L
                                                                    ON C.content_id = L.content_id
                                                                   AND C.content_name = 'pointsPurchasedMailToAdmin'
                                                                   AND C.content_type = 'email'
                                                                   AND L.lang_id = '".$_SESSION["lang_id"]."'";

                                                                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                                                                $mailRw  = mysqli_fetch_array($mailRs);

                                                                $mainTextShow   = $mailRw['content'];
                                                                $mainTextShow    = str_replace("{POINT_NAME}", "{point_name}", $mainTextShow);

                                                                $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{point_val}","{point_name}","{POINT_NAME}","{payment_type}","{date}","{sess_PointAmount}","{guserFName}");
                                                                $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,$_SESSION['sess_PointSelected'],POINT_NAME,POINT_NAME,"Google CheckOut",date('m/d/Y'),CURRENCY_CODE.$_SESSION["sess_PointAmount"],$_SESSION["guserFName"]);
                                                                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                                                                $mailcontent1   = $mainTextShow;

                                                                $subject    = $mailRw['content_title'];
                                                                $subject    = str_replace("{POINT_NAME}", POINT_NAME, $subject);

                                                                $StyleContent = MailStyle($sitestyle, SITE_URL);
								  $EMail = $var_admin_email;


								  $arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
								  $arrReplace	= array(SITE_TITLE,$StyleContent,SITE_URL,'Admin',$mailcontent1,$logourl,date('m/d/Y'),SITE_NAME,$subject);
								  $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
								  $msgBody=str_replace($arrSearch,$arrReplace,$msgBody);
								  send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');

								 $gc_flag=true;

								 $message = stripslashes(MESSAGE_THANKYOU_FOR_PAYMENT_RECEIPT_EMAILED);//"Thank you for your payment.&nbsp;&nbsp;Your transaction has been completed, and a receipt for your purchase has been emailed to you.<br> You may log into your account at ".SITE_URL."to view details of this transaction.";
								 //$message .="<br>&nbsp;<br>Please login to access the services of ".SITE_URL."";

								 //clear sessions
								 $_SESSION['sess_success_fee_id'] = "";
							}//end else
					   }//end if
			break;
	}//end switch
}//end if
else{
	$gc_flag=false;
	$gc_status='failure';
	$_SESSION['sess_flag_failure']=$gc_flag;
}//end else
?>
