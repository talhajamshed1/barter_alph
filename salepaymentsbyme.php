<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include ("./includes/session_check.php");
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/enable_module.php");
include_once('./includes/gpc_map.php');

$txtSearch = $_REQUEST['txtSearch'];
$ddlSearchType = $_REQUEST['ddlSearchType'];
$flag = 2;
if ($_POST["postback"] == "Y") {
    $sql = "Select sd.nSaleId from " . TABLEPREFIX . "saledetails sd  where sd.nSaleId='" . addslashes($_POST["saleid"]) . "' AND sd.nUserId='" . $_SESSION["guserid"] . "' AND sd.dDate='" . addslashes($_POST["dt"]) . "' AND sd.vSaleStatus IN('2','3')  AND sd.vDelivered='N' ";
    $firstqry=$sql;
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        $sql = "Update " . TABLEPREFIX . "saledetails set vDelivered='Y' where  nSaleId='" . addslashes($_POST["saleid"]) . "' AND nUserId='" . $_SESSION["guserid"] . "' AND dDate='" . addslashes($_POST["dt"]) . "'";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $flag = 1;
    }//end if
    else {
        $flag = 0;
    }//end else
}//end if


$qryopt = "";
$txtSearch  =   trim($txtSearch);
if ($txtSearch != "") {
    if ($ddlSearchType == "transno") {
        $qryopt .= "  and sd.vTxnId like '" . addslashes($txtSearch) . "%'";
    }//end if
    else if ($ddlSearchType == "amount") {
        $qryopt .= "  and sd.nAmount like '" . addslashes($txtSearch) . "%'";
    }//end else if
    else if ($ddlSearchType == "transmode") {
        //$va_method = strtolower($txtSearch);
        
        //$txtSearchNew = get_payment_name($txtSearch);
        $qryopt .= " and sd.vMethod='$txtSearch' ";
    }//end else if
    else if ($ddlSearchType == "date") {
        $date = $txtSearch;
        //$arr = split("/", $date);
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
        $qryopt .= "  and sd.dTxnDate  like '" . addslashes($newdate) . "%'";
    }//end else if
}//end if
//checking escrow status
if (DisplayLookUp('Enable Escrow') == 'Yes') {
    $SaleStatus = '';
}//end if
else {
    $SaleStatus = " OR sd.vSaleStatus ='4'";
}//end esle

$sqlsale = "SELECT s.vTitle, sd.nAmount,sd.nSaleId,sd.nUserId,sd.dDate as 'dDate2',sd.vDelivered,date_format(sd.dTxnDate,'%m/%d/%Y') as dTxnDate,  date_format(sd.dDate ,'%m/%d/%Y') as dDate, sd.vTxnId, sd.vMethod  , u.vLoginName,sd.nQuantity ";
$sqlsale .= " FROM " . TABLEPREFIX . "sale s  INNER JOIN " . TABLEPREFIX . "saledetails sd ON s.nSaleId = sd.nSaleId LEFT JOIN  " . TABLEPREFIX . "users u ON u.nUserId  = s.nUserId ";
$sqlsale .= " WHERE ";
$sqlsale .= " sd.nUserId  = '" . $_SESSION["guserid"] . "' AND (sd.vSaleStatus ='2'  OR sd.vSaleStatus ='3' " . $SaleStatus . ") ";
$sqlsale .= $qryopt;
$sqlsale .= "  order by sd.dDate DESC ";
$totalrows = mysqli_num_rows(mysqli_query($conn, $sqlsale));

$sqlsale=dopaging($sqlsale,'',PAGINATION_LIMIT);
$rssale = mysqli_query($conn, $sqlsale) or die(mysqli_error($conn));
$numRecords = mysqli_num_rows($rssale);

//PAGINATION_LIMIT
if($numRecords>0) {
    
        $pagenumber     =   getCurrentPageNum();
        $defaultUrl     =   $_SERVER['PHP_SELF'];
        $querysting     =   "&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&";
        $paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
        $pageString     =   getnavigation($totalrows,PAGINATION_LIMIT);
        include_once("lib/pager/pagination.php"); 
        $pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}
/*$navigate = pageBrowser($totalrows, 5, 5, "&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
$sqlsale = $sqlsale . $navigate[0];
$rssale = mysqli_query($conn, $sqlsale) or die(mysqli_error($conn));*/




