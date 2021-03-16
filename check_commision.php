<?php
include ("./includes/config.php");
include ("./includes/functions.php");

//typed amount
$Amount=$_GET['q'];

if(isset($_GET["from"]) && $_GET["from"] =='points'){
   $points = $Amount;
   $Amount =  getEquivalentPriceFromPoint($points);
}
$Amount=round($Amount,2);

$getListingFeeCommissionSql = "SELECT * FROM ".TABLEPREFIX."listingfee WHERE vActive='1' AND (
                              (nFrom <= '".$Amount."' AND nTo >= '".$Amount."') )";
$listingFeeCommissionRes    = mysqli_query($conn, $getListingFeeCommissionSql);


if(mysqli_num_rows($listingFeeCommissionRes) < 1){
    mysqli_free_result($listingFeeCommissionRes);
    $getListingFeeCommissionSql = "SELECT * FROM ".TABLEPREFIX."listingfee WHERE vActive='1' AND
                              (above <= '".$Amount."') AND above > 0 ORDER BY above desc";	
    $listingFeeCommissionRes    = mysqli_query($conn, $getListingFeeCommissionSql);
}
    
$listingFeeCommissionRow    = mysqli_fetch_array($listingFeeCommissionRes);
$listingFeeCommission       = $listingFeeCommissionRow["nPrice"];


/*
 * Caluculate listing fee based on the commission fetched
 */
$listingFee     = round(($listingFeeCommission*$Amount)/100,2);

if($listingFee){
    echo round($listingFee,2);
}else{
    echo "0";
}
?>
