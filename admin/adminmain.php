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
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');

$sale_tot=fetchSingleValue(select_rows(TABLEPREFIX.'sale','count(*) as cnt',"where vDelStatus='0' and nQuantity>'0'"),'cnt');
$wish_tot=fetchSingleValue(select_rows(TABLEPREFIX.'swap','count(*) as cnt',"where vPostType='wish' and vDelStatus='0'"),'cnt');
$swap_tot=fetchSingleValue(select_rows(TABLEPREFIX.'swap','count(*) as cnt',"where vPostType='swap' and vDelStatus='0'"),'cnt');
$users_tot=fetchSingleValue(select_rows(TABLEPREFIX.'users','count(*) as cnt',"where vDelStatus='0'"),'cnt');
$users_pend=fetchSingleValue(select_rows(TABLEPREFIX.'users','count(*) as cnt',"where vDelStatus='0' and vStatus='1'"),'cnt');
$sale_pend=fetchSingleValue(select_rows(TABLEPREFIX.'saleinter','count(*) as cnt',"where vDelStatus='0'"),'cnt');
$swapwish_pend=fetchSingleValue(select_rows(TABLEPREFIX.'swapinter','count(*) as cnt',"where vDelStatus='0'"),'cnt');
$featured_pend=fetchSingleValue(select_rows(TABLEPREFIX.'saleextra','count(*) as cnt',"where vMode!=''"),'cnt');
?>
<div class="row admin_wrapper">
	<div class="admin_container">
    
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="18%" valign="top"> <!--  Admin menu comes here -->
		                                 <?php require("../includes/adminmenu.php"); ?>
									<!--   Admin menu  comes here ahead --></td>
                   <td width="4%"></td>
                  <td width="78%" valign="top">
          
                   
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top">
						<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td bgcolor="">
                            <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2 admin_table1">
<form name="frmAffMain" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>">
                              <tr bgcolor="#ffffff">
                                <td width="100%" colspan="2" align="center" class="">
                                <div class="innersubheader">
                                	<h2> Welcome To Admin Panel</h2>
                                </div>
                               </td>
                              </tr>
                              
                              <tr bgcolor="">
                                <td colspan="2" align="center">
                                
                               <div class="admin_dashboard">
                                <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                              <tr bgcolor="" class="rowunderline">
                                <td colspan="4" align="left" class="subheader">Statistics</td>
                                </tr>
                              <tr bgcolor="">
                                <td width="52%" align="left" class="maintext2">Total Number of Swap Item(s)</td>
                                <td width="4%" align="left" valign="middle">&raquo;</td>
                                <td width="13%" align="left">&nbsp;</td>
                                <td width="31%" align="right"><?php echo $swap_tot;?></td>
                              </tr>
							  <?php
							  		//checking point enable in website
									if(DisplayLookUp('EnablePoint')!='1')
									{
							  ?>
                              <tr bgcolor="">
                                <td align="left">Total Number of Sale Item(s)</td>
                                <td align="left" valign="middle">&raquo;</td>
                                <td align="left">&nbsp;</td>
                                <td align="right"><?php echo $sale_tot;?></td>
                              </tr>
							  <?php
							  		}//end if
							  ?>
                              <tr bgcolor="">
                                <td align="left">Total Number of Wish Item(s)</td>
                                <td align="left" valign="middle">&raquo;</td>
                                <td align="left">&nbsp;</td>
                                <td align="right"><?php echo $wish_tot;?></td>
                              </tr>
                              <tr bgcolor="">
                                <td align="left">Total Number of Users</td>
                                <td align="left" valign="middle">&raquo;</td>
                                <td align="left">&nbsp;</td>
                                <td align="right"><?php echo $users_tot;?></td>
                              </tr>
                              <tr bgcolor="" class="rowunderline">
                                <td align="left" class="subheader">Pending Lists </td>
                                <td align="left" valign="middle">&nbsp;</td>
                                <td align="left">&nbsp;</td>
                                <td align="right">&nbsp;</td>
                              </tr>
                              <tr bgcolor="">
                                <td align="left">Registration</td>
                                <td align="left" valign="middle">&raquo;</td>
                                <td align="left">&nbsp;</td>
                                <td align="right"><?php echo $users_pend;?></td>
                              </tr>
							   <?php
							  		//checking point enable in website
						/*			if(DisplayLookUp('EnablePoint')!='1')
									{
							  ?>
                              <tr bgcolor="">
                                <td align="left">Sale Approvals </td>
                                <td align="left" valign="middle">&raquo;</td>
                                <td align="left">&nbsp;</td>
                                <td align="right"><?php echo $sale_pend;?></td>
                              </tr>
							  <?php
							  		}
							  ?>
                              <tr bgcolor="">
                                <td align="left">Swap/Wish Approvals </td>
                                <td align="left" valign="middle">&raquo;</td>
                                <td align="left">&nbsp;</td>
                                <td align="right"><?php echo $swapwish_pend;?></td>
                              </tr>
							   <?php
							  		//checking point enable in website
									if(DisplayLookUp('EnablePoint')!='1')
									{
							  ?>
                              <tr bgcolor="">
                                <td align="left">Item Addition Approvals </td>
                                <td align="left" valign="middle">&raquo;</td>
                                <td align="left">&nbsp;</td>
                                <td align="right"><?php echo $featured_pend;?></td>
                              </tr>
							  <?php
							  		}*/
							  ?>
                            </table>
                               </div>
</td>
                              </tr>

							  </form>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="right" class="help"><a href="help_list.php">Need help for the admin control panel<span class="glyphicon glyphicon-question-sign"></span></a></td>
                              </tr>
                            </table>
</td>
                          </tr>
                        </table></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
        
    </div>
</div>
<?php include_once('../includes/footer_admin.php');?>			  