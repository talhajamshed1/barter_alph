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
//send mesage
if (isset($_POST['btnSubAdd']) && $_POST['btnSubAdd'] != '') {
    if (function_exists('get_magic_quotes_gpc')) {
        $txtTitle = stripslashes($_POST['txtTitle']);
        $txtMsg = stripslashes($_POST['txtMsg']);
    }//end if
    else {
        $txtTitle = $_POST['txtTitle'];
        $txtMsg = $_POST['txtMsg'];
    }//end else

    $pquery = ($_POST['qyery'] != '') ? $pquery = '?' . $_POST['qyery'] : $pquery = '';

    //insert into tbl message
    mysqli_query($conn, "insert into " . TABLEPREFIX . "Messages (nToUserId,nFromUserId,vTitle,vMsg,vStatus,nDate)
						values ('" . $_POST['ToId'] . "','" . $_SESSION["guserid"] . "','" . addslashes($txtTitle) . "','" . addslashes($txtMsg) . "',
						'N',now())") or die(mysqli_error($conn));
    $_SESSION['succ_msg'] = MESSAGE_MESSAGE_SENT_SUCCESSFULLY;
    $redirect_url = $_SERVER['HTTP_REFERER'];
	
    header('location:' . $redirect_url);
    //header('location:./catwiseproducts.php' . $pquery);
}//end if


$txtSearch = "";
$cmbSearchType = "";
$var_catid = "";
$var_item_desc = "";
$var_item_type = 1;
$var_source = "";
$sql = "";
$detailfile = "swapitemdisplay.php";


$var_catid = "";

$var_catid      = $_REQUEST["catid"];
$var_item_desc  = $_REQUEST["cmbItemType"];

if($var_item_desc=='')
{
    $var_item_desc  = 'swap';
}
//$var_catid = ($_GET["catid"] != "") ? $_GET["catid"] : (($_POST["catid"] != "") ? $_POST["catid"] : "");
//$var_item_desc = ($_POST["cmbItemType"] != "") ? $_POST["cmbItemType"] : (($_GET["cmbItemType"] != "") ? $_GET["cmbItemType"] : "");
$var_item_type = ($var_item_desc == "sale") ? 3 : (($var_item_desc == "wish") ? 2 : 1);

 if ($_REQUEST["txtSearch"] != "") {
    $txtSearch = $_REQUEST["txtSearch"];
    $cmbSearchType = $_REQUEST["cmbSearchType"];
}//end else if
 $cmbSearchType = $_REQUEST["cmbSearchType"];
$title = HEADING_PRODUCTS_PER_CART_DIARY;
$qryopt = "";


if ($txtSearch != "") {
    $txtSearch  =    strip_tags($txtSearch);
    $txtSearch  =   trim($txtSearch);
    
    if($cmbSearchType=='')
    {
        $cmbSearchType  = TEXT_CATEGORY;
    }
    
    if ($cmbSearchType == TEXT_CATEGORY) {
       $qryopt .= " AND L.vCategoryDesc like '%" . addslashes($txtSearch) . "%' ";
    }//end if
    else if ($cmbSearchType == TEXT_TITLE) {
        $qryopt .= " AND S.vTitle like '%" . addslashes($txtSearch) . "%' ";
    }//end else if
    else if ($cmbSearchType == TEXT_DESCRIPTION) {
        $qryopt .= " AND S.vDescription like '%" . addslashes($txtSearch) . "%'";
    }//end else if
    else if ($cmbSearchType == TEXT_BRAND) {
        $qryopt .= " AND S.vBrand like '%" . addslashes($txtSearch) . "%'";
    }//end else if
    else if ($cmbSearchType == TEXT_CONDITION) {
        $qryopt .= "  AND S.vCondition like '" . addslashes($txtSearch) . "%'";
    }//end else if
    else if ($cmbSearchType == TEXT_SELLER_NAME) {
        $qryopt .= "  AND u.vLoginName like '%" . addslashes($txtSearch) . "%'";
    }//end else if
}//end if
if ($fid > 0) {
    $qryopt .= " AND u.nUserId = '" . addslashes($fid) . "' ";
}//end if
//echo $var_item_desc;

if ($var_item_desc == "swap" || $var_item_desc == "") {
    $var_item_type = 1;
    $sql = "SELECT S.nSwapId,L.vCategoryDesc,S.vTitle,S.nPoint,
                                                date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',S.vFeatured,S.vUrl,
                                                                S.nUserId,S.nValue,S.vDescription,S.vBrand,S.vType,S.vCondition,S.vYear,S.vSmlImg FROM
                                                 " . TABLEPREFIX . "swap S Left outer join " . TABLEPREFIX . "category C on
                                                 S.nCategoryId = C.nCategoryId
                                                 LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                 LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=S.nUserId
                                                  where (S.nCategoryId  = '" . addslashes($var_catid) . "' or '" . addslashes($var_catid) . "' = '')
                                                  AND S.vDelStatus = '0' AND S.vPostType='swap' AND u.vStatus='0' and u.vDelStatus = '0' and (u.nUserId <> '".$_SESSION['guserid']."' or '".$_SESSION['guserid']."' = '".$fid."') ";


    $sql .=$qryopt . " ORDER BY S.vFeatured DESC,S.dPostDate DESC  ";
    $totalrows = mysqli_num_rows(mysqli_query($conn, $sql));
//                                    if ($totalrows<=0 && trim($_REQUEST["cmbItemType"])=='') $var_item_desc = "wish";
}//end if
if ($var_item_desc == "wish") {
    $var_item_type = 2;
    $sql = "SELECT S.nSwapId,L.vCategoryDesc,S.vTitle,S.nPoint,
                                                date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',S.vFeatured,S.vUrl,
                                                                        S.nUserId,S.nValue,S.vDescription,S.vBrand,S.vType,S.vCondition,S.vYear,S.vSmlImg FROM
                                                " . TABLEPREFIX . "swap S Left outer join " . TABLEPREFIX . "category C on
                                                S.nCategoryId = C.nCategoryId
                                                LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                        LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=S.nUserId
                                                where (S.nCategoryId  = '" . addslashes($var_catid) . "' or '" . addslashes($var_catid) . "' = '')
                                                AND S.vDelStatus = '0' AND S.vPostType='wish'  AND u.vStatus='0' and u.vDelStatus = '0' and (u.nUserId <> '".$_SESSION['guserid']."' or '".$_SESSION['guserid']."' = '".$fid."') ";

    $sql .=$qryopt . " ORDER BY S.vFeatured DESC,S.dPostDate DESC  ";
    $totalrows = mysqli_num_rows(mysqli_query($conn, $sql));
//                                    if ($totalrows<=0 && trim($_REQUEST["cmbItemType"])=='') $var_item_desc = "sale";
}//end else if
if ($var_item_desc == "sale") {
    $var_item_type = 3;
    $sql = "SELECT S.nSaleId as 'nSwapId',L.vCategoryDesc,S.vTitle,S.nPoint,
                                         date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',S.vFeatured,S.vUrl,
                                                                 S.nUserId,S.nValue,S.vDescription,S.vBrand,S.vType,S.vCondition,S.vYear,S.vSmlImg FROM
                                         " . TABLEPREFIX . "sale S Left outer join " . TABLEPREFIX . "category C on
                                         S.nCategoryId = C.nCategoryId
                                         LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'
                                                                 LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=S.nUserId
                                         where (S.nCategoryId  = '" . addslashes($var_catid) . "' or '" . addslashes($var_catid) . "' = '')
                                         AND S.vDelStatus = '0'  AND u.vStatus='0' and u.vDelStatus = '0' and (u.nUserId <> '".$_SESSION['guserid']."' or '".$_SESSION['guserid']."' = '".$fid."') ";

    $sql .= $qryopt . " AND S.nQuantity > '0' ORDER BY S.vFeatured DESC,S.dPostDate DESC  ";
    $totalrows = mysqli_num_rows(mysqli_query($conn, $sql));
    
}//end else if
//$navigate = pageBrowser($totalrows, 10, 10, "&catid=$var_catid&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=$var_item_desc&no=".$fid."&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function

    $sql=dopaging($sql,'',PAGINATION_LIMIT);
    
   // $sql = $sql . $navigate[0];
    $rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $numRecords = mysqli_num_rows($rs);
    
    if($numRecords>0) {
    
        $pagenumber     =   getCurrentPageNum();
        $defaultUrl     =   $_SERVER['PHP_SELF'];
        $querysting     =   "&catid=$var_catid&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=$var_item_desc&no=".$fid."&";
        $paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]" .$querysting;
        $pageString     =   getnavigation($totalrows);
        include_once("lib/pager/pagination.php"); 
        $pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}

    if ($var_item_type == 1) {
        $var_source = "s";
        $var_param = "swapid";
        $var_item_desc = "swap";
    }//end if
    else if ($var_item_type == 2) {
        $var_source = "w";
        $var_param = "swapid";
        $var_item_desc = "wish";
    }//end else if
    else {
        $var_source = "sa";
        $var_param = "saleid";
        $var_item_desc = "sale";
    }//end else

    $message = ($message != '') ? $message : $_SESSION['sessionMsg'];
    unset($_SESSION['sessionMsg']);
    
    $numRows    =   mysqli_num_rows($rs);
    
   
                                
                                
