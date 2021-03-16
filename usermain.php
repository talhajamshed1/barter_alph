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

include("./includes/config.php");
include("./includes/session.php");
include("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
include("./includes/session_check.php");

$const = get_defined_constants(true);
//echo "<pre>";
//print_r($const['user']['MENU_/^*$/']);
//echo "</pre>";
include_once('./includes/gpc_map.php');
include_once('./includes/title.php');

extract($_GET);
$itemPerPage = 2;

$page = $_REQUEST['page'];

if($page == '')
	$page=(ENABLE_POINT != '1') ? 'saleslist' : 'swaplist';


if(!isset($_SESSION['pagenav']) || $_GET['page'] =='') {
	$_SESSION['pagenav'] = array('saleslist'=>0,
		'swaplist'=>0,
		'wishlist'=>0,
		'myoffers'=>0,
		'offersforme'=>0,
		'saleoffers'=>0,
		'saleoffersforme'=>0);
}
$numRecordsPage = 5;
$numPageLinks = 5;
?>
<script type="text/javascript">
	function getUrlParameter(sParam)
	{
		var sPageURL = window.location.search.substring(1);
		var sURLVariables = sPageURL.split('&');
		for (var i = 0; i < sURLVariables.length; i++) 
		{
			var sParameterName = sURLVariables[i].split('=');
			if (sParameterName[0] == sParam) 
			{
				return sParameterName[1];
			}
		}
	}        

	function getUrlParams(url) {
		var params = {};
		url.substring(1).replace(/[?&]+([^=&]+)=([^&]*)/gi,
			function (str, key, value) {
				params[key] = value;
			});
		return params;
	}
	function blinkIt()
	{
		if (!document.all) return;
		else
		{
			for(i=0;i<document.all.tags('blink').length;i++)
			{
				s=document.all.tags('blink')[i];
				s.style.visibility=(s.style.visibility=='visible') ?'hidden':'visible';
            }//edn for loop
        }//end else
    }//edn function
</script>
<style>

	#s1_content{
		display:<?php echo (($page=='saleslist')?'block':'none') ?>;
	}
	#s2_content{
		display:<?php echo (($page=='swaplist')?'block':'none') ?>;
	}
	#s3_content{
		display:<?php echo (($page=='wishlist')?'block':'none') ?>;
	}
	#s4_content{
		display:<?php echo (($page=='myoffers')?'block':'none') ?>;
	}
	#s5_content{
		display:<?php echo (($page=='offersforme')?'block':'none') ?>;
	}
	#s6_content{
		display:<?php echo (($page=='saleoffers')?'block':'none') ?>;
	}
	#s7_content{
		display:<?php echo (($page=='saleoffersforme')?'block':'none') ?>;
	}
</style>



