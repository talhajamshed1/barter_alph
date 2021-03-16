<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $conn;
function isValidLogin($user,$password,$display,$device_id,$device_type)
{
	global $conn; 
    $response_array=array();
//     if($type=="affiliate")
//	 {
//		  $sql_aff=mysqli_query($conn, "SELECT nAffiliateId,vFirstName, vLastName   FROM ".TABLEPREFIX."affiliate
//									WHERE vLoginName = '$user' and vDelStatus != '1'") or die(mysqli_error($conn));
//		  if(mysqli_num_rows($sql_aff) > 0)
//		  {
//			  $sql = "SELECT nAffiliateId,vFirstName, vLastName   FROM ".TABLEPREFIX."affiliate";
//                       $sql .= " WHERE vLoginName = '$user' AND vPassword = '".md5($password)."' and vDelStatus != '1'";
//                       $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
//                       if(mysqli_num_rows($result) > 0)
//			   {
//                       $row = mysqli_fetch_array($result);
//                       $response_array["affid"] = $row["nAffiliateId"];
//                       $response_array["loginname"] = $user;
//                       $name = $row["vFirstName"];
//                       if($row["vLastName"]!="")
//                       {
//                               $name .= " ". $row["vLastName"];
//                       }//end if
//                       $response_array["affname"] = $name;
//                       save_device_and_authkey($response_array["affid"],$device_id,$device_type,$auth_key);
////                        header("location:affiliatemain.php");
////                       exit();
//               }//endif
//			   else
//			   {
//                       $message = ERROR_INVALID_USERNAME_PASSWORD;
//               }//end else
//		  }//end if
//          else
//          {
//				$message = ERROR_INVALID_USERNAME_PASSWORD;
//	      }//end else
//     }//end if
//	 else if($type=="user")
//	 {
		 $sql_use = mysqli_query($conn, "SELECT u.nUserId, u.vEmail,u.vStatus,u.vPhone,u.vFirstName,n.nLoggedOn  FROM ".TABLEPREFIX."users u 
							LEFT JOIN ".TABLEPREFIX."online n ON (u.nUserId = n.nUserId) WHERE
				            u.vLoginName = '$user' and u.vDelStatus != '1'") or die(mysqli_error($conn));

		  if(mysqli_num_rows($sql_use) >0)
		  {
				$sql = "SELECT u.nUserId, u.vEmail,u.vStatus,u.vPhone,u.vFirstName,n.nLoggedOn,u.nPlanId,u.stripe_pub_key,u.stripe_secret_key  FROM ".TABLEPREFIX."users u 
							LEFT JOIN ".TABLEPREFIX."online n ON (u.nUserId = n.nUserId) WHERE
				            u.vLoginName = '$user' AND u.vPassword = '".md5($password)."' and u.vDelStatus != '1'";
                               

               $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
               if(mysqli_num_rows($result) >0)
			   {
                  $row = mysqli_fetch_array($result);
				  
 				  //checking date of expiry
				  $sqlChkExp=mysqli_query($conn, "SELECT u.dPlanExpDate < now() as expired FROM ".TABLEPREFIX."users u LEFT JOIN
				  							 ".TABLEPREFIX."plan p ON u.nPlanId=p.nPlanId WHERE u.nUserId='".$row["nUserId"]."'
											 AND p.vPeriods!='F' and u.dPlanExpDate!='0000-00-00'") or die(mysqli_error($conn));
                                                                   
                                  if(mysqli_num_rows($sqlChkExp)>0)
				  {
				  	  if(mysqli_result($sqlChkExp,0,'expired')=='1')
					  {
					  		$response_array["userid"] = $row["nUserId"];
							$response_array["useremail"] = $row["vEmail"];
                                                        if (trim($row["vFirstName"])=='')$response_array["userFName"] = $user;
							else $response_array["userFName"] = $row["vFirstName"];
							$response_array["loginname"] = $user;
							$response_array["phone"] = $row["vPhone"];
							$response_array["sess_PlanId"] = $row["nPlanId"];
							$response_array['sess_uName']=$row['vLoginName'];
							if($row['stripe_pub_key'] == "" || $row['stripe_secret_key'] == ""){
								$response_array['stripe_status']= "Invalid";
							}else{
								$response_array['stripe_status']= "Valid";
							}	
							$now = time();
							$session_active_time = ini_get("session.gc_maxlifetime");
							$activeTill = time() + $session_active_time;
							
							$response_array['sess_activeTill']=$activeTill;
		
							/* User is loggin for the first time */
							mysqli_query($conn, "Update ".TABLEPREFIX."users set nLastLogin='".$now."' where nUserId='".$row['nUserId']."'") or
													die(mysqli_error($conn));
			
							if ($row['nLoggedOn']=='')
							{
								// No entry in the online table
								mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."online (nUserId,nLoggedOn,nActiveTill,nIdle,vVisible) VALUES
													 ('".$row['nUserId']."','".$now."','".$activeTill."','0','".$display."')") or die(mysqli_error($conn));
							}//end if
							else
							{
								mysqli_query($conn, "UPDATE ".TABLEPREFIX."online SET nLoggedOn='".$now."',nActiveTill='".$activeTill."',vVisible='".$display."',
														nIdle='0' WHERE nUserId='".$row['nUserId']."'") or die(mysqli_error($conn));
							}//end else
								
							//plan expired	
						    $response_array['sess_upgradeplan']="PLEASEUPGRADE";
                                                    $response_array['sess_upgradeplan_message']=MESSAGE_CURRENT_PLAN_EXPIRED;
                                                    $message=MESSAGE_CURRENT_PLAN_EXPIRED;
//							echo "<script>alert(\"".MESSAGE_CURRENT_PLAN_EXPIRED."\");</script>";
//							echo "<script>location.href='change_plan.php'</script>";
//							exit();
					  }//end if
					  else
					  {
						  if($row["vStatus"] == "0")
						  {
								$response_array["userid"] = $row["nUserId"];
								$response_array["useremail"] = $row["vEmail"];
                                                                if (trim($row["vFirstName"])=='')$response_array["userFName"] = $user;
                                                                else $response_array["userFName"] = $row["vFirstName"];
								$response_array["loginname"] = $user;
								$response_array["phone"] = $row["vPhone"];
								$response_array["sess_PlanId"] = $row["nPlanId"];
								$response_array['sess_uName']=$row['vLoginName'];
								if($row['stripe_pub_key'] == "" || $row['stripe_secret_key'] == ""){
								$response_array['stripe_status']= "Invalid";
								}else{
									$response_array['stripe_status']= "Valid";
								}
								$now = time();
								$session_active_time = ini_get("session.gc_maxlifetime");
								$activeTill = time() + $session_active_time;
								
								$response_array['sess_activeTill']=$activeTill;
		
								/* User is loggin for the first time */
								mysqli_query($conn, "Update ".TABLEPREFIX."users set nLastLogin='".$now."' where nUserId='".$row['nUserId']."'") or
													die(mysqli_error($conn));
			
								if ($row['nLoggedOn']=='')
								{
									// No entry in the online table
									mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."online (nUserId,nLoggedOn,nActiveTill,nIdle,vVisible) VALUES
													 ('".$row['nUserId']."','".$now."','".$activeTill."','0','".$display."')") or die(mysqli_error($conn));
								}//end if
								else
								{
									mysqli_query($conn, "UPDATE ".TABLEPREFIX."online SET nLoggedOn='".$now."',nActiveTill='".$activeTill."',vVisible='".$display."',
														nIdle='0' WHERE nUserId='".$row['nUserId']."'") or die(mysqli_error($conn));
								}//end else

//                                                                $redirectPath = base64_decode($response_array['sessionAfterLoginRedirect']);
//
//								//checking any redirect exist or not
//								if (isset($response_array['sessionAfterLoginRedirect']) && $response_array['sessionAfterLoginRedirect']!='')
//								{
//									unset($_SESSION['sessionAfterLoginRedirect']);
//									header("location:".$redirectPath);
//									exit();
//								}//end if
//								else
//								{
//                                                                        unset($_SESSION['sessionAfterLoginRedirect']);
//									header("location:usermain.php");
//									exit();
//								}//end else	
							}//end if	
							else
							{
								$message = "<br>".str_replace('{link}',"<a href=\"mailto:".SITE_EMAIL."\">".SITE_EMAIL."</a>",MESSAGE_SORRY_NO_ACCESS_NOW);
							}//end else		  
					  }//end else
				  }//end if
				  else
				  {
					  if($row["vStatus"] == "0")
					  {
							$response_array["userid"] = $row["nUserId"];
							$response_array["useremail"] = $row["vEmail"];
							if (trim($row["vFirstName"])=='')$response_array["userFName"] = $user;
							else $response_array["userFName"] = $row["vFirstName"];
                                                        //$response_array["guserFName"] = $row["vFirstName"];
							$response_array["loginname"] = $user;
							$response_array["phone"] = $row["vPhone"];
							$response_array["sess_PlanId"] = $row["nPlanId"];
							$response_array['sess_uName']=$row['vLoginName'];
							if($row['stripe_pub_key'] == "" || $row['stripe_secret_key'] == ""){
								$response_array['stripe_status']= "Invalid";
							}else{
								$response_array['stripe_status']= "Valid";
							}
							$now = time();
							$session_active_time = ini_get("session.gc_maxlifetime");
							$activeTill = time() + $session_active_time;
							
							$response_array['sess_activeTill']=$activeTill;
                                    
							/* User is loggin for the first time */
							mysqli_query($conn, "Update ".TABLEPREFIX."users set nLastLogin='".$now."',preferred_language='".$_SESSION["lang_id"]."' where nUserId='".$row['nUserId']."'") or
												die(mysqli_error($conn));
		
							if ($row['nLoggedOn']=='')
							{
								// No entry in the online table
								mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."online (nUserId,nLoggedOn,nActiveTill,nIdle,vVisible) VALUES
												 ('".$row['nUserId']."','".$now."','".$activeTill."','0','".$display."')") or die(mysqli_error($conn));
							}//end if
							else
							{
								mysqli_query($conn, "UPDATE ".TABLEPREFIX."online SET nLoggedOn='".$now."',nActiveTill='".$activeTill."',vVisible='".$display."',
													nIdle='0' WHERE nUserId='".$row['nUserId']."'") or die(mysqli_error($conn));
							}//end else
							
							//checking any redirect exist or not
//							if (isset($response_array['sessionAfterLoginRedirect']) && $response_array['sessionAfterLoginRedirect']!='')
//							{
//                                                               
//								$redirectPath = base64_decode($_SESSION['sessionAfterLoginRedirect']);
//								unset($response_array['sessionAfterLoginRedirect']);
//								header("location:".$redirectPath);
//								exit();
//							}//end if
//							else
//							{
//								header("location:usermain.php");
//								exit();
//							}//end else		
						}//end if
						else
					    {
						    $message = "<br>".str_replace('{link}',"<a href=\"mailto:".SITE_EMAIL."\">".SITE_EMAIL."</a>",MESSAGE_SORRY_NO_ACCESS_NOW);
					    }//end else		  
				  }//end else date expiry checking ending
                                  $auth_key=md5(uniqid(rand(), TRUE));
                                  save_device_and_authkey($response_array["userid"],$device_id,$device_type,$auth_key);
                                  $response_array["auth_key"]=$auth_key;
               }//end if
			   else
			   {
                	$message = INVALID_CREDENTIALS;
               }//end else
               
		  }//end if
		  else
	      {
				$message = INVALID_CREDENTIALS;
		  }//end else
               
//     }//end else if
//	   else if($type=="admin")
//	   {
//       	  
//			$adminuser="";
//			if(DisplayLookUp('adminname')!='')
//			{
//				$adminuser=DisplayLookUp('adminname');
//			}//end if
////echo $adminuser.'--'.$user;exit;
//          if($user==$adminuser)
//		  {
//                if(DisplayLookUp('2')!='')
//				{
//                    $adminpass=DisplayLookUp('2');
//                        if(md5($password)==$adminpass)
//						{
//                                $response_array["adminid"] = "SWAAPADMIN";
//                                $response_array["loginname"] = $user;
//                                save_device_and_authkey($response_array["adminid"],$device_id,$device_type,$auth_key);
////                                header("location:adminmain.php");
////                                exit();
//                        }//end if
//						else
//						{
//                                $message = ERROR_INVALID_USERNAME_PASSWORD;
//                        }//end else
//                }//end if
//				else
//				{
//                        $message = ERROR_INVALID_USERNAME_PASSWORD;
//                }//end else
//        }//end if
//		else
//		{
//                $message = ERROR_INVALID_USERNAME_PASSWORD;
//        }//end else
//     }//end esle if
$status=1;
     if($message!="")
	 {
          $status=0;
     }//end if
     
     return array('status'=>$status,'error'=>$message,'data'=>$response_array);
}


//New Function for Image Handling  

function resizeImage($sourcePath,$imageName,$imageType,$oldWidth,$oldHeight,$newImageWidth,$newImageHeight,$type){

    $originalsrc = $sourcePath.$imageName;
    $src         = $sourcePath.$type.$imageName;
    copy($originalsrc,$src);

    $jpeg_quality = 100;
    
    switch ($imageType) {
        case 'jpeg':
            $image = imagecreatefromjpeg($src);
            break;
        case 'jpg':
            $image = imagecreatefromjpeg($src);

        case 'png':
            $image = imagecreatefrompng($src);
            break;
        case 'gif':
            $image = imagecreatefromgif($src);
            break;
    }
            
    $aspect = $oldWidth / $oldHeight; 
    if($oldWidth>=$oldHeight) {
        $newWidth = $newImageWidth;
        $newHeight = $newWidth / $aspect;
        if($newHeight>$newImageHeight) {
            $newWidth = ($newImageHeight*$newImageWidth)/$newHeight;
            $newHeight = $newImageHeight;
        }
    }
    else {
        $newHeight = $newImageHeight;
        $newWidth  = $newHeight *  $aspect;
        if($newWidth>$newImageWidth) {
            $newHeight = ($newImageHeight*$newImageWidth)/$newWidth;
            $newWidth = $newImageWidth;
        }
    }
    
    $dst_r = ImageCreateTrueColor($newWidth, $newHeight ); 
    imagealphablending( $dst_r, false );
    imagesavealpha( $dst_r, true );
    imagecopyresampled($dst_r, $image, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);

    $destImage = $src;

    switch ($imageType) {
        case 'jpeg': {
                imagejpeg($dst_r,$destImage,$jpeg_quality);
                break;
            }
        case 'jpg': {
                imagejpeg($dst_r,$destImage,$jpeg_quality);
                break;
            }
        case 'png': {
                imagepng($dst_r,$destImage,9);
                break;
            }
        case 'gif': {
                imagegif($dst_r,$destImage);
                break;
            }
    }
    return $imageType;
}


//function boxResize($path, $sourceFileName, $destFileName,$destination_path, $img_width, $img_height,$method,$bgcolor='FFFFFF') {
//
//    include_once ('imagehandle.php');
//    $ObjImagehandle                         = new ImagehandleComponent();
//    $ObjImagehandle->source_path            = $path.$sourceFileName;
//    $ObjImagehandle->preserve_aspect_ratio  = true;
//    $ObjImagehandle->enlarge_smaller_images = true;
//    $ObjImagehandle->preserve_time          = true;
//    $ObjImagehandle->target_path = $destination_path.$destFileName;
//    $ObjImagehandle = $ObjImagehandle->resize($img_width, $img_height,$method,$bgcolor);
//}

    function image_save($action,$image_data,$image_destination="../pics",$image_prefix='')   
    {
         ini_set("memory_limit", "-1");
         set_time_limit(0);
         include "class.upload.php";
        if(isset($action)&& $action =='upload'){
                 $image_data = str_replace(' ', '+', $image_data);
                 $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image_data));
//                    $info = getimagesizefromstring($image_data);
//                    list($width, $height)=$info;
//
//                    if($width<=250 || $height<=250) {
//                        echo trim('filesizeError');
//                        exit;
//                    }
//                    else {
//                       $mime = $info['mime'];
//                        $imageType= explode("/",$mime);
                        $imageType='jpg';//$imageType[1];

                    $dir_dest = $image_destination;//"../pics";
                    $picbigname = ($image_prefix!='')?$image_prefix:(substr(md5(microtime()),rand(0,26),8));//'test_api_product';//$_FILES['product_image']['name'];

                    $final_image_big = "";
                    $extension="";
                    if ($picbigname != "") {
                        $extension = $imageType;//end(explode(".", $_FILES['product_image']['name']));
                        $final_image_big = $picbigname."_".time();//$picbigname.$_SESSION["guserid"]."_".time().$fileNameWithoutExtension;
                    }
                     $picbig_newname = $final_image_big.'.'.$extension;//$final_image_big
                     $image_path=$dir_dest.'/'.$picbig_newname;
                 if(file_put_contents($image_path, $image_data)){
                    if ($picbig_newname != "") {

                    	if($image_prefix != 'profile')
                    	{
	                        $files = $image_path;//$picbig_newname;//$_FILES['product_image'];
	                        $handle = new Upload($files);

		                    if($handle->uploaded) 
		                    {
		                        $handle->image_resize = false;
		                        $handle->image_ratio_y = true;
		                        $handle->file_new_name_body = 'small_'.$final_image_big;
		                        $handle->Process($dir_dest);
		                        $handle->file_new_name_body = 'medium_'.$final_image_big;
		                        $handle->Process($dir_dest);
		                        $handle->file_new_name_body = 'large_'.$final_image_big;
		                        $handle->Process($dir_dest);
		                    }
                		}
                		else if($image_prefix == 'profile')
                		{
                			resizeImg($image_path, 200 ,200, false,100, 0,"");
                		}
                }
           }
           return  $picbig_newname;
        }

    }

function resizeImg($imgPath, $maxWidth, $maxHeight, $directOutput = true, $quality = 90, $verbose, $imageType) {
    // get image size infos (0 width and 1 height,
    //     2 is (1 = GIF, 2 = JPG, 3 = PNG)

    $size = getimagesize($imgPath);

    // break and return false if failed to read image infos
    if (!$size) {
        if ($verbose && !$directOutput)
            echo "<br />".ERROR."<br />";//Not able to read image infos.
        return false;
    }

    // relation: width/height
    $relation = $size[0] / $size[1];
    // maximal size (if parameter == false, no resizing will be made)
    $maxSize = array($maxWidth ? $maxWidth : $size[0], $maxHeight ? $maxHeight : $size[1]);
    // declaring array for new size (initial value = original size)
    $newSize = $size;
    // width/height relation
    $relation = array($size[1] / $size[0], $size[0] / $size[1]);


    if (($newSize[0] > $maxWidth)) {
        $newSize[0] = $maxSize[0];
        $newSize[1] = $newSize[0] * $relation[0];
    }

    if (($newSize[1] > $maxHeight)) {
        $newSize[1] = $maxSize[1];
        $newSize[0] = $newSize[1] * $relation[1];
    }
	$newSize[0] = $maxSize[0];
	$newSize[1] = $maxSize[1];
    // create image

    switch ($size[2]) {
        case 1:
            if (function_exists("imagecreatefromgif")) {
                $originalImage = imagecreatefromgif($imgPath);
            } else {
                if ($verbose && !$directOutput)
                    echo "<br />".ERROR."<br />";//No GIF support in this php installation, sorry.
                return false;
            }
            break;
        case 2: $originalImage = imagecreatefromjpeg($imgPath);
            break;
        case 3: $originalImage = imagecreatefrompng($imgPath);
            break;
        default:
            if ($verbose && !$directOutput)
                echo "<br />".ERROR_INVALID_IMAGE."<br />";//No valid image type.
            return false;
    }


    // create new image

    $resizedImage = imagecreatetruecolor($newSize[0], $newSize[1]);

    //checking transparency start here
    if (($size[2] == 1) || ($size[2] == 3)) {
        $trnprt_indx = imagecolortransparent($originalImage);
        // If we have a specific transparent color
        if ($trnprt_indx >= 0) {
            // Get the original image's transparent color's RGB values
            $trnprt_color = @imagecolorsforindex($originalImage, $trnprt_indx);
            // Allocate the same color in the new image resource
            $trnprt_indx = @imagecolorallocate($resizedImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
            // Completely fill the background of the new image with allocated color.
            @imagefill($resizedImage, 0, 0, $trnprt_indx);
            // Set the background color for new image to transparent
            imagecolortransparent($resizedImage, $trnprt_indx);
        }//end if
        // Always make a transparent background color for PNGs that don't have one allocated already
        else if ($size[2] == 3) {
            // Turn off transparency blending (temporarily)
            imagealphablending($resizedImage, false);

            // Create a new transparent color for image
            $color = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);

            // Completely fill the background of the new image with allocated color.
            imagefill($resizedImage, 0, 0, $color);

            // Restore transparency blending
            imagesavealpha($resizedImage, true);
        }//end else if
    }//end if transparency check
    //checking transparency end here


    imagecopyresampled($resizedImage, $originalImage, 0, 0, 0, 0, $newSize[0], $newSize[1], $size[0], $size[1]);

    $rz = $imgPath;

    // output or save
    if ($directOutput) {
        imagejpeg($resizedImage);
    } else {

        $rz = preg_replace("/\.([a-zA-Z]{3,4})$/", "" . $imageType . ".jpg", $imgPath);
        imagejpeg($resizedImage, $rz, $quality);
    }
    // return true if successfull
    return $rz;
}
    
    
 function validate_values_delete($var_source, $var_swapid,$user_id) {
    global $conn;
    $ret_flag = true;
    $var_mesg = "";

    if ($var_source == "s" || $var_source == "w") {
        $sql = "Select nSwapId from " . TABLEPREFIX . "swap where nSwapId = '"
                . addslashes($var_swapid) . "'   AND
                     vDelStatus='0' AND nUserId='" . $user_id . "'";
        if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {
            $var_mesg =  "Sorry, this swap item has already been swapped or deleted <br>";
            $ret_flag = false;
        }//end if
    }//end if
    else if ($var_source == "sa") {
        $sql = "Select nSaleId from " . TABLEPREFIX . "sale where nSaleId = '"
                . addslashes($var_swapid) . "'   AND
                         vDelStatus='0' AND nUserId='" . $user_id . "'";
        if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {
            $var_mesg = "Sorry, this item has already been purchased or deleted <br>";
            $ret_flag = false;
        }//end if
    }//end else
    return array('result'=>$ret_flag,'message'=>$var_mesg);
}




function save_device_and_authkey($user_id,$device_id,$device_type,$auth_key)
{
    if($user_device_id=device_exists($user_id,$device_id,$device_type))
    {
        update_authkey($user_device_id,$auth_key);
    }
    else
    {
        add_device_and_authkey($user_id,$device_id,$device_type,$auth_key);
    }
}

function payment_options()
{
    return array('paypal');
}
function paypal_details()
{
    $txtPaypalEmail = DisplayLookUp('paypalemail');
     $txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
    $txtPaypalSandbox = DisplayLookUp('paypalmode');
    $paypalenabled = DisplayLookUp('paypalsupport');

        if (DisplayLookUp('plan_system')!='yes') {
            if ($txtPaypalSandbox == "TEST") {
                $paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
                $paypalbuttonurl = "https://www.sandbox.paypal.com/en_US/i/btn/x-click-but23.gif";
            }//endi f
            else {
                $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
                $paypalbuttonurl = "https://www.paypal.com/en_US/i/btn/x-click-but23.gif";
            }//end else
        }//end if
        else {
            if ($txtPaypalSandbox == "TEST") {
                $paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
                $paypalbuttonurl = "https://www.sandbox.paypal.com/en_US/i/btn/x-click-but20.gif";
            }//endi f
            else {
                $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
                $paypalbuttonurl = "https://www.paypal.com/en_US/i/btn/x-click-but20.gif";
            }//end else
        }//end else
    return array('paypal_email'=>$txtPaypalEmail,'paypal_auth_token'=>$txtPaypalAuthtoken,'paypal_mode'=>$txtPaypalSandbox,'paypal_enabled'=>$paypalenabled,'paypal_url'=>$paypalurl,'paypal_button_url'=>$paypalbuttonurl);
}

function paypal_success($tx_token)
{
    $paypal_details=paypal_details();
    $req = 'cmd=_notify-synch';
//    $tx_token = $_GET['tx'];
    $auth_token = $paypal_details['paypal_auth_token'];//$txtPaypalAuthtoken;
    $req .= "&tx=$tx_token&at=$auth_token";
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$paypal_details['paypal_url']);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$req);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,false);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = explode(PHP_EOL, $result);
    $keyarray = array();
    foreach($result as $res) {
        list($key, $val) = explode("=", $res);
        $keyarray[urldecode($key)] = urldecode($val);
    }
    if($result[0] == 'SUCCESS'){
        $txnid = $keyarray['txn_id'];
        if ($paypal_details['paypal_mode'] == "TEST") {
            $txnid = $tx_token;
        }
    return array('txn_id'=>$txnid,'option_selection2'=>$keyarray['option_selection2']);
    }
    return false;
}

