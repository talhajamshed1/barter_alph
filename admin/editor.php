<?php
// Cross-Browser Rich Text Editor
// http://www.kevinroth.com/rte/demo.htm
// Written by Kevin Roth (kevin@NOSPAMkevinroth.com - remove NOSPAM)
// Visit the support forums at http://www.kevinroth.com/forums/index.php?c=2
?>
<script language="JavaScript" type="text/javascript" src="./editor/html2xhtml.min.js"></script>
<script language="JavaScript" type="text/javascript" src="./editor/richtext_compressed.js"></script>
<script language="JavaScript" type="text/javascript">
<!--
//Usage: initRTE(imagesPath, includesPath, cssFile, genXHTML, encHTML)
initRTE("./editor/images/", "./editor/", "", true);
//-->
</script>
<noscript><p><b>Javascript must be enabled to use this form.</b></p></noscript>
<?php
    function rteSafe($strText) {
            //returns safe code for preloading in the RTE
            $tmpString = $strText;

            //convert all types of single quotes
            $tmpString = str_replace(chr(145), chr(39), $tmpString);
            $tmpString = str_replace(chr(146), chr(39), $tmpString);
            $tmpString = str_replace("'", "&#39;", $tmpString);

            //convert all types of double quotes
            $tmpString = str_replace(chr(147), chr(34), $tmpString);
            $tmpString = str_replace(chr(148), chr(34), $tmpString);
    //	$tmpString = str_replace("\"", "\"", $tmpString);

            //replace carriage returns & line feeds
            $tmpString = str_replace(chr(10), " ", $tmpString);
            $tmpString = str_replace(chr(13), " ", $tmpString);

            return $tmpString;
    }
?>