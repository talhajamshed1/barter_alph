
var xmlHttp

function showHint(str)
{
    alert(str);
	if (str.length==0)
	{ 
		document.getElementById("showContent").innerHTML="";
		return;
	}//end if
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	}//end if
	
	var url="template_preview.php";
	url=url+"?q="+str;
	url=url+"&sid="+Math.random();

	xmlHttp.onreadystatechange=stateChanged ;
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}//end funciton 

function stateChanged() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		document.getElementById("showContent").innerHTML=xmlHttp.responseText;
	}//end if 
}//end funciton

function GetXmlHttpObject()
{ 
	var objXMLHttp=null;
	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest();
	}//end if
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}//end else if
	return objXMLHttp;
}  

function getContent(str)
{
if (str.length!=0)
{
 document.getElementById("cid").value= str;
}
 document.frmSettings.submit();
}


