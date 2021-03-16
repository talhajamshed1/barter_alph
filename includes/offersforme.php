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
$errsflag=0;
   	global $numRecordsPage,$numPageLinks;
 $recStart = $_GET['begin'];
	if($_GET['page']!='offersforme')
		$_GET['begin'] = $_SESSION['pagenav']['offersforme'];
	else
		$_SESSION['pagenav']['offersforme'] = $_GET['begin'];


$sql = "Select ST.nSwapId,ST.nUserId,ST.vPostType,ST.vStatus,ST.vBlink,ST.nSTId,ST.nParentId,
                U.vLoginName as 'UserName',
                S.vTitle,date_format(ST.dDate,'%m/%d/%Y') as 'dPostDate'  from
                " . TABLEPREFIX . "swaptxn ST
				Left outer join " . TABLEPREFIX . "swap S on ST.nSwapId = S.nSwapId
				Left Outer Join " . TABLEPREFIX . "users U on ST.nUserId = U.nUserId
                where ST.nUserReturnId = '" . $_SESSION["guserid"] . "' and ST.vStatus <> 'N'
                Order By ST.dDate DESC";

			$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
			$totalRecords = mysqli_num_rows($result);
		
		
		  	$navigate = pageBrowser($totalRecords, $numPageLinks, $numRecordsPage, "&page=offersforme", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
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
                        $newStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'vStatus', "WHERE nSwapId= '" . addslashes($row['nSwapId']) . "'
                  											AND nUserId = '" . addslashes($row['nUserId']) . "' AND nSTId='" . $row['nSTId'] . "'"), 'vStatus');

                        //checking offer status
                        switch ($newStatus) {
                            case "A":
                                $shwOfferStatus = TEXT_ACCEPTED;
                                $errsflag=0;
                                break;

                            case "R":
                                $shwOfferStatus = TEXT_REJECTED;
                                $errsflag=1;
                                break;

                            case "N":
                                $shwOfferStatus = TEXT_INVALID;
                                $errsflag=1;
                                break;

                            default:
                                $shwOfferStatus = TEXT_INPROGRESS;
                                $errsflag=0;
                                break;
                        }//end switch

                        switch ($row['vBlink']) {
                            case "B":
                                $shwBlink = '<blink class="blinkytext">&nbsp;<sup>'.TEXT_NEW.'</sup></blink>';
                                break;

                            default:
                                $shwBlink = '';
                                break;
                        }//end switch
                        
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
                            <td align="center"><?php echo $cnt; ?></td>
                            <td class="maintext" align="left"><?php echo htmlentities($title); ?>&nbsp;&nbsp;<?php echo $shwBlink; ?></td>
                            <td align="left"><?php echo htmlentities($row["UserName"]); ?></td>
                            <td align="left"><?php echo date('m/d/Y', strtotime($row["dPostDate"])); ?></td>
                            <td class="<?php if($errsflag == 0){ ?>success<?php }else{?>warning<?php } ?>" align="left"><?php echo $shwOfferStatus; ?></td>
                            <td align="left"><?php echo (($row["vPostType"] == "swap") ? TEXT_SWAP_OFFER : TEXT_WISH_OFFER); ?></td>
                            <td align="left"><?php echo "<a href='" . (($row["vPostType"] == "swap") ? "makeoffer.php" : "makeoffer.php") . "?post_type=".$row["vPostType"]."&userid=" . $row["nUserId"] . "&uname=" . urlencode($row["UserName"]) . "&nSTId=" . $row['nSTId'] . "'>".LINK_VIEW."</a>"; ?></td>
                        </tr>
                        <?php
                        $cnt++;
                    }//end while
                    ?> <tr><td colspan="7" class="navigation">
                         <div>
						<div class="left"><?php echo str_replace('{total_rows}',$totalrows,str_replace('{current_rows}',$navigate[1],TEXT_LISTING_RESULTS)); ?>  </div>
                 <div class="right"><?php echo($navigate[2]); ?></div>
				 <div class="clear"></div>				 
				 </div>
				 </td></tr>   
                    <?php
                }//end if
                else {
                    ?>
                    <tr align="center" bgcolor="#FFFFFF" >
                        <td colspan="7" style="border : 0px;"><strong><?php echo ERROR_SORRY_NO_OFFERS_RECEIVED; ?></strong></td>
                    </tr>
                <?php } $_GET['begin'] = $recStart;//end else?>

            
