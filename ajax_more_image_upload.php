<?php
include ("includes/config.php");
include ("includes/session.php");
include ("includes/functions.php");
include('includes/class.upload.php');
// Upload Image

if(isset($_REQUEST['action'])&& $_REQUEST['action'] =='upload'){ //echopre($_FILES); echopre1($_REQUEST);

    $fileId  = $_REQUEST['fileId'];
    $pType   = $_REQUEST['pType'];
    
    list($width, $height, $type, $attr) = getimagesize($_FILES['product_more_image_'.$fileId]['tmp_name']);

    if($width<=250 || $height<=250) {
        echo trim('filesizeError');
        exit;
    }
    else {

        $dir_dest = "pics";
        //big Image Upload
        $picbigname = $_FILES['product_more_image_'.$fileId]['name'];
        
        if ($picbigname != "") {
            $fileName = basename($picbigname);
            $extension = end(explode(".", $picbigname));
            $fileNameWithoutExtension = preg_replace("/\.[^.]+$/", "", $fileName);
            $final_image_big = $_SESSION["guserid"]."_".time().$fileNameWithoutExtension;
        }
        $picbig_newname = $final_image_big;
        
        if ($picbig_newname != "") {

            $files = $_FILES['product_more_image_'.$fileId];
            $handle = new Upload($files);
            if ($handle->uploaded) {
                $handle->image_resize = false;
                $handle->image_ratio_y = true;
                //$handle->image_x = 283;
                //$handle->image_y = 269;
                $handle->file_new_name_body = $picbig_newname;
                $handle->Process($dir_dest);
                $big_image_name_after_upload = $handle->file_dst_name;

                $handle->file_new_name_body = 'small_'.$picbig_newname;
                $handle->Process($dir_dest);
                $handle->file_new_name_body = 'medium_'.$picbig_newname;
                $handle->Process($dir_dest);
                $handle->file_new_name_body = 'large_'.$picbig_newname;
                $handle->Process($dir_dest);
            }
        }
        echo trim($big_image_name_after_upload."**".$fileId);
        exit;
    }
}

// Use image as such
if(isset($_REQUEST['action'])&& $_REQUEST['action'] =='upload_existing'){ //echopre1($_REQUEST);

    $imageName = $_REQUEST['image'];
    $fileId  = $_REQUEST['fileId'];
    $info = getimagesize("pics/".$imageName);
    $oldWidth  = $info[0];
    $oldHeight = $info[1];

    $mime = $info['mime'];
    $mime = explode("/",$mime);
    $imageType= $mime[1];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $targ_w = $_REQUEST['w'];
            $targ_h = $_REQUEST['h'];
            $targ_x = $_REQUEST['x'];
            $targ_y = $_REQUEST['y'];
            
            if($targ_x == "" && $targ_y == "") { 
                $sourcePath = "pics/";
                resizeImage($sourcePath,$imageName,$imageType,$oldWidth,$oldHeight,553,458,"large_");
                resizeImage($sourcePath,$imageName,$imageType,$oldWidth,$oldHeight,248,248,"medium_");
                resizeImage($sourcePath,$imageName,$imageType,$oldWidth,$oldHeight,120,120,"small_");

            }
            else { 

                $currenrWorkingDir = getcwd();
                $originalsrc = $currenrWorkingDir."/pics/".$imageName;
                $src         = $currenrWorkingDir."/pics/large_".$imageName;
                copy($originalsrc,$src);
                

                $jpeg_quality = 100;
    
                switch ($imageType) {
                    case jpeg:
                        $image = imagecreatefromjpeg($src);
                        break;
                    case jpg:
                        $image = imagecreatefromjpeg($src);

                    case png:
                        $image = imagecreatefrompng($src);
                        break;
                    case gif:
                        $image = imagecreatefromgif($src);
                        break;
                }
                
                $dst_r = ImageCreateTrueColor(553, 458 );
                imagealphablending( $dst_r, false );
                imagesavealpha( $dst_r, true );
                imagecopyresampled($dst_r,$image,0,0,$targ_x, $targ_y,553,458,$targ_w,$targ_h);
                $destLocation = $currenrWorkingDir."/pics/large_";
                
                $destImage = $src;

                switch ($imageType) {
                    case jpeg: {
                            imagejpeg($dst_r,$destImage,$jpeg_quality);
                            break;
                        }
                    case jpg: {
                            imagejpeg($dst_r,$destImage,$jpeg_quality);
                            break;
                        }
                    case png: {
                            imagepng($dst_r,$destImage,9);
                            break;
                        }
                    case gif: {
                            imagegif($dst_r,$destImage);
                            break;
                        }
                }
                
               // boxResize($destLocation,$imageName,$imageName,$currenrWorkingDir."pics/medium_", 248, 248,"ZEBRA_IMAGE_BOXED");
                boxResize($destLocation,$imageName,$imageName,$currenrWorkingDir."/pics/small_", 120, 120,"ZEBRA_IMAGE_BOXED");

        }

        $imagePath              =   "pics/large_".$imageName;
        $origImage              =   "pics/".$imageName;
        $returnString           =   '<div class="jqMoreImageContainer brd_pc_stls"  style="padding:5px;border:0px solid #D3D1D1;margin:10px; position:relative; float:left;width:225px;">
                                    
                                     <div style="width:22px; position:absolute; top:18px; right:55px; height:22px;">
                                        <a href="javascript:;" class="jqMoreImageDeleteBeforeSave" dataImage="'.$origImage.'" dataId="'.$fileId.'"><img border="0" src="images/dlt_btn.png" title="delete this image" /></a>
                                    </div>
                                    <img class="jqMoreImage mn_pcs" src="'.$imagePath.'" id="image'.$fileId.'" width= "225" height="180" />  <br/>
                                     </div>';
        $returnString           =   $returnString."{valsep}".$fileId;

        echo $returnString;
        exit;
    }


}


// Delete Image 

if(isset($_REQUEST['action'])&& $_REQUEST['action'] =='delMoreImageBeforeSave'){ 
    
       $id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
       $image = isset($_REQUEST['image']) ? trim($_REQUEST['image']) : '';
       
       $imageName   = str_replace("pics/",'',$image);
       //echo $imageName;
        if(is_file("pics/".$imageName)&& file_exists("pics/".$imageName)) {
            unlink("pics/".$imageName);
        }
        if(is_file("pics/large_".$imageName)&& file_exists("pics/large_".$imageName)) {
            unlink("pics/large_".$imageName);
        }
       
        if(is_file("pics/small_".$imageName)&& file_exists("pics/small_".$imageName)) {
            unlink("pics/small_".$imageName);
        }
        echo json_encode(array('success' =>MSG_MORE_IMAGE_DELETED_SUCCESSFULLY));
        exit;
    }

?>