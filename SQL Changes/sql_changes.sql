# 29-06-2015
CREATE TABLE IF NOT EXISTS `es_state` (
  `ts_id` bigint(20) NOT NULL auto_increment,
  `tc_id` bigint(20) default '0',
  `ts_code` varchar(20) default NULL,
  `ts_name` varchar(250) default NULL,
  `ts_status` char(1) default 'A',
  PRIMARY KEY  (`ts_id`)
);

INSERT INTO `es_state` (`ts_id`, `tc_id`, `ts_code`, `ts_name`, `ts_status`) VALUES
	(1, 1, 'AL', 'ALABAMA', 'A'),
	(2, 1, 'AK', 'ALASKA', 'A'),
	(4, 1, 'AZ', 'ARIZONA', 'A'),
	(5, 1, 'AR', 'ARKANSAS', 'A'),
	(6, 1, 'CA', 'CALIFORNIA', 'A'),
	(7, 1, 'CO', 'COLORADO', 'A'),
	(8, 1, 'CT', 'CONNECTICUT', 'A'),
	(9, 1, 'DE', 'DELAWARE', 'A'),
	(10, 1, 'DC', 'DISTRICT OF COLUMBIA', 'A'),
	(12, 1, 'FL', 'FLORIDA', 'A'),
	(13, 1, 'GA', 'GEORGIA', 'A'),
	(15, 1, 'HI', 'HAWAII', 'A'),
	(16, 1, 'ID', 'IDAHO', 'A'),
	(17, 1, 'IL', 'ILLINOIS', 'A'),
	(18, 1, 'IN', 'INDIANA', 'A'),
	(19, 1, 'IA', 'IOWA', 'A'),
	(20, 1, 'KS', 'KANSAS', 'A'),
	(21, 1, 'KY', 'KENTUCKY', 'A'),
	(22, 1, 'LA', 'LOUISIANA', 'A'),
	(23, 1, 'ME', 'MAINE', 'A'),
	(25, 1, 'MD', 'MARYLAND', 'A'),
	(26, 1, 'MA', 'MASSACHUSETTS', 'A'),
	(27, 1, 'MI', 'MICHIGAN', 'A'),
	(28, 1, 'MN', 'MINNESOTA', 'A'),
	(29, 1, 'MS', 'MISSISSIPPI', 'A'),
	(30, 1, 'MO', 'MISSOURI', 'A'),
	(31, 1, 'MT', 'MONTANA', 'A'),
	(32, 1, 'NE', 'NEBRASKA', 'A'),
	(33, 1, 'NV', 'NEVADA', 'A'),
	(34, 1, 'NH', 'NEW HAMPSHIRE', 'A'),
	(35, 1, 'NJ', 'NEW JERSEY', 'A'),
	(36, 1, 'NM', 'NEW MEXICO', 'A'),
	(37, 1, 'NY', 'NEW YORK', 'A'),
	(38, 1, 'NC', 'NORTH CAROLINA', 'A'),
	(39, 1, 'ND', 'NORTH DAKOTA', 'A'),
	(40, 1, 'MP', 'NORTHERN MARIANA ISLANDS', 'A'),
	(41, 1, 'OH', 'OHIO', 'A'),
	(42, 1, 'OK', 'OKLAHOMA', 'A'),
	(43, 1, 'OR', 'OREGON', 'A'),
	(44, 1, 'PW', 'PALAU', 'A'),
	(45, 1, 'PA', 'PENNSYLVANIA', 'A'),
	(46, 1, 'PR', 'PUERTO RICO', 'A'),
	(47, 1, 'RI', 'RHODE ISLAND', 'A'),
	(48, 1, 'SC', 'SOUTH CAROLINA', 'A'),
	(49, 1, 'SD', 'SOUTH DAKOTA', 'A'),
	(50, 1, 'TN', 'TENNESSEE', 'A'),
	(51, 1, 'TX', 'TEXAS', 'A'),
	(52, 1, 'UT', 'UTAH', 'A'),
	(53, 1, 'VT', 'VERMONT', 'A'),
	(54, 1, 'VI', 'VIRGIN ISLANDS', 'A'),
	(55, 1, 'VA', 'VIRGINIA', 'A'),
	(56, 1, 'WA', 'WASHINGTON', 'A'),
	(57, 1, 'WV', 'WEST VIRGINIA', 'A'),
	(58, 1, 'WI', 'WISCONSIN', 'A');
	
# 3-07-15
ALTER TABLE  eswap_sliders ADD COLUMN `vlocUrl` varchar(200) default NULL;

# 16-07-15

UPDATE `db_eswaps2_2`.`es_help_lang` SET `vHdescription`='All the users registered with this site have the option to post feedback  about other users .\r\n\r\n<b>Post feedback</b>\r\n\r\n     To post feedback about a user,first you have to select the item of the intended user you wish to buy/swap ( <a href=\'{LINK}\'>click here to know how to select an item for sale</a> ) .The upcoming screen will displays the item in detail with four additional links such as  \' view large image \', \' users postings \' etc  . Click on \' users posting \' . In the ensuing screen you can view the intended user\'s listing such as sale,wish,offers etc. Click on \' post feedback about the user  \' on the top section of the screen . In the ensuing screen enter your feedback and click on \' post feedback \'\r\n\r\n\r\n\r\n<b>View feedback</b>\r\n\r\nTo view the feedback posted against you click on \' View feedbacks \' in the left.The upcoming screen will displays all the user feedback against you.To view feedback in detail click on feedback title against feedback row. ' WHERE `help_lang_id`='49';