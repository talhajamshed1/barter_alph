<?php
// error_reporting(0); 

ob_start(); 

define('INSTALLED', true); 

define('SITE_URL', 'http://localhost/barter/'); 

$HOST = "localhost:3306";
$DATABASENAME = "alphares1_barter";
$USER = "root";
$PASSWORD = "1";

$txtDBPrefix = "eswap_";
$imagefolder = SITE_URL . "/images";
$stylesfolder = SITE_URL . "/themes";
$rootserver = SITE_URL;


//db connection
$conn = mysqli_connect($HOST, $USER, $PASSWORD);
mysqli_select_db($conn, $DATABASENAME);

define('TABLEPREFIX', 'eswap_');

$sql = "Select nLookUpCode,vLookUpDesc from ".$txtDBPrefix."lookup WHERE nLookUpCode IN('4','sitelogo','sitestyle','surl','SessionTimeout','currency','currencycode')";
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($rs) > 0)
{
	while($row = mysqli_fetch_array($rs))
		{
			switch($row["nLookUpCode"])
				{
					case 4:
						define("SITE_EMAIL", htmlentities($row["vLookUpDesc"]));
						define("ADMIN_EMAIL", htmlentities($row["vLookUpDesc"]));
					break;
					case "sitelogo":
						$logourl  = $row["vLookUpDesc"];
					break;
					case "sitestyle": 
						$sitestyle = $row["vLookUpDesc"];
					break;
					case "surl": 
						define("SECURE_SITE_URL", htmlentities($row["vLookUpDesc"]));
					break;
					case "SessionTimeout": 
						define("SESSION_TIMEOUT", htmlentities($row["vLookUpDesc"]));
					break;
					case "currency": 
						define("CURRENCY_CODE", $row["vLookUpDesc"]);
					break;
					case "currencycode": 
						define("PAYMENT_CURRENCY_CODE", $row["vLookUpDesc"]);
					break;
				}
		}
}
$secureserver=SECURE_SITE_URL;
define("UPGRADED2.4", true);

?>