<?php include_once('./includes/title.php'); ?>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>

<div class="homepage_contentsec">
	<div class="container">
		<div class="row">
			<div class="col-lg-3">
				<?php include_once ("./includes/categorymain.php"); ?>
			</div>
			<div class="col-lg-9">			
				<div class="innersubheader">
					<h4><?php echo HEADING_PAYMENT_STATUS; ?></h4>
				</div>
				<div class="row">
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>
					<div class="col-lg-8 col-sm-12 col-md-10 col-xs-12 main_form_outer">
						<?php if ($_SESSION["guserid"] == "") {
								include_once("./login_box.php");
							} ?>
							
							<?php 
							if (isset($flag) && $flag == false) { 
								?>
									<div class="row warning"><?php echo $message; ?></div>
								<?php
								}//end if
								else { 
									?>
								<div class="row main_form_inner">
									<h4><?php echo HEADING_REGISTRATION_INFO; ?></h4>
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_LOGIN_NAME; ?></label>
									<?php echo  $var_login_name ?>
								</div>
								<?php if($var_first_name!=''){ ?>
								<div class="row main_form_inner">
									<label><?php echo TEXT_FIRST_NAME; ?></label>
									<?php echo  $var_first_name ?>
								</div>
								<?php } if($var_last_name != ''){?>
								<div class="row main_form_inner">
									<label><?php echo TEXT_LAST_NAME; ?></label>
									<?php echo  $var_last_name ?>
								</div>
								<?php }?>
								<div class="row main_form_inner">
									<label><?php echo TEXT_AMOUNT; ?></label>
									<?php echo CURRENCY_CODE; ?><?php echo  $totalamt ?>
								</div>
								<div class="row main_form_inner">
									<label><?php echo TEXT_REGISTRATION_DATE; ?></label>
									<?php echo  $now ?>
								</div>
								<div class="row main_form_inner">
									<label><?php echo  $message ?></label>
								</div>
								<?php
							}//end else
							?>
						</form>
					</div>	
					<div class="col-lg-2 col-sm-12 col-md-12 col-xs-12"></div>			
				</div>
				
				<div class="subbanner">
					<?php include('./includes/sub_banners.php'); ?>
				</div>	
				
			</div>
		</div>  
	</div>
</div>

<?php require_once("./includes/footer.php"); ?>