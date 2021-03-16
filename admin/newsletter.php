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
$PGTITLE='newsletter';

if($_POST["btnSubmit"] =="Send Letter") {
    $txtMatter = $_POST["txtMatter"];
    $langid = $_POST["lang"];

        /*
        * Fetch user language details
        */

        $lanSql = "SELECT lang_name,folder_name FROM ".TABLEPREFIX."lang WHERE lang_id = '".$langid."'";
        $langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));
        $langRw = mysqli_fetch_array($langRs);

    //mail all the users except the current user  with the alertstatus turned on the swap item details
    $sql = "Select vFirstName,vLoginName,vEmail from ".TABLEPREFIX."users where vNLStatus='Y' and preferred_language = '".$langid."'";
    $result=mysqli_query($conn, $sql) or die(mysqli_error($conn));
    if(mysqli_num_rows($result) > 0) {
        while($row=mysqli_fetch_array($result)) {
            $EMail = stripslashes($row["vEmail"]);
            $Name = stripslashes($row["vLoginName"]);
            $mailcontent1='<table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left" valign="top" class="maintext2">'.$txtMatter.'</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left">Thank You,</td>
                              </tr>
                              <tr bgcolor="#FFFFFF">
                                <td colspan="2" align="left">'.SITE_NAME.' Crew. | <a href="'.SITE_URL.'" target="_blank">'.SITE_URL.'</a></td>
                              </tr>
                            </table>';

            $StyleContent=MailStyle($sitestyle,SITE_URL);
            $subject = "Newsletter from ".SITE_NAME."!";

            //readf file n replace
            $arrSearch	= array("{TITLE}","{STYLE}","{SITE-URL}","{NAME}","{CONTENT}","{SITE-LOGO}","{DATE}","{SITE-NAME}","{HEAD}");
            $arrReplace	= array('<b>NewsLetter From '.SITE_TITLE.'</b>',$StyleContent,SITE_URL,$Name,$mailcontent1,$logourl,date('F d, Y'),SITE_NAME,$subject);
            $msgBody    = file_get_contents('../languages/'.$langRw["folder_name"].'/mail.html');
            $msgBody    = str_replace($arrSearch,$arrReplace,$msgBody);

           

            send_mail($EMail,$subject,$msgBody,SITE_EMAIL,'Admin');
            $message="Newsletter successfully sent!";
        }//end while
        
    }//end if
    else {
        $message="Cannot send newsletter as there are no subscribers currently.";
    }//end else
}//end first if



/*
* Fetch user language details
*/

$lanSql = "SELECT lang_id,lang_name,folder_name FROM ".TABLEPREFIX."lang";
$langRs = mysqli_query($conn, $lanSql) or die(mysqli_error($conn));

?>
<?php //include "editor.php";//editor ?>
<script type="text/javascript" src="<?php echo SITE_URL?>/ckeditor/ckeditor.js"></script>
<script language="javascript" type="text/javascript">
    
    window.onload = function()
	{
		CKEDITOR.replace( 'editor1' );
	};
        
    function validatefrmNewsletterForm()
    {
        if ( CKEDITOR.instances.editor1.getData() == '' ){
                        alert("Please enter matter for the newsletter!");
                        CKEDITOR.instances.editor1.focus();
                        return false;
                    }
                    
         return true;           
        /*var frm = document.frmNewsletter;
        updateRTEs('txtMatter');
        if(frm.txtMatter.value == "")
        {
            alert("Please enter matter for the newsletter");
            return false;
        }//end if
        else
        {
            return true;
        }*///end else
    }//end function

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
                    <td width="100%" class="heading_admn boldtextblack" align="left">NewsLetter</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="10" class="admin_tble_2">
                <tr>
                    <td align="left" valign="top" class="noborderbottm">
                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td bgcolor="#ffffff" class="noborderbottm"><table width="100%"  border="0" cellspacing="1" cellpadding="4" class="maintext2">
                                        <form name="frmNewsletter" method ="POST" action = "<?php echo $_SERVER['PHP_SELF']?>" onsubmit="return validatefrmNewsletterForm();">
                                            <?php if(isset($message) && $message!='') {
                                                ?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="center" class="warning"><?php echo $message;?></td>
                                            </tr>
                                                <?php  }//end if?>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="left" style="padding-left: 101px;">
                                                    Select Language : &nbsp;
                                                    <select name="lang">
                                                    <?php
                                                        while($langRw = mysqli_fetch_array($langRs)){
                                                    ?>
                                                        <option value="<?php echo $langRw["lang_id"]?>"><?php echo $langRw["lang_name"]?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                    </select> &nbsp;
                                                    <a class="tooltip" style="display:inline;color:black;">
                                                         <span><?php echo "Select the language for newsletter content." ?></span><b><i>?</i></b>
                                                         </a>
                                                </td>
                                                
                                            </tr>
                                            
                                      
											
                                            <tr align="center" bgcolor="#FFFFFF">
                                                <td class="no_padding_td" style="margin:10px 0 0 0; ">
                                                    <!--<script language="JavaScript" type="text/javascript">
                                                        var rte1 = new richTextEditor('txtMatter');
                                                        rte1.html = '<?php //echo rteSafe($content); ?>';
                                                        rte1.width = 600;
                                                        rte1.height = 200;
                                                        //rte1.toolbar1 = false;
                                                        //rte1.toolbar2 = false;
                                                        //rte1.toggleSrc = false;
                                                        rte1.build();
                                                    </script>-->
                                                    <textarea id="editor1" name="txtMatter"></textarea>
                                                </td>
                                            </tr>
                                            <tr bgcolor="#FFFFFF">
                                                <td align="center"><input type="submit" name="btnSubmit" value="Send Letter" class="submit"></td>
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