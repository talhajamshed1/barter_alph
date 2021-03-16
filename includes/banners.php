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

$sqlBanners = "SELECT B.*, BL.vName, min(B.nCount) from " . TABLEPREFIX . "banners B
                        LEFT JOIN " . TABLEPREFIX . "banners_lang BL on B.nBId = BL.banner_id and BL.lang_id = '" . $_SESSION['lang_id'] . "' 
                    WHERE B.vActive='1' and B.vLocation='Home'
                    GROUP BY B.nBId 
                    ORDER BY rand() limit 0,1";
$resultBanners = mysqli_query($conn, $sqlBanners) or die(mysqli_error($conn));

$row = 1;
if (mysqli_num_rows($resultBanners) > 0){ 
?>
    <div class="row">
    	<div class="col-lg-12">
        <div class="box1_mdfd">
        <?php
        while ($arrCool = mysqli_fetch_array($resultBanners)){
            mysqli_query($conn, "update " . TABLEPREFIX . "banners set nCount=nCount+1 where nBId='" . $arrCool['nBId'] . "'") or die(mysqli_error($conn));
            if ($arrCool["vImg"] != ''){
                if(file_exists("./banners/".trim($arrCool["vImg"]))){
            ?>										
            <tr>
                <td width="90%" class="leftcoloumnlist" align="center">
                    <?php  echo '<a href="' . $arrCool["vlocUrl"] . '" target="_blank"><img src="./banners/' . $arrCool["vImg"] . '" name="img1" class="img-responsive"  border="0" alt="' . $arrCool["vName"] . '" title="' . $arrCool["vName"] . '"></a>';//width="468" height="60"  ?>							    
                </td>
            </tr>
            <?php 
                }
            }
            $row++;
        } 
        ?>
        </div>
        </div>
    </div>
     <?php
}//end if
?>
	
   
   