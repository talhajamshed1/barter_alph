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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/session_check.php");

include_once('./includes/gpc_map.php');

 if ($_GET["saleid"] != "") {
    $amnt = $_GET["amnt"];
    $saleid = $_GET["saleid"];
    //$userid=$_GET["userid"];
    $now = $_GET["dt"];
}//end if
else if ($_POST["saleid"] != "") {
    $saleid = $_POST["saleid"];
    $now = urldecode($_POST["dt"]);
    $cost = $_POST["amnt"];
    $amnt = $cost;
}//end else if
if(!empty($saleid)) {
	$_SESSION['userBuyID'] = $saleid;
	$_SESSION['userBuyNow'] = $now;
}
                                                                        

if (DisplayLookUp('Enable Escrow') == 'Yes') {
    $txtPaypalEmail = DisplayLookUp('paypalemail');
    $txtPaypalAuthtoken = DisplayLookUp('paypalauthtoken');
}//end if
else {
    //if escrow is disble select buyer paypal email id
 /*   $condition = "where nUserId='" . $_SESSION['sess_buyerid_escrow'] . "'";
    $txtPaypalEmail = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vPaypalEmail', $condition), 'vPaypalEmail');
    $txtPaypalAuthtoken = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vPaypalAuthToken', $condition), 'vPaypalAuthToken');
	*/
    
    $userid = fetchSingleValue(select_rows(TABLEPREFIX . 'sale', 'nUserId', ' WHERE nSaleId = '.$saleid), 'nUserId');
     $condition = "where nUserId='" . $userid . "'";
    $txtPaypalEmail = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vPaypalEmail', $condition), 'vPaypalEmail');   

    if( $txtPaypalEmail == '')
	{
                echo "<script>
                        Paypal settings are not enabled by the seller. Please select an another payment method.
                      </script>";
		header('location:'.$_SERVER["HTTP_REFERER"]);
		exit();
	}
     
    $txtPaypalAuthtoken = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vPaypalAuthToken', $condition), 'vPaypalAuthToken');
	
}//end else

$txtPaypalSandbox = DisplayLookUp('paypalmode');
$paypalenabled = DisplayLookUp('paypalsupport');

if ($paypalenabled != "YES") {
    header('location:index.php');
    exit();
}//end if

if ($txtPaypalSandbox == "TEST") {
    $paypalurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    $paypalbuttonurl = "https://www.sandbox.paypal.com/en_US/i/btn/x-click-but23.gif";
}//end if
else {
    $paypalurl = "https://www.paypal.com/cgi-bin/webscr";
    $paypalbuttonurl = "https://www.paypal.com/en_US/i/btn/x-click-but23.gif";
}//end else

include_once('./includes/title.php');
?>
 <style type="text/css">
#pageloaddiv {
position: fixed;
left: 0px;
top: 50px;
width: 100%;
height: 100%;
z-index: 1000;
background: url('images/pageloader.gif') no-repeat center center;
}
</style>
<?php include_once('./includes/top_header.php'); ?>
    <script language="Javascript">
    $jqr(document).ready(function(){
        //$jqr("#pageloaddiv").fadeOut(2000);
         $jqr("form#ppform").submit();
         //setTimeout($jqr("form#ppform").submit(),1000);
         
         $jqr('form#ppform').submit(function() {
                var pass = true;
                //some validations

                if(pass == false){
                    return false;
                }
               // $jqr("form#ppform").submit();
                $jqr("#pageloaddiv").fadeOut(2000);

                return true;
            });
    });
    //onLoad="javascript:document.frmPay.submit();"
    </script>
	

