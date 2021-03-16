<?php
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include("./languages/".$_SESSION['lang_folder']."/category.php");//language file
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');
//checking typed item is valid or not
switch ($_GET['type']) {
    case "sale":
    $showPage = true;
    break;

    case "swap":
    $showPage = true;
    break;
    
    case "wish":
    $showPage = true;
    break;

    default:
    $showPage = false;
    $message = ERROR_INVALID_TYPE;
    break;
}//end switch

/*
 * Query to check if the seller have added a paypal email in his account details before adding a product
 */

$sqlPaypalCheck = mysqli_query($conn, "SELECT vLoginName FROM " . TABLEPREFIX . "users WHERE vPaypalEmail != '' AND nUserId = " . $_SESSION["guserid"]);
$paypalEmailAdded = (mysqli_num_rows($sqlPaypalCheck) > 0) ? true : false;
if(!$paypalEmailAdded)
{
	$showPage = false;
	$message = ERROR_PAYPAL_EMAIL;
}

/*
 * Ends here
 */

if ($_GET['type'] == 'sale') {
    //checking sale module status
    $sqlSale = mysqli_query($conn, "SELECT vActive FROM " . TABLEPREFIX . "client_module_category where nCategoryId='2' and vActive='1'") or die(mysqli_error($conn));
    switch (mysqli_num_rows($sqlSale)) {
        case "0":
        header('location:addsale.php?type=swap');
        exit();
        break;
    }//end switch
}

$MaxUploadSize = ini_get('upload_max_filesize');
//list($MaxUploadSize, $ext) = split('M', $MaxUploadSize);
list($MaxUploadSize, $ext) = explode('M', $MaxUploadSize);
//$MaxUploadSize = $MaxUploadSize * 1024;
//get the values for comission and featured
if (DisplayLookUp('7') != '') {
    $comm = DisplayLookUp('7');
}

$fea = DisplayLookUp('5');

if (DisplayLookUp('8') != '') {
    $commlmt = DisplayLookUp('8');
}
//Extract get variables
$type = $_GET["type"];
$act = $_GET["act"];
$txtCommission = 0;

if ($type == "sale") {
    $imagebar = HEADING_ADD_NEW_SALE_ITEM;
}
else if ($type == "swap") {
    $imagebar = HEADING_ADD_NEW_SWAP_ITEM;
}
else if ($type == "wish") {
    $imagebar = HEADING_ADD_NEW_WISH_ITEM;
}
//function to display categories in nested manner

function make_selectlist($current_cat_id, $count) {
    static $option_results;
    global $conn;
    if (!isset($current_cat_id)) {
        $current_cat_id = 0;
    }
    $count = $count + 1;
    $sql = "SELECT c.nCategoryId as id, L.vCategoryDesc as name from " . TABLEPREFIX . "category c
    LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
    where nParentId = '$current_cat_id' order by c.nPosition,c.nParentId ASC";
    $get_options = mysqli_query($conn, $sql);
    $num_options = mysqli_num_rows($get_options);
    if ($num_options > 0) {
        while (list($cat_id, $cat_name) = mysqli_fetch_row($get_options)) {
            if ($current_cat_id != 0) {
                $indent_flag = "&nbsp;&nbsp;&nbsp;&nbsp;";
                for ($x = 2; $x <= $count; $x++) {
                    $indent_flag .= "&raquo;&nbsp;";
                }
            }
            $cat_name = $indent_flag . $cat_name;
            $option_results[$cat_id] = $cat_name;
            make_selectlist($cat_id, $count);
        }
    }
    return $option_results;
}

