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
function func_swap_list($uid = 0, $fid = 0) { 
  	global $numRecordsPage,$numPageLinks,$conn;

  	$recStart = $_GET['begin'];
	if($_GET['page']!='swaplist')
		$_GET['begin'] = $_SESSION['pagenav']['swaplist'];
	else
		$_SESSION['pagenav']['swaplist'] = $_GET['begin'];

  $now = mktime(date('H') - 24, date('i'), date('s'), date('m'), date('d'), date('Y'));
    $date = date('Y-m-d H:i:s', $now);
    $sql = "delete from " . TABLEPREFIX . "swaptemp where dDate <= '" . $date . "'";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
    ?>

<?php
                    $sql = "SELECT s.nSwapId,s.vTitle,date_format(s.dPostDate,'%m/%d/%Y') as
                    'dPostDate', s.vFeatured, L.vCategoryDesc,s.nCategoryId FROM " . TABLEPREFIX . "swap s
                        left join " . TABLEPREFIX . "category c on s.nCategoryId  = c.nCategoryId
                        LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                     where  vPostType = 'swap' AND s.vDelStatus = '0' ";

                    $targetfile = "";
                    $detailfile = "";
                    if ($uid === 0) {
                        $targetfile = "swaplistdetailed.php";
                        $detailfile = "swapitemdisplay.php";
                        $showTitleDel = LINK_DETAILS;
                        if ($fid > 0) {
                            $targetfile .="?rf=sid&no=$fid&uname=" . urlencode($_GET["uname"]);
                            $sql .= " AND s.nUserId= '" . addslashes($fid) . "' ";
                        }//end if
                    }//end if
                    else if ($uid > 0) {
                        $targetfile = "userswapdetailed.php";
                        $detailfile = "swapitem.php";
                         $showTitleEdit =    LINK_EDIT;
                         $showTitleDel  =    LINK_DELETE;
                        //$showTitleDel = LINK_EDIT . ' | ' . LINK_DELETE;
                        $sql .= "   AND nUserId = '" . $_SESSION["guserid"] . "' ";
                    }//end else if

                    $sql .= "  ORDER BY s.vFeatured DESC,s.dPostDate DESC ";
                   
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    $totalrows = mysqli_num_rows($result);
                    $sql=dopaging($sql,'',PAGINATION_LIMIT);
                   // $sql = $sql . $navigate[0];
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    $numRecords = mysqli_num_rows($result);
                     if($numRecords>0) {
    
                    $pagenumber     =   getCurrentPageNum();
                    $defaultUrl     =   $_SERVER['PHP_SELF'].'?uid='.$_GET["uid"].'&uname='.$_GET["uname"];
                    $querysting     =   "&page=swaplist";
                    $paginationUrl  =   $_SERVER['PHP_SELF'].'?uid='.$_GET["uid"].'&uname='.$_GET["uname"].'&p=[p]' .$querysting;
                    $pageString     =   getnavigation($totalrows);
                    include_once("lib/pager/pagination.php"); 
                    $pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}
                    
                       /* if($numRecordsPage =='')
                            $numRecordsPage = 10;
		  	$navigate = pageBrowser($totalRecords,$numPageLinks, $numRecordsPage, "&page=swaplist", $_GET['numBegin'], $_GET['start'], $_GET['begin'], $_GET['num']);
//execute the new query with the appended SQL bit returned by the function
            $sql_search = $sql . $navigate[0];*/
           	
 
    if (mysqli_num_rows($result) > 0) {
      	switch ($_GET['begin']) {
            case "":
                $cnt = 1;
                break;

            default:
                $cnt = $_GET['begin'] + 1;
                break;
        }//end switch
        if($pagenumber != 1) {  $cnt = PAGINATION_LIMIT+1; }

        while ($row = mysqli_fetch_array($result)) {
            
            $style = ($row["vFeatured"] == "N") ? "style1" : "boldtextblack";
            
$newStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'vStatus', "WHERE nSwapId= '" . addslashes($row['nSwapId']) . "' AND nUserId = '" . addslashes($_SESSION["guserid"]) . "' AND vStatus!='N' "), 'vStatus');

if(!$newStatus)
    $newStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'vStatus', "WHERE (nSwapReturnId= '" . addslashes($row['nSwapId']) . "' AND nUserReturnId = '" . addslashes($_SESSION["guserid"]) . "') AND vStatus!='N' "), 'vStatus');

$countSql = "select nSTId from ".TABLEPREFIX."swaptxn where (nSwapId='".$row['nSwapId']."' or nSwapReturnId in
 (select nSwapReturnId from ".TABLEPREFIX."swaptxn where nSwapId='".$row['nSwapId']."') ) or (nSwapReturnId='".$row['nSwapId']."'  or nSwapId in (select nSwapId from ".TABLEPREFIX."swaptxn where nSwapReturnId='".$row['nSwapId']."') ) and ((nUserReturnId='".$_SESSION["guserid"]."') or (nUserId='".$_SESSION["guserid"]."')) limit 1";
$countRs = mysqli_query($conn, $countSql) or die(mysqli_error($conn));
$countRw = mysqli_fetch_array($countRs);

