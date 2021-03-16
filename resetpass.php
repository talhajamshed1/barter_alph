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
include_once('./includes/gpc_map.php');

$errsflag = 0;

if ($_SESSION['guserid']!=''){
    header("Location:usermain.php");
    exit();
}

$id = addslashes($_GET["id"]);
$pw = addslashes($_GET["p"]);

$sql = "Select nUserId,vEmail,vLoginName from " . TABLEPREFIX . "users WHERE vEmail = '$id' AND vPassword = '$pw'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
    $email = $row["vEmail"];
    $uid = $row["nUserId"];
    $vloginname = $row["vLoginName"];

    $newpass = generatePassword();


    /*
    * Fetch user language details
    */

    $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
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
           AND L.lang_id = '".$_SESSION["lang_id"]."'";
    }else{
        $mailSql = "SELECT L.content,L.content_title
          FROM ".TABLEPREFIX."content C
          JOIN ".TABLEPREFIX."content_lang L
            ON C.content_id = L.content_id
           AND C.content_name = 'passwordreset'
           AND C.content_type = 'email'
           AND L.lang_id = '".$_SESSION["lang_id"]."'";
    }
    $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
    $mailRw  = mysqli_fetch_array($mailRs);

    $mainTextShow   = $mailRw['content'];
    $mainTextShow   = str_replace('{newpass}', $newpass, $mainTextShow);

    $arrTSearch     = array("{SITE_NAME}","{SITE_URL}","{SITE_EMAIL}","{LoginName}","{Password}","{activate_link}",);
    $arrTReplace    = array(SITE_NAME,SITE_URL,SITE_EMAIL,htmlentities($row["vLoginName"]),$row["vPassword"],$activate_link );
    $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

    $mailcontent1   = $mainTextShow;

    $subject    = $mailRw['content_title'];
    $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

    $StyleContent=MailStyle($sitestyle,SITE_URL);

    //readf file n replace
    $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
    $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, $vloginname, $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
    $msgBody = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
    $msgBody = str_replace($arrSearch, $arrReplace, $msgBody);

    send_mail($email, $subject, $msgBody, SITE_EMAIL, 'Admin');


    $sql = "Update  " . TABLEPREFIX . "users set vPassword='" . md5($newpass) . "' WHERE nUserId = '$uid'";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $message = TEXT_PASSWORD_RESET_MAIL_SENT;
    $errsflag = 0;
}//end if
else {
    $message = ERROR_INVALID_LINK;
    $errsflag = 1;
}//end else
include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function Validate(){
        if(document.frmPassword.txtUserName.value==""){
            alert(<?php echo ERROR_USERNAME_EMPTY; ?>);
        }else{
            alert();
            document.frmPassword.submit()
        }

    }
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="headerbg"><?php require_once("./includes/header.php"); ?>
                <?php //require_once("menu.php"); ?>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3">
                            <?php include_once ("./includes/categorymain.php"); ?>
                        </div>
                        <div class="col-lg-9">
                            <div class="product-list-head"><h3><?php echo TEXT_FORGOT_PASSWORD; ?></h3></div>
                            <br>
                             <?php if ($_SESSION["guserid"] == "") {
                                include_once("./login_box.php");
                            } ?>

                            <form name="frmPassword" method ="POST" action = "<?php echo $_SERVER['PHP_SELF'] . "?act=post" ?>">
                                <?php
                                if (isset($message) && $message != '') {
                                    ?>
                                    <div bgcolor="#FFFFFF" style="min-height: 400px;">
                                        <div colspan="2" align="center" class="<?php if($errsflag == 0){ ?>success<?php }else{?>warning<?php } ?>"><?php echo $message; ?></div>
                                    </div>
                                <?php }//end if?>  
                                                           
                            </form>
                            <?php include('./includes/sub_banners.php'); ?>



                        </div>
                    </div>
                </div>
                <?php require_once("./includes/footer.php"); ?>