function device_exists($user_id,$device_id,$device_type)
{
    global $conn;
    $sql = "SELECT user_device_id  FROM ".TABLEPREFIX."user_devices WHERE
				            device_id = '$device_id' AND device_type = '".$device_type."' and user_id ='".$user_id."'";
                               

               $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
               if(mysqli_num_rows($result) >0)
               {
                   $row = mysqli_fetch_array($result);
                   return $row['user_device_id'];
               }
               return false;
    
}


function add_device_and_authkey($user_id,$device_id,$device_type,$auth_key)
{
    global $conn;
    $date_added=date('m-d-Y');
    mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."user_devices (user_id,device_id,device_type,auth_key,date_added,date_updated) VALUES
												 ('".$user_id."','".$device_id."','".$device_type."','".$auth_key."','".$date_added."','".$date_added."')") or die(mysqli_error($conn));
}

function update_authkey($user_device_id,$auth_key)
{
    global $conn;
    mysqli_query($conn, "UPDATE ".TABLEPREFIX."user_devices SET auth_key='".$auth_key."' WHERE user_device_id='".$user_device_id."'") or die(mysqli_error($conn));
							
}


function DisplayLookUp($name) {
    global $conn;
    $sql = mysqli_query($conn, "select vLookUpDesc from " . TABLEPREFIX . "lookup where nLookUpCode='" . addslashes($name) . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($sql) > 0) {
        return (mysqli_result($sql, 0, 'vLookUpDesc'));
    }//end if
    
}function fetchSingleValue($ResourceArray, $Field) {
    if (mysqli_num_rows($ResourceArray) > 0) {
        return (mysqli_result($ResourceArray, 0, $Field));
    }//end if
}

