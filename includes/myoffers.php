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
function swapIsValid($i) {
    global $conn;
    $var_ret = false;
    $sql = "Select nSwapId,nUserId from " . TABLEPREFIX . "swaptxn where nSwapId = '"
            . addslashes($i) . "' AND nUserId='" . $_SESSION["guserid"] . "'
                    AND vStatus NOT IN('A','N') ";
    if (mysqli_num_rows(mysqli_query($conn, $sql)) <= 0) {
        //if the resultset is empty.
        $var_ret = false;
    }//end if
    else {
        $var_ret = true;
    }//end else
    return $var_ret;
}

//end if

function allExists($a) {
    global $conn;
    $var_ret = false;
    if ($a != "") {
        $ch_arr = explode(",", $a);
        $sql = "Select nSwapId from " . TABLEPREFIX . "swap where vSwapStatus='0'
                                 AND vDelStatus='0' AND nSwapId in($a)";

        if (mysqli_num_rows(mysqli_query($conn, $sql)) != count($ch_arr)) {
            $var_ret = false;
        }//end if
        else {
            $var_ret = true;
        }//end else
    }//end if
    else {
        $var_ret = true;
    }//end else
    return $var_ret;
}

//end function
$errsflag=0;
 	global $numRecordsPage,$numPageLinks;
	$recStart = $_GET['begin'];
	if($_GET['page']!='myoffers')
		$_GET['begin'] = $_SESSION['pagenav']['myoffers'];
	else
		$_SESSION['pagenav']['myoffers'] = $_GET['begin'];

 $sql = "Select ST.nSwapId,ST.nUserId,ST.vPostType,ST.vStatus,ST.nSTId,ST.nParentId,
                            vLoginName as 'UserName',
                            S.vTitle,date_format(ST.dDate,'%m/%d/%Y') as 'dPostDate'  from
                            " . TABLEPREFIX . "swaptxn ST
                                            Left Outer Join " . TABLEPREFIX . "users U on ST.nUserReturnId = U.nUserId
                                            Left outer join " . TABLEPREFIX . "swap S on ST.nSwapReturnId = S.nSwapId
                            where ST.nUserId = '" . $_SESSION["guserid"] . "' and ST.vStatus <> 'N'
                            Order By ST.dDate DESC";
            /*
              $sql = "Select ST.nSwapId,ST.nUserId,ST.vPostType,ST.vStatus,ST.nSTId,
              vLoginName as 'UserName',
              S.vTitle,date_format(ST.dDate,'%m/%d/%Y') as 'dPostDate'  from
              ".TABLEPREFIX."swaptxn ST Left outer join ".TABLEPREFIX."swap S on ST.nSwapId = S.nSwapId
              Left Outer Join ".TABLEPREFIX."users U on S.nUserId = U.nUserId
              where ST.nUserId = '" . $_SESSION["guserid"] . "'
              Order By ST.dDate DESC Limit 0,5";
             */
			$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
			$totalRecords = mysqli_num_rows($result);
		
		
		  	$navigate = pageBrowser($totalRecords,$numPageLinks, $numRecordsPage, "&page=myoffers", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
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
                        //Populate data
                        $sqlCheck = "Select nSwapReturnId from " . TABLEPREFIX . "swapreturn where
                                                      nSwapId = '" . addslashes($row['nSwapId']) . "' AND
                                                      nUserId = '" . $_SESSION["guserid"] . "' ";
                        $resultCheck = mysqli_query($conn, $sqlCheck) or die(mysqli_error($conn));
                        $varcount = 0;
                        $var_array = "";
                        $var_check_for = "";
                        if (mysqli_num_rows($resultCheck) > 0) {
                            while ($rowCheck = mysqli_fetch_array($resultCheck)) {
                                $var_array .= "chk[$varcount]='chk" . $rowCheck["nSwapReturnId"] . "';";
                                $var_check_for .="'" . $rowCheck["nSwapReturnId"] . "',";
                                $varcount++;
                            }//end while
                        }//end if


                        $var_check_for = substr($var_check_for, 0, -1);
                        if (swapIsValid($row['nSwapId']) == true && allExists($var_check_for) == true) {
                            $newStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'vStatus', "WHERE nSwapId= '" . addslashes($row['nSwapId']) . "'
                                                                                                                                                        AND nUserId = '" . addslashes($_SESSION["guserid"]) . "' AND nSTId='" . $row['nSTId'] . "'"), 'vStatus');
                        } else {
                            $newStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'vStatus', "WHERE nSwapId= '" . addslashes($row['nSwapId']) . "'
                                                                                                                                                        AND nUserId = '" . addslashes($_SESSION["guserid"]) . "' AND nSTId='" . $row['nSTId'] . "'"), 'vStatus');
                        }//end else
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
                        
                        if(strlen($row["vTitle"])>28)
                        {
                        	$title = substr(htmlentities($row["vTitle"]),0,28)."...";
                        }
                        else
                        {
                        	$title = $row["vTitle"];
                        }

                        ?>
                        <tr >
                            <td align="center" align="left"><?php echo $cnt; ?></td>
                            <td class="maintext" align="left">
                            <?php 
                                if ($row["vTitle"] == '') $row["vTitle"] = MESSAGE_NO_ITEM_SELECTED; 
                                echo htmlentities($title); 
                            ?>
                            </td>
                            <td align="left"><?php echo htmlentities($row["UserName"]); ?></td>
                            <td align="left"><?php echo date('m/d/Y', strtotime($row["dPostDate"])); ?></td>
                            <td class="<?php if($errsflag == 0){ ?>success<?php }else{?>warning<?php } ?>" align="left"><?php echo $shwOfferStatus; ?></td>
                            <td align="left"><?php echo (($row["vPostType"] == "swap") ? TEXT_SWAP_OFFER : TEXT_WISH_OFFER); ?></td>
                            <td align="left"><?php echo "<a href='" . (($row['vPostType'] == "swap") ? "makeoffer.php" : "makeoffer.php") . "?post_type=".$row["vPostType"]."&nSTId=" . $row['nSTId'] . "&uname=".htmlentities($row["UserName"])."'>".LINK_VIEW."</a>"; ?></td>
                        </tr>
                        <?php
                        $cnt++;
                    }//end while
                    ?><tr>
					<td colspan="7" class="navigation">
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
                        <td colspan="7" style="border : 0px;"><strong><?php echo ERROR_SORRY_NO_OFFERS_MADE; ?></strong></td>
                    </tr>
<?php }

$_GET['begin'] = $recStart;
//end else ?>

            
