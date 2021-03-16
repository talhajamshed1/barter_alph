<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                      |
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
$PGTITLE='categories';

if($_GET["catid"]!="")
{
   $catid =$_GET["catid"];
}//end if
elseif($_POST["catid"]!="")
{
  $catid =$_POST["catid"];
}//end else if
if($_GET["parentid"]!="")
{
  $parentid =$_GET["parentid"];
}//end else if
elseif($_POST["parentid"]!="")
{
  $parentid =$_POST["parentid"];
}//end else  if

$categorydesc = $_REQUEST['categorydesc'];
//category update
if($_POST["btnUpdateCategory"]=="Update Category")
{
    $message = '';
   $parentcatid = $parentid;
   $langid = $_POST["lang"];
 $assignedname1=$_POST['catimage'];

   //*******************Image Uploads Akhil***************************//

            if($_FILES['txtPic']['name']!='') {
        if ($_FILES['txtPic']['size'] <= 0 or !isValidWebImageType($_FILES['txtPic']['type'],$_FILES['txtPic']['name'],$_FILES['txtPic']['tmp_name'])) {
            $message .= "* Please upload a valid Category image(jpg/gif/png)<br>";
            $error = true;
            exit;
        } //end if

        list($newwidth,$newheight)=@getimagesize($_FILES['txtPic']['tmp_name']);

        $chkWidth='190';
        $chkHight='200';
        if($newwidth>=$chkWidth && $newheight>=$chkHight) {
            @chmod('../banners',0777);
           

            if (!$error) {

                if ($_FILES['txtPic']['size'] > 0) {
                        
                     //remove the old image from the banner folder
                    if(trim($_POST['catimage'])!='nocatimage.jpg'){
                        @unlink('../banners/'.$_POST['catimage']);
                    }

                    $imagewidth_height_type_array = explode(":", ImageType($_FILES['txtPic']['tmp_name']));
                    $imagetype = $imagewidth_height_type_array[0];
                    $imagetype_width=$imagewidth_height_type_array[1];
                    $imagetype_height=$imagewidth_height_type_array[2];
                    $assignedname1 = "banner" . time() . "." . $imagetype;
                    
                    //********************Upload image ***************//
                    @chmod("../banners/$assignedname1", 0777);
                    @copy($_FILES['txtPic']['tmp_name'], "../banners/" . $assignedname1);
                    //resizeImg("../banners/" . $assignedname1, $chkWidth ,$chkHight, false,100, 0,"");
                }//end if
                else {
                   $assignedname1=$_POST['catimage'];
                   // list($imagetype_width,$imagetype_height)=@getimagesize("../banners/".$assignedname);
                }//end else


            }//end if
            $imgFlag=true;
        }//end if
        else {
            $imgFlag=false;$error=true;
            $message.= "Category  Image size should be ".$chkWidth."x".$chkHight." or greater";
            $_SESSION['sessionMsg'] = "Category  Image size should be ".$chkWidth."x".$chkHight." or greater";
            //header('location:add_banners.php');
           // exit();
        }//end else
    }

if($assignedname1 ==''){
    $assignedname1='nocatimage.jpg';
}

            //********************Image Uploads Akhil ends***************************//
if($error==true){
}
else{

    $sqlupdatecat = "UPDATE ".TABLEPREFIX."category SET cat_image = '". addslashes($assignedname1) ."' WHERE  nCategoryId  = $catid";
      $resultupdatecat = mysqli_query($conn, $sqlupdatecat) or die (mysqli_error($conn));






foreach($langid as $val){
    $sel = "select * from ".TABLEPREFIX."category_lang WHERE cat_id ='".$catid."' and lang_id = '".$val."' ";
    $srs = mysqli_query($conn, $sel);

   if(mysqli_num_rows($srs)){

    $txtCategoryName = $_POST["txtCategoryName$val"];       
    
    $sqlcatdetails = "SELECT CA.nCategoryId,CL.cat_lang_id,CL.vCategoryDesc,CA.nParentId,CA.nCount FROM ".TABLEPREFIX."category CA
                       JOIN ".TABLEPREFIX."category_lang CL
                         ON CA.nCategoryId = CL.cat_id";
    $sqlcatdetails .= " AND CL.vCategoryDesc = '" . addslashes($txtCategoryName) . "'
                        AND CA.nCategoryId <> '$catid'
                        AND CA.nParentId = '$parentcatid'
                        AND CL.cat_lang_id = $val";

   $resultcatdetails = mysqli_query($conn, $sqlcatdetails) or die(mysqli_error($conn));
   if(mysqli_num_rows($resultcatdetails)!=0 && $txtCategoryName!='')
   {
       $message .= "The category '". stripslashes(htmlentities($txtCategoryName)) ."' already exists! Please enter a different name!<br>";
   }//end if
   else
   {


      $sqlupdatecat = "UPDATE ".TABLEPREFIX."category_lang SET vCategoryDesc = '". addslashes($txtCategoryName) ."' WHERE lang_id = '".$val."' AND cat_id = $catid";
      $resultupdatecat = mysqli_query($conn, $sqlupdatecat) or die (mysqli_error($conn));
      $_SESSION['sessionMsg']='Category updated successfully !!!';

   }//end else
        
  }else{
      $txtCategoryName = $_POST["txtCategoryName$val"];     
      /*
      * insert into sub category table
      */
     $sqlinsertcatdet    = "INSERT INTO ".TABLEPREFIX."category_lang(cat_id,lang_id,vCategoryDesc) VALUES ";
     $sqlinsertcatdet   .= " ('". $catid ."',$val,'". addslashes(trim($txtCategoryName)) ."') ";
     $resultinsertcatdet = mysqli_query($conn, $sqlinsertcatdet) or die(mysqli_error($conn));
  }
 }
      if(!$message){
   
      header("location:categories.php");
      exit();
      }
}//end if
}

