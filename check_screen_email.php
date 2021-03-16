<?php
include ("./includes/config.php");
session_start();
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file

$id = $_GET['q'];
$type = $_GET['type'];

//checking screen name alreadye exists or not
if ($type == 'screen' || $type == 'username' ) { 
  //  if (ereg('[^A-Za-z0-9+_]', $id) && $type == 'username') {
      if (preg_match("/^([^A-Za-z0-9+_]+)/", $id) && $type == 'username') {


        echo '<span class="warning">'.ERROR_USERNAME_NOT_ALPHANUMERIC_SELECT_DIFFERENT.'</span>';
    }else{

    // check if user already exists
    $sqluserexists = mysqli_query($conn, "SELECT vLoginName FROM " . TABLEPREFIX . "users  WHERE vLoginName='" . addslashes($id) . "' AND vDelStatus!='1'") or die(mysqli_error($conn));

    if (mysqli_num_rows($sqluserexists) > 0) {
        echo '<span class="warning">'.ERROR_USERNAME_INUSE_SELECT_DIFFERENT.'</span>';
    }//end if
   }
}//end if
if ($type == 'phone') {
    if (check_phone($id) == false) {
        echo '<span class="warning">'.ERROR_INVALID_PHONE_NOTALLOWED_ALPHABETS_SP_CHARACTERS.'</span>';
    }//end if
}//end if

if ($type == 'fax') {
    if (check_phone($id) == false) {
        echo '<span class="warning">'.ERROR_INVALID_PHONE_NOTALLOWED_ALPHABETS_SP_CHARACTERS.'</span>';
    }//end if
}//end if
?>
