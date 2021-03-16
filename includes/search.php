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
if (isset($_POST['btnSubAdd']) && $_POST['btnSubAdd'] != '') {
    if (function_exists('get_magic_quotes_gpc')) {
        $txtTitle = stripslashes($_POST['txtTitle']);
        $txtMsg = stripslashes($_POST['txtMsg']);
    }
    else {
        $txtTitle = $_POST['txtTitle'];
        $txtMsg = $_POST['txtMsg'];
    }

    $pquery = ($_POST['qyery'] != '') ? $pquery = '?' . $_POST['qyery'] : $pquery = '';

    //insert into tbl message
    mysqli_query($conn, "insert into " . TABLEPREFIX . "messages (nToUserId,nFromUserId,vTitle,vMsg,vStatus,nDate)
      values ('" . $_POST['ToId'] . "','" . $_SESSION["guserid"] . "','" . addslashes($txtTitle) . "','" . addslashes($txtMsg) . "',
      'N',now())") or die(mysqli_error($conn));
    $_SESSION['succ_msg'] = MESSAGE_MESSAGE_SENT_SUCCESSFULLY;
    
    $redirectUrl = basename($_SERVER['PHP_SELF']).$pquery;
    //echo $redirectUrl;exit;

    echo '<script type="text/javascript">window.location="'.$redirectUrl.'";</script>';exit;
}

if (trim($_REQUEST['txtHomeSearch']) == TEXT_SEARCH_PRODUCT_NAME) { 
    $_REQUEST['txtHomeSearch'] = '';
}
$txtHomeSearch = trim($_REQUEST['txtHomeSearch']);
$txtHomeSearch =strip_tags($txtHomeSearch);


if (isset($_REQUEST['txtHomeSearch']) && trim($_REQUEST['txtHomeSearch']) != '') {
    $txtHomeSearch = trim($_REQUEST['txtHomeSearch']);
    $txtHomeSearch =strip_tags($txtHomeSearch);
}
$having = '';
if($_REQUEST["txtSearchRadius"] && trim($_REQUEST["txtSearchRadius"])) {
    $distance_km = trim($_REQUEST["txtSearchRadius"]);
    $distance_km = strip_tags($distance_km); 
}

if($_REQUEST["txtUserLat"] && trim($_REQUEST["txtUserLat"])) {
    $latitude = trim($_REQUEST["txtUserLat"]);
    $latitude = strip_tags($latitude); 
}
if($_REQUEST["txtUserLng"] && trim($_REQUEST["txtUserLng"])) {
    $longitude = trim($_REQUEST["txtUserLng"]);
    $longitude = strip_tags($longitude); 
}
if(!empty($distance_km) && !empty($latitude) && !empty($longitude)){ 
    $radius_km = $distance_km;

    //$having = " AND (ST_Distance_Sphere(point(-".$longitude.", ".$latitude."),point(-u.longitude, u.latitude)) <=".$radius_km."*1000) "; 
    $having = " AND ROUND (( 6371 * acos( cos( radians($latitude) ) * cos( radians(u.latitude) ) * cos( radians( u.longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( u.latitude ) ) ) ) ,2) <=".$radius_km;

}

    //checking point stats
if (ENABLE_POINT != '1') {
    $sqlsearch = "(SELECT sw.vTitle, sw.nSwapId, sw.nCategoryId, sw.dPostDate, sw.vUrl, sw.vPostType, sw.nValue, sw.nPoint, 
    sw.nUserId as swapUserid, sw.vDescription, sw.nValue, sw.vCondition, sw.vType, sw.vBrand,sw.vYear 
    FROM " . TABLEPREFIX . "swap sw
    LEFT JOIN " . TABLEPREFIX . "users u ON sw.nUserId=u.nUserId
    WHERE u.vStatus='0' AND vTitle LIKE '%" . addslashes($txtHomeSearch) . "%'
    AND sw.vDelStatus='0' and u.nUserId <> '".$_SESSION['guserid']."'".$having."  
    ORDER BY sw.vFeatured DESC, sw.dPostDate DESC) 
    UNION
    (SELECT s.vTitle, s.nSaleId, s.nCategoryId, s.dPostDate, s.vUrl, s.vType, s.nValue, s.nPoint, 
    s.nUserId as saleUserid ,s.vDescription, s.nValue, s.vCondition, s.vType, s.vBrand, s.vYear 
    FROM " . TABLEPREFIX . "sale s
    LEFT JOIN " . TABLEPREFIX . "users u ON s.nUserId=u.nUserId
    WHERE u.vStatus='0' AND vTitle LIKE '%" . addslashes($txtHomeSearch) . "%'
    AND s.vDelStatus='0' AND s.nQuantity > '0' and u.nUserId <> '".$_SESSION['guserid']."'".$having."  
    ORDER BY s.vFeatured DESC, s.dPostDate DESC)";
}
else {

    $sqlsearch = "SELECT sw.vTitle, sw.nSwapId, sw.nCategoryId, sw.dPostDate, sw.vUrl, sw.vPostType, sw.nValue, sw.nPoint,
    sw.nUserId as swapUserid, sw.vDescription, sw.nValue, sw.vCondition, sw.vType, sw.vBrand, sw.vYear 
    FROM " . TABLEPREFIX . "swap sw
    LEFT JOIN " . TABLEPREFIX . "users u ON sw.nUserId=u.nUserId
    WHERE sw.vTitle LIKE '%" . addslashes($txtHomeSearch) . "%' AND u.vStatus='0'
    AND sw.vDelStatus='0' and u.nUserId <> '".$_SESSION['guserid']."'".$having." 
    ORDER BY sw.vFeatured DESC, sw.dPostDate DESC";
}

