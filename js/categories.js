var xmlHttp

function showCategory(str,nLevel)
{
	/*if (str.length==0)
	{ 
		document.getElementById("txtDisplayCategory").innerHTML=""
		return
	}//end if*/ 
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}//end if 
	var url="category_list.php"
	url=url+"?q="+str+"&lev="+nLevel
	url=url+"&sid="+Math.random()
	xmlHttp.onreadystatechange=stateChanged 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}//end function 

function stateChanged() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		var resText1  = xmlHttp.responseText;
		var resText2 = resText1.split('txtDisplayCategory');
		var resText = resText2[1].split('"');
		
		var oldSpanId=parseInt(resText[0])-1;
		var spanName='txtDisplayCategory'+oldSpanId;
		document.getElementById(spanName).innerHTML=resText1;
	}//end if 
}//end funciton

function GetXmlHttpObject()
{ 
	var objXMLHttp=null
	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest()
	}//end if
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
	}//end else if
	return objXMLHttp
}//end funciton 
