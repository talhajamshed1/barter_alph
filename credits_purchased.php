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
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
include ("./includes/session_check.php");

include_once('./includes/gpc_map.php');

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
    if ($ddlSearchType == "date") {
        $qryopt .= "  and dDate like '" . change_date_format(addslashes($txtSearch),'mmddyy-to-mysql') . "%'";
    }//end if
    else if ($ddlSearchType == "number") {
        $qryopt .= "  and vTxnId like '" . addslashes($txtSearch) . "%'";
    }//end else if
}//end if

$sqlSent = "SELECT nId,nUserId,nAmount,nPoints,date_format(dDate,'%m/%d/%Y') as sentDate,vTxnId,vMethod,vStatus,vReferenceNo ";
$sqlSent .= " FROM " . TABLEPREFIX . "creditpayments";
$sqlSent .= " WHERE nUserId  = '" . $_SESSION["guserid"] . "' ";
$sqlSent .= $qryopt;
$sqlSent .= "  order by dDate DESC ";
$totalrows = mysqli_num_rows(mysqli_query($conn, $sqlSent));

$navigate = pageBrowser($totalrows, 5, 5, "&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
$sqlSent = $sqlSent . $navigate[0];
$rssale = mysqli_query($conn, $sqlSent) or die(mysqli_error($conn));
include_once('./includes/title.php');
?>
<script type="text/javascript" src="js/dhtmlwindow.js"></script>
<script language="javascript" type="text/javascript">
    function clickSearch()
    {
        document.frmSent.submit();
    }
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
  <div class="homepage_contentsec">
    <div class="container">
      <div class="row">
        <div class="col-lg-3">
          <?php include_once ("./includes/usermenu.php"); ?>
        </div>
        <div class="col-lg-9">
          <div class="full-width">
            <div class="innersubheader2 row">
              <div class="col-lg-12">
                <h3><?php echo str_replace('{point_name}',POINT_NAME,HEADING_POINTS_PURCHASE_HISTORY); ?></h3>
              </div>
            </div>
            <div class="space"></div>
          </div>
          <div class="full-width">
            <div class="col-lg-12">
              <div class="row">
              <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                  <td align="left" valign="top"><?php include('./includes/points_menu.php'); ?>
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td  class="tabContent tabcontent_wrapper">
                    <div class="table-responsive">
                        <table border="1" cellspacing="0" cellpadding="0" class="table table-bordered">
                          <form name="frmSent" method="post" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
                            <?php
                                                                        $message = ($message != '') ? $message : $_SESSION['sessionMsg'];
                                                                        unset($_SESSION['sessionMsg']);

                                                                        if (isset($message) && $message != '') {
                                                                            ?>
                            <tr >
                              <td colspan="7" align="center" class="warning"><?php echo $message; ?></td>
                            </tr>
                            <?php }//end if?>
                            <tr >
                              <td colspan="7" align="center">
									<div class=" search_container_listing search_table">
                                      <div class="col-lg-5"></div>
                                      <div class="col-lg-3">
                                      	<select name="ddlSearchType" class="form-control">
                                        <option value="date" <?php if ($ddlSearchType == "date" || $ddlSearchType == "") {
                                                                                                echo("selected");
                                                                                                } ?>><?php echo TEXT_TRANSACTION_DATE; ?>(<?php echo TEXT_MM_DD_YYYY; ?>)</option>
                                        <option value="number"  <?php if ($ddlSearchType == "number" || $ddlSearchType == "") {
                                                                                                echo("selected");
                                                                                                } ?>><?php echo TEXT_TRANSACTION_NUMBER; ?></option>
                                      </select>
                                    
                                      </div>
                                      <div class="col-lg-3">
                                      	<input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="form-control">
                                      </div>
                                      <div class="col-lg-1"> 
                                      	<a href="javascript:clickSearch();" class="login_btn comm_btn_orng_tileeffect2 link_style3"><?php echo BUTTON_GO; ?></a>
                                      </div>
                                      <div class="clear"></div>
                                    </div>
									
									
									
                                      
                                    
                                     </td>
                            </tr>
                            <tr align="center"  class="">
                              <th width="6%"><?php echo TEXT_SLNO; ?> </th>
                              <th width="20%"><?php echo TEXT_TRANSACTION_DATE; ?></th>
                              <th width="18%"><?php echo TEXT_TRANSACTION_NUMBER; ?></th>
                              <th width="11%"><?php echo TEXT_MODE; ?></th>
                              <th width="10%"><?php echo POINT_NAME; ?></th>
                              <th width="12%"><?php echo TEXT_AMOUNT; ?></th>
                              <th width="14%"><?php echo TEXT_STATUS; ?></th>
                            </tr>
                            <?php
                                                                        if (mysqli_num_rows($rssale) > 0) {
                                                                            
                                                                            $cnt=(isset($_GET['begin']) && $_GET['begin']!='') ? $_GET['begin'] + 1 : 1;

                                                                            while ($arr = mysqli_fetch_array($rssale)) {
                                                                                $trnansmode = get_payment_name($arr["vMethod"]);

                                                                                //checking status
                                                                                switch ($arr["vStatus"]) {
                                                                                    case "P":
                                                                                        $shwStatus = TEXT_PENDING_TO_UPDATE_ACCOUNT;
                                                                                        break;

                                                                                    case "A":
                                                                                        $shwStatus = TEXT_ADDED_TO_ACCOUNT;
                                                                                        break;
                                                                                }//end switch

                                                                                $showLink = "<a href=\"#\" onClick=\"divwin=dhtmlwindow.open('divbox" . $arr['nId'] . "', 'div', 'somediv" . $arr['nId'] . "', '".HEADING_TRANSACTION_DETAILS."', 'width=550px,height=170px,left=550px,top=190px,resize=1,scrolling=1'); return false\">";
        $closeLink = '</a>';
        ?>
                            <tr >
                              <td align="center"><?php echo $cnt; ?></td>
                              <td><?php echo $showLink . date('m/d/Y', strtotime($arr["sentDate"])) . $closeLink; ?></td>
                              <td><?php echo $showLink . htmlentities($arr["vTxnId"]) . $closeLink; ?></td>
                              <td><?php echo $showLink . htmlentities($trnansmode) . $closeLink; ?></td>
                              <td><?php echo $showLink . htmlentities($arr["nPoints"]) . $closeLink; ?></td>
                              <td><?php echo $showLink . CURRENCY_CODE.htmlentities($arr["nAmount"]) . $closeLink; ?></td>
                              <td><?php echo $showLink . htmlentities($shwStatus) . $closeLink; ?>
                                <div id="somediv<?php echo $arr['nId']; ?>" style="display:none">
                                  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td bgcolor="#ffffff"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                          <tr align="center" >
                                            <td width="6%" align="left" class="gray"><?php echo TEXT_TRANSACTION_DATE; ?></td>
                                            <td width="6%" align="left"><?php echo date('m/d/Y', strtotime($arr["sentDate"])); ?></td>
                                          </tr>
                                          <tr >
                                            <td align="left" class="gray"><?php echo TEXT_AMOUNT; ?></td>
                                            <td align="left"><?php echo CURRENCY_CODE.htmlentities($arr["nAmount"]); ?></td>
                                          </tr>
                                          <tr >
                                            <td align="left" class="gray"><?php echo TEXT_MODE; ?></td>
                                            <td align="left"><?php echo htmlentities($trnansmode); ?></td>
                                          </tr>
                                          <!--<tr >
                                                                                                        <td align="left" class="gray"><?php echo TEXT_REFERENCE_NUMBER; ?></td>
                                                                                                        <td align="left"><?php echo htmlentities($arr["vReferenceNo"]); ?></td>
                                                                                                    </tr>-->
                                          <tr >
                                            <td align="left" class="gray"><?php echo TEXT_TRANSACTION_NUMBER; ?></td>
                                            <td align="left"><?php echo htmlentities($arr["vTxnId"]); ?></td>
                                          </tr>
                                          <tr >
                                            <td align="left" class="gray"><?php echo POINT_NAME; ?></td>
                                            <td align="left"><?php echo htmlentities($arr["nPoints"]); ?></td>
                                          </tr>
                                        </table></td>
                                    </tr>
                                  </table>
                                </div></td>
                            </tr>
                            <?php
                                                                                $cnt++;
                                                                            }//end while
}//end if
?>
                            <tr >
                              <td colspan="7" align="left" class="navigation"><div>
                                <div class="left"><?php echo str_replace('{total_rows}', $totalrows, str_replace('{current_rows}', $navigate[1], TEXT_LISTING_RESULTS)); ?></div>
                                <div class="right">
                                <?php echo($navigate[2]); ?>
                                <div>
                                  <div class="clear"></div>
                                </div></td>
                            </tr>
                          </form>
                        </table>
                      </div></td>
                  </tr>
                </table>
                  </td>
                
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