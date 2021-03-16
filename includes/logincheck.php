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
include ("session.php");
function isValidLogin($type,$user,$password,$display)
{
	global $conn;
    //clear sessions if set
     /*if(isset($_SESSION["gadminid"]))
	 {
        $_SESSION["gadminid"] = "";
        session_unregister("gaffid");
        session_unset();
     }//end if

     if (isset($_SESSION["gaffid"]))
	 {
        $_SESSION["gaffid"] = "";
        session_unregister("gaffid");
        session_unset();
     }//end if

     if(isset($_SESSION["guserid"]))
	 {
        $_SESSION["guserid"] = "";
        session_unregister("guserid");
        session_unset();
     }//end if*/


     if($type=="affiliate"){
		  $sql_aff=mysqli_query($conn, "SELECT nAffiliateId,vFirstName, vLastName   FROM ".TABLEPREFIX."affiliate
									WHERE vLoginName = '$user' and vDelStatus != '1'") or die(mysqli_error($conn));
		  if(mysqli_num_rows($sql_aff) > 0)
		  {
			  $sql = "SELECT nAffiliateId,vFirstName, vLastName   FROM ".TABLEPREFIX."affiliate";
                       $sql .= " WHERE vLoginName = '$user' AND vPassword = '".md5($password)."' and vDelStatus != '1'";
                       $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                       if(mysqli_num_rows($result) > 0)
			   {
                       $row = mysqli_fetch_array($result);
                       $_SESSION["gaffid"] = $row["nAffiliateId"];
                       $_SESSION["gloginname"] = $user;
                       $name = $row["vFirstName"];
                       if($row["vLastName"]!="")
                       {
                               $name .= " ". $row["vLastName"];
                       }//end if
                       $_SESSION["gaffname"] = $name;
                        header("location:affiliatemain.php");
                       exit();
               }//endif
			   else
			   {
                       $message = ERROR_INVALID_USERNAME_PASSWORD;
               }//end else
		  }//end if
          else
          {
				$message = ERROR_INVALID_USERNAME_PASSWORD;
	      }//end else
     }else if($type=="user"){
            $sql_use = mysqli_query($conn, "SELECT u.nUserId, u.vEmail,u.vStatus,u.vPhone,u.vFirstName,n.nLoggedOn  FROM ".TABLEPREFIX."users u 
                            LEFT JOIN ".TABLEPREFIX."online n ON (u.nUserId = n.nUserId) WHERE
                            u.vLoginName = '$user' and u.vDelStatus != '1'") or die(mysqli_error($conn));

            if(mysqli_num_rows($sql_use) > 0){
                $sql = "SELECT u.nUserId, u.vEmail,u.vStatus,u.vPhone,u.vFirstName,n.nLoggedOn,u.nPlanId  FROM ".TABLEPREFIX."users u 
                LEFT JOIN ".TABLEPREFIX."online n ON (u.nUserId = n.nUserId) WHERE
                u.vLoginName = '$user' AND u.vPassword = '".md5($password)."' and u.vDelStatus != '1'";

                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if(mysqli_num_rows($result) >0){
                    $row = mysqli_fetch_array($result);

                //checking date of expiry
                $sqlChkExp=mysqli_query($conn, "SELECT u.dPlanExpDate < now() as expired FROM ".TABLEPREFIX."users u LEFT JOIN
                     ".TABLEPREFIX."plan p ON u.nPlanId=p.nPlanId WHERE u.nUserId='".$row["nUserId"]."'
                     AND p.vPeriods!='F' and u.dPlanExpDate!='0000-00-00'") or die(mysqli_error($conn));

                if(mysqli_num_rows($sqlChkExp)>0){
                    if(mysqli_result($sqlChkExp,0,'expired')=='1')
                    {
                    $_SESSION["guserid"] = $row["nUserId"];
                    $_SESSION["guseremail"] = $row["vEmail"];
                    if (trim($row["vFirstName"])=='')$_SESSION["guserFName"] = $user;
                    else $_SESSION["guserFName"] = $row["vFirstName"];
                    $_SESSION["gloginname"] = $user;
                    $_SESSION["gphone"] = $row["vPhone"];
                    $_SESSION["sess_PlanId"] = $row["nPlanId"];
                    $_SESSION['sess_uName']=$row['vLoginName'];

                    $now = time();
                    $session_active_time = ini_get("session.gc_maxlifetime");
                    $activeTill = time() + $session_active_time;

                    $_SESSION['sess_activeTill']=$activeTill;

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
                    $_SESSION['sess_upgradeplan']="PLEASEUPGRADE";
                    $_SESSION['sess_upgradeplan_message']=MESSAGE_CURRENT_PLAN_EXPIRED;
                    echo "<script>alert(\"".MESSAGE_CURRENT_PLAN_EXPIRED."\");</script>";
                    echo "<script>location.href='change_plan.php'</script>";
                    exit();
                    }//end if
                    else
                    {
                    if($row["vStatus"] == "0")
                    {
                    $_SESSION["guserid"] = $row["nUserId"];
                    $_SESSION["guseremail"] = $row["vEmail"];
                    if (trim($row["vFirstName"])=='')$_SESSION["guserFName"] = $user;
                    else $_SESSION["guserFName"] = $row["vFirstName"];
                    $_SESSION["gloginname"] = $user;
                    $_SESSION["gphone"] = $row["vPhone"];
                    $_SESSION["sess_PlanId"] = $row["nPlanId"];
                    $_SESSION['sess_uName']=$row['vLoginName'];

                    $now = time();
                    $session_active_time = ini_get("session.gc_maxlifetime");
                    $activeTill = time() + $session_active_time;

                    $_SESSION['sess_activeTill']=$activeTill;

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

                    $redirectPath = base64_decode($_SESSION['sessionAfterLoginRedirect']);

                    //checking any redirect exist or not
                    if (isset($_SESSION['sessionAfterLoginRedirect']) && $_SESSION['sessionAfterLoginRedirect']!='')
                    {
                    unset($_SESSION['sessionAfterLoginRedirect']);
                    header("location:".$redirectPath);
                    exit();
                    }//end if
                    else
                    {
                    unset($_SESSION['sessionAfterLoginRedirect']);
                    header("location:usermain.php");
                    exit();
                    }//end else	
                    }//end if	
                    else
                    {
                    $message = "<br>".str_replace('{link}',"<a href=\"mailto:".SITE_EMAIL."\">".SITE_EMAIL."</a>",MESSAGE_SORRY_NO_ACCESS_NOW);
                    }//end else		  
                    }//end else
                }
                else{
                    if($row["vStatus"] == "0"){ //If active user
                        $_SESSION["guserid"]        = $row["nUserId"];
                        $_SESSION["guseremail"]     = $row["vEmail"];
                        if (trim($row["vFirstName"]) == '')
                            $_SESSION["guserFName"] = $user;
                        else 
                            $_SESSION["guserFName"] = $row["vFirstName"];
                        //$_SESSION["guserFName"] = $row["vFirstName"];
                        $_SESSION["gloginname"]     = $user;
                        $_SESSION["gphone"]         = $row["vPhone"];
                        $_SESSION["sess_PlanId"]    = $row["nPlanId"];
                        $_SESSION['sess_uName']     = $row['vLoginName'];

                        $now = time();
                        $session_active_time    = ini_get("session.gc_maxlifetime");
                        $activeTill             = time() + $session_active_time;
                        $_SESSION['sess_activeTill']=$activeTill;

                        /* User is loggin for the first time */
                        mysqli_query($conn, "Update ".TABLEPREFIX."users set nLastLogin='".$now."',preferred_language='".$_SESSION["lang_id"]."' where nUserId='".$row['nUserId']."'") or die(mysqli_error($conn));

                        if ($row['nLoggedOn'] == '')
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
                        if (isset($_SESSION['sessionAfterLoginRedirect']) && $_SESSION['sessionAfterLoginRedirect']!='')
                        {
                            $redirectPath = base64_decode($_SESSION['sessionAfterLoginRedirect']);
                            unset($_SESSION['sessionAfterLoginRedirect']);
                            header("location:".$redirectPath);
                            exit();
                        }//end if
                        else
                        {
                            header("location:usermain.php");
                            exit();
                        }//end else		
                }//end if
                else{
                    $message = "<br>".str_replace('{link}',"<a href=\"mailto:".SITE_EMAIL."\">".SITE_EMAIL."</a>",MESSAGE_SORRY_NO_ACCESS_NOW);
                }//end else		  
                }//end else date expiry checking ending
                }//end if
                else
                {
                    $message = ERROR_INVALID_USERNAME_PASSWORD;
                }//end else
            }//end if
            else
            {
                $message = ERROR_INVALID_USERNAME_PASSWORD;
            }//end else
            }
            else if($type == "admin"){       	   
			$adminuser="";
			if(DisplayLookUp('adminname')!='')
			{
				$adminuser=DisplayLookUp('adminname');
			}//end if

          if($user==$adminuser)
		  {
                if(DisplayLookUp('2')!='')
				{
                    $adminpass=DisplayLookUp('2');
                        if(md5($password)==$adminpass)
						{
                                $_SESSION["gadminid"] = "SWAAPADMIN";
                                $_SESSION["gloginname"] = $user;
                                header("location:adminmain.php");
                                exit();
                        }//end if
						else
						{
                                $message = ERROR_INVALID_USERNAME_PASSWORD;
                        }//end else
                }//end if
				else
				{
                        $message = ERROR_INVALID_USERNAME_PASSWORD;
                }//end else
        }//end if
		else
		{
                $message = ERROR_INVALID_USERNAME_PASSWORD;
        }//end else
     }//end esle if

     if($message!="")
	 {
          return $message;
     }//end if
}//end if

function isValidLoginwithoutPassword($user,$password,$display)
{
	global $conn;
    //clear sessions if set
     /*if(isset($_SESSION["gadminid"]))
	 {
        $_SESSION["gadminid"] = "";
        session_unregister("gaffid");
        session_unset();
     }//end if

     if (isset($_SESSION["gaffid"]))
	 {
        $_SESSION["gaffid"] = "";
        session_unregister("gaffid");
        session_unset();
     }//end if

     if(isset($_SESSION["guserid"]))
	 {
        $_SESSION["guserid"] = "";
        session_unregister("guserid");
        session_unset();
     }//end if*/


     
	 
	  
       	   
			$adminuser="";
			if(DisplayLookUp('adminname')!='')
			{
				$adminuser=DisplayLookUp('adminname');
			}//end if

          if($user==$adminuser)
		  {
                if(DisplayLookUp('2')!='')
				{
                    $adminpass=DisplayLookUp('2');
                        if(md5($password)==$adminpass)
						{
                                $_SESSION["gadminid"] = "SWAAPADMIN";
                                $_SESSION["gloginname"] = $user;
                                header("location:adminmain.php");
                                exit();
                        }//end if
						else
						{
                                $message = ERROR_INVALID_USERNAME_PASSWORD;
                        }//end else
                }//end if
				else
				{
                        $message = ERROR_INVALID_USERNAME_PASSWORD;
                }//end else
        }//end if
		else
		{
                $message = ERROR_INVALID_USERNAME_PASSWORD;
        }//end else
     

     if($message!="")
	 {
          return $message;
     }//end if
}//end if
?>