?>
<script language="javascript" type="text/javascript">
    function showMsg(nid)
    {
        document.getElementById('msgShow'+nid).style.display='';
    }
    function hideMsg(nid)
    {
        document.getElementById('msgShow'+nid).style.display='none';
    }

    function ValidateMsg(fid,toid)
    {
        if(trim(document.getElementById('txtTitle'+fid).value)=='')
        {
            alert("<?php echo ERROR_SUBJECT_EMPTY; ?>");
            document.getElementById('txtTitle'+fid).focus();
            return false;
        }
        if(trim(document.getElementById('txtMsg'+fid).value)=='')
        {
            alert("<?php echo ERROR_MESSAGE_EMPTY; ?>");
            document.getElementById('txtMsg'+fid).focus();
            return false;
        }
        else
        {
            document.getElementById('btnSubAdd').value='Send Message';//this is not text but value (not relevent for multi-lang)
            document.getElementById('ToId').value=toid;
            document.getElementById('txtTitle').value=document.getElementById('txtTitle'+fid).value;
            document.getElementById('txtMsg').value=document.getElementById('txtMsg'+fid).value;
            document.getElementById('frmMsg').submit();
        }
    }

</script>
<link rel="stylesheet" href="styles/jquery.remodal.css">

<div class="row product-more-list viewmode gridView"> 
   <div class="full-width">
    <div class="innersubheader2">
        <?php if(!empty($_SESSION['succ_msg'])) { ?>
           <div class="success_msg">
            <span class="glyphicon glyphicon-ok"></span>
            <p class="msg"><?php if(!empty($_SESSION['succ_msg'])) {echo $_SESSION['succ_msg']; unset($_SESSION['succ_msg']);}

            ?></p>
            <div class="clear"></div>
        </div>
    <?php } ?>

    <div class="col-lg-12">
        <h3>
            <?php
            if ($txtHomeSearch != "") {
                echo TEXT_SEARCH_RESULTS . '  "' . htmlentities($txtHomeSearch) . '"';
            }
            else {
                echo TEXT_SEARCH;
            }
            ?>
        </h3>
    </div>
</div>
</div>