//end function
//if post back
if (isset($_POST["btnSubmit"]) && $_POST["btnSubmit"] != "") {
    // get the form field values

    $txtCategory = addslashes($_POST["txtCategory"]);
    $txtTitle = addslashes($_POST["txtTitle"]);
    $txtBrand = addslashes($_POST["txtBrand"]);
    $ddlType = addslashes($_POST["ddlType"]);
    $ddlCondition = addslashes($_POST["ddlCondition"]);
    $txtYear = addslashes($_POST["txtYear"]);
    $txtValue = addslashes($_POST["txtValue"]);
    $txtPoint = addslashes($_POST["txtPoint"]);
    $txtShipping = addslashes($_POST["txtShipping"]);
    // $txtPicture = addslashes($_POST["txtPicture"]);
    $txtPicture = addslashes($_POST["txtPicture"]);
    
    $txtDescription = addslashes($_POST["txtDescription"]);
    $txtQuantity = addslashes($_POST["txtQuantity"]);
    $txtFeatured = addslashes($_POST["txtFeatured"]);
    $message = "";
    $now = date('Y-m-d H:i:s');
    $chkFeatured = $_POST["chkFeatured"];
    $txtCommission = $_POST["txtCommission"];
    $txtSmallImage = $_POST["txtPictureSmall"];

    //check point status
    switch ($EnablePoint) {
        case "1":
        case "2":
        $newField = ",nPoint";
        $newValue = ",'" . $txtPoint . "'";
        break;

        case "0":
        $newField = '';
        $newValue = '';
        break;
    }//end switch

    
    //manage uploads
   /* if (is_uploaded_file($_FILES['txtPicture']['tmp_name'][0])) {
        //get file size
        $size = $_FILES['txtPicture']['size'][0] / (1024 * 1024);

        //set file size limit
        if ($size > $MaxUploadSize) {
            $message = ERROR_FILE_TOO_LARGE;
        }
        //set file type
        $file_type = $_FILES['txtPicture']['type'][0];
        $file_tempname = $_FILES['txtPicture']['tmp_name'][0];
        //check if its image file
        if (!getimagesize($file_tempname)) {
            $message = ERROR_FILE_REQUIRED_FORMAT;
        }

        if (($file_type != "image/gif") && ($file_type != "image/jpeg") && ($file_type != "image/pjpeg")) {
            $message = ERROR_FILE_REQUIRED_FORMAT;
        }
        //see if file already exists
        $sqlpictureexists = "select vUrl from " . TABLEPREFIX . "sale where vUrl='pics/";
        $sqlpictureexists.=ReplaceArray($_FILES['txtPicture']['name'][0]);
        $sqlpictureexists.="'";
        $resultpictureexists = mysqli_query($conn, $sqlpictureexists) or die(mysqli_error($conn));


        //move file to the pics directory
        $file_name = "";
        if ($file_type == "image/pjpeg" || $file_type == "image/jpeg") {
            $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . ".jpg";
        }
        else if ($file_type == "image/gif") {
            $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . ".gif";
        }
        else if ($file_type == "image/bmp") {
            $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . ".bmp";
        }
        if ($message == "") {
            $file_name = ReplaceArray($file_name);
            move_uploaded_file($_FILES['txtPicture']['tmp_name'][0], $file_name);
			chmod($file_name, 0755);
            
			// new code to resize the image if it is too large
			list($old_width, $old_height, $imgtype, $attr) = getimagesize($file_name);
			if($old_width > 800)
			{
				$max_width = 800;
				$max_height = 600;
				// Calculate the scaling we need to do to fit the image inside our frame
				$scale      = min($max_width/$old_width, $max_height/$old_height);
				// Get the new dimensions
				$new_width  = ceil($scale*$old_width);
				$new_height = ceil($scale*$old_height);
				$file_name = resizeImg($file_name, $new_width, $new_height, false, 100, 0, "");
			}
			// new resize code ends

         	
            $txtSmallImage = resizeImg($file_name, 120, 120, false, 100, 0, "_thumb");
			$txtPicture = $file_name;
        }
    }  */
    
    //generate sql depending on post type
    
    if ($type == "sale") {

        //sql for sale
        $sql = "INSERT INTO " . TABLEPREFIX . "sale (nSaleId, nCategoryId, nUserId,";
        $sql .= "vTitle, vBrand, vType, vCondition, vYear, nValue,";
        $sql .= "nShipping, vUrl, vDescription, dPostDate, nQuantity,vDelStatus,vSmlImg,vImgDes " . $newField . ")";
        $sql .= "VALUES ('', '$txtCategory', '";
        $sql .= $_SESSION["guserid"];
        $sql .= "', '$txtTitle',";
        $sql .= "'$txtBrand', '$ddlType', '$ddlCondition', '$txtYear', '$txtValue',";
        $sql .= "'$txtShipping', '$txtPicture', '$txtDescription','";
        $sql .= "$now" . "'";
        $sql .= ", '$txtQuantity','0','$txtSmallImage','" . addslashes($_POST['txtImgDes'][0]) . "' " . $newValue . ")";
    }
    else if ($type == "swap") {
        //sql for swap
        $sql = "INSERT INTO " . TABLEPREFIX . "swap (nSwapId, nCategoryId, nUserId, vTitle,";
        $sql .="vBrand, vType, vCondition, vYear, nValue, nShipping, vUrl,";
        $sql .="vDescription, vPostType, dPostDate, vSwapStatus, vDelStatus,vSmlImg,vImgDes " . $newField . ")";
        $sql .="VALUES ('', '$txtCategory', '";
        $sql .=$_SESSION["guserid"];
        $sql .="', '$txtTitle', '$txtBrand', '$ddlType', '$ddlCondition', '$txtYear',";
        $sql .="'$txtValue', '$txtShipping', '$txtPicture', '$txtDescription', 'swap','";
        $sql .="$now" . "'";
        $sql .=",'0','0','$txtSmallImage','" . addslashes($_POST['txtImgDes'][0]) . "' " . $newValue . ")";
    }//end esle if 
    else if ($type == "wish") {
        //sql for wish
        $sql = "INSERT INTO " . TABLEPREFIX . "swap (nSwapId, nCategoryId, nUserId, vTitle,";
        $sql .="vBrand, vType, vCondition, vYear, nValue, vUrl,";
        $sql .="vDescription, vPostType, dPostDate, vSwapStatus, vSmlImg, vDelStatus " . $newField . ")";
        $sql .="VALUES ('', '$txtCategory', '";
        $sql .=$_SESSION["guserid"];
        $sql .="', '$txtTitle', '$txtBrand', '$ddlType', '$ddlCondition', '$txtYear',";
        $sql .="'$txtValue', '$txtPicture', '$txtDescription', 'wish','";
        $sql .="$now" . "'";
        $sql .=",'0','$txtSmallImage','0' " . $newValue . ")";
    }
    //on non error insert data

    if (($act == 'post') && ($message == "")) {
        if (($chkFeatured != "featured") && ($txtCommission <= 0)) {//if payment is not required, it would be inserted now, otherwise after payment it would be inserted
            //execute query and send mail
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $ntmpid='';
        }
        else {
            // Make featurd if acommission and featured fee is zero
            if($txtCommission <= 0 && $chkFeatured == "featured" && $fea == 0){
                mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $ntmpid='';
                $NewId = mysqli_insert_id($conn);
                $featuredSql = "UPDATE " . TABLEPREFIX . "sale SET vFeatured = 'Y' WHERE nSaleId = '".$NewId."'";
                mysqli_query($conn, $featuredSql);
                $chkFeatured = '';
            }else{
                $ntmpid = $_SESSION["guserid"];
            }
        }
        //inert multiple images 
        if(!$NewId || $NewId == ''){
            $NewId = mysqli_insert_id($conn);
        }
        $fieldId = '';

        switch ($_GET['type']) {
            case "swap":
            $fieldId = 'nSwapId';
            break;

            case "sale":
            $fieldId = 'nSaleId';
            break;

            case "wish":
            $fieldId = 'nSwapId';
            break;
        }//end switch
        
        //check bal images
        
        
             /*
             * Product more images 
            */
             $moreFiles = $_POST['productMoreImage'];
           /* echo "<pre>";
            print_r($moreFiles);
            echo "</pre>";
            exit;*/
            for ($x = 0; $x < count($moreFiles); $x++) {
                $moreImageName = $moreFiles[$x];
                if ($moreImageName != "") {
                  //  insert into gallery table

                    $moreImage_large            = "pics/large_".$moreImageName;
                    
                    $txtSmallImage1             = "pics/small_".$moreImageName;
                    
                    $txtMediumImage1             = "pics/medium_".$moreImageName;
                    
                    $more_image_description     =  $_POST['txtImgDes'][$x+1];

                    mysqli_query($conn, "insert into " . TABLEPREFIX . "gallery (nUserId," . $fieldId . ",vImg,vDes,nTempId,vSmlImg,vMedImg) values 
                        ('" . $_SESSION["guserid"] . "','" . $NewId . "','" . $moreImage_large . "',
                        '" . addslashes($more_image_description) . "','" . $ntmpid . "',
                        '" . $txtSmallImage1 . "','" .  $txtMediumImage1."')") or die(mysqli_error($conn));
                    
                }
            }
            //manage uploads
           /* if (is_uploaded_file($_FILES['txtPicture']['tmp_name'][$i])) {
              //  list($oldName, $ext) = split('[.]', $_FILES['txtPicture']['name'][$i]);
                 list($oldName, $ext) = explode('[.]', $_FILES['txtPicture']['name'][$i]);

                //get file size
                $size = $_FILES['txtPicture']['size'][$i] / (1024 * 1024);

                //set file size limit
                if ($size > $MaxUploadSize) {
                    $message = ERROR_FILE_TOO_LARGE;
                }
                //set file type
                $file_type = $_FILES['txtPicture']['type'][$i];
                $file_tempname = $_FILES['txtPicture']['tmp_name'][$i];
                //check if its image file
                if (!getimagesize($file_tempname)) {
                    $message = ERROR_FILE_REQUIRED_FORMAT."<br>";
                }

                if (($file_type != "image/gif") && ($file_type != "image/jpeg") && ($file_type != "image/pjpeg")) {
                    $message = ERROR_FILE_REQUIRED_FORMAT;
                }
                //move file to the pics directory
                $file_name = "";
                if ($file_type == "image/pjpeg" || $file_type == "image/jpeg") {
                    $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . '_' . $oldName . ".jpg";
                }
                else if ($file_type == "image/gif") {
                    $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . '_' . $oldName . ".gif";
                }
                else if ($file_type == "image/bmp") {
                    $file_name = "pics/" . $_SESSION["guserid"] . "_" . time() . '_' . $oldName . ".bmp";
                }
                if ($message == "") {
                    $file_name = ReplaceArray($file_name);
                    move_uploaded_file($_FILES['txtPicture']['tmp_name'][$i], $file_name);
                    chmod($file_name, 0755);
					
					// new code to resize the image if it is too large
			
					list($old_width, $old_height, $type, $attr) = getimagesize($file_name);
					if($old_width > 800)
					{
						$max_width = 800;
						$max_height = 600;
						// Calculate the scaling we need to do to fit the image inside our frame
						$scale      = min($max_width/$old_width, $max_height/$old_height);
						// Get the new dimensions
						$new_width  = ceil($scale*$old_width);
						$new_height = ceil($scale*$old_height);
						$file_name = resizeImg($file_name, $new_width, $new_height, false, 100, 0, "");
					}
					// new resize code ends
		
					
					$txtSmallImage1 = resizeImg($file_name, 120, 120, false, 100, 0, "_thumb");
					$txtPicture1 = $file_name;

					
					
                  
                   // $txtSmallImage = resizeImg($txtPicture, 120, 120, false, 100, 0, "_thumb");

                    //insert into gallery table

                    mysqli_query($conn, "insert into " . TABLEPREFIX . "gallery (nUserId," . $fieldId . ",vImg,vDes,nTempId,vSmlImg) values 
												('" . $_SESSION["guserid"] . "','" . $NewId . "','" . $txtPicture1 . "',
												'" . addslashes($_POST['txtImgDes'][$i]) . "','" . $ntmpid . "',
												'" . $txtSmallImage1 . "')") or die(mysqli_error($conn));
                }
            }*/
            
        //echo($sql . "<br>" . $txtFeatured);
            
            $routesql = "Select vRoute from " . TABLEPREFIX . "category where nCategoryId ='$txtCategory'";
        //echo $routesql;
            $result = mysqli_query($conn, $routesql) or die(mysqli_error($conn));
            $row = mysqli_fetch_array($result);
            $route = $row["vRoute"];
            $countsql = "UPDATE " . TABLEPREFIX . "category SET nCount=nCount+1 WHERE nCategoryId in($route)";
            $result = mysqli_query($conn, $countsql) or die(mysqli_error($conn));

            $txtCategoryname = addslashes($_POST["txtCategoryname"]);
            $strFind = array("&Acirc;", "&nbsp;", "&raquo;");
            $strReplce = array("", "", "");
            $txtCategoryname   = str_replace($strFind,$strReplce,$txtCategoryname);
            if($EnablePoint==2){
                $itemValue  =   $txtPoint;
            }else{
                $itemValue  =   CURRENCY_CODE.$txtValue;
            }       

        /*
        * Fetch user language details
        */

        $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$_SESSION["lang_id"]."'";
        $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
        $langRw = mysqli_fetch_array($langRs);

        /*
        * Fetch email contents from content table
        */
        $mailSql = "SELECT L.content,L.content_title
        FROM ".TABLEPREFIX."content C
        JOIN ".TABLEPREFIX."content_lang L
        ON C.content_id = L.content_id
        AND C.content_name = 'addsales'
        AND C.content_type = 'email'
        AND L.lang_id = '".$_SESSION["lang_id"]."'";
        $mailRs  = mysqli_query($conn, $mailSql) or die(mysqli_error($conn));
        $mailRw  = mysqli_fetch_array($mailRs);

        $mainTextShow   = $mailRw['content'];
        $enbPnt = DisplayLookUp("EnablePoint");

        
        if($enbPnt=="2"){

            $mainTextShow   = str_replace("{pricename}",POINT_NAME.'/'.TEXT_PRICE, $mainTextShow);            
            //$itemValue  =   $txtPoint."/".CURRENCY_CODE.$txtValue;
            $itemValue   = (!empty($txtPoint)) ? $txtPoint : '--';
            $itemValue  .= (!empty($itemValue)) ? '   /  ' : '';
            $itemValue  .= (!empty($txtValue)) ? CURRENCY_CODE.$txtValue : '--';
            $itemValue  .= (!empty($txtShipping)) ? '  ('.TEXT_SHIPPING_CHARGE.' : '.CURRENCY_CODE.$txtShipping.')' : '';
            
        }else if($enbPnt=="1"){

            $mainTextShow   = str_replace("{pricename}",POINT_NAME, $mainTextShow);
            $itemValue  =   $txtPoint;

        }else{

            $mainTextShow   = str_replace("{pricename}",TEXT_PRICE, $mainTextShow);
            $itemValue  =   $txtValue;
            if($itemValue) {
               $itemValue  =   CURRENCY_CODE.$itemValue;
               $itemValue  .= (!empty($txtShipping)) ? '  ('.TEXT_SHIPPING_CHARGE.' : '.CURRENCY_CODE.$txtShipping.')' : '';
           }
       }

       $brandReplace = (!empty($txtBrand)) ? stripslashes($txtBrand) : '--';

       $arrTSearch	= array("{SITE_NAME}","{SITE_URL}","{txtTitle}","{txtBrand}","{ddlType}","{ddlCondition}","{txtValue}","{txtCategoryname}","{txtYear}");
       $arrTReplace	= array(SITE_NAME,SITE_URL,stripslashes($txtTitle),$brandReplace,stripslashes($ddlType),stripslashes($ddlCondition),$itemValue,stripslashes($txtCategoryname),$txtYear);

       $mainTextShow   = str_replace($arrTSearch,$arrTReplace,$mainTextShow);

       $mailcontent1   = $mainTextShow;

       $subject    = $mailRw['content_title'];
       $subject    = str_replace('{SITE_NAME}',SITE_NAME,$subject);

       $StyleContent   = MailStyle($sitestyle,SITE_URL);

       $sql = "Select vFirstName,vLoginName,vEmail from " . TABLEPREFIX . "users where nUserId = '" . $_SESSION["guserid"] . "' and vAlertStatus='Y' and vDelStatus = '0' and vStatus = '0'";

       $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
       if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $EMail = stripslashes($row["vEmail"]);
                //readf file n replace
            $arrSearch = array("{TITLE}", "{STYLE}", "{SITE-URL}", "{NAME}", "{CONTENT}", "{SITE-LOGO}", "{DATE}", "{SITE-NAME}", "{HEAD}");
            $arrReplace = array(SITE_TITLE, $StyleContent, SITE_URL, htmlentities($row["vLoginName"]), $mailcontent1, $logourl, date('m/d/Y'), SITE_NAME, $subject);
            $msgBody    = file_get_contents('languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody    = str_replace($arrSearch, $arrReplace, $msgBody);
              //echo $msgBody; exit;
                //$msgBody;
            send_mail($EMail, $subject, $msgBody, SITE_EMAIL, 'Admin');
        }
    }


    if (($chkFeatured == "featured") || ($txtCommission > 0)) {

        if ($chkFeatured == "featured") {
            $feaentry = "$fea";
        }
        else {
            $feaentry = "0";
        }

        $sql = "INSERT INTO " . TABLEPREFIX . "saleextra (nSaleextraId, nCategoryId, nUserId,";
        $sql .= "vTitle, vBrand, vType, vCondition, vYear, nValue,";
        $sql .= "nShipping, vUrl, vDescription, dPostDate, nQuantity,nFeatured,nCommission,vSmlImg,vImgDes " . $newField . ")";
        $sql .= "VALUES ('', '$txtCategory', '";
        $sql .= $_SESSION["guserid"];
        $sql .= "', '$txtTitle',";
        $sql .= "'$txtBrand', '$ddlType', '$ddlCondition', '$txtYear', '$txtValue',";
        $sql .= "'$txtShipping', '$txtPicture', '$txtDescription','";
        $sql .= "$now" . "'";
        $sql .= ", '$txtQuantity','$feaentry','$txtCommission','$txtSmallImage','" . addslashes($_POST['txtImgDes'][0]) . "' " . $newValue . ")";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        
        $sql = "Select nSaleextraId from " . TABLEPREFIX . "saleextra where dPostDate='$now' AND nUserId = '" . $_SESSION["guserid"] . "'";
        
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) > 0) {
            if ($row1 = mysqli_fetch_array($result)) {
                $id = $row1["nSaleextraId"];
                $_SESSION["gsaleextraid"] = $id;
                $_SESSION["points"] = $txtPoint;
                header("location:featuredpay.php");
                exit();
            }
        }
    }
}

}

