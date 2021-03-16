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
if (DisplayLookUp('BannerDisplay') == 'Yes') {
    /* $sqlBanners = "SELECT *,min(nCount) from " . TABLEPREFIX . "banners where vActive='1' and vLocation='Sub'
      GROUP BY nBId order by rand(),nPosition desc limit 0,1"; */
    $sqlBanners = "SELECT B.*, BL.vName, min(B.nCount) from " . TABLEPREFIX . "banners B
                        LEFT JOIN " . TABLEPREFIX . "banners_lang BL on B.nBId = BL.banner_id and BL.lang_id = '" . $_SESSION['lang_id'] . "' 
                    WHERE B.vActive='1' and B.vLocation='Sub'
                    GROUP BY B.nBId 
                    ORDER BY rand(),B.nPosition ASC";
    $resultBanners = mysqli_query($conn, $sqlBanners) or die(mysqli_error($conn));

    $row = 1;
    if (mysqli_num_rows($resultBanners) > 0) {
        ?>

<div class="subbanner">
            <?php
            $w=0;
            while ($arrCool = mysqli_fetch_array($resultBanners)) {
                ++$w;                
                mysqli_query($conn, "update " . TABLEPREFIX . "banners set nCount=nCount+1 where nBId='" . $arrCool['nBId'] . "'") or die(mysqli_error($conn));
                ?>

                <?php
                //if ($arrCool["vImg"] != '') {
                if(is_file('./banners/'.$arrCool["vImg"])) {
                    echo '<a href="' . $arrCool["vlocUrl"] . '" target="_blank"><img src="./banners/' . $arrCool["vImg"] . '" name="img1" width="728" height="90" border="0" alt="' . $arrCool["vName"] . '" title="' . $arrCool["vName"] . '"></a>';
                    break;
                }//end if
                else {
                    continue;
                }
                 
                ?>
                <?php
                $row++;
            } // while loop
            ?>
</div>




        <?php
    }//end if
}//end if
?>
