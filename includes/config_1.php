<?php
ob_start();

define('INSTALLED', true);

define('SITE_URL', 'http://localhost/eswap');

$HOST = "192.168.0.11";
$DATABASENAME = "db_eswaps2_2";
$USER = "root";
$PASSWORD = "status";

$txtDBPrefix = "es_";
$imagefolder = SITE_URL . "/images";
$stylesfolder = SITE_URL . "/themes";
$rootserver = "http://localhost/eswap";

$rootserver = SITE_URL;

//db connection
$conn = mysqli_connect($HOST, $USER, $PASSWORD);
mysqli_select_db($conn, $DATABASENAME);

define('TABLEPREFIX', 'es_');

$sql = "Select nLookUpCode,vLookUpDesc from ".$txtDBPrefix."lookup WHERE nLookUpCode IN('4','sitetitle','sitename','sitelogo','sitestyle','surl','SessionTimeout','currency','currencycode')";
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
					case "sitetitle":
						define("SITE_TITLE", htmlentities($row["vLookUpDesc"]));
					break;
					case "sitename":
						define("SITE_NAME", htmlentities($row["vLookUpDesc"]));
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

?>
