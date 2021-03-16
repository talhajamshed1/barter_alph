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
?>
<?php
ob_start();
include_once('../includes/config.php');
include_once('../includes/adminsession.php');
include_once('../includes/functions.php');
if (ClientFilePathName($_SERVER['PHP_SELF']) == 'meta_tags.php') {
    $ShowContent = ($_POST['ddlContent'] != '') ? $_POST['ddlContent'] : 'index.php';
    $load = "ShowTitle('" . $ShowContent . "');";
} else {
    $load = '';
} if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
} else {
    $imagefolder = $secureserver;
} 

/*if (ClientFilePathName($_SERVER['PHP_SELF']) == 'email_contents.php') {
    if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
        $ddlType = stripslashes($_POST['ddlType']);
    } else {
        $ddlType = $_POST['content_type'];
    } $ddlType = ($ddlType != '') ? $ddlType : 'addsales';
    $showPageVar = "showHint('" . $ddlType . "');";
}*/
function FC718EAC1D5F164063CBA5FB022329FC7($RD7A9632D7A0B3B4AC99AAFB2107A2613) {
    preg_match("/^(http:\/\/)?([^\/]+)/i", $RD7A9632D7A0B3B4AC99AAFB2107A2613, $R2BC3A0F3554F7C295CD3CC4A57492121);
    $RADA370F97D905F76B3C9D4E1FFBB7FFF = $R2BC3A0F3554F7C295CD3CC4A57492121[2];
    $R74A7D124AAF5D989D8BDF81867C832AC = 0;
    $RA7B9A383688A89B5498FC84118153069 = strlen($RADA370F97D905F76B3C9D4E1FFBB7FFF);
    for ($RA09FE38AF36F6839F4A75051DC7CEA25 = 0; $RA09FE38AF36F6839F4A75051DC7CEA25 < $RA7B9A383688A89B5498FC84118153069; $RA09FE38AF36F6839F4A75051DC7CEA25++) {
        $RF5687F6BBE9EC10202A32FA6C037D42B = substr($RADA370F97D905F76B3C9D4E1FFBB7FFF, $RA09FE38AF36F6839F4A75051DC7CEA25, 1);
        if ($RF5687F6BBE9EC10202A32FA6C037D42B == ".")
            $R74A7D124AAF5D989D8BDF81867C832AC = $R74A7D124AAF5D989D8BDF81867C832AC + 1;
    } $R14AFFF8F3EA02262F39E2785944AAF6F = explode('.', $RADA370F97D905F76B3C9D4E1FFBB7FFF);
    $R7CC58E1ED1F92A448A027FD22153E078 = strtolower(substr($RADA370F97D905F76B3C9D4E1FFBB7FFF, -7));
    $RF413F06AEBBCEF5E1C8B1019DEE6FE6B = "";
    $R368D5A631F1B03C79555B616DDAC1F43 = array('.com.uk', 'kids.us', 'kids.uk', '.com.au', '.com.br', '.com.pl', '.com.ng', '.com.ar', '.com.ve', '.com.ng', '.com.mx', '.com.cn');
    $RF413F06AEBBCEF5E1C8B1019DEE6FE6B = in_array($R7CC58E1ED1F92A448A027FD22153E078, $R368D5A631F1B03C79555B616DDAC1F43);
    if (!$RF413F06AEBBCEF5E1C8B1019DEE6FE6B) {
        if (count($R14AFFF8F3EA02262F39E2785944AAF6F) == 1) {
            $RF877B1AAD1B2CBCDEC872ADF18E765B7 = $RADA370F97D905F76B3C9D4E1FFBB7FFF;
        } else if ((count($R14AFFF8F3EA02262F39E2785944AAF6F) > 1) && (strlen(substr($R14AFFF8F3EA02262F39E2785944AAF6F[count($R14AFFF8F3EA02262F39E2785944AAF6F) - 2], 0, 38)) > 2)) {
            preg_match("/[^\.\/]+\.[^\.\/]+$/", $RADA370F97D905F76B3C9D4E1FFBB7FFF, $R2BC3A0F3554F7C295CD3CC4A57492121);
            $RF877B1AAD1B2CBCDEC872ADF18E765B7 = $R2BC3A0F3554F7C295CD3CC4A57492121[0];
        } else {
            preg_match("/[^\.\/]+\.[^\.\/]+\.[^\.\/]+$/", $RADA370F97D905F76B3C9D4E1FFBB7FFF, $R2BC3A0F3554F7C295CD3CC4A57492121);
            $RF877B1AAD1B2CBCDEC872ADF18E765B7 = $R2BC3A0F3554F7C295CD3CC4A57492121[0];
        }
    }else
        $RF877B1AAD1B2CBCDEC872ADF18E765B7 = $R14AFFF8F3EA02262F39E2785944AAF6F[count($R14AFFF8F3EA02262F39E2785944AAF6F) - 3]; $R10870E60972CEA72E14A11D115E17EA5 = explode('.', $RF877B1AAD1B2CBCDEC872ADF18E765B7);
    $RD48CAD37DBDD2B2F8253B59555EFBE03 = strtoupper(trim($R10870E60972CEA72E14A11D115E17EA5[0]));
    return $RD48CAD37DBDD2B2F8253B59555EFBE03;
}