if ($var_item_type == '1' || $var_item_type == '2') {
    $nShowJavaId = 'nSwapId';
}//end if
else {
    //$nShowJavaId='nSaleId';
    $nShowJavaId = 'nSwapId';
}//end else

?>
<script language="javascript" type="text/javascript">
    function showMsg(nid)
    {
        document.getElementById('msgShow'+nid).style.display='';
    }//end function

    function hideMsg(nid)
    {
        document.getElementById('msgShow'+nid).style.display='none';
    }//end function

    function ValidateMsg(fid,toid)
    {
        if(trim(document.getElementById('txtTitle'+fid).value)=='')
        {
            alert("<?php echo ERROR_SUBJECT_EMPTY; ?>");
            document.getElementById('txtTitle'+fid).focus();
            return false;
        }//end if
        if(trim(document.getElementById('txtMsg'+fid).value)=='')
        {
            alert("<?php echo ERROR_MESSAGE_EMPTY; ?>");
            document.getElementById('txtMsg'+fid).focus();
            return false;
        }//end if
        else
        {
            document.getElementById('btnSubAdd').value='Send Message';//this is not text but value (not relevent for multi-lang)
            document.getElementById('ToId').value=toid;
            document.getElementById('txtTitle').value=document.getElementById('txtTitle'+fid).value;
            document.getElementById('txtMsg').value=document.getElementById('txtMsg'+fid).value;
            document.getElementById('frmMsg').submit();
        }//end else
    }//end function

    function formSubmit(type)
    {
        document.getElementById("cmbItemType").value = type;
        document.frmProducts.submit();
    }
