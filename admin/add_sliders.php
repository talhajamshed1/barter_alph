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
$PGTITLE='sliders';

function addhttp($url) {
	if (!preg_match("~^(?:ht)tps?://~i", $url)) {
		$url = "http://" . $url;
	}
	return $url;
}

if($_GET['mode']=='edit') {
    $sql=mysqli_query($conn, "SELECT *
                            FROM ".TABLEPREFIX."sliders 
                           WHERE nSId='".$_GET['id']."'") or die(mysqli_error($conn));
    if(mysqli_num_rows($sql)>0) {
        $slider_row = mysqli_fetch_array($sql);

        $txtSliderName=mysqli_result($sql,0,'vName');
        $txtSliderLoc=mysqli_result($sql,0,'vlocUrl');
        $radActive=mysqli_result($sql,0,'vActive');
        $assignedname=mysqli_result($sql,0,'vImg');
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
        $nSId = addslashes($_POST['nSId'] );
        $radActive=addslashes($_POST['radActive']);
        $txtSliderLoc=addslashes($_POST['txtSliderLoc']);
        $txtSliderName = $_POST["txtSliderName"];
    }//end if
    else {
        $nSId = $_POST['nSId'] ;
        $txtSliderName = $_POST["txtSliderName"];
        $txtSliderLoc=addslashes($_POST['txtSliderLoc']);
        $radActive=$_POST['radActive'];
    }//end else

    $http='';
    //validation
$_SESSION['sessionMsg'] ='';
    if($_FILES['txtPic']['name']!='') {
        if ($_FILES['txtPic']['size'] <= 0 or !isValidWebImageType($_FILES['txtPic']['type'],$_FILES['txtPic']['name'],$_FILES['txtPic']['tmp_name'])) {
            $message .= "* Please upload a valid slider image(jpg/gif/png)<br>";
            $error = true;
        } //end if

        list($newwidth,$newheight)=@getimagesize($_FILES['txtPic']['tmp_name']);

        $chkWidth='1366'; //468
        $chkHight='421'; //60

        if($newwidth>=$chkWidth && $newheight>=$chkHight) {
            @chmod('../sliders',0777);
            //remove the old image from the slider folder
            @unlink('../sliders/'.$_POST['assignedname']);

            if (!$error) {
                if ($_FILES['txtPic']['size'] > 0) {
                    $imagewidth_height_type_array = explode(":", ImageType($_FILES['txtPic']['tmp_name']));
                    $imagetype = $imagewidth_height_type_array[0];
                    $imagetype_width=$imagewidth_height_type_array[1];
                    $imagetype_height=$imagewidth_height_type_array[2];
                    $assignedname = "slider" . time() . "." . $imagetype;
                }//end if
                else {
                    $assignedname=$_POST['assignedname'];
                    list($imagetype_width,$imagetype_height)=@getimagesize("../sliders/".$assignedname);
                }//end else

                @chmod("../sliders/$assignedname", 0777);
                @copy($_FILES['txtPic']['tmp_name'], "../sliders/" . $assignedname);
                resizeImg("../sliders/" . $assignedname, $chkWidth ,$chkHight, false,100, 0,"");
            }//end if
            $imgFlag=true;
        }//end if
        else {
            $imgFlag=false;$error=true;
            $_SESSION['sessionMsg'] = "Slider size should be ".$chkWidth."x".$chkHight." or greater";
            //header('location:add_sliders.php');
            //exit();
        }//end else
    }

        if ($txtSliderName == "") {
        $message .= "* First slider name is mandatory<br>";
        $error = true;
    } //end if
    

}//end if

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Add Sliders' && !$error) {
    $imgFlag=true;
    
    $slider_url = addhttp($_POST['txtSliderLoc']);
    if($imgFlag==true && $_POST['id']=='') {
        
        mysqli_query($conn, "insert into ".TABLEPREFIX."sliders (vName,vImg,nDate,vlocUrl,vActive) values
							('".addslashes(trim($txtSliderName))."','".addslashes($assignedname)."',
							now(),'".addslashes(trim($slider_url))."','".addslashes($radActive)."')") or die(mysqli_error($conn));

        header('location:sliders.php?msg=a');
        exit();
    }//end if
}//end if

if(isset($_POST["btnSubmit"]) && $_POST["btnSubmit"]=='Edit Sliders' && !$error) {  
    if($_POST['id']!='') {
    	
    	$slider_url = addhttp($_POST['txtSliderLoc']);
    	
        if($imgFlag==true){ 
        	
       $update_query =	"update ".TABLEPREFIX."sliders set vName='".addslashes($txtSliderName)."', vImg='".addslashes($assignedname)."',vlocUrl='".$slider_url."', vActive='".addslashes($radActive)."' where nSId='".$_POST['id']."'";
        mysqli_query($conn, $update_query) or die(mysqli_error($conn));
        }
        else
           { 
      		$update_query = "update ".TABLEPREFIX."sliders set vName='".addslashes($txtSliderName)."',vlocUrl='".$slider_url."',vActive='".addslashes($radActive)."' where nSId='".$_POST['id']."'";    	
        	mysqli_query($conn, $update_query) or die(mysqli_error($conn));
        }
      }//end if
       
        header('location:sliders.php?msg=e');
        exit();
    
}//end if

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

        border: 1px solid gray;
        padding: 10px;
        background: rgb(230, 230, 230) none repeat scroll 0%;
        -moz-background-clip: -moz-initial;
        -moz-background-origin: -moz-initial;
        -moz-background-inline-policy: -moz-initial;
        color: gray;
    }
