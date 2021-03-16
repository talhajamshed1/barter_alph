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

$sql = "SELECT s.vTitle, s.nSaleId, s.vFeatured, s.vUrl, s.vDescription, s.nQuantity, s.dPostDate, 
				s.nValue,s.nPoint, s.vCondition, s.vType, s.vBrand, s.vYear, u.vLoginName 
				FROM  " . TABLEPREFIX . "sale s 
				LEFT JOIN " . TABLEPREFIX . "users u ON u.nUserId = s.nUserId
				WHERE s.vFeatured = 'Y' AND s.nQuantity >'0' AND s.vDelStatus = '0' AND u.vStatus = '0'
				AND u.nUserId <> '".$_SESSION['guserid']."' ORDER BY s.dPostDate DESC LIMIT 0,4 ";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$displayTooltip = DisplayLookUp('ThumbToolTip');
if($displayTooltip == 'Yes')
{
	$toolTip = true;
}
else 
{
	$toolTip = false;
}
?>

<div class="featured_wrps">
	<h2 class="headings1">
    	<?php echo HEADING_FEATURED_ITEMS;?>
    </h2>
       <div class="featured_wrps">
      <?php  
         if (mysqli_num_rows($result) != 0) {
                        ?>
    <div class="row">
        <?php  $i=0;
			while ($arr = mysqli_fetch_array($result)) {

							 $var_burl = getDisplayImage($arr["nSaleId"], "sa", $arr["vUrl"]);
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

			 if($i<6){
		?>
    	<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ">
        	<div class="featured_product">
            	<div class="featured_hoverdiv hover-zoom">
                	<?php /*?><a href="<?php echo $rootserver ?>/swapitemdisplay.php?saleid=<?php echo $arr["nSaleId"]?>&source=sa" ><?php echo TEXT_MOUSE_OVER;?></a><?php */?>
                	<?php if($toolTip) {?>
                	<a href="#modal<?php echo $i;?>"><?php echo TEXT_MOUSE_OVER;?></a>
                	<?php }?>
                </div>
            	<div class="ftrd_prdct_img"><img src="<?php echo $var_burl;?>" /></div>
                <p class="ftrd_prdct_name"><a href="<?php echo $rootserver ?>/swapitemdisplay.php?saleid=<?php echo $arr["nSaleId"]?>&source=sa" ><?php echo (strlen(trim($arr["vTitle"]))>28)?substr(htmlentities($arr["vTitle"]),0,28)."...":htmlentities($arr["vTitle"])?></a></p>
               


            </div>
        </div>
       
        <?php } 
        ?>
        <!------QUICK VIEW ---->
	<div class="remodal" data-remodal-id="modal<?php echo $i;?>">
	
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
				<div class="welcome"><h4><?php echo (strlen(trim($arr["vTitle"]))>21)?strtoupper(substr(htmlentities($arr["vTitle"]),0,21))."...":strtoupper(htmlentities($arr["vTitle"]))?></h4></div>
				<?php if($arr['vDescription']!='') { ?>   
				<div class="pop_description"><p><?php echo substr(htmlentities($arr['vDescription']),0,200);?> </p></div>
				<?php } ?>
				<div class="profs_bld"><b>Posted On </b> &nbsp; <?php echo date('m/d/Y', strtotime($arr["dPostDate"])); ?></div>
				<div class="profs_bld"><b>Posted By </b> &nbsp; <?php echo $arr['vLoginName']; ?></div>
				<div class="profs_bld"><b>Brand </b> &nbsp; <?php echo $arr["vBrand"];?></div>
				<div class="profs_bld"><b>Type </b> &nbsp; <?php echo $arr["vType"];?></div>
				<div class="profs_bld"><b>Condition </b> &nbsp; <?php echo $arr["vCondition"];?></div>
				<div class="profs_bld"><b>Year </b> &nbsp; <?php echo $arr["vYear"];?></div>
			</div>
		</div>
	</div>
	
	</div>
	<!----End QUICK VIEW  --->
	<?php $i++; } ?>
      
    </div>
  <?php } else {?>  
  	<div class="col-lg-12">
  		<div class="box1_mdfd">
        	Sorry No featured products available.
        </div>
   	</div>    
  <?php }?>
    </div>
    <div class="clear"></div>
</div>