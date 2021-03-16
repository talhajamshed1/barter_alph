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
include ("./includes/session_check.php");

include_once('./includes/gpc_map.php');

$qryopt = "";
if ($_POST["txtSearch"] != "") {
    $txtSearch = $_POST["txtSearch"];
}//end if
else if ($_GET["txtSearch"] != "") {
    $txtSearch = $_GET["txtSearch"];
}//end else if
if ($_POST["ddlSearchType"] != "") {
    $ddlSearchType = $_POST["ddlSearchType"];
}//end if
else if ($_GET["ddlSearchType"] != "") {
    $ddlSearchType = $_GET["ddlSearchType"];
}//end else if

$qryopt = "";

if ($txtSearch != "") {
    if ($ddlSearchType == "transno") {
        $qryopt .= "  and vTxnId like '" . addslashes($txtSearch) . "%'";
    }//end if
    else if ($ddlSearchType == "amount") {
        $qryopt .= "  and abs(nSwapAmount) like '" . addslashes($txtSearch) . "%'";
    }//end else if
    else if ($ddlSearchType == "transmode") {
        $va_method = "";
        //$txtSearchNew = get_payment_name($txtSearch);
        $qryopt .= " and vMethod='$txtSearch' ";
    }//end if
    else if ($ddlSearchType == "date") {
        $date = $txtSearch;
       // $arr = split("/", $date);
         $arr = explode("/", $date);
        if (strlen($arr[0]) < 2) {
            $month = "0" . $arr[0];
        }//end if
        else {
            $month = $arr[0];
        }//end else
        if (strlen($arr[1]) < 2) {
            $day = "0" . $arr[1];
        }//end if
        else {
            $day = $arr[1];
        }//end else
        $year = $arr[2];
        $newdate = $year . "-" . $month . "-" . $day;
        $qryopt .= "  and dTxnDate  like '" . addslashes($newdate) . "%'";
    }//end else if
}//end if


//$sql = " SELECT sw.vTitle, sw.nSwapAmount,date_format(sw.dTxnDate,'%m/%d/%Y') as 'dTxnDate', date_format(sw.dPostDate,'%m/%d/%Y') as dDate, sw.vTxnId, sw.vMethod, vPostType, ";
//$sql .= " IF(sw.nSwapAmount < 0,u1.vLoginName,u.vLoginName) as LoginName ,";
//$sql .= " IF(sw.vSwapStatus ='2','Partner Paid','Escrow Paid') as vStatus ";
//$sql .= " FROM " . TABLEPREFIX . "swap sw LEFT JOIN  " . TABLEPREFIX . "users u ON u.nUserId  = sw.nSwapMember ";
//$sql .= " LEFT JOIN " . TABLEPREFIX . "users u1 ON u1.nUserId = sw.nUserId ";
//$sql .= " WHERE ";
//$sql .= " (sw.nSwapMember  = '" . $_SESSION["guserid"] . "' AND (sw.vSwapStatus ='2'  OR sw.vSwapStatus ='3') AND sw.nSwapAmount < 0 )" . $qryopt;
//$sql .= " OR  (sw.nUserId  = '" . $_SESSION["guserid"] . "' AND (sw.vSwapStatus ='2'  OR sw.vSwapStatus ='3') AND sw.nSwapAmount > 0 ) " . $qryopt;
//$sql .= "  order by sw.dPostDate DESC ";

function getdbcontents_sql($sql)
{
    global $conn;
        $fn_res		=	mysqli_query($conn, $sql);
        $arrcnt		=	-1;
        $dataarr	=	array();
        while($temp	= mysqli_fetch_assoc($fn_res))
                {
                        $arrcnt++;
                        $dataarr[$arrcnt]	=	$temp;
                }
        return $dataarr;
}


$sql = "SELECT (
    CASE 
        WHEN nUserId='".$_SESSION["guserid"]."' AND nAmountTake> 0 THEN nSwapId
        WHEN nUserReturnId='".$_SESSION["guserid"]."' AND nAmountGive> 0 THEN nSwapReturnId      
    END) AS swapId,
    (
    CASE 
        WHEN nUserId='".$_SESSION["guserid"]."' AND nAmountTake> 0 THEN nSwapReturnId
        WHEN nUserReturnId='".$_SESSION["guserid"]."' AND nAmountGive> 0 THEN nSwapId      
    END) AS swapedId,(
    CASE 
        WHEN nUserId='".$_SESSION["guserid"]."' AND nAmountTake> 0 THEN nAmountTake
        WHEN nUserReturnId='".$_SESSION["guserid"]."' AND nAmountGive> 0 THEN nAmountGive      
    END) AS amount
 from ". TABLEPREFIX."swaptxn WHERE vStatus='A'";
