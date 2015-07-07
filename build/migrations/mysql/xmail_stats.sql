DROP TABLE IF EXISTS `__PREFIX__xmail_stats`;

CREATE TABLE `__PREFIX__xmail_stats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
  `list_id` int(11) unsigned DEFAULT NULL COMMENT 'Mail list',
  `subject` varchar(255) NOT NULL DEFAULT '' COMMENT 'Subscriber name',
  `status` enum('ok','pending','failed','abort') NOT NULL DEFAULT 'pending' COMMENT 'Mailing status',
  `date_start` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Mailing start date',
  `date_finish` datetime NULL COMMENT 'Mailing finish date',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_date_start` (`date_start`),
  KEY `idx_date_finish` (`date_finish`),
  KEY `idx_status_date_start` (`status`,`date_start`),
  KEY `idx_status_date_finish` (`status`,`date_finish`),
  CONSTRAINT `fk_list_id` FOREIGN KEY (`list_id`) REFERENCES `__PREFIX__xmail_lists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mailing status table';