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

$saleid     = $_REQUEST['nId'];

//checking user status
if ($_GET["source"] == "s") {
    //fetch seller id
    $sellerId = fetchSingleValue(select_rows(TABLEPREFIX . 'swap', 'nUserId', "where nSwapId='" . $_GET["nId"] . "'"), 'nUserId');

    //seller produt id array
    $sellerProductArray = array();

    //create seller product array
    $selleProd = mysqli_query($conn, "SELECT nSwapId FROM " . TABLEPREFIX . "swap WHERE nUserId='" . $sellerId . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($selleProd) > 0) {
        while ($arrP = mysqli_fetch_array($selleProd)) {
            $sellerProductArray[] = $arrP['nSwapId'];
        }//end while loop
    }//end if
    $sellerProductArray = join($sellerProductArray, ",");

    //checking atleast one post against this
    $cndPost = "where nUserId='" . $_SESSION["guserid"] . "' AND nSwapId IN (" . $sellerProductArray . ") LIMIT 0,1";
    $userAllowPostFeedback = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'nUserId', $cndPost), 'nUserId');
}//end if
else if ($_GET["source"] == "w") {
    //fetch seller id
    $sellerId = fetchSingleValue(select_rows(TABLEPREFIX . 'swap', 'nUserId', "where nSwapId='" . $_GET["nId"] . "'"), 'nUserId');

    //seller produt id array
    $sellerProductArray = array();

    //create seller product array
    $selleProd = mysqli_query($conn, "SELECT nSwapId FROM " . TABLEPREFIX . "swap WHERE nUserId='" . $sellerId . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($selleProd) > 0) {
        while ($arrP = mysqli_fetch_array($selleProd)) {
            $sellerProductArray[] = $arrP['nSwapId'];
        }//end while loop
    }//end if
    $sellerProductArray = join($sellerProductArray, ",");

    //checking atleast one post against this
    $cndPost = "where nUserId='" . $_SESSION["guserid"] . "' AND nSwapId IN (" . $sellerProductArray . ") LIMIT 0,1";
    $userAllowPostFeedback = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'nUserId', $cndPost), 'nUserId');
}//end else if
else if ($_GET["source"] == "sa") {
    //fetch seller id
    $sellerId = fetchSingleValue(select_rows(TABLEPREFIX . 'sale', 'nUserId', "where nSaleId='" . $_GET["nId"] . "'"), 'nUserId');

    //seller produt id array
    $sellerProductArray = array();

    //create seller product array
    $selleProd = mysqli_query($conn, "SELECT nSaleId FROM " . TABLEPREFIX . "sale WHERE nUserId='" . $sellerId . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($selleProd) > 0) {
        while ($arrP = mysqli_fetch_array($selleProd)) {
            $sellerProductArray[] = $arrP['nSaleId'];
        }//end while loop
    }//end if
    $sellerProductArray = join($sellerProductArray, ",");

    //checking atleast one post against this
    $cndPost = "where nUserId='" . $_SESSION["guserid"] . "' AND nSaleId IN (" . $sellerProductArray . ") LIMIT 0,1";
    $userAllowPostFeedback = fetchSingleValue(select_rows(TABLEPREFIX . 'saledetails', 'nUserId', $cndPost), 'nUserId');
}//end else if
//checking at least one post agains this user
if (trim($userAllowPostFeedback) == '') {
    header('location:swapitemdisplay.php?saleid=' . $_GET['saleid'] . '&source=' . $_GET['source']);
    exit();
}//end if

$Nfdbackid = $_GET['uid'];

if (isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] != "") {
 
    $Nfdbackid = $_POST['Nfdbackid'];
    $txtDescription = addslashes(trim($_POST['txtDescription']));
    $txtTitle = addslashes(trim($_POST['txtTitle']));
    $radType = $_POST['radType'];
    $saleid  = $_POST['nId'];

    /*if (!get_magic_quotes_gpc()) {
        foreach ($_POST as $key => $value) {
            $$key = addslashes($value);
        }//end foreach
    }//end if
    */
    if ($txtTitle == "") {
        $message = ERROR_TITLE_EMPTY;
    }//end if
    else if ($txtDescription == "") {
        $message .= ERROR_EMPTY_FEEDBACK;
    }//end else
    else {
        $sql = "INSERT INTO " . TABLEPREFIX . "userfeedback (nUserFBId,vTitle,nUserId,vMatter,dPostDate,vStatus,nSaleId)";
        $sql .= " values('" . $_SESSION["guserid"] . "','$txtTitle','$Nfdbackid','$txtDescription',now(),'" . $radType . "','".$saleid."')";
        mysqli_query($conn, $sql) or die("Could Not Exicute:" . mysqli_error($conn));
        $message = MESSAGE_FEEDBACK_POSTED;
        $txtDescription = "";
        $txtTitle = "";
        $radType = "";
    }//end else
    
    header("Location:usermain.php?page=saleoffers");
    exit;
}//end if