$flag=0;
    if(!is_null($countRw['nSTId'])) {
        $sql = "SELECT ST.*, U.vLoginName as user,UR.vLoginName as return_user from " . TABLEPREFIX . "swaptxn ST
                    left join " . TABLEPREFIX . "users U on U.nUserId = ST.nUserId
                    left join " . TABLEPREFIX . "users UR on UR.nUserId = ST.nUserReturnId
                    where ST.nSTId='" . $countRw['nSTId'] . "'";
        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        
       
        if ($srow1 = mysqli_fetch_array($res)){
       
            if ($srow1['nUserId']==$this_user && $srow1['vStatus']=='O') {//edit mode for the offering person
                 $nSwapId_array = explode(',',$srow1['nSwapId']);
                $nSwapId_user_array = explode(',',$srow1['nSwapReturnId']);
                $this_user_points_give  = $srow1['nPointTake'];
                $other_user_points      = $srow1['nPointGive'];
                $this_user_points       = $srow1['nPointGive'];
                $this_user_amount       = $srow['nAmountGive'];
                $other_user_amount      = $srow['nAmountTake'];
            }
            else {
               
                if ($srow1['nUserId']==$this_user){//view mode for the offering person
                   
                    $nSwapId_array = explode(',',$srow1['nSwapId']);
                    $nSwapId_user_array = explode(',',$srow1['nSwapReturnId']);
                    $this_user_points_give  = $srow1['nPointGive'];
                    $other_user_points      = $srow1['nPointTake'];
                    $this_user_points       = $srow1['nPointGive'];
                    $this_user_amount       = $srow['nAmountGive'];
                    $other_user_amount      = $srow['nAmountTake'];
                }
                else {//view mode for the accepting person
                   
                    $nSwapId_array = explode(',',$srow1['nSwapReturnId']);
                    $nSwapId_user_array = explode(',',$srow1['nSwapId']);
                    
                    $other_user_points    = $srow1['nPointGive'];
                    $this_user_points     = $srow1['nPointTake'];
                    $this_user_amount       = $srow['nAmountTake'];
                    $other_user_amount      = $srow['nAmountGive'];
                    
                }
               
            }
           
           $srow =$srow1;
        }
        if($srow['nSwapId'] != '' && $srow['nSwapReturnId'] != '') {
            $psql = "select vSwapStatus from " . TABLEPREFIX . "swap where vSwapStatus >= 2 and (nSwapId in (".$srow['nSwapId'].") or nSwapId in (".$srow['nSwapReturnId']."))";

            $pres = mysqli_query($conn, $psql) or die(mysqli_error($conn));
            if (mysqli_num_rows($pres)>0) $payment_status = 'y';

            $p1 = $srow['nAmountGive']-$srow['nAmountTake'];
            $p2 = $srow['nAmountGive']+$srow['nAmountTake'];
            if ($p1!=0 && $p2!=0 && $payment_status=='y'){//if payment completed
                $flag=1;
            }
            else if ($p1!=0 && $p2!=0){//if payment not completed
                $flag=0;
            }
            else {//if payment not required
                $flag=1;
            }
            $payment_status='';
        }
    } 
            switch ($newStatus) {
                            case "O":
                                $shwOfferStatus = TEXT_OFFER_SENT;
                                $errsflag=0;
                                break;

                            case "R":
                                $shwOfferStatus = TEXT_REJECTED;
                                $errsflag=1;
                                break;

                            case "A":
                                $shwOfferStatus = TEXT_ACCEPTED;
                                $errsflag=0;
                                break;

                            default:
                                $shwOfferStatus = TEXT_NEWW;
                                $errsflag=0;
                                break;
                        }//end switch
            ?>
                            <tr>
                                <td align="center"><?php echo $cnt; ?></td>
                                <td align="left"><?php echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=s' class='$style'>" . htmlentities($row["vCategoryDesc"]) . "</a>"; ?></td>
                                <td  align="left"><?php 
                                if(strlen($row["vTitle"])>28)
                                {
                                	
                                	$title = substr(htmlentities($row["vTitle"]),0,28)."...";
                                }
                                else
                                {
                                	$title = $row["vTitle"];
                                }
                echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=s' class='$style'>" . $title . "</a>";
                if ($row["vFeatured"] == "Y") {
                    echo "&nbsp;<img src=images/featured3.gif>";
                }//end if
                ?>
                                </td>
                                <td align="left"><?php echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=s' class='$style'>" . date('m/d/Y', strtotime($row["dPostDate"])) . "</a>"; ?></td>
                                <td class="<?php if($errsflag == 0){ ?>success<?php }else{?>warning<?php } ?>" align="left"><?php echo $shwOfferStatus; ?></td>
                                <?php if($flag): ?>
                                    <td align="left"><?php echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=s' class='$style'></a>"; ?> &nbsp; <?php echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=s' class='$style clsDelete'>" . $showTitleDel . "</a>"; ?></td>
                                <?php else: ?>
                                    <td align="left"><?php echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=s' class='$style'>" . $showTitleEdit . "</a>"; ?> &nbsp; <?php echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=s' class='$style clsDelete'>" . $showTitleDel . "</a>"; ?></td>
                                <?php endif; ?>
                            </tr>
                                    <?php
                                    $cnt++;
                                }//end while
                                
                                ?>
	<tr>
		<td colspan="6" class="navigation">
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
		
<tr align="center" bgcolor="#FFFFFF">
	<td colspan="6" style="border : 0px;"><strong><?php echo MESSAGE_SORRY_NO_RECORDS; ?></strong></td>
</tr>
						
                    <?php }//end else?>
                
<?php 
$_GET['begin'] = $recStart;
//$resultArray = array($result,$targetfile);
//return $resultArray;
}

//end function?>				  