$result = getdbcontents_sql($sql);
$swapIdArray = array();
$swapAmountArray = array();
foreach($result as $key=>$val)
{
if(trim($val['swapId'])){
    $swapIdArray[] = $val['swapId'];
    $swapAmountArray[$val['swapId']]['amount'] = $val['amount'];
    $swapAmountArray[$val['swapId']]['swapItem'] = $val['swapedId'];
}
}
$swapIdString =  implode(",",  array_unique($swapIdArray));
$sql = " SELECT nSwapId,vTitle,nUserId, nSwapAmount,date_format(dTxnDate,'%m/%d/%Y') as 'dTxnDate', date_format(dPostDate,'%m/%d/%Y') as dDate, vTxnId, vMethod, vPostType";
$sql .= " FROM " . TABLEPREFIX . "swap";

if($swapIdString)$sql .= " WHERE nSwapId IN (".$swapIdString.")";
else $sql .= " WHERE nSwapId IN ('nil')";
 $sql .= $qryopt;
$sql .= "  order by dPostDate DESC ";


$sess_back = "mypayments.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch;

$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

$sql=dopaging($sql,'',PAGINATION_LIMIT);
//$sql = $sql . $navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$numRecords = mysqli_num_rows($rs);

/*$sqlsale=dopaging($sqlsale,'',PAGINATION_LIMIT);
$rssale = mysqli_query($conn, $sqlsale) or die(mysqli_error($conn));

$navigate = pageBrowser($totalrows, 5, 5, "&ddlSearchType=$ddlSearchType&txtSearch=$txtSearch&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
*/
 if($numRecords>0) {
$pagenumber     =   getCurrentPageNum();
$defaultUrl     =   $_SERVER['PHP_SELF'];
$querysting     =   "ddlSearchType=$ddlSearchType&txtSearch=$txtSearch&";
$paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
$pageString     =   getnavigation($totalrows);
include_once("lib/pager/pagination.php"); 
$pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}