</script>

<script src="<?php echo SITE_URL?>/js/responsive-tabs-2.3.2.js"></script>
<script type="text/javascript" src="./fancybox/jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="./fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="./fancybox/jquery.fancybox-1.3.4.css" media="screen" />
 
<script type="text/javascript">
    jQuery.noConflict();
  
    jQuery(document).ready(function() {
        //jQuery('#swap').addClass("active");
        if(jQuery(".various1").length >0){
            jQuery(".various1").fancybox({
                'titlePosition'		: 'inside',
                'transitionIn'		: 'none',
                'transitionOut'		: 'none'
            });  
        
        }
        
        jQuery(".dropdown-menu li a").each(function(){
            var selText = jQuery(this).text();
            <?php if($cmbSearchType!='') { ?>
                    var selText1 = '<?php echo $cmbSearchType;?>';
               jQuery(this).parents('.dropdown').find('.dropdown-toggle').html(selText1+' <span class="caret"></span>');
            <?php } ?>
            //alert(selText);
        });   
     
         jQuery('.form-group #btn_Search').click(function(){ 
             //jQuery("form#frmProducts").data('bootstrapValidator').defaultSubmit();
               jQuery('#frmProducts').submit();
    });
        
        <?php if($cmbSearchType!='') { ?>
         jQuery('#caret').text('<?php echo $cmbSearchType;?>');
         
           <?php } ?>
      
      jQuery(".dropdown-menu li a").click(function(){
        var selText = jQuery(this).text();
        //alert(selText);
       //jQuery(".dropdown-menu  span").html(selText);
       jQuery('#cmbSearchType').val(selText);
       // jQuery(this).closest('.dropdown-menu').children('a.dropdown-toggle').text(selText);
        jQuery(this).parents('.dropdown').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
       // jQuery(this).parents('.btn-group').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
});   
    });
</script>
<style type="text/css">
<!--
.img_pps img {
	height: auto!important;
	margin: 0 !important;
	padding: 0 !important;
	width: 100%!important;
}
-->
</style>