function FCE74825B5A01C99B06AF231DE0BD667D($RD7A9632D7A0B3B4AC99AAFB2107A2613) {
    if (F12DE84D0D1210BE74C53778CF385AA4D($RD7A9632D7A0B3B4AC99AAFB2107A2613))
        return true; $RD7A9632D7A0B3B4AC99AAFB2107A2613 = FC718EAC1D5F164063CBA5FB022329FC7($RD7A9632D7A0B3B4AC99AAFB2107A2613);
    $RB5719367F67DC84F064575F4E19A2606 = getLicense();
    $RFDFD105B00999E2642068D5711B49D5D = substr($RD7A9632D7A0B3B4AC99AAFB2107A2613, 0, 3);
    $RA6CC906CDD1BAB99B7EB044E98D68FAE = substr($RD7A9632D7A0B3B4AC99AAFB2107A2613, -3, 3);
    $R8439A88C56A38281A17AE2CE034DB5B7 = substr($RB5719367F67DC84F064575F4E19A2606, 0, 3);
    $R254A597F43FF6E1BE7E3C0395E9409D4 = substr($RB5719367F67DC84F064575F4E19A2606, 3, 3);
    $RDE2A352768EABA0E164B92F7ACA37DEE = substr($RB5719367F67DC84F064575F4E19A2606, -3, 3);
    $R254A597F43FF6E1BE7E3C0395E9409D4 = FCE67EB692054EBB3F415F8AF07562D82($R254A597F43FF6E1BE7E3C0395E9409D4, 3);
    $RDE2A352768EABA0E164B92F7ACA37DEE = FCE67EB692054EBB3F415F8AF07562D82($RDE2A352768EABA0E164B92F7ACA37DEE, 3);
    $R705EE0B4D45EEB1BC55516EB53DF7BCE = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9);
    $RA7B9A383688A89B5498FC84118153069 = strlen($RD7A9632D7A0B3B4AC99AAFB2107A2613);
    $RA5694D3559F011A29A639C0B10305B51 = 0;
    for ($RA09FE38AF36F6839F4A75051DC7CEA25 = 0; $RA09FE38AF36F6839F4A75051DC7CEA25 < $RA7B9A383688A89B5498FC84118153069; $RA09FE38AF36F6839F4A75051DC7CEA25++) {
        $RF5687F6BBE9EC10202A32FA6C037D42B = substr($RD7A9632D7A0B3B4AC99AAFB2107A2613, $RA09FE38AF36F6839F4A75051DC7CEA25, 1);
        $RA5694D3559F011A29A639C0B10305B51 = $RA5694D3559F011A29A639C0B10305B51 + $R705EE0B4D45EEB1BC55516EB53DF7BCE[$RF5687F6BBE9EC10202A32FA6C037D42B];
    } if ($RA5694D3559F011A29A639C0B10305B51 != ($R8439A88C56A38281A17AE2CE034DB5B7 - 11))
        return false; else if (strcmp($RFDFD105B00999E2642068D5711B49D5D, $R254A597F43FF6E1BE7E3C0395E9409D4) != 0)
        return false; else if (strcmp($RA6CC906CDD1BAB99B7EB044E98D68FAE, $RDE2A352768EABA0E164B92F7ACA37DEE) != 0)
        return false; else
        return true;
}

function FCE67EB692054EBB3F415F8AF07562D82($R8409EAA6EC0CE2EA307354B2E150F8C2, $R68EAF33C4E51B47C7219F805B449C109) {
    $RF413F06AEBBCEF5E1C8B1019DEE6FE6B = strrev($R8409EAA6EC0CE2EA307354B2E150F8C2);
    return $RF413F06AEBBCEF5E1C8B1019DEE6FE6B;
}

function F12DE84D0D1210BE74C53778CF385AA4D($R5E4A58653A4742A450A6F573BD6C4F18) {
    if (preg_match("/^[0-9].+$/", $R5E4A58653A4742A450A6F573BD6C4F18)) {
        return true;
    }else
        return false;
}