</style>
<script language="javascript" type="text/javascript">
    function validateSliders(){
        var frm = document.frmSliders;
        if(document.getElementById("txtSliderName")){
            var bval = document.getElementById("txtSliderName").value;
            if(trim(bval) == ""){
                alert("Slider Name cannot be empty.");
                document.getElementById("txtSliderName").focus();
                return false;
            }
        }
        if(document.getElementById("txtSliderLoc").value=="http://"){
            alert("Slider Location cannot be empty.");
            document.getElementById("txtSliderLoc").focus();
            return false;
        }
            <?php
            if($_GET['id']=='') {
                ?>
                   else if(trim(frm.txtPic.value)==""){
                            alert("Slider Image cannot be empty.");
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
                        document.frmSliders.img1.src=document.frmSliders.txtPic.value;
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
                    <td width="100%" class="heading_admn boldtextblack" align="left"><?php echo $btnVal;?> Sliders</td>
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
            <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                <tr>
                    <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frmSliders" method ="POST" action="<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validateSliders();" enctype="multipart/form-data">
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
                                            
                                            
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left">Slider Name <span class="warning">*</span></td>
                                                <td colspan="2" align="left">
                                                    <input type="text" id="txtSliderName" name="txtSliderName" class="textbox2" size="65" maxlength="200" value="<?php echo $txtSliderName;?>" >
                                                </td>
                                            </tr>
                                            
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left">Target Location (URL)</td>
                                                <td colspan="2" align="left"><input type="text" name="txtSliderLoc" id="txtSliderLoc" class="textbox2" size="65" maxlength="200" value="<?php echo $http.htmlentities($txtSliderLoc);?>"></td>
                                            </tr>
                                            
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left">Slider Image <span class="warning">*</span></td>
                                                <td width="45%" align="left" colspan="2"><input type="file" name="txtPic" class="textbox2">
                                                    <br><font color="#FF0000"><div id="home1" style="<?php if($radLocation!='Home') {
                                                echo 'display:none;';
                                            }?>">Image size should be 1366x421 or greater</div>
                                                        <div id="sub1" style="<?php if($radLocation!='Sub') {
                                                    echo 'display:none;';
                                                }if
                                                ($radLocation=='') {
                                                    echo 'display:inline';
                                                    }?>">Image size should be 1366x421 or greater</div></font></td> </tr>
                                                                                               
                                             <?php if($assignedname!='') { 
                                                 echo "<tr><td colspan='3'>";
                                                                                                    
                                                echo ' <img src="../sliders/'.$assignedname.'" name="img1"  width="468" height="60">';
                                                }//end if
                                                ?>                                          
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
                                                <td colspan="2"><input type="submit" name="btnSubmit" value="<?php echo $btnVal;?> Sliders" class="submit"/></td>
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
