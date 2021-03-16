<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: 			*/
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004-2008 ARMIA INC                                    |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts SocialWare                    |
// +----------------------------------------------------------------------+
// | Authors: simi<simi@armia.com>             		                      |
// |          										                      |
// +----------------------------------------------------------------------+
class Pager 
  {
  /*********************************************************************************** 
   * int findStart (int limit) 
   * Returns the start offset based on $_GET['page'] and $limit 
   ***********************************************************************************/ 
   function findStart($limit,$numrows) 
    { 
	 if ((!isset($_GET['p'])) || ($_GET['p'] == "1")) 
      { 
     	  $start = 0; 
      	 $_GET['p'] = 1; 
      } 
     else 
      { 
       $start = ($_GET['p']-1) * $limit; 
      } 
	  if($start>$numrows)
	  {
	  	 $start = 0; 
	  }
     return $start; 
    } 
  /*********************************************************************************** 
   * int findPages (int count, int limit) 
   * Returns the number of pages needed based on a count and a limit 
   ***********************************************************************************/ 
   function findPages($count, $limit) 
    { 
     $pages = (($count % $limit) == 0) ? $count / $limit : floor($count / $limit) + 1; 
     return $pages; 
    } 
  /*********************************************************************************** 
   * string pageList (int curpage, int pages) 
   * Returns a list of pages in the format of "� < [pages] > �" 
   ***********************************************************************************/ 
   function pageList($curpage, $pages) 
    { 
    $page_list  = "";
	 $str = "";
	 foreach( $_GET as $key=>$value)
	  {
	    if ($key=="message" || $key=="page" || $key=="pagegroup")
		{}
		else
		 $str = $str."&".$key."=".$value;
	   }	  
	 foreach( $_POST as $key=>$value)
	  {
	    if ($key=="message" || $key=="page" || $key=="pagegroup")
		{}
		else
		 $str = $str."&".$key."=".$value;
	   }	  
    
$pagegroup = $_REQUEST['pagegroup'];
$limitset = 10;
if ($pagegroup== ""){
	    $pagegroup = 1;
		}
     /* Print the first and previous page links if necessary */ 
   /* if (($curpage != 1) && ($curpage)) 
      { 
	    $str1 = $str . "&pagegroup=1";
       $page_list .= "  <a href=\"".$_SERVER['PHP_SELF']."?page=1".$str1."\" title=\"First Page\"><font color=#ff0000>�</font></a> "; 
      } */
  
	$prevgrouppage = ($pagegroup - 1) * ($limitset);
	if (($prevgrouppage) > 0) 
      { 
	   $str1 = $str . "&pagegroup=" . ($pagegroup-1);
	   $spage = ($limitset*($pagegroup-1)) + 1 - $limitset;
       $page_list .= "<a href=\"".$_SERVER['PHP_SELF']."?page=".($spage).$str1."\" title=\"Previous Page\" class=\"paging_links\"><b>Previous $limitset pages....</b></a> ";
      } 

  
	
	
	$startpage = (($pagegroup - 1) * $limitset);
	
	
     /* Print the numeric page list; make the current page unlinked and bold */ 
     for ($i=$startpage+1; $i<=$pages; $i++) 
      { 
	    $str1 = $str . "&pagegroup=" . $pagegroup;
	  if ($i > ($startpage + $limitset))
		      break;
       if ($i == $curpage) 
        { 
	        // c h a n g e   l i n k s   c l a s s    h e r e
         $page_list .= ""."<span class=\"current_page\"><b>&nbsp;".$i."&nbsp;</b></span>"; 
        } 
       else 
        { 
		
         $page_list .= "<a href=\"".$_SERVER['PHP_SELF']."?page=".$i.$str1."\" title=\"Page ".$i."\" class='paging_links'>&nbsp;".$i."&nbsp;</a>";
        } 
       $page_list .= " "; 
      } 

    $nextgrouppage = $pagegroup * $limitset;
     /* Print the Next and Last page links if necessary */ 
     if (($nextgrouppage+1) <= $pages) 
      { 
		   $str1 = $str . "&pagegroup=" . ($pagegroup+1);
		   $spage = ($limitset*$pagegroup) + 1;
		   $page_list .= "<a href=\"".$_SERVER['PHP_SELF']."?page=".($spage).$str1."\" title=\"Next PageSet\"><span class=\"paging_links\"><b>....Next $limitset Pages</b></span></a> ";
      } 

  /*   if (($curpage != $pages) && ($pages != 0)) 
      { 
      if (($pages%$limitset) == 0) 
	    $str1 = $str . "&pagegroup=" . ($pages/$limitset);
	  else
	   $str1 = $str . "&pagegroup=" . (($pages/$limitset) + 1);
	   
	   $page_list .= "<a href=\"".$_SERVER['PHP_SELF']."?page=".$pages.$str1."\" title=\"Last Page\"><font color=#ff0000>�</font></a> "; 
      }  */
     $page_list .= "\n"; 

     return $page_list; 
    } 
  /*********************************************************************************** 
   * string nextPrev (int curpage, int pages) 
   * Returns "Previous | Next" string for individual pagination (it's a word!) 
   ***********************************************************************************/ 
   function nextPrev($curpage, $pages) 
    { 
     $next_prev  = ""; 
     $page_list  = "";
	 $str = "";
	 foreach( $_GET as $key=>$value)
	  {
	    if ($key=="message" || $key=="page")
		{}
		else
		 $str = $str."&".$key."=".$value;
	   }
	
	 foreach( $_POST as $key=>$value)
	  {
	    if ($key=="message" || $key=="page")
		{}
		else
		 $str = $str."&".$key."=".$value;
	   }	  	  
     if (($curpage-1) <= 0) 
      { 
       $next_prev .= "<span class=pagsimple>Previous</span>"; 
      } 
     else 
      { 
       $next_prev .= "<a href=\"".$_SERVER['PHP_SELF']."?page=".($curpage-1).$str."\" class=paglink>Previous</a>"; 
      } 

     $next_prev .= " <font color=#000000>|</font> "; 

     if (($curpage+1) > $pages) 
      { 
       $next_prev .= "<span class=pagsimple>Next</span>"; 
      } 
     else 
      { 
       $next_prev .= "<a href=\"".$_SERVER['PHP_SELF']."?page=".($curpage+1).$str."\" class=paglink>Next</a>"; 
      } 
     $next_prev .= "\n";
     return $next_prev; 
    } 
  } 
?>