$R8FF184E9A1491F3EC1F61AEB9A33C033 = "invalidlicenseadmin.php";
$RD7A9632D7A0B3B4AC99AAFB2107A2613 = strtoupper(trim($_SERVER['HTTP_HOST']));
if ($RD7A9632D7A0B3B4AC99AAFB2107A2613 == '192.168.0.11' || $RD7A9632D7A0B3B4AC99AAFB2107A2613 == 'LOCALHOST' || $RD7A9632D7A0B3B4AC99AAFB2107A2613 == 'JEEVA.ORG' || $RD7A9632D7A0B3B4AC99AAFB2107A2613 == '127.0.0.1' || CLOUDINSTALLED == true) {
;
} else if (!FCE74825B5A01C99B06AF231DE0BD667D($RD7A9632D7A0B3B4AC99AAFB2107A2613)) {
    header("Location:$R8FF184E9A1491F3EC1F61AEB9A33C033");
    exit;
}

//checking size
@list($width_new, $height_new) = @getimagesize('../images/' . $logourl);
if ($width_new > 300) {
    $width_new = '300';
}//end if
else {
    $width_new = $width_new;
}//end else
//checking height
if ($height_new > 100) {
    $height_new = '100';
}//end if
else {
    $height_new = $height_new;
}//end else
include('sever_injection.php');
if (ClientFilePathName($_SERVER['PHP_SELF']) == 'edituser.php?userid=' . $_GET['userid']) {
    $ddlCountry = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vCountry', "WHERE nUserId='" . $_REQUEST['userid'] . "'"), 'vCountry');
    $loadFunction = "document.getElementById('ddlCountry').value='" . $ddlCountry . "';";
}//end if

if (ClientFilePathName($_SERVER['PHP_SELF']) == 'setconf.php') {
    if(!isset($_POST['ddlStyle'])){
	$loadFunction="showHint('".DisplayLookUp('sitestyle')."');";
    }
}//end if

if (ClientFilePathName($_SERVER['PHP_SELF']) == 'add_listing_combinations.php') {
    $loadFunction = "document.frmListing.txtTo.focus();";
}//end if
if($_SESSION['lang_id'] == ""){
    $_SESSION['lang_id'] = "1";
    $_SESSION['lang_folder'] = "en";

}
ContentText(); //to assign the content text to constants
/* if ($_SERVER['HTTP_HOST']=='localhost'){//need to delete this
  $stylesfolder = '../themes'; //temporary
  $sitestyle = 'Computers/style.css';//temporary
  } */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta name="generator" content="iscripts">
        <title><?php echo SITE_TITLE; ?></title>
        <link href="<?php echo $stylesfolder; ?>/<?php echo $sitestyle; ?>" rel="stylesheet" type="text/css">
        <link href="<?php echo SITE_URL;?>/styles/admin-style.css" rel="stylesheet" type="text/css">
        <link href="<?php echo SITE_URL;?>/styles/bootstrap.min.css" rel="stylesheet" type="text/css">
        <script LANGUAGE="javascript" src="../includes/functions.js"></script>
        <script LANGUAGE="javascript">
            function validateLoginForm(){
                var frm = window.document.frmLogin;
                if(trim(frm.txtUserName.value) ==""){
                    alert("Please enter a user name");
                    frm.txtUserName.focus();
                    return false;
                }else if(frm.txtPassword.value ==""){
                    alert("Please enter password");
                    frm.txtPassword.focus();
                    return false;
                }
                return true;
            }
        </script>
    </head>
    <body <?php if (isset($notregistered) and $notregistered == "1") {
    echo "onload='loadFields();'";
} ?> onLoad="<?php echo $load; ?><?php echo $showPageVar; ?><?php echo $loadFunction; ?>">
        <div id="Layout"  align="center">
        
        <div class="adminheader_top_panel ">
        	<div class="admin_container nobg">
            	 <table width="100%" height="24"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" height="24" valign="middle" class="topcolor">&nbsp;</td>
                    <td width="50%" align="right" valign="middle" class="topcolor"><?php if (isset($_SESSION["gadminid"]) && $_SESSION["gadminid"] != '') {
    echo '<span class="link_logout"><a href="adminmain.php">Home</a>&nbsp;&nbsp;|&nbsp;&nbsp; <a href="help_list.php">Admin Help Listings</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="logout.php">Logout</a>&nbsp;&nbsp;</span>';
} else {
    echo '&nbsp;';
} ?></td>
                </tr>
            </table>
            	
            </div>
        </div>
        <div class="row">
        	<div class="admin_container bgwhite">
            	<div class="logo_admin">
                    <a href="<?php echo SITE_URL?>/admin/adminmain.php">
                        <?php if($imagefolder){?>
                	<img src="<?php echo $imagefolder; ?>/images/<?php echo  $logourl ?>" width="<?php echo $width_new; ?>" height="<?php echo $height_new; ?>" border="0">
                        <?php }else{ ?>
                        <img src="../images/<?php echo  $logourl ?>" width="<?php echo $width_new; ?>" height="<?php echo $height_new; ?>" border="0">
                        <?php } ?>
                    </a>
                </div>
                <div class="adminwelcome">
                
                      <span class="glyphicon glyphicon-user"></span>
                 Welcome <span>Administrator </span>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
           
            