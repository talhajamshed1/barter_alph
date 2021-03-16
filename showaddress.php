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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
include_once('./includes/gpc_map.php');

$sql = "SELECT * from " . TABLEPREFIX . "swaptxn where nSTId='" . $_REQUEST['nSTId'] . "'";
$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if ($srow = mysqli_fetch_array($res)){
    if ($srow['nUserId']==$_SESSION['guserid']) {
        $this_user = $srow['nUserId'];
        $other_user = $srow['nUserReturnId'];
    }
    else {
        $this_user = $srow['nUserReturnId'];
        $other_user = $srow['nUserId'];
    }
}
if ($_GET["pg"] == "wish" || $_GET["pg"] == "swap") {
    $var_disp1 = HEADING_YOUR_INFORMATION;
    $var_disp2 = HEADING_OTHER_USERS_INFORMATION;
}//end if
else {
    $var_disp1 = HEADING_OTHER_USERS_INFORMATION;
    $var_disp2 = HEADING_YOUR_INFORMATION;
}//end else


    $var_swapid = $_REQUEST["swapid"];
    $var_userid = $_REQUEST["userid"];
    $var_mode = $_REQUEST["mode"];
    $var_flag = $_REQUEST["flag"];

include ("./includes/session_check.php");

if ($var_mode == "A" && $var_flag = "true") {

    $sql = "SELECT vLoginName,vFirstName,vLastName,vAddress1,vAddress2,vCity,
             vState,vCountry,nZip,vPhone,vFax,vEmail  FROM
             " . TABLEPREFIX . "users U where nUserId = '" . $other_user . "' ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        if ($row = mysqli_fetch_array($result)) {
            $var_login1 = stripslashes($row["vLoginName"]);
            $var_first1 = stripslashes($row["vFirstName"]);
            $var_last1 = stripslashes($row["vLastName"]);
            $var_address11 = stripslashes($row["vAddress1"]);
            $var_address21 = stripslashes($row["vAddress2"]);
            $var_city1 = stripslashes($row["vCity"]);
            $var_state1 = stripslashes($row["vState"]);
            $var_country1 = stripslashes($row["vCountry"]);
            $var_zip1 = stripslashes($row["nZip"]);
            $var_phone1 = stripslashes($row["vPhone"]);
            $var_fax1 = stripslashes($row["vFax"]);
            $var_email1 = stripslashes($row["vEmail"]);
        }//end if
    }//end if


    $sql = "SELECT vLoginName,vFirstName,vLastName,vAddress1,vAddress2,vCity,vState,
             vCountry,nZip,vPhone,vFax,vEmail FROM
             " . TABLEPREFIX . "users U where nUserId='" . $this_user . "' ";

    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        if ($row = mysqli_fetch_array($result)) {
            $var_login2 = stripslashes($row["vLoginName"]);
            $var_first2 = stripslashes($row["vFirstName"]);
            $var_last2 = stripslashes($row["vLastName"]);
            $var_address12 = stripslashes($row["vAddress1"]);
            $var_address22 = stripslashes($row["vAddress2"]);
            $var_city2 = stripslashes($row["vCity"]);
            $var_state2 = stripslashes($row["vState"]);
            $var_country2 = stripslashes($row["vCountry"]);
            $var_zip2 = stripslashes($row["nZip"]);
            $var_phone2 = stripslashes($row["vPhone"]);
            $var_fax2 = stripslashes($row["vFax"]);
            $var_email2 = stripslashes($row["vEmail"]);
        }//end if
    }//end if
    $message = TEXT_DETAILS_OF_COMMUNITATION_GIVEN_BELOW;
    $var_form = "A";
}//end if
else if ($var_mode == "R" && $var_flag = "true") {
    $message2 = TEXT_OFFER_REJECTED;
    $var_form = "R";
}//end else if

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
	
	<div class="homepage_contentsec">
		<div class="container">
			<div class="row">
				<div class="col-lg-3">
					<?php include_once ("./includes/usermenu.php"); ?>
				</div>
				<div class="col-lg-9">
					<div class="innersubheader">
						<h4>
							<?php
								if ($var_mode == "A" && $var_flag = "true") {
									echo HEADING_CONTACT_DETAILS;
								}//end if
							?>
						</h4>
					</div>
					<div class="clearfix"></div>
						
						<div class="row">
							<?php if ($_SESSION["guserid"] == "") {
								include_once("./login_box.php");
							} ?>
							<form name="frmMakeOffer" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
							
							<?php
								if (isset($message) && $message != '') {
									?>
									<div class="col-xs-12 success"><?php echo $message; ?></div>
									<div class="clearfix">&nbsp;</div>
								<?php
								}//end if
								if ($var_form == "A") {
							?>
							
							<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
								<h4><?php echo  $var_disp1 ?></h4>
								
								<div class="user_prof_outer user_prof_gray">
									<div class="user_prof_L"><b><?php echo TEXT_FULL_NAME; ?></b></div>
									<div class="user_prof_R">
										<?php echo
											$var_last1
													. " "
													. $var_first1
											?>
									</div>
								</div>
								<div class="user_prof_outer">
									<div class="user_prof_L"><b><?php echo TEXT_ADDRESS_LINE1; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_address11 ?></div>
								</div>
								<div class="user_prof_outer user_prof_gray">
									<div class="user_prof_L"><b><?php echo TEXT_ADDRESS_LINE2; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_address21 ?></div>
								</div>
								<div class="user_prof_outer">
									<div class="user_prof_L"><b><?php echo TEXT_CITY; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_city1 ?></div>
								</div>
								<div class="user_prof_outer user_prof_gray">
									<div class="user_prof_L"><b><?php echo TEXT_STATE; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_state1 ?></div>
								</div>
								<div class="user_prof_outer">
									<div class="user_prof_L"><b><?php echo TEXT_COUNTRY; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_country1 ?></div>
								</div>
								<div class="user_prof_outer user_prof_gray">
									<div class="user_prof_L"><b><?php echo TEXT_ZIP; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_zip1 ?></div>
								</div>
								<div class="user_prof_outer">
									<div class="user_prof_L"><b><?php echo TEXT_PHONE; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_phone1 ?></div>
								</div>
								<div class="user_prof_outer user_prof_gray">
									<div class="user_prof_L"><b><?php echo TEXT_FAX; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_fax1 ?></div>
								</div>
								<div class="user_prof_outer">
									<div class="user_prof_L"><b><?php echo TEXT_EMAIL; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_email1 ?></div>
								</div>
								
							</div>
							
							<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
								<h4><?php echo  $var_disp2 ?></h4>
								
								<div class="user_prof_outer user_prof_gray">
									<div class="user_prof_L"><b><?php echo TEXT_FULL_NAME; ?></b></div>
									<div class="user_prof_R">
										<?php echo
											$var_last2
											. " "
											. $var_first2
										?>
									</div>
								</div>
								<div class="user_prof_outer">
									<div class="user_prof_L"><b><?php echo TEXT_ADDRESS_LINE1; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_address12 ?></div>
								</div>
								<div class="user_prof_outer user_prof_gray">
									<div class="user_prof_L"><b><?php echo TEXT_ADDRESS_LINE2; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_address22 ?></div>
								</div>
								<div class="user_prof_outer">
									<div class="user_prof_L"><b><?php echo TEXT_CITY; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_city2 ?></div>
								</div>
								<div class="user_prof_outer user_prof_gray">
									<div class="user_prof_L"><b><?php echo TEXT_STATE; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_state2 ?></div>
								</div>
								<div class="user_prof_outer">
									<div class="user_prof_L"><b><?php echo TEXT_COUNTRY; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_country2 ?></div>
								</div>
								<div class="user_prof_outer user_prof_gray">
									<div class="user_prof_L"><b><?php echo TEXT_ZIP; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_zip2 ?></div>
								</div>
								<div class="user_prof_outer">
									<div class="user_prof_L"><b><?php echo TEXT_PHONE; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_phone2 ?></div>
								</div>
								<div class="user_prof_outer user_prof_gray">
									<div class="user_prof_L"><b><?php echo TEXT_FAX; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_fax2 ?></div>
								</div>
								<div class="user_prof_outer">
									<div class="user_prof_L"><b><?php echo TEXT_EMAIL; ?></b></div>
									<div class="user_prof_R"><?php echo  $var_email2 ?></div>
								</div>
								
							</div>
							<?php
						}//end if
						else if ($var_mode == "R") {
							?>
							<div class="col-xs-12 success"><?php echo $message2; ?></div>
							<?php
						}//end else if
						else {
							?>							
							<div class="col-xs-12 success"><?php echo  $message ?></div>
							<?php
						}//end else
						?>
						<div class="clearfix">&nbsp;</div>
							<div class="col-xs-12">
								<div class="space2">
									<a href="usermain.php" class="backbtn right"><span class=" glyphicon glyphicon-circle-arrow-left"></span> <?php echo LINK_BACK_TO_DASHBOARD; ?></a>
								</div>
							</div>
							</form>
						</div> 
				
														
					<div class="subbanner">
						<?php include('./includes/sub_banners.php'); ?>
					</div>			
				</div>			
				
				
			</div>
		</div>
	</div>

<?php require_once("./includes/footer.php"); ?>