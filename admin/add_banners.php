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
$PGTITLE='banners';
if($_GET['mode']=='edit') {
    $sql=mysqli_query($conn, "SELECT *
                            FROM ".TABLEPREFIX."banners B
                            JOIN ".TABLEPREFIX."banners_lang L
                           WHERE B.nBId='".$_GET['id']."'
                             AND L.banner_id = '".$_GET['id']."'") or die(mysqli_error($conn));
    if(mysqli_num_rows($sql)>0) {
        $banner_row = mysqli_fetch_array($sql);

        $txtBannerName=mysqli_result($sql,0,'vName');
        $txtBannerLoc=mysqli_result($sql,0,'vlocUrl');
        $radActive=mysqli_result($sql,0,'vActive');
        $assignedname=mysqli_result($sql,0,'vImg');
        $radLocation=mysqli_result($sql,0,'vLocation');
        $http='';
    }//end if
    $mode='edit';
    $btnVal='Edit';
}//end if
else {
    $btnVal='Add';
    $http='http://';
}//end else


if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]!='') {
   
    if (function_exists('get_magic_quotes_gpc')) {
        $nBId = addslashes($_POST['nBId'] );
        $txtBannerLoc = addslashes($_POST["txtBannerLoc"]);
        $radLocation=addslashes($_POST["radLocation"]);
        $radActive=addslashes($_POST['radActive']);
        $txtBannerName = $_POST["txtBannerName"];
    }//end if
    else {
        $nBId = $_POST['nBId'] ;
        $txtBannerName = $_POST["txtBannerName"];
        $txtBannerLoc = $_POST["txtBannerLoc"];
        $radLocation=$_POST["radLocation"];
        $radActive=$_POST['radActive'];
    }//end else

    $http='';
    //validation
$_SESSION['sessionMsg'] ='';
    if($_FILES['txtPic']['name']!='') {
        if ($_FILES['txtPic']['size'] <= 0 or !isValidWebImageType($_FILES['txtPic']['type'],$_FILES['txtPic']['name'],$_FILES['txtPic']['tmp_name'])) {
            $message .= "* Please upload a valid banner image(jpg/gif/png)<br>";
            $error = true;
        } //end if

        list($newwidth,$newheight)=@getimagesize($_FILES['txtPic']['tmp_name']);

        $chkWidth='728'; //468
        $chkHight='90'; //60

        if($newwidth>=$chkWidth && $newheight>=$chkHight) {
            @chmod('../banners',0777);
            //remove the old image from the banner folder
            @unlink('../banners/'.$_POST['assignedname']);

            if (!$error) {
                if ($_FILES['txtPic']['size'] > 0) {
                    $imagewidth_height_type_array = explode(":", ImageType($_FILES['txtPic']['tmp_name']));
                    $imagetype = $imagewidth_height_type_array[0];
                    $imagetype_width=$imagewidth_height_type_array[1];
                    $imagetype_height=$imagewidth_height_type_array[2];
                    $assignedname = "banner" . time() . "." . $imagetype;
                }//end if
                else {
                    $assignedname=$_POST['assignedname'];
                    list($imagetype_width,$imagetype_height)=@getimagesize("../banners/".$assignedname);
                }//end else

                @chmod("../banners/$assignedname", 0777);
                @copy($_FILES['txtPic']['tmp_name'], "../banners/" . $assignedname);
                resizeImg("../banners/" . $assignedname, $chkWidth ,$chkHight, false,100, 0,"");
            }//end if
            $imgFlag=true;
        }//end if
        else {
            $imgFlag=false;$error=true;
            $_SESSION['sessionMsg'] = "Banner size should be ".$chkWidth."x".$chkHight." or greater";
            //header('location:add_banners.php');
            //exit();
        }//end else
    }

        if ($txtBannerName[0] == "") {
        $message .= "* First banner name is mandatory<br>";
        $error = true;
    } //end if
    if (trim($txtBannerLoc) == "") {
        $message .= "* Please Enter banner Location<br>";
        $error = true;
    } //end if

}//end if
function insertBannerName($last_banner_id,$bannername,$language_id) {
        /*
      * insert into  banner language table
        */
        global $conn;
        $sqlinsertbandet    = "INSERT INTO ".TABLEPREFIX."banners_lang(banner_id,lang_id,vName) VALUES ";
        $sqlinsertbandet   .= " ('". $last_banner_id ."',$language_id,'". addslashes(trim($bannername)) ."') ";
        $resultinsertbandet = mysqli_query($conn, $sqlinsertbandet) or die(mysqli_error($conn));

    }

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Add Banner' && !$error) {
    $imgFlag=true;
    
    if($imgFlag==true && $_POST['id']=='') {
        $sql = mysqli_query($conn, "SELECT MAX(nPosition) as max from ".TABLEPREFIX."banners") or die(mysqli_error($conn));
        $rw = mysqli_fetch_array($sql);
        $maxorder = $rw['max']+1;

        mysqli_query($conn, "insert into ".TABLEPREFIX."banners (vlocUrl,vImg,nDate,vWidth,vHeight,nPosition,vActive,vLocation) values
							('".addslashes(trim($txtBannerLoc))."','".addslashes($assignedname)."',
							now(),'".$imagetype_width."','".$imagetype_height."','".$maxorder."','".addslashes($radActive)."',
							'".addslashes($radLocation)."')") or die(mysqli_error($conn));

        $last_banner_id = mysqli_insert_id($conn);

        /*
                 * call function to insert details into banner language table
        */

        $i = 0;
        foreach($txtBannerName as $bannername) {
            $language_id = $_POST["lang$i"];
            
            insertBannerName($last_banner_id,$bannername,$language_id);
            $i++;
        }

        header('location:banners.php?msg=a');
        exit();
    }//end if
}//end if

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Edit Banner' && !$error) {
    if($_POST['id']!='') {
        if($imgFlag==true){
        mysqli_query($conn, "update ".TABLEPREFIX."banners set vlocUrl='".addslashes(trim($txtBannerLoc))."',vWidth='".$imagetype_width."',
							vImg='".addslashes($assignedname)."',vHeight='".$imagetype_height."',vLocation='".addslashes($radLocation)."',
							vActive='".addslashes($radActive)."' where nBId='".$_POST['id']."'") or die(mysqli_error($conn));
        }else{
            mysqli_query($conn, "update ".TABLEPREFIX."banners set vlocUrl='".addslashes(trim($txtBannerLoc))."',vLocation='".addslashes($radLocation)."',
							vActive='".addslashes($radActive)."' where nBId='".$_POST['id']."'") or die(mysqli_error($conn));
        }

        /*
                 * Update details into banner language table
        */
      }//end if
        $i = 0;
        foreach($txtBannerName as $bannername) {
            $language_id = $_POST["lang$i"];

       $sel = "select * from ".TABLEPREFIX."banners_lang WHERE banner_id ='".$_POST['id']."' and lang_id = '".$language_id."' ";
            $srs = mysqli_query($conn, $sel);
            
            
            "UPDATE ".TABLEPREFIX."banners_lang set vName='".addslashes(trim($bannername))."'
                              WHERE banner_id ='".$_POST['id']."' and lang_id = '".$language_id."'";
            if(mysqli_num_rows($srs)){
               
            mysqli_query($conn, "UPDATE ".TABLEPREFIX."banners_lang set vName='".addslashes(trim($bannername))."'
                              WHERE banner_id ='".$_POST['id']."' and lang_id = '".$language_id."'") or die(mysqli_error($conn));
            }else{
                insertBannerName($_POST['id'],$bannername,$language_id);
            }
            $i++;
        }

        header('location:banners.php?msg=e');
        exit();
    
}//end if

$langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                                                    WHERE lang_status = 'y'";
$langRs      = mysqli_query($conn, $langSql);

$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);
?>
<style type="text/css">
    .transparent
    {
        filter:alpha(opacity=50);
        -moz-opacity:0.5;
        opacity:0.5;

        border: 1px solid rgb(204, 102, 0);
        padding: 10px;
        background: rgb(255, 255, 204) none repeat scroll 0%;
        -moz-background-clip: -moz-initial;
        -moz-background-origin: -moz-initial;
        -moz-background-inline-policy: -moz-initial;
        color: rgb(51, 0, 0);
    }

    .Indicator
    {
        font-family:Verdana;
        font-size:25px;
        /*background: Green url(images/indicator_medium.gif) no-repeat right;*/
        position:absolute;
        top:350px;
        left:550px;
        display:none;
        width:55px;
        z-index:99999;

        /*border: 1px solid gray;*/
        padding: 10px;
        /*background: rgb(230, 230, 230) none repeat scroll 0%;*/
        -moz-background-clip: -moz-initial;
        -moz-background-origin: -moz-initial;
        -moz-background-inline-policy: -moz-initial;
        color: gray;
    }
</style>
<script language="javascript" type="text/javascript">
    function validateBanners(){
        var frm = document.frmBanners;
        if(document.getElementById("txtBannerName1")){
            var bval = document.getElementById("txtBannerName1").value;
            if(trim(bval) == ""){
                alert("Banner Name cannot be empty.");
                document.getElementById("txtBannerName1").focus();
                return false;
            }
        }
        if(document.getElementById("txtBannerLoc").value=="http://"){
            alert("Banner Location cannot be empty.");
            document.getElementById("txtBannerLoc").focus();
            return false;
        }
            <?php
            if($_GET['id']=='') {
                ?>
                   else if(trim(frm.txtPic.value)==""){
                            alert("Banner Image cannot be empty.");
                            frm.txtPic.focus();
                            return false;
                        }
             <?php }//end if?>
                        if(frm.txtPic.value!='')
                        {
                            UploadFile();
                        }//end if
                        return true;
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
//                        document.getElementsByName('img1').src = window.URL.createObjectURL(this.files[0])
                        document.frmBanners.img1.src=window.URL.createObjectURL(document.frmBanners.txtPic.files[0]);
                    }

                    //size display check
                    function ImageSize(s)
                    {
                        if(s=='H')
                        {
                            document.getElementById('home1').style.display='';
                            document.getElementById('sub1').style.display='none';
                        }//end if
                        else
                        {
                            document.getElementById('home1').style.display='none';
                            document.getElementById('sub1').style.display='';
                        }//end else
                    }//end function
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
                    <td width="100%" class="heading_admn boldtextblack" align="left"><?php echo $btnVal;?> Banner</td>
                </tr>
            </table>
            <div id="parentDiv" class="transparent" style="position:absolute; background-color:Gray;
                 width:100%; height:500%; display:none; top:0px; left:0px; z-index:5000;">
            </div>


            <div id="waitIndicatorDiv" class="Indicator">
                <table id="uploadimageloader" width="50%" style="display:none;" align="center">
                    <tr>
                        <td align="center"><img src="../images/ajax-loader.gif"></td>
                    </tr>
                </table>
            </div>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                <tr>
                    <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frmBanners" method ="POST" action="<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateBanners();" enctype="multipart/form-data">
                                            <input name="id" type="hidden" id="id" value="<?php echo $_GET['id'];?>">
                                            <input type="hidden" name="assignedname" value="<?php echo $assignedname;?>">
                                            <?php if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td colspan="3" align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                            <?php  }//end if?>
                                            <tr bgcolor="#FFFFFF">
                                            <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
                                            <td colspan="3" align="left" class="warning"> * indicates mandatory fields</td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td width="31%" align="left">Banner Display Location</td>
                                                <td colspan="2"><input type="radio" value="Home" name="radLocation" <?php if($radLocation=='Home') {
                                                    echo 'checked';
                                                }?> onClick="ImageSize('H');">Homepage<input name="radLocation" type="radio" value="Sub" <?php if($radLocation=='Sub') {
                                                    echo 'checked';
                                                }if
                                                ($radLocation=='') {
                                                    echo 'checked';
                                                }?> onClick="ImageSize('S');">
                                                    Subpage</td>
                                            </tr>
                                            <?php
                                            $i=0;
                                            $txtBannerNameEdit  =   '';
                                            while($langRow = mysqli_fetch_array($langRs)) {
                                              
                                                if($mode=="edit") {
                                                  $bl_sql = "SELECT * FROM " . TABLEPREFIX . "banners_lang
                                                    WHERE lang_id = '".$langRow["lang_id"]."'
                                                      AND banner_id = '".$banner_row["banner_id"]."'";
                                                $bl_rs = mysqli_query($conn, $bl_sql) ;
                                                $bl_rw = mysqli_fetch_array($bl_rs);
                                                $txtBannerNameOld = $bl_rw["vName"];
                                                
                                                $txtBannerNameEdit  =   $txtBannerNameOld;
                                            }
                                            else { 
                                                $txtBannerNameEdit  =   $txtBannerName[$i];
                                            }

                                            ?>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left"><?php echo ucwords($langRow["lang_name"])?> Banner Name <?php if($langRow["lang_name"]=="English"){?><span class="warning">*</span><?php }?></td>
                                                <td colspan="2" align="left">
                                                    <input type="text" <?php if($langRow["lang_name"]=="English"){?> id="txtBannerName1" <?php }?>  name="txtBannerName[]" class="textbox2" size="65" maxlength="200" value="<?php echo $txtBannerNameEdit;?>" autocomplete="off">
                                                    <input type="hidden" name="lang<?php echo $i; ?>" value="<?php echo $langRow["lang_id"]?>" >
                                                </td>
                                            </tr>
                                            <?php 
                                             $i++;
                                            }?>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left">Target Location (URL) <span class="warning">*</span></td>
                                                <td colspan="2" align="left"><input type="text" name="txtBannerLoc" id="txtBannerLoc" class="textbox2" size="65" maxlength="200" value="<?php echo $http.htmlentities($txtBannerLoc);?>" autocomplete="off"></td>
                                            </tr>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left">Banner Image <span class="warning">*</span></td>
                                                <td width="45%" align="left" colspan="2"><input type="file" name="txtPic" class="textbox2" onChange="change_sitemap();">
                                                    <br><font color="#FF0000"><div id="home1" style="<?php if($radLocation!='Home') {
                                                echo 'display:none;';
                                            }?>">Image size should be 728x90 or greater</div>
                                                        <div id="sub1" style="<?php if($radLocation!='Sub') {
                                                    echo 'display:none;';
                                                }if
                                                ($radLocation=='') {
                                                    echo 'display:inline';
                                                    }?>">Image size should be 728x90 or greater</div></font></td> </tr>
                                                                                               
                                             <?php if($assignedname!='') { 
                                                 echo "<tr><td colspan='3'>";
                                                                                                    
                                                echo ' <img src="../banners/'.$assignedname.'" name="img1"  width="468" height="60">';
                                                }//end if
                                                else {
                                                    echo '<img src="../images/default_album.gif" name="img1" width="69" height="51">';
                                                    echo "</td></tr>";
                                                }//end else?>                                          
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left"> Active </td>
                                                <td colspan="2" align="left"><input type="radio" name="radActive" value="1" <?php if($radActive=='1') {
                                                    echo 'checked';
                                                }if
                                                                                                    ($radActive=='') {
                                                                                                        echo 'checked';
                                                }?>>Yes <input type="radio" name="radActive" value="0" <?php if($radActive=='0') {
                                                    echo 'checked';
                                                }?>>No</td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td colspan="2">
                                                    <input type="submit" name="btnSubmit" value="<?php echo $btnVal;?> Banner" class="submit"/>
                                                    <input type="button" name="btnCancel" value="Cancel" class="submit" onclick="document.location='banners.php';"/>
                                                </td>
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
