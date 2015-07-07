ALTER TABLE `__PREFIX__xmail_stats` DROP FOREIGN KEY  `fk_list_id`;

DROP TABLE IF EXISTS `__PREFIX__xmail_lists`;

CREATE TABLE `__PREFIX__xmail_lists` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'List ID',
  `subject` varchar(255) NOT NULL DEFAULT '' COMMENT 'List subject',
  `message` text COMMENT 'List message',
  `date_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Default create date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mailing List\'s';