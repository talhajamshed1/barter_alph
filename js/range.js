var xmlHttp

function showRange(rtype,str,vmode,nrId)
{
	if (str.length==0)
	{ 
		document.getElementById("txtRange").innerHTML=""
		return;
	}//end if
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}//end if 
	var url="check_range.php"
	url=url+"?q="+str+"&rtype="+rtype+"&vmode="+vmode+"&nrId="+nrId
	url=url+"&sid="+Math.random()
	xmlHttp.onreadystatechange=stateChanged 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}//end function

function showEscrowRange(rtype,str,vmode,nrId)
{
	if (str.length==0)
	{
		document.getElementById("txtRange").innerHTML=""
		return;
	}//end if
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}//end if
	var url="check_escrow_range.php"
	url=url+"?q="+str+"&rtype="+rtype+"&vmode="+vmode+"&nrId="+nrId+"&from=swap"
	url=url+"&sid="+Math.random()

	xmlHttp.onreadystatechange=stateChanged
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}//end function


function stateChanged() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		document.getElementById("txtRange").innerHTML=xmlHttp.responseText 
	}//end if 
}//end function

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
}//end function  
