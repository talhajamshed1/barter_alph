var xmlHttp

function showHint(str)
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
var url="template_preview.php"
url=url+"?q="+str
url=url+"&sid="+Math.random()
xmlHttp.onreadystatechange=stateChanged 
xmlHttp.open("GET",url,true)
xmlHttp.send(null)
}

function getContent(str)
{
if (str.length==0)
{
 document.getElementById("cid").value= str;
}
 document.frmSettings.submit();
}

function stateChanged() 
{ 
    if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
    {
    document.getElementById("txtHint").innerHTML=xmlHttp.responseText
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
