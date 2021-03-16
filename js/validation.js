//LAST MODIFIED	: 	May 2, 2003
/*
Abbrieviation:
bln   : Boolean Variable
str   : String Variable
int   : Integer Variable
Mesg  : Message 
win   : WindowObject
obj   : Object
Prm   : Parameter
*/
/*
LIST OF FUNCTIONS
len, left, right, mid, ltrim, rtrim, trim

IsEmpty(objWord,strErrorMesg)
IsConfirmPassword(objWordInit,objWordFinal,strErrorMesg)
IsEmailAddress(objEmail,strErrorMesg)
IsNumeric(objNumber,strErrorMesg)
IsPositiveNumber(objNumber,strErrorMesg)
IsNumericNoBlank(objNumber,strErrorMesg)
IsAlpha()
IsContainsSpace(objWord,strErrorMesg) 
IsValidPassword(objWord,intMinLength)
IsLengthGreater(objString, intMaxLength,strErrorMesg)
InitializeFormObject(FormName)
ShowMe(objName)	
HideMe(objName)	
showFrame(objFrameName,cur)	
MM_findObj(n, d)
MM_setTextOfLayer(objName,x,newText)
ChangeHtml(SpanName,NewText)
ChangeFrameHtml(SpanName,NewText)
CheckImage(checkFile)
SetFrame(ActWidth,ActHeight,FrameID,FrameUrl)
ChangeFrameUrl(FrameID,FrameUrl)
HideFrame(pFrameId)
OpenWindow(FileName, WinName, WinWidth, WinHeight, ScrollVal)
FixedTopWindow(FileName, WinName, WinWidth, WinHeight, WinLeft, WinTop, ScrollVal)
FullWindow(FileName,WinName,ScrollVal)
fnBackToUrl(x)

movelist(thisobj, i_, thatobj)
addMore(str)
removefield(rem,str)
viewDynamic(isAddAllowed,str)
URLCheckingSpecific(str)
*/

//Delcare variable for browser
var isNav, isIE;
var ieall="";
var sty="";
var imageext	=	new Array(".gif",".jpg",".jpeg")

//Browser checking
if(parseInt(navigator.appVersion)>=4)
   {
   if(navigator.appName=="Netscape") {
      isNav=true;
   }
   else {
      isIE=true;
      var ieall="all.";
	  var sty=".style";
	  }
   }
   	  

function len(str) {
	return String(str).length;  
}


function left(str, n)
{
        if (n <= 0)     // Invalid bound, return blank string
                return "";
        else if (n > String(str).length)   // Invalid bound, return
                return str;                // entire string
        else 		// Valid bound, return appropriate substring
                return String(str).substring(0,n);
}


function right(str, n)
{
        if (n <= 0)     // Invalid bound, return blank string
           return "";
        else if (n > String(str).length)   // Invalid bound, return
           return str;                     // entire string
        else { 		// Valid bound, return appropriate substring
           var iLen = String(str).length;
           return String(str).substring(iLen, iLen - n);
        }
}

function mid(str, start, len)
{
        // Make sure start and len are within proper bounds
        if (start < 0 || len < 0) return "";

        var iEnd, iLen = String(str).length;
        if (start + len > iLen)
                iEnd = iLen;
        else
                iEnd = start + len;

        return String(str).substring(start,iEnd);
}

function ltrim(str)
/***
        PURPOSE: Remove leading blanks from string.
***/
{
        var whitespace = new String(" \t\n\r");
        var s = new String(str);
        if (whitespace.indexOf(s.charAt(0)) != -1) {
            var j=0, i = s.length;
            while (j < i && whitespace.indexOf(s.charAt(j)) != -1)
                j++;
            s = s.substring(j, i);
        }
        return s;
}

function rtrim(str)
/***
        PURPOSE: Remove trailing blanks from our string.
***/
{
        var whitespace = new String(" \t\n\r");
        var s = new String(str);
        if (whitespace.indexOf(s.charAt(s.length-1)) != -1) {
            var i = s.length - 1;       // Get length of string
            while (i >= 0 && whitespace.indexOf(s.charAt(i)) != -1)
                i--;
            s = s.substring(0, i+1);
        }
        return s;
}

function trim(str)
/***
        PURPOSE: Remove trailing and leading blanks from our string.
***/
{
        return rtrim(ltrim(str));
}


function SetStatus(strMesg) { 
	self.status = strMesg
} 



function IsChecked(objWord, strErrorMesg)
{
	if (objWord.checked==false)
	{
		alert(strErrorMesg);
		objWord.focus();
		return false;

	}
	else
	{
		return true;
	}
}