function mysqli_result($res, $row, $field=0) { 
    $res->data_seek($row); 
    $datarow = $res->fetch_array(); 
    return $datarow[$field]; 
} 

function isValidUsername($str) {
    if (preg_match("/[^0-9a-zA-Z+_]/", $str)) {
        return false;
    } else {
        return true;
    }
}

function select_rows($table, $fieldlist, $condition) {
    $sql = "select $fieldlist from $table $condition ";
    $resArray = QueryResult($sql, 'Select');
    return ($resArray);
}

function QueryResult($sql, $PVal) {
    global $conn;
    if ($sql != NULL) {
        $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        return $rs;
    }//end if
    else {
        return '<b>MySQL Error</b>: Empty Query!';
    }//end else
}

function send_mail($email_to, $subject, $message, $from_email, $from_name='') {
    require_once '../PHPMailer/src/Exception.php';
    require_once '../PHPMailer/src/PHPMailer.php';
    require_once '../PHPMailer/src/SMTP.php';
    if ($from_name == '') {
        $from_name = 'Admin';
    }//end if

    $mail = new PHPMailer\PHPMailer\PHPMailer(false);
    $encryptionMethod = DisplayLookUp('encryption');


    //Server settings
    $mail->SMTPDebug = 0;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = DisplayLookUp('smtp_host');                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = DisplayLookUp('smtp_email');                     // SMTP username
    $mail->Password   = openssl_decrypt(DisplayLookUp('smtp_password'), $encryptionMethod, DisplayLookUp('secret_hash'));     // SMTP password
    $mail->SMTPSecure =  DisplayLookUp('smtp_protocol');        // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = DisplayLookUp('smtp_port');             // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    //Recipients
    $mail->setFrom($from_email, $from_name);
    $mail->addAddress($email_to);               // Name is optional
    $mail->addReplyTo($from_email, $from_name);
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    // Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $message;
    

    @$mail->send();

}

