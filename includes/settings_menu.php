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
$PGTITLE=ClientFilePathName($_SERVER['PHP_SELF']);

?>
<ul id="maintab" class="shadetabs">
	<li <?php if($PGTITLE=='setconf.php'){echo 'class="selected"';}?>><a href="setconf.php">General Settings</a></li>
	<?php
		//checking point enable in website
		if(ENABLE_POINT!='1')
		{
			//checking listing type enable in website
			if(DisplayLookUp('Listing Type')=='1')
			{
	?>
		<li <?php if($PGTITLE=='listing_combinations.php' || $PGTITLE=='add_listing_combinations.php' || $PGTITLE=='add_listing_combinations.php?nLId='.$_GET['nLId'].'&mode=edit'){echo 'class="selected"';}?>><a href="listing_combinations.php">Listing Fee Range</a></li>
	<?php
			}//end if
		}//end if
	?>
        <?php
        //checking point enable in website
        if(DisplayLookUp('Enable Escrow')=='Yes' && DisplayLookUp('EscrowCommissionType')=='range')
        {
        ?>
		<li <?php if($PGTITLE=='manage_commission_range.php' || $PGTITLE=='manage_commission_range.php?msg='.$_GET["msg"] || $PGTITLE=='add_commission_range.php' || $PGTITLE=="add_commission_range.php?nLId=".$_GET["nLId"]."&mode=edit&oldId=".$_GET["oldId"]
                             || $PGTITLE=="add_commission_range.php?msg=".$_GET["msg"]){echo 'class="selected"';}?>><a href="manage_commission_range.php">Escrow Commission Range</a></li>
	<?php
	}//end if
	?>
                <li <?php if($PGTITLE=='language_contents.php'){echo 'class="selected"';}?>><a href="language_contents.php">Set Language Contents</a></li>
</ul>