//function for Empty Checking with alert message
function IsEmpty(objWord,strErrorMesg) {
        var blnIsEmpty = true;	
	var blnObjectPrm=false;
	var i=0;	
	var strWord='';
	var objThis;

	if (isIE)
		blnObjectPrm = (typeof(objWord)=='object'?true:false) ; 

	if (blnObjectPrm) {
		objThis = objWord;
		strWord=objWord.value;
	}
	else {
		if (objWord.substring(0,8)=='document') {
			objThis =eval(objWord); 
			strWord=objThis.value;
			blnObjectPrm = true; 
		}
		else
			strWord=objWord;
	}

	while(i<strWord.length && blnIsEmpty) {
		if (strWord.charAt(i)!=' ')
			blnIsEmpty=false;
		i++;
  	}
	if (blnIsEmpty && strErrorMesg!='') {
		alert(strErrorMesg);
		if (blnObjectPrm) {
			//objThis.select();
			objThis.focus();
		}
	}
	return blnIsEmpty;
}


//Confirm Passswords

function IsConfirmPassword(objWordInit,objWordFinal,strErrorMesg)
{
	if(objWordInit.value!=objWordFinal.value)
	{
		alert(strErrorMesg);
		//objWordFinal.select();
		objWordFinal.focus();
		return false;
	}	
	else
	{
		return true;
	}				
}

//function for Email Checking 
function IsEmailAddress(objEmail,strErrorMesg) {
        var blnIsEmailAddress = true;	
	var blnObjectPrm=false;
	var i=0;	
	var strEmail='';
	var objThis;

	if (isIE)
		blnObjectPrm = (typeof(objEmail)=='object'?true:false) ; 

	if (blnObjectPrm) {
		objThis = objEmail;
		strEmail=objEmail.value;
	}
	else {
		if (objEmail.substring(0,8)=='document') {
			objThis =eval(objEmail); 
			strEmail=objThis.value;
			blnObjectPrm = true; 
		}
		else
			strEmail=objEmail;
	}

blnIsEmailAddress=(strEmail.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/)!=-1);
	if (!blnIsEmailAddress && strErrorMesg!='') {
		alert(strErrorMesg);
		if (blnObjectPrm) {
			//objThis.select();
			objThis.focus();		
		}
	}
	return blnIsEmailAddress;
}

//function for Positive and Negative numeric Checking
function IsNumeric(objNumber,strErrorMesg) {
	var blnIsNumeric = true;	
	var blnObjectPrm=false;
	var i=0;	
	var strNumber='';
	var objThis;
	var digits="0123456789,";
	var temp;

	if (isIE)
		blnObjectPrm = (typeof(objNumber)=='object'?true:false) ; 

	if (blnObjectPrm) {
		objThis = objNumber;
		strNumber=objNumber.value;
	}
	else {
		if (objNumber.substring(0,8)=='document') {
			objThis =eval(objNumber); 
			strNumber=objThis.value;
			blnObjectPrm = true; 
		}
		else
			strNumber=objNumber;
	}	

	for (var i=0;i<strNumber.length;i++) {
		temp=strNumber.substring(i,i+1)
		if (digits.indexOf(temp)==-1) {
			blnIsNumeric=false;
	    }
	}
	if (!blnIsNumeric && strErrorMesg!='') {
		alert(strErrorMesg);
		if (blnObjectPrm) { 
			//objThis.select(); 
			objThis.focus(); 		
		}
	}
	return blnIsNumeric;
}

//function for Positive Numerber Checking
function IsPositiveNumber(objNumber,strErrorMesg)
{
	var blnIsPositiveNumber = true;	
	var blnObjectPrm=false;
	var strNumber='';
	var objThis;

	if (isIE)
		blnObjectPrm = (typeof(objNumber)=='object'?true:false) ; 

	if (blnObjectPrm) {
		objThis = objNumber;
		strNumber=objNumber.value;
	}
	else {
		if (objNumber.substring(0,8)=='document') {
			objThis =eval(objNumber); 
			strNumber=objThis.value;
			blnObjectPrm = true; 
		}
		else
			strNumber=objNumber;
	}	

	if(isNaN(strNumber)) {
		blnIsPositiveNumber=false;
	}
	else if(strNumber<=0) {
		blnIsPositiveNumber=false;
	}
	else {
		 blnIsPositiveNumber = true; 
	}
	if (!blnIsPositiveNumber && strErrorMesg!='') {
		alert(strErrorMesg);
		if (blnObjectPrm) { 
			//objThis.select();
			objThis.focus();		
		}
	}
	return blnIsPositiveNumber;
}

//function for Numeric and no blank Checking
function IsNumericNoBlank(objNumber,strErrorMesg) {
	blnIsNumericNoBlank = !IsEmpty(objNumber,strErrorMesg);
	if (blnIsNumericNoBlank)
		blnIsNumericNoBlank = IsPositiveNumber(objNumber,strErrorMesg)
	return blnIsNumericNoBlank;
}



