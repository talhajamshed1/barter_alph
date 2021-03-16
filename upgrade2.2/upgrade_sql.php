<?php
include ("../includes/config.php");
function execute_sql($sql){//just execute the query
    $res = mysqli_query($connection, $sql) or die($sql."<br />".mysqli_error($connection));
    return $res;
}
function sql_safe($str){
     $str = stripslashes($str);
    return addslashes($str);
}

//Temp comment starts here
//Banner Table
$sql = "CREATE TABLE `".TABLEPREFIX."banners_lang` (
        `banner_lang_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `banner_id` INT NOT NULL COMMENT  'banner id',
        `lang_id` INT NOT NULL COMMENT  'language id',
        `vName` VARCHAR( 200 ) NOT NULL COMMENT  'banner name'
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";//for creating banner language table
execute_sql($sql);

$sql = "select nBId,vName from `".TABLEPREFIX."banners`";//getting the banner name and id from the banner table
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "insert into `".TABLEPREFIX."banners_lang`
                (`banner_id`,`lang_id`,`vName`)
               values
                 ('".$row->nBId."','1','".sql_safe($row->vName)."')
            ";//inserting them to the language table
    execute_sql($sql2);
}
execute_sql("ALTER TABLE  `".TABLEPREFIX."banners` DROP  `vName`");//drop the unwanted field in the banner table

//Category Table
$sql = "CREATE TABLE `".TABLEPREFIX."category_lang` (
        `cat_lang_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `cat_id` INT NOT NULL COMMENT  'category id',
        `lang_id` INT NOT NULL COMMENT  'language id',
        `vCategoryDesc` VARCHAR( 100 ) NOT NULL COMMENT  'category name'
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";//for creating category language table
execute_sql($sql);

$sql = "select nCategoryId,vCategoryDesc from `".TABLEPREFIX."category`";//getting the category name and id from the category table
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "insert into `".TABLEPREFIX."category_lang`
                (`cat_id`,`lang_id`,`vCategoryDesc`)
               values
                 ('".$row->nCategoryId."','1','".sql_safe($row->vCategoryDesc)."')
            ";//inserting them to the language table
    execute_sql($sql2);
}
execute_sql("ALTER TABLE  `".TABLEPREFIX."category` DROP  `vCategoryDesc`");//drop the unwanted field in the category table

//Faq Table
$sql = "CREATE TABLE `".TABLEPREFIX."faq_lang` (
        `faq_lang_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `faq_id` INT NOT NULL COMMENT  'faq id',
        `lang_id` INT NOT NULL COMMENT  'language id',
        `vTitle` VARCHAR( 255 ) NOT NULL COMMENT  'question',
        `vDes` text COMMENT  'answer'
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";//for creating faq language table
execute_sql($sql);

$sql = "select nFId,vTitle,vDes from `".TABLEPREFIX."faq`";//getting the faq Q and A and id from the faq table
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "insert into `".TABLEPREFIX."faq_lang`
                (`faq_id`,`lang_id`,`vTitle`,`vDes`)
               values
                 ('".$row->nFId."','1','".sql_safe($row->vTitle)."','".sql_safe($row->vDes)."')
            ";//inserting them to the language table
    execute_sql($sql2);
}
execute_sql("ALTER TABLE `".TABLEPREFIX."faq`  DROP `vTitle`,  DROP `vDes`;");//drop the unwanted field in the faq table


//Help Category Table
$sql = "CREATE TABLE `".TABLEPREFIX."helpcategory_lang` (
        `help_cat_lang_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `help_cat_id` INT NOT NULL COMMENT  'help category id',
        `lang_id` INT NOT NULL COMMENT  'language id',
        `vHctitle` VARCHAR( 100 ) NOT NULL COMMENT  'help category name'
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";//for creating help category language table
execute_sql($sql);

