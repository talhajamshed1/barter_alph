<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com Â© 2005               |
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
include ("./includes/enable_module.php");
$message = "";
$var_flag = false;
$var_update_flag = false;
$var_error_message = "";
$var_show_flag = false;
include ("./includes/session_check.php");

if ($_GET["saleid"] != "") {
    $saleid = $_GET["saleid"];
    $quantityREQD = $_GET["reqd"];
    $amount = $_GET["amnt"];
    $total = $_GET["tot"];
    $source = $_GET["source"];
    $now = $_GET["dt"];
    $amount = ($amount == "") ? ($total / $quantityREQD) : $amount;
}//end if
else if ($_POST["saleid"] != "") {
    $saleid = $_POST["saleid"];
    $quantityREQD = $_POST["reqd"];
    $amount = $_POST["amnt"];
    $total = $_POST["tot"];
    $source = $_POST["source"];
    $cctype = $_POST["cctype"];
    $now = urldecode($_POST["dt"]);
}//end else if


//paypal email
$paypalEmail='';
$sellerEmail='';
$sql = "Select U.vPaypalEmail,U.vEmail,U.vLoginName from " . TABLEPREFIX . "sale S LEFT JOIN
" . TABLEPREFIX . "users U ON S.nUserId = U.nUserId where S.nSaleId = '" . $saleid . "'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    $row1 = mysqli_fetch_array($result);
    $paypalEmail = $row1['vPaypalEmail'];
    $sellerEmail = $row1['vEmail'];
    $sellerName  = $row1['vLoginName'];
}

if($_GET['reload']=='YES' && $sellerEmail!=''){

    if (DisplayLookUp('4') != '') {
        $admin_email = DisplayLookUp('4');
        }//end if

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
        AND C.content_name = 'paypalEmailRequestToSeller'
        AND C.content_type = 'email'
        AND L.lang_id = '".$_SESSION["lang_id"]."'";
        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $mainTextShow   = $mailRw['content'];
        $subject        = $mailRw['content_title'];
        $subject        = str_replace('{SITE_NAME}',SITE_NAME,$subject);
        

        $arrTSearch	= array("{SITE_NAME}","{SITE_URL}");
        $arrTReplace	= array(SITE_NAME,SITE_URL);
        
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);
        
        $mailcontent1   = $mainTextShow;
        
        
        $StyleContent=MailStyle($sitestyle,SITE_URL);
        //readf file n replace
        $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
        $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, $sellerName, $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
        $msgBody    = file_get_contents('./languages/'.$langRw["folder_name"].'/mail.html');
        $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);        

        send_mail($sellerEmail, $subject, $msgBody, $admin_email, 'Administrator');
    }

//checking escrow status
    if (DisplayLookUp('Enable Escrow') == 'Yes') {
        $SaleStatus = '1';
}//end if
else {
    $SaleStatus = "4";
}//end esle
// sale details loading
$var_sale_flag = true;
$var_rej_flag = true;
$sql = "Select nAmount,dDate,nQuantity,vSaleStatus,vRejected,vAddress1,vAddress2,vCity,vState,vCountry,nZip,vPhone from " . TABLEPREFIX . "saledetails where ";
$sql .= " nSaleId='" . $saleid . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
$sql .= $now . "' ";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    if ($row = mysqli_fetch_array($result)) {
        $quantityREQD = $row["nQuantity"];
        $vAddress1    = $row["vAddress1"];
        $vAddress2    = $row["vAddress2"];
        $vCity        = $row["vCity"];
        $vState       = $row["vState"];
        $Country      = $row["vCountry"];
        $nZip         = $row["nZip"];
        $vPhone       = $row["vPhone"];
        $total = $row["nAmount"];
        if ($row["vSaleStatus"] != $SaleStatus) {
            $var_sale_flag = false;
        }//end if
        else if ($row["vRejected"] == "1") {
            $var_rej_flag = false;
        }//end else
    }//end if
}//end if
//end of loading