// checks for only alphabets
function IsAlpha(objString,strErrorMesg) {
	var blnIsAlpha = true;
	var blnObjectPrm=false;
	var i=0;	
	var strString='';
	var objThis;
	if (isIE)
		blnObjectPrm = (typeof(objString)=='object'?true:false) ; 

	if (blnObjectPrm) {
		objThis = objString;
		strString=objString.value;
	}
	else {
		if (objString.substring(0,8)=='document') {
			objThis =eval(objString); 
			strString=objThis.value;
			blnObjectPrm = true; 
		}
		else
			strString=objString;
	}

	if(strString.length>0) {
		var strAlpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz";
 		for (i = 0; i<strString.length && blnIsAlpha; i++) { 
			if (strAlpha.indexOf(strString.charAt(i)) == -1) 
				blnIsAlpha=false;
			alert(strErrorMesg);
		}
	}
	if (!blnIsAlpha && strErrorMesg!='') {
		alert(strErrorMesg);
		if (blnObjectPrm) { 
			//objThis.select();
			objThis.focus();		
		}
	}
      	return blnIsAlpha;
}

//Check valid characters 
function IsCharacter(objString,strErrorMesg) {
	var blnIsAlpha = true;
	var blnObjectPrm=false;
	var i=0;	
	var strString='';
	var objThis;
	if (isIE)
		blnObjectPrm = (typeof(objString)=='object'?true:false) ; 

	if (blnObjectPrm) {
		objThis = objString;
		strString=objString.value;
	}
	else {
		if (objString.substring(0,8)=='document') {
			objThis =eval(objString); 
			strString=objThis.value;
			blnObjectPrm = true; 
		}
		else
			strString=objString;
	}

	if(strString.length>0) {
		var strAlpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz0123456789,.';:_+|\@`";
 		for (i = 0; i<strString.length && blnIsAlpha; i++) { 
			if (strAlpha.indexOf(strString.charAt(i)) == -1) 
				blnIsAlpha=false;
			
		}
	}
	
	
	
	if (!blnIsAlpha && strErrorMesg!='') {
		alert(strErrorMesg);
		if (blnObjectPrm) { 
			//objThis.select();
			objThis.focus();		
		}
	}
      	return blnIsAlpha;
}
//function for No Space Checking
function IsContainsSpace(objWord,strErrorMesg) {
	var blnIsContainsSpace = false;
	var blnObjectPrm=false;
	var i=0;	
	var strWord='';
	var objThis;

	if (isIE)
		blnObjectPrm = (typeof(objWord)=='object'?true:false) ; 

	if (blnObjectPrm) {
		objThis = objWord;
		strWord=objWord.value;
	}
	else {
		if (objWord.substring(0,8)=='document') {
			objThis =eval(objWord); 
			strWord=objThis.value;
			blnObjectPrm = true; 
		}
		else
			strWord=objWord;
	}

	while(i<strWord.length && (!blnIsContainsSpace)) { 
		if (strWord.charAt(i)==' ') { 
			blnIsContainsSpace=true;
		}
		i++;
	}	
	if (blnIsContainsSpace && strErrorMesg!='')
	{
		alert(strErrorMesg);
		objWord.select(); 
			objWord.focus();
			
		if (blnObjectPrm) { 
			objThis.select(); 
			objThis.focus(); 		
		}
	}

	return blnIsContainsSpace;
}

//function for Password Checking
function IsValidPassword(objPassword,intMinLength,intMaxLength,strLabelName)
{
        var blnIsValidPassword = true;	
	var blnObjectPrm = false;
	var strPassword='';
	var objThis;
	if (strLabelName=='')
		strLabelName='Password';
	if (isIE)
		blnObjectPrm = (typeof(objPassword)=='object'?true:false) ; 

	if (blnObjectPrm) {
		objThis = objPassword;
		strPassword=objPassword.value;
	}
	else {
		if (objPassword.substring(0,8)=='document') {
			objThis =eval(objPassword); 
			strPassword=objThis.value;
			blnObjectPrm = true; 
		}
		else
			strPassword=objPassword;
	}
	var strErrorMesg = ""
	if (strPassword.value=="" && intMinLength==0)
		strErrorMesg = "Please enter the "+strLabelName;
	else if(IsContainsSpace(strPassword))
		strErrorMesg = strLabelName+" should not contain Spaces.";
	else if(strPassword.length < intMinLength)
		strErrorMesg = strLabelName+" Should be atleast " + intMinLength + " characters";
	else if(strPassword.length > intMaxLength)
		strErrorMesg = strLabelName+" Should be maximum " + intMaxLength + " characters";
	if (strErrorMesg)
	{
		blnIsValidPassword = false;
		alert(strErrorMesg);
		if (blnObjectPrm) { 
			objThis.select(); 
			objThis.focus(); 		
		}
	}
	return blnIsValidPassword;
}


