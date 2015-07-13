SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `__PREFIX__xmail_active_log`;

CREATE TABLE `__PREFIX__xmail_active_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `subscriber_id` int(11) unsigned DEFAULT NULL COMMENT 'Subscriber ID',
  `list_id` int(11) unsigned DEFAULT NULL COMMENT 'Mail list ID',
  `date_send` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Send date',
  PRIMARY KEY (`id`),
  KEY `fk_subscriber_id` (`subscriber_id`),
  KEY `fk_subscribers_list_id` (`list_id`),
  CONSTRAINT `fk_subscriber_id` FOREIGN KEY (`subscriber_id`) REFERENCES `__PREFIX__xmail_subscribers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_subscribers_list_id` FOREIGN KEY (`list_id`) REFERENCES `__PREFIX__xmail_lists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscriber''s active lists';

SET FOREIGN_KEY_CHECKS = 1;