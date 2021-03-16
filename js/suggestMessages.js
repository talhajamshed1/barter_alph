function toggleMsg(idElement)
{
  element = document.getElementById(idElement);
  if(element.style.display!='none')
  {
    element.style.display='none';
  }//end if
  else 
  {
    element.style.display='';
  }//end else
}//end function
