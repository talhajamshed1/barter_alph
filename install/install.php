<?php
/* -------------- /
 New code for install tracker, added by girish
 /------------------*/
@set_time_limit(0);
// Enable full error, warning and notice reporting
error_reporting(0);
/* -------------- /
 New code for install tracker, added by girish
 /------------------*/

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts iScripts eSwap                     |
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
include ("../includes/config.php");
include ("../includes/session.php");
$logoimage="logo.gif";
$imagefolder =  "../images";
$stylesfolder = "../styles";
$sitestyle="default2.css";
$logourl="logo.gif";
$productname="iScripts eSwap";
$ddlCurrency='$';
$ddlCurrency = 'USD';
$txtSiteNameArr = explode('.', $_SERVER['SERVER_NAME']);
$txtSiteName = $txtSiteNameArr[0];

define("SITE_TITLE", "iScripts eSwap");

session_start();

function isValidUsername($str)
{
if (trim($str) !="" )
{
if ( preg_match ( "/[^0-9a-zA-Z+_]", $str ) )
{
return false;
}//end if
else
{
return true;
}//end else
}//end if
else
{
return false;
}//end else
}//end function

function splitsqlfile($sql, $delimiter)
{
// Split up our string into "possible" SQL statements.
$tokens = explode($delimiter, $sql);
// try to save mem.
$sql = "";
$output = array();
// we don't actually care about the matches preg gives us.
$matches = array();
// this is faster than calling count($oktens) every time thru the loop.
$token_count = count($tokens);
for ($i = 0; $i < $token_count; $i++)
{
// Don't wanna add an empty string as the last thing in the array.
if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
{
// This is the total number of single quotes in the token.
$total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
// Counts single quotes that are preceded by an odd number of backslashes,
// which means they're escaped quotes.
$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

$unescaped_quotes = $total_quotes - $escaped_quotes;
// If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
if (($unescaped_quotes % 2)==0)
{
// It's a complete sql statement.
$output[] = $tokens[$i];
// save memory.
$tokens[$i] = "";
}//end if
else
{
// incomplete sql statement. keep adding tokens until we have a complete one.
// $temp will hold what we have so far.
$temp = $tokens[$i] . $delimiter;
// save memory..
$tokens[$i] = "";
// Do we have a complete statement yet?
$complete_stmt = false;

for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
{
// This is the total number of single quotes in the token.
$total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
// Counts single quotes that are preceded by an odd number of backslashes,
// which means they're escaped quotes.
$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

$unescaped_quotes = $total_quotes - $escaped_quotes;

if (($unescaped_quotes % 2)==1) {
// odd number of unescaped quotes. In combination with the previous incomplete
// statement(s), we now have a complete statement. (2 odds always make an even)
$output[] = $temp . $tokens[$j];
// save memory.
$tokens[$j] = "";
$temp = "";
// exit the loop.
$complete_stmt = true;
// make sure the outer loop continues at the right point.
$i = $j;
} else {
// even number of unescaped quotes. We still don't have a complete statement.
// (1 odd and 1 even always make an odd)
$temp .= $tokens[$j] . $delimiter;
// save memory.
$tokens[$j] = "";
}
} // for..
} // else
}
}
return $output;
}

//function for image name replace
function ReplaceArray($name)
{
$name=str_replace(' ','_',$name);
$name=str_replace("'",'_',$name);
return ($name);
}//end fucnction

function resizeImg($imgPath, $maxWidth, $maxHeight, $directOutput = true, $quality = 90, $verbose,$imageType)
{
// get image size infos (0 width and 1 height,
//     2 is (1 = GIF, 2 = JPG, 3 = PNG)

$size = getimagesize($imgPath);

// break and return false if failed to read image infos
if(!$size){
if($verbose && !$directOutput)echo "<br />Not able to read image infos.<br />";
return false;
}

// relation: width/height
$relation = $size[0]/$size[1];
// maximal size (if parameter == false, no resizing will be made)
$maxSize = array($maxWidth?$maxWidth:$size[0],$maxHeight?$maxHeight:$size[1]);
// declaring array for new size (initial value = original size)
$newSize = $size;
// width/height relation
$relation = array($size[1]/$size[0], $size[0]/$size[1]);


if(($newSize[0] > $maxWidth))
{
$newSize[0]=$maxSize[0];
$newSize[1]=$newSize[0]*$relation[0];
}



/*
 if(($newSize[1] > $maxHeight))
 {
 $newSize[1]=$maxSize[1];
 $newSize[0]=$newSize[1]*$relation[1];
 }
 */

// create image

switch($size[2])
{
case 1:
if(function_exists("imagecreatefromgif"))
{
$originalImage = imagecreatefromgif($imgPath);
}else{
if($verbose && !$directOutput)echo "<br />No GIF support in this php installation, sorry.<br />";
return false;
}
break;
case 2: $originalImage = imagecreatefromjpeg($imgPath); break;
case 3: $originalImage = imagecreatefrompng($imgPath); break;
default:
if($verbose && !$directOutput)echo "<br />No valid image type.<br />";
return false;
}


// create new image

$resizedImage = imagecreatetruecolor($newSize[0], $newSize[1]);

imagecopyresampled($resizedImage, $originalImage,0, 0, 0, 0,$newSize[0], $newSize[1], $size[0], $size[1]);

$rz=$imgPath;

// output or save
if($directOutput)
{
imagejpeg($resizedImage);
}
else
{

$rz=preg_replace("/\.([a-zA-Z]{3,4})$/","".$imageType.".jpg",$imgPath);
imagejpeg($resizedImage, $rz, $quality);
}
// return true if successfull
return $rz;
}

function isValidTableName($str)
{
if (trim($str) != "") {
if (preg_match ("/[^a-zA-Z+_]/", $str)) {
return false;
} else {
return true;
}
} else {
return false;
}
}
function isValidEmail($email)
{
$email = trim($email);
if ($email=="")
return false;
/**  if (!eregi("^" . "[a-z0-9]+([_\\.-][a-z0-9]+)*" . // user
 "@" . "([a-z0-9]+([\.-][a-z0-9]+)*)+" . // domain
 "\\.[a-z]{2,}" . // sld, tld
 "$", $email, $regs)
 ) {*/
if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',$email)) {
return false;
} else {
return true;
}
}
function isNotNull($value)
{
if (is_array($value)) {
if (sizeof($value) > 0) {
return true;
} else {
return false;
}
} else {
if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
return true;
} else {
return false;
}
}
}

function isValidWebImageType($mimetype,$filename,$tempname){
$blacklist = array("php", "phtml", "php3", "php4", "js", "shtml", "pl" ,"py", "exe");
foreach ($blacklist as $file)
{
if(preg_match("/\.$file\$/i", "$filename"))
{
return false;
}
}
//check if its image file
if (!getimagesize($tempname))
{
return false;
}

if(($mimetype=="image/pjpeg") || ($mimetype=="image/jpeg") || ($mimetype=="image/x-png")|| ($mimetype=="image/png")|| ($mimetype=="image/gif")||
($mimetype=="image/x-windows-bmp")|| ($mimetype=="image/bmp") ){
return true;
}else{
return false;
}
}