<body onLoad="timersOne();setInterval('blinkIt()',500);">
	<?php include_once('./includes/top_header.php'); ?>
	<script src="js/responsive-tabs-2.3.2.js"></script>
	<script type="text/javascript">
     // var $jqr1=jQuery.noConflict();
     var thePage = getUrlParameter('page');
     
     $jqr(document).ready(function() {

     	if (typeof thePage != "undefined")
     	{ 
     		$jqr('div.tab-content').children().removeClass('active');

     		switch(thePage)
     		{
     			case 'saleslist':
     			pathName = '#s1';
     			$jqr('#myTab a[href="' + pathName + '"]').closest('li').addClass('active');
     			$jqr('#s1').addClass("active");
     			break; 
     			case 'swaplist':
     			pathName = '#s2';
     			$jqr('#myTab a[href="' + pathName + '"]').closest('li').addClass('active');
     			$jqr('#s2').addClass("active");
     			break;      
     			case 'wishlist':
     			pathName = '#s3';
     			$jqr('#myTab a[href="' + pathName + '"]').closest('li').addClass('active');
     			$jqr('#s3').addClass("active");  
     			break;     
     			case 'myoffers':
     			pathName = '#s4';
     			$jqr('#myTab a[href="' + pathName + '"]').closest('li').addClass('active');
     			$jqr('#s4').addClass("active");  
     			break;
     			case 'offersforme':
     			pathName = '#s5';
     			$jqr('#myTab a[href="' + pathName + '"]').closest('li').addClass('active');
     			$jqr('#s5').addClass("active");  
     			break;
     			case 'saleoffers':
     			pathName = '#s6';
     			$jqr('#myTab a[href="' + pathName + '"]').closest('li').addClass('active');
     			$jqr('#s6').addClass("active");  
     			break;
     			case 'saleoffersforme':
     			pathName = '#s7';
     			$jqr('#myTab a[href="' + pathName + '"]').closest('li').addClass('active');
     			$jqr('#s7').addClass("active");  
     			break;
     		}
     	} else{
            //alert('here22');
            <?php if (ENABLE_POINT != '1') { ?>
            	pathName = '#s1';
            	$jqr('#myTab a[href="' + pathName + '"]').closest('li').addClass('active');
            	<?php } else { ?>
            		pathName = '#s2';
            		$jqr('#myTab a[href="' + pathName + '"]').closest('li').addClass('active');
            		$jqr('#s2').addClass("active");  
            		<?php } ?>     
            	}



        // Delete Records
        
        $jqr(".clsDelete").click(function(e){
        	var url = $jqr(e.target).attr("href");
        	e.preventDefault();
        	var params = getUrlParams(url); 
        	var saleid  = params["saleid"];
        	var type    = params["source"];
        	var catid   = params["catid"];
        	var page    = params["page"];  
        	switch(type)
        	{
        		case 'sa':
        		pathName = 'saleslist';

        		break; 
        		case 's':
        		pathName = 'swaplist';

        		break;

        		case 'w':
        		pathName = 'wishlist';

        		break;   

        	}     

        	if(confirm('<?php echo TEXT_CONFIRM_DELETE;?>'))
        	{             
        		$jqr.ajax({
                type: "POST", // HTTP method POST or GET
                url: "delete_page.php", //Where to make Ajax calls
                dataType:"text", // Data type, HTML, json etc.
                data: {delete_id : saleid,source:type,catid:catid}, //Form variables
                success:function(response){

                	if(response=='success')
                	{
                		alert('<?php echo MESSAGE_ITEM_DELETED;?>');
                        //$jqr(e).closest('tr').remove();
                        window.location = 'usermain.php?page='+pathName;
                        //window.location.href=usermain.php?page=+page;
                    }else{
                    	alert('<?php echo ERROR_ITEM_CANNOT_DELETED;?>');

                    }

                    //$("#contentText").val(''); //empty text field on successful

                },
                error:function (xhr, ajaxOptions, thrownError){
                    //$("#FormSubmit").show(); //show submit button
                    //$("#LoadingImage").hide(); //hide loading image
                    alert(thrownError);
                }
            });
        	}
        });
        
    });



