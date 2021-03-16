ALTER TABLE eswaps_lookup CHANGE vLookUpDesc vLookUpDesc text; 

ALTER TABLE eswaps_users ADD nLastLogin int(11) NOT NULL default '0' AFTER dEndDate,
ADD vReferenceNo varchar(100) NOT NULL default '' AFTER nLastLogin,
ADD dReferenceDate datetime default '' AFTER vReferenceNo,
ADD nRefId int(11) default '0' AFTER dReferenceDate,
ADD vName varchar(100) NOT NULL default '' AFTER nRefId,
ADD vBank varchar(100) NOT NULL default '' AFTER vName,
ADD vPaypalEmail varchar(200) NOT NULL default '' AFTER vBank,
ADD vPaypalAuthToken varchar(200) NOT NULL default '' AFTER vPaypalEmail;

ALTER TABLE eswaps_swap ADD vSmlImg varchar(200) NOT NULL default '' AFTER dPartnerDate,
ADD vImgDes text NOT NULL default '' AFTER vSmlImg,
ADD nPoint double NOT NULL default '0' AFTER vImgDes;

ALTER TABLE eswaps_sale ADD vSmlImg varchar(200) NOT NULL default '' AFTER vDelStatus,
ADD vImgDes text NOT NULL default '' AFTER vSmlImg;

ALTER TABLE eswaps_saleextra ADD vSmlImg varchar(200) NOT NULL default '' AFTER vMode,
ADD vImgDes text NOT NULL default '' AFTER vSmlImg;

ALTER TABLE eswaps_category ADD nPosition int(11) NOT NULL default '0' AFTER nCount;

ALTER TABLE eswaps_swaptxn ADD vBlink CHAR( 1 ) DEFAULT 'B' NOT NULL AFTER vPostType;

ALTER TABLE eswaps_swaptxn ADD nSTId BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE eswaps_userfeedback ADD vStatus ENUM( 'S', 'D', 'N' ) DEFAULT 'N' NOT NULL AFTER dPostDate;
ALTER TABLE eswaps_swapreturn ADD nSTId BIGINT(20) default '0' NOT NULL AFTER nUserId;

drop table if exists eswaps_banners;

CREATE TABLE eswaps_banners (
  nBId int(11) NOT NULL auto_increment,
  vName varchar(200) default NULL,
  vlocUrl varchar(200) default NULL,
  vImg varchar(200) default NULL,
  nDate date default '0000-00-00',
  vWidth varchar(100) default NULL,
  vHeight varchar(100) default NULL,
  nPosition int(11) default NULL,
  vActive enum('1','0') default '0',
  vLocation varchar(10) default NULL,
  nCount int(11) default '0',
  PRIMARY KEY  (nBId)
) TYPE=MyISAM;

drop table if exists eswaps_chat;

CREATE TABLE eswaps_chat (
  nUserId int(100) NOT NULL default '0',
  vMsg varchar(250) NOT NULL default '',
  vTimeStamp varchar(250) NOT NULL default '',
  vDisplayed char(1) NOT NULL default '',
  nFromId int(100) default NULL
) TYPE=MyISAM;

drop table if exists eswaps_faq;

CREATE TABLE eswaps_faq (
  nFId int(11) NOT NULL auto_increment,
  vTitle varchar(200) default NULL,
  vDes text,
  nPosition int(11) default '0',
  vActive enum('0','1') default '0',
  PRIMARY KEY  (nFId)
) TYPE=MyISAM;

drop table if exists eswaps_gallery;

CREATE TABLE eswaps_gallery (
  nId int(11) NOT NULL auto_increment,
  nUserId int(11) default '0',
  nSwapId int(11) default '0',
  nSaleId int(11) default '0',
  vImg varchar(200) default NULL,
  vDes text,
  nTempId int(11) default '0',
  vSmlImg varchar(200) default NULL,
  vDelStatus enum('0','1') default '0',
  PRIMARY KEY  (nId)
) TYPE=MyISAM;

drop table if exists eswaps_Help;

CREATE TABLE eswaps_Help (
  nHId int(11) NOT NULL auto_increment,
  nHcId int(11) default NULL,
  vHtitle varchar(100) default NULL,
  vHdescription text,
  nHposition int(11) default NULL,
  vActive enum('0','1') default NULL,
  vHimage varchar(100) default NULL,
  PRIMARY KEY  (nHId)
) TYPE=MyISAM;

drop table if exists eswaps_helpcategory;