include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmAffMain.submit();
    }
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
<script language="javascript" type="text/javascript">
    $jqr(document).ready(function (){
        var searchType  = '<?php echo $ddlSearchType;?>';
        var searchVal   = '<?php echo $txtSearch;?>';
        var payments_with_array_with_keys = {'pp': '<?php echo TEXT_PAYPAL;?>', 'wp' : '<?php echo TEXT_WORLDPAY;?>', 'bp' : '<?php echo TEXT_BLUEPAY;?>','cc' : '<?php echo TEXT_CREDIT_CARD;?>','bu' : '<?php echo TEXT_BUSINESS_CHECK;?>','ca' : '<?php echo TEXT_CASIER_CHECK;?>','mo' : '<?php echo TEXT_MONEY_ORDER;?>','wt' : '<?php echo TEXT_WIRE_TRANSFER;?>','pc' : '<?php echo TEXT_PERSONAL_CHECK;?>','yp' : '<?php echo TEXT_YOUR_PAY;?>','gc' : '<?php echo TEXT_GOOGLE_CHECKOUT;?>'};
        $jqr("#payments_with_name").hide();
        if(searchType=="transmode")
        {
                $jqr("#txtSearch").hide();
                $jqr("#payments_with_name").show();
                $jqr.each(payments_with_array_with_keys, function(val, text) {
                        $jqr('#payments_with_name').append( $jqr('<option></option>').val(val).html(text) )
                    });
                    
               $jqr("#payments_with_name").val(searchVal);     
        }
        
     $jqr("#ddlSearchType").change(function(e){
     var paymentVal=$jqr(this).val();
     if(paymentVal=="transmode")
     {  
         $jqr("#txtSearch").hide();
         $jqr("#payments_with_name").show();
       
                    $jqr.each(payments_with_array_with_keys, function(val, text) {
                    $jqr('#payments_with_name').append( $jqr('<option></option>').val(val).html(text) )
                    });

                    e.preventDefault();
         
                }
});   
});   
  </script>
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">
				 <div class="row">
						<div class="col-lg-12">
							<div class="innersubheader2">
								<h3><?php echo HEADING_SWAP_WISH_TRANSACTION_DETAILS; ?></h3>
							</div>
						</div>
				   </div>
					<div class="space">&nbsp;</div>
				   <div class="full-width">
						<div class="col-lg-12">
							<?php include('./includes/account_menu.php'); ?>                          
					  <div class="tabContent tabcontent_wrapper">
						  <div class="table-responsive">
							<table width="100%"  border="0" cellspacing="0" cellpadding="4" class="table table-bordered table-hover">
								<form name="frmAffMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
									<?php
									$message = ($message != '') ? $message : $_SESSION['sessionMsg'];
									unset($_SESSION['sessionMsg']);
	
									if (isset($message) && $message != '') {
										?>
										<tr >
											<td colspan="7" align="center" class="warning"><?php echo $message; ?></td>
										</tr>
									<?php }//end if ?>			
									<tr >
										<td colspan="7" align="center"><?php transaction_search_area(); ?></td>
									</tr>  
									<tr align="center"  class="gray">
										<th width="7%"><?php echo TEXT_SLNO; ?> </th>
										<th width="16%"><?php echo TEXT_TITLE; ?></th>
										<th width="19%"><?php echo TEXT_USERNAME; ?></th>
										<th width="19%"><?php echo TEXT_TRANSACTION_DATE; ?> </th>
										<th width="20%"><?php echo TEXT_TRANSACTION_MODE; ?></th>
										<th width="20%"><?php echo TEXT_STATUS; ?></th>
										<th width="20%"><?php echo TEXT_AMOUNT; ?></th>
									</tr>
									<?php
									if (mysqli_num_rows($rs) > 0) {
										$cnt = 1;
										while ($arr = mysqli_fetch_array($rs)) {
											$paydate = $arr["dTxnDate"];
											$paytype = $arr["vPostType"];
											$amount = abs($arr["nSwapAmount"]);
                                                                                         if(!$amount)
                                                                                           $amount = $swapAmountArray[$arr["nSwapId"]]['amount'];
											$transid = $arr["vTxnId"];
											$status = $arr["vStatus"];
											$trnansmode = "";
											$trnansmode = get_payment_name($arr["vMethod"]);
                                                                                        $userArray = getdbcontents_sql("SELECT vLoginName FROM ". TABLEPREFIX."users WHERE nUserId='".$arr["nUserId"]."'");
											$username = $userArray[0]["vLoginName"];
                                                                                        if($arr["nSwapId"] && !$trnansmode )
                                                                                        {

                                                                                            $swapedItemId = $swapAmountArray[$arr["nSwapId"]]['swapItem'];
                                                                                            $userArray = getdbcontents_sql("SELECT date_format(dTxnDate,'%m/%d/%Y') as 'dTxnDate', date_format(dPostDate,'%m/%d/%Y') as dDate, vTxnId, vMethod, vPostType FROM ". TABLEPREFIX."swap WHERE nSwapId='".$swapedItemId."'");
                                                                                            $trnansmode = get_payment_name($userArray[0]["vMethod"]);
                                                                                            $paydate = $userArray[0]["dTxnDate"];
                                                                                        }
                                                                                        $title = $arr["vTitle"];
											
											?>
											<tr >
												<td align="center"><?php  echo (($page*$limit)+$cnt-$limit);?></td>
												<td><?php echo htmlentities($title); ?></td>
												<td><?php echo htmlentities($username); ?></td>
												<td><?php echo date('m/d/Y', strtotime($paydate)); ?></td>
												<td><?php echo htmlentities($trnansmode); ?></td>
												<td><?php echo htmlentities('Partner Paid'); ?></td>
												<td><?php echo CURRENCY_CODE.htmlentities($amount); ?></td>
											</tr>
											<?php
											$cnt++;
										}//end while
									//}//end if
									?>
									<tr >
										<td colspan="7" align="left" class="navigation">
										<div class="pagination_wrapper">  
                                                                                <div class="left">
                                                                                    <?php echo str_replace('{total_rows}',$totalrows,str_replace('{current_rows}',$pageString,TEXT_LISTING_RESULTS)); ?>
                                                                                </div>
                                                                                <div class="right">
                                                                             <?php
                                                                                    //Pagination code
                                                                                     echo $pg->process();
                                                                             ?>
                                                                           </div>
										</td>
									</tr>
                                                                        
                                                                        <?php } ?>
								</form>
							</table>
						  </div>
					  </div>
					</div>
				<div class="clear"></div>
				</div>				
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
                
<?php require_once("./includes/footer.php"); ?>