var $jquery=jQuery.noConflict();
$jquery(document).ready(function(){


    var pType = $jquery("#pType").val(); 
    
    var settings = {
        url: 'ajax_image_upload.php?action=upload&pType='+pType,
        method: "POST",
        allowedTypes:"jpg,png,jpeg,gif,JPG,PNG,JPEG,GIF",
        fileName: "product_image",
        multiple: false,
        autoSubmit:true,
        dragDropStr: '',
        onSubmit:function(files){  
           /* var img = document.getElementsByName('product_image');
            alert(img);
            var d_width = img.width;
            var d_height = img.height;
            alert(d_width);alert(d_height);*/

            $jquery("#error_product_image").html("");

            // $("#jqUploadFileStartTxt").show();
            // $("#jqUploadFileStartTxt").html("Initializing upload");
        },
        onSuccess:function(files,response,xhr)
        {  
            $jquery("#jqUploadFileStartTxt").hide();
            $jquery(".ajax-file-upload-statusbar").hide();

            if(response=="filesizeError"){ 
                $jquery("#jqCropImageDiv").hide();
                alert("Please upload image greater than 250px X 250px resolution");
                /*bannerUploaded =    0;
                $$jquery("#error_product_image").html(MESSAGE_DIMENSION);
                $jquery(".jqAjaxLoader").hide();
                $jquery(".ajax-file-upload-statusbar").hide();
                return false;*/
            }
            else {
                
                
                
                setTimeout(function () {

                    $jquery("#jqUploadFileStartTxt").fadeOut();

                    var product_image  =   response;
                    var image_db_save_name  = "pics/medium_"+product_image ;
                    var small_image_name    = "pics/small_"+product_image ; 

                    $jquery("#txtPicture").val(image_db_save_name);
                    $jquery("#txtPictureSmall").val(small_image_name);
                    $jquery(".ajax-file-upload-statusbar").hide();
                    $jquery("#jqCropImageDiv").show();
                    $jquery("#cropButtonClicked").val(0);
                    $jquery("#bannerName").val(product_image);
                    $jquery("#mulitplefileuploader").parent().siblings('#jqCropImageDiv').find('.jqImageHoldingDiv').html("");
                    //$jquery('.jqImageHoldingDiv').html("");
                    var appendToDiv=$jquery("#mulitplefileuploader").parent().siblings('#jqCropImageDiv').find('.jqImageHoldingDiv');


                    var myImage = $jquery("<img></img>", {
                        src: "pics/"+product_image,
                        id:"imagetocrop"
                    }).appendTo($jquery(appendToDiv));
                    console.log(myImage);

                    myImage.Jcrop({
                        bgColor: 'transparent',
                        onSelect: updateCoords,
                        aspectRatio: 250 /250,
                        boxWidth: 800,
                        boxHeight: 600,
                        minSize: [250,250]
                    });
                     //for removing crop
                   
                    uploadexiting(response);
                    
                }, 2000);
                
                
                
                
                
            }

        },
        onError: function(files,status,errMsg)
        {
            $jquery("#status").html("<font color='red'>"+MESSAGE_FAILED_UPLOAD+"</font>");
        }
    }


    var uploadObj = $jquery("#mulitplefileuploader").uploadFile(settings);

    $jquery(".jqCropImage").live("click",function(){ 

        var cropButtonClicked = $jquery("#cropButtonClicked").val();
        if(cropButtonClicked==0){
            $jquery("#cropButtonClicked").val(1);

            var x= $jquery("#x").val();
            var x= $jquery("#x").val();
            var y= $jquery("#y").val();
            var w= $jquery("#w").val();
            var h= $jquery("#h").val();
            if(x=="" && w==""){
                $jquery("#cropButtonClicked").val(0);
                alert("Please select an area to crop ");
                return false;
            }
            var bannerName = $jquery("#bannerName").val();

            $jquery.ajax({
                url: 'ajax_image_upload.php?action=upload_existing',
                type:'post',
                dataType:'html',
                data: "x="+x+"&y="+y+"&w="+w+"&h="+h+"&image="+bannerName,
                success:function(response) { //alert(response);
                    $jquery("#jqCropImageDiv").hide();
                    closeIFrame(bannerName);
                    response                =   response.split("{valsep}");

                    var imageString         =   response[0];
                    $jquery('#jqproductImageDiv').html(imageString);
                    $jquery('.jqMakeDefaultImageHintDivOld').hide();
                    $jquery('.jqMakeDefaultImageHintDiv').show();
                    $jquery("#JQUploadedImageDiv").show();

                    $jquery("#error_product_image").html("");
                    $jquery(".jqAjaxLoaderImage").hide();
                    
                    
                    // Hide if image alredy exist
                    var existImage  = $jquery("#jQImage").val();
                    
                    if(existImage!='')
                    {
                        $jquery("#JqexistImage").html("");
                        $jquery(".JqexistImage").hide();
                    }
                    
                    imageUploaded =    1;

                    $jquery("#x").val("");
                    $jquery("#y").val("");
                    $jquery("#w").val("");
                    $jquery("#h").val("");
                    $jquery("#cropButtonClicked").val(0);

                }
            });
        }
    });
    $jquery(".jqCloseCropImage").on("click",function(){
        if (!confirm(MESSAGE_CONFIRM_CROP)) {
            return false;
        }
        var image = $jquery("#imagetocrop");

        //        var x= image.width()/2-parseInt(150);
        //        var y= image.height()/2-parseInt(174);
        var x = "";
        var y ="";
        var w= 300;
        var h= 347;
        
        var bannerName = $jquery("#bannerName").val();
        
        $jquery.ajax({
            url: 'ajax_image_upload.php?action=upload_existing',
            type:'post',
            dataType:'html',
            data: "x="+x+"&y="+y+"&w="+w+"&h="+h+"&image="+bannerName ,
            success:function(response) {
                $jquery("#jqCropImageDiv").hide();
                closeIFrame(bannerName);
                response                =   response.split("{valsep}");

                var imageString         =   response[0];
                $jquery('#jqproductImageDiv').html(imageString);
                $jquery("#error_product_image").html("");
                $jquery(".jqAjaxLoaderImage").hide();
                $jquery("#JQUploadedImageDiv").show();
                imageUploaded =    1;

            }
        });
    });

});

