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
$PGTITLE='categories';

if($_GET["catid"]!="")
{
    $catid =$_GET["catid"];
}//end if
else if($_POST["catid"]!="")
{
   $catid =$_POST["catid"];
}//end else if

$assignedname='';

//print_r($_POST);

if($_POST["btnAddNew"]=="Add New Category")
{
 
function categoryUpdate($txtCategoryName,$parentcatid,$langid,$sqlcatid)
{

     /*
      * insert into sub category table
      */
     global $conn;
     $sqlinsertcatdet    = "INSERT INTO ".TABLEPREFIX."category_lang(cat_id,lang_id,vCategoryDesc) VALUES ";
     $sqlinsertcatdet   .= " ('". $sqlcatid ."',$langid,'". addslashes(trim($txtCategoryName)) ."') ";
     $resultinsertcatdet = mysqli_query($conn, $sqlinsertcatdet) or die(mysqli_error($conn));

}

   $txtCategoryNamePost = $_POST["txtCategoryName"];

    if(!isset($catid) || $catid=="")
    {//at the top level
         $parentcatid = "0";
    }//end if
    else
    {
         $parentcatid = $catid;
    }//end else
   $message = '';
    if($txtCategoryNamePost[0]=='')
    {
    $message .= "First category cannot be blank";
    }
    
    if(is_array($txtCategoryNamePost))
    {
        $k = 0;$t=0;

        foreach($txtCategoryNamePost as $txtCategoryName){            
        $langid = $_POST["lang$t"];        
        if(trim($txtCategoryName)){
        $sqlcatdetails = "SELECT CL.vCategoryDesc FROM ".TABLEPREFIX."category CA
                        JOIN ".TABLEPREFIX."category_lang CL
                          ON CA.nCategoryId = CL.cat_id
                         AND CA.nParentId ='".addslashes($parentcatid)."'";
        $sqlcatdetails .= "  AND CL.vCategoryDesc = '" . addslashes(trim($txtCategoryName)) . "' AND CL.lang_id = '".$langid."'";

        $resultcatdetails = mysqli_query($conn, $sqlcatdetails) or die(mysqli_error($conn));
 
       if(mysqli_num_rows($resultcatdetails)!=0)
       {
           $message .= "The category '". htmlentities($txtCategoryName) ."' already exists! Please enter a different name!<br>";
       }//end if
        }
       $t++;
      
     }
     
    
       if($message=='')
       {
		if($parentcatid!='0')
		{
			$sql1 = "SELECT  count(*) as cnt FROM ".TABLEPREFIX."sale  WHERE  nCategoryId = '" . addslashes($parentcatid) . "' ";
        	$result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
			$row1= mysqli_fetch_array($result1);
			$salecount = $row1["cnt"];
			$sql1 = "SELECT  count(*) as cnt FROM ".TABLEPREFIX."swap  WHERE  nCategoryId = '" . addslashes($parentcatid) . "' ";
        	$result1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
			$row1= mysqli_fetch_array($result1);
			$swapcount = $row1["cnt"];
		}//end if
		$count = $salecount + $swapcount;
        if( $count==0 )
        {

            //*******************Image Uploads Akhil***************************//
           
            if($_FILES['txtPic']['name']!='') {
        if ($_FILES['txtPic']['size'] <= 0 or !isValidWebImageType($_FILES['txtPic']['type'],$_FILES['txtPic']['name'],$_FILES['txtPic']['tmp_name'])) {
            $message .= "* Please upload a valid category image(jpg/gif/png)<br>";
            $error = true;
            exit;
        } //end if

        list($newwidth,$newheight)=@getimagesize($_FILES['txtPic']['tmp_name']);

        $chkWidth='190';
        $chkHight='200';
       

        if($newwidth>=$chkWidth && $newheight>=$chkHight) {
            @chmod('../banners',0777);
            //remove the old image from the banner folder
           // @unlink('../banners/'.$_POST['assignedname']);
           
            if (!$error) {
               
                if ($_FILES['txtPic']['size'] > 0) {
                   
                    $imagewidth_height_type_array = explode(":", ImageType($_FILES['txtPic']['tmp_name']));
                    $imagetype = $imagewidth_height_type_array[0];
                    $imagetype_width=$imagewidth_height_type_array[1];
                    $imagetype_height=$imagewidth_height_type_array[2];
                    $assignedname = "banner" . time() . "." . $imagetype;
                }//end if
                else {
                   // $assignedname=$_POST['assignedname'];
                   // list($imagetype_width,$imagetype_height)=@getimagesize("../banners/".$assignedname);
                }//end else

                @chmod("../banners/$assignedname", 0777);
                @copy($_FILES['txtPic']['tmp_name'], "../banners/" . $assignedname);
                //resizeImg("../banners/" . $assignedname, $chkWidth ,$chkHight, false,100, 0,"");
            }//end if
            $imgFlag=true;
        }//end if
        else {
            $imgFlag=false;$error=true;
            $_SESSION['sessionMsg'] = "Category Image size should be ".$chkWidth."x".$chkHight." or greater";
            //header('location:add_banners.php');
            //exit();
        }//end else
    }

if($assignedname ==''){
    $assignedname='nocatimage.jpg';
}

            //********************Image Uploads Akhil ends***************************//


                    if($parentcatid=="0")
                    {
                            ////top level category
				 $sql = mysqli_query($conn, "SELECT MAX(nPosition) as max from ".TABLEPREFIX."category") or die(mysqli_error($conn));
				 $rw = mysqli_fetch_array($sql);
	 			 $maxorder = $rw['max']+1;

                 /*
                  * Insert into parent category table
                  */
                 $sqlinsertcat      = "INSERT INTO ".TABLEPREFIX."category(nParentId,vRoute,nPosition,cat_image) VALUES ";
                 $sqlinsertcat     .= " ('". $parentcatid ."',null,'".$maxorder."','".$assignedname."') ";
                 $resultinsertcat   = mysqli_query($conn, $sqlinsertcat) or die(mysqli_error($conn));
                 $sqlcatid          = mysqli_insert_id($conn);
                 
                 $sqlupdatecat  = "UPDATE ".TABLEPREFIX."category SET vRoute= '".$sqlcatid."' WHERE nCategoryId = '".$sqlcatid."'";
                 mysqli_query($conn, $sqlupdatecat) or die(mysqli_error($conn));
		 $message       = 'Records added successfully !!!';

            }//end if
            else
            {//not a top category, ie, there are categories under it

                $sqlcatdetails = "SELECT  vRoute,nCount,nParentId  FROM ".TABLEPREFIX."category WHERE nCategoryId = '$parentcatid'  ";
                $resultcatdetails = mysqli_query($conn, $sqlcatdetails) or die(mysqli_error($conn));
                $rowcatdetails = mysqli_fetch_array($resultcatdetails);
                $route = $rowcatdetails["vRoute"] ;
                if($route=="")
		{
                    $route = $parentcatid;
                }//end if

                $sql = mysqli_query($conn, "SELECT MAX(nPosition) as max from ".TABLEPREFIX."category") or die(mysqli_error($conn));
                $rw = mysqli_fetch_array($sql);
                $maxorder = $rw['max']+1;

                $sqlinsertcat = "INSERT INTO ".TABLEPREFIX."category(nParentId,vRoute,nPosition,cat_image) VALUES ";
                $sqlinsertcat .= " ('".$parentcatid."','".$route."','".$maxorder."','".$assignedname."') ";
                $resultinsertcat = mysqli_query($conn, $sqlinsertcat) or die(mysqli_error($conn));
                $sqlcatid = mysqli_insert_id($conn);

                if($route!="")
		{
                    $route .= ",".$sqlcatid;
                    $sqlupdatecat = "UPDATE ".TABLEPREFIX."category SET vRoute= '".$route."' WHERE nCategoryId = '".$sqlcatid."'";
                    $resultupdatecat = mysqli_query($conn, $sqlupdatecat) or die(mysqli_error($conn));
                }//end if
             }//end else
             $assignedname = '';
        }//end if
        else
        {
                $message = "Since there are some products under this category, child categories cannot be added here!";
        }//end else
                
        foreach ($txtCategoryNamePost as $cat){
          $txtCategoryName = $cat;
          $langid = $_POST["lang$k"];
          $k++;
          categoryUpdate($txtCategoryName,$parentcatid,$langid,$sqlcatid);
        }
        
    }//end if



    }
}//end if
$message=($message!='')?$message:$_SESSION['sessionMsg'];
unset($_SESSION['sessionMsg']);