function isValidWMImageType($mimetype)
{
/* if (($mimetype=="image/pjpeg") || ($mimetype=="image/jpeg") || ($mimetype=="image/gif")) {
 return true;
 } else {
 return false;
 }*/
if ($mimetype=="image/gif") {
return true;
} else {
return false;
}
}

function getFilePermission($file)
{
$perm = fileperms($file);
if($perm===false)
{
return "0000";
}//end if
else
{
return substr(sprintf('%o', $perm), -4);
}//end else
}//end funciton

function stripslashes_deep($value)
{
$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
return $value;
}//end function
function getServerOS()
{
return strtoupper(substr(PHP_OS, 0, 3));
}//end function

if(function_exists('set_magic_quotes_runtime'))
{
//set_magic_quotes_runtime(0);
}//end if

if (function_exists('get_magic_quotes_gpc') )
{
$_POST = array_map('stripslashes_deep', $_POST);
$_GET = array_map('stripslashes_deep', $_GET);
$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}//end if

//currency code array
function CurrencyCodeCheck($chkCur)
{
$crrencyArray=array("AUD"=>"A $","CAD"=>"C $","EUR"=>"&euro;","GBP"=>"&pound;","JPY"=>"&yen;","USD"=>"$","NZD"=>"$","HKD"=>"HK$","SGD"=>"$",
							"CZK"=>"Kc","DKK"=>"kr","HUF"=>"Ft","ILS"=>"&#8362;","MXN"=>"$","NOK"=>"kr","PLN"=>"zl","SEK"=>"kr","CHF"=>"CHF");

foreach($crrencyArray as $ckey=>$cval)
{
if($ckey==$chkCur)
{
return ($cval);
}//end if
}//end foreach
}//end function

$schemafile = "schema.sql";
$datafile = "data.sql";
$configfile = "../includes/config.php";

$configcontents = @fread(@fopen($configfile, 'r'), @filesize($configfile));
$pos = strpos($configcontents, "INSTALLED");
if ($pos===false)
{
;
}//end if
else
{
header("location:./index.php");
exit();
}//end else

$fullurl = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
if($_SERVER['HTTPS']=='on'){
$http = "https://";
}else {
$http = "http://";
}
$pos = strrpos($fullurl, "/");
if ($pos === false) { // note: three equal signs
// not found...
} else {
$fullurl = substr($fullurl, 0, $pos);
}
//$fullurl = $_SERVER["SERVER_NAME"];
$txtSiteSecureURL   = "https://".$fullurl;
$fullurl            = $http . $fullurl;
$txtSiteURL         = $fullurl;

//check server configuration
$OS	= getServerOS();

$val1 = ini_get("safe_mode");
//$val2 = ini_get("short_open_tag");
$val2 = 1;
$val3 = ini_get("file_uploads");
$val4 = ini_get("open_basedir");

if($_POST['cldpack'] != 1) {
$openbasedircheck="0" ;
if((!empty($val4) || $val4==1) and  (!empty($val3) || $val3==1))
{
if($_POST["submittest"]=="Upload")
{
$uploadpath=substr($_FILES['testupload']['tmp_name'],0,strlen($_FILES['testupload']['tmp_name'])-strlen(basename($_FILES['testupload']['tmp_name']))-1);

$val4=$val4.":"."  ";
$openbasedirarray=explode(":",$val4);

if(! in_array($uploadpath,$openbasedirarray))
{
// echo "<br>&nbsp;&nbsp;".$ivf."Please add '$uploadpath' in your openbase directory ";
}//end if
else
{
$openbasedircheck="1";
}//end else
$_SESSION['sess_openbasedircheck']=$openbasedircheck;
}//end if
}//end if
else
{
$_SESSION['sess_openbasedircheck']="1";
}//end else
}
$mysqlsupport=true;
if (!function_exists('mysqli_connect'))
{
$mysqlsupport = false;
}//end if

if($_POST['cldpack'] != 1) {
if( (! empty($val1) || $val1==1) or  (empty($val2) || $val2 !=1) or (empty($val3) || $val3 !=1) or !$mysqlsupport or $_SESSION['sess_openbasedircheck'] !='1')
{
$serverconfiguration="FAILURE";
}//end if
else
{
$serverconfiguration="OK";
}//end else
}

//file n folder permission check  start here
$directories = array("../images/","../pics/profile/","../lang_flags/","../pics/","../help/","../sliders/","../includes/config.php");
umask(0);

$passed['files'] = true;
foreach ($directories as $dir)
{
$exists = $write = false;

// Try to create the directory if it does not exist
if (!file_exists($phpbb_root_path . $dir))
{
@mkdir($phpbb_root_path . $dir, 0777);
@chmod($phpbb_root_path . $dir, 0777);
}//end file check if

// Now really check
if (file_exists($phpbb_root_path . $dir) && is_dir($phpbb_root_path . $dir))
{
if (!@is_writable($phpbb_root_path . $dir))
{
@chmod($phpbb_root_path . $dir, 0777);
}//end if
$exists = true;
}//end second file check if

// Now check if it is writable by storing a simple file
$fp = @fopen($phpbb_root_path . $dir . 'test_lock', 'wb');
if ($fp !== false)
{
$write = true;
}//end write if
@fclose($fp);

@unlink($phpbb_root_path . $dir . 'test_lock');

$passed['files'] = ($exists && $write && $passed['files']) ? true : false;

$exists = ($exists) ? '<strong style="color:green">FOUND</strong>' : '<strong style="color:red">NOT_FOUND</strong>';
$write = ($write) ? ', <strong style="color:green">WRITABLE</strong>' : (($exists) ? 'UNWRITABLE' : '');
}//end for each
//file n folder permission check end here


