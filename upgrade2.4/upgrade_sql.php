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
 execute_sql("ALTER TABLE `".TABLEPREFIX."Banners` RENAME TO `".TABLEPREFIX."banners`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Banners_lang` RENAME TO `".TABLEPREFIX."banners_lang`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Chat` RENAME TO `".TABLEPREFIX."chat`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Client_Module_Category` RENAME TO `".TABLEPREFIX."client_module_category`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."CounterOffer` RENAME TO `".TABLEPREFIX."counteroffer`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."CreditPayments` RENAME TO `".TABLEPREFIX."creditpayments`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."escrowRangeFee` RENAME TO `".TABLEPREFIX."escrowrangefee`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Faq` RENAME TO `".TABLEPREFIX."faq`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Faq_lang` RENAME TO `".TABLEPREFIX."faq_lang`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Gallery` RENAME TO `".TABLEPREFIX."gallery`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Help` RENAME TO `".TABLEPREFIX."help`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."HelpCategory` RENAME TO `".TABLEPREFIX."helpcategory`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."HelpCategory_lang` RENAME TO `".TABLEPREFIX."helpCategory_lang`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Help_lang` RENAME TO `".TABLEPREFIX."help_lang`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."ListingFee` RENAME TO `".TABLEPREFIX."listingfee`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Messages` RENAME TO `".TABLEPREFIX."messages`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."MetaTags` RENAME TO `".TABLEPREFIX."metatags`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."MetaTags_lang` RENAME TO `".TABLEPREFIX."metatags_lang`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Online` RENAME TO `".TABLEPREFIX."online`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Plan` RENAME TO `".TABLEPREFIX."plan`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Plan_lang` RENAME TO `".TABLEPREFIX."plan_lang`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."PointHistory` RENAME TO `".TABLEPREFIX."pointhistory`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."Smilies` RENAME TO `".TABLEPREFIX."smilies`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."SuccessFee` RENAME TO `".TABLEPREFIX."successfee`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."SuccessTransactionPayments` RENAME TO `".TABLEPREFIX."successtransactionpayments`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."UserCredits` RENAME TO `".TABLEPREFIX."usercredits`");
 execute_sql("ALTER TABLE `".TABLEPREFIX."swap` ADD COLUMN nQuantity double");
 
 

//Temp comment starts here
//Banner Table
//$sql = "select nPlanId,vPlanName from `".TABLEPREFIX."Plan`";//getting the plan name and id from the plan table
//$res = execute_sql($sql);
//while ($row = mysqli_fetch_object($res)){
    
     execute_sql("ALTER TABLE `".TABLEPREFIX."swaptxn`
	CHANGE COLUMN `nSTId` `nSTId` BIGINT(20) NOT NULL AUTO_INCREMENT FIRST");
        
          execute_sql("ALTER TABLE `".TABLEPREFIX."gallery`
	ADD COLUMN `vMedImg` VARCHAR(200) NULL DEFAULT NULL AFTER `vSmlImg`");
      
    execute_sql("ALTER TABLE `".TABLEPREFIX."saledetails` 
				ADD COLUMN `vAddress1` VARCHAR(100) NULL DEFAULT NULL,
				ADD COLUMN `vAddress2` VARCHAR(100) NULL DEFAULT NULL,
				ADD COLUMN `vCity` VARCHAR(100) NULL DEFAULT NULL,
				ADD COLUMN `vState` VARCHAR(100) NULL DEFAULT NULL,
				ADD COLUMN `vCountry` VARCHAR(100) NULL DEFAULT NULL,
				ADD COLUMN `nZip` VARCHAR(11) NULL DEFAULT NULL,
				ADD COLUMN `vPhone` VARCHAR(50) NULL DEFAULT NULL");
    
        execute_sql("CREATE TABLE IF NOT EXISTS `".TABLEPREFIX."sliders` (
            `nSId` INT(11) NOT NULL AUTO_INCREMENT,
            `vName` VARCHAR(200) NULL,
            `vImg` VARCHAR(200) NULL DEFAULT NULL,
            `nDate` DATE NULL DEFAULT '0000-00-00',
            `vActive` ENUM('1','0') NULL DEFAULT '0',
            PRIMARY KEY (`nSId`)
        )
        COLLATE='latin1_swedish_ci'
        ENGINE=MyISAM;");
    execute_sql("ALTER TABLE `".TABLEPREFIX."sliders`
	ADD COLUMN `vlocUrl` varchar(200) default NULL");
    /*execute_sql("INSERT INTO `".TABLEPREFIX."content` ( `content_id` ,`content_name` ,`content_type` ,`content_status`)
                VALUES (NULL , 'homebannertexts', 'page', 'y')");*/

 

//    ;
//}

?>