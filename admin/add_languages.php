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
$PGTITLE = 'languages';

if ($_GET['mode'] == 'edit') {
    $sql = mysqli_query($conn, "select * from " . TABLEPREFIX . "lang where lang_id='" . $_REQUEST['id'] . "'") or die(mysqli_error($conn));
    if (mysqli_num_rows($sql) > 0) {
        $lang_name = mysqli_result($sql, 0, 'lang_name');
        $folder_name = mysqli_result($sql, 0, 'folder_name');
        $country_abbrev = mysqli_result($sql, 0, 'country_abbrev');
        $assignedname = $flag_file = mysqli_result($sql, 0, 'flag_file');
        $lang_status = mysqli_result($sql, 0, 'lang_status');
    }//end if
    $mode = 'edit';
    $btnVal = 'Edit';
}//end if
else {
    $btnVal = 'Add';
}//end else


if (isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] != '') {
    if (function_exists('get_magic_quotes_gpc')) {
        $langid = stripslashes($_POST['id']);
        $lang_name = stripslashes($_POST["lang_name"]);
        $folder_name = stripslashes($_POST["folder_name"]);
        $country_abbrev = stripslashes($_POST["country_abbrev"]);
        $flag_file = stripslashes($_FILES["flag_file"]['name']);
        $lang_status = stripslashes($_POST['lang_status']);
    }//end if
    else {
        $langid = ($_POST['id']);
        $lang_name = ($_POST["lang_name"]);
        $folder_name = ($_POST["folder_name"]);
        $country_abbrev = ($_POST["country_abbrev"]);
        $flag_file = ($_FILES["flag_file"]['name']);
        $lang_status = ($_POST['lang_status']);
    }//end else


    $error = false;
    if (trim($lang_name) == "") {
        $message .= "* Please enter language name<br>";
        $error = true;
    } //end if
    if (trim($folder_name) == "") {
        $message .= "* Please enter folder name<br>";
        $error = true;
    } //end if
    if (trim($flag_file) == "" && $langid=='') {
        $message .= "* Please select a flag file<br>";
        $error = true;
    } //end if
    
    if ($langid!=''){
        $edit_exist_cond = " and lang_id <> '".$langid."'";
        $btnVal = 'Edit';
    }
    else {
        $btnVal = 'Add';
    }
    
    $language_check=mysqli_query($conn, "select lang_name from ".TABLEPREFIX."lang where (lang_name='".addslashes(trim($lang_name))."' or folder_name='".addslashes(trim($folder_name))."') ".$edit_exist_cond) or die(mysqli_error($conn));
    if(mysqli_num_rows($language_check)>0)
    {
        $message .= "* Language name or Folder name already exists <br>";
        $error = true;
    }else{
        $lang_dir       = '../languages';
        $lang_files     = scandir($lang_dir);
        //echo "<pre>"; print_r($lang_files); echo "</pre>";
        
        if(is_array($lang_files) && count($lang_files) > 0){
            foreach ($lang_files as $result) {
                if ($result === '.' or $result === '..') 
                    continue;
                if (is_dir($lang_dir . '/' . $result)){
                    $arrExistFolders[] = $result;
                }
            }
        }
        //echo "<pre>"; print_r($arrExistFolders); echo "</pre>";
        
        if(is_array($arrExistFolders) && count($arrExistFolders)>0){            
            if(!in_array(trim($folder_name),$arrExistFolders)){
                $message .= "* Folder with name '".trim($folder_name)."' is not exists on 'languages' folder to add language!<br>";
                $error = true;
            }
        }
        //die();

        if ($_FILES['flag_file']['name'] != '') {
            if ($_FILES['flag_file']['size'] <= 0 or !isValidWebImageType($_FILES['flag_file']['type'], $_FILES['flag_file']['name'], $_FILES['flag_file']['tmp_name'])) {
                $message .= "* Please upload a valid flag image(jpg/gif/png)<br>";
                $error = true;
            } //end if

            if (!$error) {
                //list($image_width, $image_height,$image_type) = @getimagesize($_FILES['flag_file']['tmp_name']);
                $img_arr = explode('.',$_FILES['flag_file']['name']);
                $image_type = $img_arr[count($img_arr)-1];
                @chmod('../lang_flags', 0777);
                //remove the old image from the folder
                @unlink('../lang_flags/'.$_POST['assignedname']);
                $assignedname = time() . "_flag." . $image_type;
                @copy($_FILES['flag_file']['tmp_name'], "../lang_flags/" . $assignedname);
                @chmod("../lang_flags/$assignedname", 0777);
                resizeImg("../lang_flags/" . $assignedname, 30, 20, false, 100, 0, "");
                $imgFlag = true;
            }//end if
        }//end if	
    }
}//end if	

if (!$error){
    if (isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] == 'Add Language') {
        if ($imgFlag == true && $_POST['id'] == '') {
            mysqli_query($conn, "insert into " . TABLEPREFIX . "lang 
                                    (lang_name,folder_name,country_abbrev,flag_file,lang_status) 
                                  values 
                                    ('" . addslashes(trim($lang_name)) . "',
                                        '" . addslashes(trim($folder_name)) . "',
                                        '" . addslashes(trim($country_abbrev)) . "',
                                        '" . addslashes($assignedname) . "',
                                        '" . addslashes($lang_status) . "')") or die(mysqli_error($conn));

            header('location:languages.php?msg=a');
            exit();
        }//end if
    }//end if

    if (isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] == 'Edit Language') {
        if ($imgFlag == true){
            $img_field = "flag_file = '" . addslashes($assignedname) . "',";
        }
        if ($_POST['id'] != '') {
            mysqli_query($conn, "update " . TABLEPREFIX . "lang 
                                    set 
                                        lang_name='" . addslashes(trim($lang_name)) . "',
                                        folder_name='" . addslashes(trim($folder_name)) . "',
                                        country_abbrev='" . addslashes(trim($country_abbrev)) . "',    
                                        " . $img_field . "
                                        lang_status='" . addslashes($lang_status) . "' 
                                    where lang_id='" . $_POST['id'] . "'") or die(mysqli_error($conn));
            header('location:languages.php?msg=e');
            exit();
        }//end if
    }//end if
}

