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
include "./includes/config.php";
include "./includes/session.php";
include "./includes/functions.php";
include "./includes/logincheck.php";

$loginmessages = "";
$flag1 = false;
//user session check
if (isset($_SESSION["guserid"]) || ($_SESSION["guserid"]!='')) {
    
    if(isset($_REQUEST['redirecturi']))
    {
       $rurl = base64_decode($_REQUEST['redirecturi']);
       header('location:'.$rurl);
    }
    else
    {
     header('location:usermain.php');
    }
    exit();
}//end if

$redirecturi = trim($_REQUEST['redirecturi']);

if ($redirecturi != '' && !$_SESSION["rurl"]) {
    $_SESSION['sessionAfterLoginRedirect'] = $redirecturi;
}
if($_SESSION["rurl"]){
    $_SESSION['sessionAfterLoginRedirect'] = $_SESSION["rurl"];
}

if (!isset($_POST)){
    if($_SESSION['sessionAfterLoginRedirect']!='' && (basename($_SERVER["SCRIPT_NAME"])=="register.php" || basename($_SERVER["SCRIPT_FILENAME"])=="login.php") ){
        if($redirecturi == ''){
        $_SESSION['sessionAfterLoginRedirect'] = base64_encode("usermain.php");
        }
    }
}

if ($_POST["btnLogin"] != "") {
    $txtUserName = trim($_POST["txtUserName"]);
    $txtPassword = addslashes(trim($_POST["txtPassword"]));
    $txtUserName = addslashes($txtUserName);
    $chkInvisible = $_POST['chkInvisible'];

    switch ($chkInvisible) {
        case "Y":
            $visible = 'N';
            break;

        case "":
            $visible = 'Y';
            break;
    }//end switch

    $loginmessages = isValidLogin('user', $txtUserName, $txtPassword, $visible);
}//end if

if (isset($_GET['status']) && $_GET['status'] == 'activate') {
    $loginmessages = MESSAGE_ACC_ACTIVATION_SUCCESSFULL;
    $flag1 = true;
}//end if

include_once('./includes/gpc_map.php');
$message = "";
include_once('./includes/title.php');

if(!$loginmessages && $_GET["msg"]){
    $loginmessages = $_GET["msg"];
}

?>
<body>
<?php include_once('./includes/top_header.php'); ?>
	<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">          	
                <div >									
					<div class="login-form-page">
                        <div class="col-sm-12"></div>
                        <div class="col-sm-12">
						<div class="login-form-page-inner">
                            <h4><?php echo HEADING_LOGIN_HERE; ?></h4>
						<form name="frmLogin" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" onSubmit="return validateLoginForm();">

                                                                <?php
								if (isset($loginmessages) && $loginmessages != '') {
                                                                 if($flag1 == false){   
									?>
									<div class="row warning"><?php echo $loginmessages; ?></div>
								<?php } else if($flag1 == true){?>
                                                                <div class="row success"><?php echo $loginmessages; ?></div>           
                                                                <?php }}?>
                                                                <br>	
                                                                
							<input type="hidden" name="source" value="<?php echo  $source ?>">
							<input type="hidden" name="saleid" value="<?php echo  $saleid ?>">
							<div class=" main_form_inner">
								<label><?php echo TEXT_USERNAME; ?></label>
								<input name="txtUserName" class="form-control" size="30">
							</div>
							<div class=" main_form_inner">
								<label><?php echo TEXT_PASSWORD; ?></label>
								<input name="txtPassword" type="password"  class="form-control"  size="30">
							</div>						
															
															
							<div class=" main_form_inner">
								<div class="col-lg-4 col-sm-12 col-md-12 col-xs-12 no_padding marg_B_five">
									<input type="checkbox" name="chkInvisible" value="Y" tabindex="3"> <?php echo TEXT_INVISIBLE_MODE; ?>
								</div>
								<div class="col-lg-8 col-sm-12 col-md-12 col-xs-12 no_padding" align="right">
									
									<label><a href="forgotpass.php"><?php echo LINK_FORGOT_PASSWORD; ?>?</a></label>
								</div>																
							</div>
							<div class=" main_form_inner">
								<label>
									<!--<input type="reset" name="btnReset" value="<?php echo BUTTON_RESET; ?>"  class="submit">&nbsp;&nbsp;&nbsp;-->
									<input type="submit" name="btnLogin" value="<?php echo BUTTON_LOGIN; ?>" class="subm_btt">
								</label>
							</div>
                            <label class="new-usr-label"><a href="./register.php"><?php echo LINK_NEW_USER_SIGNUP_HERE; ?></a></label>
						</form>
						</div>	
                        </div>			
					</div>
                	<div class="row">
					 <?php include('./includes/sub_banners.php'); ?>
                    </div>
                </div>
            	<div class="col-lg-3"></div>
            </div>  
        </div>
 </div> 

    
<?php require_once("./includes/footer.php"); ?>