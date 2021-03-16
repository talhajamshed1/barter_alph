<?php
include('chat_config.php');
//session_start();
include ("../includes/session.php");
include ("../includes/functions.php");
include ("../includes/session_check.php");

$condition="where nUserId='".$_GET['requestid']."'";
$chatWith=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',$condition),'vLoginName');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Chat with <?php echo ucfirst($chatWith);?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="jquery.js"></script>
<!--  <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script> -->
<script type="text/javascript" src="ajax.js"></script>
<script type="text/javascript" src="functions.js"></script>
<script type="text/javascript">
</script>
<link href="<?php echo $stylesfolder;?>/<?php echo $sitestyle;?>" rel="stylesheet" type="text/css">
<link href="styles.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
<!--
if (document.layers)
  document.captureEvents(Event.KEYDOWN);
  document.onkeydown =
    function (evt) { 
      var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
      if (keyCode == 13)
       {
           setTimeout(function(){ send_message('output_div','text_content','<?php echo $_GET['requestid'];?>'); }, 100);
         return false;
       }
    }
//-->
</script>
</head>
<body onload="get_message('output_div','<?php echo $_GET['requestid'];?>');">
<center>
 <div class="body-holder">
    <div class="window">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
		  <td><img src='../icons/header_01.jpg' height="34"></td>
		  <td background="../icons/header_02.jpg" >
			<img src="../icons/1.gif" border="0" >
		  </td>
		  <td background="../icons/header_02.jpg" width="100%">
			<div id="window_title">&nbsp;<?php echo LINK_CHAT_WITH; ?> <?php echo ucfirst($chatWith);?></div>
		  </td>
		  <td background="../icons/header_02.jpg">
			<img src="../icons/tipclose.gif" width="20" height="15" onClick="javascript:window.close();">
		  </td>
		  <td><img src='../icons/header_03.jpg'></td>
		</tr>
		</table>
	</div>
 	 <div id="tools">
	   <?php
	   $res_smilies = mysqli_query($conn, "select * from ".TABLEPREFIX."smilies") or die(mysqli_error($conn));
	   $count = 0;
	   echo "<table cellspacing=1 border=0 cellpadding=1>\n<tr>";
	   	while($row_smilies = mysqli_fetch_array($res_smilies))
		{
			if($count == 4)
			{
				echo "</tr><tr>";
				$count=0;
			}
			echo "\n<td align='center' width='35'>\n <a href='#' alt='".$row_smilies['vImgCode']."' onClick=\"add_code('".($row_smilies['vImgCode'])."');\" border=\"0\"><img src=\"../icons/".$row_smilies['nSId'].".gif\" border=\"0\"></a>\n</td>";
			$count ++;
		}
		echo "</table>";
	  ?>
	 </div>
     <div class="main_container">
		<div class="output-div-container">
		   <div id="output_div" onClick="show_smilies();">
		   </div>
		</div>
		<!-- <table border="0" width="504" class="tool_table">
		 <tr>
		  <td width="20"><a href="#" onClick="show_smilies('tool')"><img src="../icons/1.gif" border="0"></a></td>
	      <td></td>	
		 </tr>
		</table> -->
		<div class="input-div bottom-chat-section">
			<a href="#" onClick="show_smilies('tool')"><img src="../icons/1.gif" border="0"></a>
			<div class="chat-bottom-tiles">
		   <textarea type="text" id="text_content" onClick="show_smilies();" size="40" placeholder="Type here..."></textarea>

		   <input type="submit" value="<?php echo BUTTON_SEND; ?>" onClick="send_message('output_div','text_content','<?php echo $_GET['requestid'];?>');">
		</div>
		</div>
	</div>
	</div>
 </center>
</body>
</html>
