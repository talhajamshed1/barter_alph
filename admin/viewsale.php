<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com © 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include_once('../includes/headeradmin_view.php');
include_once('../includes/admin_login_session.php');
$PGTITLE='sale';


$message="";
$var_saleid="";
$var_userid="";
$var_uname="";
$var_bname="";
$var_date="";
$var_quantity=0;
$var_amount=0;
$var_method="";

if($_GET["saleid"] != "")
{
   $var_saleid = $_GET["saleid"];
   $var_userid = $_GET["userid"];
   $var_date = $_GET["dDate"];
}//end if
?>
<script language="javascript" type="text/javascript">
 function validateLoginForm(){
     var frm = window.document.frmLogin;
     if(trim(frm.txtUserName.value) ==""){
        alert("Please enter a user name");
        frm.txtUserName.focus();
        return false;
     }else if(frm.txtPassword.value ==""){
        alert("Please enter password");
        frm.txtPassword.focus();
        return false;
     }
     return true;
 }

 function clickAccept(){
          document.frmMakeOffer.postback.value="A";
          document.frmMakeOffer.submit();
 }
 function clickReject(){
          document.frmMakeOffer.postback.value="R";
          document.frmMakeOffer.submit();
 }

 function viewDetails(i){
 var str = '../itemdetails.php?swapid=' + i;
 var left = Math.floor( (screen.width - 300) / 2);
 var top = Math.floor( (screen.height - 400) / 2);

 var loginWindow=window.open(str,"approvalpage","top=" + top + ",left=" + left + ",toolbars=no,maximize=no,resize=no,width=300,height=400,location=no,directories=no,scrollbars=yes,border=thin,caption=no");
 }

</script>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="81%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="94%" height="32" class="headerbg">&nbsp;</td>
                      <td width="6%" align="right" valign="top" class="headerbg">&nbsp;</td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="84%" class="heading_admn boldtextblack" align="left">Transaction Details</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
<?php

          $sql = "Select sd.nSaleId,sd.nUserId,sd.vMethod,sd.vTxnId,sd.nAmount,sd.dDate,sd.nQuantity,sd.nPoint,
		  		sd.vSaleStatus,u.vLoginName
		  		from ".TABLEPREFIX."saledetails  sd inner join ".TABLEPREFIX."users u on
				sd.nUserId = u.nUserId where sd.nSaleId='" . addslashes($var_saleid) . "' AND
				sd.nUserId='" . addslashes($var_userid) . "' AND
				sd.dDate='" . addslashes($var_date) . "'";

           $result=mysqli_query($conn, $sql);
           if(mysqli_num_rows($result) > 0)
		   {
              if($row=mysqli_fetch_array($result))
			  {

					$var_amount=$row["nAmount"];
                                        $var_point = $row["nPoint"];
					$var_quantity=$row["nQuantity"];
					$var_method=$row["vMethod"];
					$var_bname=$row["vLoginName"];
					switch($var_method) 
					{
						case "cc" :
									$disp_method = "Credit Card";
									break;
						case "pp" :
									$disp_method = "PayPal";
									break;
									
						case "wp" :
									$disp_method = "WorldPay";
									break;
									
						case "bp" :
									$disp_method = "BluePay";
									break;
									
						case "bu" :
									$disp_method = "Business Check";
									break;
						case "ca" :
									$disp_method = "Cashiers Check";
									break;
						case "wt" :
									$disp_method = "Wire Transfer";
									break;
						case "mo" :
									$disp_method = "Money Order";
									break;
						case "pc" :
									$disp_method = "Personal Check";
									break;
					}//end switch
              }//end if
           }//end if
