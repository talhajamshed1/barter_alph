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
include_once('./includes/gpc_map.php');
include_once('./includes/title.php');

?>
<body onLoad="timersOne();">
 
    <?php include_once('./includes/top_header.php'); ?>
   <div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
                <div class="col-lg-9">
                    <div class="row">
                    	<div class="col-lg-12">
                        	<div class="innersubheader">
                            	
                                <h4 class="hlpicon"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;<?php echo MENU_HELP; ?> - <?php echo strtoupper($_GET['name']); ?></h4>
                              </div>
                            
                                <div class="cms_content">
                                    <?php
                                    $help = "SELECT * FROM " . TABLEPREFIX . "help H
                                            LEFT JOIN " . TABLEPREFIX . "help_lang L on H.nHId = L.help_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                        where H.vActive='1' and H.nHId='" . $_REQUEST['id'] . "' ORDER BY H.nHposition desc";

                                    $result = mysqli_query($conn, $help) or die(mysqli_error($conn));
                                    if (mysqli_num_rows($result) > 0) { 

										$res = mysqli_fetch_array($result);
										if (strpos($res['vHdescription'],'{SALE_HELP_LINK}') != false)
										{
											
											$res['vHdescription'] = str_replace('{SALE_HELP_LINK}',"help_details.php?id=36&name=How to sell an item with Eswaps ...",$res['vHdescription']);
											
										}
			
									?>
                                <p><?php //echo nl2br(utf8_encode(mysqli_result($result, 0, 'vHdescription'))); 
                                		 echo nl2br(utf8_encode($res['vHdescription']));
                                		?></p>
                                    <?php } ?>
                                </div>
                            <div><a href="help.php" class="comm_btn_grey"><b><?php echo LINK_BACK; ?></b></a> </div>
                        </div>
                    </div>					
                	<div class="subbanner">
					 <?php include('./includes/sub_banners.php'); ?>
                    </div>
                </div>
            </div>  
        </div>
 </div> 
          
    
   
<?php require_once("./includes/footer.php"); ?>