$sqluserdetails = "SELECT  vAlertStatus , vNLStatus FROM " . TABLEPREFIX . "users  WHERE  nUserId  = '" . $_SESSION["guserid"] . "'";
$resultuserdetails = mysqli_query($conn, $sqluserdetails) or die(mysqli_error($conn));
if (mysqli_num_rows($resultuserdetails) > 0) {
    $rowuser = mysqli_fetch_array($resultuserdetails);
    $alertstatus = $rowuser["vAlertStatus"];
    $nlstatus = $rowuser["vNLStatus"];
}//end if

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function validatefeedBackForm()
    {
        var frm = window.document.frmFeedBack;
        if(trim(frm.txtTitle.value)=="")
        {
            alert("<?php echo ERROR_TITLE_EMPTY; ?>");
            frm.txtTitle.focus();
            return false;
        }//endif
        else if(trim(frm.txtDescription.value)=="")
        {
            alert("<?php echo ERROR_EMPTY_FEEDBACK; ?>");
            frm.txtDescription.focus();
            return false;
        }//end else
        return true;
    }
</script>
<body onLoad="timersOne();">

<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">
				
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 ">
						<div class="feedback-form">
							<h4><?php echo HEADING_POST_FEEDBACK; ?></h4>
						<form name="frmFeedBack" method ="post" action = "" onsubmit="return validatefeedBackForm();">
							<input type="hidden" name="Nfdbackid" value="<?php echo  $Nfdbackid ?>">
                                                        <input type="hidden" name="nId" value="<?php echo $_REQUEST['nId'];?>">
							<?php
							if (isset($message) && $message != '') {
								?>
                                                          <div class="success_msg">
                                    <span class="glyphicon glyphicon-ok-circle"></span><?php echo  $message;?>
                                </div>
							<?php }//end if ?>
							<div class="main_form_inner">
								<div class="clear">&nbsp;</div>
								<label><?php echo TEXT_RATING; ?><span class="warning">*</span></label>
								<div class="rating-container">
                                <div class=" col-lg-4">
                                    <div class="lbl">
                                        <label>
    									<input type="radio" name="radType" value="S" <?php if ($radType == 'S') {
    										echo 'checked';
    									}if ($radType == '') {
    										echo 'checked';
    									} ?>><?php echo TEXT_SATISFIED; ?>
                                    </label>
                                    <span></span>
                                </div>
								</div>								
								<div class=" col-lg-4">
                                     <div class="lbl">
                                        <label>
									<input type="radio" name="radType" value="D" <?php if ($radType == 'D') {
										echo 'checked';
									} ?>><?php echo TEXT_DISSATISFIED; ?>
                                </label>
                                    <span></span>
                                </div>
								</div>								
								<div class=" col-lg-4">
                                     <div class="lbl">
                                        <label>
									<input type="radio" name="radType" value="N" <?php if ($radType == 'N') {
										echo 'checked';
									} ?>><?php echo TEXT_NEUTRAL; ?>
                                </label>
                                    <span></span>
                                </div>
								</div>
								</div>
								
							</div>
							<div class=" main_form_inner">
								<label><?php echo TEXT_TITLE; ?> <span class="warning">*</span></label>
								<input type="text" class="form-control" name="txtTitle" size="40" maxlength="100" value="<?php echo  htmlentities(stripslashes($txtTitle)) ?>" />
							</div>
							<div class="main_form_inner">
								<label><?php echo TEXT_FEEDBACK; ?> <span class="warning">*</span></label>
								<textarea class="form-control" name="txtDescription" cols="37"><?php echo  htmlentities(stripslashes($txtDescription)) ?></textarea>
							</div>
							<div class="main_form_inner">
								<label>
									<input type="submit" name="btnSubmit" class="subm_btt" value="<?php echo HEADING_POST_FEEDBACK; ?>"/>
								</label>
							</div>
						</form>
                        <div class="text-center">
                        <a class="back" href="javascript:location.href='usermain.php?page=saleoffers';"><b><?php echo LINK_BACK; ?></b></a>
                    </div>
					</div>
                </div>
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>			
				</div>				
				<div class="subbanner">
				 	<?php include('./includes/sub_banners.php'); ?>
				</div>
			</div>
		</div>  
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>