$installed = false;
$txtDBServerName="localhost";
if ((isset($_POST["btnContinue"]) && $_POST["btnContinue"]=="Continue") || $_POST['cldpack'] == 1 )
{
if($_POST['cldpack'] == 1) {

$txtDBServerName    = "localhost";//$_POST["txtDBServerName"];
$txtDBName          = $_POST['db_name'];//$_POST["txtDBName"];
$txtDBUserName      = $_POST['db_user'];//$_POST["txtDBUserName"];
$txtDBPassword      = $_POST['db_password'];//$_POST["txtDBPassword"];
$txtDBPrefix        = "eswap_";//$_POST["txtDBPrefix"];
$txtSiteName        = $_POST['store_name'];//$_POST["txtSiteName"];
$txtAdminName       = 'admin';//$_POST["txtAdminName"];
$txtAdminPassword   = 'admin';//$_POST["txtAdminPassword"];
$txtConfirmAdminPassword = 'admin';//$_POST["txtConfirmAdminPassword"];
$txtAdminEmail      = $_POST['user_email'];//$_POST["txtAdminEmail"];

} else {

$txtDBServerName = $_POST["txtDBServerName"];
$txtDBName = $_POST["txtDBName"];
$txtDBUserName = $_POST["txtDBUserName"];
$txtDBPassword = $_POST["txtDBPassword"];
$txtDBPrefix = "eswap_";;
$txtLicenseKey = $_POST["txtLicenseKey"];
$txtSiteName = $_POST["txtSiteName"];
$txtAdminName = 'admin';
$txtAdminPassword = 'admin';
$txtConfirmAdminPassword = 'admin';
$txtAdminEmail = $_POST["txtAdminEmail"];
$ddlCurrencySymbol = CurrencyCodeCheck("USD");
$ddlCurrency = "USD";
}
$imagedir = "../images/";

//if not able to give permission automatically, give permission manually
if($write=='UNWRITABLE')
{
$imagedir = "../images/";
$picsdir = "../pics/";
$picsprofiledir = "../pics/profile/";
$langflagdir = "../lang_flags/";
$helpdir = "../help/";
$bannersdir = "../banners/";
$pemdir = "../pem/";
$slidersdir = "../sliders/";
}//end if

$logofile = $_FILES['userfile'];
$logofilename = ReplaceArray($_FILES['userfile']['name']);
$logofiletype = $_FILES['userfile']['type'];

$logotempname	= $_FILES['userfile']['tmp_name'];

$logoimagedest = $imagedir . $logofilename;
$wmfilesmalldest = $imagedir . $wmfilesmallname;
$wmfilebigdest = $imagedir . $wmfilebigname;

$message = "";
if($_POST['cldpack'] != 1) {
if (!isNotNull($txtDBServerName))
{
$message .= " * Database Server Name is empty!" . "<br>";
$error = true;
}//end if
if (!isNotNull($txtDBName))
{
$message .= " * Database Name is empty!" . "<br>";
$error = true;
}//end if
if (!isNotNull($txtDBUserName))
{
$message .= " * Database User Name is empty!" . "<br>";
$error = true;
}//end if
if (!isNotNull($txtLicenseKey))
{
$message .= " * License Key is empty!" . "<br>";
$error = true;
}//end if
if (!isNotNull($txtSiteName))
{
$message .= " * Site Name is empty!" . "<br>";
$error = true;
}//end if
if (!isNotNull($txtSiteURL))
{
$message .= " * Site URL is empty!" . "<br>";
$error = true;
}//end if
/*
if ($logofiletype != "")
{
if (!isValidWebImageType($logofiletype,$logofilename,$logotempname))
{
$message .= " * Invalid Logo file ! Upload an image (jpg/gif/bmp/png)" . "<br>";
$error = true;
}//end if
else
{
if (file_exists($logoimagedest))
{
@rename($imagedir.$logofilename,$imagedir.'old_'.$logofilename);
}//end if
}//end else
}//end if

if (!isNotNull($txtAdminName))
{
$message .= " * Admin Login Name is empty!" . "<br>";
$error = true;
}//end if
else
{
if(!isValidUsername($txtAdminName))
{
$message .= " * Invalid Admin Login Name! Please use only alphabets (aA-zZ) and underscore ( _ )!" . "<br>";
$error = true;
}//end if
}//end else

if (!isNotNull($txtAdminPassword))
{
$message .= " * Admin Password is empty!" . "<br>";
$error = true;
}//end if
if (!isNotNull($txtConfirmAdminPassword))
{
$message .= " * Admin Confirmation Password is empty!" . "<br>";
$error = true;
}//end if

if (isNotNull($txtConfirmAdminPassword) and isNotNull($txtAdminPassword))
{
if ($txtAdminPassword != $txtConfirmAdminPassword)
{
$message .= " * Admin Passwords should match!" . "<br>";
$error = true;
}//end if
}//end if
*/
if (!isNotNull($txtAdminEmail))
{
$message .= " * Admin Email is empty!" . "<br>";
$error = true;
}//end if
else
{
if (!isValidEmail($txtAdminEmail))
{
$message .= " * Invalid Admin Email!" . "<br>";
$error = true;
}//end if
}//end else
/*
if (isNotNull($txtDBPrefix))
{
if (!isValidTableName($txtDBPrefix))
{
$message .= " * Invalid table prefix! (Use only letters (aA-zZ) and underscore( _ ) with no spaces)" . "<br>";
$error = true;
}//end if
}//end if
*/
}
//if not able to give permission automatically
if($_POST['cldpack'] != 1) {
if($write=='UNWRITABLE')
{
if($OS != "WIN")
{
if (!is_writable($configfile) || !is_readable($configfile) || !is_executable($configfile))
{
$error = true;
$message .= " * config file not writable! Please change the permission(chmod 777 for linux server) of 'includes/config.php'<br>";
}//end if
if (!is_writable($imagedir) || !is_readable($imagedir) || !@file_exists("$imagedir"."."))
{
$error = true;
$message .= " * Provide write permission (chmod 777 for linux server) of 'images' folder<br>";
}//end if
if (!is_writable($picsdir) || !is_readable($picsdir) || !@file_exists("$picsdir"."."))
{
$error = true;
$message .= " * Provide write permission (chmod 777 for linux server) of 'pics' folder<br>";
}//end if
if (!is_writable($picsprofiledir) || !is_readable($picsprofiledir) || !@file_exists("$picsprofiledir"."."))
{
$error = true;
$message .= " * Provide write permission (chmod 777 for linux server) of 'pics/profile' folder<br>";
}//end if
if (!is_writable($langflagdir) || !is_readable($langflagdir) || !@file_exists("$langflagdir"."."))
{
$error = true;
$message .= " * Provide write permission (chmod 777 for linux server) of 'lang_flags' folder<br>";
}//end if
if (!is_writable($helpdir) || !is_readable($helpdir) || !@file_exists("$helpdir"."."))
{
$error = true;
$message .= " * Provide write permission (chmod 777 for linux server) of 'help' folder<br>";
}//end if
if (!is_writable($bannersdir) || !is_readable($bannersdir) || !@file_exists("$bannersdir"."."))
{
$error = true;
$message .= " * Provide write permission (chmod 777 for linux server) of 'banners' folder<br>";
}//end if
if (!is_writable($pemdir) || !is_readable($pemdir) || !@file_exists("$pemdir"."."))
{
$error = true;
$message .= " * Provide write permission (chmod 777 for linux server) of 'pem' folder<br>";
}//end if
if (!is_writable($slidersdir) || !is_readable($slidersdir) || !@file_exists("$slidersdir"."."))
{
	$error = true;
	$message .= " * Provide write permission (chmod 777 for linux server) of 'sliders' folder<br>";
}//end if
}//end if
}//end if
}
$connection = @mysqli_connect($txtDBServerName, $txtDBUserName, $txtDBPassword);
if ($connection===false)
{
$error = true;
$message .= " * Connection Not Successful! Please verify your database details!<br>";
}//end if
else
{
$dbselected = @mysqli_select_db($connection, $txtDBName);
if (!$dbselected)
{
$error = true;
$message .= " * Database could not be selected! Please verify your database details!<br>";
}//end if
}//end else



/*if( ini_get('safe_mode') )
 {//safe_mode is on
 $error = true;
 $message .= " * The script requires PHP with safe mode Off to work properly. Installation cannot continue! <br>";
 }//end if*/


if ($error)
{
$message = "<u><b>Please correct the following errors to continue:</b></u>" . "<br>" . $message;
// echo $message;
}//end if
else
{
//uploading logo
if($logofilename=="")
{
$logofilename = $logoimage;
}//end if
else
{
$imgResize=false;
//checking size
@list($width_new,$height_new)=@getimagesize($logotempname);
if($width_new>300)
{
$width_new='300';
$imgResize=true;
}//end if
else
{
$width_new=$width_new;
}//end else
//checking height
if($height_new>100)
{
$height_new='100';
$imgResize=true;
}//end if
else
{
$height_new=$height_new;
}//end else
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $logoimagedest))
{
chmod($logoimagedest,0777);
if($imgResize==ture)
{
$logofilename=resizeImg($logoimagedest, $width_new ,$height_new, false, 100, 0,"_thumb");
//$logofilename=substr($logofilename,10,strlen($logofilename));
}//end if
else
{
$logofilename=$logoimage;
}//end else
}//end if
else
{
$logofilename = $logoimage;
}//end else
}//end else