include_once('./includes/title.php');

?>
<body onLoad="timersOne();">
    <script language="javascript1.1" type="text/javascript" src="js/commission.js"></script>
    <script language="javascript" type="text/javascript">


        //fill parent id
        var Parent=new Array;
        fea= <?php echo  $fea ?>;
        comm= <?php echo  $comm ?>;
        commlmt= <?php echo  $commlmt ?>;
        var enablePoint = <?php echo $EnablePoint ?>;

        <?php
        $parentsql = "select distinct nParentId from " . TABLEPREFIX . "category";
        $result = mysqli_query($conn, $parentsql);
        $count = 0;
        $disp = "";
        if (mysqli_num_rows($result) != 0) {
            while ($row = mysqli_fetch_array($result)) {
                $disp .="Parent[$count]=\"";
                $disp .=$row["nParentId"];
                $disp .="\";\n";
                $count = $count + 1;
            }
        }
        echo $disp;

        $max_additional_imgs = DisplayLookUp('MaxOfImages');
        ?>


    //function to validate form
    function validateSaleForm()
    {

        var frm = window.document.frmSale;
        
        if(trim(frm.cat_id.options[frm.cat_id.options.selectedIndex].value) == ""){
            alert("<?php echo ERROR_EMPTY_CATEGORY; ?>");
            frm.cat_id.focus();
            return false;
        }else if(trim(frm.txtTitle.value) == ""){
            alert("<?php echo ERROR_EMPTY_TITLE; ?>");
            frm.txtTitle.focus();
            return false;
        }else if(trim(frm.txtDescription.value) == ""){
            alert("<?php echo ERROR_EMPTY_DESCRIPTION; ?>");
            frm.txtDescription.focus();
            return false;
        }
        <?php if ($EnablePoint == '2') { ?>  
            if( (trim(frm.txtPoint.value) == "" || parseInt(trim(frm.txtPoint.value)) <= 0) && (trim(frm.txtValue.value) == "" ))
            {
                alert("<?php echo str_replace('{point_name}',POINT_NAME,ERROR_INVALID_PRICE); ?>");
                frm.txtValue.focus();
                return false;
            }
            if((trim(frm.txtValue.value)==""))
            {
                alert("<?php echo ERROR_INVALID_PRICE; ?>");
                frm.txtValue.focus();
                return false;
            }else{

                if(parseInt(trim(frm.txtValue.value)) <= 0)
                {
                 alert("<?php echo ERROR_ZERO_PRICE; ?>");
                 frm.txtValue.focus();
                 return false;
             }
         }
         <?php } ?>
         <?php if ($EnablePoint == '1') { ?>

            if((trim(frm.txtPoint.value) =="")||(parseInt(trim(frm.txtPoint.value)) <= 0))
            {
                alert("<?php echo str_replace('{point_name}',POINT_NAME,ERROR_INVALID_POINT); ?>");
                frm.txtPoint.focus();
                return false;
            }
            <?php }
            if ($EnablePoint == '0') {

                ?>
                if((trim(frm.txtValue.value)==""))
                {

                    alert("<?php echo ERROR_INVALID_PRICE; ?>");
                    frm.txtValue.focus();
                    return false;
                }else{

                    if(parseInt(trim(frm.txtValue.value)) <= 0)
                    {
                     alert("<?php echo ERROR_ZERO_PRICE; ?>");
                     frm.txtValue.focus();
                     return false;
                 }
             }

             <?php } ?>

             <?php
             if ($type == "sale") {
                ?>
                if((trim(frm.txtQuantity.value) == "")||(frm.txtQuantity.value <= 0)){ 
                    alert("<?php echo ERROR_EMPTY_QUANTITY; ?>");
                    frm.txtQuantity.focus();
                    return false;
                }
                <?php
            }
            ?>
            return true;
        }


        function setcatValue(){

            selvalue=document.frmSale.cat_id.options[document.frmSale.cat_id.options.selectedIndex].value;
          
            if(selvalue){
            flag="false";
            for(i=0;i<Parent.length;i++){
                if(Parent[i]==selvalue){
                    flag="true";
                }
            }
            if(flag=="false"){
                document.frmSale.txtCategory.value=document.frmSale.cat_id.options[document.frmSale.cat_id.options.selectedIndex].value;
            }else{
                document.frmSale.txtCategory.value = "";
                document.frmSale.cat_id.options.selectedIndex=0;
                alert("<?php echo ERROR_SELECT_SUBCATEGORY; ?>");

            }
            txtCategoryname = document.frmSale.cat_id.options[document.frmSale.cat_id.options.selectedIndex].text;

        //  	 $txtCategoryname = <?php echo  txtCategoryname ?>;
        //	 alert($txtCategoryname);
        document . getElementById("txtCategoryname") . value = txtCategoryname;
    }
        //	 alert(categoryname);
    }

    /*function checkNumeric(ids){

        var val=document.getElementById(ids).value;

        if ((isNaN(val))||(val<0)||(parseInt(val,10)==0)){
            alert("<?php //echo ERROR_POSITIVE_VALUE; ?>");
            document.getElementById(ids).value="0";
            document.getElementById(ids).focus();
        }
    }*/


    function addFeatured(){

    }

    function checkCommission(obj)
    {
        check_float_value(obj);
        //if (parseInt(obj.value)==0) document.frmSale.txtCommission.value="0";
        
    }//end function

    function calculator(){
        window.open('calculator.php','','width=300,height=300,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no');
    }

    function add_more_upload() 
    {
        var new_total = Math.round(document.frmSale.TOTAL_FILE.value) + 1;
        
        var max_upload_file = '<?php echo $max_additional_imgs ?>';
        
       // alert(new_total + '----' + max_upload_file);
        //return false;
        var currentVal  = parseInt(document.frmSale.TOTAL_FILE.value);
        if(new_total<=max_upload_file)
        {	
           // alert('pic'+new_total);
           document.getElementById('pic'+new_total).style.display='';
           var newVal      = currentVal + 1;
           document.frmSale.TOTAL_FILE.value=newVal;
       }
       
       var newTotal  = parseInt(document.frmSale.TOTAL_FILE.value);
         //alert(newTotal + '----' + max_upload_file);
         if(newTotal>=max_upload_file-1){
            document.frmSale.upload_more.style.display='none';}
    }//end funciton