include_once('./includes/title.php');

?>
<script language="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmAffMain.submit();
    }

    function clickDeliver(a,b){

        if(confirm('<?php echo DELIVERY_STATUS_CHANGE;?>'))
        {
            document.frmAffMain.saleid.value=a;
            document.frmAffMain.dt.value=b;
            document.frmAffMain.postback.value='Y';
            document.frmAffMain.method="post";
            document.frmAffMain.submit();
        }
       
    }

</script>
<body onLoad="timersOne();">
    <?php
   /* if ($flag == 1) {
        echo("<script>alert('".MESSAGE_DELIVERY_STATUS_UPDATED."');</script>");
    }//end if
    else if ($flag == 0) {
        echo("<script>alert('".ERROR_MISMATCH_ENTRY."');</script>");
    }//end else*/
    ?>
    <?php include_once('./includes/top_header.php');
    
    ?>
    
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
         //var ArrayList  = '<?php echo json_encode($paymentlistarrray); ?>';
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
                    <div class="full-width">
                        <div class="innersubheader2">
                            <div class="col-lg-12">
                            <h3><?php echo HEADING_SALES_TRANSACTION_DETAILS; ?></h3>
                            </div>
                        </div>
                        <div class="space">&nbsp;</div>
                    </div>
                    <?php if ($flag == 1) { ?>
                    <div class="success_msg">
                        <span class="glyphicon glyphicon-ok-circle"></span>
                        <?php echo MESSAGE_DELIVERY_STATUS_UPDATED;?>
                    </div>
                    <?php } 
                    if ($flag == 0) {?>
                    <div class="error_msg">
                        <span class="glyphicon glyphicon-remove-circle"></span>
                        <?php echo ERROR_MISMATCH_ENTRY;?>
                </div>
                    <?php } ?>          
                    <div class="space">&nbsp;</div>
                    
                    <div class="full-width">
                    	<div class="col-lg-12">
                        	<div class="table-responsive">
                            <table width="100%"  border="0" cellspacing="0" cellpadding="table">
                                            <tr>
                                                <td align="left" valign="top"><?php include('./includes/account_menu.php'); ?>
                                                    
                                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td class="tabContent tabcontent_wrapper">
                                                                    <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="table table-bordered">
                                                                    <form name="frmAffMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>" onSubmit="return Validate();">

                                                                        <?php
                                                                        $message = ($message != '') ? $message : $_SESSION['sessionMsg'];
                                                                        unset($_SESSION['sessionMsg']);

                                                                        if (isset($message) && $message != '') {
                                                                            ?>
                                                                        <tr >
                                                                            <td colspan="8" align="center" class="warning"><?php echo $message; ?></td>
                                                                        </tr>
                                                                            <?php }//end if ?>
                                                                        <tr ><input type="hidden" name="saleid" id="saleid" value="">
                                                                        <input type="hidden" name="userid" id="userid" value="">
                                                                        <input type="hidden" name="dt" id="dt" value="">
                                                                        <input type="hidden" name="postback" id="postback" value="">
                                                                        <td colspan="8" align="center" ><?php transaction_search_area(); ?></td>
                                                                        </tr>  
                                                                        <tr align="center"  class="gray">
                                                                            <th width="5%" valign="top"><?php echo TEXT_SLNO; ?> </th>
                                                                            <th width="25%" valign="top"><?php echo TEXT_TITLE; ?></th>
                                                                            <th width="20%" valign="top"><?php echo TEXT_USERNAME; ?></th>
                                                                            <th width="10%" valign="top"><?php echo TEXT_TRANSACTION_DATE; ?> </th>
                                                                            <th width="5%" valign="top"><?php echo TEXT_TRANSACTION_NUMBER; ?></th>
                                                                            <th width="10%" valign="top"><?php echo TEXT_TRANSACTION_MODE; ?></th>
                                                                            <th width="15%" valign="top"><?php echo TEXT_AMOUNT; ?></th>
                                                                            <th width="10%" valign="top"><?php echo TEXT_DELIVERED; ?></th>
                                                                        </tr>
                                                                        <?php
                                                                        if (mysqli_num_rows($rssale) > 0) {
                                                                            switch ($_GET['begin']) {
                                                                                case "":
                                                                                        $cnt = 1;
                                                                                        break;

                                                                                default:
                                                                                        $cnt = $_GET['begin'] + 1;
                                                                                        break;
                                                                        }//end switch
                                                                            while ($arr = mysqli_fetch_array($rssale)) {
                                                                                $paydate = $arr["dTxnDate"];
                                                                                $amount = $arr["nAmount"];
                                                                                $transid = $arr["vTxnId"];
                                                                                $trnansmode = "";
                                                                                $trnansmode = get_payment_name($arr["vMethod"]);

                                                                                $title = $arr["vTitle"];
                                                                                $username = $arr["vLoginName"];
                                                                                ?>
                                                                        <tr >
                                                                            <td align="center"><?php  echo (($page*$limit)+$cnt-$limit);?></td>
                                                                            <td><a href="<?php echo $rootserver; ?>/buynext.php?saleid=<?php echo $arr["nSaleId"]; ?>&tot=<?php echo $arr["nAmount"]; ?>&reqd=<?php echo $arr["nQuantity"]; ?>&dt=<?php echo urlencode($arr["dDate2"]); ?>&"><?php echo htmlentities($title); ?></a></td>
                                                                            <td><a href="<?php echo $rootserver; ?>/buynext.php?saleid=<?php echo $arr["nSaleId"]; ?>&tot=<?php echo $arr["nAmount"]; ?>&reqd=<?php echo $arr["nQuantity"]; ?>&dt=<?php echo urlencode($arr["dDate2"]); ?>&"><?php echo htmlentities($username); ?></a></td>
                                                                            <td><a href="<?php echo $rootserver; ?>/buynext.php?saleid=<?php echo $arr["nSaleId"]; ?>&tot=<?php echo $arr["nAmount"]; ?>&reqd=<?php echo $arr["nQuantity"]; ?>&dt=<?php echo urlencode($arr["dDate2"]); ?>&"><?php echo date('m/d/Y', strtotime($paydate)); ?></a></td>
                                                                            <td><a href="<?php echo $rootserver; ?>/buynext.php?saleid=<?php echo $arr["nSaleId"]; ?>&tot=<?php echo $arr["nAmount"]; ?>&reqd=<?php echo $arr["nQuantity"]; ?>&dt=<?php echo urlencode($arr["dDate2"]); ?>&"><?php if($transid) echo htmlentities($transid);else echo "--"; ?></a></td>
                                                                            <td><a href="<?php echo $rootserver; ?>/buynext.php?saleid=<?php echo $arr["nSaleId"]; ?>&tot=<?php echo $arr["nAmount"]; ?>&reqd=<?php echo $arr["nQuantity"]; ?>&dt=<?php echo urlencode($arr["dDate2"]); ?>&"><?php echo htmlentities($trnansmode); ?></a></td>
                                                                            <td><a href="<?php echo $rootserver; ?>/buynext.php?saleid=<?php echo $arr["nSaleId"]; ?>&tot=<?php echo $arr["nAmount"]; ?>&reqd=<?php echo $arr["nQuantity"]; ?>&dt=<?php echo urlencode($arr["dDate2"]); ?>&"><?php echo CURRENCY_CODE.htmlentities($amount); ?></a></td>
                                                                            <td><?php echo (($arr["vDelivered"] == "N") ? "<a href=\"javascript:clickDeliver('" . $arr["nSaleId"] . "','" . htmlentities($arr["dDate2"]) . "');\">".TEXT_NO."</a>" : TEXT_YES); ?></td>
                                                                        </tr>
                                                                                <?php
                                                                                $cnt++;
                                                                            }//end while
                                                                        
                                                                        ?>
                                                                        <tr >
                                                                            <td colspan="8" align="left" class="navigation">
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
                        
                </div>      </td>
                                                                        </tr>
                                                                        <?php } ?>                                        
                                                                    </form>
                                                                    </table>

                                                                </td></tr>
                                                        </table></td>
                                            </tr>
                                        </table>
                            </div>
                        </div>
                    </div>
                	<div class="full-width subbanner">
						<?php include('./includes/sub_banners.php'); ?>
                    </div>               
				</div>
			</div>
		</div>
	</div>
<?php require_once("./includes/footer.php"); ?>