//for deletion
else if($_POST["btnDeleteCategory"]=="Delete Category")
{
      $txtCategoryName = $_POST["txtCategoryName"];

      $sqlcatdetails = "SELECT  * FROM ".TABLEPREFIX."category  WHERE nParentId='".$catid."'";

        $resultcatdetails = mysqli_query($conn, $sqlcatdetails) or die(mysqli_error($conn));
		
        if(mysqli_num_rows($resultcatdetails)>0)
		{
           $message = "The category '".htmlentities($txtCategoryName)."' has categories under it! Please delete the subcategories first!";
        }//end if
		else
		{
                 $itemspresent = "0";
                 $sql = "SELECT  nCategoryId  FROM ".TABLEPREFIX."sale  WHERE nCategoryId ='".$catid."' and vDelStatus!='1'";
                 $resultcat = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                 if(mysqli_num_rows($resultcat)!=0)
				 {
                     $itemspresent = "1";
                     $message = "There are some items for sale under this category! Please delete the sale items before deleting the category!";
                 }//end if
                 else
                 {
                    $sql = "SELECT  nCategoryId  FROM ".TABLEPREFIX."swap  WHERE nCategoryId='".$catid."' and vDelStatus!='1'";
                    $resultcat = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if(mysqli_num_rows($resultcat)!=0)
					{
                        $itemspresent = "1";
                        $message = "There are some items for swap under this category! Please delete the swap items before deleting the category!";
						//header("location:editcategory.php?catid=".$catid."&parentid=".$parentid);
                     }//end if
                }//end else
                 if($itemspresent=="0")
				 {
                    $sqldeletecat = "DELETE FROM ".TABLEPREFIX."category WHERE nCategoryId='".$catid."'";
                    $resultdeletecat  = mysqli_query($conn, $sqldeletecat ) or die(mysqli_error($conn));

                    $sqldeletecat_1 = "DELETE FROM ".TABLEPREFIX."category_lang WHERE cat_id='".$catid."'";
                    $resultdeletecat_1  = mysqli_query($conn, $sqldeletecat_1) or die(mysqli_error($conn));
                    
                    $_SESSION['sessionMsg']='Category deleted successfully !!!';
                    
                    header("location:categories.php");
		    exit();
                }//end if
        }//end else
}//end else if

$sql =  "SELECT CA.nCategoryId,CL.cat_lang_id,CL.vCategoryDesc,CA.nParentId,CA.nCount,CA.cat_image FROM ".TABLEPREFIX."category CA
                                                  JOIN ".TABLEPREFIX."category_lang CL
                                                    ON CA.nCategoryId = CL.cat_id
                                                    AND CL.lang_id = '".$_SESSION["lang_id"]."'";
$sql .= " AND CA.nCategoryId = '" . $catid . "'";
if($categorydesc)
$sql .=   " AND CL.vCategoryDesc = '".$categorydesc."'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result)!= 0)
{
   $row = mysqli_fetch_array($result) ;
   $categoryname = $row["vCategoryDesc"];
   $currcatid = $row["nCategoryId"];
   $parentid = $row["nParentId"];
   $langid = $row["cat_lang_id"];
    $assignedname1 = $row["cat_image"] ==''?'nocatimage.jpg':$row["cat_image"];
}//end if

