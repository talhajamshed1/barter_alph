<?php
include ("includes/config.php");
include ("includes/session.php");
include ("includes/functions.php");
include('includes/class.upload.php');
// Upload Image

if(isset($_REQUEST['action'])&& $_REQUEST['action'] =='upload'){ //echopre1($_REQUEST);

    $pType = $_REQUEST['pType'];
    list($width, $height, $type, $attr) = getimagesize($_FILES['product_image']['tmp_name']);

    if($width<=250 || $height<=250) {
        echo trim('filesizeError');
        exit;
    }
    else {
            $dir_dest = "pics";
            //big Image Upload
            $picbigname = $_FILES['product_image']['name'];

            $final_image_big = "";
            if ($picbigname != "") {
                $fileName = basename($picbigname);
                $extension = end(explode(".", $_FILES['product_image']['name']));
                $fileNameWithoutExtension = preg_replace("/\.[^.]+$/", "", $fileName);
                $final_image_big = $_SESSION["guserid"]."_".time().$fileNameWithoutExtension;
            }
             $picbig_newname = $final_image_big;
            
            if ($picbig_newname != "") {
                $files = $_FILES['product_image'];
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
        echo trim($big_image_name_after_upload);
        exit;                                           
    }
}


// Use image as such
if(isset($_REQUEST['action'])&& $_REQUEST['action'] =='upload_existing'){

    $imageName = $_REQUEST['image'];
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
            $fileId = 1;
            
            if($targ_x == "" && $targ_y == "") { 
                $sourcePath = "pics/";
                resizeImage($sourcePath,$imageName,$imageType,$oldWidth,$oldHeight,553,458,"large_");
                resizeImage($sourcePath,$imageName,$imageType,$oldWidth,$oldHeight,260,233,"medium_");
                resizeImage($sourcePath,$imageName,$imageType,$oldWidth,$oldHeight,120,120,"small_");
            }
            else { 

                $currenrWorkingDir = getcwd();
                
               // $currenrWorkingDir = str_replace("admin","", $currenrWorkingDir);
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
                
                $dst_r = ImageCreateTrueColor(250, 250 );
                imagealphablending( $dst_r, false );
                imagesavealpha( $dst_r, true );
                imagecopyresampled($dst_r,$image,0,0,$targ_x, $targ_y,250,250,$targ_w,$targ_h);
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
                
                boxResize($destLocation,$imageName,$imageName,$currenrWorkingDir."/pics/medium_", 260, 233,"ZEBRA_IMAGE_BOXED");
                boxResize($destLocation,$imageName,$imageName,$currenrWorkingDir."/pics/small_", 120, 120,"ZEBRA_IMAGE_BOXED");

        }

        $imagePath              =   "pics/medium_".$imageName;
        $origImage              =   "pics/".$imageName;
        /*$returnString           =   '<div style="float:left" class="prdctimg_listing">
                                     <img src="'.$imagePath.'"  id="image'.$fileId.'" ></div>';*/
        $returnString           =   '<div class="jqMoreImageContainer brd_pc_stls"  style="padding:5px;border:0px solid #D3D1D1;margin:10px 415px 10px 10px; position:relative; float:left;width:225px;">
                                    
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

?>