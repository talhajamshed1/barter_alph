var xmlHttp

function showCommision(str)
{
    
    var url="check_commision.php"
        
        if(enablePoint == "2" || enablePoint == "1" ){
           var strPoint = document.getElementById("txtPoint").value; 
        }     
        
        var strPrice = document.getElementById("txtValue").value;        
        var from;
        var str;
        if(strPrice > 0){
            str = strPrice;
            from = 'price';
        } else if(strPoint > 0){
            str = strPoint;
            from = 'points';
        }
        
        //Commented this seems buggy
        /*if (str.length==0 || str==0)
	{
            str = document.getElementById("txtPoint").value;
            if(str.length==0){
		document.getElementById("txtCommission").value="0"
		return;
            }else{
                var from = "points";
            }
	}else{
            var from = 'price';
        }//end if
        */

	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}//end if

	url = url+"?q="+str
        url = url+"&from="+from
	url = url+"&sid="+Math.random()
	xmlHttp.onreadystatechange=stateChanged 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}//end function 

function stateChanged() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		document.getElementById("txtCommission").value=xmlHttp.responseText 
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
