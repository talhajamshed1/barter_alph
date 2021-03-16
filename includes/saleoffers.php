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
function func_sale_offers($uid = 0, $fid = 0) {
	global $numRecordsPage,$numPageLinks,$conn;
   $recStart = $_GET['begin'];
	if($_GET['page']!='saleoffers')
		$_GET['begin'] = $_SESSION['pagenav']['saleoffers'];
	else
		$_SESSION['pagenav']['saleoffers'] = $_GET['begin'];

                    $sql = "Select sd.nSaleId,sd.nAmount,sd.nQuantity,date_format(sd.dDate,'%m/%d/%Y') as 'dDate',dDate as 'dDate2',s.vTitle,u.vLoginName from ";
                    $sql .= " " . TABLEPREFIX . "saledetails sd inner join " . TABLEPREFIX . "sale s on sd.nSaleId = s.nSaleId inner join " . TABLEPREFIX . "users u on ";
                    $sql .= " s.nUserId = u.nUserId  where sd.nUserId='" . $_SESSION["guserid"] . "'";

                    $targetfile = "usersaleofferdetailed.php";
                    $detailfile = "buynext.php";

                      $sql .= " ORDER BY s.vFeatured DESC,sd.dDate DESC";
			$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
			$totalRecords = mysqli_num_rows($result);
		
		
		  	$navigate = pageBrowser($totalRecords, $numPageLinks, $numRecordsPage, "&page=saleoffers", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
            $sql_search = $sql . $navigate[0];
           	$result = mysqli_query($conn, $sql_search) or die(mysqli_error($conn));

                    if (mysqli_num_rows($result) > 0) {
						switch ($_GET['begin']) {
							case "":
								$cnt = 1;
								break;
				
							default:
								$cnt = $_GET['begin'] + 1;
								break;
						}//end switch
                        while ($row = mysqli_fetch_array($result)) {
                            $uid = fetchSingleValue(select_rows(TABLEPREFIX."users", "nUserId", "where vLoginName = '".$row["vLoginName"]."'"), "nUserId");
                            $style = "style1"; 
                            if(strlen($row["vTitle"])>28)
                            {
                            	$title = substr(htmlentities($row["vTitle"]),0,28)."...";
                            }
                            else
                            {
                            	$title = $row["vTitle"];
                            } 
                            ?>
                            <tr>
                                <td  align="left"><?php echo $cnt; ?></td>
                                <td  align="left"><?php echo "<a href='" . $detailfile . "?saleid=" . $row["nSaleId"] . "&tot=" . $row["nAmount"] . "&reqd=" . $row["nQuantity"] . "&dt=" . urlencode($row["dDate2"]) . "&\" class='$style'>" . htmlentities($title) . "</a>"; ?></td>
                                <td align="left"><?php echo "<a href='" . $detailfile . "?saleid=" . $row["nSaleId"] . "&tot=" . $row["nAmount"] . "&reqd=" . $row["nQuantity"] . "&dt=" . urlencode($row["dDate2"]) . "&\" class='$style'>" . htmlentities($row["vLoginName"]) . "</a>"; ?></td>
                                <td align="left">
                                    <?php if(getFeedBackCount($row["nSaleId"],$_SESSION["guserid"])) {  echo LINK_FEEDBACK_ALREDAY_POSTED ; }else { ?>
                                    <a href="userfeedback.php?uid=<?php echo $uid;?>&uname=<?php echo htmlentities($row["vLoginName"]);?>&nId=<?php echo $row["nSaleId"]?>&source=sa"><?php echo LINK_POST_FEEDBACK;?></a>
                                    <?php } ?> 
                                </td>
                                <td align="left"><?php echo "<a href='" . $detailfile . "?saleid=" . $row["nSaleId"] . "&tot=" . $row["nAmount"] . "&reqd=" . $row["nQuantity"] . "&dt=" . urlencode($row["dDate2"]) . "&\" class='$style'>" . date('m/d/Y', strtotime($row["dDate"])) . "</a>"; ?></td>
                                <td align="left"><?php echo "<a href='" . $detailfile . "?saleid=" . $row["nSaleId"] . "&tot=" . $row["nAmount"] . "&reqd=" . $row["nQuantity"] . "&dt=" . urlencode($row["dDate2"]) ."&' class='$style'>".LINK_VIEW."</a>"; ?></td>
                            </tr>
                            <?php
                            $cnt++;
                        }//end while
                        ?>  <tr><td colspan="7" class="navigation">
                  <div>
                  <div class="left"><?php echo str_replace('{total_rows}',$totalrows,str_replace('{current_rows}',$navigate[1],TEXT_LISTING_RESULTS)); ?></div>
				  <div class="right"><?php echo($navigate[2]); ?></div>
				  <div class="clear"></div>
					</div>
                            </td></tr>  
                            <?php
                        }//end if
                        else {
                            ?>
                        <tr align="center" bgcolor="#FFFFFF" >
                            <td colspan="5" style="border : 0px;"><strong><?php echo ERROR_SORRY_NO_ITEMS_TO_DISPLAY; ?></strong></td>
                        </tr>
                    <?php }//end else?>
               
<?php 
//$resultArray = array($result,$targetfile);
//return $resultArray;
 $_GET['begin'] = $recStart;

}

//end function ?>