// writing to the config file....................
$uniqueid = time() . mt_rand() . session_id();
if (strlen($uniqueid) > 15)


{
$uniqueid = substr($uniqueid, 0, 15);
$uniqueid = md5($uniqueid);
}//end if

if(empty($ddlCurrency))
{
$ddlCurrency='$';
}//end if
//$ddlCurrencySymbol
$fp = fopen($configfile, "w+");
$configcontent = "<?php\n";
$configcontent .= "error_reporting(0); \n\n";
$configcontent .= "ob_start(); \n\n";
$configcontent .= "define('INSTALLED', true); \n\n";
if($_POST['cldpack'] == 1) {
$configcontent .= "define('CLOUDINSTALLED', true); \n\n";
$site_url_var = ""."'$'"."_SERVER["."SERVER_NAME]"."";
$site_url_1 = str_replace("'", "", $site_url_var);
$site_url = "'http://'.".$site_url_1;
if($_SERVER['HTTPS']=='on'){
	$http = "'https://'.";
}else {
	$http = "'http://'.";
}
$secure_site_url = $http.$site_url_1;
$configcontent .= "define('SITE_URL', $site_url); \n\n";
//$configcontent .="\t\t\t\t\t\t"."define('SECURE_SITE_URL', $secure_site_url);"."\n";
} else {
$configcontent .= "define('SITE_URL', '$txtSiteURL'); \n\n";
//$configcontent .="\t\t\t\t\t\t".'define("SECURE_SITE_URL", htmlentities($row["vLookUpDesc"]));'."\n";
}
$configcontent .= '$HOST = "' . $txtDBServerName . "\";" . "\n";
$configcontent .= '$DATABASENAME = "' . $txtDBName . "\";" . "\n";
$configcontent .= '$USER = "' . $txtDBUserName . "\";" . "\n";
$configcontent .= '$PASSWORD = "' . $txtDBPassword . "\";" . "\n\n";
$configcontent .= '$txtDBPrefix = "' . $txtDBPrefix . "\";" . "\n";
$configcontent .= '$imagefolder = SITE_URL . "/images";'."\n";
$configcontent .= '$stylesfolder = SITE_URL . "/themes";'."\n";
$configcontent .='$rootserver = SITE_URL;'."\n\n\n";
$configcontent .='//db connection'."\n";
$configcontent .='$conn = mysqli_connect($HOST, $USER, $PASSWORD);'."\n";
$configcontent .='mysqli_select_db($conn, $DATABASENAME);'."\n\n";
$configcontent .="define('TABLEPREFIX', '$txtDBPrefix');"."\n\n";
$configcontent .='$sql = "Select nLookUpCode,vLookUpDesc from ".$txtDBPrefix."lookup WHERE nLookUpCode IN(\'4\',';
$configcontent .='\'sitelogo\',\'sitestyle\',\'surl\',\'SessionTimeout\',\'currency\',\'currencycode\')";'."\n";
$configcontent .='$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));'."\n";;
$configcontent .='if (mysqli_num_rows($rs) > 0)'."\n";
$configcontent .='{'."\n";
$configcontent .="\t".'while($row = mysqli_fetch_array($rs))'."\n";
$configcontent .="\t\t".'{'."\n";
$configcontent .="\t\t\t".'switch($row["nLookUpCode"])'."\n";
$configcontent .="\t\t\t\t".'{'."\n";
$configcontent .="\t\t\t\t\t".'case 4:'."\n";
$configcontent .="\t\t\t\t\t\t".'define("SITE_EMAIL", htmlentities($row["vLookUpDesc"]));'."\n";
$configcontent .="\t\t\t\t\t\t".'define("ADMIN_EMAIL", htmlentities($row["vLookUpDesc"]));'."\n";
$configcontent .="\t\t\t\t\t".'break;'."\n";
$configcontent .="\t\t\t\t\t".'case "sitelogo":'."\n";
$configcontent .="\t\t\t\t\t\t".'$logourl  = $row["vLookUpDesc"];'."\n";
$configcontent .="\t\t\t\t\t".'break;'."\n";
$configcontent .="\t\t\t\t\t".'case "sitestyle": '."\n";
$configcontent .="\t\t\t\t\t\t".'$sitestyle = $row["vLookUpDesc"];'."\n";
$configcontent .="\t\t\t\t\t".'break;'."\n";
$configcontent .="\t\t\t\t\t".'case "surl": '."\n";
$configcontent .="\t\t\t\t\t\t".'define("SECURE_SITE_URL", htmlentities($row["vLookUpDesc"]));'."\n";
$configcontent .="\t\t\t\t\t".'break;'."\n";
$configcontent .="\t\t\t\t\t".'case "SessionTimeout": '."\n";
$configcontent .="\t\t\t\t\t\t".'define("SESSION_TIMEOUT", htmlentities($row["vLookUpDesc"]));'."\n";
$configcontent .="\t\t\t\t\t".'break;'."\n";
$configcontent .="\t\t\t\t\t".'case "currency": '."\n";
$configcontent .="\t\t\t\t\t\t".'define("CURRENCY_CODE", $row["vLookUpDesc"]);'."\n";
$configcontent .="\t\t\t\t\t".'break;'."\n";
$configcontent .="\t\t\t\t\t".'case "currencycode": '."\n";
$configcontent .="\t\t\t\t\t\t".'define("PAYMENT_CURRENCY_CODE", $row["vLookUpDesc"]);'."\n";
$configcontent .="\t\t\t\t\t".'break;'."\n";
$configcontent .="\t\t\t\t".'}'."\n";
$configcontent .="\t\t".'}'."\n";
$configcontent .='}'."\n";
$configcontent .='$secureserver=SECURE_SITE_URL;'."\n";
$configcontent .='define("UPGRADED2.4", true);'."\n";

