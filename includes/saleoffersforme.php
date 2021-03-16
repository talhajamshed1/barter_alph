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
function func_sale_offers_forme($uid = 0, $fid = 0) {
		global $numRecordsPage,$numPageLinks,$conn;
                
	$recStart = $_GET['begin'];
		if($_GET['page']!='saleoffersforme')
			$_GET['begin'] = $_SESSION['pagenav']['saleoffersforme'];
		else
			$_SESSION['pagenav']['saleoffersforme'] = $_GET['begin'];
                    //checking escrow status
                    if (DisplayLookUp('Enable Escrow') == 'Yes') {
                        $SaleStatus = '';
                    }//end if
                    else {
                        $SaleStatus = " OR sd.vSaleStatus ='4'";
                    }//end esle

                    $sql = "Select sd.nSaleId,sd.nUserId,sd.nAmount,sd.nQuantity,dDate,date_format(sd.dDate,'%m/%d/%Y') as 'dDate2',s.vTitle,u.vLoginName from ";
                    $sql .= " " . TABLEPREFIX . "saledetails sd inner join " . TABLEPREFIX . "sale s on sd.nSaleId = s.nSaleId inner join " . TABLEPREFIX . "users u on ";
                    $sql .= " sd.nUserId = u.nUserId  where s.nUserId='" . $_SESSION["guserid"] . "' AND (sd.vSaleStatus ='2'  OR sd.vSaleStatus ='3' " . $SaleStatus . ") ";

                    $targetfile = "saleofferformedetail.php";
                    $detailfile = "saleofferformeitem.php";

                    $sql .= " ORDER BY s.vFeatured DESC,s.dPostDate DESC";
			$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
			$totalRecords = mysqli_num_rows($result);
		
		
		  	$navigate = pageBrowser($totalRecords, $numPageLinks, $numRecordsPage, "&page=saleoffersforme", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
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
                            $style = "style1";
                            
                            if(strlen($row["vTitle"])>38)
                            {
                            	$title = substr(htmlentities($row["vTitle"]),0,38)."...";
                            }
                            else
                            {
                            	$title = $row["vTitle"];
                            }
                            
                            ?>
                            <tr >
                                <td  align="left"><?php echo $cnt; ?></td>
                                <td  align="left"><?php echo "<a href='" . $detailfile . "?saleid=" . $row["nSaleId"] . "&userid=" . $row["nUserId"] . "&dt=" . urlencode($row["dDate"]) . "&\"'  class='$style'>" . htmlentities($title) . "</a>"; ?></td>
                                <td align="left"><?php echo "<a href='" . $detailfile . "?saleid=" . $row["nSaleId"] . "&userid=" . $row["nUserId"] . "&dt=" . urlencode($row["dDate"]) . "&\"'  class='$style'>" . htmlentities($row["vLoginName"]) . "</a>"; ?></td>
                                <td align="left"><?php echo "<a href='" . $detailfile . "?saleid=" . $row["nSaleId"] . "&userid=" . $row["nUserId"] . "&dt=" . urlencode($row["dDate"]) . "&\"'  class='$style'>" . date('m/d/Y', strtotime($row["dDate2"])) . "</a>"; ?></td>
                                <td align="left"><?php echo "<a href='" . $detailfile . "?saleid=" . $row["nSaleId"] . "&userid=" . $row["nUserId"] . "&dt=" . urlencode($row["dDate"]) . "&\"'  class='$style'>".LINK_VIEW."</a>"; ?></td>
                            </tr>
                            <?php
                            $cnt++;
                        }//end while
?>
                        <tr><td colspan="7" class="navigation">
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