<link rel="stylesheet" href="styles/jquery.remodal.css">
                    	
   <?php                           
             if($var_catid!='')  { // ttab Navigation?>            
                           <div class="margin_t_20">    
                                <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
                                    
                            <li class="<?php if($var_item_desc=='swap'){ echo "active";}?>"><a href="#swap"  class="booth <?php if($var_item_desc=='swap'){ echo "active";}?>" id="swap"> <?php echo MENU_SWAP."(".$swapCount.")"; ?></a></li>
                                                <?php
                    if(ENABLE_POINT != '1') {
                    ?>
                             <li class="<?php if($var_item_desc=='sale'){ echo "active";}?>"><a href="#sale" class="booth <?php if($var_item_desc=='sale'){ echo "active";}?>" id="sale"> <?php echo MENU_SALE."(".$saleCount.")"; ?></a></li>
                    
                    <?php
                     }
                    ?>
                             
                              <li class="<?php if($var_item_desc=='wish'){ echo "active";}?>"><a href="#wish" class="booth <?php if($var_item_desc=='wish'){ echo "active";}?>" id="wish">  <?php echo MENU_WISH."(".$wishCount.")"; ?></a></li>
                    
                           </ul>
                            </div>          
                         
             <?php }?>          

		<div class="row">
           	<div class="innersubheader">
        	<div class="col-lg-5 col-sm-12 col-md-6 col-xs-12" >
                 <?php if ( $page_name == 'swaplistdetailed.php') { ?> 
            	<h3><?php echo TEXT_SWAP_ITEMS; ?></h3>
                <?php  } else if($page_name == 'wishlistdetailed.php' ){?> 
                <h3><?php echo TEXT_WISH_ITEMS; ?></h3>
                 <?php  } else if($page_name == 'salelistdetailed.php' ){?> 
                 <h3><?php echo TEXT_SALE_ITEMS; ?></h3>
                  <?php  } ?>
                
               
            </div> 
         
            
            <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12 right">
				<form name="frmProducts" id="frmProducts" action="<?php echo basename($_SERVER['PHP_SELF']);?>" method="POST" role="form">                  
				 <input name="catid" type="hidden" id="catid" VALUE="<?php echo $var_catid; ?>">
				 <input type="hidden" name="cmbItemType" id="cmbItemType" value="">
					<input type="hidden" name="cmbSearchType" id="cmbSearchType" value="" >
					<div class="searchoption_inner">
					<div class="searchoption_inner_row">
						<div class="searchoption_inner_cell" style="width:30%;">
							
							<select name="cmbSearchType" class="form-control pull-left">
								<option <?php if($cmbSearchType==TEXT_CATEGORY){?> selected="selected" <?php }?> value="<?php echo TEXT_CATEGORY; ?>"><?php echo TEXT_CATEGORY; ?></option>
								<option <?php if($cmbSearchType==TEXT_TITLE){?> selected="selected" <?php }?> value="<?php echo TEXT_TITLE; ?>"><?php echo TEXT_TITLE; ?></option>
								<option <?php if($cmbSearchType==TEXT_DESCRIPTION){?> selected="selected" <?php }?> value="<?php echo TEXT_DESCRIPTION; ?>"><?php echo TEXT_DESCRIPTION; ?></option>
								<option <?php if($cmbSearchType==TEXT_BRAND){?> selected="selected" <?php }?> value="<?php echo TEXT_BRAND; ?>"><?php echo TEXT_BRAND; ?></option>
								<option <?php if($cmbSearchType==TEXT_CONDITION){?> selected="selected" <?php }?> value="<?php echo TEXT_CONDITION; ?>"><?php echo TEXT_CONDITION; ?></option>
								<option <?php if($cmbSearchType==TEXT_SELLER_NAME){?> selected="selected" <?php }?> value="<?php echo TEXT_SELLER_NAME; ?>"><?php echo TEXT_SELLER_NAME; ?></option>
							</select>

							
							<!--<div class="dropdown">
								<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
									<?php echo TEXT_CATEGORY;?>
									<span class="caret"></span>
								</button>                     
								<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
									<li role="presentation"><a role="menuitem" tabindex="-1" href="#"><?php echo TEXT_CATEGORY; ?></a></li>
									<li role="presentation"><a role="menuitem" tabindex="-1" href="#"></a></li>
									<li role="presentation"><a role="menuitem" tabindex="-1" href="#"></a></li>
									<li role="presentation"><a role="menuitem" tabindex="-1" href="#"></a></li>
									<li role="presentation"><a role="menuitem" tabindex="-1" href="#"></a></li>
									<li role="presentation"><a role="menuitem" tabindex="-1" href="#"></a></li>
									<!--<li role="presentation" class="divider"></li>
									<li role="presentation"><a role="menuitem" tabindex="-1" href="#">Separated link</a></li>-->
								<!--
								</ul>
							</div>-->
						</div>
						<div class="searchoption_inner_cell" style="width:60%;">
							<div class="form-group">
								<input type="text" class="form-control pull-left" placeholder="<?php echo BUTTON_SEARCH;?>" value="<?php echo(htmlentities($txtSearch));?>" name="txtSearch" >
								<div class="clear"></div>
							</div>
						</div>
						<div class="searchoption_inner_cell" style="width:10%;">
							<button type="submit" class="btn actionbtn btn-default pull-right" id="btn_Search"><?php echo TEXT_GO;?></button>
						</div>
					
						<div class="clear"></div>
					</div>
					</div>
			 </form>   
            </div>                   
            <div class="clear"></div>
         </div>

        </div>

		<div class="row">
<?php 
if ($var_item_type == '1' || $var_item_type == '2') {
    $nShowJavaId = 'nSwapId';
}//end if
else {
    //$nShowJavaId='nSaleId';
    $nShowJavaId = 'nSwapId';
}//end else

