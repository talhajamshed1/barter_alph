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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
//include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

if ($_GET['source'] == 'sa') {
    //checking sale module status
    $sqlSale = mysqli_query($conn, "SELECT vActive FROM " . TABLEPREFIX . "client_module_category where nCategoryId='2' and vActive='1'") or die(mysqli_error($conn));
    switch (mysqli_num_rows($sqlSale)) {
        case "0":
        header('location:usermain.php');
        exit();
        break;
    }//end switch
}//end if

include_once('./includes/title.php');
?>



<script language="javascript" type="text/javascript">
    function movepic(img_thumb_src,img_src,img_desc) {

        //document.getElementById('itemImage').src='show_merge_image.php?vImg='+img_thumb_src;
        document.getElementById('itemImage').src=''+img_src;
        document.getElementById('itemImagea').href =img_src;
        //document.getElementById('itemImagev').href =img_src;
        document.getElementById('itemImagea').title =img_desc;
        //document.getElementById('itemImagev').title =img_desc;
    }

    function quoteAndEscape(str) {

        return ''
            + '&#39;'                      // open quote '
            + (''+str)                     // force string
        .replace(/\\/g, '\\\\')    // double \
        .replace(/"/g, '\\&quot;') // encode "
        .replace(/'/g, '\\&#39;')  // encode '
        + '&#39;';           // close quote '

    }
</script>



<script type="text/javascript" src="./js/prototype.js"></script>
<script type="text/javascript" src="./js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="./js/lightbox.js"></script>
<link rel="stylesheet" href="./styles/lightbox.css" type="text/css" media="screen" />

<script type="text/javascript">
    function setDisplay(id) {
        var pd_detail = document.getElementById("pd_detail");
        var pd_description = document.getElementById("pd_description");

        var s1 = document.getElementById("s1");
        var s2 = document.getElementById("s2");

        pd_detail.style.display = "none";
        s1.className = "";
        pd_description.style.display = "none";
        s2.className = "";

        if(id=="pd_detail"){
            pd_detail.style.display = "block";
            s1.className = "selected";
        } else if(id=="pd_description"){
            pd_description.style.display = "block";
            s2.className = "selected";
        }

    }
</script>



<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php'); ?>

    <script language="Javascript">
        $jqr(document).ready(function() {
            $jqr("#btn_buy").click(function(event){
                event.preventDefault();
                var txtPrice    =   $jqr('#txtPrice').val();
                var txtPoint    =   $jqr('#txtPoint').val();
                var swapid      =   $jqr('#swap_id').val();
                var txtQty      =   $jqr('#txtQty').val();
                var txtsource   =   $jqr('#txtsource').val();

                if(txtPoint!=1 && txtPrice==0)
                {
                    alert('<?php echo TEXT_NO_PRICE_SELLER; ?>');
                    return false;

                }else if(txtsource=="sa" && txtQty<=0 ){
                    alert('<?php echo ITEM_LESS_QTY; ?>');
                    return false;
                }
                else{
                    window.location.href='buy.php?saleid='+swapid+'&source=sa';
                }

            }); 
        });
    </script>


    <!--  -->
    <div class="homepage_contentsec">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ol class="breadcrumb" style="display: block;">
                        <?php echo getBreadCrumbs("categorydetail.php", addslashes($var_category_id)); ?>
                    </ol>
                </div>
                <div class="product-detail-section">
                    <?php
                    $var_post_type = "";
                    $var_mesg = "";
                    $ref_file = "";
                    $ref_file2 = "";
                    $sql = "";
                    $var_url = "";
                    $var_url_small = "";
                    $var_swapid = "";
                    $var_source = "";
                    $var_category_desc = "";
                    $var_category_id = "";
                    $var_post_date = "";
                    $var_title = "";
                    $var_brand = "";
                    $var_type = "";
                    $var_condition = "";
                    $var_year = "";
                    $var_value = "";
                    $var_point = "";
                    $var_shipping = "";
                    $var_description = "";
                    $var_quantity = 0;
                    $var_command = "";
                    $var_user_name = "";
                    $var_user_id = "";
                    $var_title_url = "";
                    $var_img_desc = "";
                    $checkSource = ($_GET["source"] != '') ? $_GET["source"] : $_POST["source"];

                    if ($_GET["source"] == "s") {
                        //fetch seller id
                        $sellerId = fetchSingleValue(select_rows(TABLEPREFIX . 'swap', 'nUserId', "where nSwapId='" . $_GET["swapid"] . "'"), 'nUserId');

                        //seller produt id array
                        $sellerProductArray = array();

                        //create seller product array
                        $selleProd = mysqli_query($conn, "SELECT nSwapId FROM " . TABLEPREFIX . "swap WHERE nUserId='" . $sellerId . "'") or die(mysqli_error($conn));

                        if (mysqli_num_rows($selleProd) > 0) {
                            while ($arrP = mysqli_fetch_array($selleProd)) {
                                $sellerProductArray[] = $arrP['nSwapId'];
                            }//end while loop
                        }//end if
                        $sellerProductArray = join($sellerProductArray, ",");

                        $userAllowPostFeedback = '';
                        if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
                            //checking atleast one post against this
                            $cndPost = "where nUserId='" . $_SESSION["guserid"] . "' AND nSwapId IN (" . $sellerProductArray . ") LIMIT 0,1";
                            $userAllowPostFeedback = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'nUserId', $cndPost), 'nUserId');
                        }//end if
                        //set the target file links to userwish
                        $ref_file = "swaplistdetailed.php";

                        //set the sql to retreive data from swap table where vPostType=wish
                        $sql = "select S.nSwapId,S.nCategoryId,
                        L.vCategoryDesc,S.nUserId,S.vPostType,
                        vLoginName as UserName,
                        S.vTitle,S.vBrand,S.vType,S.vCondition,
                        S.vYear,S.nValue,S.nShipping,S.vUrl,S.vDescription,date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
                        S.vImgDes,S.vSmlImg,S.nPoint from
                        " . TABLEPREFIX . "swap S
                        left join " . TABLEPREFIX . "users U on S.nUserId = U.nUserId
                        left join " . TABLEPREFIX . "category C on S.nCategoryId = C.nCategoryId
                        LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                        where  
                        nSwapId = '" . addslashes($_GET["swapid"]) . "'"
                        . " AND S.vDelStatus = '0' AND U.vStatus='0'";


                        $gTableId = 'nSwapId';
                        $gCheckCond = " and nSaleId='0'";
                    }//end if
                    else if ($_GET["source"] == "w") {
                        //fetch seller id
                        $sellerId = fetchSingleValue(select_rows(TABLEPREFIX . 'swap', 'nUserId', "where nSwapId='" . $_GET["swapid"] . "'"), 'nUserId');

                        //seller produt id array
                        $sellerProductArray = array();

                        //create seller product array
                        $selleProd = mysqli_query($conn, "SELECT nSwapId FROM " . TABLEPREFIX . "swap WHERE nUserId='" . $sellerId . "'") or die(mysqli_error($conn));

                        if (mysqli_num_rows($selleProd) > 0) {
                            while ($arrP = mysqli_fetch_array($selleProd)) {
                                $sellerProductArray[] = $arrP['nSwapId'];
                            }//end while loop
                        }//end if
                        $sellerProductArray = join($sellerProductArray, ",");

                        $userAllowPostFeedback = '';
                        if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
                            //checking atleast one post against this
                            $cndPost = "where nUserId='" . $_SESSION["guserid"] . "' AND nSwapId IN (" . $sellerProductArray . ") LIMIT 0,1";
                            $userAllowPostFeedback = fetchSingleValue(select_rows(TABLEPREFIX . 'swaptxn', 'nUserId', $cndPost), 'nUserId');
                        }//end if
                        //set the target file links to userwish
                        $ref_file = "wishlistdetailed.php";

                        //set the sql to retreive data from swap table where vPostType=wish
                        $sql = "select nSwapId,S.nCategoryId,
                        L.vCategoryDesc,S.nUserId,S.vPostType,'0' as 'nQuantity',
                        vLoginName as UserName,
                        S.vTitle,S.vBrand,S.vType,S.vCondition,
                        S.vYear,S.nValue,S.nShipping,S.vUrl,S.vDescription,date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
                        S.vImgDes,S.vSmlImg,S.nPoint from
                        " . TABLEPREFIX . "swap S
                        left join " . TABLEPREFIX . "users U on S.nUserId = U.nUserId
                        left join " . TABLEPREFIX . "category C on S.nCategoryId = C.nCategoryId
                        LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                        where  
                        nSwapId = '" . addslashes($_GET["swapid"]) . "'"
                        . " AND S.vDelStatus = '0' AND U.vStatus='0'";

                        $gTableId = 'nSwapId';
                        $gCheckCond = " and nSaleId='0'";
                    }//end else if
                    else if ($_GET["source"] == "sa") {
                        //fetch seller id
                        $sellerId = fetchSingleValue(select_rows(TABLEPREFIX . 'sale', 'nUserId', "where nSaleId='" . $_GET["saleid"] . "'"), 'nUserId');

                        //seller produt id array
                        $sellerProductArray = array();

                        //create seller product array
                        $selleProd = mysqli_query($conn, "SELECT nSaleId FROM " . TABLEPREFIX . "sale WHERE nUserId='" . $sellerId . "'") or die(mysqli_error($conn));

                        if (mysqli_num_rows($selleProd) > 0) {
                            while ($arrP = mysqli_fetch_array($selleProd)) {
                                $sellerProductArray[] = $arrP['nSaleId'];
                            }//end while loop
                        }//end if
                        $sellerProductArray = join($sellerProductArray, ",");

                        $userAllowPostFeedback = '';
                        if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
                            //checking atleast one post against this
                            $cndPost = "where nUserId='" . $_SESSION["guserid"] . "' AND nSaleId IN (" . $sellerProductArray . ") LIMIT 0,1";
                            $userAllowPostFeedback = fetchSingleValue(select_rows(TABLEPREFIX . 'saledetails', 'nUserId', $cndPost), 'nUserId');
                        }//end if
                        //set the target file links to usersale
                        $ref_file = "salelistdetailed.php";

                        //set the sql to retreive data from sale table
                        $sql = "select nSaleId as 'nSwapId',S.nCategoryId,
                        L.vCategoryDesc,S.nUserId,'sale' as 'vPostType',
                        vLoginName as UserName,
                        S.vTitle,S.vBrand,S.vType,S.vCondition,S.nQuantity,
                        S.vYear,S.nValue,S.nShipping,S.vUrl,S.vDescription,date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
                        S.vImgDes,S.vSmlImg, S.nPoint from
                        " . TABLEPREFIX . "sale S
                        left join " . TABLEPREFIX . "users U on S.nUserId = U.nUserId
                        left join " . TABLEPREFIX . "category C on S.nCategoryId = C.nCategoryId
                        LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                        where  
                        nSaleId = '" . addslashes($_GET["saleid"]) . "'"
                        . " AND S.vDelStatus = '0' AND U.vStatus='0' ";


                        $gTableId = 'nSaleId';
                        $gCheckCond = " and nSwapId='0'";
                    }//end else if


                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result) != 0) {
                        if ($row = mysqli_fetch_array($result)) {
                            if ($row["vPostType"] == "swap") {
                                $var_post_type = 'Swap Item';
                                $var_title_url = HEADING_ITEM_DETAILS;
                            }//end if
                            else if ($row["vPostType"] == "wish") {
                                $var_post_type = 'Wish Item';
                                $var_title_url = HEADING_ITEM_WISHED;
                            }//end else if
                            else if ($row["vPostType"] == "sale") {
                                $var_post_type = 'Item For Sale';
                                $var_title_url = HEADING_ITEM_DETAILS;
                            }//end else if

                            $var_post_date = $row["dPostDate"];
                            $var_swapid = $row["nSwapId"];
                            $var_source = $_GET["source"];
                            $var_url = $row["vUrl"];
                            if ($row["vUrl"] != '') {

                                $var_large_image = str_replace('medium_', '', $row["vUrl"]);
                                if (@file_exists($var_large_image)) { 
                                    $var_url = $var_large_image;
                                }

                                $var_image_original = str_replace('medium_', '', $row["vUrl"]);

                                if (@file_exists($var_image_original)) {
                                    $var_original_image_name = $var_image_original;
                                } else {

                                    $var_original_image_name = $row["vUrl"];
                                }
                            }



                            $var_url_small = $row["vSmlImg"];
                            $var_category_desc = $row["vCategoryDesc"];
                            $var_category_id = $row["nCategoryId"];
                            $var_title = $row["vTitle"];
                            $var_brand = $row["vBrand"];
                            $var_type = $row["vType"];
                            $var_condition = $row["vCondition"];
                            $var_year = $row["vYear"];
                            $var_value = $row["nValue"];
                            $var_point = $row["nPoint"];
                            $var_shipping = $row["nShipping"];
                            $var_quantity = $row["nQuantity"];
                            $var_description = $row["vDescription"];
                            $var_command = "";
                            $var_user_id = $row["nUserId"];
                            $var_user_name = $row["UserName"];
                            $ref_file2 = $ref_file . "?rf=sid&no=" . $var_user_id;
                            $var_img_desc = $row["vImgDes"];


                            /*
                             * Check if the logged in user has don any transaction(sale/swap) with this item owner
                             */
                            if ($_SESSION["guserid"] != '') {
                                $itemOwner = $var_user_id;
                                $transactionStatus = getTransactionStatus($itemOwner);
                            } else {
                                $transactionStatus = false;
                            }

                            //checking user status
                            $condition = "where nUserId='" . $row['nUserId'] . "'";
                            $userStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'online', 'vVisible', $condition), 'vVisible');

                            //if posted user not logged user
                            if ($row['nUserId'] != $_SESSION["guserid"]) {
                                $chatWindow = '<a href="javascript:WindowPop(\'' . ucfirst($row['nUserId']) . '\');">
                                <span class="glyphicon glyphicon-comment"></span>'
                                . LINK_CHAT_WITH . ' ' . ucfirst($row['UserName']) . '</a>';
                            }//end if
                            else {
                                $chatWindow = '';
                            }//end else

                            switch ($userStatus) {
                                case "Y":
                                $userStatus = $chatWindow . '<img src="./themes/Antique/online_icon.png">';
                                break;

                                case "N":
                                $userStatus = TEXT_OFFLINE ;
                                break;

                                case "":
                                $userStatus =  TEXT_OFFLINE ;
                                break;
                            }//end switch
                        }//end if
                    }//end if

                    if ($_GET["prod"] == "p") {
                        $ref_file = "catwiseproducts.php?catid=" . $_GET["catid"]
                        . "&cmbSearchType=" . $_GET["cmbSearchType"] . "&txtSearch="
                        . urlencode($_GET["txtSearch"]) . "&cmbItemType="
                        . $_GET["cmbItemType"];
                    }//end if

                    $ref_file2 = "home.php?uid=" . $var_user_id . "&uname=" . urlencode($var_user_name) . "&display=" . urlencode('show');

                    if (trim($_GET['catid']) != '') {
                        $showCatId = $_GET['catid'];
                    }//end if
                    else {
                        $showCatId = $var_category_id;
                    }//end else
                    ?>


                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        ?>
                        <div class="container">
                            <div class="row">
                                <?php
                                if (trim($var_url) == "" || !file_exists($var_url)) {
                                    $var_url = "pics/nophoto_big.jpg";
                                }//endif

                                if ($_GET['source'] != 'w') {

                                    if (trim($var_img_desc) != '') {
                                        if (strlen($var_img_desc) > 100)
                                            $var_img_desc = substr($var_img_desc, 0, strrpos(substr($var_img_desc, 0, 64), ' '));
                                        else
                                            $var_img_desc = $var_img_desc;
                                    }//end if

                                    $chkId = ($_GET["swapid"] != '') ? $_GET["swapid"] : $_GET["saleid"];
                                    $arrImages = mysqli_query($conn, "select * from " . TABLEPREFIX . "gallery where $gTableId='" . addslashes($chkId) . "' and vImg!='' and vDelStatus='0' " . $gCheckCond) or die(mysqli_error($conn));
                                    $noImages  = mysqli_num_rows($arrImages);

                                    if($noImages>0)
                                    {
                                        $imageArray = array();       
                                        while ($arr = mysqli_fetch_array($arrImages)) {
                                            $imageArray[] = $arr;
                                        }

                                    }
                                    ?>

                                    <div class="col-md-5 col-sm-4">
                                        <div class="productdetailpopup-left zoom-gallery">
                                            <div class="Product-img-container ">      
                                                <?php 
                                                $first_image = "";

                                                if ($var_original_image_name){ 
                                                    ?> 

                                                    <i class="expand-btn  "><img src="<?php echo SITE_URL?>/images/expand-ic.svg"></i>

                                                    <a title="<?php echo $var_img_desc; ?>" class="zoom" href="<?php echo $var_original_image_name ?>">
                                                        <img id="ProductImg-detail" src="<?php echo $var_original_image_name ?>" data-zoom-image="<?php echo $var_original_image_name ?>"/>
                                                    </a>
                                                    <?php 
                                                    $first_image = $var_original_image_name;
                                                } else if (!$var_original_image_name && $noImages>0) {  

                                                    $firstImage =  $imageArray[0]; 

                                                    $var_more_image_original = str_replace('large_', '', $firstImage["vImg"]);

                                                    if (@file_exists($var_more_image_original)) {
                                                        $var_more_original_image_name = $var_more_image_original;
                                                    } else {
                                                        $var_more_original_image_name = $arr["vImg"];
                                                    }
                                                    $first_image = $var_more_original_image_name;
                                                    ?>
                                                    <!--<a href="<?php //echo $var_more_original_image_name; ?>" rel="lightbox" id="itemImagea" title="<?php //echo $firstImage["vDes"]; ?>"> <img src="<?php //echo $var_more_original_image_name ?>" id="itemImage"/></a> -->
                                                    <i class="expand-btn  "><img src="<?php echo SITE_URL?>/images/expand-ic.svg"></i>
                                                    <a title="<?php echo $firstImage["vDes"]; ?>" class="zoom" href="<?php echo $var_more_original_image_name ?>">
                                                        <img id="ProductImg-detail" src="<?php echo $var_more_original_image_name ?>" data-zoom-image="<?php echo $var_more_original_image_name ?>"/>
                                                    </a>

                                                    <?php 
                                                } else { 
                                                    ?>
                                                    <i class="expand-btn  "><img src="<?php echo SITE_URL?>/images/expand-ic.svg"></i>
                                                    <a title="No Image" class="zoom" href="pics/nophoto_big.jpg">
                                                        <img id="ProductImg-detail" src="pics/nophoto_big.jpg" data-zoom-image="pics/nophoto_big.jpg"/>
                                                    </a>
                                                    <!-- <a href="#" rel="lightbox" id="itemImagea" title="No Image"> <img src="pics/nophoto_big.jpg" id="itemImage"/></a> -->
                                                    <?php 
                                                } ?>
                                            </div>                                           



                                            <!---- error ---->

                                            <div id="product_item_gallery-detail">
                                                <?php 
                                                if ($var_original_image_name) { 
                                                    ?>
                                                    <div class="item">
                                                        <a title="<?php echo htmlentities($string); ?>" href="<?php echo $first_image; ?>" class="product_gallery_item active" data-image="<?php echo $first_image; ?>" data-zoom-image="<?php echo $first_image; ?>" tabindex="0">
                                                            <img src="<?php echo $first_image; ?>" alt="product_small_img1">
                                                        </a>
                                                    </div>
                                                    <?php 
                                                }
                                                ?>

                                                <?php 
                                                if ($noImages > 0) {          
                                                    foreach($imageArray as $key=>$val){

                                                        if ($val["vImg"] != '') {
                                                            $var_more_image_original = str_replace('large_', '', $val["vImg"]);

                                                            if (@file_exists($var_more_image_original)) {

                                                                $var_more_original_image_name = $var_more_image_original;
                                                            } else {

                                                                $var_more_original_image_name = $val["vImg"];
                                                            }
                                                        }

                                                        $string = addslashes(preg_replace("/[\r\n]+/", ' ', (preg_replace('/\s\s+/', ' ', $val['vDes']))));
                                                        ?>


                                                        <div class="item">

                                                            <a title="<?php echo htmlentities($string); ?>" href="<?php echo $var_more_original_image_name; ?>" class="product_gallery_item " data-image="<?php echo $var_more_original_image_name; ?>" data-zoom-image="<?php echo $var_more_original_image_name; ?>" tabindex="0">
                                                                <img src="<?php echo $var_more_original_image_name; ?>" alt="product_small_img1">
                                                            </a>
                                                        </div>

                                                        <?php

                                                    }//end while
                                                    ?>
                                                    <?php 
                                                } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                } 
                                else {
                                    ?>
                                    <div class="col-md-5 col-sm-4">
                                        <div class="productdetailpopup-left zoom-gallery">
                                            <div class="Product-img-container "> 
                                                <i class="expand-btn  "><img src="<?php echo SITE_URL?>/images/expand-ic.svg"></i>
                                                <a title="<?php echo $var_description; ?>" class="zoom" href="<?php echo $var_url ?>">
                                                    <img id="ProductImg-detail" src="<?php echo $var_url ?>" data-zoom-image="<?php echo $var_url ?>"/>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>


                                <div class="col-md-7 col-sm-8 ">
                                    <div class="productdetailpopup-right">

                                        <h3><?php echo htmlentities($var_title); ?></h3>
                                       <!--  <span class="user-status"><?php echo $userStatus ?></span> -->
                                        <input type="hidden" name="txtPrice" id="txtPrice" value="<?php echo $var_value; ?>">
                                        <input type="hidden" name="txtEnablePoint" id="txtEnablePoint" value="<?php echo $EnablePoint; ?>">
                                        <input type="hidden" name="swap_id" id="swap_id" value="<?php echo $var_swapid; ?>">
                                        <input type="hidden" name="txtQty" id="txtQty" value="<?php echo $var_quantity; ?>">
                                        <input type="hidden" name="txtsource" id="txtsource" value="<?php echo $_REQUEST['source']; ?>">

                                        <?php 
                                        if ($checkSource != "w") {

                                            if ($EnablePoint != '1') {
                                                $price = CURRENCY_CODE . $var_value;
                                                ?>                                      
                                                <div class="bottom-price">
                                                    <h4><?php echo $price; ?></h4>
                                                    <?php if ($checkSource != "w") { ?>
                                                    <span class="ship-price"><?php echo TEXT_SHIPPING; ?> <i><?php echo CURRENCY_CODE; ?><?php echo ($var_shipping != "") ? $var_shipping : 0; ?></i></span>
                                                    <?php } ?>
                                                    <span class="post-dte"><?php echo TEXT_POSTED_ON; ?> <i><?php echo date('m/d/Y', strtotime($var_post_date)); ?></i></span>
                                                </div>

                                                <?php
                                            }

                                            if ($EnablePoint != '0' && $var_point > 0) {
                                                $points = $var_point;
                                                ?>

                                                <div class="bottom-price">
                                                    <h4><?php echo POINT_NAME." ".$points; ?></h4>
                                                    <!--span class="ship-price"><?php //echo TEXT_SHIPPING; ?> <i><?php //echo CURRENCY_CODE; ?><?php //echo ($var_shipping != "") ? $var_shipping : 0; ?></i></span> -->
                                                    <span class="post-dte"><?php echo TEXT_POSTED_ON; ?> <i><?php echo date('m/d/Y', strtotime($var_post_date)); ?></i></span>

                                                </div>

                                                <?php 
                                            } 
                                        }?>

                                        <div class="bottom-usr">
                                            <span><?php echo TEXT_POSTED_BY; ?>
                                                <i>
                                                    <?php if ($transactionStatus) { 
                                                        ?>
                                                        <a  href="user_profile.php?uid=<?php echo $var_user_id . "&uname=" . urlencode($var_user_name); ?>">
                                                            <?php 
                                                        } ?>
                                                        <?php echo htmlentities($var_user_name); ?></i>
                                                    </a>
                                                </span>

<!--                                                <div class="rating-usr">
                                                    <i class="flaticon-star"></i>
                                                    <i class="flaticon-star"></i>
                                                    <i class="flaticon-star"></i>
                                                    <i class="flaticon-star-1"></i>
                                                    <i class="flaticon-star-1"></i>
                                                </div>-->

                                            <!-- <?php if ($transactionStatus) { 
                                                ?>
                                                <a class="reviews-a" href="user_profile.php?uid=<?php echo $var_user_id . "&uname=" . urlencode($var_user_name); ?>"><?php echo LINK_VIEW_PROFILE; ?></a>
                                                <?php 
                                            } ?> -->

                                            <?php
                                                //checking at least one post agains this user
                                            if (trim($userAllowPostFeedback) != '') {
                                                if ($_GET['source'] == 'sa') {
                                                    $passId = $_GET['saleid'];
                                                }
                                                else {
                                                    $passId = $_GET['swapid'];
                                                }//end else
                                                ?>                              <a class="reviews-a" href="userfeedback.php?uid=<?php echo $var_user_id . '&uname=' . urlencode($var_user_name) . '&nId=' . $passId . '&source=' . $_GET['source']; ?>"><?php echo LINK_POST_FEEDBACK; ?></a> 

                                                <?php 
                                            } ?>
                                        </div>

                                        <?php 
                                        $desc = $var_description;
                                        ?>

                                        <p><?php echo nl2br($desc);?></p>
                                        <table>
                                            <tr>
                                                <td><?php echo TEXT_CATEGORY; ?></td>
                                                <td><?php echo htmlentities($var_category_desc); ?></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo TEXT_BRAND; ?></td>
                                                <td><?php echo  $var_brand ? htmlentities($var_brand) : '--'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo TEXT_TYPE; ?></td>
                                                <td><?php echo $var_type ? htmlentities($var_type) : '--'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo TEXT_CONDITION; ?></td>
                                                <td><?php echo $var_condition ? htmlentities($var_condition) : '--'; ?></td>
                                            </tr>
                                            <?php if ($checkSource != "w") { ?> 
                                            <tr>
                                                <td><?php echo TEXT_YEAR; ?></td>
                                                <td><?php echo $var_year; ?></td> 

                                            </tr>
                                            <?php } ?>
                                            <?php if ($_GET["source"] == "sa" || $_POST["source"] == "sa") { ?>
                                            <tr>
                                                <td><?php echo TEXT_QUANTITY; ?></td>
                                                <td><?php echo $var_quantity; ?></td> 

                                            </tr>
                                            <?php } ?>

                                        </table> 



                                        <!--main action button-->
                                        <?php
                                        $sql = "Select nSwapId,vTitle,nValue,nPoint from " . TABLEPREFIX . "swap where (vSwapStatus= '0' or '" . $mode . "' = 'view')
                                        AND (vDelStatus='0' or 'add' = 'view') AND vPostType='swap' AND
                                        nUserId = '" . $_SESSION['guserid'] . "'
                                        ORDER BY vTitle ASC ";
                                        $rs = mysqli_query($conn, $sql);
                                        if (mysqli_num_rows($rs) < 1) {
                                            $onclick = (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != "") ? "alert('You have no items to swap. Please add a swap item');" : "$('#login-modal').modal('show');
";
                                        } else {
                                            $onclick = "document.location.href='makeoffer.php?userid=$var_user_id&swapid=$var_swapid";
                                            if ($_GET["source"] == "s") {
                                                $onclick = $onclick . "&post_type=swap';";
                                            } else if ($_GET["source"] == "w") {
                                                $onclick = $onclick . "&post_type=wish';";
                                            }
                                        }
                                        if ($_GET["source"] == "sa") {
                                            if ($var_quantity > 0) { ?>
                                                 <div class="clearfix">
                                             <div class="user-status"><?php echo $userStatus ?></div>
                                         </div>
                                            <input type="submit" name="btSale" class="make-offer-btn" id="btn_buy" value=" <?php echo BUTTON_BUY ?> " />

                                            <?php  }//end if
                                            else {
                                                echo MESSAGE_NO_QUANTITY_FOR_PURCHASE;
                                            }//end else$var_swapid
                                        }//end if
                                        else if ($_GET["source"] == "s") {
                                            ?>
                                            <div class="clearfix">
                                            <div class="user-status"><?php echo $userStatus ?></div>
                                        </div>
                                            <input type="button" class="make-offer-btn" name="btOffer" value="<?php echo BUTTON_MAKE_OFFER; ?>" onClick="<?php echo $onclick; ?>">
                                         
                                            <?php
                                        }//end else if
                                        else if ($_GET["source"] == "w") {
                                            ?>
                                             <div class="clearfix">
                                            <div class="user-status"><?php echo $userStatus ?></div>
                                        </div>
                                            <input type="button" class="make-offer-btn" name="btOffer" value="<?php echo BUTTON_MAKE_OFFER; ?>" onClick="<?php echo $onclick; ?>">
                                          
                                            <?php
                                        }//end else if
                                        ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <?php  
                    }
                    else {
                        ?>
                        <div><?php echo MESSAGE_SORRY_NO_RECORDS; ?></div>
                        <?php
                    }//end else 
                    ?>
                </div>
                <div class=" ">
                    <div class="col-lg-12">
                        <div class="pr_deatails_xtralinks">
                            
                            <a href="<?php echo $ref_file ?>"> <span class="glyphicon glyphicon-tasks"></span> <?php echo HEADING_ITEM_LIST; ?></a>


                         <a href="<?php echo $ref_file2 ?>"> <span class="glyphicon glyphicon-th-list"></span> <?php echo HEADING_POSTINGS_OF . ' ' . htmlentities($var_user_name) ?></a> 
                         
                     </div>
                 </div>
             </div>
         </div>
         <br><br>
         <div class="item-slider-section">
             <div class="row">
               <?php 
               require_once("./includes/related_items.php");
               ?>
           </div>
       </div> 
   </div>
</div>

<?php require_once("./includes/footer.php"); ?>