//function for checking the string length with the parameter passed
function IsLengthGreater(objString, intMaxLength,strErrorMesg)
{
	var blnIsLengthGreater = false;
	var blnObjectPrm=false;
	var i=0;	
	var strString='';
	var objThis;
	if (isIE)
		blnObjectPrm = (typeof(objString)=='object'?true:false) ; 

	if (blnObjectPrm) {
		objThis = objString;
		strString=objString.value;
	}
	else {
		if (objString.substring(0,8)=='document') {
			objThis =eval(objString); 
			strString=objThis.value;
			blnObjectPrm = true; 
		}
		else
			strString=objString;
	}

	if(strString.length>intMaxLength)
		blnIsLengthGreater=true;
	if (blnIsLengthGreater && strErrorMesg!='') {
		alert(strErrorMesg);
		if (blnObjectPrm) { 
			objThis.select(); 
			objThis.focus(); 		
		}
	}
	return blnIsLengthGreater;
}










function IsLengthless(objString,intMaxLength,intMaxLength2,strErrorMesg)
{
	var blnIsLengthGreater = false;
	var blnObjectPrm=false;
	var i=0;	
	var strString='';
	var objThis;
	if (isIE)
		blnObjectPrm = (typeof(objString)=='object'?true:false) ; 

	if (blnObjectPrm) {
		objThis = objString;
		strString=objString.value;
	}
	else {
		if (objString.substring(0,8)=='document') {
			objThis =eval(objString); 
			strString=objThis.value;
			blnObjectPrm = true; 
		}
		else
			strString=objString;
	}

	if(strString.length<intMaxLength || strString.length>intMaxLength2)
		blnIsLengthGreater=true;
	if (blnIsLengthGreater && strErrorMesg!='') {
		alert(strErrorMesg);
		objString.focus();
		return false;
		if (blnObjectPrm) { 
			objThis.select(); 
			objThis.focus(); 		
		}
	}
	return true;
	return blnIsLengthGreater;
}
















//function for reset the value of form's fields
function InitializeFormObject(FormName){
var vFormObj=eval("document."+FormName)
  for(i=0; i<vFormObj.elements.length; i++) {
	if ((vFormObj.elements[i].type=="text") || (vFormObj.elements[i].type=="password") || 
            (vFormObj.elements[i].type=="textarea"))
	{
		vFormObj.elements[i].value="";
	}
	else if(vFormObj.elements[i].type=="checkbox")
	{
		vFormObj.elements[i].checked=false;
	}
}

//function for checking object visibility
function IsObjectVisible(objName) {
 	return (eval("document."+ieall+objName+sty).visibility=="visible")
}


//function to make visible, hidden object
function ShowMe(objName)	
{
	eval("document."+ieall+objName+sty).visibility="visible";
	return;
}

//function to make hidden, visible object
function HideMe(objName) 
{
	eval("document."+ieall+ObjName+sty).visibility="hidden";
	return;
}

//function for show Frame
function ShowFrame(objFrameName,cur)	
{
	var intClientWidth = window.screen.availWidth;
	var intFrameTop	= 15;
	var intDifference = 0;
	for (var _x=1; _x < cur; _x++)
	{
		intFrameTop += eval("app"+_x).offsetHeight;
	}
	var objFrame=eval("document."+ieall+objFrameName+sty);
	objFrame.visibility = "visible";
	difference = (intClientWidth - objFrame.width.substring(0,objFrame.width.length-2)) / 2;
	objFrame.top = intFrameTop
	objFrame.left = intDifference
}


//**************** function for Validation message change ***************

function MM_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
}

function MM_setTextOfLayer(objName,x,newText) { //v3.0
  if ((obj=MM_findObj(objName))!=null) with (obj)
    if (navigator.appName=='Netscape') {document.write(unescape(newText)); document.close();}
    else innerHTML = unescape(newText);
return;
}

//function for Change InnerHTML of Opener Window
function ChangeHtml(SpanName,NewText)	{
	var showChangeHtml=eval("window.opener.document."+ieall+SpanName);
	showChangeHtml.innerHTML=NewText;
	return;
	}

//function for Change InnerHTML of Frame Window
function ChangeFrameHtml(SpanName,NewText)	{
	var showChangeHtml=eval("window.top.document."+ieall+SpanName);
	showChangeHtml.innerHTML=NewText;
	return;
	}

//function for Image Checking
function CheckImage(checkFile)
{
 stat = false
	if((checkFile.indexOf(".gif")>0) || (checkFile.indexOf(".jpg")>0) || (checkFile.indexOf(".bmp")>0))
	{
		stat=true
	}
  return stat;
}


