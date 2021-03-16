var selected_count;
var $jquery=jQuery.noConflict();
$jquery(document).ready(function(){

   // var pType = $("#pType").val();
    
    $jquery('.fileuploader').each(function(i, j){ 
         
//alert('sadasd');
//alert($('[name="MAX_NO_IMAGES"]').val());
        var settings = {
        url:'ajax_more_image_upload.php?action=upload&fileId='+i,
        method: "POST",
        allowedTypes:"jpg,png,jpeg,gif,JPG,PNG,JPEG,GIF",
        fileName: "product_more_image_"+i,
        multiple: false,
        autoSubmit:true,
        dragDropStr: '',
        onSubmit:function(files){
            $jquery("#error_product_more_image").html("");
        },
        onSuccess:function(files,response,xhr){
            
            $jquery("#jqUploadFileStartTxt").hide();
            $jquery(".ajax-file-upload-statusbar").hide();
            selected_count = i;
            
            if(response=="filesizeError"){ 
                $jquery("#jqCropMoreImageDiv_"+i).hide();
                alert("Please upload image greater than 250px X 250px resolution");
            }
            else {
               
                setTimeout(function () {

                    $jquery("#jqUploadFileStartTxt").fadeOut();
                    
                    response = response.split("**");
                    var product_image = response[0];
                    //alert(product_image);

                    $jquery(".ajax-file-upload-statusbar").hide();
                    $jquery("#jqCropMoreImageDiv_"+i).show();
                    $jquery("#cropButtonClicked").val(0);
                    $jquery("#productMoreImage_"+i).val(product_image);
                    $jquery("#productMoreImageId_"+i).val(response[1]);
                    $jquery('#jqCropMoreImageDiv_'+i).find('.jqImageHoldingDiv').html("");
//                    $jquery('.jqImageHoldingDiv').html("");
var appendToDiv=$jquery('#jqCropMoreImageDiv_'+i).find('.jqImageHoldingDiv');
//alert("Here");
console.log(appendToDiv);
                    var myImage = $jquery("<img></img>", {
                        src: "pics/"+product_image,
                        id:"imagetocrop_"+i
                    }).appendTo($jquery(appendToDiv));
                    
//                    console.log(myImage);

                    myImage.Jcrop({
                        bgColor: 'transparent',
                        onSelect: updateCoords,
                        aspectRatio: 250 /250,
                        boxWidth: 800,
                        boxHeight: 600,
                        minSize: [250,250]
                    });
                    
                    
                    //for removing crop
                    uploadexitingmore(i);
                    
                }, 2000);
            }
        },
        onError: function(files,status,errMsg)
        {
            $jquery("#status").html("<font color='red'>"+UPLOAD_FALIED+"</font>");
        }
    }

    var uploadObj = $jquery("#multi_file_upload_"+i).uploadFile(settings);
    
    
    //$(".fileuploader").uploadFile(settings);
    
    });
    
    
    
    $jquery(".jqMoreImageDeleteBeforeSave").live("click",function(){
        if(confirm(DELETE_CONFIRM)){
            var id    = $jquery(this).attr("dataId");
            var image = $jquery(this).attr("dataImage");
            $jquery("#productMoreImage_"+id).val("");
            $jquery("#productMoreImageId_"+id).val("");
            var delLink = $jquery(this);
            $jquery.ajax({url:'ajax_more_image_upload.php?action=delMoreImageBeforeSave',
            data:{id:id, image:image},
            type:'post',
            dataType:'json',
            success:function(reply){
                if(reply)
                {
                    if(reply.success)
                    {
                        //$(delLink).parents('.jqMoreImageContainer').find('.jqMoreImageZoom').remove();
                        $jquery(delLink).parents('.jqMoreImageContainer').find('.jqMoreImage').remove();
                        $jquery(delLink).parents('.jqMoreImageContainer').find('.jqMoreImageDeleteBeforeSave').remove();
                    }
                }
            }
        });
        }
        return false;
    });
  /*  var settings = {
	url: "ajax_more_image_upload.php?action=upload",
	method: "POST",
	allowedTypes:"jpg,png,gif,doc,pdf,zip",
	fileName: "myfile",
	multiple: true,
	onSuccess:function(files,data,xhr)
	{
		$("#status").html("<font color='green'>Upload is success</font>");
		
	},
	onError: function(files,status,errMsg)
	{		
		$("#status").html("<font color='red'>Upload is Failed</font>");
	}
}
//$("#mulitplefileuploader").uploadFile(settings);
$(".fileuploader").each(function(){ $(this).uploadFile(settings); });*/

    //

    $jquery(".jqCropMoreImage").live("click",function(){ 
        
        var cropButtonClicked = $jquery("#cropButtonClicked").val(); 
        selected_count=$jquery(this).parent().parent().attr('id').split('_')[1];
        if(cropButtonClicked==0){
            $jquery("#cropButtonClicked").val(1);

            var x= $jquery("#x").val();
            var y= $jquery("#y").val();
            var w= $jquery("#w").val();
            var h= $jquery("#h").val();
            if(x=="" && w==""){
                $jquery("#cropButtonClicked").val(0);
                alert("Please select an area to crop ");
                return false;
            }
  
            var bannerName = $jquery("#productMoreImage_"+selected_count).val();
            var fileId     =  $jquery("#productMoreImageId_"+selected_count).val();
            
            $jquery.ajax({
                url: 'ajax_more_image_upload.php?action=upload_existing',
                type:'post',
                dataType:'html',
                data: "x="+x+"&y="+y+"&w="+w+"&h="+h+"&image="+bannerName+"&fileId="+fileId,
                success:function(response) { 
                    $jquery("#jqCropMoreImageDiv_"+selected_count).hide();
                    closeIFrame(bannerName);
                    response                =   response.split("{valsep}");

                    var imageString         =   response[0];
                    var fileId              =   response[1];
                   // alert(fileId);
                    //$('#jqproductImageMoreDiv_'+fileId).css({"width":"relative", "top":"-75px"});
                    $jquery('#jqproductImageMoreDiv_'+fileId).html(imageString);
                    $jquery('.jqMakeDefaultImageHintDivOld').hide();
                   $jquery('.jqMakeDefaultImageHintDiv').show();
                    $jquery("#JQUploadedImageMoreDiv").show();

                    $jquery("#error_product_image").html("");
                    $jquery(".jqAjaxLoaderImage").hide();
                    var existImage  = $jquery("#txtExist_moreImage"+fileId).val();
                    
                    if(existImage!='')
                    {
                        $jquery("#JqMoreExistImage"+fileId).html("");
                        $jquery(".JqMoreExistImage"+fileId).hide();
                    }

                    imageUploaded =    1;
                    bindThickBoxEvents();

                    $jquery("#x").val("");
                    $jquery("#y").val("");
                    $jquery("#w").val("");
                    $jquery("#h").val("");
                    $jquery("#cropButtonClicked").val(0);
                }
            });
        }
        return false;
    });
    $jquery(".jqCloseCropMoreImage").on("click",function(){
        if (!confirm(MESSAGE_CONFIRM_CROP)) {
            return false;
        }
        selected_count=$jquery(this).parent().parent().attr('id').split('_')[1];
        var image = $jquery("#imagetocrop");

        //        var x= image.width()/2-parseInt(150);
        //        var y= image.height()/2-parseInt(174);
        var x = "";
        var y ="";
        var w= 350;
        var h= 350;
        
        var bannerName = $jquery("#productMoreImage_"+selected_count).val();
        var fileId     =  $jquery("#productMoreImageId_"+selected_count).val();
        
        $jquery.ajax({
            url: 'ajax_more_image_upload.php?action=upload_existing',
            type:'post',
            dataType:'html',
            data: "x="+x+"&y="+y+"&w="+w+"&h="+h+"&image="+bannerName+"&fileId="+fileId,
            success:function(response) {
                $jquery("#jqCropMoreImageDiv_"+selected_count).hide();
                closeIFrame(bannerName);
                response                =   response.split("{valsep}");

                var imageString         =   response[0];
                var fileId              =   response[1];
                $jquery('#jqproductImageMoreDiv_'+fileId).html(imageString);
                $jquery("#error_product_more_image").html("");
                $jquery(".jqAjaxLoaderImage").hide();
                $jquery("#JQUploadedImageMoreDiv").show();
                imageUploaded =    1;
               // bindThickBoxEvents();

                    var existImage  = $jquery("#txtExist_moreImage"+fileId).val();
                    
                    if(existImage!='')
                    {
                        $jquery("#JqMoreExistImage"+fileId).html("");
                        $jquery(".JqMoreExistImage"+fileId).hide();
                    }

            }
        });
        return false;
    });

});

