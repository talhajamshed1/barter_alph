<?php
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");

if (isset($_SESSION["guserid"]) && $_SESSION["guserid"] != '') {
    //chcking live msg
    $OnlMsg = mysqli_query($conn, "select * from " . TABLEPREFIX . "chat where nUserId='" . $_SESSION["guserid"] . "' and vDisplayed='0'")
            or die();//mysqli_error($conn)
    if (mysqli_num_rows($OnlMsg) > 0) {
        for ($jk = 0; $jk < mysqli_num_rows($OnlMsg); $jk++) {
            if ($chkFrmId != mysqli_result($OnlMsg, $jk, 'nFromId')) {
                $nFrmArray[$jk].=mysqli_result($OnlMsg, $jk, 'nFromId');
            }//end if
            $chkFrmId = mysqli_result($OnlMsg, $jk, 'nFromId');
        }//end for loop
    }//end if
    //checking array
    if (is_array($nFrmArray)) {
        foreach ($nFrmArray as $valAr) {
            ?>
            <?php echo SITE_URL; ?>/chat/chat.php?requestid=<?php echo $valAr; ?>&,'OnlineChat_<?php echo $valAr; ?>','top=100,left=100,width=550,height=425,scrollbars=yes,toolbar=no,resizable=1' 'chk'
            <?php
        }//end foreach
    }//end if
}//end if
?>
