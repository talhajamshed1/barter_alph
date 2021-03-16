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
include_once('../includes/headeradmin.php');
include_once('../includes/admin_login_session.php');
$PGTITLE='help';

if($_GET['mode']=='edit') {
    
    $mode='edit';
    $btnVal='Edit';
}//end if
else {
    $btnVal='Add';
}//end else


if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]!='') {
    if (function_exists('get_magic_quotes_gpc') ) {
        $nHId = stripslashes($_POST['nHId'] );
        $ddlCategory = stripslashes($_POST["ddlCategory"]);
        $txtTitle = $_POST["txtTitle"];
        $txtDes =  $_POST["txtDes"];
        $radActive =  stripslashes($_POST["radActive"]);
        $assignedname =  stripslashes($_POST["assignedname"]);
    }//end if
    else {
        $nHId = $_POST['nHId'] ;
        $ddlCategory = $_POST["ddlCategory"];
        $txtTitle = $_POST["txtTitle"];
        $txtDes = $_POST["txtDes"];
        $radActive = $_POST["radActive"];
        $assignedname =  $_POST["assignedname"];
    }//end else


    if($_FILES['hfile']['name']!='') {
        //if file already exits remove
        @unlink('../help/'.$assignedname);

        $name=$_FILES['hfile']['name'];
        @chmod('../help',0777);
        $assignedname=time().'_'.$name;
        @copy($_FILES['hfile']['tmp_name'],'../help/'.$assignedname);
    }//end if
    else {
        $assignedname=$assignedname;
    }//end else


    if ($txtTitle[0] == "") {
        $message .= "* First title is mandatory<br>";
        $error = true;
    } //end if

    if ($txtDes[0] == "") {
        $message .= "* First description is mandatory<br>";
        $error = true;
    } //end if

}//end if


if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Add Help' && !$error) {
    $sql = mysqli_query($conn, "SELECT MAX(nHposition) as max from ".TABLEPREFIX."help") or die(mysqli_error($conn));
    $rw = mysqli_fetch_array($sql);
    $maxorder = $rw['max']+1;

    mysqli_query($conn, "insert into ".TABLEPREFIX."help (nHcId,nHposition,vActive,vHimage) values
					('".addslashes($ddlCategory)."','".$maxorder."','".addslashes($radActive)."','".addslashes($assignedname)."')") or die(mysqli_error($conn));
    $last_insert_id = mysqli_insert_id($conn);
    $i = 0;
    foreach($txtTitle as $title) {
        $language_id = $_POST["lang$i"];
        $title_sql = "INSERT INTO ".TABLEPREFIX."help_lang(help_id,lang_id,vHtitle,vHdescription) VALUES (
                                        '".$last_insert_id."','".$language_id."','".addslashes($title)."','".addslashes($txtDes[$i])."')";
        $title_rs  = mysqli_query($conn, $title_sql);
        $i++;
    }

    header('location:help.php?msg=a&ddlCategory='.$ddlCategory);
    exit();
}//end if

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Edit Help' && !$error) {
    
    $usql = "update ".TABLEPREFIX."help set vActive='".addslashes($radActive)."',
						 nHcId='".addslashes($ddlCategory)."'";
    if($assignedname!="")
        $usql .= ",vHimage='".addslashes($assignedname)."'";
	$usql .= " WHERE nHId='".$nHId."'";
        $u_rs = mysqli_query($conn, $usql) or die(mysqli_error($conn));

    $i = 0;
    foreach($txtTitle as $title) {
        $language_id = $_POST["lang$i"];
        $sel = "select * from ".TABLEPREFIX."help_lang WHERE help_id ='".$nHId."' and lang_id = '".$language_id."' ";
        $srs = mysqli_query($conn, $sel);

        if(mysqli_num_rows($srs)){
        $title_sql = "UPDATE ".TABLEPREFIX."help_lang SET vHtitle='".addslashes($title)."',vHdescription='".addslashes($txtDes[$i])."'
                      WHERE help_id = '".$nHId."' AND lang_id = '".$language_id."'";
        $title_rs  = mysqli_query($conn, $title_sql);
        }else{
            $title_sql = "INSERT INTO ".TABLEPREFIX."help_lang(help_id,lang_id,vHtitle,vHdescription) VALUES (
                                        '".$nHId."','".$language_id."','".addslashes($title)."','".addslashes($txtDes[$i])."')";
            $title_rs  = mysqli_query($conn, $title_sql);
        }
        
        $i++;
    }
    header('location:help.php?msg=e&ddlCategory='.$ddlCategory);
    exit();
}//end if