if ($numRows > 0) { 
    while ($arr = mysqli_fetch_array($rs)) {
        ++$nm;
        if ($arr["vFeatured"] == "N") {
            $style = "textgrey";
            $featured_img = "";
        }//end if
        else {
            $style = "boldtextblack";
            $featured_img = "&nbsp;<img src=images/featured_star.png>";
        }//end else

        if (trim($arr["vSmlImg"]) == "" || !file_exists($arr["vSmlImg"])) {
            $var_url = "images/nophoto.gif";
            @list($thumb_wid, $thumb_hei) = @getimagesize('./' . $var_url);

            if ($thumb_wid > 250) {
                $thumb_wid = '250';
            }//end if
            else {
                $thumb_wid = $thumb_wid;
            }//end else

            if ($thumb_hei > 250) {
                $thumb_hei = '250';
            }//end if
            else {
                $thumb_hei = $thumb_hei;
            }//end else
        }//end if
        else {
            $thumb_siz = getimagesize($arr["vSmlImg"]);
            $thumb_wid = ($thumb_siz[0]>120)?'120':$thumb_siz[0];
            $thumb_hei = ($thumb_siz[1]>120)?'120':$thumb_siz[1];
            $var_url = $arr["vSmlImg"];
        }//end else

        switch (trim($arr["vUrl"])) {
            case "":
            case !file_exists($arr["vUrl"]):
                $var_burl = "images/nophoto.gif";
                break;

            default:
                $var_burl = $arr["vUrl"];
                break;
        }//end switch
//checking user status
        $condition = "where nUserId='" . $arr['nUserId'] . "'";
        $userStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'Online', 'vVisible', $condition), 'vVisible');

        //if posted user not logged user
        if ($arr['nUserId'] != $_SESSION["guserid"]) {
            //fetching user name
            $vLoginName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
            $chatWindow = '<a href="javascript:WindowPop(\'' . ucfirst($arr['nUserId']) . '\');" class="chat_online" title="'.LINK_CHAT_WITH.' ' . ucfirst($vLoginName) . '" title="'.LINK_CHAT_WITH.' ' . ucfirst($vLoginName) . '""></a>';

            //$offMessage = '&nbsp;&nbsp;<span style=\'cursor: pointer;\' onClick="showMsg(\'' . $arr[$nShowJavaId] . '\');" class="login_btn">'.BUTTON_SEND_MESSAGE.'</span>';//style="background-image:url(images/sendamail.gif);padding:5px 4px 5px 4px;cursor:pointer;"
            //$offMessage = '<a class="offline_msg_link" style="position:relative;" href="#inlinebox'.$nm.'" onClick="showMsg(\'' . $arr[$nShowJavaId] . '\');">'.BUTTON_SEND_MESSAGE.'</a><a class="chat_offline"></a>';
            $offMessage = '<a class="offline_msg_link" style="position:relative;" href="#modal'.$arr[$nShowJavaId].'">'.BUTTON_SEND_MESSAGE.'</a><a class="chat_offline"></a>';
			
        }//end if
        else {
            $chatWindow = '';
            $offMessage = '';
        }//end else

        if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
            $offMessage = $offMessage;
        }//end if
        else {
           // $offMessage = '';
        }//end else

        switch ($userStatus) {
            case "Y":
                $userStatus = '<a class="online_msg_link" href="Javascript:WindowPop(\'' . ucfirst($arr['nUserId']) . '\');">'.TEXT_ONLINE. "</a>".$chatWindow;

                break;

            case "N":
                $userStatus = $offMessage;
                break;

            case "":
                $userStatus = $offMessage;
                break;
        }//end switch
        //cheching thumbnail tooltip enable or not
        switch (DisplayLookUp('ThumbToolTip')) {
            case "Yes":
                $thumClass = ' thumbnail';

                //fetching user name
                $condition = "where nUserId='" . $arr['nUserId'] . "'";
                $UserName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
                @list($width, $height) = @getimagesize('./' . $var_burl);
                if ($width > 250) {
                    $width = '250';
                }//end if
                else {
                    $width = $width;
                }//end else

                if ($height > 250) {
                    $height = '250';
                }//end if
                else {
                    $height = $height;
                }//end else

                $price_points = '';
                $price_points .= '<table width="100%"  border="0" cellspacing="0" cellpadding="1">
                            <tr>
                              <td align="left" class="welcome_2">';
                if (($EnablePoint == '0' || $EnablePoint == '2') && $arr['nValue']>0) {
                    $price_points .= TEXT_PRICE.' ' . CURRENCY_CODE . $arr['nValue'];
                }
                if ($EnablePoint == '2' && $arr['nValue'] > 0 && $arr['nPoint'] > 0) $price_points .= ' & ';
                if (($EnablePoint == '1' || $EnablePoint == '2') && $arr['nPoint']>0) {
                    $price_points .=  $arr['nPoint'] .' '.POINT_NAME. '';
                }
                $price_points .= '</td>
                            </tr>
                          </table>';
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
                // Item Price End  
                
                 //if ($var_burl != "images/nophoto.gif") {
                    $immg = '<table width="100%"  border="0" cellspacing="0" cellpadding="5">
                        <tr>
                          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td align="left"><img src="' . $var_burl . '"  border="0"></td>
                            </tr>
                          </table>
                          </td>
                        </tr>
                      </table>';//width="' . $width . '" height="' . $height . '"
                //}
                //else $immg = '';
                $thumContent = '<table width="100%"  border="0" cellspacing="0" cellpadding="10" class="maintext2_pop">
                    <tr>
                      <td width="17%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td><div class="img_pps">'.$immg.'</div></td>
                        </tr>
                        <tr>
                          <td height="5"></td>
                        </tr>
                        <tr>
                          <td><div class="prce_txtspup"><p>'.$price_points.'</p></div></td>
                        </tr>
                      </table></td>
                          <td width="83%" valign="top">
                          <table width="100%"  border="0" cellspacing="0" cellpadding="1">
                              <tr>
                                <td align="left" valign="top" class="welcome"><h4>'.htmlentities($arr["vTitle"]) . '</h4></td>
                              </tr>
                              <tr>
                                <td align="left" valign="top" class="smalltext"><div class="pop_description"><p>' . htmlentities($arr['vDescription']) . '</p></div></td>
                              </tr>
                              <tr>
                                <td align="left"></td>
                              </tr>
                              <tr>
                                <td align="left"><div class="profs_bld">'.TEXT_CATEGORY.' &raquo; ' . htmlentities($arr['vCategoryDesc']) . '</div></td>
                              </tr>
                              <tr>
                                <td align="left"><div class="profs_bld">'.TEXT_POSTED_ON.' &raquo; ' . date('m/d/Y', strtotime($arr['dPostDate'])) . '</div></td>
                              </tr>
                              <tr>
                                <td align="left"><div class="profs_bld">'.TEXT_POSTED_BY.' &raquo; ' . htmlentities($UserName) . '</div></td>
                              </tr>
                              <tr>
                                <td align="left"><div class="profs_bld">'.TEXT_BRAND.' &raquo; ' . htmlentities($arr['vBrand']) . '</div></td>
                              </tr>
                              <tr>
                                <td align="left"><div class="profs_bld">'.TEXT_TYPE.' &raquo; ' . htmlentities($arr['vType']) . '</div></td>
                              </tr>
                              <tr>
                                <td align="left"><div class="profs_bld">'.TEXT_CONDITION.' &raquo; ' . htmlentities($arr['vCondition']) . '</div></td>
                              </tr>
                              <tr>
                                <td align="left"><div class="profs_bld">'.TEXT_YEAR.' &raquo; ' . htmlentities($arr['vYear']) . '</div></td>
                              </tr>
                            </table>                                                                						
                          </td>
                     </tr>
                  </table>
';
                
                $thumbSpan = '<span>' . $thumContent;
                break;

            case "No":
                $thumClass = '';
                $thumbSpan = '';
                break;

            case "":
                $thumClass = '';
                $thumbSpan = '';
                break;
        }//end switch
        
    $condition = "where nUserId='" . $arr['nUserId'] . "'";
    $userRs=select_rows(TABLEPREFIX . 'users', 'nUserId, vLoginName, vFirstName, vLastName, vAddress1, vCity, vState, vCountry, vFax, nZip, vIMStatus, profile_image', $condition);
    $userData=array();
    $userData=mysqli_fetch_assoc($userRs);
     if($_SESSION["guserid"] != ''){
        $itemOwner  = $arr['nUserId'];
        $transactionStatus  =   getTransactionStatus($itemOwner);
    }else{
        $transactionStatus  = false;
    }
	
    if($transactionStatus){
        $hrf_profile = "class='various1' href='#inline".$nm."'";
    }else{
        $hrf_profile = "style='cursor: default;'"; 
    }
    
    ?>
        <div class="col-lg-4">
        <div class="product_list_item">
        	<div class="col-lg-12 col-md-12 col-sm-12 no_padding">
            	<div class="product_listing_img">
                	<?php echo "<a href='" . $detailfile . "?" . $var_param . "=" . $arr["nSwapId"] . "&source=" . $var_source . "&prod=p&catid=$var_catid&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=" . $var_item_desc . "&' class='" . $style . $thumClass . "'>";
                        ?>
                        <img src="<?php echo $var_burl;?>"  border="0">
                        <?php echo $thumbSpan . "</a>"; ?>
						
						<div class="layoutmodi1 status_outer">
                    	<!--<a class="offline_msg_link" href="#">Send Message</a>-->
                        <?php echo $userStatus;?>
                   	 	</div>
						<?php if($thumClass!="") {?>
						<div class="quick_view">
							<?php $var_link = $rootserver."/".$detailfile."?".$var_param."=".$arr["nSwapId"]."&source=".$var_source."&prod=p&catid=".$var_catid;?>
							<a href="#modal<?php echo 'q' . $nm;?>"><?php echo TEXT_MOUSE_OVER;?></a>
						</div>
                        <?php } ?>
                </div>
            </div>
			<div class="clear"> </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
            	<div class="row">
                	<div class="col-lg-12 ">
                    	<h2 class="product_heading">
                            <a href="<?php echo $detailfile."?".$var_param . "=" . $arr["nSwapId"] . "&source=" . $var_source . "&prod=p&catid=$var_catid&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=" . $var_item_desc;?>" title="<?php echo strtoupper(htmlentities($arr["vTitle"]))?>"> <?php echo (strlen(trim($arr["vTitle"]))>29)?substr(htmlentities($arr["vTitle"]),0,29)."...":htmlentities($arr["vTitle"])?></a>
						</h2>
                    </div>                    
                </div>
                <div class="row">
                <div class="col-lg-12">
					<?php if($arr['vDescription']!='') { ?>      
					<!--<p class="prdct_details">
						 <?php echo substr(htmlentities($arr['vDescription']),0,100);?>
						<a href="<?php echo $detailfile."?".$var_param . "=" . $arr["nSwapId"] . "&source=" . $var_source . "&prod=p&catid=$var_catid&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=" . $var_item_desc;?>">More..</a>
					</p>-->
					<?php } ?>
					<h4 class="prdct_price"><?php //echo $item_name; ?><span><?php echo $item_price ?></span> </h4>
					<span class="date_list"><?php echo date('m/d/Y', strtotime($arr["dPostDate"])); ?></span>
                </div>
				<div class="col-lg-12 no_padding">
					<h5 class="prdct_postedby">
						<?php 
							if($userData['vIMStatus']=='Y') {
							if(is_file('./pics/profile/'.$userData['profile_image'])) {
								echo '<img src="./pics/profile/'.$userData['profile_image'].'" width="25" height="25" alt="'.$userData['vLoginName'].'" title="'.$userData['vLoginName'].'" />';
							}
							}?>
						<!-- <?php //echo TEXT_POSTED_BY ?>  &nbsp; <a  <?php //echo $hrf_profile;?> ><?php //echo $userData['vLoginName']; ?></a> -->
                                            
						<?php echo TEXT_POSTED_BY ?>  &nbsp; <a href="user_profile.php?uid=<?php echo $userData['nUserId'] . "&uname=" . urlencode($userData['vLoginName']); ?>"><?php echo $userData['vLoginName']; ?></a>
					</h5>
				</div>
                </div>
            </div>
        </div>

        </div>

