/*
==================================================================
LTrim(string) : Returns a copy of a string without leading spaces.
==================================================================
*/
function LTrim(str)
/*
   PURPOSE: Remove leading blanks from our string.
   IN: str - the string we want to LTrim
*/
{
    var whitespace = new String(" \t\n\r");

    var s = new String(str);

    if (whitespace.indexOf(s.charAt(0)) != -1) {
        // We have a string with leading blank(s)...

        var j=0, i = s.length;

        // Iterate from the far left of string until we
        // don't have any more whitespace...
        while (j < i && whitespace.indexOf(s.charAt(j)) != -1)
            j++;

        // Get the substring from the first non-whitespace
        // character to the end of the string...
        s = s.substring(j, i);
    }
    return s;
}
/*
==================================================================
LTrim(string) : Returns a copy of a string without leading spaces.
==================================================================
*/
function LTrim(str)
/*
   PURPOSE: Remove leading blanks from our string.
   IN: str - the string we want to LTrim
*/
{
    var whitespace = new String(" \t\n\r");

    var s = new String(str);

    if (whitespace.indexOf(s.charAt(0)) != -1) {
        // We have a string with leading blank(s)...

        var j=0, i = s.length;

        // Iterate from the far left of string until we
        // don't have any more whitespace...
        while (j < i && whitespace.indexOf(s.charAt(j)) != -1)
            j++;

        // Get the substring from the first non-whitespace
        // character to the end of the string...
        s = s.substring(j, i);
    }
    return s;
}

/*
==================================================================
RTrim(string) : Returns a copy of a string without trailing spaces.
==================================================================
*/
function RTrim(str)
/*
   PURPOSE: Remove trailing blanks from our string.
   IN: str - the string we want to RTrim

*/
{
    // We don't want to trip JUST spaces, but also tabs,
    // line feeds, etc.  Add anything else you want to
    // "trim" here in Whitespace
    var whitespace = new String(" \t\n\r");

    var s = new String(str);

    if (whitespace.indexOf(s.charAt(s.length-1)) != -1) {
        // We have a string with trailing blank(s)...

        var i = s.length - 1;       // Get length of string

        // Iterate from the far right of string until we
        // don't have any more whitespace...
        while (i >= 0 && whitespace.indexOf(s.charAt(i)) != -1)
            i--;


        // Get the substring from the front of the string to
        // where the last non-whitespace character is...
        s = s.substring(0, i+1);
    }

    return s;
}
/*
=============================================================
Trim(string) : Returns a copy of a string without leading or trailing spaces
=============================================================
*/
function trim(str)
/*
   PURPOSE: Remove trailing and leading blanks from our string.
   IN: str - the string we want to Trim

   RETVAL: A Trimmed string!
*/
{
    return RTrim(LTrim(str));
}



/*
=============================================================
checkEmail : test and return true/false whether the supplied string is a valid email address or not
=============================================================
*/

function checkMail(email)
{
    var str1=email;
    var arr=str1.split('@');
    var eFlag=true;
    if(arr.length != 2)
    {
        eFlag = false;
    }
    else if(arr[0].length <= 0 || arr[0].indexOf(' ') != -1 || arr[0].indexOf("'") != -1 || arr[0].indexOf('"') != -1 || arr[1].indexOf('.') == -1)
    {
        eFlag = false;
    }
    else
    {
        var dot=arr[1].split('.');
        if(dot.length < 2)
        {
            eFlag = false;
        }
        else
        {
            if(dot[0].length <= 0 || dot[0].indexOf(' ') != -1 || dot[0].indexOf('"') != -1 || dot[0].indexOf("'") != -1)
            {
                eFlag = false;
            }

            for(i=1;i < dot.length;i++)
            {
                if(dot[i].length <= 0 || dot[i].indexOf(' ') != -1 || dot[i].indexOf('"') != -1 || dot[i].indexOf("'") != -1 || dot[i].length > 4)
                {
                    eFlag = false;
                }
            }
        }
    }
    return eFlag;
}


function limitLength(element, maxLength)
{
    if (element.value.length > maxLength)
    {
        element.value = element.value.substring(0, maxLength);
        return 0;
    }
}


function openWindow(url,ht,wt)
{
    window.open(url,'','scrollbars=no,menubar=no,height=ht,width=wt,resizable=yes,toolbar=no,location=no,status=no');
}

function check_numeric_value (obj){
    if(isNaN(parseInt(obj.value)) || parseInt(obj.value) == 'NaN' || parseInt(obj.value) < 0) 
        obj.value = 0; 
    else 
        obj.value = parseInt(obj.value);
}

function check_float_value (obj){
    if(isNaN(parseFloat(obj.value)) || parseFloat(obj.value) == 'NaN' || parseFloat(obj.value) < 0) 
        obj.value = 0; 
    else 
        obj.value = parseFloat(obj.value);
}

