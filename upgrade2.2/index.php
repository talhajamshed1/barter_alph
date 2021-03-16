<?php
session_start();
error_reporting(0);
function isValidUsername($str)
{
    if (trim($str) !="" ) 
	{
        if ( preg_match ( "/[^0-9a-zA-Z+_]/", $str ) )
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
   /* if (!eregi("^" . "[a-z0-9]+([_\\.-][a-z0-9]+)*" . // user
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

include_once('../includes/config.php');

$configfile = "../includes/config.php";

$logoimage="logo.gif";
$imagefolder =  "../images";
$stylesfolder = "../styles";
$sitestyle="default2.css";
$logourl="logo.gif";
$productname="iScript eSwap";

$configcontents = @fread(@fopen($configfile, 'r'), @filesize($configfile));
$pos = strpos($configcontents, "UPGRADED2.2");
if ($pos===false) 
{
	;
}//end if
else 
{
   header("location:../index.php");
   exit();
}//end else

//Section - A - include the settings  file here and assign the database connection 
//and open a live connecton here to the database

$var_host = $HOST;
$var_user = $USER;
$var_password = $PASSWORD;
$var_database = $DATABASENAME;
$txtDBPrefix = TABLEPREFIX;
$txtSiteName = "iScript eSwap";
$txtAdminEmail=ADMIN_EMAIL;
$var_server=$rootserver;

$flag = false;
$num = 0;
if ($connection = @mysqli_connect($var_host,$var_user,$var_password)) {
	if (@mysqli_select_db($connection, $var_database)) {
		$flag = true;
	}
	else {
		echo("Cannot select the db from the given connection.  Please check your configuration settings.");
		exit;
	}
}	
else {
	echo("Cannot select the db from the given connection.  Please check your configuration settings.");
	exit;
}

//End Section - A


$fullurl = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
if($_SERVER['HTTPS']=='on')
{
	$http = "https://";
}//end if
else 
{
	$http = "http://";
}//end else

$pos = strrpos($fullurl, "/");
if ($pos===false) 
{ 
	// note: three equal signs
    // not found...
}//end if
else 
{
    $fullurl = substr($fullurl, 0, $pos);
}//end else

$txtSiteURL ="http://".$fullurl;
$txtSecureSiteURL="https://" . $fullurl;

//check server configuration
  $OS	= getServerOS();
//PHP 5.4 Fix
  /*$val1 = ini_get("safe_mode");
  $val2 = ini_get("short_open_tag");*/
  $val3 = ini_get("file_uploads");
  $val4 = ini_get("open_basedir");

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
  
   $mysqlsupport=true;
   if (!function_exists('mysqli_connect')) 
   {
        $mysqlsupport = false;
   }//end if

  if( (! empty($val1) || $val1==1) or  (empty($val2) || $val2 !=1) or (empty($val3) || $val3 !=1) or !$mysqlsupport or $_SESSION['sess_openbasedircheck'] !='1')
  {
	   $serverconfiguration="FAILURE";
  }//end if 
  else
  {
	 $serverconfiguration="OK";
  }//end else

//file n folder permission check  start here
$directories = array("../images/","../pics/profile/","../lang_flags/","../pics/","../help/","../includes/config.php");
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

$upgrade = false;
$txtDBServerName="localhost";
if (isset($_POST["btnContinue"]) && $_POST["btnContinue"]=="Continue") 
{
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
	}//end if
    $message = "";

//if not able to give permission automatically
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
	}//end if
}//end if
//PHP 5.4 Fix
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
		 // writing to the config file....................
    	$uniqueid = time() . mt_rand() . session_id();
    	if (strlen($uniqueid) > 15) 
		{
        	$uniqueid = substr($uniqueid, 0, 15);
			$uniqueid = md5($uniqueid);
    	}//end if
    
        require_once('./upgrade_sql.php');//upgrade database
                
        /**Split the queries in the file and run**/
        $sqlquery = @fread(@fopen("data.sql", 'r'), @filesize("data.sql"));
        $sqlquery = preg_replace('/eswaps_/', $txtDBPrefix, $sqlquery);
        $sqlquery = splitsqlfile($sqlquery, ";");
        for($i = 0; $i < sizeof($sqlquery); $i++){
            mysqli_query($connection, utf8_encode($sqlquery[$i])) or die(mysqli_error($connection));//$sqlquery[$i]."<br />".
        }//end for loop

        //store config file details
        $configfile_content=file_get_contents($configfile);

        $final_configcontentNew="define('UPGRADED2.2', true); \n\n?>";
        //replace installed with upgrad with new values
        $final_configcontent=str_replace('?>',$final_configcontentNew,$configfile_content);
        //$final_configcontent=str_replace(",'sitelogo','sitestyle'",'',$final_configcontent);
        //changes write into config.inc.php
        $fp = fopen($configfile,'w+');
        fwrite($fp,$final_configcontent);
        
        $upgrade3 = true;
    }//end if
}//end if
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo "iScript eSwap Upgradation";?></title>
<link href="<?php echo $stylesfolder;?>/<?php echo $sitestyle;?>" rel="stylesheet" type="text/css">
</head>
<body>
<div id="Layout" align="center">
<table width="100%" height="38"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td height="38" valign="middle" class="topcolor">      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="44%">&nbsp;</td>
          <td width="56%">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="headerbg"><?php require_once("../includes/header_install.php");?>
	<table width="100%" height="25"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="67%" align="left" valign="middle" class="linkbar"><table width="60%"  border="0" cellspacing="0" cellpadding="2">
              <tr align="center" class="link">
                <td width="8%"><a class="listing" title="OnlineInstallationManual" href="#" onClick="window.open('<?php echo htmlentities($txtSiteURL);?>/docs/eswap.doc','OnlineInstallationManual','top=100,left=100,width=820,height=550,scrollbars=yes,toolbar=no,status=yrd');"><strong>Upgradation Manual</strong></a></td>
                <td width="14%"><a class="listing" title="Readme" href="#" onClick="window.open('<?php echo htmlentities($txtSiteURL);?>/Readme.txt','Readme','top=100,left=100,width=820,height=550,scrollbars=yes,toolbar=no,status=yrd');"><strong>Read Me</strong></a></td>
                <td width="12%"><a class="listing" title="If you have any difficulty, submit a ticket to the support department" href="#" onClick="window.open('http://www.iscripts.com/support/postticketbeforeregister.php','','top=100,left=100,width=820,height=550,scrollbars=yes,toolbar=no,status=yrd,resizable=yes');">														
						<strong>Get Support</strong></a></td>
              </tr>
            </table></td>
          </tr>
        </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="14%" valign="top">&nbsp;</td>
                <td width="86%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="link3">&nbsp;</td>
                  </tr>
                </table>
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="heading" align="left">Welcome to iScripts eSwap Upgradation</td>
                  </tr>
                </table>
				<table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                              <?php if ($serverconfiguration=="FAILURE") 
							  		{
										$ivo = "<span class='install_value_ok'>";
										$ivf = "<span class='install_value_fail'>";
										$sc = "</span>";
							   ?>
							  <tr bgcolor="#FFFFFF">
                                <td width="27%" align="left">Checking PHP Version...</td>
                                <td width="73%" align="left"><?php echo $ivo.PHP_VERSION.$sc . " ";
									if(version_compare(PHP_VERSION,"5.0") >=0 )
									{
										echo $ivo."".$sc; 
									}//end if
									else 
									{
										echo $ivf."(5.0 or higher required)".$sc; $fatal = true;
									}//end else
									?>
								</td>
							  </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Checking System Information...</td>
                                <td align="left"><?php echo $ivo. PHP_OS .$sc;?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Checking PHP Server API...</td>
                                <td align="left"><?php echo $ivo. php_sapi_name().$sc;?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Checking Path to 'php.ini'... </td>
                                <td align="left"><?php echo $ivo.PHP_CONFIG_FILE_PATH.$sc;?></td>
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
                                <td align="left"><?php echo $ivo . (( $mysqlsupport) ? "On" : " $ivf This program requires MYSQL support. Please recompile your PHP with MYSQL Support.") . $sc;?></td>
                              </tr>
                            <!--
                            //PHP 5.4 Fix
                            <tr bgcolor="#FFFFFF">
                                <td align="left">Checking safe_mode...</td>
                                <td align="left"><?php $val1 = ini_get("safe_mode");
											echo  ((!empty($val1) || $val1==1) ? $ivf ."On-Please turn off safe_mode in the php.ini" : $ivo ."Off") . $sc;
									?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Checking short_tags...</td>
                                <td align="left"><?php $val2 = ini_get("short_open_tag");
											echo  ((!empty($val2) || $val2==1) ?$ivo."On" : $ivf."Off-Please turn on short_tags in the php.ini") . $sc;
											?></td>
                              </tr>-->
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Checking file_uploads...</td>
                                <td align="left"><?php echo $val3 = ini_get("file_uploads");
											echo ((!empty($val3) || $val3==1) ? $ivo . "On" : $ivf . "Off - Please turn on file_uplaods in the php.ini file").$sc;
											?></td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">Checking open base directory...</td>
                                <td align="left"><?php $val4 = ini_get("open_basedir");
											echo ((!empty($val4) || $val4==1) ? $ivf . "On - $ivf Please upload a test file " : $ivo . "Off ") . $sc;
											?></td>
                              </tr>


                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><?php
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
								<form name="frmInstall" method="post" action="" enctype="multipart/form-data">
											<table width="100%" border="0">
											<tr>
											  <td width="35%" align="left" class="maintext2">
												 File</td>
												<td align="left"><input type="file" name="testupload" class="textbox2">
												<INPUT type="submit" name="submittest" value="Upload" class="submit">
												</td>
											</tr></table>	
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
                                <td colspan="2" class="warning"><?php echo $ivf."Fatal errors detected.  Please correct the above red items and reload.";?></td>
                              </tr>
							  <?php }//end if
							  }//end if
							  else if (!$upgrade3 ) 
							  {
							  ?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" class="subheader">Thank you for choosing <?php echo $productname?>.</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left">
							<?php
							 //if not able to give permission automatically
							 if($write=='UNWRITABLE')
							 {
							?>
								In order to complete this upgradation, enter the details requested below.<br>Please note the following points before you continue:<br>
								&nbsp;&nbsp;&nbsp;&nbsp;1. Provide write permission (chmod 777 for linux server) of 'includes/config.php'. (After installation dont forget to change it back to 644)<br>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;2. Provide write permission (chmod 777 for linux server) of 'images' folder. (After installation dont forget to change it back to 755)<br>
								&nbsp;&nbsp;&nbsp;&nbsp;3. Provide write permission (chmod 777 for linux server) of 'pics' folder. <br>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;3. Provide write permission (chmod 777 for linux server) of 'pics/profile' folder. <br>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;3. Provide write permission (chmod 777 for linux server) of 'lang_flags' folder. <br>
								&nbsp;&nbsp;&nbsp;&nbsp;4. Provide write permission (chmod 777 for linux server) of 'help' folder. <br>
								&nbsp;&nbsp;&nbsp;&nbsp;5. Provide write permission (chmod 777 for linux server) of 'banners' folder. <br>
								&nbsp;&nbsp;&nbsp;&nbsp;6. Provide write permission (chmod 777 for linux server) of 'pem' folder. <br>
								&nbsp;&nbsp;&nbsp;&nbsp;7. After the upgradation delete the 'upgrade2.2' folder and its contents.
								<?php }//end if
								  else
								  {
								  		echo 'Please note the following points before you continue:<br>';
								  		echo "&nbsp;&nbsp;&nbsp;&nbsp;1. After the upgradation delete the 'upgrade2.2' folder and its contents.";
								  }//end else
						   ?>				</td>
                              </tr>
							  <?php if(isset($message) && $message!='')
							  		{
							  ?>
                              <tr align="left" bgcolor="#FFFFFF">
                                <td colspan="2" class="warning"><?php echo $message;?></td>
                              </tr>
							  <?php   }//end if?>
							  <form name="frmInstall" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>" enctype="multipart/form-data">
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td align="left"><input type="submit" name="btnContinue" value="Continue" class="submit"></td>
                              </tr>
							  <?php }//end else if
							   else 
							   {
							?>
                              <tr align="center" bgcolor="#FFFFFF">
                                <td colspan="2" class="subheader">Congratulations! The upgradation completed successfully!</td>
                                </tr>
                              <tr bgcolor="#FFFFFF">
							  <td colspan="2"><b>To ensure complete security, now <br> 
                                  a)You should remove the 'upgrade2.2' directory.<br>
								b)Change the permission of includes/config.php to 644</b>								</td>
                                </tr>
							   <tr bgcolor="#FFFFFF">
                                <td align="left" colspan="2"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
                                      <tr>
                                        <td width="31%" rowspan="2" align="left" valign="top"><strong>Admin URL </strong></td>
                                        <td colspan="2" align="left" valign="top"><a href="<?php echo $txtSiteURL.'/admin/index.php';?>" target="_blank">Login to the admin panel</a> and change the settings to suit yours.</td>
                                      </tr>
                                      <tr>
                                        <td width="2%" align="left" valign="top"><a href="<?php echo $txtSiteURL.'/admin/index.php';?>" target="_blank"><img src="../images/admin_login_install.jpg" border="0" height="25"></a></td>
                                        <td width="67%" align="left" valign="top"><a href="<?php echo $txtSiteURL.'/admin/index.php';?>" target="_blank"><?php echo $txtSiteURL.'/admin/index.php';?></a></td>
                                      </tr>
                                      <tr>
                                        <td align="left" valign="top"><strong>Home URL </strong></td>
                                        <td align="left" valign="top"><a href="<?php echo $txtSiteURL.'/index.php';?>" target="_blank"><img src="../images/home_page.jpg" border="0" height="25"></a></td>
                                        <td align="left" valign="top"><a href="<?php echo $txtSiteURL.'/index.php';?>" target="_blank"><?php echo $txtSiteURL.'/index.php';?></a></td>
                                      </tr>
                                    </table></td>
                                </tr>
								<?php }//end else?>
							  </form>
                            </table>
</td>
                          </tr>
                        </table></td>
                      </tr>
                  </table>
				</td>
              </tr>
            </table></td>
          </tr>
      </table>
<?php require_once("../includes/installfooter.php");?>