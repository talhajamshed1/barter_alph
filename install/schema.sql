SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- --------------------------------------------------------

--
-- Table structure for table `eswap_affiliate`
--

CREATE TABLE IF NOT EXISTS `eswap_affiliate` (
  `nAffiliateId` int(11) NOT NULL auto_increment,
  `vLoginName` varchar(100) default NULL,
  `vPassword` varchar(100) default NULL,
  `vFirstName` varchar(100) default NULL,
  `vLastName` varchar(100) default NULL,
  `vAddress1` varchar(100) default NULL,
  `vAddress2` varchar(100) default NULL,
  `vCity` varchar(100) default NULL,
  `vState` varchar(100) default NULL,
  `vCountry` varchar(100) default NULL,
  `nZip` int(11) default NULL,
  `vPhone` varchar(50) default NULL,
  `vFax` varchar(50) default NULL,
  `vEmail` varchar(100) default NULL,
  `vUrl` varchar(255) default NULL,
  `nAmount` double default '0',
  `vDelStatus` varchar(10) NOT NULL default '0',
  PRIMARY KEY  (`nAffiliateId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_Banners`
--

CREATE TABLE IF NOT EXISTS `eswap_banners` (
  `nBId` int(11) NOT NULL auto_increment,
  `vlocUrl` varchar(200) default NULL,
  `vImg` varchar(200) default NULL,
  `nDate` date default '0000-00-00',
  `vWidth` varchar(100) default NULL,
  `vHeight` varchar(100) default NULL,
  `nPosition` int(11) default NULL,
  `vActive` enum('1','0') default '0',
  `vLocation` varchar(10) default NULL,
  `nCount` int(11) default '0',
  PRIMARY KEY  (`nBId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_banners_lang`
--

CREATE TABLE IF NOT EXISTS `eswap_banners_lang` (
  `banner_lang_id` int(11) NOT NULL auto_increment,
  `banner_id` int(11) NOT NULL COMMENT 'banner id',
  `lang_id` int(11) NOT NULL COMMENT 'language id',
  `vName` varchar(200) NOT NULL COMMENT 'blog name',
  PRIMARY KEY  (`banner_lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_batches`
--

CREATE TABLE IF NOT EXISTS `eswap_batches` (
  `nBatchId` bigint(20) NOT NULL auto_increment,
  `vBatchName` varchar(100) default NULL,
  `dDate` datetime default NULL,
  PRIMARY KEY  (`nBatchId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_cashtxn`
--

CREATE TABLE IF NOT EXISTS `eswap_cashtxn` (
  `nCashTxnId` int(11) NOT NULL auto_increment,
  `nUserId` int(11) default '0',
  `nAffiliateId` int(11) default '0',
  `nAmount` double default '0',
  `nCommission` double default '0',
  `dDate` datetime default NULL,
  `vMode` varchar(100) default NULL,
  `vModeNo` varchar(100) default NULL,
  `vReason` varchar(100) default NULL,
  `vKey` varchar(100) default NULL,
  PRIMARY KEY  (`nCashTxnId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_category`
--

CREATE TABLE IF NOT EXISTS `eswap_category` (
  `nCategoryId` int(11) NOT NULL auto_increment,
  `nParentId` int(11) NOT NULL default '0',
  `vRoute` varchar(100) default NULL,
  `nCount` int(11) default '0',
  `nPosition` int(11) default '0',
  PRIMARY KEY  (`nCategoryId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_category_lang`
--

CREATE TABLE IF NOT EXISTS `eswap_category_lang` (
  `cat_lang_id` int(11) NOT NULL auto_increment,
  `cat_id` int(11) NOT NULL COMMENT 'category id',
  `lang_id` int(11) NOT NULL COMMENT 'language id',
  `vCategoryDesc` varchar(100) NOT NULL COMMENT 'category name',
  PRIMARY KEY  (`cat_lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_chat`
--

CREATE TABLE IF NOT EXISTS `eswap_chat` (
  `nUserId` int(100) NOT NULL default '0',
  `vMsg` varchar(250) NOT NULL default '',
  `vTimeStamp` varchar(250) NOT NULL default '',
  `vDisplayed` char(1) NOT NULL default '',
  `nFromId` int(100) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_client_module_category`
--

CREATE TABLE IF NOT EXISTS `eswap_client_module_category` (
  `nCategoryId` mediumint(9) NOT NULL auto_increment,
  `nParentId` int(11) default '0',
  `vCategoryTitle` varchar(100) NOT NULL default '',
  `vCategoryFile` varchar(100) NOT NULL default '',
  `nCposition` varchar(10) default NULL,
  `vActive` enum('0','1') default NULL,
  `nTmp_status` int(11) default '0',
  PRIMARY KEY  (`nCategoryId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_content`
--

CREATE TABLE IF NOT EXISTS `eswap_content` (
  `content_id` int(11) NOT NULL auto_increment,
  `content_name` varchar(100) NOT NULL COMMENT 'content name',
  `content_type` enum('page','email','') NOT NULL default 'page' COMMENT 'content type',
  `content_status` enum('y','n') NOT NULL default 'y' COMMENT 'status',
  PRIMARY KEY  (`content_id`),
  UNIQUE KEY `content_name` (`content_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_content_lang`
--

CREATE TABLE IF NOT EXISTS `eswap_content_lang` (
  `content_lang_id` int(11) NOT NULL auto_increment,
  `content_id` int(11) NOT NULL COMMENT 'content id',
  `lang_id` int(11) NOT NULL COMMENT 'language id',
  `content` text COMMENT 'actual content',
  `content_title` text NOT NULL COMMENT 'content title - mainly for email subject',
  PRIMARY KEY  (`content_lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_counteroffer`
--

CREATE TABLE IF NOT EXISTS `eswap_counteroffer` (
  `nCId` int(11) NOT NULL auto_increment,
  `nSTId` bigint(20) NOT NULL default '0',
  `nPostedBy` bigint(20) NOT NULL default '0',
  `nPostedTo` bigint(20) NOT NULL default '0',
  `nPrice` float NOT NULL default '0',
  `vDes` text NOT NULL,
  `vType` varchar(4) NOT NULL default '',
  `vStatus` char(1) NOT NULL default '',
  PRIMARY KEY  (`nCId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_creditpayments`
--

CREATE TABLE IF NOT EXISTS `eswap_creditpayments` (
  `nId` int(11) NOT NULL auto_increment,
  `nUserId` int(11) NOT NULL default '0',
  `nAmount` double NOT NULL default '0',
  `nPoints` double NOT NULL default '0',
  `vTxnId` varchar(200) NOT NULL default '',
  `vMethod` char(2) NOT NULL default '00',
  `dDate` date NOT NULL default '0000-00-00',
  `vCurrentTransaction` varchar(200) NOT NULL default '',
  `vStatus` char(1) NOT NULL default 'P',
  `vName` varchar(200) NOT NULL default '',
  `vBank` varchar(200) NOT NULL default '',
  `vReferenceNo` varchar(200) NOT NULL default '',
  `dReferenceDate` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`nId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_escrowrangefee`
--

CREATE TABLE IF NOT EXISTS `eswap_escrowrangefee` (
  `nLId` int(11) NOT NULL auto_increment,
  `nFrom` double NOT NULL default '0',
  `nTo` double NOT NULL default '0',
  `above` bigint(20) NOT NULL,
  `nPrice` double NOT NULL default '0',
  `vActive` enum('0','1') NOT NULL default '0',
  `nLPosition` int(11) NOT NULL default '0',
  PRIMARY KEY  (`nLId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_faq`
--

CREATE TABLE IF NOT EXISTS `eswap_faq` (
  `nFId` int(11) NOT NULL auto_increment,
  `nPosition` int(11) default '0',
  `vActive` enum('0','1') default '0',
  PRIMARY KEY  (`nFId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_faq_lang`
--

CREATE TABLE IF NOT EXISTS `eswap_faq_lang` (
  `faq_lang_id` int(11) NOT NULL auto_increment,
  `faq_id` int(11) NOT NULL COMMENT 'faq id',
  `lang_id` int(11) NOT NULL COMMENT 'language id',
  `vTitle` varchar(255) NOT NULL COMMENT 'question',
  `vDes` text COMMENT 'answer',
  PRIMARY KEY  (`faq_lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_gallery`
--

CREATE TABLE IF NOT EXISTS `eswap_gallery` (
  `nId` int(11) NOT NULL auto_increment,
  `nUserId` int(11) default '0',
  `nSwapId` int(11) default '0',
  `nSaleId` int(11) default '0',
  `vImg` varchar(255) default NULL,
  `vDes` text,
  `nTempId` int(11) default '0',
  `vSmlImg` varchar(255) default NULL,
  `vDelStatus` enum('0','1') default '0',
  PRIMARY KEY  (`nId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_help`
--

CREATE TABLE IF NOT EXISTS `eswap_help` (
  `nHId` int(11) NOT NULL auto_increment,
  `nHcId` int(11) default NULL,
  `nHposition` int(11) default NULL,
  `vActive` enum('0','1') default NULL,
  `vHimage` varchar(100) default NULL,
  PRIMARY KEY  (`nHId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_helpCategory`
--

CREATE TABLE IF NOT EXISTS `eswap_helpcategory` (
  `nHcId` int(11) NOT NULL auto_increment,
  `vHtype` varchar(6) default NULL,
  `nHcposition` int(11) default NULL,
  `vActive` enum('0','1') default NULL,
  PRIMARY KEY  (`nHcId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_helpCategory_lang`
--

CREATE TABLE IF NOT EXISTS `eswap_helpcategory_lang` (
  `help_cat_lang_id` int(11) NOT NULL auto_increment,
  `help_cat_id` int(11) NOT NULL COMMENT 'help category id',
  `lang_id` int(11) NOT NULL COMMENT 'language id',
  `vHctitle` varchar(100) NOT NULL COMMENT 'help category name',
  PRIMARY KEY  (`help_cat_lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_help_lang`
--

CREATE TABLE IF NOT EXISTS `eswap_help_lang` (
  `help_lang_id` int(11) NOT NULL auto_increment,
  `help_id` int(11) NOT NULL COMMENT 'help id',
  `lang_id` int(11) NOT NULL COMMENT 'language id',
  `vHtitle` varchar(100) NOT NULL COMMENT 'help title',
  `vHdescription` text COMMENT 'help text',
  PRIMARY KEY  (`help_lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_lang`
--

CREATE TABLE IF NOT EXISTS `eswap_lang` (
  `lang_id` int(11) NOT NULL auto_increment,
  `lang_name` varchar(100) NOT NULL COMMENT 'language name',
  `folder_name` varchar(100) NOT NULL COMMENT 'language folder name',
  `flag_file` varchar(100) NOT NULL COMMENT 'flag file name',
  `lang_status` enum('y','n') NOT NULL default 'y' COMMENT 'status',
  `country_abbrev` text NOT NULL COMMENT 'country abbreviations for ip search',
  PRIMARY KEY  (`lang_id`),
  UNIQUE KEY `lang_name` (`lang_name`),
  UNIQUE KEY `folder_name` (`folder_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_listingfee`
--

CREATE TABLE IF NOT EXISTS `eswap_listingfee` (
  `nLId` int(11) NOT NULL auto_increment,
  `nFrom` double NOT NULL default '0',
  `nTo` double NOT NULL default '0',
  `above` bigint(20) NOT NULL,
  `nPrice` double NOT NULL default '0',
  `vActive` enum('0','1') NOT NULL default '0',
  `nLPosition` int(11) NOT NULL default '0',
  PRIMARY KEY  (`nLId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_lookup`
--

CREATE TABLE IF NOT EXISTS `eswap_lookup` (
  `nLookUpCode` varchar(100) default NULL,
  `vLookUpDesc` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_mandatory`
--

CREATE TABLE IF NOT EXISTS `eswap_mandatory` (
  `nManFieldId` bigint(20) NOT NULL auto_increment,
  `vManFieldName` varchar(200) default 'vFieldName',
  `vManLabelName` varchar(200) default 'Field Name',
  `vManFieldType` char(2) default 'TB',
  `vManStatus` char(1) default 'N',
  `vActiveStatus` char(1) default 'A',
  `nOrder` int(2) default '0',
  PRIMARY KEY  (`nManFieldId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_messages`
--

CREATE TABLE IF NOT EXISTS `eswap_messages` (
  `nMsgId` int(11) NOT NULL auto_increment,
  `nToUserId` int(11) default '0',
  `nFromUserId` int(11) default '0',
  `vTitle` tinytext,
  `vMsg` text,
  `vStatus` enum('Y','N') default 'N',
  `nDate` date default '0000-00-00',
  `vToDel` enum('Y','N') default 'N',
  `vFromDel` enum('Y','N') default 'N',
  PRIMARY KEY  (`nMsgId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_metatags`
--

CREATE TABLE IF NOT EXISTS `eswap_metatags` (
  `nId` int(11) NOT NULL auto_increment,
  `vPageName` varchar(100) default NULL,
  PRIMARY KEY  (`nId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_metatags_lang`
--

CREATE TABLE IF NOT EXISTS `eswap_metatags_lang` (
  `meta_lang_id` int(11) NOT NULL auto_increment,
  `meta_id` int(11) NOT NULL COMMENT 'meta id',
  `lang_id` int(11) NOT NULL COMMENT 'language id',
  `vTitle` varchar(200) NOT NULL COMMENT 'meta title',
  `vKeywords` text COMMENT 'meta keywords',
  `vDescription` text COMMENT 'meta description',
  PRIMARY KEY  (`meta_lang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Table structure for table `eswap_online`
--

CREATE TABLE IF NOT EXISTS `eswap_online` (
  `nUserId` bigint(20) NOT NULL default '0',
  `nLoggedOn` int(15) NOT NULL default '0',
  `nActiveTill` int(15) NOT NULL default '0',
  `nIdle` smallint(6) NOT NULL default '0',
  `vVisible` enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (`nUserId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_payment`
--

CREATE TABLE IF NOT EXISTS `eswap_payment` (
  `nTxn_no` int(11) NOT NULL auto_increment,
  `vTxn_type` varchar(10) default NULL,
  `vTxn_id` varchar(100) default NULL,
  `nTxn_amount` double default '0',
  `vTxn_mode` varchar(10) default NULL,
  `dTxn_date` datetime default NULL,
  `nUserId` int(11) default '0',
  `nSaleId` int(11) default '0',
  `vInvno` varchar(100) default '0',
  `vPlanStatus` enum('A','I','P','C') NOT NULL default 'P',
  `vComments` text NOT NULL,
  `nPlanId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`nTxn_no`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_paymentdetails`
--

CREATE TABLE IF NOT EXISTS `eswap_paymentdetails` (
  `nPaymentId` int(11) NOT NULL auto_increment,
  `vName` varchar(100) default NULL,
  `vReferenceNo` varchar(100) default NULL,
  `vBank` varchar(100) default NULL,
  `dReferenceDate` datetime default NULL,
  `dEntryDate` datetime default NULL,
  PRIMARY KEY  (`nPaymentId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_pins`
--

CREATE TABLE IF NOT EXISTS `eswap_pins` (
  `nPINId` bigint(20) NOT NULL auto_increment,
  `nPIN` varchar(100) default NULL,
  `nBatchId` bigint(20) default NULL,
  `nUserId` int(11) default '0',
  `dAltmntDate` datetime default NULL,
  PRIMARY KEY  (`nPINId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_plan`
--

CREATE TABLE IF NOT EXISTS `eswap_plan` (
  `nPlanId` int(11) NOT NULL auto_increment,
  `nPrice` float default NULL,
  `vActive` enum('0','1') default '1',
  `vPeriods` char(1) default '',
  `nPosition` int(11) NOT NULL default '0',
  PRIMARY KEY  (`nPlanId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_plan_lang`
--

CREATE TABLE IF NOT EXISTS `eswap_plan_lang` (
  `plan_lang_id` int(11) NOT NULL auto_increment,
  `plan_id` int(11) NOT NULL COMMENT 'plan id',
  `lang_id` int(11) NOT NULL COMMENT 'language id',
  `vPlanName` varchar(100) NOT NULL COMMENT 'plan name',
  PRIMARY KEY  (`plan_lang_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_pointhistory`
--

CREATE TABLE IF NOT EXISTS `eswap_pointhistory` (
  `nHId` bigint(20) NOT NULL auto_increment,
  `nSendBy` int(11) NOT NULL default '0',
  `nSendTo` int(11) NOT NULL default '0',
  `nPoints` double NOT NULL default '0',
  `dDate` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`nHId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_referrals`
--

CREATE TABLE IF NOT EXISTS `eswap_referrals` (
  `nRefId` int(11) NOT NULL auto_increment,
  `nUserId` int(11) default '0',
  `vName` varchar(100) default NULL,
  `vAddress` varchar(100) default NULL,
  `vPhone` varchar(20) default NULL,
  `vFax` varchar(20) default NULL,
  `vEmail` varchar(100) default NULL,
  `vSurveyStatus` char(2) default '0',
  `dSurveyDate` datetime default NULL,
  `nSurveyAmount` double default '0',
  `vSurveyAnswer` text,
  `vRegStatus` char(2) default '0',
  `dRegDate` datetime default NULL,
  `nRegAmount` double default '0',
  `vPayStatus` char(2) default '0',
  `nSurveyCashTxn` int(11) default '0',
  `nRegCashTxn` int(11) default '0',
  `nUserRegId` int(11) default '0',
  `vSurveyIP` varchar(20) default NULL,
  PRIMARY KEY  (`nRefId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_sale`
--

CREATE TABLE IF NOT EXISTS `eswap_sale` (
  `nSaleId` int(11) NOT NULL auto_increment,
  `nCategoryId` int(11) default '0',
  `nUserId` int(11) default '0',
  `vTitle` varchar(100) default NULL,
  `vBrand` varchar(100) default NULL,
  `vType` varchar(100) default NULL,
  `vCondition` varchar(100) default NULL,
  `vYear` int(4) default NULL,
  `nValue` double default '0',
  `nShipping` double default '0',
  `vUrl` varchar(255) default NULL,
  `vDescription` text,
  `dPostDate` datetime default NULL,
  `nQuantity` double default '0',
  `vFeatured` char(2) NOT NULL default 'N',
  `vDelStatus` tinyint(1) NOT NULL default '0',
  `vSmlImg` varchar(200) default NULL,
  `vImgDes` text,
  `nPoint` double NOT NULL default '0',
  PRIMARY KEY  (`nSaleId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_saledetails`
--

CREATE TABLE IF NOT EXISTS `eswap_saledetails` (
  `nSaleId` int(11) default '0',
  `nUserId` int(11) default '0',
  `vMethod` varchar(50) default NULL,
  `vTxnId` varchar(100) default NULL,
  `nAmount` double default '0',
  `dDate` datetime default NULL,
  `nQuantity` double default '0',
  `vSaleStatus` varchar(10) default '2',
  `nCashTxnId` int(11) default '0',
  `vRejected` varchar(10) default '0',
  `dTxnDate` datetime default NULL,
  `vDelivered` char(2) default 'N',
  `nPoint` double NOT NULL default '0',
  `vAddress1` varchar(255) default NULL,
  `vAddress2` varchar(255) default NULL,
  `vCity` varchar(100) default NULL,
  `vState` varchar(100) default NULL,
  `vCountry` varchar(100) default NULL,
  `nZip` varchar(11) default NULL,
  `vPhone` varchar(50) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_saleextra`
--

CREATE TABLE IF NOT EXISTS `eswap_saleextra` (
  `nSaleextraId` int(11) NOT NULL auto_increment,
  `nCategoryId` int(11) default '0',
  `nUserId` int(11) default '0',
  `vTitle` varchar(100) default NULL,
  `vBrand` varchar(100) default NULL,
  `vType` varchar(100) default NULL,
  `vCondition` varchar(100) default NULL,
  `vYear` int(4) default NULL,
  `nValue` double default '0',
  `nShipping` double default '0',
  `vUrl` varchar(255) default NULL,
  `vDescription` text,
  `dPostDate` datetime default NULL,
  `nQuantity` double default '0',
  `nFeatured` double NOT NULL default '0',
  `nCommission` double default '0',
  `vReferenceNo` varchar(100) default NULL,
  `vName` varchar(100) default NULL,
  `vBank` varchar(100) default NULL,
  `dReferenceDate` datetime default NULL,
  `vMode` varchar(10) default NULL,
  `vSmlImg` varchar(200) default NULL,
  `vImgDes` text,
  `nPoint` float NOT NULL default '0',
  PRIMARY KEY  (`nSaleextraId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_saleinter`
--

CREATE TABLE IF NOT EXISTS `eswap_saleinter` (
  `nSaleInterId` int(11) NOT NULL auto_increment,
  `nSaleId` int(11) default '0',
  `nUserId` int(11) default '0',
  `dDate` datetime default NULL,
  `vMethod` varchar(50) default NULL,
  `vName` varchar(100) default NULL,
  `vBank` varchar(100) default NULL,
  `vReferenceNo` varchar(100) default NULL,
  `dReferenceDate` datetime default NULL,
  `dEntryDate` datetime default NULL,
  `vDelStatus` varchar(10) default '0',
  PRIMARY KEY  (`nSaleInterId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_saletemp`
--

CREATE TABLE IF NOT EXISTS `eswap_saletemp` (
  `nSaleTempId` int(11) NOT NULL auto_increment,
  `nSaleId` int(11) default '0',
  `nUserId` int(11) default '0',
  `vMethod` varchar(50) default NULL,
  `nAmount` double default '0',
  `dDate` datetime default '0000-00-00 00:00:00',
  `nQuantity` double default '0',
  PRIMARY KEY  (`nSaleTempId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_smilies`
--

CREATE TABLE IF NOT EXISTS `eswap_smilies` (
  `nSId` int(100) NOT NULL auto_increment,
  `vImgCode` varchar(100) NOT NULL default '',
  `vPath` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`nSId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_successfee`
--

CREATE TABLE IF NOT EXISTS `eswap_successfee` (
  `nSId` bigint(20) NOT NULL auto_increment,
  `nUserId` int(11) NOT NULL default '0',
  `nPurchaseBy` int(11) NOT NULL default '0',
  `nProdId` int(11) NOT NULL default '0',
  `nAmount` double NOT NULL default '0',
  `nPoints` double NOT NULL default '0',
  `vStatus` char(1) NOT NULL default 'P',
  `dDate` date NOT NULL default '0000-00-00',
  `vType` enum('sa','s','w') NOT NULL default 's',
  PRIMARY KEY  (`nSId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_successtransactionpayments`
--

CREATE TABLE IF NOT EXISTS `eswap_successtransactionpayments` (
  `nId` int(11) NOT NULL auto_increment,
  `nSId` bigint(20) default '0',
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_surveyquestions`
--

CREATE TABLE IF NOT EXISTS `eswap_surveyquestions` (
  `nQuestionId` tinyint(4) NOT NULL auto_increment,
  `vQuestionText` varchar(100) default NULL,
  `nQuestionType` tinyint(2) default NULL,
  PRIMARY KEY  (`nQuestionId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_swap`
--

CREATE TABLE IF NOT EXISTS `eswap_swap` (
  `nSwapId` int(11) NOT NULL auto_increment,
  `nCategoryId` int(11) default NULL,
  `nUserId` int(11) default NULL,
  `vTitle` varchar(100) default NULL,
  `vBrand` varchar(100) default NULL,
  `vType` varchar(50) default NULL,
  `vCondition` varchar(50) default NULL,
  `vYear` int(4) default NULL,
  `nValue` double default '0',
  `nShipping` double default NULL,
  `vUrl` varchar(255) default NULL,
  `vDescription` text,
  `vPostType` varchar(10) default NULL,
  `dPostDate` datetime default NULL,
  `nSwapAmount` double default '0',
  `nSwapMember` int(11) default NULL,
  `vEscrow` varchar(50) default NULL,
  `vMethod` varchar(50) default NULL,
  `vTxnId` varchar(100) default NULL,
  `nSwapReturnId` int(11) default NULL,
  `vSwapStatus` varchar(50) default '0',
  `vFeatured` char(2) default 'N',
  `nCashTxnId` int(11) default '0',
  `vDelStatus` varchar(10) NOT NULL default '0',
  `dTxnDate` datetime default NULL,
  `vDelivered` char(2) default 'N',
  `vOwnerDelivery` char(2) default 'N',
  `dOwnerDate` datetime default NULL,
  `vPartnerDelivery` char(2) default 'N',
  `dPartnerDate` datetime default NULL,
  `vSmlImg` varchar(200) default NULL,
  `vImgDes` text,
  `nPoint` double NOT NULL default '0',
  `nQuantity` double NOT NULL default '0',
  PRIMARY KEY  (`nSwapId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_swapinter`
--

CREATE TABLE IF NOT EXISTS `eswap_swapinter` (
  `nSwapInterId` int(11) NOT NULL auto_increment,
  `nSwapId` int(11) default '0',
  `nUserId` int(11) default '0',
  `nAmount` double default '0',
  `vMethod` varchar(50) default NULL,
  `vMode` varchar(10) default NULL,
  `vPostType` varchar(10) default NULL,
  `dDate` datetime default NULL,
  `nEntryUser` int(11) default '0',
  `dEntryDate` datetime default NULL,
  `vName` varchar(100) default NULL,
  `vBank` varchar(100) default NULL,
  `vReferenceNo` varchar(100) default NULL,
  `dReferenceDate` datetime default NULL,
  `vDelStatus` varchar(10) default '0',
  PRIMARY KEY  (`nSwapInterId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_swapreturn`
--

CREATE TABLE IF NOT EXISTS `eswap_swapreturn` (
  `nSwapId` int(11) default '0',
  `nUserId` int(11) default '0',
  `nSwapReturnId` int(11) default '0',
  `nSTId` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_swaptemp`
--

CREATE TABLE IF NOT EXISTS `eswap_swaptemp` (
  `nTempId` int(11) NOT NULL auto_increment,
  `nSwapId` int(11) default '0',
  `nUserId` int(11) default '0',
  `nAmount` double default '0',
  `vMethod` varchar(50) default NULL,
  `vMode` varchar(10) default NULL,
  `vPostType` varchar(10) default NULL,
  `dDate` datetime default NULL,
  PRIMARY KEY  (`nTempId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_swaptxn`
--

CREATE TABLE IF NOT EXISTS `eswap_swaptxn` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_tempdata`
--

CREATE TABLE IF NOT EXISTS `eswap_tempdata` (
  `nId` int(11) NOT NULL auto_increment,
  `vValue` varchar(200) default NULL,
  `vData` longtext,
  PRIMARY KEY  (`nId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_usercredits`
--

CREATE TABLE IF NOT EXISTS `eswap_usercredits` (
  `nCId` bigint(20) NOT NULL auto_increment,
  `nUserId` bigint(20) NOT NULL default '0',
  `nPoints` float NOT NULL default '0',
  PRIMARY KEY  (`nCId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_userfeedback`
--

CREATE TABLE IF NOT EXISTS `eswap_userfeedback` (
  `nFBId` bigint(20) NOT NULL auto_increment,
  `nUserId` int(11) NOT NULL default '0',
  `vTitle` varchar(100) NOT NULL default '',
  `nUserFBId` bigint(20) NOT NULL default '0',
  `nSaleId` INT(11) NOT NULL DEFAULT '0', 
  `vMatter` text NOT NULL,
  `dPostDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `vStatus` enum('S','D','N') NOT NULL default 'N',
  PRIMARY KEY  (`nFBId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_users`
--

CREATE TABLE IF NOT EXISTS `eswap_users` (
  `nUserId` int(11) NOT NULL auto_increment,
  `vLoginName` varchar(100) default NULL,
  `vPassword` varchar(100) default NULL,
  `vFirstName` varchar(100) default NULL,
  `vLastName` varchar(100) default NULL,
  `vAddress1` varchar(100) default NULL,
  `vAddress2` varchar(100) default NULL,
  `vCity` varchar(100) default NULL,
  `vState` varchar(100) default NULL,
  `vCountry` varchar(100) default NULL,
  `nZip` int(11) default NULL,
  `vPhone` varchar(50) default NULL,
  `vFax` varchar(50) default NULL,
  `vEmail` varchar(100) default NULL,
  `vUrl` varchar(255) default NULL,
  `vGender` varchar(10) default NULL,
  `vEducation` varchar(50) default NULL,
  `vDescription` text,
  `nAmount` double default '0',
  `vMethod` varchar(50) default NULL,
  `vTxnId` varchar(100) default NULL,
  `dDateReg` datetime default NULL,
  `nAccount` double default '0',
  `vAlertStatus` char(1) NOT NULL default 'N',
  `vNLStatus` char(1) NOT NULL default 'N',
  `vStatus` varchar(10) default '0',
  `vAdvSource` varchar(100) NOT NULL default '',
  `vAdvEmployee` varchar(100) default NULL,
  `vDelStatus` varchar(10) NOT NULL default '0',
  `nAffiliateId` int(11) default '0',
  `vMembershipType` varchar(10) default '0',
  `vCodeForPin` varchar(10) default '0',
  `dStartDate` datetime default NULL,
  `dEndDate` datetime default NULL,
  `nLastLogin` int(11) default NULL,
  `vReferenceNo` varchar(100) NOT NULL default '',
  `dReferenceDate` datetime default NULL,
  `nRefId` int(11) default '0',
  `vName` varchar(100) NOT NULL default '',
  `vBank` varchar(100) NOT NULL default '',
  `vPaypalEmail` varchar(200) NOT NULL default '',
  `vPaypalAuthToken` varchar(200) NOT NULL default '',
  `nPlanId` int(11) NOT NULL default '0',
  `dPlanExpDate` date NOT NULL default '0000-00-00',
  `profile_image` varchar(255) NULL,
  `vIMStatus` enum('Y','N') NOT NULL default 'N' COMMENT 'show image to all',
  `preferred_language` int(11) NOT NULL default '1' COMMENT 'language id',
  `latitude` varchar(25) NULL,
  `longitude` varchar(25) NULL,
  PRIMARY KEY  (`nUserId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_user_referral`
--

CREATE TABLE IF NOT EXISTS `eswap_user_referral` (
  `nUserId` int(11) default '0',
  `nSurveyCount` int(11) default '0',
  `nRegCount` int(11) default '0',
  `nSurveyAmount` double default '0',
  `nRegAmount` double default '0',
  `nSurveyPaid` double default '0',
  `nRegPaid` double default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eswap_sliders`
--

CREATE TABLE `eswap_sliders` (
	`nSId` INT(11) NOT NULL AUTO_INCREMENT,
	`vName` VARCHAR(200) NULL,
	`vImg` VARCHAR(200) NULL DEFAULT NULL,
	`nDate` DATE NULL DEFAULT '0000-00-00',
	`vActive` ENUM('1','0') NULL DEFAULT '0',
	PRIMARY KEY (`nSId`)
)
COLLATE='latin1_swedish_ci'
ENGINE=MyISAM;

--
-- New Field to Table eswap_category
--

ALTER TABLE `eswap_category` ADD `cat_image` VARCHAR( 300 ) NULL AFTER `nPosition`;

ALTER TABLE `eswap_users` CHANGE `nZip` `nZip` VARCHAR( 11 ) NULL DEFAULT NULL;

ALTER TABLE `eswap_gallery`	ADD COLUMN `vMedImg` VARCHAR(255) NULL DEFAULT NULL AFTER `vSmlImg`;

ALTER TABLE `eswap_sliders` ADD COLUMN `vlocUrl` varchar(200) default NULL;

ALTER TABLE `eswap_swaptxn` ADD `wishedId` INT( 11 ) NOT NULL ;