function getCreditCardType(accountNumber){

  //start without knowing the credit card type
  var result = "unknown";

  //first check for MasterCard
  if (/^5[1-5]/.test(accountNumber)) {
    document.getElementById('replace_code1').innerHTML="Card Validation Code(CVC2)";
  }

  //then check for Visa
  else if (/^4/.test(accountNumber)) {
    document.getElementById('replace_code1').innerHTML="Card Validation Code(CVV2)";
  }

  //then check for AmEx
  else if (/^3[47]/.test(accountNumber)) {
    document.getElementById('replace_code1').innerHTML="Card Validation Code(CID)";
  } else {
    document.getElementById('replace_code1').innerHTML="Card Validation Code(CVV2)";
  }

}










/*
==================================================================
RTrim(string) : Returns a copy of a string without trailing spaces.
==================================================================
*/
function RTrim(str)
/*
   PURPOSE: Remove trailing blanks from our string.
   IN: str - the string we want to RTrim

*/
{
    // We don't want to trip JUST spaces, but also tabs,
    // line feeds, etc.  Add anything else you want to
    // "trim" here in Whitespace
    var whitespace = new String(" \t\n\r");

    var s = new String(str);

    if (whitespace.indexOf(s.charAt(s.length-1)) != -1) {
        // We have a string with trailing blank(s)...

        var i = s.length - 1;       // Get length of string

        // Iterate from the far right of string until we
        // don't have any more whitespace...
        while (i >= 0 && whitespace.indexOf(s.charAt(i)) != -1)
            i--;


        // Get the substring from the front of the string to
        // where the last non-whitespace character is...
        s = s.substring(0, i+1);
    }

    return s;
}
/*
=============================================================
Trim(string) : Returns a copy of a string without leading or trailing spaces
=============================================================
*/
function trim(str)
/*
   PURPOSE: Remove trailing and leading blanks from our string.
   IN: str - the string we want to Trim

   RETVAL: A Trimmed string!
*/
{
    return RTrim(LTrim(str));
}



/*
=============================================================
checkEmail : test and return true/false whether the supplied string is a valid email address or not
=============================================================
*/

function checkMail(email)
{
    var str1=email;
    var arr=str1.split('@');
    var eFlag=true;
    if(arr.length != 2)
    {
        eFlag = false;
    }
    else if(arr[0].length <= 0 || arr[0].indexOf(' ') != -1 || arr[0].indexOf("'") != -1 || arr[0].indexOf('"') != -1 || arr[1].indexOf('.') == -1)
    {
        eFlag = false;
    }
    else
    {
        var dot=arr[1].split('.');
        if(dot.length < 2)
        {
            eFlag = false;
        }
        else
        {
            if(dot[0].length <= 0 || dot[0].indexOf(' ') != -1 || dot[0].indexOf('"') != -1 || dot[0].indexOf("'") != -1)
            {
                eFlag = false;
            }

            for(i=1;i < dot.length;i++)
            {
                if(dot[i].length <= 0 || dot[i].indexOf(' ') != -1 || dot[i].indexOf('"') != -1 || dot[i].indexOf("'") != -1 || dot[i].length > 4)
                {
                    eFlag = false;
                }
            }
        }
    }
    return eFlag;
}


function limitLength(element, maxLength)
{
    if (element.value.length > maxLength)
    {
        element.value = element.value.substring(0, maxLength);
        return 0;
    }
}


function openWindow(url,ht,wt)
{
    window.open(url,'','scrollbars=no,menubar=no,height=ht,width=wt,resizable=yes,toolbar=no,location=no,status=no');
}

function check_numeric_value (obj){
    if(isNaN(parseInt(obj.value)) || parseInt(obj.value) == 'NaN' || parseInt(obj.value) < 0) 
        obj.value = 0; 
    else 
        obj.value = parseInt(obj.value);
}

function check_float_value (obj){
    if(isNaN(parseFloat(obj.value)) || parseFloat(obj.value) == 'NaN' || parseFloat(obj.value) < 0) 
        obj.value = 0; 
    else 
        obj.value = parseFloat(obj.value);
}

function getCreditCardType(accountNumber){

  //start without knowing the credit card type
  var result = "unknown";

  //first check for MasterCard
  if (/^5[1-5]/.test(accountNumber)) {
    document.getElementById('replace_code1').innerHTML="Card Validation Code(CVC2)";
  }

  //then check for Visa
  else if (/^4/.test(accountNumber)) {
    document.getElementById('replace_code1').innerHTML="Card Validation Code(CVV2)";
  }

  //then check for AmEx
  else if (/^3[47]/.test(accountNumber)) {
    document.getElementById('replace_code1').innerHTML="Card Validation Code(CID)";
  } else {
    document.getElementById('replace_code1').innerHTML="Card Validation Code(CVV2)";
  }

}