if ($_POST["postback"] == "Y") {
    
    if ($var_rej_flag == true && $var_sale_flag == true) {
        $sql = "Update " . TABLEPREFIX . "saledetails set vMethod='" . addslashes($cctype) . "',dTxnDate=now() where ";
        $sql .= " nSaleId='" . $saleid . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='";
        $sql .= $now . "' ";
        
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        $amnt = $_POST["tot"];
        if ($cctype == "cc") {
            header('location:buycc.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=' . $amnt . '&paymethod=cc&');
            exit();
        } else if ($cctype == "yp") {
            header('location:buycc.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=' . $amnt . '&paymethod=yp&');
            exit();
        }else if ($cctype == "sp") {
            header('location:buycc.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=' . $amnt . '&paymethod=sp&');
            exit();
        }
        else if ($cctype == "gc") {
            header('location:buycc.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=' . $amnt . '&paymethod=gc&reqd='.$_POST['reqd']);
            exit();
        } else if ($cctype == "bp") {
            header('location:buycc.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=' . $amnt . '&paymethod=bp&');
            exit();
        } else if ($cctype == "pp") {
            header("location:buypp.php?saleid=$saleid&userid=" . $_SESSION["guserid"] . "&dt=" . urlencode($now) . "&amnt=" . $amnt . "&");
            exit();
        }//end else if
        else if ($cctype == "rp") {
            header('location:buycc.php?saleid=' . $saleid . '&userid=' . $_SESSION["guserid"] . '&dt=' . urlencode($now) . '&amnt=' . $amnt . '&paymethod=rp&reqd='.$_POST['reqd']);
            exit();
        } else if ($cctype == "wp") {
            header("location:buywp.php?saleid=$saleid&userid=" . $_SESSION["guserid"] . "&dt=" . urlencode($now) . "&amnt=" . $amnt . "&");
            exit();
        }//end else if
        else {
            header("location:buyothers.php?saleid=" . $saleid . "&userid=" . $_SESSION["guserid"] . "&dt=" . urlencode($now) . "&amnt=" . $amnt . "&");
            exit();
        }//end else
    }//end if
    else {
        $error_message = ERROR_SALE_OFFER_REJECTED_BY_USER;
    }//end else
}//end if

if ($_POST["btnLogin"] == "Login") {
    $txtUserName = $_POST["txtUserName"];
    $txtPassword = $_POST["txtPassword"];

    $txtUserName = addslashes($txtUserName);
    $sqluserdetails = "SELECT nUserId, vEmail,vStatus  FROM " . TABLEPREFIX . "users WHERE vLoginName = '$txtUserName' AND
    vPassword = '" . md5($txtPassword) . "' ";
    $resultuserdetails = mysqli_query($conn, $sqluserdetails) or die(mysqli_error($conn));
    if (mysqli_num_rows($resultuserdetails) > 0) {
        $row = mysqli_fetch_array($resultuserdetails);
        if ($row["vStatus"] == "0") {
            $_SESSION["guserid"] = $row["nUserId"];
            $_SESSION["guseremail"] = $row["vEmail"];
            $_SESSION["gloginname"] = stripslashes($txtUserName);
            $var_flag = true;
        }//end if
        else {
            $message = ERROR_ACCESS_DENIED_CONTACT_EMAIL."<a href=\"mailto:" . SITE_EMAIL . "\">" . SITE_EMAIL . "</a>";
            $var_flag = false;
        }//end else
    }//end if
    else {
        $message = ERROR_INVALID_USERNAME_PASSWORD;
        $var_flag = false;
    }//end else
}//end if
//display confirm message
//get the item requested
//get item equested details