$configcontent .= "\n?>";
fwrite($fp, $configcontent);


$sqlquery = @fread(@fopen($schemafile, 'r'), @filesize($schemafile));
//$sqlquery = preg_replace('/eswaps_/', $txtDBPrefix, $sqlquery);
$sqlquery = splitsqlfile($sqlquery, ";");
// creating the tables......................................
for($i = 0; $i < sizeof($sqlquery); $i++)
{
mysqli_query($connection, $sqlquery[$i]) or die(mysqli_error($connection));
}//end for loop

$dataquery = @fread(@fopen($datafile, 'r'), @filesize($datafile));
$dataquery = preg_replace('/eswaps_/', $txtDBPrefix, $dataquery);
$dataquery = splitsqlfile($dataquery, ";");
// populating the tables with initial data......................................
for($i = 0; $i < sizeof($dataquery); $i++)
{
mysqli_query($connection, utf8_encode($dataquery[$i])) or die(mysqli_error($connection));
}//end for loop
// inserting the admin details in to the settings table.............

$adminusername = $txtAdminName;
$adminpassword = md5($txtAdminPassword);
$sql = "Update " . $txtDBPrefix . "lookup set vLookUpDesc='" . addslashes($txtAdminName) . "'
							 where nLookUpCode = 'adminname'";

mysqli_query($connection, $sql) or die(mysqli_error($connection));

$sql = "Update " . $txtDBPrefix . "lookup set vLookUpDesc='" . addslashes($adminpassword) . "'
							 where nLookUpCode = '2'";

mysqli_query($connection, $sql) or die(mysqli_error($connection));

$sql1 = "Update " . $txtDBPrefix . "lookup set vLookUpDesc='" . addslashes($txtLicenseKey) . "'
							 where nLookUpCode = 'vLicenceKey'";

mysqli_query($connection, $sql1) or die(mysqli_error($connection));

/*$sql2 = "Update " . $txtDBPrefix . "lookup set vLookUpDesc='" . addslashes($txtSiteName) . "'
 where nLookUpCode = 'sitetitle'";

 mysqli_query($conn, $sql2) or die(mysqli_error($conn));

 $sql3 = "Update " . $txtDBPrefix . "lookup set vLookUpDesc='" . addslashes($txtSiteName) . "'
 where nLookUpCode = 'sitename'";

 mysqli_query($conn, $sql3) or die(mysqli_error($conn));

 */

$sql2 = "select content_id from " . $txtDBPrefix . "content where content_name = 'sitetitle' or content_name='sitename'";
$res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));
while ($row2 = mysqli_fetch_object($res2)){
if ($cids=='') $cids = $row2->content_id;
else $cids .= ','.$row2->content_id;
}

$sql3 = "Update " . $txtDBPrefix . "content_lang set content = '" . addslashes($txtSiteName) . "'
							 where content_id in (".$cids.")";

mysqli_query($connection, $sql3) or die(mysqli_error($connection));


$sql4 = "Update " . $txtDBPrefix . "lookup set vLookUpDesc='" . addslashes($logofilename) . "'
							 where nLookUpCode = 'sitelogo'";

mysqli_query($connection, $sql4) or die(mysqli_error($connection));

$sql5 = "Update " . $txtDBPrefix . "lookup set vLookUpDesc='" . addslashes($txtAdminEmail) . "'
							 where nLookUpCode = '4'";

mysqli_query($connection, $sql5) or die(mysqli_error($connection));

$sql6 = "Update " . $txtDBPrefix . "lookup set vLookUpDesc='" . addslashes($txtSecureSiteURL) . "'
							 where nLookUpCode = 'surl'";

mysqli_query($connection, $sql6) or die(mysqli_error($connection));


$sql7 = "Update " . $txtDBPrefix . "lookup set vLookUpDesc='" . addslashes($ddlCurrencySymbol) . "'
							 where nLookUpCode = 'currency'";

mysqli_query($connection, $sql7) or die(mysqli_error($connection));

$sql8 = "Update " . $txtDBPrefix . "lookup set vLookUpDesc='" . addslashes($ddlCurrency) . "'
							 where nLookUpCode = 'currencycode'";

mysqli_query($connection, $sql8) or die(mysqli_error($connection));

/* -------------- /
 New code for install tracker, added by girish
 /------------------*/
$string		= "";
$pro		= urlencode("eSwap 2.2");
$dom		= urlencode($txtSiteURL);
$ipv		= urlencode($_SERVER['REMOTE_ADDR']);
$mai		= urlencode($txtAdminEmail);
$string		= "pro=$pro&dom=$dom&ipv=$ipv&mai=$mai";
$contents	= "no";
$file		= @fopen("http://www.iscripts.com/installtracker.php?$string", 'r');
if ($file)
{
$contents = @fread($file, 8192);
}//end if
/* -------------- /
 New code for install tracker, added by girish
 /------------------*/
$installed = true;
}//end if
}//end if
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo SITE_TITLE;?></title>
<link href="<?php echo $stylesfolder;?>/<?php echo $sitestyle;?>"
	rel="stylesheet" type="text/css">
<script language="javascript1.1" type="text/javascript"
	src="../js/bubble-tooltip.js"></script>
<link rel="stylesheet" href="../styles/bubble-tooltip.css"
	type="text/css" media="screen">
<script language="javascript" type="text/javascript">
function showAuthMsg(ccode)
{
	if(ccode!='USD')
	{
		document.getElementById('showErrorMsg').style.display='';
	}//end if
	else
	{
		document.getElementById('showErrorMsg').style.display='none';
	}//end else
}//end function
</script>
<style type="text/css">
<!--
.install_option {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 9pt;
	color: #333333
}

.install_value_ok {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 9pt;
	font-weight: bold;
	color: #009900
}

