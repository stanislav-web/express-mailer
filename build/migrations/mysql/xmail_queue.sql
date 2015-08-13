DROP TABLE IF EXISTS `__PREFIX__xmail_queue`;

CREATE TABLE `__PREFIX__xmail_queue` (
  `pid` int(11) unsigned DEFAULT NULL COMMENT 'Queue ID',
  `storage` varchar(32) NOT NULL DEFAULT '' COMMENT 'Used storage adapter',
  `broker` varchar(32) NOT NULL DEFAULT '' COMMENT 'Used queue adapter',
  `mail` varchar(32) NOT NULL DEFAULT '' COMMENT 'Used mail adapter',
  `priority` tinyint(2) unsigned DEFAULT '0' COMMENT 'Queue priority',
  `date_activation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Activation date',
  KEY `idx_pid` (`pid`),
  UNIQUE KEY (`pid`, `storage`, `broker`, `mail`),
  KEY `idx_adapters` (`storage`, `broker`, `mail`),
  KEY `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mail queue list';