$sql = "SELECT s.nSaleId,s.nUserId,s.vTitle,s.nQuantity,s.nShipping,s.nValue,s.nPoint,u.vLoginName,u.vAddress1,u.vAddress2,u.vCity,
u.vState,u.vCountry,";
$sql .= "u.nZip,u.vFax,u.vEmail from " . TABLEPREFIX . "sale s inner join " . TABLEPREFIX . "users u ";
$sql .= " on s.nUserId = u.nUserId where s.nSaleId  = '$saleid' ";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
      $User = $row["nUserId"];
      $Title = $row["vTitle"]; 
      if ( preg_match('/\s/',$Title) ) { $Title = $Title; } 
      else { 
        if (strlen($Title) > 36) {
            $Title = substr($Title, 0, 36).'...';
        }
    }

    $QuantityAVL = $row["nQuantity"];
    $ShipingPrice = $row["nShipping"];
    $Price = $row["nValue"];
    $var_login = $row["vLoginName"];
    $var_address1 = $row["vAddress1"];
    $var_address2 = $row["vAddress2"];
    $var_city = $row["vCity"];
    $var_state = $row["vState"];
    $var_country = $row["vCountry"];
    $var_zip = $row["nZip"];
    $var_fax = $row["vFax"];
    $var_email = $row["vEmail"];
    $var_point = $row["nPoint"];
	}//end while
}//end if

include_once('./includes/gpc_map.php');

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function varify()
    {
        reqd= document.frmBuy.quantityREQD.value;
        avail = document.frmBuy.quantityAVL.value;
        if(isNaN(reqd) || reqd.substring(0,1) == " " || reqd.length <= 0 || parseInt(reqd) > parseInt(avail) || parseInt(reqd) < 1)
        {
            alert("<?php echo ERROR_QUANTITY_INVALID; ?>");
            document.frmBuy.quantityREQD.value="1";
        }//end if
        document.frmBuy.total.value=parseInt(document.frmBuy.amount.value)*parseInt(document.frmBuy.quantityREQD.value);
    }//end funciton


    function proceed(cc)
    {
        document.frmBuy.cctype.value=cc;
        document.frmBuy.postback.value='Y';
        document.frmBuy.submit();
    }//edn function

    function informseller(){
        alert('<?php echo TEXT_PAYPAL_EMAIL_ERROR?>');
        if(window.location.href.indexOf("reload=YES") == '-1'){
            window.location.href = location.href + 'reload=YES';
        }else{
            window.location.href = location.href + 'reload=NO';
        }        
        return true;
        
    }//end function