.install_value_fail {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 7pt;
	font-weight: bold;
	color: #CC0000
}
-->
</style>
</head>
<body>
<div id="Layout" align="center">
<table width="100%" height="38" border="0" cellpadding="0"
	cellspacing="0">
	<tr>
		<td height="38" valign="middle" class="topcolor">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="44%">&nbsp;</td>
				<td width="56%">&nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="headerbg"><?php require_once("../includes/header_install.php");?>
		<table width="100%" height="25" border="0" cellpadding="0"
			cellspacing="0">
			<tr>
				<td width="67%" align="left" valign="middle" class="linkbar">
				<table width="60%" border="0" cellspacing="0" cellpadding="2">
					<tr align="center" class="link">
						<td width="8%"><a class="listing" title="OnlineInstallationManual"
							href="#"
							onClick="window.open('<?php echo htmlentities($txtSiteURL);?>/docs/eswap.doc','OnlineInstallationManual','top=100,left=100,width=820,height=550,scrollbars=yes,toolbar=no,status=yrd');"><strong>Installation
						Manual</strong></a></td>
						<td width="14%"><a class="listing" title="Readme" href="#"
							onClick="window.open('<?php echo htmlentities($txtSiteURL);?>/Readme.txt','Readme','top=100,left=100,width=820,height=550,scrollbars=yes,toolbar=no,status=yrd');"><strong>Read
						Me</strong></a></td>
						<td width="12%"><a class="listing"
							title="If you have any difficulty, submit a ticket to the support department"
							href="#"
							onClick="window.open('http://www.iscripts.com/support/postticketbeforeregister.php','','top=100,left=100,width=820,height=550,scrollbars=yes,toolbar=no,status=yrd,resizable=yes');">
						<strong>Get Support</strong></a></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="86%" valign="top">
						<table width="100%" border="0" cellspacing="0" cellpadding="2">
							<tr>
								<td class="link3">&nbsp;</td>
							</tr>
						</table>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td class="heading" align="left">Welcome to iScripts eSwap
								Installation</td>
							</tr>
						</table>
						<table width="100%" border="0" cellspacing="0" cellpadding="10">
							<tr>
								<td align="left" valign="top">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td bgcolor="#EEEEEE">
										<table width="100%" border="0" cellspacing="1" cellpadding="4"
											class="maintext2">
											<?php if ($serverconfiguration=="FAILURE")
							  		{
							  		$ivo = "<span class='install_value_ok'>";
							  		$ivf = "<span class='install_value_fail'>";
							  		$sc = "</span>";
							  		?>
											<tr bgcolor="#FFFFFF">
												<td width="24%" align="left">Checking PHP Version...</td>
												<td colspan="3" align="left"><?php echo $ivo.PHP_VERSION.$sc . " ";
												if(version_compare(PHP_VERSION,"5.0") >=0 )
												{
												echo $ivo."".$sc;
												}//end if
												else
												{
												echo $ivf."(5.0 or higher required)".$sc; $fatal = true;
												}//end else
												?></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">Checking System Information...</td>
												<td colspan="3" align="left"><?php echo $ivo. PHP_OS .$sc;?></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">Checking PHP Server API...</td>
												<td colspan="3" align="left"><?php echo $ivo. php_sapi_name().$sc;?></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">Checking Path to 'php.ini'...</td>
												<td colspan="3" align="left"><?php echo $ivo.PHP_CONFIG_FILE_PATH.$sc;?></td>
											</tr>
											<?php
											$mysqlsupport=true;
											if (!function_exists('mysqli_connect'))
											{
											$mysqlsupport = false;
											}//end if
											?>
											<tr bgcolor="#FFFFFF">
												<td height="28" align="left">Checking Mysql support...</td>
												<td colspan="3" align="left"><?php echo $ivo . (( $mysqlsupport) ? "On" : " $ivf This program requires MYSQL support. Please recompile your PHP with MYSQL Support.") . $sc;?></td>
											</tr>
											<!--  <tr bgcolor="#FFFFFF">
                                <td align="left">Checking safe_mode...</td>
                                <td colspan="3" align="left"><?php $val1 = ini_get("safe_mode");
											echo  ((!empty($val1) || $val1==1) ? $ivf ."On-Please turn off safe_mode in the php.ini" : $ivo ."Off") . $sc;
									?></td>
                              </tr>-->
											<!-- <tr bgcolor="#FFFFFF">
                                <td align="left">Checking short_tags...</td>
                                <td colspan="3" align="left"><?php $val2 = ini_get("short_open_tag");
											echo  ((!empty($val2) || $val2==1) ?$ivo."On" : $ivf."Off-Please turn on short_tags in the php.ini") . $sc;
											?></td>
                              </tr>-->
											<tr bgcolor="#FFFFFF">
												<td align="left">Checking file_uploads...</td>
												<td colspan="3" align="left"><?php $val3 = ini_get("file_uploads");
												echo ((!empty($val3) || $val3==1) ? $ivo . "On" : $ivf . "Off - Please turn on file_uplaods in the php.ini file").$sc;
												?></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">Checking open base directory...</td>
												<td colspan="3" align="left"><?php $val4 = ini_get("open_basedir");
												echo ((!empty($val4) || $val4==1) ? $ivf . "On - $ivf Please upload a test file " : $ivo . "Off ") . $sc;
												?></td>
											</tr>


											<tr bgcolor="#FFFFFF">
												<td colspan="4" align="left"><?php
												if((!empty($val4) || $val4==1)  and  (!empty($val3) || $val3==1) )
												{
												if($_POST["submittest"]=="Upload")
												{
												$uploadpath=substr($_FILES['testupload']['tmp_name'],0,strlen($_FILES['testupload']['tmp_name'])-strlen(basename($_FILES['testupload']['tmp_name']))-1);
												$uploadpatharray=explode("/",$uploadpath);
												$openbasedirarray=explode(":",$val4);
												$existflag=0;
												$checkitem="";

												for($i=1;$i<count($uploadpatharray);$i++)
												{
												$checkitem=$checkitem."/".$uploadpatharray[$i];
												if(! in_array($checkitem,$openbasedirarray)  and ! in_array($checkitem."/",$openbasedirarray))
												{
												;
												}//end if
												else
												{
												$existflag=1;
												break;
												}//end else
												}//end for loop

												if($existflag==1)
												{
												$openbasedircheck="1";
												}//end if
												else
												{
												echo $ivf."Please add '$uploadpath' in openbase directory entry of your configuration file.[contact your hosting provider]";
												}//end else
												$_SESSION['sess_openbasedircheck']=$openbasedircheck;
												}//end if
												if($openbasedircheck !="1")
												{
												?>
												<form name="frmInstall" method="post" action=""
													enctype="multipart/form-data">
												<table width="100%" border="0">
													<tr>
														<td width="35%" align="left" class="maintext2">File</td>
														<td align="left"><input type="file" name="testupload"
															class="textbox2"> <INPUT type="submit" name="submittest"
															value="Upload" class="submit"></td>
													</tr>
												</table>
												</form>
												<?php }//end if
												}//end if
												?></td>
											</tr>
											<?php
											if( (! empty($val1) || $val1==1) or  (empty($val2) || $val2 !=1) or (empty($val3) || $val3 !=1) or  ! $gdv or $openbasedircheck !='1')
											{

											?>
											<tr align="center" bgcolor="#FFFFFF">
												<td colspan="4" class="warning"><?php echo $ivf."Fatal errors detected.  Please correct the above red items and reload.";?></td>
											</tr>
											<?php }//end if
							  		}//end if
							  		else if (!$installed )
							  		{
							  		?>
											<tr bgcolor="#FFFFFF">
												<td colspan="4" align="left" class="subheader">Thank you for
												choosing <?php echo $productname?>.</td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td colspan="4" align="left">In order to complete this
												installation, enter the details requested below.<br>
												Please note the following points before you continue:<br>
												<?php
												//if not able to give permission automatically
												if($write=='UNWRITABLE')
												{
												?> &nbsp;&nbsp;&nbsp;&nbsp;1. Provide write permission
												(chmod 777 for linux server) of 'includes/config.php'.
												(After installation dont forget to change it back to 644)<br>
												&nbsp;&nbsp;&nbsp;&nbsp;2. Provide write permission (chmod
												777 for linux server) of 'images' folder. (After
												installation dont forget to change it back to 755)<br>
												&nbsp;&nbsp;&nbsp;&nbsp;3. Provide write permission (chmod
												777 for linux server) of 'pics' folder. <br>
												&nbsp;&nbsp;&nbsp;&nbsp;3. Provide write permission (chmod
												777 for linux server) of 'pics/profile' folder. <br>
												&nbsp;&nbsp;&nbsp;&nbsp;3. Provide write permission (chmod
												777 for linux server) of 'lang_flags' folder. <br>
												&nbsp;&nbsp;&nbsp;&nbsp;4. Provide write permission (chmod
												777 for linux server) of 'help' folder. <br>
												&nbsp;&nbsp;&nbsp;&nbsp;5. Provide write permission (chmod
												777 for linux server) of 'banners' folder. <br>
												&nbsp;&nbsp;&nbsp;&nbsp;6. Provide write permission (chmod
												777 for linux server) of 'pem' folder. <br>
												&nbsp;&nbsp;&nbsp;&nbsp;7. Provide write permission (chmod
												777 for linux server) of 'sliders' folder. <br>
												&nbsp;&nbsp;&nbsp;&nbsp;8. The database you install into
												should already exist.<br>
												&nbsp;&nbsp;&nbsp;&nbsp;9. Provide a table prefix to
												distinguish <?php echo $productname?> tables from other
												tables or if you have some similar tables.<br>
												&nbsp;&nbsp;&nbsp;&nbsp;10. After the installation delete the
												'install' folder and its contents. <?php }//end if
												else
												{
								  		echo "&nbsp;&nbsp;&nbsp;&nbsp;1. The database you install into should already exist.<br>
											  &nbsp;&nbsp;&nbsp;&nbsp;2. Provide a table prefix to distinguish ".$productname." tables from other tables or if you have some similar tables.<br>
											  &nbsp;&nbsp;&nbsp;&nbsp;3. After the installation delete the 'install' folder and its contents.";
												}//end else
												?></td>
											</tr>
											<?php if(isset($message) && $message!='')
							  		{
							  		?>
											<tr align="left" bgcolor="#FFFFFF">
												<td colspan="4" class="warning"><?php echo $message;?></td>
											</tr>
											<?php   }//end if?>
											<form name="frmInstall" method="post"
												action="<?php echo $_SERVER["PHP_SELF"];?>"
												enctype="multipart/form-data">
											<tr bgcolor="#FFFFFF">
												<td colspan="4" align="left" class="subheader">Database
												Details</td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">Database Server/Hostname</td>
												<td colspan="3" align="left"><input name="txtDBServerName"
													id="txtDBServerName" type="text" class="textbox2" size="20"
													maxlength="100"
													value="<?php echo htmlentities($txtDBServerName);?>"> <img src="css/Help.png" width="20" height="20" title="Database server name,eg:localhost"></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">Database Name</td>
												<td colspan="3" align="left"><input name="txtDBName"
													id="txtDBName" type="text" class="textbox2" size="20"
													maxlength="100"
													value="<?php echo htmlentities($txtDBName);?>"> <img src="css/Help.png" width="20" height="20" title="Name of your Mysql Database"></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">Database User Name</td>
												<td colspan="3" align="left"><input name="txtDBUserName"
													id="txtDBUserName" type="text" class="textbox2" size="20"
													maxlength="100"
													value="<?php echo htmlentities($txtDBUserName);?>"> <img src="css/Help.png" width="20" height="20" title="Mysql Username"></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">Database Password</td>
												<td colspan="3" align="left"><input name="txtDBPassword"
													id="txtDBPassword" type="text" class="textbox2" size="20"
													maxlength="100"
													value="<?php echo htmlentities($txtDBPassword);?>"> <img src="css/Help.png" width="20" height="20" title="Mysql Password"></td>
											</tr>
											<!--
											<tr bgcolor="#FFFFFF">
												<td align="left">Database Table Prefix</td>
												<td colspan="2" align="left"><input name="txtDBPrefix"
													id="txtDBPrefix" type="text" class="textbox2" size="20"
													maxlength="30"
													value="<?php echo htmlentities($txtDBPrefix);?>"
													onFocus="showToolTip(event,'Some hosts allow only a certain DB name per site. Use table prefix in this case for distinct iScripts eSwap','tblprefix','txtDBPrefix');"
													onBlur="hideToolTip('tblprefix')"></td>
												<td width="35%" class="dashedline3">
												<div id="bubble_tooltiptblprefix">
												<div class="bubble_top"><span>&nbsp;</span></div>
												<div class="bubble_middle"><span
													id="bubble_tooltip_contenttblprefix">&nbsp;</span></div>
												<div class="bubble_bottom"></div>
												</div>
												</td>
											</tr>
											-->
											<tr bgcolor="#FFFFFF">
												<td colspan="4" align="left" class="subheader">Site Details</td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">License Key</td>
												<td colspan="2" align="left"><input name="txtLicenseKey"
													id="txtLicenseKey" type="text" class="textbox2" size="40"
													maxlength="40"
													value="<?php echo htmlentities($txtLicenseKey);?>"
													> <img src="css/Help.png" width="20" height="20" title="The script would function only for the domain it is licensed. If you cannot recall the license, its also included in the email you received with subject: 'iScripts.com software download link'. You can also get the license key from your user panel at www.iscripts.com"></td>
												<td width="35%" class="dashedline3">
												<div id="bubble_tooltiplicensekey">
												<div class="bubble_top"><span>&nbsp;</span></div>
												<div class="bubble_middle"><span
													id="bubble_tooltip_contentlicensekey">&nbsp;</span></div>
												<div class="bubble_bottom"></div>
												</div>
												</td>
											</tr>
											<!--
											<tr bgcolor="#FFFFFF">
												<td align="left">Select Currency</td>
												<td colspan="3" align="left"><select name="ddlCurrency"
													class="textbox2" onChange="showAuthMsg(this.value);">
													<option value="USD"
													<?php if($ddlCurrency=='USD'){echo 'selected';}?>>U.S.
													Dollars</option>
													<option value="AUD"
													<?php if($ddlCurrency=='AUD'){echo 'selected';}?>>Australian
													Dollars</option>
													<option value="GBP"
													<?php if($ddlCurrency=='GBP'){echo 'selected';}?>>British
													Pounds</option>
													<option value="CAD"
													<?php if($ddlCurrency=='CAD'){echo 'selected';}?>>Canadian
													Dollars</option>
													<option value="CZK"
													<?php if($ddlCurrency=='CZK'){echo 'selected';}?>>Czech
													Koruna</option>
													<option value="DKK"
													<?php if($ddlCurrency=='DKK'){echo 'selected';}?>>Danish
													Kroner</option>
													<option value="EUR"
													<?php if($ddlCurrency=='EUR'){echo 'selected';}?>>Euros</option>
													 <img src="css/Help.png" width="20" height="20" title="Database server name,eg:localhost"><option value="HKD"
													<?php if($ddlCurrency=='HKD'){echo 'selected';}?>>Hong
													Kong Dollar</option>
													<option value="HUF"
													<?php if($ddlCurrency=='HUF'){echo 'selected';}?>>Hungarian
													Forint</option>
													<option value="ILS"
													<?php if($ddlCurrency=='ILS'){echo 'selected';}?>>Israeli
													New Shekels</option>
													<option value="JPY"
													<?php if($ddlCurrency=='JPY'){echo 'selected';}?>>Japanese
													Yen</option>
													<option value="MXN"
													<?php if($ddlCurrency=='MXN'){echo 'selected';}?>>Mexican
													Pesos</option>
													<option value="NZD"
													<?php if($ddlCurrency=='NZD'){echo 'selected';}?>>New
													Zealand Dollar</option>
													<option value="NOK"
													<?php if($ddlCurrency=='NOK'){echo 'selected';}?>>Norwegian
													Kroner</option>
													<option value="PLN"
													<?php if($ddlCurrency=='PLN'){echo 'selected';}?>>Polish
													Zlotych</option>
													<option value="SGD"
													<?php if($ddlCurrency=='SGD'){echo 'selected';}?>>Singapore
													Dollar</option>
													<option value="SEK"
													<?php if($ddlCurrency=='SEK'){echo 'selected';}?>>Swedish
													Kronor</option>
													<option value="CHF"
													<?php if($ddlCurrency=='CHF'){echo 'selected';}?>>Swiss
													Francs</option>
												</select></td>
											</tr>
											
											<tr bgcolor="#FFFFFF" id="showErrorMsg" style="<?php if($_POST['ddlCurrency']=='USD' || $_POST['ddlCurrency']==''){echo 'display:none;';}?>">
												<td align="left">&nbsp;</td>
												<td colspan="3" align="left" class="maintext warning"><b>Authorize.net
												will support only US Dollar</b></td>
											</tr>
											-->
											<tr bgcolor="#FFFFFF">
												<td align="left">Site Name</td>
												<td colspan="4" align="left"><input name="txtSiteName"
													id="txtSiteName" type="text" class="textbox2" size="40"
													maxlength="100"
													value="<?php echo htmlentities($txtSiteName);?>"> <img src="css/Help.png" width="20" height="20" title="Official Sitename"><input
													name="txtSiteURL" id="txtSiteURL" type="hidden"
													class="textbox2" size="50" maxlength="100"
													value="<?php echo htmlentities($txtSiteURL);?>" readonly></td>
											</tr>
											<!--
											<tr bgcolor="#FFFFFF">
												<td align="left" valign="top">Site Logo</td>
												<td width="23%" align="left" valign="top"><input type="file"
													name="userfile" class="textbox2"></td>
												<td colspan="2" align="left"><img src="../images/logo.gif"
													width="300" height="100"></td>
											</tr>
											-->
											<tr bgcolor="#FFFFFF">
												<td colspan="4" align="left" class="subheader">Administration
												Details</td>
											</tr>
											<!--
											<tr bgcolor="#FFFFFF">
												<td align="left">Admin Login Name</td>
												<td colspan="3" align="left"><input name="txtAdminName"
													id="txtAdminName" type="text" class="textbox2" size="40"
													maxlength="100"
													value="<?php echo htmlentities($txtAdminName);?>"></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">Admin Password</td>
												<td colspan="3" align="left"><input
													name="txtConfirmAdminPassword" id="txtConfirmAdminPassword"
													type="password" class="textbox2" size="40" maxlength="100"
													value=""></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">Confirm Admin Password</td>
												<td colspan="3" align="left"><input name="txtAdminPassword"
													id="txtAdminPassword" type="password" class="textbox2"
													size="40" maxlength="100" value=""></td>
											</tr>
											-->
											<tr bgcolor="#FFFFFF">
												<td align="left">Admin Email</td>
												<td colspan="3" align="left"><input name="txtAdminEmail"
													id="txtAdminEmail" type="text" class="textbox2" size="40"
													maxlength="100"
													value="<?php echo htmlentities($txtAdminEmail);?>"> <img src="css/Help.png" width="20" height="20" title="Email of Site Admin"></td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td align="left">&nbsp;</td>
												<td colspan="3" align="left"><input type="submit"
													name="btnContinue" value="Continue" class="submit"></td>
											</tr>
											<?php }//end else if
											else
											{ 
											?>
											<tr align="center" bgcolor="#FFFFFF">
												<td colspan="4" class="subheader">Congratulations! The
												installation completed successfully!</td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td colspan="4"><b>To ensure complete security, now <br>
												a)You should remove the 'install' directory.<br>
												b)Change the write permission of includes/config.php to 644</b>
												</td>
											</tr>
											<tr bgcolor="#FFFFFF">
												<td colspan="4" align="left">
												<table width="100%" border="0" cellspacing="1"
													cellpadding="5">
													<tr>
														
														<td colspan="3" align="left" valign="top">
															<a target="_blank">Login to the admin panel</a> and change
														the settings to suit yours.</td>
													</tr>
													<tr colspan="2" >
														<td width="31%" rowspan="2" align="left" valign="top"><img
															src="../images/admin_login_install.jpg" border="0"
															height="25"><strong>Admin
														URL </strong></td>
														<td width="2%" align="left" valign="top"><a
															href="<?php echo $txtSiteURL.'/admin/index.php';?>"
															target="_blank"></a></td>
														</tr>
													
													<tr>
														
														<td width="67%" align="left" valign="top"><a
															href="<?php echo $txtSiteURL.'/admin/index.php';?>"
															target="_blank"><?php echo $txtSiteURL.'/admin/index.php';?></a></td>
													</tr>
													<tr>
														<td width="2%" align="left" valign="top">Admin Username</a></td>
														<td width="67%" align="left" valign="top">admin</td>
													</tr>
													<tr>
														<td width="2%" align="left" valign="top">Admin Password</a></td>
														<td width="67%" align="left" valign="top">admin</td>
													</tr>
													<tr>
														<td align="left" valign="top"><img src="../images/home_page.jpg"
															border="0" height="25"><strong>Home URL </strong></td>
														<td align="left" valign="top"><a
															href="<?php echo $txtSiteURL.'/index.php';?>"
															target="_blank"><?php echo $txtSiteURL.'/index.php';?></a></td>
													</tr>
												</table>
												</td>
											</tr>
											<?php }//end else?></form>
										</table>
										</td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<?php require_once("../includes/installfooter.php");?>

                            
                            