?>                         
						  <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmMakeOffer" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr bgcolor="#FFFFFF">
                                <td align="left" valign="top"><fieldset class="fldset"><legend class="gray">Transaction Details ...</legend>
                                       <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                         <td height="120" align="left" valign="top">
                                          <span style="width:100%; height:100%; border:0 groove #990033;overflow:auto; " id="mainSpan">
                                             <table>
		                                      <tr>
                                                  <td class=textblack>
                                                     Buyer
                                                  </td>
                                                  <td class=textblack>
                                                     <?php echo $var_bname?>
                                                </td>
                                               </tr>
		                                      <tr>
                                                  <td class=textblack>
                                                     Quantity
                                                  </td>
                                                  <td class=textblack>
                                                     <?php echo $var_quantity?>
                                                </td>
                                               </tr>
		                              <tr>
                                                  <td class=textblack>
                                                     Amount
                                                  </td>
                                                  <td class=textblack>
                                                     <?php echo CURRENCY_CODE;?><?php echo $var_amount?>
                                                </td>
                                               </tr>
                                               <tr>
                                                  <td class=textblack>
                                                     <?php echo POINT_NAME; ?>
                                                  </td>
                                                  <td class=textblack>
                                                     <?php echo $var_point?>
                                                </td>
                                               </tr>
		                                      <tr>
                                                  <td class=textblack>
                                                     Method
                                                  </td>
                                                  <td class=textblack>
                                                     <?php echo $disp_method?>
                                                </td>
                                               </tr>
		                                      <tr>
                                                  <td class=textblack>
                                                    Date
                                                  </td>
                                                  <td class=textblack>
                                                     <?php echo $var_date?>
                                                </td>
                                               </tr>
                                           </table>
                                          </span>
                                         </td>
                                        </tr>
                                       </table>
                                </fieldset></td>
                                <td align="left" valign="top"><fieldset class="fldset">
                                                                          <legend class="gray">Sale Item ...</legend>
                                                                          <table>
        <?
             $sql = "select S.nSaleId,S.nCategoryId,
                     L.vCategoryDesc,S.nUserId,
                     vLoginName as 'UserName',
                     S.vTitle,S.vBrand,S.vType,S.vCondition,
                     S.vYear,S.nValue,S.nPoint,S.nShipping,S.vUrl,S.vDescription,date_format(S.dPostDate,'%m/%d/%Y') as 'dPostDate'
                     from
                     ".TABLEPREFIX."sale S
                         left join ".TABLEPREFIX."users U on S.nUserId = U.nUserId
                         left join ".TABLEPREFIX."category C on S.nCategoryId = C.nCategoryId
                         LEFT JOIN " . TABLEPREFIX . "category_lang L on C.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                     where  
                     nSaleId = '" . addslashes($var_saleid) . "'";

				//echo($sql);
               $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
               if(mysqli_num_rows($result) > 0)
			   {
                  if($row=mysqli_fetch_array($result))
				  {
                                        $var_uname = $row["UserName"];
                                        $var_post_type= $row["vPostType"];
                                        //$var_payment_flag=($row["vSwapStatus"] == "2")?false:true;
                                         //$var_payment_flag=($row["vSwapStatus"] == "2" || $row["vSwapStatus"] == "3")?false:true;
                                         $pointValue=round(($row["nValue"]/DisplayLookUp('PointValue'))*DisplayLookUp('PointValue2'),2);
                                         $showPrice='<br>'.$pointValue.'&nbsp;('.POINT_NAME.')';

                                        //calculate shipping too
                                        $showShippping='<br>'.round(($row["nShipping"]/DisplayLookUp('PointValue'))*DisplayLookUp('PointValue2'),2).'&nbsp;('.POINT_NAME.')';
      ?>
                                              <tr>
                                                  <td colspan="2" width="100%"  class=textgrey>
                                                      <?
                                                        if($row["vUrl"] == ""){
                                                           echo("Picture n/a");
                                                        }
                                                        else{
                                                           echo("<img src=\"../" . $row["vUrl"] . "\" width='40' height='40'>");
                                                        }
                                                      ?>
                                                  </td>
                                              </tr>
											  <tr>
                                                  <td  class=textgrey>
                                                      Owner:
                                                  </td>
                                                  <td class=textblack>
                                                       <?php echo $row["UserName"]?>
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td  class=textgrey>
                                                      Title:
                                                  </td>
                                                  <td class=textblack>
                                                       <?php echo $row["vTitle"]?>
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td class=textgrey>
                                                     Description:
                                                  </td>
                                                  <td class=textblack>
                                                       <?php echo $row["vDescription"]?>
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td class=textgrey>
                                                      Brand:
                                                  </td>
                                                  <td class=textblack>
                                                       <?php echo $row["vBrand"]?>
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td class=textgrey>
                                                      Type:
                                                  </td>
                                                  <td class=textblack>
                                                       <?php echo $row["vType"]?>
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td class=textgrey>
                                                      Condition:
                                                  </td>
                                                  <td class=textblack>
                                                       <?php echo $row["vCondition"]?>
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td class=textgrey>
                                                      Price:
                                                  </td>
                                                  <td class=textblack>
                                                       <?php echo CURRENCY_CODE;?><?php echo $row["nValue"]?> <?php //echo $showPrice;?>
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td class=textgrey>
                                                      <?php echo POINT_NAME; ?>:
                                                  </td>
                                                  <td class=textblack>
                                                      <?php echo $row["nPoint"]?>
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td class=textgrey>
                                                      Shipping:
                                                  </td>
                                                  <td class=textblack>
                                                       <?php echo CURRENCY_CODE;?><?php echo $row["nShipping"]?> <?php //echo $showShippping;?>
                                                  </td>
                                              </tr>
<!--
                                              <tr>
                                                  <td>
                                                      User:
                                                  </td>
                                                  <td>
                                                       <?php echo $row["UserName"]?>
                                                  </td>
                                              </tr>
-->
                                              <tr>
                                                  <td class="textgrey">
                                                      Post Date:
                                                  </td>
                                                  <td class=textblack>
                                                       <?php echo $row["dPostDate"]?>
                                                  </td>
                                              </tr>
        <?
                  }
               }
               else{
        ?>
                                              <tr>
                                                  <td colspan="2" height="100%"  class=textgrey>

                                                      The status of this item has been changed.
                                                      You cannot sell this item.
                                                      <br>&nbsp;
                                                      <br>&nbsp;
                                                      <br>&nbsp;
                                                      <br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;
                                                  </td>
                                              </tr>
        <?
               }
        ?>
                                       </table>
                                                                           </fieldset></td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center"><input type="button" name="btClose" value="Close" class="submit" onClick="javascript:window.close();"></td>
                              </tr>
							  </form>
                            </table>
</td>
                          </tr>
                        </table></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
