SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `__PREFIX__xmail_subscribers`;

CREATE TABLE `__PREFIX__xmail_subscribers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Subscriber ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Subscriber name',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT 'Subscriber email',
  `state` enum('disabled','active','moderated') NOT NULL DEFAULT 'disabled' COMMENT 'Activity state, 0 - disabled, 1 - active, 2 - moderated',
  `checked` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Is email checked & valid',
  `date_registration` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Default subscriber reg date',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_email` (`email`),
  KEY `idx_state` (`state`),
  KEY `idx_checked` (`checked`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscriber\'s list';

SET FOREIGN_KEY_CHECKS = 1;