<table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td valign="top" bgcolor="#EEEEEE"><table width="100%"  border="1" cellspacing="1" cellpadding="4" class="maintext2">
            <?php
            $sess_back = $targetfile . "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtHomeSearch=" . $txtHomeSearch . "&source=" . $var_source . "&no=" . $var_no;
            //get the total amount of rows returned
            $totalrows = mysqli_num_rows(mysqli_query($conn, $sqlsearch));
            
            $sqlsearch=dopaging($sqlsearch,'',PAGINATION_LIMIT);
            //echo $sqlsearch;
            $result = mysqli_query($conn, $sqlsearch) or die(mysqli_error($conn));
            
            $numRecords = mysqli_num_rows($result);


            /*$navigate = pageBrowser($totalrows, 10, 10, "&cmbSearchType=$cmbSearchType&txtHomeSearch=" . urlencode($txtHomeSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
            //execute the new query with the appended SQL bit returned by the function
            $sql = $sql . $navigate[0];
            $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));*/

            if($numRecords>0) {
                $pagenumber     =   getCurrentPageNum();
                //$defaultUrl     =   $_SERVER['PHP_SELF'];
                $defaultUrl     =   $_SERVER['PHP_SELF']."?cmbSearchType=$cmbSearchType&txtHomeSearch=" . urlencode($txtHomeSearch);
                $querysting     =   "&cmbSearchType=$cmbSearchType&txtHomeSearch=" . urlencode($txtHomeSearch) . "&rf=$var_rf&no=$var_no&uname=" . urlencode($var_uname) . "&";
                $paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
                $pageString     =   getnavigation($totalrows);
                include_once("lib/pager/pagination.php"); 
                $pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
            }


            $message = ($message != '') ? $message : $_SESSION['sessionMsg'];
            unset($_SESSION['sessionMsg']);

            if (isset($message) && $message != '') {
                ?>
                <tr bgcolor="#FFFFFF">
                    <td colspan="4" align="center" class="warning"><?php echo $message; ?></td>
                </tr>
            <?php }?>			
        </table>
    </td>
</tr>
</table>
<?php
$var_url    = '';
$i = 0;

