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
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

$sqluserdetails = "SELECT * FROM " . TABLEPREFIX . "users  WHERE  nUserId  = '" . $_GET['uid'] . "'";
$resultuserdetails = @mysqli_query($conn, $sqluserdetails) or die(mysqli_error($conn));
$rowuser = @ mysqli_fetch_array($resultuserdetails);

$vLoginName = $rowuser["vLoginName"];
$vFirstName = $rowuser["vFirstName"];
$vLastName = $rowuser["vLastName"];
$vAddress1 = $rowuser["vAddress1"];
$vAddress2 = $rowuser["vAddress2"];
$vCity = $rowuser["vCity"];
$vState = $rowuser["vState"];
$nZip = $rowuser["nZip"];
$vPhone = $rowuser["vPhone"];
$vFax = $rowuser["vFax"];
$vEmail = $rowuser["vEmail"];
$vCountry = $rowuser["vCountry"];
$vGender = $rowuser["vGender"];
$vUrl = $rowuser["vUrl"];
$vEducation = $rowuser["vEducation"];
$vDescription = $rowuser["vDescription"];
$profile_image = $rowuser["profile_image"];
$display_image = $rowuser["vIMStatus"];



//checking feedback status
$cndSatisfied = "where nUserId='" . $_GET['uid'] . "' AND vStatus='S'";
$userSatisfied = fetchSingleValue(select_rows(TABLEPREFIX . 'userfeedback', 'count(vStatus) as satisfied', $cndSatisfied), 'satisfied');

$cndDisatisfied = "where nUserId='" . $_GET['uid'] . "' AND vStatus='D'";
$userDisatisfied = fetchSingleValue(select_rows(TABLEPREFIX . 'userfeedback', 'count(vStatus) as dissatisfied', $cndDisatisfied), 'dissatisfied');

$cndNeutral = "where nUserId='" . $_GET['uid'] . "' AND vStatus='N'";
$userNetural = fetchSingleValue(select_rows(TABLEPREFIX . 'userfeedback', 'count(vStatus) as neutral', $cndNeutral), 'neutral');
 if ($profile_image=='' || !file_exists('./pics/profile/'.$profile_image) || $display_image!='Y'){
   
    $profile_image = '';
    
}
else 
    $profile_image = './pics/profile/'.$profile_image;
    
    
include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); 
    
   
    //echo $profile_image;
    if($_GET['uid'] == $_SESSION["guserid"] && $rowuser["profile_image"])          
    {
        
        $profile_image = './pics/profile/'.$rowuser["profile_image"];
    }
   
    ?>
	
    
    
    
	<div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php if(isset($_GET['uid']) && $_GET['uid']==$_SESSION["guserid"]) { include_once ("./includes/usermenu.php"); } else { include_once ("./includes/categorymain.php"); } ?></div>
                <div class="col-lg-9">					
					<div class="innersubheader ">
                    	<h4 style="float:left; width:auto;"><?php echo str_replace('{user_name}',$vLoginName,HEADING_PROFILE_OF); ?></h4>
						<a href="javascript:history.go(-1);" class="back-btn" ><?php echo LINK_BACK; ?></a> 
                    </div>
					
					<div class="row">
						<div class="user_profile_main_outer ">
							
							<!-- <div class="col-lg-3 col-sm-12 col-md-6 col-xs-12">
								<div class="row">
									<?php if ($profile_image!='') { ?>
									<img src="<?php echo $profile_image; ?>" alt="" class="user_prof_img" />
									<?php } ?>
								</div>
							</div> -->
							<div class="col-lg-12">
								<div class="profile-section">
									<div class="profile-section-image">
										<?php if ($profile_image!='') { ?>
										<img src="<?php echo $profile_image; ?>" alt="" class="user_prof_img" />
										<?php }else { ?>
											<img src="<?php echo SITE_URL?>/images/default-user-image.png" alt="user image">
										<?php } ?>								
									</div>

								<div class="profile-section-inner  clearfix">
									<h4><?php echo HEADING_CONTACT_DETAILS; ?></h4>
									<div class="row">
										<div class="clearfix">
										<div class="col-lg-6 col-xs-6 custom-text-div">
											<label><?php echo TEXT_FIRST_NAME; ?></label>
											<div><?php echo $vFirstName; ?></div>
										</div>
										
										<div class="col-lg-6 col-xs-6 custom-text-div">
											<label><?php echo TEXT_LAST_NAME; ?></label>
											<div><?php echo $vLastName; ?></div>
										</div>
									</div>
									<div class="clearfix">
									<div class="col-lg-6 col-xs-6 custom-text-div">
										<label><?php echo TEXT_ADDRESS ?></label>
										<div><?php echo $vAddress1 . $vAddress2; ?></div>
									</div>
									<div class="col-lg-6 col-xs-6 custom-text-div">
										<label><?php echo TEXT_CITY; ?></label>
										<div><?php echo $vCity; ?></div>
									</div>
								</div>
								<div class="clearfix">
									<div class="col-lg-6 col-xs-6 custom-text-div">
										<label><?php echo TEXT_STATE; ?></label>
										<div><?php echo $vState; ?></div>
									</div>
									<div class="col-lg-6 col-xs-6 custom-text-div">
										<label><?php echo TEXT_ZIP; ?></label>
										<div><?php echo $nZip; ?></div>
									</div>
								</div>
								<div class="clearfix">
									<div class="col-lg-6 col-xs-6 custom-text-div">
										<label><?php echo TEXT_FAX; ?></label>
										<div><?php echo $vFax; ?></div>
									</div>
									<div class="col-lg-6 col-xs-6 custom-text-div">
										<label><?php echo TEXT_COUNTRY; ?></label>
										<div ><?php echo $vCountry; ?></div>
									</div>
								</div>
								</div>
							</div>
							</div>
							</div>
							<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
								<div class="profile-section-bottom clearfix">
									<h4><?php echo HEADING_FEEDBACK_DETAILS; ?></h4>
									<div class="col-lg-6 col-xs-6 custom-text-div">
										<label><?php echo TEXT_NO_SATISFIED_CUSTOMERS; ?></label>
										<div ><?php echo $userSatisfied; ?></div>
									</div>
									<div class="col-lg-6 col-xs-6 custom-text-div">
										<label><?php echo TEXT_NO_DISSATISFIED_CUSTOMERS; ?></label>
										<div><?php echo $userDisatisfied; ?></div>
									</div>
									<div class="col-lg-6 col-xs-6 custom-text-div">
										<label><?php echo TEXT_NO_NEUTRAL_CUSTOMERS; ?></label>
										<div><?php echo $userNetural; ?></div>
									</div>
								</div>
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
 
                
<script>
	//loadFields();
</script>
<?php require_once("./includes/footer.php"); ?>