$sql = "select nHcId,vHctitle from `".TABLEPREFIX."helpcategory`";//getting the help category name and id from the help category table
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "insert into `".TABLEPREFIX."helpcategory_lang`
                (`help_cat_id`,`lang_id`,`vHctitle`)
               values
                 ('".$row->nHcId."','1','".sql_safe($row->vHctitle)."')
            ";//inserting them to the language table
    execute_sql($sql2);
}
execute_sql("ALTER TABLE  `".TABLEPREFIX."helpcategory` DROP  `vHctitle`");//drop the unwanted field in the help category table


//Help Table
$sql = "CREATE TABLE `".TABLEPREFIX."help_lang` (
        `help_lang_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `help_id` INT NOT NULL COMMENT  'help id',
        `lang_id` INT NOT NULL COMMENT  'language id',
        `vHtitle` VARCHAR( 100 ) NOT NULL COMMENT  'help title',
        `vHdescription` text COMMENT  'help text'
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";//for creating help language table
execute_sql($sql);

$sql = "select nHId,vHtitle,vHdescription from `".TABLEPREFIX."Help`";//getting the help title and description and id from the help table
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "insert into `".TABLEPREFIX."help_lang`
                (`help_id`,`lang_id`,`vHtitle`,`vHdescription`)
               values
                 ('".$row->nHId."','1','".sql_safe($row->vHtitle)."','".sql_safe($row->vHdescription)."')
            ";//inserting them to the language table
    execute_sql($sql2);
}
execute_sql("ALTER TABLE `".TABLEPREFIX."Help`  DROP `vHtitle`,  DROP `vHdescription`;");//drop the unwanted field in the help table

//Meta Tags Table
$sql = "CREATE TABLE `".TABLEPREFIX."metatags_lang` (
        `meta_lang_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `meta_id` INT NOT NULL COMMENT  'meta id',
        `lang_id` INT NOT NULL COMMENT  'language id',
        `vTitle` VARCHAR( 200 ) NOT NULL COMMENT  'meta title',
        `vKeywords` text COMMENT  'meta keywords',
        `vDescription` text COMMENT  'meta description'
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";//for creating meta language table
execute_sql($sql);

$sql = "select nId,vTitle,vKeywords,vDescription from `".TABLEPREFIX."metatags`";//getting the meta details and id from the metatags table
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "insert into `".TABLEPREFIX."metatags_lang`
                (`meta_id`,`lang_id`,`vTitle`,`vKeywords`,`vDescription`)
               values
                 ('".$row->nId."','1','".sql_safe($row->vTitle)."','".sql_safe($row->vKeywords)."','".sql_safe($row->vDescription)."')
            ";//inserting them to the language table
    execute_sql($sql2);
}
execute_sql("ALTER TABLE `".TABLEPREFIX."metatags`  DROP `vTitle`,  DROP `vKeywords`,  DROP `vDescription`");//drop the unwanted field in the metatags table
execute_sql("ALTER TABLE `".TABLEPREFIX."metatags` CHANGE  `vPageName`  `vPageName` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL");


//Plan Table
$sql = "CREATE TABLE `".TABLEPREFIX."plan_lang` (
        `plan_lang_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `plan_id` INT NOT NULL COMMENT  'plan id',
        `lang_id` INT NOT NULL COMMENT  'language id',
        `vPlanName` VARCHAR( 100 ) NOT NULL COMMENT  'plan name'
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";//for creating plan language table
execute_sql($sql);

$sql = "select nPlanId,vPlanName from `".TABLEPREFIX."plan`";//getting the plan name and id from the plan table
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "insert into `".TABLEPREFIX."plan_lang`
                (`plan_id`,`lang_id`,`vPlanName`)
               values
                 ('".$row->nPlanId."','1','".sql_safe($row->vPlanName)."')
            ";//inserting them to the language table
    execute_sql($sql2);
}
execute_sql("ALTER TABLE  `".TABLEPREFIX."plan` DROP  `vPlanName`");//drop the unwanted field in the plan table


//CMS Table
$sql = "CREATE TABLE  `".TABLEPREFIX."content` (
        `content_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `content_name` VARCHAR( 100 ) NOT NULL UNIQUE COMMENT  'content name',
        `content_type` ENUM(  'page',  'email', '' ) NOT NULL DEFAULT  'page' COMMENT  'content type',
        `content_status` ENUM(  'y',  'n' ) NOT NULL DEFAULT  'y' COMMENT  'status'
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";//for creating content table
execute_sql($sql);