//function for show Frame
function SetFrame(ActWidth,ActHeight,FrameID,FrameUrl)
{

	var FramePath=eval("window."+FrameID+".location");
	var theIFrame=eval("document.all."+FrameID+".style")

	if(FramePath.pathname.indexOf(".htm")>-1)
	{
		var CurWidth, CurHeight //variable for store the value of width and height of current resulotion

		if(isNav)
		{ //checking brower is netscape
			CurWidth=parseInt((window.innerWidth-ActWidth)/2);
			CurHeight=parseInt((window.innerHeight-ActHeight)/2);
		}
		else
		{
			CurWidth=parseInt((window.screen.availWidth-ActWidth)/2);
			CurHeight=parseInt((window.screen.availHeight-ActHeight)/2);
		}
		theIFrame.left=CurWidth;
		theIFrame.top=CurHeight;
		FramePath.href=FrameUrl;
		//alert(FrameUrl);
	}
	theIFrame.visibility='visible';	
}

//function for show Frame
function ChangeFrameUrl(FrameID,FrameUrl)
{
	var FramePath=eval("window."+FrameID+".location");
	var theIFrame=eval("document.all."+FrameID+".style")
	FramePath.href=FrameUrl;
	theIFrame.visibility='visible';	
}
//function for hide Frame
function HideFrame(pFrameId)
{

	if(ClickValue.innerHTML=="0")
	{
		var theIFrame=eval("document.all."+pFrameId+".style");
		theIFrame.visibility='hidden';
	}
	ClickValue.innerHTML="0";
}


//function for open new window accoring to resulotion
function OpenWindow(FileName, WinName, WinWidth, WinHeight, ScrollVal)
{
var CurWidth, CurHeight //variable for store the value of width and height of current resulotion

if(isNav){ //checking brower is netscape
	CurWidth=window.innerWidth;
	CurHeight=window.innerHeight;}
else{
	CurWidth=window.screen.availWidth-16;
	CurHeight=window.screen.availHeight-20;}

if (CurWidth>800){ //checking current resulotion greater than 800
	WinWidth=Math.round(((CurWidth-800)*WinWidth/800)+WinWidth)  //setting window width with perpotion  to the more that 800*600 resolution
	//WinHeight=Math.round(((CurHeight-600)*WinHeight/600)+WinHeight) //setting window height with perpotion  to the more that 800*600 resolution
	}

var WinTop, WinLeft //variable for store the value of top and left of current resulotion

WinLeft=Math.round((CurWidth-WinWidth)/2);
WinTop=Math.round((CurHeight-WinHeight)/2);

	new_window=open(FileName,WinName,"'toolbar=no, directories=no, menubar=no, scrollbars="+ScrollVal+", width="+WinWidth+", height="+WinHeight+", Left="+WinLeft+", Top="+WinTop+"'");
	new_window.blur();
	new_window.focus();
}

//function for open new window accoring to parameter send
function FixedTopWindow(FileName, WinName, WinWidth, WinHeight, WinLeft, WinTop, ScrollVal)
{
var CurWidth, CurHeight //variable for store the value of width and height of current resulotion

if(isNav){ //checking brower is netscape
	CurWidth=window.innerWidth;
	CurHeight=window.innerHeight;}
else{
	CurWidth=window.screen.availWidth-16;
	CurHeight=window.screen.availHeight-20;}

if (CurWidth>800){ //checking current resulotion greater than 800
	WinWidth=Math.round(((CurWidth-800)*WinWidth/800)+WinWidth)  //setting window width with perpotion  to the more that 800*600 resolution
	}
	
if(CurWidth<800)
{
	WinTop= WinTop-24 //setting window height with perpotion  to the more that 800*600 resolution
}
	new_window=open(FileName,WinName,"'toolbar=no, directories=no, menubar=no, scrollbars="+ScrollVal+", width="+WinWidth+", height="+WinHeight+", Left="+WinLeft+", Top="+WinTop+"'");
	new_window.blur();
	new_window.focus();
}

//function for open new window accoring to parameter send
function FullWindow(FileName,WinName,ScrollVal)
{
	Full_window=window.open(FileName,WinName,"fullscreen=1,toolbar=no, directories=no, menubar=no, scrollbars="+ScrollVal);
	Full_window.blur();
	Full_window.focus();
}

//function for back url
function fnBackToUrl(x)
{
	if(window.document.location.href.indexOf("NoHistory")>-1)
		window.document.location.href=x;
	else
		history.back();
}

}

function ChangeQuot(pStrVal)
{
  var vStrVal=pStrVal;
  while(vStrVal.indexOf("&quot;")>=0)
  {
	vStrVal=vStrVal.replace("&quot;","\"");
  }
  return vStrVal;
}


// Functions Start for moving list box item right and left
function movelist(thisobj, i_, thatobj)
{
    if(i_ >= 0 && left(thisobj.options[i_].text,1) != '-') 
    {
		var no   = new Option()
		no.value = thisobj.options[i_].value
		no.text  = thisobj.options[i_].text
		thatobj.options[thatobj.options.length] = no
		thisobj.options[i_].value = ''
		thisobj.options[i_].text = ''
		refresh(thisobj)
    }
}