//if category is blank
if($ddlCategory=='') {
    $ddlCategory=$_GET['ddlCategory'];
}//end if


//fectch category from Help category table
$category   = mysqli_query($conn, "SELECT *
                             FROM " . TABLEPREFIX . "helpcategory_lang L
                             JOIN " . TABLEPREFIX . "helpcategory H
                               ON L.help_cat_id = H.nHcId
                              AND L.lang_id = '".$_SESSION["lang_id"]."'
                              AND H.vHtype='client' AND H.vActive='1' ORDER BY H.nHcposition DESC") or die(mysqli_error($conn));

$langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                                                    WHERE lang_status = 'y'";
$langRs      = mysqli_query($conn, $langSql);

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<script language="javascript" type="text/javascript">
    function category_chk()
    {
       /* if (trim(document.frmHelp.ddlCategory.value)=="")
        {
            alert("Category can't be blank");
            document.frmHelp.ddlCategory.focus();
            return false;
        }*/

<?php if($_GET['nHId']=='') {?>
                 else if (trim(document.frmHelp.hfile.value)=="")
                 {
                     alert("Help Image can't be blank");
                     document.frmHelp.hfile.focus();
                     return false;
                 }//end if
    <?php }?>
                 else
                 {
                     if(trim(document.frmHelp.himage.value)!='')
                     {
                         UploadFile();
                     }//end if
                     return true;
                 }//end else
             }

             function showPrograsBar()
             {
                 FreezePage();
                 document.getElementById('uploadimageloader').style.display='inline';
                 //PrograsBar =window.open("prograssbar.php",'welcome','resizable=no,width=0,height=0,screenX=150,screenY=150,top=150,left=150');

             }
             function closePrograsBar(){
                 if (PrograsBar && PrograsBar.open && !PrograsBar.closed) PrograsBar.close();
             }

             function UploadFile(){
                 showPrograsBar();
             }
             function FreezePage()
             {
                 document.getElementById("parentDiv").style.width = window.screen.width;
                 document.getElementById("parentDiv").style.height = window.screen.height*20;
                 document.getElementById("parentDiv").style.display = 'inline';

                 ShowIndicator();
             }

             function ShowIndicator()

             {
                 document.getElementById("waitIndicatorDiv").style.display = 'block';
             }
             function change_sitemap(){
                 document.frmHelp.img1.src=document.frmHelp.himage.value;
             }

</script>
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
                    <td width="100%" class="heading_admn boldtextblack" align="left"><?php echo $btnVal;?> Help</td>
                </tr>
            </table>
            <div id="parentDiv" class="transparent" style="position:absolute; background-color:Gray;
                 width:100%; height:500%; display:none; top:0px; left:0px; z-index:5000;">
            </div>


            <div id="waitIndicatorDiv" class="Indicator">
                <table id="uploadimageloader" width="50%" style="display:none;" align="center">
                    <tr>
                        <td align="center"><img src="../images/loading.gif"></td>
                    </tr>
                </table>
            </div>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10">
                <tr>
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#EEEEEE"><table width="100%"  border="0" cellspacing="1" cellpadding="0" class="admin_tble_2">

                                        <form name="frmHelp" method ="POST" action = "" onSubmit="return category_chk()" enctype="multipart/form-data">
                                            <?php if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="3" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                            <?php  }//end if?>
                                            <tr bgcolor="#FFFFFF">
                                            <input type="hidden" name="nHId" value="<?php echo $_GET['nHId'];?>">
                                            <input type="hidden" name="ddlCategory" value="<?php echo $ddlCategory;?>">
                                            <input name="assignedname" type="hidden"  value="<?php echo $assignedname?>">
                                            <td colspan="3" align="left" class="warning"> * indicates mandatory fields</td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">Category <span class="warning">*</span></td>
                                                <td colspan="2">
                                                    <select name="ddlCategory" class="textbox2">
                                                        <?php
                                                        if(mysqli_num_rows($category)>0) {
                                                            while($arr=mysqli_fetch_array($category)) {?>
                                                                    <option value="<?php echo $arr['nHcId'];?>" <?php if($ddlCategory==$arr['nHcId']) {
                                                                        echo 'selected';
                                                                    }?>><?php echo $arr['vHctitle'];?></option>
                                                                    <?php
                                                                }//end while
                                                            }//end if
                                                            else {
                                                                echo '<option value="Nil">NIL</option>';
                                                            }//end if?>

                                                    </select>
                                                </td>
                                            </tr>
                                            <?php
                                            $i=0;
                                            while($langRow = mysqli_fetch_array($langRs)) {

                                                if($mode=='edit'){
                                               $c_sql = "SELECT h.*,hp.*,hc.*,h.vActive as ha,h.nHposition as nPos
                                                            FROM ".TABLEPREFIX."help h
                                                       LEFT JOIN ".TABLEPREFIX."helpcategory hc
                                                              ON h.nHcId=hc.nHcId
                                                            JOIN ".TABLEPREFIX."help_lang hp
                                                              ON h.nHId = hp.help_id
                                                             AND hp.lang_id = '".$langRow["lang_id"]."'
                                                           WHERE hc.vHtype='client'
                                                             AND h.nHId='".$_GET['nHId']."'";
                                                        $rs_c =   mysqli_query($conn, $c_sql);

                                                        $rw_c       = mysqli_fetch_array($rs_c);
                                                        $txtTitleOld   = $rw_c['vHtitle'];
                                                        $txtDescOld    = $rw_c['vHdescription'];
                                                        $radActive  = $rw_c['ha'];
                                                        $assignedname = $rw_c['vHimage'];
                                                }

                                              ?>

                                            <tr bgcolor="#FFFFFF">
                                                <td width="31%" align="left"><?php echo ucwords($langRow["lang_name"])?> Title <?php if($i==0){?><span class="warning">*</span><?php }?></td>
                                                <td colspan="2">
                                                    <input type="text" class="textbox2" name="txtTitle[]" size="70" value="<?php if($txtTitleOld){echo stripslashes($txtTitleOld);}else{echo stripslashes($txtTitle[$i]);}?>"/>
                                                    <input type="hidden" name="lang<?php echo $i; ?>" value="<?php echo $langRow["lang_id"]?>" >
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" valign="top"><?php echo ucwords($langRow["lang_name"])?> Description <?php if($i==0){?><span class="warning">*</span><?php }?></td>
                                                <td colspan="2" align="left" valign="top"><textarea name="txtDes[]" cols="65" rows="15" class="textbox2"><?php if($txtDescOld){echo htmlentities(stripslashes($txtDescOld));}else{echo stripslashes($txtDesc[$i]);}?></textarea></td>
                                            </tr>
                                            <?php
                                            $i++;
                                            } 
                                            ?>
                                            
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left">Help File <!-- <span class="warning">*</span> --></td>
                                                <td width="46%" align="left"><input type="file" name="hfile" class="textbox2" onChange="change_sitemap();">
                                                    <br><br><span class="warning">Image size should be 22 x 20</span></td>
                                                <td width="23%" align="left"><?php if($assignedname!='') {
                                                        echo ' <img src="../help/'.$assignedname.'" name="img1" width="22" height="20">';
                                                    }//end if
                                                    else {
                                                        echo ' <img src="../images/default_album.gif" name="img1" width="22" height="20">';
                                                    }//end else?>
                                                </td>
                                            </tr>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left"> Active </td>
                                                <td colspan="2" align="left"><input type="radio" name="radActive" value="1" <?php if($radActive=='1') {
                                                            echo 'checked';
                                                        }if
                                                    ($radActive=='') {
                                                        echo 'checked';
                                                    }?>>Yes <input type="radio" name="radActive" value="0" <?php if($radActive=='0') {
                                                        echo 'checked';
                                                    }?>>No
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td colspan="2"><input type="submit" name="btnSubmit" value="<?php echo $btnVal;?> Help" class="submit"/></td>
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
</div>
</div>
<?php include_once('../includes/footer_admin.php');?>