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
//include ("./includes/session_check.php");

include_once('./includes/gpc_map.php');

$fee_percent = 0;
if (DisplayLookUp('14') != '') {
    $fee_percent = DisplayLookUp('14');
}//end if

  if(DisplayLookUp("Enable Escrow")=="Yes") {
    $escrowType = DisplayLookUp("EscrowCommissionType");
    if($escrowType=="range") {
        $var_calc_amnt=($row["nSwapAmount"] < 0)?(-1 * $row["nSwapAmount"]):(1 * $row["nSwapAmount"]);
        $es_sql = "SELECT * FROM ".TABLEPREFIX."escrowrangefee
                            WHERE vActive = 1 AND (nFrom < '".$var_calc_amnt."' AND nTo > '".$var_calc_amnt."') OR above < '".$var_calc_amnt."' ";
        $es_rs  = mysqli_query($conn, $es_sql);
        if(mysqli_num_rows($es_rs)>0) {
                    $es_rw = mysqli_fetch_array($es_rs);
                    $fee_percent = $es_rw["nPrice"];
                    $var_escrow=$var_calc_amnt * $fee_percent / 100;
                }
            }else {
                if($escrowType=="percentage"){
                    $var_calc_amnt=($row["nAmount"] < 0)?(-1 * $row["nAmount"]):(1 * $row["nAmount"]);
                    $var_escrow=$var_calc_amnt * $fee_percent / 100;
                }else{
                    $var_escrow = $fee_percent;
                }
            }
        }



include_once('./includes/title.php');
?>
<script language="javascript" type="text/javascript">
    var percent="";
    percent=<?php echo  $fee_percent ?>;


    var xmlHttp

function calculate()
{
    xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}//end if
    val=document.getElementById("amount").value;
    
    if ((isNaN(val))||(val<0)||(parseInt(val,10)==0))
    {
        alert("<?php echo ERROR_POSITIVE_VALUE; ?>");
        document.getElementById("amount").value=0;
        document.getElementById("fees").value=0;
        document.getElementById("amount").focus();
    }//end if
    else{

    var url="check_escrow.php"
    url=url+"?q="+val
    url=url+"&sid="+Math.random()
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
    }
}//end function

function stateChanged()
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{
		document.getElementById("fees").innerHTML=xmlHttp.responseText
	}//end if
}//end function

function GetXmlHttpObject()
{
	var objXMLHttp=null
	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest()
	}//end if
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
	}//end else if
	return objXMLHttp
}//end function  

/*
    function calculate()
    {
        val=document.getElementById("amount").value;
        if ((isNaN(val))||(val<0)||(parseInt(val,10)==0))
        {
            alert("<?php //echo ERROR_POSITIVE_VALUE; ?>");
            document.getElementById("amount").value=0;
            document.getElementById("fees").value=0;
            document.getElementById("amount").focus();
        }//end if
        else
        {
            var OrginalValue=parseFloat(percent)*parseFloat(document.getElementById("amount").value)/100;

            var RoundedValue = OrginalValue * Math.pow(10, 2);
            RoundedValue = Math.round(RoundedValue);
            RoundedValue = RoundedValue / Math.pow(10, 2);

            document.getElementById("fees").value=RoundedValue;
        }//end else
    }//end if
    */
</script>
<body onLoad="timersOne();">
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="headerbg">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td class="link3">&nbsp;</td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="heading" align="left"><?php echo TEXT_ESCROW_FEES; ?></td>
                                            </tr>
                                        </table>
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                                            <tr>
                                                <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left" colspan="2">
                                                                        <?php
                                                                            if($escrowType=="fixed") {
//                                                                                echo CURRENCY_CODE;
//                                                                                echo $fee_percent;
                                                                                }

                                                                            if($escrowType=="percentage"){
//                                                                               echo $fee_percent; echo "%";
                                                                             }
                                                                            if($escrowType=="range"){
//                                                                                echo ERROR_ENTER_AMOUNT_TO_VIEW_ESCROW_FEE;
                                                                            }
                                                                            echo ERROR_ENTER_AMOUNT_TO_VIEW_ESCROW_FEE;
                                                                         ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td width="35%" align="left"><?php echo TEXT_POSTING_AMOUNT; ?></td>
                                                                        <td width="65%" align="left"><?php echo CURRENCY_CODE; ?><input type="text" name="amount" id="amount" class="textbox2" size="4" value="0"></td>
                                                                    </tr>
                                                                    
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td align="left">&nbsp;</td>
                                                                        <td align="left"><input type="button" onClick="calculate();" name="calculate" value="<?php echo BUTTON_CALCULATE; ?>" class="submit"></td>
                                                                    </tr>
                                                                    <tr bgcolor="#FFFFFF">
                                                                        <td colspan="2" align="left"> <?php echo TEXT_ESCROW_FEES.': '.CURRENCY_CODE;?>
                                                                            <span id="fees">0</span>
                                                                                <!-- <input type="text" name="fees" id="fees" value="0" class="textbox2"> -->
                                                                                </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table></td>
                    </tr>
                </table>
                </body></html>