?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/jquery.min.1.11.1.js"></script>
<script LANGUAGE="javascript">
    var $jqr=jQuery.noConflict();
    $jqr(document).ready(function() {
   $jqr('#btnSubmit').click(function(e) {
        var isValid = true;
        $jqr('input.jqCategory').each(function () {
            if ($jqr.trim($jqr(this).val()) == '') {
                isValid = false;
                $jqr(this).css({
                    "border": "1px solid red",
                    "background": "#FFCECE"
                });
            }
            else {
                $jqr(this).css({
                    "border": "",
                    "background": ""
                });
            }
        });
        if (isValid == false) {
             alert("Category Name cannot be empty.");
            e.preventDefault();
        }
        
    });
});

function validateForm()
{
    $jqr('input.jqCategory').each(function () {
            if($jqr(this).val()=="")
            {
                 alert("Category Name cannot be empty.");
                  return false;
            }           /* $(this).rules("add", {
                required: true
            })*/
        return false;
});
    
             
        /*if(trim(frm.txtCategoryName.value)==""){
                alert("Category Name cannot be empty.");
                frm.txtCategoryName.focus();
                return false;
        }else{
                return true;
        }*/
}
function confirmDelete(){
        var frm = window.document.frmCategories;
        if(confirm("Are you sure you want to delete this category?")){
                return true;
        }else{
                return false;
        }
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
                        <td width="100%" class="heading_admn" align="left"><span class="boldtextblack">Edit / Delete Category ' <?php echo htmlentities($categoryname);?> '</span></td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                      <tr>
                        <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
<form name="frmCategories" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>"  enctype="multipart/form-data">
<?php if(isset($message) && $message!='')
        {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><b><?php echo getCategoryLink($PHP_SELF,$catid); ?></b></td>
                      </tr>
                              
                            <?php
                                     if(mysqli_num_rows($result)=='0')
                                     {
                                         echo "<tr bgcolor='#FFFFFF'><td colspan='2'>No records to display!</td></tr>";
                                     }//end else
                             ?>
							<input type="hidden" value="<?php echo $catid?>" name="catid" >
                                                        <input type="hidden" value="<?php echo $parentid?>" name="parentid" >
                              
                                <?php



                                $langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                                                    WHERE lang_status = 'y'";
                               
                                 $langRs      = mysqli_query($conn, $langSql);
                                   $i=0;
                                   while($langRow = mysqli_fetch_array($langRs))
                                   {

                                       $sql_1 =  "SELECT CA.nCategoryId,CL.cat_lang_id,CL.vCategoryDesc,CA.nParentId,CA.nCount FROM ".TABLEPREFIX."category CA
                                                  JOIN ".TABLEPREFIX."category_lang CL
                                                    ON CA.nCategoryId = CL.cat_id";
                                       $sql_1 .= " AND CA.nCategoryId = '" . $catid . "'
                                                   AND CL.lang_id = '".$langRow["lang_id"]."'";
                                    
                                        $result_1 = mysqli_query($conn, $sql_1) or die(mysqli_error($conn));
                                        
                                        if(mysqli_num_rows($result_1)!= 0)
                                        {
                                           $row_1 = mysqli_fetch_array($result_1) ;
                                        }
                              ?>
                                <tr bgcolor="#FFFFFF">
                                <td width="20%" align="left"><?php echo ucwords($langRow["lang_name"])?> Category Name</td>
                                <td width="80%">
                                    <input type = "text" value="<?php echo stripslashes(htmlentities($row_1["vCategoryDesc"]))?>" class="jqCategory textbox" name="txtCategoryName<?php echo $langRow["lang_id"]?>" size="40" maxlength="100">
                                    <input type="hidden" name="lang[]" value="<?php echo $langRow["lang_id"]?>" >
                                </td>
                                </tr>
                                 <?php
//                                        }
                                        $row_1 = array();
                                    $i++;
                                 }?>
                                <tr bgcolor="#FFFFFF">
                                <td align="left">Category  Image

                                </td>
                                <td><input type="file"  name="txtPic" >
                                   
                                    <input type="hidden" name="catimage" value="<?php echo $assignedname1?>">
                                    <div  style="color: #FF0000">Image size should be 190 x 200 or greater</div>
                                    <div style="height: 75; width: 75"><img src="../banners/<?php echo $assignedname1?>" height="75" width="75" alt=""></div>
                                </td>
                      </tr>
                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;
                                    
                                </td>
                                <td><input type="submit" class="JqSubmit submit" name="btnUpdateCategory" value="Update Category" id="btnSubmit" >
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit"  class="submit"  name="btnDeleteCategory" value="Delete Category" onClick="return confirmDelete();">
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="button"  class="submit_grey"  name="btnCancel" value="Cancel" onClick="window.location.href='<?php echo "categories.php?catid=".$parentid ?>'"></td>
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