<div class="homepage_contentsec">
<div class="container">
	<div class="row">
		<div class="col-lg-3"></div>
		<div class="col-lg-6">					
			<div class="innersubheader row">
				<h4><?php echo HEADING_PAYMENT_PROCESS; ?></h4>
			</div>
			
			<div class="row">
				<div class="col-lg-12 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<?php
					   
						//checking escrow status
						if (DisplayLookUp('Enable Escrow') == 'Yes') {
							$SaleStatus = '1';
						}//end if
						else {
							$SaleStatus = "4";
						}//end esle

							$userid = $_SESSION["guserid"];
							$cc_err = "";
							$cc_flag = false;
							$var_insert_flag = false;

							$var_sale_flag = false;
							$var_rej_flag = false;
							$sql = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
							$sql .= " on sd.nSaleId = s.nSaleId ";
							$sql .= " where  sd.nSaleId='" . addslashes($saleid) . "' AND sd.nUserId='" . $_SESSION["guserid"] . "' AND sd.dDate='";
							$sql .= addslashes($now) . "' ";

							$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
							if (mysqli_num_rows($result) > 0) {
								if ($row = mysqli_fetch_array($result)) {
									$cost = $row["nAmount"];
									$amnt = $cost;
									$var_title = $row["vTitle"];
									$reqd = $row["nQuantity"];
									if ($row["vSaleStatus"] == $SaleStatus) {
										$var_sale_flag = true;
									}//end if
									if ($row["vRejected"] == "0") {
										$var_rej_flag = true;
									}//end if
								}//end if
							}//end if
							else {
								$cc_err = '<span class="warning">'.ERROR_CHECK_YOUR_INPUT.'</span>';
							}//end else

							$sql = "Select nSaleInterId from " . TABLEPREFIX . "saleinter where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
							$sql .= addslashes($now) . "' AND vDelStatus='0' ";
							$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
							if (mysqli_num_rows($result) > 0) {

								header('buyothers.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=$amnt&');
								exit();
							}//end if
							else {
								$var_insert_flag = true;
							}//end else

							if ($var_sale_flag == true && $var_rej_flag == true) {
								
									$customValues   = $saleid ."@".$now;
								?>
							
								<?php echo TEXT_WAIT_DIRECT_TO_PAYPAL; ?>...........
								<form name="_xclick" id="ppform"   action="<?php echo  $paypalurl ?>" method="post" >
									<input type="hidden" name="cmd" value="_xclick">
									<input type="hidden" name="business" value="<?php echo  $txtPaypalEmail ?>">
									<input type="hidden" name="item_name" value="<?php echo  htmlentities($var_title) ?>">
									<input type="hidden" name="item_number" value="<?php echo  $saleid ?>">
									<input type="hidden" name="amount" value="<?php echo  round($amnt, 2) ?>">
									<input type="hidden" name="no_shipping" value="1">
									<input type="hidden" name="custom" value="<?php echo  $customValues ?>">
									<input type="hidden" name="rm" value="2">
									<input type="hidden" name="notify_url" value="<?php echo SECURE_SITE_URL; ?>/buyipn.php">
									<input type="hidden" name="return" value="<?php echo SECURE_SITE_URL; ?>/buysuccess.php">
									<input type="hidden" name="cancel_return" value="<?php echo SECURE_SITE_URL; ?>/failure.php">
									<input type="hidden" name="no_note" value="1">
									<input type="hidden" name="currency_code" value="<?php echo PAYMENT_CURRENCY_CODE; ?>">
									<input type="hidden" name="on0" value="TempId">
									<input type="hidden" name="os0" maxlength="200" value="<?php echo  $userid ?>">
									<input type="hidden" name="on1" value="Purchase Date">
									<input type="hidden" name="os1" maxlength="200" value="<?php echo  htmlentities($now) ?>">
									<input type="hidden" name="bn" value="armiasystems_shoppingcart_wps_us">
									<input type="image" src="<?php echo $paypalbuttonurl;?>" border="0" name="submit" alt="" height="0" width="0">
								</form>
								
								<div id="pageloaddiv"></div>
								<?php
							} else {
								header("location:" . SITE_URL . "/index.php?paid=no");
								exit();
							}
							?>		
				</div>
				
				<div class="clear"></div>					
				
				<div class="col-lg-12 col-sm-12 col-md-12">
					<div class="subbanner">
						<?php include('./includes/sub_banners.php'); ?>
					</div>
				</div>
				
			</div>					
		</div>
		<div class="col-lg-3"></div>
	</div>  
</div>
</div>

<?php require_once("./includes/footer.php"); ?>