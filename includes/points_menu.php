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
if ($_SERVER['SERVER_PORT'] == "80") {
    $imagefolder = $rootserver;
}//end if
else {
    $imagefolder = $secureserver;
}//end else

if ($EnablePoint == '0') {
    header('Location:index.php');
    exit();
}//end if
//checking selected file
$tis_page = explode('?',ClientFilePathName($_SERVER['PHP_SELF']));
switch ($tis_page[0]) {
    case "sent_points.php":
        $sClass1 = 'class="active"';
        $pTableWidth = '100%';
        
        break;

    case "received_points.php":
        $sClass2 = 'class="active"';
        
        $pTableWidth = '100%';
        break;

    case "my_points.php":
        $sClass3 = 'class="active"';
        $pTableWidth = '100%';
         $tabClass   =  'active';
        break;

    case "send_points.php":
        $sClass4 = 'class="active"';
        $pTableWidth = '100%';
         $tabClass   =  'active';
        break;

    case "buy_credits.php":
        $sClass5 = 'class="active"';
        $pTableWidth = '100%';
         $tabClass   =  'active';
        break;

    case "buy_credits.php?" . $_SERVER['QUERY_STRING']:
        $sClass5 = 'class="active"';
        $pTableWidth = '100%';
        
        break;

    case "credits_purchased.php":
        $sClass6 = 'class="active"';
        $pTableWidth = '100%';
       
        break;

}//end switch
?>
<div class="full-width points_tab">
	<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
		<li <?php echo $sClass3; ?>><a href="my_points.php" <?php echo $sClass3; ?>><?php echo str_replace('{point_name}',POINT_NAME,MENU_MY_POINTS); ?></a></li>
		<?php
		//checking admin allow transfer between users
		if (DisplayLookUp('PointTransfer') == '1') {
		?>
		<li <?php echo $sClass4; ?>><a href="send_points.php" <?php echo $sClass4; ?>><?php echo str_replace('{point_name}',POINT_NAME,MENU_SEND_POINTS); ?></a></li>
		<li <?php echo $sClass1; ?>><a href="sent_points.php" <?php echo $sClass1; ?>><?php echo str_replace('{point_name}',POINT_NAME,MENU_SENT_POINTS); ?></a></li>
		<?php
		}//end if
		?>
		<li <?php echo $sClass2; ?>><a href="received_points.php" <?php echo $sClass2; ?>><?php echo str_replace('{point_name}',POINT_NAME,MENU_RECEIVED_POINTS); ?></a></li>
		<li <?php echo $sClass5; ?>><a href="buy_credits.php" <?php echo $sClass5; ?>><?php echo str_replace('{point_name}',POINT_NAME,MENU_BUY_POINTS); ?></a></li>
		<li <?php echo $sClass6; ?>><a href="credits_purchased.php" <?php echo $sClass6; ?>><?php echo MENU_PURCHASE_LIST; ?></a></li>
	</ul>
</div>
<script type="text/javascript">
     var $jbs=jQuery.noConflict();
  (function($) {
      fakewaffle.responsiveTabs( [ 'phone', 'tablet' ] );
  })(jQuery);
  
   $jbs( '#myTab a' ).click( function ( e ) {
        e.preventDefault();
       //  $jbs('#'+$jbs(this).attr("id")).show();
            //$(".booth").removeClass("selected");
         $jbs('#'+$jbs(this).attr("id")).addClass("active");
       var go_to_url = ($jbs( this ).attr("href"));
        location.href = go_to_url;
        //$("#filter-bar .current").attr("href");
        $jbs( this ).tab( 'show' );
    } );
</script>