</script>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>
    
    <div class="homepage_contentsec">
       <div class="container">
          <div class="row">
             <div class="col-lg-3">
                 <?php
                 if ($var_flag == false) {
                    include_once ("./includes/categorymain.php");
			}//end if
			else {
				include_once ("./includes/usermenu.php");
			}//end else
			?>
			
			
     </div>
     <div class="col-lg-9">				
        <div class="row">
          
           
           <?php                  

           if (trim($_GET['total_points'])!='' && ENABLE_POINT != '0' && $_GET['total_points'] > 0)

               echo ' <div class="alert alert-success">'.str_replace('{point_name}',POINT_NAME,str_replace('{points}', $_GET['total_points'], MESSAGE_POINT_SUCCESSFULLY_DEDUCTED_FROM_ACCOUNT)).'</div>'; ?>


           
       </div>
       
       <div class="innersubheader">
           <h4><?php echo HEADING_PAYMENT_FORM; ?></h4>
       </div>
       
       <?php
       if ($var_flag == false) {
           ?>
           <?php
           if ($_SESSION["guserid"] == "") {
             include_once("./login_box.php");
         }
         ?>
         <?php
					}//end if
					else {
                        ?>
                        
                        <div class="clearfix">
                           
                            <div class="col-lg-12 profile-section-bottom heading-style-h4">
                               <form name="frmBuy" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                  <?php
                                  if (isset($message) && $message != '') {
                                     
                                      if($var_flag == false){   
                                       ?>
                                       <div class="row warning"><?php echo $message; ?></div>
                                       <?php } else if($var_flag == true){?>
                                       <div class="row success"><?php echo $message; ?></div>           
                                       <?php }?>
                                       <br>						
                                       <?php
							}//end if
							if ($var_flag == true) {
								if ($_SESSION["guserid"] != $User) {
                                  ?>
                                  
                                  <div class="clearfix">
                                     <input type="hidden" name="cctype" id=cctype  value="">
                                     <input type="hidden" name="source" value="<?php echo  $source ?>">
                                     <input type="hidden" name="saleid" value="<?php echo  $saleid ?>">
                                     <input type="hidden" name="postback" id="postback" value="">
                                     <input type="hidden" name="dt" id="dt" value="<?php echo  urlencode($now) ?>">
                                     <h4><?php echo HEADING_CONTACT_DETAILS; ?></h4>
                                 </div>
                                 
                                 <div class="row custom-text-div">
                                     <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                        <label><?php echo TEXT_NAME; ?></label>
                                        <div><?php echo  htmlentities($var_login) ?></div>
                                    </div>
                                    
                                    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                        <label><?php echo TEXT_ADDRESS; ?></label>
                                        <div>
                                            <?php echo  htmlentities($var_address1) ?>
                                            <?php echo  htmlentities($var_address2) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row custom-text-div">
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                   <label><?php echo TEXT_CITY; ?></label>
                                   <div><?php echo  htmlentities($var_city) ?></div>
                               </div>
                               <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                <label><?php echo TEXT_STATE; ?></label>
                                <div><?php echo  htmlentities($var_state) ?></div>
                            </div>
                        </div>


                        <div class="row custom-text-div">
                         <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                            <label><?php echo TEXT_COUNTRY; ?></label>
                            <div><?php echo  htmlentities($var_country) ?></div>
                        </div>
                        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                         <label><?php echo TEXT_ZIP; ?></label>
                         <div><?php echo  $var_zip ?></div>
                     </div>
                 </div>



                 <div class="row custom-text-div">
                     <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                        <label><?php echo TEXT_FAX; ?></label>
                        <div><?php echo  htmlentities($var_fax) ?></div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                     <label><?php echo TEXT_EMAIL; ?></label>
                     <div><?php echo  htmlentities($var_email) ?></div>
                 </div>
             </div>
             
             
             <div class="clearfix">
                 
                 <h4><?php echo SHIPPING_ADDRESS; ?></h4>
             </div>
             
             <div class="row custom-text-div">
                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                    <label><?php echo TEXT_ADDRESS; ?></label>
                    <div><?php echo  htmlentities($vAddress1) ?><br /><?php echo  htmlentities($vAddress2) ?></div>
                </div>
                
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                    <label><?php echo TEXT_CITY; ?></label>
                    <div><?php echo  htmlentities($vCity) ?></div>
                </div>
            </div>



            <div class="row custom-text-div">
             <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                <label><?php echo TEXT_STATE; ?></label>
                <div><?php echo  htmlentities($vState) ?></div>
            </div>
            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                <label><?php echo TEXT_COUNTRY; ?></label>
                <div><?php echo  htmlentities($Country) ?></div>
            </div>
        </div>


        <div class="row custom-text-div">
         <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
            <label><?php echo TEXT_ZIP; ?></label>
            <div><?php echo  $nZip ?></div>
        </div>
        
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
            <label><?php echo TEXT_PHONE; ?></label>
            <div><?php echo  htmlentities($vPhone) ?></div>
        </div>
    </div>
    
    
    <div class="clearfix">
     <h4>
        <?php echo HEADING_SALES_DETAILS; ?>
        <?php
        if ($var_rej_flag == false) {
           echo "<br> ".ERROR_SALE_REJECTED_BY_OWNER."<br>";
								}//end if
								?>
							</h4>
						</div>
						<div class="row custom-text-div">
							<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                <label><?php echo TEXT_TITLE; ?></label>
                                <div><?php echo  htmlentities($Title) ?></div>
                            </div>
                            
                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                <label><?php echo TEXT_QUANTITY_REQUIRED; ?></label>
                                <div><?php echo  $quantityREQD ?>
                                    <input type="hidden" name="reqd" value="<?php echo  $quantityREQD ?>"></div>
                                </div>
                            </div>


                            <div class="row custom-text-div">
                             <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                <label><?php echo TEXT_AMOUNT; ?> [<?php echo TEXT_INCLUDING_SHIPPING; ?>]</label>
                                <div>
                                    <?php echo CURRENCY_CODE; ?> <?php echo  $amount ?>
                                    <input type="hidden" name="amnt" value="<?php echo  $amount ?>">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                <label><?php echo TEXT_TOTAL_AMOUNT; ?></label>
                                <div>
                                    <?php echo CURRENCY_CODE; ?> <?php echo  $total ?>
                                    <input type="hidden" name="tot" value="<?php echo  $total ?>">
                                </div>
                            </div>
                        </div>


                        <?php if (ENABLE_POINT != '0'){ ?>
                        <div class="row custom-text-div">
                         <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                            <label><?php echo str_replace('{point_name}',POINT_NAME,TEXT_TOTAL_POINTS); ?></label>
                            <div><?php echo  ($var_point*$quantityREQD) ?></div>
                        </div>
                    </div>
                    <?php } ?>
                    
                    <?php
                    if ($var_sale_flag == true && $var_rej_flag == true) {
							//checking point enable in website
                            //do not show if payment done

                     if (ENABLE_POINT != '0' && $total == 0) {
                         ?>
                         <div class="clearfix">
                             <h4><?php echo TEXT_USE_REDEEM; ?> <?php echo POINT_NAME; ?></h4>
                         </div>
                         
                         <div class="row ">
                             <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                 <?php echo TEXT_USE; ?> <?php echo POINT_NAME; ?>
                             </div>

                             <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><a href="javascript:proceed('rp');"><img src="images/redeempoints.jpg" border="0" alt=""></a></div>
                             
                         </div>
                         <?php
							}//end checking point if
                         ?>
                         <?php if($total > 0) {?>
                         <div class="clearfix">
                             <h4><?php echo TEXT_USE_CREDIT_CARDS; ?></h4>
                         </div>
                         <?php }
                         
                         if (DisplayLookUp('Enable Escrow') == 'Yes' && $total > 0) {
                            
                             if (PAYMENT_CURRENCY_CODE == 'USD') {
                                if (DisplayLookUp('authsupport') == "YES") {
                                   ?>
                                   <div class="row main_form_inner custom-text-div">
                                     <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><label><?php echo TEXT_USE_AUTHORIZE; ?></label></div>
                                     <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><a href="javascript:proceed('cc');"><img src="images/cc.jpg" border="0" alt=""></a></div>
                                 </div>
                                 <?php
								}//end if
							}//end if
							if (DisplayLookUp('enablestripe') == "Y" && $total > 0) {
                             ?>							
                             <div class="row main_form_inner ">
                                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><label><?php echo TEXT_USE_STRIPE; ?></label></div>
                                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                   <a href="javascript:proceed('sp');"><img src="images/cc.jpg" width="180" height="31" title="" border="0" alt=""></a>
                               </div>
                           </div>
                           <?php
							}//end if
							if (DisplayLookUp('yourpaysupport') == "YES" && $total > 0) {
								?>
                              <div class="row main_form_inner">
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><?php echo TEXT_USE_YOURPAY; ?></div>
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><a href="javascript:proceed('yp');"><img src="images/cc.jpg" border="0" alt=""></a></div>
                             </div>
                             <?php
						}//end if
						/*if (DisplayLookUp('googlesupport') == "YES") {
							?>
						<div class="row main_form_inner">
							<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><?php echo TEXT_USE_GOOGLE_CHECKOUT; ?></div>
							<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><a href="javascript:proceed('gc');"><img src="images/checkout.gif" border="0" alt=""></a></div>
						</div>
						<?php
                 }//end if*/
                 if (DisplayLookUp('enableworldpay') == "Y" && $total > 0) {
                    ?>
                    <div class="row main_form_inner">
                     <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><?php echo TEXT_USE_WORLDPAY; ?></div>
                     <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                        <a href="javascript:proceed('wp');"><img src="images/cc.jpg" width="180" height="31" title="" border="0" alt=""></a>
                    </div>
                </div>
                <?php
						}//end if
						if (DisplayLookUp('enablebluepay') == "Y" && $total > 0) {
                          ?>							
                          <div class="row main_form_inner">
                             <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><?php echo TEXT_USE_BLUEPAY; ?></div>
                             <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                <a href="javascript:proceed('bp');"><img src="images/cc.jpg" width="180" height="31" title="" border="0" alt=""></a>
                            </div>
                        </div>
                        <?php
						}//end if
						}//end if
						if (DisplayLookUp('paypalsupport') == "YES" && $total > 0) {
                         
                          ?>
                          <div class="row main_form_inner">
                             <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><?php echo TEXT_USE_PAYPAL; ?></div>
                             <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                                <a <?php if($paypalEmail!='' || DisplayLookUp('Enable Escrow') == 'Yes'){ ?>href="javascript:proceed('pp');" <?php }else{ ?> onclick="return informseller();" <?php } ?>><img src="images/x-click-but20.gif" border="0" alt=""></a>
                            </div>
                        </div>
                        <?php 
						}//end if
						//only available escrow is enable
						if (DisplayLookUp('Enable Escrow') == 'Yes' || PAYMENT_CURRENCY_CODE == 'USD' && $total > 0) {
							if (DisplayLookUp('otherpayment') == 'YES') {
                              ?>
                              
                              <div class="clearfix">
                                 <h4><?php echo TEXT_OTHER_PAYMENTS; ?></h4>
                             </div>						
                             <div class="row main_form_inner">
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><?php echo TEXT_USE_WIRETRANSFER; ?></div>
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><a href="javascript:proceed('wt');"><img src="images/wireftransfer.gif" border="0" alt=""></a></div>
                             </div>
                             <div class="row main_form_inner">
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><?php echo TEXT_USE_PERSONAL_CHECK; ?></div>
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><a href="javascript:proceed('pc');"><img src="images/personalcheck.gif" border="0"></a></div>
                             </div>
                             <div class="row main_form_inner">
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><?php echo TEXT_USE_BUSINESS_CHECK; ?></div>
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><a href="javascript:proceed('bu');"><img src="images/businesscheque.gif" border="0" alt=""></a></div>
                             </div>
                             <div class="row main_form_inner">
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><?php echo TEXT_USE_CASHIERS_CHECK; ?></div>
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><a href="javascript:proceed('ca');"><img src="images/cashierscheque.gif" border="0"></a></div>
                             </div>
                             <div class="row main_form_inner">
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><?php echo TEXT_USE_MONEY_ORDER; ?></div>
                                 <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6"><a href="javascript:proceed('mo');"><img src="images/moneyorder.gif" border="0"></a></div>
                             </div>
                             <?php }
                         }?>
                         <?php
                     }
                     else {
                        ?>  
                        <div class="row main_form_inner">
                            <br>
                            <button type="submit" value="<?php echo LINK_BACK_TO_DASHBOARD; ?>"  height="21" class="btn btn-default btn-new" onclick="window.location.href='usermain.php'; return false;">
                             <?php echo LINK_BACK_TO_DASHBOARD;?></button>							
                         </div>
                         <?php }
						}//end if
						else {
							echo '<div class="row main_form_inner warning">'.ERROR_ITEMP_POSTED_BY_YOU_CANNOT_PAY.'</div>';
						}//end else
					}//end if
					else {
						echo '<div class="row main_form_inner warning">' . $var_error_message . '</div>';
					}//end else
					?>						
               </form>
               
           </div>
           
       </div>
       <?php }//end else ?>		
       
       <div class="subbanner">
           <?php include('./includes/sub_banners.php'); ?>
       </div>		
   </div>
</div>  
</div>
</div>

<?php require_once("./includes/footer.php"); ?>