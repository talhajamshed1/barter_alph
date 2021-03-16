<?php
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
if($_REQUEST['id']!='')
{
    $var_swapid = $_REQUEST['id'];
    
    $var_source = $_REQUEST['source'];
    
 if($var_source=='sa')  {  
     $querypic   = "Select vUrl,vSmlImg from " . TABLEPREFIX . "sale where nSaleId='" . addslashes($var_swapid) . "' ";
  }
 else{
        $querypic   = "Select vUrl,vSmlImg from " . TABLEPREFIX . "swap where nSwapId='" . addslashes($var_swapid) . "' ";
  }
    $result_pic = mysqli_query($conn, $querypic) or die(mysqli_error($conn));
    if (mysqli_num_rows($result_pic) > 0) {
        if ($row = mysqli_fetch_array($result_pic)) {
            $func_pic_url = $row["vUrl"];
            $func_txtSmallImage = $row["vSmlImg"];
        }//end if
        
       if(file_exists($func_pic_url))  {
         @unlink($func_pic_url);
       }
       
        if(file_exists($func_txtSmallImage))  {
         @unlink($func_txtSmallImage);
       }
     
    if($var_source=='sa')  { 
         $sql_update = "UPDATE " . TABLEPREFIX . "sale SET vUrl = '' , vSmlImg = '' where nSaleId='" . addslashes($var_swapid) . "' ";
       mysqli_query($conn, $sql_update);
      
    }else{
        $sql_update = "UPDATE " . TABLEPREFIX . "swap SET vUrl = '' , vSmlImg = '' where nSwapId='" . addslashes($var_swapid) . "' ";
        mysqli_query($conn, $sql_update); 
      
    }    
       
                    
    }//end if
}


if( $_REQUEST['delid']!='' )
{
    //$var_swapid = $_REQUEST['id'];
    $vardel_id  = $_REQUEST['delid']; 
    $querypic   = "Select vImg, vSmlImg from " . TABLEPREFIX . "gallery where nId='" . addslashes($vardel_id) . "' ";
    
    $result_pic = mysqli_query($conn, $querypic) or die(mysqli_error($conn));
    if (mysqli_num_rows($result_pic) > 0) {
        if ($row = mysqli_fetch_array($result_pic)) {
            $func_txtSmallImage = $row["vSmlImg"];
            $vImg   =   $row["vImg"];
        }//end if
        
      
          if(file_exists($func_txtSmallImage))  {
         @unlink($func_txtSmallImage);
       }
       
        if(file_exists($vImg))  {
         @unlink($vImg);
       }
       
       $sql_update = "UPDATE " . TABLEPREFIX . "gallery SET vImg='', vSmlImg = '' where nId='" . addslashes($vardel_id) . "' ";
       mysqli_query($conn, $sql_update);
       
                    
    }//end if
}


?>


