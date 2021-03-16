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
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file for category
include ("./includes/logincheck.php");

include_once('./includes/gpc_map.php');
$message="";
include_once('./includes/title.php');

//if($_REQUEST['catid'])
?>
<body onLoad="timersOne();">
    <?php include_once('./includes/top_header.php');?>
    <script type="text/javascript">
    jQuery.noConflict();
  
    jQuery(document).ready(function() {
        //jQuery('#swap').addClass("active");
        
        
        jQuery(".dropdown-menu li a").each(function(){
            var selText = jQuery(this).text();
            <?php if($cmbSearchType!='') { ?>
                    var selText1 = '<?php echo $cmbSearchType;?>';
               jQuery(this).parents('.dropdown').find('.dropdown-toggle').html(selText1+' <span class="caret"></span>');
            <?php } ?>
            //alert(selText);
        });   
     
         jQuery('.form-group #btn_Search').click(function(){ 
             //jQuery("form#frmProducts").data('bootstrapValidator').defaultSubmit();
               jQuery('#frmProducts').submit();
    });
        
        <?php if($cmbSearchType!='') { ?>
         jQuery('#caret').text('<?php echo $cmbSearchType;?>');
         
           <?php } ?>
      
      jQuery(".dropdown-menu li a").click(function(){
        var selText = jQuery(this).text();
        //alert(selText);
       //jQuery(".dropdown-menu  span").html(selText);
       jQuery('#cmbSearchType').val(selText);
       // jQuery(this).closest('.dropdown-menu').children('a.dropdown-toggle').text(selText);
        jQuery(this).parents('.dropdown').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
       // jQuery(this).parents('.btn-group').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
});   
    });
</script>
    <div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-md-4 col-lg-3"><?php include_once("./includes/categorymain.php");?></div>
                <div class="col-md-8 col-lg-9">
                    
                    <!-- <div class="row">
                    	<div class="col-lg-12">
                            <div>
                            <ol class="breadcrumb" style="margin-bottom:0;">
                            <?php echo getBreadCrumbs($PHP_SELF,addslashes($_GET["catid"])); ?>
                            </ol>
                            </div><?php //echo getCategoryLink($PHP_SELF,addslashes($_GET["catid"]));?>
                        </div>
                         <?php if ($_SESSION["guserid"] == "") {
							include_once("./login_box.php");
						}?>
                    </div> -->

                    
             <!--        <div class="row">
                    	<div class="col-lg-12">
                        	<div class="innersubheader">
                            <h3><?php echo TEXT_CATEGORY; ?></h3>
                            </div>
                        </div>
                    </div>
                     <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12 right">
				<form name="frmProducts" id="frmProducts" action="<?php echo basename($_SERVER['PHP_SELF']);?>" method="POST" role="form">                  
				 <input name="catid" type="hidden" id="catid" VALUE="<?php echo $var_catid; ?>">
				 <input type="hidden" name="cmbItemType" id="cmbItemType" value="">
					<input type="hidden" name="cmbSearchType" id="cmbSearchType" value="" >
					<div class="searchoption_inner">
					<div class="searchoption_inner_row">
						<div class="searchoption_inner_cell" style="width:30%;">
							<div class="dropdown">
								<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
									<?php echo TEXT_CATEGORY;?>
									<span class="caret"></span>
								</button>                     
								<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
									<li role="presentation"><a role="menuitem" tabindex="-1" href="#"><?php echo TEXT_CATEGORY; ?></a></li>
																	
								</ul>
							</div>
						</div>
						<div class="searchoption_inner_cell" style="width:60%;">
							<div class="form-group">
								<input type="text" class="form-control pull-left" placeholder="<?php echo BUTTON_SEARCH;?>" value="<?php echo(htmlentities($txtSearch));?>" name="txtSearch" >
								<div class="clear"></div>
							</div>
						</div>
						<div class="searchoption_inner_cell" style="width:10%;">
							<button type="submit" class="btn actionbtn btn-default pull-right" id="btn_Search"><?php echo TEXT_GO;?></button>
						</div>
					
						<div class="clear"></div>
					</div>
					</div>
			 </form>   
            </div>  -->  

            <div class="product-list-head">
                <h3><?php echo TEXT_CATEGORY; ?></h3>
                <form name="frmProducts" id="frmProducts" action="<?php echo basename($_SERVER['PHP_SELF']);?>" method="POST" role="form">  
                <input name="catid" type="hidden" id="catid" VALUE="<?php echo $var_catid; ?>">
                 <input type="hidden" name="cmbItemType" id="cmbItemType" value="">
                    <input type="hidden" name="cmbSearchType" id="cmbSearchType" value="" >  
                    <select class="selectpicker">
                        <option><?php echo TEXT_CATEGORY;?></option>
                       
                    </select>
                    <input type="text"  placeholder="<?php echo BUTTON_SEARCH;?>" value="<?php echo(htmlentities($txtSearch));?>" name="txtSearch" >
                    
                    <button type="submit"  id="btn_Search"><?php echo TEXT_GO;?></button>
                </form>
            </div>
            <!-- category-list -->
           





            <!-- //-category-list -->
            <div class="clear"></div>
			 <?php
				include_once("./includes/subcategorylist.php");
			?>
              
				<div class="subbanner">
					<!-- <?php include('./includes/sub_banners.php');?> -->
				</div>
					
			    </div>
            </div>  
        </div>
 </div>  
<?php require_once("./includes/footer.php");?>