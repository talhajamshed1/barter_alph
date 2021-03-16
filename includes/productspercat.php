<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+ 
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>                              |
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
	}
	else {
		$txtTitle = $_POST['txtTitle'];
		$txtMsg = $_POST['txtMsg'];
	}
	
	$msgId  = $_POST['msgId'];

	$pquery = ($_POST['qyery'] != '') ? $pquery = '?' . $_POST['qyery'] : $pquery = '';

		//insert into tbl message
	mysqli_query($conn, "insert into " . TABLEPREFIX . "messages (nToUserId,nFromUserId,vTitle,vMsg,vStatus,nDate)
		values ('" . $_POST['ToId'] . "','" . $_SESSION["guserid"] . "','" . addslashes($txtTitle) . "','" . addslashes($txtMsg) . "',
		'N',now())") or die(mysqli_error($conn));
	$_SESSION['succ_msg'] = MESSAGE_MESSAGE_SENT_SUCCESSFULLY;
	$_SESSION['succ_msg_msg'] = $msgId;
	$redirect_url = $_SERVER['HTTP_REFERER'];

	header("location:".$redirect_url);
		//header('location:./catwiseproducts.php' . $pquery);
}

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
$var_item_desc  = $_REQUEST['cmbItemType'] ? $_REQUEST['cmbItemType'] : $_REQUEST["var_item_desc"];

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
}
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
	}
	else if ($cmbSearchType == TEXT_TITLE) {
		$qryopt .= " AND S.vTitle like '%" . addslashes($txtSearch) . "%' ";
	}
	else if ($cmbSearchType == TEXT_DESCRIPTION) {
		$qryopt .= " AND S.vDescription like '%" . addslashes($txtSearch) . "%'";
	}
	else if ($cmbSearchType == TEXT_BRAND) {
		$qryopt .= " AND S.vBrand like '%" . addslashes($txtSearch) . "%'";
	}
	else if ($cmbSearchType == TEXT_CONDITION) {
		$qryopt .= "  AND S.vCondition like '" . addslashes($txtSearch) . "%'";
	}
	else if ($cmbSearchType == TEXT_SELLER_NAME) {
		$qryopt .= "  AND u.vLoginName like '%" . addslashes($txtSearch) . "%'";
	}
}
if ($fid > 0) {
	$qryopt .= " AND u.nUserId = '" . addslashes($fid) . "' ";
}

 /*
	* Get Swap count
	*/
	$swapsql = "SELECT S.nSwapId,L.vCategoryDesc,S.vTitle,S.nPoint, date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
	S.vFeatured,S.vUrl, S.nUserId,S.nValue,S.vDescription,S.vBrand,S.vType,S.vCondition,S.vYear,S.vSmlImg 
	FROM " . TABLEPREFIX . "swap S 
	LEFT OUTER JOIN " . TABLEPREFIX . "category C ON S.nCategoryId = C.nCategoryId
	LEFT JOIN " . TABLEPREFIX . "category_lang L ON C.nCategoryId = L.cat_id AND L.lang_id = '" . $_SESSION['lang_id'] . "'
	LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=S.nUserId
	WHERE (S.nCategoryId  = '" . addslashes($_REQUEST['catid']) . "' OR '" . addslashes($_REQUEST['catid']) . "' = '')
	AND S.vDelStatus = '0' AND S.vPostType='swap' AND u.vStatus='0' AND u.vDelStatus = '0' AND vSwapStatus= '0' 
	AND (u.nUserId <> '".$_SESSION['guserid']."' OR '".$_SESSION['guserid']."' = '".$fid."') ".$qryopt." ";

	$swapCount = mysqli_num_rows(mysqli_query($conn, $swapsql));
 /*
	* Get wish count
	*/

	$wishSql = "SELECT S.nSwapId,L.vCategoryDesc,S.vTitle,S.nPoint, date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate', 
	S.vFeatured,S.vUrl, S.nUserId,S.nValue,S.vDescription,S.vBrand,S.vType,S.vCondition,S.vYear,S.vSmlImg 
	FROM " . TABLEPREFIX . "swap S 
	LEFT OUTER JOIN " . TABLEPREFIX . "category C ON S.nCategoryId = C.nCategoryId
	LEFT JOIN " . TABLEPREFIX . "category_lang L ON C.nCategoryId = L.cat_id AND L.lang_id = '" . $_SESSION['lang_id'] . "'
	LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=S.nUserId
	WHERE (S.nCategoryId  = '" . addslashes($_REQUEST['catid']) . "' OR '" . addslashes($_REQUEST['catid']) . "' = '')
	AND S.vDelStatus = '0' AND S.vPostType='wish'  AND u.vStatus='0' AND u.vDelStatus = '0'  AND vSwapStatus= '0'
	AND (u.nUserId <> '".$_SESSION['guserid']."' OR '".$_SESSION['guserid']."' = '".$fid."') ".$qryopt."";

	$wishCount = mysqli_num_rows(mysqli_query($conn, $wishSql));

	/*
	* Get sale count
	*/
	$saleSql = "SELECT S.nSaleId as 'nSwapId',L.vCategoryDesc,S.vTitle,S.nPoint, date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
	S.vFeatured,S.vUrl, S.nUserId,S.nValue,S.vDescription,S.vBrand,S.vType,S.vCondition,S.vYear,S.vSmlImg 
	FROM " . TABLEPREFIX . "sale S 
	LEFT OUTER JOIN " . TABLEPREFIX . "category C ON S.nCategoryId = C.nCategoryId
	LEFT JOIN " . TABLEPREFIX . "category_lang L ON C.nCategoryId = L.cat_id AND L.lang_id = '" . $_SESSION['lang_id'] . "'
	LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=S.nUserId
	WHERE (S.nCategoryId  = '" . addslashes($_REQUEST['catid']) . "' OR '" . addslashes($_REQUEST['catid']) . "' = '')
	AND S.vDelStatus = '0'  AND u.vStatus='0' AND u.vDelStatus = '0' AND S.nQuantity > '0'
	AND (u.nUserId <> '".$_SESSION['guserid']."' OR '".$_SESSION['guserid']."' = '".$fid."') ".$qryopt." ";

	$saleCount = mysqli_num_rows(mysqli_query($conn, $saleSql));


	if ($var_item_desc == "swap" || $var_item_desc == "") { 
		$var_item_type = 1;
		
		$sql = "SELECT S.nSwapId, L.vCategoryDesc, S.vTitle, S.nPoint, date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
		S.vFeatured, S.vUrl, S.nUserId, S.nValue, S.vDescription, S.vBrand, S.vType, S.vCondition, S.vYear, S.vSmlImg 
		FROM  " . TABLEPREFIX . "swap S 
		LEFT OUTER JOIN " . TABLEPREFIX . "category C ON S.nCategoryId = C.nCategoryId
		LEFT JOIN " . TABLEPREFIX . "category_lang L ON C.nCategoryId = L.cat_id AND L.lang_id = '" . $_SESSION['lang_id'] . "'
		LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId=S.nUserId
		WHERE (S.nCategoryId  = '" . addslashes($var_catid) . "' OR '" . addslashes($var_catid) . "' = '')
		AND S.vDelStatus = '0' AND S.vPostType='swap' AND u.vStatus='0' AND u.vDelStatus = '0' AND vSwapStatus= '0'
		AND (u.nUserId <> '".$_SESSION['guserid']."' OR '".$_SESSION['guserid']."' = '".$fid."') ";

		$sql .= $qryopt . " ORDER BY S.vFeatured DESC, S.dPostDate DESC ";
		$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));
																	//if ($totalrows<=0 && trim($_REQUEST["cmbItemType"])=='') $var_item_desc = "wish";
	}
	if ($var_item_desc == "wish") {
		$var_item_type = 2;
		$sql = "SELECT S.nSwapId, L.vCategoryDesc, S.vTitle, S.nPoint, date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
		S.vFeatured, S.vUrl, S.nUserId, S.nValue, S.vDescription, S.vBrand, S.vType, S.vCondition, S.vYear, S.vSmlImg 
		FROM  " . TABLEPREFIX . "swap S 
		LEFT OUTER JOIN " . TABLEPREFIX . "category C on S.nCategoryId = C.nCategoryId
		LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id AND L.lang_id = '" . $_SESSION['lang_id'] . "'
		LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = S.nUserId
		WHERE (S.nCategoryId  = '" . addslashes($var_catid) . "' OR '" . addslashes($var_catid) . "' = '')
		AND S.vDelStatus = '0' AND S.vPostType = 'wish' AND u.vStatus = '0' AND u.vDelStatus = '0' AND vSwapStatus= '0'
		AND (u.nUserId <> '".$_SESSION['guserid']."' OR '".$_SESSION['guserid']."' = '".$fid."') ";

		$sql .= $qryopt . " ORDER BY S.vFeatured DESC, S.dPostDate DESC ";
		$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));
	 // if ($totalrows<=0 && trim($_REQUEST["cmbItemType"])=='') $var_item_desc = "sale";
	}
	if ($var_item_desc == "sale") {
		$var_item_type = 3;
		$sql = "SELECT S.nSaleId as 'nSwapId',L.vCategoryDesc,S.vTitle,S.nPoint, date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate',
		S.vFeatured,S.vUrl,S.nUserId,S.nValue,S.vDescription,S.vBrand,S.vType,S.vCondition,S.vYear,S.vSmlImg 
		FROM  " . TABLEPREFIX . "sale S 
		LEFT OUTER JOIN " . TABLEPREFIX . "category C on S.nCategoryId = C.nCategoryId
		LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id AND L.lang_id = '" . $_SESSION['lang_id'] . "'
		LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = S.nUserId
		WHERE (S.nCategoryId  = '" . addslashes($var_catid) . "' OR '" . addslashes($var_catid) . "' = '')
		AND S.vDelStatus = '0'  AND u.vStatus='0' AND u.vDelStatus = '0' 
		AND (u.nUserId <> '".$_SESSION['guserid']."' OR '".$_SESSION['guserid']."' = '".$fid."') ";

		$sql .= $qryopt . " AND S.nQuantity > '0' ORDER BY S.vFeatured DESC,S.dPostDate DESC  ";
		$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));
		
	}