function MailStyle($sName, $Url) {
    $Url = '.';
    if (!file_exists($Url . '/themes/' . $sName)) $Url = '..';
    
    $contents = file_get_contents($Url . '/themes/' . $sName);
    $contents = str_replace('url(', 'url(' . $Url . '/' . $sName . '/', $contents);
    return ($contents);
}

    function getUserEmail($user_id){
        global $conn;
         $sql = "SELECT vEmail FROM " . TABLEPREFIX . "users where nUserId = '" . $user_id . "'";
         $result =mysqli_query($conn, $sql);
         $row = mysqli_fetch_array($result);
         return $row['vEmail'];
       }
    
       
         
        function get_login_user_name($user_id)
        {
         global $conn;
         $sql = "SELECT vLoginName FROM " . TABLEPREFIX . "users where nUserId = '" . $user_id . "'";
         $result =mysqli_query($conn, $sql);
         $row = mysqli_fetch_array($result);
         return $row['vLoginName'];
        }
        
    
        
        function get_stripe_secret_key($user_id)
        {
         global $conn;
         $sql = "SELECT stripe_secret_key FROM " . TABLEPREFIX . "users where nUserId = '" . $user_id . "'";
         $result =mysqli_query($conn, $sql);
         $row = mysqli_fetch_array($result);
         return $row['stripe_secret_key'];
        }
        
        function get_stripe_public_key($user_id)
        {
         global $conn;
         $sql = "SELECT stripe_pub_key FROM " . TABLEPREFIX . "users where nUserId = '" . $user_id . "'";
         $result =mysqli_query($conn, $sql);
         $row = mysqli_fetch_array($result);
         return $row['stripe_pub_key'];
        }
        
    
     
         