$lookup_vals_text = "'sitetitle','sitename','Meta Keywords','Meta Description','headerCaption','PointName'";
$sql = "select nLookUpCode,vLookUpDesc from `".TABLEPREFIX."lookup` where `nLookUpCode` IN (".$lookup_vals_text.")";//getting the values from the lookup table
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "insert into `".TABLEPREFIX."content`
                (`content_name`,`content_type`)
               values
                 ('".sql_safe($row->nLookUpCode)."','')
            ";//inserting them to the content table
    execute_sql($sql2);
}


$lookup_vals_page = "'contact','about','wish','swap','sell','privacy','terms','hintro'";
$sql = "select nLookUpCode,vLookUpDesc from `".TABLEPREFIX."lookup` where `nLookUpCode` IN (".$lookup_vals_page.")";//getting the page values from the lookup table
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "insert into `".TABLEPREFIX."content`
                (`content_name`,`content_type`)
               values
                 ('".sql_safe($row->nLookUpCode)."','page')
            ";//inserting them to the content table
    execute_sql($sql2);
}

$lookup_vals_email = "'addsales','addsurvey','soldout','contactus','featured','forgotpass','payea','payea2','passwordreset','tellfrnd','settled','payea3','plansubcancel','expired','points','SuccessFeeMail'";


$sql = "CREATE TABLE `".TABLEPREFIX."content_lang` (
        `content_lang_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `content_id` INT NOT NULL COMMENT  'content id',
        `lang_id` INT NOT NULL COMMENT  'language id',
        `content` TEXT COMMENT  'actual content',
        `content_title` TEXT COMMENT  'content title - mainly for email subject'
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";//for creating content language table
execute_sql($sql);

$sql = "select C.content_id, L.vLookUpDesc from `".TABLEPREFIX."lookup` L
            left join `".TABLEPREFIX."content` C on L.nLookUpCode = C.content_name
        where L.nLookUpCode IN (".$lookup_vals_text.",".$lookup_vals_page.")";//getting the lookup values and content id from the tables
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "insert into `".TABLEPREFIX."content_lang`
                (`content_id`,`lang_id`,`content`)
               values
                 ('".$row->content_id."','1','".sql_safe($row->vLookUpDesc)."')
            ";//inserting them to the language table
    execute_sql($sql2);
}

execute_sql("DELETE from `".TABLEPREFIX."lookup` where nLookUpCode IN (".$lookup_vals_text.",".$lookup_vals_page.",".$lookup_vals_email.")");//drop the unwanted entries in the lookup table

//Language table
$sql = "CREATE TABLE `".TABLEPREFIX."lang` (
        `lang_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `lang_name` VARCHAR( 100 ) NOT NULL UNIQUE COMMENT  'language name',
        `folder_name` VARCHAR( 100 ) NOT NULL UNIQUE COMMENT  'language folder name',
        `flag_file` VARCHAR( 100 ) NOT NULL COMMENT  'flag file name',
        `lang_status` ENUM(  'y',  'n' ) NOT NULL DEFAULT  'y' COMMENT  'status',
        `country_abbrev` TEXT COMMENT  'country abbreviations for ip search'
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";//for creating language table
execute_sql($sql);

