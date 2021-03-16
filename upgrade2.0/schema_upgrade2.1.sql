ALTER TABLE eswaps_swaptxn ADD vBlink CHAR( 1 ) DEFAULT 'B' NOT NULL AFTER vPostType;
ALTER TABLE eswaps_swaptxn ADD nSTId BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE eswaps_userfeedback ADD vStatus ENUM( 'S', 'D', 'N' ) DEFAULT 'N' NOT NULL AFTER dPostDate;
ALTER TABLE eswaps_swap ADD nPoint double NOT NULL default '0' AFTER vImgDes;
ALTER TABLE eswaps_swapreturn ADD nSTId BIGINT(20) default '0' NOT NULL AFTER nUserId;

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
