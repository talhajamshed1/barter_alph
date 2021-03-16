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
if ($_SESSION['guserid']!=''){
if($_SERVER['SERVER_PORT']=="80")
{
 	$imagefolder=$rootserver;
}//end if
else
{
   $imagefolder=$secureserver;
}//end else
for($i=0;$i<mysqli_num_rows($toplinks);$i++)
{
	$MenuEnableArray[]=mysqli_result($toplinks,$i,'vCategoryTitle');
}//end for loop


//fetching total counts
$sale_tot=fetchSingleValue(select_rows(TABLEPREFIX.'sale','count(*) as cnt',"where vDelStatus='0' and nUserId='".$_SESSION["guserid"]."'"),'cnt');
$wish_tot=fetchSingleValue(select_rows(TABLEPREFIX.'swap','count(*) as cnt',"where vPostType='wish' and vDelStatus='0' and nUserId='".$_SESSION["guserid"]."'"),'cnt');
/*$countSql = "select count(*) as cnt from ".TABLEPREFIX."swap where vSwapStatus < 2 and 
(nSwapId in (select nSwapId from ".TABLEPREFIX."swap where nUserId='".$_SESSION["guserid"]."') and
nSwapId not in
 (select nSwapReturnId from ".TABLEPREFIX."swaptxn where nUserReturnId='".$_SESSION["guserid"]."') )
 and vDelStatus='0' and nUserId='".$_SESSION["guserid"]."'";
echo $countSql;
$countRs = mysqli_query($conn, $countSql) or die(mysqli_error($conn));
$countRw = mysqli_fetch_array($countRs);*/
$swap_tot=fetchSingleValue(select_rows(TABLEPREFIX.'swap','count(*) as cnt',"where vPostType='swap' and vDelStatus='0' and nUserId='".$_SESSION["guserid"]."'"),'cnt');
//$swap_tot = $countRw['cnt'];
$Messages_tot=fetchSingleValue(select_rows(TABLEPREFIX.'messages','count(*) as cnt',"where vToDel='N' and nToUserId='".$_SESSION["guserid"]."'"),'cnt');
$MegSent_tot=fetchSingleValue(select_rows(TABLEPREFIX.'messages','count(*) as cnt',"where vFromDel='N' and nFromUserId='".$_SESSION["guserid"]."'"),'cnt');
$feedback_tot=fetchSingleValue(select_rows(TABLEPREFIX."userfeedback as f,".TABLEPREFIX."users as u",'count(*) as cnt',
										"where f.nUserId='".$_SESSION["guserid"]."' and f.nUserFBId=u.nUserId"),'cnt');
//checking array
if(is_array($MenuEnableArray))
{
	if(in_array('Sell',$MenuEnableArray))
	{
		$SaleMenu='<li><a href="'.$rootserver.'/addsale.php?type=sale">'.Highligt($PG_TITLE,'addsale.php?type=sale',MENU_ADD_SALE,$selctdMenuColor).' ('.$sale_tot.')</a></li>';
	}//end if

	if(in_array('Swap',$MenuEnableArray))
	{
		$SwapMenu='<li><a href="'.$rootserver.'/addsale.php?type=swap">'.Highligt($PG_TITLE,'addsale.php?type=swap',MENU_ADD_SWAP,$selctdMenuColor).' ('.$swap_tot.')</a></li>';
	}//end if

	if(in_array('Wish',$MenuEnableArray))
	{
		$WishMenu='<li><a href="'.$rootserver.'/addsale.php?type=wish">'.Highligt($PG_TITLE,'addsale.php?type=wish',MENU_ADD_WISH,$selctdMenuColor).' ('.$wish_tot.')</a></li>';
	}//end if
}//end if

//checking point enable in website
if($EnablePoint!='0')
{
	$accntFile='swpaymentsbyme.php';
}//end if
else
{
	$accntFile='salepaymentsbyme.php';
}//end else
?>
<div class="body1">
                                <div id="menu" class="profile-side-menu">
                                  <ul>
									 <?php echo $SaleMenu;?>
									 <?php echo $SwapMenu;?>
									 <?php echo $WishMenu;?>
									 <li><a href="<?php echo $rootserver?>/<?php echo $accntFile;?>"><?php echo Highligt($PG_TITLE,'salepaymentsbyme.php',MENU_ACC_SUMMARY,$selctdMenuColor);?></a></li>
									<?php if(DisplayLookUp('Enable Escrow')=='Yes')
										  {
											  //checking point enable in website
											if(ENABLE_POINT!='1')
											{
							  		 ?>
									 <li><a href="<?php echo $rootserver?>/escrowpayments.php"><?php echo Highligt($PG_TITLE,'escrowpayments.php',MENU_ESCROW_PAYMENTS,$selctdMenuColor);?></a></li>
								    <?php 
											}//end if
										}//end if?>
									 <li><a href="<?php echo $rootserver?>/editprofile.php"><?php echo Highligt($PG_TITLE,'editprofile.php',MENU_EDIT_PROFILE,$selctdMenuColor);?></a></li>
                                                                         <?php $userProfileDetails=(isset($_GET['uid']) && $_GET['uid']==$_SESSION["guserid"]) ? ClientFileName($_SERVER['PHP_SELF']) : $PG_TITLE ; ?>
									 <li><a href="<?php echo $rootserver?>/user_profile.php?uid=<?php echo $_SESSION["guserid"]; ?>"><?php echo Highligt($userProfileDetails,'user_profile.php',MENU_VIEW_MYPROFILE,$selctdMenuColor);?></a></li>
									 <?php
										//checking point enable in website
										if(ENABLE_POINT!='0')
										{
											echo '<li><a href="'.$rootserver.'/my_points.php">'.Highligt($PG_TITLE,'my_points.php',POINT_NAME,$selctdMenuColor).'</a></li>';
											echo '<li><a href="'.$rootserver.'/buy_credits.php">'.Highligt($PG_TITLE,'buy_credits.php',MENU_BUY.' '.POINT_NAME,$selctdMenuColor).'</a></li>';
										}//end if
										?>
									 <li><a href="<?php echo $rootserver?>/message.php"><?php echo Highligt($PG_TITLE,'message.php',MENU_RECEIVED_MESSAGES,$selctdMenuColor).'&nbsp;('.$Messages_tot.')';?></a></li>
									 <li><a href="<?php echo $rootserver?>/message_sent.php"><?php echo Highligt($PG_TITLE,'message_sent.php',MENU_SENT_MESSAGES,$selctdMenuColor).'&nbsp;('.$MegSent_tot.')';?></a></li>
									 <?php
									  //checking point enable in website
									  if(ENABLE_POINT!='1')
									  {
									 ?>
									 <li><a href="<?php echo $rootserver?>/viewfeedbacks.php"><?php echo Highligt($PG_TITLE,'viewfeedbacks.php',MENU_VIEW_FEEDBACKS,$selctdMenuColor).'&nbsp;('.$feedback_tot.')';?></a></li>
									 <?php
									   }//end if
									 ?>
									 <!--<li><a href="<?php //echo $rootserver; ?>/settings.php"><?php //echo Highligt($PG_TITLE,'settings.php',MENU_SETTINGS,$selctdMenuColor);?></a></li>-->
							<?php
									//if(DisplayLookUp('15')!='1' && DisplayLookUp('Enable Escrow')!='Yes')	
                                                                        if(DisplayLookUp('15')!='1' && DisplayLookUp('plan_system')=='yes')	
									{
							?>
 									 <li><a href="<?php echo $rootserver?>/plan_orders.php"><?php echo Highligt($PG_TITLE,'plan_orders.php',MENU_PLAN_ORDERS,$selctdMenuColor);?></a></li>
									 <li><a href="<?php echo $rootserver?>/change_plan.php"><?php echo Highligt($PG_TITLE,'change_plan.php',MENU_CHANGE_PLAN,$selctdMenuColor);?></a></li>
							<?php
									}//end if
											$sql="select vLookUpDesc from ".TABLEPREFIX."lookup where nLookupcode=15 and vLookUpDesc='0'" ;
											$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
											if(mysqli_num_rows($result)>0) 
											{
        										echo '<li><a href="'.$rootserver.'/addsurvey.php">'.Highligt($PG_TITLE,'addsurvey.php',MENU_ADD_REFERRALS,$selctdMenuColor).'</a></li>';
											}//end if
									?>
                                  </ul>
                                </div>
                              </div>
<?php } ?>