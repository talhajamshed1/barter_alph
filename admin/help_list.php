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


define('COLOR1','#FFFFFF');
define('COLOR2','#FFFFFF');

//$sql="SELECT h.*,hc.* FROM ".TABLEPREFIX."help h left join ".TABLEPREFIX."helpcategory hc on h.nHcId=hc.nHcId
//			where hc.vHtype='admin' and h.vActive='1' GROUP BY h.nHcId ORDER BY h.nHposition desc";

//all category listing
	$sql = "SELECT h.*,hp.*,hct.vHctitle,hc.*,h.vActive as ha,h.nHposition as nPos
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
              GROUP BY hc.nHcId
              ORDER BY hc.nHcposition ASC,h.nHposition ASC";
        
       

$sess_back= $targetfile .  "?begin=" . $begin . "&num=" . $num . "&numBegin=" . $numBegin . "&cmbSearchType=" . $cmbSearchType . "&txtSearch=" . $txtSearch . "&source=" . $var_source . "&no=" . $var_no;

//get the total amount of rows returned
$totalrows = mysqli_num_rows(mysqli_query($conn, $sql));

/*
Call the function:

I've used the global $_GET array as an example for people
running php with register_globals turned 'off' :)
*/

$navigate = pageBrowser($totalrows,10,10,"&cmbSearchType=$cmbSearchType&txtSearch=" . urlencode($txtSearch) . "&",$_GET[numBegin],$_GET[start],$_GET[begin],$_GET[num]);

//execute the new query with the appended SQL bit returned by the function
$sql = $sql.$navigate[0];
$rs = mysqli_query($conn, $sql) or die(mysqli_error($conn));
?>
<div class="row admin_wrapper">
	<div class="admin_container">

 
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="18%" valign="top"> <!--  Admin menu comes here -->
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
                        <td width="84%" class="heading_admn boldtextblack" align="left">Admin Help Listing</td>
                        <td width="16%">&nbsp;</td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                      <tr>
                        <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
							<?php
										if(mysqli_num_rows($rs)!='0')
										{
									?>
							<tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left">
									<table width="100%"  border="0" cellspacing="1" cellpadding="5">
									<tr>
    									<td align="left"><?php echo($navigate[2]);?></td>
    									<td align="right"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
 									</tr>
									</table></td>
                              </tr>
							  <?php   }//end if
							  			echo '<tr>';
										if(mysqli_num_rows($rs)>0)
										{	
											for($i=0;$i<mysqli_num_rows($rs);$i++)
											{
//												$contents=mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."help where vActive='1' and
//																		nHcId='".mysqli_result($rs,$i,'nHcId')."' ORDER BY nHposition desc") or die(mysqli_error($conn));
                                                                                               $contents=mysqli_query($conn, "SELECT h.*,hp.*,hct.vHctitle,hc.*,h.vActive as ha,h.nHposition as nPos
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
                                                                                                   AND hc.nHcId = '".mysqli_result($rs,$i,'nHcId')."' ORDER BY h.nHposition ASC");
												if(mysqli_num_rows($contents)>0)
												{	
													if($k==4)
													{
														echo '</tr><tr><td width="25%" valign="top"><table class="subheader" border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF"><tbody>
 																		  <td height="24" bgcolor="#B7B7B7" align="left">&nbsp;&nbsp;<b>'.ucfirst(mysqli_result($rs,$i,'vHctitle')).'</b></td></tr>';
																		 
														$k=1;
													}//end if
													else
													{
														echo '<td width="25%" valign="top"><table class="subheader" border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF"><tbody>
 																		  <td height="24" bgcolor="#B7B7B7">&nbsp;&nbsp;<b>'.ucfirst(mysqli_result($rs,$i,'vHctitle')).'</b></td></tr>';
														$k++;
													}//end else
													
													
													for($j=0;$j<mysqli_num_rows($contents);$j++)
												    {
														$bgcolor = ($bgcolor == COLOR1) ? COLOR2 : COLOR1;
										?>
							  <tr bgcolor="<?php echo $bgcolor; ?>">
                                <td width="4%" align="left" bgcolor="<?php echo $bgcolor;?>" colspan="2"><table width="100%"  border="0" cellpadding="5" cellspacing="1" class="maintext2"><tbody>
  <tr>
    <td align="center" valign="middle" width="3%"><a href="help_details.php?id=<?php echo mysqli_result($contents,$j,'nHId');?>&name=<?php echo mysqli_result($contents,$j,'vHtitle');?>"><img src="../help/<?php echo mysqli_result($contents,$j,'vHimage');?>" width="22" height="20" alt="<?php echo mysqli_result($contents,$j,'vHtitle');?>" border="0">
	  </a></td>
    <td align="left" valign="top" width="97%"><a href="help_details.php?id=<?php echo mysqli_result($contents,$j,'nHId');?>&name=<?php echo mysqli_result($contents,$j,'vHtitle');?>"><?php echo mysqli_result($contents,$j,'vHtitle');?></a></td>
  </tr></tbody>
                                  </table></td>
                              </tr>
							  <?php
													}//end inner for loop
												}//end if
												echo '</tbody></table></td></tr>';
											}//end out for loop
										}//end if
							?>
							 <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><table width="100%"  border="0" cellspacing="1" cellpadding="5">
  <tr>
    <td align="left"><?php echo($navigate[2]);?></td>
    <td align="right"><?php echo("Listing $navigate[1] of $totalrows results.");?></td>
  </tr>
</table></td>
                              </tr>
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