</script>


<?php include_once('./includes/top_header.php'); ?>
<script language="Javascript">
 $jqr(document).ready(function() {
     
    
     
    $jqr(".jQNumericOnly").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($jqr.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
             (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right, down, up
             (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
             return;
         }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
});
</script>


<style>
    .cropselection_popup
    {
        right: 0px !important;
        margin-top: 47px !important;
    }
</style>


<link href="styles/upload_image.css" rel="stylesheet" type="text/css">
<link href="styles/jquery.Jcrop.css" rel="stylesheet" type="text/css">
<script src="languages/<?php echo $_SESSION['lang_folder']?>/message.js"></script>

<div class="homepage_contentsec">

    <div class="container">
        <div class="row">
            <div class="col-lg-3"><?php include_once ("./includes/usermenu.php"); ?></div>
            <div class="col-lg-9">

                <div class="innersubheader">
                    <h4><?php echo $imagebar; ?></h4>
                </div>

                <div class="">

                    <div class="col-lg-12 col-sm-12 col-md-10 col-xs-12 form-section ">
                        <form  name="frmSale" method="POST" action="<?php echo "addsale.php?type=" . $type . "&act=post"; ?>" onSubmit="return validateSaleForm();" enctype="multipart/form-data">
                            <?php
				            //on perfect entry display result
                            if (($act == 'post') && ($message == "")) { 
                                ?>
                                <div class="row success"><?php echo MESSAGE_ITEM_SUCCESSFULLY_LISTED; ?></div>
                                <?php
                            }
                            else { 
                                //show the entry form 
                                ?>

                                <?php
                                if (isset($message) && $message != '') {
                                    ?>
                                    <div class="row warning"><?php echo $message; ?></div>
                                    <?php
                                }

								//check vulnerabilitie
                                if ($showPage != false) { 
                                    ?>
                                    <input type="hidden"  name="txtCategory" size="40" maxlength="11" value="<?php echo  stripslashes($txtCategory) ?>"/>
                                    <input type="hidden" id="txtCategoryname" name="txtCategoryname" value="">			
                                    <div class="row main_form_inner">
                                        <label><?php echo TEXT_CATEGORY; ?> <span class="warning">*</span></label>
                                        <select  name="cat_id" onChange="setcatValue();" class="comm_input form-control">
                                            <option value="">-- <?php echo TEXT_SELECT_ONE; ?> -- </option>
                                            <?php
                                            $get_options = make_selectlist(0, 0);
                                            if (count($get_options) > 0) {
                                                $categories = $_POST['cat_id'];
                                                foreach ($get_options as $key => $value) {
                                                    $options .="<option value=\"$key\"";
                                                    if ($_POST['cat_id'] == "$key") {
                                                        $options .=" selected=\"selected\"";
                                                    }
                                                    $options .=">".utf8_encode($value)."</option>\n";
                                                }
                                            }
                                            echo $options;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="row main_form_inner">
                                        <label><?php echo TEXT_TITLE; ?> <span class="warning">*</span></label>
                                        <input type="text" class="comm_input form-control" name="txtTitle"  maxlength="100" value="<?php echo htmlentities(stripslashes($txtTitle)) ?>" />
                                    </div>
                                    <div class="row main_form_inner">
                                        <label><?php echo TEXT_BRAND; ?></label>
                                        <input type="text" class="comm_input form-control" name="txtBrand" size="40" maxlength="100" value="<?php echo htmlentities(stripslashes($txtBrand)) ?>" />
                                    </div>
                                    <div class="row main_form_inner">
                                        <label><?php echo TEXT_TYPE; ?></label>
                                        <select name="ddlType" class="comm_input form-control">
                                            <option value='<?php echo TEXT_NEW; ?>' <?php echo ($ddlType == TEXT_NEW || $ddlType == "") ? "Selected" : ""; ?>><?php echo TEXT_NEW; ?></option>
                                            <option value='<?php echo TEXT_USED; ?>' <?php echo ($ddlType == TEXT_USED) ? "Selected" : ""; ?>><?php echo TEXT_USED; ?></option>
                                        </select>
                                    </div>
                                    <div class="row main_form_inner">
                                        <label><?php echo TEXT_CONDITION; ?></label>
                                        <select name="ddlCondition" class="comm_input form-control">
                                            <option value='<?php echo TEXT_NEW; ?>' <?php echo ($ddlCondition == "" || $ddlCondition == TEXT_NEW) ? "Selected" : ""; ?>><?php echo TEXT_NEW; ?></option>
                                            <option value='<?php echo TEXT_LIKE_NEW; ?>' <?php echo ($ddlCondition == TEXT_LIKE_NEW) ? "Selected" : ""; ?>><?php echo TEXT_LIKE_NEW; ?></option>
                                            <option value='<?php echo TEXT_VERY_GOOD; ?>' <?php echo ($ddlCondition == TEXT_VERY_GOOD) ? "Selected" : ""; ?>><?php echo TEXT_VERY_GOOD; ?></option>
                                            <option value='<?php echo TEXT_GOOD; ?>' <?php echo ($ddlCondition == TEXT_GOOD) ? "Selected" : ""; ?>><?php echo TEXT_GOOD; ?></option>
                                        </select>
                                    </div>
                                    <div class="row main_form_inner">
                                        <label><?php echo TEXT_ITEM_DESCRIPTION; ?> <span class="warning">*</span></label>
                                        <textarea class="comm_input form-control" name="txtDescription" cols="60" rows="6"><?php echo  htmlentities(stripslashes($txtDescription)) ?></textarea>
                                    </div>
                                    <?php if($type!='wish') {  ?>   
                                    <div class="row main_form_inner">
                                        <label><?php echo TEXT_YEAR; ?></label>
                                        <select name="txtYear" id="txtYear" class="comm_input form-control">
                                            <option value=""><?php echo TEXT_SELECT_YEAR; ?></option>
                                            <?php
                                            for ($i = 1900; $i <= 2050; $i++) {
                                                $showCheckd = '';
                                                if ($i == $txtYear || $i == date('Y')) {
                                                    $showCheckd = 'selected';
                                                }
                                                echo '<option value="' . $i . '" ' . $showCheckd . '>' . $i . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php
								    //checking point stats
                                    if ($EnablePoint == '1' || $EnablePoint == '2') {
                                        $checkCommission = ($EnablePoint=='2') ? 'showCommision(this.value);' : '';
                                        ?>
                                        <div class="row main_form_inner">
                                            <label><?php echo POINT_NAME. ' ('.TEXT_PER_ITEM.')'; if($EnablePoint==1){?> <span class="warning">*</span><?php }?></label>
                                            <input type="text" class="comm_input form-control jQNumericOnly" name="txtPoint"  id="txtPoint" onChange="check_float_value(this);<?php echo $checkCommission; ?>" size="5" maxlength="10" value="<?php echo htmlentities(stripslashes($txtPoint)) ?>" />
                                        </div>
                                        <?php
                                    }
									//checking point stats
                                    if ($EnablePoint == '0' || $EnablePoint == '2') {
                                        if (in_array('Sell', ModuleAcess($toplinks))) {
                                            ?>
                                            <div class="row main_form_inner">
                                                <label><?php echo TEXT_PRICE. ' ('.TEXT_PER_ITEM.')'; ?>&nbsp;( <?php echo CURRENCY_CODE; ?> ) &nbsp; <span class="warning">*</span></label>

                                                <input type="text" class="comm_input form-control jQNumericOnly" name="txtValue"  id="txtValue"
                                                <?php
                                                if ($type != "sale") {
                                                    echo "onChange='check_float_value(this)'";
                                                }
                                                else {
                                                //checking point enable in website
                                                    if (DisplayLookUp('Listing Type') == '1') {
                                                        echo "onChange='checkCommission(this);showCommision(this.value);' onBlur='checkCommission(this);showCommision(this.value);'";
                                                    }
                                                }
                                                ?>
                                                size="5" maxlength="10" value="<?php echo  htmlentities(stripslashes($txtValue)) ?>" />
                                                <?php
                                                if (DisplayLookUp('Enable Escrow') == 'Yes' && $_REQUEST['type']=='sale') {
                                                    echo '&nbsp;<a href="javascript:calculator();">'.LINK_ESCROW_FEES_CALCULATOR.'</a>';
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
									}//end point check if

                                } // Commented wish item price , year , points    

                                if ($type != "wish") {
									//checking point stats
                                    if ($EnablePoint == '0' || $EnablePoint == '2') {
                                        if (in_array('Sell', ModuleAcess($toplinks))) {
                                            ?>
                                            <div class="row main_form_inner">
                                                <label><?php echo TEXT_SHIPPING_CHARGE; ?><!--<span class="warning">*</span>-->&nbsp;( <?php echo CURRENCY_CODE; ?> ) &nbsp; </label>

                                                <input type="text" class="comm_input form-control jQNumericOnly"  id="txShipping" onChange="javascript:check_float_value(this);"  name="txtShipping" size="5" maxlength="10" value="<?php echo  htmlentities(stripslashes($txtShipping)) ?>"/>
                                            </div>
                                            <?php
                                        }
                                    }//end point check if
                                    ?>
                                    <div class="row main_form_inner">
                                        <input type="hidden" id="cropButtonClicked" value="0" />
                                        <label><?php echo TEXT_PICTURE_IF_ANY; ?></label>
                                        <div style="clear: both; position: relative;">
                                            <div style="float: left;" id="mulitplefileuploader" class="prduct_images"><?php echo TEXT_CHOOSE_IMAGE; ?></div>
                                            <input type="hidden" name="pType" id="pType" value="product" />

                                            <div class="col-lg-8 col-sm-12 col-md-12 col-xs-12 row">
                                                <div class="warning"><?php echo str_replace('{max_images}',DisplayLookUp('MaxOfImages'),TEXT_MAX_NO_IMAGES); ?></div>
                                                <div class="warning"><?php echo 'Image size should be greater than'; ?> 250px X 250px</div>
                                                <div class="warning"><?php echo str_replace('{MaxUploadSize}',$MaxUploadSize,TEXT_MAX_UPLOAD_SIZE_IS); ?></div>
                                                <div class="warning"><?php echo ERROR_FILE_REQUIRED_FORMAT; ?></div>
                                            </div>
                                        </div>	

                                        <div id="jqCropImageDiv" class="cropselection_popup" style="display: none;">
                                            <span><a href="#" class="banner_close jqCloseCropImage"></span></a>
                                            <div class="imgcrop_btncontainer">
                                                <input type="button" value="<?php echo IMAGE_CROP_VALUE;?>" class="button left jqCropImage"  />
                                                <div class="clear"></div>
                                            </div>
                                            <input type="hidden" id="x" name="x" />
                                            <input type="hidden" id="y" name="y" />
                                            <input type="hidden" id="w" name="w" />
                                            <input type="hidden" id="h" name="h" />
                                            <input type="hidden" id="bannerName" name="bannerName" value="<?php echo $_POST['bannerName'];?>" />
                                            <input type="hidden" id="txtPicture" name="txtPicture" value="<?php echo $_POST['txtPicture'];?>" />
                                            <input type="hidden" id="txtPictureSmall" name="txtPictureSmall" value="<?php echo $_POST['txtPictureSmall'];?>" />

                                            <input type="hidden" id="bannerFileId" name="bannerFileId" />
                                            <div class="notificationdivstyle1"><?php echo IMAGE_SELCTION_NOTE;?></div>
                                            <div class="jqImageHoldingDiv"></div>
                                        </div>

                                        <div class="row">
                                            <div class="row-rightcol">
                                                <p class="text-style13" id="jqUploadFileStartTxt" style="display: none;">
                                                    <img src="images/loading.gif" id="uploaded_banner_image" class="jqAjaxLoaderImage">
                                                </p>
                                                <p class="error_msg" style="color:red; font-size: 13px; font-weight: bold;" id="error_product_image"></p>
                                            </div>

                                            <div id="status"></div>
                                            <div class="clear"></div>
                                        </div>

                                        <div id="JQUploadedImageDiv">
                                            <div class="jqproductImageDiv" id="jqproductImageDiv">
                                                <?php if($_POST['bannerName']){ ?>
                                                <img src="pics/medium_<?php echo $_POST['bannerName'];?>" border="0" width="183" height="191">
                                                <?php } ?>
                                            </div>
                                        </div>


                                        <label style="display:none;"><?php echo TEXT_DESCRIPTION; ?></label>
                                        <textarea style="display:none;" name="txtImgDes[0]" class="comm_input form-control" rows="6" cols="60" ></textarea><br><br>
                                        <?php
                                        $cnt =0;
                                        $maxImages=DisplayLookUp('MaxOfImages');
                                        for ($iCount =1; $iCount <=$maxImages; $iCount++) {

                                            echo '<div id="pic'.$iCount.'" style="display:none;">';
                                            ?>
                                            <div class="jqMoreImageContainer" style="padding:5px;border:1px solid #D3D1D1;margin:10px 142px 10px 1px;float:left;">
                                                <div class="fileuploader" data="<?php echo $iCount; ?>" id="multi_file_upload_<?php echo $iCount?>" ><b><?php echo TEXT_CHOOSE_IMAGE; ?> <?php echo $iCount ; ?></b>
                                                </div>

                                                <div class="jqproductImageMoreDiv" id="jqproductImageMoreDiv_<?php echo $iCount;?>">
                                                    <?php
                                                    if($_POST['productMoreImage']){
                                                        $pImage = $_POST['productMoreImage'][$iCount];
                                                        if ($pImage!=""){
                                                            ?>
                                                            <img src="pics/medium_<?php echo $pImage;?>" border="0" width= "187" height="170" />

                                                            <?php  
                                                        } 
                                                    }?>
                                                </div>

                                            </div>

                                            <div id="jqCropMoreImageDiv_<?php echo $iCount;?>" class="cropselection_popup" style="display: none;">
                                                <span><a href="#" class="banner_close jqCloseCropMoreImage"></span></a>
                                                <div class="imgcrop_btncontainer">
                                                    <input type="button" value="<?php echo IMAGE_CROP_VALUE;?>" class="button left jqCropMoreImage" />
                                                    <div class="clear"></div>
                                                </div>
                                                <input type="hidden" id="x" name="x" />
                                                <input type="hidden" id="y" name="y" />
                                                <input type="hidden" id="w" name="w" />
                                                <input type="hidden" id="h" name="h" />
                                                <input type="hidden" id="productMoreImage_<?php echo $iCount;?>" name="productMoreImage[]" value="<?php echo $_POST['productMoreImage'][$iCount];?>" />
                                                <input type="hidden" id="productMoreImageId_<?php echo $iCount;?>" name="productMoreImageId[]" value="<?php echo $_POST['productMoreImageId'][$iCount];?>" />
                                                <div class="notificationdivstyle1"><?php echo IMAGE_SELCTION_NOTE;?></div>
                                                <div class="jqImageHoldingDiv"></div>
                                            </div>                               


                                            <?php 
                                            $newCount   =   $cnt + 1;
                                            echo '<label style="display:none;">'.TEXT_DESCRIPTION.'   '.$newCount.'</label>
                                            <textarea style="display:none;" name="txtImgDes[' . $iCount . ']" class="form-control" rows="6" cols="60" ></textarea><br><br></div>';
                                            $cnt++;
                                        }

                                        $max_additional_imgs = DisplayLookUp('MaxOfImages');
                                        ?>

                                        <input type="hidden" name="TOTAL_FILE" value="0" />
                                        <input type="hidden" name="MAX_NO_IMAGES" value="<?php echo $max_additional_imgs; ?>" />
                                        <?php
                                        if ($max_additional_imgs > '1') {
                                            ?>
                                            <input style="float: left;" type='button' value="<?php echo BUTTON_UPLOAD_MORE; ?>" onClick="add_more_upload();" class="submit" name="upload_more" />
                                            <?php 
                                        }  ?>
                                    </div> 
                                    <?php 
                                }

                                if($type == "wish"){ ?>
                                <div class="row main_form_inner">

                                    <input type="hidden" id="cropButtonClicked" value="0" />
                                    <label><?php echo TEXT_PICTURE_IF_ANY; ?></label>
                                    <div style="clear: both; position: relative;">
                                        <div style="float: left;" id="mulitplefileuploader" class="prduct_images"><?php echo TEXT_CHOOSE_IMAGE; ?></div>
                                        <input type="hidden" name="pType" id="pType" value="product" />

                                        <div class="col-lg-8 col-sm-12 col-md-12 col-xs-12 row">
                                            <div class="warning"><?php echo str_replace('{max_images}',DisplayLookUp('MaxOfImages'),TEXT_MAX_NO_IMAGES); ?></div>
                                            <!--<br><span class="warning"><?php //echo TEXT_IMAGE_SIZE_SHOULD_BE; ?> 393 x 269</span>-->
                                            <div class="warning"><?php echo str_replace('{MaxUploadSize}',$MaxUploadSize,TEXT_MAX_UPLOAD_SIZE_IS); ?></div>
                                            <div class="warning"><?php echo ERROR_FILE_REQUIRED_FORMAT; ?></div>
                                        </div>
                                    </div>	
                                    <div id="jqCropImageDiv" class="cropselection_popup" style="display: none;">
                                        <span>

                                            <a href="#" class="banner_close jqCloseCropImage"></a>
                                        </span>
                                        <div class="imgcrop_btncontainer">
                                            <input type="button" value="<?php echo IMAGE_CROP_VALUE;?>" class="button left jqCropImage"  />
                                            <div class="clear"></div>
                                        </div>
                                        <input type="hidden" id="x" name="x" />
                                        <input type="hidden" id="y" name="y" />
                                        <input type="hidden" id="w" name="w" />
                                        <input type="hidden" id="h" name="h" />
                                        <input type="hidden" id="bannerName" name="bannerName" value="<?php echo $_POST['bannerName'];?>" />
                                        <input type="hidden" id="txtPicture" name="txtPicture" value="<?php echo $_POST['txtPicture'];?>" />
                                        <input type="hidden" id="txtPictureSmall" name="txtPictureSmall" value="<?php echo $_POST['txtPictureSmall'];?>" />

                                        <input type="hidden" id="bannerFileId" name="bannerFileId" />
                                        <div class="notificationdivstyle1"><?php echo IMAGE_SELCTION_NOTE;?></div>
                                        <div class="jqImageHoldingDiv"></div>
                                    </div>

                                    <div class="row">
                                        <div class="row-rightcol">
                                            <p class="text-style13" id="jqUploadFileStartTxt" style="display: none;">
                                                <img src="images/loading.gif" id="uploaded_banner_image" class="jqAjaxLoaderImage">
                                            </p>
                                            <p class="error_msg" style="color:red; font-size: 13px; font-weight: bold;" id="error_product_image"></p>
                                        </div>

                                        <div id="status"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div id="JQUploadedImageDiv">
                                        <div class="jqproductImageDiv" id="jqproductImageDiv">
                                            <?php if($_POST['bannerName']){ ?>
                                            <img src="pics/medium_<?php echo $_POST['bannerName'];?>" border="0" width="183" height="191">
                                            <?php } ?>
                                        </div>
                                    </div>
									<!--<div class="col-lg-4 col-sm-12 col-md-12 col-xs-12 no_padding">
										<div class="full_width">
											<div style="position:relative;">
												<a href="#" onClick="javascript:return false;" class="small_btt"><?php echo TEXT_CHOOSE_IMAGE; ?></a>
												<input type="file" name="txtPicture[0]" id="txtPicture_0" class=" " size="1" onChange="javascript:document.getElementById('file_name_0').innerHTML=this.value;" style="position:absolute;top:0px;left:-30px;opacity:0;filter:alpha(opacity=0)" />
												<span id="file_name_0" class="name_txt"></span>
											</div>
										</div>
									</div>
									<div class="col-lg-8 col-sm-12 col-md-12 col-xs-12">
										<div class="warning"><?php //echo str_replace('{MaxUploadSize}',$MaxUploadSize,TEXT_MAX_UPLOAD_SIZE_IS); ?></div>
										<div class="warning"><?php //echo ERROR_FILE_REQUIRED_FORMAT; ?></div>
									</div>-->
								</div>
								<?php } ?>

                                <?php
                                if ($type == "sale") {
                                    ?>
                                    <div class="row main_form_inner">
                                        <label><?php echo TEXT_QUANTITY; ?> <span class="warning">*</span></label>
                                        <input type="text" class="comm_input form-control jQNumericOnly"  id="txtQuantity" onChange="check_numeric_value(this)"  name="txtQuantity" size="4" maxlength="5" value="<?php echo  htmlentities(stripslashes($txtQuantity)) ?>"
                                        <?php
                                        if (DisplayLookUp('Listing Type') == '1') {
                                            echo "onChange='showCommision(document.getElementById(\"txtValue\").value);' onBlur='showCommision(document.getElementById(\"txtValue\").value);'";
                                        }
                                        ?> />
                                    </div>
                                    <div class="row main_form_inner">
                                        <input  type = "checkbox" name="chkFeatured" <?php
                                        if ($chkFeatured == "featured") {
                                            echo "CHECKED";
                                        }
                                        ?> value="featured">
                                        <label style="width:80% "><?php echo TEXT_FEATURED; ?></label>
                                    </div>
                                    <div class="row main_form_inner">
                                        <h4><?php echo TEXT_PRICE_FOR_POSTING; ?></h4>
                                        <?php
                                        //checking admin enabled listing fee
                                        if(DisplayLookUp('Listing Type')=='1') {//different range

                                            echo '<h5>1) '.TEXT_LISTING_FEE_RANGE.'</h5><div class="row">
                                            
                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">'.TEXT_PRICE_RANGE.'</div>
                                            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">'.TEXT_COMMISSION.'</div>';

                                            $sqlRange=mysqli_query($conn, "SELECT * FROM ".TABLEPREFIX."listingfee WHERE vActive='1' ORDER BY nLPosition ASC") or die(mysqli_error($conn));
                                            if(mysqli_num_rows($sqlRange)>0)
                                            {
                                                while($arrRange=mysqli_fetch_array($sqlRange))
                                                {
                                                    if($arrRange['nTo']!="0"){
                                                        echo 
                                                        '<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">' . CURRENCY_CODE . $arrRange['nFrom'] . '&nbsp;-&nbsp;' . CURRENCY_CODE . $arrRange['nTo'] . '</div>
                                                        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">' . $arrRange['nPrice'] . '%</div>';
                                                    } else if($arrRange['above']){
                                                        echo '<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">Above ' .CURRENCY_CODE.$arrRange['above'] .'</div>
                                                        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">' . $arrRange['nPrice'] . '%</div>';
                                                    }
                                                }
                                            }
                                            echo '</div>';
                                            $showCnt='2';
                                        }
                                        else
                                        {
                                            $showCnt='1';
                                        }
                                        ?>
                                        <?php
                                        $featu_content = file_get_contents("./languages/".$_SESSION['lang_folder']."/feature_an_item.html"); 
                                        $featu_content = str_replace('{showCnt}', $showCnt, $featu_content);
                                        $featu_content = str_replace('{SITE_NAME}', SITE_NAME, $featu_content);
                                        $featu_content = str_replace('{CURRENCY_CODE}', CURRENCY_CODE, $featu_content);
                                        $featu_content = str_replace('{fea}', $fea, $featu_content);
                                        echo $featu_content;
                                        ?>
                                    </div>
                                    <div class="row main_form_inner">
                                        <label><?php if(DisplayLookUp('Listing Type')=='1'){ echo TEXT_COMMISSION_FOR_POSTING; } ?></label>

                                        <?php if(DisplayLookUp('Listing Type')=='1'){ ?>
                                        <div style="text-align: center; margin-top: 7px; float: left; width: 3%; margin-left: 6%;">
                                            <?php echo CURRENCY_CODE;  ?>
                                        </div>
                                        <?php } ?>
                                        <input type="<?php if(DisplayLookUp('Listing Type')=='1'){ echo 'text'; } else { echo 'hidden'; } ?>" class="comm_input form-control" style="width:90%; float:right; "  id="txtCommission"   name="txtCommission" size="4" maxlength="5" value="<?php echo  $txtCommission ?>" readonly/>
                                        <input type="hidden" id="txtFeatured" name="txtFeatured" value="">
                                    </div>
                                    <?php } ?>
                                    <div class="row main_form_inner">
                                        <label>
                                            <input type="submit" name="btnSubmit" value="<?php echo BUTTON_ADD_ITEM; ?>" class="subm_btt">
                                        </label>
                                    </div>
                                    <?php
                                }//end vulnerabilitie check if
                            }
                            ?>
                        </form>
                    </div>
                    <div class="col-lg-2 col-sm-12 col-md-1 col-xs-2"></div>						
                </div>					
                <div class="subbanner">
                    <?php include('./includes/sub_banners.php'); ?>
                </div>
            </div>
        </div>  
    </div>
</div>
<script>
    setTimeout(function(){ setcatValue(); }, 3000);
     
</script>    
<?php require_once("./includes/footer.php"); ?>