function uploadexitingmore(index)
{
    selected_count=index;
        var image = $jquery("#imagetocrop");

        //        var x= image.width()/2-parseInt(150);
        //        var y= image.height()/2-parseInt(174);
        var x = "";
        var y ="";
        var w= 350;
        var h= 350;
        
        var bannerName = $jquery("#productMoreImage_"+selected_count).val();
        var fileId     =  $jquery("#productMoreImageId_"+selected_count).val();
        
        $jquery.ajax({
            url: 'ajax_more_image_upload.php?action=upload_existing',
            type:'post',
            dataType:'html',
            data: "x="+x+"&y="+y+"&w="+w+"&h="+h+"&image="+bannerName+"&fileId="+fileId,
            success:function(response) {
                $jquery("#jqCropMoreImageDiv_"+selected_count).hide();
                closeIFrame(bannerName);
                response                =   response.split("{valsep}");

                var imageString         =   response[0];
                var fileId              =   response[1];
                $jquery('#jqproductImageMoreDiv_'+fileId).html(imageString);
                $jquery("#error_product_more_image").html("");
                $jquery(".jqAjaxLoaderImage").hide();
                $jquery("#JQUploadedImageMoreDiv").show();
                imageUploaded =    1;
               // bindThickBoxEvents();

                    var existImage  = $jquery("#txtExist_moreImage"+fileId).val();
                    
                    if(existImage!='')
                    {
                        $jquery("#JqMoreExistImage"+fileId).html("");
                        $jquery(".JqMoreExistImage"+fileId).hide();
                    }

            }
        });
        return false;
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
    var htmlString  =   '<img class="uploadedbanner" src="pics/large_"'+bannerName+'" id="uploaded_banner_image" width="475" height="95" alt="">';
    $jquery(".jqUploadedBannerImage").html(htmlString);
    $jquery("#error_banner_image").html("");
    $jquery(".jqAjaxLoader").hide();
}

function removeThickBoxEvents() {
        $jquery('.thickbox').each(function(i) {
            $jquery(this).unbind('click');
        });
    }

function bindThickBoxEvents() {
        removeThickBoxEvents();
        //tb_init('a.thickbox, area.thickbox, input.thickbox');
    }