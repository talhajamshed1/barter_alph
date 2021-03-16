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
include("./includes/config.php");
include("./includes/session.php");
include("./includes/functions.php");
include("./includes/logincheck.php");

if (!isset($_SESSION["guseraffid"]) || $_SESSION["guseraffid"] == "") {
    /*if (function_exists('session_register')) {
        session_register("guseraffid");
    }//end if
    */
    $_SESSION["guseraffid"] = $_GET["guseraffid"];
}//end if

include_once('./includes/gpc_map.php');
$message = "";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title><?php echo $siteTitle; ?></title>
        <meta name="keywords" content="<?php echo $keyword; ?>">
        <meta name="description" content="<?php echo $description; ?>">
        <link href="<?php echo $stylesfolder; ?>/<?php echo $sitestyle; ?>" rel="stylesheet" type="text/css">
        <link href="favicon.ico" type="image/x-icon" rel="icon">
        <link href="favicon.ico" type="image/x-icon" rel="shortcut icon">
    </head>
    <style type="text/css">
        .stepcarousel
        {
            position: relative; /*leave this value alone*/
            border: 5px solid #FF0000;
            background-color:#BEBEBE;
            overflow: scroll; /*leave this value alone*/
            width: 700px; /*Width of Carousel Viewer itself 270*/
            height: 150px; /*Height should enough to fit largest content's height*/
        }
        .stepcarousel .belt
        {
            position: absolute; /*leave this value alone*/
            left: 0;
            top: 0;
        }
        .stepcarousel .panel
        {
            float: left; /*leave this value alone*/
            overflow: hidden; /*clip content that go outside dimensions of holding panel DIV*/
            margin: 25px 10px 10px 10px; /*margin around each panel*/
            width: 800px; /*Width of each panel holding each content. If removed, widths should be individually defined on each content DIV then. 250*/
        }
    </style>
    <body onLoad="timersOne();">
        <table width="100%" height="38"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td height="38" valign="middle" class="topcolor">      
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="44%" align="left" class="toplinks">&nbsp;</td>
                            <td width="56%">&nbsp;</td>
                        </tr>
                    </table></td>
            </tr>
        </table>
<?php
if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else
//checking size
@list($width_new, $height_new) = @getimagesize($imagefolder . '/images/' . $logourl);
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
?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="headerbg">
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="18%" class="logo"><a href="./index.php"><img src="<?php echo $imagefolder; ?>/images/<?php echo  $logourl ?>" border="0" width="<?php echo $width_new; ?>" height="<?php echo $height_new; ?>" alt="<?php echo SITE_NAME; ?>" title="<?php echo SITE_NAME; ?>"></a></td>
                            <td width="28%">&nbsp;</td>
                            <td width="54%" align="right" class="headerright"><table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                    <tr>
                                        <td width="58%" align="right" class="captiontext"><?php echo HEADER_CAPTION; ?></td>
                                        <td width="42%">&nbsp;</td>
                                    </tr>
                                </table></td>
                        </tr>
                    </table>
                    <table width="100%" height="25"  border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="70%" valign="middle" class="linkbar"><table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                    <tr align="center" class="link"><td width="5%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </td></tr>
                                </table></td>
                            <td width="33%" valign="middle" class="linkbar">&nbsp;</td>
                        </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="74%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="heading" align="left">&nbsp;</td>
                                                </tr>
                                            </table>
                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="maintext" align="center">
                                                        <p align=center>
                                                            <b><?php echo ERROR_INVALID_LICENCE_KEY; ?></b>.<br><br> Please contact <b><?php echo str_replace('{email}',DisplayLookUp('4'),TEXT_CONTACT_EMAIL); ?></b>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr><td>&nbsp;</td></tr>
                                                <tr><td>&nbsp;</td></tr>
                                                <tr><td>&nbsp;</td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table></td>
                        </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td height="19" class="footer"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>

                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="53%">&nbsp;</td>
                                        <td width="47%"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="8%" align="right" valign="top"><img src="images/blkcrnr.gif" width="40" height="23"></td>
                                                    <td width="92%" bgcolor="#000000" class="poweredby"><?php echo TEXT_POWEREDBY; ?></td>
                                                </tr>
                                            </table></td>
                                    </tr>
                                </table></td>
                        </tr>
                    </table></td>
            </tr>
        </table>
    </body>
</html>