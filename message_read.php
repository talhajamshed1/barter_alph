<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                         |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		                  |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
include ("./includes/config.php");
include ("./includes/session.php");
include ("./includes/functions.php");
include("./languages/" . $_SESSION['lang_folder'] . "/user.php"); //language file
$message = "";
include ("./includes/session_check.php");
include_once('./includes/gpc_map.php');

include_once('./includes/title.php');

$from = ($_GET['from'] != '') ? $_GET['from'] : 'read';
$getId = ($_GET['mid'] != '') ? $_GET['mid'] : '0';

if ($from != 'sent') {
    //update mail read status
    mysqli_query($conn, "update " . TABLEPREFIX . "messages set vStatus='Y' WHERE nMsgId='" . $getId . "'") or die(mysqli_error($conn));
    $sql = mysqli_query($conn, "SELECT *  FROM " . TABLEPREFIX . "messages WHERE nMsgId='" . $getId . "' and vToDel='N'") or die(mysqli_error($conn));
}//end if
else {
    $sql = mysqli_query($conn, "SELECT *  FROM " . TABLEPREFIX . "messages WHERE nMsgId='" . $getId . "' and vFromDel='N'") or die(mysqli_error($conn));
}//end else

$checkCount = mysqli_num_rows($sql);
if (@mysqli_num_rows($sql) > 0) {
    $title = mysqli_result($sql, 0, 'vTitle');
    $msg = mysqli_result($sql, 0, 'vMsg');
    $date = mysqli_result($sql, 0, 'nDate');
    $fromId = mysqli_result($sql, 0, 'nFromUserId');
    //fetching from user name
    $condition = "where nUserId='" . mysqli_result($sql, 0, 'nFromUserId') . "'";
    $fromUser = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');

    //fetching from user name
    $condition = "where nUserId='" . mysqli_result($sql, 0, 'nToUserId') . "'";
    $toUser = fetchSingleValue(select_rows(TABLEPREFIX . 'users', 'vLoginName', $condition), 'vLoginName');
}//end if
//send mesage
if (isset($_POST['btnGo']) && $_POST['btnGo'] != '') {
    if (function_exists('get_magic_quotes_gpc')) {
        $txtTitle = stripslashes($_POST['txtTitle']);
        $txtMsg = stripslashes($_POST['txtMsg']);
    }//end if
    else {
        $txtTitle = $_POST['txtTitle'];
        $txtMsg = $_POST['txtMsg'];
    }//end else

    $pquery = ($_POST['qyery'] != '') ? $pquery = '?' . $_POST['qyery'] : $pquery = '';

    //insert into tbl message
    mysqli_query($conn, "insert into " . TABLEPREFIX . "messages (nToUserId,nFromUserId,vTitle,vMsg,vStatus,nDate)
						values ('" . $_POST['ToId'] . "','" . $_SESSION["guserid"] . "','" . addslashes($txtTitle) . "','" . addslashes($txtMsg) . "',
						'N',now())") or die(mysqli_error($conn));
    $_SESSION['succ_msg'] = MESSAGE_MESSAGE_SENT_SUCCESSFULLY;

    header('location:./message.php' . $pquery);
    exit();
}//end if

if (isset($_GET['mode']) && $_GET['mode'] == 'del') {
    if ($from != 'sent') {
        mysqli_query($conn, "update " . TABLEPREFIX . "messages set vToDel='Y' WHERE nMsgId='" . $_GET['id'] . "'") or die(mysqli_error($conn));

        /*$nextMailId = mysqli_query($conn, "Select min(nMsgId) as msgid from " . TABLEPREFIX . "messages where nToUserId='" . $_SESSION["guserid"] . "'
									and vToDel='N' order by nDate Desc") or die(mysqli_error($conn));
        if (mysqli_num_rows($nextMailId) > 0) {
            $msgId = mysqli_result($nextMailId, 0, 'msgid');
            $_SESSION['succ_msg'] = MESSAGE_MESSAGE_DELETED_SUCCESSFULLY;
            header('location:./message_read.php?mid=' . $msgId);
        }//end if
        else {*/
        $_SESSION['succ_msg'] = MESSAGE_MESSAGE_DELETED_SUCCESSFULLY;//.' '.MESSAGE_NO_MORE_MESSAGES;
        header('location:./message.php');
        //}//end else
        exit();
    }//end if
    else {
        mysqli_query($conn, "update " . TABLEPREFIX . "messages set vFromDel='Y' WHERE nMsgId='" . $_GET['id'] . "'") or die(mysqli_error($conn));

        /*$nextMailId = mysqli_query($conn, "Select min(nMsgId) as msgid from " . TABLEPREFIX . "messages where nFromUserId='" . $_SESSION["guserid"] . "'
										and vFromDel='N' order by nDate Desc") or die(mysqli_error($conn));
        if (mysqli_num_rows($nextMailId) > 0) {
            $msgId = mysqli_result($nextMailId, 0, 'msgid');
            $_SESSION['succ_msg'] = MESSAGE_MESSAGE_DELETED_SUCCESSFULLY;
            header('location:./message_read.php?mid=' . $msgId . '&from=sent');
        }//end if
        else {*/
        $_SESSION['succ_msg'] = MESSAGE_MESSAGE_DELETED_SUCCESSFULLY;//.' '.MESSAGE_NO_MORE_MESSAGES;
        header('location:./message_sent.php');
        //}//end else
        exit();
    }//end else
    exit();
}//end if
?>
<script language="javascript" type="text/javascript">
    function showMsg()
    {
        document.getElementById('msgShow').style.display='';
        document.frmMsg.txtMsg.focus();
    }//end function

    function hideMsg()
    {
        document.getElementById('msgShow').style.display='none';
    }//end function

    function ValidateMsg()
    {
        var s=document.frmMsg;
        if(trim(s.txtTitle.value)=='')
        {
            alert("<?php echo ERROR_SUBJECT_EMPTY; ?>");
            s.txtTitle.focus();
            return false;
        }//end if
        if(trim(s.txtMsg.value)=='')
        {
            alert("<?php echo ERROR_MESSAGE_EMPTY; ?>");
            s.txtMsg.focus();
            return false;
        }//end if
        return true;
    }//end function
</script>
<script type="text/javascript" src="./fancybox/jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="./fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="./fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
    jQuery.noConflict();

    var $j = jQuery
    $j(document).ready(function() {
        
        /*
         *   Examples - various
         */
         if(jQuery(".various1").length >0){
        $j(".various1").fancybox({
            'titlePosition'		: 'inside',
            'transitionIn'		: 'none',
            'transitionOut'		: 'none'
        });
         }
    });
</script>
<body onLoad="timersOne();">
<?php include_once('./includes/top_header.php'); ?>
  <div class="homepage_contentsec">
    <div class="container">
      <div class="row">
        <div class="col-lg-3">
          <?php include_once ("./includes/usermenu.php"); ?>
        </div>
        <div class="col-lg-9">
          <div class="row">
            <div class="col-lg-12 col-md-12 ">
              <div class="innersubheader2"><h3><?php echo HEADING_READ_MESSAGE; ?></h3></div>
            </div>
            
          </div>
          <div class="row">
            <div class="col-lg-12">
            	<div class="space2">
                	<a href="javascript:history.go(-1);" class="backbtn right"> <span class=" glyphicon glyphicon-circle-arrow-left"></span> <?php echo LINK_BACK; ?></a> 
                    <div class="clear"></div>
                </div>
              <div class="table-responsive">
                <table width="100%"  border="0" cellspacing="1" cellpadding="4" class="table table-bordered">
                  <?php
                                                                    if (isset($_SESSION['succ_msg']) && $_SESSION['succ_msg'] != '') {
                                                                        ?>
                  <tr align="center" bgcolor="#FFFFFF">
                    <td colspan="2" class="success"><b><?php echo $_SESSION['succ_msg']; ?></b></td>
                  </tr>
                  <?php
                                                                        unset($_SESSION['succ_msg']);
                                                                    }
                                                                    //list if records exists
                                                                    if ($checkCount > 0) {
                                                                        if ($from != 'sent') {
                                                                            ?>
                  <tr align="center" bgcolor="#FFFFFF">
                    <td width="39%" align="left"><strong><?php echo TEXT_FROM; ?></strong></td>
                    <td width="61%" align="left"><?php echo $fromUser; ?></td>
                  </tr>
                  <?php
                                                                        }//end if
                                                                        else {
                                                                            ?>
                  <tr align="center" bgcolor="#FFFFFF">
                    <td width="39%" align="left"><strong><?php echo TEXT_TO; ?></strong></td>
                    <td width="61%" align="left"><?php echo $toUser; ?></td>
                  </tr>
                  <?php
                                                                        }//end else
                                                                        ?>
                  <tr bgcolor="#FFFFFF">
                    <td align="left"><strong><?php echo TEXT_DATE; ?></strong></td>
                    <td align="left"><?php echo date('m/d/Y', strtotime($date)); ?></td>
                  </tr>
                  <tr bgcolor="#FFFFFF">
                    <td align="left"><strong><?php echo TEXT_SUBJECT; ?></strong></td>
                    <td align="left"><?php echo $title; ?></td>
                  </tr>
                  <tr bgcolor="#FFFFFF">
                    <td align="left" valign="top"><strong><?php echo TEXT_MESSAGE; ?></strong></td>
                    <td align="left" valign="top"><?php echo nl2br($msg); ?></td>
                  </tr>
                  <tr bgcolor="#FFFFFF">
                    <td colspan="2" align="right">
                      
                      <a onclick="javascript:if(!confirm('<?php echo MESSAGE_ARE_YOUR_SURE_TO_DELETE; ?>')) return false;" href="message_read.php?id=<?php echo $getId; ?>&mode=del&from=<?php echo $from; ?>" class="backbtn right" ><?php echo LINK_DELETE; ?></a>
                      
                      <?php
						if ($from != 'sent') {
							?>
                      <!--<a class="various1 actionbtn right" style="margin-right:8px;"  href="#inline1" onClick="showMsg();"></a>-->
					  
					  <a href="#modal" class="actionbtn right" style="margin-right:8px;"><?php echo LINK_REPLY; ?></a>
					  
                      <?php }//end if  ?>
                      </td>
                  </tr>
               
                </table>

				<div class="remodal" data-remodal-id="modal">
					<?php echo '<form action="" method="post" name="frmMsg"  onSubmit="return ValidateMsg();">'; ?>
						<input type="hidden" name="qyery" value="<?php echo $_SERVER['QUERY_STRING']; ?>">
						<input type="hidden" name="ToId" value="<?php echo $fromId; ?>">
						<?php
						$txtTitle = TEXT_RE.' ' . $title;
						$txtMsg = '----- '.TEXT_ORIGINAL_MESSAGE.' -----' . $msg;
						?>
						<div style="width:90%; height:auto; padding:5px 5px 0px 5px; margin:0 5%;">
                                                <div class="full_width main_form_inner"><label><?php echo REPLY_MESSAGE_TEXT; ?></label></div>
						<div class="full_width main_form_outer" style="padding:10px; border:0px;  ">
						<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
							<div class="popup_outer">
								<div class="popup_inner">
									<label class="factlisting"><?php echo TEXT_SUBJECT; ?> <span class="warning">*</span></label>
									<input type="text" name="txtTitle" class="textbox_contact_flsd form-control" value="<?php echo $txtTitle; ?>" size="50">
								</div>
								<div class="popup_inner">
									<label class="factlisting"><?php echo TEXT_MESSAGE; ?> <span class="warning">*</span></label>
									<textarea name="txtMsg" cols="55" rows="8" class="textbox_contact2 form-control" id="txtMsgF"><?php echo $txtMsg; ?></textarea>
								</div>
								<div class="popup_inner">
									<label><input type="submit" name="btnGo" value="<?php echo BUTTON_SEND_MESSAGE; ?>" class="submit"></label>
								</div>
							</div>
						</div>
						<div class="clear"></div>
						</div>
						</div>
																				
					<?php echo '</form>'; ?>
					</div>
					
					 
                  <?php
				}//end if
				?>
              </div>
            </div>
          </div>		  
          <div class="full-width subbanner">
            <div>
              <?php include('./includes/sub_banners.php'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php require_once("./includes/footer.php"); ?>

<link rel="stylesheet" href="styles/jquery.remodal.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="../libs/jquery/dist/jquery.min.js"><\/script>')</script>
<script src="js/jquery.remodal.js"></script>

<!-- Events -->
<script>
  $(document).on("open", ".remodal", function () {
    console.log("open");
  });

  $(document).on("opened", ".remodal", function () {
    console.log("opened");
  });

  $(document).on("close", ".remodal", function (e) {
    console.log('close' + (e.reason ? ", reason: " + e.reason : ''));
  });

  $(document).on("closed", ".remodal", function (e) {
    console.log('closed' + (e.reason ? ', reason: ' + e.reason : ''));
  });

  $(document).on("confirm", ".remodal", function () {
    console.log("confirm");
  });

  $(document).on("cancel", ".remodal", function () {
    console.log("cancel");
  });

//  You can open or close it like this:
//  $(function () {
//    var inst = $.remodal.lookup[$("[data-remodal-id=modal]"").data("remodal")];
//    inst.open();
//    inst.close();
//  });

  //  Or init in this way:
  var inst = $("[data-remodal-id=modal2]").remodal();
  //  inst.open();
  
</script>
