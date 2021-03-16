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

$txtSearch = $_REQUEST['txtSearch'];
$ddlSearchType = $_REQUEST['ddlSearchType'];
//checking admin allow transfer between users
if (DisplayLookUp('PointTransfer') != '1') {
    header('location:my_points.php');
    exit();
}//end if

$qryopt = "";
if ($txtSearch != "") {
    if ($ddlSearchType == "date") {
        $qryopt .= "  and ph.dDate like '" . change_date_format(addslashes($txtSearch),'mmddyy-to-mysql') . "%'";
    }//end if
    else if ($ddlSearchType == "amount") {
        $qryopt .= "  and ph.nPoints like '" . addslashes($txtSearch) . "%'";
    }//end else if
}//end if

$sqlSent = "SELECT u.vLoginName,ph.nPoints,date_format(ph.dDate,'%m/%d/%Y') as sentDate ";
$sqlSent .= " FROM " . TABLEPREFIX . "pointhistory ph LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId  = ph.nSendTo ";
$sqlSent .= " WHERE ph.nSendBy  = '" . $_SESSION["guserid"] . "' ";
$sqlSent .= $qryopt;
$sqlSent .= "  order by ph.dDate DESC ";
$totalrows = mysqli_num_rows(mysqli_query($conn, $sqlSent));

$navigate = pageBrowser($totalrows, 5, 5, "&ddlSearchType=$ddlSearchType&txtSearch=" . urlencode($txtSearch) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
$sqlSent = $sqlSent . $navigate[0];
$rssale = mysqli_query($conn, $sqlSent) or die(mysqli_error($conn));

include_once('./includes/title.php');
?>
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
                <h3><?php echo str_replace('{point_name}',POINT_NAME,HEADING_SENT_POINTS_HISTORY); ?></h3>
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
                    <td class="tabContent tabcontent_wrapper" >
                    	<div class="table-responsive">
                        	<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
                        <form name="frmSent" method="post" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
                          <?php
                                                                        $message = ($message != '') ? $message : $_SESSION['sessionMsg'];
                                                                        unset($_SESSION['sessionMsg']);

                                                                        if (isset($message) && $message != '') {
                                                                            ?>
                          <tr >
                            <td colspan="4" align="center" class="warning"><?php echo $message; ?></td>
                          </tr>
                          <?php }//end if?>
                          <tr>
                            <td colspan="4" align="center">
                            <div class="full-width search_container_listing search_table">
                              <div class="col-lg-5"></div>
                              <div class="col-lg-3">
                              	<select name="ddlSearchType" class="form-control">
                                        <option value="date" <?php if ($ddlSearchType == "date" || $ddlSearchType == "") {
                                                                                                    echo("selected");
                                                                                                } ?>><?php echo TEXT_SENT_DATE; ?> (<?php echo 				TEXT_MM_DD_YYYY; ?>)</option>
                                        <option value="amount"  <?php if ($ddlSearchType == "amount" || $ddlSearchType == "") {
                                                                                                    echo("selected");
                                                                                                } ?>><?php echo POINT_NAME; ?></option>
                                      </select>
                                      
                               
                              </div>
                              <div class="col-lg-3">
                               <input type="text" name="txtSearch" size="20" maxlength="50" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }" class="form-control ">
                              </div>
                              <div class="col-lg-1"> 
                              <a href="javascript:clickSearch();" class="login_btn comm_btn_orng_tileeffect2 actionbtn "><?php echo BUTTON_GO; ?></a>
                              </div>
                              <div class="clear"></div>
                            </div>
                                                                            
                            
                              </td>
                          </tr>
                          <tr align="center"  class="">
                            <th width="8%"><?php echo TEXT_SLNO; ?> </th>
                            <th width="34%"><?php echo TEXT_SENT_TO; ?></th>
                            <th width="29%"><?php echo POINT_NAME; ?></th>
                            <th width="29%"> <?php echo TEXT_SENT_DATE; ?></th>
                          </tr>
                          <?php
                                                                        if (mysqli_num_rows($rssale) > 0) {
                                                                            $cnt = 1;
                                                                            while ($arr = mysqli_fetch_array($rssale)) {
                                                                                ?>
                          <tr >
                            <td align="center"><?php echo $cnt; ?></td>
                            <td><?php echo htmlentities($arr["vLoginName"]); ?></td>
                            <td><?php echo htmlentities($arr["nPoints"]); ?></td>
                            <td><?php echo date('m/d/Y', strtotime($arr["sentDate"])); ?></td>
                          </tr>
                          <?php
                                                                                    $cnt++;
                                                                                }//end while
                                                                            }//end if
                                                                            ?>
                          <tr >
                            <td colspan="4" align="left" class="navigation"><div>
                                <Div class="left"><?php echo str_replace('{total_rows}', $totalrows, str_replace('{current_rows}', $navigate[1], TEXT_LISTING_RESULTS)); ?></Div>
                                <Div class="right"><?php echo($navigate[2]); ?></Div>
                                <div class="clear"></div>
                              </div></td>
                          </tr>
                        </form>
                      </table>
                        </div>
                    </td>
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