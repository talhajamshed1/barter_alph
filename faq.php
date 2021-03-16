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
include "./includes/config.php";
include "./includes/session.php";
include "./includes/functions.php";
include "./includes/logincheck.php";

include_once('./includes/gpc_map.php');
$message = "";
include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <script language="javascript" type="text/javascript" src="js/switch_content.js"></script>
    <script type="text/javascript">
        function hide(count,id)
        {
            for(var i=0;i<count;i++)
            {
                if(i!=id)
                {
                    document.getElementById('sc'+i).style.display='none';
                }//end if
            }//end for
        }//end function
    </script>
    <?php include_once('./includes/top_header.php'); ?>
    
    <div class="homepage_contentsec">
    	<div class="container">
        	<div class="row">
            	<div class="col-lg-3"><?php include_once ("./includes/categorymain.php"); ?></div>
                <div class="col-lg-9">
                     <div class="row">
                    	<div class="col-lg-12">
                            <div class=" privacy-policy-section">
                        	<div class="innersubheader">
                            	
                                <h4><?php echo MENU_FAQ; ?></h4>
                              </div>
                                <div class="cms_content">
                                	<?php
                                                                    $faq = mysqli_query($conn, "select * from " . TABLEPREFIX . "faq F
                                                                                        LEFT JOIN " . TABLEPREFIX . "faq_lang L on F.nFId = L.faq_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                                                                        where F.vActive='1' order by F.nPosition ASC") or die(mysqli_error($conn));
                                                                    if (mysqli_num_rows($faq) > 0) {
                                                                        for ($i = 0; $i < mysqli_num_rows($faq); $i++) {
                                                                            $bgcolor = ($bgcolor == COLOR1) ? COLOR2 : COLOR1;
                                                                            ?>
																			<div class="faquestions">

                                                                            <b><?php echo $i + 1; ?>.</b>
                                                                                <?php echo '<a href="#" onClick="javascript:expandcontent(this, \'sc' . $i . '\'),hide(' . mysqli_num_rows($faq) . ',' . $i . ');return false;">&nbsp;<b>' . utf8_encode(mysqli_result($faq, $i, 'vTitle')) . '</b></a>
								'; ?></div>
																			<!--<div class="border_1">--><?php echo '<div id="sc' . $i . '" class="switchcontent">
  
    &raquo;
 ' . nl2br(utf8_encode(mysqli_result($faq, $i, 'vDes'))) . '
  
</div>'; ?><!--</div>-->
                                                                            <?php
                                                                        }//end for
                                                                    }//end if
                                                                    else {
                                                                        echo '<strong>'.MESSAGE_SORRY_NO_RECORDS.'</strong>';
                                                                    }//end else
                                                                    ?>	
                                </div>
                            
                        </div>
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