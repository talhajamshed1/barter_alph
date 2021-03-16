<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		          |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
//fetching active top links from db
if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != "") {
    $toplinks = session_toplinks(TABLEPREFIX);
}//end if
else {
    $toplinks = toplinks(TABLEPREFIX);
}//end else
//
//setting active link colors
$filename2 = "themes/" . $sitestyle;
$css_file2 = file_get_contents("$filename2");
$color_line2 = explode("\n", strstr($css_file2, '.link_current'));
$selctdMenuColor = explode(":", $color_line2[2]);
$selctdMenuColor = trim(str_replace(";", "", $selctdMenuColor[1]));
?>


<div class="tp_main_navs">
<div class="tp_menus">
<div class="mid_navs">
				<ul>
<?php
$escapeFlag=0;
if ($toplinks != '0') {

    for ($i = 0; $i < mysqli_num_rows($toplinks); $i++) {
        if (mysqli_result($toplinks, $i, 'nCposition') != 'Logged') {
		$escapeFlag=0;
            switch (strtoupper(mysqli_result($toplinks, $i, 'vCategoryTitle'))){
                case 'HOME': $menu_item = MENU_HOME; 
                            $escapeFlag=1;
                            break;
                case 'WISH': $menu_item = MENU_WISH; break;
                case 'SWAP': $menu_item = MENU_SWAP; break;
                case 'SELL': $menu_item = MENU_SELL; break;
                case 'REGISTER': $menu_item = MENU_REGISTER; break;
                case 'LOGIN': $menu_item = MENU_LOGIN; 
					$escapeFlag=1;
					break;
                case 'ONLINE MEMBERS': $menu_item = MENU_ONLINE_MEMBERS; break;
                case 'CATEGORY DISPLAY': $menu_item = MENU_CATEGORY_DISPLAY; break;
                case 'REFERRAL': $menu_item = MENU_REFERRAL; break;
                case 'MY BOOTH': $menu_item = MENU_MYBOOTH; break;
                case 'LOGOUT': $menu_item = MENU_LOGOUT; break;
                default: $menu_item = 'NULL'; break;
            }
			if($escapeFlag!=1)
			{
					
            echo '<li><a href="' . mysqli_result($toplinks, $i, 'vCategoryFile') . '" >' . Highligt($PG_TITLE, mysqli_result($toplinks, $i, 'vCategoryFile'), $menu_item, $selctdMenuColor) . '</a></li>';
			$escapeFlag=0;
			}
        }//end if
    }//end first for loop
}//end if
$sql = "select vLookUpDesc from " . TABLEPREFIX . "lookup where nLookupcode=15 and vLookUpDesc='0'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
if (mysqli_num_rows($result) > 0) {
    echo '<li><a href="' . $rootserver . '/referralinfo.php" >' . Highligt($PG_TITLE, 'referralinfo.php', MENU_REFERRAL, $selctdMenuColor) . '</a></li>';
}//end if

if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != "") {
    $loggedlinks = logged_toplinks(TABLEPREFIX);
    if ($loggedlinks != '0') {
        for ($i = 0; $i < mysqli_num_rows($loggedlinks); $i++) {
		$escapeFlag=0;
            switch (strtoupper(mysqli_result($loggedlinks, $i, 'vCategoryTitle'))){
                case 'HOME': $menu_item = MENU_HOME;
                            $escapeFlag=1;
                            break;
                case 'WISH': $menu_item = MENU_WISH; break;
                case 'SWAP': $menu_item = MENU_SWAP; break;
                case 'SELL': $menu_item = MENU_SELL; break;
                case 'REGISTER': $menu_item = MENU_REGISTER; break;
                case 'LOGIN': $menu_item = MENU_LOGIN; 
						$escapeFlag=1;
				break;
                case 'ONLINE MEMBERS': $menu_item = MENU_ONLINE_MEMBERS; break;
                case 'CATEGORY DISPLAY': $menu_item = MENU_CATEGORY_DISPLAY; break;
                case 'REFERRAL': $menu_item = MENU_REFERRAL; break;
                case 'MY BOOTH': $menu_item = MENU_MYBOOTH; break;
                case 'LOGOUT': $menu_item = MENU_LOGOUT; 
                                $escapeFlag=1;
                                break;
                default: $menu_item = 'NULL'; break;
            }
			if($escapeFlag!=1)
			{
                            echo '<li><a href="' . mysqli_result($loggedlinks, $i, 'vCategoryFile') . '">' . Highligt($PG_TITLE, mysqli_result($loggedlinks, $i, 'vCategoryFile'), $menu_item, $selctdMenuColor) . '</a></li>';
			$escapeFlag=0;
			}
        }//end for loop
    }//end if
}//end if
?>
                    </ul>
					<div class="clear"></div>
					</div>


<div class="clear"></div>
</div>

<div class="search_rightsection">
<form name="frmHSearch" method="post" action="search.php" onSubmit="return ValidSearch();">



<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="26%" align="left"><input name="txtHomeSearch" type="text" class="search" value="<?php echo stripslashes(htmlentities($txtHomeSearch)); ?>" onFocus="javascript:Clear_text(this,'in');" onBlur="javascript:Clear_text(this,'out');"></td>
		<td width="15%" align="center"><input type="submit" value="<?php echo BUTTON_SEARCH; ?>"  height="21" class="login_btn comm_btn_orng_tileeffect"></td>
		<td width="59%"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
				<tr align="center" class="toplinks">
				<td>
				
					</td>
				</tr>
			</table></td>
	</tr>
	</table>
</form>
<div class="clear"></div>
</div>


<div class="clear"></div>
</div>


