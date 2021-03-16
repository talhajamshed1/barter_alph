<?php include_once('./includes/title.php'); ?>
<body onLoad="timersOne();">

    <?php include_once('./includes/top_header.php'); 

if (strlen($var_title) > 80) {
                $var_title = substr($var_title, 0, 80).'...';
}
    ?>
<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
			<div class="col-lg-9">
				<div class="innersubheader">
					<h4><?php echo HEADING_PAYMENT_STATUS; ?></h4>
				</div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
					<form name="frmBuy" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
						<?php
							if (isset($flag) && $flag == false) {
								?>
							<div class="row warning"><?php echo $var_message; ?></div>
						<?php
						}//end if
						else {
							?>
						<div class="subheader"><h4><?php echo HEADING_PURCHASE_DETAILS; ?></h4></div>
						
						<div class="row main_form_inner">
							<label><?php echo TEXT_TITLE; ?></label>
							<label><?php echo  htmlentities($var_title) ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_QUANTITY; ?></label>
							<label><?php echo  $var_quantity ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_AMOUNT; ?></label>
							<label><?php echo CURRENCY_CODE; ?><?php echo  $var_amount ?><?php echo $showPrice; ?></label>
						</div>
						<div class="row main_form_inner">
							<label><?php echo TEXT_PURCHASED_DATE; ?></label>
							<label><?php echo date('m/d/Y H:i:s', strtotime($var_date)); ?></label>
						</div>
						<div class="row main_form_inner">
							<div class="row sucess_msg"><?php echo  $var_message ?></div>
						</div>
						<!--<div class="row main_form_inner">
							<label>
								<input type="submit" name="btnLogin" value="dsfdsfsfds" class="subm_btt">
							</label>
						</div>-->
							<?php
						}//end else
						?>
						
					</form>																	
					</div>	
					<div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>			
				</div>
				
				<div class="subbanner">
				 	<?php include('./includes/sub_banners.php'); ?>
				</div>
				
			</div>
		</div>  
	</div>
</div>
                
<?php require_once("./includes/footer.php"); ?>