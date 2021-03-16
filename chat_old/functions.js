function show_smilies(sender)
{
	if(sender)
	{
	  var getStyle = document.getElementById('tools').style.visibility;
	  if(getStyle == "visible")
		document.getElementById('tools').style.visibility = "hidden";
	  else
		document.getElementById('tools').style.visibility = "visible";
	}
	else
	{
		document.getElementById('tools').style.visibility = "hidden";
	}

}
function add_code(code)
{
  document.getElementById('text_content').value = document.getElementById('text_content').value + code;
  show_smilies();
  if (document.getElementById('smilie'))
	  document.getElementById('smilie').value = code;
  //document.getElementById('smilie').value = code;
}
function get_message(div_id,sender)
{
    setInterval ("get_new_message('"+div_id+"',"+sender+")", 1500 );   
}
function send_message(div_id,content_id,sender)
{
	myDate1 = new Date(); 
        timestamp1 = myDate1.getTime();
	subject_id = div_id;
	content = document.getElementById(content_id).value.trim();
	if(content.length)
	{   
		document.getElementById(content_id).value = "";
		document.getElementById(content_id).focus();
		http.open("GET", "script_page.php?uid="+sender+"&set=1&t="+timestamp1+"&content=" + escape(content), false);
		http.onreadystatechange = handleHttpResponse;
		http.send(null);       	 
	}
}	
function get_new_message(div_id,sender)
{
	myDate = new Date(); 
	timestamp = myDate.getTime();
	subject_id = div_id;
    http.open("GET", "script_page.php?uid="+sender+"&get=1&t="+timestamp+"", true);
	http.onreadystatechange = handleHttpResponse;
	http.send(null);
}