<!--div class="remodal" data-remodal-id="modal">
	<p>
		one
	</p>
	<p>
		1111111
	</p>
</div

<div data-remodal-id="modal2">
	<h1>two</h1>
	<p>2222</p> 
</div>
-->
<!------QUICK VIEW ---->
<div class="remodal" data-remodal-id="modal<?php echo 'q' . $nm; ?>">

<div class="col-lg-12 col-md-12 col-sm-12 no_padding">
	<div class="col-lg-6 col-md-6 col-sm-12 no_padding">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="quick_img_popup_outer">
				<img border="0" src="<?php echo $var_burl;?>" class="quick_img_popup">
			</div>
			<div class="welcome_2">
				<?php echo ($arr['nValue'] > 0) ? 'Price ' : '' ?><?php echo $item_price ?>
			</div>
		</div>		
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 no_padding">
		<div class="col-lg-12 col-md-12 col-sm-12 no_padding">
			<div class="welcome"><h4><?php echo (strlen(trim($arr["vTitle"]))>21)?strtoupper(substr(htmlentities($arr["vTitle"]),0,21))."...":strtoupper(htmlentities($arr["vTitle"]));?></h4></div>
			<?php if($arr['vDescription']!='') { ?>   
			<div class="pop_description"><p><?php echo substr(htmlentities($arr['vDescription']),0,200);?> </p></div>
			<?php } ?>
			<div class="profs_bld"><b>Category </b> &nbsp; <?php echo $arr["vCategoryDesc"];?></div>
			<div class="profs_bld"><b>Posted On </b> &nbsp; <?php echo date('m/d/Y', strtotime($arr["dPostDate"])); ?></div>
			<div class="profs_bld"><b>Posted By </b> &nbsp; <?php echo $userData['vLoginName']; ?></div>
			<div class="profs_bld"><b>Brand </b> &nbsp; <?php echo $arr["vBrand"];?></div>
			<div class="profs_bld"><b>Type </b> &nbsp; <?php echo $arr["vType"];?></div>
			<div class="profs_bld"><b>Condition </b> &nbsp; <?php echo $arr["vCondition"];?></div>
			<div class="profs_bld"><b>Year </b> &nbsp; <?php echo $arr["vYear"];?></div>
		</div>
	</div>