function uploadexiting(bannerName)
{
    
     var x = "";
        var y ="";
        var w= 300;
        var h= 347;
     $jquery.ajax({
            url: 'ajax_image_upload.php?action=upload_existing',
            type:'post',
            dataType:'html',
            data: "x="+x+"&y="+y+"&w="+w+"&h="+h+"&image="+bannerName ,
            success:function(response) {
                $jquery("#jqCropImageDiv").hide();
                closeIFrame(bannerName);
                response                =   response.split("{valsep}");

                var imageString         =   response[0];
                $jquery('#jqproductImageDiv').html(imageString);
                $jquery("#error_product_image").html("");
                $jquery(".jqAjaxLoaderImage").hide();
                $jquery("#JQUploadedImageDiv").show();
                imageUploaded =    1;

            }
        });
}
function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $jquery('#blah').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}


function jcropp(){
    $jquery('#popup_banner_image').Jcrop();
}
function updateCoords(c)
{
    $jquery('#x').val(c.x);
    $jquery('#y').val(c.y);
    $jquery('#w').val(c.w);
    $jquery('#h').val(c.h);
};
function closeIFrame(bannerName){
    var htmlString  =   '<img class="uploadedbanner" src="pics/medium_"'+bannerName+'" id="uploaded_banner_image" width="475" height="95" alt="">';
    $jquery(".jqUploadedBannerImage").html(htmlString);
    $jquery("#error_banner_image").html("");
    $jquery(".jqAjaxLoader").hide();
}