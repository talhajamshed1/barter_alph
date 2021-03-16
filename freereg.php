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
include "./includes/config.php";
include "./includes/session.php";
include "./includes/functions.php";
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include "./includes/logincheck.php";

include_once('./includes/gpc_map.php');

//populate data
$id = $_GET["id"];

$approval_tag = "0";

if (DisplayLookUp('userapproval') != '') {
    $approval_tag = DisplayLookUp('userapproval');
}//end if
//clear sessions
$_SESSION['nPlanId'] = '';
$_SESSION['sess_Plan_Mode'] = '';

$sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
$sql .="vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
$sql .="vDescription  ,date_format(dDateReg,'%m/%d/%Y') as 'dDateReg'   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee
					from " . TABLEPREFIX . "users where nUserId='$id'";

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

if (mysqli_num_rows($result) > 0) {
    if ($row = mysqli_fetch_array($result)) {
        $flag = true;
        $var_login_name = $row["vLoginName"];
        $var_first_name = $row["vFirstName"];
        $var_last_name = $row["vLastName"];
        $var_email = $row["vEmail"];
        $totalamt = $row["nAmount"];
        $paytype = $row["vMethod"];
        $now = date('m/d/Y', strtotime($row["dDateReg"]));

        //admin approval
        if ($approval_tag == "1") {
            $message = str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT_AFTER_ADMIN_APPROVAL);
        }//end if
        if ($approval_tag == "E") {
            $message = str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT_AFTER_EMAIL_VERIFICATION);
        }//end if
        if ($approval_tag == "0") {
            $message = str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT_NOW) . "<br>&nbsp;<br><a href='login.php'>".LINK_CLICK_LOGIN."</a>";
            $_SESSION["rurl"] = base64_encode("usermain.php");
        }//end if
    }//end if
}//end if
else {
    $message = str_replace("{site_url}",SITE_URL,MESSAGE_LOGIN_AT);
}//end else

include_once('./includes/title.php');
?>

<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
    <div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
                <div class="col-lg-9">
                    <div class="full_width">
                    	<div class="col-lg-12 ">
                            <div class="new-reg-form">
                        	<div class=" main_form_inner ">
                            	
                                <h4><?php echo HEADING_REGISTRATION_FORM; ?></h4>
                              </div>
                                <div class=" main_form_inner">
                                <?php    
                                    if ($flag == false) {
                                        if (isset($message) && $message != '') {
                                            ?>
                                           <div class="notification_msg">
                                            <span class="glyphicon glyphicon-exclamation-sign"></span><?php echo $message;?>
                                        </div>

                                        <?php
                                        }//end if
                                    }//end if
                                    else {
                                        ?>
                                                <h5><?php echo HEADING_REGISTRATION_INFO; ?></h5>
                              
                                                                <label><?php echo TEXT_LOGIN_NAME; ?> : &nbsp; <b><?php echo  $var_login_name; ?></b> </label>
                                                                <?php if($var_first_name!='') { ?><label><?php echo TEXT_FIRST_NAME; ?> : &nbsp; <b><?php echo  $var_first_name ;?></b> </label> <?php } ?>

                                                                <label><?php echo TEXT_REGISTRATION_DATE; ?> : &nbsp; <b><?php echo  $now ;?> </b></label>
                                                                <br>
                                                                <label class="msg-lbl"><?php echo $message;?></label>
                                                               
                                    <?php } ?>
                                </div>                            
                            </div>
                        </div>
                    </div>
                	<div class="subbanner">
					 <?php include('./includes/sub_banners.php'); ?>
                    </div>
                </div>
            </div>  
        </div>
 </div> 
<?php require_once("./includes/footer.php"); ?>