CREATE TABLE eswaps_helpcategory (
  nHcId int(11) NOT NULL auto_increment,
  vHtype varchar(6) default NULL,
  vHctitle varchar(100) default NULL,
  nHcposition int(11) default NULL,
  vActive enum('0','1') default NULL,
  PRIMARY KEY  (nHcId)
) TYPE=MyISAM;

drop table if exists eswaps_messages;

CREATE TABLE eswaps_messages (
  nMsgId int(11) NOT NULL auto_increment,
  nToUserId int(11) default '0',
  nFromUserId int(11) default '0',
  vTitle tinytext,
  vMsg text,
  vStatus enum('Y','N') default 'N',
  nDate date default '0000-00-00',
  vToDel enum('Y','N') default 'N',
  vFromDel enum('Y','N') default 'N',
  PRIMARY KEY  (nMsgId)
) TYPE=MyISAM;

drop table if exists eswaps_metatags;

CREATE TABLE eswaps_metatags (
  nId int(11) NOT NULL auto_increment,
  vTitle varchar(200) default NULL,
  vKeywords text,
  vDescription text,
  vPageName varchar(20) default NULL,
  PRIMARY KEY  (nId)
) TYPE=MyISAM;

drop table if exists eswaps_online;

CREATE TABLE eswaps_online (
  nUserId bigint(20) NOT NULL default '0',
  nLoggedOn int(15) NOT NULL default '0',
  nActiveTill int(15) NOT NULL default '0',
  nIdle smallint(6) NOT NULL default '0',
  vVisible enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (nUserId)
) TYPE=MyISAM;

drop table if exists eswaps_smilies;

CREATE TABLE eswaps_smilies (
  nSId int(100) NOT NULL auto_increment,
  vImgCode varchar(100) NOT NULL default '',
  vPath varchar(100) NOT NULL default '',
  PRIMARY KEY  (nSId)
) TYPE=MyISAM;

drop table if exists eswaps_client_module_category;

CREATE TABLE eswaps_client_module_category (
  nCategoryId mediumint(9) NOT NULL auto_increment,
  nParentId int(11) default '0',
  vCategoryTitle varchar(100) NOT NULL default '',
  vCategoryFile varchar(100) NOT NULL default '',
  nCposition varchar(10) default NULL,
  vActive enum('0','1') default NULL,
  nTmp_status int(11) default '0',
  PRIMARY KEY  (nCategoryId)
) TYPE=MyISAM;

drop table if exists eswaps_plan;

CREATE TABLE eswaps_plan (
  nPlanId int(11) NOT NULL auto_increment,
  vPlanName varchar(100) default NULL,
  nPrice float default NULL,
  vActive enum('0','1') default '1',
  vPeriods char(1) default '',
  nPosition int(11) NOT NULL default '0',
  PRIMARY KEY  (nPlanId)
) TYPE=MyISAM;

drop table if exists eswaps_usercredits;

CREATE TABLE eswaps_usercredits (
  nCId bigint(20) NOT NULL auto_increment,
  nUserId bigint(20) NOT NULL default '0',
  nPoints float NOT NULL default '0',
  PRIMARY KEY  (nCId)
) TYPE=MyISAM;

drop table if exists eswaps_pointhistory;

CREATE TABLE eswaps_pointhistory (
  nHId bigint(20) NOT NULL auto_increment,
  nSendBy int(11) NOT NULL default '0',
  nSendTo int(11) NOT NULL default '0',
  nPoints double NOT NULL default '0',
  dDate date NOT NULL default '0000-00-00',
  PRIMARY KEY  (nHId)
) TYPE=MyISAM;

drop table if exists eswaps_creditpayments;

CREATE TABLE eswaps_creditpayments (
  nId int(11) NOT NULL auto_increment,
  nUserId int(11) NOT NULL default '0',
  nAmount double NOT NULL default '0',
  nPoints double NOT NULL default '0',
  vTxnId varchar(200) NOT NULL default '',
  vMethod char(2) NOT NULL default '00',
  dDate date NOT NULL default '0000-00-00',
  vCurrentTransaction varchar(200) NOT NULL default '',
  vStatus char(1) NOT NULL default 'P',
  vName varchar(200) NOT NULL default '',
  vBank varchar(200) NOT NULL default '',
  vReferenceNo varchar(200) NOT NULL default '',
  dReferenceDate date NOT NULL default '0000-00-00',
  PRIMARY KEY  (nId)
) TYPE=MyISAM;

drop table if exists eswaps_counteroffer;

