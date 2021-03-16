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
?>
<div class="body1 category-drop-body" >
	<button class="category-drop" id="category-drop"><img class="img-responsive" src="<?php echo SITE_URL?>/images/menu-btn-ic.svg"></button>
	<div id="menu" class="side-category-menu">
		<div class="side-category-menu-inner">
		<i class="flaticon-cancel" id="category-close"></i>

		<h4>Categories</h4>
		<ul>
			<?php
			$sql = "SELECT * FROM " . TABLEPREFIX . "category C
						LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
						where C.nParentId = '0' order by C.nPosition,C.nParentId ASC";
			$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
			if (mysqli_num_rows($result) != 0) {
				while ($row = mysqli_fetch_array($result)) {
					//checking showing which page
					switch (substr($PG_TITLE, 0, -8)) {
						case "catwiseproducts.php":
							$showpath = 'catwiseproducts.php?catid=' . $row["nCategoryId"];
							break;

						default:
							$showpath = 'categorydetail.php?catid=' . $row["nCategoryId"] . '&categorydesc=' . urlencode(htmlentities($row["vCategoryDesc"]));
							break;
					}//end switch

					echo '<li><a href="' . $rootserver . '/categorydetail.php?catid=' . $row["nCategoryId"] . "&categorydesc=" . urlencode(htmlentities($row["vCategoryDesc"])) . '">' . Highligt($PG_TITLE, $showpath, htmlentities($row["vCategoryDesc"]), '#000000') . ' <span>' . toGetTotal($row["nCategoryId"], $toplinks) . '</span></a></li>';
				}//end while
			}//end if
			else {
				echo '<li>&nbsp;&nbsp;&nbsp;&nbsp;'.MESSAGE_SORRY_NO_CATEGORY.'</li>';
			}//end else
			?>

		</ul>
	</div>
</div>
</div>