//$navigate = pageBrowser($totalrows, 10, 10, "&catid=$var_catid&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=$var_item_desc&no=".$fid."&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function

	$sql = dopaging($sql,'',PAGINATION_LIMIT);

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
	}
	else if ($var_item_type == 2) {
		$var_source = "w";
		$var_param = "swapid";
		$var_item_desc = "wish";
	}
	else {
		$var_source = "sa";
		$var_param = "saleid";
		$var_item_desc = "sale";
	}
	$message = ($message != '') ? $message : $_SESSION['sessionMsg'];
	unset($_SESSION['sessionMsg']);

	$numRows    =   mysqli_num_rows($rs);

	if ($var_item_type == '1' || $var_item_type == '2') {
		$nShowJavaId = 'nSwapId';
	}
	else {
		//$nShowJavaId='nSaleId';
		$nShowJavaId = 'nSwapId';
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
						document.getElementById('msgId').value=fid;
						document.getElementById('txtTitle').value=document.getElementById('txtTitle'+fid).value;
						document.getElementById('txtMsg').value=document.getElementById('txtMsg'+fid).value;
						document.getElementById('frmMsg').submit();
					}
				}

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
						'titlePosition'   : 'inside',
						'transitionIn'    : 'none',
						'transitionOut'   : 'none'
					});  

				}
				
				jQuery(".dropdown-menu li a").each(function(){
					var selText = jQuery(this).text();
					<?php if($cmbSearchType!='') { ?>
						var selText1 = '<?php echo $cmbSearchType;?>';

								// jQuery(this).parents('.dropdown').find('.dropdown-toggle').html(selText1+' <span class="caret"></span>');

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

/* .img_pps img {
		height: auto!important;
		margin: 0 !important;
		padding: 0 !important;
		width: 100%!important;
}
*/
</style>


<link rel="stylesheet" href="styles/jquery.remodal.css">



<div class="clearfix">


	<div class="product-list-head">
		<div >
			<?php if ( $page_name == 'swaplistdetailed.php') { ?> 
				<h3><?php echo TEXT_SWAP_ITEMS; ?></h3>
			<?php } else if($page_name == 'wishlistdetailed.php' ){?> 
				<h3><?php echo TEXT_WISH_ITEMS; ?></h3>
			<?php } else if($page_name == 'salelistdetailed.php' ){?> 
				<h3><?php echo TEXT_SALE_ITEMS; ?></h3>
			<?php } ?>
		</div> 

		<div class="product-list-head-right">
			<form name="frmProducts" id="frmProducts" action="<?php echo basename($_SERVER['PHP_SELF']);?>" method="POST" role="form">                  
				<input name="catid" type="hidden" id="catid" VALUE="<?php echo $var_catid; ?>">
				<input type="hidden" name="cmbItemType" id="cmbItemType" value="">
				<input type="hidden" name="cmbSearchType" id="cmbSearchType" value="" >
				<input type="hidden" name="var_item_desc" id="var_item_desc" value="<?php echo $var_item_desc;?>" >



				<select name="cmbSearchType" class="selectpicker">
					<option <?php if($cmbSearchType==TEXT_CATEGORY){?> selected="selected" <?php }?> value="<?php echo TEXT_CATEGORY; ?>"><?php echo TEXT_CATEGORY; ?></option>
					<option <?php if($cmbSearchType==TEXT_TITLE){?> selected="selected" <?php }?> value="<?php echo TEXT_TITLE; ?>"><?php echo TEXT_TITLE; ?></option>
					<option <?php if($cmbSearchType==TEXT_DESCRIPTION){?> selected="selected" <?php }?> value="<?php echo TEXT_DESCRIPTION; ?>"><?php echo TEXT_DESCRIPTION; ?></option>
					<option <?php if($cmbSearchType==TEXT_BRAND){?> selected="selected" <?php }?> value="<?php echo TEXT_BRAND; ?>"><?php echo TEXT_BRAND; ?></option>
					<option <?php if($cmbSearchType==TEXT_CONDITION){?> selected="selected" <?php }?> value="<?php echo TEXT_CONDITION; ?>"><?php echo TEXT_CONDITION; ?></option>
					<option <?php if($cmbSearchType==TEXT_SELLER_NAME){?> selected="selected" <?php }?> value="<?php echo TEXT_SELLER_NAME; ?>"><?php echo TEXT_SELLER_NAME; ?></option>
				</select>

				<input type="text" class="form-control pull-left" placeholder="<?php echo BUTTON_SEARCH;?>" value="<?php echo(htmlentities($txtSearch));?>" name="txtSearch" >

				<button type="submit" class="btn actionbtn btn-default pull-right" id="btn_Search"><?php echo TEXT_GO;?></button>

			</form>  
			<div class="view-mode" id="viewMode">
				<a href="#" id="gridView" class="active"><i class="flaticon-grid"></i></a>
				<a href="#" id="listView" class=""><i class="flaticon-list"></i></a>
			</div> 
		</div>                   

	</div>

	<?php                           
	if($var_catid!='')  { // ttab Navigation?>            
		<div class="margin_t_20">    
			<ul class="nav nav-tabs nav-justified tab-tiles" role="tablist" id="myTab">

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

	<div class="row product-more-list viewmode gridView" >
		<?php 
		if ($var_item_type == '1' || $var_item_type == '2') {
			$nShowJavaId = 'nSwapId';
		}
		else {

			$nShowJavaId = 'nSwapId';
		}

		if ($numRows > 0) { 
			while ($arr = mysqli_fetch_array($rs)) {
				++$nm;
				if ($arr["vFeatured"] == "N") {
					$style = "";
					$featured_img = "";
				}
				else {
					$style = "boldtextblack";
					$featured_img = "&nbsp;<img src=images/featured_star.png>";
				}

				if (trim($arr["vSmlImg"]) == "" || !file_exists($arr["vSmlImg"])) {
					$var_url = "images/nophoto.gif";
					@list($thumb_wid, $thumb_hei) = @getimagesize('./' . $var_url);

					if ($thumb_wid > 250) {
						$thumb_wid = '250';
					}
					else {
						$thumb_wid = $thumb_wid;
					}

					if ($thumb_hei > 250) {
						$thumb_hei = '250';
					}
					else {
						$thumb_hei = $thumb_hei;
					}
				}
				else {
					$thumb_siz = getimagesize($arr["vSmlImg"]);
					$thumb_wid = ($thumb_siz[0]>120)?'120':$thumb_siz[0];
					$thumb_hei = ($thumb_siz[1]>120)?'120':$thumb_siz[1];
					$var_url = $arr["vSmlImg"];
				}
				$imgUrlT = trim($arr["vUrl"]) ;
			 //echo "<pre>";print_r($arr);exit;
				$chkId = $arr['nSwapId'];
				if($var_item_type==1) 
					$var_burl = getDisplayImage($chkId,"w",$imgUrlT);
				else if($var_item_type==2) 
					$var_burl = getDisplayImage($chkId,"s",$imgUrlT);
				else if($var_item_type==3) 
					$var_burl = getDisplayImage($chkId,"sa",$imgUrlT);

												//end switch
												//checking user status

				$condition = "where nUserId='" . $arr['nUserId'] . "'";
				$userStatus = fetchSingleValue(select_rows(TABLEPREFIX . 'online', 'vVisible', $condition), 'vVisible');

												//if posted user not logged user
				if ($arr['nUserId'] != $_SESSION["guserid"]) {
														//fetching user name
					$vLoginName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
					$chatWindow = '<a href="javascript:WindowPop(\'' . ucfirst($arr['nUserId']) . '\');" class="chat_online" title="'.LINK_CHAT_WITH.' ' . ucfirst($vLoginName) . '" title="'.LINK_CHAT_WITH.' ' . ucfirst($vLoginName) . '""></a>';

					$offMessage = '<a   href= "#" data-toggle="modal" data-target="#modal'.$arr[$nShowJavaId].'" title="'.BUTTON_SEND_MESSAGE.'"><i class="flaticon-email"></i></a>';

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
					$userStatus = '<a class="online_msg_link" href="Javascript:WindowPop(\'' . ucfirst($arr['nUserId']) . '\');">'.TEXT_ONLINE. "</a>".$chatWindow;

					break;

					case "N":
					$userStatus = '<a class="offline_msg_link" style="position:relative;">'.TEXT_OFFLINE.'</a><a class="chat_offline"></a>';
					break;

					case "":
					$userStatus = '<a class="offline_msg_link" style="position:relative;">'.TEXT_OFFLINE.'</a><a class="chat_offline"></a>';
					break;
				}

					//cheching thumbnail tooltip enable or not
				switch (DisplayLookUp('ThumbToolTip')) {
					case "Yes":
					$thumClass = ' ';

					//fetching user name
					$condition = "where nUserId='" . $arr['nUserId'] . "'";
					$UserName = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
					@list($width, $height) = @getimagesize('./' . $var_burl);
					if ($width > 250) {
						$width = '250';
					}
					else {
						$width = $width;
					}

					if ($height > 250) {
						$height = '250';
					}
					else {
						$height = $height;
					}

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
					<td>
					<table width="100%"  border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td align="left"><img src="' . $var_burl . '"  border="0"></td>
					</tr>
					</table>
					</td>
					</tr>
					</table>';
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


				<div class="col-lg-4 col-xs-6 col-sm-4 col-md-4">
					<div class="product-more-list-tile">
						<div class="product-more-list-tile-img-sec">
							<div class="layoutmodi1 status_outer">
								<a><?php echo $userStatus; ?></a>                         
							</div>
							<?php if(isset($arr["vCondition"]) && ($arr["vCondition"]!="")) { ?>
								<span class="condition-new-label"><?php echo $arr["vCondition"]; ?></span>    
							<?php } ?>
							<img src="<?php echo $var_burl;?>"  alt="Product"/>
							<div class="action-tiles">
								<?php echo "<a href='" . $detailfile . "?" . $var_param . "=" . $arr["nSwapId"] . "&source=" . $var_source . "&prod=p&catid=$var_catid&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=" . $var_item_desc . "&' class='" . $style . $thumClass . "'>";
								?> <i class="flaticon-shopping-cart"></i></a> 

								<?php if($thumClass!="") {?>
									<a href="#" data-toggle="modal" data-target="#detailproductModal<?php echo $nm; ?>" title="Quick View" ><i class="flaticon-zoom-in"></i></a>
								<?php } ?>
								<!--<a href="#" data-toggle="modal" data-target="#sendMessagemodal" title="Send Message" ><i class="flaticon-email"></i></a>-->
								<?php echo $offMessage ; ?>
							</div>
						</div>



						<div class="product-more-list-tile-bottom-sec">
							<a href="<?php echo $detailfile."?".$var_param . "=" . $arr["nSwapId"] . "&source=" . $var_source . "&prod=p&catid=$var_catid&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=" . $var_item_desc;?>" title="<?php echo strtoupper(htmlentities($arr["vTitle"]))?>">
								<h4 class="product_heading">
									<?php echo (strlen(trim($arr["vTitle"]))>20)?substr(htmlentities($arr["vTitle"]),0,20)."...":htmlentities($arr["vTitle"])?>  
								</h4>
								<h3>
									<?php if($item_price!=="")
									echo $item_price ;
									?>
								</h3>
							</a>
							<?php 
							$posted_by = $userData['vLoginName'];
							if(strlen($userData['vLoginName'])>20)
							{
								$posted_by = substr($userData['vLoginName'], 0,20)."...";
							}
							?> 
							<span>

								<?php echo TEXT_POSTED_BY ?>  <i><a href="user_profile.php?uid=<?php echo $userData['nUserId'] . "&uname=" . urlencode($userData['vLoginName']); ?>" tabindex="-1"><?php echo $posted_by; ?></a></i></span>

								<?php echo "<a class= 'makeoffer-btn' href='" . $detailfile . "?" . $var_param . "=" . $arr["nSwapId"] . "&source=" . $var_source . "&prod=p&catid=$var_catid&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . urlencode($txtSearch) . "&cmbItemType=" . $var_item_desc . "&' class='" . $style . $thumClass . "' >";?> Make offer</a>

							</div>
						</div>
					</div>

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
					<!------QUICK VIEW ---->
					<!--- new Modal -->

					<div id="detailproductModal<?php echo  $nm; ?>" class="modal productdetailpopup" role="dialog">
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

						<!-- New Modal End --> 


						<!----End QUICK VIEW  --->

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
						<div id="modal<?php echo $arr[$nShowJavaId]; ?>" class="modal sendMessagemodal"  role="dialog">
							<button type="button" class="close" data-dismiss="modal">&times;</button>    
							<div id="inlinebox<?php echo $nm ?>" class="modal-dialog">
								<div class="modal-content">

									<div class="modal-body">
										<h4><?php echo SEND_MESSAGE_TEXT; ?></h4>
										<?php if($itemListId!=$arr[$nShowJavaId]) { ?>
											<?php echo '<form action="" method="post" name="frmMsg" id="frmMsg">'; ?>
											<input type="hidden" name="qyery" value="<?php echo $_SERVER['QUERY_STRING']; ?>">
											<input type="hidden" name="ToId" id="ToId">
											<input type="hidden" name="msgId" id="msgId">                
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
													<input type="button" name="btnGo" value="<?php echo BUTTON_SEND_MESSAGE; ?>" class="sendMessagemodal-btn" onClick="return ValidateMsg(<?php echo $arr[$nShowJavaId]; ?>,<?php echo $arr['nUserId']; ?>);"><!--&nbsp;<input type="reset" name="btnGo2" value="<?php //echo BUTTON_RESET; ?>" class="submit">-->
												</label>
											</div>


											<?php echo '</form>'; ?>

										<?php } else { unset($_SESSION['succ_msg_msg']);?>
										<div class="homepage_contentsec maintext2">
											<div class="full_width main_form_inner"><label><?php echo $message; ?></label></div>
										</div>
									<?php }?>
								</div>

							</div>

						</div>
					</div>


					<?php 

				} // end while 
				?>

			</div>
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
			<div class="no-product-items">
				<div class="msg">
					<img src="<?php echo SITE_URL;?>/images/no-product-img.gif">
					<span>- <?php echo NO_PRODUCTS_FOUND;?> -</span>
				</div>
			</div>
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="../libs/jquery/dist/jquery.min.js"><\/script>')</script>
<script src="js/jquery.remodal.js"></script>

<!-- Events -->
<script>
/*  $(document).on("open", ".remodal", function () {
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


	var inst = $("[data-remodal-id=modal2]").remodal();*/

	$('.dropdown-toggle').dropdown();
	
</script>