CREATE TABLE eswaps_counteroffer (
	nCId INT NOT NULL AUTO_INCREMENT ,
	nSTId BIGINT DEFAULT '0' NOT NULL ,
	nPostedBy BIGINT DEFAULT '0' NOT NULL ,
	nPostedTo BIGINT DEFAULT '0' NOT NULL ,
	nPrice FLOAT NOT NULL ,
	vDes TEXT NOT NULL ,
	vType CHAR( 4 ) NOT NULL ,
	vStatus CHAR( 1 ) NOT NULL ,
	PRIMARY KEY ( nCId )
) TYPE=MyISAM;

drop table if exists eswaps_successfee;

CREATE TABLE eswaps_successfee (
	nSId BIGINT NOT NULL AUTO_INCREMENT ,
	nUserId INT DEFAULT '0' NOT NULL ,
	nPurchaseBy INT DEFAULT '0' NOT NULL ,
	nProdId INT DEFAULT '0' NOT NULL ,
	nAmount DOUBLE DEFAULT '0' NOT NULL ,
	nPoints DOUBLE DEFAULT '0' NOT NULL ,
	vStatus CHAR( 1 ) DEFAULT 'P' NOT NULL ,
	dDate DATE DEFAULT '0000-00-00' NOT NULL ,
	vType enum('sa','s','w') DEFAULT 's' NOT NULL ,
	PRIMARY KEY ( nSId )
) TYPE = MYISAM ;

drop table if exists eswaps_successtransactionpayments;

CREATE TABLE eswaps_successtransactionpayments (
  nId int(11) NOT NULL auto_increment,
  nSId int(11) NOT NULL default '0',
  nUserId int(11) NOT NULL default '0',
  nAmount double NOT NULL default '0',
  nProdId double NOT NULL default '0',
  vTxnId varchar(200) NOT NULL default '',
  vMethod char(2) NOT NULL default '00',
  dDate date NOT NULL default '0000-00-00',
  vStatus char(1) NOT NULL default 'P',
  vName varchar(200) NOT NULL default '',
  vBank varchar(200) NOT NULL default '',
  vReferenceNo varchar(200) NOT NULL default '',
  dReferenceDate date NOT NULL default '0000-00-00',
  PRIMARY KEY  (nId)
) TYPE=MyISAM;

drop table if exists eswaps_listingfee;

CREATE TABLE eswaps_listingfee 
(
	nLId INT NOT NULL AUTO_INCREMENT ,
	nFrom DOUBLE DEFAULT '0' NOT NULL ,
	nTo DOUBLE DEFAULT '0' NOT NULL ,
	nPrice DOUBLE DEFAULT '0' NOT NULL ,
	vActive ENUM( '0', '1' ) DEFAULT '0' NOT NULL ,
	nLPosition INT DEFAULT '0' NOT NULL ,
	PRIMARY KEY ( nLId )
) TYPE=MyISAM;

ALTER TABLE `eswaps_payment ADD vPlanStatus ENUM( 'A', 'I', 'P', 'C' ) NOT NULL AFTER vInvno ,
ADD `vComments TEXT NOT NULL AFTER vPlanStatus ,
ADD nPlanId INT NOT NULL AFTER vComments ;

drop table if exists eswaps_plan;

CREATE TABLE IF NOT EXISTS eswaps_plan (
  nPlanId int(11) NOT NULL AUTO_INCREMENT,
  vPlanName varchar(100) DEFAULT NULL,
  nPrice float DEFAULT NULL,
  vActive enum('0','1') DEFAULT '1',
  vPeriods char(1) DEFAULT '',
  nPosition int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (nPlanId)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE eswaps_users ADD nLastLogin INT NOT NULL AFTER dEndDate ,
ADD vReferenceNo VARCHAR( 100 ) NOT NULL AFTER nLastLogin ,
ADD dReferenceDate DATETIME NOT NULL AFTER vReferenceNo ,
ADD nRefId INT NOT NULL DEFAULT '0' AFTER dReferenceDate ,
ADD vName VARCHAR( 100 ) NOT NULL AFTER nRefId ,
ADD vBank VARCHAR( 100 ) NOT NULL AFTER vName ,
ADD vPaypalEmail VARCHAR( 200 ) NOT NULL AFTER vBank ,
ADD vPaypalAuthToken VARCHAR( 200 ) NOT NULL AFTER vPaypalEmail ,
ADD nPlanId INT NOT NULL DEFAULT '0' AFTER vPaypalAuthToken ,
 ADD dPlanExpDate DATE NOT NULL DEFAULT '0000-00-00';