//checking query string
if($_GET['catid']!='')
{
	$queryString='?catid='.$_GET['catid'].'&categorydesc='.$_GET['categorydesc'];
	$queryStringOrder='&catid='.$_GET['catid'].'&categorydesc='.$_GET['categorydesc'];
}//end if
else
{
	$queryString='';
	$queryStringOrder='';
}//end else

//ordering content
if(isset($_GET['Action']) && $_GET['Action']=='ordering')
{

	$oldId=$_GET['id'];
	$oldPosition=$_GET['pos'].' and nParentId='.$_GET['npantid'];
	$table=TABLEPREFIX."category";
	$PositionfieldName='nPosition'; //db field name
	$IdfieldName='nCategoryId'; //db field name
	$returnPath='location:categories.php'.$queryString;
	/*
	if(isset($_GET['move']) && $_GET['move']=='up')
	{
		OrderUp($table,$oldId,$oldPosition,$PositionfieldName,$IdfieldName,$returnPath);
		listing();
	}//end if

	if(isset($_GET['move']) && $_GET['move']=='down')
	{
		OrderDown($table,$oldId,$oldPosition,$PositionfieldName,$IdfieldName,$returnPath);
		listing();
	}//end if
        */
        if(isset($_GET['move'])) {
            sortableDb("category", $oldId, $PositionfieldName, $IdfieldName, $_GET['move']);
        }
}//end if

