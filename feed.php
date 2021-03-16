<?php

// Let us first make sure the php file is 
// interpeted as xml information 
include_once("./includes/config.php");
session_start();
include ("./includes/functions.php");

$curdat = date('Y-m-d');

header("Content-Type: text/xml;charset=iso-8859-1");

echo '<?xml version="1.0" encoding="iso-8859-1"?>';
echo "\n";

$sql = "SELECT s.nSwapId,s.vTitle,date_format(s.dPostDate,'%m/%d/%Y') as 'dPostDate',s.vFeatured, L.vCategoryDesc,s.vUrl,s.nUserId,
				s.nValue,s.vDescription,s.vBrand,s.vType,s.vCondition,s.vYear,s.vSmlImg 
                         FROM " . TABLEPREFIX . "swap s
                                LEFT JOIN " . TABLEPREFIX . "category c on s.nCategoryId=c.nCategoryId  
                                LEFT JOIN " . TABLEPREFIX . "category_lang L on c.nCategoryId = L.cat_id and L.lang_id = '" . $_SESSION['lang_id'] . "' 
                        where s.vPostType='swap' AND s.vDelStatus='0'  
			ORDER BY s.vFeatured DESC,s.dPostDate DESC";

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$num = mysqli_num_rows($result);

// First we print the overall xml information 
echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
echo "\n";
echo "<channel>\n";
echo '<atom:link href="' . SITE_URL . '/feed.php" rel="self" type="application/rss+xml" />';
echo "\n";
// Here comes the global title for you rss 
echo "<title>" . SITE_TITLE . "</title>\n";
// Here comes the url for the page that the rss applies for 
echo "<link>" . SITE_URL . "</link>\n";
// Here comes a short description of the page 
echo "<description>" . HEADING_LATEST_SWAP_FEED . "</description>\n";

// Now over to the dynamic part 

if ($num == 0) {
    echo "<item>\n";
    echo "<title>".MESSAGE_SORRY_NO_RECORDS."</title>\n";
    echo "<link>" . SITE_URL . "/swaplistdetailed.php</link>\n";
    echo "<description>\n";
    echo MESSAGE_SORRY_NO_RECORDS;
    echo "</description>\n";
    echo "<guid isPermaLink='true'>" . SITE_URL . "/swaplistdetailed.php</guid>\n";
    echo "</item>\n";
} else {

    while ($row = mysqli_fetch_array($result)) {
        $pos = strpos($row["vDescription"], ".");
        if($pos <= 0){
            $pos = 250;
        }
        $showDes = str_replace('&', 'and', substr($row["vDescription"], 0, $pos + 1));
        

        echo "<item>\n";
        echo "<title>" . str_replace('&', TEXT_AND , ucfirst($row["vTitle"])) . "</title>\n";
        echo "<link>" . SITE_URL . "/swapitemdisplay.php?swapid=" . $row["nSwapId"] . "&amp;source=s</link>\n";
        echo "<description>\n";
        echo '<![CDATA[' . html_entity_decode(stripslashes($showDes)) . ']]>';
        echo "</description>\n";
        echo "<guid isPermaLink='true'>" . SITE_URL . "/swapitemdisplay.php?swapid=" . $row["nSwapId"] . "&amp;source=s</guid>\n";
        echo "</item>\n";
    }
}
// And at last the closing tags for the overall info 
echo "</channel>\n";
echo "</rss>\n";
?>

