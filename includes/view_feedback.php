<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
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
function func_feed_detailed($uid = 0, $fid = 0) {
    global $conn;
    $var_uname = ($_GET["uname"] != "") ? $_GET["uname"] : $_POST["uname"];
    ?>
    <div class="full-width ">
                    	<div class="innersubheader">
                        	
                        
            <h4><?php
    if ($var_uname != "") {
        echo "<i> ".HEADING_SALE_LIST_FROM. ' ' . htmlentities($var_uname) . "</i>";
    }//end if
    else {
        echo HEADING_FEEDBACK_DETAILS;
    }//end else
    ?></h4>
       </div>
                    </div>
   
    
    
   
           
    	
                    <form name="frmSale" id="frmSale" ACTION="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                     <div class="full-width search_container_listing">
    <?php
    $txtSearch = "";
    $cmbSearchType = "";
    $var_rf = "";
    $var_no = "";

    if ($_GET["txtSearch"] != "") {
        $txtSearch = $_GET["txtSearch"];
        $cmbSearchType = $_GET["cmbSearchType"];
    }//end if
    else if ($_POST["txtSearch"] != "") {
        $txtSearch = $_POST["txtSearch"];
        $cmbSearchType = $_POST["cmbSearchType"];
    }//end else

    if ($_GET["rf"] == "sid" || $_POST["rf"] == "sid") {
        $var_rf = "sid";
        $var_no = (strlen($_GET["no"]) > 0) ? $_GET["no"] : $_POST["no"];
    }//end if

    $qryopt = "";
    if ($txtSearch != "") {
        if ($cmbSearchType == "user") {
            $qryopt .= "  AND  (u.vFirstName like '" . addslashes($txtSearch) . "%' || u.vLoginName like '" . addslashes($txtSearch) . "%' || u.vLastName like '" . addslashes($txtSearch) . "%'  ) ";
        }//end if 
        else if ($cmbSearchType == "title") {
            $qryopt .= " AND f.vTitle like '" . addslashes($txtSearch) . "%'";
        }//ens else if
        else if ($cmbSearchType == "date") {
            $qryopt .= " AND date_format(dPostDate,'%m/%d/%Y') like '" . addslashes($txtSearch) . "%'";
        }//end else if
    }//end if


    $targetfile = "";
    $detailfile = "";

    $targetfile = "viewfeedbacks.php";
    $detailfile = "viewfeedbackdesc.php";

    $sql = "select f.nFBId ,f.nUserId ,f.vTitle ,f.nUserFBId ,date_format(f.dPostDate,'%m/%d/%Y') as dPostDate,u.vLoginName,f.vStatus
				 from " . TABLEPREFIX . "userfeedback as f," . TABLEPREFIX . "users as u where f.nUserId='" . $_SESSION["guserid"] . "' and f.nUserFBId=u.nUserId ";
    $sql .= $qryopt . " ORDER BY f.dPostDate DESC ";
    foreach ($_GET as $key => $value) {
        $$key = $value;
    }//end foreach
    if (!isset($begin) || $begin == "") {
        $begin = 0;
    }//end if


    $sess_back = $targetfile . "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;
    $_SESSION["backurl_feed_usr"] = $sess_back;
//get the total amount of rows returned
    $totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

    $sql=dopaging($sql,'',PAGINATION_LIMIT);
    
    /*
      Call the function:

      I've used the global $_GET array as an example for people
      running php with register_globals turned 'off' :)
     */

    //$navigate = pageBrowser($totalrows, 10, 10, "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
    //$sql = $sql . $navigate[0];
    $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $numRecords = mysqli_num_rows($rs);
    
    if($numRecords>0) {
    
$pagenumber     =   getCurrentPageNum();
$defaultUrl     =   $_SERVER['PHP_SELF'];
$querysting     =   "&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&";
$paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
$pageString     =   getnavigation($totalrows);
include_once("lib/pager/pagination.php"); 
$pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}

    $message = ($message != '') ? $message : $_SESSION['sessionMsg'];
    unset($_SESSION['sessionMsg']);

    if (isset($message) && $message != '') {
		
        ?>
                           
                                <div class="warning"><?php echo $message; ?></div>
                           
                            
                            
        	
    
                        <?php }//end if?>			
                        <input name="postback" type="hidden" id="postback">
                        <input NAME="rf" TYPE="hidden" id="rf" VALUE="<?php echo  $var_rf ?>">
                        <input NAME="no" TYPE="hidden" id="no" VALUE="<?php echo  $var_no ?>">
                        <input name="uname" TYPE="hidden" id="uname" VALUE="<?php echo  htmlentities($var_uname) ?>">
						<div class="col-lg-4"></div>
                       <div class="viewfeedback-form">
                       
                                       
                                        <select name="cmbSearchType" class="form-control">
                                            <option value="user"  <?php if ($cmbSearchType == "user" || $cmbSearchType == "") {
                                                    echo("selected");
                                                } ?>><?php echo TEXT_USERNAME; ?></option>
                                                                        <option value="title" <?php if ($cmbSearchType == "title") {
                                                    echo("selected");
                                                } ?>><?php echo TEXT_TITLE; ?></option>
                                                                        <option value="date" <?php if ($cmbSearchType == "date") {
                                                    echo("selected");
                                                } ?>><?php echo TEXT_DATE; ?></option>
                                        </select>
                                         
                                        <input type="text" name="txtSearch"  class="form-control" value="<?php echo(htmlentities($txtSearch)); ?>"  onKeyPress="if(window.event.keyCode == '13'){ return false; }"> 
                                                                           
                                        <a href="javascript:document.frmSale.submit();" class="login_btn comm_btn_orng_tileeffect2 link_style3">
                                         <?php echo TEXT_SEARCH; ?>
                                        </a></div>
                                       	<div class="clear"></div>
                                       </div>
                                       
                                        
                                        
                                   <div class="table-responsive">
                                   <table class="table table-bordered">
                     
                        <tr align="center"  class="gray">
                            <th width="7%"><?php echo TEXT_SLNO; ?></th>
                            <th width="16%"><?php echo TEXT_USERNAME ?></th>
                            <th width="19%"><?php echo TEXT_TITLE; ?></th>
                            <th width="19%"><?php echo TEXT_DATE; ?></th>
                            <th width="19%"><?php echo TEXT_STATUS ?></th>
                        </tr>
                        <?php
                        if (mysqli_num_rows($rs) > 0) {
                            $cnt = 1;
                            while ($arr = mysqli_fetch_array($rs)) { 
                                //check status
                                switch ($arr['vStatus']) {
                                    case "S":
                                        $showStatus = TEXT_SATISFIED;
                                        break;

                                    case "D":
                                        $showStatus = TEXT_DISSATISFIED;
                                        break;

                                    case "N":
                                        $showStatus = TEXT_NEUTRAL;
                                        break;
                                }//end switch
                                ?>
                                <tr >
                                    <td align="center"><?php echo $cnt; ?></td>
                                    <td align="center"><?php echo "<a href='" . $detailfile . "?nFBid=" . $arr["nFBId"] . "&source=sa'>" . htmlentities($arr["vLoginName"]) . "</a>"; ?></td>
                                    <td class="maintext" align="center"><?php echo "<a href='" . $detailfile . "?nFBid=" . $arr["nFBId"] . "&source=sa'>" . stripslashes(htmlentities($arr["vTitle"])) . "</a>"; ?></td>
                                    <td align="center"><?php echo "<a href='" . $detailfile . "?nFBid=" . $arr["nFBId"] . "&source=sa'>" . date('m/d/Y', strtotime($arr["dPostDate"])) . "</a>"; ?></td>
                                    <td align="center"><?php echo htmlentities($showStatus); ?></td>
                                </tr>
                                <?php
                                $cnt++;
                            }//end while
                    ?>        
                       
                        <tr >
                            <td colspan="5" align="left">
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
                        
                         </div> 			
			 </td>
                        </tr>
                 <?php       
                    }//end if 
                    else { 
                  ?>   
                        <tr><td colspan="5" style="text-align: center;padding:20px;"><?php echo MESSAGE_SORRY_NO_RECORDS;?></td></tr>
                    <?php } ?>    
                                   </table>
                                   
                                   </div>
                    </form>
               
            
<?php }

//end function ?>				  