function refresh(thisobj)
{
   for(var i=0; i<thisobj.options.length; i++)
   {
      if(thisobj.options[i].value == '')
      {
         for(var j=i; j<thisobj.options.length-1; j++)
         {
            thisobj.options[j].value = thisobj.options[j+1].value;
            thisobj.options[j].text = thisobj.options[j+1].text;
         }
         var ln = i;
         break;
      }
   }
   if(ln < thisobj.options.length)
   {
      thisobj.options.length -= 1;
      refresh(thisobj);
   }
}

function moveall(thisobj, thatobj)
{
	for(var j=0; j<thisobj.options.length; j++) 
	{
			allatatime(thisobj, j, thatobj)
	}
	refresh(thisobj)
	return true
}

function allatatime(thisobj, i_, thatobj)
{
    if(i_ >= 0 && left(thisobj.options[i_].text,1) != '-') 
    {
		var no   = new Option()
		no.value = thisobj.options[i_].value
		no.text  = thisobj.options[i_].text
		thatobj.options[thatobj.options.length] = no

		thisobj.options[i_].value = ''
		thisobj.options[i_].text = ''
    }
}

// Functions END for moving list box item right and left

function IsDateFormatOK(strdate,strFormatMask,strErrorMesg) {
	
	if (strFormatMask == 'DD/MM/YYYY')
	{
		if (strdate.length != 10)
		{
			if	(strErrorMesg=='')
				alert('Please enter a valid date in DD/MM/YYYY format');
			else
				alert(strErrorMesg);
			return false;
		}
		var intDay = strdate.substr(0,2);
		var intMonth = strdate.substr(3,2);
		var intYear = strdate.substr(6,4);
		var c = intDay + intMonth + intYear;
	}
	blnFormatMaskOK =  (!isNaN(c) && strdate.substr(2,1)=='/' && strdate.substr(5,1)=='/' && IsDateValid(intDay,intMonth,intYear));
	if (!blnFormatMaskOK && strErrorMesg!='')
		alert(strErrorMesg);	
	return (blnFormatMaskOK);
}

function dategreater(dd,mm,yyyy,dd1,mm1,yyyy1)
{
	var dt = new Date();
	dt.setDate(dd);
	dt.setMonth(mm-1);
	dt.setFullYear(yyyy);
	
	var dt1 = new Date();
	dt1.setDate(dd1);
	dt1.setMonth(mm1-1);
	dt1.setFullYear(yyyy1);
	
	if(dt>=dt1)
	{
		return (false);
	}
	return (true);
}

function IsDateValid(dd,mm,yyyy)
{
	var dt = new Date();
	dt.setMonth(0);
	dt.setDate(1);
	dt.setFullYear(yyyy);
	dt.setMonth(mm-1);
	dt.setDate(dd);
	var dd1=dt.getDate();
	var mm1=dt.getMonth()+1;
	var yyyy1=dt.getFullYear();
	if((dd!=dd1)||(mm!=mm1)||(yyyy!=yyyy1)||(yyyy==-1)||yyyy.length==0)	 {
		return false;
	}
	return true;
}

function fncheckall()
{
	for (i=0; i<document.forms[0].elements.length; i++)
	{
		if(document.forms[0].elements[i].type == "checkbox")
		{
				if(document.forms[0].ckCheck.checked)
				{
					document.forms[0].elements[i].checked = true
				}
				else
				{
					document.forms[0].elements[i].checked = false
				}
		}
	}	
	return false;
}

function OpenWindow(FileName, WinName, WinWidth, WinHeight, ScrollVal)
{
var CurWidth, CurHeight //variable for store the value of width and height of current resulotion

if(isNav){ //checking brower is netscape
	CurWidth=window.innerWidth;
	CurHeight=window.innerHeight;}
else{
	CurWidth=window.screen.availWidth-16;
	CurHeight=window.screen.availHeight-20;}

if (CurWidth>800){ //checking current resulotion greater than 800
	WinWidth=Math.round(((CurWidth-800)*WinWidth/800)+WinWidth)  //setting window width with perpotion  to the more that 800*600 resolution
	//WinHeight=Math.round(((CurHeight-600)*WinHeight/600)+WinHeight) //setting window height with perpotion  to the more that 800*600 resolution
	}

var WinTop, WinLeft //variable for store the value of top and left of current resulotion

WinLeft=Math.round((CurWidth-WinWidth)/2);
WinTop=Math.round((CurHeight-WinHeight)/2);

	new_window=open(FileName,WinName,"'toolbar=no, directories=no, menubar=no, scrollbars="+ScrollVal+", width="+WinWidth+", height="+WinHeight+", Left="+WinLeft+", Top="+WinTop+"'");
	new_window.blur();
	new_window.focus();
}


