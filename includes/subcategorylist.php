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
?>

<?php

 if ($_REQUEST["txtSearch"] != "") {
    $txtSearch = $_REQUEST["txtSearch"];
    $cmbSearchType = $_REQUEST["cmbSearchType"];
}//end else if

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
   /* else if ($cmbSearchType == TEXT_TITLE) {
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
    }//end else if*/
}//end if
$sql1 = "SELECT C.nCategoryId,L.vCategoryDesc,C.nParentId,C.nCount,C.cat_image FROM " . TABLEPREFIX . "category C
                LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "'";
 $sql1 .= " where C.nParentId  = '" . addslashes($_GET["catid"]) . "'  ".$qryopt." ORDER BY C.nPosition,C.nParentId ASC ";
$result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql1));
/*
Call the function:
I've used the global $_GET array as an example for people
running php with register_globals turned 'off' :)
*/
$navigate = pageBrowser($totalrows, 12, 12, "&catid=".$_GET[catid]."&", $_GET[numBegin], $_GET[start], $_GET[begin], $_GET[num]);
//execute the new query with the appended SQL bit returned by the function
//$sql1 = $sql1 . $navigate[0];
$sql1=dopaging($sql1,'',PAGINATION_LIMIT);
$numRecords = mysqli_query($conn, $sql1);
if($numRecords>0) {
    
$pagenumber     =   getCurrentPageNum();
$defaultUrl     =   $_SERVER['PHP_SELF'];
$querysting     =   "&catid=".$_GET[catid]."&";
$paginationUrl  =   $_SERVER['PHP_SELF']."?p=[p]".$querysting;
$pageString     =   getnavigation($totalrows);
include_once("lib/pager/pagination.php"); 
$pg = new bootPagination($pagenumber,PAGINATION_LIMIT,$totalrows,$defaultUrl,$paginationUrl);
}
$result2 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
?>
<!----------------------- plain HTML----------------->
<div class="category-list">
                <div class="row">
<?php
$catcount=-1;
$cat_image = '';
if (mysqli_num_rows($result2) > 0) {
    while ($arr = mysqli_fetch_array($result2)) {
        $cat_image = $arr["cat_image"] ==''?'nocatimage.jpg':$arr["cat_image"];
        $cat_image = "banners/". $cat_image;
         if(!is_file($cat_image) && !file_exists($cat_image)) {
                             $cat_image = "banners/nocatimage.jpg";
                }
        $catcount++;
        $catdesc= stripslashes(htmlentities($arr["vCategoryDesc"]));
        $catlength=18;
        if(strlen($catdesc) > $catlength ) {
            $catdesc = substr($catdesc, 0,$catlength) . '....' ;
        }
        ?>

   
   	
    	<!-- <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 ">
			<div class="categorylisting_wrapper">
        	<div class="contain_catgeory">
            	<div class="contain_image">	
                	 <a href="<?php echo  $PHP_SELF . '?catid=' . $arr["nCategoryId"] . '&categorydesc=' . urlencode(stripslashes($arr["vCategoryDesc"]))?>"><img src="<?php echo $cat_image?>" alt="" ></a>
                </div>
                <div class="contain_cat_name">
                	<?php echo '<a href="' . $PHP_SELF . '?catid=' . $arr["nCategoryId"] . '&categorydesc=' . urlencode(stripslashes($arr["vCategoryDesc"])) . '">' . $catdesc . '</a>&nbsp;(' . toGetTotal($arr["nCategoryId"], $toplinks) . ')'; ?>
                </div>
                <div class="clear"></div>
            </div>
			</div>
        </div> -->        
                    <div class="col-md-4 col-xs-6 col-sm-4">
                        <a href="<?php echo  $PHP_SELF . '?catid=' . $arr["nCategoryId"] . '&categorydesc=' . urlencode(stripslashes($arr["vCategoryDesc"]))?>">
                            <div class="category-list-tile">
                                <div class="category-list-tile-img">
                                    <img src="<?php echo $cat_image?>" alt="" >
                                </div>                     
                                <span><?=$catdesc.'<i>('.toGetTotal($arr["nCategoryId"], $toplinks).')</i>';?></span>
                            </div>
                        </a>
                    </div>
                 
        
      
       

<?php
    }
 ?>
 <div class="col-lg-12">
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
</div>   
 <?php   
  }
else {
    echo "<script> document.location.href='catwiseproducts.php?catid=" . $_GET["catid"] . "';</script>";
}//end else
?>
</div>


 </div>
   
   <!----------------------- plain HTML----------------->