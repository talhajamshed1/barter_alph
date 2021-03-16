<?php
//echo 'hi';exit;

include "../includes/config.php";

if (!function_exists('getallheaders')) 
{
   function getallheaders() 
   {      
//   ini_set('display_errors', 1);
      foreach ($_SERVER as $name => $value) 
      {
          if (substr($name, 0, 5) == 'HTTP_')
          {    
              $key_name=substr($name, 5);
//              $replace_str=str_replace('_', ' ',$key );
//              $key_name=str_replace(' ', '-', ucwords(strtolower($replace_str)));
              $headers[$key_name] = $value;

          }
      }
      return $headers;
   }
}




$request_url =  'http://' . $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI']; 
$f = @fopen('requestlogs.txt', 'a+');
if ($f) {
  @fputs($f, date("m.d.Y g:ia")."  ".$_SERVER['REMOTE_ADDR']."  ".$request_url."\n");
  @fclose($f);
}
$result['status'] = 0;
if (isset($_GET['action_type'])) {
//    print_r($_SERVER);
    $headers=getallheaders();
 //print_r($headers);exit;
    //pilot server
    $device_id=$headers['Device-Id'];
    $device_type=$headers['Device-Type'];
    $auth_key=isset($headers['Auth-Key'])?$headers['Auth-Key']:'';
    
    //$device_id=$headers['device_id'];
    //$device_type=$headers['device_type'];
    //$auth_key=isset($headers['auth_key'])?$headers['auth_key']:'';
    
    //demo.iscripts.com
    // $device_id=$headers['DEVICE_ID'];
    // $device_type=$headers['DEVICE_TYPE'];
    // $auth_key=isset($headers['AUTH_KEY'])?$headers['AUTH_KEY']:'';
    $requested_action=$_GET['action_type'];
    /*
     * Access Token Checking
    */
//    $accessToken  =  getAccessTokenValue();
//   $validityCheck   = 0;
//    if($_GET['action_type']!='login' && $_GET['seller_id']!=''){
//       $validityCheck =  checkAccessTokenValid($_GET['seller_id'],$accessToken);
//    } 
    /*
    * Access Token Checking End
    */


    $excluded_actions = array(
        'login',
        'register',
        'forgotpassword',
        'retrievecategory',
        'retrieveallcategory',
        'retrieveproduct',
        'retrieveallproducts',
        'productsearch',
        'retrieveallproductsby_category',
        'messageinbox',
        'messageoutbox',
        'createmessage',
        'deletemessage',
        'changelanguage',
        'getlanguages',
//        'paypal_test'
//        'additem',
//        'edititem',
//        'userprofile'
        //'deleteitem',
//        'retrieveuserproductsby_type'
        ); // actions excluded for auth key validation
    
//    echo $device_id.$device_type.$auth_key;exit;

    $user_id = validate_auth_key($device_id,$device_type,$auth_key); // get user details from auth key
    if(!in_array($requested_action, $excluded_actions) && !$user_id)
    {
       $result['status'] = 0;
       $result['auth_status'] = 1;
       $result['message'] ="Invalid Auth Token";
       $result['data']   = array(); 
    }
  
else {
//    if($validityCheck==0){
        switch ($requested_action) {

            case 'login': {
                    $result = loginAPI($_POST['username'],$_POST['password'],($_POST["lang_id"])?$_POST["lang_id"]:"");
                    break;
                }
            case 'logout': {
                    $result=logoutAPI();
                    break;
            }
            case 'forgotpassword':{
                    $result = forgot_passwordAPI($_POST['email'],($_POST["lang_id"])?$_POST["lang_id"]:"");
                break;
            }
            case 'register': { 
                    $result = registerAPI($_POST,($_POST["lang_id"])?$_POST["lang_id"]:"");
                    break;
                }
            case 'registration_payment': { 
                    $result = registration_paymentAPI($_POST['user_id'],$_POST['password'],$_POST['tx'],($_POST["lang_id"])?$_POST["lang_id"]:"");
                    break;
                } 
            case 'planslist': { 
                    $result = plansAPI();
                    break;
                }
            case 'retrievecategory': { 
                $result = retrieveCategoryAPI($_POST['cat_id'],$_POST['lang_id']);
                break;
            }
            case 'retrieveallcategory': { 
                $result = retrieveAllCategoryAPI($_POST['lang_id']);
                break;
            }  
            case 'retrieveallproducts': { 
                $result = retrieveAllProductsAPI($_POST['type'],$_POST['lang_id'],$user_id,0,'','','',($_POST['latitude'])?$_POST['latitude']:'',($_POST['longitude'])?$_POST['longitude']:'');
                break;
            } 
            case 'retrieveswapwishproducts':{
                $result = retrieveSwap_WishProductsAPI($_POST['type'],$_POST['lang_id'],$_POST['cat_id'],$user_id,$_POST['other_userid'],'',$_POST['transaction_type']);
                break;
            }
            case 'retrieveproduct': { 
                $result = retrieveProductAPI($_POST['prod_id'],$_POST['lang_id'],$_POST['type']);
                break;
            } 
            case 'retrieveallproductsby_category':{
               $result= retrieveAllProductsByCategoryAPI($_POST['cat_id'],$_POST['type'],$user_id,($_POST['latitude'])?$_POST['latitude']:'',($_POST['longitude'])?$_POST['longitude']:'');
               break;
            }
            case 'retrieveuserproductsby_type':{
                $result=retrieveUserProductsByTypeAPI($user_id,$_POST['type'],$_POST['swap_status']);
                break;
            }
            case 'productsearch':{
                $result=productsearchAPI($_POST['search_term'],$user_id,$_POST['productType'],($_POST['latitude'])?$_POST['latitude']:'',($_POST['longitude'])?$_POST['longitude']:'');
                break;
            }
            case 'myproductsearch':{
                $result=myproductsearchAPI($_POST['search_term'],$user_id,$_POST['productType']);
                break;
            }
            case 'updateprofile': { 
                $result = updateUserProfileAPI($_POST,$user_id);
                break;
            }  
            case 'saveuserimage': {
                $result =save_userimageAPI($_POST['profile_image'],$user_id);
                break;
            }
            case 'userprofile':{
                 $result = userprofileAPI($user_id);
                    break;
            }
            case 'paypal_details':{
                $result=paypal_detailsAPI();
                break;
            }
            
            case 'additem':{
                $result=addItem($_POST);
                break;
            }
            
            case 'edititem':{
                $result=editItem($_POST);
                break;
            }
            case 'deleteitem':{
                 $result=Delete_productAPI($_POST['type'],$_POST['cat_id'],$_POST['product_id'],$user_id);
                break;
            }
            case 'buysaleitem':{
                $result=buySaleProductAPI($_POST,$user_id);
                break;
            }
            case 'get_shipping_details':{
                $result=getShippingDetailsAPI($_POST['sale_id'],$user_id,$_POST['date']);
                break;
            }
            case 'makeoffer':{
                 $result=makeofferAPI($_POST,$user_id);
                  break;
            }
            
            case 'makeofferdeliver':{
                 $result=makeoffer_deliverAPI($_POST);
                  break;
            }
            case 'makeofferreject':{
                 $result=makeoffer_rejectAPI($_POST);
                  break;
            }
            case 'makeofferapprove':{
                 $result=makeoffer_approveAPI($_POST);
                  break;
            }
            case 'makeofferdelete':{
                 $result=makeoffer_deleteAPI($_POST,$user_id);
                  break;
            }
            case 'makeofferedit':{
                 $result=makeoffer_editAPI($_POST,$user_id);
                  break;
            }
             case 'makeofferview':{
                 $result=make_offer_viewAPI($_POST,$user_id);
                  break;
            }
            case 'offeroutbox':{
                 $result=offer_outboxAPI($user_id);
                  break;
                
            }
            case 'offerinbox':{
                 $result=offer_inboxAPI($user_id);
                  break;
            }
            case 'sale_payment':{
             $result=sale_payment($_POST['saleid'],$_POST['sale_date'],$_POST['token'],$_POST['lang_id'],$user_id,$_POST['seller_user']);
                  break;
                
            }
            case 'make_offer_payment':{
             $result=make_offer_payment($_POST['swap_id'],$_POST['amount'],$_POST['token'],$user_id);
                  break;
                
            }
            case 'messageinbox':{
                $result=message_inboxAPI($user_id);
            break;
            }
            case 'messageoutbox':{
                $result=message_outboxAPI($user_id);
            break;
            }
            case 'createmessage':{
                $result = create_messageAPI($_POST,$user_id);
            break;
            }
            case 'deletemessage':{
                $result=Delete_messageAPI($_POST,$user_id);
            break;
           }
           case 'changelanguage':{
               $result=Change_languageAPI($_POST,($user_id)?$user_id:'');
           break;
           }
           case 'getlanguages':{
                $result = Get_languagesAPI();
            break;
            }
//            case 'orderdetails': { 
//                    $result = orderDetailsAPI($_GET['order_id'],$_GET['seller_id']);
//                    break;
//                }   
//            case 'paymentlist': { 
//                    $result = paymentlistAPI($_GET['seller_id']);
//                    break;
//                }
//            case 'requestforpaymentlist': { 
//                    $result = requestForPaymentlistAPI($_GET['seller_id']);
//                    break;
//                }
//            case 'filterpaymentlist': { 
//                    $result = filterPaymentlistAPI($_GET['seller_id'],$_GET['status']);
//                    break;
//                }    
//            case 'refundlist':{
//                    $result = refundList($_GET['seller_id']);
//                    break;
//                }
//            case 'filterRefundlist':{
//                    $result = refundList($_GET['seller_id'],$_GET['refund_status']);
//                    break;
//                }
//            case 'refundDetail':{
//                    $result = refundDetailAPI($_GET['seller_id'],$_GET['refund_id']);
//                    break;
//                }            
//            case 'updateproductdetail': {   
//                    $result = productupdateAPI();
//                    break;    
//                }
//            case 'reviewlist': { 
//                    $result = reviewlistAPI($_GET['seller_id']);
//                    break;
//                }    
//            case 'messagelist': { 
//                    $result = messagelistAPI($_GET['seller_id']);
//                    break;
//                }
//            case 'updateorderstatus': { 
//                    $result = updateorderStatusAPI($_GET['order_id'],$_GET['status_id']);
//                    break;
//                }
//            case 'changepassword': { 
//                    $result = changepasswordAPI($_GET['artist_id'],$_GET['old_password'],$_GET['new_password']);
//                    break;
//                }
//            case 'changeRefundStatus': { 
//                    $result = changeRefundStatusAPI();
//                    break;
//                }
//            case 'requestforpayment': { 
//                    $result = requestForPaymentAPI($_GET['order_list'],$_GET['seller_id']);
//                    break;
//                }      
//            case 'getUnreadCount': { 
//                    $result = getUnreadCountAPI($_GET['seller_id']);
//                    break;
//                }
//            case 'deleteProduct': { 
//                    $result = deleteProductAPI($_GET['product_id']);
//                    break;
//                } 
//            case 'deleteMessage': { 
//                    $result = deleteSellerMessageAPI($_GET['message_id']);
//                    break;
//                }  
//            case 'categoryList':{
//                    $result = getCategoriesAPI();
//                    break;
//                }
//            case 'subCategoryList':{
//                    $result = getCategoriesAPI($_GET['category_id']);
//                    break;
//                }    
//            case 'addProduct':{
//                    $result = addProductAPI();
//                    break;
//                } 
//            case 'sellerProfile':{
//                $result = sellerProfileAPI($_GET['seller_id']);
//                break;
//            }           
//            case 'editSellerProfile':{
//                $result = editSellerProfileAPI();
//                break;
//            }        
            default: {
                    $result['status'] = 0;
                    $result['message'] = "Not a valid request";
                    $result['data'] = array();
                    break;
                }
        }
        
}
//     }else{
//          $result['status'] = 0;
//          $result['message'] ="Invalid Access Token";
//          $result['data']   = array();
//     }
} else {
    $result['status'] = 0;
    $result['message'] ="Not a valid request";
    $result['data'] = array();
}
print_r(json_encode($result));
//echo $result;



    //---- Seller Login API ---//
    function loginAPI($username=NULL,$password=NULL,$lang_id='') { 
    global $conn,$device_id,$device_type;
    include_once("apifunctions.php");
    include_once("language.php");
    $txtUserName = addslashes($username);
    $response = isValidLogin($txtUserName,$password,'',$device_id,$device_type);
        return $response;
   
}


    function logoutAPI()
    {

    }
    function registerAPI($request_array,$autologin=1,$lang_id='')
    {
        global $conn,$sitestyle,$logourl,$device_id,$device_type;
         include_once("apifunctions.php");
         include_once("language.php");
         $message='';
        $refBy=$vAdvEmployee=$var_refid=NULL;    
        $response_array=array();
        $lang_id = 1;//fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'preferred_language', "WHERE nUserId='" . $_SESSION["guserid"] . "'"), 'preferred_language');


        $vLoginName = addslashes($request_array['login_name']);
        $vPassword = addslashes($request_array['password']);
        $vEmail = addslashes($request_array['email']);
        $nlstatus = $request_array["chk_newsletters"];
    //    $plan=isset($request_array['ddlPlan'])?$request_array['ddlPlan']:'';
        if ($nlstatus) {
            $nlstatus = "Y";
        }//end if
        else {
            $nlstatus = "N";
        }


            $sqluserexists = "SELECT vLoginName FROM " . TABLEPREFIX . "users  WHERE vLoginName = '" . addslashes($vLoginName) . "' AND vDelStatus!='1'";
            $resultuserexists = mysqli_query($conn, $sqluserexists) or die(mysqli_error($conn));

            //check for duplicate email
            $sqlemailexists = "SELECT vEmail FROM " . TABLEPREFIX . "users  WHERE vEmail = '" . addslashes($vEmail) . "' AND vDelStatus!='1'";
            $resultemailexists = mysqli_query($conn, $sqlemailexists) or die(mysqli_error($conn));

            if (mysqli_num_rows($resultuserexists) > 0) {
                $message = ERROR_USERNAME_EXIST;
                $notregistered = "1";
                $msgClass   =   'error_msg';
            }//end if
            else if (mysqli_num_rows($resultemailexists) > 0) {
                $message = ERROR_EMAIL_EXIST;
                $notregistered = "1";
                $msgClass   =   'error_msg';
            } // if username valid
            else if (!isValidUsername($vLoginName)) {
                $message = ERROR_USERNAME_INVALID_NO_SPECIAL_CHARS;
                $notregistered = "1";
                $msgClass   =   'error_msg';
            }//end if
            else {
                $notregistered = "0";
            }
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $free_registration=false;
        $sql = "Select vLookUpDesc from " . TABLEPREFIX . "lookup where nLookUpCode = '15' and vLookUpDesc='1'";
                                                                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                                    if (mysqli_num_rows($result) > 0) {
                                                                        $free_registration=1;
                                                                    }


        if($free_registration && $notregistered == "0")
        { 
            $approval_tag = "0";

            if (DisplayLookUp('userapproval') != '') {
                $approval_tag = DisplayLookUp('userapproval');
            }//end if
            //approve by admin
//            if ($approval_tag == "1") $vStatus = 1;
//            else if ($approval_tag == "E") $vStatus = 4;
//            else $vStatus = 0;
            $vStatus = 0;
            // database entry
            $sql = "INSERT INTO " . TABLEPREFIX . "users(vLoginName,vPassword,vStatus,vEmail,dDateReg,vMethod,nAmount,vTxnId,vDelStatus,nRefId,vNLStatus,vAdvEmployee)";
            $sql .= " Values('" . addslashes($vLoginName) . "',";
            $sql .= "'" . md5(addslashes($vPassword)) . "',";
            $sql .= "'" . addslashes($vStatus) . "',";
            $sql .= "'" . addslashes($vEmail) . "',";
            $sql .= "now(),";
            $sql .= "'free','0','free','0',";
            $sql .= (empty($refBy)) ? "NULL," : "'" . $refBy . "',";
            $sql .= "'" . addslashes($nlstatus) . "',";
            $sql .= "'".$vAdvEmployee."')";                       

            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $uid = mysqli_insert_id($conn);



            $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$lang_id."'";
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
                   AND L.lang_id = '".$lang_id."'";
            }else{
                $mailSql = "SELECT L.content,L.content_title
                  FROM ".TABLEPREFIX."content C
                  JOIN ".TABLEPREFIX."content_lang L
                    ON C.content_id = L.content_id
                   AND C.content_name = 'welcomeMailUser'
                   AND C.content_type = 'email'
                   AND L.lang_id = '".$lang_id."'";
            }
            $activate_link = '<a href="' . SITE_URL . '/activation.php?uid=' . $uid . '&status=eactivate">Activate</a>';
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($vLoginName),$vPassword,$activate_link );
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

            $StyleContent   =  MailStyle($sitestyle,SITE_URL);

            $EMail = $vEmail;

            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, addslashes($vLoginName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody    = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody    = str_replace($arrSearch, $arrReplace, $msgBody);



            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

            $sql = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, 
                                                    nSaleId) VALUES ('R', 'free', ' 0', 'free',now(), $uid, '')";

            mysqli_query($conn, $sql) or die(mysqli_error($conn));

            // mail send to admin
            if (DisplayLookUp('4') != '') {
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
                   AND L.lang_id = '".$lang_id."'";

            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];

            $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($vLoginName),htmlentities($vLoginName),$vEmail );
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
            $StyleContent=MailStyle($sitestyle,SITE_URL);


            $EMail = $var_admin_email;    

            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Administrator', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);


            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

             if($autologin)
                {
                    $response_array=isValidLogin($vLoginName,$vPassword,'',$device_id,$device_type);
                }
                else
                {
                   $response_array=array('login_name'=>$vLoginName,'plan'=>'');
                }
        }

        ///////////////////////////// paid registration///////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////

        if(!$free_registration && $notregistered == "0")
        {

            $condReg = "where nPlanId='" . $request_array['ddlPlan'] . "'";
            $PlanMode = fetchSingleValue(select_rows(TABLEPREFIX . 'plan', 'vPeriods', $condReg), 'vPeriods');
            $PlanAmount = fetchSingleValue(select_rows(TABLEPREFIX . 'plan', 'nPrice', $condReg), 'nPrice');

            $isFree = 0; 
            if(DisplayLookUp('plan_system') == 'yes'){
                if($PlanMode == 'F'){
                    $isFree = 1;
                }
            }else{
                if(DisplayLookUp('3') == '0'){
                    $isFree = 1;
                }
            }


            if($isFree){
                $approval_tag = "0";

                if (DisplayLookUp('userapproval') != '') {
                    $approval_tag = DisplayLookUp('userapproval');
                }//end if
                //approve by admin
//                if ($approval_tag == "1") $vStatus = 1;
//                else if ($approval_tag == "E") $vStatus = 4;
//                else $vStatus = 0;
                    $vStatus = 0;
                    // database entry
                    $sql = "INSERT INTO " . TABLEPREFIX . "users(vLoginName,vPassword,vEmail,vStatus,vMethod,dDateReg,nAmount,vTxnId,vDelStatus,vNLStatus,nPlanId,nRefId,vAdvEmployee)";
                    $sql .= " Values('" . addslashes($vLoginName) . "',";
                    $sql .= "'" . md5(addslashes($vPassword)) . "',";
                    $sql .= "'" . addslashes($vEmail) . "',";
                    $sql .= "'".$vStatus."',";
                    $sql .= "'free',";
                    $sql .= "now(),";
                    $sql .= "'0',";
                    $sql .= "'free','0',";
                    $sql .= "'" . addslashes($nlstatus) . "',";
                    $sql .= "'" . $request_array['ddlPlan'] . "','" . $var_refid . "','".$vAdvEmployee."')";

                mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $uid = mysqli_insert_id($conn);


                //send mail to user
                $EMail = $vEmail;

                /*
                * Fetch user language details
                */

                $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$lang_id."'";
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
                       AND L.lang_id = '".$lang_id."'";
                }else{
                    $mailSql = "SELECT L.content,L.content_title
                      FROM ".TABLEPREFIX."content C
                      JOIN ".TABLEPREFIX."content_lang L
                        ON C.content_id = L.content_id
                       AND C.content_name = 'welcomeMailUser'
                       AND C.content_type = 'email'
                       AND L.lang_id = '".$lang_id."'";
                }
                $activate_link = '<a style="color:black !important;" href="' . SITE_URL . '/activation.php?uid=' . $uid . '&status=eactivate">Activate</a>';
                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                $mailRw  = mysqli_fetch_array($mailRs);

                $mainTextShow   = $mailRw['content'];

                $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
                $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($vLoginName),$vPassword,$activate_link );
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent1   = $mainTextShow;

                $subject    = $mailRw['content_title'];
                $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

                $StyleContent   =  MailStyle($sitestyle,SITE_URL);
                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, addslashes($vLoginName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

                $sql = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId, 
                                                                    nSaleId,vPlanStatus, nPlanId) VALUES ('R', 'free', ' 0', 'free',now(), $uid, '','A', '".$request_array['ddlPlan']."')";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

                // mail send to admin
                if (DisplayLookUp('4') != '') {
                    $var_admin_email = DisplayLookUp('4');
                }//end if


                $EMail = $var_admin_email;

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
                       AND L.lang_id = '".$lang_id."'";

                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                $mailRw  = mysqli_fetch_array($mailRs);

                $mainTextShow   = $mailRw['content'];

                $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
                $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($vLoginName),htmlentities($vLoginName),$vEmail );
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent1   = $mainTextShow;

                $subject    = $mailRw['content_title'];
                $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
                $StyleContent=MailStyle($sitestyle,SITE_URL);

                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Administrator', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
                 if($autologin)
                {
                    $response_array=isValidLogin($vLoginName,$vPassword,'',$device_id,$device_type);
                }
                else
                {
                          $response_array=array('login_name'=>$vLoginName,'user_id'=>$uid,'password'=>  base64_encode($vPassword),'plan'=>$request_array['ddlPlan'],'plan_mode'=>$PlanMode,'method'=>'free','plan_amount'=>'0');
                }

            }//end if free plan mode check
            else {
                $sql = "INSERT INTO " . TABLEPREFIX . "users(vLoginName,vPassword,vEmail,dDateReg,vStatus,nPlanId,vDelStatus,nRefId,vNLStatus,vAdvEmployee)";
                $sql .= " VALUES ('" . addslashes($vLoginName) . "',
                            '" . md5(addslashes($vPassword)) . "',
                            '" . addslashes($vEmail) . "',
                            now(),'0','" . $request_array['ddlPlan'] . "','0','" . $refBy . "','".$nlstatus."','".$vAdvEmployee."')";


                mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $uid = mysqli_insert_id($conn);



                $notregistered = "0";

                if($autologin)
                {
                    $response_array=isValidLogin($vLoginName,$vPassword,'',$device_id,$device_type);
                }
                else
                {
                     $response_array=array('login_name'=>$vLoginName,'user_id'=>$uid,'password'=>  base64_encode($vPassword),'plan'=>$request_array['ddlPlan'],'plan_mode'=>$PlanMode,'method'=>'','plan_amount'=>$PlanAmount);
  
                }
     
            }//end else



        }
        $status=1;
        if($message !='')
        {
            $status=0;
        }
        $response=array('status'=>$status,'error'=>$message,'data'=>$response_array);
        return $response;
    }

    function registration_paymentAPI($user_id,$password_enrypted,$tx_token,$lang_id)
    {
        global $conn,$sitestyle,$logourl;
        $lang_id=1;
        $password=  base64_decode($password_enrypted);
    //    $sitestyle=$logourl=NULL;
         include_once("apifunctions.php");
         include_once("language.php");
         $approval_tag = "0";
         if (DisplayLookUp('userapproval') != '') {
                $approval_tag = DisplayLookUp('userapproval');
         }

         $plan_details_sql="select ep.nPlanId,nPrice,vPeriods from es_users eu inner join es_plan ep on eu.nPlanId=ep.nPlanId where eu.nUserId=".$user_id;
         $plan_details = @mysqli_query($conn, $plan_details_sql) or die(mysqli_error($conn));
         $plan_details_array = @mysqli_fetch_array($plan_details);
         $plan_id=$plan_details_array['nPlanId'];
         $plan_amount=$plan_details_array['nPrice'];
         $plan_mode=$plan_details_array['vPeriods'];
         if($keyarray=paypal_success($tx_token))
         {
          $txnid=$keyarray['txn_id'];     
        $var_id = $user_id;
        $var_amount = "";
        $var_txnid = "";
        $var_method = "";
        $var_login_name = "";
        $var_password = "";
        $var_first_name = "";
        $var_last_name = "";
        $var_date = "";
        $status=1;
        $var_txnid = $txnid;

        $sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
        $sql .="vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
        $sql .="vDescription  ,dDateReg   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee,nRefId
                                            from " . TABLEPREFIX . "users where nUserId='" . $var_id . "' and vDelStatus = '0'";

        $result = @mysqli_query($conn, $sql) or die(mysqli_error($conn));

        if (@mysqli_num_rows($result) > 0) {       //If data is there in the temp table
            if ($row = @mysqli_fetch_array($result)) {
                $sqltxn = "Select * from " . TABLEPREFIX . "payment where vTxn_id ='$txnid' AND vTxn_mode='pp'";

                $resulttxn = @mysqli_query($conn, $sqltxn) or die(mysqli_error($conn));
                if (@mysqli_num_rows($resulttxn) <= 0) {  // the tran id not present in the database
                    $var_login_name = $row["vLoginName"];
                    $var_password = $row["vPassword"];
                    $var_first_name = $row["vFirstName"];
                    $var_last_name = $row["vLastName"];
                    $var_email = $row["vEmail"];
                    $totalamt = $row["nAmount"];
                    $paytype = $row["vMethod"];
                    $now = $var_date = date('m/d/Y');
                    $userUpdate = '';
                    $payTableField = '';
                    $payTableFieldValue = '';

                    //if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
                    if (DisplayLookUp('15') != '1' && DisplayLookUp('plan_system') == 'yes') {
                        //calculate end date
                        switch ($plan_mode) {
                            case "M":
                                $addInterval = 'MONTH';
                                break;

                            case "Y":
                                $addInterval = 'YEAR';
                                break;
                        }//end switch

                        $expDate = mysqli_query($conn, "SELECT DATE_ADD(now(),INTERVAL 1 " . $addInterval . ") as expPlanDate") or die(mysqli_error($conn));
                        if (mysqli_num_rows($expDate) > 0) {
                            $nExpDate = mysqli_result($expDate, 0, 'expPlanDate');
                        }//end if

                        $userUpdate = ",dPlanExpDate='" . $nExpDate . "'";

                        //add one field in payment table
                        $payTableField = ',vPlanStatus,nPlanId';
                        $payTableFieldValue = ",'A','" . $plan_id . "'";
                        $totalamt = $plan_amount;
                    }//end if register mode and escrow checking

                    if ($approval_tag == "1") {
                        $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',vDelStatus='0' " . $userUpdate . "
                                                                                    WHERE nUserId='" . $row['nUserId'] . "'";
                    }//end if
                    if ($approval_tag == "E") {
                        $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',
                                                                                            vStatus='4',vDelStatus='0' " . $userUpdate . " WHERE nUserId='" . $row['nUserId'] . "'";
                    }//end if
                    else {
                        $sql = "UPDATE " . TABLEPREFIX . "users SET dDateReg=now(),vTxnId='" . addslashes($var_txnid) . "',
                                                                            vStatus='0',vDelStatus='0' " . $userUpdate . " WHERE nUserId='" . $row['nUserId'] . "'";
                    }//end else

                    @mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    $var_new_id = @mysqli_insert_id($conn);

                    //Addition for referrals
    //                $var_reg_amount = 0;
    //
    //                if ($row["nRefId"] != "0") {
    //                    $sql = "Select nRefId,nUserId,nRegAmount from " . TABLEPREFIX . "referrals where vRegStatus='0' AND nRefId='" . $row["nRefId"] . "'";
    //                    $result_test = @mysqli_query($conn, $sql) or die(mysqli_error($conn));
    //
    //                    if (@mysqli_num_rows($result_test) > 0) {
    //                        if ($row_final = @mysqli_fetch_array($result_test)) {
    //                            $var_reg_amount = $row_final["nRegAmount"];
    //
    //                            $sql = "Update " . TABLEPREFIX . "referrals set vRegStatus='1',";
    //                            $sql .= "nUserRegId='" . $var_new_id . "',dRegDate=now() where nRefId='" . $row_final["nRefId"] . "'";
    //
    //                            @mysqli_query($conn, $sql) or die(mysqli_error($conn));
    //
    //                            $sql = "Select nUserId from " . TABLEPREFIX . "user_referral where nUserId='" . $row_final["nUserId"] . "'";
    //                            $result_ur = @mysqli_query($conn, $sql) or die(mysqli_error($conn));
    //                            if (mysqli_num_rows($result_ur) > 0) {
    //                                $sql = "Update " . TABLEPREFIX . "user_referral set nRegCount = nRegCount + 1,nRegAmount=nRegAmount + $var_reg_amount where nUserId='" . $row_final["nUserId"] . "'";
    //                            }//end if
    //                            else {
    //                                $sql = "insert into " . TABLEPREFIX . "user_referral(nUserId,nRegCount,nRegAmount) values('"
    //                                        . $row_final["nUserId"] . "','1','$var_reg_amount')";
    //                            }//end else
    //                            @mysqli_query($conn, $sql) or die(mysqli_error($conn));
    //                        }//end if
    //                    }//end if
    //                }//end if
    //                //end of referrals

    //                $_SESSION["gtempid"] = "";

                    /*
                    * Fetch user language details
                    */

                    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$lang_id."'";
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
                           AND L.lang_id = '".$lang_id."'";
                    }else{
                        $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'welcomeMailUser'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$lang_id."'";
                    }
                    $activate_link = '<a href="' . SITE_URL . '/activation.php?uid=' . $var_new_id . '&status=eactivate">Activate</a>';
                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                    $mailRw  = mysqli_fetch_array($mailRs);

                    $mainTextShow   = $mailRw['content'];
                    if(!$password){
                        $mainTextShow = str_replace("{Password}", '', $mainTextShow);
                        $mainTextShow = str_replace("Password", '', $mainTextShow);
                    }

                    $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}");
                    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($row["vLoginName"]),$password,$activate_link );
                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                    $mailcontent1   = $mainTextShow;

                    $subject    = $mailRw['content_title'];
                    $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

                    $StyleContent=MailStyle($sitestyle,SITE_URL);              

                    $EMail = $var_email;

                    //readf file n replace
                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                    $msgBody    = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                    send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

                    $user_id = $var_new_id;
                    /* get the invoice number */
                    $sql1 = "Select  LPAD(MAX(FORMAT(vInvno,0))+1,6,'0')  as maxinvid from " . TABLEPREFIX . "payment ";
                    $result1 = @mysqli_query($conn, $sql1) or die(mysqli_error($conn));
                    $row1 = @mysqli_fetch_array($result1);
                    $Inv_id = $row1['maxinvid'];

    //                $user_id = ($_SESSION["guserid"] != '') ? $_SESSION["guserid"] : $_SESSION["gtempid"];

                    $sqlPayment = "INSERT INTO " . TABLEPREFIX . "payment (vTxn_type, vTxn_id, nTxn_amount, vTxn_mode, dTxn_date, nUserId,
                                                            nSaleId,vInvno " . $payTableField . ") VALUES ('R', '$txnid', ' $totalamt', '$paytype',now(), '" . $user_id . "',
                                                            '','$Inv_id' " . $payTableFieldValue . ")";
                    $result = @mysqli_query($conn, $sqlPayment) or die(mysqli_error($conn));

                    $var_admin_email = SITE_NAME;

                    if (DisplayLookUp('4') != '') {
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
                           AND L.lang_id = '".$lang_id."'";

                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                    $mailRw  = mysqli_fetch_array($mailRs);

                    $mainTextShow   = $mailRw['content'];

                    $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
                    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($var_login_name),htmlentities($var_first_name),$var_email );
                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                    $mailcontent1   = $mainTextShow;

                    $subject2    = $mailRw['content_title'];
                    $subject2    = str_replace('{SITE_NAME}',SITE_NAME,$subject2);
                    $StyleContent=MailStyle($sitestyle,SITE_URL);

                    $EMail = $var_admin_email;


                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject2);
                    $msgBody_ad = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody_ad = str_replace($arrSearch, $arrReplace, $msgBody_ad);

                    send_mail($EMail, $subject2, $msgBody_ad, SITE_EMAIL, 'Admin');

                    //now send the mail containing the link to get the pin
    //                $_SESSION["guserid"] = "";
    //                $plan_id = '';
    //                $plan_mode = '';
    //                $_SESSION['sess_Plan_Amt'] = '';

                    $flag = true;

                    if ($approval_tag == "1") {
                        $message =  str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT_AFTER_ADMIN_APPROVAL);
                    }//end if
                    if ($approval_tag == "E") {
                        $message = str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT_AFTER_EMAIL_VERIFICATION);
                    }//end if
                    if ($approval_tag == "0") {
                        $message = str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT_NOW) . "<br>&nbsp;<br><a href='login.php'>".LINK_CLICK_LOGIN."</a>";
                    }//end if
                }//end if
                else {
                    $message = str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT);
                }//end else
            }//end if
        }//end if
        else {  //If the data is not present in the temperory table
            $var_id = $user_id;
            $uname = $keyarray['option_selection2'];
            $txnid = $keyarray['txn_id'];
            $var_txnid = $txnid;


            $sql = "Select nUserId,vLoginName,vPassword,vFirstName ,vLastName  ,vAddress1  ,vAddress2  ,vCity  , ";
            $sql .="vState ,vCountry ,nZip , vPhone ,vFax  ,vEmail ,vUrl , vGender  ,vEducation,";
            $sql .="vDescription  ,dDateReg   ,nAffiliateId, vMethod,nAmount,vAdvSource,vAdvEmployee from " . TABLEPREFIX . "users where vLoginName='" . addslashes($uname) . "'";

            $result = @mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (@mysqli_num_rows($result) > 0) {
                if ($row = @mysqli_fetch_array($result)) {
                    $var_login_name = $row["vLoginName"];
                    $var_password = $row["vPassword"];
                    $var_first_name = $row["vFirstName"];
                    $var_last_name = $row["vLastName"];
                    $var_email = $row["vEmail"];
                    $totalamt = $row["nAmount"];
                    $paytype = $row["vMethod"];
                    $now = $var_date = date('m-d-Y');

    //                $_SESSION["gtempid"] = "";

                    //if (DisplayLookUp('15') != '1' && DisplayLookUp('Enable Escrow') != 'Yes') {
                    if (DisplayLookUp('15') != '1' && DisplayLookUp('plan_system') == 'yes') {
                        $totalamt = $plan_amount;
                    }//end if register mode and escrow checking
                    /*
                    * Fetch user language details
                    */

                    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$lang_id."'";
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
                           AND L.lang_id = '".$lang_id."'";
                    }else{
                        $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'welcomeMailUser'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$lang_id."'";
                    }
                    $activate_link = '<a href="' . SITE_URL . '/activation.php?uid=' . $var_new_id . '&status=eactivate">Activate</a>';
                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                    $mailRw  = mysqli_fetch_array($mailRs);

                    $mainTextShow   = $mailRw['content'];
                    if(!$password)
                        $mainTextShow = str_replace("{Password}", '', $mainTextShow);

                    $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
                    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($row["vLoginName"]),$password,$activate_link );
                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                    $mailcontent1   = $mainTextShow;

                    $subject    = $mailRw['content_title'];
                    $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

                    $EMail = $var_email;

                    $mainTextShow   = $mailRw['content'];
                    if(!$password)
                        $mainTextShow = str_replace("{Password}", '', $mainTextShow);

                    //readf file n replace
                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Member', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                    $msgBody    = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                    //send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');//this is a duplicate mail

                    $var_admin_email = ADMIN_EMAIL;

                    if (DisplayLookUp('4') != '') {
                        $var_admin_email = DisplayLookUp('4');
                    }//end if

                   $EMail = $var_admin_email;

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
                           AND L.lang_id = '".$lang_id."'";

                    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                    $mailRw  = mysqli_fetch_array($mailRs);

                    $mainTextShow   = $mailRw['content'];

                    $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{var_login_name}","{var_first_name}","{var_email}",);
                    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($var_login_name),htmlentities($var_first_name),$var_email );
                    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                    $mailcontent1   = $mainTextShow;

                    $subject    = $mailRw['content_title'];
                    $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
                    $StyleContent=MailStyle($sitestyle,SITE_URL);

                    $EMail = $var_admin_email;
                    //readf file n replace
                    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Admin', $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                    $msgBody_ad    = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                    $msgBody_ad = str_replace($arrSearch, $arrReplace, $msgBody_ad);

                    //send_mail($EMail, $subject, $msgBody_ad, SITE_EMAIL, 'Admin');//this is a duplicate mail

                    $flag = true;
    //                $_SESSION["tmp_pd"] = '';
                    $message = MESSAGE_TRANSACTION_COMPLETED_MAIL_SENT_TO_YOU;
                    $message .="<br>&nbsp;<br>".str_replace("{site_url}",SITE_URL,MESSAGE_ACCESS_ACCOUNT);
                    if ($approval_tag == "1") {
                        $message .= "<br><br>".MESSAGE_LOGIN_ACCOUNT_AFTER_ADMIN_APPROVAL;
                    }//end if
                    if ($approval_tag == "E") {
                        $message .= "<br><br>".MESSAGE_LOGIN_ACCOUNT_AFTER_EMAIL_VERIFICATION;
                    }//end if
                }//end if
            }//end if
            else {
                $status=0;
                $message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;
             }//end else
            }//end if
         }
         else
         {
             $status=0;
                 $message = ERROR_COMMUNICATION_ERROR_WITH_PAYMENT_SERVER;

         }
         $response_data=array(); 
         $error=$message;
         if($status==1)
         {
            $response_data=array('success_message'=>$message);
            $error='';
         }
         return array('status'=>$status,'error'=>$error,'data'=>$response_data);

    }

    function forgot_passwordAPI($email_address,$lang_id='')
    {
        global $conn,$sitestyle,$logourl;
             include_once("apifunctions.php");
             include_once("language.php");

        if($lang_id ==""){
        $lang_id=1;
        }
         $sql = "Select vLoginName,vEmail,vPassword from " . TABLEPREFIX . "users where  vEmail='" . $email_address . "'";

        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $email = $row["vEmail"];
            $password = $row["vPassword"];
            $vloginname = $row["vLoginName"];

            /*
            * Fetch user language details
            */

            $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$lang_id."'";
            $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
            $langRw = mysqli_fetch_array($langRs);

            /*
            * Fetch email contents from content table
            */
           $mailSql = "SELECT L.content,L.content_title
                      FROM ".TABLEPREFIX."content C
                      JOIN ".TABLEPREFIX."content_lang L
                        ON C.content_id = L.content_id
                       AND C.content_name = 'forgotpass'
                       AND C.content_type = 'email'
                       AND L.lang_id = '".$lang_id."'";
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);

            $mainTextShow   = $mailRw['content'];
            $reset_link     = '<a href="' . SITE_URL . '/resetpass.php?id=' . $email_address . '&p=' . $password . '">' . SITE_URL . '/resetpass.php?id=' . $email_address . '&p=' . $password . '</a>';

            $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{reset_link}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,$reset_link);
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];

            $StyleContent=MailStyle($sitestyle,SITE_URL);

    //        $EMail = $admin_email;

            //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, $vloginname, $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody    = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody    = str_replace($arrSearch, $arrReplace, $msgBody);
            send_mail($email, $subject, $msgBody, SITE_EMAIL, 'Admin');

            $message = str_replace('{email}',$email_address,TEXT_MAIL_SENT_TO_ENABLE_PASSWORD_RESET);
            $status = 1;
        }//end if
        else {
            $message = ERROR_EMAIL_INVALID;
            $status = 0;
        }//end else 
        $response_data=array();
        $error=$message;
        if($status==1)
        {
            $response_data=array('success_message'=>$message);
            $error='';
        }

        return array('status'=>$status,'error'=>$error,'data'=>$response_data);

    }

    function plansAPI(){
        global $conn;
        $response_data=array();
        $status=$lang_id=1;
        $message='';
        include_once("apifunctions.php");
        include_once("language.php");
        $sqlPlan = mysqli_query($conn, "SELECT * FROM " . TABLEPREFIX . "plan P
                                    LEFT JOIN " . TABLEPREFIX . "plan_lang L on P.nPlanId = L.plan_id and L.lang_id = '" . $lang_id . "'
                                    WHERE P.vActive='1' order by P.nPosition ASC")
                             or die(mysqli_error($conn));
                                    if (mysqli_num_rows($sqlPlan) > 0) {
                                            while ($arrPlan = mysqli_fetch_array($sqlPlan)) {
                                                    switch ($arrPlan['vPeriods']) {
                                                            case "M":
                                                                    $year = 'Per Month';
                                                                    break;

                                                            case "Y":
                                                                    $year = 'Per Year';
                                                                    break;

                                                            case "F":
                                                                    $year = 'Free';
                                                                    break;
                                                    }//end switch
                                                    $response_data[$arrPlan['nPlanId']]=$arrPlan['vPlanName'] . ' ( ' . CURRENCY_CODE . $arrPlan['nPrice'] . ' - ' . $year . ')';
    //                                                $shwSelcted = '';
    //
    //                                                if ($arrPlan['nPlanId'] == $_POST['ddlPlan']) {
    //                                                        $shwSelcted = 'selected="selected"';
    //                                                }//end if

    //                                                echo '<option value="' . $arrPlan['nPlanId'] . '">' . $arrPlan['vPlanName'] . ' ( ' . CURRENCY_CODE . $arrPlan['nPrice'] . ' - ' . $year . ')</option>';
                                            }//end while loop
                                    }
                                    else
                                    {
                                        $status=0;
                                        $message=NO_PLANS_FOUND;
                                    }
                                    return array('status'=>$status,'error'=>$message,'data'=>$response_data);
    }
    
    /********************************* Category APIs ********************************/
    function retrieveCategoryAPI($cat_id,$lang_id){ 
        global $conn;
        $response_data      = array();
        $category_image_url = SITE_URL."/banners/"; 
        include_once("apifunctions.php");
        include_once("language.php");
        
        if(trim($cat_id) == ""){
            $message        = CATEGORY_ID_MISSING;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else if(!is_numeric($cat_id)){
            $message        = CATEGORY_ID_NUMERIC;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($lang_id) == ""){
            $lang_id = 1;
        }
                 
        /* Category retrieval starts here */        
        $query     = "SELECT "
                        . "C.`nCategoryId`,"
                        . "C.`nParentId`,"
                        . "C.`vRoute`,"
                        . "C.`nCount`,"
                        . "C.`nPosition`,"
                        . "C.`cat_image`,"
                        . "L.`lang_id`,"
                        . "L.`vCategoryDesc` "
                        . "FROM `".TABLEPREFIX."category` C "
                        . "LEFT JOIN `".TABLEPREFIX."category_lang` L ON C.`nCategoryId` = L.`cat_id` "
                        . "WHERE 1=1";   
        if(trim($cat_id) <> ""){
            $query .= " AND C.`nCategoryId` = '".trim($cat_id)."'";
        }
        if(trim($lang_id) <> ""){
            $query .= " AND L.`lang_id` = '".trim($lang_id)."'";
        }
        //echo $query;
        $dbArray       = mysqli_query($conn, $query);
        if (mysqli_num_rows($dbArray) > 0){
            $result = array();
            while($rlt = mysqli_fetch_array($dbArray,MYSQLI_ASSOC)){
                //echo "<pre>"; print_r($rlt); echo "</pre>";
                if(is_array($rlt) && count($rlt)>0){
                    $res = array();
                    foreach($rlt as $key=>$val){
                       switch($key){
                            case "nCategoryId" :                                                                   
                                $keyval = "category_id";
                                $value = $val;
                               break;
                            case "vCategoryDesc" :
                                $keyval = "category_name";
                                $value  = utf8_encode($val);
                                break; 
                            case "nParentId" :
                                $keyval = "parent_id";
                                $value  = $val;
                                break; 
                            case "cat_image" :
                                $keyval = "category_image";
                                if(trim($val) <> ""){
                                    $value  = $category_image_url.$val;
                                }else{
                                    $value  = $val;
                                }                                
                                break; 
                            case "vRoute" :
                                $keyval = "category_route";
                                $value  = $val;
                                break; 
                            case "nPosition" :
                                $keyval = "position";
                                $value  = $val;
                                break; 
                           default: 
                               $keyval  = $key;
                               $value   = $val;
                               break;
                       }
                       $res[$keyval] = $value;
                    } 
                    $result[] = $res;
                }                           
            }
            //echo "<pre>"; print_r($result); echo "</pre>";
            
            $message        = "";            
            $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
            return $responseArray;
        }else{
            $message        = "Invalid Category";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
    }
    
    function retrieveAllCategoryAPI($lang_id){  
        global $conn;  
        include_once("apifunctions.php");
        include_once("language.php");      
       
        if(trim($lang_id) == ""){
            $lang_id = 1;
        }
        $response_data      = array();        
        $category_image_url = SITE_URL."/banners/"; 
                
        /* Category retrieval starts here */        
        $query     = "SELECT "
                        . "C.`nCategoryId`,"
                        . "C.`nParentId`,"
                        . "C.`vRoute`,"
                        . "C.`nCount`,"
                        . "C.`nPosition`,"
                        . "C.`cat_image`,"
                        . "L.`lang_id`,"
                        . "L.`vCategoryDesc` "
                        . "FROM `".TABLEPREFIX."category` C "
                        . "LEFT JOIN `".TABLEPREFIX."category_lang` L ON C.`nCategoryId` = L.`cat_id` "
                        . "WHERE 1=1";           
        if(trim($lang_id) <> ""){
            $query .= " AND L.`lang_id` = '".trim($lang_id)."'";
        }
        $query .= " ORDER BY C.`nCategoryId` ASC";
        //echo $query;
        $dbArray       = mysqli_query($conn, $query);
        if (mysqli_num_rows($dbArray) > 0){
            $result = array();
            while($rlt = mysqli_fetch_array($dbArray,MYSQLI_ASSOC)){
                //echo "<pre>"; print_r($rlt); echo "</pre>";
                if(is_array($rlt) && count($rlt)>0){
                    $res = array();
                    foreach($rlt as $key=>$val){
                       switch($key){
                            case "nCategoryId" :                                                                   
                                $keyval = "category_id";
                                $value = $val;
                               break;
                            case "vCategoryDesc" :
                                $keyval = "category_name";
                                $value  = utf8_encode($val);
                                break; 
                            case "nParentId" :
                                $keyval = "parent_id";
                                $value  = $val;
                                break; 
                            case "cat_image" :
                                $keyval = "category_image";
                                if(trim($val) <> ""){
                                    $value  = $category_image_url.$val;
                                }else{
                                    $value  = $val;
                                }   
                                break; 
                            case "vRoute" :
                                $keyval = "category_route";
                                $value  = $val;
                                break; 
                            case "nPosition" :
                                $keyval = "position";
                                $value  = $val;
                                break; 
                           default: 
                               $keyval  = $key;
                               $value   = $val;
                               break;
                       }
                       $res[$keyval] = $value;
                    } 
                    $result[] = $res;
                }                           
            }
            //echo "<pre>"; print_r($result); echo "</pre>";
            
            $message        = "";            
            $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
            return $responseArray;
        }else{
            $message        = CATEGORIES_NOT_FOUND;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
    }
    /********************************* Category APIs ********************************/
    
    /********************************* Product APIs ********************************/
    function retrieveProductAPI($product_id,$lang_id,$type){    
        global $conn;  
        include_once("apifunctions.php");
        include_once("language.php");      
       
        if(trim($product_id) == ""){
            $message        = PRODUCT_ID.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else if(!is_numeric($product_id)){
            $message        = PRODUCT_ID.MUST_BE_NUMERIC;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else if($type == ""){
            $message        = PRODUCT_TYPE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else if(!is_numeric($type)){
            $message        = PRODUCT_TYPE.MUST_BE_NUMERIC;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($lang_id) == ""){
            $lang_id = 1;
        }
        $response_data      = array();        
        $product_image_url  = SITE_URL; 
        $default_image  = "images/nophoto.gif";
          
        $db_name=($type==3)?'sale':'swap';
        $primary_key=($type==3)?'nSaleId':'nSwapId';
        $posttype=($type==3)?"'sale' as vPostType":"s.vPostType";
        $swapstatus=($type==3)?"":"s.vSwapStatus,";
        /* Product retrieval starts here */   
        $query = "SELECT 
                    s.".$primary_key.",
                    s.vTitle,
                    s.vBrand,
                    date_format(s.dPostDate,'%m/%d/%Y') as 'dPostDate',
                    s.vUrl,
                    s.vSmlImg,
                    s.vType,
                    s.vCondition,
                    s.vYear,
                    s.nValue,
                    s.nShipping,
                    s.vUrl,
                    s.vDescription,
                    s.nQuantity,
                    s.nPoint,
                    s.vImgDes,
                    s.vSmlImg,
                    $posttype,
                    s.vFeatured,
                    c.nCategoryId,
                    L.vCategoryDesc,
                    $swapstatus
                    s.vDelStatus,
                    u.vLoginName as 'UserName',
                    u.nUserId as 'UsersId'
                    FROM ".TABLEPREFIX.$db_name." s
                LEFT JOIN ".TABLEPREFIX."category c on s.nCategoryId = c.nCategoryId 
                LEFT JOIN ".TABLEPREFIX."users u on s.nUserId=u.nUserId
                LEFT JOIN ".TABLEPREFIX."category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '".$lang_id."' 
                    WHERE 1=1";        
        if(trim($product_id) <> ""){
            $query .= " AND s.".$primary_key." = '".trim($product_id)."'";
        }
        if(trim($lang_id) <> ""){
            $query .= " AND L.`lang_id` = '".trim($lang_id)."'";
        }
        $query .= " AND s.vDelStatus =  '0' ORDER BY s.dPostDate DESC ";        
        //echo $query;
        
        $dbArray       = mysqli_query($conn, $query);
        if (mysqli_num_rows($dbArray) > 0){
            $result = array();
            while($rlt = mysqli_fetch_array($dbArray,MYSQLI_ASSOC)){
                //echo "<pre>"; print_r($rlt); echo "</pre>";
                if(is_array($rlt) && count($rlt)>0){
                    $res = array();
                    foreach($rlt as $key=>$val){
                       switch($key){
                            case "nCategoryId" :                                                                   
                                $keyval = "category_id";
                                $value = $val;
                               break;
                           case "vCategoryDesc" :                                                                   
                                $keyval = "category_name";
                                $value = utf8_encode($val);
                               break;
                            case "vTitle" :
                                $keyval = "product_name";
                                $value  = utf8_encode($val);
                                break; 
                            case "dPostDate" :
                                $keyval = "posted_date";
                                $value  = $val;
                                break;
                            case "vUrl" :
                                $keyval = "product_image";
                                if(trim($val) <> ""){
                                    $value  = $product_image_url.$val;
                                }else{
                                    $value  = $product_image_url.$default_image;
                                }   
                                break;   
                                
                          case "nValue" :
                                $keyval = "price";
                                $value  = $val;
                                break;  
                            case "nShipping" :
                                $keyval = "shipping";
                                $value  = $val;
                                break;  
                            case "nPoint" :
                                $keyval = "point";
                                $value  = $val;
                                break;  
                              case "vUrl" :
                                $keyval = "url";
                                $value  = $val;
                                break;  
                             case "vDescription" :
                                $keyval = "description";
                                $value  = $val;
                                break;  
                             case "nQuantity" :
                                $keyval = "quantity";
                                $value  = $val;
                                break;  
                            case $primary_key :
                                $keyval = "product_id";
                                $value  = $val;
                                break; 
                            case "vSwapStatus" :
                                $keyval = "swap_status";
                                $value  = $val;
                                break; 
                            case "vFeatured" :
                                $keyval = "is_featured";
                                $value  = $val;
                                break;  
                            case "vDelStatus" :
                                $keyval = "delete_status";
                                $value  = $val;
                                break; 
                            case "UserName" :
                                $keyval = "user_name";
                                $value  = $val;
                                break; 
                            case "UsersId" :
                                $keyval = "users_id";
                                $value  = $val;
                                break; 
                            case "vBrand" :
                                $keyval = "brand_name";
                                $value  = $val;
                                break;
                            case "vType" :
                                $keyval = "product_type";
                                $value  = $val;
                                break;
                            case "vCondition" :
                                $keyval = "condition";
                                $value  = $val;
                                break;
                            case "vYear" :
                                $keyval = "manufacturing_year";
                                $value  = $val;
                                break;
                            case "vPostType" :
                                $keyval = "type";
                                $value  = $val;
                                break;
                            case "vSmlImg" :
                                $keyval = "thumb_image";
                                if(trim($val) <> ""){
                                    $value  = $product_image_url.$val;
                                }else{
                                    $value  = $val;
                                }   
                                break;
                           default:    
                               $keyval  = $key;
                               $value   = $val;
                               break;
                       }
                       $res[$keyval] = $value;
                    } 
                    $result[] = $res;
                }                           
            }
            //echo "<pre>"; print_r($result); echo "</pre>";
            
            $message        = "";            
            $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
            return $responseArray;
        }else{
            $message        = NO_PRODUCTS_FOUND;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
    }
    
    function retrieveAllProductsAPI($type,$lang_id,$userid='',$logged = 0,$cat_id='',$search_term='',$swap_status='',$latitude='',$longitude=''){//echo "hello";exit;
        //error_reporting(E_ALL);
        //ini_set('display_errors', 1);
        global $conn; 
        include_once("apifunctions.php");
        include_once("language.php");       
       if(trim($search_term) == "" && trim($cat_id) == ""){ //// if searchin products all types should be retreived
        if(trim($type) == ""){
            $message        = PRODUCT_TYPE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
       }
        if(trim($lang_id) == ""){
            $lang_id = 1;
        }
        $response_data      = array();        
        $product_image_url  = SITE_URL; 
         $default_image  = "images/nophoto.jpg";   
         
         $db_name=($type==3)?'sale':'swap';
        $primary_key=($type==3)?'nSaleId':'nSwapId';
        $posttype=($type==3)?"'sale' as vPostType":"s.vPostType";
        $swapstatus=($type==3)?"":"s.vSwapStatus,";
        $having = '';
        if(!empty($latitude) && !empty($longitude) ){ 
            $radius_km = 10;  // static 10 kms given , so need to replace it 
             
            //$having = " AND (ST_Distance_Sphere(point(-".$longitude.", ".$latitude."),point(-u.longitude, u.latitude)) <=".$radius_km."*1000) "; 
            $having = " AND ROUND (( 6371 * acos( cos( radians($latitude) ) * cos( radians(u.latitude) ) * cos( radians( u.longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( u.latitude ) ) ) ) ,2) <=".$radius_km;
             
        }
        /* Product retrieval starts here */   
        $query = "SELECT 
                    s.$primary_key,
                    s.vTitle,
                    s.vBrand,
                    date_format(s.dPostDate,'%m/%d/%Y') as 'dPostDate',
                    s.vUrl,
                    s.vSmlImg,
                    s.vType,
                    s.vCondition,
                    s.vDescription,
                    s.vYear,
                    $posttype,
                    s.vFeatured,
                    c.nCategoryId,
                    L.vCategoryDesc,
                    $swapstatus
                    s.vDelStatus,
                    u.vLoginName as 'UserName'
                    FROM ".TABLEPREFIX."$db_name s
                LEFT JOIN ".TABLEPREFIX."category c on s.nCategoryId = c.nCategoryId 
                LEFT JOIN ".TABLEPREFIX."users u on s.nUserId=u.nUserId
                 JOIN ".TABLEPREFIX."category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '".$lang_id."' 
                    WHERE 1=1";
        if($having){
            $query = $query.$having;
        }            
        if(trim($type) <> ""){
            if(trim($type) == 1 || (trim($type) == 2 && $swap_status == 1)){
                $query .= " AND s.`vPostType` = 'swap'";
            }
            else if(trim($type) == 2){
                $query .= " AND s.`vPostType` = 'wish'";
            }
//            else if(trim($type) == 3){
//                $query .= " AND s.`vPostType` = 'sale'";
//            }
        }
        if(trim($cat_id)<>"")
        {
             $query .= " AND s.`nCategoryId` = $cat_id";
        }
        
        if(trim($userid)<>"" && $logged == 1)
        {
             $query .= " AND s.`nUserId` = $userid";
        }
        else if(trim($userid)<>"" && $logged == 0)
        {
            $query .= " AND s.`nUserId` != $userid";
        }
        
        if(trim($search_term) <> "")
        {
            $query.=" AND ("
            ."s.vTitle like '%".$search_term
            ."%' OR s.vBrand like '%".$search_term
            ."%' OR s.vDescription like '%".$search_term
            ."%')";
        }
        if(trim($type) !=3){
                $query .= " AND s.vSwapStatus='0'";
            }
        $query .= " AND s.vDelStatus =  '0' ORDER BY s.dPostDate DESC ";   
//        return $query;
        $dbArray       = mysqli_query($conn, $query);
        if (mysqli_num_rows($dbArray) > 0){
            $result = array();
            while($rlt = mysqli_fetch_array($dbArray,MYSQLI_ASSOC)){
                //echo "<pre>"; print_r($rlt); echo "</pre>";
                if(is_array($rlt) && count($rlt)>0){
                    $res = array();
                    foreach($rlt as $key=>$val){
                       switch($key){
                            case "nCategoryId" :                                                                   
                                $keyval = "category_id";
                                $value = $val;
                               break;
                           case "vCategoryDesc" :                                                                   
                                $keyval = "category_name";
                                $value = utf8_encode($val);
                               break;
                            case "vTitle" :
                                $keyval = "product_name";
                                $value  = utf8_encode($val);
                                break; 
                            case "dPostDate" :
                                $keyval = "posted_date";
                                $value  = $val;
                                break;
                            case "vUrl" :
                                $keyval = "product_image";
                                if(trim($val) <> ""){
                                    $value  = $product_image_url.$val;
                                }else{
                                     $value  = $product_image_url.$default_image;
                                }   
                                break;                             
                            case $primary_key  :
                                $keyval = "product_id";
                                $value  = $val;
                                break; 
                            case "vSwapStatus" :
                                $keyval = "swap_status";
                                $value  = $val;
                                break; 
                            case "vFeatured" :
                                $keyval = "is_featured";
                                $value  = $val;
                                break;  
                            case "vDelStatus" :
                                $keyval = "delete_status";
                                $value  = $val;
                                break; 
                            case "UserName" :
                                $keyval = "user_name";
                                $value  = $val;
                                break; 
                            case "vBrand" :
                                $keyval = "brand_name";
                                $value  = $val;
                                break;
                            case "vType" :
                                $keyval = "product_type";
                                $value  = $val;
                                break;
                            case "vCondition" :
                                $keyval = "condition";
                                $value  = $val;
                                break;
                             case "vDescription" :
                                $keyval = "description";
                                $value  = $val;
                                break;
                            case "vYear" :
                                $keyval = "manufacturing_year";
                                $value  = $val;
                                break;
                            case "vPostType" :
                                $keyval = "type";
                                $value  = $val;
                                break;
                            case "vSmlImg" :
                                $keyval = "thumb_image";
                                if(trim($val) <> ""){
                                    $value  = $product_image_url.$val;
                                }else{
                                    $value  = $val;
                                }   
                                break;
                           default:    
                               $keyval  = $key;
                               $value   = $val;
                               break;
                       }
                       $res[$keyval] = $value;
                    } 
                    $result[] = $res;
                }                           
            }
            //echo "<pre>"; print_r($result); echo "</pre>";
            
            $message        = "";            
            $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
            return $responseArray;
        }else{
            $message        = NO_PRODUCTS_FOUND;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
    }
    
    
    
    
    function retrieveSwap_WishProductsAPI($type,$lang_id,$cat_id='',$userid='',$other_user='',$recursive='',$transaction_type=''){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        include_once("apifunctions.php");
        include_once("language.php");
        global $conn;     
       if(trim($cat_id) == ""){ //// if searchin products all types should be retreived
        if(trim($type) == ""){
            $message        = PRODUCT_TYPE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($other_user)=="" && !$recursive)
        {
           $message        = OTHER_USER_ID.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray; 
        }
       }
        if(trim($lang_id) == ""){
            $lang_id = 1;
        }
        $response_data      = array();        
        $product_image_url  = SITE_URL; 
         $default_image  = "images/nophoto.gif";   
         
         $db_name=($type==3)?'sale':'swap';
        $primary_key=($type==3)?'nSaleId':'nSwapId';
        $posttype=($type==3)?"'sale' as vPostType":"s.vPostType";
        $swapstatus=($type==3)?"":"s.vSwapStatus,";
        /* Product retrieval starts here */   
        $query = "SELECT 
                    s.$primary_key,
                    s.vTitle,
                    s.vBrand,
                    date_format(s.dPostDate,'%m/%d/%Y') as 'dPostDate',
                    s.vUrl,
                    s.vSmlImg,
                    s.vType,
                    s.vCondition,
                    s.vDescription,
                    s.vYear,
                    $posttype,
                    s.vFeatured,
                    c.nCategoryId,
                    L.vCategoryDesc,
                    $swapstatus
                    s.vDelStatus,
                    u.vLoginName as 'UserName',
                    s.nValue
                    FROM ".TABLEPREFIX."$db_name s
                LEFT JOIN ".TABLEPREFIX."category c on s.nCategoryId = c.nCategoryId 
                LEFT JOIN ".TABLEPREFIX."users u on s.nUserId=u.nUserId
                LEFT JOIN ".TABLEPREFIX."category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '".$lang_id."' 
                    WHERE 1=1";
        if(trim($type) <> ""){
            if(trim($type) == 1 || (trim($type) == 2 && $transaction_type == 1)){
                $query .= " AND s.`vPostType` = 'swap'";
            }
            else if(trim($type) == 2){
                $query .= " AND s.`vPostType` = 'wish'";
            }
//            else if(trim($type) == 3){
//                $query .= " AND s.`vPostType` = 'sale'";
//            }
        }
        if(trim($cat_id)<>"")
        {
             $query .= " AND s.`nCategoryId` = $cat_id";
        }
        if(trim($userid)<>"")
        {
             $query .= " AND s.`nUserId` = $userid";
        }
//        if(trim($search_term) <> "")
//        {
//            $query.=" AND ("
//            ."s.vTitle like '".$search_term
//            ."%' OR s.vBrand like '".$search_term
//            ."%' OR s.vDescription like '".$search_term
//            ."%')";
//        }

        $query .= " AND s.vDelStatus =  '0' ORDER BY s.dPostDate DESC ";   
        
//        return $query;
        $dbArray       = mysqli_query($conn, $query);
        if (mysqli_num_rows($dbArray) > 0){
            $result = array();
            while($rlt = mysqli_fetch_array($dbArray,MYSQLI_ASSOC)){
                //echo "<pre>"; print_r($rlt); echo "</pre>";
                if(is_array($rlt) && count($rlt)>0){
                    $res = array();
                    foreach($rlt as $key=>$val){
                       switch($key){
                            case "nCategoryId" :                                                                   
                                $keyval = "category_id";
                                $value = $val;
                               break;
                           case "vCategoryDesc" :                                                                   
                                $keyval = "category_name";
                                $value = utf8_encode($val);
                               break;
                            case "vTitle" :
                                $keyval = "product_name";
                                $value  = utf8_encode($val);
                                break;
                            case "nValue" :
                                $keyval = "product_price";
                                $value  = utf8_encode($val);
                                break;
                            case "dPostDate" :
                                $keyval = "posted_date";
                                $value  = $val;
                                break;
                            case "vUrl" :
                                $keyval = "product_image";
                                if(trim($val) <> ""){
                                    $value  = $product_image_url.$val;
                                }else{
                                     $value  = $product_image_url.$default_image;
                                }   
                                break;                             
                            case $primary_key  :
                                $keyval = "product_id";
                                $value  = $val;
                                break; 
                            case "vSwapStatus" :
                                $keyval = "swap_status";
                                $value  = $val;
                                break; 
                            case "vFeatured" :
                                $keyval = "is_featured";
                                $value  = $val;
                                break;  
                            case "vDelStatus" :
                                $keyval = "delete_status";
                                $value  = $val;
                                break; 
                            case "UserName" :
                                $keyval = "user_name";
                                $value  = $val;
                                break; 
                            case "vBrand" :
                                $keyval = "brand_name";
                                $value  = $val;
                                break;
                            case "vType" :
                                $keyval = "product_type";
                                $value  = $val;
                                break;
                            case "vCondition" :
                                $keyval = "condition";
                                $value  = $val;
                                break;
                             case "vDescription" :
                                $keyval = "description";
                                $value  = $val;
                                break;
                            case "vYear" :
                                $keyval = "manufacturing_year";
                                $value  = $val;
                                break;
                            case "vPostType" :
                                $keyval = "type";
                                if(trim($type) == 2 && $transaction_type == 1)
                                {
                                    $value = "wish";
                                }
                                else
                                    $value  = $val;
                                break;
                            case "vSmlImg" :
                                $keyval = "thumb_image";
                                if(trim($val) <> ""){
                                    $value  = $product_image_url.$val;
                                }else{
                                    $value  = $val;
                                }   
                                break;
                           default:    
                               $keyval  = $key;
                               $value   = $val;
                               break;
                       }
                       $res[$keyval] = $value;
                    } 
                    $result[] = $res;
                }                           
            }
            //echo "<pre>"; print_r($result); echo "</pre>";
            $return_result=array(0=>$result);
            if(trim($other_user)<>"")
            {
                $return_result[1]=retrieveSwap_WishProductsAPI($type,$lang_id,$cat_id,$other_user,'',1,$transaction_type);
            }
            if($recursive)
            {
                return $return_result;
            }
            $message        = "";            
            $responseArray  = array('status' => 1,'error'=> $message,'data'=> $return_result);
            return $responseArray;
        }else{
            $message        = NO_PRODUCTS_FOUND;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    function productsearchAPI($search_term='',$userid='',$type,$latitude='',$longitude='')
    {
        
        include_once("apifunctions.php");
        include_once("language.php");
        if(trim($search_term)=='')
        {
            $responseArray  = array('status' => 1,'error'=> SEARCH_TEXT_ERROR,'data'=> array());
            return $responseArray;
        }
        else
        {
        return retrieveAllProductsAPI($type,1,$userid,0,'',$search_term,'',$latitude,$longitude);
        }
        
    }
    
    function myproductsearchAPI($search_term='',$userid='',$type)
    {
        include_once("apifunctions.php");
        include_once("language.php");
        if(trim($search_term)=='')
        {
            $responseArray  = array('status' => 1,'error'=> SEARCH_TEXT_ERROR,'data'=> array());
            return $responseArray;
        }
        else
        {
        return retrieveAllProductsAPI($type,1,$userid,1,'',$search_term);
        }
        
    }
    
    function retrieveAllProductsByCategoryAPI($cat_id='',$type='',$userid='',$latitude='',$longitude='')
    {  
        include_once("apifunctions.php");
        include_once("language.php"); 
       if(trim($cat_id)=='')
        {
            $responseArray  = array('status' => 1,'error'=> 'cat_id'.MISSING_PARAMETER,'data'=> array());
            return $responseArray;
        }
        else
        {
        return retrieveAllProductsAPI($type,1,$userid,0,$cat_id,'','',($latitude)?$latitude:'',($longitude)?$longitude:'');
        }
         
    }
    
    function retrieveUserProductsByTypeAPI($user_id,$type,$swap_status)
    {
        include_once("apifunctions.php");
        include_once("language.php");
        if(trim($user_id)=='')
        {
            $responseArray  = array('status' => 1,'error'=> 'user_id'.MISSING_PARAMETER,'data'=> array());
            return $responseArray;
        }
        else
        {
        return retrieveAllProductsAPI($type,1,$user_id,1,'','',$swap_status);
        }
        
    }
    function validateStripeKeys($public_key,$private_key){
        include_once("apifunctions.php");
        include_once("language.php");
        $error_flag = 0 ;
        $error_msg = "";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/tokens");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "card[number]=4242424242424242&card[exp_month]=12&card[exp_year]=2017&card[cvc]=123");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $public_key . ":");

        $response = json_decode(curl_exec($ch),true);

        if( curl_errno($ch) ){
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);

        if(substr($response["error"]["message"],0, 24 ) == "Invalid API Key provided"){
           $error_flag = 1;
           $error_msg = "Invalid Stripe Public Key";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/tokens");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "card[number]=4242424242424242&card[exp_month]=12&card[exp_year]=2017&card[cvc]=123");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $private_key . ":");

        $response = json_decode(curl_exec($ch),true);

        if( curl_errno($ch) ){
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);

        if(substr($response["error"]["message"],0, 24 ) == "Invalid API Key provided"){
           if($error_flag == 1 ){
             $error_msg = INVALID_STRIPE_KEY;
           }else{
             $error_flag = 1;
             $error_msg = INVALID_SECRET_KEY;
            }
        }

        $data['error_status'] = $error_flag;
        $data['error_msg'] = $error_msg;

        return $data;

    }
    /********************************* Product APIs ********************************/
    
    function updateUserProfileAPI($postval,$user_id){   
        global $conn; 
        include_once("apifunctions.php");
        include_once("language.php"); 
        //echo "<pre>"; print_r($postval); echo "</pre>"; die();        
        /*if(count($postval) == 0){
            $message        = "The mandatory parameters are missing on the header";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        
        
        if(trim($postval["first_name"]) == ""){
            $message        = "The first name is missing on the parameters";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($postval["address1"]) == ""){
            $message        = "The address line 1 is missing on the parameters";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }*/
//        if(trim($postval["email"]) == ""){
//            $message        = "The email address is missing on the parameters";
//            $result         = array();
//            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
//            return $responseArray;
//        }
//        if(trim($postval["paypal_email"]) == ""){
//            $message        = "The paypal email address is missing on the parameters";
//            $result         = array();
//            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
//            return $responseArray;
//        }
                
        $response_data  = array();   
        $nUserId        = $user_id;//trim($postval["user_id"]);
        $vFirstName     = trim($postval["first_name"]);
        $vLastName      = trim($postval["last_name"]);
        $vAddress1      = trim($postval["address1"]);
        $vAddress2      = trim($postval["address2"]);
        $vCountry       = trim($postval["country"]);
        $vState         = trim($postval["state"]);
        $vCity          = trim($postval["city"]);
        $nZip           = trim($postval["zip"]);
        $vPhone         = trim($postval["phone"]);
        $vFax           = trim($postval["fax"]);
//        $vEmail         = trim($postval["email"]);
        $vUrl           = trim($postval["weburl"]);
        $vGender        = trim($postval["gender"]);
        $vPaypalEmail   = trim($postval["paypal_email"]);
        $vStripePubKey   = trim($postval["stripe_pub_key"]);
        $vStripeSecretKey   = trim($postval["stripe_secret_key"]);

        $check_stripe_keys = validateStripeKeys($vStripePubKey,$vStripeSecretKey);
        
        if($check_stripe_keys['error_status']) {
            $message        = $check_stripe_keys['error_msg'];
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        
        //$profile_image=trim($postval["profile_image"]);
                
                 
        $vAddress1      = addslashes($vAddress1);
        $vAddress2      = addslashes($vAddress2);
        
        $sqluser = "SELECT `nUserId` FROM `".TABLEPREFIX."users`  WHERE `nUserId` = '".$nUserId."'";
        $dbArray = mysqli_query($conn, $sqluser) or die(mysqli_error($conn));
        if (mysqli_num_rows($dbArray) > 0){
        
            
            
            /*$result_pic = mysqli_query($conn, "Select `profile_image` from " . TABLEPREFIX . "users WHERE `nUserId` = '".$nUserId."'") or die(mysqli_error($conn));
            
                if ($row = mysqli_fetch_array($result_pic)) {                        
                    $func_pic_url = $row["profile_image"];
                    
                }//end if
          
             if ($profile_image=='') {
                $profile_image = $func_pic_url;
            }//end if
            else if ($profile_image!='') {
                @unlink('../pics/profile/'.$func_pic_url);
                $big_image_name_after_upload= image_save('upload',$profile_image,"../pics/profile",'profile');
                $profile_image=$big_image_name_after_upload;
               
            }*/
           
            ///////////////////////////////////////////////////////////////////////////
            
            /* Product retrieval starts here */   
            /*$query = "UPDATE `".TABLEPREFIX."users` SET "
                . "vFirstName   = '" . addslashes($vFirstName)!=''?addslashes($vFirstName):'' . "', "
                . "vLastName    = '" . addslashes($vLastName)!=''?addslashes($vLastName):'' . "',"
                . "vAddress1    = '" . addslashes($vAddress1)!=''?addslashes($vAddress1):'' . "',"
                . "vAddress2    = '" . addslashes($vAddress2)!=''?addslashes($vAddress2):'' . "',"
                . "vCity        = '" . addslashes($vCity)!=''?addslashes($vCity):'' . "',"
                . "vState       = '" . addslashes($vState)!=''?addslashes($vState):'' . "',"
                . "vCountry     = '" . addslashes($vCountry)!=''?addslashes($vCountry):'' . "',"
                . "nZip         = '" . addslashes($nZip)!=''?addslashes($nZip):'' . "',"
                . "vPhone       = '" . addslashes($vPhone)!=''?addslashes($vPhone):'' . "',"
                . "vFax         = '" . addslashes($vFax)!=''?addslashes($vFax):'' . "' ,"
//                . "vEmail       = '" . addslashes($vEmail) . "',"
                . "vUrl         = '" . addslashes($vUrl)!=''?addslashes($vUrl):'' . "',"
                . "vGender      = '" . addslashes($vGender)!=''?addslashes($vGender):'' . "',"
                . "profile_image = '" . $profile_image . "',"
                ."vIMStatus ='Y',"
                . "vPaypalEmail = '" . addslashes($vPaypalEmail)!=''?addslashes($vPaypalEmail):'' . "', "
                    . "stripe_pub_key      = '" . addslashes($vStripePubKey)!=''?addslashes($vStripePubKey):'' . "',"
                    . "stripe_secret_key      = '" . addslashes($vStripeSecretKey)!=''?addslashes($vStripeSecretKey):'' . "'"
                . "WHERE `nUserId` = '" .trim($nUserId)."'";*/
            $vFirstName = addslashes($vFirstName)!=''?addslashes($vFirstName):'';
            $vLastName = addslashes($vLastName)!=''?addslashes($vLastName):'';
            $vAddress1 = addslashes($vAddress1)!=''?addslashes($vAddress1):'';
            $vAddress2 = addslashes($vAddress2)!=''?addslashes($vAddress2):'';
            $vCity = addslashes($vCity)!=''?addslashes($vCity):'';
            $vState = addslashes($vState)!=''?addslashes($vState):'';
            $vCountry = addslashes($vCountry)!=''?addslashes($vCountry):'';
            $nZip = addslashes($nZip)!=''?addslashes($nZip):'';
            $vPhone = addslashes($vPhone)!=''?addslashes($vPhone):'';
            $vFax = addslashes($vFax)!=''?addslashes($vFax):'';
            $vUrl =addslashes($vUrl)!=''?addslashes($vUrl):'';
            $vGender = addslashes($vGender)!=''?addslashes($vGender):'';
            $vPaypalEmail = addslashes($vPaypalEmail)!=''?addslashes($vPaypalEmail):'';
            $vStripePubKey = addslashes($vStripePubKey)!=''?addslashes($vStripePubKey):'';
            $vStripeSecretKey = addslashes($vStripeSecretKey)!=''?addslashes($vStripeSecretKey):'';
                
/*$query = "UPDATE `".TABLEPREFIX."users` SET vFirstName = '".$vFirstName."', vLastName = '".$vLastName."', vAddress1 = '".$vAddress1. "',vAddress2 = '".$vAddress2."',vCity = '".$vCity."',vState = '".$vState."',vCountry = '".$vCountry."',nZip = '".$nZip."',vPhone = '".$vPhone."',vFax = '".$vFax."' ,vUrl = '".$vUrl."',vGender = '".$vGender."',profile_image = '".$profile_image."',vIMStatus ='Y',vPaypalEmail = '".$vPaypalEmail."',stripe_pub_key = '".$vStripePubKey."',stripe_secret_key = '".$vStripeSecretKey."' WHERE `nUserId` = '" .trim($nUserId)."'";*/

$query = "UPDATE `".TABLEPREFIX."users` SET vFirstName = '".$vFirstName."', vLastName = '".$vLastName."', vAddress1 = '".$vAddress1. "',vAddress2 = '".$vAddress2."',vCity = '".$vCity."',vState = '".$vState."',vCountry = '".$vCountry."',nZip = '".$nZip."',vPhone = '".$vPhone."',vFax = '".$vFax."' ,vUrl = '".$vUrl."',vGender = '".$vGender."',vIMStatus ='Y',vPaypalEmail = '".$vPaypalEmail."',stripe_pub_key = '".$vStripePubKey."',stripe_secret_key = '".$vStripeSecretKey."' WHERE `nUserId` = '" .trim($nUserId)."'";

            
            @mysqli_query($conn, $query) or die(mysqli_error($conn));
            $result["user_id"] = trim($nUserId);
            
            $message        = "";            
            $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
            return $responseArray;
        }else{
            $message        = NO_USER_FOUND;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
    }
    
    
    function save_userimageAPI($profile_image,$user_id)
    {
        global $conn;  
        include_once("apifunctions.php");
        include_once("language.php");
//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);
        if(trim($profile_image) == ""){
            $message        = PRODILE_IMAGE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        $profile_image=trim($profile_image);
        
         $result_pic = mysqli_query($conn, "Select `profile_image` from " . TABLEPREFIX . "users WHERE `nUserId` = '".$user_id."'") or die(mysqli_error($conn));
            
                if ($row = mysqli_fetch_array($result_pic)) {                        
                    $func_pic_url = $row["profile_image"];
                    
                }//end if
          
             if ($profile_image=='') {
                $profile_image = $func_pic_url='';//SITE_URL.'../images/nophoto_available_men_small.jpg';
            }//end if
            else if ($profile_image!='') {
                @unlink('../pics/profile/'.$func_pic_url);
                $big_image_name_after_upload= image_save('upload',$profile_image,"../pics/profile",'profile');
                $profile_image=$big_image_name_after_upload;
               
            }
            
            $query = "UPDATE `".TABLEPREFIX."users` SET "
                . "profile_image = '" . $profile_image . "',"
                    ."vIMStatus ='Y'"
                . "WHERE `nUserId` = '" .trim($user_id)."'";
            @mysqli_query($conn, $query) or die(mysqli_error($conn));
            
            $message        = "";  
            $user_image=($profile_image=='')?(SITE_URL.'images/nophoto_available_men_small.jpg'):(SITE_URL.'pics/profile/'.$profile_image);
            $result =array('profile_image'=>$user_image);
            $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
            return $responseArray;
        
    }
    
    
    
    function userprofileAPI($user_id)
    {
     global $conn;
     include_once("apifunctions.php");
     include_once("language.php");

     if(trim($user_id) == ""){
        $message        = USER_ID.MISSING_PARAMETER;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    else if(!is_numeric($user_id)){
        $message        = USER_ID.MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    //print_r($postval);
    //$user_id        = trim($postval["user_id"]);
    
    $sqluserdetails = "SELECT * FROM " . TABLEPREFIX . "users  WHERE  nUserId  = '" . $user_id . "'";
    $resultuserdetails = @mysqli_query($conn, $sqluserdetails) or die(mysqli_error($conn));
    $rowuser = @ mysqli_fetch_array($resultuserdetails);

    $vLoginName = addslashes($rowuser["vLoginName"]!="")?addslashes($rowuser["vLoginName"]):'';
    $vFirstName = addslashes($rowuser["vFirstName"]!="")?addslashes($rowuser["vFirstName"]):'';
    $vLastName = addslashes($rowuser["vLastName"]!="")?addslashes($rowuser["vLastName"]):'';;
    $vAddress1 = addslashes($rowuser["vAddress1"]!="")?addslashes($rowuser["vAddress1"]):'';;
    $vAddress2 = addslashes($rowuser["vAddress2"]!="")?addslashes($rowuser["vAddress2"]):'';;
    $vCity = addslashes($rowuser["vCity"]!="")?addslashes($rowuser["vCity"]):'';
    $vState = addslashes($rowuser["vState"]!="")?addslashes($rowuser["vState"]):'';
    $nZip = addslashes($rowuser["nZip"]!="")?addslashes($rowuser["nZip"]):'';
    $vPhone = addslashes($rowuser["vPhone"]!="")?addslashes($rowuser["vPhone"]):'';
    $vFax = addslashes($rowuser["vFax"]!="")?addslashes($rowuser["vFax"]):'';
    $vEmail = addslashes($rowuser["vEmail"]!="")?addslashes($rowuser["vEmail"]):'';
    $vCountry = addslashes($rowuser["vCountry"]!="")?addslashes($rowuser["vCountry"]):'';
    $vGender = addslashes($rowuser["vGender"]!="")?addslashes($rowuser["vGender"]):'';
    $vUrl = addslashes($rowuser["vUrl"]!="")?addslashes($rowuser["vUrl"]):'';
    $vEducation = addslashes($rowuser["vEducation"]!="")?addslashes($rowuser["vEducation"]):'';
    $vDescription = addslashes($rowuser["vDescription"]!="")?addslashes($rowuser["vDescription"]):'';
    $profile_image = addslashes($rowuser["profile_image"]!="")?addslashes($rowuser["profile_image"]):'';
    $display_image = addslashes($rowuser["vIMStatus"]!="")?addslashes($rowuser["vIMStatus"]):'';
    $paypal_email=addslashes($rowuser["vPaypalEmail"]!="")?addslashes($rowuser["vPaypalEmail"]):'';
    $vStripePubKey   = addslashes($rowuser["stripe_pub_key"]!="")?addslashes($rowuser["stripe_pub_key"]):'';
    $vStripeSecretKey   = addslashes($rowuser["stripe_secret_key"]!="")?addslashes($rowuser["stripe_secret_key"]):'';

    if ($profile_image=='' || !file_exists('../pics/profile/'.$profile_image) || $display_image!='Y'){
        $profile_image = SITE_URL.'images/nophoto_available_men_small.jpg';
    }
    else 
        $profile_image = SITE_URL.'pics/profile/'.$profile_image;

    //checking feedback status
    $cndSatisfied = "where nUserId='" . $user_id . "' AND vStatus='S'";
    $userSatisfied = fetchSingleValue(select_rows(TABLEPREFIX . 'userfeedback', 'count(vStatus) as satisfied', $cndSatisfied), 'satisfied');

    $cndDisatisfied = "where nUserId='" . $user_id . "' AND vStatus='D'";
    $userDisatisfied = fetchSingleValue(select_rows(TABLEPREFIX . 'userfeedback', 'count(vStatus) as dissatisfied', $cndDisatisfied), 'dissatisfied');

    $cndNeutral = "where nUserId='" . $user_id . "' AND vStatus='N'";
    $userNetural = fetchSingleValue(select_rows(TABLEPREFIX . 'userfeedback', 'count(vStatus) as neutral', $cndNeutral), 'neutral');

     return array('status'=>1,'error'=>'','data'=>array('first_name'=>$vFirstName,'last_name'=>$vLastName,
         'profile_image'=>$profile_image,'address1'=>$vAddress1,'address2'=>$vAddress2,'city'=>$vCity,'state'=>$vState,'email'=>$vEmail,
         'zip'=>$nZip,'fax'=>$vFax,'country'=>$vCountry,'phone'=>$vPhone,'gender'=>$vGender,
         'no_of_satisfied_customers'=>$userSatisfied,
         'no_of_dissatisfied_customers'=>$userDisatisfied,'paypal_email'=>$paypal_email,'stripe_pub_key'=>$vStripePubKey,'stripe_secret_key'=>$vStripeSecretKey,
         'no_of_neutral_customers'=>$userNetural));   


    }

    function paypal_detailsAPI()
    {
        global $conn;
        include_once("apifunctions.php");
        include_once("language.php");
        return paypal_details();
    }

    
    
    
    function validate_auth_key($device_id,$device_type,$auth_key)
    {
        global $conn;
        $sql = "SELECT user_id  FROM ".TABLEPREFIX."user_devices WHERE
                                                device_id = '$device_id' AND device_type = '".$device_type."' and auth_key ='".$auth_key."'";
//echo $sql;exit;
                   $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                   if(mysqli_num_rows($result) >0)
                   {
                       $row = mysqli_fetch_array($result);
                       return $row['user_id'];
                   }
                   return false;
    }


    function addItem($postval)
    {        
//        error_reporting(E_ALL);
//    ini_set('display_errors',1);
    if(trim($postval["user_id"]) == ""){
        $message        = USER_ID.MISSING_PARAMETER;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    else if(!is_numeric($postval["user_id"])){
        $message        = USER_ID.MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }   
    $user = trim($postval["user_id"]);
     global $conn;  
     include_once("apifunctions.php");
     include_once("language.php");
     
     $EnablePoint = DisplayLookUp('EnablePoint');
     $fea = DisplayLookUp('5');
        //echo "<pre>"; print_r($postval); echo "</pre>"; die();        
        if(count($postval) == 0){
            $message        = MANDATORY_PARAMETERS;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($postval["type"]) == ""){
            $message        = TYPE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else if(trim($postval["type"]) != "sale" && trim($postval["type"]) != "swap" && trim($postval["type"]) != "wish"){
            $message        = INVALID_TYPE_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        
        if(trim($postval["category"]) == ""){
            $message        = CATEGORY.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($postval["title"]) == ""){
            $message        = TITLE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($postval["item_description"]) == ""){
            $message        = ITEM_DESCRIPTION.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        
        if($postval["type"] == "swap" || $postval["type"] == "sale"){
            if(trim($postval["price"]) == ""){
                $message        = PRICE.MISSING_PARAMETER;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray;
            }
        }
        
        if($postval["type"] == "sale"){
            if(trim($postval["quantity"]) == ""){
                $message        = QUANTITY.MISSING_PARAMETER;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray;
            }
        }
        
        $response_data  = array();   
        $type           = trim($postval["type"]);
        $nUserId        = trim($postval["user_id"]);
        $txtCategory    = trim($postval["category"]);
        $txtTitle       = trim($postval["title"]);
        $txtBrand       = trim($postval["brand"]);
        $ddlType        = trim($postval["product_type"]);
        $ddlCondition   = trim($postval["condition"]);
        $txtDescription = trim($postval["item_description"]);
        $txtYear        = trim($postval["year"]);
        $txtPoint       = trim($postval["points"]);
        $txtValue       = trim($postval["price"]);
        $txtShipping    = trim($postval["shipping_charge"]);
        $txtPicture     = $postval["main_image"];
        $imgDescription = trim($postval["image_description"]);
        $chkFeatured    = trim($postval["featured"]);
        $txtCommission  = trim($postval["commission"]);
        $txtQuantity    = addslashes($postval["quantity"]);
        $now            = date('Y-m-d H:i:s');
        if(trim($txtPicture)<>"")
        {
        $big_image_name_after_upload= image_save('upload',$txtPicture);
//        return $big_image_name_after_upload;
        $txtPicture = "pics/medium_".$big_image_name_after_upload;
        $txtSmallImage = "pics/small_".$big_image_name_after_upload;   
        }
        else
        {
            $txtPicture="";
            $txtSmallImage="";
        }
        
        switch ($EnablePoint) {
        case "1":
        case "2":
            $newField = ",nPoint";
            $newValue = ",'" . $txtPoint . "'";
            break;

        case "0":
            $newField = '';
            $newValue = '';
            break;
        }  
        
        /*list($width, $height, $type, $attr) = getimagesize($_FILES['product_image']['tmp_name']);

        if($width<=250 || $height<=250) {
            $message        = "Image dimension incorrect.";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else {
                $dir_dest = "pics";
                //big Image Upload
                $picbigname = $_FILES['product_image']['name'];

                $final_image_big = "";
                if ($picbigname != "") {
                    $fileName = basename($picbigname);
                    $extension = end(explode(".", $_FILES['product_image']['name']));
                    $fileNameWithoutExtension = preg_replace("/\.[^.]+$/", "", $fileName);
                    $final_image_big = $_SESSION["guserid"]."_".time().$fileNameWithoutExtension;
                }
                 $picbig_newname = $final_image_big;

                if ($picbig_newname != "") {
                    $files = $_FILES['product_image'];
                    $handle = new Upload($files);

                if ($handle->uploaded) {
                    $handle->image_resize = false;
                    $handle->image_ratio_y = true;
                    //$handle->image_x = 283;
                    //$handle->image_y = 269;
                    $handle->file_new_name_body = $picbig_newname;
                    $handle->Process($dir_dest);
                    $big_image_name_after_upload = $handle->file_dst_name;

                    $handle->file_new_name_body = 'small_'.$picbig_newname;
                    $handle->Process($dir_dest);
                    $handle->file_new_name_body = 'medium_'.$picbig_newname;
                    $handle->Process($dir_dest);
                    $handle->file_new_name_body = 'large_'.$picbig_newname;
                    $handle->Process($dir_dest);
                }
            }
            
            $txtPicture = "pics/medium_".$big_image_name_after_upload;
            $txtSmallImage = "pics/small_".$big_image_name_after_upload;                                           
        }
        */
        
        if ($type == "sale") {

        //sql for sale
        $sql = "INSERT INTO " . TABLEPREFIX . "sale (nSaleId, nCategoryId, nUserId,";
        $sql .= "vTitle, vBrand, vType, vCondition, vYear, nValue,";
        $sql .= "nShipping, vUrl, vDescription, dPostDate, nQuantity,vDelStatus,vSmlImg,vImgDes " . $newField . ")";
        $sql .= "VALUES ('', '$txtCategory', '";
        $sql .= $nUserId;
        $sql .= "', '$txtTitle',";
        $sql .= "'$txtBrand', '$ddlType', '$ddlCondition', '$txtYear', '$txtValue',";
        $sql .= "'$txtShipping', '$txtPicture', '$txtDescription','";
        $sql .= "$now" . "'";
        $sql .= ", '$txtQuantity','0','$txtSmallImage','" . addslashes($imgDescription) . "' " . $newValue . ")";
        }
        else if ($type == "swap") {
            //sql for swap
            $sql = "INSERT INTO " . TABLEPREFIX . "swap (nSwapId, nCategoryId, nUserId, vTitle,";
            $sql .="vBrand, vType, vCondition, vYear, nValue, nShipping, vUrl,";
            $sql .="vDescription, vPostType, dPostDate, vSwapStatus, vDelStatus,vSmlImg,vImgDes " . $newField . ")";
            $sql .="VALUES ('', '$txtCategory', '";
            $sql .=$nUserId;
            $sql .="', '$txtTitle', '$txtBrand', '$ddlType', '$ddlCondition', '$txtYear',";
            $sql .="'$txtValue', '$txtShipping', '$txtPicture', '$txtDescription', 'swap','";
            $sql .="$now" . "'";
            $sql .=",'0','0','$txtSmallImage','" . addslashes($imgDescription) . "' " . $newValue . ")";
        }//end esle if 
        else if ($type == "wish") {
            //sql for wish
            $sql = "INSERT INTO " . TABLEPREFIX . "swap (nSwapId, nCategoryId, nUserId, vTitle,";
            $sql .="vBrand, vType, vCondition, vYear, nValue, vUrl,";
            $sql .="vDescription, vPostType, dPostDate, vSwapStatus, vSmlImg, vDelStatus " . $newField . ")";
            $sql .="VALUES ('', '$txtCategory', '";
            $sql .=$nUserId;
            $sql .="', '$txtTitle', '$txtBrand', '$ddlType', '$ddlCondition', '$txtYear',";
            $sql .="'$txtValue', '$txtPicture', '$txtDescription', 'wish','";
            $sql .="$now" . "'";
            $sql .=",'0','$txtSmallImage','0' " . $newValue . ")";
        }

        if (($chkFeatured != "featured") && ($txtCommission <= 0)) {//if payment is not required, it would be inserted now, otherwise after payment it would be inserted
            //execute query and send mail
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $ntmpid='';
        }
        else {
            // Make featurd if acommission and featured fee is zero
            if($txtCommission <= 0 && $chkFeatured == "featured" && $fea == 0){
                mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $ntmpid='';
                $NewId = mysqli_insert_id($conn);
                $featuredSql = "UPDATE " . TABLEPREFIX . "sale SET vFeatured = 'Y' WHERE nSaleId = '".$NewId."'";
                mysqli_query($conn, $featuredSql);
                $chkFeatured = '';
            }else{
                $ntmpid = $nUserId;
            }
        }
        
        if(!$NewId || $NewId == ''){
            $NewId = mysqli_insert_id($conn);
        }
        $fieldId = '';
        
        switch ($_GET['type']) {
            case "swap":
                $fieldId = 'nSwapId';
                break;

            case "sale":
                $fieldId = 'nSaleId';
                break;

            case "wish":
                $fieldId = 'nSwapId';
                break;
        }
        /*
        $moreFiles = $_POST['productMoreImage'];
        
        for ($x = 0; $x < count($moreFiles); $x++) {
            $moreImageName = $moreFiles[$x];
            if ($moreImageName != "") {
              //  insert into gallery table

                $moreImage_large            = "pics/large_".$moreImageName;

                $txtSmallImage1             = "pics/small_".$moreImageName;

                $txtMediumImage1             = "pics/medium_".$moreImageName;

                $more_image_description     =  $_POST['txtImgDes'][$x];

                mysqli_query($conn, "insert into " . TABLEPREFIX . "gallery (nUserId," . $fieldId . ",vImg,vDes,nTempId,vSmlImg,vMedImg) values 
                                                                                            ('" . $_SESSION["guserid"] . "','" . $NewId . "','" . $moreImage_large . "',
                                                                                            '" . addslashes($more_image_description) . "','" . $ntmpid . "',
                                                                                            '" . $txtSmallImage1 . "','" .  $txtMediumImage1."')") or die(mysqli_error($conn));

            }
        }
        */   
        $routesql = "Select vRoute from " . TABLEPREFIX . "category where nCategoryId ='$txtCategory'";
        //echo $routesql;
        $result = mysqli_query($conn, $routesql) or die(mysqli_error($conn));
        $row = mysqli_fetch_array($result);
        $route = $row["vRoute"];
        $countsql = "UPDATE " . TABLEPREFIX . "category SET nCount=nCount+1 WHERE nCategoryId in($route)";
        $result = mysqli_query($conn, $countsql) or die(mysqli_error($conn));

        $txtCategoryname = addslashes($_POST["txtCategoryname"]);
        $strFind = array("&Acirc;", "&nbsp;", "&raquo;");
        $strReplce = array("", "", "");
        $txtCategoryname   = str_replace($strFind,$strReplce,$txtCategoryname);
        if($EnablePoint==2){
            $itemValue  =   $txtPoint;
        }else{
            $itemValue  =   CURRENCY_CODE.$txtValue;
        }
        
        /*
        * Fetch user language details
        */
        
        $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$lang_id."'";
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
                   AND L.lang_id = '".$lang_id."'";
        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $mainTextShow   = $mailRw['content'];
        $enbPnt = DisplayLookUp("EnablePoint");

       
        if($enbPnt=="2"){
            
            $mainTextShow   = str_replace("{pricename}",POINT_NAME.'/'.TEXT_PRICE, $mainTextShow);            
            //$itemValue  =   $txtPoint."/".CURRENCY_CODE.$txtValue;
            $itemValue   = (!empty($txtPoint)) ? $txtPoint : '--';
            $itemValue  .= (!empty($itemValue)) ? '   /  ' : '';
            $itemValue  .= (!empty($txtValue)) ? CURRENCY_CODE.$txtValue : '--';
            $itemValue  .= (!empty($txtShipping)) ? '  ('.TEXT_SHIPPING_CHARGE.' : '.CURRENCY_CODE.$txtShipping.')' : '';
            
        }else if($enbPnt=="1"){
            
            $mainTextShow   = str_replace("{pricename}",POINT_NAME, $mainTextShow);
            $itemValue  =   $txtPoint;

        }else{
            
            $mainTextShow   = str_replace("{pricename}",TEXT_PRICE, $mainTextShow);
            $itemValue  =   $txtValue;
            if($itemValue) {
               $itemValue  =   CURRENCY_CODE.$itemValue;
               $itemValue  .= (!empty($txtShipping)) ? '  ('.TEXT_SHIPPING_CHARGE.' : '.CURRENCY_CODE.$txtShipping.')' : '';
            }
        }
        
        $brandReplace = (!empty($txtBrand)) ? stripslashes($txtBrand) : '--';

        $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{txtTitle}","{txtBrand}","{ddlType}","{ddlCondition}","{txtValue}","{txtCategoryname}","{txtYear}");
        $arrTReplace    = array(SITE_NAME,SITE_URL,stripslashes($txtTitle),$brandReplace,stripslashes($ddlType),stripslashes($ddlCondition),$itemValue,stripslashes($txtCategoryname),$txtYear);
        
        $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);
 
        $mailcontent1   = $mainTextShow;

        $subject    = $mailRw['content_title'];
        $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

        $StyleContent   = MailStyle($sitestyle,SITE_URL);

        $sql = "Select vFirstName,vLoginName,vEmail from " . TABLEPREFIX . "users where nUserId = '" . $nUserId . "' and vAlertStatus='Y' and vDelStatus = '0' and vStatus = '0'";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $EMail = stripslashes($row["vEmail"]);
                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, htmlentities($row["vLoginName"]), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                $msgBody    = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody    = str_replace($arrSearch, $arrReplace, $msgBody);
                //$msgBody;
                send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
            }
        }

        /*    
        if (($chkFeatured == "featured") || ($txtCommission > 0)) {
//echo $txtTitle;exit;
            if ($chkFeatured == "featured") {
                $feaentry = "$fea";
            }
            else {
                $feaentry = "0";
            }

            $sql = "INSERT INTO " . TABLEPREFIX . "saleextra (nSaleextraId, nCategoryId, nUserId,";
            $sql .= "vTitle, vBrand, vType, vCondition, vYear, nValue,";
            $sql .= "nShipping, vUrl, vDescription, dPostDate, nQuantity,nFeatured,nCommission,vSmlImg,vImgDes " . $newField . ")";
            $sql .= "VALUES ('', '$txtCategory', '";
            $sql .= $nUserId;
            $sql .= "', '$txtTitle',";
            $sql .= "'$txtBrand', '$ddlType', '$ddlCondition', '$txtYear', '$txtValue',";
            $sql .= "'$txtShipping', '$txtPicture', '$txtDescription','";
            $sql .= "$now" . "'";
            $sql .= ", '$txtQuantity','$feaentry','$txtCommission','$txtSmallImage','" . addslashes($imgDescription) . "' " . $newValue . ")";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
 
            $sql = "Select nSaleextraId from " . TABLEPREFIX . "saleextra where dPostDate='$now' AND nUserId = '" . $nUserId . "'";
            
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                if ($row1 = mysqli_fetch_array($result)) {
                    $id = $row1["nSaleextraId"];
                    $_SESSION["gsaleextraid"] = $id;
                    $_SESSION["points"] = $txtPoint;
                    header("location:featuredpay.php");
                    exit();
                }
            }
        }
        */
        
        $message        = PRODUCT_ADD_SUCCESS_MSG;  
        $result         = array();
        $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
        return $responseArray;                      
    }
    
    function editItem($postval)
    {
//        error_reporting(E_ALL);
//ini_set('display_errors', 1);

        global $conn;  
        include_once("apifunctions.php");
        include_once("language.php");
        $EnablePoint = DisplayLookUp('EnablePoint');
        
        if(count($postval) == 0){
            $message        = MANDATORY_PARAMETERS;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($postval["type"]) == ""){
            $message        = TYPE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else if(trim($postval["type"]) != "sale" && trim($postval["type"]) != "swap" && trim($postval["type"]) != "wish"){
            $message        = INVALID_TYPE_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($postval["product_id"]) == ""){
            $message        = PRODUCT_ID.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else if(!is_numeric($postval["product_id"])){
            $message        = MISSING_PARAMETER.MUST_BE_NUMERIC;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($postval["user_id"]) == ""){
            $message        = USER_ID.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else if(!is_numeric($postval["user_id"])){
            $message        = USER_ID.MUST_BE_NUMERIC;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($postval["category"]) == ""){
            $message        = CATEGORY.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($postval["title"]) == ""){
            $message        = TITLE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        if(trim($postval["item_description"]) == ""){
            $message        = ITEM_DESCRIPTION.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        
        if($postval["type"] == "swap" || $postval["type"] == "sale"){
            if(trim($postval["price"]) == ""){
                $message        = PRICE.MISSING_PARAMETER;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray;
            }
        }    
        
        if($postval["type"] == "sale"){
            if(trim($postval["quantity"]) == ""){
                $message        = QUANTITY.MISSING_PARAMETER;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray;
            }
        }  
        
        if($postval["type"] == "swap" || $postval["type"] == "wish") 
        {
           $sql = "Select nSwapId from " . TABLEPREFIX . "swap where nSwapId = '"
                . addslashes($postval["product_id"]) . "'   AND
                     vDelStatus='0' AND nUserId='" . $postval["user_id"] . "'";

           if(mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) 
           {
                $message        = ERROR_ITEM_ALREADY_SWAPPED_DELETED;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray;
           }//end if
        }//end if
        else if ($postval["type"] == "sale") 
        {
            $sql = "Select nSaleId from " . TABLEPREFIX . "sale where nSaleId = '"
                    . addslashes($postval["product_id"]) . "'   AND
                          vDelStatus='0' AND nUserId='" . $postval["user_id"] . "'";
            if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {
                $message        = ERROR_ITEM_ALREADY_PURCHASED_DELETED;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray;
            }//end if
        }//end else

        //checking point enable in website
        if ($EnablePoint == '1' || $EnablePoint == '2') {
            if (!is_numeric($postval["points"]) || intval($postval["points"]) <= 0) {
                $message        = INVALID_POINT;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray;
            }//end if
        }//end if
        if ($EnablePoint == '0' || $EnablePoint == '2') {
            if (!is_numeric($postval["price"]) || intval($postval["price"]) <= 0) {
                $message        = INVALID_PRICE;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray;
            }//end if
        }//end else
    
        $response_data          = array();   
        $var_post_type          = trim($postval["type"]);
        //$nUserId                = $postval["user_id"];
        $var_swapid             = $postval["product_id"];
        //$var_post_date          = $postval["posted_on"];
        $var_url                = $postval["main_image"];
        $var_category_id        = $postval["old_category"];
        $var_category_new_id    = $postval["category"];
        $var_title              = $postval["title"];
        $var_brand              = $postval["brand"];
        $var_type               = $postval["product_type"];
        $var_condition          = $postval["condition"];
        $var_year               = $postval["year"];
        $var_value              = $postval["price"];
        $txtPoint               = $postval["points"];
        $var_shipping           = $postval["shipping_charge"];
        $var_description        = $postval["item_description"];
        $var_quantity           = $postval["quantity"];
        $txtImgDes              = $postval["image_description"];
    
        $txtPicture             = $postval["main_image"];//addslashes($_POST["txtPicture"]);
//        $txtSmallImage          = $postval["txtPictureSmall"];//addslashes($_POST["txtPictureSmall"]);

        if($var_category_id != $var_category_new_id) 
        {
            $sql = "Select nCategoryId,vRoute from " . TABLEPREFIX . "category
                         where nCategoryId IN($var_category_id,$var_category_new_id)";
            $sub_route_old = "";
            $sub_route_new = "";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    if ($row["nCategoryId"] == $var_category_id) {
                        $sub_route_old = $row["vRoute"];
                    }//end if
                    else {
                        $sub_route_new = $row["vRoute"];
                    }//end else
                }//end while loop
            }//end if

            if ($var_post_type == "swap" || $var_source == "wish") {
                if ($sub_route_old!=''){
                    $sql = "Update " . TABLEPREFIX . "category set nCount = nCount - 1
                   where nCategoryId  IN($sub_route_old) ";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }
                if ($sub_route_new!=''){
                    $sql = "Update " . TABLEPREFIX . "category set nCount = nCount + 1
                                   where nCategoryId  IN($sub_route_new) ";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }
            }//end if
            else if ($var_post_type == "sale") {
                $sql = "Select nQuantity from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($var_swapid) . "'";
                $resultcheck = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if (mysqli_num_rows($resultcheck) > 0) {
                    $row = mysqli_fetch_array($resultcheck);
                    $quantity_to_check = $row["nQuantity"];
                    settype($quantity_to_check, double);
                    if ($quantity_to_check > 0 && $sub_route_old!='') {
                        $sql = "Update " . TABLEPREFIX . "category set nCount = nCount - 1
                                     where nCategoryId  IN($sub_route_old) ";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }//endi f
                }//end if
                settype($var_quantity, double);
                if ($var_quantity > 0) {
                    $sql = "Update " . TABLEPREFIX . "category set nCount = nCount + 1
                                      where nCategoryId  IN($sub_route_new) ";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }//end if
            }//end if
        }
        else 
        {
            if ($var_post_type == "sale") {
                $sql = "Select nCategoryId,vRoute from " . TABLEPREFIX . "category
                                 where nCategoryId ='$var_category_id'";
                $sub_route_old = "";
                $sub_route_new = "";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if (mysqli_num_rows($result) > 0) {
                    if ($row = mysqli_fetch_array($result)) {
                        $sub_route_old = $row["vRoute"];
                    }//end if
                }//end if
                settype($var_quantity, double);
                $sql = "Select nQuantity from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($var_swapid) . "'";
                $resultcheck = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if (mysqli_num_rows($resultcheck) > 0) {
                    $row = mysqli_fetch_array($resultcheck);
                    $quantity_to_check = $row["nQuantity"];
                    settype($quantity_to_check, double);
                    if ($quantity_to_check == 0 && $var_quantity > 0 && $sub_route_old!='') {
                        $sql = "Update " . TABLEPREFIX . "category set nCount = nCount + 1
                                         where nCategoryId  IN($sub_route_old) ";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }//end if
                    else if ($quantity_to_check > 0 && $var_quantity == 0 && $sub_route_old!='') {
                        $sql = "Update " . TABLEPREFIX . "category set nCount = nCount - 1
                                          where nCategoryId  IN($sub_route_old) ";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }//end else if
                }//end if

            }//end if
        }
        
        $var_category_id = $var_category_new_id;
        
        if($var_post_type == "swap" || $var_post_type == "wish") 
        {
           $result_pic = mysqli_query($conn, "Select vUrl,vSmlImg from " . TABLEPREFIX . "swap where nSwapId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
            if (mysqli_num_rows($result_pic) > 0) {
                if ($row = mysqli_fetch_array($result_pic)) {                        
                    $func_pic_url = $row["vUrl"];
                    $func_txtSmallImage = $row["vSmlImg"];
                }//end if
            }//end if
            $result_pic = null;
            $row = null;

            if ($txtPicture=='') {
                $file_name = $txtPicture = $func_pic_url;
                $txtSmallImage = $func_txtSmallImage;
            }//end if
            else if ($txtPicture!='') {
                @unlink('../'.$func_pic_url);
                @unlink('../'.$func_txtSmallImage);
                $big_image_name_after_upload= image_save('upload',$txtPicture);
                $txtPicture = "pics/medium_".$big_image_name_after_upload;
                $txtSmallImage = "pics/small_".$big_image_name_after_upload;   
            }//end else if
            //check point status

            switch ($EnablePoint) {
                case "2":
                case "1":
                    $newField = ",nPoint='" . $txtPoint . "'";
                    break;

                case "0":
                    $newField = '';
                    break;
            }//end switch

            $var_url = $file_name;
            $sql_update = "Update " . TABLEPREFIX . "swap set nCategoryId='" . addslashes($var_category_id) . "',"
                    . "vTitle='" . addslashes($var_title) . "',"
                    . "vBrand='" . addslashes($var_brand) . "',"
                    . "vType='" . addslashes($var_type) . "',"
                    . "vCondition='" . addslashes($var_condition) . "',"
                    . "vYear='" . addslashes($var_year) . "',"
                    . "nValue='" . addslashes($var_value) . "',"
                    . "nShipping='" . addslashes($var_shipping) . "',"
                    . "vUrl='" . addslashes($txtPicture) . "',"
                    . "vDescription='" . addslashes($var_description) . "',"
                    . "vSmlImg='" . addslashes($txtSmallImage) . "',"
                    . "vImgDes='" . addslashes($txtImgDes) . "' "
                    . $newField
                    . " where nSwapId='" . addslashes($var_swapid) . "'";

            mysqli_query($conn, $sql_update) or die(mysqli_error($conn));
            
            /*
            //update gallery starts here
            if(is_array($_POST['productMoreImage']))
            {
                $moreFiles = $_POST['productMoreImage'];
                for ($x = 0; $x < count($moreFiles); $x++) {
                   $moreImageName = $moreFiles[$x];
                   if ($moreImageName != "") {

                       $moreImage_large            = "pics/large_".$moreImageName;
                       $moreImage_medium            = "pics/medium_".$moreImageName;
                       $txtSmallImage             = "pics/small_".$moreImageName;

                 if($_POST['nGalId'][$x]!='')      { 


                   $update_query =  "update " . TABLEPREFIX . "gallery set vImg='" .mysqli_real_escape_string($conn, $moreImage_large) . "',vSmlImg='" . mysqli_real_escape_string($conn, $txtSmallImage) . "' ,vMedImg='" . mysqli_real_escape_string($conn, $moreImage_medium) . "' where nUserId='" . $_SESSION["guserid"] . "' and nSwapId='" . addslashes($var_swapid) . "' 
                                                                                   and nId='" . $_POST['nGalId'][$x] . "'";

                    mysqli_query($conn, $update_query) or die(mysqli_error($conn));
                 }       
                 else{

                    $insert_query         = "insert into " . TABLEPREFIX . "gallery (nUserId,nSwapId,vDes,vImg,vSmlImg,vMedImg) values
                                                ('" . $_SESSION["guserid"] . "','" . addslashes($var_swapid) . "',
                                                 '" . addslashes($_POST['txtImgDesGal'][$x]) . "','" . mysqli_real_escape_string($conn, $moreImage_large) . "',
                                                 '" . mysqli_real_escape_string($conn, $txtSmallImage) . "' , '".mysqli_real_escape_string($conn, $moreImage_medium)."' )";

                     mysqli_query($conn, $insert_query) or die(mysqli_error($conn));
                 }

                }
                }
            }*/
           
            /*
            if (is_array($_POST['txtImgDesGal'])) {
                $k = 0;
            foreach ($_POST['txtImgDesGal'] as $val) {
                //update into gallery table
            if($_POST['nGalId'][$k]!='')      {     
                mysqli_query($conn, "update " . TABLEPREFIX . "gallery set vDes='" . mysqli_real_escape_string($conn, $val) . "'
                                            where nUserId='" . $_SESSION["guserid"] . "' and nSaleId='" . addslashes($var_swapid) . "' 
                                                and nId='" . $_POST['nGalId'][$k] . "'") or die(mysqli_error($conn));
            }
           
                $k++;
            }//end foreach
            }//end if
            */    
               
    //update gallery stops here
                //update gallery contents

                $message        = PRODUCT.UPDATE_SUCCESS;  
                $result         = array();
                $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
                return $responseArray;
        }
        else if($var_post_type == "sale") 
        {
                $result_pic = mysqli_query($conn, "Select vUrl,vSmlImg from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                if (mysqli_num_rows($result_pic) > 0) {
                    if ($row = mysqli_fetch_array($result_pic)) {
                        $func_pic_url = $row["vUrl"];
                        $func_txtSmallImage = $row["vSmlImg"];
                    }//end if
                }//end if
                $result_pic = null;
                $row = null;

                if ($txtPicture=='') {
                    $txtPicture = $file_name = $func_pic_url;
                    $txtSmallImage = $func_txtSmallImage;
                }//end if
                else if ($txtPicture!='') {
                     @unlink('../'.$func_pic_url);
                     @unlink('../'.$func_txtSmallImage);
                      $big_image_name_after_upload= image_save('upload',$txtPicture);
                      $txtPicture = "pics/medium_".$big_image_name_after_upload;
                      $txtSmallImage = "pics/small_".$big_image_name_after_upload;   
                     
                }//end if
                //check point status
                switch ($EnablePoint) {
                    case "2":
                    case "1":
                        $newField = ",nPoint='" . $txtPoint . "'";
                        break;

                    case "0":
                        $newField = '';
                        break;
                }//end switch
                $var_url = $txtPicture;
                $sql_update = "Update " . TABLEPREFIX . "sale set nCategoryId='" . addslashes($var_category_id) . "',"
                        . "vTitle='" . mysqli_real_escape_string($conn, $var_title) . "',"
                        . "vBrand='" . mysqli_real_escape_string($conn, $var_brand) . "',"
                        . "vType='" . mysqli_real_escape_string($conn, $var_type) . "',"
                        . "vCondition='" . mysqli_real_escape_string($conn, $var_condition) . "',"
                        . "vYear='" . mysqli_real_escape_string($conn, $var_year) . "',"
                        . "nValue='" . mysqli_real_escape_string($conn, $var_value) . "',"
                        . "nShipping='" . mysqli_real_escape_string($conn, $var_shipping) . "',"
                        . "vUrl='" . mysqli_real_escape_string($conn, $txtPicture) . "',"
                        . "vDescription='" . mysqli_real_escape_string($conn, $var_description) . "',"
                        . "vSmlImg='" . mysqli_real_escape_string($conn, $txtSmallImage) . "',"
                        . "vImgDes='".mysqli_real_escape_string($conn, $txtImgDes)."',"
                        . "nQuantity='" . mysqli_real_escape_string($conn, $var_quantity) . "'  "
                        . $newField. " where nSaleId='" . addslashes($var_swapid) . "'";
                mysqli_query($conn, $sql_update) or die(mysqli_error($conn));
               
//echo $sql_update;exit;
                //update gallery starts here
                
                /*
                if(is_array($_POST['productMoreImage']))
                {
                     $moreFiles = $_POST['productMoreImage'];
                      for ($x = 0; $x < count($moreFiles); $x++) {
                        $moreImageName = $moreFiles[$x];
                        if ($moreImageName != "") {
                          //  insert into gallery table

                            $moreImage_large            = "pics/large_".$moreImageName;
                            $moreImage_medium            = "pics/medium_".$moreImageName;
                            $txtSmallImage             = "pics/small_".$moreImageName;

                            //$more_image_description     =  $_POST['txtImgDes'][$x];
                 
                  //Update Alreday Existing Iamge
                      if($_POST['nGalId'][$x]!='')      { 
                        $update_query =  "update " . TABLEPREFIX . "gallery set vImg='" . mysqli_real_escape_string($conn, $moreImage_large). "',vSmlImg='" . mysqli_real_escape_string($conn, $txtSmallImage) . "'
                                                    where nUserId='" . $_SESSION["guserid"] . "' and nSaleId='" . addslashes($var_swapid) . "' 
                                                        and nId='" . $_POST['nGalId'][$x] . "'";
                            
                         mysqli_query($conn, $update_query) or die(mysqli_error($conn));
                      }       
                // End Updation  
                      
                      //Insert More Images 
                      else{
                         
                         $insert_query         = "insert into " . TABLEPREFIX . "gallery (nUserId,nSaleId,vDes,vImg,vSmlImg,vMedImg) values
                                                     ('" . $_SESSION["guserid"] . "','" . addslashes($var_swapid) . "',
                                                      '" . addslashes($_POST['txtImgDesGal'][$x]) . "','" . mysqli_real_escape_string($conn, $moreImage_large) . "',
                                                      '" . mysqli_real_escape_string($conn, $txtSmallImage) . "' , '".mysqli_real_escape_string($conn, $moreImage_medium)."')";
                          
                          mysqli_query($conn, $insert_query) or die(mysqli_error($conn));
                      }
                      
                }
                }         
                }*/
                
                /*
                if (is_array($_POST['txtImgDesGal'])) {
                      $k = 0;
                  foreach ($_POST['txtImgDesGal'] as $val) {
                      //update into gallery table
                  if($_POST['nGalId'][$k]!='')      {     
                      mysqli_query($conn, "update " . TABLEPREFIX . "gallery set vDes='" . mysqli_real_escape_string($conn, $val) . "'
                                                  where nUserId='" . $_SESSION["guserid"] . "' and nSaleId='" . addslashes($var_swapid) . "' 
                                                      and nId='" . $_POST['nGalId'][$k] . "'") or die(mysqli_error($conn));
                  }

                      $k++;
                  }//end foreach
                }//end if
                */
                
                $message        = PRODUCT.UPDATE_SUCCESS;  
                $result         = array();
                $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
                return $responseArray;
        }
    }
    
    ///// Delete item API
    
    function Delete_productAPI($var_source,$var_category_id,$var_swapid,$user_id)
     {
        global $conn;
        include_once("apifunctions.php");
        include_once("language.php");
        if(trim($var_source) ==""){
            $message        = TYPE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($var_source) != 1 && trim($var_source) != 2 && trim($var_source) != 3){
            $message        = INVALID_TYPE_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($var_category_id) == ""){
            $message        = CATEGORY_ID_MISSING;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
       
        else if(trim($var_swapid) == ""){
            $message        = PRODUCT_ID.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
            
        $var_source=($var_source==1)?'s':($var_source==2?'w':'sa');
        
//        $var_source         =   $_REQUEST['source'];
//        $var_category_id    =   $_REQUEST['catid'];
//        $var_swapid         =   $_REQUEST['delete_id'];
        $validation_result=validate_values_delete($var_source, $var_swapid,$user_id);
         if ($validation_result['result']) {
                    if ($var_source == "s" || $var_source == "w") {
                        //block that handles the decrement
                        $sql = "Select nCategoryId,vRoute from " . TABLEPREFIX . "category
                                           where nCategoryId ='" . addslashes($var_category_id) . "'";
                        $sub_route_old = "";

                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        if (mysqli_num_rows($result) > 0) {
                            if ($row = mysqli_fetch_array($result)) {
                                $sub_route_old = $row["vRoute"];
                            }//end if
                        }//end if
                        if ($sub_route_old!=''){
                            $sql = "Update " . TABLEPREFIX . "category set nCount = nCount - 1
                                                where nCategoryId  IN($sub_route_old) ";
                            mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        }
                        //end of block

                        $result_pic = mysqli_query($conn, "Select vUrl,vSmlImg from " . TABLEPREFIX . "swap where nSwapId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                        if (mysqli_num_rows($result_pic) > 0) {
                            if ($row = mysqli_fetch_array($result_pic)) {
                                $func_pic_url = $row["vUrl"];
                                $func_txtSmallImage = $row["vSmlImg"];
                            }//end if
                        }//end if
                        $result_pic = null;
                        $row = null;

                        //get the main  swapid's where the present swapid is present
                        //in the swaptxn table.Update the swaptxn table.

                        $sql = "Select distinct nSwapId from " . TABLEPREFIX . "swapreturn where
                                           nSwapReturnId= '" . addslashes($var_swapid) . "' ";
                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        $sub_slist = $var_swapid;
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_array($result)) {
                                $sub_slist .= "," . $row["nSwapId"];
                            }//end while
                        }//end if

                        $sql = "Update " . TABLEPREFIX . "swaptxn set vStatus='N'  where
                                         nSwapId IN($sub_slist) AND vStatus != 'A'";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));

                        //End of changes in the swaptxn table
                        //Delete the item from the ".TABLEPREFIX."swap table by changing the vDelStatus to 1
                        $sql = "Update " . TABLEPREFIX . "swap set vDelStatus='1' where
                                         nSwapId= '" . addslashes($var_swapid) . "' ";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));

                        //Delete the item from the ".TABLEPREFIX."gallery table by changing the vDelStatus to 1
                        mysqli_query($conn, "update " . TABLEPREFIX . "gallery set vDelStatus='1' where nUserId='" . $_SESSION["guserid"] . "' and
                                                                                                nSwapId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));

                        //Deletion s/w $sql_update="delete from ".TABLEPREFIX."swap where nSwapid='" . addslashes($var_swapid) . "'";
                        $gTableId = 'nSwapId';
                    }//end if
                    else if ($var_source == "sa") {
                        $result_pic = mysqli_query($conn, "Select vUrl,nQuantity,vSmlImg from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                        if (mysqli_num_rows($result_pic) > 0) {
                            if ($row = mysqli_fetch_array($result_pic)) {
                                $func_pic_url = $row["vUrl"];
                                $quantity_to_check = $row["nQuantity"];
                                $func_txtSmallImage = $row["vSmlImg"];
                            }//end if
                        }//end if
                        $result_pic = null;
                        $row = null;

                        //block that handles the decrementation of the count of categories
                        settype($quantity_to_check, double);
                        if ($quantity_to_check > 0) {
                            $sql = "Select nCategoryId,vRoute from " . TABLEPREFIX . "category
                                             where nCategoryId ='" . addslashes($var_category_id) . "'";
                            $sub_route_old = "";
                            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                            if (mysqli_num_rows($result) > 0) {
                                if ($row = mysqli_fetch_array($result)) {
                                    $sub_route_old = $row["vRoute"];
                                }//end if
                            }//end if

                            if ($sub_route_old!=''){
                                $sql = "Update " . TABLEPREFIX . "category set nCount = nCount - 1
                                                      where nCategoryId  IN($sub_route_old) ";
                                mysqli_query($conn, $sql) or die(mysqli_error($conn));
                            }
                        }//end if
                        //end of block

                        $sql = "Update " . TABLEPREFIX . "sale set vDelStatus='1' where
                                        nSaleid= '" . addslashes($var_swapid) . "' ";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));

                        //Delete the item from the ".TABLEPREFIX."gallery table by changing the vDelStatus to 1
                        mysqli_query($conn, "update " . TABLEPREFIX . "gallery set vDelStatus='1' where nUserId='" . $_SESSION["guserid"] . "' and
                                                                                                nSaleId='" . addslashes($var_swapid) . "'") or die(mysqli_error($conn));
                        $gTableId = 'nSaleId';
                    }//end else if

                    $func_pic_url = "";
                    $func_txtSmallImage = "";

                    //on successful deletion
                    // mysqli_query($conn, $sql_update);
                    $del_flag = true;
                    $var_message = 'success';
                     $responseArray  = array('status' => 1,'error'=> 'Product deleted successfully','data'=> array());
                }//end if
                else
                {
                    $var_message = 'failure';
                     $responseArray  = array('status' => 0,'error'=> $validation_result['message'],'data'=> array());
                }

                        return $responseArray;
    } 
    
  //// for buying products on sale  
    
    function buySaleProductAPI($post_values,$user_id)
{
     global $conn;  
     include_once("apifunctions.php");
     include_once("language.php");
     
    $saleid = $post_values["saleid"];
    $source = $post_values["type"];

     if(trim($source) ==""){
            $message        = TYPE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($source) != 1 && trim($source) != 2 && trim($source) != 3){
            $message        = INVALID_TYPE_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($saleid) == ""){
            $message        = SALE_ID.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($user_id) == ""){
            $message        = USER_NOT_FOUND;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
      
    
    $sql = "SELECT  nUserId,nQuantity,nShipping,nValue,nPoint FROM " . TABLEPREFIX . "sale where nSaleId ='" . addslashes($saleid) . "'";
    $result     = mysqli_query($conn, $sql);
    $numRows    = mysqli_num_rows($result); 
    if ($numRows>0) {
        $row = mysqli_fetch_array($result);
        $seller_id = $row["nUserId"];
        $nQuantity = $row["nQuantity"];
        
        if($nQuantity<=0)
        {
           $message        = 'Requested '.$nQuantity.' '.ERR_ALREADY_PURCHASED;//ERROR_REQUESTED_ITEM_ALREADY_PURCHASED;//"The user ID on the parameters should be numeric value";
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
            
      
        }
    }
        

    //get posted values from form
    $quantityREQD = $post_values["quantityREQD"];
    $quantityREQD = (abs($quantityREQD) > 0) ? abs($quantityREQD) : 1;
    $amount       = $post_values["amount"];
    $total        = $post_values["total"];
    $points       = $post_values["points"];
    $total_points = $post_values["total_points"];
    $nSaleId      = $saleid;
    $nUserId      = $user_id;//$_SESSION["guserid"];
    $vAddress1    = $post_values["vAddress1"];
    $vAddress2    = $post_values["vAddress2"];
    $vCity        = $post_values["vCity"];
    $vState       = $post_values["vState"];
    $Country      = $post_values["vCountry"];
    $nZip         = $post_values["nZip"];
    $vPhone       = $post_values["vPhone"];
    $lang_id      = isset($post_values['lang_id'])?$post_values['lang_id']:1;
    $lang_id      = ($lang_id!='')?$lang_id:1;
    //make sure the quanity asked is not purchased by another user
    $sql = "SELECT  nUserId,nQuantity,nShipping,nValue,nPoint FROM " . TABLEPREFIX . "sale where nSaleId ='" . addslashes($nSaleId) . "'";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_array($result)) {
        $seller_id = $row["nUserId"];
        $nQuantity = $row["nQuantity"];
        $db_shipping = $row["nShipping"];
        $db_price = $row["nValue"];
        $db_point = $row["nPoint"];
        $db_total = $db_shipping + $db_price;
    }
    $flag = true;
    $EnablePoint = DisplayLookUp('EnablePoint');
       
    if ($EnablePoint != '0') {//if not price only
        
        $sql = mysqli_query($conn, "select C.content_name, L.content from " . TABLEPREFIX . "content C
                        LEFT JOIN " . TABLEPREFIX . "content_lang L on C.content_id = L.content_id and L.lang_id = '" . $lang_id . "'
                        where C.content_type='' and C.content_status='y' and C.content_name='PointName'") or die(mysqli_error($conn));
        
        $row = mysqli_fetch_array($sql);
        $point_name=utf8_encode($row['content']);
        
        
        $sql = "select nPoints from " . TABLEPREFIX . "usercredits where nUserId='".$nUserId."'";
        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if ($row = mysqli_fetch_array($res)){//user has purchased points
            if ($row['nPoints']<$total_points ){//if sufficient point is not available
                $flag = false;
                $message = 'Available '.$point_name.' is less.';
            }
            else //sufficient point is available
                $flag = true;
        }
        else if ($total_points==0){//points not required for the purchase
            $flag = true;
        }
        else{//user has not purchased a point yet
            $flag = false;
            $message = 'No '.$point_name.' available in your account.';
        }
    }
    
    if(($vAddress1=="" || $vCity=="" || $vState=="" || $Country=="" || $nZip=="" || $vPhone=="") && $flag == true && $message=='')
    {
        $flag = false;
        $message = MANDATORY_FIELDS_COMPULSORY;
    } 
    
    if ($flag == true && $message==''){
    //if enough quanity of item is avalable
    if ($nQuantity >= $quantityREQD) {

        $total = $db_total * $quantityREQD;  //edited on Jan 13, 2005
        $total = round($total, 2);
        $total_points = $db_point * $quantityREQD;
        $total_points = round($total_points, 2);
        
        /*
        Commented on 09 Dec 2011
        Reduce the required qunatity when payment success
        //reduce requested quantity from the master table
        $sql = "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity - $quantityREQD where nSaleId ='" . addslashes($nSaleId) . "'";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        */
        
        $success_fee = DisplayLookUp('SuccessFee');//success fee for the transaction
        $free_trans_level = DisplayLookUp('freeTransactionsPerMonth');//no. of free trans per month
        $paid_trans = 'N';
        $this_user = $seller_id;
        $succ_trans_sql = "select s.nUserId from " . TABLEPREFIX . "saledetails sd left join " . TABLEPREFIX . "sale s on s.nSaleId = sd.nSaleId where sd.vSaleStatus>=2 and s.nUserId = '".$this_user."' and dDate > '".date('Y-m-').'01 00:00:00'."'
                                    union 
                               select st.nUserId from " . TABLEPREFIX . "swaptxn st where st.vStatus = 'A' and st.dDate > '".date('Y-m-').'01 00:00:00'."' and st.nUserId = '".$this_user."'
                                   union 
                               select st2.nUserReturnId from " . TABLEPREFIX . "swaptxn st2 where st2.vStatus = 'A' and st2.dDate > '".date('Y-m-').'01 00:00:00'."' and st2.nUserReturnId = '".$this_user."'";
        $succ_trans_res = mysqli_query($conn, $succ_trans_sql) or die(mysqli_error($conn));//to count the no. of trans
        if (mysqli_num_rows($succ_trans_res) >= $free_trans_level) $paid_trans = 'Y';

        if ($success_fee > 0 && $paid_trans == 'Y'){//if transaction fee needs to be paid make the entries
            mysqli_query($conn, "INSERT INTO " . TABLEPREFIX . "successfee (nUserId,nPurchaseBy,nProdId,nAmount,
                                nPoints,dDate,vType) VALUES ('" . $this_user . "','" . $user_id . "',
                                '" . $nSaleId . "','" . $success_fee . "','0',now(),'sa')") or die(mysqli_error($conn));
        }
        
        //transfer the requested quantity to the temp table
        $now = date('Y-m-d H:i:s');

        //checking escrow status
        if (DisplayLookUp('Enable Escrow') == 'Yes') {
            $SaleStatus = '1';
        }
        else {
            $SaleStatus = "4";
        }

        $sql = "insert into " . TABLEPREFIX . "saledetails(nSaleId,nUserId,nAmount,nPoint,dDate,nQuantity,vSaleStatus,vRejected,vAddress1,vAddress2,vCity,vState,vCountry,nZip,vPhone) values(";
        $sql .= "'" . addslashes($nSaleId) . "',";
        $sql .= "'" . $nUserId . "',";
        $sql .= "'" . $total . "',";
        $sql .= "'" . $total_points . "',";
        $sql .= "'" . $now . "',";
        $sql .= "'" . $quantityREQD . "',";
        $sql .= "'" . $SaleStatus . "',";
        $sql .= "'0',";
        $sql .= "'" . $vAddress1 . "',";
        $sql .= "'" . $vAddress2 . "',";
        $sql .= "'" . $vCity . "',";
        $sql .= "'" . $vState . "',";
        $sql .= "'" . $Country . "',";
        $sql .= "'" . $nZip . "',";
        $sql .= "'" . $vPhone."')";
        
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        
        if ($EnablePoint != '0') {
            $sql = "update " . TABLEPREFIX . "usercredits set nPoints = nPoints - ".$total_points." where nUserId='".$nUserId."'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));//decrementing the points for buyer

            $sql = "update " . TABLEPREFIX . "usercredits set nPoints = nPoints + ".$total_points." where nUserId='".$seller_id."'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));//incrementing the points for seller
        }
        //redirect to payment page
        //CZQ check for zero quantity,if zero, decrease the no from categories
        if (($nQuantity - $quantityREQD) == 0) { 
            $sql = "SELECT C.vRoute FROM " . TABLEPREFIX . "sale S inner join " . TABLEPREFIX . "category
                                      C on S.nCategoryId = C.nCategoryId where nSaleId='" . addslashes($nSaleId) . "'";

            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                if ($row = mysqli_fetch_array($result)) {

                    $sql = "Update " . TABLEPREFIX . "category set nCount=nCount - 1 where nCategoryId IN(" . $row["vRoute"] . ")";
                    mysqli_query($conn, $sql) or die(mysqli_error($conn));
                }
            }
        }

             $message='Success';
//             $result=array();
//             $result['saleid']=$nSaleId;
//             $result['source']=$source;
//             $result['amount']=$amount;
//             $result['total']=$total;
//             $result['points']=$points;
//             $result['total_points']=$total_points;
//             $result['required_quantity']=$quantityREQD;
//             $result['date']=$now;
              $result         = array('saleid'=>$nSaleId,'source'=>$source,'amount'=>$amount,'total'=>$total,'points'=>$points,'total_points'=>$total_points,'required_quantity'=>$quantityREQD,'date'=>$now);
        $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
        return $responseArray;
        
        
    }
    else {
         //if enough quanity of item is not avalable
        $message='Requested '.$nQuantity.' '.ERR_ALREADY_PURCHASED;//ERROR_REQUESTED_ITEM_ALREADY_PURCHASED;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
        
       
        
    }
    }
    else
    {
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }

}

    
function getShippingDetailsAPI($saleid,$user_id,$date)
{
    global $conn;
    include_once("apifunctions.php");
    include_once("language.php");
        if(trim($saleid) == ""){
            $message        = SALE_ID.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($user_id) == ""){
            $message        = USER_NOT_FOUND;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else if(trim($date) == ""){
            $message        = DATE_TXT.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
    
    
    $sql = "Select s.vTitle,s.nValue as rate,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected,sd.vAddress1,sd.vAddress2,sd.vCity,sd.vState,sd.vCountry,sd.nZip,sd.vPhone,us.stripe_pub_key, s.nUserId as seller_user from " . TABLEPREFIX . "saledetails sd join " . TABLEPREFIX . "sale s on sd.nSaleId=s.nSaleId join " . TABLEPREFIX . "users us on s.nUserId=us.nUserId where ";
    $sql .= " sd.nSaleId='" . $saleid . "' AND sd.nUserId='" . $user_id . "' AND sd.dDate='";
    $sql .= urldecode($date) . "' ";
//    return $sql;
//    $sql = "SELECT  * FROM " . TABLEPREFIX . "saledetails where nSaleId ='" . addslashes($saleid) . "'";
    $result     = mysqli_query($conn, $sql);
    $numRows    = mysqli_num_rows($result); 
    if ($numRows>0) {
//        $result         = array();
        $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
        $seller_pub_key = get_stripe_public_key($row['seller_user']);
        $row['seller_pub_key'] = $seller_pub_key;
//        return $row;
//        $result=
        $message= '';
        $responseArray  = array('status' => 1,'error'=> $message,'data'=> $row);
        return $responseArray;
        
    }
    else
    {
        $message= TXT_SHIPPING_DETAILS.TXT_NOT_FOUND;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    
    
}



function makeofferAPI($post_array,$user_id)
{ 
    global $conn,$sitestyle,$logourl;
    include_once("apifunctions.php");
    include_once("language.php");
//    $nSTId = $post_array["nSTId"];//primary key
//    $other_user = $post_array["userid"];
    $post_type = $post_array["post_type"];//wish or swap --
    $var_swapid = $post_array["swapid"];
    $var_swap_hidden__id = $post_array["chkSwap_hidden"];
    $var_swap_user_id = $post_array["chkSwap_user_hidden"];
    $var_mpay = $post_array["txtMpay"];
    $var_hpay = $post_array["txtHpay"];
    
    
    
     if(trim($post_type) == ""){
            $message        = POST_TYPE.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($var_swapid) == ""){
            $message        = SWAP_ID_NOT_FOUND;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        else if(trim($var_swap_hidden__id) == ""){
            $message        = CHKSWAP_HIDDEN.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($var_swap_user_id) == ""){
            $message        = CHKSWAP_USER_HIDDEN.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        /* else if(trim($var_mpay) == ""){
            $message        = "txtMpay is missing on the parameters.";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($var_hpay) == ""){
            $message        = "txtHpay is missing on the parameters.";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }*/
    
    
    $var_mpoint = isset($post_array["txtMpoint"])?$post_array["txtMpoint"]:'';
    $var_hpoint = isset($post_array["txtHpoint"])?$post_array["txtHpoint"]:'';
    $var_description = isset($post_array["txtAdditional"])?addslashes($post_array["txtAdditional"]):'';
    $var_other_user = isset($post_array["other_user"])?$post_array["other_user"]:'';
    $parent_id = isset($post_array["parent_id"])?$post_array["parent_id"]:'';//paren
    $lang_id      = isset($post_array['lang_id'])?$post_array['lang_id']:1;
    $lang_id      = ($lang_id!='')?$lang_id:1;

// if ($post_array["postback"] == "Y") {//add
    
    
    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$lang_id."'";
    $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
    $langRw = mysqli_fetch_array($langRs);


        $counter_offer  = 'N';
        if($post_array['counter_offer']=='Y')
        {
            $counter_offer  =   'Y';
        }
        
        if ($parent_id!=0){
            $wishsql = "Select wishedId from " . TABLEPREFIX . "swaptxn where nSTId ='$parent_id'";
            $wishRs = mysqli_query($conn, $wishsql) or die(mysqli_error($conn));
            $wishRw = mysqli_fetch_array($wishRs);
            $wishedId   = $wishRw['wishedId'];

        }
         
            $wishid = $post_type =='wish' ? ($post_array['swapid'] ? $post_array['swapid'] : $wishedId) : '';

            $sql = "INSERT INTO ".TABLEPREFIX."swaptxn (nSwapId, nSwapReturnId, nUserId, nUserReturnId, nAmountGive, nAmountTake, 
                    nPointGive, nPointTake, nParentId, vPostType, vStatus, dDate, vText, wishedId) VALUES 
                    ('".$var_swap_hidden__id."', '".$var_swap_user_id."', '".$user_id."', '".$var_other_user."', '".$var_mpay."', 
                    '".$var_hpay."', '".$var_mpoint."', '".$var_hpoint."', '".$parent_id."', '".$post_type."', 'O', now(), 
                    '".$var_description."', '".$wishid."')";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));           
        //exit;
         // send email notification to other user
        if($counter_offer=="N"){
            $mailRw = array();
            $mailSql = "SELECT L.content,L.content_title
              FROM ".TABLEPREFIX."content C
              JOIN ".TABLEPREFIX."content_lang L
                ON C.content_id = L.content_id
               AND C.content_name = 'newofferReceived'
               AND C.content_type = 'email'
               AND L.lang_id = '".$lang_id."'";
           
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);
            
            $condition = "where nUserId='" . $var_other_user . "'";
            $UserName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
            
            $UserEmail  =  getUserEmail($var_other_user);
            $login_username = ucfirst(get_login_user_name($user_id));
            $mainTextShow   = $mailRw['content'];
            $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{receiver_user_name}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($login_username));
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
            $StyleContent=MailStyle($sitestyle,SITE_URL);
            $EMail = $UserEmail; 
            
          

        //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, ucfirst($UserName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            
            $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
          
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
              
             send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
        }
       // echo $counter_offer;exit;
      
         if($counter_offer=="Y"){
            $mailRw = array();
            $mailSql = "SELECT L.content,L.content_title
              FROM ".TABLEPREFIX."content C
              JOIN ".TABLEPREFIX."content_lang L
                ON C.content_id = L.content_id
               AND C.content_name = 'counterOfferReceived'
               AND C.content_type = 'email'
               AND L.lang_id = '".$lang_id."'";
            //echo $mailSql;exit;
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);
            
            $condition = "where nUserId='" . $var_other_user . "'";
            $UserName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
            
            $UserEmail  =  getUserEmail($var_other_user);
            $login_username = ucfirst(get_login_user_name($user_id));//ucfirst($_SESSION["gloginname"]);
            $mainTextShow   = $mailRw['content'];
            //echo $mainTextShow;exit;
            $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{receiver_user_name}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($login_username));
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
            $StyleContent=MailStyle($sitestyle,SITE_URL);
            $EMail = $UserEmail; 
            

        //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, ucfirst($UserName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            
            $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
             
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
            
          //echo $subject.'<br />'.$EMail.'<br />'.$msgBody;exit;
            //send_mail('nirmala.v@armiasystems.com', $subject, $msgBody, SITE_EMAIL, 'Admin');
          
            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
        }
        if ($parent_id!=0){
            $sql_up = "Update " . TABLEPREFIX . "swaptxn set vStatus = 'N' where nSTId='".$parent_id."' and vStatus!='A'";
            mysqli_query($conn, $sql_up) or die(mysqli_error($conn));//invalidating the parent offer
        }
    
        $result=array('swapid'=>$var_swapid);
        $message= '';
        $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
        return $responseArray;
//        header('location:makeofferconfirm.php?mode=add&flag=true&swapid=' . $var_swapid);
//        exit();
//    }
    
        
        }

        
        
        
        
        function makeoffer_editAPI($post_array,$user_id)
{
    global $conn;//,$sitestyle,$logourl;
    include_once("apifunctions.php");
    include_once("language.php");
    $nSTId = $post_array["nSTId"];//primary key
    //$post_type = $post_array["post_type"];//wish or swap --
    //$var_swapid = $post_array["swapid"];
    $var_swap_hidden__id = $post_array["chkSwap_hidden"];
    $var_swap_user_id = $post_array["chkSwap_user_hidden"];
    $var_mpay = $post_array["txtMpay"];
    $var_hpay = $post_array["txtHpay"];
    
    
    if(trim($nSTId) == ""){
            $message        = "nSTId".MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
     /*else if(trim($post_type) == ""){
            $message        = "Post type is missing on the parameters";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($var_swapid) == ""){
            $message        = "Swap id not found,Invalid auth token";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }*/
        else if(trim($var_swap_hidden__id) == ""){
            $message        = CHKSWAP_HIDDEN.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($var_swap_user_id) == ""){
            $message        = CHKSWAP_USER_HIDDEN.MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
        /* else if(trim($var_mpay) == ""){
            $message        = "txtMpay is missing on the parameters.";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }
         else if(trim($var_hpay) == ""){
            $message        = "txtHpay is missing on the parameters.";
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }*/
    
    $var_mpoint = isset($post_array["txtMpoint"])?$post_array["txtMpoint"]:'';
    $var_hpoint = isset($post_array["txtHpoint"])?$post_array["txtHpoint"]:'';
    $var_description = isset($post_array["txtAdditional"])?addslashes($post_array["txtAdditional"]):'';

     $sql = "update ".TABLEPREFIX."swaptxn set 
                    nSwapId = '".$var_swap_hidden__id."',
                    nSwapReturnId = '".$var_swap_user_id."',
                    nAmountGive = '".$var_mpay."',
                    nAmountTake = '".$var_hpay."',
                    nPointGive = '".$var_mpoint."',
                    nPointTake = '".$var_hpoint."',
                    dDate = now(),
                    vText = '".$var_description."'
                where nSTId='" . $nSTId . "' AND nUserId= '" . $user_id . "' 
               ";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        
        
//        header("location:makeofferconfirm.php?mode=edit&flag=true");
//        exit();
    
        $result=array(0=>"Success");
        $message= '';
        $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
        return $responseArray;
        
       }
       
       
       
       
       
       
       
        
       
function makeoffer_deleteAPI($post_array,$user_id)
{
    global $conn;//,$sitestyle,$logourl;
    include_once("apifunctions.php");
    include_once("language.php");
    $nSTId = $post_array["nSTId"];//primary key

    if(trim($nSTId) == ""){
            $message        = "nSTId".MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }

    $sql = "Delete from " . TABLEPREFIX . "swaptxn where nSTId='" . $nSTId . "' AND nUserId= '" . $user_id . "' ";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
//        header("location:makeofferconfirm.php?mode=delete&flag=true");
//        exit();
//        
        $result=array(0=>"Success");
        $message= '';
        $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
        return $responseArray;
        
       }
       
       
       
       
       
       
       
       
       
       
       
       
      
 function makeoffer_approveAPI($post_array)
{
//     error_reporting(E_ALL);
//     ini_set('display_errors', 1);
//     
     
    global $conn,$sitestyle,$logourl;
    include_once("apifunctions.php");
    include_once("language.php");
    $nSTId = $post_array["nSTId"];//primary key

//    
    if(trim($nSTId) == ""){
            $message        = "nSTId".MISSING_PARAMETER;
            $result         = array();
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
            return $responseArray;
        }

    $var_other_user = isset($post_array["other_user"])?$post_array["other_user"]:'';
    $parent_id = isset($post_array["parent_id"])?$post_array["parent_id"]:'';//paren
    $lang_id      = isset($post_array['lang_id'])?$post_array['lang_id']:1;
    $lang_id      = ($lang_id!='')?$lang_id:1;
    $message= '';
    
    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$lang_id."'";
    $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
    $langRw = mysqli_fetch_array($langRs);

    $sql = "SELECT * from " . TABLEPREFIX . "swaptxn where nSTId='" . $nSTId . "'";
        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if ($srow = mysqli_fetch_array($res)){
            $return_status=1;
            $nSwapId_array = explode(',',$srow['nSwapId']);
            $nSwapId_user_array = explode(',',$srow['nSwapReturnId']);
            
            $points = ($srow['nPointGive'] - $srow['nPointTake']);
            if ($points != 0){
                $sql = "UPDATE " . TABLEPREFIX . "usercredits SET nPoints=nPoints-".$points." WHERE nUserId='" . $srow['nUserId'] . "'";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//points for offered user

                $sql = "UPDATE " . TABLEPREFIX . "usercredits SET nPoints=nPoints+".$points." WHERE nUserId='" . $srow['nUserReturnId'] . "'";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//points for accepting user
            }
            
            /******** Check if the transaction limit for the swap transactions users are over or not ******/
            $success_fee        = DisplayLookUp('SuccessFee');//success fee for the transaction
            $free_trans_level   = DisplayLookUp('freeTransactionsPerMonth');//no. of free trans per month
            
            $paid_trans         = 'N';
            $succ_trans_sql_1     = "SELECT s.nUserId from " . TABLEPREFIX . "saledetails as sd "
                                        . "LEFT JOIN " . TABLEPREFIX . "sale s on s.nSaleId = sd.nSaleId "
                                        . "WHERE sd.vSaleStatus >= 2 "
                                        . "and s.nUserId = '".$srow['nUserId']."' "
                                        . "and sd.dDate > '".date('Y-m-').'01 00:00:00'."'";
            $succ_trans_res_1     = mysqli_query($conn, $succ_trans_sql_1) or die(mysqli_error($conn));
            
            $succ_trans_sql_2     = "SELECT st.nUserId from ".TABLEPREFIX."swaptxn as st "
                                        . "WHERE st.vStatus = 'A' "
                                        . "AND st.dDate > '".date('Y-m-').'01 00:00:00'."' "
                                        . "AND st.nUserId = '".$srow['nUserId']."'";
            $succ_trans_res_2     = mysqli_query($conn, $succ_trans_sql_2) or die(mysqli_error($conn));
            
            $succ_trans_sql_3     = "SELECT st2.nUserReturnId from ".TABLEPREFIX."swaptxn as st2 "
                                        . "WHERE st2.vStatus = 'A' "
                                        . "AND st2.dDate > '".date('Y-m-').'01 00:00:00'."' "
                                        . "AND st2.nUserReturnId = '".$srow['nUserId']."'";
            $succ_trans_res_3     = mysqli_query($conn, $succ_trans_sql_3) or die(mysqli_error($conn));
                                
           
            
            if (mysqli_num_rows($succ_trans_res_1) >= $free_trans_level || mysqli_num_rows($succ_trans_res_2) >= $free_trans_level || mysqli_num_rows($succ_trans_res_3) >= $free_trans_level){  //echo "h1111";
                $paid_trans = 'Y'; //If transactions are more than the allowed free limit
            }
            
            if ($success_fee > 0 && $paid_trans == 'Y'){//if transaction fee needs to be paid make the entries
                $sql = "INSERT INTO " . TABLEPREFIX . "successfee (
                        nUserId,
                        nPurchaseBy,
                        nProdId,
                        nAmount,
                        nPoints,
                        dDate,
                        vType
                        ) 
                        VALUES(
                        '".$srow['nUserId']."',"
                        . "'" . $srow['nUserReturnId']."',"
                        . "'" . $nSwapId_array[0]."',"
                        . "'" . $success_fee."',"
                        . "'0',"
                        . "now(),"
                        . "'".(($srow['vPostType']=='swap')?'s':'w')."'"
                        . ")";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//success fee
            }
            
            $paid_trans = 'N';
            $succ_trans_sql_1 = "select s.nUserId FROM " . TABLEPREFIX . "saledetails as sd "
                    . "LEFT JOIN " . TABLEPREFIX . "sale s on s.nSaleId = sd.nSaleId "
                    . "WHERE sd.vSaleStatus >= 2 "
                    . "and s.nUserId = '".$srow['nUserReturnId']."' "
                    . "and sd.dDate > '".date('Y-m-').'01 00:00:00'."'";
            $succ_trans_res_1 = mysqli_query($conn, $succ_trans_sql_1) or die(mysqli_error($conn));//to count the no. of trans
            
            $succ_trans_sql_2 = "SELECT st.nUserId FROM `".TABLEPREFIX."swaptxn` as st "
                    . "WHERE st.vStatus = 'A' "
                    . "and st.dDate > '".date('Y-m-').'01 00:00:00'."' "
                    . "and st.nUserId = '".$srow['nUserReturnId']."'";
            $succ_trans_res_2 = mysqli_query($conn, $succ_trans_sql_2) or die(mysqli_error($conn));//to count the no. of trans
            
            $succ_trans_sql_3 = "SELECT st2.nUserReturnId FROM `".TABLEPREFIX."swaptxn` as st2 "
                    . "WHERE st2.vStatus = 'A' "
                    . "and st2.dDate > '".date('Y-m-').'01 00:00:00'."' "
                    . "and st2.nUserReturnId = '".$srow['nUserReturnId']."'";
            $succ_trans_res_3 = mysqli_query($conn, $succ_trans_sql_3) or die(mysqli_error($conn));//to count the no. of trans
            
           
            if (mysqli_num_rows($succ_trans_res_1) >= $free_trans_level || mysqli_num_rows($succ_trans_res_2) >= $free_trans_level || mysqli_num_rows($succ_trans_res_3) >= $free_trans_level){ 
                $paid_trans = 'Y'; //If transactions are more than the allowed free limit
            }
            
            if ($success_fee > 0 && $paid_trans == 'Y'){//if transaction fee needs to be paid make the entries
                $sql = "INSERT INTO " . TABLEPREFIX . "successfee (
                    nUserId,
                    nPurchaseBy,
                    nProdId,
                    nAmount,
                    nPoints,
                    dDate,
                    vType
                    ) VALUES(
                    '" . $srow['nUserReturnId'] . "',"
                    . "'" . $srow['nUserId'] . "',"
                    . "'" . $nSwapId_user_array[0] . "',"
                    . "'" . $success_fee . "',"
                    . "'0',"
                    . "now(),"
                    . "'".(($srow['vPostType']=='swap')?'s':'w')."'"
                    . ")";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));//success fee for the other user
            }
            /******** Check if the transaction limit for the swap transactions users are over or not ******/
                                    
            $sql = "Update " . TABLEPREFIX . "swaptxn set vStatus ='A' where nUserId = '" . $var_other_user . "'  AND nSTId='" . $nSTId . "'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));//Update the status of the offer
            
            //$swaps = $srow['nSwapId'].','.$srow['nSwapReturnId'];
            $sql = "Update " . TABLEPREFIX . "swap set vSwapStatus='1', nSwapMember = '".$srow['nUserReturnId']."' where nSwapId IN(".$srow['nSwapId'].") ";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));//update thes swap status
            
            $sql = "Update " . TABLEPREFIX . "swap set vSwapStatus='1', nSwapMember = '".$srow['nUserId']."' where nSwapId IN(".$srow['nSwapReturnId'].") ";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));//update thes swap status

            $sql = "UPDATE " . TABLEPREFIX . "swap SET vSwapStatus='1' WHERE nSwapId='" . $srow['wishedId'] . "'";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            
            $parent_id = $srow['nParentId'];
            while ($parent_id != 0){
                $sql_counter = "SELECT * from " . TABLEPREFIX . "swaptxn where nSTId='" . $parent_id . "'";
                $res_counter = mysqli_query($conn, $sql_counter) or die(mysqli_error($conn));
                if ($srow_counter = mysqli_fetch_array($res_counter)){
                    $sql_up = "Update " . TABLEPREFIX . "swaptxn set vStatus = 'N' where nSTId='".$srow_counter['nSTId']."' and vStatus!='A'";
                    mysqli_query($conn, $sql_up) or die(mysqli_error($conn));//if counter offer exits make the previous offers invalid
                    $parent_id = $srow_counter['nParentId'];
                }
            }
            
            $all_swap_array = explode(',',$srow['nSwapId'].','.$srow['nSwapReturnId']);
            foreach($all_swap_array as $allkey => $allval){//loop all the swap and swapreturn ids
                $sql_up = "Update " . TABLEPREFIX . "swaptxn set vStatus = 'N' 
                        where (nSwapId like '".$allval.",%' or nSwapId like '%,".$allval.",%' or nSwapId like '%,".$allval."' or nSwapId = '".$allval."'
                                or
                                nSwapReturnId like '".$allval.",%' or nSwapReturnId like '%,".$allval.",%' or nSwapReturnId like '%,".$allval."' or nSwapReturnId = '".$allval."'
                                ) 
                                and vStatus!='A'";
                mysqli_query($conn, $sql_up) or die(mysqli_error($conn));//if the swap item is present in another offer, make the offer invalid
            }
            
            
            $sql = mysqli_query($conn, "select C.content_name, L.content from " . TABLEPREFIX . "content C
                        LEFT JOIN " . TABLEPREFIX . "content_lang L on C.content_id = L.content_id and L.lang_id = '" . $lang_id . "'
                        where C.content_type='' and C.content_status='y' and C.content_name='PointName'") or die(mysqli_error($conn));
        
        $row = mysqli_fetch_array($sql);
        $point_name=utf8_encode($row['content']);
            
//            $point_name=utf8_encode($row['content']);
            
            if ($points<0)
                $message = "-$points $point_name successfully deducted from your account.";//str_replace('{point_name}',$point_name,str_replace('{points}', (-1 * $points), "-$points $point_name successfully deducted from your account."));
            elseif ($points>0)
                $message = "$points $point_name successfully added to your account.";//str_replace('{point_name}',$point_name,str_replace('{points}', $points, "$points $point_name successfully added to your account."));
            
            $mailRw = array();
            $mailSql = "SELECT L.content,L.content_title
              FROM ".TABLEPREFIX."content C
              JOIN ".TABLEPREFIX."content_lang L
                ON C.content_id = L.content_id
               AND C.content_name = 'offerAccepted'
               AND C.content_type = 'email'
               AND L.lang_id = '".$lang_id."'";
            //echo $mailSql;exit;
            $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
            $mailRw  = mysqli_fetch_array($mailRs);
            
            $condition = "where nUserId='" . $var_other_user . "'";
            $UserName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
            
            $UserEmail  =  getUserEmail($var_other_user);
            $login_username = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');//ucfirst($_SESSION["gloginname"]);
            $mainTextShow   = $mailRw['content'];
            //echo $mainTextShow;exit;
            $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{user_name}");
            $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($login_username));
            $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

            $mailcontent1   = $mainTextShow;

            $subject    = $mailRw['content_title'];
            $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);
            $StyleContent=MailStyle($sitestyle,SITE_URL);
            $EMail = $UserEmail; 
            
           

        //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, ucfirst($UserName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            
            $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
             
            $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
            
//            return $EMail. $subject. $msgBody. SITE_EMAIL;
       
            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
            
//            $result=array('nSTId'=>$nSTId);
//            $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
//        return $responseArray;

        }
        else {
            $message=SWAP_DETAILS_NOT_FOUND;
            $return_status=0;

        }
    $result=array('nSTId'=>$nSTId);
            $responseArray  = array('status' => $return_status,'error'=> $message,'data'=> $result);
        return $responseArray;
   
        
        }
       
       
       
   function makeoffer_rejectAPI($post_array)
   {
        global $conn;//,$sitestyle,$logourl;
        include_once("apifunctions.php");
        include_once("language.php");
        $nSTId = $post_array["nSTId"];//primary key
        //$var_swapid = $post_array["swapid"];
        $var_other_user = $post_array["other_user"];
    
        if(trim($nSTId) == ""){
                $message        = "nSTId".MISSING_PARAMETER;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray;
            }
         else if(trim($var_other_user) == ""){
            $message        = OTHER_USER_ID.MISSING_PARAMETER;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray; 
         }
             $sql = "Update " . TABLEPREFIX . "swaptxn set vStatus ='R' where  nUserId = '" . $var_other_user . "'  AND nSTId='" . $nSTId . "' ";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));//updating to rejected status
           $message='';
        $result=array('swapid'=>$nSTId,'userid'=>$var_other_user);
            $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
        return $responseArray;
      
           
     }
     
     
     
     function makeoffer_deliverAPI($post_array)
     {
         global $conn;//,$sitestyle,$logourl;
         include_once("apifunctions.php");
         include_once("language.php");
        $nSTId = $post_array["nSTId"];//primary key
       
        if(trim($nSTId) == ""){
                $message        = "nSTId".MISSING_PARAMETER;
                $result         = array();
                $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
                return $responseArray;
            }
         
         
         $sql = "SELECT * from " . TABLEPREFIX . "swaptxn where nSTId='" . $nSTId . "'";
        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if ($srow = mysqli_fetch_array($res)){
            //owner fields not required
            if ($srow['nUserId']==$_SESSION["guserid"])//offered person updates delivery
                $sql = "Update " . TABLEPREFIX . "swap set vPartnerDelivery='Y',dPartnerDate=now() where nSwapId in (" . $srow['nSwapReturnId'] . ")";
            else//accepting person updates delivery
                $sql = "Update " . TABLEPREFIX . "swap set vPartnerDelivery='Y',dPartnerDate=now() where nSwapId in (" . $srow['nSwapId'] . ")";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }
        $message= MESSAGE_DELIVERY_STATUS_UPDATED;
         $result=array('nSTId'=>$nSTId);
        $responseArray  = array('status' => 1,'error'=> $message,'data'=> $result);
        return $responseArray;
         
//        echo "<script>alert('".MESSAGE_DELIVERY_STATUS_UPDATED."');</script>";
//        header("location:makeoffer.php?post_type=".$_POST['post_type']."&uname=".$_POST['uname']."&nSTId=".$nSTId);
//        exit;
//        
     }
       

        
        
        
        
     
        
             
     function make_offer_viewAPI($post_array,$user_id)
     {
         global $conn;
         include_once("apifunctions.php");
         include_once("language.php");
         
       $nSTId = $post_array["nSTId"];//primary key
    $other_user = $post_array["other_userid"];
     
    
     if(trim($nSTId) == ""){
        $message        = "The nSTId ".MISSING_PARAMETER;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    else if(!is_numeric($other_user)){
        $message        = OTHER_USER_ID.MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    
    

        mysqli_query($conn, "Update " . TABLEPREFIX . "swaptxn set vBlink ='N' WHERE nSTId='" . $nSTId . "'") or die(mysqli_error($conn));//update the (new)blink to 'N'
        

//$sql = "SELECT ST.* from " . TABLEPREFIX . "swaptxn ST where ST.nSTId='" . $nSTId . "'";
          $sql = "SELECT ST.*, U.vLoginName as user, UR.vLoginName as return_user, U.stripe_pub_key as user_stripe_key, UR.stripe_pub_key as other_user_stripe_key  from " . TABLEPREFIX . "swaptxn ST
                    left join " . TABLEPREFIX . "users U on U.nUserId = ST.nUserId
                    left join " . TABLEPREFIX . "users UR on UR.nUserId = ST.nUserReturnId
                    where ST.nSTId='" . $nSTId . "'";
        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if ($srow = mysqli_fetch_array($res)){
            $other_user_name = $srow['return_user'];
            $this_user_name = $srow['user'];
            
            //echo "thisUser=>".$this_user."---Row-----".$srow['nUserId'];
            
            if ($srow['nUserId']==$user_id && $srow['vStatus']=='O') {//edit mode for the offering person
              
                $mode = 'edit';
                $other_user = $srow['nUserReturnId'];
                $other_user_name = $srow['return_user'];
                $this_user_name = $srow['user'];
                $nSwapId_array = explode(',',$srow['nSwapId']);
                $nSwapId_user_array = explode(',',$srow['nSwapReturnId']);
                $nAmountGive = $srow['nAmountGive'];
                $nAmountTake = $srow['nAmountTake'];
                $AdditionalInfo = $srow['vText'];
                
                /*if ($srow['vStatus']<>'O'){
                    $var_error_message = ERROR_CANNOT_COMPLETE_THIS_OFFER_REASON."<br>&nbsp;&nbsp;<br>1)".ERROR_ITEM_POSTED_BY_YOU."<br>2)".ERROR_ITEM_NOT_VALID;
                }*/
            }
            else {
                
                $mode = 'view';
                if ($srow['nUserId']==$user_id){//view mode for the offering person
                    $other_user = $srow['nUserReturnId'];
                    $other_user_name = $srow['return_user'];
                    $this_user_name = $srow['user'];
                    $nSwapId_array = explode(',',$srow['nSwapId']);
                    $nSwapId_user_array = explode(',',$srow['nSwapReturnId']);
                    $nAmountGive = $srow['nAmountGive'];
                    $nAmountTake = $srow['nAmountTake'];
                    $AdditionalInfo = $srow['vText'];
                }
                else {//view mode for the accepting person
                    $other_user = $srow['nUserId'];
                    $other_user_name = $srow['user'];
                    $this_user_name = $srow['return_user'];
                    $nSwapId_array = explode(',',$srow['nSwapReturnId']);
                    $nSwapId_user_array = explode(',',$srow['nSwapId']);
                    $nAmountGive = $srow['nAmountGive'];
                    $nAmountTake = $srow['nAmountTake'];
                    $AdditionalInfo = $srow['vText'];
                }
               
            }
           
            if($other_user_name ==''){
                $other_user_name = $_GET["uname"];
            }
            else{
                if($srow['nUserId']!='')  
                 $other_user_name = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', "WHERE nUserId='" . $srow['nUserId'] . "'"), 'vLoginName');
              }
              
              
              
            
            if ($user_id == $srow['nUserId']){
                $sql_owner = "select * from " . TABLEPREFIX . "swap where nSwapId in (".$srow['nSwapReturnId'].")";
                $sql_other_user = "select * from " . TABLEPREFIX . "swap where nSwapId in (".$srow['nSwapId'].")";
            }
            else {
                $sql_owner = "select * from " . TABLEPREFIX . "swap where nSwapId in (".$srow['nSwapId'].")";
                $sql_other_user = "select * from " . TABLEPREFIX . "swap where nSwapId in (".$srow['nSwapReturnId'].")";
            }
            $res_swap = mysqli_query($conn, $sql_owner) or die(mysqli_error($conn));
            $var_owner_delivery = 'Y';
            while ($row_swap = mysqli_fetch_array($res_swap)){
                    if ($row_swap['vPartnerDelivery'] == 'N'){
                            $var_owner_delivery = 'N';
                    }
                    //$var_owner_date = $row_swap['dPartnerDate'];
            }                               
            $res_swap = mysqli_query($conn, $sql_other_user) or die(mysqli_error($conn));
            $var_partner_delivery = 'Y';
            while ($row_swap = mysqli_fetch_array($res_swap)){
                    if ($row_swap['vPartnerDelivery'] == 'N'){
                            $var_partner_delivery = 'N';
                    }
                    //$var_partner_date = $row_swap['dPartnerDate'];
            }
                                                                
        }
         
        $payment_status = 'N';
        $psql = "select vSwapStatus from " . TABLEPREFIX . "swap where vSwapStatus >= 2 and (nSwapId in (".$srow['nSwapId'].") or nSwapId in (".$srow['nSwapReturnId']."))";
    $pres = mysqli_query($conn, $psql) or die(mysqli_error($conn));
    if(mysqli_num_rows($pres)>0) $payment_status = 'Y';
    
    $return_result=array();
    $return_result['mode']=$mode;
    $return_result['status']=$srow['vStatus'];
    $return_result['other_user']=$other_user;
    $return_result['other_user_name']=$other_user_name;
    $return_result['user']=$other_user_name;
    $return_result['swap_array']=$nSwapId_array;
    $return_result['swap_id_user_array']=$nSwapId_user_array;
    $return_result['nAmountGive']= $nAmountGive;
    $return_result['nAmountTake']= $nAmountTake ;
    $return_result['AdditionalInfo']= $AdditionalInfo;
    $return_result['nUserId']= $srow['nUserId'];
    $return_result['nUserReturnId']= $srow['nUserReturnId'];
    $return_result['nSTId']= $srow['nSTId'];
    $return_result['payment_status']= $payment_status;
    $return_result['owner_delivery']= $var_owner_delivery;
    $return_result['partner_delivery']= $var_partner_delivery;
    $return_result['user_stripe_key']= $srow['user_stripe_key'];
    $return_result['other_user_stripe_key']= $srow['other_user_stripe_key'];
    $message='';
    $responseArray  = array('status' => 1,'error'=> $message,'data'=> $return_result);
    return $responseArray;
 }
     
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
     function offer_outboxAPI($user_id)
     {
    global $conn;
    include_once("apifunctions.php");
    include_once("language.php");
      $sql = "Select ST.nSwapId,ST.nUserId, ST.nUserReturnId, ST.vPostType,ST.vStatus,ST.nSTId,ST.nParentId,
                            vLoginName as 'UserName',
                            S.vTitle,date_format(ST.dDate,'%m/%d/%Y') as 'dPostDate'  from
                            " . TABLEPREFIX . "swaptxn ST
                                            Left Outer Join " . TABLEPREFIX . "users U on ST.nUserReturnId = U.nUserId
                                            Left outer join " . TABLEPREFIX . "swap S on ST.nSwapReturnId = S.nSwapId
                            where ST.nUserId = '" . $user_id . "' and ST.vStatus <> 'N'
                            Order By ST.dDate DESC";
         
            
        
        
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

               $return_result=array();
                if (mysqli_num_rows($result) > 0) {
    
                
                
                $cnt=0;
                    while ($row = mysqli_fetch_array($result)) {
                        //Populate data
                        $sqlCheck = "Select nSwapReturnId from " . TABLEPREFIX . "swapreturn where
                                                      nSwapId = '" . addslashes($row['nSwapId']) . "' AND
                                                      nUserId = '" . $user_id . "' ";
                        $resultCheck = mysqli_query($conn, $sqlCheck) or die(mysqli_error($conn));
                        $varcount = 0;
                        $var_array = "";
                        $var_check_for = "";
                        if (mysqli_num_rows($resultCheck) > 0) {
                            while ($rowCheck = mysqli_fetch_array($resultCheck)) {
                                $var_array .= "chk[$varcount]='chk" . $rowCheck["nSwapReturnId"] . "';";
                                $var_check_for .="'" . $rowCheck["nSwapReturnId"] . "',";
                                $varcount++;
                            }//end while
                        }//end if


                        $var_check_for = substr($var_check_for, 0, -1);
                        if (swapIsValid($row['nSwapId']) == true && allExists($var_check_for) == true) {
                            $newStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'vStatus', "WHERE nSwapId= '" . addslashes($row['nSwapId']) . "'
                                                                                                                                                        AND nUserId = '" . addslashes($user_id) . "' AND nSTId='" . $row['nSTId'] . "'"), 'vStatus');
                        } else {
                            $newStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'vStatus', "WHERE nSwapId= '" . addslashes($row['nSwapId']) . "'
                                                                                                                                                        AND nUserId = '" . addslashes($user_id) . "' AND nSTId='" . $row['nSTId'] . "'"), 'vStatus');
                        }//end else
                        //checking offer status

                        switch ($newStatus) {
                            case "A":
                                $shwOfferStatus = 'Accepted';//TEXT_ACCEPTED;
                                break;

                            case "R":
                                $shwOfferStatus = 'Rejected';//TEXT_REJECTED;
                                break;

                            case "N":
                                $shwOfferStatus = 'Invalid';//TEXT_INVALID;
                                break;

                            default:
                                $shwOfferStatus = 'InProcess';//TEXT_INPROGRESS;
                                break;
                        }//end switch
                        
                        if(strlen($row["vTitle"])>28)
                        {
                            $title = substr(htmlentities($row["vTitle"]),0,28)."...";
                        }
                        else
                        {
                            $title = $row["vTitle"];
                        }

                        ?>

                        <?php
                        $return_result[$cnt]['sl_no']=($cnt+1);
                        $return_result[$cnt]['title']=htmlentities($title);
                        $return_result[$cnt]['offered_to']=htmlentities($row["UserName"]);
                        $return_result[$cnt]['date']=date('m/d/Y', strtotime($row["dPostDate"]));
                        $return_result[$cnt]['status']=$shwOfferStatus;
                        $return_result[$cnt]['offer_type']=($row["vPostType"] == "swap") ? 'Swap Offer' : 'Wish Offer';
                        $return_result[$cnt]['st_id']=$row["nSTId"];
                        $return_result[$cnt]['other_userid']=$row["nUserReturnId"];
                        $return_result[$cnt]['my_status']=$newStatus;
                        $cnt++;
                    }//end while
                   
                    $message='';
                    $responseArray  = array('status' => 1,'error'=> $message,'data'=> $return_result);
        return $responseArray;
                    
                }//end if
                else {
                    
                 $message=ERROR_SORRY_NO_OFFERS_MADE;
                    $responseArray  = array('status' => 0,'error'=> $message,'data'=> $return_result);
        return $responseArray;
                      }

         
     }
     
     
     function offer_inboxAPI($user_id)
     {
   
    global $conn;
    include_once("apifunctions.php");
    include_once("language.php");
        $sql = "Select ST.nSwapId,ST.nUserId, ST.nUserReturnId, ST.vPostType,ST.vStatus,ST.vBlink,ST.nSTId,ST.nParentId,
                U.vLoginName as 'UserName',
                S.vTitle,date_format(ST.dDate,'%m/%d/%Y') as 'dPostDate'  from
                " . TABLEPREFIX . "swaptxn ST
                Left outer join " . TABLEPREFIX . "swap S on ST.nSwapId = S.nSwapId
                Left Outer Join " . TABLEPREFIX . "users U on ST.nUserId = U.nUserId
                where ST.nUserReturnId = '" . $user_id . "' and ST.vStatus <> 'N'
                Order By ST.dDate DESC";


            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                 $return_result=array();           
//
                if (mysqli_num_rows($result) > 0) {

                $cnt=0;
                    while ($row = mysqli_fetch_array($result)) {
                        $newStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'vStatus', "WHERE nSwapId= '" . addslashes($row['nSwapId']) . "'
                                                            AND nUserId = '" . addslashes($row['nUserId']) . "' AND nSTId='" . $row['nSTId'] . "'"), 'vStatus');

                        
                        switch ($newStatus) {
                            case "A":
                                $shwOfferStatus = 'Accepted';//TEXT_ACCEPTED;
                                break;

                            case "R":
                                $shwOfferStatus = 'Rejected';//TEXT_REJECTED;
                                break;

                            case "N":
                                $shwOfferStatus = 'Invalid';//TEXT_INVALID;
                                break;

                            default:
                                $shwOfferStatus = 'InProcess';//TEXT_INPROGRESS;
                                break;
                        }
                        
                        if(strlen($row["vTitle"])>28)
                        { 
                            $title = substr(htmlentities($row["vTitle"]),0,28)."...";
                        }
                        else
                        {
                            $title = $row["vTitle"];
                        }
                        
                        
                         $return_result[$cnt]['sl_no']=($cnt+1);
                        $return_result[$cnt]['title']=htmlentities($title);
                        $return_result[$cnt]['blink']=htmlentities($row['vBlink']);
                        $return_result[$cnt]['offered_to']=htmlentities($row["UserName"]);
                        $return_result[$cnt]['date']=date('m/d/Y', strtotime($row["dPostDate"]));
                        $return_result[$cnt]['status']=$shwOfferStatus;
                        $return_result[$cnt]['offer_type']=($row["vPostType"] == "swap") ? 'Swap Offer' : 'Wish Offer';
                        $return_result[$cnt]['st_id']=$row["nSTId"];
                        $return_result[$cnt]['other_userid']=$row["nUserId"];
                        $return_result[$cnt]['my_status']=$newStatus;
                        $cnt++;
                    }//end while
                    
                    $message='';
                    $responseArray  = array('status' => 1,'error'=> $message,'data'=> $return_result);
                    return $responseArray;
                }
                //end if
                else {
                    $message=ERROR_SORRY_NO_OFFERS_RECEIVED;
                    $responseArray  = array('status' => 0,'error'=> $message,'data'=> $return_result);
            return $responseArray;
                } 
         
         
     }
     
     
//     
     function sale_payment($saleid,$sale_date,$token,$lang_id=1,$userid,$seller_user)
     {
      global $conn,$sitestyle,$logourl;  
      //error_reporting(E_ALL);
      //ini_set('display_errors',1);
      include_once("apifunctions.php");
      include_once("language.php");
 include "stripe.php";    
             if(trim($saleid) == ""){
        $message        = "The sale id ".MISSING_PARAMETER;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    else if(!is_numeric($saleid)){
        $message        = "The sale id ".MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
        else if(trim($sale_date) == ""){
        $message        = "The sale date ".MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    else if(trim($token) == ""){
        $message        = "The token ".MISSING_PARAMETER;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
     
     
     try{
     $login_name=  get_login_user_name($userid);
     $user_email=getUserEmail($userid);
     //$api_key=get_stripe_secret_key($userid);
     $api_key=get_stripe_secret_key($seller_user);
     $now=$sale_date;
     $quantityREQD=false;
     $message='';
      $sql_qty = "Select nQuantity from " . TABLEPREFIX . "saledetails where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . addslashes($userid);
                $sql_qty .= "' AND dDate='" . urldecode($now) . "'";
                $result_qty = mysqli_query($conn, $sql_qty) or die(mysqli_error($conn));
     
     
//                 return $sql_qty;
                if (mysqli_num_rows($result_qty) > 0) {
                    $row_det = mysqli_fetch_array($result_qty);
                    
                    $quantityREQD = $row_det['nQuantity'];
                }
     $userid = fetchSingleValue(select_rows(TABLEPREFIX . 'sale', 'nUserId', ' WHERE nSaleId = '.$saleid), 'nUserId');           
     $sql_amount = "Select nValue,nShipping from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . addslashes($userid)."'";           
       $result_amount = mysqli_query($conn, $sql_amount) or die(mysqli_error($conn));
      $per_item_amount=0;
//     return $sql_amount;
     if (mysqli_num_rows($result_amount) > 0) { 
                    $row_amount = mysqli_fetch_array($result_amount);
                    
                    $per_item_amount = $row_amount['nresultValue']+$row_amount['nShipping'];
//                    return $per_item_amount;
        }
        else
        {
            $cc_flag=false;
            $message=HEADING_SALES_DETAILS.TXT_NOT_FOUND; 
        }
       if($per_item_amount>0)
       {
     $amount=$per_item_amount*$quantityREQD;

//     return $amount;
        $charge_details=create_charge($api_key,$token,$amount,$user_email);
            if ($charge_details->id) {

                $txnid=$charge_details->id;

                $cc_flag = true;

            }
             else {
                 $message=STRIPE_FAILED;
                 $cc_flag= false;
                // log for manual investigation
            }//end else if
       }
       else
       {
           $cc_flag=false;
            $message=TRANSACTION_AMOUNT_ZERO ;
       }
    if ($cc_flag == true) {
        $sql = "Select vSaleStatus from " . TABLEPREFIX . "saledetails where nSaleId='" . addslashes($saleid) . "' AND nUserId='" . addslashes($userid);
        $sql .= "' AND dDate='" . addslashes($now) . "' AND  vSaleStatus IN('2','3')";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) <= 0) {
            $sql = "Select vTxnId from " . TABLEPREFIX . "saledetails where vTxnId='" . addslashes($txnid) . "' AND vMethod='stripe'";
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) <= 0) {
                
            if (DisplayLookUp('Enable Escrow') == 'Yes') {
                $SaleStatus = '2';
            }//end if
            else {
                $SaleStatus = '3';
            }//end esle
                //update the database when this is okay
                $sql = "Update " . TABLEPREFIX . "saledetails set vSaleStatus='".$SaleStatus."',vTxnId='$txnid',dTxnDate=now() where ";
                $sql .= " nSaleId='" . addslashes($saleid) . "' AND nUserId='" . addslashes($userid) . "' AND dDate='";
                $sql .= addslashes($now) . "' ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));

//                    $quantityREQD = $row_det['nQuantity'];
                    if($quantityREQD)
                    {
                    //reduce requested quantity from the master table
                        $sql = "UPDATE " . TABLEPREFIX . "sale SET nQuantity=nQuantity - $quantityREQD where nSaleId ='" . addslashes($saleid) . "'";
                        mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    }



                $sql = "Select nUserId from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($saleid) . "'";
                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

            }//end if


$sql = "insert into " . TABLEPREFIX . "tempdata(nId,vValue,vData)  values('','" . addslashes($saleid) . "|" . addslashes($userid) . "|" .
        addslashes($now) . "','" . addslashes($txnid) . "');";
mysqli_query($conn, $sql) or die(mysqli_error($conn));
             
     ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
//                    MAIL SECTION
                    
     /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 

  $sql = "Select s.vTitle,sd.nAmount,sd.dDate,sd.nQuantity,sd.vSaleStatus,sd.vRejected from " . TABLEPREFIX . "saledetails  sd inner join " . TABLEPREFIX . "sale s ";
            $sql .= " on sd.nSaleId = s.nSaleId ";
            $sql .= " where  sd.nSaleId='" . addslashes($saleid) . "' AND sd.nUserId='" . $userid. "' AND sd.dDate='";
            $sql .= addslashes($now) . "' AND sd.vSaleStatus='" . $SaleStatus . "' AND sd.vRejected='0' ";
     
            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            if (mysqli_num_rows($result) > 0) {
                if ($row = mysqli_fetch_array($result)) {
                    $var_title = $row["vTitle"];
                    $var_quantity = $row["nQuantity"];
                    $var_amount = $row["nAmount"];
                    $var_date = $now;
                    $flag = true;


                    //send mail to seller
                    $subject = "One of your products listed at " . SITE_NAME . " has been sold.";
                    //fetching seller information
                    $condition = "where nSaleId='" . $saleid . "'";
                    $sellerUserId = fetchSingleValue(select_rows(TABLEPREFIX . 'sale', 'nUserId', $condition), 'nUserId');

                    $condition = "where nUserId='" . $sellerUserId . "'";
                    $SellerName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
                    $EMail = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vEmail', $condition), 'vEmail');

                    if (DisplayLookUp('4') != '') {
                    $var_admin_email = DisplayLookUp('4');
                    }//end if
              
                /*
                * Fetch user language details
                */

                $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$lang_id."'";
                $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
                $langRw = mysqli_fetch_array($langRs);

                /*
                * Fetch email contents from content table
                */
               $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'soldout'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$lang_id."'";
                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                $mailRw  = mysqli_fetch_array($mailRs);

                $mainTextShow   = $mailRw['content'];

                if(!$txnid || $txnid==''){
                   $mainTextShow = str_replace("{txnid}", "", $mainTextShow);
                   $mainTextShow = str_replace("Transaction Id", "", $mainTextShow);
                }

                $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{var_quantity}","{txnid}","{guserFName}","{Account Summary}");
                $arrTReplace    = array(SITE_NAME,SITE_URL,$var_title,CURRENCY_CODE.$var_amount,$var_quantity,$txnid,$login_name,"'Account Summary'");
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent1   = $mainTextShow;

                $subject        = $mailRw['content_title'];
                $subject        = str_replace("{SITE_NAME}",SITE_NAME,$subject);

                $StyleContent   = MailStyle($sitestyle,SITE_URL);


               //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, addslashes($SellerName), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
                $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

               send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');

                //send mail to seller end
                //send mail to buyer
                $mailRw = array();
                /*
                * Fetch email contents from content table
                */
               $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'soldoutMailToBuyer'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$lang_id."'";
                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                $mailRw  = mysqli_fetch_array($mailRs);

                $mainTextShow   = $mailRw['content'];
                $mainTextShow   = $mailRw['content'];
                if(!$txnid || $txnid==''){
                   $mainTextShow = str_replace("{txnid}", "", $mainTextShow);
                   $mainTextShow = str_replace("Transaction Id", "", $mainTextShow);
                }

                $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{var_quantity}","{txnid}","{guserFName}","{Account Summary}");
                $arrTReplace    = array(SITE_NAME,SITE_URL,$var_title,CURRENCY_CODE.$var_amount,$var_quantity,$txnid,$login_name,"'Account Summary'");
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent11   = $mainTextShow;

                $subject2        = $mailRw['content_title'];
                $subject2        = str_replace("{SITE_NAME}",SITE_NAME,$subject2);

                $StyleContent   = MailStyle($sitestyle,SITE_URL);

                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, addslashes($login_name), $mailcontent11, $logourl, date('m/d/Y'), SITE_NAME, $subject2);
                $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
               
                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);
                
                send_mail($user_email, $subject2, $msgBody, SITE_EMAIL, 'Admin');
                //send mail to buyer end
                //
                //send mail to admin
                $mailRw = array();
                /*
                * Fetch email contents from content table
                */
               $mailSql = "SELECT L.content,L.content_title
                          FROM ".TABLEPREFIX."content C
                          JOIN ".TABLEPREFIX."content_lang L
                            ON C.content_id = L.content_id
                           AND C.content_name = 'soldoutMailToAdmin'
                           AND C.content_type = 'email'
                           AND L.lang_id = '".$lang_id."'";
                $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
                $mailRw  = mysqli_fetch_array($mailRs);


                $mainTextShow   = $mailRw['content'];

                $arrTSearch = array("{SITE_NAME}","{SITE_URL}","{var_title}","{var_amount}","{sellerName}","{buyerName}");
                $arrTReplace    = array(SITE_NAME,SITE_URL,$var_title,CURRENCY_CODE.$var_amount,$SellerName,$login_name);
                $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

                $mailcontent12   = $mainTextShow;

                $subject3        = $mailRw['content_title'];
                $subject3        = str_replace("{SITE_NAME}",SITE_NAME,$subject2);

                $StyleContent   = MailStyle($sitestyle,SITE_URL);

                //readf file n replace
                $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
                $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, 'Administrator', $mailcontent12, $logourl, date('m/d/Y'), SITE_NAME, $subject3);
                $msgBody = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');

                $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

                
                send_mail($var_admin_email, $subject3, $msgBody, SITE_EMAIL, 'Admin');
                
             

                    $message = stripslashes(MESSAGE_THANKYOU_FOR_PAYMENT_RECEIPT_EMAILED);
//                    $_SESSION['sess_buyerid_escrow'] = '';
                }//end if
            }//end if

         }

    }
    
     }catch(Stripe_CardError $e) {
                    $message = $e->getMessage();
                    //header('Location: complete-cardapplication.php?step=2&msg=regfailed&paln='.$plantype);
                    //exit();
                }catch (Stripe_InvalidRequestError $e) {
                  $message = $e->getMessage();
                } catch (Stripe_AuthenticationError $e) {
                  $message = $e->getMessage();
                } catch (Stripe_ApiConnectionError $e) {
                 $message = $e->getMessage();
                } catch (Stripe_Error $e) {
                    $message = $e->getMessage();
                } catch (Exception $e) {
                    $message = $e->getMessage();
                }
    $status=($message!='')?0:1;
    $return_result=array();
    $responseArray  = array('status' => $status,'error'=> $message,'data'=> $return_result);
    return $responseArray;
   }
   
   function make_offer_payment($swapid,$amount,$token,$user_id)
   {
        global $conn,$sitestyle,$logourl;  
        error_reporting(E_ALL);
        ini_set('display_errors',1);
        include_once("apifunctions.php");
        include_once("language.php");
        include "stripe.php";
       
        if(trim($swapid) == ""){
        $message        = "The swap id".MISSING_PARAMETER;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
        else if(trim($user_id) == ""){
        $message        = "The user id ".MISSING_PARAMETER;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    else if(!is_numeric($user_id)){
        $message        = "The user id ".MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
        else if(trim($amount) == ""){
        $message        = "The amount ".MISSING_PARAMETER;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    else if(!is_numeric($amount)){
        $message        = "The amount ".MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    else if(trim($token) == ""){
        $message        = "The token".MISSING_PARAMETER;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
        
        //$login_name =   get_login_user_name($userid);
        $user_email =   getUserEmail($user_id);
        $api_key    =   get_stripe_secret_key($user_id);
        $message = '';
        
        $charge_details=create_charge($api_key,$token,$amount,$user_email);
        if($charge_details->id) 
        {
            $txnid=$charge_details->id;
            $method = 'stripe';                
            $cc_flag = true;

            $sql = "Update " . TABLEPREFIX . "swap set 
                    nSwapAmount='$amount',
                    vEscrow='1',
                    vMethod='$method',
                    vTxnId='$txnid',
                    vSwapStatus='2',dTxnDate=now() where
                    nSwapId in (" . $swapid . ") ";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $message= PAYMENT_SUCCESS; 
            $status = 1;
        }
        else 
        {
             $message=STRIPE_FAILED;
             $cc_flag= false;
             $status = 0;
            // log for manual investigation
        }
        
        //$status=($message!='')?0:1;
        $return_result=array();
        $responseArray  = array('status' => $status,'error'=> $message.$sql,'data'=> $return_result);
        return $responseArray;
   }

   function message_inboxAPI($user_id){
    global $conn;
    include_once("apifunctions.php");
    include_once("language.php");

    $sql = "SELECT m.nMsgId,m.nToUserId,m.nFromUserId,m.vTitle,m.vMsg,m.nDate,u.vLoginName  FROM " . TABLEPREFIX . "messages m JOIN ". TABLEPREFIX ."users u ON m.nFromUserId = u.nUserId  WHERE nToUserId='" . $user_id . "' and vToDel='N' order by nDate Desc";
    //$sql = "SELECT *  FROM " . TABLEPREFIX . "messages WHERE  vToDel='N' order by nDate Desc";
    $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $return_result = array();
    if (mysqli_num_rows($rs) > 0) {
    $count = 0;
    while($row=mysqli_fetch_array($rs)){
        //print_r($row);
        
        foreach($row as $key=>$val){
           // echo $key."=>".$val."<br>";
           $rkey = "";
           $rvalue = "";
            switch ($key){
                case 'nMsgId':
                    $rkey="msg_id";
                    $rvalue = $val;
                break;
                case 'nToUserId' :
                    $rkey="to_user_id";
                    $rvalue = $val;
                break;
                case 'nFromUserId' :
                    $rkey = 'from_user_id';
                    $rvalue = $val;
                break;
                case 'vTitle':
                    $rkey = 'title';
                    $rvalue = $val;
                break;
                case 'vMsg':
                    $rkey = 'message';
                    $rvalue = $val;
                break;
                case 'nDate':
                    $rkey = 'date';
                    $rvalue = $val;
                break;
                case 'vLoginName':
                    $rkey = 'from_user';
                    $rvalue = $val;
                break;
                  
            }
            if($rkey && $rvalue){
            $return_result[$count][$rkey] = $rvalue;
            }
            
        }
        //array_push($return_result,$row);
        
                $count++;
    }
    $message='';
    $responseArray  = array('status' => 1,'error'=> $message,'data'=> $return_result);
    return $responseArray;
   
    }else{
        $message=NO_MESSAGES_INBOX;
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $return_result);
    return $responseArray;
   }
}

function message_outboxAPI($user_id){

    global $conn;
    include_once("apifunctions.php");
    include_once("language.php");

    $sql = "SELECT m.nMsgId,m.nToUserId,m.nFromUserId,m.vTitle,m.vMsg,m.nDate,u.vLoginName  FROM " . TABLEPREFIX . "messages m JOIN ". TABLEPREFIX ."users u ON m.nToUserId = u.nUserId  WHERE nFromUserId='" . $user_id . "' and vFromDel='N' order by nDate Desc";
    //$sql = "SELECT *  FROM " . TABLEPREFIX . "messages WHERE  vToDel='N' order by nDate Desc";
    $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $return_result = array();
    if (mysqli_num_rows($rs) > 0) {
    $count = 0;    
    while($row=mysqli_fetch_array($rs)){
        //print_r($row);
        
        foreach($row as $key=>$val){
           // echo $key."=>".$val."<br>";
           $rkey = "";
           $rvalue = "";
            switch ($key){
                case 'nMsgId':
                    $rkey="msg_id";
                    $rvalue = $val;
                break;
                case 'nToUserId' :
                    $rkey="to_user_id";
                    $rvalue = $val;
                break;
                case 'nFromUserId' :
                    $rkey = 'from_user_id';
                    $rvalue = $val;
                break;
                case 'vTitle':
                    $rkey = 'title';
                    $rvalue = $val;
                break;
                case 'vMsg':
                    $rkey = 'message';
                    $rvalue = $val;
                break;
                case 'nDate':
                    $rkey = 'date';
                    $rvalue = $val;
                break;
                case 'vLoginName':
                    $rkey = 'to_user';
                    $rvalue = $val;
                break;
                  
            }
            if($rkey && $rvalue){
            $return_result[$count][$rkey] = $rvalue;
            }
            
        }
        //array_push($return_result,$row);
        
                $count++;
    }
    $message='';
    $responseArray  = array('status' => 1,'error'=> $message,'data'=> $return_result);
    return $responseArray;
   
    }else{
        $message=NO_MESSAGES_OUTBOX;
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $return_result);
        return $responseArray;
   }

}
function create_messageAPI($inputs,$user_id){
    global $conn;
    include_once("apifunctions.php");
    include_once("language.php");
    $requiredParameters = array("title",'message','touserid');
    $return_result = array();
    foreach($requiredParameters as $req){
        if(!$inputs[$req]){
            $message = $req.MISSING_PARAMETER;
            $responseArray  = array('status' => 0,'error'=> $message,'data'=> $return_result);
            return $responseArray;

        }
    }
    if(!is_numeric($inputs['touserid'])){
        $message        = "touserid ".MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    
    $title = $inputs["title"];
    $message = $inputs["message"];
    $touserid = $inputs["touserid"];
    $sqluserexists = "SELECT vLoginName FROM " . TABLEPREFIX . "users  WHERE nUserId = '" . $touserid . "' AND vDelStatus!='1'";
    $resultuserexists = mysqli_query($conn, $sqluserexists) or die(mysqli_error($conn));
    if (mysqli_num_rows($resultuserexists) > 0) {

    $final_result = mysqli_query($conn, "insert into " . TABLEPREFIX . "messages (nToUserId,nFromUserId,vTitle,vMsg,vStatus,nDate)
						values ('" . $touserid . "','" . $user_id . "','" . addslashes($title) . "','" . addslashes($message) . "',
                        'N',now())") or die(mysqli_error($conn));
    if($final_result){
        $message = MESSAGE_MESSAGE_SENT_SUCCESSFULLY;
        $return_result["msg_id"] = mysqli_insert_id($conn);
        $responseArray  = array('status' => 1,'error'=> $message,'data'=> $return_result);
        return $responseArray;

    } 
    
    } else {
        $message = INVALID_TO_USER;
        $return_result = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $return_result);
        return $responseArray;
    }

}

function Delete_messageAPI($inputs,$user){
    global $conn;
    include_once("apifunctions.php");
    include_once("language.php");
    //print_r($inputs);exit;
    if(trim($inputs["msg_id"]) == "" || !is_numeric($inputs["msg_id"])){
        $message        = "msg_id ".MISSING_PARAMETER." or ".MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    } if(trim($inputs["inbox"]) == "" || !is_numeric($inputs["msg_id"])){
        $message        = "inbox parameter".MISSING_PARAMETER." or ".MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    $nMsgId = $inputs["msg_id"];
    if ($inbox) {
    $query = mysqli_query($conn, "update " . TABLEPREFIX . "messages set vToDel='Y' WHERE nMsgId='" . $nMsgId . "' and nToUserId ='".$user."'") or die(mysqli_error($conn));
    }
    else{
    $query = mysqli_query($conn, "update " . TABLEPREFIX . "messages set vFromDel='Y' WHERE nMsgId='" . $nMsgId . "' AND nFromUserId='".$user."'") or die(mysqli_error($conn));
    }

    if($query){

        $var_message = 'success';
        $responseArray  = array('status' => 1,'error'=> MESSAGE_MESSAGE_DELETED_SUCCESSFULLY,'data'=> array());
    }//end if
    else{
        $var_message = 'failure';
        $responseArray  = array('status' => 0,'error'=> "Unable to delete Message",'data'=> array());
    }

    return $responseArray;


}
function Change_languageAPI($inputs,$user=''){
    global $conn;
    include_once("apifunctions.php");
    include_once("language.php");
    if(trim($inputs['language_id']) == "" || !is_numeric($inputs['language_id'])){
        $message        = "language_id".MISSING_PARAMETER." or ".MUST_BE_NUMERIC;
        $result         = array();
        $responseArray  = array('status' => 0,'error'=> $message,'data'=> $result);
        return $responseArray;
    }
    $sql_lang = "select lang_id,folder_name,country_abbrev from " . TABLEPREFIX . "lang where lang_id= '" . addslashes(trim($inputs['language_id'])) . "' and lang_status='y'";
    
    $res_lang = mysqli_query($conn, $sql_lang) or die(mysqli_error($conn));
    if ($obj_row = mysqli_fetch_object($res_lang)) {
        
        if($user){
        mysqli_query($conn, "Update ".TABLEPREFIX."users set preferred_language='".$inputs["language_id"]."' where nUserId='".$user."'") or
                                                    die(mysqli_error($conn));
        }
        $responseArray  = array('status' => 1,'error'=> "",'data'=> array());
        return $responseArray;
}

}

function Get_languagesAPI(){
    global $conn;
    $sql_flag = "select * from ".TABLEPREFIX."lang where lang_status = 'y' ";
    $res_flag = mysqli_query($conn, $sql_flag) or die(mysqli_error($conn));
    $result = array();
    $message = '';
    $status = 1;
    while($query_data = mysqli_fetch_array($res_flag)){
          $languageDir    =   "languages/".$query_data['folder_name'];
        array_push($result,$query_data);
       //if (is_dir($languageDir)) {

    }
    if(!$result){
        $message= EMPTY_LANGUAGE_SET;
        $status = 0;
    }
    $responseArray  = array('status' => $status,'error'=> $message,'data'=> $result);
    return $responseArray;

}
