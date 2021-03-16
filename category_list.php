<?php
include ("./includes/config.php");
session_start();
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category

$nCatId		=       $_GET['q'];
$level		=	$_REQUEST['lev'];
$newlevel	=	$level+1;

//display root category starts here
$sqlCategory = mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."category C
        LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
        where C.nParentId = '".$nCatId."' order by C.nPosition desc") or die(mysqli_error($conn));
if(mysqli_num_rows($sqlCategory)>0)
{
?>
	<select name="ddlCateory" class="textbox2" onChange="showCategory(this.value,<?php echo $newlevel;?>);" multiple>
<?php
		while($arrCategory = mysqli_fetch_array($sqlCategory))
		{
			echo '<option value="'.$arrCategory["nCategoryId"].'">'.$arrCategory["vCategoryDesc"].'</option>';
		}//end while
?>
	</select>
<?php
}//end if
else
{
	echo '<img src="images/success.gif" width="32" height="32" border="0">'.MESSAGE_YOU_SELECTED_CATEGORY.'. <a href="catwiseproducts.php?catid='.$nCatId.'"><b>'.LINK_CLICK_CONTINUE.'</b>.</a>';
}//end else
?>
<span id="txtDisplayCategory<?php echo $newlevel;?>"></span>
