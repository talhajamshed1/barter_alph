<?php
include('chat_config.php');
include ("../includes/session.php");
include ("../includes/functions.php");

if(isset($_GET['set']))
{
	if(isset($_GET['content']))
	{
		$str_msg = stripslashes($_GET['content']);
		$time = getdate();
		$str_msg = add_smilies($str_msg);
		$t_stamp = $time['hours'].":".$time['minutes'].":".$time['seconds'];

		 
		($_GET['uid']!=$_SESSION["guserid"])? $color ="red" : $color ="green";
		
		 mysqli_query($conn, "INSERT INTO ".TABLEPREFIX."chat (nUserId,vMsg,vTimeStamp,vDisplayed,nFromId)
					 VALUES ('".$_GET['uid']."', '".addslashes($str_msg)."', '".$t_stamp."', '0','".$_SESSION["guserid"]."')") 
					 or die(mysqli_error($conn));

		 echo "<br><font color=$color><b>".$_SESSION["guserFName"]." </b>[".$t_stamp."] <b>: </b></font>" . $str_msg;
	}//end if
}//end first if
else if(isset($_GET['get']))
{ 
	if($_GET['uid']!=$_SESSION["guserid"])
	{
		$uid = $_GET['uid'];
		$uid2 = $_SESSION["guserid"];
		
		$color  = "green";
	}//end if
	else
	{
		$uid = $_SESSION["guserid"];
		$uid2 = $_GET['uid'];
		$color  = "red";
	}//end else

	$condition="where nUserId='".$uid."'";
	$chatWith=fetchSingleValue(select_rows(TABLEPREFIX.'users','vLoginName',$condition),'vLoginName');

    $get = "select * from ".TABLEPREFIX."chat where nUserId= '".$uid2."' and vDisplayed='0'
							and nFromId='".$uid."'";
				
  	$res = mysqli_query($conn, $get)or die(mysqli_error($conn));
		   //$num_rows = mysqli_num_rows($res);
	if(mysqli_num_rows($res)>0)
	{
		while($new_msg = mysqli_fetch_array($res))
		{
			echo "<br><font color=\"$color\"><b>".$chatWith." </b>[".$new_msg['vTimeStamp']."] <b>: </b></font>".$new_msg['vMsg'];
			
			//update with showing user
			$mark2 = mysqli_query($conn, "update ".TABLEPREFIX."chat set vDisplayed='1' where nUserId=".$uid2." and vDisplayed='0'
										and nFromId='".$uid."'") or die(mysqli_error($conn));	
		}//end while
	}//end if
}//end if
else
{
	echo "<font color=red><b><br>Error processing data...!</b></font>";
}//end else

function add_smilies($str_msg)
{
	global $conn;
 	$get_smiles = mysqli_query($conn, "select * from ".TABLEPREFIX."smilies order by length(vImgCode) desc") or die(mysqli_error($conn));	
 	while($row_smilies = mysqli_fetch_array($get_smiles))
  	{
    	$str_msg = str_replace(''.$row_smilies['vImgCode'].''," <img src='../icons/".$row_smilies['nSId'].".gif'> ",$str_msg);
  	}//end while
  return $str_msg;
}
?>
