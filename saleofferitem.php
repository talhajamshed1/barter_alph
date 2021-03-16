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
include("./languages/" . $_SESSION['lang_folder'] . "/category.php"); //language file
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

$message = "";
$var_swapid = "";
$var_description = "";
$var_mpay = 0;
$var_hpay = 0;

if ($_GET["saleid"] != "") {
    $var_saleid = $_GET["saleid"];
}//end if
else if ($_POST["saleid"] != "") {
    $var_saleid = $_POST["saleid"];
}//end else if


if (isset($_POST["btnReject"]) && $_POST["btnReject"] != "") {
    $sql = "SELECT vRejected, nQuantity FROM " . TABLEPREFIX . "saledetails WHERE nSaleId= '" . $var_saleid . "' ";
    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if (mysqli_num_rows($result) > 0) {
        if ($row2 = mysqli_fetch_array($result)) {
            if ($row2["vRejected"] == "0") {
                $qty = $row2["nQuantity"];
                $sqlupdateqty = "UPDATE " . TABLEPREFIX . "sale SET nQuantity = nQuantity+$qty  WHERE nSaleId= '" . $var_saleid . "' ";
                mysqli_query($conn, $sqlupdateqty);
                $sql = "UPDATE " . TABLEPREFIX . "saledetails SET vRejected ='1' WHERE nSaleId= '" . $var_saleid . "' ";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));
            }//end if
        }//end if
    }//end if
}//end if
//End of editing

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <script language="javascript" type="text/javascript">
        function clickPhoto(picName)
        {
            var str="picture.php?url=" + picName;
            var left = Math.floor( (screen.width - 300) / 2);
            var top = Math.floor( (screen.height - 400) / 2);
            picture=window.open(str,"picturedisplay","top=" + top + ",left=" + left + ",toolbars=no,maximize=yes,resize=no,width=300,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
        }//end function
    </script>
<?php include_once('./includes/top_header.php'); ?>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="headerbg"><?php require_once("./includes/header.php"); ?>
                <?php require_once("menu.php"); ?>
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="10%" height="688" valign="top"><?php include_once ("./includes/usermenu.php"); ?>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td id="leftcoloumnbtm"></td>
                                            </tr>
                                        </table></td>
                                    <td width="74%" valign="top">
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td class="link3">&nbsp;</td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="heading" align="left"><?php echo HEADING_OFFER_DETAILS; ?></td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                                    <form name="frmMakeOffer" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
                                                                        <?php
                                                                        if (isset($message) && $message != '') {
                                                                            ?>
                                                                            <tr bgcolor="#FFFFFF">
                                                                                <td colspan="2" align="center" class="warning"><?php echo $message; ?></td>
                                                                            </tr>
                                                                        <?php }//end if?>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td width="55%" align="left"> <fieldset class="fldset"><legend class="gray"><?php echo HEADING_SALE_OFFER_DETAILS; ?></legend>

                                                                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="maintext2">
                                                                                        <tr>
                                                                                            <td align="left" valign="top">
                                                                                                <span style="width:100%; height:100%; border:0 groove #990033;overflow:auto; " id="mainSpan">
                                                                                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                                                        <?php
                                                                                                        $sql1 = "SELECT u.vAddress1 ,u.vAddress2, u.vCity , u.vState, u.vCountry , u.nZip , u.vPhone, u.vEmail,
                                                                                                                                                CONCAT(CONCAT(u.vFirstName,'  '),u.vLastName) as UserName,
                                                                                                                                                sd.nQuantity ,sd.nAmount
                                                                                                                                                FROM  " . TABLEPREFIX . "saledetails sd, " . TABLEPREFIX . "users u
                                                                                                                                                where
                                                                                                                                                sd.nUserId = u.nUserId
                                                                                                                                                AND sd.nSaleId = '$var_saleid'";

                                                                                                        $result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
                                                                                                        if (mysqli_num_rows($result1) > 0) {
                                                                                                            if ($row1 = mysqli_fetch_array($result1)) {
                                                                                                                ?>
                                                                                                                <tr align="left">
                                                                                                                    <td width="35%"><strong>
                                                                                                                         <?php echo TEXT_USERNAME; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td width="65%">
                                                                                                                        <?php echo  htmlentities($row1["UserName"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>

                                                                                                                <tr align="left">
                                                                                                                    <td><strong>
                                                                                                                            <?php echo TEXT_ADDRESS_LINE1; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td>
                                                                                                                        <?php echo  htmlentities($row1["vAddress1"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td><strong>
                                                                                                                            <?php echo TEXT_ADDRESS_LINE2; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td>
                                                                                                                        <?php echo  htmlentities($row1["vAddress2"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td><strong>
                                                                                                                            <?php echo TEXT_CITY; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td>
                                                                                                                        <?php echo  htmlentities($row1["vCity"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td><strong>
                                                                                                                            <?php echo TEXT_STATE; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td>
                                                                                                                        <?php echo  htmlentities($row1["vState"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td><strong>
                                                                                                                            <?php echo TEXT_COUNTRY; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td>
                                                                                                                        <?php echo  htmlentities($row1["vCountry"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td><strong>
                                                                                                                            <?php echo TEXT_ZIP; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td>
                                                                                                                        <?php echo  htmlentities($row1["nZip"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td><strong>
                                                                                                                            <?php echo TEXT_PHONE; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td>
                                                                                                                        <?php echo  htmlentities($row1["vPhone"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td><strong>
                                                                                                                            <?php echo TEXT_EMAIL; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td>
                                                                                                                        <?php echo  htmlentities($row1["vEmail"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td colspan="2">&nbsp;
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td colspan="2" >&nbsp;
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td><strong>
                                                                                                                            <?php echo TEXT_AMOUNT_OFFERED; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td>
                                                                                                                        <?php echo  htmlentities($row1["nAmount"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr align="left">
                                                                                                                    <td><strong>
                                                                                                                            <?php echo TEXT_QUANTITY_REQUIRED; ?>
                                                                                                                        </strong></td>
                                                                                                                    <td>
                                                                                                                        <?php echo  htmlentities($row1["nQuantity"]) ?>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <?php
                                                                                                            }//end if
                                                                                                        }//end if
                                                                                                        ?>
                                                                                                    </table>
                                                                                                </span>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </fieldset></td>
                                                                            <td width="45%" align="left" valign="top"> <fieldset class="fldset">
                                                                                    <legend class="gray"><?php echo HEADING_SALES_ITEM_DETAILS; ?>...</legend>
                                                                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="maintext2">
                                                                                        <?php
                                                                                        $sql = "SELECT s.nSaleId,s.vTitle,date_format(dPostDate,'%m/%d/%Y') as
                                                                                                                     'dPostDate',s.vFeatured,  s.vTitle , s.vBrand ,s.vUrl,
                                                                                                                         s.vType ,s.vCondition ,s.vYear ,s.nValue,s.vDescription ,sd.vRejected ,sd.vSaleStatus ,sd.nAmount,sd.nQuantity,
                                                                                                                         L.vCategoryDesc,
                                                                                                                         CONCAT(CONCAT(u.vFirstName,'  '),u.vLastName) as UserName
                                                                                                                         FROM " . TABLEPREFIX . "sale s
                                                                                                                             left join " . TABLEPREFIX . "category c on s.nCategoryId  = c.nCategoryId
                                                                                                                             left join " . TABLEPREFIX . "saledetails sd on s.nSaleId  = sd.nSaleId
                                                                                                                             left join " . TABLEPREFIX . "users u on sd.nUserId = u.nUserId
                                                                                                                             LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                                                                                                     where  sd.vSaleStatus= 1 
                                                                                                                           AND s.vDelStatus = '0'
                                                                                                                             AND sd.nSaleId = '$var_saleid'";

                                                                                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                                                        if (mysqli_num_rows($result) > 0) {
                                                                                            if ($row = mysqli_fetch_array($result)) {
                                                                                                ?>
                                                                                                <tr>
                                                                                                    <td colspan="2">
                                                                                                <?php
                                                                                                if ($row["vUrl"] == "") {
                                                                                                    //echo "Picture n/a";
                                                                                                }//end if
                                                                                                else {
                                                                                                    echo "<a href=\"javascript:clickPhoto('" . $row["vUrl"] . "');\" class='style1'><img src=\"" . $row["vUrl"] . "\" width='100' height='75' border=1><br><font size='1' face='verdana'>".LINK_VIEW_LARGE_IMAGE."</font></a>";
                                                                                                }//end else
                                                                                                ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td width="33%"><strong>
                                                                                                            <?php echo TEXT_TITLE; ?>
                                                                                                        </strong></td>
                                                                                                    <td width="67%">
                                                                                                        <?php echo  htmlentities($row["vTitle"]) ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_ITEM_DESCRIPTION; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  htmlentities($row["vDescription"]) ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_BRAND; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  htmlentities($row["vBrand"]) ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_TYPE; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  htmlentities($row["vType"]) ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_CONDITION; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  htmlentities($row["vCondition"]) ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_PRICE; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  $row["nValue"] ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_USERNAME; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  htmlentities($row["UserName"]) ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_POSTED_ON; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  htmlentities($row["dPostDate"]) ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                        <?php
                                                                                                    }//end if
                                                                                                }//end if
                                                                                                else {
                                                                                                    ?>
                                                                                            <tr align="center">
                                                                                                <td colspan="2" class="warning"><strong><?php echo MESSAGE_ITEM_NOT_FOUND; ?></strong></td>
                                                                                            </tr>
                                                                                                    <?php
                                                                                                }//end else
                                                                                                ?>                                       
                                                                                    </table>
                                                                                </fieldset>
                                                                            </td>
                                                                        </tr>
                                                                    </form>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td colspan="2" align="center" class="warning"> 
                                                                            <form name="frmSaleOffer">
                                                                                        <?php
                                                                                        $sql = "SELECT sd.vRejected ,sd.vSaleStatus	FROM  " . TABLEPREFIX . "saledetails sd
					                 					where	 sd.nSaleId = '$var_saleid'";
                                                                                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                                                        if (mysqli_num_rows($result) > 0) {
                                                                                            if ($row = mysqli_fetch_array($result)) {
                                                                                                if ($row["vRejected"] == "1") {
                                                                                                    echo ERROR_STATUS_OF_ITEM_REJECTED;
                                                                                                }//end if
                                                                                                else if ($row["vSaleStatus"] > 1) {
                                                                                                    echo ERROR_CANNOT_REJECT_PAYMENT_DONE;
                                                                                                }//end else if
                                                                                                else {
                                                                                                    ?>
                                                                                            <input type="hidden" name="saleid" value="<?php echo  $var_saleid ?>">
                                                                                            <input type = "submit" name= "btnReject" value="Reject this offer" class="submit">
                                                                                        <?php
                                                                                        }//end else
                                                                                    }//end 2nd if
                                                                                }//end if
                                                                                ?>
                                                                            </form></td>
                                                                    </tr>
                                                                    </form>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                        </table>
										<?php include('./includes/sub_banners.php'); ?>
                                    </td>
                                </tr>
                            </table></td>
                    </tr>
                </table>
                <script language="javascript" type="text/javascript">
                    try
                    {
                        for(i=0;i < chk.length;i++)
                        {
                            eval(document.getElementById(chk[i]).checked=true);
                        }//end for loop
                    }//end try
                    catch(e)
                    {
                        //    alert('Have a  nice day!');
                    }//end catch
                </script>
<?php require_once("./includes/footer.php"); ?>