if (mysqli_num_rows($result) > 0) {
    while ($arr = mysqli_fetch_array($result)) {					

		//print_r($arr);die();

        $style = ($arr["vFeatured"] == "N") ? "style1" : "boldtextblack";

        if ($arr['vPostType'] != 'swap' && $arr['vPostType'] != 'wish') {
            $var_url = getDisplayImage($arr["nSwapId"], "sa", $arr["vUrl"]);
            $detailfile = 'swapitemdisplay.php?saleid=' . $arr['nSwapId'] . '&source=sa&txtHomeSearch=' . urlencode($txtHomeSearch);
        }
        else {
            $var_url = getDisplayImage($arr["nSwapId"], "s", $arr["vUrl"]);
            $detailfile = 'swapitemdisplay.php?swapid=' . $arr['nSwapId'] . '&source=s&txtHomeSearch=' . urlencode($txtHomeSearch);
        }


        switch($arr['vPostType'])
        {
            case "swap" :
            $ItemUser   = $arr['swapUserid'];
            $nShowJavaId = 'nSwapId';
            $var_source = "s";
            break;
            case "sale" :
            $ItemUser   = $arr['saleUserid'];
            $nShowJavaId='nSaleId';
            $var_source = "sa";
            break;
            default :
            $ItemUser   = $arr['swapUserid'];
            $var_source = "w";

        }
        $condition = "where nUserId='" . $ItemUser . "'";
        $userRs=select_rows(TABLEPREFIX . 'users', 'vLoginName, vFirstName, vLastName, vAddress1, vCity, vState, vCountry, vFax, nZip, vIMStatus, profile_image', $condition);
        $userData=array();
        $userData=mysqli_fetch_assoc($userRs);



        //$condition = "where nUserId='" . $ItemUser . "'";
        
        
        $condition1 =    "where nUserId='" . $ItemUser . "'";  
        $userStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'online', 'vVisible', $condition1), 'vVisible');


        //echo $arr['nUserId'] ."----". $_SESSION["guserid"];die();
        //if posted user not logged user
        $nShowJavaId = "nSwapId";
        //var_dump($arr);exit;
        if ($arr['swapUserid'] != $_SESSION["guserid"]) {
            //fetching user name
            $vLoginName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
            $chatWindow = '<a href="javascript:WindowPop(\'' . ucfirst($ItemUser) . '\');" class="chat_online" title="'.LINK_CHAT_WITH.' ' . ucfirst($vLoginName) . '" title="'.LINK_CHAT_WITH.' ' . ucfirst($vLoginName) . '""></a>';
            

            $offMessage = '<a   href= "#" data-toggle="modal" data-target="#msgShow'.$arr[$nShowJavaId].'" title="'.BUTTON_SEND_MESSAGE.'"><i class="flaticon-email"></i></a>';

        }
        else {
            $chatWindow = '';
            $offMessage = '';
        }

        if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
            $offMessage = $offMessage;
        }
        else {
           $offMessage = '';
       }

       switch ($userStatus) {
        case "Y":
                 //$userStatus = '<a class="online_msg_link" href="#">'.TEXT_ONLINE. "</a>".$chatWindow;

        $userStatus = '<a class="online_msg_link" href="Javascript:WindowPop(\'' . ucfirst($ItemUser) . '\');">'.TEXT_ONLINE. "</a>".$chatWindow;
        break;

        case "N":
        $userStatus = '<a class="offline_msg_link" style="position:relative;">'.TEXT_OFFLINE.'</a><a class="chat_offline"></a>';
        break;

        case "":
        $userStatus = '<a class="offline_msg_link" style="position:relative;">'.TEXT_OFFLINE.'</a><a class="chat_offline"></a>';
        break;
    }

         // Item Price
    $item_price='';
    $item_name='';

    if (($EnablePoint == '0' || $EnablePoint == '2') && $arr['nValue']>0) {
        $item_price .= CURRENCY_CODE . $arr['nValue'];
        $item_name.=TEXT_PRICE.'&nbsp;';

    }
    if ($EnablePoint == '2' && $arr['nValue'] > 0 && $arr['nPoint'] > 0) $item_price .= ' & '; 
    if (($EnablePoint == '1' || $EnablePoint == '2') && $arr['nPoint']>0) {
        $item_price .=  $arr['nPoint'] .' '.POINT_NAME. '';

    }
    ?>	

    <?php
    $img_list = array();
    if($var_source != "w") {

        $vurl = trim($arr["vUrl"]);
        if ($vurl != ''){

            $gallery_list = getMoreImages($arr["nSwapId"], $var_source);
            $vurl_img = str_replace('medium_', '', $vurl);

            if(@file_exists($vurl_img)) {
                $first_image = $vurl_img;
            } elseif (@file_exists($vurl)) {
                $first_image = $vurl;
            } elseif (!empty($gallery_list)) {
                $first_image = array_shift($gallery_list);
            } else{
                $first_image = "images/nophoto.gif";
            }

            // get more images
            if (!empty($gallery_list)) {
                foreach ($gallery_list as $img) {
                    $img_list[] = $img;
                }
            }
        } else{

            $gallery_list = getMoreImages($arr["nSwapId"], $var_source);
            if(!empty($gallery_list)){

                $first_image = array_shift($gallery_list);
                foreach ($gallery_list as $img) {
                    $img_list[] = $img;
                }
            } else{
                $first_image = "images/nophoto.gif"; 
            }
        }
    }
    else {

        $vurl = trim($arr["vUrl"]);
        if ($vurl != ''){

            $vurl_img = str_replace('medium_', '', $vurl);
            if(@file_exists($vurl_img)) {
                $first_image = $vurl_img;
            } elseif (@file_exists($vurl)) {
                $first_image = $vurl;
            } else{
                $first_image = "images/nophoto.gif"; 
            }
        }
    }

    ?>

    <div class="col-lg-4 col-xs-6 col-sm-4 col-md-4">
        <div class="product-more-list-tile">
            <div class="product-more-list-tile-img-sec">
                <div class="layoutmodi1 status_outer">
                    <?php echo $userStatus;?>                         
                </div>
                <span class="condition-new-label"><?php echo $arr["vCondition"]; ?></span>    
                <img src="<?php echo $var_url;?>" alt="Product">
                <div class="action-tiles">
                    <a href="<?php echo $detailfile . "&prod=p&catid=$var_catid&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=" . $var_item_desc;?>"> <i class="flaticon-shopping-cart"></i></a> 

                    <a href="#" data-toggle="modal" data-target="#detailproductModal<?php echo $i; ?>" title="Quick View" ><i class="flaticon-zoom-in"></i></a>
                    <?php echo $offMessage ; ?>
                </div>
            </div>


            <div class="product-more-list-tile-bottom-sec">
                <a href="<?php echo $detailfile . "&prod=p&catid=$var_catid&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=" . $var_item_desc;?>">
                    <h4 class="product_heading"><?php echo substr(htmlentities($arr["vTitle"]),0,25); if(strlen($arr["vTitle"])>25) { echo "..."; } ?></h4>
                    <h3><?php echo $item_price ?></h3>
                </a>

                <span><?php echo TEXT_POSTED_BY ?> <i><a href="#" tabindex="-1"><?php echo $hrf_profile;?><?php echo (strlen(trim($userData["vLoginName"]))>9)?substr(htmlentities($userData["vLoginName"]),0,9)."..":htmlentities($userData["vLoginName"]) ?></a></i>

                </span>
                    <!-- <a class="makeoffer-btn" href="swapitemdisplay.php?swapid=172&amp;source=w&amp;prod=p&amp;catid=&amp;cmbSearchType=&amp;txtSearch=&amp;cmbItemType=wish&amp;"> Make offer</a>
                    -->
                </div>

            </div>

        </div>

        <!-- ----------------- -->
        <div id="msgShow<?php echo $arr[$nShowJavaId]; ?>" class="modal sendMessagemodal"  role="dialog">

            <button type="button" class="close" data-dismiss="modal">&times;</button> 
            <div id="inlinebox<?php echo $nm ?>" class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-body">
                        <h4><?php echo SEND_MESSAGE_TEXT; ?></h4>
                        <?php echo '<form action="" method="post" name="frmMsg" id="frmMsg">'; ?>
                        <input type="hidden" name="qyery" value="txtHomeSearch=<?php echo $txtHomeSearch; ?>">
                        <input type="hidden" name="ToId" id="ToId">
                        <input type="hidden" name="btnSubAdd" id="btnSubAdd">
                        <input type="hidden" name="txtTitle" id="txtTitle">
                        <input type="hidden" name="txtMsg" id="txtMsg">

                        <div class="frm-ctrl">
                            <input type="text" name="txtTitle<?php echo $arr[$nShowJavaId]; ?>" class="form-control" value="<?php echo $txtTitle; ?>" size="50" id="txtTitle<?php echo $arr[$nShowJavaId]; ?>" placeholder="<?php echo TEXT_SUBJECT; ?>">
                        </div>

                        <div class="frm-ctrl">

                            <textarea name="txtMsg<?php echo $arr[$nShowJavaId]; ?>" cols="55" rows="8" class="form-control" id="txtMsg<?php echo $arr[$nShowJavaId]; ?>" placeholder="<?php echo TEXT_MESSAGE; ?>"><?php echo $txtMsg; ?></textarea>
                        </div>
                        <div class="frm-ctrl-btn">
                            <label>
                                <input type="button" name="btnGo" value="<?php echo BUTTON_SEND_MESSAGE; ?>" class="sendMessagemodal-btn" onClick="return ValidateMsg(<?php echo $arr[$nShowJavaId]; ?>,<?php echo $arr['swapUserid']; ?>);"><!--&nbsp;<input type="reset" name="btnGo2" value="<?php //echo BUTTON_RESET; ?>" class="submit">-->
                            </label>
                        </div>
                        <?php echo '</form>'; ?>
                    </div>				
                </div>
            </div>
        </div>

        <!--Send Mesasge Area-->
        <div id="sendMessagemodal" class="modal sendMessagemodal" role="dialog">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-body">
                        <h4>Send Message</h4>
                        <form>
                            <div class="frm-ctrl">
                                <input type="text" placeholder="Subject" required="">
                            </div>
                            <div class="frm-ctrl">
                                <textarea  placeholder="Message" required=""></textarea>
                            </div>
                            <div class="frm-ctrl-btn">
                                <button class="sendMessagemodal-btn" type="submit">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!------QUICK VIEW ---->
        <div id="detailproductModal<?php echo  $i; ?>" class="modal productdetailpopup" role="dialog">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-dialog">
             <div class="modal-content">

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-5">
                            <div class="productdetailpopup-left">
                                <div class="Product-img-container">
                                    <img id="ProductImg<?php echo  $nm; ?>" src="<?php echo $first_image;?>" data-zoom-image="<?php echo $first_image;?>"/>
                                </div>
                                <div class="product_item_gallery" id="product_item_gallery<?php echo $nm; ?>">
                                    <a class="product_gallery_item active" data-image="<?php echo $first_image;?>" data-zoom-image="<?php echo $first_image;?>" tabindex="0">
                                        <img src="<?php echo $first_image;?>" alt="product_img" data-targetid ="<?php echo  $nm; ?>">
                                    </a>

                                    <?php 
                                    if(!empty($img_list)){
                                        foreach($img_list as $key=>$image){?>
                                            <a class="product_gallery_item" data-image="<?php echo $image;?>" data-zoom-image="<?php echo $image;?>" tabindex="0">
                                                <img src="<?php echo $image;?>" alt="product_img_<?php echo $key; ?>" data-targetid ="<?php echo  $nm; ?>">
                                            </a>

                                        <?php } }?>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="productdetailpopup-right">
                                    <h3><?php echo (strlen(trim($arr["vTitle"]))>21)?strtoupper(substr(htmlentities($arr["vTitle"]),0,21))."...":strtoupper(htmlentities($arr["vTitle"]));?></h3>
                                    <div class="bottom-price">
                                        <h4><?php echo ($arr['nValue'] > 0) ? 'Price ' : '' ?><?php echo $item_price ?></h4>

                                        <span class="post-dte">Posted On <i><?php echo date('m/d/Y', strtotime($arr["dPostDate"])); ?></i></span>
                                    </div>
                                    <div class="bottom-usr">
                                        <span><?php echo TEXT_POSTED_BY ?> <i><a href="user_profile.php?uid=<?php echo $userData['nUserId'] . "&uname=" . urlencode($userData['vLoginName']); ?>" tabindex="-1"><?php echo $posted_by; ?></a></i></span>
                                    </div>
                                    <p><?php echo substr(htmlentities($arr['vDescription']),0,200);?></p>
                                    <table>
                                        <tr>
                                            <td>Category</td>
                                            <td><?php echo  $arr["vCategoryDesc"] ? $arr["vCategoryDesc"] : '--'; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Brand</td>
                                            <td><?php echo  $arr["vBrand"] ? $arr["vBrand"] : '--'; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Type</td>
                                            <td><?php echo  $arr["vType"] ? $arr["vType"] : '--'; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Condition</td>
                                            <td><?php echo  $arr["vCondition"] ? $arr["vCondition"] : '--'; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Year</td>
                                            <td><?php echo  $arr["vYear"] ? $arr["vYear"] : '--'; ?></td>
                                        </tr>
                                    </table>
                                    <!--  <a class="make-offer-btn" href="#">Buy Now</a> -->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>