execute_sql("INSERT INTO `".TABLEPREFIX."lang` (`lang_id`, `lang_name`, `folder_name`, `flag_file`, `lang_status`, `country_abbrev`) VALUES
(1, 'English', 'en', '1309175744_flag.png', 'y', 'US,UK'),
(2, 'French', 'fr', '1309178676_flag.gif', 'y', 'FR'),
(5, 'Spanish', 'es', '1313581288_flag.png', 'y', 'ES'),
(6, 'German', 'de', '1313581399_flag.gif', 'y', 'DE')");

execute_sql("INSERT INTO `".TABLEPREFIX."lookup` (`nLookUpCode`, `vLookUpDesc`) VALUES ('language_by_ip', 'no')");


//Updating titles for the content table
execute_sql("UPDATE `".TABLEPREFIX."content` C
               LEFT JOIN `".TABLEPREFIX."content_lang` L on (C.content_id = L.content_id)
                   SET L.content_title='About Us'
            WHERE C.content_name = 'about'
            ");
execute_sql("UPDATE `".TABLEPREFIX."content` C
               LEFT JOIN `".TABLEPREFIX."content_lang` L on (C.content_id = L.content_id)
                   SET L.content_title='Contact Us'
            WHERE C.content_name = 'contact'
            ");
execute_sql("UPDATE `".TABLEPREFIX."content` C
               LEFT JOIN `".TABLEPREFIX."content_lang` L on (C.content_id = L.content_id)
                   SET L.content_title='Wish'
            WHERE C.content_name = 'wish'
            ");
execute_sql("UPDATE `".TABLEPREFIX."content` C
               LEFT JOIN `".TABLEPREFIX."content_lang` L on (C.content_id = L.content_id)
                   SET L.content_title='Sell'
            WHERE C.content_name = 'sell'
            ");
execute_sql("UPDATE `".TABLEPREFIX."content` C
               LEFT JOIN `".TABLEPREFIX."content_lang` L on (C.content_id = L.content_id)
                   SET L.content_title='Swap'
            WHERE C.content_name = 'swap'
            ");
execute_sql("UPDATE `".TABLEPREFIX."content` C
               LEFT JOIN `".TABLEPREFIX."content_lang` L on (C.content_id = L.content_id)
                   SET L.content_title='Privacy Policy'
            WHERE C.content_name = 'privacy'
            ");
execute_sql("UPDATE `".TABLEPREFIX."content` C
               LEFT JOIN `".TABLEPREFIX."content_lang` L on (C.content_id = L.content_id)
                   SET L.content_title='Terms'
            WHERE C.content_name = 'terms'
            ");
execute_sql("UPDATE `".TABLEPREFIX."content` C
               LEFT JOIN `".TABLEPREFIX."content_lang` L on (C.content_id = L.content_id)
                   SET L.content_title='Eswap'
                WHERE C.content_name = 'hintro'
            ");

execute_sql("ALTER TABLE  `".TABLEPREFIX."users` ADD  `profile_image` VARCHAR( 255 ) NOT NULL, ADD  `preferred_language` INT NOT NULL DEFAULT  '1' COMMENT  'language id'");
execute_sql("ALTER TABLE  `".TABLEPREFIX."users` ADD  `vIMStatus` ENUM(  'Y',  'N' ) NOT NULL DEFAULT  'N' COMMENT  'show image to all'");
execute_sql("INSERT INTO `".TABLEPREFIX."content` (`content_id`, `content_name`, `content_type`, `content_status`) VALUES ('31', 'cashback', 'page', 'y')");
execute_sql("INSERT INTO `".TABLEPREFIX."content_lang` (`content_lang_id`, `content_id`, `lang_id`, `content`, `content_title`) VALUES ('31', '31', '1', '<p>Are you tired of this?  Working hard to sale your merchandise on websites, only to see your hard earned money has been taken away from you due to high listing fees and etc...  Well, {site_name} encourages our members to sale on our site.  We present to you, our cash back Sales Rewards Program.</p>
<p>As a member of {site_name}, you could start earning cash back when you sale items on our site. Because we credit back to our Sellers 1% of our small 2% commission for listing an item to the site, as cash back bonuses on all sales over {currency_code} 300.  This is simply our way of showing our appreciation towards you because, we believe, You Are the Sales Associate!</p>
<p>Although, we provide you with the tools needed to sale on the site, the best tool we can provide you with is <b>MOTIVATION.</b>  Whether you are an experienced internet Seller, or a beginner, with minimal effort this program will work for anyone.  While other Sites are busy reaping the benefits from Sellers on their sites.  We are busy building shared interest with our Associates. We promise you, you will love our cash back <b><u>Sales Rewards Program</u></b>, and you can take that to the bank!</p>
<p></p>', 'Cash Back Bonus Policy')");
execute_sql("ALTER TABLE  `".TABLEPREFIX."sale` ADD  `nPoint` DOUBLE NOT NULL DEFAULT  '0'");//sale item table
execute_sql("ALTER TABLE  `".TABLEPREFIX."saledetails` ADD  `nPoint` DOUBLE NOT NULL DEFAULT  '0'");//sale table
execute_sql("RENAME TABLE `".TABLEPREFIX."swaptxn` TO `".TABLEPREFIX."swaptxn_old`");
execute_sql("
        CREATE TABLE IF NOT EXISTS `".TABLEPREFIX."swaptxn` (
          `nSwapId` varchar(55) NOT NULL,
          `nSwapReturnId` varchar(55) NOT NULL,
          `nUserId` int(11) default NULL,
          `nUserReturnId` int(11) NOT NULL,
          `vText` text,
          `dDate` datetime default NULL,
          `nAmountGive` double default '0',
          `nAmountTake` double default '0',
          `vStatus` varchar(10) default NULL,
          `vPostType` varchar(10) default NULL,
          `vBlink` char(1) NOT NULL default 'B',
          `nSTId` bigint(20) NOT NULL auto_increment,
          `nParentId` int(11) NOT NULL default '0',
          `nPointGive` int(11) NOT NULL default '0',
          `nPointTake` int(11) NOT NULL default '0',
          PRIMARY KEY  (`nSTId`)
        ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci
    ");
/*
//this set of codes are not required
execute_sql("ALTER TABLE  `".TABLEPREFIX."swaptxn` DROP INDEX  `nSwapId`");
execute_sql("ALTER TABLE  `".TABLEPREFIX."swaptxn` ADD PRIMARY KEY (  `nSTId` )");
execute_sql("ALTER TABLE  `".TABLEPREFIX."swaptxn` CHANGE  `nSwapId`  `nSwapId` VARCHAR( 55 ) NOT NULL");
execute_sql("ALTER TABLE  `".TABLEPREFIX."swaptxn` CHANGE  `nSTId`  `nSTId` BIGINT( 20 ) NOT NULL AUTO_INCREMENT");
execute_sql("ALTER TABLE  `".TABLEPREFIX."swaptxn` ADD  `nSwapReturnId` VARCHAR( 55 ) NOT NULL AFTER  `nSwapId`, ADD  `nUserReturnId` INT NOT NULL AFTER  `nUserId`, ADD  `nParentId` INT NOT NULL DEFAULT  '0', ADD  `nPointGive` INT NOT NULL DEFAULT  '0', ADD  `nPointTake` INT NOT NULL DEFAULT  '0'");
  */

$sql = "select S.*, Sw.nUserId as other_user from `".TABLEPREFIX."swaptxn_old` S
            left join  `".TABLEPREFIX."swap` Sw on S.nSwapId = Sw.nSwapId ";//getting all the records from the swaptxn table
$res = execute_sql($sql);
while ($row = mysqli_fetch_object($res)){
    $sql2 = "select nSwapReturnId from `".TABLEPREFIX."swapreturn`
                 where nSTId = '".$row->nSTId."'";//getting all the records from the swapreturn table
    $res2 = execute_sql($sql2);
    $swap_return_ids = '';
    while ($row2 = mysqli_fetch_object($res2)){
        if ($swap_return_ids=='') $swap_return_ids = $row2->nSwapReturnId;
        else $swap_return_ids .= ','.$row2->nSwapReturnId;
    }
    $sql2 = "insert into `".TABLEPREFIX."swaptxn`
                (`nSwapId`,`nSwapReturnId`,`nUserId`,`nUserReturnId`,`vText`,`dDate`,`nAmountGive`,`nAmountTake`,`vStatus`,`vPostType`,`vBlink`,`nSTId`,`nParentId`,`nPointGive`,`nPointTake`)
               values
                 ('".$swap_return_ids."','".$row->nSwapId."','".$row->nUserId."','".$row->other_user."','".sql_safe($row->vText)."','".$row->dDate."','".$row->nAmountGive."','".$row->nAmountTake."','".$row->vStatus."','".$row->vPostType."','".$row->vBlink."','".$row->nSTId."','0','0','0')
            ";//inserting them to the new swap transaction table
    execute_sql($sql2);
}


execute_sql("INSERT INTO `".TABLEPREFIX."lookup` (
            `nLookUpCode` ,
            `vLookUpDesc`
            )
            VALUES (
            'EscrowCommissionType', 'percentage'
            )");
execute_sql("INSERT INTO `".TABLEPREFIX."lookup` (
            `nLookUpCode` ,
            `vLookUpDesc`
            )
            VALUES (
            'EscrowFixed', '10'
            )");
execute_sql("INSERT INTO `".TABLEPREFIX."lookup` (
            `nLookUpCode` ,
            `vLookUpDesc`
            )
            VALUES (
            'freeTransactionsPerMonth', '0'
            )");
execute_sql("CREATE TABLE `".TABLEPREFIX."escrowrangefee` (
            `nLId` int( 11 ) NOT NULL AUTO_INCREMENT ,
            `nFrom` BIGINT NOT NULL default '0',
            `nTo` BIGINT NOT NULL default '0',
            `nPrice` BIGINT NOT NULL default '0',
            `above` BIGINT NOT NULL default '0',
            `vActive` enum( '0', '1' ) NOT NULL default '0',
            `nLPosition` int( 11 ) NOT NULL default '0',
            PRIMARY KEY ( `nLId` )
            ) ENGINE = MYISAM DEFAULT CHARSET = latin1");
execute_sql("INSERT INTO `".TABLEPREFIX."lookup` (`nLookUpCode`, `vLookUpDesc`) VALUES ('monthlyFeePerTransaction', '0')");

execute_sql("ALTER TABLE `".TABLEPREFIX."saleextra` ADD  `nPoint` FLOAT NOT NULL DEFAULT  '0'");
execute_sql("INSERT INTO `".TABLEPREFIX."lookup` (`nLookUpCode`, `vLookUpDesc`) VALUES ('plan_system', 'no')");

execute_sql("CREATE TABLE `".TABLEPREFIX."monthlyTransactionPayments` (
  `nId` int(11) NOT NULL auto_increment,
  `nUserId` int(11) NOT NULL default '0',
  `nAmount` double NOT NULL default '0',
  `nProdId` double NOT NULL default '0',
  `vTxnId` varchar(200) NOT NULL default '',
  `vMethod` char(2) NOT NULL default '00',
  `dDate` date NOT NULL default '0000-00-00',
  `vStatus` char(1) NOT NULL default 'P',
  `vName` varchar(200) NOT NULL default '',
  `vBank` varchar(200) NOT NULL default '',
  `vReferenceNo` varchar(200) NOT NULL default '',
  `dReferenceDate` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`nId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1");//monthly fee transaction payments

execute_sql("ALTER TABLE `".TABLEPREFIX."listingfee` ADD `above` BIGINT NOT NULL AFTER `nTo` ");



execute_sql("ALTER TABLE `".TABLEPREFIX."escrowrangefee` CHANGE `nFrom` `nFrom` DOUBLE NOT NULL DEFAULT '0',
CHANGE `nTo` `nTo` DOUBLE NOT NULL DEFAULT '0'");
execute_sql("UPDATE `".TABLEPREFIX."client_module_category` SET `vCategoryFile` = 'categorydetail.php' WHERE `vCategoryTitle` = 'Category Display'");
execute_sql("INSERT INTO `".TABLEPREFIX."lookup` (
`nLookUpCode` ,
`vLookUpDesc`
)
VALUES (
'welcomeImage', ''
)");
 
?>