</div>

</div>
<!----End QUICK VIEW  --->

<!--Send Mesasge Area-->

<div id="msgShow<?php echo $arr[$nShowJavaId]; ?>" class="remodal" data-remodal-id="modal<?php echo $arr[$nShowJavaId]; ?>">
	<div id="inlinebox<?php echo $nm ?>" style="width:90%; height:auto; padding:5px 5px 0px 5px; margin:0 5%;">
		<?php if(!isset($message)) { ?>
		<?php echo '<form action="" method="post" name="frmMsg" id="frmMsg">'; ?>
		<input type="hidden" name="qyery" value="<?php echo $_SERVER['QUERY_STRING']; ?>">
		<input type="hidden" name="ToId" id="ToId">
		<input type="hidden" name="btnSubAdd" id="btnSubAdd">
		<input type="hidden" name="txtTitle" id="txtTitle">
		<input type="hidden" name="txtMsg" id="txtMsg">

		<div class="homepage_contentsec maintext2">
                    <div class="full_width main_form_inner"><label><?php echo SEND_MESSAGE_TEXT; ?></label></div>
			<div class="full_width"> 
				<div class="full_width">
					<div class="full_width">
						<div class="full_width">
							<div class="full_width main_form_outer" style="padding:10px; border:0px; ">
								<div class="full_width main_form_inner">
									<label><?php echo TEXT_SUBJECT; ?> <span class="warning">*</span></label>
									<input type="text" name="txtTitle<?php echo $arr[$nShowJavaId]; ?>" class="form-control" value="<?php echo $txtTitle; ?>" size="50" id="txtTitle<?php echo $arr[$nShowJavaId]; ?>">
								</div>
								<div class="full_width main_form_inner">
									<label><?php echo TEXT_MESSAGE; ?> <span class="warning">*</span></label>
									<textarea name="txtMsg<?php echo $arr[$nShowJavaId]; ?>" cols="55" rows="8" class="form-control" id="txtMsg<?php echo $arr[$nShowJavaId]; ?>"><?php echo $txtMsg; ?></textarea>
								</div>
								<div class="full_width main_form_inner">
									<label>
										<input type="button" name="btnGo" value="<?php echo BUTTON_SEND_MESSAGE; ?>" class="subm_btt" onClick="return ValidateMsg(<?php echo $arr[$nShowJavaId]; ?>,<?php echo $arr['nUserId']; ?>);"><!--&nbsp;<input type="reset" name="btnGo2" value="<?php //echo BUTTON_RESET; ?>" class="submit">-->
									</label>
								</div>
							</div>				
						</div>
					</div>
				</div>  
			</div>
		</div>
		
	<?php echo '</form>'; ?>
	<?php } else { ?>
		<div class="homepage_contentsec maintext2">
             <div class="full_width main_form_inner"><label><?php echo $message; ?></label></div>
        </div>
	<?php }?>
	</div>
	<div class="clear"></div> 