//         global $conn;
         function swapIsValid($i) {
        global $conn;
        $var_ret = false;
        $sql = "Select nSwapId,nUserId from " . TABLEPREFIX . "swaptxn where nSwapId = '"
                . addslashes($i) . "' AND nUserId='" . $_SESSION["guserid"] . "'
                        AND vStatus NOT IN('A','N') ";
        if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {
            //if the resultset is empty.
            $var_ret = false;
        }//end if
        else {
            $var_ret = true;
        }//end else
        return $var_ret;
}


function allExists($a) {
    global $conn;
    $var_ret = false;
    if ($a != "") {
        $ch_arr = explode(",", $a);
        $sql = "Select nSwapId from " . TABLEPREFIX . "swap where vSwapStatus='0'
                                 AND vDelStatus='0' AND nSwapId in($a)";

        if (mysqli_num_rows(mysqli_query($conn, $sql)) != count($ch_arr)) {
            $var_ret = false;
        }//end if
        else {
            $var_ret = true;
        }//end else
    }//end if
    else {
        $var_ret = true;
    }//end else
    return $var_ret;
     }
     
     
     
     
//     function CurlMePost($url,$post){ 
//	// $post is a URL encoded string of variable-value pairs separated by &
//	$ch = curl_init();
//	curl_setopt ($ch, CURLOPT_URL, $url);
//	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
//	curl_setopt ($ch, CURLOPT_POST, 1);
//	curl_setopt ($ch, CURLOPT_POSTFIELDS, $post); 
//	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
//	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 3); // 3 seconds to connect
//	curl_setopt ($ch, CURLOPT_TIMEOUT, 10); // 10 seconds to complete
//	$output = curl_exec($ch);
//	curl_close($ch);
//	return $output;
//}