</script>
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">

				<?php 
				if($_SESSION["updated_msg"] != ''){
					$message = $_SESSION["updated_msg"];
					unset($_SESSION["updated_msg"]);
				}
				if (isset($message) && $message != '') {
					?>
					<div class="success_msg">
						<span class="glyphicon glyphicon-ok-circle"></span><?php echo  $message;?>
					</div>
					<br><br>

					<?php }//end if?>
					<div class="full-width">
						<div class="clear">&nbsp;</div> 
						<div>

							<div class="">

								<ul class="nav nav-tabs nav-justified usermain-tab" role="tablist" id="myTab">
									<?php
									if (ENABLE_POINT != '1') {
										?>
										<li <?php if($page=='saleslist') { echo "class=active" ; } ?>><a href="#s1" class="booth" ><?php echo HEADING_LATEST_SALES_ADDITIONS; ?></a></li>
										<?php
									}
									?>
									<li <?php if($page=='swaplist') { echo "class=active" ; } ?>><a href="#s2" class="booth"><?php echo HEADING_LATEST_SWAP_ADDITIONS; ?></a></li>
									<li <?php if($page=='wishlist') { echo "class=active" ; } ?>><a href="#s3"  class="booth"><?php echo HEADING_LATEST_WISH_ADDITIONS; ?></a></li>
									<li <?php if($page=='myoffers') { echo "class=active" ; } ?>><a href="#s4"  class="booth"><?php echo HEADING_MY_OFFERS; ?></a></li>
									<li <?php if($page=='offersforme') { echo "class=active" ; } ?>><a href="#s5"  class="booth"><?php echo HEADING_OFFERS_FOR_ME; ?></a></li>
									<?php
									if (ENABLE_POINT != '1') {
										?>
										<li <?php if($page=='saleoffers') { echo "class=active" ; } ?>><a href="#s6"  class="booth"><?php echo HEADING_MY_SALES_ORDERS; ?></a></li>
										<li <?php if($page=='saleoffersforme') { echo "class=active" ; } ?>><a href="#s7"  class="booth"><?php echo HEADING_SALES_ORDERS_FOR_ME; ?></a></li>
										<?php
									}
									?>
								</ul>
								<div class=" tabcontainer_new tab-content responsive">
									<?php
									if (ENABLE_POINT != '1') {
										if (in_array('Sell', $MenuEnableArray)) { 
											?>
											<div class="tab-pane active" id="s1"> 
												<div class="table-responsive">
													<table style="width: 100%;" class="table table-bordered" cellpadding="0" cellspacing="0" border="0">
														<tr align="left">
															<td colspan="6"><img src="images/featured_star.png" alt="<?php echo TEXT_FEATURED; ?>" title="<?php echo TEXT_FEATURED; ?>">
																&nbsp;<strong><?php echo TEXT_FEATURED; ?></strong>
															</td>
														</tr>

														<tr>
															<th align="center" valign="top"><?php echo TEXT_SLNO; ?></th>
															<th align="left" colspan="2" valign="top"><?php echo TEXT_CATEGORY; ?></th>
															<th align="left" valign="top"><?php echo TEXT_TITLE; ?></th>
															<th align="left" valign="top"><?php echo TEXT_DATE; ?></th>
															<th align="left" valign="top"><?php echo TEXT_ACTION; ?></th>
														</tr>
														<?php
														if (in_array('Sell', $MenuEnableArray)) {
															include_once("./includes/saleslist.php");
															func_sale_list(1);

														}//end if


														?>
													</table>
												</div>
											</div>
											<?php } 
										}
										?>     
										<div class="tab-pane" id="s2">
											<div class="table-responsive">
												<table  style="width: 100%;" class="table table-bordered " cellpadding="0" cellspacing="0" border="0">
													<tr align="left" bgcolor="#FFFFFF">
														<td colspan="6"><img src="images/featured_star.png"  alt="<?php echo TEXT_FEATURED; ?>" title="<?php echo TEXT_FEATURED; ?>">
															&nbsp;<strong><?php echo TEXT_FEATURED; ?></strong>
														</td>
													</tr>

													<tr>
														<th align="center" valign="top"><?php echo TEXT_SLNO; ?></th>
														<th align="left" valign="top"><?php echo TEXT_CATEGORY; ?></th>
														<th align="left" valign="top"><?php echo TEXT_TITLE; ?></th>
														<th align="left" valign="top"><?php echo TEXT_DATE; ?></th>
														<th align="left" valign="top"><?php echo TEXT_STATUS; ?></th>
														<th align="left" valign="top"><?php echo TEXT_ACTION; ?></th>
													</tr>
													<?php 
													include_once("./includes/swaplist.php");
													func_swap_list(1);
													?>
												</table>
											</div>
										</div>

										<div class="tab-pane" id="s3">
											<div class="table-responsive">
												<table  style="width: 100%;" class="table table-bordered " cellpadding="0" cellspacing="0" border="0">
													<!-- /Here the latest sales list comes -->
													<!-- Here the latest wish list comes -->
													<tr align="left" bgcolor="#FFFFFF">
														<td colspan="6"><img src="images/featured_star.png"  alt="<?php echo TEXT_FEATURED; ?>" title="<?php echo TEXT_FEATURED; ?>">
															&nbsp;<strong><?php echo TEXT_FEATURED; ?></strong>
														</td>
													</tr>

													<tr>
														<th align="center" valign="top"><?php echo TEXT_SLNO; ?></th>
														<th align="left" colspan="2" valign="top"><?php echo TEXT_CATEGORY; ?></th>
														<th align="left" valign="top"><?php echo TEXT_TITLE; ?></th>
														<th align="left" valign="top"><?php echo TEXT_DATE; ?></th>
														<th align="left" valign="top"><?php echo TEXT_ACTION; ?></th>
													</tr>
													<?php
													include_once("./includes/wishlist.php");
													func_wish_list(1);
													?>
												</table>
											</div>                      
										</div>
										<div class="tab-pane" id="s4">
											<div class="table-responsive">
												<table  style="width: 100%;" class="table table-bordered " cellpadding="0" cellspacing="0" border="0">  <tr>
													<th align="center" valign="top"><?php echo TEXT_SLNO; ?></th>
													<th align="left" valign="top"><?php echo TEXT_TITLE; ?></th>
													<th align="left" valign="top"><?php echo OFFERED_TO; ?></th>
													<th align="left" valign="top"><?php echo TEXT_DATE; ?></th>
													<th align="left" valign="top"><?php echo TEXT_STATUS; ?></th>
													<th align="left" valign="top"><?php echo TEXT_OFFER_TYPE; ?></th>
													<th align="left" valign="top"><?php echo TEXT_ACTION; ?></th>
												</tr>
												<?php
												include_once("./includes/myoffers.php");
												?>
											</table>
										</div>
									</div>
									<div class="tab-pane" id="s5"> 
										<div class="table-responsive">
											<table style="width: 100%;" class="table table-bordered" cellpadding="0" cellspacing="0" border="0"> <tr>
												<th align="center" valign="top"><?php echo TEXT_SLNO; ?></th>
												<th align="left" valign="top"><?php echo TEXT_TITLE; ?></th>
												<th align="left" valign="top"><?php echo OFFERED_BY; ?></th>
												<th align="left" valign="top"><?php echo TEXT_DATE; ?></th>
												<th align="left" valign="top"><?php echo TEXT_STATUS; ?></th>
												<th align="left" valign="top"><?php echo TEXT_OFFER_TYPE; ?></th>
												<th align="left" valign="top"><?php echo TEXT_ACTION; ?></th>
											</tr>
											<?php
											include_once("./includes/offersforme.php");
											?>
										</table>
									</div>
								</div>

								<?php 
										//checking point enable in website
								if (ENABLE_POINT != '1') {
									if (in_array('Sell', $MenuEnableArray)) {
										?>

										<div class="tab-pane" id="s6"> 
											<div class="table-responsive">
												<table style="width: 100%;" class="table table-bordered " cellpadding="0" cellspacing="0" border="0"> <tr>
													<th align="center" valign="top"><?php echo TEXT_SLNO; ?></th>
													<th align="left" valign="top"><?php echo TEXT_TITLE; ?></th>
													<th align="left" valign="top"><?php echo TEXT_SELLER_NAME; ?></th>
													<th align="left" valign="top"><?php echo TEXT_FEEDBACK; ?></th>
													<th align="left" valign="top"><?php echo TEXT_DATE; ?></th>
													<th align="left" valign="top"><?php echo TEXT_ACTION; ?></th>
												</tr>
												<?php 
												include_once("./includes/saleoffers.php");

														//some error with this function please check - rafeeq
												func_sale_offers(1); 
														// <!-- /Here the latest wish list comes -->
														// <!-- Here the latest wish list comes -->
												?></table>
											</div>
										</div>

										<div class="tab-pane" id="s7"> 
											<div class="table-responsive">
												<table style="width: 100%;" class="table table-bordered table-hover table-responsive" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<th align="center" valign="top"><?php echo TEXT_SLNO; ?></th>
														<th align="left" valign="top"><?php echo TEXT_TITLE; ?></th>
														<th align="left" valign="top"><?php echo TEXT_BUYER; ?></th>
														<th align="left" valign="top"><?php echo TEXT_DATE; ?></th>
														<th align="left" valign="top"><?php echo TEXT_ACTION; ?></th>
													</tr>

													<?php 
													include_once("./includes/saleoffersforme.php"); 
													func_sale_offers_forme(1);
													?>

												</table>
												<?php }

											}//end if ?>
										</div>
									</div>

								</div>
							</div>



						</div>
					</div>

					<div class="row">
						<div class="col-lg-12"><?php include('./includes/sub_banners.php'); ?></div>
					</div>

				</div>
			</div>
		</div>
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
		//alert($jbs( this ).attr("id"));
		$jbs( this ).tab( 'show' );
	} );
</script>

<?php require_once("./includes/footer.php"); ?>
