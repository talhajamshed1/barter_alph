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
$PGTITLE='help_list';
?>
<div class="row admin_wrapper">
	<div class="admin_container">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="19%" valign="top"> <!--  Admin menu comes here -->
		                                 <?php require("../includes/adminmenu.php"); ?>
									<!--   Admin menu  comes here ahead --></td>
                   <td width="4%"></td>
                  <td width="78%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td bgcolor="#FFFFFF"><img src="../images/spacer.gif" width="1" height="1"></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="94%" height="32" class="headerbg">&nbsp;</td>
                      <td width="6%" align="right" valign="top" class="headerbg"><a href="adminmain.php"><img src="../images/home-icon1.gif" width="44" height="25" border="0"></a></td>
                    </tr>
                  </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td width="84%" class="heading_admn boldtextblack" align="left">Help About - <?php echo strtoupper($_GET['name']);?></td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                              <?php 
                                 $help="SELECT h.*,hp.*,hct.vHctitle,hc.*,h.vActive as ha,h.nHposition as nPos
                                          FROM ".TABLEPREFIX."help h
                                     LEFT JOIN ".TABLEPREFIX."helpcategory hc
                                            ON h.nHcId=hc.nHcId
                                          JOIN ".TABLEPREFIX."helpcategory_lang hct
                                            ON hc.nHcId = hct.help_cat_id
                                          JOIN ".TABLEPREFIX."help_lang hp
                                            ON h.nHId = hp.help_id
                                           AND hp.lang_id = '".$_SESSION["lang_id"]."'
                                           AND hct.lang_id = '".$_SESSION["lang_id"]."'
                                           AND hc.vHtype='admin'
                                           AND hc.vActive='1'
                                           AND h.nHId = '".$_GET["id"]."'";
										
                                $result = mysqli_query($conn, $help) or die(mysqli_error($conn));
                                if(mysqli_num_rows($result)>0)
                                {
				?>
							  <tr bgcolor="#FFFFFF">
                                <td width="20%" align="left"><?php echo nl2br(mysqli_result($result,0,'vHdescription'));?></td>
                              </tr>
							  <tr bgcolor="#FFFFFF">
							    <td align="left"><a href="help_list.php"><b>Back</b></a></td>
						      </tr>
							   <?php
					  		  		}//end if
							 		else
							  		{
							  	 		echo '<tr bgcolor="#FFFFFF"><td align="left">&nbsp;</td></tr>';
							  	    }//end else
					  		?>
                            </table></td>
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