$message = ($message != '') ? $message : $_SESSION['sessionMsg'];
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
        width: 31px;
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
    function validate_frm(){
        var frm = document.frm_language;
        var format = /^[A-Za-z]+$/;
		
        if(trim(frm.lang_name.value) == ""){
            alert("Language Name cannot be empty.");
            frm.lang_name.focus();
            return false;
        }

        /*if(!frm.lang_name.value.match(format)){
            alert("Language Name accepts alphabets only.");
            frm.lang_name.focus();
            return false;
        }*/
        
        if(trim(frm.folder_name.value) == ""){
            alert("Folder Name cannot be empty.");
            frm.folder_name.focus();
            return false;
        }

<?php
if ($_REQUEST['id'] == '') {
    ?>
                    if(trim(frm.flag_file.value)==""){
                        alert("Flag Image cannot be empty.");
                        frm.flag_file.focus();
                        return false;
                    }
<?php }//end if ?>
                if(frm.flag_file.value!='')
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
                document.frm_language.img1.src=document.frm_language.flag_file.value;
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
                    <td width="100%" class="heading_admn boldtextblack" align="left"><?php echo $btnVal; ?> Language</td>
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
                    <td align="left" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0" class="admin_tble_2">
                            <tr>
                                <td bgcolor="#ffffff" class="noborderbottm">
                                    <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frm_language" method ="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>" onsubmit="return validate_frm();" enctype="multipart/form-data">
                                            <input name="id" type="hidden" id="id" value="<?php echo $_REQUEST['id']; ?>">
                                            <input type="hidden" name="assignedname" value="<?php echo $assignedname; ?>">
<?php if (isset($message) && $message != '') { ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td colspan="3" align="center" class="warning"><?php echo $message; ?></td>
                                                </tr>
<?php }//end if ?>							  
                                            <tr bgcolor="#FFFFFF">
                                            <td colspan="3" align="left" class="warning"> * indicates mandatory fields</td>
                                            </tr>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left">Language Name <span class="warning">*</span></td>
                                                <td colspan="2" align="left"><input type="text" name="lang_name" class="textbox2" size="65" maxlength="200" value="<?php echo  htmlentities($lang_name); ?>" autocomplete="off"></td>
                                            </tr>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left">Folder Name <span class="warning">*</span></td>
                                                <td colspan="2" align="left"><input type="text" name="folder_name" class="textbox2" size="65" maxlength="200" value="<?php echo  htmlentities($folder_name); ?>" autocomplete="off"></td>
                                            </tr>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left">Flag Image <?php if (trim($_REQUEST['id'])==''){ ?><span class="warning">*</span><?php } ?></td>
                                                <td width="45%" align="left">
                                                    <input type="file" name="flag_file" class="textbox2" onChange="change_sitemap();">
                                                </td>
                                                <td width="24%" align="left">
                                            <?php
                                            if ($assignedname != '') {
                                                echo ' <img src="../lang_flags/' . $assignedname . '" name="img1">';
                                            }
                                            ?>
                                                </td>
                                            </tr>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left">Country Abbreviations</td>
                                                <td colspan="2" align="left">
                                                    <input type="text" name="country_abbrev" class="textbox2" size="65" maxlength="200" value="<?php echo  htmlentities($country_abbrev); ?>" autocomplete="off">
                                                    <br />(separate multiple country abbreviations by comma)
                                                </td>
                                            </tr>
                                            <tr valign="top" bgcolor="#FFFFFF">
                                                <td align="left"> Active </td>
                                                <td colspan="2" align="left"><input type="radio" name="lang_status" value="y" <?php if ($lang_status == 'y') {
                                                echo 'checked';
                                            } ?>>Yes <input type="radio" name="lang_status" value="n" <?php if ($lang_status == 'n' || $lang_status == '') {
                                                echo 'checked';
                                            } ?>>No</td>
                                            </tr>
                                             <tr valign="top" bgcolor="#FFFFFF">
                                                 <td align="left" colspan="3">
                                                     <strong>Steps to add a new language:</strong><br>
                                                     <ol>
                                                         <li>Create a copy of 'en'(or any) folder inside the languages folder</li>
                                                         <li>Rename the copied folder to the new language code</li>
                                                         <li>Inside the new folder, translate the content of all the files to new language and save it</li>
                                                         <li>Then come to this page (Add Language)</li>
                                                         <li>Add Language name, Folder name, Upload flag, Country Abbreviations(Country codes where the language is spoken) and Status(active to yes or no)</li>
                                                         <li>Add the new language contents for all the section like CMS(faq,email etc),Help etc</li>
                                                     </ol>
                                                 </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left">&nbsp;</td>
                                                <td colspan="2"><input type="submit" name="btnSubmit" value="<?php echo $btnVal; ?> Language" class="submit"/></td>
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
<?php include_once('../includes/footer_admin.php'); ?>