<?php
$i++;
}
?>
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
<?php 
} else { ?>
    <div class="product_list_item" style="border: 0">
        <div class="no-search-result">
            <div>
               <img class="img-responsive" src="<?php echo SITE_URL?>/images/no-product-found.svg"  alt="no results found"  >
               <span><?php echo ERROR_SORRY_NO_ITEMS_TO_DISPLAY;?></span>
           </div>
       </div>

   </div>
   <?php
}
?>			
</form>

</div>
<?php 
if($_REQUEST["txtSearchRadius"] && trim($_REQUEST["txtSearchRadius"])){
    ?>
    <div><a href="" id="clearSeachRadius" class="product_heading" ><?php echo TEXT_VIEW_SIMILAR_PRODUCTS; ?></a>
        <?php
    }
    ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../libs/jquery/dist/jquery.min.js"><\/script>')</script>
    <script src="js/jquery.remodal.js"></script>

    <!-- Events -->
    <script>
      $(document).on("open", ".remodal", function () {
        console.log("open");
    });

      $(document).on("opened", ".remodal", function () {
        console.log("opened");
    });

      $(document).on("close", ".remodal", function (e) {
        console.log('close' + (e.reason ? ", reason: " + e.reason : ''));
    });

      $(document).on("closed", ".remodal", function (e) {
        console.log('closed' + (e.reason ? ', reason: ' + e.reason : ''));
    });

      $(document).on("confirm", ".remodal", function () {
        console.log("confirm");
    });

      $(document).on("cancel", ".remodal", function () {
        console.log("cancel");
    });

      function removeParam(key, sourceURL) {
        var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }
            rtn = rtn + "?" + params_arr.join("&");
        }
        return rtn;
    }
    $("#clearSeachRadius").click(function(e){
      e.preventDefault();
      var alteredUrl = removeParam("txtSearchRadius", window.location.href);
      window.location = alteredUrl;

  });

//  You can open or close it like this:
//  $(function () {
//    var inst = $.remodal.lookup[$("[data-remodal-id=modal]"").data("remodal")];
//    inst.open();
//    inst.close();
//  });

  //  Or init in this way:
  var inst = $("[data-remodal-id=modal2]").remodal();
  //  inst.open();
  
  
</script>
