CREATE TABLE `es_user_devices` (
  `user_device_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL DEFAULT '0',
  `device_id` varchar(255) NOT NULL DEFAULT '0',
  `device_type` varchar(255) NOT NULL DEFAULT '0',
  `auth_key` varchar(255) NOT NULL DEFAULT '0',
  `date_added` datetime DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1



ALTER TABLE `es_users`
ADD `stripe_pub_key` varchar(255) NOT NULL;

ALTER TABLE `es_users`
ADD `stripe_secret_key` varchar(255) NOT NULL;