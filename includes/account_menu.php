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
if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else
//checking selected file
$tis_page = explode('?',ClientFilePathName($_SERVER['PHP_SELF']));
switch ($tis_page[0]) {
    case "salepaymentsbyme.php":
        $sClass1 = 'class="active"';
        break;

    case "salepaymentsforme.php":
        $sClass2 = 'class="active"';
        break;

    case "swpaymentsbyme.php":
        $sClass3 = 'class="active"';
        break;

    case "swpaymentsforme.php":
        $sClass4 = 'class="active"';
        break;

    case "user_account_payment_details.php":
        $sClass5 = 'class="active"';
        break;
    
    case "success_fee_pending.php":
        $sClass7 = 'class="active"';
        $pTableWidth = '100%';
        break;
}//end switch
?>

<div class="full-width points_tab">
	<ul class="nav nav-tabs nav-justified" role="tablist">
		<?php
		//checking point enable in website
		if ($EnablePoint != '1') {
		?>								
		<li <?php echo $sClass1; ?>><a href="salepaymentsbyme.php" <?php echo $sClass1; ?>><?php echo TEXT_PAYMENTS_MADE; ?>(<?php echo MENU_SALE; ?>)</a></li>
		<li <?php echo $sClass2; ?>><a href="salepaymentsforme.php" <?php echo $sClass2; ?>><?php echo TEXT_PAYMENTS_RECEIVED; ?>(<?php echo MENU_SALE; ?>)</a></li>
		<?php
		}//end if
		?>
		<li <?php echo $sClass3; ?>><a href="swpaymentsbyme.php" <?php echo $sClass3; ?>><?php echo TEXT_PAYMENTS_MADE; ?>(<?php echo MENU_SWAP; ?>)</a></li>
		<li <?php echo $sClass4; ?>><a href="swpaymentsforme.php" <?php echo $sClass4; ?>><?php echo TEXT_PAYMENTS_RECEIVED; ?>(<?php echo MENU_SWAP; ?>)</a></li>
		<li <?php echo $sClass5; ?>><a href="user_account_payment_details.php" <?php echo $sClass5; ?>><?php echo TEXT_COMPLETED_TRANSACTIONS; ?>(<?php echo MENU_SALE; ?>) </a></li>
		<li <?php echo $sClass7; ?> style="border-right:0 none; "><a href="success_fee_pending.php" <?php echo $sClass7; ?>><?php echo MENU_PENDING_ORDER_CONFIRMATIONS; ?>(<?php echo MENU_SWAP; ?>)</a></li>
	</ul>
</div>