</div>

<?php 

        } // end while 
 ?>
 
        </div>
<!----PAgination--->

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

<!---End Pagination--->
<?php 
    }  else { ?>
<div class="col-lg-12">
<div class="product_list_item">
    <p class="prdct_details"><?php echo NO_PRODUCTS_FOUND;?></p>
</div>
</div>

<?php } ?>

<script type="text/javascript">
     var $jbs=jQuery.noConflict();
  (function($) {
      fakewaffle.responsiveTabs( [ 'phone', 'tablet' ] );
  })(jQuery);
  
   $jbs( '#myTab a' ).click( function ( e ) {
        e.preventDefault();
        //alert('hiiiii');
       //  $jbs('#'+$jbs(this).attr("id")).show();
            //$(".booth").removeClass("selected");
           // alert($jbs($jbs(this).attr("id")));
            var pType   =  $jbs( this ).attr("id");
            //alert(pType);
            $jbs('#cmbItemType').val(pType);
            
       $jbs('#pType').addClass("active");
        $jbs('#'+$jbs(this).attr("id")).addClass("active");
      // alert($jbs( this ).attr("id"));
      document.frmProducts.submit();
        //$jbs( this ).tab( 'show' );
    } );
      
     
</script>



<!-- You can define the global options -->
<script>
  // window.remodalGlobals = {
  //   namespace: "remodal",
  //   defaults: {
  //     hashTracking: true,
  //     closeOnConfirm: true,
  //     closeOnCancel: true,
  //     closeOnEscape: true, 
  //     closeOnAnyClick: true
  //   }
  // };
</script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
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

//  You can open or close it like this:
//  $(function () {
//    var inst = $.remodal.lookup[$("[data-remodal-id=modal]"").data("remodal")];
//    inst.open();
//    inst.close();
//  });

  //  Or init in this way:
  var inst = $("[data-remodal-id=modal2]").remodal();
  //  inst.open();
  
  
  $('.dropdown-toggle').dropdown();
  
</script>
