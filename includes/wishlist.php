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
function func_wish_list($uid = 0, $fid = 0) {
		global $numRecordsPage,$numPageLinks,$conn;

	  $recStart = $_GET['begin'];
  if($_GET['page']!='wishlist')
  	$_GET['begin'] = $_SESSION['pagenav']['wishlist'];
  else
  	 $_SESSION['pagenav']['wishlist'] = $_GET['begin'];

    $now = mktime(date('H') - 24, date('i'), date('s'), date('m'), date('d'), date('Y'));
    $date = date('Y-m-d H:i:s', $now);
    $sql = "delete from " . TABLEPREFIX . "swaptemp where dDate <= '" . $date . "'";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    $sql = "SELECT s.nSwapId,s.vTitle,date_format(s.dPostDate,'%m/%d/%Y') as
                   'dPostDate', L.vCategoryDesc,s.nCategoryId FROM " . TABLEPREFIX . "swap s
                       left join " . TABLEPREFIX . "category c on s.nCategoryId  = c.nCategoryId
                       LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                   where  vPostType = 'wish' AND s.vDelStatus = '0'";

                    $targetfile = "";
                    $detailfile = "";
                    if ($uid === 0) {
                        $targetfile = "wishlistdetailed.php";
                        $detailfile = "swapitemdisplay.php";
                        $showTitleDel = LINK_DETAILS;
                        if ($fid > 0) {
                            $targetfile .="?rf=sid&no=$fid&uname=" . urlencode($_GET["uname"]);
                            $sql .= " AND s.nUserId = '" . addslashes($fid) . "' ";
                        }//end if
                    }//end if
                    else if ($uid > 0) {
                        $targetfile = "userwishdetailed.php";
                        $detailfile = "swapitem.php";
                         $showTitleEdit =    LINK_EDIT;
                        $showTitleDel  =    LINK_DELETE;
                        //$showTitleDel = LINK_EDIT . ' | ' . LINK_DELETE;
                        $sql .= "   AND s.nUserId = '" . $_SESSION["guserid"] . "' ";
                    }//end else if

                    $sql .= "   ORDER BY dPostDate DESC";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    $totalrows = mysqli_num_rows($result);
                    $sql=dopaging($sql,'',PAGINATION_LIMIT);
                   // $sql = $sql . $navigate[0];
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    $numRecords = mysqli_num_rows($result);
                     if($numRecords>0) {
    
                    $pagenumber     =   getCurrentPageNum();
                    $defaultUrl     =   $_SERVER['PHP_SELF'].'?uid='.$_GET["uid"].'&uname='.$_GET["uname"];
                    $querysting     =   "&page=wishlist";
                    $paginationUrl  =   $_SERVER['PHP_SELF'].'?uid='.$_GET["uid"].'&uname='.$_GET["uname"].'&p=[p]' .$querysting;
                    $pageString     =   getnavigation($totalrows);
                    include_once("lib/pager/pagination.php"); 
                    $pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}
                    



//$navigate = pageBrowser($totalRecords, $numPageLinks, $numRecordsPage, "&page=wishlist", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
//$sql_search = $sql . $navigate[0];
//$result = mysqli_query($conn, $sql_search) or die(mysqli_error($conn));
if($_GET["uid"] != "" && $_GET["uname"] != ""){
?>
<tr><td class="grey" colspan="6">
<h4>Wish Items</h4></td></tr>
<?php
}
    if (mysqli_num_rows($result) > 0) {
         switch ($_GET['begin']) {
            case "":
                $cnt = 1;
                break;

            default:
                $cnt = $_GET['begin'] + 1;
                break;
        }//end switc
        if($pagenumber != 1) {  $cnt = PAGINATION_LIMIT+1; }
        
        while ($row = mysqli_fetch_array($result)) {
            ?>
                            <tr >
                                <td align="center"><?php echo $cnt; ?></td>
                                <td colspan="2" align="left"><?php echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=w'>" . htmlentities($row["vCategoryDesc"]) . "</a>"; ?></td>
                                <td  align="left"><?php
                                if(strlen($row["vTitle"])>28)
                                {
                                	$title = substr(htmlentities($row["vTitle"]),0,28)."...";
                                }
                                else
                                {
                                	$title = $row["vTitle"];
                                }
                               
                echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=w'>" .$title. "</a>";
                if ($row["vFeatured"] == "Y") {
                    echo "&nbsp;<img src=images/featured3.gif>";
                }//end if
                ?>
                                </td>
                                <td align="left"><?php echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=w'>" . date('m/d/Y', strtotime($row["dPostDate"])) . "</a>"; ?></td>
                                <td align="left"> <?php echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=w'>" . $showTitleEdit . "</a>"; ?> &nbsp; <?php echo "<a href='" . $detailfile . "?swapid=" . $row["nSwapId"] . "&source=w' class='clsDelete'>" . $showTitleDel . "</a>"; ?></td>
                            </tr>
                                    <?php
                                    $cnt++;
                                }//end while
                                
                                ?>
<tr><td colspan="6" class="navigation">
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
</td></tr>
        <?php
                               
    }//end if
    else {
        ?>
<tr align="center" bgcolor="#FFFFFF">
	<td colspan="6" style="border : 0px;"><strong><?php echo MESSAGE_SORRY_NO_RECORDS; ?></strong></td>
</tr>
                    <?php }//end else?>
                
<?php 
//$resultArray = array($result,$targetfile);
//return $resultArray;
$_GET['begin'] = $recStart;
}

//end function?>				  