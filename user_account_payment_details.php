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
$userid = $_SESSION["guserid"];
$username = "";

if ($_POST["username"] != "") {
    $username = $_POST["username"];
}//end if
else if ($_GET["username"] != "") {
    $username = $_GET["username"];
}//end else if

if ($_POST["userid"] != "") {
    $userid = $_POST["userid"];
}//end if
else if ($_GET["userid"] != "") {
    $userid = $_GET["userid"];
}//end else if

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

if ($txtSearch != "") {
    if ($ddlSearchType == "transmode") {
        $va_method = "";
        //$txtSearchNew = get_payment_name($txtSearch);
        $qryopt .= " and vMethod='$txtSearch' ";
    }//end if
    else if ($ddlSearchType == "transno") {
        $qryopt .= "  and vModeNo like '" . addslashes($txtSearch) . "%'";
    }//end else if
    else if ($ddlSearchType == "amount") {
        $qryopt .= "  and nAmount like '" . addslashes($txtSearch) . "%'";
    }//end else if
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
        $qryopt .= "  and dDate  like '" . addslashes($newdate) . "%'";
    }//end else if
}//end if

$sql = "SELECT * FROM " . TABLEPREFIX . "cashtxn  WHERE nUserId  = '" . addslashes($userid) . "' " . $qryopt . "  order by dDate DESC ";
$sess_back = "salepaymentsbyme.php?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch;

$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));
$sql=dopaging($sql,'',PAGINATION_LIMIT);
//$sql = $sql . $navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$numRecords = mysqli_num_rows($rs);
  if($numRecords>0) {
$pagenumber     =   getCurrentPageNum();
$defaultUrl     =   $_SERVER['PHP_SELF'];
$querysting     =   "ddlSearchType=$ddlSearchType&txtSearch=$txtSearch&";
$paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
$pageString     =   getnavigation($totalrows);
include_once("lib/pager/pagination.php"); 
$pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}

/*$navigate = pageBrowser($totalrows, 5, 5, "&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&userid=$userid&username=" . urlencode($username) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql . $navigate[0];*/

//echo $sql;exit;
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$message = ($message != '') ? $message : $_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);

include_once('./includes/title.php');
?>
<script LANGUAGE="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmAccMain.submit();
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
								<h3><?php echo HEADING_TRANSACTION_SUMMARY; ?></h3>
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
								<form name="frmAccMain" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
									<tr >
										<td colspan="6" align="center" class="gray"><b><?php echo HEADING_AMOUNT_ACTUALLY_TRANSFERED; ?></b></td>
									</tr>
									<?php
									if (isset($message) && $message != '') {
										?>
										<tr >
											<td colspan="5" align="center" class="warning"><?php echo $message; ?></td>
										</tr>
									<?php }//end if ?>	
									<tr ><input type="hidden" name="username" value="<? echo htmlentities($username); ?>" />
									<input type="hidden" name="userid" value="<? echo $userid; ?>" />
									<td colspan="6" align="center"><?php transaction_search_area(); ?></td>
									</tr>  
									<tr align="center"  class="gray">
										<th width="12%"><?php echo TEXT_SLNO; ?> </th>
										<th width="19%" align="left"><?php echo TEXT_TRANSACTION_NUMBER; ?></th>
										<th width="19%" align="left"><?php echo TEXT_TRANSACTION_DATE; ?> </th>
										<th width="25%" align="left"><?php echo TEXT_TRANSACTION_MODE; ?></th>
										<th width="25%" align="left"><?php echo TEXT_AMOUNT; ?></th>
									</tr>
									<?php
									if (mysqli_num_rows($rs) > 0) {
										$cnt = 1;
										while ($arr = mysqli_fetch_array($rs)) {
											?>
											<tr >
												<td align="center"><?php  echo (($page*$limit)+$cnt-$limit);?></td>
												<td class="maintext"><?php echo "<a href='user_transdetails.php?transid=" . $arr["nCashTxnId"] . "&userid=" . $userid . "&username=" . urlencode($username) . "'>" . $arr["nCashTxnId"] . "</a>"; ?></td>
												<td><?php
												if ($arr["dDate"] != '0000-00-00') {
													echo "<a href='user_transdetails.php?transid=" . $arr["nCashTxnId"] . "&userid=" . $userid . "&username=" . urlencode($username) . "'>" . date('m/d/Y', strtotime($arr["dDate"])) . "</a>";
												}//end if
												?></td>
												<td><?php echo "<a href='user_transdetails.php?transid=" . $arr["nCashTxnId"] . "&userid=" . $userid . "&username=" . urlencode($username) . "'>" . $arr["vMode"] . "</a>"; ?></td>
												<td><?php echo "<a href='user_transdetails.php?transid=" . $arr["nCashTxnId"] . "&userid=" . $userid . "&username=" . urlencode($username) . "'>" . CURRENCY_CODE.$arr["nAmount"] . "</a>"; ?></td>
											</tr>
											<?php
											$cnt++;
										}//end while
									//end if
									?>
									<tr >
										<td colspan="6" align="left" class="navigation">
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