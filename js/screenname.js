var xmlHttp

function showHint(str,rtype)
{
if (str.length==0)
{ 
document.getElementById("txtHint").innerHTML=""
return
}
xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
{
alert ("Browser does not support HTTP Request")
return
} 
var url="check_screen_email.php"
url=url+"?q="+str+"&type="+rtype
url=url+"&sid="+Math.random()
xmlHttp.onreadystatechange=stateChanged 
xmlHttp.open("GET",url,true)
xmlHttp.send(null)
} 

function showHint2(str,rtype)
{
if (str.length==0)
{ 
document.getElementById("txtHint2").innerHTML=""
return
}
xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
{
alert ("Browser does not support HTTP Request")
return
} 
var url="check_screen_email.php"
url=url+"?q="+str+"&type="+rtype
url=url+"&sid="+Math.random()
xmlHttp.onreadystatechange=stateChanged2 
xmlHttp.open("GET",url,true)
xmlHttp.send(null)
} 

function showHint3(str,rtype)
{
if (str.length==0)
{ 
document.getElementById("txtHint3").innerHTML=""
return
}
xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
{
alert ("Browser does not support HTTP Request")
return
} 
var url="check_screen_email.php"
url=url+"?q="+str+"&type="+rtype
url=url+"&sid="+Math.random()
xmlHttp.onreadystatechange=stateChanged3 
xmlHttp.open("GET",url,true)
xmlHttp.send(null)
} 

function stateChanged() 
{ 
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
{ 
document.getElementById("txtHint").innerHTML=xmlHttp.responseText 
} 
}


function stateChanged2() 
{ 
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
{ 
document.getElementById("txtHint2").innerHTML=xmlHttp.responseText 
} 
} 

function stateChanged3() 
{ 
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
{ 
document.getElementById("txtHint3").innerHTML=xmlHttp.responseText 
} 
} 


function GetXmlHttpObject()
{ 
var objXMLHttp=null
if (window.XMLHttpRequest)
{
objXMLHttp=new XMLHttpRequest()
}
else if (window.ActiveXObject)
{
objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
}
return objXMLHttp
}  