//for deletion
 if(isset($_GET['mode']) && $_GET['mode']=='delete')
{
      $txtCategoryName = $_GET["catName"];
      $catid=$_GET['catid'];

      $sqlcatdetails = "SELECT  nCategoryId FROM ".TABLEPREFIX."category  WHERE nParentId='".$catid."'";

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
                     }//end if
                }//end else
                 if($itemspresent=="0")
		 {

                     $sqlcatdetails = "SELECT cat_image FROM ".TABLEPREFIX."category  WHERE nCategoryId ='".$catid."'";

                      $resultcatdetails = mysqli_query($conn, $sqlcatdetails) or die(mysqli_error($conn));

                       if(mysqli_num_rows($resultcatdetails)>0)
                       {
                           $row= mysqli_fetch_array($resultcatdetails);
                                if(trim($row['cat_image'])!='nocatimage.jpg'){
                                @unlink('../banners/'.$row['cat_image']);
                            }
                       }

                    $sqldeletecat = "DELETE FROM ".TABLEPREFIX."category WHERE nCategoryId='".$catid."'";
                    $resultdeletecat  = mysqli_query($conn, $sqldeletecat ) or die(mysqli_error($conn));

                    $sqldeletechildcat = "DELETE FROM ".TABLEPREFIX."category_lang WHERE cat_id='".$catid."'";
                    $resultdeletechildcat  = mysqli_query($conn, $sqldeletechildcat ) or die(mysqli_error($conn));

                    $_SESSION['sessionMsg']='Category deleted successfully !!!';          
                    header("location:categories.php");
                    exit();
                }//end if
        }//end else
}//end else if
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/jquery.min.1.11.1.js"></script>
<script LANGUAGE="javascript"> 
 var $jqr=jQuery.noConflict();
    $jqr(document).ready(function() {
   $jqr('#btnAddNew').click(function(e) {
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
                        <td width="100%" class="heading_admn" align="left"><span class="boldtextblack">Categories</span></td>
                      </tr>
                    </table>
                    <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                      <tr>
                        <td align="left" valign="top" class="noborderbottm"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                    <form name="frmCategories" method="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
<?php if(isset($message) && $message!='')
					      {
					?>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="center" class="warning"><?php echo $message;?></td>
                              </tr>
<?php  }//end if?>							  
                              <!--<tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left"><b><?php echo getCategoryLink($PHP_SELF,$catid); ?></b></td>
                      </tr>-->
                              
                            <?php
                                  $sql = "SELECT CL.vCategoryDesc,CA.nCategoryId,CA.nParentId,CA.nPosition,CA.cat_image FROM ".TABLEPREFIX."category CA
                                             INNER JOIN ".TABLEPREFIX."category_lang CL
                                                     ON CA.nCategoryId = CL.cat_id
                                                    AND CL.lang_id = '".$_SESSION["lang_id"]."'";
                                  
                                  $sql .= " AND CA.nParentId  = '" . addslashes($catid) . "' ORDER BY CA.nPosition,CA.nParentId ASC";
                                  
                                  $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                  if(mysqli_num_rows($result)!= 0){
                                        switch($_GET['begin'])
                                        {
                                                case "":
                                                        $cnt=1;
                                                break;

                                                default:
                                                        $cnt=$_GET['begin']+1;
                                                break;
                                        }//end switch
							
					$count=mysqli_num_rows($result);
                                        while($row = mysqli_fetch_array($result))
					{
                                         ?>
                                              <tr bgcolor='#FFFFFF'><td colspan='2'>
                                              <table width='100%' border='0' cellspacing='0' cellpadding='0' class='maintext'>
                                                  <tr bgcolor='#FFFFFF' class="cat_listingtbl">
                                                      <td width='4%' align='center' valign='middle'>&nbsp;
                                                          
                                                      </td>
                                                     <td width="7%"  align='center'>
                                                         <!-- Category image -->
                                                         <?php
                                                        $assignedname1 = $row["cat_image"] ==''?'nocatimage.jpg':$row["cat_image"];
                                                       
                                                        ?>
                                                         <img src="../banners/<?php echo $assignedname1?>" height="40" width="40" alt="">
                                                         
                                                         <!-- Category image ends-->
                                                     </td>
                                                     
                                                     <td width="69%" align="left" valign="middle">
                                                     	 


                                                         <a href="<?php echo $PHP_SELF."?catid=" . $row["nCategoryId"] . "&categorydesc=".urlencode($row["vCategoryDesc"])?>" class="tooltip">
                                                         <span><?php echo SUB_CATEGORY_TEXT ?></span>
														 <?php echo htmlentities($row["vCategoryDesc"])?>
                                                         </a>
                                                     
                                                     
                                                     </td>
                                                     <td width="6%" align="center">
                                                        
                                                         <a href='editcategory.php?catid=<?php echo $row["nCategoryId"]?>&parentid=<?php echo $row["nParentId"]?>'><span class="glyphicon glyphicon-edit"></span></a>
                                                     
                                                     </td>
                                                     <td width="6%" align="center" valign="middle">
                                                         <a href="categories.php?catid=<?php echo $row["nCategoryId"]?>parentid=<?php echo $row["nParentId"]?>&catName=<?php echo  htmlentities($row["vCategoryDesc"])?>&mode=delete" onClick="return confirm('Are you want to delete?')">
                                                         <span class="glyphicon glyphicon-trash"></span>
                                                         </a>
                                                            
                                                     
                                                     </td>
                                                      <td width="8%" align="center" valign="middle">
                                       <?php if($cnt!=1)
                                         {
                                        ?>
					 <a href='categories.php?Action=ordering&move=up&id=<?php echo $row["nCategoryId"];?>&pos=<?php echo $row["nPosition"];?><?php echo $queryStringOrder;?>&npantid=<?php echo $row['nParentId'];?>'>
							
							<img src="../images/up.gif" alt="Up" border=0></a><?php            }//end if?>
					<?php 					 if($cnt!=$count)
											 {?>
							<a href="categories.php?Action=ordering&move=down&id=<?php echo $row["nCategoryId"];?>&pos=<?php echo $row["nPosition"];?><?php echo $queryStringOrder;?>&npantid=<?php echo $row['nParentId'];?>">
									   
							<img src="../images/down.gif" alt="Down" border=0>
                                                        </a><?php }//end if ?>
                                                      
                                                      </td>
                                                     
                                                     
                                                     </tr></table>
                                                   </td></tr>
					<?php
					     
					 $cnt++;
                                        }//end while
                                   }//end if
								   else
								   {
                                       echo "<tr bgcolor='#FFFFFF'><td colspan='6'>No records to display!</td></tr>";
                                   }//end else

                                   $langSql     = "SELECT lang_id,lang_name,folder_name FROM " . TABLEPREFIX . "lang
                                                    WHERE lang_status = 'y'";
                                   $langRs      = mysqli_query($conn, $langSql);
                                   $i=0;
                                   while($langRow = mysqli_fetch_array($langRs))
                                   {
                              ?>
                                      <tr bgcolor="#FFFFFF">
                                        <td width="33%" align="left"><?php echo ucwords($langRow["lang_name"])?> Category Name</td>
                                        <td width="67%">
                                            <input type="text" value="" class="jqCategory textbox" name="txtCategoryName[]" size="30" maxlength="100">
                                            <input type="hidden" name="lang<?php echo $i; ?>" value="<?php echo $langRow["lang_id"]?>" >
                                        </td>
                                      </tr>
                             <?php $i++;}?>
                                  <tr bgcolor="#FFFFFF">
                                <td align="left">Category Image
                                    
                                </td>
                                <td><input type="file"  name="txtPic" >
                                    <div  style="color: #FF0000">Image size should be 190 x 200 or greater</div>
                                    <div style="height: 75; width: 75"><?php if($assignedname!='') { ?><img src="../banners/<?php echo $assignedname?>" height="75" width="75" alt=""><?php } ?></div>
                                </td>
                      </tr>

                              <tr bgcolor="#FFFFFF">
                                <td align="left">&nbsp;</td>
                                <td><input type="submit" value="Add New Category" id="btnAddNew" name="btnAddNew" size="20" maxlength="100" class="submit">
                                                        <input type="hidden" value="<?php echo $catid?>" name="catid"></td>
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