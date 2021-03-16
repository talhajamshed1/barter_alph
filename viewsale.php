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

$message = "";
$var_saleid = "";
$var_userid = "";
$var_uname = "";
$var_bname = "";
$var_date = "";
$var_quantity = 0;
$var_amount = 0;
$var_method = "";

include_once('./includes/gpc_map.php');

if ($_GET["saleid"] != "") {
    $var_saleid = $_GET["saleid"];
    $var_userid = $_GET["userid"];
    $var_date = $_GET["dDate"];
}//end if

include_once('./includes/title.php');
?>
<body onLoad="timersOne();">
    <script language="javascript" type="text/javascript">
        function clickAccept()
        {
            document.frmMakeOffer.postback.value="A";
            document.frmMakeOffer.submit();
        }//end function
        function clickReject()
        {
            document.frmMakeOffer.postback.value="R";
            document.frmMakeOffer.submit();
        }//end function

        function viewDetails(i)
        {
            var str = 'itemdetails.php?swapid=' + i;
            var left = Math.floor( (screen.width - 300) / 2);
            var top = Math.floor( (screen.height - 400) / 2);

            var loginWindow=window.open(str,"approvalpage","top=" + top + ",left=" + left + ",toolbars=no,maximize=no,resize=no,width=300,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
        }//end function
    </script>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="headerbg">      
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="74%" valign="top">
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td class="link3">&nbsp;</td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="heading" align="left"><?php echo HEADING_TRANSACTION_DETAILS; ?></td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <?php
                                            $sql = "Select sd.nSaleId,sd.nUserId,sd.vMethod,sd.vTxnId,sd.nAmount,sd.dDate,sd.nQuantity,sd.nPoint,
                                                        sd.vSaleStatus,u.vLoginName
                                                        from " . TABLEPREFIX . "saledetails  sd 
                                                            inner join " . TABLEPREFIX . "users u on sd.nUserId = u.nUserId 
                                                        where sd.nSaleId='" . addslashes($var_saleid) . "' AND
                                                            sd.nUserId='" . addslashes($var_userid) . "' AND
                                                            sd.dDate='" . addslashes($var_date) . "'";

                                            $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                            if (mysqli_num_rows($result) > 0) {
                                                if ($row = mysqli_fetch_array($result)) {
                                                    $var_amount = $row["nAmount"];
                                                    $var_point = $row["nPoint"];
                                                    $var_quantity = $row["nQuantity"];
                                                    $var_method = $row["vMethod"];
                                                    $var_bname = $row["vLoginName"];
                                                    $disp_method = get_payment_name($var_method);
                                                    
                                                }//end if
                                            }//end if
                                            ?>
                                            <form name="frmMakeOffer" method="POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
                                                <tr>
                                                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="5" class="maintext2">
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td colspan="2" align="left" class="subheader"><b><?php echo str_replace('{status}',$var_show_mesg,MESSAGE_STATUS_OF_ITEM_IS_STATUS); ?></b></td>
                                                                        </tr>
                                                                        <tr bgcolor="#FFFFFF">
                                                                            <td width="20%" align="left" valign="top"><fieldset class="fldset">
                                                                                    <legend class="gray"><?php echo HEADING_TRANSACTION_DETAILS; ?>...</legend>
                                                                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="maintext2">
                                                                                        <tr>
                                                                                            <td height="120" align="left" valign="top">
                                                                                                <span style="width:100%; height:100%; border:0 groove #990033;overflow:auto; " id="mainSpan">
                                                                                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="maintext2">
                                                                                                        <tr>
                                                                                                            <td><?php echo TEXT_BUYER; ?></td>
                                                                                                            <td><?php echo  $var_bname ?></td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td><?php echo TEXT_QUANTITY; ?></td>
                                                                                                            <td><?php echo  $var_quantity ?></td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td><?php echo TEXT_AMOUNT; ?></td>
                                                                                                            <td><?php echo CURRENCY_CODE; ?><?php echo  $var_amount ?></td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td><?php echo POINT_NAME; ?></td>
                                                                                                            <td><?php echo  $var_point ?></td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td><?php echo TEXT_METHOD; ?></td>
                                                                                                            <td><?php echo  $disp_method ?></td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td><?php echo TEXT_DATE; ?></td>
                                                                                                            <td><?php echo  date('m/d/Y', strtotime($var_date)) ?></td>
                                                                                                        </tr>
                                                                                                    </table>
                                                                                                </span>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </fieldset></td>
                                                                            <td width="20%" align="left" valign="top"><fieldset class="fldset">
                                                                                    <legend class="gray"><?php echo TEXT_SALE_ITEM; ?>...</legend>
                                                                                    <table border="0" cellpadding="0" cellspacing="0" class="maintext2">
                                                                                        <?php
                                                                                        $sql = "select S.nSaleId,S.nCategoryId,
                                                                                                             L.vCategoryDesc,S.nUserId,
                                                                                                             vLoginName as 'UserName',
                                                                                                             S.vTitle,S.vBrand,S.vType,S.vCondition,
                                                                                                             S.vYear,S.nValue,S.nPoint,S.nShipping,S.vUrl,S.vDescription,date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate'
                                                                                                             from
                                                                                                             " . TABLEPREFIX . "sale S
                                                                                                                 left join " . TABLEPREFIX . "users U on S.nUserId = U.nUserId
                                                                                                                 left join " . TABLEPREFIX . "category C on S.nCategoryId = C.nCategoryId
                                                                                                                 LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                                                                                                             where  
                                                                                                             nSaleId = '" . addslashes($var_saleid) . "'";


                                                                                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                                                                        if (mysqli_num_rows($result) > 0) {
                                                                                            if ($row = mysqli_fetch_array($result)) {
                                                                                                $var_uname = $row["UserName"];
                                                                                                $var_post_type = $row["vPostType"];
                                                                                                ?>
                                                                                                <tr>
                                                                                                    <td colspan="2">
                                                                                                <?php
                                                                                                if ($row["vUrl"] == "") {
                                                                                                    //echo "Picture n/a";
                                                                                                }//end if
                                                                                                else {
                                                                                                    echo "<img src=\"./" . $row["vUrl"] . "\" width='40' height='40'>";
                                                                                                }//end else
                                                                                                ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td width="45%"><strong>
                                                                                                            <?php echo TEXT_SELLER_NAME; ?>
                                                                                                        </strong></td>
                                                                                                    <td width="55%">
                                                                                                        <?php echo  $row["UserName"] ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_TITLE; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  $row["vTitle"] ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_DESCRIPTION; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  $row["vDescription"] ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_BRAND; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  $row["vBrand"] ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_TYPE; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  $row["vType"] ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_CONDITION; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  $row["vCondition"] ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_PRICE; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo CURRENCY_CODE; ?><?php echo  $row["nValue"] ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo POINT_NAME; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                       <?php echo  $row["nPoint"] ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr align="left">
                                                                                                    <td><strong>
                                                                                                            <?php echo TEXT_SHIPPING; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo CURRENCY_CODE; ?><?php echo  $row["nShipping"] ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <!--
                                                                                                                                              <tr>
                                                                                                                                                  <td>
                                                                                                                                                      User
                                                                                                                                                  </td>
                                                                                                                                                  <td>
                                                                                                                                                    <?php echo  $row["UserName"] ?>
                                                                                                                                                  </td>
                                                                                                                                              </tr>
                                                                                                -->
                                                                                                <tr align="left">
                                                                                                    <td class="textgrey"><strong>
                                                                                                            <?php echo TEXT_POSTED_ON; ?>
                                                                                                        </strong></td>
                                                                                                    <td>
                                                                                                        <?php echo  date('m/d/Y', strtotime($row["dPostDate"])) ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <?php
                                                                                            }//end if
                                                                                        }//end if
                                                                                        else {
                                                                                            ?>
                                                                                            <tr align="center">
                                                                                                <td colspan="2" class="warning"><strong><?php echo ERROR_STATUS_OF_ITEM_CHANGED_CANNOT_SELL; ?></strong>.</td>
                                                                                            </tr>
                                                                                            <?php
                                                                                        }//end else
                                                                                        ?>
                                                                                    </table>

                                                                                </fieldset></td>
                                                                        </tr>
                                                                        <tr align="center" bgcolor="#FFFFFF">
                                                                            <td colspan="2"><input type="button" name="btClose" value="<?php echo LINK_CLOSE; ?>" class="submit" onClick="javascript:window.close();"></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table></td>
                                                </tr></form>
                                        </table>
										<?php include('./includes/sub_banners.php'); ?>
                                    </td>
                                </tr>
                            </table></td>
                    </tr>
                </table>