function OpenWindowMenu(FileName, WinName, WinWidth, WinHeight, ScrollVal)
{
var CurWidth, CurHeight //variable for store the value of width and height of current resulotion

if(isNav){ //checking brower is netscape
	CurWidth=window.innerWidth;
	CurHeight=window.innerHeight;}
else{
	CurWidth=window.screen.availWidth-16;
	CurHeight=window.screen.availHeight-20;}

if (CurWidth>800){ //checking current resulotion greater than 800
	WinWidth=Math.round(((CurWidth-800)*WinWidth/800)+WinWidth)  //setting window width with perpotion  to the more that 800*600 resolution
	//WinHeight=Math.round(((CurHeight-600)*WinHeight/600)+WinHeight) //setting window height with perpotion  to the more that 800*600 resolution
	}

var WinTop, WinLeft //variable for store the value of top and left of current resulotion

WinLeft=Math.round((CurWidth-WinWidth)/2);
WinTop=Math.round((CurHeight-WinHeight)/2);

	new_window=open(FileName,WinName,"'toolbar=no, directories=no, menubar=yes, scrollbars="+ScrollVal+", width="+WinWidth+", height="+WinHeight+", Left="+WinLeft+", Top="+WinTop+"'");
	new_window.blur();
	new_window.focus();
}

function addMore(str)
{
	var vStoreData =	"";
	var vDataError	=	false;
	var vHdFieldVal=	eval("document."+FormName+".hdCount"+str+".value");
	FieldValue	=	new Array();

	for (var x=0;x<vHdFieldVal;x++)
	{
		FieldValue[x]	=	new Array();
		var y 	=	0;
		FieldValue[x][y++]	=	0;
		for (;y<=column.length;y++)
		{
			FieldValue[x][y]	=	eval("document."+FormName+"."+column[(y-1)][0]+str+(x+1)+".value")
			var isEmpty = false;
			if (column[(y-1)][5].length>0)
			{
				isEmpty = eval(column[(y-1)][5]+"('"+FieldValue[x][y]+"')");
			}

			if (isEmpty & FieldValue[x][0]==0)
			{
			 	FieldValue[x][0]	=	column[(y-1)][1];
				vDataError			=	true;
			}
		}

		if(FieldValue[x][0]	==	0)
		{
			FieldValue[x][0]	=	"&nbsp;"
		}
	}

	if (vDataError)
	{
		eval("document."+FormName+".hdCount"+str).value =	eval("document."+FormName+".hdCount"+str).value
		viewDynamic(1,str); 
	}	
	else
	{
	eval("document."+FormName+".hdCount"+str).value =	eval(eval("document."+FormName+".hdCount"+str).value) + 1
		viewDynamic(0,str); 
	}	
}



function removefield(rem,str)
{
	var vStoreData =	"";
	var vDataError	=	false;
	var vHdFieldVal=	eval("document."+FormName+".hdCount"+str+".value");

	FieldValue	=	new Array()
	var z	=	0;
	for (var x=0;x<vHdFieldVal;x++)
	{

		if(eval(x) != eval(rem))
		{
			FieldValue[z]	=	new Array()		
			var y 	=	0;
			FieldValue[z][y++]	=	0;

			for (;y<=column.length;y++)
			{
				FieldValue[z][y]	=	eval("document."+FormName+"."+column[(y-1)][0]+str+(x+1)+".value")
				var isEmpty = false;
			
				if (column[(y-1)][5].length>0)
					isEmpty = eval(column[(y-1)][5]+"('"+FieldValue[z][y]+"')");

				if (isEmpty & FieldValue[z][0]==0)
				{
				 	FieldValue[z][0]	=	column[(y-1)][1];
					vDataError	=	true;
				}
			}

			if(FieldValue[z][0]	==	0)
			{
				FieldValue[z][0]	=	"&nbsp;"
			}
			z++;
		}
	}

		eval("document."+FormName+".hdCount"+str).value = eval(eval("document."+FormName+".hdCount"+str).value) - 1
	viewDynamic(1,str); 
}


