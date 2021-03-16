<?php
   /* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
   // +----------------------------------------------------------------------+
   // | PHP version 4/5                                                      |
   // +----------------------------------------------------------------------+
   // | This source file is a part of iScripts eSwap                         |
   // +----------------------------------------------------------------------+
   // | Authors: Programmer<simi@armia.com>                       |
   // +----------------------------------------------------------------------+
   // | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
   // | All rights reserved                                                  |
   // +----------------------------------------------------------------------+
   // | This script may not be distributed, sold, given away for free to     |
   // | third party, or used as a part of any internet services such as      |
   // | webdesign etc.                                                       |
   // +----------------------------------------------------------------------+
   include_once("./includes/config.php");
   include_once("./includes/session.php");
   include_once("./includes/functions.php");
   include_once("./includes/logincheck.php");
   $loginmessage = "";
   
   
   $_SESSION['sessionAfterLoginRedirect'] = base64_encode($PG_TITLE);
   
   if ($_POST["btnLogin"] != "") {     
       $txtUserName = $_POST["txtUserName"];
       $txtPassword = addslashes(trim($_POST["txtPassword"]));
       $txtUserName = addslashes($txtUserName);
       $chkInvisible = $_POST['chkInvisible'];
       if($_SESSION["rurl"]){
           $_SESSION['sessionAfterLoginRedirect'] = $_SESSION["rurl"];
       }
   
       switch ($chkInvisible) {
           case "Y":
               $visible = 'N';
               break;
   
           case "":
               $visible = 'Y';
               break;
       }//end switch
       $loginmessage = isValidLogin('user', $txtUserName, $txtPassword, $visible);
       header("location:login.php?msg=$loginmessage");
   }//end if
   ?>
<script LANGUAGE="javascript">
   function validateLoginDropForm(){
       var frm = window.document.frmLoginDrop;
       if(trim(frm.txtUserName.value) ==""){
           alert("<?php echo ERROR_USERNAME_EMPTY; ?>");
           frm.txtUserName.focus();
           return false;
       }else if(frm.txtPassword.value ==""){
           alert("<?php echo ERROR_PASSWORD_EMPTY; ?>");
           frm.txtPassword.focus();
           return false;
       }
       return true;
   }
</script>
<?php
   if (isset($loginmessage) && $loginmessage != '' &&(!preg_match("/login.php/i", $_SERVER['PHP_SELF']))) {
   ?>
<script type="text/javascript">
   var $jql=jQuery.noConflict();
   $jql(document).ready(function () {
       $jqr('.signin').addClass('menu-open');
       $jql('#signin_menu').show();
   });
</script>
<?php
   }
   ?>
<script language="javascript" type="text/javascript" src="js/qTip.js"></script>
<!-- <div class="login_area_container_drop">
   <div class="login_area_cnt">
       <form name="frmLoginDrop" method="POST" action = "" onSubmit="return validateLoginDropForm();">
   <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      
       <tr>
           <td>
    
        <table width="100%"  border="0" cellspacing="5" cellpadding="0" class="login_tbl">
                   
                       <input type="hidden" name="btnLogin" value="Login">
                       <?php
      if (isset($loginmessage) && $loginmessage != '') {
          ?> 
                           <tr>
                               <td colspan="2" align="center" class="maintext_small warning"><?php echo $loginmessage; ?></td>
                           </tr>
                       <?php }//end if ?>
                       <tr>
                           <td colspan="2">
                               <input name="txtUserName" type="text" class="login_input" id="txtUserName" maxLength="50" size="15" title="<?php echo TEXT_USERNAME; ?>">
                           </td>
                          
                          
                       </tr>
                       <tr>
                          
                           <td colspan="2">
                               <input name="txtPassword" id="txtPassword" type="password" maxLength="50" size="15" class="login_input" title="<?php echo TEXT_PASSWORD; ?>"></td>
                         
                       </tr>
                    <tr>
                     <td align="left" class="invisiblemode" >
                           <input type="checkbox" name="chkInvisible" value="Y" tabindex="3" class="login_check"> <?php echo TEXT_INVISIBLE_MODE; ?></td>
                    <td align="right"><input type="submit" name="btnLogin" value="<?php echo BUTTON_LOGIN; ?>" class="login_btn_dropdown"></td>
                    </tr>
                       
                   
               </table>
            
            </td>
       </tr>
      
       <tr>
    <td colspan="2">
    <table width="100%" cellpadding="0" cellspacing="5" border="0">
    <tr>
        
           <td><span style="font-size:15px;"><a href="register.php" id="newSignUp"><?php echo LINK_NEW_USER_SIGNUP_HERE; ?></a></span><br>
               <a href="forgotpass.php" id="forgotPass"><?php echo LINK_FORGOT_PASSWORD; ?>?</a></td>
    </tr>
    </table>
    </tr>
    
   </table></form>
   </div>
   <div class="login_area_btm"></div>
   </div> -->
<!-- login popup -->
<div id="login-modal" class="modal loginModal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Login</h4>
         </div>
         <div class="modal-body">
            <form name="frmLoginDrop" method="POST" action = "" onSubmit="return validateLoginDropForm();">
               <input type="hidden" name="btnLogin" value="Login">
               
               <?php
                  if (isset($loginmessage) && $loginmessage != '') {
                      ?> 
                      <script type="text/javascript">

                        window.addEventListener('load', (event) => {
                        
                          $('#login-modal').modal('show');
                        });
                      </script>

               <div colspan="2" align="center" class="maintext_small warning"><?php echo $loginmessage; ?></div>
               <?php }//end if ?>
               <div class="frm-ctrl">
                  <input name="txtUserName" type="text" class="login_input" id="txtUserName" maxLength="50" size="15" title="<?php echo TEXT_USERNAME; ?>" placeholder="Username">
               </div>
               <div class="frm-ctrl">
                  <input name="txtPassword" id="txtPassword" type="password" maxLength="50" size="15" class="login_input" title="<?php echo TEXT_PASSWORD; ?>" placeholder="Password">
               </div>
               <div class="btn-grp">
                <label>
                  <input type="checkbox" name="chkInvisible" value="Y" tabindex="3" class="login_check"> <?php echo TEXT_INVISIBLE_MODE; ?>
                </label>
                 <a class="forgotpass"  href="forgotpass.php"><?php echo LINK_FORGOT_PASSWORD; ?>?</a>
               </div>
               <div >
                 
                  <input type="submit" name="btnLogin" value="<?php echo BUTTON_LOGIN; ?>" class="login_btn_dropdown">
               </div>
               <span class="reglink">Dont have account ? <a href="register.php"><?php echo LINK_NEW_USER_SIGNUP_HERE; ?></a></span>
            </form>
         </div>
      </div>
   </div>
</div>