function viewDynamic(isAddAllowed,str)
{
	var vStoreData = "<table width='100%' border='0' cellspacing='0' cellpadding='2' id='generateHTML"+str+"'>";

	for (var y=0;y<FieldValue.length;y++)
	{
		vStoreData += "<tr>";
		vStoreData	+=	"<td width='20'>";
		if(FieldValue.length==1 && isAddAllowed==1)
			vStoreData	+=	"&nbsp;"			
		else	
			vStoreData	+=	"<input type='button' class='InputButton' name='add' value='-' onClick = \"removefield("+y+",'"+str+"')\">"


			vStoreData	+=	"</td>";
		for (var x=0;x<column.length;x++)
		{
			vStoreData	+=	"<td  width='140' align='center'><input type='text' class='InputText' size="+column[x][2]+" maxlength="+column[x][3]+" name='"+column[x][0]+str+(y+1)+"' value='"+FieldValue[y][x+1]+"' ";
			if (column[x][6]!='')
				vStoreData	+=	"onFocus = '"+column[x][6]+"'";
			vStoreData	+=	"				 ></td>";
		}
		
		vStoreData	+=	"<td  width='20'>";
		if(isAddAllowed==1 && y==FieldValue.length-1)
		{			
			vStoreData	+=	"<input type='button' class='InputButton' name='add' value='+' onClick=\"addMore('"+str+"');\"></td>";
		}
		else
		{
			vStoreData	+=	"&nbsp;";			
		}	
			vStoreData	+=	"</td>";
						
		vStoreData	+= "<td><span class='MaroonText'>" + FieldValue[y][0] + "</span></td>" 
		vStoreData += "</tr>";			
	}
			
	if(isAddAllowed==0)
	{
		vStoreData  += "<tr>";
		vStoreData	+=	"<td width='20'>";
		vStoreData	+=	"<input type='button' class='InputButton' name='add' value='-' onClick = \"removefield("+y+",'"+str+"')\">"
		vStoreData	+=	"</td>";
		for (var x=0;x<column.length;x++)
		{
			vStoreData	+=	"<td  width='140' align='center'><input type='text' class='InputText' size="+column[x][2]+" maxlength="+column[x][3]+" name='"+column[x][0]+str+(y+1)+"' value='"+column[x][4]+"' ";
			if (column[x][6]!='')
				vStoreData	+=	"onFocus = '"+column[x][6]+"'";
			vStoreData	+=	"				></td>";
		}
		
		vStoreData	+=	"<td  width='20'><input type='button' class='InputButton' name='add' value='+' onClick=\"addMore('"+str+"');\"></td>";

		vStoreData	+= "<td><span class='error1'>&nbsp;</span></td>" 
		vStoreData  += "</tr>";
	}
	
		vStoreData += "</table>";		

		eval("generateHTML"+str).outerHTML	=	vStoreData

	var ctr	=	FieldValue.length
	if (isAddAllowed==0)
	{
		ctr++;
		eval("document."+FormName+"."+column[0][0]+str+ctr+".select()");
		eval("document."+FormName+"."+column[0][0]+str+ctr+".focus()");	
	}
}
	
//function for URL Checking of Multiple Records Generation





function checkimage(str,strErrorMesg) {
	if(str.substring(0,-3) == "gif" || str.substring(0,-4) == "jpeg")
		{
		   return true;
		}
		else
	    {
		alert(strErrorMesg);
		return false;
	    }

		return true;
}


function checkspecialch(objString,strErrorMesg)
{
	 var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":<>?";
  for (var i = 0; i < objString.value.length; i++) {
  	if (iChars.indexOf(objString.value.charAt(i)) != -1) {
		alert(strErrorMesg);
		objString.select(); 
		objString.focus(); 
		return false;
  	}

	
  }

return true;
}

function checkImageType(str,strErrorMesg) {
	if(str.substring(0,-3) == "jpg" || str.substring(0,-4) == "jpeg") {
		   return true;
	}
	else {
		alert(strErrorMesg);
		return false;
	 }
	return true;
}

function disableKeysForFileInput() {
	var isTabKey = false;
	if (event.keyCode == 9) {
		isTabKey = true;
	} else {
		isTabKey = false;
	}
	return isTabKey;
}

function displayPreview(fileObj, imageElementName) {
	//alert(fileObj.value);
	document.getElementById(imageElementName).src = fileObj.value;
}




function validEmail(email) 
{ 
    if (email == ""){return false;} 
    badStuff = ";:/,' \"\\"; 
     
    for (i=0; i<badStuff.length; i++) 
    { 
        badCheck = badStuff.charAt(i) 
        if (email.indexOf(badCheck,0) != -1){return false;} 
    } 

    posOfAtSign = email.indexOf("@",1) 
     
    if (posOfAtSign == -1){return false;} 

    if (email.indexOf("@",posOfAtSign+1) != -1){return false;} 
    posOfPeriod = email.indexOf(".", posOfAtSign) 
     
    if (posOfPeriod == -1){return false;} 
    if (posOfPeriod+2 > email.length){return false;} 
     
return true; 
} 




function left_chk()
{

		var errorstr = '';
		var msgstr = "Sorry, we cannot complete your request.\nPlease provide us the missing or incorrect information enclosed below.\n\n";
		
		with(document.formleft)
		{ 	
			if(username.value=='') { errorstr += "- Please enter username.\n"; }
			if (password.value=='') { errorstr += "- Please enter password.\n";}
		}	
		
		if (errorstr != '')
		{
			msgstr = msgstr + errorstr;
			alert(msgstr);
			return false;
		}
		else
		{
			return true;
		}	
			
} 



function textCounter(field, countfield, maxlimit)
{
	if (field.value.length > maxlimit)
	{
		field.value = field.value.substring(0, maxlimit);
	}
	else
	{
              countfield.value = maxlimit - field.value.length;
    }
}